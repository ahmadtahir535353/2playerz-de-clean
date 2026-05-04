<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameReleaseResource\Pages;
use App\Filament\Resources\GameReleaseResource\RelationManagers;
use App\Models\GameRelease;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class GameReleaseResource extends Resource
{
    protected static ?string $model = GameRelease::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Release Calendar';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('messages.release_calendar.navigation_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.release_calendar.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('messages.release_calendar.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.release_calendar.plural_model_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.release_calendar.game_information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('messages.release_calendar.name'))
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 600)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $operation, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug((string) $old)) {
                                    return;
                                }

                                if ($operation == 'create' || ($operation == 'edit' && empty($get('slug')))) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label(__('messages.release_calendar.slug'))
                            ->required()
                            ->maxLength(191)
                            ->unique(ignorable: fn(?GameRelease $record) => $record),
                        TextInput::make('link')
                            ->label(__('messages.release_calendar.link_address'))
                            ->url()
                            ->placeholder('https://example.com/game')
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn ($state) => blank($state) ? '' : trim((string) $state))
                            ->nullable(),
                    ])
                    ->columns(2),
                
                Section::make(__('messages.release_calendar.release_date'))
                    ->schema([
                        DatePicker::make('release_date')
                            ->label(__('messages.release_calendar.release_date'))
                            ->placeholder('dd.mm.yyyy')
                            ->displayFormat('d.m.Y')
                            ->native(false)
                            ->helperText(__('messages.release_calendar.release_date_helper'))
                            ->nullable()
                            ->live(debounce: 400)
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (empty($state)) {
                                    return;
                                }

                                try {
                                    $date = Carbon::parse($state);
                                    $set('release_year', $date->year);
                                    $set('release_month', $date->month);
                                } catch (\Exception $e) {
                                    // Ignore parse errors, user can still set year/month manually
                                }
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (blank($state)) {
                                    return null;
                                }

                                try {
                                    return Carbon::parse($state)->toDateString();
                                } catch (\Exception $e) {
                                    return null;
                                }
                            }),
                        TextInput::make('release_year')
                            ->label(__('messages.release_calendar.year'))
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(2100)
                            ->nullable()
                            ->dehydrated(true),
                        Select::make('release_month')
                            ->label(__('messages.release_calendar.month'))
                            ->options([
                                1 => 'Jan',
                                2 => 'Feb',
                                3 => 'Mar',
                                4 => 'Apr',
                                5 => 'May',
                                6 => 'Jun',
                                7 => 'Jul',
                                8 => 'Aug',
                                9 => 'Sep',
                                10 => 'Oct',
                                11 => 'Nov',
                                12 => 'Dec',
                            ])
                            ->nullable()
                            ->dehydrated(true),
                    ])
                    ->columns(3),
                
                Section::make(__('messages.release_calendar.platforms'))
                    ->schema([
                        Toggle::make('playstation')
                            ->label(__('messages.release_calendar.playstation'))
                            ->default(false),
                        Toggle::make('xbox')
                            ->label(__('messages.release_calendar.xbox'))
                            ->default(false),
                        Toggle::make('nintendo')
                            ->label(__('messages.release_calendar.nintendo'))
                            ->default(false),
                    ])
                    ->columns(3),
                
                Section::make(__('messages.release_calendar.subscription_services'))
                    ->schema([
                        Toggle::make('ps_plus')
                            ->label(__('messages.release_calendar.ps_plus'))
                            ->default(false),
                        Toggle::make('game_pass')
                            ->label(__('messages.release_calendar.game_pass'))
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.release_calendar.name'))
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('playstation')
                    ->label(__('messages.release_calendar.playstation'))
                    ->sortable(),
                ToggleColumn::make('xbox')
                    ->label(__('messages.release_calendar.xbox'))
                    ->sortable(),
                ToggleColumn::make('nintendo')
                    ->label(__('messages.release_calendar.nintendo'))
                    ->sortable(),
                ToggleColumn::make('ps_plus')
                    ->label(__('messages.release_calendar.ps_plus'))
                    ->sortable(),
                ToggleColumn::make('game_pass')
                    ->label(__('messages.release_calendar.game_pass'))
                    ->sortable(),
                TextColumn::make('release_date')
                    ->label(__('messages.release_calendar.date'))
                    ->getStateUsing(function ($record) {
                        if (!$record) {
                            return '—';
                        }
                        if ($record->release_date) {
                            return $record->release_date->format('d.m.Y');
                        }
                        if ($record->release_year) {
                            if ($record->release_month) {
                                $monthNames = [
                                    1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April',
                                    5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember'
                                ];
                                $monthName = $monthNames[$record->release_month] ?? $record->release_month;
                                return $monthName . ' ' . $record->release_year;
                            }
                            return (string) $record->release_year;
                        }
                        return '—';
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('playstation')
                    ->label(__('messages.release_calendar.playstation'))
                    ->query(fn (Builder $query): Builder => $query->where('playstation', true)),
                Tables\Filters\Filter::make('xbox')
                    ->label(__('messages.release_calendar.xbox'))
                    ->query(fn (Builder $query): Builder => $query->where('xbox', true)),
                Tables\Filters\Filter::make('nintendo')
                    ->label(__('messages.release_calendar.nintendo'))
                    ->query(fn (Builder $query): Builder => $query->where('nintendo', true)),
                Tables\Filters\Filter::make('with_dates')
                    ->label(__('messages.release_calendar.with_release_date'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('release_date')),
                Tables\Filters\Filter::make('without_dates')
                    ->label(__('messages.release_calendar.without_release_date'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('release_date')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGameReleases::route('/'),
            'create' => Pages\CreateGameRelease::route('/create'),
            'edit' => Pages\EditGameRelease::route('/{record}/edit'),
        ];
    }
}
