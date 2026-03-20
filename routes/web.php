<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
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

    Route::get('blog', [PostController::class, 'index'])->name('blog');
    Route::post('blog', [PostController::class, 'store'])->name('posts.store');
    Route::get('blog/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::put('blog/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('blog/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::post('blog/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';