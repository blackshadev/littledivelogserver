<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\TagController;

Route::get('/', [TagController::class, 'index']);
Route::get('/{tag}', [TagController::class, 'show']);
