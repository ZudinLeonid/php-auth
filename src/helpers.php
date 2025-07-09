<?php

const DB_HOST = "localhost";
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "php_project";

function getDB() {
    $dbConnection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$dbConnection) {
        die("При подключении к базе данных произошла ошибка!" . mysqli_connect_error());
    }
    
    mysqli_set_charset($dbConnection, "utf8mb4");
    
    return $dbConnection;
}