<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:5,1'])->prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
});