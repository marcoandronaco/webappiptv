<?php

use App\Http\Controllers\Customer\ChannelController;
use App\Http\Controllers\Customer\PlaylistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'deviceCode' => 'DEVICE-84F91A7C20D3',
    ]);
});

Route::prefix('cliente')->name('customer.')->group(function () {
    Route::post('/playlists/{playlist}/import', [PlaylistController::class, 'import'])
        ->name('playlists.import');
        
    Route::resource('playlists', PlaylistController::class);

     Route::get('/player', [ChannelController::class, 'index'])
        ->name('channels.index');

    Route::get('/player/{channel}', [ChannelController::class, 'show'])
        ->name('channels.show');
});


