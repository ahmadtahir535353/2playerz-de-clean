<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\PlansResource\Pages;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Plan;
use App\Models\Subscription;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PlansResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static ?int $navigationSort = Sidebar::PLANS->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('messages.common.name') . ':')
                            ->validationAttribute(__('messages.common.name'))
                            ->placeholder(__('messages.common.name'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignorable: fn(?Plan $record) => $record),
                        Select::make('frequency')
                            ->label(__('messages.plans.frequency') . ':')
                            ->validationAttribute(__('messages.plans.frequency'))
                            ->placeholder(__('messages.plans.frequency'))
                            ->options(plan::DURATION)
                            ->native(false)
                            ->searchable()
                            ->default(1)
                            ->required(),
                        Select::make('currency_id')
                            ->label(__('messages.plans.currency') . ':')
                            ->validationAttribute(__('messages.plans.currency'))
                            ->placeholder(__('messages.plans.select_currency'))
                            ->relationship('currency', 'currency_name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),
                        TextInput::make('price')
                            ->label(__('messages.plans.price') . ':')
                            ->validationAttribute(__('messages.plans.price'))
                            ->placeholder(__('messages.plans.price'))
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->required(),
                        TextInput::make('post_count')
                            ->label(__('messages.plans.no_of_posts') . ':')
                            ->validationAttribute(__('messages.plans.no_of_posts'))
                            ->placeholder(__('messages.plans.no_of_posts'))
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->required(),
                        TextInput::make('trial_days')
                            ->label(__('messages.plans.trial_days') . ':')
                            ->validationAttribute(__('messages.plans.trial_days'))
                            ->placeholder(__('messages.plans.trial_days'))
                            ->numeric()
                            ->integer()
                            ->minValue(0),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.common.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->label(__('messages.plans.price'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('frequency')
                    ->label(__('messages.plans.frequency'))
                    ->sortable()
                    ->state(function (Plan $record) {
                        if ($record->frequency == 1) {
                            return __('messages.plans.monthly');
                        } elseif ($record->frequency == 2) {
                            return __('messages.plans.yearly');
                        } else {
                            return __('messages.plans.unlimited');
                        }
                    })
                    ->badge()
                    ->color(function (Plan $record) {
                        if ($record->frequency == 1) {
                            return 'primary';
                        } elseif ($record->frequency == 2) {
                            return 'info';
                        } else {
                            return 'success';
                        }
                    }),

                // ToggleColumn::make('is_default')
                //     ->label(__('messages.language.is_default'))
                //     ->updateStateUsing(function (Plan $record, bool $state) {
                //         if ($state) {
                //             Plan::where('is_default', true)->update(['is_default' => false]);
                //             $record->is_default = true;
                //             $record->save();
                //         }
                //     }),

                ViewColumn::make('is_default')
                    ->sortable()
                    ->label(__('messages.language.is_default'))
                    ->view('filament.tables.columns.status-switcher'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Plan $record) {
                        $subscription = Subscription::where('plan_id', $record->id)->where('status', Subscription::ACTIVE)->count();
                        if ($record->is_default == 1) {
                            return Notification::make()
                                ->title(__('messages.placeholder.default_plan'))
                                ->danger()
                                ->send();
                        }
                        if ($subscription > 0) {
                            return Notification::make()
                                ->title(__('messages.placeholder.plan_already_used'))
                                ->danger()
                                ->send();
                        }

                        $record->delete();

                        return Notification::make()
                            ->title(__('messages.placeholder.plan_deleted_successfully'))
                            ->success()
                            ->send();
                    })
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.plans.plan')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.plans.plans'))
                        ->successNotificationTitle(__('messages.placeholder.plan_deleted_successfully')),
                ]),
            ]);
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlans::route('/create'),
            'view' => Pages\ViewPlans::route('/{record}'),
            'edit' => Pages\EditPlans::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.plans.plans');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.plans.plans');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_plans');
    }
}
