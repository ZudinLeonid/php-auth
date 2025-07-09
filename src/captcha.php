<?php

function check_captcha(string $token): bool
{
    if ($token === '') {
        return false;
    }

    $url = 'https://smartcaptcha.yandexcloud.net/validate';

    $args = [
        'secret' => SMARTCAPTCHA_SERVER_KEY,
        'token'  => $token,
        'ip'     => $_SERVER['REMOTE_ADDR'] ?? '',
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($statusCode !== 200) {
        return false;
    }

    $resp = json_decode($response);
    $respStatus = $resp->status;
    return $respStatus === "ok";

    $data = json_decode($response, true);
    return isset($data['status']) && $data['status'] === 'ok';
}