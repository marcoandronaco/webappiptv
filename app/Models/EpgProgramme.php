<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpgProgramme extends Model
{
    protected $fillable = [
        'channel_id',
        'title',
        'description',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}