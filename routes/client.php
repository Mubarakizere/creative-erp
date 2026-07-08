<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
|
| These routes are for the client portal. They require authentication
| and active user status. All routes are prefixed with /client.
|
*/

Route::middleware(['auth', 'check.status', 'track.activity'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', function () {
        return view('client.dashboard');
    })->name('dashboard');
});
