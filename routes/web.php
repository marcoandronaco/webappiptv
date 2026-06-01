<?php

use App\Http\Controllers\Customer\PlaylistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('cliente')->name('customer.')->group(function () {
    Route::resource('playlists', PlaylistController::class);
});
