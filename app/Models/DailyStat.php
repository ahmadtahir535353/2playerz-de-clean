<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStat extends Model
{
    protected $table = 'daily_stats';

    protected $fillable = ['date', 'post_views', 'unique_visitors'];

    protected $casts = [
        'date' => 'date',
        'post_views' => 'integer',
        'unique_visitors' => 'integer',
    ];
}
