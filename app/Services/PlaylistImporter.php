<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Playlist;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use SplFileObject;

class PlaylistImporter
{
    private int $chunkSize = 300;

    public function import(Playlist $playlist): int
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(3600);

        $playlist->channels()->delete();

        if ($playlist->type === 'xtream') {
            return $this->importXtream($playlist);
        }

        return $this->importM3u($playlist);
    }

    private function importM3u(Playlist $playlist): int
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(3600);

        if (!$playlist->m3u_url) {
            throw new RuntimeException('URL M3U mancante.');
        }

        $tmpPath = storage_path('app/temp_playlist_' . Str::uuid() . '.m3u');

        try {
            $response = Http::connectTimeout(10)
                ->timeout(180)
                ->sink($tmpPath)
                ->get($playlist->m3u_url);

            if (!$response->successful()) {
                throw new RuntimeException('Impossibile leggere la playlist M3U. Codice HTTP: ' . $response->status());
            }

            if (!file_exists($tmpPath) || filesize($tmpPath) === 0) {
                throw new RuntimeException('Playlist M3U vuota o non scaricata.');
            }

            $file = new SplFileObject($tmpPath, 'r');

            $buffer = [];
            $count = 0;
            $current = null;
            $now = now();

            while (!$file->eof()) {
                $line = trim((string) $file->fgets());

                if ($line === '' || str_starts_with($line, '#EXTM3U')) {
                    continue;
                }

                if (str_starts_with($line, '#EXTINF')) {
                    $current = $this->parseExtinf($line);
                    continue;
                }

                if ($current && filter_var($line, FILTER_VALIDATE_URL)) {
                    $group = trim((string) ($current['group_title'] ?? ''));

                    if ($group === '') {
                        $group = 'Senza categoria';
                    }

                    $name = $current['name'] ?: 'Canale senza nome';

                    $buffer[] = [
                        'playlist_id' => $playlist->id,
                        'name' => Str::limit($name, 250, ''),
                        'type' => $this->detectType($group, $name, $line),
                        'is_series_parent' => false,
                        'logo' => $this->limitNullable($current['logo'] ?? null),
                        'group_title' => $this->limitNullable($group),
                        'tvg_id' => $this->limitNullable($current['tvg_id'] ?? null),
                        'stream_url' => $line,
                        'stream_id' => null,
                        'series_id' => null,
                        'season_number' => null,
                        'episode_number' => null,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $current = null;

                    if (count($buffer) >= $this->chunkSize) {
                        $count += $this->insertChunk($buffer);
                    }
                }
            }

            $count += $this->insertChunk($buffer);

            if ($count === 0) {
                throw new RuntimeException('Nessun canale trovato nella playlist M3U.');
            }

            $playlist->update([
                'last_used_at' => now(),
            ]);

            return $count;
        } finally {
            if (file_exists($tmpPath)) {
                @unlink($tmpPath);
            }
        }
    }

    private function importXtream(Playlist $playlist): int
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(3600);

        if (!$playlist->xtream_host || !$playlist->xtream_username || !$playlist->xtream_password) {
            throw new RuntimeException('Dati Xtream mancanti.');
        }

        $host = rtrim($playlist->xtream_host, '/');
        $username = $playlist->xtream_username;
        $password = $playlist->xtream_password;

        $authResponse = Http::connectTimeout(10)
            ->timeout(40)
            ->get($host . '/player_api.php', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!$authResponse->successful()) {
            throw new RuntimeException('Impossibile collegarsi al server Xtream.');
        }

        $authData = $authResponse->json();

        if (!is_array($authData)) {
            throw new RuntimeException('Risposta Xtream non valida.');
        }

        if (isset($authData['user_info']['auth']) && (int) $authData['user_info']['auth'] !== 1) {
            throw new RuntimeException('Credenziali Xtream non valide.');
        }

        $liveCategories = $this->getXtreamCategories($host, $username, $password, 'get_live_categories');
        $vodCategories = $this->getXtreamCategories($host, $username, $password, 'get_vod_categories');
        $seriesCategories = $this->getXtreamCategories($host, $username, $password, 'get_series_categories');

        $count = 0;

        $playlist->update([
            'import_message' => 'Importazione Live TV...',
        ]);

        $count += $this->importXtreamSection(
            playlist: $playlist,
            host: $host,
            username: $username,
            password: $password,
            action: 'get_live_streams',
            type: 'live',
            categories: $liveCategories
        );

        $playlist->update([
            'import_message' => 'Importazione Film...',
        ]);

        $count += $this->importXtreamSection(
            playlist: $playlist,
            host: $host,
            username: $username,
            password: $password,
            action: 'get_vod_streams',
            type: 'film',
            categories: $vodCategories
        );

        $playlist->update([
            'import_message' => 'Importazione catalogo Serie...',
        ]);

        $count += $this->importXtreamSeriesCatalog(
            playlist: $playlist,
            host: $host,
            username: $username,
            password: $password,
            categories: $seriesCategories
        );

        if ($count === 0) {
            throw new RuntimeException('Nessun contenuto trovato tramite API Xtream.');
        }

        $playlist->update([
            'last_used_at' => now(),
        ]);

        return $count;
    }

    private function getXtreamCategories(
        string $host,
        string $username,
        string $password,
        string $action
    ): array {
        $response = Http::connectTimeout(10)
            ->timeout(40)
            ->get($host . '/player_api.php', [
                'username' => $username,
                'password' => $password,
                'action' => $action,
            ]);

        if (!$response->successful()) {
            return [];
        }

        $categories = $response->json();

        if (!is_array($categories)) {
            return [];
        }

        $map = [];

        foreach ($categories as $category) {
            $id = $category['category_id'] ?? null;
            $name = $category['category_name'] ?? null;

            if ($id !== null && $name) {
                $map[(string) $id] = $name;
            }
        }

        return $map;
    }

    private function importXtreamSection(
        Playlist $playlist,
        string $host,
        string $username,
        string $password,
        string $action,
        string $type,
        array $categories = []
    ): int {
        ini_set('memory_limit', '2048M');
        set_time_limit(3600);

        $response = Http::connectTimeout(10)
            ->timeout(120)
            ->get($host . '/player_api.php', [
                'username' => $username,
                'password' => $password,
                'action' => $action,
            ]);

        if (!$response->successful()) {
            return 0;
        }

        $streams = $response->json();

        if (!is_array($streams)) {
            return 0;
        }

        $buffer = [];
        $count = 0;
        $now = now();

        foreach ($streams as $stream) {
            $streamId = $stream['stream_id'] ?? null;

            if (!$streamId) {
                continue;
            }

            $categoryId = isset($stream['category_id']) ? (string) $stream['category_id'] : null;

            $group = $stream['category_name'] ?? null;

            if (!$group && $categoryId && isset($categories[$categoryId])) {
                $group = $categories[$categoryId];
            }

            if (!$group) {
                $group = 'Senza categoria';
            }

            if ($type === 'live') {
                $streamUrl = $host . '/live/' .
                    rawurlencode($username) . '/' .
                    rawurlencode($password) . '/' .
                    $streamId . '.m3u8';

                $name = $stream['name'] ?? 'Canale senza nome';
            } else {
                $extension = $stream['container_extension'] ?? 'mp4';

                $streamUrl = $host . '/movie/' .
                    rawurlencode($username) . '/' .
                    rawurlencode($password) . '/' .
                    $streamId . '.' . $extension;

                $name = $stream['name'] ?? 'Film senza nome';
            }

            $buffer[] = [
                'playlist_id' => $playlist->id,
                'name' => Str::limit($name, 250, ''),
                'type' => $type,
                'is_series_parent' => false,
                'logo' => $this->limitNullable($stream['stream_icon'] ?? null),
                'group_title' => $this->limitNullable($group),
                'tvg_id' => null,
                'stream_url' => $streamUrl,
                'stream_id' => $this->limitNullable((string) $streamId),
                'series_id' => null,
                'season_number' => null,
                'episode_number' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($buffer) >= $this->chunkSize) {
                $count += $this->insertChunk($buffer);
            }
        }

        $count += $this->insertChunk($buffer);

        return $count;
    }

    private function importXtreamSeriesCatalog(
        Playlist $playlist,
        string $host,
        string $username,
        string $password,
        array $categories = []
    ): int {
        ini_set('memory_limit', '2048M');
        set_time_limit(3600);

        $response = Http::connectTimeout(10)
            ->timeout(120)
            ->get($host . '/player_api.php', [
                'username' => $username,
                'password' => $password,
                'action' => 'get_series',
            ]);

        if (!$response->successful()) {
            return 0;
        }

        $seriesList = $response->json();

        if (!is_array($seriesList)) {
            return 0;
        }

        $buffer = [];
        $count = 0;
        $now = now();

        foreach ($seriesList as $serie) {
            $seriesId = $serie['series_id'] ?? null;

            if (!$seriesId) {
                continue;
            }

            $categoryId = isset($serie['category_id']) ? (string) $serie['category_id'] : null;

            $group = $serie['category_name'] ?? null;

            if (!$group && $categoryId && isset($categories[$categoryId])) {
                $group = $categories[$categoryId];
            }

            if (!$group) {
                $group = 'Serie';
            }

            $buffer[] = [
                'playlist_id' => $playlist->id,
                'name' => Str::limit($serie['name'] ?? 'Serie senza nome', 250, ''),
                'type' => 'serie',
                'is_series_parent' => true,
                'logo' => $this->limitNullable($serie['cover'] ?? null),
                'group_title' => $this->limitNullable($group),
                'tvg_id' => null,
                'stream_url' => '#',
                'stream_id' => $this->limitNullable((string) $seriesId),
                'series_id' => $this->limitNullable((string) $seriesId),
                'season_number' => null,
                'episode_number' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($buffer) >= $this->chunkSize) {
                $count += $this->insertChunk($buffer);
            }
        }

        $count += $this->insertChunk($buffer);

        return $count;
    }

    public function importEpisodesForSeries(Channel $seriesChannel): int
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(900);

        if (!$seriesChannel->is_series_parent || !$seriesChannel->series_id) {
            throw new RuntimeException('Questa non è una serie valida.');
        }

        $playlist = $seriesChannel->playlist;

        if (!$playlist || $playlist->type !== 'xtream') {
            throw new RuntimeException('Playlist Xtream non valida.');
        }

        $host = rtrim($playlist->xtream_host, '/');
        $username = $playlist->xtream_username;
        $password = $playlist->xtream_password;

        Channel::where('playlist_id', $playlist->id)
            ->where('type', 'serie')
            ->where('is_series_parent', false)
            ->where('series_id', $seriesChannel->series_id)
            ->delete();

        $response = Http::connectTimeout(10)
            ->timeout(120)
            ->get($host . '/player_api.php', [
                'username' => $username,
                'password' => $password,
                'action' => 'get_series_info',
                'series_id' => $seriesChannel->series_id,
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('Impossibile leggere gli episodi della serie.');
        }

        $info = $response->json();

        if (!is_array($info)) {
            throw new RuntimeException('Risposta episodi non valida.');
        }

        $episodes = $info['episodes'] ?? [];

        if (!is_array($episodes)) {
            throw new RuntimeException('Nessun episodio trovato.');
        }

        $buffer = [];
        $count = 0;
        $now = now();

        foreach ($episodes as $seasonNumber => $seasonEpisodes) {
            if (isset($seasonEpisodes['id'])) {
                $seasonEpisodes = [$seasonEpisodes];
            }

            if (!is_array($seasonEpisodes)) {
                continue;
            }

            foreach ($seasonEpisodes as $episode) {
                $episodeId = $episode['id'] ?? null;

                if (!$episodeId) {
                    continue;
                }

                $episodeTitle = $episode['title'] ?? 'Episodio';
                $episodeNumber = $episode['episode_num'] ?? null;
                $extension = $episode['container_extension'] ?? 'mp4';

                $seasonNumberInt = is_numeric($seasonNumber) ? (int) $seasonNumber : 1;
                $episodeNumberInt = is_numeric($episodeNumber) ? (int) $episodeNumber : 1;

                $seasonLabel = str_pad((string) $seasonNumberInt, 2, '0', STR_PAD_LEFT);
                $episodeLabel = str_pad((string) $episodeNumberInt, 2, '0', STR_PAD_LEFT);

                $name = $seriesChannel->name . ' - S' . $seasonLabel . 'E' . $episodeLabel . ' - ' . $episodeTitle;

                $streamUrl = $host . '/series/' .
                    rawurlencode($username) . '/' .
                    rawurlencode($password) . '/' .
                    $episodeId . '.' . $extension;

                $episodeLogo = null;

                if (isset($episode['info']) && is_array($episode['info'])) {
                    $episodeLogo = $episode['info']['movie_image'] ?? null;
                }

                $buffer[] = [
                    'playlist_id' => $playlist->id,
                    'name' => Str::limit($name, 250, ''),
                    'type' => 'serie',
                    'is_series_parent' => false,
                    'logo' => $this->limitNullable($episodeLogo ?? $seriesChannel->logo),
                    'group_title' => $this->limitNullable($seriesChannel->group_title ?: 'Serie'),
                    'tvg_id' => null,
                    'stream_url' => $streamUrl,
                    'stream_id' => $this->limitNullable((string) $episodeId),
                    'series_id' => $this->limitNullable((string) $seriesChannel->series_id),
                    'season_number' => $seasonNumberInt,
                    'episode_number' => $episodeNumberInt,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($buffer) >= $this->chunkSize) {
                    $count += $this->insertChunk($buffer);
                }
            }
        }

        $count += $this->insertChunk($buffer);

        if ($count === 0) {
            throw new RuntimeException('Nessun episodio importato.');
        }

        return $count;
    }

    private function insertChunk(array &$buffer): int
    {
        if (count($buffer) === 0) {
            return 0;
        }

        Channel::insert($buffer);

        $inserted = count($buffer);
        $buffer = [];

        return $inserted;
    }

    private function parseExtinf(string $line): array
    {
        preg_match('/tvg-id="([^"]*)"/i', $line, $tvgId);
        preg_match('/tvg-logo="([^"]*)"/i', $line, $logo);
        preg_match('/group-title="([^"]*)"/i', $line, $groupTitle);

        $name = 'Canale senza nome';

        if (str_contains($line, ',')) {
            $parts = explode(',', $line);
            $name = trim(end($parts));
        }

        return [
            'name' => $name,
            'tvg_id' => $tvgId[1] ?? null,
            'logo' => $logo[1] ?? null,
            'group_title' => $groupTitle[1] ?? null,
        ];
    }

    private function detectType(?string $group, ?string $name, ?string $url): string
    {
        $text = Str::lower(($group ?? '') . ' ' . ($name ?? '') . ' ' . ($url ?? ''));

        if (
            str_contains($text, 'film') ||
            str_contains($text, 'movie') ||
            str_contains($text, 'movies') ||
            str_contains($text, 'vod') ||
            str_contains($text, 'cinema')
        ) {
            return 'film';
        }

        if (
            str_contains($text, 'serie') ||
            str_contains($text, 'series') ||
            str_contains($text, 'tv show') ||
            str_contains($text, 'season') ||
            str_contains($text, 'stagione') ||
            str_contains($text, 'episodio')
        ) {
            return 'serie';
        }

        return 'live';
    }

    private function limitNullable(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        return Str::limit($value, 250, '');
    }
}
