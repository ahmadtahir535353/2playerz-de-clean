<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Followers;
use App\Models\Post;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerProfileController extends Controller
{
    protected function resolveUserByIdentifier($identifier): User
    {
        return User::query()
            ->where('username', $identifier)
            ->orWhere(function ($query) use ($identifier) {
                if (is_numeric($identifier)) {
                    $query->where('id', (int) $identifier);
                }
            })
            ->firstOrFail();
    }

    public function showProfile()
    {
        $customer = Auth::user();
        $customer->comments_count = DB::table('comments')
            ->where('user_id', $customer->id)
            ->count();

        $customer->likes_count = DB::table('likes')
            ->where('user_id', $customer->id)
            ->count();
        
        // Get followings - first 8 users (4 in first row, 4 in second row)
        $followings = Followers::where('following', $customer->id)
            ->with('follower')
            ->take(8)
            ->get();
        
        // Get total followings count
        $totalFollowings = Followers::where('following', $customer->id)->count();
        
        // Calculate remaining count
        $remainingCount = $totalFollowings > 8 ? $totalFollowings - 8 : 0;
        
        $psnCardUrl = $customer->psn_id ? "https://card.exophase.com/psn/{$customer->psn_id}.png" : null;
        $xboxCardUrl = $customer->xbox_live_id ? "https://card.exophase.com/xbox/{$customer->xbox_live_id}.png" : null;    
        return view('customer-panel.profile', compact('customer', 'psnCardUrl', 'xboxCardUrl', 'followings', 'totalFollowings', 'remainingCount'));
    }

    public function edit()
    {
        $customer = Auth::user();
        return view('customer-panel.edit-profile', compact('customer'));
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'avatar' => 'nullable|image|max:2048',
            'psn_id' => 'nullable|string|max:255', 
            'xbox_live_id' => 'nullable|string|max:255',
            'who_can_send_messages' => 'required|in:all,following,nobody',
            'message_notification_preference' => 'required|in:notification_only,email_and_notification',
        ]);

        $user->email = $request->email;
        $user->psn_id = $request->psn_id; 
        $user->xbox_live_id = $request->xbox_live_id;
        $user->about_me = $request->about_me;
        $user->location = $request->location;
        $user->occupation = $request->occupation;
        $user->consoles = $request->consoles;
        $user->accessories = $request->accessories;
        $user->favorite_games = $request->favorite_games;
        $user->favorite_genre = $request->favorite_genre;
        $user->favorite_series = $request->favorite_series;
        $user->favorite_films = $request->favorite_films;
        $user->favorite_music = $request->favorite_music;
        $user->hobbies = $request->hobbies;
        $user->my_motto = $request->my_motto;
        $user->theme = $request->theme;
        $user->who_can_send_messages = $request->who_can_send_messages;
        $user->message_notification_preference = $request->message_notification_preference;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            // Clear existing profile media collection to replace old avatar
            $user->clearMediaCollection(User::PROFILE);
            
            // Add new avatar using Spatie Media Library
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection(User::PROFILE, config('app.media_disc', 'public'));
        }

        $user->last_activity_at = \Illuminate\Support\Carbon::now();
        
        if (!$user->is_username_edit && $request->filled('username') && $user->username !== $request->username) {
            $user->username = $request->username;
            $user->is_username_edit = true;
        }    

        $user->save();

        return redirect()->route('customer.profile.edit')->with('success', __('messages.other_lang.profile_success_message'));
    }


    public function myComments()
    {
        $user = Auth::user();
        $comments = Comment::where('user_id', $user->id)
            ->with('post')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        if (request()->ajax()) {
            $html = view('customer-panel.partials.comments-list', compact('comments'))->render();

            return response()->json([
                'html' => $html,
                'hasMore' => $comments->hasMorePages(),
                'nextPage' => $comments->currentPage() + 1,
            ]);
        }

        return view('customer-panel.my-comments', compact('comments'));
    }

    // public function notification(){
    //     return view('customer-panel.notifications');
    // }


    public function notification(Request $request)
    {
        $filter = $request->get('filter', 'all');
        
        $notifications = DB::table('notifications')
            ->where('to_user_id', auth()->id())
            ->latest()
            ->get();

        // Apply filter if provided
        if ($filter !== 'all') {
            $notifications = $notifications->filter(function($notification) use ($filter) {
                $data = json_decode($notification->data, true);
                $message = strtolower($data['message'] ?? '');
                $type = $notification->type ?? '';
                
                if ($filter === 'comments') {
                    // Exclude AggregatedFollowingActivityNotification (followed users' activities)
                    if ($type === 'App\\Notifications\\AggregatedFollowingActivityNotification') {
                        return false;
                    }
                    
                    // Exclude messages about followed users' activities
                    if (str_contains($message, 'mitglieder, denen du folgst') || 
                        str_contains($message, 'mitglied, dem du folgst') ||
                        str_contains($message, 'members you follow') ||
                        str_contains($message, 'member you follow')) {
                        return false;
                    }
                    
                    // Exclude like notifications
                    $hasLikeKeyword = str_contains($message, 'gefällt') || 
                                      str_contains($message, 'liked');
                    if ($hasLikeKeyword) {
                        return false;
                    }
                    
                    // Only include actual comment replies (antwortet/replied to your comment)
                    // This ensures only direct comment replies are shown, not general activity
                    return str_contains($message, 'antwortet') || 
                           str_contains($message, 'replied');
                } elseif ($filter === 'likes') {
                    // Filter for likes (including likes on comments)
                    return str_contains($message, 'gefällt') || 
                           str_contains($message, 'liked');
                } elseif ($filter === 'following') {
                    // Filter for actions of members I follow
                    return $type === 'App\\Notifications\\AggregatedFollowingActivityNotification' ||
                           str_contains($message, 'folgt') || 
                           str_contains($message, 'follow') ||
                           str_contains($message, 'mitglieder, denen du folgst');
                }
                return true;
            });
        }

        // Convert to paginated collection
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $items = $notifications->forPage($currentPage, $perPage);
        $total = $notifications->count();
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($request->ajax()) {
            return response()->json([
                'data' => $items->values()->all(),
                'next_page_url' => $paginator->nextPageUrl(),
                'has_more_pages' => $paginator->hasMorePages()
            ]);
        }

        $notifications = $paginator;
        return view('customer-panel.notifications', compact('notifications', 'filter'));
    }

    public function markAsRead($id)
    {
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where('to_user_id', auth()->id())
            ->first();

        if (!$notification) {
            return redirect()->back();
        }

        DB::table('notifications')
            ->where('id', $id)
            ->update(['read_at' => now()]);

        $data = json_decode($notification->data, true) ?? [];
        $actionUrl = $data['action_url'] ?? null;
        if ($actionUrl) {
            return redirect($actionUrl);
        }
        $postId = $data['post_id'] ?? null;
        $commentId = $data['comment_id'] ?? null;
        $post = Post::find($postId);

        if ($post) {
            return redirect()->route('detailPage', $post->slug . '#comment-' . $commentId);
        }

        return redirect()->back();
    }

    public function deleteNotification($id)
    {
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where('to_user_id', auth()->id())
            ->first();

        if ($notification) {
            DB::table('notifications')
                ->where('id', $id)
                ->where('to_user_id', auth()->id())
                ->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    public function deleteAllNotifications()
    {
        $deleted = DB::table('notifications')
            ->where('to_user_id', auth()->id())
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alle Benachrichtigungen wurden gelöscht.',
            'deleted_count' => $deleted
        ]);
    }

    public function publicProfile($identifier)
    {
        $user = $this->resolveUserByIdentifier($identifier);
        $user->comments_count = DB::table('comments')
            ->where('user_id', $user->id)
            ->count();

        $user->likes_count = DB::table('likes')
            ->where('user_id', $user->id)
            ->count();
        
        // Track profile visit
        $profileVisitorService = app(\App\Services\ProfileVisitorService::class);
        $profileVisitorService->trackVisit($user, request());
        
        // Get followings - first 8 users (4 in first row, 4 in second row) for the viewed user
        $followings = Followers::where('following', $user->id)
            ->with('follower')
            ->take(8)
            ->get();
        
        // Get total followings count
        $totalFollowings = Followers::where('following', $user->id)->count();
        
        // Calculate remaining count
        $remainingCount = $totalFollowings > 8 ? $totalFollowings - 8 : 0;
        
        $psnCardUrl = $user->psn_id ? "https://card.exophase.com/psn/{$user->psn_id}.png" : null;
        $xboxCardUrl = $user->xbox_live_id ? "https://card.exophase.com/xbox/{$user->xbox_live_id}.png" : null;

        $isBlockedByMe = auth()->check() && auth()->id() !== (int) $user->id
            ? UserBlock::where('blocker_id', auth()->id())->where('blocked_id', $user->id)->exists()
            : false;

        if ((string) $identifier !== (string) $user->username) {
            return redirect()->route('user.public.profile', $user->username, 301);
        }

        return view('customer-panel.user-profile', compact('user', 'psnCardUrl', 'xboxCardUrl', 'followings', 'totalFollowings', 'remainingCount', 'isBlockedByMe'));
    }


    public function publicComments(Request $request, $identifier)
    {
        $user = $this->resolveUserByIdentifier($identifier);
        $perPage = 5;
        $page = $request->input('page', 1);

        // If viewer and profile user have a block relationship, show no comments
        if (auth()->check() && UserBlock::isBlockedBetween(auth()->id(), $user->id)) {
            $comments = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1);
        } else {
            $comments = Comment::where('user_id', $user->id)
                ->with('post')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        }

        if ($request->ajax()) {
            $html = view('customer-panel.partials.public-comment-item', [
                'comments' => $comments,
                'user' => $user
            ])->render();

            return response()->json([
                'html' => $html,
                'hasMore' => $comments->hasMorePages(),
                'nextPage' => $comments->currentPage() + 1,
            ]);
        }

        return view('customer-panel.user-public-comments', compact('user', 'comments'));
    }

    public function followUser($identifier)
    {
        $userToFollow = $this->resolveUserByIdentifier($identifier);
        $currentUser = Auth::user();

        // Check if already following
        $alreadyFollowing = Followers::where('following', $currentUser->id)
            ->where('followers', $userToFollow->id)
            ->exists();

        if (!$alreadyFollowing) {
            Followers::create([
                'following' => $currentUser->id,
                'followers' => $userToFollow->id,
            ]);

            // Send notification to the user being followed
            DB::table('notifications')->insert([
                'type' => 'App\\Notifications\\NewFollowerNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userToFollow->id,
                'to_user_id' => $userToFollow->id,
                'from_user_id' => $currentUser->id,
                'post_id' => null,
                'data' => json_encode([
                    'message' => $currentUser->username . ' folgt dir jetzt',
                    'follower_name' => $currentUser->username,
                    'follower_id' => $currentUser->id,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $message = __('messages.followed_successfully', ['username' => $userToFollow->username]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->route('user.public.profile', $userToFollow->username)
            ->with('success', $message);
    }

    public function unfollowUser($identifier)
    {
        $userToUnfollow = $this->resolveUserByIdentifier($identifier);
        $currentUser = Auth::user();

        Followers::where('following', $currentUser->id)
            ->where('followers', $userToUnfollow->id)
            ->delete();

        $message = __('messages.unfollowed_successfully', ['username' => $userToUnfollow->username]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->route('user.public.profile', $userToUnfollow->username)
            ->with('success', $message);
    }

    public function blockUser($identifier)
    {
        $currentUser = Auth::user();
        $userToBlock = $this->resolveUserByIdentifier($identifier);

        if ($currentUser->id === $userToBlock->id) {
            if (request()->ajax()) {
                return response()->json(['error' => __('messages.block.cannot_block_self')], 400);
            }
            return redirect()->back()->with('error', __('messages.block.cannot_block_self'));
        }

        UserBlock::firstOrCreate(
            ['blocker_id' => $currentUser->id, 'blocked_id' => $userToBlock->id]
        );

        $message = __('messages.block.blocked_successfully', ['username' => $userToBlock->username]);
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return redirect()->route('user.public.profile', $userToBlock->username)->with('success', $message);
    }

    public function unblockUser($identifier)
    {
        $currentUser = Auth::user();
        $userToUnblock = $this->resolveUserByIdentifier($identifier);

        UserBlock::where('blocker_id', $currentUser->id)->where('blocked_id', $userToUnblock->id)->delete();

        $message = __('messages.block.unblocked_successfully', ['username' => $userToUnblock->username]);
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return redirect()->route('blocked.members')->with('success', $message);
    }

    public function blockedMembers(Request $request)
    {
        $currentUser = Auth::user();
        $blockedUsers = UserBlock::where('blocker_id', $currentUser->id)
            ->with('blocked')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('customer-panel.blocked-members', compact('blockedUsers'));
    }

    public function membersFollowing(Request $request)
    {
        $currentUser = Auth::user();
        
        // Get users that the current user is following
        // Order by latest followed first (created_at desc)
        // Show 8 per page for better performance
        $followingUsers = Followers::where('following', $currentUser->id)
            ->with('follower')
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        // Get following user IDs
        $followingUserIds = Followers::where('following', $currentUser->id)
            ->pluck('followers')
            ->toArray();

        // Get recent activities (comments) from following members with pagination (3 per page)
        $activitiesQuery = \App\Models\Comment::whereIn('user_id', $followingUserIds)
            ->where('status', 1)
            ->with(['users', 'post'])
            ->orderBy('created_at', 'desc');

        // If AJAX request for activities
        if ($request->ajax() && $request->has('activities_page')) {
            $activitiesPage = $request->get('activities_page', 1);
            $activitiesPaginated = $activitiesQuery->paginate(3, ['*'], 'activities_page', $activitiesPage);
            
            $activities = $activitiesPaginated->getCollection()->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'username' => $comment->users->username ?? 'Unknown',
                    'profile_image' => $comment->users->profile_image ?? asset('web/media/avatars/150-2.jpg'),
                    'post_id' => $comment->post_id,
                    'post_title' => $comment->post->title ?? 'Unknown Post',
                    'post_slug' => $comment->post->slug ?? '#',
                    'comment_text' => \Str::limit(strip_tags($comment->comment), 150),
                    'created_at' => $comment->created_at,
                    'created_at_human' => $comment->created_at->diffForHumans(),
                ];
            });
            
            $activitiesHtml = view('customer-panel.partials.activities-list', compact('activities'))->render();
            
            return response()->json([
                'html' => $activitiesHtml,
                'hasMore' => $activitiesPaginated->hasMorePages(),
                'nextPage' => $activitiesPaginated->currentPage() + 1,
            ]);
        }

        // If AJAX request for following users (check for page parameter, not activities_page)
        if ($request->ajax() && !$request->has('activities_page')) {
            // Re-fetch following users with the requested page
            $page = $request->get('page', 1);
            $followingUsers = Followers::where('following', $currentUser->id)
                ->with('follower')
                ->orderBy('created_at', 'desc')
                ->paginate(8, ['*'], 'page', $page);
            
            $html = view('customer-panel.partials.following-cards', compact('followingUsers'))->render();
            
            return response()->json([
                'html' => $html,
                'hasMore' => $followingUsers->hasMorePages(),
                'nextPage' => $followingUsers->currentPage() + 1,
            ]);
        }

        // Get first page of activities (3 per page)
        $activitiesPaginated = $activitiesQuery->paginate(3, ['*'], 'activities_page', 1);
        
        $activities = $activitiesPaginated->getCollection()->map(function($comment) {
            return [
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'username' => $comment->users->username ?? 'Unknown',
                'profile_image' => $comment->users->profile_image ?? asset('web/media/avatars/150-2.jpg'),
                'post_id' => $comment->post_id,
                'post_title' => $comment->post->title ?? 'Unknown Post',
                'post_slug' => $comment->post->slug ?? '#',
                'comment_text' => \Str::limit(strip_tags($comment->comment), 150),
                'created_at' => $comment->created_at,
                'created_at_human' => $comment->created_at->diffForHumans(),
            ];
        });

        $activitiesHasMore = $activitiesPaginated->hasMorePages();

        return view('customer-panel.members-following', compact('followingUsers', 'activities', 'activitiesHasMore'));
    }

}

