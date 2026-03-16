<?php
require_once __DIR__ . '/common.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $adminTitle ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-page">
<div class="wrapper">
    <header class="header">
        <div class="header_body container">
            <a href="../index.php" class="header_logo-link">На сайт</a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-primary btn-sm me-2">Главная админки</a>
                <a href="../profile.php" class="btn btn-outline-secondary btn-sm">Профиль</a>
            </div>
        </div>
    </header>
    <main class="main">
        <div class="container">
            <h1 class="admin-page-title mb-4"><?= $adminTitle ?></h1>
            <p class="text-muted mb-4">Управление всеми данными сайта.</p>
            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <a href="users.php" class="admin-card card text-decoration-none text-dark h-100">
                        <div class="card-body">
                            <h3 class="h5 card-title">Пользователи</h3>
                            <p class="card-text small text-muted">Редактирование пользователей, ролей</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="categories.php" class="admin-card card text-decoration-none text-dark h-100">
                        <div class="card-body">
                            <h3 class="h5 card-title">Категории</h3>
                            <p class="card-text small text-muted">Категории товаров</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="products.php" class="admin-card card text-decoration-none text-dark h-100">
                        <div class="card-body">
                            <h3 class="h5 card-title">Товары</h3>
                            <p class="card-text small text-muted">Товары, варианты, изображения</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="orders.php" class="admin-card card text-decoration-none text-dark h-100">
                        <div class="card-body">
                            <h3 class="h5 card-title">Заказы</h3>
                            <p class="card-text small text-muted">Список и статусы заказов</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="reviews.php" class="admin-card card text-decoration-none text-dark h-100">
                        <div class="card-body">
                            <h3 class="h5 card-title">Отзывы</h3>
                            <p class="card-text small text-muted">Модерация отзывов</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="addresses.php" class="admin-card card text-decoration-none text-dark h-100">
                        <div class="card-body">
                            <h3 class="h5 card-title">Адреса</h3>
                            <p class="card-text small text-muted">Адреса доставки пользователей</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
