<?php

use Illuminate\Support\Facades\Route;

Route::get('/sessions', [\App\Http\Controllers\Api\AuthController::class, 'listSessions']);
Route::post('/sessions', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::delete('/sessions', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
Route::get('/sessions/refresh', [\App\Http\Controllers\Api\AuthController::class, 'access']);
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
