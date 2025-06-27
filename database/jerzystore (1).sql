-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 11:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jerzystore`
--

-- --------------------------------------------------------

--
-- Table structure for table `jerseys`
--

CREATE TABLE `jerseys` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jerseys`
--

INSERT INTO `jerseys` (`id`, `name`, `category`, `price`, `created_at`, `image_path`) VALUES
(11, 'chelsea home kit', 'XPGHD01', 2800.00, '2025-06-23 13:38:51', 'uploads/chelsea_h1.jpg'),
(12, 'Man united away kit', 'XPHY002', 2800.00, '2025-06-23 13:48:17', 'uploads/manu_away.png'),
(13, 'Man united home kit', 'XPHY003', 1500.00, '2025-06-23 14:01:39', 'uploads/home1.webp'),
(14, 'Barcelona home kit', 'XPHY004', 1500.00, '2025-06-24 13:31:07', 'uploads/barca_h.png'),
(15, 'Man united Away kit', 'XPGHD05', 1500.00, '2025-06-24 14:05:18', 'uploads/man away.png'),
(16, 'Man united Away kit', 'XPHY006', 1500.00, '2025-06-24 14:05:32', 'uploads/man_a.webp'),
(17, 'Chelsea away kit', 'XPGHD07', 1500.00, '2025-06-24 14:05:55', 'uploads/chelseaawayy.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `jersey_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `quantity` int(11) DEFAULT 1,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `users_id`, `jersey_id`, `name`, `price`, `image_path`, `created_at`, `quantity`, `status`) VALUES
(14, 4, 13, 'Man united home kit', 1500.00, 'uploads/home1.webp', '2025-06-24 12:30:19', 1, 'pending'),
(28, 5, 11, 'chelsea home kit', 2800.00, 'uploads/chelsea_h1.jpg', '2025-06-26 07:38:35', 1, 'pending'),
(30, 3, 13, 'Man united home kit', 1500.00, 'uploads/home1.webp', '2025-06-27 09:48:52', 1, 'submitted'),
(31, 3, 14, 'Barcelona home kit', 1500.00, 'uploads/barca_h.png', '2025-06-27 11:20:06', 1, 'submitted'),
(35, 3, 12, 'Man united Away kit', 1500.00, 'uploads/man away.png', '2025-06-27 12:19:40', 1, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `role`, `reset_token`, `reset_token_expiry`) VALUES
(1, 'omar', '$2y$10$qt6t5QS914DyZXIHLpZ6Me074.TBs/ABGAVoojSmMCJHkzKQwrA9G', 'hommiedelaco@gmail.com', '2025-06-23 08:18:56', 'admin', '337a890982229fa2990e6c4aeaf0b568', '2025-06-26 08:33:02'),
(3, 'sufiani', '$2y$10$d5awDbn0a8e/wiTE9wpFc.bnKrrNEvFfB5pez4GpP8LPwwqegs9Wm', 'suf@gmail.com', '2025-06-23 12:43:35', 'user', NULL, NULL),
(4, 'Kututa', '$2y$10$RhjY.sBUEeut1aP7LQ81gOEn5Hzdwj44rVYhqEN0qVnZxgRfjY/Oe', 'vinnykututa@gmail.com', '2025-06-24 19:30:04', 'user', NULL, NULL),
(5, 'SWABRI', '$2y$10$LpU8Kddtnc0SS0lRBo2qaO4gA4jqQ1s4rpyxTopKiZ3M/u2NJ9mZu', 'mohammedswabri65@gmail.com', '2025-06-26 11:16:44', 'user', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jerseys`
--
ALTER TABLE `jerseys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `jersey_id` (`jersey_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jerseys`
--
ALTER TABLE `jerseys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`jersey_id`) REFERENCES `jerseys` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
