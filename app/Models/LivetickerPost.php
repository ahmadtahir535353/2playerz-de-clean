<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivetickerPost extends Model
{
    protected $table = 'liveticker_post';

    protected $fillable = ['post_id', 'content'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
