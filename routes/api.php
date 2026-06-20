<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ShortUrlController as AdminShortUrlController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/admin-register', [AdminAuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refreshToken']);

    // Auth routes
    Route::middleware('auth:api')->group(function (): void {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('admin')->middleware(['auth:api', 'admin'])->group(function (): void {
    Route::prefix('short-urls')->group(function (): void {
        Route::get('/', [AdminShortUrlController::class, 'index']);
        Route::get('/{id}', [AdminShortUrlController::class, 'show']);
        Route::put('/{id}', [AdminShortUrlController::class, 'update']);
        Route::delete('/{id}', [AdminShortUrlController::class, 'destroy']);
    });
});

Route::prefix('short-urls')->group(function (): void {
    Route::get('/{shortCode}', [ShortUrlController::class, 'redirect']);

    // Auth routes
    Route::middleware('auth:api')->group(function (): void {
        Route::get('/', [ShortUrlController::class, 'index']);
        Route::post('/', [ShortUrlController::class, 'store']);
        Route::put('/{id}', [ShortUrlController::class, 'update']);
        Route::delete('/{id}', [ShortUrlController::class, 'destroy']);
        Route::get('/info/{id}', [ShortUrlController::class, 'show']);
    });
});
