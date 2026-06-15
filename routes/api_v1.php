<?php

use App\Http\Controllers\Api\V1\MainController;
use Illuminate\Support\Facades\Route;

// API Version 1 routes
Route::get('/status', [MainController::class, 'status']);

// List of categories
Route::get('/categories', [MainController::class, 'listCategories']);

// List of products
Route::get('/products', [MainController::class, 'listProducts']);

// List of movements
Route::get('/movements', [MainController::class, 'listMovements']);

// Get category by ID
Route::get('categories/{id}', [MainController::class, 'getCategory']);
