<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

        $groups = Channel::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->whereHas('playlist', function ($q) {
                $q->where('is_active', true);
            })
            ->whereNotNull('group_title')
            ->where('group_title', '!=', '')
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

    public function show(Channel $channel)
    {
        abort_unless($channel->is_active, 404);
        abort_unless($channel->playlist && $channel->playlist->is_active, 404);

        return view('customer.channels.show', [
            'channel' => $channel,
        ]);
    }
}
