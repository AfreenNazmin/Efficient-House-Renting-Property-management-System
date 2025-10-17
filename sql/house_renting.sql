-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 04:56 AM
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
-- Database: `house_renting`
--

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversation_participants`
--

CREATE TABLE `conversation_participants` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_read` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

CREATE TABLE `favourites` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favourites`
--

INSERT INTO `favourites` (`id`, `tenant_id`, `property_id`, `created_at`) VALUES
(1, 1, 1, '2025-10-09 04:59:48');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_users`
--

CREATE TABLE `pending_users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('tenant','landlord') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `rent` int(11) NOT NULL,
  `landlord` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `bedrooms` int(11) DEFAULT 1,
  `property_type` varchar(50) DEFAULT 'Apartment',
  `description` text DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `posted_date` date NOT NULL DEFAULT curdate(),
  `featured` tinyint(1) DEFAULT 0,
  `size` varchar(50) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT 1,
  `floor` varchar(50) DEFAULT NULL,
  `parking` tinyint(1) DEFAULT 0,
  `furnished` tinyint(1) DEFAULT 0,
  `map_embed` text DEFAULT NULL,
  `floor_plan` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `status` enum('Rent','Sell') NOT NULL DEFAULT 'Rent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `property_name`, `location`, `rent`, `landlord`, `image`, `bedrooms`, `property_type`, `description`, `available`, `posted_date`, `featured`, `size`, `bathrooms`, `floor`, `parking`, `furnished`, `map_embed`, `floor_plan`, `latitude`, `longitude`, `status`) VALUES
(1, 'something', 'dhaka', 1000, 'Afreen ', 'uploads/OIP.jfif', 1, 'Apartment', NULL, 1, '2025-10-06', 0, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, 'Rent'),
(4, 'Flat X', 'Dhaka', 1200, 'Rakib', 'downloads/pexels-pixabay-210538.jpg', 2, 'Apartment', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, 23.810300, 90.412500, 'Rent'),
(5, 'Grand Residency', 'Dhaka', 1200, 'RKB', 'uploads/1759751036_pexels-pixabay-210538.jpg', 3, 'xyz', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, 23.810300, 90.412500, 'Rent'),
(6, 'Flat X', 'Gulshan-1', 50000, 'RKB', 'uploads/1759751302_pexels-pixabay-164558.jpg', 5, 'abc', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, 23.811700, 90.421000, 'Rent'),
(7, 'Green Villa', 'Savar', 15000, 'RKB', 'uploads/1759751515_4.avif', 5, 'Apartment', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, 23.868200, 90.255900, 'Rent'),
(8, 'Lakeview Studio', 'Gulshan', 50000, 'RKB', 'uploads/1759751591_12.webp', 5, 'Flat', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, 23.811700, 90.421000, 'Rent'),
(9, 'Sunset Villa', 'Chandpur', 8000, 'mehedi hasan rakib', 'uploads/1759760223_9.jpeg', 4, 'House type', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, 'Rent'),
(10, 'Oakwood Villa', 'mohamoodpur', 5000, 'mehedi hasan rakib', 'uploads/1759760467_7.jpg', 3, 'Studio type', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, 'Rent'),
(11, 'Pinewood House', 'Narayanganj', 10000, 'mehedi hasan rakib', 'uploads/1759760642_10.jpg', 4, 'Apartment type', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, 'Rent');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rental_requests`
--

CREATE TABLE `rental_requests` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `tenant_name` varchar(255) NOT NULL,
  `tenant_email` varchar(255) NOT NULL,
  `tenant_phone` varchar(20) NOT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `move_in_date` date NOT NULL,
  `rental_period` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `current_address` text DEFAULT NULL,
  `emergency_contact` text NOT NULL,
  `notes` text DEFAULT NULL,
  `terms` tinyint(1) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rental_requests`
--

INSERT INTO `rental_requests` (`id`, `property_id`, `tenant_name`, `tenant_email`, `tenant_phone`, `national_id`, `move_in_date`, `rental_period`, `payment_method`, `current_address`, `emergency_contact`, `notes`, `terms`, `request_date`) VALUES
(1, 1, 'Tanvir', 'mrk243719@gmail.com', '01234567890', '', '2025-10-10', 4, 'cash', '', '0123456789', 'rrfg', 1, '2025-10-07 11:04:41'),
(2, 1, 'Tanvir', 'mrk243719@gmail.com', '01234567890', '', '2025-10-10', 4, 'cash', '', '0123456789', 'rrfg', 1, '2025-10-07 11:09:26'),
(3, 1, 'Tanvir', 'mrk243719@gmail.com', '01234567890', '', '2025-10-10', 4, 'cash', '', '0123456789', 'rrfg', 1, '2025-10-07 11:09:56');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roommates`
--

CREATE TABLE `roommates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `budget` int(11) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `smoking` enum('Yes','No') DEFAULT NULL,
  `pets` enum('Yes','No') DEFAULT NULL,
  `cleanliness` enum('High','Medium','Low') DEFAULT NULL,
  `about` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `role` varchar(20) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `created_at`, `is_verified`, `role`, `reset_token`, `reset_expiry`) VALUES
(1, 'Afreen ', 'afreen@gmail.com', '$2y$10$41p7IIaZq1CsYC/4Az1.MeTW4mKidgv2kCUSabgJcSxCO6ND/Xw6m', NULL, '2025-09-18 14:25:33', 1, 'tenant', NULL, NULL),
(3, 'Afreen ', 'afreen1@gmail.com', '$2y$10$3UXVsxxSefeYX46L9WSkU.0FXu5NIrmIMcFl6sx.K9ynJkqCo5BXe', NULL, '2025-09-18 14:30:43', 1, 'landlord', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `idx_conv_id` (`conversation_id`);

--
-- Indexes for table `pending_users`
--
ALTER TABLE `pending_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `rental_requests`
--
ALTER TABLE `rental_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `roommates`
--
ALTER TABLE `roommates`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favourites`
--
ALTER TABLE `favourites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_users`
--
ALTER TABLE `pending_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rental_requests`
--
ALTER TABLE `rental_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roommates`
--
ALTER TABLE `roommates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD CONSTRAINT `conversation_participants_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`),
  ADD CONSTRAINT `conversation_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`),
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rental_requests`
--
ALTER TABLE `rental_requests`
  ADD CONSTRAINT `rental_requests_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
