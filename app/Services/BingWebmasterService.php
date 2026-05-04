<?php

namespace App\Services;

use App\Models\BingWebmasterToken;
use App\Models\BingWebmasterData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BingWebmasterService
{
    protected $baseUrl = 'https://ssl.bing.com/webmaster/api.svc/json';

    /**
     * Get HTTP client with SSL configuration
     */
    protected function getHttpClient()
    {
        $client = Http::withHeaders([
            'Content-Type' => 'application/json',
        ]);

        // For local development, disable SSL verification
        if (app()->environment('local')) {
            $client = $client->withOptions([
                'verify' => false,
            ]);
        }

        return $client;
    }

    /**
     * Get list of sites
     */
    public function getSites(BingWebmasterToken $token): array
    {
        try {
            $response = $this->getHttpClient()->get($this->baseUrl . '/GetSites', [
                'apikey' => $token->api_key,
            ]);

            if ($response->successful()) {
                return $response->json('d') ?? [];
            }

            Log::error('Bing Webmaster Get Sites Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Bing Webmaster Get Sites Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch query statistics from Bing Webmaster Tools
     */
    public function fetchQueryStats(
        BingWebmasterToken $token,
        string $startDate,
        string $endDate,
        string $siteUrl = null
    ): array {
        try {
            $siteUrl = $siteUrl ?? $token->site_url;
            if (!$siteUrl) {
                throw new \Exception('Site URL is required');
            }

            Log::info('Bing Webmaster Fetch Query Stats: Starting', [
                'token_id' => $token->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            // Bing API note: The API returns aggregated data, not day-by-day data like GSC
            // The date returned is typically the last available data date
            $response = $this->getHttpClient()->get($this->baseUrl . '/GetQueryStats', [
                'apikey' => $token->api_key,
                'siteUrl' => $siteUrl,
                'rowLimit' => 10000, // Get maximum rows
            ]);

            if ($response->successful()) {
                $data = $response->json('d') ?? [];
                Log::info('Bing Webmaster Fetch Query Stats: Success', [
                    'records' => count($data),
                ]);
                return $data;
            }

            Log::error('Bing Webmaster Fetch Query Stats Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Bing Webmaster Fetch Query Stats Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch page statistics from Bing Webmaster Tools
     */
    public function fetchPageStats(
        BingWebmasterToken $token,
        string $startDate,
        string $endDate,
        string $siteUrl = null
    ): array {
        try {
            $siteUrl = $siteUrl ?? $token->site_url;
            if (!$siteUrl) {
                throw new \Exception('Site URL is required');
            }

            $response = $this->getHttpClient()->get($this->baseUrl . '/GetPageStats', [
                'apikey' => $token->api_key,
                'siteUrl' => $siteUrl,
                'rowLimit' => 10000, // Get maximum rows
            ]);

            if ($response->successful()) {
                return $response->json('d') ?? [];
            }

            Log::error('Bing Webmaster Fetch Page Stats Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Bing Webmaster Fetch Page Stats Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch rank and traffic data
     */
    public function fetchRankAndTrafficStats(
        BingWebmasterToken $token,
        string $siteUrl = null
    ): array {
        try {
            $siteUrl = $siteUrl ?? $token->site_url;
            if (!$siteUrl) {
                throw new \Exception('Site URL is required');
            }

            $response = $this->getHttpClient()->get($this->baseUrl . '/GetRankAndTrafficStats', [
                'apikey' => $token->api_key,
                'siteUrl' => $siteUrl,
            ]);

            if ($response->successful()) {
                return $response->json('d') ?? [];
            }

            Log::error('Bing Webmaster Fetch Rank Stats Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Bing Webmaster Fetch Rank Stats Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse Bing API date format: /Date(1767945600000-0800)/
     */
    protected function parseBingDate($dateString): string
    {
        if (preg_match('/\/Date\((\d+)([+-]\d{4})?\)\//', $dateString, $matches)) {
            $timestamp = (int)($matches[1] / 1000); // Convert milliseconds to seconds
            return date('Y-m-d', $timestamp);
        }
        return date('Y-m-d'); // Fallback to today
    }

    /**
     * Store query data in database
     */
    public function storeQueryData(BingWebmasterToken $token, array $data, string $defaultDate): void
    {
        foreach ($data as $row) {
            // Use actual date from API response if available
            $rowDate = isset($row['Date']) ? $this->parseBingDate($row['Date']) : $defaultDate;
            
            BingWebmasterData::updateOrCreate(
                [
                    'token_id' => $token->id,
                    'date' => $rowDate,
                    'data_type' => 'query',
                    'query' => $row['Query'] ?? null,
                ],
                [
                    'clicks' => $row['Clicks'] ?? 0,
                    'impressions' => $row['Impressions'] ?? 0,
                    'ctr' => ($row['Clicks'] ?? 0) > 0 && ($row['Impressions'] ?? 0) > 0 
                        ? ($row['Clicks'] / $row['Impressions']) 
                        : 0,
                    'position' => $row['AvgImpressionPosition'] ?? 0,
                ]
            );
        }
    }

    /**
     * Store page data in database
     */
    public function storePageData(BingWebmasterToken $token, array $data, string $defaultDate): void
    {
        foreach ($data as $row) {
            // Use actual date from API response if available
            $rowDate = isset($row['Date']) ? $this->parseBingDate($row['Date']) : $defaultDate;
            
            BingWebmasterData::updateOrCreate(
                [
                    'token_id' => $token->id,
                    'date' => $rowDate,
                    'data_type' => 'page',
                    'page' => $row['Query'] ?? $row['Url'] ?? null, // Bing uses 'Query' field for URLs in PageStats
                ],
                [
                    'clicks' => $row['Clicks'] ?? 0,
                    'impressions' => $row['Impressions'] ?? 0,
                    'ctr' => ($row['Clicks'] ?? 0) > 0 && ($row['Impressions'] ?? 0) > 0 
                        ? ($row['Clicks'] / $row['Impressions']) 
                        : 0,
                ]
            );
        }
    }

    /**
     * Store overall/summary data
     */
    public function storeOverallData(BingWebmasterToken $token, array $queryStats, string $date): void
    {
        $totalClicks = 0;
        $totalImpressions = 0;
        $totalPositionSum = 0;
        $count = 0;

        foreach ($queryStats as $row) {
            $totalClicks += $row['Clicks'] ?? 0;
            $totalImpressions += $row['Impressions'] ?? 0;
            if (isset($row['AvgImpressionPosition'])) {
                $totalPositionSum += $row['AvgImpressionPosition'];
                $count++;
            }
        }

        $avgPosition = $count > 0 ? $totalPositionSum / $count : 0;
        $ctr = $totalImpressions > 0 ? $totalClicks / $totalImpressions : 0;

        BingWebmasterData::updateOrCreate(
            [
                'token_id' => $token->id,
                'date' => $date,
                'data_type' => 'overall',
                'query' => null,
                'page' => null,
                'device_type' => null,
                'country' => null,
            ],
            [
                'clicks' => $totalClicks,
                'impressions' => $totalImpressions,
                'ctr' => $ctr,
                'position' => $avgPosition,
            ]
        );
    }

    /**
     * Store rank and traffic data (complete site statistics)
     */
    public function storeRankAndTrafficData(BingWebmasterToken $token, array $rankStats, string $defaultDate): void
    {
        // Bing GetRankAndTrafficStats returns array of data
        // We need to extract and store data by date
        if (empty($rankStats)) {
            return;
        }

        // Group data by date
        $dataByDate = [];
        
        foreach ($rankStats as $row) {
            $rowDate = isset($row['Date']) ? $this->parseBingDate($row['Date']) : $defaultDate;
            
            if (!isset($dataByDate[$rowDate])) {
                $dataByDate[$rowDate] = [
                    'clicks' => 0,
                    'impressions' => 0,
                    'position_sum' => 0,
                    'position_count' => 0,
                ];
            }
            
            $dataByDate[$rowDate]['clicks'] += $row['Clicks'] ?? 0;
            $dataByDate[$rowDate]['impressions'] += $row['Impressions'] ?? 0;
            
            if (isset($row['AvgImpressionPosition']) && $row['AvgImpressionPosition'] > 0) {
                $dataByDate[$rowDate]['position_sum'] += $row['AvgImpressionPosition'];
                $dataByDate[$rowDate]['position_count']++;
            }
        }

        // Store data for each date
        foreach ($dataByDate as $date => $stats) {
            $avgPosition = $stats['position_count'] > 0 
                ? $stats['position_sum'] / $stats['position_count'] 
                : 0;
            $ctr = $stats['impressions'] > 0 
                ? $stats['clicks'] / $stats['impressions'] 
                : 0;

            Log::info('Bing Webmaster Store Rank Data', [
                'date' => $date,
                'total_clicks' => $stats['clicks'],
                'total_impressions' => $stats['impressions'],
                'avg_ctr' => $ctr,
                'avg_position' => $avgPosition,
            ]);

            BingWebmasterData::updateOrCreate(
                [
                    'token_id' => $token->id,
                    'date' => $date,
                    'data_type' => 'overall',
                    'query' => null,
                    'page' => null,
                    'device_type' => null,
                    'country' => null,
                ],
                [
                    'clicks' => $stats['clicks'],
                    'impressions' => $stats['impressions'],
                    'ctr' => $ctr,
                    'position' => $avgPosition,
                ]
            );
        }
    }
}
