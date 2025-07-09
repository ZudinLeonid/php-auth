<?php

session_start();

require_once __DIR__  . "/src/helpers.php";

if (empty($_SESSION['user']['id'])) {
    header("Location: index.html");
    exit;
}

$db = getDB();
$userId = $_SESSION['user']['id'];

$sql = "SELECT `name`, `login`, `phone`, `email` FROM `users` WHERE `id` = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "Ошибка! Пользователь не найден!";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles/style.css">
    <title>Профиль</title>
</head>
<body>
    <main>
        <h2>Личный кабинет</h2>
        <h3>Добро пожаловать, <?= htmlspecialchars($user['name']) ?>!</h3>
        <form action="src/update_profile_method.php" method="post">
            <label>Имя:
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </label>
            <label>Логин:
                <input type="text" name="login" value="<?= htmlspecialchars($user['login']) ?>" required>
            </label>
            <label>Телефон:
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
            </label>
            <label>Почта:
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </label>
            <label>Новый пароль (если хотите сменить):
                <input type="password" name="password">
            </label>
            <label>Подтвердите новый пароль:
                <input type="password" name="passwordConfirm">
            </label>

            <button type="submit">Сохранить изменения</button>
        </form>
        <a href="src/logout.php">Выйти из аккаунта</a>
    </main>
</body>
</html>