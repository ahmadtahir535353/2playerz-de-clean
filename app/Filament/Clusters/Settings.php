<?php

namespace App\Filament\Clusters;

use App\Enums\Sidebar;
use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Auth;

class Settings extends Cluster

{
    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?int $navigationSort = Sidebar::SETTINGS->value;

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermissionTo('manage_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.settings');
    }
}
