<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Album;
use App\Enums\Sidebar;
use App\Models\Gallery;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AlbumCategory;
use App\Models\GalleryImages;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clusters\Album as FilamentAlbum;
use App\Http\Middleware\CheckPaddingSubscription;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GalleryImagesResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\GalleryImagesResource\RelationManagers;
use Filament\Forms\Components\Section;

class GalleryImagesResource extends Resource
{
    protected static ?string $model = Gallery::class;

    // protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $cluster = FilamentAlbum::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = Sidebar::GALLERY_IMAGES->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Section::make([
                    Select::make('lang_id')
                        ->label(__('messages.gallery.language') . ':')
                        ->validationAttribute(__('messages.gallery.language'))
                        ->placeholder(__('messages.gallery.language'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->afterStateUpdated(function (Set $set) {
                            $set('album_id', null);
                            $set('category', null);
                        })
                        ->relationship('language', 'name'),
                    Select::make('album_id')
                        ->label(__('messages.gallery.album') . ':')
                        ->validationAttribute(__('messages.gallery.album'))
                        ->placeholder(__('messages.gallery.album'))
                        ->options(function (Get $get) {
                            return Album::query()
                                ->where('lang_id', $get('lang_id'))
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->afterStateUpdated(function (Set $set) {
                            $set('category_id', null);
                        })
                        ->required(),
                    Select::make('category_id')
                        ->label(__('messages.gallery.category') . ':')
                        ->validationAttribute(__('messages.gallery.category'))
                        ->placeholder(__('messages.gallery.category'))
                        ->required()
                        ->options(function (Get $get) {
                            return AlbumCategory::where('lang_id', $get('lang_id'))->where('album_id', $get('album_id'))->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload(),
                    TextInput::make('title')
                        ->label(__('messages.gallery.title') . ':')
                        ->validationAttribute(__('messages.gallery.title'))
                        ->placeholder(__('messages.gallery.title'))
                        ->unique(ignorable: fn(?Gallery $record) => $record)
                        ->required()
                        ->maxLength(255),
                    SpatieMediaLibraryFileUpload::make('image')
                        ->label(__('messages.gallery.image') . ':')
                        ->validationAttribute(__('messages.gallery.image'))
                        ->multiple()
                        ->reorderable()
                        ->collection(Gallery::GALLERY_IMAGE),
                    Hidden::make('is_default')
                        ->default(false)
                        ->dehydrated(function ($state) {
                            if (Schema::hasColumn('galleries', 'is_default')) {
                                return true;
                            }
                        }),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->defaultSort('id', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('Image')
                    ->label(__('messages.post.image'))
                    ->circular()
                    ->collection(Gallery::GALLERY_IMAGE)
                    ->simpleLightbox()
                    ->defaultImageUrl(asset('front_web/images/default.jpg')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('messages.common.title'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('language.name')
                    ->label(__('messages.common.language'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('album.name')
                    ->label(__('messages.gallery.album'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('messages.post.category'))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.post.gallery'))
                    ->successNotificationTitle(__('messages.placeholder.gallery_image_deleted_successfully')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.post.gallerys'))
                        ->successNotificationTitle(__('messages.placeholder.gallery_image_deleted_successfully')),
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
            'index' => Pages\ListGalleryImages::route('/'),
            'create' => Pages\CreateGalleryImages::route('/create'),
            'view' => Pages\ViewGalleryImages::route('/{record}'),
            'edit' => Pages\EditGalleryImages::route('/{record}/edit'),
        ];
    }
    // public static function getNavigationGroup(): string
    // {
    //     return __('messages.albums');
    // }

    public static function getNavigationLabel(): string
    {
        return __('messages.images');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.images');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_gallery');
    }
}
