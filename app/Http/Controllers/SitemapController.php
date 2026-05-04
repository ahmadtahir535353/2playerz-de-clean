<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class SitemapController extends Controller
{
    public function news(): Response
    {
        // Disable debugbar and other middlewares that might inject scripts
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
            \Barryvdh\Debugbar\Facades\Debugbar::disable();
        }

        // Language + publication name apni site ke hisab se set karen
        $publicationName = '2Playerz';
        $language        = 'de'; // 'de' for German; 'en' if English; adjust if needed

        // Fresh articles from last 48 hours (Google News requirement: max 48 hours)
        $from = Carbon::now()->subHours(48);

        // Get News category ID - check both name and slug, case-insensitive
        $newsCategory = Category::withoutGlobalScopes()
            ->where(function($q) {
                $q->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim('news'))])
                  ->orWhereRaw('LOWER(TRIM(slug)) = ?', [strtolower(trim('news'))]);
            })
            ->first();

        if (!$newsCategory) {
            // If no News category found, return empty sitemap
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                   '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n" .
                   '</urlset>';
            
            return response($xml, 200)
                ->header('Content-Type', 'application/xml; charset=UTF-8')
                ->header('X-Robots-Tag', 'noindex')
                ->header('Cache-Control', 'public, max-age=60');
        }

        // IMPORTANT: Get ONLY News category articles for Google News sitemap
        // Google News requirements:
        // 1. Only News category articles
        // 2. Articles from last 48 hours (Google News strict requirement)
        // 3. Must have created_at date (using created_at as published date)
        // 4. Must be active and visible
        // 5. Date must not be in future
        $now = Carbon::now();
        $articles = Post::withoutGlobalScopes()
            ->with('category') // Load category relationship to verify
            ->where('category_id', $newsCategory->id) // ONLY News category
            ->whereNotNull('created_at') // Must have creation date
            ->where('created_at', '>=', $from) // Last 48 hours (Google News limit)
            ->where('created_at', '<=', $now) // Ensure not in future
            ->where('visibility', Post::VISIBILITY_ACTIVE)
            ->where('status', Post::STATUS_ACTIVE)
            ->orderByDesc('created_at') // Latest first
            ->limit(1000) // Google allows max 1000 URLs per sitemap
            ->get([
                'id', 'slug', 'title', 'created_at', 'updated_at', 'category_id', 'scheduled_post_time'
            ])
            // Final filter - ensure only News category articles and valid dates
            ->filter(function($article) use ($newsCategory, $from, $now) {
                // Use scheduled_post_time if exists and not null, otherwise created_at
                $publishDate = $article->scheduled_post_time ?? $article->created_at;
                
                // Ensure date is valid: not null, within last 48 hours, and not in future
                if (!$publishDate) {
                    return false;
                }
                
                // If scheduled_post_time is in future, use created_at instead
                if ($article->scheduled_post_time && $article->scheduled_post_time->gt($now)) {
                    $publishDate = $article->created_at;
                }
                
                return $article->category_id == $newsCategory->id 
                    && $publishDate->gte($from)
                    && $publishDate->lte($now); // Not in future
            })
            ->values(); // Re-index array after filter

        // Cache for 1 minute to avoid hammering on high traffic
        $xml = Cache::remember('news_sitemap_xml', 60, function () use ($articles, $publicationName, $language) {
            // Render view without any layout/wrapper
            $xmlContent = view('sitemaps.news', [
                'articles'        => $articles,
                'publicationName' => $publicationName,
                'language'        => $language,
            ])->render();
            
            // Clean any potential script tags (safety measure)
            $xmlContent = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $xmlContent);
            $xmlContent = preg_replace('/<script[^>]*\/>/', '', $xmlContent);
            
            return $xmlContent;
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('X-Robots-Tag', 'noindex')
            ->header('Cache-Control', 'public, max-age=60');
    }

    /**
     * Generate regular sitemap for cronjob
     * Simple route without authentication - for cPanel cronjob
     */
    public function generate()
    {
        try {
            // Run the same artisan command that admin panel uses
            Artisan::call('generate:sitemap');
            
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Sitemap generated successfully',
                'timestamp' => now()->toDateTimeString(),
                'output' => trim($output)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating sitemap: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }

    /**
     * Generate Google News sitemap
     * This clears the cache and regenerates the news sitemap
     */
    public function generateNewsSitemap()
    {
        try {
            // Clear the cached news sitemap
            Cache::forget('news_sitemap_xml');
            
            // Get fresh data (same logic as news() method)
            $publicationName = '2Playerz';
            $language = 'de';
            $from = Carbon::now()->subHours(48); // Google News: max 48 hours
            
            $newsCategory = Category::withoutGlobalScopes()
                ->where(function($q) {
                    $q->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim('news'))])
                      ->orWhereRaw('LOWER(TRIM(slug)) = ?', [strtolower(trim('news'))]);
                })
                ->first();
            
            if (!$newsCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'News category not found',
                    'timestamp' => now()->toDateTimeString()
                ], 404);
            }
            
            $now = Carbon::now();
            $articles = Post::withoutGlobalScopes()
                ->with('category')
                ->where('category_id', $newsCategory->id)
                ->whereNotNull('created_at')
                ->where('created_at', '>=', $from)
                ->where('created_at', '<=', $now) // Ensure not in future
                ->where('visibility', Post::VISIBILITY_ACTIVE)
                ->where('status', Post::STATUS_ACTIVE)
                ->orderByDesc('created_at')
                ->limit(1000)
                ->get([
                    'id', 'slug', 'title', 'created_at', 'updated_at', 'category_id', 'scheduled_post_time'
                ])
                ->filter(function($article) use ($newsCategory, $from, $now) {
                    // Use scheduled_post_time if exists and not null, otherwise created_at
                    $publishDate = $article->scheduled_post_time ?? $article->created_at;
                    
                    // Ensure date is valid: not null, within last 48 hours, and not in future
                    if (!$publishDate) {
                        return false;
                    }
                    
                    // If scheduled_post_time is in future, use created_at instead
                    if ($article->scheduled_post_time && $article->scheduled_post_time->gt($now)) {
                        $publishDate = $article->created_at;
                    }
                    
                    return $article->category_id == $newsCategory->id 
                        && $publishDate->gte($from)
                        && $publishDate->lte($now); // Not in future
                })
                ->values();
            
            // Generate and cache the XML
            $xmlContent = view('sitemaps.news', [
                'articles' => $articles,
                'publicationName' => $publicationName,
                'language' => $language,
            ])->render();
            
            // Clean any potential script tags
            $xmlContent = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $xmlContent);
            $xmlContent = preg_replace('/<script[^>]*\/>/', '', $xmlContent);
            
            // Cache for 1 hour
            Cache::put('news_sitemap_xml', $xmlContent, 3600);
            
            return response()->json([
                'success' => true,
                'message' => 'Google News sitemap generated successfully',
                'articles_count' => $articles->count(),
                'timestamp' => now()->toDateTimeString()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating news sitemap: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }
}

