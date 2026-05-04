<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Models\Album;
use App\Models\AlbumCategory;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Emoji;
use App\Models\Followers;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\Poll;
use App\Models\PointRule;
use App\Models\PollResult;
use App\Models\PostReactionEmoji;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Models\Subscriber;
use App\Scopes\LanguageScope;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory as FirebaseFactory;

class LandingPageController extends AppBaseController
{
    public function toggleLike(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = auth()->id();
        $itemId = $request->item_id;
        $itemType = $request->item_type;
        $likePoints = (int) (PointRule::where('key', 'like')->value('points') ?? 0);
        $like = DB::table('likes')
            ->where('user_id', $userId)
            ->where('item_id', $itemId)
            ->where('item_type', $itemType)
            ->first();

        if ($like) {
            // Unlike
            DB::table('likes')
                ->where('user_id', $userId)
                ->where('item_id', $itemId)
                ->where('item_type', $itemType)
                ->delete();

            $liked = false;
            if ($itemType === 'post') {
                DB::table('users')->where('id', $userId)->decrement('comment_points', $likePoints);
                // Delete notification when post is unliked
                $this->deletePostLikeNotification($itemId);
            }

            // Delete notification when comment is unliked
            if ($itemType === 'comment') {
                $this->deleteLikeNotification($itemId);
            }
        } else {
            // Like — block: cannot like a comment from a user you have blocked or who blocked you
            if ($itemType === 'comment') {
                $comment = Comment::find($itemId);
                if ($comment && $comment->user_id && UserBlock::isBlockedBetween($userId, $comment->user_id)) {
                    return response()->json(['message' => __('messages.block.cannot_reply_blocked')], 403);
                }
            }
            DB::table('likes')->insert([
                'user_id' => $userId,
                'item_id' => $itemId,
                'item_type' => $itemType,
                'created_at' => now(),
            ]);

            $liked = true;
             if ($itemType === 'post') {
                DB::table('users')->where('id', $userId)->increment('comment_points', $likePoints);
                $this->sendPostLikeNotification($itemId);
            }

            // Send notification if it's a comment
            if ($itemType === 'comment') {
                $this->sendLikeNotification($itemId);
            }
        }

        // Get updated like count
        $likeCount = DB::table('likes')
            ->where('item_id', $itemId)
            ->where('item_type', $itemType)
            ->count();

        $this->updateLastActivity();

        return response()->json([
            'liked' => $liked,
            'likes' => $likeCount,
        ]);
    }

