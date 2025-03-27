<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function getProduct($product_id)
    {
        // Тестовые данные (в реальной реализации будем обращаться к API Яндекс.Маркета)
        $testProducts = [
            '123' => [
                'id' => '123',
                'name' => 'Смартфон Xiaomi Redmi Note 10 Pro',
                'price' => 24990,
                'currency' => 'RUB',
                'url' => 'https://market.yandex.ru/product/123',
                'image' => 'https://example.com/image1.jpg',
                'rating' => 4.5,
                'updated_at' => now()->toDateTimeString()
            ],
            '456' => [
                'id' => '456',
                'name' => 'Наушники Apple AirPods Pro',
                'price' => 18990,
                'currency' => 'RUB',
                'url' => 'https://market.yandex.ru/product/456',
                'image' => 'https://example.com/image2.jpg',
                'rating' => 4.8,
                'updated_at' => now()->toDateTimeString()
            ]
        ];

        if (array_key_exists($product_id, $testProducts)) {
            return response()->json($testProducts[$product_id]);
        }

        return response()->json([
            'error' => 'Product not found',
            'message' => 'The requested product ID does not exist in our test database'
        ], 404);
    }
}

//После получения API ключа через Яндекс.Разработчика следует модифицировать функцию:
/*public function getRealProduct($product_id)
{
    $apiKey = env('YANDEX_MARKET_API_KEY');
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey
    ])->get("https://api.market.yandex.ru/v1/products/{$product_id}");

    if ($response->successful()) {
        return $response->json();
    }

    return response()->json([
        'error' => 'Failed to fetch product',
        'details' => $response->json()
    ], $response->status());
}*/
