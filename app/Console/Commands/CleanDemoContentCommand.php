<?php

namespace App\Console\Commands;

use App\Services\DemoCleanupService;
use Illuminate\Console\Command;

class CleanDemoContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:clean {--minutes=3 : Age in minutes to delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean demo-created content older than 3 minutes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutes = (int) $this->option('minutes') ?: 3;
        $count = DemoCleanupService::cleanExpired($minutes);
        $this->info("Successfully cleaned {$count} expired demo content items.");
        return 0;
    }
}
