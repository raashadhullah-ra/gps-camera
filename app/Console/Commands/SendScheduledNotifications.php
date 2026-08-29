<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send all scheduled push notifications whose delivery time has arrived';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Checking for due scheduled notification campaigns...');

        $processed = $notificationService->processDueScheduledNotifications();

        $this->info("Successfully processed {$processed} scheduled notification campaign(s).");

        return Command::SUCCESS;
    }
}
