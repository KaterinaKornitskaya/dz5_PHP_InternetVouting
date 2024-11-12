<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Голосование за язык программирования</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Какой язык программирования вам нравится больше всего?</h2>
<div class="vote-container">
    <form action="index.php" method="get">
        <div class="radio-group">
            <input type="radio" id="cPlus" value="C++" name="vote">
            <label for="cPlus">C++</label>

            <input type="radio" id="cSharp" value="C#" name="vote">
            <label for="cSharp">C#</label>

            <input type="radio" id="javaScript" value="JavaScript" name="vote">
            <label for="javaScript">JavaScript</label>

            <input type="radio" id="php" value="PHP" name="vote">
            <label for="php">PHP</label>

            <input type="radio" id="java" value="Java" name="vote">
            <label for="java">Java</label>
        </div>
        <input type="submit" value="Голосовать" class="submit-btn">
    </form>
</div>

<?php
// массив для хранения голосов
$votes = [];

// Путь к файлам для хранения голосов и IP-адресов
$votesFile = 'votes.txt';
$ipFile = 'ip_log.json';

// Получаем текущий IP-адрес пользователя
$userIp = $_SERVER['REMOTE_ADDR'];
$currentTime = time();

// Загружаем историю голосований с IP
if (file_exists($ipFile)) {
    $ipLog = json_decode(file_get_contents($ipFile), true);
} else {
    $ipLog = [];
}

// Проверка, голосовал ли этот IP в последние 60 минут (3600 секунд)
if (isset($ipLog[$userIp]) && ($currentTime - $ipLog[$userIp] < 3600)) {
    echo "<p style='color:red;'>Вы уже голосовали. Пожалуйста, подождите час перед следующим голосованием.</p>";
} else {
    // Загружаем текущие голоса
    // Если файл существует, считываем текущие значения голосов, иначе инициализируем их с нуля
    if (file_exists($votesFile)) {
        $votes = json_decode(file_get_contents($votesFile), true);
    } else {
        $votes = [
            'C++' => 0,
            'C#' => 0,
            'JavaScript' => 0,
            'PHP' => 0,
            'Java' => 0,
        ];
    }

    // Обработка голосования
    if (isset($_GET['vote'])) {
        $vote = $_GET['vote'];
        if (isset($votes[$vote])) {
            $votes[$vote] += 1;

            // Обновляем файл голосов
            file_put_contents($votesFile, json_encode($votes));

            // Обновляем файл IP-адресов и времени голосования
            $ipLog[$userIp] = $currentTime;
            file_put_contents($ipFile, json_encode($ipLog));
        }
    }
}

// Вычисляем общее количество голосов
$totalVotes = array_sum($votes);

// Вывод результатов голосования с процентами и графическим отображением
if ($totalVotes > 0) {
    echo "<div class='vote-container result-text'><h3>Результаты голосования:</h3>";
    foreach ($votes as $language => $count) {
        $percentage = ($totalVotes > 0) ? round(($count / $totalVotes) * 100) : 0;

        // Выводим процент и графическую полоску
        echo "<p>$language: $count голосов ($percentage%)</p>";
        echo "<div class='progress-container'>
                <div class='progress-bar' style='width: $percentage%;'>$percentage%</div>
              </div>";
    }
    echo "</div>";
}
?>
</body>
</html>
