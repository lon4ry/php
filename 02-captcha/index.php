<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>CAPTCHA</title>
    <style>
        body { font-family: sans-serif; max-width: 420px; margin: 40px auto; padding: 0 16px; }
        img { display: block; margin-bottom: 8px; cursor: pointer; border: 1px solid silver; }
        input, button { font-size: 16px; padding: 6px; }
    </style>
</head>
<body>
    <h1>Проверка CAPTCHA</h1>
    <form action="check.php" method="post">
        <img src="captcha.php" alt="CAPTCHA" title="Нажмите, чтобы обновить картинку"
             onclick="this.src = 'captcha.php?' + Date.now()">
        <label>
            Введите символы с картинки:
            <input type="text" name="answer" autocomplete="off" required autofocus>
        </label>
        <button type="submit">Проверить</button>
    </form>
</body>
</html>
