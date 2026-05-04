<?php

namespace App\Filament\Resources\PostsResource\Pages;

use App\Filament\Resources\PostsResource;
use App\Models\Plan;
use App\Models\Post;
use App\Models\PostArticle;
use App\Models\PostAudio;
use App\Models\PostGallery;
use App\Models\PostSortList;
use App\Models\PostVideo;
use App\Models\LivetickerContent;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CreatePosts extends CreateRecord
{
    protected static string $resource = PostsResource::class;

    protected static bool $canCreateAnother = false;

    public static int $tab = 0;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.placeholder.post_created_successfully');
    }

    public function getTitle(): string
    {
        return __('messages.common.add') . ' ' . __('messages.post.post');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url($this->getResource()::getUrl('format')),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // unset($data['liveticker_header'], $data['liveticker_footer']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getState();

        $this->record->livetickerContent()->updateOrCreate(
            ['post_id' => $this->record->id],
            [
                'header' => $data['liveticker_header'] ?? null,
                'footer' => $data['liveticker_footer'] ?? null,
            ]
        );
    }


    protected function beforeFill()
    {
        if (Auth::user()->hasRole('customer')) {

            $count = Post::whereCreatedBy(getLogInUserId())->count();
            $invoiceLimit = currentActiveSubscription()->no_of_post;
            $frequency = currentActiveSubscription()->plan_frequency;
            // dd($invoiceLimit, $count);
            if ($invoiceLimit <= $count && ($frequency != Plan::UNLIMITED)) {
                Notification::make()
                    ->danger()
                    ->title(__('messages.placeholder.your_plan_is_expired_Please_choose_a_plan_to_continue_the_services'))
                    ->send();

                return redirect()->route('filament.customer.resources.posts.index');
            }
        }
    }


    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by'] = (!empty($data['created_by'])) ? $data['created_by'] : getLogInUserId();
        $data['tags'] = !empty($data['tags']) && is_array($data['tags']) ? implode(",", $data['tags']) : null;
        if (isset($data['scheduled_post']) && $data['scheduled_post']) {
            $data['status'] = Post::STATUS_DRAFT;
            $data['visibility'] = Post::VISIBILITY_DEACTIVE;
        } else {
            if (isset($data['status']) && $data['status']) {
                $data['status'] = Post::STATUS_DRAFT;
                $data['visibility'] = Post::VISIBILITY_DEACTIVE;
            } else {
                $data['status'] = Post::STATUS_ACTIVE;
            }
        }
        if (Schema::hasColumn('posts', 'is_default')) {
            $data['is_default'] = false;
        }

        if (!isset($data['scheduled_post_delete'])) {
            $data['scheduled_delete_post_time'] = null;
            $data['scheduled_post_delete'] = 0;
        }
        $postVisibilityCount = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->whereCreatedBy(getLogInUserId())->whereVisibility(1)->count();

        $data['featured'] = (isset($data['featured']) && $data['featured']) ? Post::FEATURED_ACTIVE : Post::FEATURED_DEACTIVE;
        if (!isset($data['status'])) {
            if (Auth::user()->hasRole('customer')) {
                $data['visibility'] = (isset($data['visibility']) && $data['visibility']) ? (($postVisibilityCount < getloginuserplan()->no_of_post) ? Post::VISIBILITY_ACTIVE : Post::VISIBILITY_DEACTIVE) : Post::VISIBILITY_DEACTIVE;
            }
            if (!Auth::user()->hasRole('customer')) {
                $data['visibility'] = (isset($data['visibility']) && $data['visibility']) ? Post::VISIBILITY_ACTIVE : Post::VISIBILITY_DEACTIVE;
            }
        }

        if ($data['status'] == Post::STATUS_DRAFT) {
            $data['visibility'] = (isset($data['visibility']) == Post::VISIBILITY_DEACTIVE);
        }

        $data['breaking'] = (isset($data['breaking']) && $data['breaking']) ? Post::BREAKING_ACTIVE : Post::BREAKING_DEACTIVE;

        $data['slider'] = (isset($data['slider']) && $data['slider']) ? Post::SLIDER_ACTIVE : Post::SLIDER_DEACTIVE;

        $data['recommended'] = (isset($data['recommended']) && $data['recommended']) ? Post::RECOMMENDED_ACTIVE : Post::RECOMMENDED_DEACTIVE;

        $data['show_registered_user'] = (isset($data['show_registered_user']) && $data['show_registered_user']) ? Post::SHOW_REGISTRED_USER_ACTIVE : Post::SHOW_REGISTRED_USER_DEACTIVE;

        $data['show_on_headline'] = (isset($data['show_on_headline']) && $data['show_on_headline']) ? Post::HEADLINE_ACTIVE : Post::HEADLINE_DEACTIVE;


        $post = static::getModel()::create($data);

        // Update user's last_seen_at and last_activity_at when post is published
        if ($data['status'] == Post::STATUS_ACTIVE && $data['visibility'] == Post::VISIBILITY_ACTIVE) {
            $user = \App\Models\User::find($data['created_by']);
            if ($user) {
                $user->update([
                    'last_seen_at' => now('Europe/Berlin'),
                    'last_activity_at' => now('Europe/Berlin')
                ]);
            }
        }

        if ($data['post_types'] == Post::ARTICLE_TYPE_ACTIVE || $data['post_types'] == Post::OPEN_AI_ACTIVE) {
            $articleInputArray = Arr::only($data, ['article_content']);
            $article = new PostArticle($articleInputArray);
            $post->postArticle()->save($article);
        } else {
            if ($data['post_types'] == Post::GALLERY_TYPE_ACTIVE) {
                $postGalleryArray = Arr::only(
                    $data,
                    ['gallery_title', 'image_description', 'gallery_content']
                );

                $galleyItemInputs = $this->galleryPrepareInputForItem($postGalleryArray);
                foreach ($galleyItemInputs as $key => $data) {
                    $gallery = new PostGallery($data);
                    /** @var Post $post */
                    if ($data['gallery_content']['gallery_title'] != null || $data['gallery_content']['image_description'] != null || $data['gallery_content']['gallery_content'] != null) {
                        $post->postGalleries()->save($gallery);
                    }
                }

                // $postGalleryArray = Arr::only($data, ['gallery_content']);
                // $galleryItemInputs = $this->prepareInputForItem($postGalleryArray);

                // foreach ($galleryItemInputs as $galleryData) {
                //     $gallery = new PostGallery($galleryData);

                //     // Ensure the data is not null before saving
                //     if (!empty($galleryData['gallery_title']) || !empty($galleryData['image_description']) || !empty($galleryData['gallery_content'])) {
                //         $post->postGalleries()->save($gallery);
                //     }
                // }
            } else {
                if ($data['post_types'] == Post::SORTED_TYPE_ACTIVE) {
                    $postSortListArray = Arr::only(
                        $data,
                        ['sort_list_title', 'image_description', 'sort_list_content']
                    );
                    $sortListItemInputs = $this->sortListPrepareInputForItem($postSortListArray);
                    foreach ($sortListItemInputs as $key => $data) {
                        $sortList = new PostSortList($data);
                        /** @var Post $post */
                        if ($data['sort_list_content']['sort_list_title'] != null || $data['sort_list_content']['image_description'] != null || $data['sort_list_content']['sort_list_content'] != null) {
                            $post->postSortLists()->save($sortList);
                        }
                    }
                } else {
                    if ($data['post_types'] == Post::VIDEO_TYPE_ACTIVE) {
                        $videoInputArray = Arr::only($data, ['video_content', 'thumbnail_image_url', 'video_url', 'video_embed_code']);
                        $postVideo = new PostVideo($videoInputArray);
                        $postVideo = $post->postVideo()->save($postVideo);
                    } else {
                        if ($data['post_types'] == Post::AUDIO_TYPE_ACTIVE) {
                            $audioInputArray = Arr::only($data, ['audio_content']);
                            $audio = new PostAudio($audioInputArray);
                            $postAudio = $post->PostAudios()->save($audio);
                        }
                    }
                }
            }
        }

        return $post;
    }

    public function galleryPrepareInputForItem(array $input): array
    {
        //  $items = array_map(function (...$values) use ($input) {
        //           return array_combine(array_keys($input), $values);
        //  }, ...array_values($input));

        //  return $items;

        $items = array_map(function ($item) {
            return [
                'gallery_title' => $item['gallery_title'] ?? null,
                'image_description' => $item['image_description'] ?? null,
                'gallery_content' => $item['gallery_content'] ?? null,
                // Add other fields as necessary
            ];
        }, $input['gallery_content'] ?? []);

        return $items;
    }
    public function sortListPrepareInputForItem(array $input): array
    {
        //  $items = array_map(function (...$values) use ($input) {
        //           return array_combine(array_keys($input), $values);
        //  }, ...array_values($input));

        //  return $items;

        $items = array_map(function ($item) {
            return [
                'sort_list_title' => $item['sort_list_title'] ?? null,
                'image_description' => $item['image_description'] ?? null,
                'sort_list_content' => $item['sort_list_content'] ?? null,
                // Add other fields as necessary
            ];
        }, $input['sort_list_content'] ?? []);

        return $items;
    }
    public function demoClick(Request $request)
    {
        $url = $request->headers->get('referer');
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $params);
        $tabs = $params['tab'] ?? null;

        if ($tabs == '-upload-video-tab') {
            self::$tab = 1;
        } else {
            self::$tab = 0;
        }
    }
}
