<?php

use App\Services\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require base_path('routes/api_v1.php');
});

Route::prefix('v2')->group(function () {
    require base_path('routes/api_v2.php');
});


// Fallback route for unmatched API requests
Route::fallback(function () {
    return ApiResponse::error('Endpoint not found', 404);
});
