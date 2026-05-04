<?php

use App\Models\AdSpaces;
use App\Models\Analytic;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Followers;
use App\Models\Language;
use App\Models\MailSetting;
use App\Models\Menu;
use App\Models\Navigation;
use App\Models\Page;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Poll;
use App\Models\PollResult;
use App\Models\Post;
use App\Models\SeoTool;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Models\Subscription;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent;
/**
 * @return Authenticatable|null
 */
if (! function_exists('getLogInUser')) {
    function getLogInUser()
    {

        return Auth::user();
    }
}

/**
 * @return mixed
 */
 if (! function_exists('getBreakingNews')) {
    function getBreakingNews()
    {
        // Step 1: Get all distinct dates with breaking news
$breakingDates = DB::table('posts')
    ->select(DB::raw('DISTINCT DATE(updated_at) as date'))
    ->where('breaking', 1)
    ->where('visibility', Post::VISIBILITY_ACTIVE)
    ->orderBy('date','desc')
    ->get();

// Step 2: For each date, get all the posts
$allBreakingNews = collect();
foreach ($breakingDates as $dateRecord) {
    $postsForDate = DB::table('posts')
        ->select('slug', 'title', DB::raw("DATE_FORMAT(updated_at, '%H:%i') as time"))
        ->where('breaking', 1)
        ->where('visibility', Post::VISIBILITY_ACTIVE)
        ->whereDate('updated_at', $dateRecord->date)
        ->get();

    $allBreakingNews[$dateRecord->date] = $postsForDate;
}

return $allBreakingNews;
    }
}

if (! function_exists('getAppName')) {
    function getAppName()
    {
        if (! Schema::hasTable('settings')) {
            return config('app.name');
        }
        static $appName;

        if (empty($appName)) {
            $appName = Setting::where('key', '=', 'application_name')->first()->value;
        }

        return $appName;
    }
}

if (! function_exists('getAppLogo')) {
    function getAppLogo()
    {
        if (! Schema::hasTable('settings')) {
            return asset('assets/image/infyom-logo.png');
        }
        static $appName;

        if (empty($appName)) {
            $appName = Setting::where('key', '=', 'logo')->first()->value;
            if (empty($appName)) {
                $appName = asset('assets/image/infyom-logo.png');
            }
        }

        return $appName;
    }
}
if (! function_exists('getAppFavicon')) {
    function getAppFavicon()
    {
        try {
            \DB::connection()->getPdo();
        } catch (\Exception $e) {
            return asset('assets/image/favicon-infyom.png');
        }

        static $appName;

        if (empty($appName)) {
            $setting = Setting::where('key', '=', 'favicon')->first();
            $appName = $setting ? $setting->value : null;

            if (empty($appName)) {
                $appName = asset('assets/image/favicon-infyom.png');
            }
        }

        return $appName;
    }
}

/**
 * @return int
 */
if (! function_exists('getLogInUserId')) {
    function getLogInUserId()
    {
        return Auth::user()->id;
    }
}

/**
 * @return string
 */
if (! function_exists('getDashboardURL')) {
    function getDashboardURL()
    {

        if (Auth::user()->hasRole('customer')) {
            return RouteServiceProvider::CUSTOMER;
        }
        if (Auth::user()->hasRole('clinic_admin')) {
            return RouteServiceProvider::HOME;
        }

        return RouteServiceProvider::HOME;
    }
}

/**
 * @return \Illuminate\Support\Collection
 */
if (! function_exists('getLanguage')) {
    function getLanguage()
    {
        static $language;
        if (empty($language)) {
            $language = Language::pluck('name', 'id');
        }

        return $language;
    }
}

/**
 * @return \Illuminate\Support\Collection
 */
if (! function_exists('getLanguageSet')) {
    function getLanguageSet()
    {
        $language = Language::pluck('name', 'iso_code');

        return $language;
    }
}

/**
 * @return mixed
 */
if (! function_exists('getAlbums')) {
    function getAlbums($langId)
    {
        return \App\Models\Album::where('lang_id', $langId)->toBase()->pluck('name', 'id')->toArray();
    }
}

if (! function_exists('getAlbumCategory')) {
    function getAlbumCategory($albumId, $langId): array
    {
        return \App\Models\AlbumCategory::where('lang_id', $langId)->where('album_id', $albumId)->pluck(
            'name',
            'id'
        )->toArray();
    }
}

if (! function_exists('getRandomColor')) {
    function getRandomColor($index): string
    {
        $badgeColors = [
            'primary',
            'success',
            'info',
            'danger',
            'warning',
        ];
        $number = ceil($index % 5);

        return $badgeColors[$number];
    }
}

/**
 * @param  $index
 * @return string
 */
if (! function_exists('getParentMenu')) {
    function getParentMenu()
    {
        $menu = Menu::whereNotNull('link')->pluck('link', 'id')->sort();

        return $menu;
    }
}

/**
 * @return mixed
 */
if (! function_exists('getHeaderElement')) {
    function getHeaderElement()
    {
        $data['navigations'] = Navigation::with('navigationable')
            ->whereHas('navigationable', function ($q) {
                $q->where('show_in_menu', 1);
            })->whereNull('parent_id')->orderBy('order_id')->get();

        //child
        $data['navigationsTakeData'] = [];
        foreach ($data['navigations'] as $item) {
            $navigationType = $item->navigationable_type == Category::class ? SubCategory::class : $item->navigationable_type;
            $data['navigationsTakeData'][$item->id] = Navigation::with('navigationable')
                ->whereHas('navigationable', function ($q) {
                    $q->where('show_in_menu', 1);
                })->where('navigationable_type', $navigationType)
                ->where('parent_id', $item->navigationable_id)->orderBy('order_id')->get();
        }

        $data['pages'] = Page::where('location', Page::MAIN_MENU)->where('visibility', 1)->get()->sort();

        return $data;
    }
}

/**
 * @return mixed
 */
if (! function_exists('getRecentPost')) {
    function getRecentPost()
    {
        return Post::with('language', 'category')->whereVisibility(Post::VISIBILITY_ACTIVE)->latest('id')->take(3)->get();
    }
}

/**
 * @return Category[]|Collection
 */
if (! function_exists('getCategory')) {
    function getCategory()
    {
        return Category::active()->where('show_in_menu', 1)->get();
    }
}

/**
 * @return mixed
 */
