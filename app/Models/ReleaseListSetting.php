<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReleaseListSetting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const IMAGE_COLLECTION = 'release_list_image';

    const LIST_TYPE_ALL = 'all';
    const LIST_TYPE_PLAYSTATION = 'playstation';
    const LIST_TYPE_XBOX = 'xbox';
    const LIST_TYPE_NINTENDO = 'nintendo';

    protected $fillable = [
        'list_type',
        'headline',
        'short_description',
        'banner_title',
        'date_not_fixed_label',
        'keywords',
        'wishlist_info',
        'created_by',
        'views_count',
    ];

    protected $casts = [
        'headline' => 'string',
        'short_description' => 'string',
        'banner_title' => 'string',
        'date_not_fixed_label' => 'string',
        'wishlist_info' => 'string',
        'created_by' => 'integer',
    ];

    /**
     * Get the user who created/last updated this setting
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::IMAGE_COLLECTION)
            ->singleFile();
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        $media = $this->getFirstMedia(self::IMAGE_COLLECTION);
        return $media ? $media->getUrl() : null;
    }
}
