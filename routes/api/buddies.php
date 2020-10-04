<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\BuddyController;

Route::get('/', [BuddyController::class, 'index']);
Route::get('/{buddy}', [BuddyController::class, 'show']);
Route::put('/{buddy}', [BuddyController::class, 'update']);
