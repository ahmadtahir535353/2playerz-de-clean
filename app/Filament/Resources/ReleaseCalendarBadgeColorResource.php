<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReleaseCalendarBadgeColorResource\Pages;
use App\Models\ReleaseCalendarBadgeColor;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReleaseCalendarBadgeColorResource extends Resource
{
    protected static ?string $model = ReleaseCalendarBadgeColor::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationGroup = 'Release Calendar';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('messages.release_calendar.navigation_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.release_calendar_badge_colors.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('messages.release_calendar_badge_colors.model_label');
    }

    public static function form(Form $form): Form
    {
        $rc = 'messages.release_calendar_badge_colors';
        return $form
            ->schema([
                Section::make(__('messages.release_calendar_badge_colors.badge_colors'))
                    ->description('Farben für die Badges auf der Release-Kalender-Seite (wie bei Playerz Levels).')
                    ->schema([
                        Section::make(__('messages.release_calendar.playstation'))
                            ->schema([
                                ColorPicker::make('playstation_bg')
                                    ->label(__("{$rc}.background_color"))
                                    ->default('#4a4a4a'),
                                ColorPicker::make('playstation_text')
                                    ->label(__("{$rc}.text_color"))
                                    ->default('#e0e0e0'),
                            ])
                            ->columns(2),
                        Section::make(__('messages.release_calendar.xbox'))
                            ->schema([
                                ColorPicker::make('xbox_bg')
                                    ->label(__("{$rc}.background_color"))
                                    ->default('#4a4a4a'),
                                ColorPicker::make('xbox_text')
                                    ->label(__("{$rc}.text_color"))
                                    ->default('#e0e0e0'),
                            ])
                            ->columns(2),
                        Section::make(__('messages.release_calendar.nintendo'))
                            ->schema([
                                ColorPicker::make('nintendo_bg')
                                    ->label(__("{$rc}.background_color"))
                                    ->default('#4a4a4a'),
                                ColorPicker::make('nintendo_text')
                                    ->label(__("{$rc}.text_color"))
                                    ->default('#e0e0e0'),
                            ])
                            ->columns(2),
                        Section::make(__('messages.release_calendar.ps_plus'))
                            ->schema([
                                ColorPicker::make('ps_plus_bg')
                                    ->label(__("{$rc}.background_color"))
                                    ->default('#1976d2'),
                                ColorPicker::make('ps_plus_text')
                                    ->label(__("{$rc}.text_color"))
                                    ->default('#ffffff'),
                            ])
                            ->columns(2),
                        Section::make(__('messages.release_calendar.game_pass'))
                            ->schema([
                                ColorPicker::make('game_pass_bg')
                                    ->label(__("{$rc}.background_color"))
                                    ->default('#107c10'),
                                ColorPicker::make('game_pass_text')
                                    ->label(__("{$rc}.text_color"))
                                    ->default('#ffffff'),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('playstation_bg')->label('PlayStation')->formatStateUsing(fn ($state) => $state ?: '-'),
                TextColumn::make('xbox_bg')->label('Xbox')->formatStateUsing(fn ($state) => $state ?: '-'),
                TextColumn::make('nintendo_bg')->label('Nintendo')->formatStateUsing(fn ($state) => $state ?: '-'),
                TextColumn::make('ps_plus_bg')->label('PS Plus')->formatStateUsing(fn ($state) => $state ?: '-'),
                TextColumn::make('game_pass_bg')->label('Game Pass')->formatStateUsing(fn ($state) => $state ?: '-'),
            ])
            ->defaultSort('id')
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReleaseCalendarBadgeColors::route('/'),
            'edit' => Pages\EditReleaseCalendarBadgeColor::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
