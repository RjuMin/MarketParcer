<?php
require 'vendor/autoload.php';
use App\Services\YandexMarketParser;

$parser = new YandexMarketParser();
$result = $parser->parseProduct('https://market.yandex.ru/product--smartfon-apple-iphone-14/123456');

print_r($result);