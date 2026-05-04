<?php

namespace App\Services;

use App\Models\GoogleSearchConsoleToken;
use App\Models\GoogleSearchConsoleData;
use Carbon\Carbon;
use Google_Client;
use Google_Service_SearchConsole;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class GoogleSearchConsoleService
{
    protected $client;
    protected $service;

    /**
     * Initialize Google Client
     */
    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('2Playerz GSC Integration');
        $this->client->setScopes([
            Google_Service_SearchConsole::WEBMASTERS_READONLY,
        ]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        
        // SSL verification handling
        // For local development, disable SSL verification
        // For production, use proper SSL certificates or handle SSL errors gracefully
        if (app()->environment('local')) {
            $httpClient = new \GuzzleHttp\Client([
                'verify' => false, // Disable SSL verification for local dev
            ]);
            $this->client->setHttpClient($httpClient);
        } else {
            // For production, try to use system CA bundle, but handle SSL errors gracefully
            try {
                $httpClient = new \GuzzleHttp\Client([
                    'verify' => true, // Use system CA bundle
                    'timeout' => 30, // Increase timeout for production
                ]);
                $this->client->setHttpClient($httpClient);
            } catch (\Exception $e) {
                // If SSL verification fails, log error but continue
                Log::warning('GSC SSL Verification Warning: ' . $e->getMessage());
            }
        }
    }

    /**
     * Set credentials from config
     */
    public function setCredentials(string $clientId, string $clientSecret, string $redirectUri): void
    {
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->setRedirectUri($redirectUri);
    }

    /**
     * Get authorization URL
     */
    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Exchange authorization code for tokens
     */
    public function exchangeCodeForTokens(string $code): array
    {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($accessToken['error'])) {
                throw new \Exception('Error fetching access token: ' . $accessToken['error']);
            }

            return [
                'access_token' => $accessToken['access_token'],
                'refresh_token' => $accessToken['refresh_token'] ?? null,
                'expires_at' => isset($accessToken['expires_in']) 
                    ? Carbon::now()->addSeconds($accessToken['expires_in'])
                    : null,
            ];
        } catch (\Exception $e) {
            Log::error('GSC Token Exchange Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Refresh access token
     */
    public function refreshToken(GoogleSearchConsoleToken $token): bool
    {
        try {
            if (!$token->refresh_token) {
                Log::error('GSC Token Refresh: No refresh token available', ['token_id' => $token->id]);
                throw new \Exception('No refresh token available');
            }

            // Get credentials from settings table
            $clientIdSetting = \App\Models\Setting::where('key', 'gsc_client_id')->first();
            $clientSecretSetting = \App\Models\Setting::where('key', 'gsc_client_secret')->first();
            $redirectUrlSetting = \App\Models\Setting::where('key', 'gsc_redirect_url')->first();

            if (!$clientIdSetting || !$clientSecretSetting || empty($clientIdSetting->value) || empty($clientSecretSetting->value)) {
                Log::error('GSC Token Refresh: Credentials not found in settings');
                throw new \Exception('Google credentials not found in settings');
            }

            try {
                $clientSecret = Crypt::decryptString($clientSecretSetting->value);
            } catch (\Exception $e) {
                Log::error('GSC Token Refresh: Failed to decrypt client secret', ['error' => $e->getMessage()]);
                throw new \Exception('Failed to decrypt client secret');
            }

            $this->setCredentials(
                $clientIdSetting->value,
                $clientSecret,
                $redirectUrlSetting->value ?? url('/gsc/callback')
            );

            $this->client->refreshToken($token->refresh_token);
            $accessToken = $this->client->getAccessToken();

            if (isset($accessToken['error'])) {
                Log::error('GSC Token Refresh: Google API error', ['error' => $accessToken['error']]);
                throw new \Exception('Error refreshing token: ' . $accessToken['error']);
            }

            $token->update([
                'access_token' => $accessToken['access_token'],
                'expires_at' => isset($accessToken['expires_in']) 
                    ? Carbon::now('UTC')->addSeconds($accessToken['expires_in'])
                    : null,
                'refresh_token' => $accessToken['refresh_token'] ?? $token->refresh_token, // Refresh token might not always be returned
            ]);

            Log::info('GSC Token refreshed successfully', ['token_id' => $token->id]);
            return true;
        } catch (\Exception $e) {
            Log::error('GSC Token Refresh Error', [
                'token_id' => $token->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Deactivate token if refresh fails
            $token->update(['is_active' => false]);
            return false;
        }
    }

    /**
     * Set access token for API calls
     */
    protected function setAccessToken(GoogleSearchConsoleToken $token): void
    {
        // For temporary tokens (not saved yet), skip expiration check
        if ($token->exists && $token->isExpired() && $token->refresh_token) {
            $this->refreshToken($token);
            $token->refresh();
        }

        $tokenData = [
            'access_token' => $token->access_token,
        ];

        if ($token->refresh_token) {
            $tokenData['refresh_token'] = $token->refresh_token;
        }

        if ($token->expires_at) {
            $tokenData['expires_in'] = $token->expires_at->diffInSeconds(Carbon::now('UTC'));
        } else {
            $tokenData['expires_in'] = 3600;
        }

        $this->client->setAccessToken($tokenData);
        $this->service = new Google_Service_SearchConsole($this->client);
    }

    /**
     * Get list of sites/properties
     */
    public function getSites(GoogleSearchConsoleToken $token): array
    {
        try {
            $this->setAccessToken($token);
            $sites = $this->service->sites->listSites();
            
            $siteList = [];
            foreach ($sites->getSiteEntry() as $site) {
                $siteList[] = [
                    'siteUrl' => $site->getSiteUrl(),
                    'permissionLevel' => $site->getPermissionLevel(),
                ];
            }

            return $siteList;
        } catch (\Exception $e) {
            Log::error('GSC Get Sites Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch performance data from GSC
     */
    public function fetchPerformanceData(
        GoogleSearchConsoleToken $token,
        string $startDate,
        string $endDate,
        string $propertyUrl = null
    ): array {
        try {
            Log::info('GSC Fetch Performance Data: Starting', [
                'token_id' => $token->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $this->setAccessToken($token);
            
            $propertyUrl = $propertyUrl ?? $token->property_url;
            if (!$propertyUrl) {
                Log::error('GSC Fetch Performance Data: Property URL missing', ['token_id' => $token->id]);
                throw new \Exception('Property URL is required');
            }

            $request = new \Google_Service_SearchConsole_SearchAnalyticsQueryRequest();
            $request->setStartDate($startDate);
            $request->setEndDate($endDate);
            $request->setDimensions(['date']);

            Log::info('GSC Fetch Performance Data: Making API request', [
                'property_url' => $propertyUrl,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $response = $this->service->searchanalytics->query($propertyUrl, $request);
            
            $data = [];
            if ($response && $response->getRows()) {
                foreach ($response->getRows() as $row) {
                    $data[] = [
                        'date' => $row->getKeys()[0],
                        'clicks' => $row->getClicks(),
                        'impressions' => $row->getImpressions(),
                        'ctr' => $row->getCtr(),
                        'position' => $row->getPosition(),
                    ];
                }
            }

            Log::info('GSC Fetch Performance Data: Success', [
                'rows_count' => count($data),
                'first_date' => !empty($data) ? $data[0]['date'] : null,
                'last_date' => !empty($data) ? end($data)['date'] : null,
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::error('GSC Fetch Performance Data Error', [
                'token_id' => $token->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Fetch query data (top queries)
     */
    public function fetchQueryData(
        GoogleSearchConsoleToken $token,
        string $startDate,
        string $endDate,
        int $rowLimit = 1000,
        string $propertyUrl = null
    ): array {
        try {
            $this->setAccessToken($token);
            
            $propertyUrl = $propertyUrl ?? $token->property_url;
            if (!$propertyUrl) {
                throw new \Exception('Property URL is required');
            }

            $request = new \Google_Service_SearchConsole_SearchAnalyticsQueryRequest();
            $request->setStartDate($startDate);
            $request->setEndDate($endDate);
            $request->setDimensions(['query']);
            $request->setRowLimit($rowLimit);

            $response = $this->service->searchanalytics->query($propertyUrl, $request);
            
            $data = [];
            foreach ($response->getRows() as $row) {
                $data[] = [
                    'query' => $row->getKeys()[0],
                    'clicks' => $row->getClicks(),
                    'impressions' => $row->getImpressions(),
                    'ctr' => $row->getCtr(),
                    'position' => $row->getPosition(),
                ];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('GSC Fetch Query Data Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch page data (top pages)
     */
    public function fetchPageData(
        GoogleSearchConsoleToken $token,
        string $startDate,
        string $endDate,
        int $rowLimit = 1000,
        string $propertyUrl = null
    ): array {
        try {
            $this->setAccessToken($token);
            
            $propertyUrl = $propertyUrl ?? $token->property_url;
            if (!$propertyUrl) {
                throw new \Exception('Property URL is required');
            }

            $request = new \Google_Service_SearchConsole_SearchAnalyticsQueryRequest();
            $request->setStartDate($startDate);
            $request->setEndDate($endDate);
            $request->setDimensions(['page']);
            $request->setRowLimit($rowLimit);

            $response = $this->service->searchanalytics->query($propertyUrl, $request);
            
            $data = [];
            foreach ($response->getRows() as $row) {
                $data[] = [
                    'page' => $row->getKeys()[0],
                    'clicks' => $row->getClicks(),
                    'impressions' => $row->getImpressions(),
                    'ctr' => $row->getCtr(),
                    'position' => $row->getPosition(),
                ];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('GSC Fetch Page Data Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Store performance data in database
     */
    public function storePerformanceData(GoogleSearchConsoleToken $token, array $data, string $dataType = 'overall'): void
    {
        foreach ($data as $row) {
            GoogleSearchConsoleData::updateOrCreate(
                [
                    'token_id' => $token->id,
                    'date' => $row['date'],
                    'data_type' => $dataType,
                    'query' => null,
                    'page' => null,
                    'device_type' => null,
                    'country' => null,
                ],
                [
                    'clicks' => $row['clicks'] ?? 0,
                    'impressions' => $row['impressions'] ?? 0,
                    'ctr' => $row['ctr'] ?? 0,
                    'position' => $row['position'] ?? 0,
                ]
            );
        }
    }

    /**
     * Store query data in database
     */
    public function storeQueryData(GoogleSearchConsoleToken $token, array $data, string $endDate = null): void
    {
        // Use provided end date or default to yesterday minus 2 days (UTC timezone)
        $defaultDate = $endDate ?? Carbon::now('UTC')->subDays(2)->format('Y-m-d');
        
        foreach ($data as $row) {
            GoogleSearchConsoleData::updateOrCreate(
                [
                    'token_id' => $token->id,
                    'date' => $defaultDate,
                    'data_type' => 'query',
                    'query' => $row['query'],
                    'page' => null,
                    'device_type' => null,
                    'country' => null,
                ],
                [
                    'clicks' => $row['clicks'] ?? 0,
                    'impressions' => $row['impressions'] ?? 0,
                    'ctr' => $row['ctr'] ?? 0,
                    'position' => $row['position'] ?? 0,
                ]
            );
        }
    }

    /**
     * Store page data in database
     */
    public function storePageData(GoogleSearchConsoleToken $token, array $data, string $endDate = null): void
    {
        // Use provided end date or default to yesterday minus 2 days (UTC timezone)
        $defaultDate = $endDate ?? Carbon::now('UTC')->subDays(2)->format('Y-m-d');
        
        foreach ($data as $row) {
            GoogleSearchConsoleData::updateOrCreate(
                [
                    'token_id' => $token->id,
                    'date' => $defaultDate,
                    'data_type' => 'page',
                    'query' => null,
                    'page' => $row['page'],
                    'device_type' => null,
                    'country' => null,
                ],
                [
                    'clicks' => $row['clicks'] ?? 0,
                    'impressions' => $row['impressions'] ?? 0,
                    'ctr' => $row['ctr'] ?? 0,
                    'position' => $row['position'] ?? 0,
                ]
            );
        }
    }

    /**
     * Fetch and store all data for a token
     */
    public function fetchAndStoreAllData(GoogleSearchConsoleToken $token, int $days = 90): bool
    {
        try {
            // Use UTC timezone to ensure consistent date calculation across servers
            // GSC data is usually available with 2 days delay, so use 2 days back as end date
            $now = Carbon::now('UTC');
            $endDate = $now->copy()->subDays(2)->format('Y-m-d');
            $startDate = $now->copy()->subDays(2)->subDays($days)->format('Y-m-d');

            Log::info('GSC Fetch Data: Date range', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'token_id' => $token->id,
                'server_timezone' => config('app.timezone'),
                'utc_now' => $now->toDateTimeString(),
                'calculated_end_date' => $endDate,
            ]);

            // Fetch overall performance data
            try {
                $performanceData = $this->fetchPerformanceData($token, $startDate, $endDate);
                Log::info('GSC Fetch Data: Performance data fetched', [
                    'rows_count' => count($performanceData),
                    'latest_date' => !empty($performanceData) ? end($performanceData)['date'] : null,
                    'first_date' => !empty($performanceData) ? $performanceData[0]['date'] : null,
                ]);
                
                if (!empty($performanceData)) {
                    $this->storePerformanceData($token, $performanceData, 'overall');
                    Log::info('GSC Fetch Data: Performance data stored', ['rows_stored' => count($performanceData)]);
                } else {
                    Log::warning('GSC Fetch Data: No performance data returned from API', [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('GSC Fetch Data: Error fetching performance data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e; // Re-throw to be caught by outer try-catch
            }

            // Fetch top queries (last 30 days for queries)
            $queryStartDate = $now->copy()->subDays(2)->subDays(30)->format('Y-m-d');
            $queryEndDate = $now->copy()->subDays(2)->format('Y-m-d');
            $queryData = $this->fetchQueryData($token, $queryStartDate, $queryEndDate, 1000);
            $this->storeQueryData($token, $queryData, $queryEndDate);

            // Fetch top pages (last 30 days for pages)
            $pageData = $this->fetchPageData($token, $queryStartDate, $queryEndDate, 1000);
            $this->storePageData($token, $pageData, $queryEndDate);

            return true;
        } catch (\Exception $e) {
            Log::error('GSC Fetch and Store All Data Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}

