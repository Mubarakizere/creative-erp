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
Route::get('/expertise', [HomeController::class, 'expertise'])->name('expertise');
Route::get('/projects', [HomeController::class, 'projects'])->name('projects');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
