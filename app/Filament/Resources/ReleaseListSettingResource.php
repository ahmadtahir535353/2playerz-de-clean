<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReleaseListSettingResource\Pages;
use App\Filament\Resources\ReleaseListSettingResource\RelationManagers;
use App\Models\Comment;
use App\Models\ReleaseListSetting;
use App\Models\User;
use Filament\Forms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class ReleaseListSettingResource extends Resource
{
    protected static ?string $model = ReleaseListSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function getNavigationLabel(): string
    {
        return __('messages.release_list_settings.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('messages.release_list_settings.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.release_list_settings.plural_model_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.release_list_settings.list_configuration'))
                    ->schema([
                        Select::make('list_type')
                            ->label(__('messages.release_list_settings.list_type'))
                            ->options([
                                ReleaseListSetting::LIST_TYPE_ALL => __('messages.release_list_settings.all_platforms'),
                                ReleaseListSetting::LIST_TYPE_PLAYSTATION => __('messages.release_calendar.playstation'),
                                ReleaseListSetting::LIST_TYPE_XBOX => __('messages.release_calendar.xbox'),
                                ReleaseListSetting::LIST_TYPE_NINTENDO => __('messages.release_calendar.nintendo'),
                            ])
                            ->required()
                            ->rules([
                                fn(?ReleaseListSetting $record) => Rule::unique('release_list_settings', 'list_type')
                                    ->where('created_by', auth()->id())
                                    ->ignore($record?->id),
                            ])
                            ->disabled(fn(?ReleaseListSetting $record) => $record !== null)
                            ->helperText(__('messages.release_list_settings.list_type_helper')),
                    ]),
                
                Section::make(__('messages.release_list_settings.page_content'))
                    ->schema([
                        Select::make('created_by')
                            ->label(__('messages.common.created_by'))
                            ->options(function () {
                                $users = User::query()
                                    ->where(function ($q) {
                                        $q->where('type', User::STAFF)->where('is_editor', 1);
                                    })
                                    ->orWhere('type', User::ADMIN)
                                    ->orderBy('first_name')
                                    ->orderBy('last_name')
                                    ->get();
                                return $users->mapWithKeys(fn (User $user) => [$user->id => $user->full_name ?? trim($user->first_name . ' ' . $user->last_name) ?: $user->email]);
                            })
                            ->searchable()
                            ->default(fn () => auth()->id())
                            ->helperText('Dieser Nutzer wird als Autor der Release-Liste angezeigt. (Editor wie bei Beiträgen, zzgl. Admin.)'),
                        TextInput::make('headline')
                            ->label(__('messages.release_list_settings.page_headline'))
                            ->maxLength(255)
                            ->placeholder('e.g., Alle bisher bekannten Spiele-Releases'),
                        Textarea::make('short_description')
                            ->label(__('messages.release_list_settings.short_description'))
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Brief description for this release list page'),
                        TextInput::make('banner_title')
                            ->label(__('messages.release_list_settings.banner_title'))
                            ->maxLength(255)
                            ->placeholder(__('messages.release_list_settings.banner_title_placeholder'))
                            ->helperText(__('messages.release_list_settings.banner_title_helper')),
                        TextInput::make('date_not_fixed_label')
                            ->label(__('messages.release_list_settings.date_not_fixed_label'))
                            ->maxLength(255)
                            ->placeholder(__('messages.release_list_settings.date_not_fixed_label_placeholder'))
                            ->helperText(__('messages.release_list_settings.date_not_fixed_label_helper')),
                        Textarea::make('keywords')
                            ->label(__('messages.release_list_settings.keywords'))
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('e.g. Spiele-Releases 2026, PlayStation, Xbox, Nintendo')
                            ->helperText(__('messages.release_list_settings.keywords_helper')),
                        Textarea::make('wishlist_info')
                            ->label(__('messages.release_list_settings.wishlist_info'))
                            ->rows(4)
                            ->maxLength(2000)
                            ->placeholder(__('messages.release_list_settings.wishlist_info_placeholder'))
                            ->helperText(__('messages.release_list_settings.wishlist_info_helper')),
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('messages.release_list_settings.page_image'))
                            ->collection(ReleaseListSetting::IMAGE_COLLECTION)
                            ->image()
                            ->imageEditor()
                            ->disk(config('app.media_disk', 'public'))
                            ->helperText(__('messages.release_list_settings.page_image_helper')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('creator.full_name')
                    ->label(__('messages.common.created_by'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('list_type')
                    ->label(__('messages.release_list_settings.list_type'))
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        ReleaseListSetting::LIST_TYPE_ALL => __('messages.release_list_settings.all_platforms'),
                        ReleaseListSetting::LIST_TYPE_PLAYSTATION => __('messages.release_calendar.playstation'),
                        ReleaseListSetting::LIST_TYPE_XBOX => __('messages.release_calendar.xbox'),
                        ReleaseListSetting::LIST_TYPE_NINTENDO => __('messages.release_calendar.nintendo'),
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        ReleaseListSetting::LIST_TYPE_ALL => 'success',
                        ReleaseListSetting::LIST_TYPE_PLAYSTATION => 'info',
                        ReleaseListSetting::LIST_TYPE_XBOX => 'success',
                        ReleaseListSetting::LIST_TYPE_NINTENDO => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('headline')
                    ->label(__('messages.release_list_settings.headline'))
                    ->searchable()
                    ->limit(50),
                TextColumn::make('stats')
                    ->label(__('messages.details.views') . ' / ' . __('messages.comments') . ' / ' . __('messages.customer_profile.likes'))
                    ->getStateUsing(function (ReleaseListSetting $record): HtmlString {
                        $viewsCount = $record->views_count ?? 0;
                        $commentsCount = Comment::where('item_type', 'release_list')->where('item_id', $record->id)->where('status', 1)->count();
                        $likesCount = DB::table('likes')->where('item_type', 'release_list')->where('item_id', $record->id)->count();
                        $viewBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-md bg-primary-600 text-white">' . number_format($viewsCount) . ' ' . __('messages.details.views') . '</span>';
                        $commentsBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-md bg-primary-600 text-white">' . number_format($commentsCount) . ' ' . __('messages.comments') . '</span>';
                        $likesBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-md bg-primary-600 text-white">' . number_format($likesCount) . ' ' . __('messages.customer_profile.likes') . '</span>';
                        return new HtmlString($viewBadge . ' ' . $commentsBadge . ' ' . $likesBadge);
                    })
                    ->html()
                    ->wrap(),
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('messages.release_list_settings.image'))
                    ->collection(ReleaseListSetting::IMAGE_COLLECTION)
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('list_type', 'asc');
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
            'index' => Pages\ListReleaseListSettings::route('/'),
            'create' => Pages\CreateReleaseListSetting::route('/create'),
            'edit' => Pages\EditReleaseListSetting::route('/{record}/edit'),
        ];
    }
}
