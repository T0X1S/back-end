<?php
if (!function_exists('isLoggedIn')) {
    require __DIR__ . '/auth.php';
}
if (!function_exists('getCartCount')) {
    require_once __DIR__ . '/cart.php';
}
$currentUser = currentUser();
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Our Place — кухонная посуда и аксессуары. Доставка по Беларуси.">
    <meta name="keywords" content="посуда, кухня, сковороды, тарелки, стаканы, Our Place">
    <meta name="author" content="Our Place">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="images/logo.svg">
    <link rel="stylesheet" href="css/index.css">
    <title>Our Place — кухонная посуда</title>
</head>
<body>
<div class="wrapper">
    <header class="header">
      <div class="header_banner">
        <p class="header_banner-title">Доставка и возврат бесплатно по всей Беларуси 🙂</p>
      </div>

      <div class="header_body container">
        <button class="burger-btn">
          <span class="burger-line"></span>
          <span class="burger-line"></span>
          <span class="burger-line"></span>
        </button>
        <nav class="header_links">
          <a class="header_link" href="catalog.php">Каталог</a>
          <a class="header_link" href="index.php#products">Популярное</a>
        </nav>
        <a class="header_logo-link" href="index.php">
          <img class="header_logo" src="images/logo.svg" alt="logo" />
        </a>
        <div class="header_group">
          <nav class="header_links">
            <a class="header_link" href="index.php#mission">Mission</a>
            <a class="header_link" href="index.php#mission">FAQs</a>
            <?php if (isLoggedIn()): ?>
            <a id="header-cart-link" class="header_link header_cart-link" href="cart.php">Корзина<?= $cartCount > 0 ? ' (' . $cartCount . ')' : '' ?></a>
            <?php endif; ?>
          </nav>
          <?php if (isLoggedIn() && $currentUser): ?>
          <?php if ($currentUser['role'] === 'admin'): ?>
          <a href="admin/" class="header_auth-btn header_auth-btn-register" style="padding:6px 12px;font-size:0.85rem;">Админ-панель</a>
          <?php endif; ?>
          <a href="profile.php" class="header_auth-profile" title="Профиль">
            <svg class="header_profile-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span class="header_profile-name"><?= htmlspecialchars(trim($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?: $currentUser['email']) ?></span>
          </a>
          <?php else: ?>
          <div class="header_auth-buttons">
            <a href="login.php" class="header_auth-btn header_auth-btn-login">Вход</a>
            <a href="register.php" class="header_auth-btn header_auth-btn-register">Регистрация</a>
          </div>
          <?php endif; ?>
        </div>
    </div>
    </header>
