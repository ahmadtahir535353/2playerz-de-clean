<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishScheduledPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $postId;

    public function __construct($postId)
    {
        $this->postId = $postId;
    }

    public function handle()
    {
        $post = Post::find($this->postId);
        if ($post && $post->scheduled_post == 1) {
            $post->update([
                'status' => 1, // Published
                'published_at' => now(),
                'created_at' => now(),
            ]);

            // Update user's last_seen_at and last_activity_at when scheduled post is published
            if ($post->visibility == 1) { // VISIBILITY_ACTIVE
                $user = \App\Models\User::find($post->created_by);
                if ($user) {
                    $user->update([
                        'last_seen_at' => now('Europe/Berlin'),
                        'last_activity_at' => now('Europe/Berlin')
                    ]);
                }
            }
        }
    }
}