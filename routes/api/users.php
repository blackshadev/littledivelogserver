<?php

declare(strict_types=1);

use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/profile', [UserProfileController::class, 'show']);
Route::get('/profile/equipment', [UserProfileController::class, 'equipment']);
Route::put('/profile/equipment', [UserProfileController::class, 'updateEquipment']);
Route::put('/profile', [UserProfileController::class, 'update']);
Route::put('/profile/password', [UserProfileController::class, 'updatePassword']);
