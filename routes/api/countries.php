<?php

use App\Http\Controllers\Api\CountryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CountryController::class, 'index']);
