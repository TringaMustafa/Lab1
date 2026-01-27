<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (JWT)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ✅ USER (logged) - Place order
    Route::post('/orders', [OrderController::class, 'store']);

    Route::middleware('is_admin')->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        // ✅ admin orders list (ma vonë)
        // Route::get('/admin/orders', [OrderController::class, 'index']);
    });
});

