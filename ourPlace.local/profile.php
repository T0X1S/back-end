<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
requireLogin();

$user = currentUser();
if (!$user) {
    header('Location: login.php');
    exit;
}

$orders = $pdo->prepare('SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$orders->execute([$_SESSION['user_id']]);
$orders = $orders->fetchAll();

require __DIR__ . '/includes/header.php';
?>
    <main class="main profile-page">
      <section class="container container-profile py-5">
        <h1 class="profile-page-title">Профиль</h1>
        <div class="profile-layout">
          <div class="profile-card profile-card-main">
            <div class="profile-avatar">
              <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <h2 class="profile-card-heading">Данные аккаунта</h2>
            <dl class="profile-list">
              <dt>Email</dt>
              <dd><?= htmlspecialchars($user['email']) ?></dd>
              <dt>Имя</dt>
              <dd><?= htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name']) ?: '—') ?></dd>
              <dt>Телефон</dt>
              <dd><?= htmlspecialchars($user['phone'] ?? '—') ?></dd>
              <dt>Роль</dt>
              <dd><?= $user['role'] === 'admin' ? 'Администратор' : 'Покупатель' ?></dd>
              <dt>Дата регистрации</dt>
              <dd><?= date('d.m.Y', strtotime($user['created_at'])) ?></dd>
            </dl>
            <div class="profile-actions">
              <a href="index.php" class="btn-site btn-site-primary">На главную</a>
              <a href="catalog.php" class="btn-site btn-site-outline">Каталог</a>
              <a href="logout.php" class="btn-site btn-site-logout">Выйти</a>
            </div>
          </div>

          <div class="profile-card profile-card-orders" id="orders">
            <h2 class="profile-card-heading">История заказов</h2>
            <?php if (count($orders) === 0): ?>
            <p class="profile-no-orders">У вас пока нет заказов. <a href="catalog.php">Перейти в каталог</a></p>
            <?php else: ?>
            <div class="profile-orders-list">
              <?php foreach ($orders as $o): ?>
              <div class="profile-order-item">
                <div class="profile-order-info">
                  <span class="profile-order-id">Заказ #<?= (int)$o['id'] ?></span>
                  <span class="profile-order-date"><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></span>
                  <span class="profile-order-status profile-order-status-<?= htmlspecialchars($o['status']) ?>"><?= htmlspecialchars($o['status']) ?></span>
                </div>
                <div class="profile-order-meta">
                  <span class="profile-order-total"><?= number_format($o['total_amount'], 2) ?> BYN</span>
                  <a href="receipt_pdf.php?order_id=<?= (int)$o['id'] ?>" class="btn-site btn-site-small">Чек PDF</a>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php';
