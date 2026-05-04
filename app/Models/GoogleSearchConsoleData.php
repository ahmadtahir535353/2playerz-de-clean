<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleSearchConsoleData extends Model
{
    use HasFactory;

    protected $table = 'google_search_console_data';

    protected $fillable = [
        'token_id',
        'date',
        'query',
        'page',
        'device_type',
        'country',
        'clicks',
        'impressions',
        'ctr',
        'position',
        'data_type',
    ];

    protected $casts = [
        'date' => 'date',
        'clicks' => 'integer',
        'impressions' => 'integer',
        'ctr' => 'decimal:4',
        'position' => 'decimal:2',
    ];

    /**
     * Get the token that owns this data.
     */
    public function token(): BelongsTo
    {
        return $this->belongsTo(GoogleSearchConsoleToken::class, 'token_id');
    }

    // Data type constants
    const TYPE_OVERALL = 'overall';
    const TYPE_QUERY = 'query';
    const TYPE_PAGE = 'page';
    const TYPE_DEVICE = 'device';
    const TYPE_COUNTRY = 'country';
}
