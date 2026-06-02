<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Services\PlaylistImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ChannelController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('tipo', 'live');

        if (!in_array($type, ['live', 'film', 'serie'], true)) {
            $type = 'live';
        }

        $search = $request->query('search');
        $group = $request->query('group');

        $query = Channel::query()
            ->with('playlist')
            ->where('type', $type)
            ->where('is_active', true)
            ->whereHas('playlist', function ($q) {
                $q->where('is_active', true);
            });

        if ($type === 'serie') {
            $query->where(function ($q) {
                $q->where('is_series_parent', true)
                    ->orWhereNull('series_id');
            });
        }

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($group) {
            $query->where('group_title', $group);
        }

        $channels = $query
            ->orderBy('group_title')
            ->orderBy('name')
            ->simplePaginate(30)
            ->withQueryString();

        $groupsQuery = Channel::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->whereHas('playlist', function ($q) {
                $q->where('is_active', true);
            })
            ->whereNotNull('group_title')
            ->where('group_title', '!=', '');

        if ($type === 'serie') {
            $groupsQuery->where(function ($q) {
                $q->where('is_series_parent', true)
                    ->orWhereNull('series_id');
            });
        }

        $groups = $groupsQuery
            ->select('group_title', DB::raw('count(*) as total'))
            ->groupBy('group_title')
            ->orderBy('group_title')
            ->get();

        return view('customer.channels.index', [
            'channels' => $channels,
            'groups' => $groups,
            'type' => $type,
            'search' => $search,
            'group' => $group,
        ]);
    }

    public function show(Channel $channel, PlaylistImporter $importer)
    {
        abort_unless($channel->is_active, 404);
        abort_unless($channel->playlist && $channel->playlist->is_active, 404);

        if ($channel->type === 'serie' && $channel->is_series_parent) {
            $importError = null;
            $importedCount = 0;

            $episodesQuery = Channel::query()
                ->where('playlist_id', $channel->playlist_id)
                ->where('type', 'serie')
                ->where('is_series_parent', false)
                ->where('series_id', $channel->series_id)
                ->where('is_active', true);

            if (!$episodesQuery->exists()) {
                try {
                    $importedCount = $importer->importEpisodesForSeries($channel);
                } catch (Throwable $e) {
                    $importError = $e->getMessage();
                }
            }

            $episodes = Channel::query()
                ->where('playlist_id', $channel->playlist_id)
                ->where('type', 'serie')
                ->where('is_series_parent', false)
                ->where('series_id', $channel->series_id)
                ->where('is_active', true)
                ->orderBy('season_number')
                ->orderBy('episode_number')
                ->get()
                ->groupBy('season_number');

            return view('customer.channels.series', [
                'series' => $channel,
                'episodes' => $episodes,
                'importError' => $importError,
                'importedCount' => $importedCount,
            ]);
        }

        return view('customer.channels.show', [
            'channel' => $channel,
        ]);
    }
}
