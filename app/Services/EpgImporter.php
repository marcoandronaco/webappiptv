<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Playlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use XMLReader;

class EpgImporter
{
    public function importPlaylist(Playlist $playlist, ?callable $logger = null): array
    {
        $epgUrl = trim((string) ($playlist->epg_url ?? ''));

        if ($epgUrl === '') {
            return [
                'playlist_id' => $playlist->id,
                'playlist_name' => $playlist->name ?? null,
                'skipped' => true,
                'reason' => 'Nessun URL EPG impostato',
                'imported' => 0,
                'skipped_programmes' => 0,
                'matched_channels' => 0,
            ];
        }

        if (!Schema::hasTable('epg_programmes')) {
            return [
                'playlist_id' => $playlist->id,
                'playlist_name' => $playlist->name ?? null,
                'skipped' => true,
                'reason' => 'Tabella epg_programmes non presente',
                'imported' => 0,
                'skipped_programmes' => 0,
                'matched_channels' => 0,
            ];
        }

        $this->log($logger, 'Scarico EPG: ' . $epgUrl);

        $dir = storage_path('app/epg');
        File::ensureDirectoryExists($dir);

        $downloadPath = $dir . '/playlist_' . $playlist->id . '_' . time() . '.download';
        $xmlPath = $dir . '/playlist_' . $playlist->id . '_' . time() . '.xml';

        $response = Http::timeout(300)
            ->retry(2, 2000)
            ->sink($downloadPath)
            ->get($epgUrl);

        if (!$response->successful()) {
            throw new RuntimeException('Download EPG fallito. HTTP ' . $response->status());
        }

        if ($this->isGzip($downloadPath, $epgUrl)) {
            $this->log($logger, 'EPG compresso rilevato. Decomprimo...');
            $this->gunzipFile($downloadPath, $xmlPath);
        } else {
            File::copy($downloadPath, $xmlPath);
        }

        try {
            $xmltvChannelNames = $this->readXmltvChannelNames($xmlPath);
            $localMaps = $this->buildLocalChannelMaps($playlist);

            $this->log($logger, 'Canali XMLTV trovati: ' . count($xmltvChannelNames));
            $this->log($logger, 'Canali live locali trovati: ' . $localMaps['total']);

            $result = $this->importProgrammes(
                $playlist,
                $xmlPath,
                $xmltvChannelNames,
                $localMaps,
                $logger
            );

            DB::table('epg_programmes')
                ->where('playlist_id', $playlist->id)
                ->where('end_at', '<', now()->subDays(2))
                ->delete();

            if (Schema::hasColumn('playlists', 'last_epg_import_at')) {
                $playlist->forceFill([
                    'last_epg_import_at' => now(),
                ])->save();
            }

            return $result;
        } finally {
            File::delete($downloadPath);
            File::delete($xmlPath);
        }
    }

