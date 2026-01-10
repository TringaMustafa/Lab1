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

    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (JWT + ADMIN ONLY)
    |--------------------------------------------------------------------------
    */
    Route::middleware('is_admin')->group(function () {

        // Dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        // CRUD routes (i shtojmë këtu)
        // Route::apiResource('menus', MenuController::class);
        // Route::apiResource('orders', OrderController::class);
        // Route::apiResource('tables', TableController::class);
        // Route::apiResource('reservations', ReservationController::class);
    });
});
