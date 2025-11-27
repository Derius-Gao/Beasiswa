<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Scholarship;
use App\Models\Transaction;
use App\Services\AIService;
use App\Services\NotificationService;

class DashboardController extends Controller
{
    protected AIService $aiService;
    protected NotificationService $notificationService;

    public function __construct(AIService $aiService, NotificationService $notificationService)
    {
        $this->aiService = $aiService;
        $this->notificationService = $notificationService;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input login
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Validasi captcha
        if (app()->environment('production')) {
            $request->validate(['g-recaptcha-response' => 'required']);
            $recaptcha = $request->input('g-recaptcha-response');
            $recaptchaSecret = '6Le6KAssAAAAANaZC9jGUS2TC-5JRCmZpPqJd37-';
            $recaptchaResponse = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptchaSecret . '&response=' . $recaptcha), true);
            if (!$recaptchaResponse['success']) {
                return back()->withErrors(['g-recaptcha-response' => 'Captcha online tidak valid'])->withInput();
            }
        } else {
            $request->validate(['captcha_answer' => 'required']);
            $answer = session('captcha_answer');
            if ((int)$request->input('captcha_answer') !== (int)$answer) {
                return back()->withErrors(['captcha_answer' => 'Captcha offline salah'])->withInput();
            }
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'major' => 'nullable|string|max:255',
            'economic_status' => 'required|string|in:low_income,middle_income,high_income',
            'level' => 'required|in:superadmin,admin,mahasiswa',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gpa' => $request->gpa,
            'major' => $request->major,
            'economic_status' => $request->economic_status,
            'level' => $request->level,
            'password' => Hash::make($request->password),
            'is_student' => $request->level === 'mahasiswa',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function index()
    {
        $user = Auth::user();

        $recentBills = $user->bills()
            ->orderByDesc('due_date')
            ->take(5)
            ->get();

        $scholarshipRecommendations = $user->scholarshipRecommendations()
            ->with('scholarship')
            ->orderByDesc('match_score')
            ->take(3)
            ->get();

        $stats = [
            'unpaid_bills' => $user->bills()->whereIn('status', ['unpaid', 'overdue'])->count(),
            'total_paid' => $user->payments()->where('status', 'completed')->sum('amount_paid'),
            'scholarship_matches' => $scholarshipRecommendations->count(),
            'notifications' => $user->notifications()->where('is_read', false)->count(),
        ];

        $insights = [
            'delay_risk' => round($this->aiService->predictPaymentDelays($user), 1),
        ];

        return view('dashboard.index', compact(
            'user',
            'recentBills',
            'scholarshipRecommendations',
            'stats',
            'insights'
        ));
    }

    public function bills()
    {
        $user = Auth::user();

        $bills = $user->bills()
            ->with('payments')
            ->orderByDesc('due_date')
            ->get();

        $summary = [
            'unpaid' => $bills->where('status', 'unpaid')->count(),
            'overdue' => $bills->where('status', 'overdue')->count(),
            'paid' => $bills->where('status', 'paid')->count(),
            'outstanding' => $bills->whereIn('status', ['unpaid', 'overdue'])->sum('amount'),
        ];

        return view('dashboard.bills', compact('bills', 'summary'));
    }

    public function payBill(Request $request, Bill $bill)
    {
        if ($bill->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:bank_transfer,credit_card,e_wallet',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'bill_id' => $bill->id,
                'user_id' => $request->user()->id,
                'amount_paid' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'paid_at' => now(),
            ]);

            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $validated['amount'],
                'type' => 'payment',
                'status' => 'pending',
                'description' => "Payment for bill #{$bill->id}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $anomalies = $this->aiService->detectTransactionAnomalies($transaction);
            if (!empty($anomalies)) {
                $transaction->update(['is_flagged' => true, 'anomaly_reason' => implode(', ', $anomalies)]);
                $this->notificationService->sendAnomalyAlert($request->user()->id, $anomalies);

                if (count($anomalies) > 2) {
                    $payment->update(['status' => 'blocked']);
                    DB::rollBack();

                    return redirect()
                        ->route('dashboard.bills')
                        ->withErrors(['payment' => 'Pembayaran diblokir karena aktivitas mencurigakan.']);
                }
            }

            $payment->update(['status' => 'completed']);
            $transaction->update(['status' => 'completed', 'processed_at' => now()]);

            $totalPaid = $bill->payments()->where('status', 'completed')->sum('amount_paid');
            if ($totalPaid >= $bill->amount) {
                $bill->update(['status' => 'paid']);
            }

            DB::commit();
            return redirect()
                ->route('dashboard.bills')
                ->with('success', 'Pembayaran berhasil diproses.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()
                ->route('dashboard.bills')
                ->withErrors(['payment' => 'Gagal memproses pembayaran. Silakan coba lagi.']);
        }
    }

    public function scholarships()
    {
        $user = Auth::user();

        $recommendations = $user->scholarshipRecommendations()
            ->with('scholarship')
            ->orderByDesc('match_score')
            ->get();

        $availableScholarships = Scholarship::where('is_active', true)
            ->orderBy('application_deadline')
            ->get();

        return view('dashboard.scholarships', [
            'recommendations' => $recommendations,
            'availableScholarships' => $availableScholarships,
        ]);
    }

    public function profile()
    {
        return view('dashboard.profile');
    }

    public function chatbot()
    {
        return view('chatbot.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
