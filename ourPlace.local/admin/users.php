<?php
require_once __DIR__ . '/common.php';

if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $delId = (int)$_GET['delete'];
    if ($delId !== $currentUser['id']) {
        $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$delId]);
    }
    header('Location: users.php');
    exit;
}

$users = $pdo->query('SELECT id, email, first_name, last_name, phone, role, is_active, created_at FROM users ORDER BY id')->fetchAll();
$pageTitle = 'Пользователи';
require_once __DIR__ . '/header.php';
?>
<div class="container">
    <h1 class="mb-3">Пользователи</h1>
    <a href="user_edit.php" class="btn btn-primary mb-3">Добавить пользователя</a>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Email</th><th>Имя</th><th>Телефон</th><th>Роль</th><th>Активен</th><th>Дата</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars(trim($u['first_name'] . ' ' . $u['last_name'])) ?></td>
                <td><?= htmlspecialchars($u['phone'] ?? '') ?></td>
                <td><?= $u['role'] === 'admin' ? 'Админ' : 'Покупатель' ?></td>
                <td><?= $u['is_active'] ? 'Да' : 'Нет' ?></td>
                <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <a href="user_edit.php?id=<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline-primary">Изменить</a>
                    <?php if ((int)$u['id'] !== $currentUser['id']): ?>
                    <a href="users.php?delete=<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?')">Удалить</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/footer.php';
