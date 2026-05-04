<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';
    protected $description = 'Publish scheduled posts whose time has come';

    public function handle()
    {
        // Check karo aise posts jinka time aa gaya ho
        $posts = Post::where('scheduled_post', 1)
                      ->where('scheduled_post_time', '<=', now())
                      ->where('status', '!=', 1)
                      ->get();

        foreach ($posts as $post) {
            $post->update([
                'status' => 1, // Published
                'published_at' => now(),
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

            $this->info("Published post: {$post->id}");
        }

        $this->info("Scheduled posts check complete.");
    }
}
