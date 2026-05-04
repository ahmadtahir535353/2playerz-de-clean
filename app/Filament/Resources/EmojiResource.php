<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\EmojiResource\Pages;
use App\Filament\Resources\EmojiResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Emoji;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class EmojiResource extends Resource
{
    protected static ?string $model = Emoji::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';

    protected static ?int $navigationSort = Sidebar::EMOJIS->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];
    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.common.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('emoji')
                    ->label(__('messages.emoji.emoji'))
                    ->formatStateUsing(fn(Emoji $record) => html_entity_decode($record->emoji))
                    ->sortable()
                    ->searchable(),
                ToggleColumn::make('status')
                    ->label(__('messages.status'))
                    ->updateStateUsing(function ($record, $state) {
                        $activeEmoji = Emoji::whereStatus(Emoji::ACTIVE);
                        if ($record->status == Emoji::ACTIVE && $activeEmoji->count() <= 4) {
                            if (auth()->user()->hasRole('customer')) {
                                redirect()->route('filament.customer.resources.emoji.index');
                            } else {
                                redirect()->route('filament.admin.resources.emoji.index');
                            }
                            return Notification::make()
                                ->danger()
                                ->title(__('messages.placeholder.You_disable_less_than_emoji'))
                                ->send();
                        }
                        if ($record->status == Emoji::DISABLE && $activeEmoji->count() >= 7) {
                            return Notification::make()
                                ->danger()
                                ->title(__('messages.placeholder.You_active_more_than_emoji'))
                                ->send();
                        }
                        $updateStatus = ! $record->status;
                        $record->update(['status' => $updateStatus]);
                        Notification::make()
                            ->success()
                            ->title(__('messages.placeholder.emoji_status_updated_successfully'))
                            ->duration(2000)
                            ->send();
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListEmoji::route('/'),
            'create' => Pages\CreateEmoji::route('/create'),
            'view' => Pages\ViewEmoji::route('/{record}'),
            'edit' => Pages\EditEmoji::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.emoji.emojis');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.emoji.emojis');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_emoji');
    }
}
