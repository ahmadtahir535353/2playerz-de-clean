<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversationId;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender'); // Ensure sender is loaded
        $this->conversationId = $message->conversation_id;

        \Log::info('MessageUpdated event created', [
            'message_id' => $message->id,
            'conversation_id' => $this->conversationId,
            'sender_id' => $message->sender_id,
            'recipient_id' => $message->recipient_id
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        \Log::info('Broadcasting MessageUpdated event to public channel: conversation.' . $this->conversationId);
        return [
            new Channel('conversation.' . $this->conversationId),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'sender_id' => $this->message->sender_id,
                'recipient_id' => $this->message->recipient_id,
                'created_at' => $this->message->created_at->format('d.m.Y, H:i'),
                'is_edited' => $this->message->is_edited,
                'edited_at' => $this->message->edited_at ? $this->message->edited_at->format('d.m.Y, H:i') : null,
                'sender' => [
                    'id' => $this->message->sender->id,
                    'username' => $this->message->sender->username,
                    'profile_image' => $this->message->sender->profile_image,
                ]
            ]
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'MessageUpdated';
    }
}
