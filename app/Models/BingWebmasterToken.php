<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BingWebmasterToken extends Model
{
    use HasFactory;

    protected $table = 'bing_webmaster_tokens';

    protected $fillable = [
        'user_id',
        'api_key',
        'site_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the Bing data for this token.
     */
    public function bingData(): HasMany
    {
        return $this->hasMany(BingWebmasterData::class, 'token_id');
    }
}
