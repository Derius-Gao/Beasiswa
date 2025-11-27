<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\AIService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $aiService;
    protected $notificationService;

    public function __construct(AIService $aiService, NotificationService $notificationService)
    {
        $this->aiService = $aiService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get user's bills
     */
    public function myBills(Request $request)
    {
        $bills = $request->user()->bills()
            ->with('payments')
            ->orderBy('due_date', 'desc')
            ->paginate(20);

        return response()->json($bills);
    }

    /**
     * Pay a bill
     */
    public function payBill(Request $request, Bill $bill)
    {
        // Ensure user owns the bill
        if ($bill->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:bank_transfer,credit_card,e_wallet',
            'reference_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = Payment::create([
                'bill_id' => $bill->id,
                'user_id' => $request->user()->id,
                'amount_paid' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'status' => 'pending', // Will be updated by payment processor
                'paid_at' => now(),
            ]);

            // Create transaction record for monitoring
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'type' => 'payment',
                'description' => "Payment for bill #{$bill->id}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Check for anomalies
            $anomalies = $this->aiService->detectTransactionAnomalies($transaction);

            if (!empty($anomalies)) {
                // Flag transaction and notify admin
                $transaction->update(['is_flagged' => true]);
                $this->notificationService->sendAnomalyAlert($request->user()->id, $anomalies);

                // Block if high risk
                if (count($anomalies) > 2) {
                    $payment->update(['status' => 'blocked']);
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Payment blocked due to suspicious activity',
                        'anomalies' => $anomalies
                    ], 400);
                }
            }

            // Process payment (simplified - in real app, integrate with payment gateway)
            $payment->update(['status' => 'completed']);

            // Update bill status if fully paid
            $totalPaid = $bill->payments()->where('status', 'completed')->sum('amount_paid');
            if ($totalPaid >= $bill->amount) {
                $bill->update(['status' => 'paid']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Payment processed successfully',
                'payment' => $payment->load('bill'),
                'anomalies_detected' => !empty($anomalies) ? $anomalies : null
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Payment processing failed'], 500);
        }
    }

    /**
     * Get transaction anomalies (admin only)
     */
    public function anomalies(Request $request)
    {
        $anomalies = Transaction::where('is_flagged', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($anomalies);
    }

    /**
     * Flag a transaction
     */
    public function flagTransaction(Request $request, Transaction $transaction)
    {
        $transaction->update(['is_flagged' => true]);

        $this->notificationService->sendAnomalyAlert(
            $transaction->user_id,
            ['Manually flagged by admin']
        );

        return response()->json(['message' => 'Transaction flagged successfully']);
    }

    /**
     * Block a transaction
     */
    public function blockTransaction(Request $request, Transaction $transaction)
    {
        $transaction->update(['is_blocked' => true]);

        // Block related payment if exists
        if ($transaction->type === 'payment') {
            $payment = Payment::where('user_id', $transaction->user_id)
                ->where('paid_at', $transaction->created_at)
                ->first();

            if ($payment) {
                $payment->update(['status' => 'blocked']);
            }
        }

        return response()->json(['message' => 'Transaction blocked successfully']);
    }

    /**
     * Process payment (webhook from payment gateway)
     */
    public function processPayment(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:completed,failed,cancelled',
            'gateway_reference' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment->update([
            'status' => $request->status,
            'reference_number' => $request->gateway_reference ?? $payment->reference_number,
        ]);

        // Update bill status if payment completed
        if ($request->status === 'completed') {
            $bill = $payment->bill;
            $totalPaid = $bill->payments()->where('status', 'completed')->sum('amount_paid');

            if ($totalPaid >= $bill->amount) {
                $bill->update(['status' => 'paid']);
            }
        }

        return response()->json(['message' => 'Payment status updated']);
    }
}
