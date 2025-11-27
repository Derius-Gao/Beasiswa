<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Reminder;
// use Twilio\Rest\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $twilio;

    public function __construct()
    {
        // $this->twilio = new Client(
        //     config('services.twilio.sid'),
        //     config('services.twilio.token')
        // );
        $this->twilio = null; // Disable Twilio for now
    }

    /**
     * Send notification to user
     */
    public function sendNotification(int $userId, string $type, string $message, array $data = [], string $channel = 'app'): bool
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'channel' => $channel,
                'message' => $message,
                'data' => $data,
                'sent_at' => now(),
            ]);

            // Send via specified channel
            switch ($channel) {
                case 'email':
                    $this->sendEmail($userId, $message, $data);
                    break;
                case 'whatsapp':
                    $this->sendWhatsApp($userId, $message);
                    break;
                case 'sms':
                    $this->sendSMS($userId, $message);
                    break;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment reminder
     */
    public function sendPaymentReminder(int $billId, int $userId, string $channel = 'email'): bool
    {
        $bill = \App\Models\Bill::find($billId);
        if (!$bill) return false;

        $message = "Reminder: Your payment of {$bill->amount} for {$bill->type} is due on {$bill->due_date->format('Y-m-d')}.";

        Reminder::create([
            'bill_id' => $billId,
            'user_id' => $userId,
            'type' => 'payment_due',
            'channel' => $channel,
            'message' => $message,
            'remind_date' => now()->toDateString(),
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        return $this->sendNotification($userId, 'reminder', $message, ['bill_id' => $billId], $channel);
    }

    /**
     * Send anomaly alert
     */
    public function sendAnomalyAlert(int $userId, array $anomalies): bool
    {
        $message = "Suspicious activity detected: " . implode(', ', $anomalies) . ". Please verify your account.";

        return $this->sendNotification($userId, 'anomaly', $message, ['anomalies' => $anomalies], 'email');
    }

    /**
     * Send scholarship recommendation
     */
    public function sendScholarshipRecommendation(int $userId, string $scholarshipName, float $matchScore): bool
    {
        $message = "Great news! You match {$matchScore}% with the {$scholarshipName} scholarship. Check your dashboard for details.";

        return $this->sendNotification($userId, 'scholarship', $message, [
            'scholarship_name' => $scholarshipName,
            'match_score' => $matchScore
        ], 'app');
    }

    private function sendEmail(int $userId, string $message, array $data): void
    {
        $user = \App\Models\User::find($userId);
        if (!$user || !$user->email) return;

        // In a real app, you'd create a proper Mailable class
        Mail::raw($message, function ($mail) use ($user) {
            $mail->to($user->email)->subject('Payment System Notification');
        });
    }

    private function sendWhatsApp(int $userId, string $message): void
    {
        $user = \App\Models\User::find($userId);
        if (!$user || !$user->phone) return;

        // Disabled for now - would use Twilio
        Log::info("WhatsApp message to {$user->phone}: {$message}");
        // try {
        //     $this->twilio->messages->create(
        //         "whatsapp:{$user->phone}",
        //         [
        //             'from' => config('services.twilio.whatsapp_number'),
        //             'body' => $message
        //         ]
        //     );
        // } catch (\Exception $e) {
        //     Log::error('WhatsApp send failed: ' . $e->getMessage());
        // }
    }

    private function sendSMS(int $userId, string $message): void
    {
        $user = \App\Models\User::find($userId);
        if (!$user || !$user->phone) return;

        // Disabled for now - would use Twilio
        Log::info("SMS message to {$user->phone}: {$message}");
        // try {
        //     $this->twilio->messages->create(
        //         $user->phone,
        //         [
        //             'from' => config('services.twilio.phone_number'),
        //             'body' => $message
        //         ]
        //     );
        // } catch (\Exception $e) {
        //     Log::error('SMS send failed: ' . $e->getMessage());
        // }
    }
}
