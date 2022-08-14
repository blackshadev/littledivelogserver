<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserEmailVerifyController;
use Illuminate\Support\Facades\Route;

Route::get('/sessions', [AuthController::class, 'listSessions']);
Route::post('/sessions', [AuthController::class, 'login']);
Route::delete('/sessions', [AuthController::class, 'logout']);
Route::delete('/sessions/{refreshToken}', [AuthController::class, 'deleteSession']);
Route::get('/sessions/refresh', [AuthController::class, 'access']);

Route::middleware(env('APP_ENV') !== 'local' ? ['throttle:4,10'] : [])->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/verify/{id}/{hash}', [UserEmailVerifyController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');
    Route::post('/verify/resend', [UserEmailVerifyController::class, 'sendVerificationEmail'])->name('verification.send');
});
