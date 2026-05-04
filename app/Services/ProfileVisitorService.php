<?php

namespace App\Services;

use App\Models\ProfileVisitor;
use App\Models\User;
use App\Notifications\ProfileVisitNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileVisitorService
{
    /**
     * Track a profile visit
     */
    public function trackVisit(User $profileOwner, Request $request): void
    {
        $visitor = Auth::user();
        $visitorId = $visitor ? $visitor->id : null;
        $visitorIp = $request->ip();
        $userAgent = $request->userAgent();

        // Do not track profile visits by Super Admin — they should not appear in profile visitors
        if ($visitor && (int) $visitor->type === User::ADMIN) {
            return;
        }

        // For logged-in users: check if this visitor already exists
        if ($visitorId) {
            $existingVisit = ProfileVisitor::where('profile_owner_id', $profileOwner->id)
                ->where('visitor_id', $visitorId)
                ->first();

            if ($existingVisit) {
                // Update existing visit: update time and move to top
                $existingVisit->update([
                    'visited_at' => now(),
                    'visitor_ip' => $visitorIp,
                    'visitor_user_agent' => $userAgent,
                ]);
                return; // Don't create new record or send notification again
            }
        } else {
            // For guests: check by IP within last hour to avoid spam
            $recentGuestVisit = ProfileVisitor::where('profile_owner_id', $profileOwner->id)
                ->whereNull('visitor_id')
                ->where('visitor_ip', $visitorIp)
                ->where('visited_at', '>=', now()->subHour())
                ->first();

            if ($recentGuestVisit) {
                // Update guest visit time
                $recentGuestVisit->update([
                    'visited_at' => now(),
                    'visitor_user_agent' => $userAgent,
                ]);
                return;
            }
        }

        // Create new visit record only if visitor doesn't exist
        $visit = ProfileVisitor::create([
            'profile_owner_id' => $profileOwner->id,
            'visitor_id' => $visitorId,
            'visitor_ip' => $visitorIp,
            'visitor_user_agent' => $userAgent,
            'visited_at' => now(),
        ]);

        // Increment the visitor count only for new visitors
        $profileOwner->increment('visitor_count');

        // Send notification only for logged-in users (not guests) and only for new visits
        if ($visitor && $visitor->id !== $profileOwner->id) {
            $profileOwner->notify(new ProfileVisitNotification($visitor, $profileOwner));
        }
    }

    /**
     * Get visitor count for a profile
     */
    public function getVisitorCount(User $profileOwner): int
    {
        return $profileOwner->visitor_count ?? 0;
    }

    /**
     * Get recent visitors for a profile (logged-in users only)
     * Returns unique visitors with their latest visit time
     */
    public function getRecentVisitors(User $profileOwner, int $limit = 20)
    {
        // Get the latest visit record for each unique visitor (exclude Super Admin)
        $latestVisitIds = DB::table('profile_visitors')
            ->join('users', 'profile_visitors.visitor_id', '=', 'users.id')
            ->where('profile_visitors.profile_owner_id', $profileOwner->id)
            ->whereNotNull('profile_visitors.visitor_id')
            ->where('users.type', '!=', User::ADMIN)
            ->select(DB::raw('MAX(profile_visitors.id) as id'))
            ->groupBy('profile_visitors.visitor_id')
            ->orderBy(DB::raw('MAX(profile_visitors.visited_at)'), 'desc')
            ->limit($limit)
            ->pluck('id');

        // Get full visitor records with visitor relationship
        return ProfileVisitor::whereIn('id', $latestVisitIds)
            ->with('visitor')
            ->orderBy('visited_at', 'desc')
            ->get();
    }

    /**
     * Get visitor statistics
     */
    public function getVisitorStats(User $profileOwner): array
    {
        $baseQuery = $profileOwner->profileVisitors()->where(function ($q) {
            $q->whereNull('visitor_id')
                ->orWhereHas('visitor', fn ($q2) => $q2->where('type', '!=', User::ADMIN));
        });
        $totalVisits = (clone $baseQuery)->count();
        $loggedInVisits = (clone $baseQuery)->whereNotNull('visitor_id')->count();
        $guestVisits = $totalVisits - $loggedInVisits;

        return [
            'total_visits' => $totalVisits,
            'logged_in_visits' => $loggedInVisits,
            'guest_visits' => $guestVisits,
            'visitor_count' => $profileOwner->visitor_count,
        ];
    }
}
