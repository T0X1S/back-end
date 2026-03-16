<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (!$email || !$password) {
        $error = 'Заполните email и пароль.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email.';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль не менее 6 символов.';
    } elseif ($password !== $password2) {
        $error = 'Пароли не совпадают.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Этот email уже зарегистрирован.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, first_name, last_name, phone, role) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$email, $hash, $first_name, $last_name, $phone ?: null, 'customer']);
            $success = true;
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
    <main class="main auth-page">
      <section class="container py-5">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5">
            <div class="form">
              <h2 class="form_title">Регистрация</h2>
              <?php if ($success): ?>
              <p class="auth-success">Вы зарегистрированы. <a href="login.php">Войти</a></p>
              <?php else: ?>
              <?php if ($error): ?>
              <p class="auth-error"><?= htmlspecialchars($error) ?></p>
              <?php endif; ?>
              <form method="post" action="register.php">
                <div class="form_group">
                  <label class="form_label" for="email">Email</label>
                  <input class="form_input" type="email" id="email" name="email" required
                         value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form_group">
                  <label class="form_label" for="password">Пароль</label>
                  <input class="form_input" type="password" id="password" name="password" required minlength="6">
                </div>
                <div class="form_group">
                  <label class="form_label" for="password2">Повторите пароль</label>
                  <input class="form_input" type="password" id="password2" name="password2" required>
                </div>
                <div class="form_group">
                  <label class="form_label" for="first_name">Имя</label>
                  <input class="form_input" type="text" id="first_name" name="first_name"
                         value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                </div>
                <div class="form_group">
                  <label class="form_label" for="last_name">Фамилия</label>
                  <input class="form_input" type="text" id="last_name" name="last_name"
                         value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                </div>
                <div class="form_group">
                  <label class="form_label" for="phone">Телефон</label>
                  <input class="form_input" type="tel" id="phone" name="phone"
                         value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                <div class="form_group">
                  <button class="form_button" type="submit">Зарегистрироваться</button>
                </div>
              </form>
              <p class="auth-link">Уже есть аккаунт? <a href="login.php">Вход</a></p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php';
