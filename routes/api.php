<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\PostController;


Route::get('login', [PassportAuthController::class, 'check_login'])->name('login');

Route::post('register', [PassportAuthController::class, 'register'])->name('register');
Route::post('login', [PassportAuthController::class, 'login']);
Route::post('reset-password', [PassportAuthController::class, 'sendPasswordResetToken']);
Route::post('reset-password/{token}', [PassportAuthController::class, 'resetPassword']);
Route::post('logout', [PassportAuthController::class, 'logout'])->middleware('auth:api');

Route::middleware('auth:api')->group( function () {
    Route::resource('posts', PostController::class);
});
