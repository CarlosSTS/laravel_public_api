<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
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

        $categories = Category::all(['id', 'name', 'description']);

        return ApiResponse::success([
            'categories' => $categories,
            'totalCategories' => $categories->count()
        ]);
    }

    public function listProducts()
    {
        // Trás todos os dados dos produtos e inclui o nome da categoria relacionada, retornando em formato JSON
        // Não consegue remover o category_id da resposta, mesmo usando select, pois a relação exige esse campo para funcionar
        // use o makeHidden para ocultar o campo category_id da resposta
        $products = Product::with('category:id,name,description')->get(['name', 'description', 'category_id']);

        // Caso necessario transformar o nomes que vem da relação para ficar mais claro na resposta,
        // pode usar o transform para isso
        $products->transform(function ($product) {
            return [
                'name' => $product->name,
                'description' => $product->description,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => ucfirst($product->category->name),
                    'description' => $product->category->description
                ] : null
            ];
        });

        return ApiResponse::success([
            'products' => $products,
            'totalProducts' => $products->count()
        ]);
    }
}
