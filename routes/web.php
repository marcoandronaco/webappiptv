<?php

use App\Http\Controllers\Customer\ChannelController;
use App\Http\Controllers\Customer\PlaylistController;
use App\Http\Controllers\Customer\TvPlayerController;
use App\Http\Controllers\StreamController;
use App\Models\Playlist;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $currentPlaylist = Playlist::query()
        ->where('is_active', true)
        ->latest('updated_at')
        ->first();

    return view('welcome', [
        'currentPlaylist' => $currentPlaylist,
    ]);
});

Route::get('/stream/{channel}/index.m3u8', [StreamController::class, 'playlist'])
    ->name('stream.hls');

Route::get('/stream/{channel}/{segment}', [StreamController::class, 'segment'])
    ->where('segment', '.*')
    ->name('stream.segment');

Route::prefix('cliente')->name('customer.')->group(function () {
    Route::post('/playlists/{playlist}/import', [PlaylistController::class, 'import'])
        ->name('playlists.import');

    Route::resource('playlists', PlaylistController::class);

     Route::get('/player', [ChannelController::class, 'index'])
        ->name('channels.index');

    Route::get('/player/{channel}', [ChannelController::class, 'show'])
        ->name('channels.show');
    
    Route::get('/tv-player', [TvPlayerController::class, 'index'])
        ->name('tv-player');
});


