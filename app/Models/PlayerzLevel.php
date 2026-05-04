<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerzLevel extends Model
{
    protected $fillable = [
        'name',
        'min_points',
        'badge_color',
        'badge_text_color',
    ];
}
