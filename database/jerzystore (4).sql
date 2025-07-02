-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2025 at 06:32 PM
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
(37, 3, 12, 'Man united Away kit', 1500.00, 'uploads/man_a.webp', '2025-06-28 17:03:02', 1, 'shipped'),
(41, 3, 13, 'Man united home kit', 1500.00, 'uploads/home1.webp', '2025-06-29 22:45:21', 1, 'shipped');

-- --------------------------------------------------------

--
-- Table structure for table `tracker`
--

CREATE TABLE `tracker` (
  `id` int(11) NOT NULL,
  `orders_id` int(11) NOT NULL,
  `orders_status` varchar(20) NOT NULL,
  `mpesa_message` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tracker`
--

INSERT INTO `tracker` (`id`, `orders_id`, `orders_status`, `mpesa_message`) VALUES
(0, 37, 'shipped', 'TFT3WSJHVV'),
(0, 41, 'shipped', 'TFT3WSJHVK');

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
(3, 'sufiani', '$2y$10$d5awDbn0a8e/wiTE9wpFc.bnKrrNEvFfB5pez4GpP8LPwwqegs9Wm', 'sufyanomar58@gmail.com', '2025-06-23 12:43:35', 'user', NULL, NULL),
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
  ADD KEY `jersey_id` (`jersey_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `tracker`
--
ALTER TABLE `tracker`
  ADD UNIQUE KEY `orders_id_2` (`orders_id`),
  ADD KEY `orders_status` (`orders_status`),
  ADD KEY `orders_id` (`orders_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

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

--
-- Constraints for table `tracker`
--
ALTER TABLE `tracker`
  ADD CONSTRAINT `tracker_ibfk_1` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tracker_ibfk_2` FOREIGN KEY (`orders_status`) REFERENCES `orders` (`status`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
