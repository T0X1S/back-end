<?php
/**
 * Корзина: для гостей — session_id в БД, для авторизованных — user_id.
 */
if (!function_exists('getCartIdentifier')) {
    require_once __DIR__ . '/auth.php';
}

function getCartIdentifier() {
    if (isLoggedIn()) {
        return ['user_id' => $_SESSION['user_id'], 'session_id' => null];
    }
    if (empty($_SESSION['cart_session_id'])) {
        $_SESSION['cart_session_id'] = bin2hex(random_bytes(16));
    }
    return ['user_id' => null, 'session_id' => $_SESSION['cart_session_id']];
}

function getCartItems() {
    if (empty($GLOBALS['pdo'])) require_once __DIR__ . '/../config/database.php';
    $pdo = $GLOBALS['pdo'];
    $id = getCartIdentifier();
    if ($id['user_id']) {
        $stmt = $pdo->prepare('SELECT c.id, c.product_id, c.product_variant_id, c.quantity, p.name, p.price, p.sku FROM cart_items c JOIN products p ON p.id = c.product_id WHERE c.user_id = ?');
        $stmt->execute([$id['user_id']]);
    } else {
        $stmt = $pdo->prepare('SELECT c.id, c.product_id, c.product_variant_id, c.quantity, p.name, p.price, p.sku FROM cart_items c JOIN products p ON p.id = c.product_id WHERE c.session_id = ?');
        $stmt->execute([$id['session_id']]);
    }
    $items = $stmt->fetchAll();
    foreach ($items as &$item) {
        $item['price'] = (float) $item['price'];
        $item['subtotal'] = $item['price'] * (int) $item['quantity'];
    }
    return $items;
}

function getCartCount() {
    $items = getCartItems();
    $n = 0;
    foreach ($items as $i) $n += (int) $i['quantity'];
    return $n;
}

function getCartTotal() {
    $items = getCartItems();
    $t = 0;
    foreach ($items as $i) $t += $i['subtotal'];
    return $t;
}

function addToCart($productId, $quantity = 1, $productVariantId = null) {
    if (empty($GLOBALS['pdo'])) require_once __DIR__ . '/../config/database.php';
    $pdo = $GLOBALS['pdo'];
    $id = getCartIdentifier();
    $productId = (int) $productId;
    $quantity = max(1, (int) $quantity);
    $productVariantId = $productVariantId ? (int) $productVariantId : null;

    if ($id['user_id']) {
        $stmt = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? AND (product_variant_id <=> ?)');
        $stmt->execute([$id['user_id'], $productId, $productVariantId]);
    } else {
        $stmt = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ? AND (product_variant_id <=> ?)');
        $stmt->execute([$id['session_id'], $productId, $productVariantId]);
    }
    $row = $stmt->fetch();
    if ($row) {
        $newQty = (int) $row['quantity'] + $quantity;
        $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?')->execute([$newQty, $row['id']]);
    } else {
        if ($id['user_id']) {
            $pdo->prepare('INSERT INTO cart_items (user_id, product_id, product_variant_id, quantity) VALUES (?,?,?,?)')
                ->execute([$id['user_id'], $productId, $productVariantId, $quantity]);
        } else {
            $pdo->prepare('INSERT INTO cart_items (session_id, product_id, product_variant_id, quantity) VALUES (?,?,?,?)')
                ->execute([$id['session_id'], $productId, $productVariantId, $quantity]);
        }
    }
}

function updateCartItem($cartItemId, $quantity) {
    if (empty($GLOBALS['pdo'])) require_once __DIR__ . '/../config/database.php';
    $pdo = $GLOBALS['pdo'];
    $id = getCartIdentifier();
    $cartItemId = (int) $cartItemId;
    $quantity = max(0, (int) $quantity);
    if ($quantity === 0) {
        if ($id['user_id']) $pdo->prepare('DELETE FROM cart_items WHERE id = ? AND user_id = ?')->execute([$cartItemId, $id['user_id']]);
        else $pdo->prepare('DELETE FROM cart_items WHERE id = ? AND session_id = ?')->execute([$cartItemId, $id['session_id']]);
        return;
    }
    if ($id['user_id']) $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?')->execute([$quantity, $cartItemId, $id['user_id']]);
    else $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND session_id = ?')->execute([$quantity, $cartItemId, $id['session_id']]);
}

function removeCartItem($cartItemId) {
    updateCartItem($cartItemId, 0);
}

function clearCart() {
    if (empty($GLOBALS['pdo'])) require_once __DIR__ . '/../config/database.php';
    $pdo = $GLOBALS['pdo'];
    $id = getCartIdentifier();
    if ($id['user_id']) $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?')->execute([$id['user_id']]);
    else $pdo->prepare('DELETE FROM cart_items WHERE session_id = ?')->execute([$id['session_id']]);
}
