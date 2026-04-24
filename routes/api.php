<?php

use App\Http\Controllers\AlumniProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PostController;
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
    Route::get('/alumni/profile/posts', [PostController::class, 'myPosts']);
    Route::get('/alumni/search', [AlumniProfileController::class, 'search']);
    Route::get('/alumni/{alumni}', [AlumniProfileController::class, 'view']);
    Route::get('/alumni/{alumni}/posts', [AlumniProfileController::class, 'posts']);
    Route::post('/alumni/{alumni}/follow', [AlumniProfileController::class, 'follow']);
    Route::post('/followers/{followRequestId}/accept', [AlumniProfileController::class, 'acceptFollowRequest']);
    Route::delete('/followers/{followRequestId}', [AlumniProfileController::class, 'declineFollowRequest']);
    Route::get('/contacts', [AlumniProfileController::class, 'contacts']);
    Route::get('/events', [EventController::class, 'index']);
    Route::put('/alumni/profile', [AlumniProfileController::class, 'update']);
    Route::post('/alumni/photo', [AlumniProfileController::class, 'uploadPhoto']);
    Route::post('/alumni/reset-password', [AuthController::class, 'resetAccountPassword']);
    Route::get('/perks', [PerkController::class, 'index']);
    Route::get('/notifications', [PostController::class, 'notifications']);
    Route::delete('/notifications/{notificationKey}', [PostController::class, 'dismissNotification']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/event-registrations', [EventRegistrationController::class, 'index']);
    Route::post('/events/{event}/registrations', [EventRegistrationController::class, 'store']);
    Route::get('/posts/{post}/comments', [PostController::class, 'comments']);
    Route::post('/posts/{post}/reactions', [PostController::class, 'react']);
    Route::post('/posts/{post}/reposts', [PostController::class, 'repost']);
    Route::post('/posts/{post}/comments', [PostController::class, 'comment']);
    Route::post('/upload-photo', [AlumniProfileController::class, 'uploadProfilePhoto']);

    // We will put things like creating posts, answering tracer studies,
    // and sending messages inside here later!
});