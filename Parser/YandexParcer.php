<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class YandexMarketParser
{
    private $client;
    private $baseUrl = 'https://market.yandex.ru';

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7'
            ]
        ]);
    }

    public function parseProduct(string $url): array
    {
        $cacheKey = 'product:'.md5($url);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        try {
            $response = $this->client->get($url);
            $html = (string)$response->getBody();
            $crawler = new Crawler($html);

            $price = $this->extractPrice($crawler);
            $name = $this->extractName($crawler);
            $rating = $this->extractRating($crawler);

            return [
                'success' => true,
                'data' => [
                    'name' => $name,
                    'price' => $price,
                    'rating' => $rating,
                    'url' => $url,
                    'timestamp' => now()->toDateTimeString()
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    Cache::put($cacheKey, $result, now()->addHours(2));
        return $result;
    }

    private function extractPrice(Crawler $crawler): ?float
    {
        try {
            $priceText = $crawler->filter('[data-auto="mainPrice"] span')->first()->text();
            return (float)preg_replace('/[^0-9]/', '', $priceText);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function extractName(Crawler $crawler): ?string
    {
        try {
            return $crawler->filter('h1')->text();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function extractRating(Crawler $crawler): ?float
    {
        try {
            return (float)$crawler->filter('[data-auto="rating"]')->text();
        } catch (\Exception $e) {
            return null;
        }
    }
}

