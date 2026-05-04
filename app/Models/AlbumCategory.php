<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Schema;

/**
 * App\Models\AlbumCategory
 *
 * @property int $id
 * @property int $lang_id
 * @property int $album_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Album $album
 * @property-read Language $language
 *
 * @method static Builder|AlbumCategory newModelQuery()
 * @method static Builder|AlbumCategory newQuery()
 * @method static Builder|AlbumCategory query()
 * @method static Builder|AlbumCategory whereAlbumId($value)
 * @method static Builder|AlbumCategory whereCreatedAt($value)
 * @method static Builder|AlbumCategory whereId($value)
 * @method static Builder|AlbumCategory whereLangId($value)
 * @method static Builder|AlbumCategory whereName($value)
 * @method static Builder|AlbumCategory whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class AlbumCategory extends Model
{
    use HasFactory;

    protected $table = 'album_categories';

    protected $fillable = [
        'name',
        'lang_id',
        'album_id',
        'is_default',
    ];

    protected $casts = [
        'name' => 'string',
        'lang_id' => 'integer',
        'album_id' => 'integer',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class, 'album_id');
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(Gallery::class, 'category_id');
    }

    public static function getForm()
    {
        return [
            TextInput::make('name')
                ->label(__('messages.common.name').':')
                ->validationAttribute(__('messages.common.name'))
                ->placeholder(__('messages.common.name'))
                ->required()
                ->maxLength(255)
                ->unique(ignorable: fn(?AlbumCategory $record) => $record),
            Select::make('album_id')
                ->label(__('messages.gallery.album').':')
                ->validationAttribute(__('messages.gallery.album'))
                ->placeholder(__('messages.gallery.album'))
                ->searchable()
                ->preload()
                ->relationship('album', 'name')
                ->required(),
            Select::make('lang_id')
                ->label(__('messages.common.language').':')
                ->validationAttribute(__('messages.common.language'))
                ->placeholder(__('messages.common.language'))
                ->required()
                ->searchable()
                ->preload()
                ->relationship('language', 'name'),
            Hidden::make('is_default')
                ->default(false)
                ->dehydrated(function ($state) {
                    if (Schema::hasColumn('album_categories', 'is_default')) {
                        return true;
                    }
                }),
        ];
    }
}