    public function index()
{
    //        dd(App::getLocale());
    //Header
    start_measure('render', 'sliderPosts');
    $data['sliderPosts'] = Post::select([
            'id', 'slug', 'post_types', 'title', 'created_at', 'category_id', 'created_by', 'image_copyright'
        ])
        ->with([
            'category:id,slug,name,color',
            'user:id,username,first_name,last_name',
            'postVideo:id,post_id,thumbnail_image_url',
            'postVideo.media',
            'media'
        ])
        ->withCount('comment')
        ->orderBy('id', 'desc')
        ->whereSlider(1)
        ->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->get();
    stop_measure('render', 'sliderPosts');

    start_measure('render', 'categories');
    $data['categories'] = Category::with(['posts' => function ($query) {
            $query->where('visibility', Post::VISIBILITY_ACTIVE)
                  ->orderBy('id', 'desc')
                  ->limit(4)
                  ->withCount('comment');
        }, 'posts.user'])
        ->whereHas('posts', function ($q) {
            return $q->where('visibility', Post::VISIBILITY_ACTIVE);
        })
        ->whereShowInHomePage(1)
        ->get();
    stop_measure('render', 'categories');

    start_measure('render', 'headlinePosts');
    $headlinePosts = Post::select([
            'id', 'slug', 'post_types', 'title', 'created_at', 'category_id', 'created_by', 'image_copyright'
        ])
        ->with([
            'category:id,slug,name,color',
            'user:id,username,first_name,last_name',
            'postVideo:id,post_id,thumbnail_image_url',
            'postVideo.media',
            'media'
        ])
        ->withCount('comment')
        ->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->where('show_on_headline', 1)
        ->latest('created_at'); // ✅ explicit latest so first() is latest  
    stop_measure('render', 'headlinePosts');

    start_measure('render', 'firstHeadlinePost');
    $data['firstHeadlinePost'] = $headlinePosts->first();
    stop_measure('render', 'firstHeadlinePost');

    start_measure('render', 'headlinePosts');
    $data['headlinePosts'] = $headlinePosts->latest()->take(4)->get();
    stop_measure('render', 'headlinePosts');

    start_measure('render', 'breakingPosts');
    $data['breakingPosts'] = $headlinePosts->latest()->skip(1)->take(3)->get();
    stop_measure('render', 'breakingPosts');

    start_measure('render', 'featurePosts');
    $featurePosts =Post::select([
            'id', 'slug', 'post_types', 'title', 'created_at', 'category_id', 'created_by', 'image_copyright'
        ])
        ->with([
            'category:id,slug,name,color',
            'user:id,username,first_name,last_name',
            'postVideo:id,post_id,thumbnail_image_url',
            'postVideo.media',
            'media'
        ])
        ->where('featured', 1)
        ->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->latest('id'); // ✅ so first() is newest featured
    stop_measure('render', 'featurePosts');

    start_measure('render', 'firstFeaturePost');
    $data['firstFeaturePost'] = $featurePosts->first();
    stop_measure('render', 'firstFeaturePost');

    start_measure('render', 'topStoryPosts');
    $data['topStoryPosts'] = Post::select([
            'id', 'slug', 'post_types', 'title', 'created_at', 'category_id', 'created_by', 'image_copyright'
        ])
        ->with([
            'category:id,slug,name,color',
            'user:id,username,first_name,last_name',
            'postVideo:id,post_id,thumbnail_image_url',
            'postVideo.media',
            'media'
        ])
        ->where('recommended', 1)
        ->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->orderBy('id', 'desc')
        ->take(4)
        ->get();
    stop_measure('render', 'topStoryPosts');

    start_measure('render', 'latestPosts');
    $data['latestPosts'] = Post::select([
'id', 'slug', 'post_types', 'title', 'created_at', 'description', 'category_id', 'created_by', 'image_copyright'
        ])
        ->with([
            'category:id,slug,name,color',
            'user:id,username,first_name,last_name',
            'postVideo:id,post_id,thumbnail_image_url',
            'postVideo.media',
            'media'
        ])
        ->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->withCount('comment')
        ->get();
    stop_measure('render', 'latestPosts');

    start_measure('render', 'postCategory');
    $data['postCategory'] = Category::select(['id', 'name'])
        ->with(['posts' => function (HasMany $q) {
            $q->select(['id', 'slug', 'post_types', 'title', 'created_at', 'category_id', 'created_by', 'image_copyright'])
              ->with([
                  'category:id,name,color',
                  'user:id,username,first_name,last_name',
                  'postVideo:id,post_id,thumbnail_image_url',
                  'postVideo.media',
                  'media'
              ])
              ->orderByDesc('created_at')
              ->withCount('comment')
              ->take(4); 
        }])
        ->whereHas('posts', function ($q) {
            return $q->where('visibility', Post::VISIBILITY_ACTIVE)
                     ->where('show_on_headline', 1);
        })
        ->where('show_in_home_page', 1)
        ->latest()
        ->take(4)
        ->get();
    stop_measure('render', 'postCategory');

    start_measure('render', 'featurePostCategory');
    $data['featurePostCategory'] = Category::whereHas('posts', function ($q) {
            $q->where('visibility', Post::VISIBILITY_ACTIVE)
              ->where('featured', '=', 1)
              ->orderBy('id', 'desc');
        })
        ->where('show_in_home_page', 1)
        ->with(['posts' => function ($q) {
            $q->where('visibility', Post::VISIBILITY_ACTIVE)
              ->where('featured', '=', 1)
              ->orderBy('id', 'desc')
              ->withCount('comment')
              ->take(4); // ✅ IMPORTANT: no ->get() here
        }])
        ->latest()
        ->take(4)
        ->get();
    stop_measure('render', 'featurePostCategory');

    start_measure('render', 'getTrendingPosts');
    $data['getTrendingPosts'] = getTrendingPost();
    stop_measure('render', 'getTrendingPosts');

    start_measure('render', 'getPopulerCategories');
    $data['getPopulerCategories'] = getPopulerCategories();
    stop_measure('render', 'getPopulerCategories');

    start_measure('render', 'getPopularNews');
    $data['getPopularNews'] = getPopularNews();
    stop_measure('render', 'getPopularNews');

    start_measure('render', 'getRecommendedPost');
    $data['getRecommendedPost'] = getRecommendedPost();
    stop_measure('render', 'getRecommendedPost');

    start_measure('render', 'getPopularTags');
    $data['getPopularTags'] = getPopularTags();
    stop_measure('render', 'getPopularTags');

    start_measure('render', 'getPoll');
    $data['getPoll']  = getPoll();
    $data['hasVoted'] = true;
    stop_measure('render', 'getPoll');

    start_measure('render', 'getOption');
    $data['getOption'] = getOption();
    stop_measure('render', 'getOption');

    start_measure('render', 'getCategories');
    $data['getCategories'] = Category::withCount('posts')
        ->orderBy('posts_count', 'desc')
        ->take(20) // ✅ avoid pulling all
        ->get();
    stop_measure('render', 'getCategories');

    // latest comments — was unbounded; add limit
    start_measure('render', 'latestComment');
    $data['latestComment'] = Comment::with('post', 'users')
        ->latest()
        ->take(10) // ✅ avoid pulling all comments
        ->get();
    stop_measure('render', 'latestComment');

    // Top 3 Users by Playerz Points (excluding staff, editors, moderators)
    start_measure('render', 'topPlayers');
    $data['topPlayers'] = \App\Models\User::where(function ($q) {
            // include NULL + all non-admin users (some “normal” users may have type=2)
            $q->whereNull('type')
                ->orWhere('type', '!=', \App\Models\User::ADMIN);
        })
        ->where(function ($q) {
            $q->whereNull('is_editor')->orWhere('is_editor', 0);
        })
        ->where(function ($q) {
            $q->whereNull('is_moderator')->orWhere('is_moderator', 0);
        })
        ->orderByDesc('comment_points')
        ->take(3)
        ->get();
    stop_measure('render', 'topPlayers');

    // Latest Members - Last 4 newly registered users (excluding admins)
    start_measure('render', 'latestMembers');
    $data['latestMembers'] = \App\Models\User::where(function($query) {
            $query->where('type', '!=', \App\Models\User::ADMIN)
                  ->where(function($q) {
                      $q->where('type', \App\Models\User::STAFF)
                        ->orWhereNull('type');
                  });
        })
        ->orderBy('created_at', 'desc')
        ->take(4)
        ->get();
    stop_measure('render', 'latestMembers');

    if (getCurrentTheme() == 1) {
        return view('theme1.home')->with($data);
    }
    return view('front_new.home')->with($data);
}

/**
 * Show the Playerz Rankings page
 */
public function playerzRanking()
{
    start_measure('render', 'playerzRanking');
    
    // Get page settings
    $settings = \App\Models\PlayerzRankingSetting::where('is_active', true)
        ->with('creator')
        ->first();
    if (!$settings) {
        // Create default settings if none exist
        $settings = \App\Models\PlayerzRankingSetting::create([
            'page_title' => 'User-Ranking',
            'page_subtitle' => 'Die besten Spieler unserer Community',
            'header_description' => 'Hier findest du die aktivsten Mitglieder unserer Community, sortiert nach ihren gesammelten Playerz Points.',
            'points_rules_content' => '<h3>Wie funktioniert das Playerz Points System?</h3><p>Sammle Punkte durch aktive Teilnahme an unserer Community!</p>',
            'is_active' => true,
        ]);
        $settings->load('creator');
    }
    
    // Record page view (once per session)
    $playerzViewKey = 'playerz_ranking_view_' . $settings->id;
    if (!session()->has($playerzViewKey)) {
        session()->put($playerzViewKey, true);
        $settings->increment('views_count');
    }
    
    // Top 10 Users by Points (excluding staff, editors, moderators)
    $topPlayers = \App\Models\User::rankingOnly()
        ->orderByDesc('comment_points')
        ->take(10)
        ->get();
    // (topPlayers uses rankingOnly() above; duplicate block removed)
    if (false) {
        \App\Models\User::where(function ($q) {
            // include NULL + all non-admin users (some “normal” users may have type=2)
            $q->whereNull('type')
                ->orWhere('type', '!=', \App\Models\User::ADMIN);
        })
        ->where(function ($q) {
            $q->whereNull('is_editor')->orWhere('is_editor', 0);
        })
        ->where(function ($q) {
            $q->whereNull('is_moderator')->orWhere('is_moderator', 0);
        })
        ->orderByDesc('comment_points')
        ->take(10)
        ->get();
    }

    // Ranking page stats (likes + comments) stored against item_type=page, item_id=settings_id
    $pageItemType = 'page';
    $pageItemId = $settings->id;

    $pageLikesCount = DB::table('likes')
        ->where('item_type', $pageItemType)
        ->where('item_id', $pageItemId)
        ->count();

    // Comments table might not have item_type/item_id yet on older DBs.
    $pageCommentsCount = 0;
    if (Schema::hasColumn('comments', 'item_type') && Schema::hasColumn('comments', 'item_id')) {
        // Fix: Update any replies that don't have item_type/item_id (inherit from parent)
        // Find parent comments for this page
        $parentCommentIds = \App\Models\Comment::where('status', 1)
            ->where('item_type', $pageItemType)
            ->where('item_id', $pageItemId)
            ->whereNull('parent_id')
            ->pluck('id');
        
        // Find replies to those parents that are missing item_type/item_id
        $repliesToFix = \App\Models\Comment::where('status', 1)
            ->whereIn('parent_id', $parentCommentIds)
            ->where(function($q) {
                $q->whereNull('item_type')->orWhereNull('item_id');
            })
            ->get();
        
        foreach ($repliesToFix as $reply) {
            $parent = \App\Models\Comment::find($reply->parent_id);
            if ($parent && $parent->item_type && $parent->item_id) {
                $reply->item_type = $parent->item_type;
                $reply->item_id = $parent->item_id;
                $reply->save();
            }
        }
        
        // Count ALL comments (parent + replies) for this page
        $pageCommentsCount = \App\Models\Comment::where('status', 1)
            ->where('item_type', $pageItemType)
            ->where('item_id', $pageItemId)
            ->count();
    }

    $pageUserLiked = false;
    if (Auth::check()) {
        $pageUserLiked = (bool) DB::table('likes')
            ->where('user_id', Auth::id())
            ->where('item_type', $pageItemType)
            ->where('item_id', $pageItemId)
            ->exists();
    }

    // Top 10 Commenting Users (most comments; ranking only)
    $commentCounts = \App\Models\Comment::where('status', 1)
        ->whereNotNull('user_id')
        ->selectRaw('user_id, count(*) as comments_count')
        ->groupBy('user_id')
        ->orderByDesc('comments_count')
        ->take(10)
        ->get();
    $commenterIds = $commentCounts->pluck('user_id')->filter()->unique()->values()->all();
    $commenterUsers = \App\Models\User::rankingOnly()->whereIn('id', $commenterIds)->get()->keyBy('id');
    $topCommentingUsers = $commentCounts->map(function ($row) use ($commenterUsers) {
        $user = $commenterUsers->get($row->user_id);
        return $user ? (object)['user' => $user, 'comments_count' => (int) $row->comments_count] : null;
    })->filter()->values();

    // Top users by likes given (same metric as public profile likes_count; ranking only)
    $mostActive = \App\Models\User::rankingOnly()
        ->selectRaw('users.*, (SELECT COUNT(*) FROM likes WHERE likes.user_id = users.id) as likes_given')
        ->havingRaw('likes_given > 0')
        ->orderByDesc('likes_given')
        ->take(10)
        ->get();

    // Users with most likes on their comments (ranking only)
    $commentLikesRank = DB::table('comments')
        ->join('likes', function ($j) {
            $j->on('likes.item_id', '=', 'comments.id')->where('likes.item_type', '=', 'comment');
        })
        ->whereNotNull('comments.user_id')
        ->selectRaw('comments.user_id, count(likes.id) as comment_likes_count')
        ->groupBy('comments.user_id')
        ->orderByDesc('comment_likes_count')
        ->limit(10)
        ->get();
    $commentLikeUserIds = $commentLikesRank->pluck('user_id')->unique()->values()->all();
    $commentLikeUsers = \App\Models\User::rankingOnly()->whereIn('id', $commentLikeUserIds)->get()->keyBy('id');
    $mostLikedCommentAuthors = $commentLikesRank->map(function ($row) use ($commentLikeUsers) {
        $user = $commentLikeUsers->get($row->user_id);
        return $user ? (object)['user' => $user, 'comment_likes_count' => (int) $row->comment_likes_count] : null;
    })->filter()->values();

    $showType = request('show'); // 'commenting' | 'points' | 'active' | 'comment-likes'
    $seeAllPaginator = null;
    if (in_array($showType, ['commenting', 'points', 'active', 'comment-likes'], true)) {
        $perPage = 25;
        if ($showType === 'commenting') {
            $seeAllPaginator = \App\Models\User::rankingOnly()
                ->whereIn('id', \App\Models\Comment::where('status', 1)->whereNotNull('user_id')->distinct()->pluck('user_id'))
                ->withCount(['comments as comments_count' => fn ($q) => $q->where('status', 1)])
                ->having('comments_count', '>', 0)
                ->orderByDesc('comments_count')
                ->paginate($perPage);
        } elseif ($showType === 'points') {
            $seeAllPaginator = \App\Models\User::rankingOnly()
                ->where('comment_points', '>', 0)
                ->orderByDesc('comment_points')
                ->paginate($perPage);
        } elseif ($showType === 'active') {
            // "active" now means: users who gave most likes (same as public profile likes_count)
            $seeAllPaginator = \App\Models\User::rankingOnly()
                ->selectRaw('users.*, (SELECT COUNT(*) FROM likes WHERE likes.user_id = users.id) as likes_given')
                ->havingRaw('likes_given > 0')
                ->orderByDesc('likes_given')
                ->paginate($perPage);
        } else {
            $userIdsWithCommentLikes = DB::table('comments')
                ->join('likes', function ($j) {
                    $j->on('likes.item_id', '=', 'comments.id')->where('likes.item_type', '=', 'comment');
                })
                ->whereNotNull('comments.user_id')
                ->distinct()
                ->pluck('comments.user_id');
            $seeAllPaginator = \App\Models\User::rankingOnly()
                ->whereIn('id', $userIdsWithCommentLikes)
                ->get()
                ->map(function ($user) {
                    $count = (int) DB::table('comments')
                        ->join('likes', function ($j) {
                            $j->on('likes.item_id', '=', 'comments.id')->where('likes.item_type', '=', 'comment');
                        })
                        ->where('comments.user_id', $user->id)
                        ->count();
                    $user->comment_likes_count = $count;
                    return $user;
                })
                ->sortByDesc('comment_likes_count')
                ->values();
            $seeAllPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $seeAllPaginator->forPage(request('page', 1), $perPage)->values(),
                $seeAllPaginator->count(),
                $perPage,
                request('page', 1),
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }
    }

    $data = [
        'settings' => $settings,
        'topPlayers' => $topPlayers,
        'topCommentingUsers' => $topCommentingUsers,
        'mostActive' => $mostActive,
        'mostLikedCommentAuthors' => $mostLikedCommentAuthors,
        'pageItemType' => $pageItemType,
        'pageItemId' => $pageItemId,
        'pageLikesCount' => $pageLikesCount,
        'pageCommentsCount' => $pageCommentsCount,
        'pageUserLiked' => $pageUserLiked,
        'showType' => $showType,
        'seeAllPaginator' => $seeAllPaginator,
    ];

    stop_measure('render', 'playerzRanking');

    if (getCurrentTheme() == 1 && view()->exists('theme1.playerz-ranking')) {
        return view('theme1.playerz-ranking')->with($data);
    }
    return view('front_new.playerz-ranking')->with($data);
}


