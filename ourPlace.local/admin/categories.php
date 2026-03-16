<?php
require_once __DIR__ . '/common.php';

if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([(int)$_GET['delete']]);
    header('Location: categories.php');
    exit;
}

$categories = $pdo->query('SELECT c.*, p.name AS parent_name FROM categories c LEFT JOIN categories p ON p.id = c.parent_id ORDER BY c.sort_order, c.name')->fetchAll();
$pageTitle = 'Категории';
require_once __DIR__ . '/header.php';
?>
<div class="container">
    <h1 class="mb-3">Категории</h1>
    <a href="category_edit.php" class="btn btn-primary mb-3">Добавить категорию</a>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Название</th><th>Slug</th><th>Родитель</th><th>Порядок</th><th>Активна</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
            <tr>
                <td><?= (int)$c['id'] ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['slug']) ?></td>
                <td><?= htmlspecialchars($c['parent_name'] ?? '—') ?></td>
                <td><?= (int)$c['sort_order'] ?></td>
                <td><?= $c['is_active'] ? 'Да' : 'Нет' ?></td>
                <td>
                    <a href="category_edit.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary">Изменить</a>
                    <a href="categories.php?delete=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?')">Удалить</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/footer.php';
