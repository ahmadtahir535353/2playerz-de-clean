<?php
namespace App\Observers;

use App\Models\Post;
use App\Models\UserFcmToken;
use App\Services\IndexNowService;
use Kreait\Firebase\Factory as FirebaseFactory;

class PostObserver
{
    protected $indexNowService;

    public function __construct(IndexNowService $indexNowService)
    {
        $this->indexNowService = $indexNowService;
    }

    public function created(Post $post)
    {
        if ($post->visibility == Post::VISIBILITY_ACTIVE && $post->status == Post::STATUS_ACTIVE) {
            $this->sendNotification($post);
            $this->submitToIndexNow($post);
        }
    }

    public function updated(Post $post)
    {
        if ($post->isDirty('visibility') && $post->visibility == Post::VISIBILITY_ACTIVE) {
            $this->sendNotification($post);
        }

        // Submit to IndexNow when post becomes visible and active
        if (($post->isDirty('visibility') || $post->isDirty('status')) 
            && $post->visibility == Post::VISIBILITY_ACTIVE 
            && $post->status == Post::STATUS_ACTIVE) {
            $this->submitToIndexNow($post);
        }
    }

    protected function submitToIndexNow(Post $post)
    {
        if (empty($post->slug)) {
            return;
        }

        try {
            $url = $this->indexNowService->getPostUrl($post->slug);
            $this->indexNowService->submitUrl($url);
        } catch (\Exception $e) {
            \Log::error('IndexNow: Failed to submit post URL', [
                'post_id' => $post->id,
                'slug' => $post->slug,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendNotification(Post $post)
    {
        $tokens = UserFcmToken::where(function($q) use ($post) {
            $q->where('user_id', '!=', $post->user_id)
              ->orWhereNull('user_id'); 
        })
        ->pluck('token')
        ->toArray();

        if (empty($tokens)) {
            return;
        }

        $factory = (new FirebaseFactory)
            ->withServiceAccount(config('services.firebase.credentials.file'));

        $messaging = $factory->createMessaging();

        $message = [
                    'data' => [
                        'title' => '📢 Neuer Beitrag veröffentlicht!',
                        'body'  => $post->title ?? 'Schauen Sie sich jetzt den neuen Beitrag an!',
                        'link'  => route('detailPage', $post->slug ?? ''),
                    ]
                ];

        $messaging->sendMulticast($message, $tokens);
    }
}
