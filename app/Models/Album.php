<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Schema;

/**
 * App\Models\Album
 *
 * @property int $id
 * @property int $lang_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Language $language
 *
 * @method static Builder|Album newModelQuery()
 * @method static Builder|Album newQuery()
 * @method static Builder|Album query()
 * @method static Builder|Album whereCreatedAt($value)
 * @method static Builder|Album whereId($value)
 * @method static Builder|Album whereLangId($value)
 * @method static Builder|Album whereName($value)
 * @method static Builder|Album whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Album extends Model
{
    use HasFactory;

    protected $table = 'albums';

    protected $fillable = ['name', 'lang_id','is_default'];

    protected $casts = [
        'name' => 'string',
        'lang_id' => 'integer',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

    public function gallery()
    {
        return $this->hasMany(Gallery::class, 'album_id');
    }

    public function AlbumCategory()
    {
        return $this->hasMany(AlbumCategory::class, 'album_id');
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
                ->unique(ignorable: fn(?Album $record) => $record),
            Select::make('lang_id')
                ->label(__('messages.common.language').':')
                ->validationAttribute(__('messages.common.language'))
                ->placeholder(__('messages.common.select_language'))
                ->required()
                ->searchable()
                ->preload()
                ->relationship('language', 'name'),
            Hidden::make('is_default')
                ->default(false)
                ->dehydrated(function ($state) {
                    if (Schema::hasColumn('albums', 'is_default')) {
                        return true;
                    }
                }),
        ];
    }
}
