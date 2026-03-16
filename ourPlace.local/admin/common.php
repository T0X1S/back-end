<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();
$currentUser = currentUser();
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$adminTitle = 'Админ-панель';
$adminBase = 'admin/';
