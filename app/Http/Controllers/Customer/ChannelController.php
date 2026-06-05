<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Services\PlaylistImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ChannelController extends Controller
{
    public function index(Request $request, PlaylistImporter $importer)
    {
        $type = $request->query('tipo', 'live');

        if (!in_array($type, ['live', 'film', 'serie'], true)) {
            $type = 'live';
        }

        $category = $request->query('category');
        $categorySearch = $request->query('category_search');
        $channelSearch = $request->query('channel_search');
        $selectedChannelId = $request->query('channel');
        $selectedEpisodeId = $request->query('episode');
        $play = $request->boolean('play');


        $baseQuery = Channel::query()
            ->with('playlist')
            ->where('type', $type)
            ->where('is_active', true)
            ->whereHas('playlist', function ($q) {
                $q->where('is_active', true);
            });

        if ($type === 'serie') {
            $baseQuery->where(function ($q) {
                $q->where('is_series_parent', true)
                    ->orWhereNull('series_id');
            });
        }

        $totalChannels = (clone $baseQuery)->count();

        $categoriesQuery = (clone $baseQuery)
            ->whereNotNull('group_title')
            ->where('group_title', '!=', '');

        if ($categorySearch) {
            $categoriesQuery->where('group_title', 'like', '%' . $categorySearch . '%');
        }

        $categories = $categoriesQuery
            ->select(
                'group_title',
                DB::raw('count(*) as total'),
                DB::raw('MIN(id) as first_channel_id')
            )
            ->groupBy('group_title')
            ->orderBy('first_channel_id')
            ->get();

        $channelsQuery = clone $baseQuery;

        if ($category) {
            $channelsQuery->where('group_title', $category);
        }

        if ($channelSearch) {
            $channelsQuery->where('name', 'like', '%' . $channelSearch . '%');
        }

        $channels = $channelsQuery
            ->orderBy('id')
            ->simplePaginate($type === 'live' ? 90 : 60)
            ->withQueryString();

        $selectedChannel = null;

        if ($selectedChannelId) {
            $selectedChannel = Channel::query()
                ->with('playlist')
                ->where('is_active', true)
                ->where('type', $type)
                ->find($selectedChannelId);
        }

        if ($type === 'live' && !$selectedChannel && $channels->count()) {
            $selectedChannel = $channels->first();
        }

        $playableChannel = null;
        $selectedSeries = null;
        $episodesBySeason = collect();
        $seriesImportError = null;
        $seriesImportedCount = 0;

        if ($type === 'live') {
            $playableChannel = $selectedChannel;
        }

        if ($type === 'film') {
            if ($selectedChannel && $play) {
                $playableChannel = $selectedChannel;
            }
        }

        if ($type === 'serie' && $selectedChannel) {
            if ($selectedChannel->is_series_parent) {
                $selectedSeries = $selectedChannel;
            } elseif ($selectedChannel->series_id) {
                $selectedSeries = Channel::query()
                    ->where('playlist_id', $selectedChannel->playlist_id)
                    ->where('type', 'serie')
                    ->where('is_series_parent', true)
                    ->where('series_id', $selectedChannel->series_id)
                    ->first();

                if ($play) {
                    $playableChannel = $selectedChannel;
                }
            } else {
                if ($play) {
                    $playableChannel = $selectedChannel;
                }
            }

            if ($selectedSeries && $selectedSeries->series_id) {
                $episodesExist = Channel::query()
                    ->where('playlist_id', $selectedSeries->playlist_id)
                    ->where('type', 'serie')
                    ->where('is_series_parent', false)
                    ->where('series_id', $selectedSeries->series_id)
                    ->where('is_active', true)
                    ->exists();

                if (!$episodesExist) {
                    try {
                        $seriesImportedCount = $importer->importEpisodesForSeries($selectedSeries);
                    } catch (Throwable $e) {
                        $seriesImportError = $e->getMessage();
                    }
                }

                $episodesBySeason = Channel::query()
                    ->where('playlist_id', $selectedSeries->playlist_id)
                    ->where('type', 'serie')
                    ->where('is_series_parent', false)
                    ->where('series_id', $selectedSeries->series_id)
                    ->where('is_active', true)
                    ->orderBy('season_number')
                    ->orderBy('episode_number')
                    ->orderBy('name')
                    ->get()
                    ->groupBy(function ($episode) {
                        return $episode->season_number ?: 1;
                    });
            }

            if ($selectedEpisodeId) {
                $episode = Channel::query()
                    ->where('type', 'serie')
                    ->where('is_series_parent', false)
                    ->where('is_active', true)
                    ->find($selectedEpisodeId);

                if ($episode) {
                    $playableChannel = $episode;
                }
            }
        }

        $epgProgrammes = collect();

        if ($type === 'live' && $playableChannel && Schema::hasTable('epg_programmes')) {
            $epgProgrammes = DB::table('epg_programmes')
                ->where('channel_id', $playableChannel->id)
                ->where('end_at', '>=', now()->subHours(2))
                ->orderBy('start_at')
                ->limit(14)
                ->get();
        }

        return view('customer.channels.index', [
            'type' => $type,
            'category' => $category,
            'categorySearch' => $categorySearch,
            'channelSearch' => $channelSearch,
            'categories' => $categories,
            'channels' => $channels,
            'selectedChannel' => $selectedChannel,
            'playableChannel' => $playableChannel,
            'selectedSeries' => $selectedSeries,
            'episodesBySeason' => $episodesBySeason,
            'seriesImportError' => $seriesImportError,
            'seriesImportedCount' => $seriesImportedCount,
            'epgProgrammes' => $epgProgrammes,
            'totalChannels' => $totalChannels,
            'play' => $play,
        ]);
    }

    public function show(Channel $channel, PlaylistImporter $importer)
    {
        abort_unless($channel->is_active, 404);
        abort_unless($channel->playlist && $channel->playlist->is_active, 404);

        return view('customer.channels.show', [
            'channel' => $channel,
        ]);
    }
}
