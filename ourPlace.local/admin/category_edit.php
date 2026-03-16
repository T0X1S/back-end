<?php
require_once __DIR__ . '/common.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cat = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $cat = $stmt->fetch();
    if (!$cat) { header('Location: categories.php'); exit; }
}

$parents = $pdo->query('SELECT id, name FROM categories ORDER BY sort_order, name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '') ?: null;
    $parent_id = (int)$_POST['parent_id'] ?: null;
    $sort_order = (int)$_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if ($parent_id === 0) $parent_id = null;

    if (!$name || !$slug) { $error = 'Название и slug обязательны.'; }
    else {
        if ($id) {
            $pdo->prepare('UPDATE categories SET name=?, slug=?, description=?, parent_id=?, sort_order=?, is_active=? WHERE id=?')
                ->execute([$name, $slug, $description, $parent_id, $sort_order, $is_active, $id]);
        } else {
            $pdo->prepare('INSERT INTO categories (name, slug, description, parent_id, sort_order, is_active) VALUES (?,?,?,?,?,?)')
                ->execute([$name, $slug, $description, $parent_id, $sort_order, $is_active]);
        }
        header('Location: categories.php');
        exit;
    }
}

$pageTitle = $cat ? 'Редактирование категории' : 'Новая категория';
require_once __DIR__ . '/header.php';
?>
<div class="container">
    <h1 class="mb-3"><?= $pageTitle ?></h1>
    <?php if (!empty($error)): ?><p class="text-danger"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post" class="mw-500">
        <?php $c = $cat ?: $_POST; ?>
        <div class="mb-2"><label class="form-label">Название *</label><input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($c['name'] ?? '') ?>"></div>
        <div class="mb-2"><label class="form-label">Slug *</label><input type="text" name="slug" class="form-control" required value="<?= htmlspecialchars($c['slug'] ?? '') ?>"></div>
        <div class="mb-2"><label class="form-label">Описание</label><textarea name="description" class="form-control"><?= htmlspecialchars($c['description'] ?? '') ?></textarea></div>
        <div class="mb-2"><label class="form-label">Родитель</label><select name="parent_id" class="form-select"><option value="0">— Нет —</option><?php foreach ($parents as $p): ?><?php if ($id && (int)$p['id'] === $id) continue; ?><option value="<?= (int)$p['id'] ?>" <?= (($c['parent_id'] ?? 0) == $p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option><?php endforeach; ?></select></div>
        <div class="mb-2"><label class="form-label">Порядок</label><input type="number" name="sort_order" class="form-control" value="<?= (int)($c['sort_order'] ?? 0) ?>"></div>
        <div class="mb-2"><label><input type="checkbox" name="is_active" value="1" <?= ($c['is_active'] ?? 1) ? 'checked' : '' ?>> Активна</label></div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="categories.php" class="btn btn-secondary">Отмена</a>
    </form>
</div>
<?php require_once __DIR__ . '/footer.php';
