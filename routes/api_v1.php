<?php

use App\Http\Controllers\Api\V1\AuthController;
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
Route::get('/categories/{id}', [MainController::class, 'getCategory']);
// Get product by ID
Route::get('/products/{id}', [MainController::class, 'getProduct']);

// Get all products in a category
Route::get('/categories/{id}/products', [MainController::class, 'getProductsByCategory']);

// Ordered movements by field and direction
Route::get('/movements/ordered/{field}/{direction}', [MainController::class, 'listMovementsOrdered']);

// Create Category
Route::post('/categories/create', [MainController::class, 'createCategory']);

// Create Product
Route::post('/products/create', [MainController::class, 'createProduct']);

// Create Movement
Route::post('/movements/create', [MainController::class, 'createMovement']);

// Update Category
Route::put('/categories/{id}/update', [MainController::class, 'updateCategory']);

// Update Product
Route::put('/products/{id}/update', [MainController::class, 'updateProduct']);

// Update Movement
Route::put('/movements/{id}/update', [MainController::class, 'updateMovement']);

// Delete Category
Route::delete('/categories/{id}/delete', [MainController::class, 'deleteCategory']);

// Delete Product
Route::delete('/products/{id}/delete', [MainController::class, 'deleteProduct']);

// Delete Movement
Route::delete('/movements/{id}/delete', [MainController::class, 'deleteMovement']);


// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
