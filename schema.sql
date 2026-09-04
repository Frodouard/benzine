CREATE DATABASE IF NOT EXISTS `mickyshop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mickyshop`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(30) NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('customer','admin','trader') NOT NULL DEFAULT 'customer',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image` VARCHAR(255) NULL,
  `category_id` INT UNSIGNED NULL,
  `seller_id` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT UNSIGNED NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `delivery_address` VARCHAR(255) NOT NULL,
  `contact_phone` VARCHAR(50) NULL,
  `contact_email` VARCHAR(150) NULL,
  `status` ENUM('Pending','Confirmed','Out for delivery','Delivered') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sender_id` INT UNSIGNED NULL,
  `receiver_id` INT UNSIGNED NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`) VALUES
('MICKY Admin', 'admin@mickyshop.com', '0000000000', '$2y$12$cB3BC5HZRcppHcbH7xr3c.W4jCGnBTCvPXIwkQChj/X8aRZc.E4Fq', 'admin'),
('Sample Trader', 'trader@mickyshop.com', '0000000000', '$2y$12$DwNQhmLntoIqGCe8J8ySYulzVdmA3F5EaMVPquNrwK25b5cuXoILi', 'trader')
ON DUPLICATE KEY UPDATE `email` = `email`;

INSERT INTO `categories` (`name`) VALUES
('Electronics'),
('Home'),
('Fashion'),
('Kids')
ON DUPLICATE KEY UPDATE `name` = `name`;
