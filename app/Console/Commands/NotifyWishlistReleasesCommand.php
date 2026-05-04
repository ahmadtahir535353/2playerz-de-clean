<?php

namespace App\Console\Commands;

use App\Models\GameRelease;
use App\Services\WishlistNotificationService;
use Illuminate\Console\Command;

class NotifyWishlistReleasesCommand extends Command
{
    protected $signature = 'wishlist:notify-releases';
    protected $description = 'Notify users when a game from their wishlist releases today';

    public function handle(WishlistNotificationService $service): int
    {
        $today = now()->toDateString();
        $games = GameRelease::whereDate('release_date', $today)->get();

        foreach ($games as $game) {
            $service->notifyReleaseToday($game);
        }

        $this->info('Processed ' . $games->count() . ' game(s) releasing today.');
        return self::SUCCESS;
    }
}
