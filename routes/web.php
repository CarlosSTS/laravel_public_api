<?php

use Illuminate\Support\Facades\Route;
use App\Services\ApiResponse;

// Fallback route for unmatched web requests
Route::fallback(function(){
    return ApiResponse::error('Endpoint not found', 404);
});
