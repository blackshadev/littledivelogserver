<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\DiveController;

Route::get('/', [DiveController::class, 'index']);
Route::get('/{dive}', [DiveController::class, 'show']);
Route::get('/{dive}/samples', [DiveController::class, 'samples']);
