<?php
/**
 * Подключение к БД ourplace (OSPanel: хост = имя модуля MySQL).
 */
$dbHost = 'MySQL-8.4';
$dbName = 'ourplace';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных. Проверьте, что MySQL запущен и база ourplace создана.');
}
$GLOBALS['pdo'] = $pdo;
