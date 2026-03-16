<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/cart.php';

$currentUser = currentUser();
$cartItems = getCartItems();
if (count($cartItems) === 0) {
    header('Location: cart.php');
    exit;
}
$cartTotal = getCartTotal();

$error = '';
$success = false;
$orderId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $customerPhone = trim($_POST['customer_phone'] ?? '') ?: null;
    $city = trim($_POST['city'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $zip = trim($_POST['zip'] ?? '') ?: null;
    $country = trim($_POST['country'] ?? '') ?: 'Беларусь';

    if (!$customerName || !$customerEmail) {
        $error = 'Укажите имя и email.';
    } elseif (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email.';
    } elseif (!$city || !$street) {
        $error = 'Укажите город и адрес доставки.';
    } else {
        $userId = isLoggedIn() ? $currentUser['id'] : null;
        $addressLine = $street . ', ' . $city . ($zip ? ' ' . $zip : '') . ', ' . $country;
        $notes = 'Адрес: ' . $addressLine;

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, status, total_amount, customer_email, customer_name, customer_phone, notes) VALUES (?,?,?,?,?,?,?)');
            $stmt->execute([$userId, 'pending', $cartTotal, $customerEmail, $customerName, $customerPhone, $notes]);
            $orderId = (int) $pdo->lastInsertId();

            foreach ($cartItems as $item) {
                $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_variant_id, product_name, quantity, price_at_order) VALUES (?,?,?,?,?,?)')
                    ->execute([$orderId, $item['product_id'], $item['product_variant_id'], $item['name'], $item['quantity'], $item['price']]);
            }

            clearCart();
            $pdo->commit();

            require_once __DIR__ . '/lib/send_order_emails.php';
            sendOrderEmails($orderId, $customerEmail, $customerName, $cartItems, $cartTotal, $addressLine);

            header('Location: order_success.php?id=' . $orderId);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Ошибка при создании заказа. Попробуйте ещё раз.';
        }
    }
}

$prefill = [
    'customer_name' => $currentUser ? trim($currentUser['first_name'] . ' ' . $currentUser['last_name']) : ($_POST['customer_name'] ?? ''),
    'customer_email' => $currentUser ? $currentUser['email'] : ($_POST['customer_email'] ?? ''),
    'customer_phone' => $currentUser ? ($currentUser['phone'] ?? '') : ($_POST['customer_phone'] ?? ''),
];

require __DIR__ . '/includes/header.php';
?>
    <main class="main page-checkout">
      <section class="container py-5">
        <h1 class="page-title">Оформление заказа</h1>
        <?php if ($error): ?>
        <p class="alert alert-danger"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <div class="row">
          <div class="col-lg-7">
            <form method="post" class="checkout-form card-site">
              <h2 class="card-site-title">Контактные данные</h2>
              <div class="form-row">
                <label class="form-label">Имя и фамилия *</label>
                <input type="text" name="customer_name" class="form-input" required value="<?= htmlspecialchars($prefill['customer_name']) ?>">
              </div>
              <div class="form-row">
                <label class="form-label">Email *</label>
                <input type="email" name="customer_email" class="form-input" required value="<?= htmlspecialchars($prefill['customer_email']) ?>">
              </div>
              <div class="form-row">
                <label class="form-label">Телефон</label>
                <input type="tel" name="customer_phone" class="form-input" value="<?= htmlspecialchars($prefill['customer_phone']) ?>">
              </div>
              <h2 class="card-site-title mt-4">Адрес доставки</h2>
              <div class="form-row">
                <label class="form-label">Улица, дом *</label>
                <input type="text" name="street" class="form-input" required value="<?= htmlspecialchars($_POST['street'] ?? '') ?>">
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-row">
                    <label class="form-label">Город *</label>
                    <input type="text" name="city" class="form-input" required value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-row">
                    <label class="form-label">Индекс</label>
                    <input type="text" name="zip" class="form-input" value="<?= htmlspecialchars($_POST['zip'] ?? '') ?>">
                  </div>
                </div>
              </div>
              <div class="form-row">
                <label class="form-label">Страна</label>
                <input type="text" name="country" class="form-input" value="<?= htmlspecialchars($_POST['country'] ?? 'Беларусь') ?>">
              </div>
              <button type="submit" class="btn-site btn-site-primary mt-3">Подтвердить заказ</button>
            </form>
          </div>
          <div class="col-lg-5">
            <div class="card-site checkout-summary">
              <h2 class="card-site-title">Ваш заказ</h2>
              <ul class="checkout-list">
              <?php foreach ($cartItems as $item): ?>
                <li><?= htmlspecialchars($item['name']) ?> × <?= (int)$item['quantity'] ?> — <?= number_format($item['subtotal'], 2) ?> BYN</li>
              <?php endforeach; ?>
              </ul>
              <p class="checkout-total">Итого: <strong><?= number_format($cartTotal, 2) ?> BYN</strong></p>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php';
