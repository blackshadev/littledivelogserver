<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;

Route::get('/sessions', [AuthController::class, 'listSessions']);
Route::post('/sessions', [AuthController::class, 'login']);
Route::delete('/sessions', [AuthController::class, 'logout']);
Route::get('/sessions/refresh', [AuthController::class, 'access']);
Route::post('/register', [AuthController::class, 'register']);
