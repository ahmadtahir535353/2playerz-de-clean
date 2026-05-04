<?php

namespace App\Events;

use App\Models\Post;
use App\Models\LivetickerPost;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class LiveTickerUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $postId;
    public int $livetickerId;

    public function __construct(int $postId, int $livetickerId)
    {
        $this->postId = $postId;
        $this->livetickerId = $livetickerId;
    }

    public function broadcastOn()
    {
        return new Channel('liveticker.' . $this->postId);
    }

    public function broadcastAs()
    {
        return 'LiveTickerUpdated';
    }

    public function broadcastWith()
    {
        // 👇 yahan fresh DB records load ho rahe hain
        // $post = Post::find($this->postId);
        $liveticker = LivetickerPost::find($this->livetickerId);

        return [
            // 'post' => $post,
            'livetickerPost' => $liveticker,
        ];
    }
}
