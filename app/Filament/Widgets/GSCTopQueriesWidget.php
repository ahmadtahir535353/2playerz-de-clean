<?php

namespace App\Filament\Widgets;

use App\Models\GoogleSearchConsoleData;
use App\Models\GoogleSearchConsoleToken;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class GSCTopQueriesWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = null;

    public function table(Table $table): Table
    {
        $token = GoogleSearchConsoleToken::where('is_active', true)->first();

        $query = GoogleSearchConsoleData::query()
            ->where('data_type', 'query')
            ->whereNotNull('query')
            ->whereDate('date', '>=', Carbon::now()->subDays(30));

        if ($token) {
            $query->where('token_id', $token->id);
        }

        return $table
            ->query($query)
            ->heading(__('messages.gsc.top_search_queries'))
            ->defaultSort('clicks', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('query')
                    ->label(__('messages.gsc.search_query'))
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('clicks')
                    ->label(__('messages.gsc.clicks'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('impressions')
                    ->label(__('messages.gsc.impressions'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ctr')
                    ->label(__('messages.gsc.ctr'))
                    ->formatStateUsing(fn ($state) => number_format($state * 100, 2) . '%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->label(__('messages.gsc.position'))
                    ->formatStateUsing(fn ($state) => number_format($state, 1))
                    ->sortable(),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin|staff');
    }

    public function heading(): string
    {
        return __('messages.gsc.top_search_queries');
    }

    public function getTableHeading(): string
    {
        return __('messages.gsc.top_search_queries');
    }

    public static function getHeading(): string
    {
        return __('messages.gsc.top_search_queries');
    }
}

