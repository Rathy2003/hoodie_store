-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2025 at 06:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `store_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `subprice` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`id`, `product_id`, `qty`, `subprice`) VALUES
(1, 6, 3, 40.98),
(1, 7, 1, 45.19),
(1, 8, 1, 60),
(1, 10, 2, 30),
(1, 11, 2, 28),
(2, 6, 4, 40.98),
(2, 7, 3, 45.19),
(2, 11, 1, 28),
(2, 12, 1, 40),
(2, 13, 1, 69.99),
(3, 8, 2, 60),
(3, 10, 2, 30),
(3, 11, 2, 28),
(3, 12, 2, 40),
(4, 7, 2, 45.19),
(4, 8, 1, 60),
(4, 13, 1, 69.99),
(5, 10, 3, 30),
(5, 11, 2, 28),
(6, 1, 2, 45),
(6, 4, 2, 115),
(6, 12, 3, 40),
(6, 13, 1, 69.99),
(7, 8, 2, 60),
(7, 13, 1, 69.99),
(7, 14, 2, 95.98);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,0) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `date`, `total`, `user_id`) VALUES
(1, '2025-01-12 10:08:53', 344, 2),
(2, '2025-01-12 10:11:13', 437, 2),
(3, '2025-01-12 10:12:14', 316, 3),
(4, '2025-01-12 11:09:08', 220, 3),
(5, '2025-01-12 11:11:14', 146, 3),
(6, '2025-01-18 15:50:34', 510, 4),
(7, '2025-01-21 10:17:18', 382, 5);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `quantity`, `image`) VALUES
(1, 'Hoodie Blue', 45, 150, '1735711442.jpeg'),
(2, 'Hoodie Grey', 45, 80, '1735711461.webp'),
(3, 'Hoodie Brown', 48.5, 98, '1735711484.webp'),
(4, 'Black and White Hoodie', 115, 15, '1735711506.webp'),
(5, 'Black Hoodie', 42.2, 52, '1735711533.webp'),
(6, 'Blue Hoodie', 40.98, 30, '1735711557.webp'),
(7, 'Darker Milk Hoodie', 45.19, 87, '1735711584.webp'),
(8, 'Frank Ocean Hoodie', 60, 8, '1735711602.webp'),
(10, 'Milk Hoodie', 30, 70, '1735711646.webp'),
(11, 'Red Hoodie', 28, 5, '1735711668.jpeg'),
(12, 'White Hoodie', 40, 100, '1735711698.jpg'),
(13, 'Strawberry Sage Hoodie', 69.99, 150, '1736345126.webp'),
(14, 'Stylish hoodie', 95.98, 140, '1737190787.jpg'),
(15, 'Culture hoodie', 29.99, 8, '1737429810.webp'),
(16, 'Tasman Hoodie 2025', 99.98, 50, '1737429923.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `role`) VALUES
(1, 'dev', 'dev', 'dev@gmail.com', '3daddcf50ef5378fab733cf032be968c', 'admin'),
(2, 'Admin', NULL, 'admin@gmail.com', '9580ab5d9db022c73d6678b07c86c9db', 'admin'),
(3, 'John', 'Son', 'johnson@gmail.com', '54058395aab911e9af3795403f0c9571', 'user'),
(4, 'Linplify', NULL, 'linkplify@gmail.com', 'bcc645546a3a2f875def29c9a0fe2556', 'user'),
(5, 'SU', 'Team', 'su1team@gmail.com', '25d55ad283aa400af464c76d713c07ad', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`id`,`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
