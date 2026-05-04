<?php

namespace App\Observers;

use App\Models\GameRelease;
use App\Services\WishlistNotificationService;

class GameReleaseObserver
{
    protected array $relevantKeys = ['name', 'release_date', 'link', 'playstation', 'xbox', 'nintendo'];

    public function updated(GameRelease $game): void
    {
        if (! $game->wasChanged($this->relevantKeys)) {
            return;
        }
        $service = app(WishlistNotificationService::class);
        $service->notifyGameNews($game);
    }
}
