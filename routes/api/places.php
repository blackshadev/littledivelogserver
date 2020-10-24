<?php

declare(strict_types=1);

use App\Http\Controllers\Api\PlaceController;

Route::get('/', [PlaceController::class, 'index']);
Route::get('/{country}', [PlaceController::class, 'indexForCountry']);
