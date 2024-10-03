<?php
require 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexão falhou: " . $e->getMessage());
}

$apiUrl = "https://api.telegram.org/bot" . TOKEN;

$offset = 0; 
while (true) {
    $response = file_get_contents($apiUrl . "/getUpdates?offset=$offset");
    $updates = json_decode($response);

    foreach ($updates->result as $update) {
        $chatId = $update->message->chat->id;
        $username = $update->message->chat->username;
        $firstName = $update->message->chat->first_name;
        $userId = $update->message->from->id;
        $offset = $update->update_id + 1; 

        if ($update->message->text == "/start") {
            saveUser($userId, $username, $firstName);

            $responseText = "Olá, $firstName! Bem-vindo ao meu bot do Telegram! Seu nome e ID foram salvos.";
            sendMessage($chatId, $responseText);
        }
    }

    sleep(1);
}

function sendMessage($chatId, $text) {
    global $apiUrl;
    $data = [
        'chat_id' => $chatId,
        'text' => $text,
    ];
    file_get_contents($apiUrl . '/sendMessage?' . http_build_query($data));
}

function saveUser($userId, $username, $firstName) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);

    if ($stmt->rowCount() === 0) {
        $stmt = $pdo->prepare("INSERT INTO users (user_id, username, first_name) VALUES (:user_id, :username, :first_name)");
        $stmt->execute([
            'user_id' => $userId,
            'username' => $username,
            'first_name' => $firstName
        ]);
    }
}
