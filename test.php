<?php
require 'vendor/autoload.php';
use App\Services\YandexMarketParser;

$parser = new YandexMarketParser();
$result = $parser->parseProduct('https://market.yandex.ru/product--absorbiruiushchii-kovrik-dlia-vannoi-gubka-bob-multiashnoe-smeshnoe-mylo-bystrosokhnushchii-simpatichnyi-diatomovyi-dver-dlia-spalni-neskolziashchii-domashnii-dekor-napolnyi-kovrik-dlia-vannoi-komnaty-pink-40x60cm/1037411353?sku=103680135059&uniqueId=134799842&do-waremd5=HYm3hMJ_ybd4bLUebGbfiw&sponsored=1&cpc=JjxLv_06X66urkTMbjoM_34gZ1Wwu7M0D6PQ02x23pi-k_gfzumiSMKoFezX7PFam8a55DFzrBvVwT-dwcy6fYDfNI-4DCGE605Omine4coYIy8WTp8D2vjgS9ePV7u2z_F2RwL2Icxn3-yGYaq3vgPG8W1DzE0Y7Pffpo-HDLAhtzVbGCRNF3dVoymGsr-NwNO3VZFnA-nJpELBX25_0YmgBZQ6Uewg6B2DNr4pBXxPB0qGxG1BhOtUOjCrY7dZCAZwgp97RKvesfGS_7Uh4q-UnKND4z8JIjmd9zB6cwZ10qQ7lf8QAb6fBsja6albkIzZLB2_UO1oGEOQXo_oVvkZDlSfszq-O7t_O8gn6UR4BnR4a5Ds_5FpILLK3QJ0o9yPkubDYV0LviFTgPHx5g%2C%2C');

print_r($result);