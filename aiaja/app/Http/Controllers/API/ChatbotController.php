<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\ChatbotLog;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Send message to chatbot
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'language' => 'nullable|string|in:auto,id,en,both',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $response = $this->aiService->generateChatbotResponse(
            $request->message,
            $request->user(),
            $request->input('language')
        );

        // Jika response kosong atau placeholder, ganti dengan jawaban default
        if (empty($response) || stripos($response, 'feature will be fully implemented soon') !== false) {
            $response = "Maaf, fitur AI belum aktif sepenuhnya. Silakan tanyakan tentang pembayaran, beasiswa, atau profil, dan saya akan mencoba membantu sebisanya.";
        }

        return response()->json([
            'success' => true,
            'response' => $response,
            'timestamp' => now(),
            'language' => $request->input('language', config('ai.chatbot.default_language', 'auto')),
        ]);
    }

    /**
     * Public chatbot API (external integrations)
     */
    public function publicMessage(Request $request)
    {
        $apiKey = $request->header('X-Chatbot-Key') ?? $request->input('api_key');
        $expectedKey = config('ai.chatbot.public_api_key');

        if (empty($expectedKey) || !hash_equals($expectedKey, (string) $apiKey)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string|max:120',
            'user_identifier' => 'nullable|string|max:191',
            'channel' => 'nullable|string|max:50',
            'language' => 'nullable|string|in:auto,id,en,both',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sessionId = $request->input('session_id') ?: Str::uuid()->toString();
        $channel = $request->input('channel', config('ai.chatbot.default_channel', 'api'));
        $user = $this->resolveUser($request->input('user_identifier'));

        $reply = $this->aiService->generateChatbotResponse($request->message, $user, $request->input('language'));

        if (empty($reply)) {
            $reply = "Maaf, server chatbot sedang sibuk. Silakan ulangi pertanyaan Anda beberapa saat lagi.";
        }

        $insights = $this->buildContextualInsights($user, $request->message);

        ChatbotLog::create([
            'user_id' => $user?->id,
            'session_id' => $sessionId,
            'channel' => $channel,
            'request_payload' => [
                'message' => $request->message,
                'user_identifier' => $request->input('user_identifier'),
            ],
            'response_text' => $reply,
            'metadata' => [
                'insights' => $insights,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'language' => $request->input('language'),
            ],
        ]);

        return response()->json([
            'success' => true,
            'reply' => $reply,
            'session_id' => $sessionId,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ] : null,
            'insights' => $insights,
            'language' => $request->input('language', config('ai.chatbot.default_language', 'auto')),
        ]);
    }

    protected function resolveUser(?string $identifier): ?User
    {
        if (empty($identifier)) {
            return null;
        }

        if (is_numeric($identifier)) {
            return User::find((int) $identifier);
        }

        return User::where('email', $identifier)
            ->orWhere('student_id', $identifier)
            ->first();
    }

    protected function buildContextualInsights(?User $user, string $message): array
    {
        $insights = [];
        $lower = strtolower($message);

        if ($user) {
            $riskScore = round($this->aiService->predictPaymentDelays($user), 2);
            $insights['delay_risk'] = [
                'score' => $riskScore,
                'level' => $riskScore >= 70 ? 'high' : ($riskScore >= 40 ? 'medium' : 'low'),
            ];

            if (str_contains($lower, 'tagihan') || str_contains($lower, 'bill')) {
                $bills = Bill::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->orderBy('due_date')
                    ->limit(3)
                    ->get(['id', 'type', 'amount', 'due_date', 'status'])
                    ->map(function (Bill $bill) {
                        return [
                            'id' => $bill->id,
                            'type' => $bill->type,
                            'amount' => $bill->amount,
                            'due_date' => optional($bill->due_date)->toDateString(),
                            'status' => $bill->status,
                        ];
                    })
                    ->values();

                if ($bills->isNotEmpty()) {
                    $insights['pending_bills'] = $bills->all();
                }
            }

            if (str_contains($lower, 'beasiswa') || str_contains($lower, 'scholarship')) {
                $recommendations = collect($this->aiService->recommendScholarships($user))
                    ->take(3)
                    ->map(function ($rec) {
                        return [
                            'name' => $rec['scholarship']->name,
                            'score' => $rec['score'],
                            'reason' => $rec['reason'],
                        ];
                    })
                    ->values();

                if ($recommendations->isNotEmpty()) {
                    $insights['scholarship_recommendations'] = $recommendations->all();
                }
            }
        }

        return $insights;
    }
}
