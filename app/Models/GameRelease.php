<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GameRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'link',
        'release_date',
        'release_year',
        'release_month',
        'playstation',
        'xbox',
        'nintendo',
        'ps_plus',
        'game_pass',
    ];

    protected $casts = [
        'release_date' => 'date',
        'release_year' => 'integer',
        'release_month' => 'integer',
        'playstation' => 'boolean',
        'xbox' => 'boolean',
        'nintendo' => 'boolean',
        'ps_plus' => 'boolean',
        'game_pass' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($gameRelease) {
            if (empty($gameRelease->slug)) {
                $gameRelease->slug = Str::slug($gameRelease->name);
            }
            
            // Extract year and month from release_date if provided
            if ($gameRelease->release_date) {
                $gameRelease->release_year = $gameRelease->release_date->year;
                $gameRelease->release_month = $gameRelease->release_date->month;
            }
            // If release_date is null but year/month are set, that's fine (partial date)
            // Don't override year/month if they're already set
        });

        static::updating(function ($gameRelease) {
            // If release_date is set, extract year and month
            if ($gameRelease->isDirty('release_date')) {
                if ($gameRelease->release_date) {
                    $gameRelease->release_year = $gameRelease->release_date->year;
                    $gameRelease->release_month = $gameRelease->release_date->month;
                }
                // If release_date is being set to null, don't clear year/month
                // They might be set separately for partial dates
            }
            // If release_year or release_month are set directly, keep them
            // release_date can be null for partial dates
        });
    }

    /**
     * Scope for filtering by platform
     */
    public function scopeForPlatform($query, $platform)
    {
        return $query->where($platform, true);
    }

    /**
     * Scope for games with any date info (exact date or year/month or year only)
     */
    public function scopeWithDates($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('release_date')
                ->orWhereNotNull('release_year');
        });
    }

    /**
     * Scope for games without any date info
     */
    public function scopeWithoutDates($query)
    {
        return $query->whereNull('release_date')->whereNull('release_year');
    }

    /**
     * Get platforms as array
     */
    public function getPlatformsAttribute()
    {
        $platforms = [];
        if ($this->playstation) $platforms[] = 'PlayStation';
        if ($this->xbox) $platforms[] = 'Xbox';
        if ($this->nintendo) $platforms[] = 'Nintendo';
        return $platforms;
    }

    /**
     * Get subscription services as array
     */
    public function getSubscriptionsAttribute()
    {
        $subscriptions = [];
        if ($this->ps_plus) $subscriptions[] = 'PS Plus';
        if ($this->game_pass) $subscriptions[] = 'Game Pass';
        return $subscriptions;
    }

    /**
     * Users who have this game on their wishlist
     */
    public function wishlistedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_game_wishlist', 'game_release_id', 'user_id')
            ->withPivot('highlighted')
            ->withTimestamps();
    }
}
