<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Channel extends Model
{
    protected $fillable = [
        'playlist_id',
        'name',
        'type',
        'logo',
        'group_title',
        'tvg_id',
        'stream_url',
        'stream_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }
}
