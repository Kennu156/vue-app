<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarkerController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('map', [MarkerController::class, 'index'])->name('map');
    Route::post('map/markers', [MarkerController::class, 'store'])->name('markers.store');
    Route::put('map/markers/{marker}', [MarkerController::class, 'update'])->name('markers.update');
    Route::delete('map/markers/{marker}', [MarkerController::class, 'destroy'])->name('markers.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';