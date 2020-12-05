<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/sessions', [AuthController::class, 'listSessions']);
Route::post('/sessions', [AuthController::class, 'login']);
Route::delete('/sessions', [AuthController::class, 'logout']);
Route::delete('/sessions/{refreshToken}', [AuthController::class, 'deleteSession']);
Route::get('/sessions/refresh', [AuthController::class, 'access']);
Route::post('/register', [AuthController::class, 'register']);
