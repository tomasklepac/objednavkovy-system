-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Pát 03. říj 2025, 21:51
-- Verze serveru: 10.4.32-MariaDB
-- Verze PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `objednavkovy_system`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `total_cents` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `status`, `note`, `total_cents`, `created_at`, `street`, `city`, `zip`) VALUES
(4, 3, 'shipped', '', 49800, '2025-09-30 21:02:06', '', '', ''),
(5, 3, 'confirmed', '', 1180, '2025-09-30 21:02:47', '', '', ''),
(6, 3, 'delivered', '', 24900, '2025-10-01 09:27:44', '', '', ''),
(7, 3, 'confirmed', '', 590, '2025-10-01 09:29:09', '', '', ''),
(8, 5, 'shipped', '', 23580, '2025-10-01 09:38:01', '', '', ''),
(9, 5, 'delivered', '', 590, '2025-10-02 07:23:31', '', '', ''),
(10, 5, 'confirmed', '', 13310, '2025-10-02 14:12:40', '', '', ''),
(11, 5, 'confirmed', '', 9950, '2025-10-02 16:28:24', 'Komenského 149', 'Starý Plzenec', '33202'),
(12, 5, 'canceled', '', 590, '2025-10-02 16:29:24', 'Komenského 149', 'Starý Plzenec', '33202'),
(13, 5, 'confirmed', '', 2490, '2025-10-02 16:49:29', 'Komenského 149', 'Starý Plzenec', '33202');

-- --------------------------------------------------------

--
-- Struktura tabulky `order_item`
--

CREATE TABLE `order_item` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price_cents` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `order_item`
--

INSERT INTO `order_item` (`order_id`, `product_id`, `quantity`, `unit_price_cents`) VALUES
(4, 2, 20, 2490),
(5, 4, 2, 590),
(6, 2, 10, 2490),
(7, 4, 1, 590),
(8, 1, 5, 1990),
(8, 2, 5, 2490),
(8, 4, 2, 590),
(9, 4, 1, 590),
(10, 1, 3, 1990),
(10, 2, 2, 2490),
(10, 4, 4, 590),
(11, 1, 5, 1990),
(12, 4, 1, 590),
(13, 2, 1, 2490);

-- --------------------------------------------------------

--
-- Struktura tabulky `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `price_cents` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price_cents`, `stock`, `image_path`, `supplier_id`, `is_active`, `created_at`) VALUES
(1, 'Jablko červené', 'Čerstvé šťavnaté jablko z farmy', 1990, 8, 'uploads/prod_68e00fcb6d2193.29855128.jpg', 2, 1, '2025-09-27 14:47:35'),
(2, 'Hruška žlutá', 'Sladká hruška z Jižní Moravy', 2490, 9, 'uploads/prod_68e011c80f0430.53612348.jpg', 2, 1, '2025-09-27 19:13:37'),
(4, 'Švestka fialová', 'Mňam', 590, 6, 'uploads/prod_68e014125b3083.52865953.jpg', 7, 1, '2025-09-29 18:34:09');

-- --------------------------------------------------------

--
-- Struktura tabulky `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `code` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `roles`
--

INSERT INTO `roles` (`id`, `code`) VALUES
(3, 'admin'),
(1, 'customer'),
(2, 'supplier');

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(120) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `is_active`, `created_at`) VALUES
(1, 'admin@local.test', '$2y$10$uGizEsfsRj7kdHVF0qRTXeyav3BD3BXDph3Y0y4OlxGJFEYBE0EpW', 'Admin', 1, '2025-09-27 13:27:38'),
(2, 'supplier@local.test', '$2y$10$G3Y2.R7fsX9eYudr0gF4MeVzho9VLenulmX3AdrXwWP/AR6Vqi65m', 'Dodavatel', 1, '2025-09-27 14:44:18'),
(3, 'customer@local.test', '$2y$10$WLTKTS.qz6L2ONdKx/OvZ.KhY99i.IBKVVNWVmEe6nIpkCRqsLmpW', 'Zákazník', 1, '2025-09-27 19:13:13'),
(5, 'tomasklepac@post.cz', '$2y$10$S0LBa9RYgmQRqVJhL2HAjuEgRUrTZn6i3C3VnKgZSiGmmbj1KGw7a', 'Tomáš Klepač', 1, '2025-09-29 17:19:35'),
(7, 'petrnovak@local.test', '$2y$10$442oRYMbiPcw2IjktG2dVuO/NoHPwE3FRDBdjc.LmSPgNo2er69C6', 'Petr Novák', 1, '2025-09-29 18:33:04');

-- --------------------------------------------------------

--
-- Struktura tabulky `user_role`
--

CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
(1, 3),
(2, 2),
(3, 1),
(5, 1),
(7, 2);

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexy pro tabulku `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexy pro tabulku `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexy pro tabulku `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexy pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexy pro tabulku `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pro tabulku `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pro tabulku `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Omezení pro tabulku `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
