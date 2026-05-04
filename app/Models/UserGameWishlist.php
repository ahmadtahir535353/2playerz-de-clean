<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGameWishlist extends Model
{
    protected $table = 'user_game_wishlist';

    protected $fillable = [
        'user_id',
        'game_release_id',
        'highlighted',
    ];

    protected $casts = [
        'highlighted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gameRelease(): BelongsTo
    {
        return $this->belongsTo(GameRelease::class, 'game_release_id');
    }
}
