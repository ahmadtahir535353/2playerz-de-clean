<?php

namespace App\Services;

use App\Models\GameRelease;
use App\Models\UserGameWishlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WishlistNotificationService
{
    /**
     * Notify users who have this game on their wishlist that it releases today.
     */
    public function notifyReleaseToday(GameRelease $game): void
    {
        $message = 'Heute erscheint ein Spiel aus deiner Wunschliste: ' . $game->name;
        $this->notifyWishlistUsers($game, $message, 'release');
    }

    /**
     * Notify users who have this game on their wishlist that there is news about it.
     */
    public function notifyGameNews(GameRelease $game): void
    {
        $message = 'Es gibt Neuigkeiten zu einem deiner Wunschlisten-Einträge: ' . $game->name;
        $this->notifyWishlistUsers($game, $message, 'news');
    }

    /**
     * Insert notification and set highlighted for each user who has this game in wishlist.
     */
    protected function notifyWishlistUsers(GameRelease $game, string $message, string $reason): void
    {
        $pivots = UserGameWishlist::where('game_release_id', $game->id)->get();
        $wishlistPageUrl = url('/customers/wishlist');

        foreach ($pivots as $pivot) {
            $this->insertNotification(
                $pivot->user_id,
                $message,
                [
                    'game_release_id' => $game->id,
                    'game_name' => $game->name,
                    'reason' => $reason,
                    'action_url' => $wishlistPageUrl,
                ]
            );
            $pivot->update(['highlighted' => true]);
        }
    }

    /**
     * Insert one notification (same structure as existing app notifications).
     */
    protected function insertNotification(int $toUserId, string $message, array $data = []): void
    {
        $data = array_merge($data, ['message' => $message]);

        $row = [
            'type' => 'App\\Notifications\\WishlistGameNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $toUserId,
            'data' => json_encode($data),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('notifications', 'to_user_id')) {
            $row['to_user_id'] = $toUserId;
        }

        DB::table('notifications')->insert($row);
    }
}
