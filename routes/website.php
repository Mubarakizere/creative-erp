<?php

use App\Http\Controllers\Website\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Website Routes
|--------------------------------------------------------------------------
|
| These routes are for the public-facing website pages.
| No authentication required.
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
