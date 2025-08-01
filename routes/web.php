<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\DevotionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::get('/about', [AboutController::class, 'show'])->name('about-us');
Route::prefix('devotion')->group(function () {
    Route::get('/{slug}', [ DevotionController::class, 'single' ]);
});

require __DIR__.'/auth.php';
