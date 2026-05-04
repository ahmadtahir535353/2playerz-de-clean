<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivetickerContent extends Model
{
     protected $table = 'liveticker_contents';
    protected $fillable = ['title', 'post_id', 'header', 'footer', 'live_indicator_enabled','live_indicator_until'];

    protected $casts = [
        'live_indicator_enabled' => 'boolean',
        'live_indicator_until'   => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function getIsLiveAttribute(): bool
    {
        if (!$this->live_indicator_enabled || !$this->live_indicator_until) return false;

        // DB me UTC hai; Berlin ke mutabiq compare karo:
        $nowBerlin   = now('Europe/Berlin');
        $untilBerlin = $this->live_indicator_until->clone()->setTimezone('Europe/Berlin');

        return $nowBerlin->lte($untilBerlin);
    }
}
