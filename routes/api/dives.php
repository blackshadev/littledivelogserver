<?php

declare(strict_types=1);

use App\Http\Controllers\Api\DiveController;
use Illuminate\Support\Facades\Route;

Route::get('/_search', [DiveController::class, 'search']);
Route::get('/', [DiveController::class, 'index']);
Route::get('/{dive}', [DiveController::class, 'show']);
Route::get('/{dive}/samples', [DiveController::class, 'samples']);
Route::put('/{dive}', [DiveController::class, 'update']);
Route::post('/', [DiveController::class, 'store']);
Route::delete('/{dive}', [DiveController::class, 'delete']);
