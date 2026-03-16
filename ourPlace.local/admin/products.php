<?php
require_once __DIR__ . '/common.php';

if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([(int)$_GET['delete']]);
    header('Location: products.php');
    exit;
}

$products = $pdo->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id')->fetchAll();
$pageTitle = 'Товары';
require_once __DIR__ . '/header.php';
?>
<div class="container">
    <h1 class="mb-3">Товары</h1>
    <a href="product_edit.php" class="btn btn-primary mb-3">Добавить товар</a>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Название</th><th>Категория</th><th>Цена</th><th>Артикул</th><th>Остаток</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= (int)$p['id'] ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                <td><?= number_format($p['price'], 2) ?> BYN</td>
                <td><?= htmlspecialchars($p['sku'] ?? '') ?></td>
                <td><?= (int)$p['stock_quantity'] ?></td>
                <td>
                    <a href="product_edit.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">Изменить</a>
                    <a href="product_variants.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-secondary">Варианты</a>
                    <a href="products.php?delete=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?')">Удалить</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/footer.php';
