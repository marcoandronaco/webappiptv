<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = Playlist::withCount('channels')->latest()->get();

        return view('customer.playlists.index', compact('playlists'));
    }

    public function create()
    {
        return view('customer.playlists.create', [
            'playlist' => new Playlist(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePlaylist($request);

        Playlist::create($data);

        return redirect()
            ->route('customer.playlists.index')
            ->with('success', 'Playlist aggiunta correttamente.');
    }

    public function edit(Playlist $playlist)
    {
        return view('customer.playlists.create', [
            'playlist' => $playlist,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Playlist $playlist)
    {
        $data = $this->validatePlaylist($request, $playlist);

        if ($data['type'] === 'xtream' && empty($data['xtream_password'])) {
            unset($data['xtream_password']);
        }

        $playlist->update($data);

        return redirect()
            ->route('customer.playlists.index')
            ->with('success', 'Playlist modificata correttamente.');
    }

    public function destroy(Playlist $playlist)
    {
        $playlist->delete();

        return redirect()
            ->route('customer.playlists.index')
            ->with('success', 'Playlist eliminata correttamente.');
    }

    private function validatePlaylist(Request $request, ?Playlist $playlist = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['m3u', 'xtream'])],

            'm3u_url' => ['nullable', 'url'],

            'xtream_host' => ['nullable', 'url'],
            'xtream_username' => ['nullable', 'string', 'max:255'],
            'xtream_password' => ['nullable', 'string', 'max:255'],

            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($data['type'] === 'm3u') {
            $request->validate([
                'm3u_url' => ['required', 'url'],
            ]);

            $data['xtream_host'] = null;
            $data['xtream_username'] = null;
            $data['xtream_password'] = null;
        }

        if ($data['type'] === 'xtream') {
            $request->validate([
                'xtream_host' => ['required', 'url'],
                'xtream_username' => ['required', 'string', 'max:255'],
                'xtream_password' => [$playlist ? 'nullable' : 'required', 'string', 'max:255'],
            ]);

            $data['m3u_url'] = null;
        }

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
