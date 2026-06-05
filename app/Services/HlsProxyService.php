<?php

namespace App\Services;

use App\Models\Channel;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;

class HlsProxyService
{
    public function start(Channel $channel): void
    {
        if (!$channel->stream_url || $channel->stream_url === '#') {
            throw new RuntimeException('URL stream non valido.');
        }

        $this->ensureFfmpegExists();

        $dir = $this->channelDir($channel);
        File::ensureDirectoryExists($dir);

        $indexPath = $this->indexPath($channel);
        $startingPath = $this->startingPath($channel);

        if ($this->isFresh($indexPath)) {
            return;
        }

        if ($this->isStarting($startingPath)) {
            return;
        }

        $this->clearChannelDir($dir);

        file_put_contents($startingPath, (string) time());

        try {
            $this->startFfmpeg($channel, $dir);
        } catch (\Throwable $e) {
            @unlink($startingPath);
            throw $e;
        }
    }

    public function indexPath(Channel $channel): string
    {
        return $this->channelDir($channel) . DIRECTORY_SEPARATOR . 'index.m3u8';
    }

    public function channelDir(Channel $channel): string
    {
        return storage_path('app/hls/channel_' . $channel->id);
    }

    public function logPath(Channel $channel): string
    {
        return $this->channelDir($channel) . DIRECTORY_SEPARATOR . 'ffmpeg.log';
    }

    private function batchFile(Channel $channel): string
    {
        return $this->channelDir($channel) . DIRECTORY_SEPARATOR . 'run_ffmpeg.bat';
    }

    private function startingPath(Channel $channel): string
    {
        return $this->channelDir($channel) . DIRECTORY_SEPARATOR . '.starting';
    }

    private function ffmpegBinary(): string
    {
        $binary = env('FFMPEG_BIN', 'ffmpeg');

        return trim(trim((string) $binary), '"');
    }

    private function ffmpegDirectory(): string
    {
        $binary = $this->ffmpegBinary();

        if ($binary === 'ffmpeg') {
            return '';
        }

        return str_replace('/', '\\', dirname($binary));
    }

