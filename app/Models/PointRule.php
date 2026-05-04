<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    protected $table = 'point_rules';
    protected $fillable = ['key','label','points'];
}
