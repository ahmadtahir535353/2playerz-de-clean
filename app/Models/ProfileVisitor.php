<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_owner_id',
        'visitor_id',
        'visitor_ip',
        'visitor_user_agent',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * Get the user whose profile was visited
     */
    public function profileOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profile_owner_id');
    }

    /**
     * Get the user who visited (null for guests)
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visitor_id');
    }

    /**
     * Check if this is a guest visit
     */
    public function isGuest(): bool
    {
        return is_null($this->visitor_id);
    }

    /**
     * Get visitor display name
     */
    public function getVisitorNameAttribute(): string
    {
        if ($this->isGuest()) {
            return 'Guest';
        }
        
        return $this->visitor ? $this->visitor->full_name : 'Unknown User';
    }

    /**
     * Get visitor profile picture
     */
    public function getVisitorProfilePictureAttribute(): ?string
    {
        if ($this->isGuest()) {
            return null;
        }
        
        return $this->visitor ? $this->visitor->profile_image : null;
    }
}