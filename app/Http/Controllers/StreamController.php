<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Services\HlsProxyService;

class StreamController extends Controller
{
    public function playlist(Channel $channel, HlsProxyService $hlsProxy)
    {
        @ini_set('max_execution_time', '120');
        @set_time_limit(120);

        abort_unless($channel->is_active, 404);
        abort_unless($channel->playlist && $channel->playlist->is_active, 404);

        try {
            $hlsProxy->start($channel);
        } catch (\Throwable $e) {
            return response(
                "ERRORE AVVIO FFMPEG\n\n" .
                "Canale ID: {$channel->id}\n" .
                "Messaggio: {$e->getMessage()}\n\n" .
                $this->debugFiles($channel, $hlsProxy),
                500
            )->header('Content-Type', 'text/plain');
        }

        $indexPath = $hlsProxy->indexPath($channel);

        /*
         * Il canale è HEVC/H.265, quindi FFmpeg deve convertirlo in H.264.
         * La prima generazione può richiedere più di 30 secondi.
         */
        $maxWaitSeconds = 90;
        $startedAt = time();

        while (!$this->playlistReady($indexPath)) {
            usleep(500000);

            if ((time() - $startedAt) >= $maxWaitSeconds) {
                return response(
                    "STREAM NON ANCORA PRONTO\n\n" .
                    "Canale ID: {$channel->id}\n" .
                    "Index atteso: {$indexPath}\n\n" .
                    $this->debugFiles($channel, $hlsProxy),
                    503
                )->header('Content-Type', 'text/plain');
            }
        }

        $content = file_get_contents($indexPath);

        if (!$content) {
            return response(
                "PLAYLIST HLS VUOTA\n\n" .
                $this->debugFiles($channel, $hlsProxy),
                503
            )->header('Content-Type', 'text/plain');
        }

        $content = $this->rewritePlaylistSegments($content, $channel);

        return response($content, 200)
            ->header('Content-Type', 'application/vnd.apple.mpegurl')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function segment(Channel $channel, string $segment)
    {
        @ini_set('max_execution_time', '120');
        @set_time_limit(120);

        abort_unless($channel->is_active, 404);
        abort_unless($channel->playlist && $channel->playlist->is_active, 404);

        $safeSegment = basename(str_replace('\\', '/', $segment));

        $path = storage_path('app/hls/channel_' . $channel->id . '/' . $safeSegment);

        abort_unless(file_exists($path), 404);

        return response()->file($path, [
            'Content-Type' => 'video/mp2t',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    private function playlistReady(string $indexPath): bool
    {
        if (!file_exists($indexPath)) {
            return false;
        }

        if (filesize($indexPath) < 20) {
            return false;
        }

        $content = file_get_contents($indexPath);

        if (!$content) {
            return false;
        }

        return str_contains($content, '#EXTM3U')
            && str_contains($content, '.ts');
    }

    private function rewritePlaylistSegments(string $content, Channel $channel): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $content);
        $rewritten = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (str_starts_with($line, '#')) {
                $rewritten[] = $line;
                continue;
            }

            $segment = basename(str_replace('\\', '/', $line));

            if (!str_ends_with(strtolower($segment), '.ts')) {
                $rewritten[] = $line;
                continue;
            }

            $rewritten[] = route('stream.segment', [
                'channel' => $channel->id,
                'segment' => $segment,
            ]);
        }

        return implode("\n", $rewritten) . "\n";
    }

    private function debugFiles(Channel $channel, HlsProxyService $hlsProxy): string
    {
        $dir = $hlsProxy->channelDir($channel);
        $indexPath = $hlsProxy->indexPath($channel);
        $logPath = $hlsProxy->logPath($channel);

        $output = "";

        $output .= "Cartella HLS: {$dir}\n";
        $output .= "Index path: {$indexPath}\n";
        $output .= "Log path: {$logPath}\n\n";

        $output .= "File presenti:\n";

        if (is_dir($dir)) {
            $files = glob($dir . DIRECTORY_SEPARATOR . '*') ?: [];

            foreach ($files as $file) {
                $output .= "- " . basename($file) . " (" . filesize($file) . " bytes)\n";
            }
        } else {
            $output .= "Cartella non esistente.\n";
        }

        $output .= "\n";

        if (file_exists($indexPath)) {
            $output .= "CONTENUTO INDEX.M3U8:\n";
            $output .= "---------------------\n";
            $output .= file_get_contents($indexPath) . "\n\n";
        } else {
            $output .= "index.m3u8 non esiste.\n\n";
        }

        if (file_exists($logPath)) {
            $output .= "ULTIME RIGHE FFMPEG.LOG:\n";
            $output .= "------------------------\n";

            $lines = file($logPath);
            $tail = array_slice($lines ?: [], -120);

            $output .= implode('', $tail);
        } else {
            $output .= "ffmpeg.log non esiste.\n";
        }

        return $output;
    }
}
