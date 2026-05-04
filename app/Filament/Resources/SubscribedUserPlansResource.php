<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\SubscribedUserPlansResource\Pages;
use App\Filament\Resources\SubscribedUserPlansResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\SubscribedUserPlans;
use App\Models\Subscription;
use Carbon\Carbon;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SubscribedUserPlansResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?int $navigationSort = Sidebar::SUBSCRIBED_USER_PLANS->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                DatePicker::make('ends_at')
                    ->label(__('messages.subscription.end_date'))
                    ->minDate(function ($record) {
                        if ($record && $record->ends_at) {
                            return Carbon::parse($record->ends_at)->format('Y-m-d');
                        }
                        return Carbon::now()->startOfDay()->format('Y-m-d');
                    }),
                Hidden::make('status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction(false)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('user.full_name')
                    ->label(__('messages.user.full_name'))
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('plan.name')
                    ->label(__('messages.subscription.plan_name'))
                    ->searchable(),
                TextColumn::make('starts_at')
                    ->label(__('messages.subscription.start_date'))
                    ->searchable()
                    ->formatStateUsing(function (Subscription $record) {
                        return \Carbon\Carbon::parse($record->starts_at)->isoFormat('DD/MM/YYYY');
                    }),
                TextColumn::make('ends_at')
                    ->label(__('messages.subscription.end_date'))
                    ->searchable()
                    ->formatStateUsing(function (Subscription $record) {
                        return \Carbon\Carbon::parse($record->ends_at)->isoFormat('DD/MM/YYYY');
                    }),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->formatStateUsing(fn ($state) => $state == Subscription::ACTIVE ? __('messages.common.active') : __('messages.common.deactive'))
                    ->color(fn ($state) => $state == Subscription::ACTIVE ? 'success' : 'danger')
                    ->badge(fn ($state) => $state == Subscription::ACTIVE ? 'success' : 'danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('lg')->iconButton()
                    ->fillForm(function (Subscription $record) {
                        $record->status = Subscription::ACTIVE;
                        return $record->toArray();
                    })
                    ->successNotificationTitle(__('messages.placeholder.subscription_successfully_retrieved')),
                // Tables\Actions\DeleteAction::make(),
                ])
                ->actionsColumnLabel(__('messages.common.action'))
                ->actionsAlignment(function () {
                    return Session::get('locale') == 'ar' ? 'left' : 'right';

                });
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubscribedUserPlans::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.subscribed_user');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.subscribed_user');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('cash_payment');
    }
}
