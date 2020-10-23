<?php

use App\Http\Controllers\Api\UploaderPackageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UploaderPackageController::class, 'index']);
Route::get('/latest/{platform}', [UploaderPackageController::class, 'latest']);
Route::get('/{version}/{platform}', [UploaderPackageController::class, 'download']);
