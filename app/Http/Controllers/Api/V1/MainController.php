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
        $movements = Movement::with(['product', 'category'])->paginate($perPage);
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
}
