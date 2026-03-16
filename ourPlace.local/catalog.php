<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';

$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;

$categories = $pdo->query('SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY sort_order, name')->fetchAll();

$sql = 'SELECT p.id, p.name, p.slug, p.description, p.price, p.sku, p.stock_quantity, p.category_id, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE p.is_active = 1';
$params = [];
if ($categoryId) {
    $sql .= ' AND p.category_id = ?';
    $params[] = $categoryId;
}
if ($search !== '') {
    $sql .= ' AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}
if ($minPrice !== null && $minPrice > 0) {
    $sql .= ' AND p.price >= ?';
    $params[] = $minPrice;
}
if ($maxPrice !== null && $maxPrice > 0) {
    $sql .= ' AND p.price <= ?';
    $params[] = $maxPrice;
}
$sql .= ' ORDER BY p.name';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
    <main class="main catalog-page">
      <section class="container py-4">
        <h1 class="mb-4">Каталог товаров</h1>
        <div class="row">
          <aside class="col-lg-3 mb-4">
            <div class="catalog-filters card p-3">
              <h3 class="h6 mb-3">Фильтры</h3>
              <form method="get" action="catalog.php" id="catalog-filters-form">
                <div class="mb-3">
                  <label class="form-label">Поиск</label>
                  <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Название или артикул">
                </div>
                <div class="mb-3">
                  <label class="form-label">Категория</label>
                  <select name="category" class="form-select">
                    <option value="">Все категории</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= $categoryId === (int)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Цена от</label>
                  <input type="number" name="min_price" class="form-control" step="0.01" min="0" value="<?= $minPrice !== null ? htmlspecialchars($minPrice) : '' ?>" placeholder="0">
                </div>
                <div class="mb-3">
                  <label class="form-label">Цена до</label>
                  <input type="number" name="max_price" class="form-control" step="0.01" min="0" value="<?= $maxPrice !== null ? htmlspecialchars($maxPrice) : '' ?>" placeholder="">
                </div>
                <button type="submit" class="btn btn-primary w-100">Применить</button>
                <a href="catalog.php" class="btn btn-outline-secondary w-100 mt-2">Сбросить</a>
              </form>
            </div>
          </aside>
          <div class="col-lg-9">
            <p class="text-muted mb-3">Найдено: <?= count($products) ?></p>
            <div class="row g-4">
              <?php foreach ($products as $p): ?>
              <div class="col-sm-6 col-md-4">
                <div class="card h-100 catalog-card">
                  <?php
                  $imgStmt = $pdo->prepare('SELECT url, alt FROM product_images WHERE product_id = ? ORDER BY sort_order LIMIT 1');
                  $imgStmt->execute([$p['id']]);
                  $img = $imgStmt->fetch();
                  $imgUrl = $img ? htmlspecialchars($img['url']) : 'images/item' . (($p['id'] - 1) % 4 + 1) . '.jpg';
                  $imgAlt = $img ? htmlspecialchars($img['alt']) : htmlspecialchars($p['name']);
                  ?>
                  <img src="<?= $imgUrl ?>" class="card-img-top catalog-card-img" alt="<?= $imgAlt ?>">
                  <div class="card-body d-flex flex-column">
                    <span class="small text-muted"><?= htmlspecialchars($p['category_name'] ?? '') ?></span>
                    <h3 class="card-title h6"><?= htmlspecialchars($p['name']) ?></h3>
                    <p class="card-text small flex-grow-1"><?= htmlspecialchars(mb_substr($p['description'], 0, 80)) ?>...</p>
                    <p class="fw-bold text-primary mb-2"><?= number_format($p['price'], 2) ?> BYN</p>
                    <div class="d-flex gap-2 flex-wrap">
                      <a href="product.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">Подробнее</a>
                      <form action="add_to_cart.php" method="post" class="d-inline">
                        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                        <input type="hidden" name="redirect" value="catalog.php">
                        <button type="submit" class="btn btn-sm btn-site-add">В корзину</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php if (count($products) === 0): ?>
            <p class="text-muted">По вашему запросу товаров не найдено.</p>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php';
