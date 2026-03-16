-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.4:3306
-- Время создания: Мар 17 2026 г., 01:07
-- Версия сервера: 8.4.6
-- Версия PHP: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `ourplace`
--

-- --------------------------------------------------------

--
-- Структура таблицы `addresses`
--

CREATE TABLE `addresses` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Home, Work, etc.',
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USA',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'для гостей',
  `product_id` int UNSIGNED NOT NULL,
  `product_variant_id` int UNSIGNED DEFAULT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `parent_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Кастрюли', 'pans', 'Pans and skillets', 10, 1, '2026-03-06 01:26:35', '2026-03-06 08:28:24'),
(2, NULL, 'Миски', 'bowls', 'Side bowls and serving bowls', 20, 1, '2026-03-06 01:26:35', '2026-03-06 08:28:09'),
(3, NULL, 'Тарелки', 'plates', 'Main plates and dinnerware', 30, 1, '2026-03-06 01:26:35', '2026-03-06 08:28:29'),
(4, NULL, 'Стаканы', 'glasses', 'Drinking glasses', 40, 1, '2026-03-06 01:26:35', '2026-03-06 08:28:41'),
(8, NULL, 'Сковороды', 'frying pans', NULL, 50, 1, '2026-03-06 08:34:35', '2026-03-06 08:34:35');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `status` enum('pending','paid','processing','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_address_id` int UNSIGNED DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `total_amount`, `shipping_address_id`, `customer_email`, `customer_name`, `customer_phone`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'pending', 580.00, NULL, 'admin@ourplace.local', 'Admin', NULL, 'Адрес: Новооктябрская, Гродно, Беларусь', '2026-03-06 01:31:10', '2026-03-06 01:31:10'),
(2, 1, 'pending', 100.00, NULL, 'admin@ourplace.local', 'Admin', NULL, 'Адрес: Новооктябрская, Гродно, Беларусь', '2026-03-06 09:32:27', '2026-03-06 09:32:27'),
(3, 2, 'pending', 295.00, NULL, 'toxi4ytg@gmail.com', 'Сергей Шульга', '+375 (33) 610-61-51', 'Адрес: Дубко 20, Гродно, Беларусь', '2026-03-06 10:42:16', '2026-03-06 10:42:16'),
(4, 2, 'pending', 245.00, NULL, 'toxi4ytg@gmail.com', 'Сергей Шульга', '+375 (33) 610-61-51', 'Адрес: Дубко 20, Гродно, Беларусь', '2026-03-06 11:00:56', '2026-03-06 11:00:56'),
(5, 2, 'pending', 345.00, NULL, 'toxi4ytg@gmail.com', 'Сергей Шульга', '+375 (33) 610-61-51', 'Адрес: Дубко 20, Гродно, Беларусь', '2026-03-09 14:55:26', '2026-03-09 14:55:26');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int UNSIGNED NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `product_variant_id` int UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `price_at_order` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now())
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_variant_id`, `product_name`, `quantity`, `price_at_order`, `created_at`) VALUES
(1, 1, 1, NULL, 'Always Pan', 4, 145.00, '2026-03-06 01:31:10'),
(2, 2, 3, NULL, 'Обеденные тарелки', 2, 50.00, '2026-03-06 09:32:27'),
(3, 3, 3, NULL, 'Обеденные тарелки', 1, 50.00, '2026-03-06 10:42:16'),
(4, 3, 1, NULL, 'Пирожковые тарелки', 1, 145.00, '2026-03-06 10:42:16'),
(5, 3, 4, NULL, 'Сковорода Always Pan', 2, 50.00, '2026-03-06 10:42:16'),
(6, 4, 3, NULL, 'Обеденные тарелки', 1, 50.00, '2026-03-06 11:00:56'),
(7, 4, 1, NULL, 'Пирожковые тарелки', 1, 145.00, '2026-03-06 11:00:56'),
(8, 4, 4, NULL, 'Сковорода Always Pan', 1, 50.00, '2026-03-06 11:00:56'),
(9, 5, 5, NULL, 'Графин', 1, 150.00, '2026-03-09 14:55:26'),
(10, 5, 3, NULL, 'Обеденные тарелки', 1, 50.00, '2026-03-09 14:55:26'),
(11, 5, 1, NULL, 'Пирожковые тарелки', 1, 145.00, '2026-03-09 14:55:26');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `sku`, `stock_quantity`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 'Пирожковые тарелки', 'always-pan', 'Продуманная конструкция позволяет выполнять работу восьми предметов традиционной посуды.', 145.00, 'AP-001', 100, 1, '2026-03-06 01:26:35', '2026-03-06 08:31:24'),
(2, 4, 'Стаканы', 'side-bowls', 'Ручная работа и возможность штабелирования. Изготовлен из переработанного стекла и натурального песка. Набор из 4.', 45.00, 'SB-001', 80, 1, '2026-03-06 01:26:35', '2026-03-06 08:32:09'),
(3, 3, 'Обеденные тарелки', 'drinking-glasses', 'Фарфоровые тарелки с ручной росписью, штабелируемые и рассчитанные на большой аппетит. Набор из 4.', 50.00, 'DG-001', 60, 1, '2026-03-06 01:26:35', '2026-03-06 08:32:00'),
(4, 8, 'Сковорода Always Pan', 'main-plates', 'Сковорода Always Pan, керамическое антипригарное покрытие\r\nРучная работа, многофункциональность (заменяет 8 предметов кухонной утвари). Изготовлена из переработанного алюминия и натуральных материалов.', 50.00, 'MP-001', 70, 1, '2026-03-06 01:26:35', '2026-03-06 08:35:20'),
(5, 4, 'Графин', 'graf', 'Графин раритетный', 150.00, 'GF-001', 50, 1, '2026-03-06 11:03:20', '2026-03-06 11:03:20');

-- --------------------------------------------------------

--
-- Структура таблицы `product_images`
--

CREATE TABLE `product_images` (
  `id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT (now())
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. Color, Size',
  `value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. Terracotta, Sky',
  `sku_suffix` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_modifier` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT (now())
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `name`, `value`, `sku_suffix`, `price_modifier`, `stock_quantity`, `sort_order`, `created_at`) VALUES
(1, 1, 'Color', 'Terracotta', NULL, 0.00, 15, 1, '2026-03-06 01:26:35'),
(2, 1, 'Color', 'Sky', NULL, 0.00, 15, 2, '2026-03-06 01:26:35'),
(3, 1, 'Color', 'Spice', NULL, 0.00, 15, 3, '2026-03-06 01:26:35'),
(4, 1, 'Color', 'Sage', NULL, 0.00, 15, 4, '2026-03-06 01:26:35'),
(5, 1, 'Color', 'Tomato', NULL, 0.00, 15, 5, '2026-03-06 01:26:35'),
(6, 1, 'Color', 'Oat', NULL, 0.00, 15, 6, '2026-03-06 01:26:35'),
(7, 1, 'Color', 'Steel', NULL, 0.00, 15, 7, '2026-03-06 01:26:35'),
(8, 1, 'Color', 'Lilac', NULL, 0.00, 10, 8, '2026-03-06 01:26:35'),
(9, 2, 'Color', 'Terracotta', NULL, 0.00, 30, 1, '2026-03-06 01:26:35'),
(10, 2, 'Color', 'Oat', NULL, 0.00, 25, 2, '2026-03-06 01:26:35'),
(11, 2, 'Color', 'Steel', NULL, 0.00, 25, 3, '2026-03-06 01:26:35'),
(12, 3, 'Color', 'Sand', NULL, 0.00, 20, 1, '2026-03-06 01:26:35'),
(13, 3, 'Color', 'Amber', NULL, 0.00, 20, 2, '2026-03-06 01:26:35'),
(14, 3, 'Color', 'Olive', NULL, 0.00, 10, 3, '2026-03-06 01:26:35'),
(15, 3, 'Color', 'White', NULL, 0.00, 10, 4, '2026-03-06 01:26:35'),
(16, 4, 'Color', 'Terracotta', NULL, 0.00, 25, 1, '2026-03-06 01:26:35'),
(17, 4, 'Color', 'Oat', NULL, 0.00, 25, 2, '2026-03-06 01:26:35'),
(18, 4, 'Color', 'Steel', NULL, 0.00, 20, 3, '2026-03-06 01:26:35');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5, 'Клафные талелки вуб даю', 1, '2026-03-06 01:33:14', '2026-03-06 01:33:14');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('customer','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin@ourplace.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', '', NULL, 'admin', 1, '2026-03-06 01:26:35', '2026-03-06 01:26:35'),
(2, 'toxi4ytg@gmail.com', '$2y$12$/2c0tDgwJZtXcnWRbmM0FuUoqrEPIZecC6SlpnsL5m5M3WS6ZImsq', 'Сергей', 'Шульга', '+375 (33) 610-61-51', 'customer', 1, '2026-03-06 08:04:19', '2026-03-06 08:04:19');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_addresses_user` (`user_id`);

--
-- Индексы таблицы `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cart_user` (`user_id`),
  ADD KEY `idx_cart_session` (`session_id`),
  ADD KEY `idx_cart_product` (`product_id`),
  ADD KEY `fk_cart_items_variant` (`product_variant_id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categories_slug` (`slug`),
  ADD KEY `idx_categories_parent` (`parent_id`),
  ADD KEY `idx_categories_sort` (`sort_order`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_created` (`created_at`),
  ADD KEY `fk_orders_address` (`shipping_address_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order` (`order_id`),
  ADD KEY `idx_order_items_product` (`product_id`),
  ADD KEY `fk_order_items_variant` (`product_variant_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_products_slug` (`slug`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_sku` (`sku`),
  ADD KEY `idx_products_active` (`is_active`);

--
-- Индексы таблицы `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_images_product` (`product_id`);

--
-- Индексы таблицы `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_variants_product` (`product_id`),
  ADD KEY `idx_product_variants_name_value` (`product_id`,`name`,`value`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reviews_product` (`product_id`),
  ADD KEY `idx_reviews_user` (`user_id`),
  ADD KEY `idx_reviews_approved` (`is_approved`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_created_at` (`created_at`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `fk_addresses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_items_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_address` FOREIGN KEY (`shipping_address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_product_variants_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
