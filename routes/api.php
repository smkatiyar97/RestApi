<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\PostController;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('login', [PassportAuthController::class, 'login'])->name('login');

Route::post('register', [PassportAuthController::class, 'register'])->name('register');
Route::post('login', [PassportAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('posts', PostController::class);
});

Route::post('password-reset', [PassportAuthController::class, 'sendPasswordResetToken']);
Route::post('password-reset/{token}', [PassportAuthController::class, 'resetPassword']);
Route::post('logout', [PassportAuthController::class, 'logout'])->middleware('auth:sanctum');
