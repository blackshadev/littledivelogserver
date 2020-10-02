<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\ComputerController;

Route::get('/', [ComputerController::class, 'index']);
Route::get('/{computer}', [ComputerController::class, 'show']);
