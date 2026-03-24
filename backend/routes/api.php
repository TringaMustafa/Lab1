<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;

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
    | USER ROUTES
    |--------------------------------------------------------------------------
    */

    // ✅ USER - Place order
    Route::post('/orders', [OrderController::class, 'store']);

    // ✅ USER - Read tables (needed for Tables.vue)
    Route::get('/tables', [TableController::class, 'index']);

    // ✅ USER - Check available tables for date/time/guests
    Route::get('/tables/available', [TableController::class, 'available']);

    // ✅ USER - Create reservation
    Route::post('/reservations', [ReservationController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (JWT + ADMIN ONLY)
    |--------------------------------------------------------------------------
    */
    Route::middleware('is_admin')->group(function () {

        // Dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        // ✅ CRUDs (Admin)
        Route::apiResource('menus', MenuController::class);
        Route::apiResource('tables', TableController::class)->except(['index']); 
        // 👆 index është për user; admin prap mundet me i pa me /tables nga user route

        Route::apiResource('reservations', ReservationController::class)->except(['store']);
        // 👆 store është për user; admin menaxhon list/show/update/delete

        // ✅ Orders admin (list/show/update/delete)
        Route::apiResource('orders', OrderController::class)->except(['store']);
    });
});
