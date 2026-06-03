<?php

use App\Http\Controllers\StreamController;
use App\Http\Controllers\Customer\ChannelController;
use App\Http\Controllers\Customer\PlaylistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\TvPlayerController;

Route::get('/', function () {
    return view('welcome', [
        'deviceCode' => 'DEVICE-84F91A7C20D3',
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


