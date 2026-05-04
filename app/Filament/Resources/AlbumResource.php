<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Album;
use App\Enums\Sidebar;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AlbumResource\Pages;
use App\Filament\Clusters\Album as FilamentAlbum;
use App\Http\Middleware\CheckPaddingSubscription;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AlbumResource\RelationManagers;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = FilamentAlbum::class;


    protected static ?int $navigationSort = Sidebar::ALBUMS->value;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        // return $form
        //     ->columns(1)
        //     ->schema([
        //         TextInput::make('name')
        //             ->label(__('messages.common.name').':')
        //             ->validationAttribute(__('messages.common.name'))
        //             ->placeholder(__('messages.common.name'))
        //             ->required()
        //             ->maxLength(255)
        //             ->unique(ignorable: fn(?Album $record) => $record),
        //         Select::make('lang_id')
        //             ->label(__('messages.common.language').':')
        //             ->validationAttribute(__('messages.common.language'))
        //             ->placeholder(__('messages.common.select_language'))
        //             ->required()
        //             ->searchable()
        //             ->preload()
        //             ->relationship('language', 'name'),
        //         Hidden::make('is_default')
        //             ->default(false)
        //             ->dehydrated(function ($state) {
        //                 if (Schema::hasColumn('albums', 'is_default')) {
        //                     return true;
        //                 }
        //             }),
        //     ]);

        return $form
            ->schema(Album::getForm())->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction(false)
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('messages.common.name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('language.name')
                    ->label(__('messages.common.language'))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->modalWidth('md')
                    ->modalHeading(__('messages.album.edit_album'))
                    ->successNotificationTitle(__('messages.placeholder.album_updated_successfully')),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.album.album'))
                    ->successNotificationTitle(__('messages.placeholder.album_deleted_successfully')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.albums'))
                        ->successNotificationTitle(__('messages.placeholder.album_deleted_successfully')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAlbums::route('/'),
        ];
    }

    // public static function getNavigationGroup(): string
    // {
    //     return __('messages.albums');
    // }

    public static function getNavigationLabel(): string
    {
        return __('messages.albums');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.albums');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_albums');
    }
}