if (! function_exists('getSettingValue')) {
    function getSettingValue()
    {
        static $settingValues = [];

        if (empty($settingValues)) {
            $settingValues = Setting::pluck('value', 'key')->toArray();
        }

        return $settingValues;
    }
}

if (! function_exists('showPollVotesCount')) {
    /**
     * Whether to show poll total votes count to frontend users (admin toggle).
     */
    function showPollVotesCount(): bool
    {
        $v = getSettingValue()['show_poll_votes_count'] ?? '1';
        return filter_var($v, FILTER_VALIDATE_BOOLEAN);
    }
}

if (! function_exists('getUrl')) {
    function getUrl()
    {
        return FacadesRequest::url();
    }
}

if (! function_exists('getNavigationDetails')) {
    function getNavigationDetails(): array
    {
        //parent navigation get
        $data['navigations'] = Navigation::with('navigationable')
            ->whereHas('navigationable', function ($q) {
                $q->where('show_in_menu', 1);
            })->whereNull('parent_id')->orderBy('order_id')->get();

        $data['menus'] = [];

        foreach ($data['navigations'] as $menu) {
            if ($menu['navigationable']['lang_id'] == getFrontSelectLanguage()) {
                $data['menus'][] = $menu;
            } elseif ($menu->navigationable_type == Menu::class) {
                $data['menus'][] = $menu;
            }
        }

        $data['navigations'] = collect($data['menus'])->take(6);
        //child
        $data['navigationsTakeData'] = [];
        foreach ($data['navigations'] as $item) {
            $navigationType = $item->navigationable_type == Category::class ? SubCategory::class : $item->navigationable_type;
            $data['navigationsTakeData'][$item->id] = Navigation::with('navigationable')
                ->whereHas('navigationable', function ($q) {
                    $q->where('show_in_menu', 1);
                })->where('navigationable_type', $navigationType)
                ->where('parent_id', $item->navigationable_id)->orderBy('order_id')->get();
        }

        $data['menuCount'] = [];
        foreach ($data['navigationsTakeData'] as $menuGet) {
            if ($menuGet->isEmpty()) {
                $data['menuCount'];
            }
        }

        //remaining navigation
        $data['navigationsSkipData'] = Navigation::with('navigationable')
            ->whereHas('navigationable', function ($q) {
                $q->where('show_in_menu', 1);
            })->whereNull('parent_id')
            ->whereNotIn('id', $data['navigations']->pluck('id')->toArray())->orderBy('order_id')->get();

        // child
        $data['navigationsSkipItem'] = [];
        foreach ($data['navigationsSkipData'] as $item) {
            $navigationType = $item->navigationable_type == Category::class ? SubCategory::class : $item->navigationable_type;
            $data['navigationsSkipItem'][$item->id] = Navigation::with('navigationable')
                ->whereHas('navigationable', function ($q) {
                    $q->where('show_in_menu', 1);
                })->where('navigationable_type', $navigationType)
                ->where('parent_id', $item->navigationable_id)->orderBy('order_id')->get();
        }

        //categoryCount
        $countMenu = Category::whereShowInMenu(1)->where('lang_id', '!=', getFrontSelectLanguage())->count();
        //total navigation
        $data['navigationsCount'] = $data['navigationsSkipData']->count() + $data['navigations']->count() - $countMenu;
        //pages
        $data['pages'] = Page::whereLangId(getFrontSelectLanguage())->where('location', Page::MAIN_MENU)->where('visibility', 1)->get()->sort();

        // dd($data);
        return $data;
    }
}

/**
 * @return array
 */
if (! function_exists('getPopularNews')) {
    function getPopularNews()
    {
        return Cache::remember('popular_news:v6', 300, function () {
            $since = now()->subDay();

            // Get "Tipps & Tricks" category ID to exclude
            $tippsTricksCategoryId = DB::table('categories')
                ->where('slug', 'tipps-tricks')
                ->orWhere(function($query) {
                    $query->whereRaw('LOWER(name) = ?', ['tipps & tricks'])
                          ->orWhereRaw('LOWER(name) = ?', ['tipps und tricks']);
                })
                ->pluck('id')
                ->first();

            // 1) Top post IDs by views in the last 24h (fetch more to account for filtering)
            $topIds = DB::table('analytics as a')
                ->join('posts as p', 'p.id', '=', 'a.post_id')
                ->where('a.created_at', '>=', $since)
                ->where('p.visibility', Post::VISIBILITY_ACTIVE)
                ->when($tippsTricksCategoryId, function($query) use ($tippsTricksCategoryId) {
                    $query->where('p.category_id', '!=', $tippsTricksCategoryId);
                })
                ->select('a.post_id', DB::raw('COUNT(*) AS cnt'))
                ->groupBy('a.post_id')
                ->orderByDesc('cnt')
                ->limit(6)
                ->pluck('a.post_id')
                ->all();

            if (empty($topIds)) {
                return [];
            }

            // 2) Fetch as Eloquent models so $appends (post_image) are computed
            //    Preserve popularity order with FIELD() using parameter bindings.
            $placeholders = implode(',', array_fill(0, count($topIds), '?'));

            $posts = Post::with(['postVideo', 'category:id,name,slug'])
                ->withCount(['comments as comment_count' => fn ($q) => $q->where('status', 1)])
                ->whereVisibility(Post::VISIBILITY_ACTIVE)
                ->whereIn('id', $topIds)
                ->orderByRaw("FIELD(id, {$placeholders})", $topIds)
                // do NOT ->toBase(); keep models so accessors run
                ->get(['id','category_id','post_types','slug','title','created_at']);

            // 3) Shape output for the Blade (includes post_image via $appends)
            return $posts->map(function (Post $p) {
                $arr = $p->toArray(); // includes 'post_image'
                // Ensure a consistent structure for category name and slug
                $arr['category'] = [
                    'name' => $p->category?->name ?? '',
                    'slug' => $p->category?->slug ?? ''
                ];
                $arr['comment_count'] = $p->comment_count ?? 0;
                return $arr;
            })->all();
        });
    }
}

/**
 * @return int
 */
if (! function_exists('getPostViewCount')) {
    function getPostViewCount($id)
    {
        // Use cached views_count for better performance
        // Falls back to analytics count if post not found
        $post = Post::find($id);
        
        // Handle NULL views_count (for posts created before migration)
        return $post ? ($post->views_count ?? 0) : 0;
    }
}

/**
 * @return array
 */
