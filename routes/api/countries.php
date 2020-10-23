<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CountryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CountryController::class, 'index']);
