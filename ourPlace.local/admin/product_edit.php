<?php
require_once __DIR__ . '/common.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) { header('Location: products.php'); exit; }
}

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY sort_order, name')->fetchAll();
$error = '';

// Удаление изображения
if (isset($_GET['delete_image']) && $id) {
    $imgId = (int)$_GET['delete_image'];
    $pdo->prepare('DELETE FROM product_images WHERE id = ? AND product_id = ?')->execute([$imgId, $id]);
    header('Location: product_edit.php?id=' . $id);
    exit;
}

// Добавление изображения по URL (только при редактировании существующего товара)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_image']) && $id) {
    $url = trim($_POST['image_url'] ?? '');
    $alt = trim($_POST['image_alt'] ?? '') ?: null;
    $sortOrder = (int)($_POST['image_sort_order'] ?? 0);
    if ($url !== '') {
        $ins = $pdo->prepare('INSERT INTO product_images (product_id, url, alt, sort_order) VALUES (?, ?, ?, ?)');
        $ins->execute([$id, $url, $alt, $sortOrder]);
    }
    header('Location: product_edit.php?id=' . $id);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['add_image'])) {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '') ?: null;
    $category_id = (int)($_POST['category_id'] ?? 0);
    if ($category_id === 0) $category_id = null;
    $price = (float)str_replace(',', '.', $_POST['price'] ?? 0);
    $sku = trim($_POST['sku'] ?? '') ?: null;
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (!$name || !$slug) {
        $error = 'Название и slug обязательны.';
    } else {
        if ($id) {
            $pdo->prepare('UPDATE products SET category_id=?, name=?, slug=?, description=?, price=?, sku=?, stock_quantity=?, is_active=? WHERE id=?')
                ->execute([$category_id, $name, $slug, $description, $price, $sku, $stock_quantity, $is_active, $id]);
            header('Location: product_edit.php?id=' . $id);
        } else {
            $pdo->prepare('INSERT INTO products (category_id, name, slug, description, price, sku, stock_quantity, is_active) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$category_id, $name, $slug, $description, $price, $sku, $stock_quantity, $is_active]);
            $newId = (int)$pdo->lastInsertId();
            header('Location: product_edit.php?id=' . $newId);
        }
        exit;
    }
}

$p = $product ?: $_POST;
$pageTitle = $product ? 'Редактирование товара' : 'Новый товар';

$images = [];
if ($id) {
    $imgStmt = $pdo->prepare('SELECT id, url, alt, sort_order FROM product_images WHERE product_id = ? ORDER BY sort_order, id');
    $imgStmt->execute([$id]);
    $images = $imgStmt->fetchAll();
}

require_once __DIR__ . '/header.php';
?>
<div class="container admin-container">
    <h1 class="admin-page-title"><?= $pageTitle ?></h1>
    <?php if ($error): ?><p class="admin-alert admin-alert-danger"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <div class="admin-card admin-card-form">
        <form method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="admin-form-group"><label class="admin-form-label">Название *</label><input type="text" name="name" class="admin-form-control" required value="<?= htmlspecialchars($p['name'] ?? '') ?>"></div>
                    <div class="admin-form-group"><label class="admin-form-label">Slug *</label><input type="text" name="slug" class="admin-form-control" required value="<?= htmlspecialchars($p['slug'] ?? '') ?>"></div>
                    <div class="admin-form-group"><label class="admin-form-label">Категория</label><select name="category_id" class="admin-form-control"><option value="0">—</option><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (($p['category_id'] ?? 0) == $c['id'] ? 'selected' : '') ?>><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="admin-form-group"><label class="admin-form-label">Описание</label><textarea name="description" class="admin-form-control" rows="4"><?= htmlspecialchars($p['description'] ?? '') ?></textarea></div>
                </div>
                <div class="col-md-6">
                    <div class="admin-form-group"><label class="admin-form-label">Цена</label><input type="text" name="price" class="admin-form-control" value="<?= htmlspecialchars($p['price'] ?? '') ?>"></div>
                    <div class="admin-form-group"><label class="admin-form-label">Артикул</label><input type="text" name="sku" class="admin-form-control" value="<?= htmlspecialchars($p['sku'] ?? '') ?>"></div>
                    <div class="admin-form-group"><label class="admin-form-label">Остаток</label><input type="number" name="stock_quantity" class="admin-form-control" min="0" value="<?= (int)($p['stock_quantity'] ?? 0) ?>"></div>
                    <div class="admin-form-group"><label class="admin-checkbox-label"><input type="checkbox" name="is_active" value="1" <?= ($p['is_active'] ?? 1) ? 'checked' : '' ?>> Активен</label></div>
                </div>
            </div>
            <div class="admin-form-actions">
                <button type="submit" class="btn-admin btn-admin-primary">Сохранить</button>
                <a href="products.php" class="btn-admin btn-admin-secondary">Отмена</a>
                <?php if ($id): ?><a href="product_variants.php?id=<?= $id ?>" class="btn-admin btn-admin-outline">Варианты товара</a><?php endif; ?>
            </div>
        </form>
    </div>

    <?php if ($id): ?>
    <div class="admin-card admin-card-images">
        <h2 class="admin-card-title">Изображения товара</h2>
        <?php if (count($images) > 0): ?>
        <ul class="admin-image-list">
            <?php foreach ($images as $img): ?>
            <li class="admin-image-item">
                <img src="<?= htmlspecialchars($img['url']) ?>" alt="" class="admin-image-thumb" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <span class="admin-image-placeholder" style="display:none;">Нет превью</span>
                <div class="admin-image-meta">
                    <span class="admin-image-url" title="<?= htmlspecialchars($img['url']) ?>"><?= htmlspecialchars(mb_substr($img['url'], 0, 50)) ?>…</span>
                    <?php if ($img['alt']): ?><span class="admin-image-alt"><?= htmlspecialchars($img['alt']) ?></span><?php endif; ?>
                    <a href="product_edit.php?id=<?= $id ?>&delete_image=<?= (int)$img['id'] ?>" class="admin-link-danger" onclick="return confirm('Удалить изображение?');">Удалить</a>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <form method="post" class="admin-add-image-form">
            <input type="hidden" name="add_image" value="1">
            <h3 class="admin-form-subtitle">Добавить изображение по ссылке</h3>
            <div class="row g-2 align-items-end">
                <div class="col-md-4"><label class="admin-form-label">URL изображения *</label><input type="text" name="image_url" class="admin-form-control" placeholder="https://... или images/photo.jpg" required></div>
                <div class="col-md-3"><label class="admin-form-label">Подпись (alt)</label><input type="text" name="image_alt" class="admin-form-control" placeholder="Описание для доступности"></div>
                <div class="col-md-2"><label class="admin-form-label">Порядок</label><input type="number" name="image_sort_order" class="admin-form-control" value="0" min="0"></div>
                <div class="col-md-2"><button type="submit" class="btn-admin btn-admin-primary w-100">Добавить</button></div>
            </div>
        </form>
    </div>
    <?php else: ?>
    <p class="admin-hint">Сохраните товар, чтобы добавить к нему изображения по ссылке.</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/footer.php';
