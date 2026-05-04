<?php

namespace App\Console\Commands;

use App\Models\DailyStat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillDailyStatsFromAnalytics extends Command
{
    protected $signature = 'daily-stats:backfill-from-analytics {--from= : Start date (Y-m-d)} {--to= : End date (Y-m-d)}';

    protected $description = 'Backfill daily_stats table from analytics table so graphs show old data';

    public function handle(): int
    {
        if (! Schema::hasTable('analytics') || ! Schema::hasTable('daily_stats')) {
            $this->error('Required tables analytics and daily_stats do not exist.');
            return self::FAILURE;
        }

        $this->info('Aggregating analytics by date (one pass over analytics table)...');

        $driver = DB::getDriverName();
        $dateExpr = $driver === 'sqlite' ? "date(created_at)" : "DATE(created_at)";

        $rows = DB::table('analytics')
            ->selectRaw("{$dateExpr} as stat_date, COUNT(*) as post_views, COUNT(DISTINCT ip) as unique_visitors")
            ->groupByRaw($dateExpr)
            ->orderByRaw($dateExpr)
            ->get();

        if ($rows->isEmpty()) {
            $this->warn('No analytics data found.');
            return self::SUCCESS;
        }

        $this->info('Upserting ' . $rows->count() . ' days into daily_stats...');

        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        foreach ($rows as $row) {
            $dateStr = $row->stat_date;
            if (is_object($dateStr)) {
                $dateStr = Carbon::parse($dateStr)->toDateString();
            }

            DailyStat::updateOrCreate(
                ['date' => $dateStr],
                [
                    'post_views' => (int) $row->post_views,
                    'unique_visitors' => (int) $row->unique_visitors,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Backfill complete. Graphs will now show historical data from analytics.');

        return self::SUCCESS;
    }
}
