<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\AIService;
use App\Services\NotificationService;

class ProcessAIScholarshipRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:recommend-scholarships {--user_id= : Specific user ID to process} {--notify : Send notifications to users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process AI-powered scholarship recommendations for users';

    protected $aiService;
    protected $notificationService;

    public function __construct(AIService $aiService, NotificationService $notificationService)
    {
        parent::__construct();
        $this->aiService = $aiService;
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user_id');
        $shouldNotify = $this->option('notify');

        if ($userId) {
            $users = User::where('id', $userId)->get();
        } else {
            $users = User::where('role', 'student')->get();
        }

        $this->info("Processing scholarship recommendations for {$users->count()} users");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $totalRecommendations = 0;

        foreach ($users as $user) {
            $recommendations = $this->aiService->recommendScholarships($user);

            foreach ($recommendations as $rec) {
                // Check if recommendation already exists
                $existing = $user->scholarshipRecommendations()
                    ->where('scholarship_id', $rec['scholarship']->id)
                    ->first();

                if (!$existing) {
                    $recommendation = $user->scholarshipRecommendations()->create([
                        'scholarship_id' => $rec['scholarship']->id,
                        'match_score' => $rec['score'],
                        'reason' => $rec['reason'],
                        'recommended_at' => now(),
                    ]);

                    $totalRecommendations++;

                    if ($shouldNotify) {
                        $this->notificationService->sendScholarshipRecommendation(
                            $user->id,
                            $rec['scholarship']->name,
                            $rec['score']
                        );

                        $recommendation->update(['is_notified' => true]);
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Generated {$totalRecommendations} scholarship recommendations successfully!");
        return 0;
    }
}