if (! function_exists('getPopularTags')) {
    function getPopularTags()
    {
        return Cache::remember('popular_tags:v3', 300, function () {
            $since = now()->subDay();

            // Top 7 post IDs by views in last 24h
            $topIds = DB::table('analytics')
                ->where('created_at', '>=', $since)
                ->select('post_id', DB::raw('COUNT(*) AS cnt'))
                ->groupBy('post_id')
                ->orderByDesc('cnt')
                ->limit(10)
                ->pluck('post_id')
                ->all();

            if (empty($topIds)) {
                return [];
            }

            // Fetch tags for those posts (keep it minimal)
            $posts = Post::query()
                ->whereVisibility(Post::VISIBILITY_ACTIVE)
                ->whereIn('id', $topIds)
                ->get(['id', 'tags']);

            $allTags = [];
            $seenTags = []; // Track normalized tags for case-insensitive duplicate detection
            
            foreach ($posts as $p) {
                $raw = $p->tags;

                if ($raw === null || $raw === '') {
                    continue;
                }

                // If it's already an array (cast), use it
                if (is_array($raw)) {
                    $list = $raw;
                } else {
                    // Try JSON
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $list = $decoded;
                    } else {
                        // Fallback: handle things like '["a","b"]' or 'a,b'
                        $s = trim((string)$raw, "[] \t\n\r\0\x0B");
                        $s = str_replace('"', '', $s);
                        $list = array_filter(array_map('trim', explode(',', $s)));
                    }
                }

                foreach ($list as $t) {
                    $t = trim((string)$t);
                    if ($t !== '') {
                        // Normalize for case-insensitive comparison
                        $normalized = mb_strtolower($t, 'UTF-8');
                        
                        // Check if we've already seen this tag (case-insensitive)
                        if (!in_array($normalized, $seenTags, true)) {
                            $seenTags[] = $normalized;
                            $allTags[] = $t; // Keep original case for display
                        }
                    }
                }
            }

            // Sort tags
            sort($allTags);

            return $allTags;
        });
    }
}

/**
 * @return Poll[]|Builder[]|Collection
 */
// if (! function_exists('getPoll')) {
//     function getPoll()
//     {
//         if (! Auth::check()) {
//             return Poll::where('lang_id', getFrontSelectLanguage())->where('vote_permission', 1)->whereStatus(1)->limit(3)->get();
//         } else {
//             return Poll::where('lang_id', getFrontSelectLanguage())->whereStatus(1)->limit(3)->get();
//         }
//     }
// }


if (!function_exists('getPoll')) {
    function getPoll($location = 'home', $postId = null)
    {
        $ip = request()->ip(); 

        $pollsQuery = Poll::where('lang_id', getFrontSelectLanguage())
            ->whereStatus(1);

       
        if ($location === 'home') {
            $pollsQuery->where('display_location', 'home');
        } elseif ($location === 'article' && $postId) {
            $pollsQuery->where('display_location', 'article')
                       ->where('post_id', $postId);
        }

        if (!Auth::check()) {
            $pollsQuery->where('vote_permission', 1);
        }

        $polls = $pollsQuery->get(); 

       
        foreach ($polls as $poll) {
            $poll->has_voted = PollResult::where('poll_id', $poll->id)
                ->where('ip_address', $ip)
                ->exists();
        }

        return $polls;
    }
}

/**
 * @return string[]
 */
if (! function_exists('getOption')) {
    function getOption(): array
    {
        return [
            'option1',
            'option2',
            'option3',
            'option4',
            'option5',
            'option6',
            'option7',
            'option8',
            'option9',
            'option10',
        ];
    }
}

/**
 * @param  int  $pollId
 */
if (! function_exists('getPollStatistics')) {
    function getPollStatistics($pollId): array
    {
        $pollResults = PollResult::with('poll')->wherePollId($pollId)->get();
        
        $resultsAns = $pollResults->pluck('answer')->toArray();
        $totalPollResults = count($pollResults);
        $totalPerAns = array_count_values($resultsAns);
        $optionAns = [];

        // dd($totalPerAns);

       
        foreach ($pollResults as $result) {
            $poll = $result->poll;
            foreach (getOption() as $option) {
                if (!empty($poll->$option)) {
                    $optionAns[$poll->$option] = !empty($totalPerAns[$poll->$option])
                        ? round(($totalPerAns[$poll->$option] * 100 / $totalPollResults), 2) : 0;
                }
            }
        }

        $data['totalPollResults'] = $totalPollResults;
        $data['optionAns'] = $optionAns;
        $data['pollId'] = $pollId;

        return $data;
    }
}

/**
 * @return string
 */
if (! function_exists('getColorClass')) {
    function getColorClass($id)
    {
        $randomClass = ['world', 'technology', 'travel', 'fashion', 'music', 'animal'];
        $index = $id % 5;

        return $randomClass[$index];
    }
}

/**
 * @return array
 */
 

function getPopulerCategories()
{
    $since = now()->subDay(); // last 24 hours

    // 1) Get popular categories directly from analytics + posts + categories
    $popular = DB::table('analytics as a')
        ->join('posts as p', 'p.id', '=', 'a.post_id')
        ->join('categories as c', 'c.id', '=', 'p.category_id')
        ->where('a.created_at', '>=', $since)
        ->whereNotNull('a.post_id')
        ->where('a.post_id', '!=', '')
        ->where('p.visibility', Post::VISIBILITY_ACTIVE)
        ->where('c.show_in_menu', Category::SHOW_IN_MENU_ACTIVE)
        ->groupBy('c.id', 'c.name', 'c.slug')
        ->select(
            'c.id',
            'c.name',
            'c.slug',
            DB::raw('COUNT(*) as views') // total views per category
        )
        ->orderByDesc('views')
        ->limit(10)
        ->get();

    if ($popular->isNotEmpty()) {
        // Normalize result format
        return $popular->map(function ($row) {
            return [
                'name'        => $row->name,
                'slug'        => $row->slug,
                'posts_count' => (int) $row->views, // or 'views'
            ];
        })->values()->all();
    }

    // 2) Fallback: if there was no analytics in last 24 hours
    return Category::where('show_in_menu', Category::SHOW_IN_MENU_ACTIVE)
        ->whereHas('posts', function ($q) {
            $q->where('visibility', Post::VISIBILITY_ACTIVE);
        })
        ->select('name', 'slug')
        ->orderBy('name', 'asc')
        ->limit(10)
        ->get()
        ->map(function ($category) {
            return [
                'name'        => $category->name,
                'slug'        => $category->slug,
                'posts_count' => 0,
            ];
        })->values()->all();
}

