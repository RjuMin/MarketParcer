<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\YandexMarketParser;

class ParserController extends Controller
{
    protected $parser;

    public function __construct(YandexMarketParser $parser)
    {
        $this->parser = $parser;
    }

    public function parseProduct(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $result = $this->parser->parseProduct($request->url);

        if (!$result['success']) {
            return response()->json([
                'error' => 'Parsing failed',
                'message' => $result['error']
            ], 400);
        }

        return response()->json($result['data']);
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
