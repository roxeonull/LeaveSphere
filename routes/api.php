<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes (Mobile App - Employee)
|--------------------------------------------------------------------------
| All routes are prefixed with /api automatically.
| Protected routes require a Bearer token obtained from POST /api/login.
*/

// Public
Route::post('/login', [AuthController::class, 'login']);

// Protected (Sanctum token required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/home', [HomeController::class, 'index']);

    Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
    Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
    Route::get('/leave-requests/{id}', [LeaveRequestController::class, 'show']);
    Route::delete('/leave-requests/{id}', [LeaveRequestController::class, 'destroy']);

    Route::get('/calendar', [CalendarController::class, 'index']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
});