    private function readXmltvChannelNames(string $xmlPath): array
    {
        $reader = new XMLReader();

        if (!$reader->open($xmlPath, null, LIBXML_NONET | LIBXML_COMPACT | LIBXML_PARSEHUGE)) {
            throw new RuntimeException('Impossibile leggere XMLTV.');
        }

        $channels = [];

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->name === 'channel') {
                $id = trim((string) $reader->getAttribute('id'));

                if ($id === '') {
                    continue;
                }

                $xml = $reader->readOuterXML();

                if (!$xml) {
                    continue;
                }

                $node = new SimpleXMLElement($xml);

                $names = [];

                foreach ($node->{'display-name'} as $displayName) {
                    $name = trim((string) $displayName);

                    if ($name !== '') {
                        $names[] = $name;
                    }
                }

                $channels[$id] = array_values(array_unique($names));
            }
        }

        $reader->close();

        return $channels;
    }

    private function buildLocalChannelMaps(Playlist $playlist): array
    {
        $channels = Channel::query()
            ->where('playlist_id', $playlist->id)
            ->where('type', 'live')
            ->where('is_active', true)
            ->get();

        $byExternalId = [];
        $byName = [];

        foreach ($channels as $channel) {
            $nameKey = $this->normalizeName($channel->name);

            if ($nameKey !== '' && !isset($byName[$nameKey])) {
                $byName[$nameKey] = $channel->id;
            }

            $possibleIds = [];

            if (Schema::hasColumn('channels', 'tvg_id') && !empty($channel->tvg_id)) {
                $possibleIds[] = $channel->tvg_id;
            }

            if (Schema::hasColumn('channels', 'epg_channel_id') && !empty($channel->epg_channel_id)) {
                $possibleIds[] = $channel->epg_channel_id;
            }

            if (Schema::hasColumn('channels', 'stream_id') && !empty($channel->stream_id)) {
                $possibleIds[] = $channel->stream_id;
            }

            foreach ($possibleIds as $externalId) {
                $idKey = $this->normalizeExternalId($externalId);

                if ($idKey !== '' && !isset($byExternalId[$idKey])) {
                    $byExternalId[$idKey] = $channel->id;
                }
            }
        }

        return [
            'total' => $channels->count(),
            'by_external_id' => $byExternalId,
            'by_name' => $byName,
        ];
    }

    private function importProgrammes(
        Playlist $playlist,
        string $xmlPath,
        array $xmltvChannelNames,
        array $localMaps,
        ?callable $logger = null
    ): array {
        $reader = new XMLReader();

        if (!$reader->open($xmlPath, null, LIBXML_NONET | LIBXML_COMPACT | LIBXML_PARSEHUGE)) {
            throw new RuntimeException('Impossibile leggere i programmi XMLTV.');
        }

        $batch = [];
        $imported = 0;
        $skipped = 0;
        $matchedChannels = [];

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT || $reader->name !== 'programme') {
                continue;
            }

            $xmlChannelId = trim((string) $reader->getAttribute('channel'));
            $startRaw = trim((string) $reader->getAttribute('start'));
            $endRaw = trim((string) $reader->getAttribute('stop'));

            $displayNames = $xmltvChannelNames[$xmlChannelId] ?? [];
            $localChannelId = $this->matchLocalChannel($xmlChannelId, $displayNames, $localMaps);

            if (!$localChannelId) {
                $skipped++;
                continue;
            }

            $startAt = $this->parseXmltvTime($startRaw);
            $endAt = $this->parseXmltvTime($endRaw);

            if (!$startAt || !$endAt || $endAt->lessThanOrEqualTo($startAt)) {
                $skipped++;
                continue;
            }

            $xml = $reader->readOuterXML();

            if (!$xml) {
                $skipped++;
                continue;
            }

            $node = new SimpleXMLElement($xml);

            $title = trim((string) ($node->title ?? ''));
            $description = trim((string) ($node->desc ?? ''));
            $category = trim((string) ($node->category ?? ''));

            if ($title === '') {
                $title = 'Programma TV';
            }

            $matchedChannels[$localChannelId] = true;

            $batch[] = [
                'playlist_id' => $playlist->id,
                'channel_id' => $localChannelId,
                'epg_channel_id' => $xmlChannelId ?: null,
                'title' => Str::limit($title, 250, ''),
                'description' => $description !== '' ? $description : null,
                'category' => $category !== '' ? Str::limit($category, 250, '') : null,
                'start_at' => $startAt->format('Y-m-d H:i:s'),
                'end_at' => $endAt->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 500) {
                $this->upsertBatch($batch);
                $imported += count($batch);
                $batch = [];

                if ($imported % 5000 === 0) {
                    $this->log($logger, 'Programmi importati/aggiornati: ' . $imported);
                }
            }
        }

        $reader->close();

        if (count($batch) > 0) {
            $this->upsertBatch($batch);
            $imported += count($batch);
        }

        return [
            'playlist_id' => $playlist->id,
            'playlist_name' => $playlist->name ?? null,
            'skipped' => false,
            'reason' => null,
            'imported' => $imported,
            'skipped_programmes' => $skipped,
            'matched_channels' => count($matchedChannels),
        ];
    }

    private function upsertBatch(array $rows): void
    {
        DB::table('epg_programmes')->upsert(
            $rows,
            ['channel_id', 'start_at', 'end_at'],
            [
                'playlist_id',
                'epg_channel_id',
                'title',
                'description',
                'category',
                'updated_at',
            ]
        );
    }

    private function matchLocalChannel(string $xmlChannelId, array $displayNames, array $localMaps): ?int
    {
        $externalKey = $this->normalizeExternalId($xmlChannelId);

        if ($externalKey !== '' && isset($localMaps['by_external_id'][$externalKey])) {
            return $localMaps['by_external_id'][$externalKey];
        }

        $nameKeyFromId = $this->normalizeName($xmlChannelId);

        if ($nameKeyFromId !== '' && isset($localMaps['by_name'][$nameKeyFromId])) {
            return $localMaps['by_name'][$nameKeyFromId];
        }

        foreach ($displayNames as $displayName) {
            $nameKey = $this->normalizeName($displayName);

            if ($nameKey !== '' && isset($localMaps['by_name'][$nameKey])) {
                return $localMaps['by_name'][$nameKey];
            }
        }

        return null;
    }

    private function parseXmltvTime(?string $raw): ?Carbon
    {
        $raw = trim((string) $raw);

        if ($raw === '') {
            return null;
        }

        $raw = preg_replace('/\s+/', ' ', $raw);
        $timezone = config('app.timezone', 'Europe/Rome');

        try {
            if (preg_match('/^(\d{14})\s*([+-]\d{4})$/', $raw, $matches)) {
                return Carbon::createFromFormat('YmdHis O', $matches[1] . ' ' . $matches[2])
                    ->setTimezone($timezone);
            }

            if (preg_match('/^(\d{14})$/', $raw, $matches)) {
                return Carbon::createFromFormat('YmdHis', $matches[1], $timezone);
            }

            if (preg_match('/^(\d{14})/', $raw, $matches)) {
                return Carbon::createFromFormat('YmdHis', $matches[1], $timezone);
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    private function normalizeExternalId(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    private function normalizeName(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = Str::ascii($value);

        $value = preg_replace('/\b(fhd|fullhd|hd|uhd|sd|4k|hevc|h265|h264)\b/i', '', $value);
        $value = preg_replace('/\b(it|ita|italia|italy)\b/i', '', $value);
        $value = preg_replace('/[^a-z0-9]+/', '', $value);

        return trim($value);
    }

    private function isGzip(string $path, string $url): bool
    {
        $urlPath = strtolower((string) parse_url($url, PHP_URL_PATH));

        if (str_ends_with($urlPath, '.gz')) {
            return true;
        }

        $handle = fopen($path, 'rb');

        if (!$handle) {
            return false;
        }

        $bytes = fread($handle, 2);
        fclose($handle);

        return $bytes === "\x1f\x8b";
    }

    private function gunzipFile(string $source, string $destination): void
    {
        $input = gzopen($source, 'rb');

        if (!$input) {
            throw new RuntimeException('Impossibile aprire file EPG gzip.');
        }

        $output = fopen($destination, 'wb');

        if (!$output) {
            gzclose($input);
            throw new RuntimeException('Impossibile creare file XML EPG.');
        }

        while (!gzeof($input)) {
            fwrite($output, gzread($input, 1024 * 1024));
        }

        gzclose($input);
        fclose($output);
    }

    private function log(?callable $logger, string $message): void
    {
        if ($logger) {
            $logger($message);
        }
    }
}