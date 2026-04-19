<?php

use App\Http\Controllers\AlumniProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/alumni/profile', [AlumniProfileController::class, 'show']);
    Route::put('/alumni/profile', [AlumniProfileController::class, 'update']);
    Route::post('/alumni/photo', [AlumniProfileController::class, 'uploadPhoto']);
    Route::post('/alumni/reset-password', [AuthController::class, 'resetAccountPassword']);
    Route::get('/perks', [PerkController::class, 'index']);

    // We will put things like creating posts, answering tracer studies,
    // and sending messages inside here later!
});