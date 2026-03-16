<?php
require_once __DIR__ . '/common.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$productId) { header('Location: products.php'); exit; }
$product = $pdo->prepare('SELECT id, name FROM products WHERE id = ?');
$product->execute([$productId]);
$product = $product->fetch();
if (!$product) { header('Location: products.php'); exit; }

if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $pdo->prepare('DELETE FROM product_variants WHERE id = ? AND product_id = ?')->execute([(int)$_GET['delete'], $productId]);
    header('Location: product_variants.php?id=' . $productId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['value'])) {
    $name = trim($_POST['name']);
    $value = trim($_POST['value']);
    $sku_suffix = trim($_POST['sku_suffix'] ?? '') ?: null;
    $price_modifier = (float)str_replace(',', '.', $_POST['price_modifier'] ?? 0);
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    if ($name !== '' && $value !== '') {
        $pdo->prepare('INSERT INTO product_variants (product_id, name, value, sku_suffix, price_modifier, stock_quantity, sort_order) VALUES (?,?,?,?,?,?,?)')
            ->execute([$productId, $name, $value, $sku_suffix, $price_modifier, $stock_quantity, $sort_order]);
        header('Location: product_variants.php?id=' . $productId);
        exit;
    }
}

$variants = $pdo->prepare('SELECT * FROM product_variants WHERE product_id = ? ORDER BY sort_order, value');
$variants->execute([$productId]);
$variants = $variants->fetchAll();

$pageTitle = 'Варианты: ' . $product['name'];
require_once __DIR__ . '/header.php';
?>
<div class="container">
    <h1 class="mb-3">Варианты товара: <?= htmlspecialchars($product['name']) ?></h1>
    <a href="product_edit.php?id=<?= $productId ?>" class="btn btn-outline-secondary mb-3">← К товару</a>
    <form method="post" class="card p-3 mb-4">
        <h3 class="h6">Добавить вариант</h3>
        <div class="row g-2">
            <div class="col-md-2"><input type="text" name="name" class="form-control form-control-sm" placeholder="Тип (напр. Color)" required></div>
            <div class="col-md-2"><input type="text" name="value" class="form-control form-control-sm" placeholder="Значение" required></div>
            <div class="col-md-2"><input type="text" name="sku_suffix" class="form-control form-control-sm" placeholder="SKU суффикс"></div>
            <div class="col-md-2"><input type="text" name="price_modifier" class="form-control form-control-sm" placeholder="Наценка" value="0"></div>
            <div class="col-md-1"><input type="number" name="stock_quantity" class="form-control form-control-sm" value="0" min="0"></div>
            <div class="col-md-1"><input type="number" name="sort_order" class="form-control form-control-sm" value="0"></div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary btn-sm">Добавить</button></div>
        </div>
    </form>
    <table class="table table-bordered">
        <thead><tr><th>Тип</th><th>Значение</th><th>SKU суффикс</th><th>Наценка</th><th>Остаток</th><th>Порядок</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($variants as $v): ?>
        <tr>
            <td><?= htmlspecialchars($v['name']) ?></td>
            <td><?= htmlspecialchars($v['value']) ?></td>
            <td><?= htmlspecialchars($v['sku_suffix'] ?? '') ?></td>
            <td><?= $v['price_modifier'] ?></td>
            <td><?= (int)$v['stock_quantity'] ?></td>
            <td><?= (int)$v['sort_order'] ?></td>
            <td><a href="product_variants.php?id=<?= $productId ?>&delete=<?= (int)$v['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?')">Удалить</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/footer.php';