//  function getPopulerCategories()
// {
//     $since = now()->subDay(); // Last 24 hours
        
//         // Step 1: Get post IDs with view counts from last 24 hours
//         $postViews = DB::table('analytics')
//             ->where('created_at', '>=', $since)
//             ->whereNotNull('post_id')
//             ->where('post_id', '!=', '')
//             ->select('post_id', DB::raw('COUNT(*) as view_count'))
//             ->groupBy('post_id')
//             ->get();
//             // dd($postViews);

//         if ($postViews->isEmpty()) {
//             // Fallback: Return static categories if no analytics data in last 24 hours
//             return Category::where('show_in_menu', Category::SHOW_IN_MENU_ACTIVE)
//                 ->whereHas('posts', function ($q) {
//                     $q->where('visibility', Post::VISIBILITY_ACTIVE);
//                 })
//                 ->select('name', 'slug')
//                 ->orderBy('name', 'asc')
//                 ->limit(10)
//                 ->get()
//                 ->map(function ($category) {
//                     return [
//                         'name'        => $category->name,
//                         'slug'        => $category->slug,
//                         'posts_count' => 0,
//                     ];
//                 })->values()->all();
//         }

//         // Step 2: Get posts with their categories
//         $postIds = $postViews->pluck('post_id')->map(function($id) {
//             return (int) $id; // Convert string to int for whereIn
//         })->filter()->unique()->values()->all();

//         if (empty($postIds)) {
//             dd('coming here');
//             // Fallback if no valid post IDs
//             return Category::where('show_in_menu', Category::SHOW_IN_MENU_ACTIVE)
//                 ->whereHas('posts', function ($q) {
//                     $q->where('visibility', Post::VISIBILITY_ACTIVE);
//                 })
//                 ->select('name', 'slug')
//                 ->orderBy('name', 'asc')
//                 ->limit(10)
//                 ->get()
//                 ->map(function ($category) {
//                     return [
//                         'name'        => $category->name,
//                         'slug'        => $category->slug,
//                         'posts_count' => 0,
//                     ];
//                 })->values()->all();
//         }
//         // dd($postIds);
        
//         $posts = Post::whereIn('id', $postIds)
//             ->where('visibility', Post::VISIBILITY_ACTIVE)
//             ->select('id', 'category_id')
//             ->get();
            
//         // dd($posts);

//         // Step 3: Create a lookup map for post views
//         $postViewMap = [];
//         foreach ($postViews as $pv) {
//             $postViewMap[(string) $pv->post_id] = (int) $pv->view_count;
//         }

//         // Step 4: Aggregate views by category
//         $categoryViews = [];
//         foreach ($posts as $post) {
//             if (!$post->category_id) continue;
            
//             $postIdStr = (string) $post->id;
//             $viewCount = $postViewMap[$postIdStr] ?? 0;
            
//             if (!isset($categoryViews[$post->category_id])) {
//                 $categoryViews[$post->category_id] = 0;
//             }
//             $categoryViews[$post->category_id] += $viewCount;
//         }

//         if (empty($categoryViews)) {
//             // Fallback if no valid categories
//             return Category::where('show_in_menu', Category::SHOW_IN_MENU_ACTIVE)
//                 ->whereHas('posts', function ($q) {
//                     $q->where('visibility', Post::VISIBILITY_ACTIVE);
//                 })
//                 ->select('name', 'slug')
//                 ->orderBy('name', 'asc')
//                 ->limit(10)
//                 ->get()
//                 ->map(function ($category) {
//                     return [
//                         'name'        => $category->name,
//                         'slug'        => $category->slug,
//                         'posts_count' => 0,
//                     ];
//                 })->values()->all();
//         }

//         // Step 5: Get categories and sort by view count
//         arsort($categoryViews); // Sort by view count descending
//         $categoryIds = array_keys($categoryViews);
        
//         $categories = Category::whereIn('id', $categoryIds)
//             ->where('show_in_menu', Category::SHOW_IN_MENU_ACTIVE)
//             ->select('id', 'name', 'slug')
//             ->get()
//             ->keyBy('id');

//         // Step 6: Build result array maintaining sort order
//         $result = [];
//         foreach ($categoryIds as $catId) {
//             if (isset($categories[$catId])) {
//                 $result[] = [
//                     'name'        => $categories[$catId]->name,
//                     'slug'        => $categories[$catId]->slug,
//                     'posts_count' => (int) $categoryViews[$catId],
//                 ];
//             }
//         }
//         // dd($result);

//         // Limit to top 10
//         return array_slice($result, 0, 10);
//     // Cache for 24 hours (86400 seconds) to match the 24-hour analytics window
//     // return Cache::remember('popular_categories:v6', 86400, function () {
        
//     // });
// }
//  if (! function_exists('getPopulerCategories')) {
//     function getPopulerCategories()
//     {
//         return Cache::remember('popular_categories:v2', 600, function () {
//             return DB::table('analytics as a')
//                 ->join('posts as p', 'p.id', '=', 'a.post_id')
//                 ->join('categories as c', 'c.id', '=', 'p.category_id')
//                 ->where('p.visibility', \App\Models\Post::VISIBILITY_ACTIVE)
//                 ->where('c.show_in_menu', \App\Models\Category::SHOW_IN_MENU_ACTIVE)
//                 ->select(
//                     'c.id',
//                     'c.name',
//                     'c.slug',
//                     DB::raw('COUNT(*) as posts_count') // actually total views
//                 )
//                 ->groupBy('c.id', 'c.name', 'c.slug')
//                 ->orderByDesc('posts_count')
//                 ->limit(10)
//                 ->get()
//                 ->map(function ($row) {
//                     return [
//                         'name'        => $row->name,
//                         'slug'        => $row->slug,
//                         'posts_count' => (int) $row->posts_count,
//                     ];
//                 })
//                 ->values()
//                 ->all();
//         });
//     }
// }
// if (! function_exists('getPopulerCategories')) {
//     function getPopulerCategories()
//     {
//         $postCount = DB::table('analytics')->select(
//             'post_id',
//             DB::raw('count("post_id") as total_count')
//         )->limit(10)
//             ->groupBy('post_id')
//             ->orderBy('total_count', 'desc')
//             ->get();
            
