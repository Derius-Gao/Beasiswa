<?php

namespace App\Services;

use GuzzleHttp\Client as HttpClient;
// use OpenAI\Client;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Scholarship;
use App\Models\ScholarshipRecommendation;
use Illuminate\Support\Facades\Config;

class AIService
{
    protected $openai;
    protected $httpClient;
		protected $provider;
		protected $ollamaBaseUrl;
		protected $ollamaChatModel;
		protected $ollamaEmbedModel;
		protected $ollamaTimeout;
		protected $openRouterBaseUrl;
		protected $openRouterChatModel;
		protected $openRouterTimeout;
		protected $openRouterKey;
		protected $defaultLanguage;

    public function __construct()
    {
        // $this->openai = new Client(config('services.openai.api_key'));
        $this->openai = null; // Disable OpenAI for now
			$this->httpClient = new HttpClient(['http_errors' => false]);

			$this->provider = Config::get('ai.provider', 'ollama');
			$this->ollamaBaseUrl = rtrim(Config::get('ai.ollama.base_url', 'http://127.0.0.1:11434'), '/');
			$this->ollamaChatModel = Config::get('ai.ollama.chat_model', 'llama3.1');
			$this->ollamaEmbedModel = Config::get('ai.ollama.embed_model', 'nomic-embed-text');
			$this->ollamaTimeout = (int) Config::get('ai.ollama.timeout', 20);
			$this->openRouterBaseUrl = rtrim(Config::get('ai.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
			$this->openRouterChatModel = Config::get('ai.openrouter.chat_model', 'mistralai/mixtral-8x7b-instruct');
			$this->openRouterTimeout = (int) Config::get('ai.openrouter.timeout', 30);
			$this->openRouterKey = Config::get('ai.openrouter.api_key');
			$this->defaultLanguage = Config::get('ai.chatbot.default_language', 'auto');
    }

    /**
     * Detect anomalies in transactions
     */
		public function detectTransactionAnomalies(Transaction $transaction): array
		{
			$anomalies = [];

			// Historical stats for user
			$history = Transaction::where('user_id', $transaction->user_id)
				->where('id', '!=', $transaction->id)
				->where('status', '!=', 'failed')
				->pluck('amount');

			$avgAmount = $history->avg() ?? 0.0;
			$stdAmount = 0.0;
			if ($history->count() > 1) {
				$mean = $avgAmount;
				$variance = $history->map(function ($a) use ($mean) {
					return pow(((float) $a) - (float) $mean, 2);
				})->avg();
				$stdAmount = sqrt($variance);
			}

			// 1) High-amount vs user average (z-score > 2 or > 2x avg)
			if ($avgAmount > 0) {
				$z = $stdAmount > 0 ? ((float) $transaction->amount - $avgAmount) / $stdAmount : 0;
				if ($transaction->amount >= ($avgAmount * 2)) {
					$anomalies[] = 'Unusual high amount vs user average';
				} elseif ($z >= 2.0) {
					$anomalies[] = 'Amount spike (z>2)';
				}
			}

			// 2) New IP for this user
			if (!empty($transaction->ip_address)) {
				$seenIp = Transaction::where('user_id', $transaction->user_id)
					->where('ip_address', $transaction->ip_address)
					->exists();
				if (!$seenIp) {
					$anomalies[] = 'New login IP';
				}
			}

			// 3) Device/user-agent changed
			if (!empty($transaction->user_agent)) {
				$seenUa = Transaction::where('user_id', $transaction->user_id)
					->where('user_agent', $transaction->user_agent)
					->exists();
				if (!$seenUa) {
					$anomalies[] = 'New device/user-agent';
				}
			}

			// Optional: lightweight LLM opinion if available (adds context, not decisive)
			if ($this->provider === 'ollama' && !empty($anomalies)) {
				$messages = [
					['role' => 'system', 'content' => 'Anda memeriksa anomali transaksi. Jawab satu kalimat singkat.'],
					['role' => 'user', 'content' => 'Amount=' . $transaction->amount . ', anomalies=' . implode('|', $anomalies)],
				];
				$opinion = $this->chatWithOllama($messages);
				if ($opinion) {
					$anomalies[] = 'LLM note: ' . mb_strimwidth($opinion, 0, 140, '…');
				}
			}

			return $anomalies;
		}

    /**
     * Predict payment delays
     */
    public function predictPaymentDelays(User $user): float
    {
        $history = $user->payments()->with('bill')->get();

        // Simple scoring based on history
        $latePayments = $history->filter(function ($payment) {
            return $payment->paid_at > $payment->bill->due_date;
        })->count();

        $totalPayments = $history->count();
        $delayRisk = $totalPayments > 0 ? ($latePayments / $totalPayments) * 100 : 0;

        // Use OpenAI for prediction (disabled for now)
        // $prompt = "Based on payment history, predict delay risk for user with {$latePayments} late payments out of {$totalPayments} total.";
        // $aiPrediction = $this->analyzeWithOpenAI($prompt);

        // Combine simple scoring with AI
        return min(100, $delayRisk); // + (str_contains(strtolower($aiPrediction), 'high') ? 20 : 0));
    }

    /**
     * Recommend scholarships
     */
    public function recommendScholarships(User $user): array
    {
        $scholarships = Scholarship::where('is_active', true)->get();
        $recommendations = [];

        foreach ($scholarships as $scholarship) {
            $matchScore = $this->calculateMatchScore($user, $scholarship);

				if ($matchScore > 50) { // Threshold for recommendation
					$reason = $this->generateRecommendationReason($user, $scholarship);
					// Optionally enrich reason with LLM (short one-liner)
					if ($this->provider === 'ollama') {
						$messages = [
							['role' => 'system', 'content' => 'Jelaskan singkat (<20 kata) mengapa beasiswa cocok.'],
							['role' => 'user', 'content' => 'User: GPA=' . $user->gpa . ', Ekonomi=' . $user->economic_status . '; Beasiswa: ' . $scholarship->name],
						];
						$extra = $this->chatWithOllama($messages);
						if ($extra) {
							$reason = trim($reason . '. ' . mb_strimwidth($extra, 0, 80, '…'));
						}
					}

					$recommendations[] = [
						'scholarship' => $scholarship,
						'score' => $matchScore,
						'reason' => $reason,
					];
				}
        }

        return $recommendations;
    }

		/**
		 * Chatbot response
		 */
		public function generateChatbotResponse(string $message, User $user = null, string $language = null): string
		{
			$language = $this->normalizeLanguage($language ?? $this->defaultLanguage);

			// Try LLM via configured provider if available
			$context = $user ? "Nama: {$user->name}; GPA: {$user->gpa}; Ekonomi: {$user->economic_status}; Prodi: {$user->major}" : '';
			$system = $this->buildSystemPrompt($language);
			$messages = [
				['role' => 'system', 'content' => $system],
				['role' => 'user', 'content' => trim($context . "\nPertanyaan: " . $message)],
			];

			if ($this->provider === 'ollama') {
				$ai = $this->chatWithOllama($messages);
				if ($ai !== null) {
					return $ai;
				}
			}

			if ($this->provider === 'openrouter' && $this->openRouterKey) {
				$ai = $this->chatWithOpenRouter($messages);
				if ($ai !== null) {
					return $ai;
				}
			}

			// Try LLM via Ollama if available
			// Fallback: simple rule-based responses
			$lower = strtolower($message);
			$response = "Halo! Saya adalah asisten AI untuk sistem pembayaran universitas. Bagaimana saya bisa membantu Anda hari ini?";
			if (str_contains($lower, 'tagihan') || str_contains($lower, 'bill')) {
				$response = "Untuk melihat atau membayar tagihan, buka menu 'Tagihan Saya'.";
			} elseif (str_contains($lower, 'beasiswa') || str_contains($lower, 'scholarship')) {
				$response = "Rekomendasi beasiswa akan tampil di profil Anda berdasarkan kecocokan data akademik.";
			} elseif (str_contains($lower, 'pembayaran') || str_contains($lower, 'payment')) {
				$response = "Anda dapat melakukan pembayaran melalui menu 'Bayar Tagihan' atau hubungi admin keuangan.";
			}

			return $this->formatFallbackResponse($response, $language);
		}

		public function checkHealth(): array
		{
			$status = ['provider' => $this->provider, 'ok' => true, 'details' => []];
			try {
				if ($this->provider === 'ollama') {
					$resp = $this->httpClient->get($this->ollamaBaseUrl . '/api/tags', [
						'timeout' => $this->ollamaTimeout,
					]);
					$status['details']['chat_model'] = $this->ollamaChatModel;
					$status['ok'] = $resp->getStatusCode() === 200;
				} elseif ($this->provider === 'openrouter') {
					$status['details']['chat_model'] = $this->openRouterChatModel;
					$status['details']['key_loaded'] = !empty($this->openRouterKey);
					$status['ok'] = !empty($this->openRouterKey);
				}
			} catch (\Throwable $e) {
				$status['ok'] = false;
				$status['error'] = $e->getMessage();
			}
			return $status;
		}

		private function chatWithOllama(array $messages): ?string
		{
			try {
				$resp = $this->httpClient->post($this->ollamaBaseUrl . '/api/chat', [
					'timeout' => $this->ollamaTimeout,
					'headers' => ['Content-Type' => 'application/json'],
					'json' => [
						'model' => $this->ollamaChatModel,
						'messages' => $messages,
						'stream' => false,
					],
				]);
				if ($resp->getStatusCode() !== 200) {
					return null;
				}
				$body = json_decode((string) $resp->getBody(), true);
				if (!is_array($body)) {
					return null;
				}
				if (isset($body['message']) && is_array($body['message']) && isset($body['message']['content'])) {
					return (string) $body['message']['content'];
				}
				return null;
			} catch (\Throwable $e) {
				return null;
			}
		}

		private function chatWithOpenRouter(array $messages): ?string
		{
			try {
				$resp = $this->httpClient->post($this->openRouterBaseUrl . '/chat/completions', [
					'timeout' => $this->openRouterTimeout,
					'headers' => [
						'Content-Type' => 'application/json',
						'Authorization' => 'Bearer ' . $this->openRouterKey,
					],
					'json' => [
						'model' => $this->openRouterChatModel,
						'messages' => $messages,
					],
				]);
				if ($resp->getStatusCode() !== 200) {
					return null;
				}
				$body = json_decode((string) $resp->getBody(), true);
				if (!is_array($body) || empty($body['choices'][0]['message']['content'])) {
					return null;
				}
				return (string) $body['choices'][0]['message']['content'];
			} catch (\Throwable $e) {
				return null;
			}
		}

		private function buildSystemPrompt(string $language): string
		{
			$base = 'Anda adalah asisten AI untuk sistem pembayaran kampus. Jawab singkat, akurat, dan sopan.';
			switch ($language) {
				case 'en':
					return $base . ' Respond in English.';
				case 'both':
					return $base . ' Respond twice: first in Indonesian, then provide the English translation prefixed with "English:".';
				case 'auto':
					return $base . ' Respond using the same language as the user. If unclear, default to Indonesian.';
				case 'id':
				default:
					return $base . ' Gunakan bahasa Indonesia.';
			}
		}

		private function normalizeLanguage(string $language): string
		{
			$language = strtolower($language);
			$allowed = ['id', 'en', 'both', 'auto'];
			return in_array($language, $allowed, true) ? $language : 'auto';
		}

		private function formatFallbackResponse(string $text, string $language): string
		{
			if ($language === 'en') {
				return $this->simpleTranslateToEnglish($text);
			}
			if ($language === 'both') {
				$english = $this->simpleTranslateToEnglish($text);
				return $text . PHP_EOL . 'English: ' . $english;
			}
			return $text;
		}

		private function simpleTranslateToEnglish(string $text): string
		{
			$dictionary = [
				"Untuk melihat atau membayar tagihan, buka menu 'Tagihan Saya'." => "To view or pay your invoices, open the 'My Bills' menu.",
				"Rekomendasi beasiswa akan tampil di profil Anda berdasarkan kecocokan data akademik." => "Scholarship recommendations will appear on your profile based on academic matching.",
				"Anda dapat melakukan pembayaran melalui menu 'Bayar Tagihan' atau hubungi admin keuangan." => "You can make a payment via the 'Pay Bill' menu or contact the finance admin.",
				"Halo! Saya adalah asisten AI untuk sistem pembayaran universitas. Bagaimana saya bisa membantu Anda hari ini?" => "Hello! I am the AI assistant for the university payment system. How can I help you today?",
			];
			return $dictionary[$text] ?? $text;
		}

    private function isSuspiciousIP(string $ip): bool
    {
        // Simple check - in real app, use IP geolocation service
        return false; // Placeholder
    }

    private function analyzeWithOpenAI(string $prompt): string
    {
        if (!$this->openai) {
            return 'AI analysis unavailable.';
        }

        try {
            $response = $this->openai->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 150
            ]);

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            return 'Unable to analyze at this time.';
        }
    }

		private function calculateMatchScore(User $user, Scholarship $scholarship): float
    {
        $score = 0;
			$criteria = (array) $scholarship->criteria;

        if (isset($criteria['min_gpa']) && $user->gpa >= $criteria['min_gpa']) {
            $score += 30;
        }

        if (isset($criteria['economic_status']) && $user->economic_status === $criteria['economic_status']) {
            $score += 40;
        }

        if (isset($criteria['major']) && $user->major === $criteria['major']) {
            $score += 30;
        }

        return $score;
    }

		private function generateRecommendationReason(User $user, Scholarship $scholarship): string
    {
        $reasons = [];
			$criteria = (array) $scholarship->criteria;

			if ($user->gpa >= ($criteria['min_gpa'] ?? 0)) {
            $reasons[] = "Your GPA of {$user->gpa} meets the requirement";
        }

			if ($user->economic_status === ($criteria['economic_status'] ?? '')) {
            $reasons[] = "Matches your economic status";
        }

        return implode(', ', $reasons);
    }
}
