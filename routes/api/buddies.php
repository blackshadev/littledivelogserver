<?php

declare(strict_types=1);

use App\Http\Controllers\Api\BuddyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BuddyController::class, 'index']);
Route::get('/{buddy}', [BuddyController::class, 'show']);
Route::put('/{buddy}', [BuddyController::class, 'update']);
Route::post('/', [BuddyController::class, 'store']);
Route::delete('/{buddy}', [BuddyController::class, 'delete']);