//             // dd($postCount);

//         $popularCategory = [];

//         $posts = Post::toBase()->whereIn('id', $postCount->pluck('post_id')->toArray())->where('visibility', Post::VISIBILITY_ACTIVE)->get()->groupBy('category_id');
        
//         $categories = Category::toBase()->where('show_in_menu', Category::SHOW_IN_MENU_ACTIVE)->get();
        
//         $cnt = 0;
//         foreach ($posts as $id => $post) {
//             $category = $categories->where('id', $id)->first();
//             if (! empty($category)) {
//                 if ($cnt > 10) {
//                     continue;
//                 }
//                 $popularCategory[$id]['name'] = $category->name;
//                 $popularCategory[$id]['slug'] = $category->slug;
//                 $popularCategory[$id]['posts_count'] = $post->count();
//                 $cnt++;
//             }
//         }

//         return array_values($popularCategory);
//     }
// }

if (! function_exists('getNavUrl')) {
    function getNavUrl($url)
    {
        $contain = Str::contains($url, 'https');
        if ($contain) {
            return $url;
        } else {
            return 'http://'.$url;
        }
    }
}

/**
 * @return string
 */
if (! function_exists('getReadingTime')) {
    function getReadingTime($body)
    {
        $myContent = $body;
        $word = str_word_count(strip_tags($myContent));
        $m = floor($word / 200);
        $s = floor($word % 200 / (200 / 60));

        if ($s > 30) {
            $m += 1;
            $s = 00;
        } else {
            $s = 00;
        }

        if ($m == 0) {
            $m += 1;
        }

        $time = $m." ".__('messages.min_read').($m == 1 ? '' : '');

        return $time;
    }
}

/**
 * @return array
 */
if (! function_exists('getTrendingPost')) {
    function getTrendingPost()
    {
        return Cache::remember('trending_posts:v3', 300, function () {
            // 1) Pre-aggregate all-time (or add a time window if you prefer)
            $agg = DB::table('analytics')
                ->select('post_id', DB::raw('COUNT(*) AS cnt'))
                ->groupBy('post_id');

            // 2) Join to posts, filter, order by views, limit 6, fetch needed fields
            $rows = DB::query()->fromSub($agg, 'a')
                ->join('posts as p', 'p.id', '=', 'a.post_id')
                ->where('p.visibility', Post::VISIBILITY_ACTIVE)
                ->orderByDesc('a.cnt')
                ->limit(6)
                ->get([
                    'p.id', 'p.category_id', 'p.post_types', 'p.slug',
                    'p.title', 'p.created_at',
                    DB::raw('a.cnt as total_count')
                ]);

            // 3) Eager-load relations for exactly these posts, keep the popularity order
            $orderedIds = $rows->pluck('id')->all();

            if (empty($orderedIds)) {
                return [];
            }

            $posts = Post::with('category', 'postVideo')
                ->whereIn('id', $orderedIds)
                // preserve SQL order
                ->orderByRaw('FIELD(id, '.implode(',', $orderedIds).')')
                ->get(['id','category_id','post_types','slug','title','created_at'])
                ->toArray();

            return $posts;
        });
    }

    function formatNewsDate($date) {
        $carbon = \Carbon\Carbon::parse($date);
        $day = $carbon->format('d');
        $year = $carbon->format('Y');
        $monthKey = 'messages.common.' . strtolower($carbon->format('F'));
        $month = ucfirst(__($monthKey));
        return "$day, $month, $year";
    }
}

/**s
 *
 * @return Post[]|Builder[]|Collection
 */
if (! function_exists('getBreakingPost')) {
    function getBreakingPost()
    {
        $getBreakingPost = Post::with('category', 'user')->whereBreaking(1)->whereVisibility(Post::VISIBILITY_ACTIVE)->orderBy('updated_at', 'DESC')->get();

        return $getBreakingPost;
    }
}

/**
 * @return Post[]|Builder[]|Collection
 */
if (! function_exists('getRecommendedPost')) {
    function getRecommendedPost()
    {
        $recommendedPosts = Post::with('category', 'postVideo')
            ->withCount(['comments as comment_count' => fn ($q) => $q->where('status', 1)])
            ->whereRecommended(1)->whereVisibility(Post::VISIBILITY_ACTIVE)->orderBy('updated_at', 'desc')->take(6)->get();

        return $recommendedPosts;
    }
}

/**
 * @return mixed|null
 */
if (! function_exists('getSelectLanguage')) {
    function getSelectLanguage()
    {
        $langIdLanguage = empty(Session::get('languageChange')['data']);

        if ($langIdLanguage) {
            $langId = 1;
        } else {
            $langId = Session::get('languageChange')['data'];
        }

        return $langId;
    }
}

/**
 * @return mixed
 */
if (! function_exists('getSelectLanguageName')) {
    function getSelectLanguageName()
    {
        return Language::find(getSelectLanguage())->name;
    }
}

if (! function_exists('getFrontSelectLanguage')) {
    function getFrontSelectLanguage()
    {
        $langIdLanguage = empty(Session::get('frontLanguageChange'));

        if ($langIdLanguage) {
            $langId = getSettingValue()['front_language'];
        } else {
            $langId = Session::get('frontLanguageChange');
        }

        return $langId;
    }
}

/**
 * @return mixed
 */
if (! function_exists('getFrontSelectLanguageName')) {
    function getFrontSelectLanguageName()
    {
        static $languageName;

        if (empty($languageName)) {
            $languageName = ! empty(Language::find(getFrontSelectLanguage())) ? Language::find(getFrontSelectLanguage())->name : '';
        }

        return $languageName;
    }
}

/**
 * @return \Anhskohbo\NoCaptcha\NoCaptcha
 */
if (! function_exists('reCaptcha')) {
    function reCaptcha()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $secret = $settings['secret_key'];
        $sitekey = $settings['site_key'];
        $captcha = new Anhskohbo\NoCaptcha\NoCaptcha($secret, $sitekey);

        return $captcha;
    }
}

/**
 * @return mixed
 */
if (! function_exists('getSEOTools')) {
    function getSEOTools()
    {
        static $seoTool;

        if (empty($seoTool)) {
            $seoTool = SeoTool::with('language')->first();
        }
        if ($seoTool->language->name == getFrontSelectLanguageName()) {
            return $seoTool;
        }
    }
}

