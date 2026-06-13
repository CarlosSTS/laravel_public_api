<?php

use App\Http\Controllers\Api\V2\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/status', [MainController::class, 'status']);
