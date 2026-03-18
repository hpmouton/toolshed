<?php

use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\DepotController;
use App\Http\Controllers\Api\V1\ToolController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FR-15 — Versioned REST API (v1)
|--------------------------------------------------------------------------
| Authenticated via bearer tokens (Laravel Sanctum).
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Tools
    Route::get('tools', [ToolController::class, 'index']);
    Route::get('tools/{tool}', [ToolController::class, 'show']);

    // Depots
    Route::get('depots', [DepotController::class, 'index']);
    Route::get('depots/{depot}/tools', [DepotController::class, 'tools']);

    // Bookings
    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::patch('bookings/{booking}', [BookingController::class, 'update']);
});
