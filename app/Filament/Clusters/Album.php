<?php

namespace App\Filament\Clusters;

use App\Enums\Sidebar;
use Filament\Clusters\Cluster;

class Album extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Albums';

    protected static ?int $navigationSort = Sidebar::ALBUMS->value;

    public static function getNavigationLabel(): string
    {
        return __('messages.albums');
    }
}
