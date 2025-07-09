<?php

session_start();

require_once __DIR__  . "/helpers.php";
require_once __DIR__  . "/config.php"; // server_key
require_once __DIR__  . "/captcha.php";

$token = $_POST['smart-token'] ?? '';
$phoneOrEmail = trim($_POST['phoneOrEmail'] ?? '');
$password = $_POST['password'] ?? '';

function showError(string $message): void {
    echo "<p style='color:red;'>$message</p>";
    echo "<p><a href='/login_page.php'>Вернуться ко входу</a></p>";
    exit;
}

if (!check_captcha($token)) {
    showError('Ошибка! Подтвердите, что вы не робот!');
}

if ($phoneOrEmail === '' || $password === '') {
    showError('Ошибка! Заполните все поля!');
}

$db = getDB();
$sql = "SELECT `id`, `name`, `login`, `email`, `phone`, `password` FROM `users` WHERE `email` = ? OR `phone` = ? LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->bind_param('ss', $phoneOrEmail, $phoneOrEmail);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    showError('Ошибка! Неверный логин или пароль!');
}

$_SESSION['user'] = [
    'id'    => $user['id'],
    'name'  => $user['name'],
    'login' => $user['login'],
];

header("Location: /profile.php");
exit;