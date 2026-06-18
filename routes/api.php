<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refreshToken']);
});

Route::prefix('short-urls')->group(function (): void {
    Route::get('/{shortCode}', [ShortUrlController::class, 'redirect']);

    // Auth routes
    Route::middleware('auth:api')->group(function (): void {
        Route::get('/', [ShortUrlController::class, 'index']);
        Route::post('/', [ShortUrlController::class, 'store']);
        Route::put('/{id}', [ShortUrlController::class, 'update']);
        Route::delete('/{id}', [ShortUrlController::class, 'destroy']);
    });
});