if (! function_exists('getCategoryNumbers')) {
    function getCategoryNumbers($range): array
    {
        $result = [];
        $count = 1;
        $start = 1;
        foreach ($range as $val) {
            if ($val % 2 == 0) {
                $skip = 1;
            } else {
                $skip = 3;
            }
            $result[] = $start;
            $start += $skip;
            $count++;
        }

        return array_values(array_unique($result));
    }
}

if (! function_exists('getCurrentVersion')) {
    function getCurrentVersion()
    {
        $composerFile = file_get_contents('../composer.json');

        $composerData = json_decode($composerFile, true);

        return $composerData['version'];
    }
}

if (! function_exists('checkAdSpaced')) {
    function checkAdSpaced($name)
    {
        $check = Setting::where('key', $name)->pluck('value')->first();

        return $check;
    }
}

if (! function_exists('getAdImageDesktop')) {
    function getAdImageDesktop($id)
    {
        $agent = new Agent;
        if ($agent->isMobile() && $agent->isTablet()) {
            $image = AdSpaces::whereAdSpaces($id)->whereAdView(AdSpaces::MOBILE)->first();
        } else {
            $image = AdSpaces::whereAdSpaces($id)->whereAdView(AdSpaces::DESKTOP)->first();
        }

        return $image;
    }
}

if (! function_exists('getAdImageMobile')) {
    function getAdImageMobile($id)
    {
        $image = AdSpaces::whereAdSpaces($id)->whereAdView(AdSpaces::MOBILE)->first();

        return $image;
    }
}

if (! function_exists('GetMail')) {
    function GetMail()
    {
        return MailSetting::first();
    }
}

if (! function_exists('getCurrencies')) {
    function getCurrencies()
    {
        $currencies = Currency::all();
        foreach ($currencies as $currency) {
            $currencyList[$currency->id] = $currency->currency_icon.' - '.$currency->currency_name;
        }

        return $currencyList;
    }
}

if (! function_exists('removeCommaFromNumbers')) {
    function removeCommaFromNumbers($number)
    {
        return (gettype($number) == 'string' && ! empty($number)) ? str_replace(',', '', $number) : $number;
    }
}

if (! function_exists('getCurrentSubscription')) {
    function getCurrentSubscription()
    {
        $subscription = Subscription::with(['plan.currency'])
            ->whereUserId(getLogInUserId())
            ->where('status', Subscription::ACTIVE)->latest()->first();

        return $subscription;
    }
}

if (! function_exists('currencyFormat')) {
    function currencyFormat($number, $currencyCode = null)
    {
        return $currencyCode.number_format($number, 2);
    }
}

if (! function_exists('getCurrentPlanDetails')) {
    function getCurrentPlanDetails()
    {
        $currentSubscription = getCurrentSubscription();
        $isExpired = $currentSubscription->isExpired();
        $currentPlan = $currentSubscription->plan;

        if ($currentPlan->price != $currentSubscription->plan_amount) {
            $currentPlan->price = $currentSubscription->plan_amount;
        }

        $startsAt = Carbon::now();
        $totalDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($currentSubscription->ends_at);
        $usedDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($startsAt);
        if ($totalDays > $usedDays) {
            $usedDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($startsAt);
        } else {
            $usedDays = $totalDays;
        }
        if ($totalDays > $usedDays) {
            $remainingDays = $totalDays - $usedDays;
        } else {
            $remainingDays = 0;
        }

        if ($totalDays == 0) {
            $totalDays = 1;
        }

        $frequency = $currentSubscription->plan_frequency == Plan::MONTHLY ? 'Monthly' : 'Yearly';

        //    $days = $currentSubscription->plan_frequency == Plan::MONTHLY ? 30 : 365;

        $perDayPrice = round($currentPlan->price / $totalDays, 2);
        // if (! empty($currentSubscription->trial_ends_at) || $isExpired) {
        //     $remainingBalance = 0.00;
        //     $usedBalance = 0.00;
        // } else {
        $isJPYCurrency = ! empty($currentPlan->currency) && isJPYCurrency($currentPlan->currency->currency_code);
        $remainingBalance = $currentPlan->price - ($perDayPrice * $usedDays);
        // $remainingBalance = $isJPYCurrency ? round($remainingBalance) : $remainingBalance;
        $usedBalance = $currentPlan->price - $remainingBalance;
        // $usedBalance = $isJPYCurrency ? round($usedBalance) : $usedBalance;
        // }

        return [
            'name' => $currentPlan->name.' / '.$frequency,
            'trialDays' => $currentPlan->trial_days,
            'startAt' => Carbon::parse($currentSubscription->starts_at)->format('jS M, Y'),
            'endsAt' => Carbon::parse($currentSubscription->ends_at)->format('jS M, Y'),
            'usedDays' => $usedDays,
            'remainingDays' => $remainingDays,
            'totalDays' => $totalDays,
            'usedBalance' => round($usedBalance, 2),
            'remainingBalance' => round($remainingBalance, 2),
            'isExpired' => $isExpired,
            'trial_ends_at' => $currentSubscription->trial_ends_at,
            'currentPlan' => $currentPlan,
        ];
    }
}

