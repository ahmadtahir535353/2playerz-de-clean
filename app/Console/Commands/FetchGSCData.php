<?php

namespace App\Console\Commands;

use App\Models\GoogleSearchConsoleToken;
use App\Models\Setting;
use App\Services\GoogleSearchConsoleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class FetchGSCData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gsc:fetch-data {--days=90 : Number of days to fetch data for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Google Search Console data for all active tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Google Search Console data fetch...');

        // Get all active tokens
        $tokens = GoogleSearchConsoleToken::where('is_active', true)->get();

        if ($tokens->isEmpty()) {
            $this->warn('No active GSC tokens found. Please connect Google Search Console first.');
            return Command::FAILURE;
        }

        $days = (int) $this->option('days');
        
        // Get credentials from settings
        $clientId = Setting::where('key', 'gsc_client_id')->first();
        $clientSecret = Setting::where('key', 'gsc_client_secret')->first();
        $redirectUrl = Setting::where('key', 'gsc_redirect_url')->first();

        if (!$clientId || !$clientSecret || empty($clientId->value) || empty($clientSecret->value)) {
            $this->error('Google API credentials not configured. Please save credentials in settings first.');
            Log::error('GSC Fetch Command: Credentials not found or empty');
            return Command::FAILURE;
        }

        try {
            $decryptedSecret = Crypt::decryptString($clientSecret->value);
        } catch (\Exception $e) {
            $this->error('Failed to decrypt Google Client Secret. Please re-save credentials in admin panel.');
            Log::error('GSC Fetch Command: Decryption error', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }

        $service = new GoogleSearchConsoleService();
        
        // Set credentials from settings
        try {
            $service->setCredentials(
                $clientId->value,
                $decryptedSecret,
                $redirectUrl->value ?? url('/gsc/callback')
            );
        } catch (\Exception $e) {
            $this->error('Failed to set credentials: ' . $e->getMessage());
            Log::error('GSC Fetch Command: Failed to set credentials', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($tokens as $token) {
            $this->info("Processing token for user: {$token->user_id} (Property: {$token->property_url})");

            try {
                $result = $service->fetchAndStoreAllData($token, $days);
                
                if ($result) {
                    $this->info("✓ Successfully fetched data for token ID: {$token->id}");
                    $successCount++;
                } else {
                    $this->error("✗ Failed to fetch data for token ID: {$token->id}");
                    $failureCount++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Error processing token ID {$token->id}: " . $e->getMessage());
                Log::error('GSC Fetch Data Command Error', [
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
