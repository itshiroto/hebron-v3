<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DevotionController;
use App\Http\Controllers\MinistryController;
use App\Http\Controllers\DivisionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'show'])->name('home');
Route::get('/about', [AboutController::class, 'show'])->name('about-us');
Route::prefix('devotion')->group(function () {
    Route::get('/', [DevotionController::class, 'all']);
    Route::get('/{slug}', [DevotionController::class, 'single']);
});

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::get('/ministry', [MinistryController::class, 'show'])->name('ministry');
Route::get('/division', [DivisionController::class, 'show'])->name('division');

require __DIR__ . '/auth.php';
