<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Enums\Sidebar;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AlbumCategory;
use App\Models\AlbumCategories;
use App\Filament\Clusters\Album;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Middleware\CheckPaddingSubscription;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AlbumCategoriesResource\Pages;
use App\Filament\Resources\AlbumCategoriesResource\RelationManagers;

class AlbumCategoriesResource extends Resource
{
    protected static ?string $model = AlbumCategory::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Album::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = Sidebar::ALBUM_CATEGORIES->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {

        return $form
            ->schema(AlbumCategory::getForm())->columns(1);
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
                Tables\Columns\TextColumn::make('album.name')
                    ->label(__('messages.gallery.album'))
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
                    ->modalHeading(__('messages.album_category.edit_album_category'))
                    ->successNotificationTitle(__('messages.placeholder.album_category_updated_successfully')),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.album_category.album_category'))
                    ->successNotificationTitle(__('messages.placeholder.album_category_deleted_successfully')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.album_category.album_categorys'))
                        ->successNotificationTitle(__('messages.placeholder.album_category_deleted_successfully')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAlbumCategories::route('/'),
        ];
    }
    // public static function getNavigationGroup(): string
    // {
    //     return __('messages.albums');
    // }

    public static function getNavigationLabel(): string
    {
        return __('messages.album_categories');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.album_categories');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_albums_category');
    }
}