    public function markAsRead($notificationId)
    {
        // Update using DB query builder
        $updated = DB::table('notifications')
            ->where('id', $notificationId)
            ->update(['read_at' => Carbon::now()]);

        // Check if any record was updated
        if ($updated) {
            return response()->json(['status' => 'read']);
        } else {
            return response()->json(['error' => 'Notification not found or already read'], 404);
        }
    }

    /**
     * Get unread notification count for current user
     */
    public function getUnreadCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $unreadCount = DB::table('notifications')
            ->where('to_user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $unreadCount]);
    }

    public function detailPage(Request $request, $slug, $id = null)
    {
        if (empty(getLogInUser())) {
            $langId = Post::withoutGlobalScope(LanguageScope::class)->whereSlug($slug)->first();
            if (!empty($langId['lang_id'])) {
                session(['frontLanguageChange' => $langId['lang_id']]);
            }
        }

        if (!empty($request->ajax())) {
            session(['frontLanguageChange' => $request->data]);
            return $this->sendSuccess(__('messages.placeholder.language_change_successfully'));
        }

        $post = Post::select([
            'id', 'slug', 'title', 'description', 'post_types', 'created_at', 'category_id', 'created_by', 
            'tags', 'keywords', 'rss_id', 'rss_link', 'optional_url', 'comment_enabled', 'image_copyright'
        ])
        ->with([
            'category:id,name,slug,color',
            'user:id,username,first_name,last_name',
            'postArticle:id,post_id,article_content',
            'postVideo:id,post_id,thumbnail_image_url,video_url,video_embed_code',
            'postVideo.media',
            'postAudios:id,post_id',
            'postAudios.media',
            'postGalleries:id,post_id',
            'postGalleries.media',
            'postSortLists:id,post_id,sort_list_title,sort_list_content',
            'postSortLists.media',
            'media',
            'rssFeed:id,feed_name,feed_url',
            'livetickerContent:id,post_id,title,header,footer,live_indicator_enabled,live_indicator_until',
            'livetickerPosts' => function($query) {
                $query->select('id', 'post_id', 'content', 'created_at')
                      ->orderBy('created_at', 'desc');
            },
        ])
        ->where('slug', $slug)
        ->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->firstOrFail();

        $user = auth()->user();

        // Attach likes_count
        $post->likes_count = DB::table('likes')
            ->where('item_id', $post->id)
            ->where('item_type', 'post')
            ->count();

        // Attach user_liked
        $post->user_liked = false;

        if ($user) {
            $post->user_liked = (bool) DB::table('likes')
                ->where('user_id', $user->id)
                ->where('item_id', $post->id)
                ->where('item_type', 'post')
                ->exists();
        }

        $data['showCaptcha'] = Setting::where('key', 'show_captcha')->first()->value;
        $post = (!empty($post) ? $post : null);

        // $previous = Post::where('id', '<', $post->id)->whereVisibility(Post::VISIBILITY_ACTIVE)->max('id');
        // $next = Post::where('id', '>', $post->id)->whereVisibility(Post::VISIBILITY_ACTIVE)->min('id');

        // if (empty($previous)) {
        //     $previous = Post::where('id', '>', $post->id)->whereVisibility(Post::VISIBILITY_ACTIVE)->max('id');
        // }

        // if (empty($next)) {
        //     $next = Post::where('id', '<', $post->id)->whereVisibility(Post::VISIBILITY_ACTIVE)->min('id');
        // }

        // $data['previousPost'] = Post::with('postVideo')->find($previous);
        // $data['nextPost'] = Post::with('postVideo')->find($next);

        $data['previousPost'] = null;
        $data['nextPost'] = null;

        // Fetch parent comments: visible OR hidden but with visible replies (show placeholder + replies)
        $comments = Comment::withTrashed()->with(['users', 'replies' => function ($query) {
            $query->withTrashed()->where('status', 1)->with('users');
        }])
            ->wherePostId($post->id)
            ->whereNull('parent_id')
            ->where(function ($q) {
                $q->where('status', 1)->orWhereHas('replies');
            })
            ->latest()
            ->paginate(2);

        foreach ($comments as $comment) {
            $comment->likes_count = DB::table('likes')
                ->where('item_id', $comment->id)
                ->where('item_type', 'comment')
                ->count();

            // Attach user_liked
            $comment->user_liked = false;

            if ($user) {
                $comment->user_liked = (bool) DB::table('likes')
                    ->where('user_id', $user->id)
                    ->where('item_id', $comment->id)
                    ->where('item_type', 'comment')
                    ->exists();
            }
            
            // Also set user_liked for replies
            foreach ($comment->replies as $reply) {
                $reply->user_liked = false;
                if ($user) {
                    $reply->user_liked = (bool) DB::table('likes')
                        ->where('user_id', $user->id)
                        ->where('item_id', $reply->id)
                        ->where('item_type', 'comment')
                        ->exists();
                }
            }
        }
        $data['comments'] = $comments;
        // Count all comments (parents + replies)
        $data['totalComments'] = Comment::where('post_id', $post->id)->where('status', 1)->count();

        $data['relatedPosts'] = Post::select([
            'id', 'slug', 'title', 'post_types', 'created_at', 'category_id', 'created_by'
        ])
        ->with([
            'category:id,name,slug,color',
            'user:id,username,first_name,last_name',
            'postVideo:id,post_id,thumbnail_image_url',
            'postVideo.media',
            'media'
        ])
        ->where('id', '!=', $post->id)
        ->where('category_id', $post->category_id)
        ->where('post_types', $post->post_types)
        ->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->latest()
        ->get();

        $data['countEmoji'] = PostReactionEmoji::wherePostId($post->id)->get()->groupBy('emoji_id');
        $data['emojis'] = Emoji::whereStatus(Emoji::ACTIVE)->get();

        //update user last activity
        $this->updateLastActivity();

        if (!empty($post->post_types)) {
            if ($post->post_types == Post::ARTICLE_TYPE_ACTIVE || $post->post_types == Post::OPEN_AI_ACTIVE) {
                $data['postDetail'] = $post;
                $data['getPoll'] = getPoll('article', $post->id);

                if (getCurrentTheme() == 1) {
                    return view('theme1.layouts.detailPages.articale-details')->with($data);
                }
                return view('front_new.detail_pages.article-details')->with($data);
            } elseif ($post->post_types == Post::GALLERY_TYPE_ACTIVE) {
                $data['postDetail'] = $post;
                $data['fileName'] = $data['postDetail']->getPostFileNameAttribute();
                $data['firstGalleryPost'] = $data['postDetail']->postGalleries->first();
                $data['lastGalleryPost'] = $data['postDetail']->postGalleries->last();
                $data['totalGalleryPost'] = count($data['postDetail']->postGalleries);
                if ((!empty($id)) && ($id > $data['totalGalleryPost'] || $id <= 0)) {
                    return redirect(route('detailPage', ['data' => $slug]));
                }
                $data['galleryPost'] = (!empty($id)) ? $data['postDetail']->postGalleries[$id - 1]
                    : $data['postDetail']->postGalleries->first();
                $data['galleryPostNo'] = (!empty($id)) ? $id : '1';
                $data['getPoll'] = getPoll('article', $post->id);

                if (getCurrentTheme() == 1) {
                    return view('theme1.layouts.detailPages.gallery-details')->with($data);
                }
                return view('front_new.detail_pages.gallery-details')->with($data);
            } elseif ($post->post_types == Post::SORTED_TYPE_ACTIVE) {
                $data['postDetail'] = $post;
                $data['getPoll'] = getPoll('article', $post->id);

                if (getCurrentTheme() == 1) {
                    return view('theme1.layouts.detailPages.sortedlist-details')->with($data);
                }
                return view('front_new.detail_pages.sortedlist-details')->with($data);
            } elseif ($post->post_types == Post::VIDEO_TYPE_ACTIVE) {
                $data['postDetail'] = $post;
                $data['getPoll'] = getPoll('article', $post->id);

                if (getCurrentTheme() == 1) {
                    return view('theme1.layouts.detailPages.video-details')->with($data);
                }
                return view('front_new.detail_pages.video-details')->with($data);
            } elseif ($post->post_types == Post::AUDIO_TYPE_ACTIVE) {
                $data['postDetail'] = $post;
                $data['getPoll'] = getPoll('article', $post->id);

                if (getCurrentTheme() == 1) {
                    return view('theme1.layouts.detailPages.audio-details')->with($data);
                }
                return view('front_new.detail_pages.audio-details')->with($data);
            } elseif ($post->post_types == Post::LIVETICKER_TYPE_ACTIVE) {
                $data['postDetail'] = $post;
                $data['getPoll'] = getPoll('article', $post->id); // optional agar polls use hote hain

                if (getCurrentTheme() == 1) {
                    return view('theme1.layouts.detailPages.liveticker-details')->with($data);
                }

                return view('front_new.detail_pages.liveticker-details')->with($data);
            }

            else {
                return redirect(route('front.home'));
            }
        }
    }

