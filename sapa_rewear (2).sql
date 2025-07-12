-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2025 at 01:11 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sapa_rewear`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Shoaib Arif Tamboli', 'shoaib@admin.com', '$2y$10$ZECjRv89mLv1ZQ/.mJhrOeehUFG.hWXKBK6c39lI4Y2u0E9CDcKzu', '2025-07-12 09:16:13');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `condition_state` varchar(50) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `availability` enum('Available','Swap Only') DEFAULT 'Available',
  `is_swapped` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `title`, `description`, `category`, `type`, `size`, `condition_state`, `tags`, `image_path`, `status`, `user_id`, `created_at`, `availability`, `is_swapped`) VALUES
(1, 'Decathlon Shirt', 'Khadi Shirt', 'Tops', 'Men', 'L', 'New with tags', '', 'uploads/1752297710_Shirt.jpg', 'pending', 1, '2025-07-12 05:21:50', 'Available', 0),
(2, 'Shorts', 'Running Baggy Shorts', 'Bottoms', 'Men', 'Free Size', 'Like new', '', 'uploads/1752297913_short.jpg', 'pending', 2, '2025-07-12 05:25:13', 'Available', 0),
(3, 'Sweater', 'Oversized Vintage knit Sweater', 'Tops', 'Men', 'XL', 'Like new', '', 'uploads/1752300321_Sweater.jpg', NULL, 2, '2025-07-12 06:05:21', 'Available', 0),
(5, 'Navy Blue Sweater', 'Navy Blue Sweater / Sweatshirt for Girls Woolen Knitted', 'Outerwear', 'Women', 'M', 'Gently used', '', 'uploads/1752303655_Sw.jpg', 'approved', 1, '2025-07-12 07:00:56', 'Swap Only', 0);

-- --------------------------------------------------------

--
-- Table structure for table `item_images`
--

CREATE TABLE `item_images` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_images`
--

INSERT INTO `item_images` (`id`, `item_id`, `image_path`) VALUES
(1, 1, 'uploads/1752297710_Shirt.jpg'),
(2, 2, 'uploads/1752297913_short.jpg'),
(3, 3, 'uploads/1752300321_Sweater.jpg'),
(4, 5, 'uploads/1752303655_Sw.jpg'),
(5, 5, 'uploads/1752303655_sw2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `redeem_logs`
--

CREATE TABLE `redeem_logs` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `redeemed_by` int(11) NOT NULL,
  `points_used` int(11) NOT NULL,
  `redeemed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `swap_requests`
--

CREATE TABLE `swap_requests` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `counterpart_item_id` int(11) DEFAULT NULL,
  `confirmed_by_owner` tinyint(1) DEFAULT 0,
  `confirmed_by_requester` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `swap_requests`
--

INSERT INTO `swap_requests` (`id`, `item_id`, `requester_id`, `status`, `requested_at`, `counterpart_item_id`, `confirmed_by_owner`, `confirmed_by_requester`) VALUES
(1, 3, 1, 'pending', '2025-07-12 10:30:48', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `points` int(11) NOT NULL DEFAULT 10,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `created_at`, `points`, `profile_image`) VALUES
(1, 'Shoaib', 'shoaib@mail.com', '$2y$10$1IYDQUfvWpgnmtyIBGp/Ku5xrjLVOv6G4Hnghf52IdKyneNo.GQFG', '8888888888', '2025-07-12 04:01:21', 20, 'user/user_1_1752314689.jpg'),
(2, 'Pranav More', 'pranav@mail.com', '$2y$10$MclOljbUqNAPZGW1bE7.6el0eXLbRXlgJcYjBW45fMJx9fkZeveK.', '', '2025-07-12 05:23:35', 20, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_images`
--
ALTER TABLE `item_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `redeem_logs`
--
ALTER TABLE `redeem_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `redeemed_by` (`redeemed_by`);

--
-- Indexes for table `swap_requests`
--
ALTER TABLE `swap_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `requester_id` (`requester_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `item_images`
--
ALTER TABLE `item_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `redeem_logs`
--
ALTER TABLE `redeem_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `swap_requests`
--
ALTER TABLE `swap_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `item_images`
--
ALTER TABLE `item_images`
  ADD CONSTRAINT `item_images_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `redeem_logs`
--
ALTER TABLE `redeem_logs`
  ADD CONSTRAINT `redeem_logs_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `redeem_logs_ibfk_2` FOREIGN KEY (`redeemed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `swap_requests`
--
ALTER TABLE `swap_requests`
  ADD CONSTRAINT `swap_requests_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `swap_requests_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
