<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class IndexNowService
{
    /**
     * IndexNow API endpoint
     */
    private const API_ENDPOINT = 'https://api.indexnow.org/IndexNow';

    private function apiKey(): string
    {
        return (string) config('services.indexnow.api_key', '');
    }

    /**
     * Submit a single URL to IndexNow
     *
     * @param string $url The full URL to submit
     * @return bool
     */
    public function submitUrl(string $url): bool
    {
        try {
            $baseUrl = config('app.url');
            $host = parse_url($baseUrl, PHP_URL_HOST);
            $apiKey = $this->apiKey();
            $keyLocation = rtrim($baseUrl, '/') . '/' . $apiKey . '.txt';

            $response = Http::post(self::API_ENDPOINT, [
                'host' => $host,
                'key' => $apiKey,
                'keyLocation' => $keyLocation,
                'urlList' => [$url]
            ]);

            if ($response->successful()) {
                Log::info('IndexNow: URL submitted successfully', [
                    'url' => $url,
                    'status' => $response->status()
                ]);
                return true;
            } else {
                Log::warning('IndexNow: Failed to submit URL', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('IndexNow: Exception while submitting URL', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Submit multiple URLs to IndexNow
     *
     * @param array $urls Array of full URLs to submit
     * @return bool
     */
    public function submitUrls(array $urls): bool
    {
        if (empty($urls)) {
            return false;
        }

        try {
            $baseUrl = config('app.url');
            $host = parse_url($baseUrl, PHP_URL_HOST);
            $apiKey = $this->apiKey();
            $keyLocation = rtrim($baseUrl, '/') . '/' . $apiKey . '.txt';

            $response = Http::post(self::API_ENDPOINT, [
                'host' => $host,
                'key' => $apiKey,
                'keyLocation' => $keyLocation,
                'urlList' => $urls
            ]);

            if ($response->successful()) {
                Log::info('IndexNow: URLs submitted successfully', [
                    'count' => count($urls),
                    'status' => $response->status()
                ]);
                return true;
            } else {
                Log::warning('IndexNow: Failed to submit URLs', [
                    'count' => count($urls),
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('IndexNow: Exception while submitting URLs', [
                'count' => count($urls),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate full URL for a post
     *
     * @param string $slug Post slug
     * @return string
     */
    public function getPostUrl(string $slug): string
    {
        return route('detailPage', $slug);
    }

    /**
     * Generate full URL for a page
     *
     * @param string $slug Page slug
     * @return string
     */
    public function getPageUrl(string $slug): string
    {
        return route('pages.show-page-slug', $slug);
    }
}

