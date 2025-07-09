<?php

session_start();

require_once __DIR__ . "/helpers.php";
require_once __DIR__ . "/validation.php";

if (empty($_SESSION['user']['id'])) {
    header("Location: index.html");
    exit;
}

$db = getDB();
$userId = $_SESSION['user']['id'];

$name = trim($_POST['name'] ?? '');
$login = trim($_POST['login'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['passwordConfirm'] ?? '';

$data = ['name' => $name, 'login' => $login, 'phone' => $phone, 'email' => $email, 'password' => $password, 'passwordConfirm' => $passwordConfirm];
$errors = [];

validateUserData($data, $errors, $userId);

if (!empty($errors)) {
    foreach ($errors as $err) {
        echo "<p style='color: red;'>$err</p>";
    }
    echo "<p><a href='/profile.php'>Вернуться назад</a></p>";
    exit;
}

$sql = "UPDATE `users` SET `name` = ?, `login` = ?, `phone` = ?, `email` = ?";
$params = [$name, $login, $phone, $email];
$types = "ssss";

if ($password !== "") {
    $sql .= ", `password` = ?";
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $params[] = $passwordHash;
    $types .= 's';
}

$sql .= " WHERE id = ?";
$params[] = $userId;
$types .= 'i';

$stmt = $db->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['login'] = $login;
    $_SESSION['user']['phone'] = $phone;
    $_SESSION['user']['email'] = $email;

    header("Location: /profile.php");
    exit;
} else {
    echo "Ошибка при обновлении данных.";
}
$stmt->close();