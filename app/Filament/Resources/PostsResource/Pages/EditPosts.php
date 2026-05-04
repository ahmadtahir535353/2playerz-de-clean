<?php

namespace App\Filament\Resources\PostsResource\Pages;

use App\Filament\Resources\PostsResource;
use App\Models\Post;
use App\Models\PostAudio;
use App\Models\PostGallery;
use App\Models\PostSortList;
use App\Models\PostVideo;
use App\Models\LivetickerContent;
use App\Scopes\AuthoriseUserActivePostScope;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditPosts extends EditRecord
{
    protected static string $resource = PostsResource::class;

    public static int $tab = 0;

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.placeholder.post_updated_successfully');
    }

    public function mutateFormDataBeforeFill(array $data): array
    {

        $data['status'] = Post::STATUS_DRAFT == $data['status'] ? 1 : 0;
        $data['tags'] = explode(",", $data['tags']);
        $record = $this->record;
        $livetickerContent = $record->livetickerContent;
        $data['liveticker_header'] = $livetickerContent->header ?? null;
        $data['liveticker_footer'] = $livetickerContent->footer ?? null;
        
        // Load live indicator settings
        if ($livetickerContent) {
            $data['live_indicator_enabled'] = (bool) ($livetickerContent->live_indicator_enabled ?? false);
            if ($livetickerContent->live_indicator_until) {
                // Convert UTC to Berlin timezone for display
                // Ensure we have a Carbon instance and convert properly
                $untilDate = $livetickerContent->live_indicator_until;
                if ($untilDate instanceof \Carbon\Carbon) {
                    $data['live_indicator_until'] = $untilDate->clone()->setTimezone('Europe/Berlin');
                } else {
                    $data['live_indicator_until'] = \Carbon\Carbon::parse($untilDate, 'UTC')->setTimezone('Europe/Berlin');
                }
            } else {
                $data['live_indicator_until'] = null;
            }
        } else {
            $data['live_indicator_enabled'] = false;
            $data['live_indicator_until'] = null;
        }
        
        return $data;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return __('messages.common.edit') . ' ' . __('messages.post.post');
    }

   protected function mutateFormDataBeforeSave(array $data): array
{
    unset($data['liveticker_header'], $data['liveticker_footer'], $data['live_indicator_enabled'], $data['live_indicator_until']);
    return $data;
}

