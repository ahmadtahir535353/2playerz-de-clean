<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\ProfileVisitorService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackProfileVisits
{
    protected $profileVisitorService;

    public function __construct(ProfileVisitorService $profileVisitorService)
    {
        $this->profileVisitorService = $profileVisitorService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track visits for user profile pages
        if ($this->shouldTrackVisit($request)) {
            $this->trackVisit($request);
        }

        return $response;
    }

    /**
     * Determine if we should track this visit
     */
    protected function shouldTrackVisit(Request $request): bool
    {
        // Only track GET requests to user profile pages
        if (!$request->isMethod('GET')) {
            return false;
        }

        // Check if this is a user profile route
        $route = $request->route();
        if (!$route) {
            return false;
        }

        $routeName = $route->getName();
        $routeParameters = $route->parameters();

        // Check for common profile route patterns
        $profileRoutes = [
            'user.profile',
            'profile.show',
            'user.show',
            'member.profile',
        ];

        if (in_array($routeName, $profileRoutes)) {
            return true;
        }

        // Check if URL contains username parameter
        if (isset($routeParameters['username']) || isset($routeParameters['user'])) {
            return true;
        }

        return false;
    }

    /**
     * Track the profile visit
     */
    protected function trackVisit(Request $request): void
    {
        try {
            $route = $request->route();
            $routeParameters = $route->parameters();

            // Get the username from route parameters
            $username = $routeParameters['username'] ?? $routeParameters['user'] ?? null;

            if (!$username) {
                return;
            }

            // Find the user by username
            $profileOwner = User::where('username', $username)->first();

            if (!$profileOwner) {
                return;
            }

            // Don't track visits to own profile
            $currentUser = auth()->user();
            if ($currentUser && $currentUser->id === $profileOwner->id) {
                return;
            }

            // Track the visit
            $this->profileVisitorService->trackVisit($profileOwner, $request);

        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::error('Profile visit tracking failed: ' . $e->getMessage());
        }
    }
}