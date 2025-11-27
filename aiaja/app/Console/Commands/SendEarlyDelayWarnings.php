<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Bill;
use App\Services\AIService;
use App\Services\NotificationService;

class SendEarlyDelayWarnings extends Command
{
	protected $signature = 'ai:early-warnings {--threshold=60 : Risk score threshold (0-100)} {--channel=email : Notification channel}';
	protected $description = 'Send early warnings to users predicted to be late in payment';

	protected $aiService;
	protected $notificationService;

	public function __construct(AIService $aiService, NotificationService $notificationService)
	{
		parent::__construct();
		$this->aiService = $aiService;
		$this->notificationService = $notificationService;
	}

	public function handle()
	{
		$threshold = (int) $this->option('threshold');
		$channel = $this->option('channel');

		$users = User::where('role', 'student')->get();
		$this->info('Evaluating delay risk for ' . $users->count() . ' users');

		$bar = $this->output->createProgressBar($users->count());
		$bar->start();

		$sent = 0;
		foreach ($users as $user) {
			$risk = $this->aiService->predictPaymentDelays($user);
			if ($risk >= $threshold) {
				// Find nearest upcoming unpaid bill
				$bill = Bill::where('user_id', $user->id)
					->where('status', 'pending')
					->orderBy('due_date', 'asc')
					->first();
				if ($bill) {
					$message = "Peringatan dini: Risiko telat bayar {$risk}% untuk tagihan {$bill->type} jatuh tempo {$bill->due_date->format('Y-m-d')}. Mohon lakukan pembayaran tepat waktu.";
					$this->notificationService->sendNotification($user->id, 'early_warning', $message, ['bill_id' => $bill->id, 'risk' => $risk], $channel);
					$sent++;
				}
			}
			$bar->advance();
		}

		$bar->finish();
		$this->newLine();
		$this->info("Early warnings sent: {$sent}");
		return 0;
	}
}


