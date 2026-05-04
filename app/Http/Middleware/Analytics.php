<?php

namespace App\Http\Middleware;

use App\Models\Analytic;
use App\Models\DailyStat;
use App\Models\Post;
use App\Scopes\AuthoriseUserActivePostScope;
use App\Scopes\LanguageScope;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

class Analytics
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = str_replace($request->root(), '', $request->url()) ?: '/';
        $uri = substr($uri, strrpos($uri, '/', -1));
        $agent = new Agent();
        $agent->setUserAgent($request->headers->get('user-agent'));
        $agent->setHttpHeaders($request->headers);
        $post = Post::withoutGlobalScope(AuthoriseUserActivePostScope::class)->withoutGlobalScope(LanguageScope::class)->where('slug', last(request()->segments()))->first();
        if (empty($post)) {
            return $next($request);
        }
        $sessionId = $request->session()->getId();
        $recordExists = Analytic::where('session', $sessionId)->where('post_id', $post->id)->where('ip', $request->ip())->exists();
        if ($recordExists) {
            return $next($request);
        }

        Analytic::create([
            'session' => $sessionId,
            'uri' => urldecode($uri),
            'country' => ! empty(Location::get($request->ip())) ? Location::get($request->ip())->countryName : '',
            'ip' => $request->ip(),
            'user_id' => getLogInUser() ? getLogInUser()->id : null,
            'post_id' => $post->id ? $post->id : null,
            'meta' => json_encode(Location::get($request->ip())),
        ]);

        // Increment cached views_count for real-time updates (no need to wait for hourly sync)
        $post->increment('views_count');

        // Update lightweight daily stats for dashboard graphs (no heavy analytics table read)
        if (\Illuminate\Support\Facades\Schema::hasTable('daily_stats') && \Illuminate\Support\Facades\Schema::hasTable('daily_visitor_ips')) {
            $today = now()->toDateString();
            DailyStat::firstOrCreate(
                ['date' => $today],
                ['post_views' => 0, 'unique_visitors' => 0]
            );
            DB::table('daily_stats')->where('date', $today)->increment('post_views');

            $ip = $request->ip();
            $inserted = DB::table('daily_visitor_ips')->insertOrIgnore([
                'date' => $today,
                'ip' => $ip,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if ($inserted) {
                DB::table('daily_stats')->where('date', $today)->increment('unique_visitors');
            }
        }

        return $next($request);
    }
}
