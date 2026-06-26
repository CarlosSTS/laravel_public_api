<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\MovementResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Movement;
use App\Models\Product;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function status()
    {
        return ApiResponse::success([
            'currentStatusString' => 'API is running',
            'serverDate' => now()->toDateString(),
            'serverTime' => now()->toTimeString(),
            'serverTimestamp' => now()->timestamp,
            'serverTimezone' => now()->timezoneName,
            'apiVersion' => 'v1'
        ]);
    }

    public function listCategories()
    {
        $perPage = request()->query('per_page', 15); // Default to 15 if not provided
        $categories = Category::paginate($perPage);

        return ApiResponse::success([
            'categories' => CategoryResource::collection($categories),
            'pagination' => [
                'currentPage' => $categories->currentPage(),
                'lastPage' => $categories->lastPage(),
                'perPage' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ]);
    }

    public function listProducts()
    {
        $perPage = request()->query('per_page', 15); // Default to 15 if not provided
        $products = Product::with('category')->paginate($perPage);
        return ApiResponse::success([
            'products' => ProductResource::collection($products),
            'pagination' => [
                'currentPage' => $products->currentPage(),
                'lastPage' => $products->lastPage(),
                'perPage' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function listMovements()
    {
        $perPage = request()->query('per_page', 15); // Default to 15 if not provided
        $movements = Movement::with('product.category')->paginate($perPage);
        return ApiResponse::success([
            'movements' => MovementResource::collection($movements),
            'pagination' => [
                'currentPage' => $movements->currentPage(),
                'lastPage' => $movements->lastPage(),
                'perPage' => $movements->perPage(),
                'total' => $movements->total(),
            ],
        ]);
    }

    public function getCategory($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::error("Category with ID {$id} not found.", 404);
        }

        return ApiResponse::success([
            'category' => new CategoryResource($category)
        ]);
    }

    public function getProduct($id)
    {
        $product = Product::with('category')->find($id);
        if (!$product) {
            return ApiResponse::error("Product with ID {$id} not found.", 404);
        }

        return ApiResponse::success([
            'product' => new ProductResource($product)
        ]);
    }

    public function getProductsByCategory($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::error("Category with ID {$id} not found.", 404);
        }

        $products = Product::where('category_id', $id)
            ->get()
            ->toResourceCollection(ProductResource::class)
            ->resolve();
        // resolve() é necessário para transformar a coleção de recursos em um array simples,
        // caso contrário, o Laravel tentará serializar os recursos como objetos JSON,
        // o que pode resultar em uma estrutura de resposta mais complexa do que o esperado.

        // array_map é usado para remover a chave 'category' de cada produto, pois já estamos retornando a categoria separadamente.
        $products = array_map(function ($product) {
            unset($product['category']); // Remove a chave 'category' do produto e vira um array simples e puro
            return $product;
        }, $products);

        return ApiResponse::success([
            'category' => new CategoryResource($category),
            'products' => $products,
            'totalProducts' => count($products),
        ]);


        // return ApiResponse::success([
        //     'category' => new CategoryResource($category),
        //     'products' => ProductResource::collection($products),
        //     'totalProducts' => $products->count(),
        // ]);
    }

    public function listMovementsOrdered($field, $direction)
    {
        // Validate the field and direction
        $validFields = ['id', 'product_id', 'quantity', 'movement_type', 'created_at'];
        $validDirections = ['asc', 'desc'];

        if (!in_array($field, $validFields)) {
            return ApiResponse::error("Invalid field: {$field}. Valid fields are: " . implode(', ', $validFields), 400);
        }

        if (!in_array($direction, $validDirections)) {
            return ApiResponse::error("Invalid direction: {$direction}. Valid directions are: " . implode(', ', $validDirections), 400);
        }

        $perPage = request()->query('per_page', 15); // Default to 15 if not provided
        $movements = Movement::with('product.category')
            ->orderBy($field, $direction)
            ->paginate($perPage);

        return ApiResponse::success([
            'movements' => MovementResource::collection($movements),
            'pagination' => [
                'currentPage' => $movements->currentPage(),
                'lastPage' => $movements->lastPage(),
                'perPage' => $movements->perPage(),
                'total' => $movements->total(),
            ],
        ]);
    }

    public function createCategory(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            // Ensure the name is required, a string, has a maximum length of 255 characters and is unique in the categories table
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string', // Description is optional and can be null, but if provided, it must be a string
        ]);

        // Verify if a category with the same name already exists
        // This check is redundant due to the unique validation rule above, but it's kept here for demonstration purposes.

        // if (Category::where('name', $validatedData['name'])->exists()) {
        // return ApiResponse::error("A category with the name '{$validatedData['name']}' already exists.", 409);
        // }

        // Create a new category
        $category = Category::create($validatedData);

        return ApiResponse::success(
            new CategoryResource($category),
            "Category created successfully.",
            201
        );
    }

    public function createProduct(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Create a new product
        $product = Product::create($validatedData);

        return ApiResponse::success(
            new ProductResource($product),
            "Product created successfully.",
            201
        );
    }

    public function createMovement(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'movement_type' => 'required|in:in,out', // Assuming movement_type can be either 'in' or 'out'
        ]);

        // Create a new movement
        $movement = Movement::create($validatedData);

        return ApiResponse::success(
            new MovementResource($movement),
            "Movement created successfully.",
            201
        );
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::error("Category with ID {$id} not found.", 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        // Update the category
        $category->update($validatedData);

        return ApiResponse::success(
            new CategoryResource($category),
            "Category updated successfully."
        );
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ApiResponse::error("Product with ID {$id} not found.", 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:products,name,' . $id,
            'description' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        // Update the product
        $product->update($validatedData);

        return ApiResponse::success(
            new ProductResource($product),
            "Product updated successfully."
        );
    }

    public function updateMovement(Request $request, $id)
    {
        $movement = Movement::find($id);
        if (!$movement) {
            return ApiResponse::error("Movement with ID {$id} not found.", 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'movement_type' => 'sometimes|required|in:in,out', // Assuming movement_type can be either 'in' or 'out'
        ]);

        // Update the movement
        $movement->update($validatedData);

        return ApiResponse::success(
            new MovementResource($movement),
            "Movement updated successfully."
        );
    }
}
