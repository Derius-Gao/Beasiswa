<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bill;
use App\Models\Reminder;
use App\Services\NotificationService;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send {--days=7 : Days before due date to send reminder} {--channel=email : Notification channel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders to users with upcoming due dates';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $channel = $this->option('channel');
        $dueDate = now()->addDays($days);

        $this->info("Sending {$channel} reminders for bills due in {$days} days");

        // Get bills due within the specified days
        $bills = Bill::where('status', 'pending')
            ->where('due_date', '>=', now()->toDateString())
            ->where('due_date', '<=', $dueDate->toDateString())
            ->with('user')
            ->get();

        $bar = $this->output->createProgressBar($bills->count());
        $bar->start();

        $sentCount = 0;
        foreach ($bills as $bill) {
            // Check if reminder already sent for this bill
            $existingReminder = Reminder::where('bill_id', $bill->id)
                ->where('remind_date', now()->toDateString())
                ->first();

            if (!$existingReminder) {
                $success = $this->notificationService->sendPaymentReminder(
                    $bill->id,
                    $bill->user_id,
                    $channel
                );

                if ($success) {
                    $sentCount++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Sent {$sentCount} reminders successfully!");
        return 0;
    }
}
