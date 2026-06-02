<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Jobs\ImportPlaylistJob;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = Playlist::withCount('channels')->latest()->get();

        return view('customer.playlists.index', [
            'playlists' => $playlists,
            'deviceCode' => 'DEVICE-XXXX-XXXX',
        ]);
    }

    public function create()
    {
        return view('customer.playlists.create', [
            'playlist' => new Playlist(),
            'mode' => 'create',
            'deviceCode' => 'DEVICE-XXXX-XXXX',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePlaylist($request);

        $playlist = Playlist::create(array_merge($data, [
            'import_status' => 'queued',
            'import_message' => 'Importazione in coda.',
        ]));

        ImportPlaylistJob::dispatch($playlist->id);

        return redirect()
            ->route('customer.playlists.index')
            ->with('success', 'Playlist salvata. Importazione Live TV, Film e Serie avviata in background.');
    }

    public function edit(Playlist $playlist)
    {
        return view('customer.playlists.create', [
            'playlist' => $playlist,
            'mode' => 'edit',
            'deviceCode' => 'DEVICE-XXXX-XXXX',
        ]);
    }

    public function update(Request $request, Playlist $playlist)
    {
        $data = $this->validatePlaylist($request, $playlist);

        if ($data['type'] === 'xtream' && empty($data['xtream_password'])) {
            unset($data['xtream_password']);
        }

        $playlist->update(array_merge($data, [
            'import_status' => 'queued',
            'import_message' => 'Importazione in coda.',
        ]));

        ImportPlaylistJob::dispatch($playlist->id);

        return redirect()
            ->route('customer.playlists.index')
            ->with('success', 'Playlist modificata. Importazione Live TV, Film e Serie riavviata in background.');
    }

    public function import(Playlist $playlist)
    {
        $playlist->update([
            'import_status' => 'queued',
            'import_message' => 'Importazione in coda.',
        ]);

        ImportPlaylistJob::dispatch($playlist->id);

        return redirect()
            ->route('customer.playlists.index')
            ->with('success', 'Importazione playlist avviata.');
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
            $passwordRequired = !$playlist || !$playlist->exists || $playlist->type !== 'xtream';

            $request->validate([
                'xtream_host' => ['required', 'url'],
                'xtream_username' => ['required', 'string', 'max:255'],
                'xtream_password' => [$passwordRequired ? 'required' : 'nullable', 'string', 'max:255'],
            ]);

            $data['m3u_url'] = null;
        }

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
