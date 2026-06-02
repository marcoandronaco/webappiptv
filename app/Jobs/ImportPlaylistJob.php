<?php

namespace App\Jobs;

use App\Models\Playlist;
use App\Services\PlaylistImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ImportPlaylistJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;
    public int $tries = 1;

    public function __construct(
        public int $playlistId
    ) {}

    public function handle(PlaylistImporter $importer): void
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(3600);

        $playlist = Playlist::find($this->playlistId);

        if (!$playlist) {
            return;
        }

        $playlist->update([
            'import_status' => 'running',
            'import_message' => 'Importazione Live TV, Film e catalogo Serie in corso...',
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
            $message = $this->shortMessage($e->getMessage());

            Log::error('Errore importazione playlist', [
                'playlist_id' => $playlist->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $playlist->update([
                'import_status' => 'failed',
                'import_message' => $message,
                'import_finished_at' => now(),
            ]);
        }
    }

    private function shortMessage(string $message): string
    {
        return Str::limit($message, 1000, '...');
    }
}
