<?php

namespace App\Jobs;

use App\Models\Playlist;
use App\Services\PlaylistImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportPlaylistJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;
    public int $tries = 1;

    public function __construct(
        public int $playlistId
    ) {}

    public function handle(PlaylistImporter $importer): void
    {
        set_time_limit(600);

        $playlist = Playlist::find($this->playlistId);

        if (!$playlist) {
            return;
        }

        $playlist->update([
            'import_status' => 'running',
            'import_message' => null,
            'import_started_at' => now(),
            'import_finished_at' => null,
        ]);

        try {
            $count = $importer->import($playlist);

            $playlist->update([
                'import_status' => 'completed',
                'import_message' => 'Importazione completata correttamente.',
                'imported_channels_count' => $count,
                'import_finished_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::error('Errore importazione playlist', [
                'playlist_id' => $playlist->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $playlist->update([
                'import_status' => 'failed',
                'import_message' => $e->getMessage(),
                'import_finished_at' => now(),
            ]);
        }
    }
}
