<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/cart.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update']) && is_array($_POST['qty'] ?? null)) {
        foreach ($_POST['qty'] as $cartId => $qty) {
            updateCartItem((int) $cartId, (int) $qty);
        }
        header('Location: cart.php');
        exit;
    }
    if (isset($_POST['remove']) && (int) $_POST['remove'] > 0) {
        removeCartItem((int) $_POST['remove']);
        header('Location: cart.php');
        exit;
    }
}

$cartItems = getCartItems();
$cartTotal = getCartTotal();

require __DIR__ . '/includes/header.php';
?>
    <main class="main page-cart">
      <section class="container py-5">
        <h1 class="page-title">Корзина</h1>
        <?php if (count($cartItems) === 0): ?>
        <p class="cart-empty">Корзина пуста. <a href="catalog.php">Перейти в каталог</a></p>
        <?php else: ?>
        <form method="post" action="cart.php" class="cart-form">
          <div class="cart-table-wrap">
            <table class="cart-table">
              <thead>
                <tr>
                  <th>Товар</th>
                  <th>Цена</th>
                  <th>Количество</th>
                  <th>Сумма</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($cartItems as $item): ?>
                <tr>
                  <td class="cart-name"><?= htmlspecialchars($item['name']) ?></td>
                  <td class="cart-price"><?= number_format($item['price'], 2) ?> BYN</td>
                  <td>
                    <input type="number" name="qty[<?= (int)$item['id'] ?>]" value="<?= (int)$item['quantity'] ?>" min="1" class="cart-qty-input">
                  </td>
                  <td class="cart-subtotal"><?= number_format($item['subtotal'], 2) ?> BYN</td>
                  <td>
                    <button type="submit" name="remove" value="<?= (int)$item['id'] ?>" class="btn-cart-remove" onclick="return confirm('Удалить?')">×</button>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="cart-actions">
            <p class="cart-total">Итого: <strong><?= number_format($cartTotal, 2) ?> BYN</strong></p>
            <button type="submit" name="update" value="1" class="btn-site btn-site-secondary">Обновить</button>
            <a href="checkout.php" class="btn-site btn-site-primary">Оформить заказ</a>
          </div>
        </form>
        <?php endif; ?>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php';
