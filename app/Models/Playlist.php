<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Playlist extends Model
{
    protected $fillable = [
        'name',
        'type',
        'm3u_url',
        'xtream_host',
        'xtream_username',
        'xtream_password',
        'is_active',
        'last_used_at',

        'import_status',
        'import_message',
        'imported_channels_count',
        'import_started_at',
        'import_finished_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'xtream_password' => 'encrypted',

        'import_started_at' => 'datetime',
        'import_finished_at' => 'datetime',
    ];

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'xtream' ? 'Conto Xtream' : 'Lista M3U';
    }

    public function getGeneratedUrlAttribute(): ?string
    {
        if ($this->type === 'm3u') {
            return $this->m3u_url;
        }

        if (!$this->xtream_host || !$this->xtream_username || !$this->xtream_password) {
            return null;
        }

        $host = rtrim($this->xtream_host, '/');

        return $host . '/get.php?username=' . urlencode($this->xtream_username)
            . '&password=' . urlencode($this->xtream_password)
            . '&type=m3u_plus&output=ts';
    }
}
