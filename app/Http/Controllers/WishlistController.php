<?php

namespace App\Http\Controllers;

use App\Models\GameRelease;
use App\Models\ReleaseCalendarBadgeColor;
use App\Models\UserGameWishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Toggle game in wishlist (add or remove). For AJAX from release calendar.
     */
    public function toggle(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.wishlist.login_required'),
                'require_login' => true,
            ], 403);
        }

        $request->validate([
            'game_release_id' => 'required|exists:game_releases,id',
        ]);

        $userId = Auth::id();
        $gameReleaseId = (int) $request->game_release_id;

        $exists = UserGameWishlist::where('user_id', $userId)
            ->where('game_release_id', $gameReleaseId)
            ->first();

        if ($exists) {
            $exists->delete();
            return response()->json([
                'success' => true,
                'on_wishlist' => false,
                'message' => __('messages.wishlist.removed'),
            ]);
        }

        UserGameWishlist::create([
            'user_id' => $userId,
            'game_release_id' => $gameReleaseId,
        ]);

        return response()->json([
            'success' => true,
            'on_wishlist' => true,
            'message' => __('messages.wishlist.added'),
        ]);
    }

    /**
     * Meine Wunschliste – profile page (10 per page, load more like my-comments)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $wishlistPaginator = $user->wishlistItems()
            ->with('gameRelease')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $badgeColors = ReleaseCalendarBadgeColor::colorsForView();

        if ($request->ajax()) {
            $html = view('customer-panel.partials.wishlist-rows', [
                'items' => $wishlistPaginator->getCollection(),
                'badgeColors' => $badgeColors,
            ])->render();

            return response()->json([
                'html' => $html,
                'hasMore' => $wishlistPaginator->hasMorePages(),
                'nextPage' => $wishlistPaginator->currentPage() + 1,
            ]);
        }

        return view('customer-panel.wishlist', compact('wishlistPaginator', 'badgeColors'));
    }

    /**
     * Remove game from wishlist (from profile wishlist page)
     */
    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:user_game_wishlist,id',
        ]);

        $item = UserGameWishlist::where('user_id', Auth::id())
            ->where('id', $request->id)
            ->firstOrFail();

        $item->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('wishlist.index')->with('success', __('messages.wishlist.removed'));
    }

    /**
     * Clear highlight when user "clicks" the highlighted game on wishlist page
     */
    public function clearHighlight(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:user_game_wishlist,id',
        ]);

        $item = UserGameWishlist::where('user_id', Auth::id())
            ->where('id', $request->id)
            ->firstOrFail();

        $item->update(['highlighted' => false]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('wishlist.index');
    }
}
