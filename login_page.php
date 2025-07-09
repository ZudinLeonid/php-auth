<?php
require_once 'src/config.php'; // client_key
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/styles/style.css">

    <title>Вход</title>
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
</head>
<body>
    <main>
        <h2>Вход</h2>

        <form action="src/login.php" method="post">
            <input required type="text" placeholder="Телефон или Email" name="phoneOrEmail">
            <input required type="password" placeholder="Пароль" name="password">

            <div id="captcha-container" class="smart-captcha" data-sitekey="<?= SMARTCAPTCHA_CLIENT_KEY ?>"></div>

            <a href="index.html">Назад</a>
            <button type="submit">Войти</button>
        </form>
    </main>
</body>
</html>