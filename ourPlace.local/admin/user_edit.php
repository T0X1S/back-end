<?php
require_once __DIR__ . '/common.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) { header('Location: users.php'); exit; }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '') ?: null;
    $role = $_POST['role'] === 'admin' ? 'admin' : 'customer';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $password = $_POST['password'] ?? '';

    if (!$email) { $error = 'Укажите email.'; }
    else {
        if ($id) {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $stmt->execute([$email, $id]);
        } else {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
        }
        if ($stmt->fetch() && !$user) { $error = 'Такой email уже есть.'; }
    }

    if (!$error) {
        if ($id) {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE users SET email=?, password_hash=?, first_name=?, last_name=?, phone=?, role=?, is_active=? WHERE id=?')
                    ->execute([$email, $hash, $first_name, $last_name, $phone, $role, $is_active, $id]);
            } else {
                $pdo->prepare('UPDATE users SET email=?, first_name=?, last_name=?, phone=?, role=?, is_active=? WHERE id=?')
                    ->execute([$email, $first_name, $last_name, $phone, $role, $is_active, $id]);
            }
        } else {
            if ($password === '') { $error = 'Укажите пароль для нового пользователя.'; }
            else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare('INSERT INTO users (email, password_hash, first_name, last_name, phone, role, is_active) VALUES (?,?,?,?,?,?,?)')
                    ->execute([$email, $hash, $first_name, $last_name, $phone, $role, $is_active]);
            }
        }
        if (!$error) { header('Location: users.php'); exit; }
    }
}

$pageTitle = $user ? 'Редактирование пользователя' : 'Новый пользователь';
require_once __DIR__ . '/header.php';
?>
<div class="container">
    <h1 class="mb-3"><?= $pageTitle ?></h1>
    <?php if ($error): ?><p class="text-danger"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post" class="mw-500">
        <div class="mb-2"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email'] ?? $_POST['email'] ?? '') ?>"></div>
        <div class="mb-2"><label class="form-label">Пароль <?= $id ? '(оставьте пустым, чтобы не менять)' : '*' ?></label><input type="password" name="password" class="form-control" <?= $id ? '' : 'required' ?>></div>
        <div class="mb-2"><label class="form-label">Имя</label><input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name'] ?? $_POST['first_name'] ?? '') ?>"></div>
        <div class="mb-2"><label class="form-label">Фамилия</label><input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name'] ?? $_POST['last_name'] ?? '') ?>"></div>
        <div class="mb-2"><label class="form-label">Телефон</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? $_POST['phone'] ?? '') ?>"></div>
        <div class="mb-2"><label class="form-label">Роль</label><select name="role" class="form-select"><option value="customer" <?= ($user['role'] ?? '') === 'customer' ? 'selected' : '' ?>>Покупатель</option><option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Админ</option></select></div>
        <div class="mb-2"><label><input type="checkbox" name="is_active" value="1" <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>> Активен</label></div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="users.php" class="btn btn-secondary">Отмена</a>
    </form>
</div>
<?php require_once __DIR__ . '/footer.php';
