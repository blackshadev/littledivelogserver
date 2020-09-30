<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\CountryController;

Route::get('/', [CountryController::class, 'index']);
