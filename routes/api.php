<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(base_path('routes/api/auth.php'));
Route::prefix('countries')->group(base_path('routes/api/countries.php'));
Route::prefix('places')->middleware('auth.tuath.access')->group(base_path('routes/api/places.php'));
Route::prefix('dives')->middleware('auth.tuath.access')->group(base_path('routes/api/dives.php'));
Route::prefix('tags')->middleware('auth.tuath.access')->group(base_path('routes/api/tags.php'));
Route::prefix('buddies')->middleware('auth.tuath.access')->group(base_path('routes/api/buddies.php'));
Route::prefix('computers')->middleware('auth.tuath.access')->group(base_path('routes/api/computers.php'));
Route::prefix('user')->middleware('auth.tuath.access')->group(base_path('routes/api/users.php'));
