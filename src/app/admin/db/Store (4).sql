  -- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Apr 04, 2022 at 02:00 PM
-- Server version: 8.0.19
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Store`
--

-- --------------------------------------------------------

--
-- Table structure for table `components`
--

CREATE TABLE `components` (
  `id` int NOT NULL,
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `components`
--

INSERT INTO `components` (`id`, `component`) VALUES
(13, 'index'),
(14, 'acl'),
(15, 'secure'),
(17, 'index'),
(19, 'product'),
(20, 'order'),
(21, 'login'),
(22, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `product` varchar(255) NOT NULL,
  `quantity` int NOT NULL,
  `created_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_name`, `customer_address`, `zipcode`, `product`, `quantity`, `created_date`) VALUES
(17, 'Vikas', 'Jankipuram', '92347', 'NesCaffe - Best Seller', 2, '2022-04-04');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int NOT NULL,
  `role` varchar(255) NOT NULL,
  `component` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `role`, `component`, `action`) VALUES
(64, 'admin', '*', '*'),
(80, 'guest', 'index', 'index'),
(81, 'guest', 'login', 'index'),
(82, 'manager', 'product', 'productList'),
(83, 'manager', 'product', 'addProduct'),
(84, 'manager', 'product', 'editProduct'),
(85, 'Accountant', 'order', 'orderList'),
(86, 'Accountant', 'order', 'editOrder'),
(87, 'Accountant', 'order', 'addOrder'),
(88, 'Accountant', 'index', 'dashboard'),
(89, 'manager', 'index', 'dashboard');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `price` int DEFAULT NULL,
  `stocks` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `tags`, `price`, `stocks`) VALUES
(70, 'Mobilenew', 'Nokia 7772', 'new', 10000, 200),
(109, 'Samsung 110', 'Best Phone in the world', 'Latest', 10000, 21),
(111, 'NesCaffe - Best Seller', 'Its A Life :)', 'Best Seller', 100, 8000),
(112, 'Dairy - ', 'Beautiful long page dairy', '', 100, 12),
(113, 'Blue Dot Pen - ', 'Blue Renolance Pen ', '', 10, 10);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role`) VALUES
(4, 'manager'),
(5, 'admin'),
(6, 'admin'),
(7, 'admin'),
(8, 'manager'),
(9, 'guest'),
(10, 'manager'),
(11, 'user'),
(14, 'Accountant');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `title_optimization` varchar(255) NOT NULL,
  `default_price` int NOT NULL,
  `default_stock` int NOT NULL,
  `default_zipcode` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `title_optimization`, `default_price`, `default_stock`, `default_zipcode`) VALUES
(1, 'Y', 100, 100, 226010);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `jwt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `jwt`) VALUES
(34, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IlZpa2FzIFJhdGhvcmUiLCJlbWFpbCI6InZpa2FzQGNlZGNvc3MuY29tIiwicGFzc3dvcmQiOiIxMjMiLCJyb2xlIjoiYWRtaW4ifQ.o0iU6CE0xGix0Co2T40Zaqq26NVyd7xvyJuSGfxC_oM'),
(35, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IlNoaXZhbSBTaW5naCIsImVtYWlsIjoic2FsbWFuQGdtYWlsLmNvbSIsInBhc3N3b3JkIjoiMTIzIiwicm9sZSI6Im1hbmFnZXIifQ.59k5PPXuENZMQN7q4qEPHnbR3ZNVK0l0gBkmcKFmEDQ'),
(36, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IlJpc2hhYmgiLCJlbWFpbCI6InJpc2hhYmhAZ21haWwuY29tIiwicGFzc3dvcmQiOiIxMjMiLCJyb2xlIjoiQWNjb3VudGFudCJ9.MDjY00R2fVCE2de_qalOEb3_IsCs7aC4o7IOk2dRzi8');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `components`
--
ALTER TABLE `components`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `components`
--
ALTER TABLE `components`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
