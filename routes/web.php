<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// The main dashboard HTML page
Route::get('/dashboard', [AnalyticsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// All routes inside here require the user to be logged in
Route::middleware('auth')->group(function () {
    
    // Default profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---------------------------------------------------------
    // YOUR NEW ANALYTICS API ROUTES
    // We add the 'api/' prefix here so your Javascript can find them
    // ---------------------------------------------------------
    Route::prefix('api')->group(function () {
        Route::get('/stats', [AnalyticsController::class, 'stats']);
        Route::get('/delay/equipment', [AnalyticsController::class, 'equipment']);
        Route::get('/delay/sub-equipment', [AnalyticsController::class, 'subEquipment']);
        Route::get('/delay/shop', [AnalyticsController::class, 'shop']);
        Route::get('/delay/monthly', [AnalyticsController::class, 'monthly']);
        Route::get('/delay/cumulative', [AnalyticsController::class, 'cumulative']);
        Route::get('/delay/continue', [AnalyticsController::class, 'continueStats']);
        Route::get('/demo/stats', [AnalyticsController::class, 'demoStats']);
    });

});

require __DIR__.'/auth.php';