    private function ensureFfmpegExists(): void
    {
        $process = new Process([
            $this->ffmpegBinary(),
            '-version',
        ]);

        $process->setTimeout(15);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                'FFmpeg non trovato. Installa FFmpeg oppure imposta FFMPEG_BIN nel file .env.'
            );
        }
    }

    private function startFfmpeg(Channel $channel, string $dir): void
    {
        $segmentPattern = $dir . DIRECTORY_SEPARATOR . 'segment_%06d.ts';
        $indexPath = $dir . DIRECTORY_SEPARATOR . 'index.m3u8';
        $logPath = $this->logPath($channel);
        $batPath = $this->batchFile($channel);
        $startingPath = $this->startingPath($channel);

        $host = parse_url($channel->stream_url, PHP_URL_HOST);

        $this->safeLog($logPath, '[' . now() . "] Avvio FFmpeg\n");
        $this->safeLog($logPath, '[' . now() . '] Canale ID: ' . $channel->id . "\n");
        $this->safeLog($logPath, '[' . now() . '] URL usato da FFmpeg: ' . $channel->stream_url . "\n");
        $this->safeLog($logPath, '[' . now() . '] Host: ' . ($host ?: 'non rilevato') . "\n");
        $this->safeLog($logPath, '[' . now() . '] Directory HLS: ' . $dir . "\n");
        $this->safeLog($logPath, '[' . now() . '] Binario FFmpeg: ' . $this->ffmpegBinary() . "\n");
        $this->safeLog($logPath, '[' . now() . '] Segment pattern: ' . $segmentPattern . "\n");

        $command = [
            $this->ffmpegBinary(),

            '-hide_banner',
            '-y',

            '-loglevel',
            'warning',

            '-reconnect',
            '1',

            '-reconnect_streamed',
            '1',

            '-reconnect_at_eof',
            '1',

            '-reconnect_on_network_error',
            '1',

            '-reconnect_delay_max',
            '5',

            '-rw_timeout',
            '15000000',

            '-user_agent',
            'VLC/3.0.18 LibVLC/3.0.18',

           '-fflags',
            '+genpts+discardcorrupt',

            '-analyzeduration',
            '10000000',

            '-probesize',
            '10000000',

            '-i',
            $channel->stream_url,

            '-map',
            '0:v:0?',

            '-map',
            '0:a:0?',

            /*
            * MODALITÀ STABILE LEGGERA:
            * Ricodifica il video ma a 480p, molto più leggero del vecchio 720p.
            * Serve perché con -c:v copy alcuni stream si bloccano dopo pochi secondi.
            */
            '-vf',
            'scale=-2:480,fps=25',

            '-c:v',
            'libx264',

            '-preset',
            'ultrafast',

            '-tune',
            'zerolatency',

            '-profile:v',
            'baseline',

            '-level',
            '3.0',

            '-pix_fmt',
            'yuv420p',

            '-crf',
            '34',

            '-maxrate',
            '900k',

            '-bufsize',
            '1800k',

            '-g',
            '100',

            '-keyint_min',
            '100',

            '-sc_threshold',
            '0',

            /*
            * Limita l'uso CPU.
            * Se ancora rallenta troppo, prova 1.
            * Se invece scatta, prova 3 o togli proprio questa riga.
            */
            '-threads',
            '2',

            '-max_muxing_queue_size',
            '2048',

            /*
            * Audio sempre convertito in AAC.
            */
            '-c:a',
            'aac',

            '-b:a',
            '96k',

            '-ac',
            '2',

            '-ar',
            '44100',

            '-af',
            'aresample=async=1:first_pts=0',

            '-f',
            'hls',

            '-hls_time',
            '4',

            '-hls_list_size',
            '6',

            '-hls_flags',
            'delete_segments+omit_endlist+independent_segments',

            '-hls_segment_type',
            'mpegts',

            '-hls_segment_filename',
            $segmentPattern,

            $indexPath,
        ];

        /*
         * ATTENZIONE:
         * Il comando viene scritto dentro un file .bat.
         * Quindi il pattern FFmpeg segment_%06d.ts deve diventare segment_%%06d.ts nel BAT,
         * altrimenti Windows interpreta %0 come nome del file .bat.
         */
        $commandLine = $this->buildWindowsCommandLineForBat($command);

        $batContent = "@echo off\r\n";

        /*
         * Laravel/PHP su Windows può avviare il BAT con un ambiente ridotto.
         * Reimpostiamo le variabili essenziali, altrimenti DNS e nslookup possono fallire.
         */
        $batContent .= "set \"SystemRoot=C:\\Windows\"\r\n";
        $batContent .= "set \"windir=C:\\Windows\"\r\n";
        $batContent .= "set \"ComSpec=C:\\Windows\\System32\\cmd.exe\"\r\n";

        $windowsPath = 'C:\\Windows\\System32;C:\\Windows;C:\\Windows\\System32\\Wbem;C:\\Windows\\System32\\WindowsPowerShell\\v1.0';

        $ffmpegDir = $this->ffmpegDirectory();

        if ($ffmpegDir !== '') {
            $windowsPath .= ';' . $ffmpegDir;
        }

        $batContent .= "set \"PATH={$windowsPath};%PATH%\"\r\n";

        $batContent .= "cd /d " . $this->windowsQuote(base_path()) . "\r\n";
        $batContent .= "echo [%DATE% %TIME%] BAT avviato >> " . $this->windowsQuote($logPath) . "\r\n";
        $batContent .= "echo [%DATE% %TIME%] PATH=%PATH% >> " . $this->windowsQuote($logPath) . "\r\n";

        if ($host) {
            $batContent .= "echo [%DATE% %TIME%] Test DNS {$host} >> " . $this->windowsQuote($logPath) . "\r\n";
            $batContent .= "C:\\Windows\\System32\\nslookup.exe " . $host . " >> " . $this->windowsQuote($logPath) . " 2>&1\r\n";
        }

        $batContent .= "set ATTEMPT=1\r\n";
        $batContent .= ":retry\r\n";
        $batContent .= "echo [%DATE% %TIME%] Tentativo FFmpeg %ATTEMPT% >> " . $this->windowsQuote($logPath) . "\r\n";

        $batContent .= $commandLine . " >> " . $this->windowsQuote($logPath) . " 2>&1\r\n";

        $batContent .= "if exist " . $this->windowsQuote($indexPath) . " goto end\r\n";
        $batContent .= "set /a ATTEMPT=%ATTEMPT%+1\r\n";

        $batContent .= "if %ATTEMPT% LEQ 8 (\r\n";
        $batContent .= "    echo [%DATE% %TIME%] FFmpeg terminato senza index.m3u8. Riprovo tra 3 secondi... >> " . $this->windowsQuote($logPath) . "\r\n";
        $batContent .= "    timeout /t 3 /nobreak > nul\r\n";
        $batContent .= "    goto retry\r\n";
        $batContent .= ")\r\n";

        $batContent .= ":end\r\n";
        $batContent .= "del /f /q " . $this->windowsQuote($startingPath) . " > nul 2>&1\r\n";
        $batContent .= "echo [%DATE% %TIME%] BAT terminato >> " . $this->windowsQuote($logPath) . "\r\n";

        file_put_contents($batPath, $batContent);

        $this->safeLog($logPath, '[' . now() . '] File BAT creato: ' . $batPath . "\n");
        $this->safeLog($logPath, '[' . now() . "] Avvio tramite cmd.exe senza PowerShell\n");

        $started = $this->startBatWithoutPowershell($batPath, $logPath);

        if (!$started) {
            @unlink($startingPath);

            $this->safeLog($logPath, '[' . now() . "] ERRORE: cmd.exe non ha avviato il BAT\n");

            throw new RuntimeException(
                'Impossibile avviare FFmpeg. Controlla ffmpeg.log in storage/app/hls/channel_' . $channel->id
            );
        }

        $this->safeLog($logPath, '[' . now() . "] FFmpeg avviato tramite BAT\n");
    }

    private function startBatWithoutPowershell(string $batPath, string $logPath): bool
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $process = new Process([
                'cmd.exe',
                '/C',
                'start',
                '',
                '/B',
                $batPath,
            ]);

            $process->setTimeout(10);
            $process->run();

            $this->safeLog($logPath, '[' . now() . "] Output cmd.exe:\n" . $process->getOutput() . "\n");
            $this->safeLog($logPath, '[' . now() . "] Error cmd.exe:\n" . $process->getErrorOutput() . "\n");

            return $process->isSuccessful();
        }

        $process = new Process([
            'bash',
            '-lc',
            'nohup ' . escapeshellarg($batPath) . ' >> ' . escapeshellarg($logPath) . ' 2>&1 &',
        ]);

        $process->setTimeout(10);
        $process->run();

        return $process->isSuccessful();
    }

    private function buildWindowsCommandLineForBat(array $command): string
    {
        return implode(' ', array_map(function ($argument) {
            return $this->windowsQuoteForBat((string) $argument);
        }, $command));
    }

    private function windowsQuoteForBat(string $value): string
    {
        /*
         * Dentro un file .bat il simbolo % ha significato speciale.
         * FFmpeg però richiede segment_%06d.ts.
         * Quindi nel BAT dobbiamo scrivere segment_%%06d.ts.
         */
        $value = str_replace('%', '%%', $value);

        return $this->windowsQuote($value);
    }

    private function windowsQuote(string $value): string
    {
        $value = str_replace('"', '\"', $value);

        return '"' . $value . '"';
    }

    private function safeLog(string $path, string $message): void
    {
        @file_put_contents($path, $message, FILE_APPEND);
    }

    private function isFresh(string $indexPath): bool
    {
        if (!file_exists($indexPath)) {
            return false;
        }

        if (filesize($indexPath) < 20) {
            return false;
        }

        return filemtime($indexPath) >= now()->subSeconds(30)->timestamp;
    }

    private function isStarting(string $startingPath): bool
    {
        if (!file_exists($startingPath)) {
            return false;
        }

        return filemtime($startingPath) >= now()->subSeconds(120)->timestamp;
    }

    private function clearChannelDir(string $dir): void
    {
        File::ensureDirectoryExists($dir);

        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}