<?php

namespace App\Filament\Resources\RssFeedResource\Pages;

use App\Filament\Resources\RssFeedResource;
use App\Models\Post;
use App\Models\RssFeed;
use Illuminate\Support\Str;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;
use Vedmant\FeedReader\Facades\FeedReader;

class ListRssFeeds extends ListRecords
{
    protected static string $resource = RssFeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('messages.rss_feed.add_rss_feed')),
        ];
    }

    public function openNewUserModal(RssFeed $record)
    {
        //     $rss = RssFeed::whereId($record['id'])->first();
        //     $feed = FeedReader::read($record['feed_url']);
        //     $postNo = 1;

        //     if($feed->get_items()){
        //         foreach ($feed->get_items() as $postData) {
        //             if ($postNo > $rss->no_post) {
        //                 break;
        //             }

        //             $data = [
        //                 'title' => $postData->get_title(),
        //                 'description' => $postData->get_description(),
        //                 'link' => $postData->get_link(),
        //                 'enclosure' => $postData->get_enclosure()->get_link(),
        //                 'source' => $postData->get_source(),
        //                 'slug' => Str::slug($postData->get_title()),
        //             ];

        //             $post = Post::withoutGlobalScope(LanguageScope::class)
        //                 ->withoutGlobalScope(PostDraftScope::class)
        //                 ->whereSlug($data['slug'])
        //                 ->first();

        //             if ($post) {
        //                 $post->update([
        //                     'title' => $data['title'],
        //                     'slug' => $data['slug'],
        //                     'description' => $data['description'] ?? $data['title'],
        //                     'keywords' => $data['title'],
        //                     'rss_link' => $data['link'],
        //                     'lang_id' => $rss->language_id,
        //                     'category_id' => $rss->category_id,
        //                     'sub_category_id' => $rss->subcategory_id,
        //                     'scheduled_post_delete' => $rss->scheduled_delete_post_time ? 1 : 0,
        //                     'scheduled_delete_post_time' => $rss->scheduled_delete_post_time,
        //                     'tags' => $rss->tags ?? '',
        //                     'created_by' => $rss->user_id,
        //                 ]);
        //             } else {
        //                 $post = Post::create([
        //                     'title' => $data['title'],
        //                     'slug' => $data['slug'],
        //                     'description' => $data['description'] ?? $data['title'],
        //                     'keywords' => $data['title'],
        //                     'visibility' => !$rss->post_draft ? 1 : 0,
        //                     'featured' => 0,
        //                     'breaking' => 0,
        //                     'slider' => 0,
        //                     'recommended' => 0,
        //                     'show_registered_user' => 0,
        //                     'show_on_headline' => 0,
        //                     'post_types' => Post::ARTICLE_TYPE_ACTIVE,
        //                     'lang_id' => $rss->language_id,
        //                     'category_id' => $rss->category_id,
        //                     'sub_category_id' => $rss->subcategory_id,
        //                     'status' => !$rss->post_draft ? 1 : 0,
        //                     'created_by' => $rss->user_id,
        //                     'rss_link' => $data['link'],
        //                     'is_rss' => true,
        //                     'scheduled_post_delete' => $rss->scheduled_delete_post_time ? 1 : 0,
        //                     'scheduled_delete_post_time' => $rss->scheduled_delete_post_time,
        //                     'tags' => $rss->tags ?? '',
        //                     'rss_id' => $rss->id,
        //                 ]);

        //                 try {
        //                     if (!empty($data['enclosure'])) {
        //                         $post->addMediaFromUrl($data['enclosure'])->toMediaCollection(Post::IMAGE_POST, config('app.media_disc'));
        //                     }
        //                 } catch (\Exception $e) {
        //                     Log::error($e->getMessage());
        //                 }
        //             }

        //             $postNo++;
        //         }
        //         Notification::make()
        //             ->title(__('messages.placeholder.feed_updated_successfully'))
        //             ->success()
        //             ->send();
        //     }else{
        //         Notification::make()
        //             ->title(__('messages.record_not_found'))
        //             ->danger()
        //             ->send();
        //     }
        // }

        $rss = RssFeed::whereId($record['id'])->first();
        $feed = FeedReader::read($record['feed_url']);
        $postNo = 1;

        if ($feed->get_items()) {
            foreach ($feed->get_items() as $postData) {
                if ($postNo > $rss->no_post) {
                    break;
                }
                $data = [];
                $data['title'] = $postData->get_title();
                $data['description'] = $postData->get_title();
                $data['link'] = $postData->get_link();
                $data['enclosure'] = $postData->get_enclosure()->link;
                $data['source'] = $postData->get_source();
                $data['slug'] = make_slug($data['title']);
                $post = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->whereSlug($data['slug'])->first();
                if (!empty($post)) {
                    $post->update([
                        'title' => $data['title'],
                        'slug' => $data['slug'],
                        'description' => isset($data['description']) ? $data['description'] : $data['title'],
                        'keywords' => $data['title'],
                        'rss_link' => $data['link'],
                        'lang_id' => $rss->language_id,
                        'category_id' => $rss->category_id,
                        'sub_category_id' => $rss->subcategory_id,
                        // 'status' => !$rss->post_draft,
                        // 'visibility' => !$rss->post_draft,
                        'scheduled_post_delete' => isset($rss->scheduled_delete_post_time) ? 1 : 0,
                        'scheduled_delete_post_time' => $rss->scheduled_delete_post_time ?? null,
                        'tags' => $rss->tags,
                    ]);
                } else {
                    $post = Post::create([
                        'title' => $data['title'],
                        'slug' => $data['slug'],
                        'description' => isset($data['description']) ? $data['description'] : $data['title'],
                        'keywords' => $data['title'],
                        'visibility' => $rss->post_draft == 0 ? 1 : 0,
                        'featured' => 0,
                        'breaking' => 0,
                        'slider' => 0,
                        'recommended' => 0,
                        'show_registered_user' => 0,
                        'show_on_headline' => 0,
                        'post_types' => Post::ARTICLE_TYPE_ACTIVE,
                        'lang_id' => $rss->language_id,
                        'category_id' => $rss->category_id,
                        'sub_category_id' => $rss->subcategory_id,
                        'status' => $rss->post_draft == 0 ? 1 : 0,
                        'created_by' => $rss->user_id,
                        'rss_link' => $data['link'],
                        'is_rss' => true,
                        'scheduled_post_delete' => isset($rss->scheduled_delete_post_time) ? 1 : 0,
                        'scheduled_delete_post_time' => $rss->scheduled_delete_post_time ?? null,
                        'tags' => $rss->tags,
                        'rss_id' => $rss->id,
                    ]);
                    try {
                        if (isset($data['enclosure']) && !empty($data['enclosure'])) {
                            $enclosureUrl = $data['enclosure'];
                            $path = parse_url($enclosureUrl, PHP_URL_PATH);
                            $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));

                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                                $post->addMediaFromUrl($enclosureUrl)->toMediaCollection(
                                    Post::IMAGE_POST,
                                    config('app.media_disc')
                                );
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
                $postNo++;
            }
            Notification::make()
                ->title(__('messages.placeholder.feed_updated_successfully'))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('messages.record_not_found'))
                ->danger()
                ->send();
        }
    }
}
