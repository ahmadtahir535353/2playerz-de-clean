<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPostViewsCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:sync-views-count {--force : Force sync all posts even if views_count is already set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync analytics count to posts.views_count column for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to sync post views count...');
        
        $startTime = microtime(true);
        
        // Efficient bulk update using a single query with JOIN
        // This is much faster than updating each post individually
        $query = "
            UPDATE posts 
            LEFT JOIN (
                SELECT post_id, COUNT(*) as analytics_count 
                FROM analytics 
                GROUP BY post_id
            ) as analytics_data ON posts.id = analytics_data.post_id
            SET posts.views_count = COALESCE(analytics_data.analytics_count, 0)
        ";
        
        // Only update posts where views_count is different (unless --force is used)
        if (!$this->option('force')) {
            $query .= " WHERE posts.views_count != COALESCE(analytics_data.analytics_count, 0) OR posts.views_count IS NULL";
        }
        
        $affectedRows = DB::update($query);
        
        $duration = round(microtime(true) - $startTime, 2);
        
        $this->info("✓ Successfully synced {$affectedRows} posts in {$duration} seconds");
        
        return Command::SUCCESS;
    }
}