if (! function_exists('getProratedPlanData')) {
    function getProratedPlanData($planIDChosenByUser)
    {
        /** @var Plan $subscriptionPlan */
        $subscriptionPlan = Plan::findOrFail($planIDChosenByUser);

        if ($subscriptionPlan->frequency == Plan::MONTHLY) {

            $newPlanDays = 30;
            $frequency = 'Monthly';
        } else {
            if ($subscriptionPlan->frequency == Plan::YEARLY) {
                $newPlanDays = 365;
                $frequency = 'Yearly';
            } else {
                $newPlanDays = 36500;
                $frequency = 'Unlimited';
            }
        }

        $currentSubscription = getCurrentSubscription();
        $startsAt = Carbon::now();

        $carbonParseStartAt = Carbon::parse($currentSubscription->starts_at);
        $currentSubsTotalDays = $carbonParseStartAt->diffInDays($currentSubscription->ends_at);
        $usedDays = $carbonParseStartAt->copy()->diffInDays($startsAt);
        $totalExtraDays = 0;
        $totalDays = $newPlanDays;

        $endsAt = Carbon::now()->addDays($newPlanDays);

        $startsAt = $startsAt->copy()->format('jS M, Y');

        if ($usedDays <= 0) {
            $startsAt = $carbonParseStartAt->copy()->format('jS M, Y');
        }

        if (! $currentSubscription->isExpired() && ! checkIfPlanIsInTrial($currentSubscription)) {
            $amountToPay = 0;

            $currentPlan = $currentSubscription->plan; // TODO: take fields from subscription

            // checking if the current active subscription plan has the same price and frequency in order to process the calculation for the proration
            $planPrice = $currentPlan->price;
            $planFrequency = $currentPlan->frequency;
            if ($planPrice != $currentSubscription->plan_amount || $planFrequency != $currentSubscription->plan_frequency) {
                $planPrice = $currentSubscription->plan_amount;
                $planFrequency = $currentSubscription->plan_frequency;
            }

            $perDayPrice = round($planPrice / $currentSubsTotalDays, 2);
            $isJPYCurrency = ! empty($subscriptionPlan->currency) && isJPYCurrency($subscriptionPlan->currency->currency_code);

            $remainingBalance = $isJPYCurrency
                ? round($planPrice - ($perDayPrice * $usedDays))
                : round($planPrice - ($perDayPrice * $usedDays), 2);
            // dd($remainingBalance,$subscriptionPlan->price);

            if ($remainingBalance < $subscriptionPlan->price) { // adjust the amount in plan
                $amountToPay = $isJPYCurrency
                    ? round($subscriptionPlan->price - $remainingBalance)
                    : round($subscriptionPlan->price - $remainingBalance, 2);
            } else {
                $perDayPriceOfNewPlan = round($subscriptionPlan->price / $newPlanDays, 2);
                // dd($perDayPriceOfNewPlan);
                // $totalExtraDays = round($remainingBalance / $perDayPriceOfNewPlan);
                $totalExtraDays = 1;
                $endsAt = Carbon::now()->addDays($totalExtraDays);
                $totalDays = $totalExtraDays;
            }

            return [
                'id' => $subscriptionPlan->id,
                'startDate' => $startsAt,
                'name' => $subscriptionPlan->name.' / '.$frequency,
                'trialDays' => $subscriptionPlan->trial_days,
                'remainingBalance' => $remainingBalance,
                'endDate' => $endsAt->format('jS M, Y'),
                'amountToPay' => $amountToPay,
                'usedDays' => $usedDays,
                'totalExtraDays' => $totalExtraDays,
                'totalDays' => $totalDays,
            ];
        }

        return [
            'id' => $subscriptionPlan->id,
            'name' => $subscriptionPlan->name.' / '.$frequency,
            'trialDays' => $subscriptionPlan->trial_days,
            'startDate' => $startsAt,
            'endDate' => $endsAt,
            'remainingBalance' => 0,
            'amountToPay' => $subscriptionPlan->price,
            'usedDays' => $usedDays,
            'totalExtraDays' => $totalExtraDays,
            'totalDays' => $totalDays,
        ];
    }
}

if (! function_exists('checkIfPlanIsInTrial')) {
    function checkIfPlanIsInTrial($currentSubscription)
    {
        $now = Carbon::now();
        if (! empty($currentSubscription->trial_ends_at)) {
            return true;
        }

        return false;
    }
}

if (! function_exists('isJPYCurrency')) {
    function isJPYCurrency($code)
    {
        return $code == Currency::JPY_CODE;
    }
}

if (! function_exists('getPaymentGateway')) {
    function getPaymentGateway()
    {
        $paymentGateway = Subscription::PAYMENT_GATEWAY;
        $selectedPaymentGateways = PaymentGateway::pluck('payment_gateway')->toArray();
        foreach ($selectedPaymentGateways as $key => $gateway) {
            $gateWayKey = array_search($gateway, $paymentGateway, true);

            if (! checkPaymentGateway($gateWayKey)) {
                unset($selectedPaymentGateways[$key]);
            }
        }

        return array_intersect($paymentGateway, $selectedPaymentGateways);
    }
}

if (! function_exists('zeroDecimalCurrencies')) {
    function zeroDecimalCurrencies(): array
    {
        return [
            'BIF',
            'CLP',
            'DJF',
            'GNF',
            'JPY',
            'KMF',
            'KRW',
            'MGA',
            'PYG',
            'RWF',
            'UGX',
            'VND',
            'VUV',
            'XAF',
            'XOF',
            'XPF',
        ];
    }
}

if (! function_exists('getStripeAPIKey')) {
    function getStripeAPIKey()
    {
        $checkStripeCreds = Setting::where('key', 'stripe_checkbox_btn')->value('value');
        $apiKey = Setting::where('key', 'stripe_key')->value('value');
        $stripeApiKey = (isset($checkStripeCreds) && $checkStripeCreds == 1) && ! empty($apiKey) ? $apiKey : config('services.stripe.key');

        return $stripeApiKey;
    }
}

if (! function_exists('getStripeSecretKey')) {
    function getStripeSecretKey()
    {
        $checkStripeCreds = Setting::where('key', 'stripe_checkbox_btn')->value('value');
        $secretKey = Setting::where('key', 'stripe_secret')->value('value');
        $apiSecret = (isset($checkStripeCreds) && $checkStripeCreds == 1) && ! empty($secretKey) ? $secretKey : config('services.stripe.secret_key');

        return $apiSecret;
    }
}

if (! function_exists('getPayPalSupportedCurrencies')) {
    function getPayPalSupportedCurrencies()
    {
        return [
            'AUD',
            'BRL',
            'CAD',
            'CNY',
            'CZK',
            'DKK',
            'EUR',
            'HKD',
            'HUF',
            'ILS',
            'JPY',
            'MYR',
            'MXN',
            'TWD',
            'NZD',
            'NOK',
            'PHP',
            'PLN',
            'GBP',
            'RUB',
            'SGD',
            'SEK',
            'CHF',
            'THB',
            'USD',
        ];
    }
}

if (! function_exists('getloginuserplan')) {
    function getloginuserplan()
    {

        return Subscription::with('plan')->whereUserId(getLogInUserId())->whereStatus(Subscription::ACTIVE)->first();
    }
}

