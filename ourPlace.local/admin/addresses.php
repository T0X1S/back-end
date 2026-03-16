<?php
require_once __DIR__ . '/common.php';

$addresses = $pdo->query('SELECT a.*, u.email FROM addresses a JOIN users u ON u.id = a.user_id ORDER BY a.id DESC')->fetchAll();
$pageTitle = 'Адреса';
require_once __DIR__ . '/header.php';
?>
<div class="container">
    <h1 class="mb-3">Адреса доставки</h1>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Пользователь</th><th>Имя</th><th>Город</th><th>Улица</th><th>Страна</th><th>По умолчанию</th></tr></thead>
            <tbody>
            <?php foreach ($addresses as $a): ?>
            <tr>
                <td><?= (int)$a['id'] ?></td>
                <td><?= htmlspecialchars($a['email']) ?></td>
                <td><?= htmlspecialchars($a['full_name']) ?></td>
                <td><?= htmlspecialchars($a['city']) ?></td>
                <td><?= htmlspecialchars($a['street']) ?></td>
                <td><?= htmlspecialchars($a['country']) ?></td>
                <td><?= $a['is_default'] ? 'Да' : 'Нет' ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (count($addresses) === 0): ?><p class="text-muted">Адресов пока нет.</p><?php endif; ?>
</div>
<?php require_once __DIR__ . '/footer.php';
