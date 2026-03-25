<?php

use App\Http\Controllers\VolkswagenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
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

    Route::get('shop', [ShopController::class, 'index'])->name('shop');
    Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('cart', [CartController::class, 'index'])->name('cart');
    Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('checkout/intent', [CheckoutController::class, 'createIntent'])->name('checkout.intent');
    Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('checkout/failed/{order}', [CheckoutController::class, 'failed'])->name('checkout.failed');


    Route::get('volkswagens', [VolkswagenController::class, 'index'])->name('volkswagens');
    Route::post('volkswagens', [VolkswagenController::class, 'store'])->name('volkswagens.store');
    Route::post('volkswagens/{volkswagen}', [VolkswagenController::class, 'update'])->name('volkswagens.update');
    Route::delete('volkswagens/{volkswagen}', [VolkswagenController::class, 'destroy'])->name('volkswagens.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';