<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(base_path('routes/api/auth.php'));
Route::prefix('countries')->group(base_path('routes/api/countries.php'));

Route::middleware(['auth.tuath.access', 'verified'])->group(function (): void {
    Route::prefix('places')->group(base_path('routes/api/places.php'));
    Route::prefix('dives')->group(base_path('routes/api/dives.php'));
    Route::prefix('tags')->group(base_path('routes/api/tags.php'));
    Route::prefix('buddies')->group(base_path('routes/api/buddies.php'));
    Route::prefix('computers')->group(base_path('routes/api/computers.php'));
    Route::prefix('user')->group(base_path('routes/api/users.php'));
});
