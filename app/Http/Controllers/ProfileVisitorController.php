<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ProfileVisitorService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Http\FormRequest;

class ProfileVisitorController extends \Illuminate\Routing\Controller
{
    protected $profileVisitorService;

    public function __construct(ProfileVisitorService $profileVisitorService)
    {
        $this->profileVisitorService = $profileVisitorService;
        $this->middleware('auth');
    }

    /**
     * Show the profile visitors page
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = 20;
        $page = $request->get('page', 1);

        // Get recent visitors
        $visitors = $this->profileVisitorService->getRecentVisitors($user, 100);
        
        // Create pagination manually
        $total = $visitors->count();
        $visitors = $visitors->slice(($page - 1) * $perPage, $perPage);
        
        $paginatedVisitors = new LengthAwarePaginator(
            $visitors,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        // Get visitor statistics
        $stats = $this->profileVisitorService->getVisitorStats($user);

        return view('front_new.profile.visitors', compact('paginatedVisitors', 'stats'));
    }

    /**
     * Get visitor count for a user profile (AJAX)
     */
    public function getVisitorCount(Request $request)
    {
        $username = $request->get('username');
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $count = $this->profileVisitorService->getVisitorCount($user);

        return response()->json(['visitor_count' => $count]);
    }

    /**
     * Get recent visitors for a user profile (AJAX)
     */
    public function getRecentVisitors(Request $request)
    {
        $username = $request->get('username');
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $visitors = $this->profileVisitorService->getRecentVisitors($user, 10);

        return response()->json([
            'visitors' => $visitors->map(function ($visitor) {
                return [
                    'id' => $visitor->visitor->id,
                    'name' => $visitor->visitor->full_name,
                    'username' => $visitor->visitor->username,
                    'profile_image' => $visitor->visitor->profile_image,
                    'visited_at' => $visitor->visited_at->diffForHumans(),
                ];
            })
        ]);
    }
}