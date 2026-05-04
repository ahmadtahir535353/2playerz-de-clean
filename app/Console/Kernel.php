<?php

namespace App\Console;

use App\Console\Commands\SchedulePost;
use App\Models\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SchedulePost::class,
         \App\Console\Commands\PublishScheduledPosts::class,
         \App\Console\Commands\SendDailyNewsletter::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('schedule-post')->everyMinute();
        $schedule->command('send-subscriber-emails')->everyMinute();
        $schedule->command('post:delete')->daily();
         $schedule->command('newsletter:send')
             ->timezone('Asia/Karachi') 
             ->everyMinute();
        
        // Sync analytics count to posts.views_count every hour for performance
        $schedule->command('posts:sync-views-count')->hourly();

        $value = getSettingValue()['rss_feed_update_time'];
        if ($value == Setting::EVERY_3_HOURS) {
            $schedule->command('sync:rss-feed')->everyThreeHours();
        } elseif ($value == Setting::TWICE_A_DAY) {
            $schedule->command('sync:rss-feed')->twiceDaily();
        } elseif ($value == Setting::EVERY_DAY) {
            $schedule->command('sync:rss-feed')->daily();
        } elseif ($value == Setting::WEEKLY) {
            $schedule->command('sync:rss-feed')->weekly();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