if (! function_exists('checkPaymentGateway')) {
    function checkPaymentGateway($paymentGateway): bool
    {

        if ($paymentGateway == Plan::STRIPE) {
            $checkStripeCreds = Setting::where('key', 'stripe_checkbox_btn')->value('value');
            $apiKey = Setting::where('key', 'stripe_key')->value('value');
            $secretKey = Setting::where('key', 'stripe_secret')->value('value');
            $apiKey = (isset($checkStripeCreds) && $checkStripeCreds == 1) && ! empty($apiKey) ? $apiKey : config('services.stripe.key');
            $apiSecret = (isset($checkStripeCreds) && $checkStripeCreds == 1) && ! empty($secretKey) ? $secretKey : config('services.stripe.secret_key');

            if (! empty($apiKey) && ! empty($apiSecret)) {
                return true;
            }

            return false;
        }

        if ($paymentGateway == Plan::PAYPAL) {
            $checkPaypalCreds = Setting::where('key', 'paypal_checkbox_btn')->value('value');
            $paypalKey = Setting::where('key', 'paypal_client_id')->value('value');
            $paypalSecretKey = Setting::where('key', 'paypal_secret')->value('value');
            $mode = Setting::where('key', 'paypal_mode')->value('value');
            $apiPaypalKey = (isset($checkPaypalCreds) && $checkPaypalCreds == 1) && ! empty($paypalKey) ? $paypalKey : config('paypal.sandbox.client_id');
            $apiPaypalSecret = (isset($checkPaypalCreds) && $checkPaypalCreds == 1) && ! empty($paypalSecretKey) ? $paypalSecretKey : config('paypal.sandbox.client_secret');
            $paypalmode = (isset($checkPaypalCreds) && $checkPaypalCreds == 1) && ! empty($mode) ? $mode : config('paypal.mode');
            if (! empty($apiPaypalKey) && ! empty($apiPaypalSecret) && ! empty($paypalmode)) {
                return true;
            }

            return false;
        }
        $manuallyEnabled = Setting::where('key', 'manually_checkbox_btn')->value('value');

        if (! empty($manuallyEnabled)) {
            return true;
        } else {
            return false;
        }

        return true;
    }
}

if (! function_exists('checkManuallyPaymentStatus')) {
    function checkManuallyPaymentStatus()
    {
        return Subscription::whereUserId(getLogInUserId())->latest()->first();
    }
}

if (! function_exists('getLanguageCategory')) {
    function getLanguageCategory($langId)
    {
        $category = Category::whereLangId($langId)->pluck('name', 'id')->toArray();

        return $category;
    }
}

if (! function_exists('getCategorySubCategory')) {
    function getCategorySubCategory($categoryId)
    {
        $subCategory = SubCategory::whereParentCategoryId($categoryId)->pluck('name', 'id')->toArray();

        return $subCategory;
    }
}

if (! function_exists('getFrontLanguage')) {
    function getFrontLanguage()
    {
        $language = Language::whereFrontLanguageStatus(Language::ACTIVE)->pluck('name', 'id');

        return $language;
    }
}

if (! function_exists('getLoginUserRole')) {
    function getLoginUserRole()
    {
        return getLogInUser()->role_name;
    }
}

if (! function_exists('checkLoginUserFollow')) {
    function checkLoginUserFollow($userId)
    {
        $following = Followers::whereFollowing(getLogInUserId())->whereFollowers($userId)->first();

        return $following;
    }
}

if (! function_exists('getCurrentTheme')) {
    function getCurrentTheme()
    {
        //return Setting::where('key', 'theme_configuration')->first()->value;
        //fixing only theme 2 for always
        return 2;
    }
}

if (! function_exists('make_slug')) {
    function make_slug($string)
    {

        return preg_replace('/[^\p{L}\p{N}]+/u', '-', trim($string));
    }
}

if (! function_exists('getFrontSelectLanguageIsoCode')) {
    function getFrontSelectLanguageIsoCode()
    {
        return Language::find(getFrontSelectLanguage())->iso_code;
    }
}

if (! function_exists('currentActiveSubscription')) {
    /**
     * @return Builder|Model|object|null
     */
    function currentActiveSubscription()
    {
        if (! Auth::user()) {
            return null;
        }
        /** @var Subscription $currentActivePlan */
        static $currentActivePlan;
        if ($currentActivePlan === null) {
            $currentActivePlan = Subscription::whereHas('plan')->with('plan')
                ->where('status', Subscription::ACTIVE)
                ->where('user_id', Auth::user()->id)
                ->latest()
                ->first();
        }

        return $currentActivePlan;
    }
}

if(!function_exists('renderPagination')){
    function renderPagination($totalPages, $currentPage)
    {
        $currentUrl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $urlParts = parse_url($currentUrl);
        parse_str($urlParts['query'] ?? '', $queryParams);

        $paginationHtml = '<div class="dash-pagination">
        <div class="row align-items-center">
            <div class="col-6">
                <p>Page ' . $currentPage . ' of ' . $totalPages . '</p>
            </div>
            <div class="col-6">
                <ul class="pagination justify-content-end">';

        // Previous Button
        $prevPage = $currentPage > 1 ? $currentPage - 1 : 1;
        $paginationHtml .= '<li class="' . ($currentPage > 1 ? '' : 'disabled') . '">
        <a href="' . ($currentPage > 1 ? '?' . http_build_query(array_merge($queryParams, ['page' => $prevPage])) : 'javascript:void(0);') . '">
            <i class="bx bx-chevron-left"></i>
        </a>
    </li>';

        // Pages to show
        $pages = [];
        $pages[] = 1;

        if ($currentPage > 3) $pages[] = '...';

        for ($i = max(2, $currentPage - 1); $i <= min($totalPages - 1, $currentPage + 1); $i++) {
            $pages[] = $i;
        }

        if ($currentPage < $totalPages - 2) $pages[] = '...';

        if ($totalPages > 1) $pages[] = $totalPages;

        foreach (array_unique($pages) as $page) {
            if ($page === '...') {
                $paginationHtml .= '<li><span class="px-2">...</span></li>';
            } else {
                $activeClass = $page == $currentPage ? 'active' : '';
                $paginationHtml .= '<li class="' . $activeClass . '">
                <a href="?' . http_build_query(array_merge($queryParams, ['page' => $page])) . '">' . $page . '</a>
            </li>';
            }
        }

        // Next Button
        $nextPage = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
        $paginationHtml .= '<li class="' . ($currentPage < $totalPages ? '' : 'disabled') . '">
        <a href="' . ($currentPage < $totalPages ? '?' . http_build_query(array_merge($queryParams, ['page' => $nextPage])) : 'javascript:void(0);') . '">
            <i class="bx bx-chevron-right"></i>
        </a>
    </li>';

        $paginationHtml .= '</ul></div></div></div>';

        return $paginationHtml;
    }

}
