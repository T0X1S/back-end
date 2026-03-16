<?php
/**
 * Сессии и проверка авторизации.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * @return bool
 */
function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

/**
 * @return array|null Данные пользователя из БД или null.
 */
function currentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    if (!isset($GLOBALS['pdo'])) {
        require __DIR__ . '/../config/database.php';
    }
    $stmt = $GLOBALS['pdo']->prepare('SELECT id, email, first_name, last_name, phone, role, created_at FROM users WHERE id = ? AND is_active = 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}

/**
 * Требовать авторизацию; иначе редирект на страницу входа.
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}
