<?php
class Database {
    private $connection;

    public function __construct($config) {
        $this->connection = new mysqli(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['name']
        );
        // Создание таблиц при необходимости
    }

    public function addSubscription($chatId, $city) {
        $stmt = $this->connection->prepare("INSERT INTO subscriptions (chat_id, city) VALUES (?, ?)");
        $stmt->bind_param("is", $chatId, $city);
        return $stmt->execute();
    }
}
?>
