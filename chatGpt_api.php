<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Автозагрузчик Composer, если он используется
require_once __DIR__ . "/../vendor/autoload.php";

$openai_api_key = ''; // Тут мой API ключ, который я вам не покажу ;)
$response = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['userInput'])) {
    $client = new Client();
    $userInput = trim($_POST['userInput']);

    $conversation = [
        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
        ['role' => 'user', 'content' => $userInput]
    ];

    try {
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $openai_api_key
            ],
            'json' => [
                'model' => 'gpt-4-1106-preview', // Модель GPT-4 Turbo
                'messages' => $conversation
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $response = $result['choices'][0]['message']['content'];
        }
    } catch (RequestException $e) {
        $response = "Ошибка: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<title>Чат с GPT-4</title>
</head>

<body class="text-center">
    <h1>Общение с GPT-4</h1>
    <form action="chat.php" method="post">
        <textarea name="userInput" rows="4" cols="50" placeholder="Введите ваше сообщение здесь..."></textarea><br>
        <input type="submit" value="Отправить">
    </form>

    <?php if (isset($response)) : ?>
        <h2>Ответ модели:</h2>
        <p><?php echo htmlspecialchars($response); ?></p>
    <?php endif; ?>
</body>

</html>


