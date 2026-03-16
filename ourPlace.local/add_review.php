<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    header('Location: catalog.php');
    exit;
}
$productId = (int) $_POST['product_id'];
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
$comment = trim($_POST['comment'] ?? '');
if ($rating < 1 || $rating > 5) {
    $_SESSION['review_error'] = 'Выберите оценку от 1 до 5.';
    header('Location: product.php?id=' . $productId . '#reviews');
    exit;
}
$stmt = $pdo->prepare('SELECT id FROM products WHERE id = ? AND is_active = 1');
$stmt->execute([$productId]);
if (!$stmt->fetch()) {
    header('Location: catalog.php');
    exit;
}
$userId = (int) $_SESSION['user_id'];
$ins = $pdo->prepare('INSERT INTO reviews (product_id, user_id, rating, comment, is_approved) VALUES (?, ?, ?, ?, 1)');
$ins->execute([$productId, $userId, $rating, $comment ?: null]);
$_SESSION['review_success'] = true;
header('Location: product.php?id=' . $productId . '#reviews');
exit;
