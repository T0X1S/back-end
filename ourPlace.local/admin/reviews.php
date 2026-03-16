<?php
require_once __DIR__ . '/common.php';

if (isset($_GET['approve']) && (int)$_GET['approve'] > 0) {
    $pdo->prepare('UPDATE reviews SET is_approved = 1 WHERE id = ?')->execute([(int)$_GET['approve']]);
    header('Location: reviews.php');
    exit;
}
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $pdo->prepare('DELETE FROM reviews WHERE id = ?')->execute([(int)$_GET['delete']]);
    header('Location: reviews.php');
    exit;
}

$reviews = $pdo->query('SELECT r.*, p.name AS product_name, u.email FROM reviews r JOIN products p ON p.id = r.product_id JOIN users u ON u.id = r.user_id ORDER BY r.created_at DESC')->fetchAll();
$pageTitle = 'Отзывы';
require_once __DIR__ . '/header.php';
?>
<div class="container">
    <h1 class="mb-3">Отзывы</h1>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Товар</th><th>Пользователь</th><th>Оценка</th><th>Комментарий</th><th>Одобрен</th><th>Дата</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($reviews as $r): ?>
            <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= htmlspecialchars($r['product_name']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td><?= (int)$r['rating'] ?></td>
                <td><?= htmlspecialchars(mb_substr($r['comment'] ?? '', 0, 80)) ?>...</td>
                <td><?= $r['is_approved'] ? 'Да' : 'Нет' ?></td>
                <td><?= date('d.m.Y', strtotime($r['created_at'])) ?></td>
                <td>
                    <?php if (!$r['is_approved']): ?><a href="reviews.php?approve=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-success">Одобрить</a><?php endif; ?>
                    <a href="reviews.php?delete=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?')">Удалить</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (count($reviews) === 0): ?><p class="text-muted">Отзывов пока нет.</p><?php endif; ?>
</div>
<?php require_once __DIR__ . '/footer.php';
