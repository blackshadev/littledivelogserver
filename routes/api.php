<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(base_path('routes/api/auth.php'));
Route::prefix('countries')->group(base_path('routes/api/countries.php'));
Route::prefix('dives')->middleware('auth.tuath.access')->group(base_path('routes/api/dives.php'));
Route::prefix('tags')->middleware('auth.tuath.access')->group(base_path('routes/api/tags.php'));
Route::prefix('buddies')->middleware('auth.tuath.access')->group(base_path('routes/api/buddies.php'));
