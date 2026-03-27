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
Route::get('/public-menus', [MenuController::class, 'index']);

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

    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/tables', [TableController::class, 'index']);
    Route::get('/tables/available', [TableController::class, 'available']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);
    Route::get('/my-reservations', [ReservationController::class, 'myReservations']);
    Route::put('/my-reservations/{reservation}', [ReservationController::class, 'updateMine']);    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (JWT + ADMIN ONLY)
    |--------------------------------------------------------------------------
    */
    Route::middleware('is_admin')->group(function () {

        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        Route::apiResource('menus', MenuController::class);
        Route::apiResource('tables', TableController::class)->except(['index']);
        Route::apiResource('reservations', ReservationController::class)->except(['store']);
        Route::apiResource('orders', OrderController::class)->except(['store']);
    });
});