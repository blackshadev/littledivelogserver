<?php

declare(strict_types=1);

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/profile', [UserController::class, 'profile']);
