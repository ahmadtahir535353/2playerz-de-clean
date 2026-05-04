<?php

namespace App\Http\Controllers;

use App\Models\GameRelease;
use App\Models\ReleaseCalendarBadgeColor;
use App\Models\ReleaseListSetting;
use App\Models\User;
use App\Models\Comment;
use App\Models\UserGameWishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReleaseCalendarController extends Controller
{
    /**
     * Get creators who have a release list setting for the given list type
     */
    private function getCreatorsForListType(string $listType)
    {
        return ReleaseListSetting::where('list_type', $listType)
            ->with('creator')
            ->get()
            ->pluck('creator')
            ->filter()
            ->unique('id')
            ->values();
    }

    /**
     * Get release list setting for list type, optionally filtered by creator
     */
    private function getSettingForListType(string $listType, ?int $creatorId = null)
    {
        $query = ReleaseListSetting::with(['creator.media'])->where('list_type', $listType);
        if ($creatorId) {
            $query->where('created_by', $creatorId);
        }
        return $query->first();
    }

    /**
     * Record a view for the release list setting (once per session per setting).
     */
    private function recordReleaseListView(?ReleaseListSetting $setting): void
    {
        if (!$setting) {
            return;
        }
        $key = 'release_list_view_' . $setting->id;
        if (session()->has($key)) {
            return;
        }
        session()->put($key, true);
        $setting->increment('views_count');
    }

    /**
     * Display all game releases (all platforms)
     */
    public function index()
    {
        $creatorId = request()->integer('creator_id', 0) ?: null;
        $setting = $this->getSettingForListType(ReleaseListSetting::LIST_TYPE_ALL, $creatorId);
        if (!$setting && $creatorId) {
            $setting = $this->getSettingForListType(ReleaseListSetting::LIST_TYPE_ALL);
        }
        
        // Ensure creator media is loaded if setting exists
        if ($setting && $setting->creator) {
            $setting->creator->load('media');
        }
        
        $this->recordReleaseListView($setting);
        
        // Get like count and user liked status
        if ($setting) {
            $setting->likes_count = DB::table('likes')
                ->where('item_id', $setting->id)
                ->where('item_type', 'release_list')
                ->count();
            
            $user = auth()->user();
            $setting->user_liked = false;
            if ($user) {
                $setting->user_liked = (bool) DB::table('likes')
                    ->where('user_id', $user->id)
                    ->where('item_id', $setting->id)
                    ->where('item_type', 'release_list')
                    ->exists();
            }
        }
        
        // Get games with dates: group by year first, then by year-month within each year
        $gamesWithDates = $this->groupReleasesByYearThenMonth(
            GameRelease::withDates()->orderBy('release_year', 'asc')->orderBy('release_month', 'asc')->orderBy('release_date', 'asc')->get()
        );

        // Get games without dates, sorted alphabetically
        $gamesWithoutDates = GameRelease::withoutDates()
            ->orderBy('name', 'asc')
            ->get();

        // Get fallback admin user if creator is not available
        $adminUser = null;
        if (!$setting || !$setting->creator) {
            $adminUser = User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->with('media')->first();
        }

        // Get comment count for release_list
        $totalComments = 0;
        if ($setting) {
            $totalComments = Comment::where('status', 1)
                ->where('item_type', 'release_list')
                ->where('item_id', $setting->id)
                ->count();
        }

        $creators = $this->getCreatorsForListType(ReleaseListSetting::LIST_TYPE_ALL);

        $wishlistGameIds = $this->getWishlistGameIds();

        $data = [
            'setting' => $setting,
            'gamesWithDates' => $gamesWithDates,
            'gamesWithoutDates' => $gamesWithoutDates,
            'adminUser' => $adminUser,
            'totalComments' => $totalComments,
            'creators' => $creators,
            'selectedCreatorId' => $creatorId,
            'badgeColors' => ReleaseCalendarBadgeColor::colorsForView(),
            'wishlistGameIds' => $wishlistGameIds,
        ];

        if (getCurrentTheme() == 1) {
            return view('theme1.release-calendar.all', $data);
        }
        return view('front_new.release-calendar.all', $data);
    }

    /**
     * Display PlayStation-specific releases
     */
    public function playstation()
    {
        $creatorId = request()->integer('creator_id', 0) ?: null;
        $setting = $this->getSettingForListType(ReleaseListSetting::LIST_TYPE_PLAYSTATION, $creatorId);
        if (!$setting && $creatorId) {
            $setting = $this->getSettingForListType(ReleaseListSetting::LIST_TYPE_PLAYSTATION);
        }
        
        // Ensure creator media is loaded if setting exists
        if ($setting && $setting->creator) {
            $setting->creator->load('media');
        }
        
        $this->recordReleaseListView($setting);
        
        // Get like count and user liked status
        if ($setting) {
            $setting->likes_count = DB::table('likes')
                ->where('item_id', $setting->id)
                ->where('item_type', 'release_list')
                ->count();
            
            $user = auth()->user();
            $setting->user_liked = false;
            if ($user) {
                $setting->user_liked = (bool) DB::table('likes')
                    ->where('user_id', $user->id)
                    ->where('item_id', $setting->id)
                    ->where('item_type', 'release_list')
                    ->exists();
            }
        }
        
        $gamesWithDates = $this->groupReleasesByYearThenMonth(
            GameRelease::forPlatform('playstation')->withDates()->orderBy('release_year', 'asc')->orderBy('release_month', 'asc')->orderBy('release_date', 'asc')->get()
        );

        $gamesWithoutDates = GameRelease::forPlatform('playstation')
            ->withoutDates()
            ->orderBy('name', 'asc')
            ->get();

        // Get fallback admin user if creator is not available
        $adminUser = null;
        if (!$setting || !$setting->creator) {
            $adminUser = User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->with('media')->first();
        }

        // Get comment count for release_list
        $totalComments = 0;
        if ($setting) {
            $totalComments = Comment::where('status', 1)
                ->where('item_type', 'release_list')
                ->where('item_id', $setting->id)
                ->count();
        }

        $creators = $this->getCreatorsForListType(ReleaseListSetting::LIST_TYPE_PLAYSTATION);

        $wishlistGameIds = $this->getWishlistGameIds();

        $data = [
            'setting' => $setting,
            'gamesWithDates' => $gamesWithDates,
            'gamesWithoutDates' => $gamesWithoutDates,
            'platform' => 'PlayStation',
            'adminUser' => $adminUser,
            'totalComments' => $totalComments,
            'creators' => $creators,
            'selectedCreatorId' => $creatorId,
            'badgeColors' => ReleaseCalendarBadgeColor::colorsForView(),
            'wishlistGameIds' => $wishlistGameIds,
        ];

        if (getCurrentTheme() == 1) {
            return view('theme1.release-calendar.platform', $data);
        }
        return view('front_new.release-calendar.platform', $data);
    }

    /**
     * Display Xbox-specific releases
     */
    public function xbox()
    {
        $creatorId = request()->integer('creator_id', 0) ?: null;
        $setting = $this->getSettingForListType(ReleaseListSetting::LIST_TYPE_XBOX, $creatorId);
        if (!$setting && $creatorId) {
            $setting = $this->getSettingForListType(ReleaseListSetting::LIST_TYPE_XBOX);
        }
        
        // Ensure creator media is loaded if setting exists
        if ($setting && $setting->creator) {
            $setting->creator->load('media');
        }
        
        $this->recordReleaseListView($setting);
        
        // Get like count and user liked status
        if ($setting) {
            $setting->likes_count = DB::table('likes')
                ->where('item_id', $setting->id)
                ->where('item_type', 'release_list')
                ->count();
            
            $user = auth()->user();
            $setting->user_liked = false;
            if ($user) {
                $setting->user_liked = (bool) DB::table('likes')
                    ->where('user_id', $user->id)
                    ->where('item_id', $setting->id)
                    ->where('item_type', 'release_list')
                    ->exists();
            }
        }
        
        $gamesWithDates = $this->groupReleasesByYearThenMonth(
            GameRelease::forPlatform('xbox')->withDates()->orderBy('release_year', 'asc')->orderBy('release_month', 'asc')->orderBy('release_date', 'asc')->get()
        );

        $gamesWithoutDates = GameRelease::forPlatform('xbox')
            ->withoutDates()
            ->orderBy('name', 'asc')
            ->get();

        // Get fallback admin user if creator is not available
        $adminUser = null;
        if (!$setting || !$setting->creator) {
            $adminUser = User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->with('media')->first();
        }

        // Get comment count for release_list
        $totalComments = 0;
        if ($setting) {
            $totalComments = Comment::where('status', 1)
                ->where('item_type', 'release_list')
                ->where('item_id', $setting->id)
                ->count();
        }

        $creators = $this->getCreatorsForListType(ReleaseListSetting::LIST_TYPE_XBOX);

        $wishlistGameIds = $this->getWishlistGameIds();

        $data = [
            'setting' => $setting,
            'gamesWithDates' => $gamesWithDates,
            'gamesWithoutDates' => $gamesWithoutDates,
            'platform' => 'Xbox',
            'adminUser' => $adminUser,
            'totalComments' => $totalComments,
            'creators' => $creators,
            'selectedCreatorId' => $creatorId,
            'badgeColors' => ReleaseCalendarBadgeColor::colorsForView(),
            'wishlistGameIds' => $wishlistGameIds,
        ];

        if (getCurrentTheme() == 1) {
            return view('theme1.release-calendar.platform', $data);
        }
        return view('front_new.release-calendar.platform', $data);
    }

    /**
     * Display Nintendo-specific releases
     */
    public function nintendo()
    {
        $creatorId = request()->integer('creator_id', 0) ?: null;
        $setting = $this->getSettingForListType(ReleaseListSetting::LIST_TYPE_NINTENDO, $creatorId);
        if (!$setting && $creatorId) {
            $setting = $this->getSettingForListType(ReleaseListSetting::LIST_TYPE_NINTENDO);
        }
        
        // Ensure creator media is loaded if setting exists
        if ($setting && $setting->creator) {
            $setting->creator->load('media');
        }
        
        $this->recordReleaseListView($setting);
        
        // Get like count and user liked status
        if ($setting) {
            $setting->likes_count = DB::table('likes')
                ->where('item_id', $setting->id)
                ->where('item_type', 'release_list')
                ->count();
            
            $user = auth()->user();
            $setting->user_liked = false;
            if ($user) {
                $setting->user_liked = (bool) DB::table('likes')
                    ->where('user_id', $user->id)
                    ->where('item_id', $setting->id)
                    ->where('item_type', 'release_list')
                    ->exists();
            }
        }
        
        $gamesWithDates = $this->groupReleasesByYearThenMonth(
            GameRelease::forPlatform('nintendo')->withDates()->orderBy('release_year', 'asc')->orderBy('release_month', 'asc')->orderBy('release_date', 'asc')->get()
        );

        $gamesWithoutDates = GameRelease::forPlatform('nintendo')
            ->withoutDates()
            ->orderBy('name', 'asc')
            ->get();

        // Get fallback admin user if creator is not available
        $adminUser = null;
        if (!$setting || !$setting->creator) {
            $adminUser = User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->with('media')->first();
        }

        // Get comment count for release_list
        $totalComments = 0;
        if ($setting) {
            $totalComments = Comment::where('status', 1)
                ->where('item_type', 'release_list')
                ->where('item_id', $setting->id)
                ->count();
        }

        $creators = $this->getCreatorsForListType(ReleaseListSetting::LIST_TYPE_NINTENDO);

        $wishlistGameIds = $this->getWishlistGameIds();

        $data = [
            'setting' => $setting,
            'gamesWithDates' => $gamesWithDates,
            'gamesWithoutDates' => $gamesWithoutDates,
            'platform' => 'Nintendo',
            'adminUser' => $adminUser,
            'totalComments' => $totalComments,
            'creators' => $creators,
            'selectedCreatorId' => $creatorId,
            'badgeColors' => ReleaseCalendarBadgeColor::colorsForView(),
            'wishlistGameIds' => $wishlistGameIds,
        ];

        if (getCurrentTheme() == 1) {
            return view('theme1.release-calendar.platform', $data);
        }
        return view('front_new.release-calendar.platform', $data);
    }

    /**
     * Get set of game_release IDs that the current user has on their wishlist
     */
    private function getWishlistGameIds(): array
    {
        $user = auth()->user();
        if (! $user) {
            return [];
        }
        return UserGameWishlist::where('user_id', $user->id)->pluck('game_release_id')->all();
    }

    /**
     * Group game releases by year, then by month (chronological 01..12). Within each month:
     * Priority 1: Exact date games first (sorted by date), then Priority 2: Month-only games (sorted by name).
     * Priority 3: Year-only games in a final section (TBA) after December.
     * Returns: [ year => [ sections ], ... ] where each section is [ 'heading' => 'Month YYYY'|null, 'games' => Collection ].
     */
    private function groupReleasesByYearThenMonth($games)
    {
        $YEAR_ONLY_KEY = '13'; // sort after December (12)

        return $games->groupBy(function ($game) {
            $year = $game->release_date
                ? $game->release_date->year
                : ($game->release_year ?? null);
            return $year;
        })->map(function ($yearGroup) use ($YEAR_ONLY_KEY) {
            $byMonth = $yearGroup->groupBy(function ($game) use ($YEAR_ONLY_KEY) {
                if ($game->release_date) {
                    return $game->release_date->format('Y-m');
                }
                if ($game->release_month && $game->release_year) {
                    return sprintf('%04d-%02d', $game->release_year, $game->release_month);
                }
                if ($game->release_year) {
                    return $game->release_year . '-' . $YEAR_ONLY_KEY;
                }
                return null;
            })->sortKeys();

            $locale = app()->getLocale();
            $sections = [];
            foreach ($byMonth as $yearMonth => $monthGames) {
                $isYearOnly = str_ends_with($yearMonth, '-' . $YEAR_ONLY_KEY);
                $heading = null;
                if ($isYearOnly) {
                    $parts = explode('-', $yearMonth);
                    $heading = $parts[0]; // e.g. "2026" for games with only release year (TBA)
                } else {
                    $parts = explode('-', $yearMonth);
                    $heading = count($parts) === 2
                        ? ucfirst(Carbon::create((int)$parts[0], (int)$parts[1], 1)->locale($locale)->monthName) . ' ' . $parts[0]
                        : $yearMonth;
                }
                $sorted = $monthGames->sortBy(function ($game) {
                    if ($game->release_date) {
                        return '0-' . $game->release_date->format('Y-m-d') . '-' . $game->name;
                    }
                    return '1-9999-12-31-' . $game->name;
                })->values();
                $sections[] = [
                    'heading' => $heading,
                    'games' => $sorted,
                ];
            }
            return $sections;
        });
    }
}
