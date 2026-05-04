<?php

namespace App\Filament\Resources\RssFeedResource\Pages;

use App\Filament\Resources\RssFeedResource;
use App\Models\Post;
use App\Models\PostArticle;
use App\Models\RssFeed;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Vedmant\FeedReader\Facades\FeedReader;

class CreateRssFeed extends CreateRecord
{
    protected static string $resource = RssFeedResource::class;

    protected static bool $canCreateAnother = false;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.placeholder.rss_feed_create_successfully');
    }

    public function getTitle(): string
    {
        return __('messages.common.add') . ' ' . __('messages.rss-feed');
    }
    public function mutateFormDataBeforeFill(array $data): array
    {

        $data['status'] = Post::STATUS_DRAFT == $data['status'] ? 1 : 0;
        $data['tags'] = explode(",", $data['tags']);

        return $data;
    }

    protected function handleRecordCreation(array $input): Model
    {
        $input['tags'] = implode(",", $input['tags']);
        $rssFeed = RssFeed::create($input);
        $rssFeed->update([
            'user_id' => getLogInUserId(),
        ]);

        $feed = FeedReader::read($input['feed_url']);
        $postNo = 1;
        foreach ($feed->get_items() as $postData) {
            if ($postNo > $input['no_post']) {
                break;
            }
            $data = [];
            $data['title'] = $postData->get_title();
            $data['article_content'] = $postData->get_content();
            $data['link'] = $postData->get_link();
            $data['enclosure'] = $postData->get_enclosure()->link;
            $data['source'] = $postData->get_source();
            $data['slug'] = make_slug($data['title']);

            $post = Post::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['title'],
                'keywords' => $data['title'],
                'visibility' => 0, // if post is draft visibility off
                'featured' => 0,
                'breaking' => 0,
                'slider' => 0,
                'recommended' => 0,
                'show_on_headline' => 0,
                'show_registered_user' => 0,
                'optional_url' => '',
                'post_types' => Post::ARTICLE_TYPE_ACTIVE,
                'lang_id' => $input['language_id'],
                'category_id' => $input['category_id'],
                'sub_category_id' => $input['subcategory_id'],
                'status' => 0,
                'created_by' => getLogInUserId(),
                'rss_link' => $data['link'],
                'is_rss' => true,
                'rss_id' => $rssFeed->id,
                'scheduled_post_delete' => isset($input['scheduled_delete_post_time']) ? 1 : 0,
                'scheduled_delete_post_time' => $input['scheduled_delete_post_time'] ?? null,
                'tags' => $input['tags'],
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
            $articleInputArray = Arr::only($data, ['article_content']);
            $article = new PostArticle($articleInputArray);
            $post->postArticle()->save($article);

            $postNo++;
        }
        return $rssFeed;
    }
}
