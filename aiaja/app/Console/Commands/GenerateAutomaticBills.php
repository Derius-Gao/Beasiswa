<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Bill;
use Carbon\Carbon;

class GenerateAutomaticBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:generate {--type= : Type of bill to generate (spp, kegiatan, cicilan)} {--month= : Month for SPP bills}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate automatic bills for users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type') ?? 'spp';
        $month = $this->option('month') ?? now()->format('Y-m');

        $this->info("Generating {$type} bills for {$month}");

        switch ($type) {
            case 'spp':
                $this->generateSPPBills($month);
                break;
            case 'kegiatan':
                $this->generateActivityBills();
                break;
            case 'cicilan':
                $this->generateInstallmentBills();
                break;
            default:
                $this->error("Unknown bill type: {$type}");
                return 1;
        }

        $this->info('Bill generation completed successfully!');
        return 0;
    }

    private function generateSPPBills(string $month)
    {
        $users = User::where('role', 'student')->get();
        $dueDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            // Check if bill already exists for this month
            $existingBill = Bill::where('user_id', $user->id)
                ->where('type', 'SPP')
                ->whereYear('due_date', $dueDate->year)
                ->whereMonth('due_date', $dueDate->month)
                ->first();

            if (!$existingBill) {
                Bill::create([
                    'user_id' => $user->id,
                    'type' => 'SPP',
                    'amount' => $this->calculateSPPAmount($user),
                    'due_date' => $dueDate,
                    'status' => 'pending',
                    'description' => "SPP for {$month}",
                    'is_auto_generated' => true,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function generateActivityBills()
    {
        // Generate activity bills (kegiatan) - assuming quarterly
        $users = User::where('role', 'student')->get();
        $currentQuarter = ceil(now()->month / 3);
        $dueDate = now()->endOfQuarter();

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $existingBill = Bill::where('user_id', $user->id)
                ->where('type', 'Kegiatan')
                ->whereYear('due_date', $dueDate->year)
                ->whereRaw('QUARTER(due_date) = ?', [$currentQuarter])
                ->first();

            if (!$existingBill) {
                Bill::create([
                    'user_id' => $user->id,
                    'type' => 'Kegiatan',
                    'amount' => 500000, // Fixed amount for activities
                    'due_date' => $dueDate,
                    'status' => 'pending',
                    'description' => "Activity fee Q{$currentQuarter} {$dueDate->year}",
                    'is_auto_generated' => true,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function generateInstallmentBills()
    {
        // Generate installment bills for users with outstanding balances
        $users = User::where('role', 'student')
            ->where('outstanding_balance', '>', 0)
            ->get();

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $installmentAmount = min($user->outstanding_balance, 1000000); // Max 1M per installment

            Bill::create([
                'user_id' => $user->id,
                'type' => 'Cicilan',
                'amount' => $installmentAmount,
                'due_date' => now()->addDays(30),
                'status' => 'pending',
                'description' => "Installment payment - Remaining: " . ($user->outstanding_balance - $installmentAmount),
                'is_auto_generated' => true,
            ]);

            // Update outstanding balance
            $user->update(['outstanding_balance' => $user->outstanding_balance - $installmentAmount]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function calculateSPPAmount(User $user): float
    {
        // Base SPP amount
        $baseAmount = 1500000; // 1.5M

        // Adjust based on scholarship or economic status
        if ($user->economic_status === 'low_income') {
            $baseAmount *= 0.8; // 20% discount
        }

        // Check for active scholarships
        $activeScholarship = $user->scholarshipRecommendations()
            ->where('is_notified', true)
            ->whereHas('scholarship', function ($query) {
                $query->where('is_active', true);
            })
            ->first();

        if ($activeScholarship) {
            $discount = $activeScholarship->scholarship->amount / 12; // Monthly discount
            $baseAmount -= $discount;
        }

        return max($baseAmount, 0);
    }
}
