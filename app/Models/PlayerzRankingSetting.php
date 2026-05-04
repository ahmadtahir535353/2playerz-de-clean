<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PlayerzRankingSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'page_title',
        'page_subtitle',
        'header_description',
        'header_image',
        'creator_user_id',
        'points_rules_content',
        'is_active',
        'views_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const HEADER_IMAGE = 'header-image';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::HEADER_IMAGE)
            ->singleFile();
    }
}
