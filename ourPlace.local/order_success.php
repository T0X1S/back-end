<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) {
    header('Location: index.php');
    exit;
}
if (isLoggedIn() && (int)$order['user_id'] !== (int)$_SESSION['user_id']) {
    header('Location: index.php');
    exit;
}
if (!isLoggedIn() && $order['user_id']) {
    header('Location: index.php');
    exit;
}

require __DIR__ . '/includes/header.php';
?>
    <main class="main page-order-success">
      <section class="container py-5 text-center">
        <div class="card-site order-success-card">
          <h1 class="page-title">Спасибо за заказ!</h1>
          <p class="order-success-text">Ваш заказ <strong>#<?= $id ?></strong> принят. Мы отправили подтверждение и чек на email <strong><?= htmlspecialchars($order['customer_email']) ?></strong>.</p>
          <div class="order-success-actions">
            <a href="receipt_pdf.php?order_id=<?= $id ?>" class="btn-site btn-site-primary" download>Скачать чек (PDF)</a>
            <?php if (isLoggedIn()): ?>
            <a href="profile.php#orders" class="btn-site btn-site-secondary">История заказов</a>
            <?php endif; ?>
            <a href="catalog.php" class="btn-site btn-site-outline">В каталог</a>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php';
