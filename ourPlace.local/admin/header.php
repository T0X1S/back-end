<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' — ' : '' ?><?= $adminTitle ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-page">
<div class="wrapper">
    <header class="header">
        <div class="header_body container">
            <a href="../index.php" class="me-3">На сайт</a>
            <a href="index.php" class="me-3">Админка</a>
            <a href="users.php" class="me-2">Пользователи</a>
            <a href="categories.php" class="me-2">Категории</a>
            <a href="products.php" class="me-2">Товары</a>
            <a href="orders.php" class="me-2">Заказы</a>
            <a href="reviews.php" class="me-2">Отзывы</a>
            <a href="addresses.php" class="me-2">Адреса</a>
            <div class="ms-auto"><a href="../profile.php" class="btn btn-outline-secondary btn-sm">Профиль</a></div>
        </div>
    </header>
    <main class="main">
