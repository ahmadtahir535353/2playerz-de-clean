<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\PostsResource\Pages;
use App\Filament\Resources\PostsResource\Pages\CreatePosts;
use App\Filament\Resources\PostsResource\Pages\EditPosts;
use App\Filament\Resources\PostsResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Category;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\PostAudio;
use App\Models\PostGallery;
use App\Models\Posts;
use App\Models\PostVideo;
use App\Models\LivetickerContent;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Models\User;
use App\Scopes\AuthoriseUserActivePostScope;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use App\Tables\Columns\PostImage;
use Aws\Api\Operation;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class PostsResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?int $navigationSort = Sidebar::POSTS->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Post::getForm())->columns(3);
    }

    public static function table(Table $table): Table
    {
        if (Auth::user()->hasRole('customer')) {
            $table = $table->modifyQueryUsing(function ($query) {
                // Removed withCount('analytics') - now using views_count column for better performance
                $query->withoutGlobalScope(AuthoriseUserActivePostScope::class)->withoutGlobalScope(LanguageScope::class)
                    ->withoutGlobalScope(PostDraftScope::class)->with(
                        'language:id,name',
                        'category:id,name'
                    )->whereCreatedBy(getLogInUserId());
                
            });
        } else {
            $table = $table->modifyQueryUsing(function ($query) {
                // Removed withCount('analytics') - now using views_count column for better performance
                $query->withoutGlobalScope(AuthoriseUserActivePostScope::class)->withoutGlobalScope(LanguageScope::class)
                    ->withoutGlobalScope(PostDraftScope::class)->with('language:id,name', 'category:id,name');
                
            });
        }

        return $table
            ->paginated([10, 25, 50])
            ->persistSortInSession()
            ->defaultSort('id', 'desc')
            ->recordUrl(false)
            ->columns([
                PostImage::make(__('messages.post.post') . __('messages.post.image')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('messages.common.title'))
                    ->description(function (Post $record) {
                        $typeBadge = sprintf('<span class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-primary-600 rounded">%s</span>', $record->type_name);
                        $categoryBadge = sprintf('<span class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-primary-600 rounded">%s</span>', $record->category->name ?? '');
                        $languageBadge = sprintf('<span class="inline-flex items-center my-1 mx-1 px-2 py-1 text-xs font-semibold text-white bg-primary-600 rounded">
                                <svg style="margin: 2px;width: 12px;height: 12px;float: left;" class="w-4 h-4 inline mr-1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="language" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                    <path fill="currentColor" d="M448 164C459 164 468 172.1 468 184V188H528C539 188 548 196.1 548 208C548 219 539 228 528 228H526L524.4 232.5C515.5 256.1 501.9 279.1 484.7 297.9C485.6 298.4 486.5 298.1 487.4 299.5L506.3 310.8C515.8 316.5 518.8 328.8 513.1 338.3C507.5 347.8 495.2 350.8 485.7 345.1L466.8 333.8C462.4 331.1 457.1 328.3 453.7 325.3C443.2 332.8 431.8 339.3 419.8 344.7L416.1 346.3C406 350.8 394.2 346.2 389.7 336.1C385.2 326 389.8 314.2 399.9 309.7L403.5 308.1C409.9 305.2 416.1 301.1 422 298.3L409.9 286.1C402 278.3 402 265.7 409.9 257.9C417.7 250 430.3 250 438.1 257.9L452.7 272.4L453.3 272.1C465.7 259.9 475.8 244.7 483.1 227.1H376C364.1 227.1 356 219 356 207.1C356 196.1 364.1 187.1 376 187.1H428V183.1C428 172.1 436.1 163.1 448 163.1L448 164zM160 233.2L179 276H140.1L160 233.2zM0 128C0 92.65 28.65 64 64 64H576C611.3 64 640 92.65 640 128V384C640 419.3 611.3 448 576 448H64C28.65 448 0 419.3 0 384V128zM320 384H576V128H320V384zM178.3 175.9C175.1 168.7 167.9 164 160 164C152.1 164 144.9 168.7 141.7 175.9L77.72 319.9C73.24 329.1 77.78 341.8 87.88 346.3C97.97 350.8 109.8 346.2 114.3 336.1L123.2 315.1H196.8L205.7 336.1C210.2 346.2 222 350.8 232.1 346.3C242.2 341.8 246.8 329.1 242.3 319.9L178.3 175.9z"></path>
                                </svg>%s</span>', $record->language->name ?? '');
                        $viewCountBadge = sprintf('<span class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-primary-600 rounded">
                                                    <svg style="margin: 2px;width: 12px;height: 12px;float: left;" class="svg-inline--fa fa-eye fs-12 text-gray me-1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="eye" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg="">
                                                        <path fill="currentColor" d="M279.6 160.4C282.4 160.1 285.2 160 288 160C341 160 384 202.1 384 256C384 309 341 352 288 352C234.1 352 192 309 192 256C192 253.2 192.1 250.4 192.4 247.6C201.7 252.1 212.5 256 224 256C259.3 256 288 227.3 288 192C288 180.5 284.1 169.7 279.6 160.4zM480.6 112.6C527.4 156 558.7 207.1 573.5 243.7C576.8 251.6 576.8 260.4 573.5 268.3C558.7 304 527.4 355.1 480.6 399.4C433.5 443.2 368.8 480 288 480C207.2 480 142.5 443.2 95.42 399.4C48.62 355.1 17.34 304 2.461 268.3C-.8205 260.4-.8205 251.6 2.461 243.7C17.34 207.1 48.62 156 95.42 112.6C142.5 68.84 207.2 32 288 32C368.8 32 433.5 68.84 480.6 112.6V112.6zM288 112C208.5 112 144 176.5 144 256C144 335.5 208.5 400 288 400C367.5 400 432 335.5 432 256C432 176.5 367.5 112 288 112z"></path>
                                                    </svg>%s</span>', number_format($record->views_count ?? 0));
                        $commentsBadge = sprintf('<span class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-primary-600 rounded">
                                    <svg style="margin: 2px;width: 12px;height: 12px;float: left;" class="svg-inline--fa fa-comments fs-12 text-gray me-1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="comments" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                        <path fill="currentColor" d="M256 32C114.6 32 0 125.1 0 240c0 47.6 19.9 91.2 52.9 126.3C38 405.7 7 439.1 6.5 439.5c-6.6 7-8.4 17.2-4.6 26S14.4 480 24 480c61.5 0 110-25.7 139.1-46.3C192 442.8 223.2 448 256 448c141.4 0 256-93.1 256-208S397.4 32 256 32zm0 368c-26.7 0-53.1-4.1-78.4-12.1l-22.7-7.2-19.5 13.8c-14.3 10.1-33.9 21.4-57.5 29 7.3-12.1 14.4-25.7 19.9-40.2l10.6-28.1-20.6-21.8C69.7 314.1 48 282.2 48 240c0-88.2 93.3-160 208-160s208 71.8 208 160-93.3 160-208 160z"></path>
                                    </svg>%s</span>', $record->comments()->count());
                        $likesBadge = sprintf('<span class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-primary-600 rounded">
                                    <svg class="svg-inline--fa fa-thumbs-up fs-12 text-gray me-1" style="margin: 2px;width: 12px;height: 12px;float: left; color: #ffffff;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="thumbs-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M128 447.1V223.1c0-17.67-14.33-31.1-32-31.1H32c-17.67 0-32 14.33-32 31.1v223.1c0 17.67 14.33 31.1 32 31.1h64C113.7 479.1 128 465.6 128 447.1zM512 224.1c0-26.5-21.48-47.98-48-47.98h-146.5c22.77-37.91 34.52-80.88 34.52-96.02C352 56.52 333.5 32 302.5 32c-63.13 0-26.36 76.15-108.2 141.6L178 186.6C166.2 196.1 160.2 210 160.1 224c-.0234 .0234 0 0 0 0L160 384c0 15.1 7.113 29.33 19.2 38.39l34.14 25.59C241 468.8 274.7 480 309.3 480H368c26.52 0 48-21.47 48-47.98c0-3.635-.4805-7.143-1.246-10.55C434 415.2 448 397.4 448 376c0-9.148-2.697-17.61-7.139-24.88C463.1 347 480 327.5 480 304.1c0-12.5-4.893-23.78-12.72-32.32C492.2 270.1 512 249.5 512 224.1z"></path></svg>%s</span>', $record->likes()->count());                       
                        $draft_post = sprintf('<span class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-primary-600 rounded">%s</span>', __('messages.post.draft_post'));
                        $html = $typeBadge . $categoryBadge . $languageBadge . $viewCountBadge . $commentsBadge . $likesBadge . ($record->status == 0 ? $draft_post : '');
                        return new HtmlString($html);
                    })
                    ->width('100%')
                    ->wrap()
                    ->searchable()
                    ->sortable()
                    // ->url(fn (Post $record) => route('detailPage', $record->slug),true),
                    ->formatStateUsing(function (Post $record) {
                        $url = route('detailPage', $record->slug);
                        $style = '' .
                            ($record->status != 0 && $record->visibility != 0 ? '' : 'pointer-events: none !important') . ' ';

                        return '<a href="' . $url . '" style="' . $style . '" target="_blank">' . $record->title . '</a>';
                    })
                    ->html(),
                ToggleColumn::make('show_on_headline')
                    ->label(__('messages.post.show_on_headline'))
                    ->sortable()
                    ->afterStateUpdated(function ($state) {
                        Notification::make()
                            ->success()
                            ->title($state ? __('messages.placeholder.post_added_on_headline_successfully') : __('messages.placeholder.post_removed_from_headline_successfully'))
                            ->duration(2000)
                            ->send();
                    }),
                ToggleColumn::make('visibility')
                    ->label(__('messages.post.visibility'))
                    ->sortable()
                    ->afterStateUpdated(function ($state, $record) {
                        // Update user's last_seen_at and last_activity_at when visibility is toggled
                        if ($state && $record->status == 1) { // If visibility is ON and post is ACTIVE
                            $user = \App\Models\User::find($record->created_by);
                            if ($user) {
                                $user->update([
                                    'last_seen_at' => now('Europe/Berlin'),
                                    'last_activity_at' => now('Europe/Berlin')
                                ]);
                            }
                        }
                        
                        Notification::make()
                            ->success()
                            ->title($state ? __('messages.placeholder.post_added_to_visibility_successfully') : __('messages.placeholder.post_removed_from_visibility_successfully'))
                            ->duration(2000)
                            ->send();
                    }),
                ToggleColumn::make('featured')
                    ->label(__('messages.post.featured'))
                    ->sortable()
                    ->afterStateUpdated(function ($state) {
                        Notification::make()
                            ->success()
                            ->title($state ? __('messages.placeholder.post_added_to_featured_successfully') : __('messages.placeholder.post_removed_from_featured_successfully'))
                            ->duration(2000)
                            ->send();
                    }),
                ToggleColumn::make('breaking')
                    ->label(__('messages.post.add_to_breaking'))
                    ->sortable()
                    ->afterStateUpdated(function ($state) {
                        Notification::make()
                            ->success()
                            ->title($state ? __('messages.placeholder.post_added_to_breaking_successfully') : __('messages.placeholder.post_removed_from_breaking_successfully'))
                            ->duration(2000)
                            ->send();
                    }),
                ToggleColumn::make('slider')
                    ->label(__('messages.post.add_to_slider'))
                    ->sortable()
                    ->afterStateUpdated(function ($state) {
                        Notification::make()
                            ->success()
                            ->title($state ? __('messages.placeholder.post_added_to_slider_successfully') : __('messages.placeholder.post_removed_from_slider_successfully'))
                            ->duration(2000)
                            ->send();
                    }),
                ToggleColumn::make('recommended')
                    ->label(__('messages.post.add_to_recommended'))
                    ->sortable()
                    ->afterStateUpdated(function ($state) {
                        Notification::make()
                            ->success()
                            ->title($state ? __('messages.placeholder.post_added_to_recommended_successfully') : __('messages.placeholder.post_removed_from_recommended_successfully'))
                            ->duration(2000)
                            ->send();
                    }),
                TextColumn::make('created_at')
                    ->label(__('messages.common.created_at'))
                    ->sortable()
                    ->wrap()
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                SelectFilter::make('post_types')
                    ->label(__('messages.post.select_post_type'))
                    ->native(false)
                    ->options(Post::TYPE),
                SelectFilter::make('category')
                    ->label(__('messages.common.select_category'))
                    ->native(false)
                    ->relationship('category', 'name'),
                SelectFilter::make('sub_category')
                    ->label(__('messages.common.select_subcategory'))
                    ->native(false)
                    ->relationship('subCategory', 'name'),
                SelectFilter::make('language')
                    ->label(__('messages.common.select_language'))
                    ->native(false)
                    ->relationship('language', 'name'),
                SelectFilter::make('status')
                    ->label(__('messages.post.posts'))
                    ->native(false)
                    ->options(Post::POST),
                SelectFilter::make('views_count')
                    ->label(__('messages.details.views'))
                    ->native(false)
                    ->placeholder(__('messages.details.select_views'))
                    ->options(Post::VIEWS)
                    ->query(fn(Builder $query) => $query)
                    ->modifyBaseQueryUsing(function (Builder $query, array $data): Builder {
                        if ($data['value'] == 'asc' || $data['value'] == 'desc') {
                            return $query->orderBy('views_count', $data['value']);
                        }
                        return $query;
                    }),
                
            ])->defaultSort('id', 'desc')
                ->actions([
                    Tables\Actions\ViewAction::make()
                            ->iconButton()
                            ->label('Preview')
                            ->url(fn ($record) =>
                                URL::temporarySignedRoute(
                                    'admin.posts.preview',
                                    now()->addHours(2),           // valid for 2 hours
                                    ['slug' => $record->slug]
                                )
                            )
                            ->openUrlInNewTab(),
                    Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->url(function ($record) {
                        if ($record->post_types == 1) {
                            $type = 'article';
                        } elseif ($record->post_types == 2) {
                            $type = 'gallery';
                        } elseif ($record->post_types == 3) {
                            $type = 'sort_list';
                        } elseif ($record->post_types == 6) {
                            $type = 'video';
                        } elseif ($record->post_types == 7) {
                            $type = 'audio';
                        } elseif ($record->post_types == 8) {
                            $type = 'open_ai';
                        } elseif ($record->post_types == 9) { // 👈 apna naya type
                            $type = 'liveticker';
                        }
                        return PostsResource::getUrl('edit', ['record' => $record->id]) . '?section=' . $type;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.post.post'))
                    ->modalDescription(__('messages.common.are_you_sure'))
                    ->modalCancelActionLabel(__('messages.common.cancel'))
                    ->modalSubmitActionLabel(__('messages.common.confirm'))
                    ->successNotificationTitle(__('messages.placeholder.post_deleted_successfully')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.post.posts'))
                        ->modalDescription(__('messages.common.are_you_sure'))
                        ->modalCancelActionLabel(__('messages.common.cancel'))
                        ->modalSubmitActionLabel(__('messages.common.confirm'))
                        ->successNotificationTitle(__('messages.placeholder.post_deleted_successfully')),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePosts::route('/create'),
            'format' => Pages\PostFormat::route('/format'),
            // 'view' => Pages\ViewPosts::route('/{record}'),
            // 'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPosts::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.post.posts');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.post.posts');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_all_post') || Auth::user()->hasPermissionTo('manage_add_post');
    }

    public static function canView(Model $record): bool
    {
        return Auth::user()->hasPermissionTo('manage_all_post');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->hasPermissionTo('manage_all_post');
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->hasPermissionTo('manage_all_post');
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasPermissionTo('manage_add_post');
    }

}
