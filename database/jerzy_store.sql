-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 11:23 AM
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
-- Database: `jerzy_store`
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jerseys`
--

INSERT INTO `jerseys` (`id`, `name`, `category`, `price`, `created_at`) VALUES
(1, 'Arsenal', 'football', 1500.00, '2025-06-23 08:27:32'),
(2, 'Man united', 'football', 1500.00, '2025-06-23 08:34:58'),
(3, 'Barcelona', 'football', 1500.00, '2025-06-23 08:44:56'),
(5, 'chelsea', 'football_home_kit', 1800.00, '2025-06-23 08:45:43'),
(6, 'chelsea', 'football_away_kit', 1800.00, '2025-06-23 08:45:57'),
(7, 'Real madrid', 'football_away_kit', 1500.00, '2025-06-23 08:46:18'),
(8, 'Real madrid', 'football_away_kit', 2500.00, '2025-06-23 08:46:38');

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
  `role` enum('user','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `role`) VALUES
(1, 'omar', '$2y$10$1Bel79XwkG94Ol/Ebnu6..WomrfeXpypBSMbZyeACXUK5iW6B5FH.', 'hommiedelaco@gmail.com', '2025-06-23 08:18:56', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jerseys`
--
ALTER TABLE `jerseys`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
