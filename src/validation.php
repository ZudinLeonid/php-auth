<?php

require_once __DIR__ . '/helpers.php';

function validateUserData(array $data, array &$errors, ?int $excludeUserId = null): array
{
    validateFormat($data, $errors);

    $password = $data['password'] ?? '';
    $passwordConfirm = $data['passwordConfirm'] ?? '';
    validatePassword($password, $passwordConfirm, $errors);

    if (!empty($errors)) {
        return $errors;
    }

    validateUniqueness($data, $errors, $excludeUserId);

    return $errors;
}

function validateFormat(array $data, array &$errors): void
{
    if (trim($data['name'] ?? '') === '') {
        $errors[] = 'Имя не может быть пустым!';
    }
    if (trim($data['login'] ?? '') === '') {
        $errors[] = 'Логин не может быть пустым!';
    }
    $phone = trim($data['phone'] ?? '');
    if (!preg_match('/^\d{10}$/', $phone)) {
        $errors[] = 'Телефон должен содержать ровно 10 цифр.';
    }
    if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Неверный формат email.';
    }
}

function validatePassword(string $password, string $passwordConfirm, array &$errors, bool $required = false): void
{
    if (!$required && $password === '' && $passwordConfirm === '') {
        return;
    }

    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен быть минимум 6 символов.';
    }

    if ($password !== $passwordConfirm) {
        $errors[] = 'Пароли не совпадают.';
    }
}

function validateUniqueness(array $data, array &$errors, ?int $excludeUserId): void {
    $db = getDB();
    $sql = "SELECT `login`, `email`, `phone` FROM `users` WHERE (`login` = ? OR `email` = ? OR `phone` = ?)";
    $params = [$data['login'], $data['email'], $data['phone']];
    $types = "sss";

    if ($excludeUserId !== null) {
        $sql .= " AND id <> ?";
        $params[] = $excludeUserId;
        $types .= "i";
    }
    $sql .= " LIMIT 1";

    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row) {
        $fields = ['login' => 'Логин', 'email' => 'Email', 'phone' => 'Телефон'];
        foreach ($fields as $field => $label) {
            if ($row[$field] === $data[$field]) {
                $errors[] = "$label уже используется.";
            }
        }
    }
    $stmt->close();
}