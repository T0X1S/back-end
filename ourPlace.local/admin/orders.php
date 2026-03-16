<?php
require_once __DIR__ . '/common.php';

$orders = $pdo->query('SELECT o.*, u.email AS user_email FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC')->fetchAll();

$pageTitle = 'Заказы';
require_once __DIR__ . '/header.php';
?>
<div class="container">
    <h1 class="mb-3">Заказы</h1>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Дата</th><th>Клиент</th><th>Email</th><th>Сумма</th><th>Статус</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= (int)$o['id'] ?></td>
                <td><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                <td><?= htmlspecialchars($o['customer_name']) ?></td>
                <td><?= htmlspecialchars($o['customer_email']) ?></td>
                <td>$<?= number_format($o['total_amount'], 2) ?></td>
                <td><?= htmlspecialchars($o['status']) ?></td>
                <td><a href="order_edit.php?id=<?= (int)$o['id'] ?>" class="btn btn-sm btn-outline-primary">Изменить</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (count($orders) === 0): ?><p class="text-muted">Заказов пока нет.</p><?php endif; ?>
</div>
<?php require_once __DIR__ . '/footer.php';
