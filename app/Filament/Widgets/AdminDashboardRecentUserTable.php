<?php

namespace App\Filament\Widgets;

use App\Models\Staff;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class AdminDashboardRecentUserTable extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->limit(5))
            ->defaultSort('id', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('profile')
                    ->label(__('messages.staff.profile'))
                    ->circular()
                    ->collection(Staff::PROFILE)
                    ->simpleLightbox()
                    ->defaultImageUrl(asset('images/avatar.png')),
                Tables\Columns\TextColumn::make('full_name')->label(__('messages.user.full_name')),
                Tables\Columns\TextColumn::make('email')->label(__('messages.user.email')),
            ])
            ->paginated(false);
    }

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin'); // Ensure authenticated user has 'admin' role
    }

    public function heading(): string
    {
        return __('messages.dashboard_show.recent_user');
    }
    public function getTableHeading(): string
    {
        return __('messages.dashboard_show.recent_user');
    }
}
