<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user1_id',
        'user2_id',
        'last_message_at',
        'last_message_id',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the first user in the conversation
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Get the second user in the conversation
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Get all messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the last message in the conversation
     */
    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * Get the other user in the conversation
     */
    public function getOtherUser($currentUserId)
    {
        if ($this->user1_id == $currentUserId) {
            return $this->user2;
        }
        return $this->user1;
    }

    /**
     * Check if user is part of this conversation
     */
    public function hasUser($userId)
    {
        return $this->user1_id == $userId || $this->user2_id == $userId;
    }

    /**
     * Get or create conversation between two users
     */
    public static function getOrCreateConversation($user1Id, $user2Id)
    {
        // Ensure user1_id is always the smaller ID for consistency
        if ($user1Id > $user2Id) {
            [$user1Id, $user2Id] = [$user2Id, $user1Id];
        }

        return static::firstOrCreate(
            [
                'user1_id' => $user1Id,
                'user2_id' => $user2Id,
            ],
            [
                'last_message_at' => now(),
            ]
        );
    }
}
