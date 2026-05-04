<?php

namespace App\Console\Commands;

use App\Models\BingWebmasterToken;
use App\Services\BingWebmasterService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class FetchBingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bing:fetch-data {--days=90 : Number of days to fetch data for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Bing Webmaster Tools data for all active tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Bing Webmaster data fetch...');

        // Get all active tokens
        $tokens = BingWebmasterToken::where('is_active', true)->get();

        if ($tokens->isEmpty()) {
            $this->warn('No active Bing Webmaster tokens found. Please configure Bing Webmaster first.');
            return Command::FAILURE;
        }

        $days = (int) $this->option('days');
        $service = new BingWebmasterService();

        $successCount = 0;
        $failureCount = 0;

        foreach ($tokens as $token) {
            $this->info("Processing token for user: {$token->user_id} (Site: {$token->site_url})");

            try {
                // Decrypt API key
                try {
                    $apiKey = Crypt::decryptString($token->api_key);
                } catch (\Exception $e) {
                    $this->error("Failed to decrypt API key for token ID: {$token->id}");
                    Log::error('Bing Fetch Command: Decryption error', [
                        'token_id' => $token->id,
                        'error' => $e->getMessage()
                    ]);
                    $failureCount++;
                    continue;
                }

                // Create a temporary token with decrypted API key for service methods
                $tempToken = new BingWebmasterToken([
                    'id' => $token->id,
                    'user_id' => $token->user_id,
                    'api_key' => $apiKey,
                    'site_url' => $token->site_url,
                    'is_active' => $token->is_active,
                ]);
                $tempToken->id = $token->id; // Ensure ID is set

                // Fetch data for the specified date range
                $endDate = Carbon::now()->format('Y-m-d');
                $startDate = Carbon::now()->subDays($days)->format('Y-m-d');

                $this->info("Fetching data from {$startDate} to {$endDate}...");

                // FIRST: Fetch overall site statistics (complete data like dashboard)
                $this->info("Fetching overall site statistics...");
                $rankStats = $service->fetchRankAndTrafficStats($tempToken);
                
                if (!empty($rankStats)) {
                    // Store rank/traffic data which gives us the complete overview
                    $service->storeRankAndTrafficData($token, $rankStats, Carbon::now()->format('Y-m-d'));
                    $this->info("✓ Stored overall site statistics");
                }

                // Fetch query statistics (top queries)
                $this->info("Fetching top query statistics...");
                $queryStats = $service->fetchQueryStats($tempToken, $startDate, $endDate);
                
                if (!empty($queryStats)) {
                    // Store query data
                    $service->storeQueryData($token, $queryStats, Carbon::now()->format('Y-m-d'));
                    $this->info("✓ Stored " . count($queryStats) . " query records");
                }

                // Fetch page statistics (top pages)
                $this->info("Fetching top page statistics...");
                $pageStats = $service->fetchPageStats($tempToken, $startDate, $endDate);
                
                if (!empty($pageStats)) {
                    // Store page data
                    $service->storePageData($token, $pageStats, Carbon::now()->format('Y-m-d'));
                    $this->info("✓ Stored " . count($pageStats) . " page records");
                }

                $this->info("✓ Successfully fetched data for token ID: {$token->id}");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("✗ Error processing token ID {$token->id}: " . $e->getMessage());
                Log::error('Bing Fetch Data Command Error', [
                    'token_id' => $token->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $failureCount++;
            }
        }

        $this->info("\n=== Summary ===");
        $this->info("Success: {$successCount}");
        $this->info("Failed: {$failureCount}");
        $this->info("Total: " . ($successCount + $failureCount));

        return $successCount > 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