    public function saveSubscribeUser(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email:filter|unique:subscribers,email',
            ],
            [
                'email.unique' => __('messages.placeholder.this_email_is_already_subscribed'),
            ]
        );

        Subscriber::create([
            'email' => $request->email,
        ]);

        if (auth()->check()) {
            $user = auth()->user();

            if ($user->newsletter_bonus_given == 0) {
                $newsletterPoints = (int) (PointRule::where('key', 'newsletter_subscribe')->value('points') ?? 0);

                $user->comment_points = ($user->comment_points ?? 0) + $newsletterPoints;
                $user->newsletter_bonus_given = 1; // mark as given
                $user->save();
            }
        }

        return $this->sendSuccess(__('messages.placeholder.subscribed_successfully'));
    }


    protected function sendLikeNotification($commentId)
    {
        $comment = Comment::find($commentId);

        if (!$comment || $comment->user_id == auth()->id()) {
            return;
        }

        $exists = DB::table('notifications')
            ->where('to_user_id', $comment->user_id)
            ->where('from_user_id', auth()->id())
            ->where('post_id', $comment->post_id)
            ->whereJsonContains('data', ['comment_id' => $comment->id])
            ->where('type', 'App\\Notifications\\LikeCommentNotification')
            ->exists();

        if (!$exists) {
            DB::table('notifications')->insert([
                'type' => 'App\\Notifications\\LikeCommentNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $comment->id,
                'to_user_id' => $comment->user_id,
                'post_id' => $comment->post_id,
                'from_user_id' => auth()->id(),
                'data' => json_encode([
                    'message' => getLogInUser()->username . ' gefällt dein Kommentar',
                    'comment_id' => $comment->id,
                    'post_id' => $comment->post_id,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🔹 Send Push Notification
            $this->sendFCMNotification(
                $comment->user_id,
                'Neuer Like',
                getLogInUser()->username . ' gefällt dein Kommentar',
                route('detailPage', $comment->post->slug ?? '') . '#comment-' . $comment->id
            );
        }
    }

    protected function deleteLikeNotification($commentId)
    {
        $comment = Comment::find($commentId);

        if (!$comment) {
            return;
        }

        // Delete notification when comment is unliked
        DB::table('notifications')
            ->where('to_user_id', $comment->user_id)
            ->where('from_user_id', auth()->id())
            ->where('post_id', $comment->post_id)
            ->whereJsonContains('data', ['comment_id' => $comment->id])
            ->where('type', 'App\\Notifications\\LikeCommentNotification')
            ->delete();
    }

    protected function sendCommentNotification($comment)
    {
        $parentComment = Comment::find($comment->parent_id);

        if (!$parentComment || $parentComment->user_id == auth()->id()) {
            return;
        }

        $exists = DB::table('notifications')
            ->where('to_user_id', $parentComment->user_id)
            ->where('from_user_id', auth()->id())
            ->where('post_id', $comment->post_id)
            ->whereJsonContains('data', ['comment_id' => $comment->id])
            ->where('type', 'App\\Notifications\\ReplyCommentNotification')
            ->exists();

        if (!$exists) {
            DB::table('notifications')->insert([
                'type' => 'App\\Notifications\\ReplyCommentNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $parentComment->id,
                'to_user_id' => $parentComment->user_id,
                'post_id' => $comment->post_id,
                'from_user_id' => auth()->id(),
                'data' => json_encode([
                    'message' => getLogInUser()->username . ' hat auf deinen Kommentar geantwortet',
                    'comment_id' => $comment->id,
                    'post_id' => $comment->post_id,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🔹 Send Push Notification
            $this->sendFCMNotification(
                $parentComment->user_id,
                'Neue Antwort',
                getLogInUser()->username . ' hat auf deinen Kommentar geantwortet',
                route('detailPage', $comment->post->slug ?? '') . '#comment-' . $comment->id
            );
        }
    }

    protected function sendFollowNotification($comment)
    {
        // Get all followers of the user who commented
        $followers = \App\Models\Followers::where('followers', $comment->user_id)
            ->with('follow')
            ->get();

        foreach ($followers as $follower) {
            $followerUserId = $follower->following;
            
            // Don't send notification to the commenter themselves
            if ($followerUserId == $comment->user_id) {
                continue;
            }

            // Check if there's an unread aggregated notification for this user
            $existingNotification = DB::table('notifications')
                ->where('to_user_id', $followerUserId)
                ->where('type', 'App\\Notifications\\AggregatedFollowingActivityNotification')
                ->whereNull('read_at')
                ->first();

            if ($existingNotification) {
                // Update existing aggregated notification
                $data = json_decode($existingNotification->data, true);
                $memberIds = $data['member_ids'] ?? [];
                
                // Add this member if not already in the list
                if (!in_array($comment->user_id, $memberIds)) {
                    $memberIds[] = $comment->user_id;
                }

                // Count unique members (not activities)
                $memberCount = count($memberIds);
                
                // Generate message with correct singular/plural
                if ($memberCount == 1) {
                    $message = "1 Mitglied, dem du folgst, hat neue Kommentare gepostet.";
                } else {
                    $message = "{$memberCount} Mitglieder, denen du folgst, haben neue Kommentare gepostet.";
                }

                DB::table('notifications')
                    ->where('id', $existingNotification->id)
                    ->update([
                        'data' => json_encode([
                            'message' => $message,
                            'count' => $memberCount,
                            'member_ids' => $memberIds,
                            'activity_type' => 'comment',
                        ]),
                        'updated_at' => now(),
                    ]);
            } else {
                // Create new aggregated notification
                $message = "1 Mitglied, dem du folgst, hat neue Kommentare gepostet.";
                
                DB::table('notifications')->insert([
                    'type' => 'App\\Notifications\\AggregatedFollowingActivityNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $followerUserId,
                    'to_user_id' => $followerUserId,
                    'post_id' => null,
                    'from_user_id' => null,
                    'data' => json_encode([
                        'message' => $message,
                        'count' => 1,
                        'member_ids' => [$comment->user_id],
                        'activity_type' => 'comment',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Send Push Notification
                $this->sendFCMNotification(
                    $followerUserId,
                    'Neue Aktivität',
                    'Mitglieder, denen du folgst, haben neue Kommentare gepostet.',
                    route('members.following')
                );
            }
        }
    }

    protected function sendFCMNotification($userId, $title, $body, $clickAction)
    {

        
        $tokens = \App\Models\UserFcmToken::where('user_id', $userId)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return; 
        }

        $factory = (new FirebaseFactory)
            ->withServiceAccount(config('services.firebase.credentials.file'));

        $messaging = $factory->createMessaging();

        $message = [
            'data' => [
                'title' => $title,
                'body'  => $body,
                'link'  => $clickAction,
            ]
        ];


        try {
            
            $messaging->sendMulticast($message, $tokens);
        } catch (\Exception $e) {
            \Log::error('FCM send error: '.$e->getMessage());
        }
    }

    protected function sendPostLikeNotification($postId)
    {
        $post = \App\Models\Post::find($postId);

        if (!$post || $post->user_id == auth()->id()) {
            return;
        }

        $exists = DB::table('notifications')
            ->where('to_user_id', $post->created_by)
            ->where('from_user_id', auth()->id())
            ->where('post_id', $post->id)
            ->where('type', 'App\\Notifications\\LikePostNotification')
            ->exists();

        if (!$exists) {
            DB::table('notifications')->insert([
                'type' => 'App\\Notifications\\LikePostNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $post->id,
                'to_user_id' => $post->created_by,
                'post_id' => $post->id,
                'from_user_id' => auth()->id(),
                'data' => json_encode([
                    'message' => getLogInUser()->username . ' gefällt dein Beitrag',
                    'post_id' => $post->id,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🔹 Send Push Notification
            $this->sendFCMNotification(
                $post->created_by,
                'Neuer Like',
                getLogInUser()->username . ' gefällt dein Beitrag',
                route('detailPage', $post->slug ?? '')
            );
        }
    }

    protected function deletePostLikeNotification($postId)
    {
        $post = \App\Models\Post::find($postId);

        if (!$post) {
            return;
        }

        // Delete notification when post is unliked
        DB::table('notifications')
            ->where('to_user_id', $post->created_by)
            ->where('from_user_id', auth()->id())
            ->where('post_id', $post->id)
            ->where('type', 'App\\Notifications\\LikePostNotification')
            ->delete();
    }


    public function saveCommentsUser(CreateCommentRequest $request)
    {
        if ((getSettingValue()['show_captcha'] == '1') && $request['g-recaptcha-response'] == null) {
            return $this->sendError(__('messages.placeholder.reCAPTCHA_required'));
        }

        $input = $request->all();

        if (Auth::check()) {
            $input['name'] = getLogInUser()->username;
            $input['email'] = getLogInUser()->email;
        }

        $input['status'] = (getSettingValue()['comment_approved'] == 1) ? 1 : 0;
        
        // If this is a reply, inherit item_type/item_id from parent comment; block: cannot reply to blocked user
        if (!empty($input['parent_id'])) {
            $parentComment = Comment::find($input['parent_id']);
            if ($parentComment) {
                if (Auth::check() && $parentComment->user_id && UserBlock::isBlockedBetween(Auth::id(), $parentComment->user_id)) {
                    return $this->sendError(__('messages.block.cannot_reply_blocked'));
                }
                // Inherit item_type/item_id from parent if not already set
                if (empty($input['item_type']) && !empty($parentComment->item_type)) {
                    $input['item_type'] = $parentComment->item_type;
                }
                if (empty($input['item_id']) && !empty($parentComment->item_id)) {
                    $input['item_id'] = $parentComment->item_id;
                }
                // Also inherit post_id if not set (for legacy compatibility)
                if (empty($input['post_id']) && !empty($parentComment->post_id)) {
                    $input['post_id'] = $parentComment->post_id;
                }
            }
        }
        
        $comment = Comment::create($input);

        if (Auth::check()) {
            $points = PointRule::where('key', 'comment')->value('points') ?? 0;

            Auth::user()->increment('comment_points', $points);
        }


        // Send notification if this is a reply
        if (!empty($comment->parent_id)) {
            $this->sendCommentNotification($comment);
        }

        // Send notification to followers when user comments
        $this->sendFollowNotification($comment);

        // Get comment count based on item_type or post_id
        if (!empty($input['item_type']) && !empty($input['item_id'])) {
            $data['commentCount'] = Comment::where('status', 1)
                ->where('item_type', $input['item_type'])
                ->where('item_id', $input['item_id'])
                ->count();
            $data['comments'] = Comment::with(['users', 'replies' => fn ($q) => $q->where('status', 1)->with('users')])
                ->where('item_type', $input['item_type'])
                ->where('item_id', $input['item_id'])
                ->whereNull('parent_id')
                ->where(function ($q) {
                    $q->where('status', 1)->orWhereHas('replies');
                })
                ->latest()
                ->get();
        } else {
            $data['commentCount'] = Comment::where('status', 1)->where('post_id', $comment->post_id)->count();
            $data['comments'] = Comment::with(['users', 'replies' => fn ($q) => $q->where('status', 1)->with('users')])
                ->wherePostId($input['post_id'])
                ->whereNull('parent_id')
                ->where(function ($q) {
                    $q->where('status', 1)->orWhereHas('replies');
                })
                ->latest()
                ->get();
        }
        
        $data['commentView'] = Comment::with('users')->find($comment->id);
        $data['commentCreated'] = $data['commentView']->created_at->diffForHumans();

        //update user last activity
        $this->updateLastActivity();

        return $this->sendResponse($data, __('messages.placeholder.comment_create_successfully'));
    }

    public function destroyComment(Comment $comment)
    {
        $comment->delete();

        $data['commentCount'] = Comment::whereStatus(1)->wherePostId($comment->post_id)->get()->count();

        return $this->sendResponse($data['commentCount'], __('messages.placeholder.comment_deleted_successfully'));
    }

    public function editComment(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (now()->diffInSeconds($comment->created_at) > 60) {
            return response()->json(['success' => false, 'message' => 'Time expired'], 403);
        }

        $comment->comment = $request->comment;
        $comment->edited_at = now();
        $comment->save();

        return response()->json(['success' => true]);
    }

    public function reportComment(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login to report a comment'], 401);
        }

        $request->validate([
            'comment_id' => 'required|exists:comments,id',
            'post_id' => 'required|exists:posts,id',
            'reported_user_id' => 'required|exists:users,id',
            'report_reason' => 'nullable|string|max:1000',
        ]);

        // Check if user already reported this comment
        $existingReport = \App\Models\CommentReport::where('comment_id', $request->comment_id)
            ->where('reported_by_user_id', Auth::id())
            ->first();

        if ($existingReport) {
            return response()->json(['success' => false, 'message' => 'You have already reported this comment']);
        }

        \App\Models\CommentReport::create([
            'comment_id' => $request->comment_id,
            'post_id' => $request->post_id,
            'reported_by_user_id' => Auth::id(),
            'reported_user_id' => $request->reported_user_id,
            'report_reason' => $request->report_reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.placeholder.comment_reported_successfully')
        ]);
    }

    public function categoryPage($slug, $subName = null)
    {
        $categoryName = Category::where('slug', $slug)->first();
        if ($categoryName == null) {
            return redirect(route('front.home'));
        }
        
        // Allow News category to be accessible even if hidden from menu (for Google News)
        $isNewsCategory = strtolower(trim($categoryName->name ?? '')) === 'news' 
            || strtolower(trim($categoryName->slug ?? '')) === 'news';
        
        // Only redirect if not News category and show_in_menu is false
        if (!$categoryName->show_in_menu && !$isNewsCategory) {
            return redirect(route('front.home'));
        }

        $categoryName = $categoryName->name;

        if (!empty($subName)) {
            $subCategory = SubCategory::where('slug', $subName)->first();
            if (!empty($subCategory)) {
                if (!$subCategory->show_in_menu) {
                    return redirect(route('front.home'));
                }
            } else {
                return redirect(route('front.home'));
            }

            $subCategory = $subCategory->name;
            if (getCurrentTheme() == 1) {
                return view('theme1.category-page', compact('slug', 'categoryName', 'subCategory', 'subName'));
            }
            return view('front_new.category-page', compact('slug', 'categoryName', 'subCategory', 'subName'));
        } else {
            if (getCurrentTheme() == 1) {
                return view('theme1.category-page', compact('categoryName', 'slug'));
            }
            return view('front_new.category-page', compact('categoryName', 'slug'));
        }
    }

    public function popularTagPage($tagName): \Illuminate\View\View
{
    $popularTag = Post::whereRaw("FIND_IN_SET(?, tags)", [$tagName])
        ->with(['user','category','comment'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    // dd($popularTag);

    if (getCurrentTheme() == 1) {
        return view('theme1.popular-tag', compact('tagName', 'popularTag'));
    }
    return view('front_new.popular-tag', compact('tagName', 'popularTag'));
}


    public function galleryPage($id = null): \Illuminate\View\View
    {
        if (!empty($id)) {
            $allSubCategory = AlbumCategory::with('album', 'gallery')->whereLangId(getFrontSelectLanguage())
                ->where('album_id', $id)->get();
            $galleryImages = Gallery::with('album', 'media', 'category')->whereLangId(getFrontSelectLanguage())
                ->where('album_id', $id)->get();

            if (getCurrentTheme() == 1) {
                return view('theme1.gallery-images', compact('galleryImages', 'allSubCategory'));
            }
            return view('front_new.gallery-images', compact('galleryImages', 'allSubCategory'));
        }

        $album = Album::with('gallery')->whereLangId(getFrontSelectLanguage())->get();
        $galleries = [];
        foreach ($album as $gallery) {
            if (!empty($gallery->gallery->first())) {
                $galleries[] = $gallery->gallery->first();
            }
        }

        if (getCurrentTheme() == 1) {
            return view('theme1.gallery-page', compact('galleries'));
        }
        return view('front_new.gallery-page', compact('galleries'));
    }

    public function allPosts(): \Illuminate\View\View
    {
        if (getCurrentTheme() == 1) {
            return view('theme1.all-posts');
        }
        return view('front_new.all-posts');
    }

    public function displayTerms(Request $request)
    {
        $settings = getSettingValue();
        if ($request->is('terms-conditions')) {
            $term = 'terms-conditions';
            $termData = $settings['terms&conditions'];
        } elseif ($request->is('support')) {
            $term = 'support';
            $termData = $settings['support'];
        } elseif ($request->is('privacy')) {
            $term = 'privacy';
            $termData = $settings['privacy'];
        } else {
            return redirect('/');
        }
        if (getCurrentTheme() == 1) {
            return view('theme1.page', compact('termData', 'term'));
        }
        return view('front_new.page', compact('termData', 'term'));
    }

    public function audioDetails(Request $request)
    {
        $audioPost = [];
        $audioPost['data'] = Post::with(['postAudios', 'media'])->where('slug', $request->audio_slug)->whereVisibility(Post::VISIBILITY_ACTIVE)->firstOrFail();
        $audioPost['audioData'] = $audioPost['data'];
        $lists = [];

        foreach ($audioPost['audioData']->postAudios->media as $data) {
            $list = [];
            $list['name'] = $data['name'];
            $list['url'] = $data->getFullUrl();
            $list['cover_art_url'] = $audioPost['audioData']->media[0]->getFullUrl();
            $lists[] = $list;
        }
        $audioPost['list'] = $lists;

        return $this->sendResponse($audioPost, __('messages.placeholder.data_retried'));
    }

    public function postReaction(Request $request)
    {
        $existemoji = PostReactionEmoji::wherePostId($request['postId'])->whereIpAddress(\Request::ip())->first();
        if ($existemoji == null) {
            $postReaction = PostReactionEmoji::create([
                'ip_address' => \Request::ip(),
                'emoji_id' => $request['emojiId'],
                'post_id' => $request['postId'],
            ]);
        } else {
            if ($existemoji->emoji_id == $request['emojiId']) {
                $existemoji->delete();
            }
        }
        $countEmoji = PostReactionEmoji::wherePostId($request['postId'])->get()->groupBy('emoji_id');

        return $this->sendResponse($countEmoji, __('messages.placeholder.data_retried'));
    }

    public function declineCookie()
    {
        session(['declined' => 1]);
    }

    public function updateLastActivity()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_activity_at = Carbon::now();
            $user->save();
        }
    }

    public function profileDashboard(Request $user)
    {
        $user = User::where(function ($q) use ($user) {
            $q->where('id', $user->user)->orWhere('username', $user->user);
        })->first();

        $user->comments_count = DB::table('comments')
            ->where('user_id', $user->id)
            ->count();

        //update user last activity
        $this->updateLastActivity();

        $user->likes_count = DB::table('likes')
            ->where('user_id', $user->id)
            ->count();

        // Track profile visit
        $profileVisitorService = app(\App\Services\ProfileVisitorService::class);
        $profileVisitorService->trackVisit($user, request());

        return view('customer-panel.user-profile', compact('user'));


        //
        //        $posts = Post::with('comment')->whereVisibility(Post::VISIBILITY_ACTIVE)->whereCreatedBy($user->id)->get();
        //        $following = Followers::with('follower')->whereFollowing($user->id)->get();
        //        $followers = Followers::with('follow')->whereFollowers($user->id)->get();
        //
        //        if (getCurrentTheme() == 1) {
        //            return view('theme1.layouts.detailPages.front-user-dashboard', compact('posts', 'user', 'following', 'followers'));
        //        }
        //        return view('front_new.detail_pages.front-user-dashboard', compact('posts', 'user', 'following', 'followers'));
    }

    public function themeChange()
    {
        $theme = getCurrentTheme();
        if ($theme == 1) {
            $theme = 2;
        } else {
            $theme = 2;
        }
        Session::put('theme', $theme);
        Setting::where('key', 'theme_configuration')->update(['value' => $theme]);
        return redirect('/');
    }

   public function getComments(Request $request)
    {
        if ($request->ajax()) {
            $postId = $request->input('post_id');
            $itemType = $request->input('item_type');
            $itemId = $request->input('item_id');
            $sort = $request->input('sort', 'newest'); // Default to newest
            $user = Auth::user();

            // Support both post_id (legacy) and item_type/item_id (new)
            if (!$postId && (!$itemType || !$itemId)) {
                return response()->json(['error' => 'Invalid post ID or item type/ID'], 400);
            }

            // Step 1: Parent comments = visible OR hidden but with visible replies (show placeholder + replies)
            $query = Comment::withTrashed()->with(['users', 'replies' => function ($query) {
                $query->withTrashed()->where('status', 1)->with('users');
            }])
                ->whereNull('parent_id')
                ->where(function ($q) {
                    $q->where('status', 1)->orWhereHas('replies');
                });
            
            // Apply filter based on item_type/item_id or post_id
            if ($itemType && $itemId) {
                $query->where('item_type', $itemType)->where('item_id', $itemId);
            } else {
                $query->where('post_id', $postId);
            }
            
            // Apply initial sorting based on sort parameter
            if ($sort === 'oldest') {
                $query->oldest();
            } else {
                $query->latest(); // Default: newest
            }
            
            $allComments = $query->get();
            
            // Step 1.5: Calculate likes_count for all comments before sorting
            foreach ($allComments as $comment) {
                $comment->likes_count = DB::table('likes')
                    ->where('item_id', $comment->id)
                    ->where('item_type', 'comment')
                    ->count();
            }
            
            // Step 1.6: Sort by top comments (likes_count) if needed
            if ($sort === 'top') {
                $allComments = $allComments->sortByDesc('likes_count')->values();
            }

            // Step 2: Manual Pagination — Based on parent + replies count
            $perPage = 25;
            $page = $request->get('page', 1);
            $offset = 0;
            $runningCount = 0;
            $paginated = collect();

            foreach ($allComments as $comment) {
                $replyCount = $comment->replies->count();
                $blockSize = 1 + $replyCount;

                if ($runningCount + $blockSize <= ($page - 1) * $perPage) {
                    $runningCount += $blockSize;
                    continue;
                }

                if ($runningCount >= $page * $perPage) {
                    break;
                }

                $paginated->push($comment);
                $runningCount += $blockSize;
            }

            // Step 3: User Like Status (likes_count already calculated in Step 1.5)
            foreach ($paginated as $comment) {
                $comment->user_liked = $user ? (bool) DB::table('likes')
                    ->where('user_id', $user->id)
                    ->where('item_id', $comment->id)
                    ->where('item_type', 'comment')
                    ->exists() : false;
                
                // Also set user_liked for replies
                foreach ($comment->replies as $reply) {
                    $reply->user_liked = $user ? (bool) DB::table('likes')
                        ->where('user_id', $user->id)
                        ->where('item_id', $reply->id)
                        ->where('item_type', 'comment')
                        ->exists() : false;
                }
            }

            // Step 4: Total count of all items (parents + replies)
            $totalItems = $allComments->reduce(function ($carry, $item) {
                return $carry + 1 + $item->replies->count();
            }, 0);
            
            // Step 4.5: Get total comment count (for display)
            $countQuery = Comment::where('status', 1);
            if ($itemType && $itemId) {
                $countQuery->where('item_type', $itemType)->where('item_id', $itemId);
            } else {
                $countQuery->where('post_id', $postId);
            }
            $totalComments = $countQuery->count();

            // Step 5: Show captcha setting
            $showCaptcha = Setting::where('key', 'show_captcha')->value('value');

            // Step 6: Return view & pagination JSON
            $view = view('front_new.detail_pages.partials.comments', [
                'comments' => $paginated,
                'showCaptcha' => $showCaptcha,
            ])->render();

            return response()->json([
                'comments_html' => $view,
                'total_comments' => $totalComments, // Total count of all comments (parent + replies)
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => ceil($totalItems / $perPage),
                    'total' => $totalItems,
                ],
            ]);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

     public function postPreview(Request $request, string $slug, $id = null)
{
    $isSignedPreview = $request->hasValidSignature();
    $user            = auth()->user();

    // 1) Same relations as detailPage
    $with = [
        'category',
        'postArticle',
        'postVideo',
        'postAudios',
        'postGalleries',
        'postSortLists.media',
        'postSortLists',
        'media',
        'rssFeed',
        'livetickerContent',
        'livetickerPosts' => function($query) {
            $query->select('id', 'post_id', 'content', 'created_at')
                  ->orderBy('created_at', 'desc');
        },
    ];

    // 2) Post fetch (preview bypasses visibility/scopes)
    if ($isSignedPreview) {
        $post = Post::withoutGlobalScopes()->with($with)->where('slug', $slug)->firstOrFail();
    } else {
        $post = Post::with($with)->where('slug', $slug)->where('visibility', 1)->firstOrFail();
    }

    // 3) Likes meta (same as detailPage)
    $post->likes_count = DB::table('likes')
        ->where('item_id', $post->id)
        ->where('item_type', 'post')
        ->count();

    $post->user_liked = false;
    if ($user) {
        $post->user_liked = (bool) DB::table('likes')
            ->where('user_id', $user->id)
            ->where('item_id', $post->id)
            ->where('item_type', 'post')
            ->exists();
    }

    // 4) Prev/Next (only visible posts)
    $previousId = Post::where('id', '<', $post->id)->whereVisibility(Post::VISIBILITY_ACTIVE)->max('id');
    $nextId     = Post::where('id', '>', $post->id)->whereVisibility(Post::VISIBILITY_ACTIVE)->min('id');
    if (empty($previousId)) {
        $previousId = Post::where('id', '>', $post->id)->whereVisibility(Post::VISIBILITY_ACTIVE)->max('id');
    }
    if (empty($nextId)) {
        $nextId = Post::where('id', '<', $post->id)->whereVisibility(Post::VISIBILITY_ACTIVE)->min('id');
    }
    $previousPost = Post::with('postVideo')->find($previousId);
    $nextPost     = Post::with('postVideo')->find($nextId);

    // 5) Comments: visible parents OR hidden parents that have visible replies (show placeholder + replies)
    $comments = Comment::withTrashed()->with(['users', 'replies' => function ($q) {
            $q->withTrashed()->where('status', 1)->with('users');
        }])
        ->wherePostId($post->id)
        ->whereNull('parent_id')
        ->where(function ($q) {
            $q->where('status', 1)->orWhereHas('replies');
        })
        ->latest()
        ->paginate(2);

    foreach ($comments as $comment) {
        $comment->likes_count = DB::table('likes')
            ->where('item_id', $comment->id)
            ->where('item_type', 'comment')
            ->count();

        $comment->user_liked = false;
        if ($user) {
            $comment->user_liked = (bool) DB::table('likes')
                ->where('user_id', $user->id)
                ->where('item_id', $comment->id)
                ->where('item_type', 'comment')
                ->exists();
        }
        
        // Also set user_liked for replies
        foreach ($comment->replies as $reply) {
            $reply->user_liked = false;
            if ($user) {
                $reply->user_liked = (bool) DB::table('likes')
                    ->where('user_id', $user->id)
                    ->where('item_id', $reply->id)
                    ->where('item_type', 'comment')
                    ->exists();
            }
        }
    }
    $totalComments = Comment::where('post_id', $post->id)->where('status', 1)->count();

    // 6) Related, emojis, captcha, poll
    $relatedPosts = Post::with('category', 'postVideo')
        ->where('id', '!=', $post->id)
        ->where('category_id', $post->category_id)
        ->where('post_types', $post->post_types)
        ->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->latest()->get();

    $countEmoji  = PostReactionEmoji::wherePostId($post->id)->get()->groupBy('emoji_id');
    $emojis      = Emoji::whereStatus(Emoji::ACTIVE)->get();
    $showCaptcha = optional(Setting::where('key', 'show_captcha')->first())->value;
    $getPoll     = getPoll('article', $post->id);

    // 7) Common data bag
    $data = [
        'postDetail'     => $post,
        'previousPost'   => $previousPost,
        'nextPost'       => $nextPost,
        'comments'       => $comments,
        'totalComments'  => $totalComments,
        'relatedPosts'   => $relatedPosts,
        'countEmoji'     => $countEmoji,
        'emojis'         => $emojis,
        'showCaptcha'    => $showCaptcha,
        'getPoll'        => $getPoll,
        'isPreview'      => $isSignedPreview,
    ];

    // 8) Type based views (EXACTLY like detailPage)
    if (!empty($post->post_types)) {
        if ($post->post_types == Post::ARTICLE_TYPE_ACTIVE || $post->post_types == Post::OPEN_AI_ACTIVE) {
            if (getCurrentTheme() == 1) {
                $view = view('theme1.layouts.detailPages.articale-details')->with($data);
            } else {
                $view = view('front_new.detail_pages.article-details')->with($data);
            }
        } elseif ($post->post_types == Post::GALLERY_TYPE_ACTIVE) {
            $data['fileName']         = $post->getPostFileNameAttribute();
            $data['firstGalleryPost'] = $post->postGalleries->first();
            $data['lastGalleryPost']  = $post->postGalleries->last();
            $data['totalGalleryPost'] = count($post->postGalleries);
            if ((!empty($id)) && ($id > $data['totalGalleryPost'] || $id <= 0)) {
                return redirect(route('detailPage', ['data' => $slug]));
            }
            $data['galleryPost']  = (!empty($id)) ? $post->postGalleries[$id - 1] : $post->postGalleries->first();
            $data['galleryPostNo'] = (!empty($id)) ? $id : '1';

            if (getCurrentTheme() == 1) {
                $view = view('theme1.layouts.detailPages.gallery-details')->with($data);
            } else {
                $view = view('front_new.detail_pages.gallery-details')->with($data);
            }
        } elseif ($post->post_types == Post::SORTED_TYPE_ACTIVE) {
            if (getCurrentTheme() == 1) {
                $view = view('theme1.layouts.detailPages.sortedlist-details')->with($data);
            } else {
                $view = view('front_new.detail_pages.sortedlist-details')->with($data);
            }
        } elseif ($post->post_types == Post::VIDEO_TYPE_ACTIVE) {
            if (getCurrentTheme() == 1) {
                $view = view('theme1.layouts.detailPages.video-details')->with($data);
            } else {
                $view = view('front_new.detail_pages.video-details')->with($data);
            }
        } elseif ($post->post_types == Post::AUDIO_TYPE_ACTIVE) {
            if (getCurrentTheme() == 1) {
                $view = view('theme1.layouts.detailPages.audio-details')->with($data);
            } else {
                $view = view('front_new.detail_pages.audio-details')->with($data);
            }
        } elseif ($post->post_types == Post::LIVETICKER_TYPE_ACTIVE) {
            if (getCurrentTheme() == 1) {
                $view = view('theme1.layouts.detailPages.liveticker-details')->with($data);
            } else {
                $view = view('front_new.detail_pages.liveticker-details')->with($data);
            }
        } else {
            return redirect(route('front.home'));
        }
    } else {
        return redirect(route('front.home'));
    }

    // 9) Preview links ko index na hone do
    if ($isSignedPreview) {
        return response($view)->header('X-Robots-Tag', 'noindex, nofollow');
    }

    return $view;
}
}
