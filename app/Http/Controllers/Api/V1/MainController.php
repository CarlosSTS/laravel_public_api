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
        // Trás todos os dados e retorna em formato JSON
        // $categories = Category::all();

        // $categories = Category::all(['id', 'name', 'description']);

        // return ApiResponse::success([
        //     'categories' => $categories,
        //     'totalCategories' => $categories->count()
        // ]);

        // API RESOURCE
        $categories = Category::all();
        return ApiResponse::success([
            // 'categories' => CategoryResource::collection($categories),
            'categories' => $categories->toResourceCollection(CategoryResource::class),
            'totalCategories' => $categories->count()
        ]);
    }

    public function listProducts()
    {
        // Busca os produtos carregando apenas os campos necessários
        // O campo category_id é obrigatório para que o Eloquent consiga resolver a relação
        // $products = Product::with('category:id,name,description')
        //     ->select('name', 'description', 'category_id')
        //     ->get()
        //     ->map(function ($product) {

        //         // Oculta category_id apenas na resposta
        //         // $product->makeHidden('category_id');

        //         return [
        //             'name' => $product->name,
        //             'description' => $product->description,
        //             'category' => $product->category
        //                 ? [
        //                     'id' => $product->category->id,
        //                     'name' => ucfirst($product->category->name),
        //                     'description' => $product->category->description,
        //                 ]
        //                 : null,
        //         ];
        //     });

        // API RESOURCE
        $products = Product::with('category')->get();
        return ApiResponse::success([
            // 'products' => $products->toResourceCollection(ProductResource::class),
            'products' => ProductResource::collection($products),
            'totalProducts' => Product::count(),
        ]);
    }

    public function listMovements()
    {
        // Carrega antecipadamente as relações product e category
        // para evitar consultas extras ao acessar os dados (N+1 Queries)
        $movements = Movement::with('product.category')->get();

        // for testing with empty collections
        // $movements = collect();

        return ApiResponse::success([
            'movements' => MovementResource::collection($movements),
            'totalMovements' => $movements->count(),
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
}
