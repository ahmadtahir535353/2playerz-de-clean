<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleSearchConsoleToken extends Model
{
    use HasFactory;

    protected $table = 'google_search_console_tokens';

    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'property_url',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
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
     * Get the GSC data for this token.
     */
    public function gscData(): HasMany
    {
        return $this->hasMany(GoogleSearchConsoleData::class, 'token_id');
    }

    /**
     * Check if token is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return true;
        }

        return $this->expires_at->isPast();
    }
}
