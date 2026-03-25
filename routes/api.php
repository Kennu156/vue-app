<?php

use App\Http\Controllers\Api\VolkswagenApiController;
use Illuminate\Support\Facades\Route;

Route::get('volkswagens', [VolkswagenApiController::class, 'index']);
Route::get('volkswagens/models', [VolkswagenApiController::class, 'models']);
Route::get('volkswagens/{volkswagen}', [VolkswagenApiController::class, 'show']);