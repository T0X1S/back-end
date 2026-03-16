<?php
require_once __DIR__ . '/common.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: orders.php'); exit; }
$order = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$order->execute([$id]);
$order = $order->fetch();
if (!$order) { header('Location: orders.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $pdo->prepare('UPDATE orders SET status=?, notes=? WHERE id=?')->execute([$_POST['status'], trim($_POST['notes'] ?? ''), $id]);
    header('Location: orders.php');
    exit;
}

$items = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$items->execute([$id]);
$items = $items->fetchAll();

$statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];
$pageTitle = 'Заказ #' . $id;
require_once __DIR__ . '/header.php';
?>
<div class="container">
    <h1 class="mb-3">Заказ #<?= $id ?></h1>
    <p><strong>Клиент:</strong> <?= htmlspecialchars($order['customer_name']) ?> | <?= htmlspecialchars($order['customer_email']) ?> | <?= htmlspecialchars($order['customer_phone'] ?? '') ?></p>
    <p><strong>Сумма:</strong> <?= number_format($order['total_amount'], 2) ?> BYN</p>
    <?php if ($order['notes']): ?><p><strong>Заметки:</strong> <?= htmlspecialchars($order['notes']) ?></p><?php endif; ?>
    <h3 class="h6 mt-3">Позиции</h3>
    <table class="table table-bordered table-sm">
        <thead><tr><th>Товар</th><th>Кол-во</th><th>Цена</th></tr></thead>
        <tbody>
        <?php foreach ($items as $i): ?>
        <tr><td><?= htmlspecialchars($i['product_name']) ?></td><td><?= (int)$i['quantity'] ?></td><td><?= number_format($i['price_at_order'], 2) ?> BYN</td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <form method="post" class="mt-3">
        <div class="mb-2"><label class="form-label">Статус</label><select name="status" class="form-select"><?php foreach ($statuses as $s): ?><option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
        <div class="mb-2"><label class="form-label">Заметки</label><textarea name="notes" class="form-control"><?= htmlspecialchars($order['notes'] ?? '') ?></textarea></div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="orders.php" class="btn btn-secondary">К списку</a>
    </form>
</div>
<?php require_once __DIR__ . '/footer.php';
