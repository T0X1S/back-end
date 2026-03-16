<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: catalog.php');
    exit;
}
$stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ? AND p.is_active = 1');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    header('Location: catalog.php');
    exit;
}
$images = $pdo->prepare('SELECT url, alt, sort_order FROM product_images WHERE product_id = ? ORDER BY sort_order');
$images->execute([$id]);
$images = $images->fetchAll();
$variants = $pdo->prepare('SELECT name, value FROM product_variants WHERE product_id = ? ORDER BY sort_order, value');
$variants->execute([$id]);
$variants = $variants->fetchAll();

$reviews = $pdo->prepare('SELECT r.id, r.rating, r.comment, r.created_at, u.first_name, u.last_name, u.email FROM reviews r JOIN users u ON u.id = r.user_id WHERE r.product_id = ? AND r.is_approved = 1 ORDER BY r.created_at DESC');
$reviews->execute([$id]);
$reviews = $reviews->fetchAll();

$reviewSuccess = !empty($_SESSION['review_success']);
$reviewError = isset($_SESSION['review_error']) ? $_SESSION['review_error'] : '';
if ($reviewSuccess) unset($_SESSION['review_success']);
if (isset($_SESSION['review_error'])) unset($_SESSION['review_error']);

require __DIR__ . '/includes/header.php';
?>
    <main class="main product-page">
      <section class="container container-product py-5">
        <nav class="product-breadcrumb mb-4"><a href="catalog.php">Каталог</a> → <span><?= htmlspecialchars($product['name']) ?></span></nav>
        <div class="product-layout">
          <div class="product-gallery">
            <?php if (count($images) > 0): ?>
            <img src="<?= htmlspecialchars($images[0]['url']) ?>" class="product-image" alt="<?= htmlspecialchars($images[0]['alt'] ?? $product['name']) ?>">
            <?php else: ?>
            <img src="images/item<?= (($product['id'] - 1) % 4 + 1) ?>.jpg" class="product-image" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php endif; ?>
          </div>
          <div class="product-info">
            <span class="product-category"><?= htmlspecialchars($product['category_name'] ?? '') ?></span>
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="product-price"><?= number_format($product['price'], 2) ?> BYN</p>
            <div class="product-description"><?= nl2br(htmlspecialchars($product['description'])) ?></div>
            <?php if (count($variants) > 0): ?>
            <p class="product-variants">Варианты: <?= htmlspecialchars(implode(', ', array_map(fn($v) => $v['value'], $variants))) ?></p>
            <?php endif; ?>
            <form action="add_to_cart.php" method="post" class="product-add-form">
              <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
              <input type="hidden" name="redirect" value="product.php?id=<?= (int)$product['id'] ?>">
              <label class="product-qty-label">Количество:</label>
              <input type="number" name="quantity" value="1" min="1" class="product-qty-input">
              <button type="submit" class="btn btn-site-add">В корзину</button>
            </form>
            <a href="catalog.php" class="btn btn-site btn-site-outline product-back-btn">В каталог</a>
          </div>
        </div>

        <section class="product-reviews" id="reviews">
          <h2 class="product-reviews-title">Отзывы и комментарии</h2>
          <?php if ($reviewSuccess): ?><p class="product-review-msg product-review-msg-success">Спасибо, ваш отзыв добавлен.</p><?php endif; ?>
          <?php if ($reviewError): ?><p class="product-review-msg product-review-msg-error"><?= htmlspecialchars($reviewError) ?></p><?php endif; ?>

          <?php if (isLoggedIn()): ?>
          <form method="post" action="add_review.php" class="product-review-form">
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <div class="product-review-form-row">
              <label class="product-review-label">Оценка *</label>
              <select name="rating" class="product-review-rating" required>
                <option value="">Выберите</option>
                <option value="5">5 — Отлично</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1 — Плохо</option>
              </select>
            </div>
            <div class="product-review-form-row">
              <label class="product-review-label">Комментарий</label>
              <textarea name="comment" class="product-review-textarea" rows="3" placeholder="Ваш отзыв о товаре..."></textarea>
            </div>
            <button type="submit" class="btn btn-site btn-site-primary">Отправить отзыв</button>
          </form>
          <?php else: ?>
          <p class="product-review-login">Чтобы оставить отзыв, <a href="login.php?redirect=<?= urlencode('product.php?id=' . $product['id'] . '#reviews') ?>">войдите</a> или <a href="register.php">зарегистрируйтесь</a>.</p>
          <?php endif; ?>

          <div class="product-review-list">
            <?php if (count($reviews) === 0): ?>
            <p class="product-review-empty">Пока нет отзывов. Будьте первым!</p>
            <?php else: ?>
            <?php foreach ($reviews as $r): ?>
            <div class="product-review-item">
              <div class="product-review-header">
                <span class="product-review-author"><?= htmlspecialchars(trim($r['first_name'] . ' ' . $r['last_name']) ?: $r['email']) ?></span>
                <span class="product-review-date"><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></span>
                <span class="product-review-stars"><?= str_repeat('★', (int)$r['rating']) ?><?= str_repeat('☆', 5 - (int)$r['rating']) ?></span>
              </div>
              <?php if (!empty($r['comment'])): ?><p class="product-review-comment"><?= nl2br(htmlspecialchars($r['comment'])) ?></p><?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php';
