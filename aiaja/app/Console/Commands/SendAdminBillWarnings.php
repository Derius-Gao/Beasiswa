<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SendAdminBillWarnings extends Command
{
    protected $signature = 'bills:warn-admin {--days=3 : Kirim peringatan untuk tagihan yang akan jatuh tempo dalam X hari}';

    protected $description = 'Mengirim ringkasan tagihan jatuh tempo dan overdue ke admin melalui email';

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $today = now()->startOfDay();

        $dueSoon = Bill::with('user')
            ->where('status', 'unpaid')
            ->whereBetween('due_date', [$today, $today->copy()->addDays($days)])
            ->orderBy('due_date')
            ->get();

        $overdue = Bill::with('user')
            ->whereIn('status', ['unpaid', 'overdue'])
            ->where('due_date', '<', $today)
            ->orderBy('due_date')
            ->get();

        if ($dueSoon->isEmpty() && $overdue->isEmpty()) {
            $this->info('Tidak ada tagihan yang perlu diperingatkan.');
            return self::SUCCESS;
        }

        $admins = User::whereIn('level', ['admin', 'superadmin'])->get();
        if ($admins->isEmpty()) {
            $this->warn('Tidak ada admin yang terdaftar untuk menerima notifikasi.');
            return self::SUCCESS;
        }

        $message = $this->composeMessage($dueSoon, $overdue, $days);

        foreach ($admins as $admin) {
            $this->notificationService->sendNotification(
                $admin->id,
                'admin_bill_warning',
                $message,
                [
                    'due_soon_count' => $dueSoon->count(),
                    'overdue_count' => $overdue->count(),
                    'look_ahead_days' => $days,
                ],
                'email'
            );
        }

        $this->info('Peringatan berhasil dikirim ke ' . $admins->count() . ' admin.');
        return self::SUCCESS;
    }

    private function composeMessage(Collection $dueSoon, Collection $overdue, int $days): string
    {
        $lines = [];

        if ($dueSoon->isNotEmpty()) {
            $lines[] = "Tagihan jatuh tempo dalam {$days} hari:";
            foreach ($dueSoon->take(5) as $bill) {
                $lines[] = sprintf(
                    '- %s (%s) jatuh tempo %s, Rp %s',
                    $bill->type,
                    $bill->user->name ?? 'Tanpa Nama',
                    $bill->due_date->format('d M Y'),
                    number_format($bill->amount, 0, ',', '.')
                );
            }
            if ($dueSoon->count() > 5) {
                $lines[] = sprintf('+ %d tagihan lainnya.', $dueSoon->count() - 5);
            }
            $lines[] = '';
        }

        if ($overdue->isNotEmpty()) {
            $lines[] = 'Tagihan yang sudah lewat jatuh tempo:';
            foreach ($overdue->take(5) as $bill) {
                $lines[] = sprintf(
                    '- %s (%s) terlambat sejak %s, status: %s',
                    $bill->type,
                    $bill->user->name ?? 'Tanpa Nama',
                    $bill->due_date->format('d M Y'),
                    $bill->status
                );
            }
            if ($overdue->count() > 5) {
                $lines[] = sprintf('+ %d tagihan lainnya.', $overdue->count() - 5);
            }
        }

        $lines[] = '';
        $lines[] = 'Silakan cek dashboard admin untuk detail lengkap.';

        return implode("\n", $lines);
    }
}


