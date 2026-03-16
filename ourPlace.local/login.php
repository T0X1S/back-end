<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
if (strpos($redirect, 'http') === 0) {
    $redirect = 'index.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $postRedirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';
    if (strpos($postRedirect, 'http') === 0) {
        $postRedirect = 'index.php';
    }
    if (!$email || !$password) {
        $error = 'Заполните email и пароль.';
    } else {
        $stmt = $pdo->prepare('SELECT id, email, password_hash, first_name, last_name FROM users WHERE email = ? AND is_active = 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = (int) $user['id'];
            header('Location: ' . $postRedirect);
            exit;
        }
        $error = 'Неверный email или пароль.';
    }
}

require __DIR__ . '/includes/header.php';
?>
    <main class="main auth-page">
      <section class="container py-5">
        <div class="row justify-content-center">
          <div class="col-md-5 col-lg-4">
            <div class="form">
              <h2 class="form_title">Вход</h2>
              <?php if ($error): ?>
              <p class="auth-error"><?= htmlspecialchars($error) ?></p>
              <?php endif; ?>
              <form method="post" action="login.php">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                <div class="form_group">
                  <label class="form_label" for="email">Email</label>
                  <input class="form_input" type="email" id="email" name="email" required
                         value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form_group">
                  <label class="form_label" for="password">Пароль</label>
                  <input class="form_input" type="password" id="password" name="password" required>
                </div>
                <div class="form_group">
                  <button class="form_button" type="submit">Войти</button>
                </div>
              </form>
              <p class="auth-link">Нет аккаунта? <a href="register.php">Регистрация</a></p>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php';
