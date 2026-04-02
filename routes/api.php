<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\MedicalRecord\MedicalRecordController;
use App\Http\Controllers\Api\MedicalRecord\AccessGrantController;
use Illuminate\Support\Facades\Route;

// Route::middleware(['throttle:5,1'])->prefix('auth')->group(function () {
//     Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
//     Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
// });

// Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('auth')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
//     Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
// });

// ============================================================
// ROUTES PUBLIQUES
// ============================================================
Route::middleware('throttle:5,1')
    ->prefix('auth')
    ->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/login',    [AuthController::class, 'login'])->name('auth.login');
    });

// ============================================================
// ROUTES PROTÉGÉES
// ============================================================
Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::get('/me',     [AuthController::class, 'me'])->name('auth.me');
        });

        // Dossiers médicaux
        Route::prefix('records')->group(function () {
            Route::get('/',          [MedicalRecordController::class, 'index'])->name('records.index');
            Route::post('/',         [MedicalRecordController::class, 'store'])->name('records.store');
            Route::get('/{id}',      [MedicalRecordController::class, 'show'])->name('records.show');
            Route::delete('/{record}', [MedicalRecordController::class, 'destroy'])->name('records.destroy');

            // Accès partagés — imbriqués sous un dossier
            Route::get('/{record}/grants',          [AccessGrantController::class, 'index'])->name('grants.index');
            Route::post('/{record}/grants',         [AccessGrantController::class, 'store'])->name('grants.store');
            Route::delete('/grants/{grant}/revoke', [AccessGrantController::class, 'revoke'])->name('grants.revoke');
        });
    });