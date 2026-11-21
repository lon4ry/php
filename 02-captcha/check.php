<?php
declare(strict_types=1);

session_start();

$isCorrect = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expected = $_SESSION['captcha'] ?? null;
    unset($_SESSION['captcha']); // одна строка — одна попытка

    $answer = mb_strtoupper(trim((string)($_POST['answer'] ?? '')));
    $isCorrect = is_string($expected) && hash_equals($expected, $answer);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Результат проверки</title>
    <style>
        body { font-family: sans-serif; max-width: 420px; margin: 40px auto; padding: 0 16px; }
        .ok { color: green; }
        .fail { color: firebrick; }
    </style>
</head>
<body>
    <?php if ($isCorrect): ?>
        <h1 class="ok">Корректно</h1>
    <?php else: ?>
        <h1 class="fail">Не корректно</h1>
    <?php endif; ?>
    <p><a href="index.php">Попробовать ещё раз</a></p>
</body>
</html>
