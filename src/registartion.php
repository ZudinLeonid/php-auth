<?php

session_start();

require_once __DIR__  . "/helpers.php";
require_once __DIR__  . "/validation.php";

$errors = [];
$data = $_POST;

validateUserData($data, $errors);

if (!empty($errors)) {
    foreach ($errors as $err) {
        echo "<p style='color: red;'>$err</p>";
    }
    echo "<p><a href='/registration.html'>Вернуться назад</a></p>";
    exit;
}

$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

$db = getDB();
$stmt = $db->prepare("INSERT INTO `users` (`name`, `login`, `phone`, `email`, `password`) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $data['name'], $data['login'], $data['phone'], $data['email'], $hashedPassword);
$stmt->execute();

$user_id = $stmt->insert_id;
$stmt->close();

$_SESSION['user'] = [
    'id'    => $user_id,
    'name'  => $data['name'],
    'login' => $data['login'],
    'phone' => $data['phone'],
    'email' => $data['email'],
];

header("Location: /profile.php");
exit;