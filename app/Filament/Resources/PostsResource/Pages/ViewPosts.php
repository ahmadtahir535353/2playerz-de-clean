<?php

namespace App\Filament\Resources\PostsResource\Pages;

use App\Filament\Resources\PostsResource;
use App\Models\Emoji;
use App\Models\Post;
use App\Models\PostReactionEmoji;
use App\Scopes\AuthoriseUserActivePostScope;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\HtmlString;

class ViewPosts extends ViewRecord
{
    protected static string $resource = PostsResource::class;

    public function mount(int | string $record): void
    {
        $this->record = Post::withoutGlobalScope(AuthoriseUserActivePostScope::class)
            ->withoutGlobalScope(LanguageScope::class)
            ->withoutGlobalScope(PostDraftScope::class)
            ->find($record);

        $this->authorizeAccess();

        if (! $this->hasInfolist()) {
            $this->fillForm();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('title')->label(''),
                TextEntry::make('description')->label(''),
                Section::make('')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('visibility')
                            ->label(__('messages.post.visibility'))
                            ->formatStateUsing(function (Post $record, $state) {
                                $color = $state ? 'success' : 'danger';
                                return new HtmlString(sprintf('<span style="--c-600:var(--' . $color . '-600);" class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-custom-600 rounded">%s</span>', $state ? __('messages.common.on') :  __('messages.common.off')));
                            }),
                        TextEntry::make('status')
                            ->label(__('messages.status'))
                            ->formatStateUsing(function (Post $record, $state) {
                                $color = $state ? 'success' : 'danger';
                                return new HtmlString(sprintf('<span style="--c-600:var(--' . $color . '-600);" class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-custom-600 rounded">%s</span>', $state ? __('messages.post.publish') :  __('messages.post.draft_post')));
                            }),
                        TextEntry::make('category.name')->label(__('messages.post.category')),
                        TextEntry::make('subCategory.name')->label(__('messages.post.sub_category')),
                        TextEntry::make('user.full_name')->label(__('messages.common.created_by')),
                        TextEntry::make('language.name')->label(__('messages.common.language')),
                        TextEntry::make('is_rss')
                            ->label(__('messages.rss-feed'))
                            ->formatStateUsing(function (Post $record, $state) {
                                $color = $state ? 'success' : 'danger';
                                return new HtmlString(sprintf('<span style="--c-600:var(--' . $color . '-600);" class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-custom-600 rounded">%s</span>', $state ? __('messages.page.yes') : __('messages.page.no')));
                            }),
                        TextEntry::make('featured')
                            ->label(__('messages.post.featured'))
                            ->formatStateUsing(function (Post $record, $state) {
                                $color = $state ? 'success' : 'danger';
                                return new HtmlString(sprintf('<span style="--c-600:var(--' . $color . '-600);" class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-custom-600 rounded">%s</span>', $state ? __('messages.page.yes') : __('messages.page.no')));
                            }),
                        TextEntry::make('breaking')
                            ->label(__('messages.details.breaking'))
                            ->formatStateUsing(function (Post $record, $state) {
                                $color = $state ? 'success' : 'danger';
                                return new HtmlString(sprintf('<span style="--c-600:var(--' . $color . '-600);" class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-custom-600 rounded">%s</span>', $state ? __('messages.page.yes') : __('messages.page.no')));
                            }),
                        TextEntry::make('slider')
                            ->label(__('messages.post.add_to_slider'))
                            ->formatStateUsing(function (Post $record, $state) {
                                $color = $state ? 'success' : 'danger';
                                return new HtmlString(sprintf('<span style="--c-600:var(--' . $color . '-600);" class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-custom-600 rounded">%s</span>', $state ? __('messages.page.yes') : __('messages.page.no')));
                            }),
                        TextEntry::make('recommended')
                            ->label(__('messages.details.recommended_post'))
                            ->formatStateUsing(function (Post $record, $state) {
                                $color = $state ? 'success' : 'danger';
                                return new HtmlString(sprintf('<span style="--c-600:var(--' . $color . '-600);" class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-custom-600 rounded">%s</span>', $state ? __('messages.page.yes') : __('messages.page.no')));
                            }),
                        TextEntry::make('show_registered_user')
                            ->label(__('messages.post.show_registered_user'))
                            ->formatStateUsing(function (Post $record, $state) {
                                $color = $state ? 'success' : 'danger';
                                return new HtmlString(sprintf('<span style="--c-600:var(--' . $color . '-600);" class="inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-custom-600 rounded">%s</span>', $state ? __('messages.page.yes') : __('messages.page.no')));
                            }),
                        TextEntry::make('keywords')
                            ->label(__('messages.post.keywords'))
                            ->formatStateUsing(function (Post $record) {
                                return str_replace(' ', ', ', $record->keywords);
                            }),
                        TextEntry::make('tags')
                            ->label(__('messages.post.tag'))
                            ->badge()
                            ->color('info'),
                        TextEntry::make('scheduled_post_time')
                            ->label(__('messages.post.publish') . ' ' . __('messages.post.scheduled_post'))
                            ->formatStateUsing(function (Post $record) {
                                return $record->scheduled_post_time ? $record->scheduled_post_time->format('d/m/Y') : __('messages.menu.n_a');
                            })
                            ->default(__('messages.menu.n_a')),
                        TextEntry::make('scheduled_post_time')
                            ->label(__('messages.delete') . ' ' . __('messages.post.scheduled_post'))
                            ->formatStateUsing(function (Post $record) {
                                return $record->scheduled_delete_post_time ? $record->scheduled_delete_post_time->format('d/m/Y') : __('messages.menu.n_a');
                            })
                            ->default(__('messages.menu.n_a')),
                        TextEntry::make('optional_url')
                            ->label(__('messages.post.optional_url'))
                            ->default(__('messages.menu.n_a')),
                    ]),
                Section::make(__('messages.post_reaction'))
                    ->schema([
                        TextEntry::make('id')
                            ->label('')
                            ->formatStateUsing(function (Post $record) {
                                $countEmoji = PostReactionEmoji::wherePostId($record->id)
                                    ->get()
                                    ->groupBy('emoji_id');
                                $emojis = Emoji::whereStatus(Emoji::ACTIVE)->get();

                                $output = '<div style="display: flex;flex-wrap: wrap;justify-content: left;gap: 20px;">';

                                foreach ($emojis as $emoji) {
                                    $count = isset($countEmoji[$emoji->id]) ? count($countEmoji[$emoji->id]) : 0;

                                    $output .= '
                                        <div style="text-align: center; position: relative;margin-right: 30px; margin-top: 30px;">
                                            <div style="font-size: 3rem;">' . html_entity_decode($emoji->emoji) . '</div>
                                            <div style="position: absolute; top: -25px; right: -35px; color: white; border-radius: 12px; padding: 0px; font-size: 0.9rem; min-width: 35px; text-align: center;" class="bg-gray-400 dark:bg-gray-600 font-bold">
                                                ' . $count . '
                                            </div>
                                            <div style="margin-top: 20px; font-size: 0.9rem;">' .  __('messages.reaction.' . $emoji->name) . '</div>
                                        </div>';
                                }

                                $output .= '</div>';

                                return $output;
                            })
                            ->html(),
                    ])
            ])->columns(1);
    }
}
