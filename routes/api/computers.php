<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ComputerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComputerController::class, 'index']);
Route::get('/{computer}', [ComputerController::class, 'show']);
Route::post('/', [ComputerController::class, 'store']);
