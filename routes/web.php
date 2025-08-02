<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DevotionController;
use App\Http\Controllers\MinistryController;
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
    Route::get('/', [ DevotionController::class, 'all' ]);
    Route::get('/{slug}', [ DevotionController::class, 'single' ]);
});

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::get('/ministry', [MinistryController::class, 'show'])->name('ministry');

require __DIR__.'/auth.php';
