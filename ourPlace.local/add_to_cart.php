<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/cart.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'cart_count' => getCartCount()]);
    } else {
        header('Location: catalog.php');
    }
    exit;
}
$productId = (int) $_POST['product_id'];
$quantity = isset($_POST['quantity']) ? max(1, (int) $_POST['quantity']) : 1;
$variantId = isset($_POST['variant_id']) && (int) $_POST['variant_id'] > 0 ? (int) $_POST['variant_id'] : null;

$stmt = $pdo->prepare('SELECT id FROM products WHERE id = ? AND is_active = 1');
$stmt->execute([$productId]);
if ($stmt->fetch()) {
    addToCart($productId, $quantity, $variantId);
}
$cartCount = getCartCount();

if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'cart_count' => $cartCount]);
    exit;
}
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'catalog.php';
if (strpos($redirect, 'http') === 0) $redirect = 'catalog.php';
header('Location: ' . $redirect);
exit;
