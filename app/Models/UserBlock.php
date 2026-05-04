<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBlock extends Model
{
    protected $fillable = ['blocker_id', 'blocked_id'];

    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    public function blocked(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }

    /**
     * Check if there is any block between two users (either direction).
     */
    public static function isBlockedBetween(int $userId1, int $userId2): bool
    {
        return static::where(function ($q) use ($userId1, $userId2) {
            $q->where('blocker_id', $userId1)->where('blocked_id', $userId2)
                ->orWhere('blocker_id', $userId2)->where('blocked_id', $userId1);
        })->exists();
    }
}
