<?php
require_once 'src/Services/YandexParser.php';

$parser = new App\Services\YandexMarketParser();
$result = $parser->parseProduct('https://market.yandex.ru/product--smartfon-apple-iphone-15/123');

print_r($result);
