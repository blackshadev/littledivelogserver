<?php

declare(strict_types=1);

use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TagController::class, 'index']);
Route::get('/{tag}', [TagController::class, 'show']);
Route::put('/{tag}', [TagController::class, 'update']);
Route::post('/', [TagController::class, 'store']);
