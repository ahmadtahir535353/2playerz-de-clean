<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Analytic;
use Carbon\Carbon;

class XSS
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Store intended URL for redirects
        if (!Auth::check() && !$request->is('login') && !$request->is('register') &&  
            !$request->is('p/fetch')) {
            session()->put('url.intended', $request->fullUrl());
        }

        // XSS Protection - Clean input data
        $this->cleanInput($request);

        // Optimized analytics tracking with caching
        $this->trackAnalytics($request);

        // Update user last seen (with throttling)
        $this->updateUserLastSeen();

        return $next($request);
    }

    /**
     * Clean input data to prevent XSS attacks
     */
    private function cleanInput(Request $request): void
    {
        // Skip XSS cleaning for certain routes to improve performance
        if ($this->shouldSkipCleaning($request)) {
            return;
        }

        // Only process POST/PUT/PATCH requests with actual data
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH']) || $request->isJson()) {
            return;
        }

        $input = $request->all();
        
        if (empty($input)) {
            return;
        }

        // Process only if there's actual string content to clean
        $hasStringContent = false;
        array_walk_recursive($input, function ($value) use (&$hasStringContent) {
            if (is_string($value) && !empty(trim($value))) {
                $hasStringContent = true;
            }
        });

        if (!$hasStringContent) {
            return;
        }

        array_walk_recursive($input, function (&$value) {
            if (!is_null($value) && is_string($value) && !empty(trim($value))) {
                // Only clean if content looks like it might contain HTML
                if ($this->needsCleaning($value)) {
                    $value = $this->cleanHtml($value);
                }
            }
        });
        
        $request->merge($input);
    }

    /**
     * Check if we should skip cleaning for this request
     */
    private function shouldSkipCleaning(Request $request): bool
    {
        // Skip for admin settings to avoid breaking configuration
        if ($request->route() && $request->route()->uri === 'admin/settings') {
            return true;
        }

        // Skip for API routes (they should handle their own validation)
        if ($request->is('api/*')) {
            return true;
        }

        // Skip for static assets and AJAX requests
        if ($request->ajax() && $request->isMethod('GET')) {
            return true;
        }

        return false;
    }

    /**
     * Check if content needs HTML cleaning (performance optimization)
     */
    private function needsCleaning(string $value): bool
    {
        // Quick check for HTML-like content
        return strpos($value, '<') !== false || 
               strpos($value, 'javascript:') !== false ||
               strpos($value, 'onclick') !== false ||
               strpos($value, 'onload') !== false;
    }

    /**
     * Clean HTML content to prevent XSS attacks
     */
    private function cleanHtml(string $value): string
    {
        // Quick exit for simple text
        if (strpos($value, '<') === false) {
            return trim($value);
        }

        // Decode HTML entities first
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove dangerous tags first (most efficient)
        $value = preg_replace('/<(script|iframe|object|embed|form|input|textarea|select|button)[^>]*>.*?<\/\1>/is', '', $value);
        
        // Remove dangerous attributes (single regex for better performance)
        $value = preg_replace('/\s*(on\w+|javascript|vbscript|data)\s*[=:]/i', '', $value);
        
        // Allow only safe HTML tags
        $value = strip_tags($value, '<p><br><strong><em><u><b><i><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><code><pre>');
        
        // Final cleanup - remove any remaining dangerous patterns
        $value = preg_replace('/javascript\s*:/i', '', $value);
        $value = preg_replace('/on\w+\s*=/i', '', $value);
        
        return trim($value);
    }

    /**
     * Track analytics with caching to reduce database queries
     */
    private function trackAnalytics(Request $request): void
    {
        $ip = $request->ip();
        $today = Carbon::today()->toDateString();
        $cacheKey = "analytics_tracked_{$ip}_{$today}";

        // Check cache first to avoid database query
        if (!Cache::has($cacheKey)) {
            // Use database query only if not in cache
            $alreadyExists = Analytic::where('ip', $ip)
                ->whereDate('created_at', $today)
                ->exists();

            if (!$alreadyExists) {
                Analytic::create([
                    'ip' => $ip,
                    'uri' => $request->path(),
                    'meta' => json_encode([
                        'user_agent' => $request->userAgent(),
                    ]),
                    'created_at' => now(),
                ]);
            }

            // Cache for 24 hours to prevent duplicate tracking
            Cache::put($cacheKey, true, now()->addDay());
        }
    }

    /**
     * Update user last seen with throttling to reduce database writes
     */
    private function updateUserLastSeen(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cacheKey = "user_last_seen_{$user->id}";
            
            // Only update if not updated in last 5 minutes
            if (!Cache::has($cacheKey)) {
                $user->update(['last_seen_at' => now()]);
                Cache::put($cacheKey, true, now()->addMinutes(5));
            }
        }
    }
}
