<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/WeatherAPI.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Telegram\Bot\Api;

class WeatherBot {
    private $telegram;
    private $weatherAPI;
    private $db;
    private $keyboard;

    public function __construct() {
        $config = include(__DIR__ . '/../config/config.php');
        $this->telegram = new Api($config['telegram_token']);
        $this->weatherAPI = new WeatherAPI($config['weather_api_key']);
        $this->db = new Database($config['db']);
        
        // Инициализация клавиатуры
        $this->keyboard = [
            ['🌤 Текущая погода', '🔔 Мои подписки'],
            ['⚙️ Настройки', '❓ Помощь']
        ];
    }

    public function handleUpdate($update) {
        $message = $update->getMessage();
        $chatId = $message->getChat()->getId();
        $text = $message->getText();

        try {
            switch (true) {
                case $text === '/start':
                    $this->sendWelcomeMessage($chatId);
                    break;
                    
                case $text === '🌤 Текущая погода':
                    $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => 'Введите название города:',
                        'reply_markup' => $this->createReplyKeyboardRemove()
                    ]);
                    break;

                case $text === '🔔 Мои подписки':
                    $this->showSubscriptions($chatId);
                    break;

                case strpos($text, '/weather') === 0:
                    $city = trim(str_replace('/weather', '', $text));
                    $this->sendWeather($chatId, $city);
                    break;

                case strpos($text, '/subscribe') === 0:
                    $city = trim(str_replace('/subscribe', '', $text));
                    $this->addSubscription($chatId, $city);
                    break;

                case strpos($text, '/unsubscribe') === 0:
                    $city = trim(str_replace('/unsubscribe', '', $text));
                    $this->removeSubscription($chatId, $city);
                    break;

                case $text === '❓ Помощь':
                    $this->sendHelp($chatId);
                    break;

                default:
                    // Если сообщение - название города
                    if (preg_match('/^[a-zA-Zа-яА-ЯёЁ\s-]+$/u', $text)) {
                        $this->sendWeather($chatId, $text);
                    } else {
                        $this->telegram->sendMessage([
                            'chat_id' => $chatId,
                            'text' => 'Неизвестная команда. Используйте кнопки меню.',
                            'reply_markup' => $this->createMainKeyboard()
                        ]);
                    }
            }
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => '⚠️ Произошла ошибка. Попробуйте позже.'
            ]);
        }
    }

    private function sendWelcomeMessage($chatId) {
        $welcomeText = "Добро пожаловать в WeatherBot! 🌦\n\n"
            . "Я могу:\n"
            . "- Показывать текущую погоду\n"
            . "- Отправлять ежедневные уведомления\n"
            . "- Хранить ваши настройки\n\n"
            . "Используйте кнопки ниже для навигации:";

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $welcomeText,
            'reply_markup' => $this->createMainKeyboard()
        ]);
    }

    private function sendWeather($chatId, $city) {
        try {
            $weather = $this->weatherAPI->getCurrentWeather($city);
            
            $response = "🌍 Город: *{$city}*\n"
                . "🌡 Температура: *{$weather['temp']}°C*\n"
                . "💧 Влажность: *{$weather['humidity']}%*\n"
                . "🌬 Ветер: *{$weather['wind_speed']} м/с*\n\n"
                . "_{$weather['description']}_";

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $response,
                'parse_mode' => 'Markdown',
                'reply_markup' => $this->createWeatherKeyboard($city)
            ]);

        } catch (Exception $e) {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "🚫 Город '{$city}' не найден!",
                'reply_markup' => $this->createMainKeyboard()
            ]);
        }
    }

    private function addSubscription($chatId, $city) {
        if ($this->weatherAPI->isValidCity($city)) {
            $this->db->addSubscription($chatId, $city);
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "✅ Подписка на '{$city}' активирована!",
                'reply_markup' => $this->createMainKeyboard()
            ]);
        } else {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "🚫 Город '{$city}' не найден!"
            ]);
        }
    }

    private function showSubscriptions($chatId) {
        $subscriptions = $this->db->getSubscriptions($chatId);
        if (empty($subscriptions)) {
            $text = "У вас нет активных подписок. 😔\nИспользуйте /subscribe [город]";
        } else {
            $text = "🔔 Ваши подписки:\n";
            foreach ($subscriptions as $sub) {
                $text .= "- {$sub['city']} (ID: {$sub['id']})\n";
            }
        }
        
        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $this->createMainKeyboard()
        ]);
    }

    private function createMainKeyboard() {
        return json_encode([
            'keyboard' => $this->keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ]);
    }

    private function createWeatherKeyboard($city) {
        return json_encode([
            'inline_keyboard' => [
                [
                    ['text' => '🔔 Подписаться', 'callback_data' => "subscribe_{$city}"]
                ]
            ]
        ]);
    }

    public function sendDailyNotifications() {
        $allSubscriptions = $this->db->getAllSubscriptions();
        foreach ($allSubscriptions as $sub) {
            $weather = $this->weatherAPI->getCurrentWeather($sub['city']);
            $message = "🌅 Доброе утро! Погода в {$sub['city']}:\n"
                . "🌡 {$weather['temp']}°C | 💧 {$weather['humidity']}%\n"
                . "_{$weather['description']}_";

            $this->telegram->sendMessage([
                'chat_id' => $sub['chat_id'],
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
        }
    }
}
?>
