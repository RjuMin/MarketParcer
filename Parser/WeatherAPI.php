<?php
class WeatherAPI {
    private $apiKey;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function getCurrentWeather($city) {
        $url = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid={$this->apiKey}&units=metric"; //тут нужно Yandex вставить или разораться че эта
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data['cod'] == 200) {
            return [
                'temp' => $data['main']['temp'],
                'humidity' => $data['main']['humidity'],
                'description' => $data['weather'][0]['description']
            ];
        }
        return null;
    }
}
?>