protected function afterSave(): void
{
    $data = $this->form->getState();

    // Prepare liveticker content data
    $livetickerData = [
        'header' => $data['liveticker_header'] ?? null,
        'footer' => $data['liveticker_footer'] ?? null,
    ];
    
    // Save live indicator settings if they exist in form data
    if (isset($data['live_indicator_enabled'])) {
        $livetickerData['live_indicator_enabled'] = (bool) $data['live_indicator_enabled'];
    }
    
    if (isset($data['live_indicator_until']) && !empty($data['live_indicator_until'])) {
        // Convert Berlin timezone to UTC for storage
        $berlinTime = \Carbon\Carbon::parse($data['live_indicator_until'], 'Europe/Berlin');
        $livetickerData['live_indicator_until'] = $berlinTime->setTimezone('UTC');
    } elseif (isset($data['live_indicator_enabled']) && !$data['live_indicator_enabled']) {
        // If disabled, clear the until time
        $livetickerData['live_indicator_until'] = null;
    }

    $this->record->livetickerContent()->updateOrCreate(
        ['post_id' => $this->record->id],
        $livetickerData
    );
}


    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    protected function resolveRecord($key): Model
    {
        return Post::withoutGlobalScopes([LanguageScope::class, PostDraftScope::class])
            ->with('livetickerContent')
            ->findOrFail($key);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['created_by'] = (!empty($data['created_by'])) ? $data['created_by'] : getLogInUserId();
        $data['tags'] = !empty($data['tags']) && is_array($data['tags']) ? implode(',', $data['tags']) : null;

        if (isset($data['scheduled_post']) && $data['scheduled_post']) {
            $data['status'] = Post::STATUS_DRAFT;
        } else {
            if (isset($data['status']) && $data['status']) {
                $data['status'] = Post::STATUS_DRAFT;
            } else {
                $data['status'] = Post::STATUS_ACTIVE;
                $data['scheduled_post'] = Post::STATUS_DRAFT;
                $data['scheduled_post_time'] = null;
            }
        }

        if (!$data['scheduled_post_delete']) {
            $data['scheduled_delete_post_time'] = null;
            $data['scheduled_post_delete'] = 0;
        }

        $postVisibilityCount = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->whereCreatedBy(getLogInUserId())->whereVisibility(1)->count();

        $data['featured'] = (isset($data['featured']) && $data['featured']) ? Post::FEATURED_ACTIVE : Post::FEATURED_DEACTIVE;

        if (Auth::user()->hasRole('customer')) {
            $data['visibility'] = (isset($data['visibility']) && $data['visibility']) ? (($postVisibilityCount < getloginuserplan()->no_of_post) ? Post::VISIBILITY_ACTIVE : Post::VISIBILITY_DEACTIVE) : Post::VISIBILITY_DEACTIVE;
        }
        if (!Auth::user()->hasRole('customer')) {
            $data['visibility'] = (isset($data['visibility']) && $data['visibility']) ? Post::VISIBILITY_ACTIVE : Post::VISIBILITY_DEACTIVE;
        }

        if ($data['status'] == Post::STATUS_DRAFT) {
            $data['visibility'] = Post::VISIBILITY_DEACTIVE;
        }

        $data['breaking'] = (isset($data['breaking']) && $data['breaking']) ? Post::BREAKING_ACTIVE : Post::BREAKING_DEACTIVE;

        $data['slider'] = (isset($data['slider']) && $data['slider']) ? Post::SLIDER_ACTIVE : Post::SLIDER_DEACTIVE;

        $data['recommended'] = (isset($data['recommended']) && $data['recommended']) ? Post::RECOMMENDED_ACTIVE : Post::RECOMMENDED_DEACTIVE;

        $data['show_registered_user'] = (isset($data['show_registered_user']) && $data['show_registered_user']) ? Post::SHOW_REGISTRED_USER_ACTIVE
            : Post::SHOW_REGISTRED_USER_DEACTIVE;

        // Preserve show_on_headline if not in form data (to prevent losing headline status on edit)
        if (isset($data['show_on_headline'])) {
            $data['show_on_headline'] = $data['show_on_headline'] ? Post::HEADLINE_ACTIVE : Post::HEADLINE_DEACTIVE;
        } else {
            // If not in form data, preserve existing value
            $data['show_on_headline'] = $record->show_on_headline ?? Post::HEADLINE_DEACTIVE;
        }

        // $post = $record->update($data);

        $post = Post::withoutGlobalScope(AuthoriseUserActivePostScope::class)->withoutGlobalScope(LanguageScope::class)
            ->withoutGlobalScope(PostDraftScope::class)
            ->findorFail($record->id);

        $post->update($data);

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
            $articleContent = $data['article_content'] ?? null;

            if ($articleContent !== null) {
                $post->postArticle()->updateOrCreate(
                    ['post_id' => $post->id],
                    ['article_content' => $articleContent]
                );
            }
        } else {
            if ($data['post_types'] == Post::GALLERY_TYPE_ACTIVE) {
                $postGalleryArray = Arr::only(
                    $data,
                    [
                        'gallery_title',
                        'image_description',
                        'gallery_content',
                        'gallery_images',
                        'gallery_image_remove',
                        'gallery_id',
                    ]
                );

                // Prepare gallery item inputs
                $galleryItemInputs = $this->galleryPrepareInputForItem($postGalleryArray);

                // $oldGalleryPost = PostGallery::where('post_id', '=', $post->id)->pluck('id')->toArray();
                // $currentGallery = !empty($postGalleryArray['gallery_id']) ? $postGalleryArray['gallery_id'] : [];
                // $remainingGalleryPost = array_diff($oldGalleryPost, $currentGallery);
                // if (count($remainingGalleryPost)) {
                //     PostGallery::whereIn('id', $remainingGalleryPost)->delete();
                // }

                foreach ($galleryItemInputs as $data) {
                    if (!empty($data['id'])) {
                        $gallery = PostGallery::find($data['id']);
                        if ($gallery) {
                            $updateData = array_filter($data, function ($value) {
                                return $value !== null;
                            });

                            $gallery->update($updateData);

                            if (empty($updateData['gallery_title']) && empty($updateData['image_description']) && empty($updateData['gallery_content'])) {
                                $gallery->delete();
                            }
                        }
                    } else {
                        $data = array_filter($data, function ($value) {
                            return $value !== null;
                        });

                        if (!empty($data['gallery_title']) || !empty($data['image_description']) || !empty($data['gallery_content'])) {
                            $galleryItem = new PostGallery($data);
                            $post->postGalleries()->save($galleryItem);
                        }
                    }
                }
            } else {
                if ($data['post_types'] == Post::SORTED_TYPE_ACTIVE) {
                    // $this->postSortListUpdate($data, $post);

                    $postSortListArray = Arr::only($data, [
                        'sort_list_title',
                        'image_description',
                        'sort_list_content',
                        'sorted_list_image',
                        'sort_list_id',
                        'sorted_list_image_remove',
                    ]);

                    // $oldSortPost = PostSortList::where('post_id', '=', $post->id)->pluck('id')->toArray();
                    // $currentSortList = !empty($postSortListArray['sort_list_id']) ? $postSortListArray['sort_list_id'] : [];
                    // $remainingSortPost = array_diff($oldSortPost, $currentSortList);
                    // if (count($remainingSortPost)) {
                    //     PostSortList::whereIn('id', $remainingSortPost)->delete();
                    // }

                    $sortListItemInputs = $this->sortListPrepareInputForItem($postSortListArray);
                    foreach ($sortListItemInputs as $key => $data) {
                        if (!empty($data['id'])) {
                            $sortList = PostSortList::findOrFail($data['id']);
                            $sortList->update($data);

                            if ($data['sort_list_title'] == null && $data['image_description'] == null && $data['sort_list_content'] == null) {
                                $sortList = PostSortList::find($data['id'])->delete();
                            }
                        } elseif ($data['sort_list_title'] != null || $data['image_description'] != null || $data['sort_list_content'] != null) {
                            $sortListItem = new PostSortList($data);
                            $sortList = $post->postSortLists()->save($sortListItem);
                        }
                    }
                } else {
                    if ($data['post_types'] == Post::VIDEO_TYPE_ACTIVE) {
                        // $this->postVideoUpdate($data, $post);
                        if (isset($data['video_content'])) {
                            $videoInputArray = [
                                'video_content' => $data['video_content'],
                                'thumbnail_image_url' => $data['thumbnail_image_url'],
                                'video_url' => $data['video_url'],
                                'video_embed_code' => $data['video_embed_code'],
                            ];
                            PostVideo::wherePostId($post->id)->update([
                                'video_content' => $videoInputArray['video_content'],
                                'thumbnail_image_url' => $videoInputArray['thumbnail_image_url'],
                                'video_url' => $videoInputArray['video_url'],
                                'video_embed_code' => $videoInputArray['video_embed_code'],
                            ]);
                        }

                        $postVideo = PostVideo::wherePostId($post->id)->first();
                    } else {
                        if ($data['post_types'] == Post::AUDIO_TYPE_ACTIVE) {
                            // $this->postAudioUpdate($data, $post);

                            $audioContent = $data['audio_content'] ?? null;

                            if ($audioContent !== null) {
                                $post->postAudios()->updateOrCreate(
                                    ['post_id' => $post->id],
                                    ['audio_content' => $audioContent]
                                );
                            }
                        }
                    }
                }
            }
        }

        return $post;
    }

    public function galleryPrepareInputForItem(array $data): array
    {
        $galleryTitles = $input['gallery_title'] ?? [];
        $imageDescriptions = $input['image_description'] ?? [];
        $galleryContents = $input['gallery_content'] ?? [];
        $galleryIds = $input['gallery_id'] ?? [];

        $items = [];
        foreach ($galleryContents as $key => $content) {
            $items[] = [
                'gallery_title' => $galleryTitles[$key] ?? null,
                'image_description' => $imageDescriptions[$key] ?? null,
                'gallery_content' => $content ?? null,
                'id' => $galleryIds[$key] ?? null,
            ];
        }
        return $items;
    }

    public function sortListPrepareInputForItem(array $data): array
    {
        $sortListTitles = $input['sort_list_title'] ?? [];
        $imageDescriptions = $input['image_description'] ?? [];
        $sortListContents = $input['sort_list_content'] ?? [];
        $sortListIds = $input['sort_list_id'] ?? [];

        $items = [];
        foreach ($sortListContents as $key => $content) {
            $items[] = [
                'sort_list_title' => $sortListTitles[$key] ?? null,
                'image_description' => $imageDescriptions[$key] ?? null,
                'sort_list_content' => $content ?? null,
                'id' => $sortListIds[$key] ?? null,
            ];
        }
        return $items;
    }

    public function demoClick(Request $request)
    {
        $url = $request->headers->get('referer');
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $params);
        $tabs = $params['tab'] ?? null;

        // if($tabs == '-url-video-tab') {
        //     self::$tab = 0;
        // } else {
        //     self::$tab = 1;
        // }

        // if($tabs == '-upload-video-tab') {
        //     self::$tab = 1;
        // } else {
        //     self::$tab = 0;
        // }

        $post = Post::with('postVideo')->find($this->record->id);
        if ($post == null || $post->postVideo->video_url == null) {
            if ($tabs == '-upload-video-tab') {
                self::$tab = 1;
            } else {
                self::$tab = 0;
            }
        } else {
            self::$tab = 1;
            if ($tabs == '-url-video-tab') {
                self::$tab = 0;
            } else {
                self::$tab = 1;
            }
        }
    }
}
