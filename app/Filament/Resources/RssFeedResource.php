<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\RssFeedResource\Pages;
use App\Filament\Resources\RssFeedResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Category;
use App\Models\Post;
use App\Models\RssFeed;
use App\Models\SubCategory;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use App\Tables\Columns\AutoUpdate;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Vedmant\FeedReader\Facades\FeedReader;

class RssFeedResource extends Resource
{
    protected static ?string $model = RssFeed::class;

    protected static ?string $navigationIcon = 'heroicon-o-rss';

    protected static ?int $navigationSort = Sidebar::RSS_FEED->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('feed_name')
                            ->label(__('messages.rss_feed.feed_name') . ':')
                            ->validationAttribute(__('messages.rss_feed.feed_name'))
                            ->placeholder(__('messages.rss_feed.feed_name'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignorable: fn(?RssFeed $record) => $record),
                        Forms\Components\TextInput::make('feed_url')
                            ->label(__('messages.rss_feed.feed_url') . ':')
                            ->validationAttribute(__('messages.rss_feed.feed_url'))
                            ->placeholder(__('messages.rss_feed.feed_url'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignorable: fn(?RssFeed $record) => $record),
                        Forms\Components\TextInput::make('no_post')
                            ->label(__('messages.rss_feed.no_posts') . ':')
                            ->validationAttribute(__('messages.rss_feed.no_posts'))
                            ->placeholder(__('messages.rss_feed.no_posts'))
                            ->required()
                            ->numeric(),
                        Select::make('language_id')
                            ->label(__('messages.common.select_language') . ':')
                            ->validationAttribute(__('messages.common.select_language'))
                            ->placeholder(__('messages.common.select_language'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('language', 'name')
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('category_id', null);
                                $set('subcategory_id', null);
                            }),
                        Select::make('category_id')
                            ->options(function (Get $get) {
                                return Category::query()
                                    ->where('lang_id', $get('language_id'))
                                    ->pluck('name', 'id');
                            })
                            ->label(__('messages.common.select_category') . ':')
                            ->validationAttribute(__('messages.common.select_category'))
                            ->placeholder(__('messages.common.select_category'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('subcategory_id', null);
                            }),
                        Select::make('subcategory_id')
                            ->options(function (Get $get) {
                                return SubCategory::query()
                                    ->where('parent_category_id', $get('category_id'))
                                    ->pluck('name', 'id');
                            })
                            ->label(__('messages.common.select_subcategory') . ':')
                            ->validationAttribute(__('messages.common.select_subcategory'))
                            ->placeholder(__('messages.common.select_subcategory'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\TagsInput::make('tags')
                            ->required()
                            ->label(__('messages.post.tag') . ':')
                            ->validationAttribute(__('messages.post.tag'))
                            ->placeholder(__('messages.post.tag')),
                        Forms\Components\DatePicker::make('scheduled_delete_post_time')
                            ->label(__('messages.rss_feed.scheduled_post_delete') . ':')
                            ->validationAttribute(__('messages.rss_feed.scheduled_post_delete'))
                            ->placeholder(__('messages.rss_feed.scheduled_post_delete'))
                            ->minDate(function ($record) {
                                if ($record && $record->ends_at) {
                                    return Carbon::parse($record->ends_at)->format('Y-m-d');
                                }
                                return Carbon::now()->startOfDay()->format('Y-m-d');
                            }),
                        Forms\Components\Toggle::make('auto_update')
                            ->inline(false)
                            ->label(__('messages.rss_feed.auto_update') . ':')
                            ->validationAttribute(__('messages.rss_feed.auto_update'))
                            ->default(true)
                            ->required(),
                        Forms\Components\Toggle::make('show_btn')
                            ->inline(false)
                            ->label(__('messages.rss_feed.show_btn') . ':')
                            ->validationAttribute(__('messages.rss_feed.show_btn'))
                            ->default(true)
                            ->required(),
                        Forms\Components\Toggle::make('post_draft')
                            ->inline(false)
                            ->label(__('messages.rss_feed.add_posts') . ':')
                            ->validationAttribute(__('messages.rss_feed.add_posts'))
                            ->required(),
                        // Hidden::make('user_id')
                        //     ->default(function () {
                        //         return getLogInUserId();
                        //     })
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->recordUrl(false)
            ->columns([
                Tables\Columns\TextColumn::make('feed_name')
                    ->label(__('messages.rss_feed.feed_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('feed_url')
                    ->label(__('messages.rss_feed.feed_url'))
                    ->url(fn(RssFeed $record) => $record->feed_url)
                    ->openUrlInNewTab()
                    ->searchable()
                    ->color('primary')
                    ->limit(40),
                Tables\Columns\TextColumn::make('no_post')
                    ->label(__('messages.rss_feed.post_import'))
                    ->state(function (RssFeed $record) {
                        return $record->posts->count() . '/' . $record->no_post;
                    })
                    ->numeric()
                    ->sortable(['no_post']),
                Tables\Columns\TextColumn::make('language.name')
                    ->label(__('messages.languages'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('messages.post.category'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.full_name')->label(__('messages.common.created_by')),

                AutoUpdate::make('auto_update')
                    ->label(__('messages.rss_feed.auto_update'))
                    ->alignCenter(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.rss-feed'))
                    ->successNotificationTitle(__('messages.placeholder.rss_feed_update_successfully')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.rss_feed.rss_feeds'))
                        ->successNotificationTitle(__('messages.placeholder.rss_feed_update_successfully')),
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
            'index' => Pages\ListRssFeeds::route('/'),
            'create' => Pages\CreateRssFeed::route('/create'),
            'view' => Pages\ViewRssFeed::route('/{record}'),
            'edit' => Pages\EditRssFeed::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.rss-feed');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.rss-feed');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_rss_feeds');
    }
}
