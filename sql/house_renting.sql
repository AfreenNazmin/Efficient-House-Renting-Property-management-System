-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 06:45 PM
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
  `phone` varchar(15) DEFAULT NULL,
  `phoneVerified` tinyint(1) NOT NULL DEFAULT 0,
  `emailVerified` tinyint(1) NOT NULL DEFAULT 0,
  `nid_number` varchar(50) DEFAULT NULL,
  `nid_front` varchar(255) DEFAULT NULL,
  `nid_back` varchar(255) DEFAULT NULL,
  `is_landlord_verified` tinyint(1) NOT NULL DEFAULT 0,
  `otp` int(6) DEFAULT NULL,
  `otp_time` datetime DEFAULT NULL,
  `admin_review_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `otp_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_users`
--

INSERT INTO `pending_users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `phone`, `phoneVerified`, `emailVerified`, `nid_number`, `nid_front`, `nid_back`, `is_landlord_verified`, `otp`, `otp_time`, `admin_review_status`, `otp_expires_at`) VALUES
(30, 'afreen', 'mrk243719@gmail.com', '$2y$10$NOPbos0XZwxAxqEfaYUQOeJLXChGAE7hCKpx30S4cXD/bnWbYU3D.', 'tenant', '2025-10-22 16:29:31', '+8801304453089', 0, 0, NULL, NULL, NULL, 0, 922533, '2025-10-22 18:29:31', 'pending', '2025-10-22 18:34:31');

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
  `status` enum('Rent','Sell') NOT NULL DEFAULT 'Rent',
  `rental_type` enum('family','bachelor','roommate','all') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `thumbnail` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `property_name`, `location`, `rent`, `landlord`, `image`, `bedrooms`, `property_type`, `description`, `available`, `posted_date`, `featured`, `size`, `bathrooms`, `floor`, `parking`, `furnished`, `map_embed`, `floor_plan`, `latitude`, `longitude`, `status`, `rental_type`, `created_at`, `thumbnail`) VALUES
(5, 'Grand Residency', 'Dhaka', 1200, 'RKB', 'uploads/1759751036_pexels-pixabay-210538.jpg', 3, 'xyz', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, 23.810300, 90.412500, 'Rent', '', '2025-10-20 19:58:25', NULL),
(6, 'Flat X', 'Gulshan-1', 50000, 'RKB', 'uploads/1759751302_pexels-pixabay-164558.jpg', 5, 'abc', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, 23.811700, 90.421000, 'Rent', '', '2025-10-20 19:58:25', NULL),
(7, 'Green Villa', 'Savar', 15000, 'RKB', 'uploads/1759751515_4.avif', 5, 'Apartment', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, 23.868200, 90.255900, 'Rent', '', '2025-10-20 19:58:25', NULL),
(8, 'Lakeview Studio', 'Gulshan', 50000, 'RKB', 'uploads/1759751591_12.webp', 5, 'Flat', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, 23.811700, 90.421000, 'Rent', '', '2025-10-20 19:58:25', NULL),
(9, 'Sunset Villa', 'Chandpur', 8000, 'mehedi hasan rakib', 'uploads/1759760223_9.jpeg', 4, 'House type', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, 'Rent', '', '2025-10-20 19:58:25', NULL),
(10, 'Oakwood Villa', 'mohamoodpur', 5000, 'mehedi hasan rakib', 'uploads/1759760467_7.jpg', 3, 'Studio type', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, 'Rent', '', '2025-10-20 19:58:25', NULL),
(11, 'Pinewood House', 'Narayanganj', 10000, 'mehedi hasan rakib', 'uploads/1759760642_10.jpg', 4, 'Apartment type', NULL, 1, '2025-10-07', 0, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, 'Rent', '', '2025-10-20 19:58:25', NULL),
(14, 'property', 'dhaka', 1000, 'Afreen ', 'uploads/ChatGPT Image Aug 5, 2025, 11_19_45 AM.png', 4, 'House', 'ec', 1, '2025-10-20', 1, '1500sq', 2, '', 1, 1, '', NULL, 0.000000, 0.000000, 'Rent', '', '2025-10-20 19:58:25', NULL),
(15, 'Murshed villa', 'dhaka', 1000, 'Afreen ', 'uploads/1760965029_error.PNG', 4, 'Apartment', 'tootal wwaste', 1, '2025-10-20', 0, '1500', 2, '4', 1, 0, '', NULL, 0.000000, 0.000000, 'Rent', '', '2025-10-20 19:58:25', NULL),
(16, 'something', 'dhaka', 1000, 'Afreen ', 'uploads/Copilot_20250728_163423.png', 4, 'Room', '......', 1, '2025-10-20', 0, '1500sq', 2, '4th', 0, 0, '', NULL, 0.000000, 0.000000, 'Rent', 'bachelor', '2025-10-20 20:32:11', NULL),
(17, 'nothiing', 'dhaka', 1000, 'Afreen ', 'uploads/Copilot_20250806_035907.png', 4, 'Apartment', '........', 1, '2025-10-20', 1, '1500sq', 2, '4th', 1, 1, '', NULL, 0.000000, 0.000000, 'Rent', '', '2025-10-20 21:05:54', NULL),
(18, 'nothiing', 'dhaka', 1000, 'Afreen ', 'uploads/Copilot_20250729_211410.png', 4, 'Studio', '', 1, '2025-10-20', 0, '1500sq', 2, '4th', 1, 1, '', NULL, 0.000000, 0.000000, 'Sell', '', '2025-10-20 21:49:36', NULL);

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
  `property_name` varchar(255) DEFAULT NULL,
  `tenant_name` varchar(255) NOT NULL,
  `tenant_email` varchar(255) NOT NULL,
  `tenant_phone` varchar(20) NOT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `move_in_date` date NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `current_address` text DEFAULT NULL,
  `emergency_contact` text NOT NULL,
  `notes` text DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `terms` tinyint(1) NOT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rental_requests`
--

INSERT INTO `rental_requests` (`id`, `property_id`, `property_name`, `tenant_name`, `tenant_email`, `tenant_phone`, `national_id`, `move_in_date`, `payment_method`, `current_address`, `emergency_contact`, `notes`, `document_path`, `status`, `terms`, `pdf_file`, `request_date`) VALUES
(4, 14, NULL, 'afreen', 'afreen@gmail.com', '01234567890', '1234688', '2025-11-02', 'cash', 'dhaka', '0123456789', 'ff', NULL, 'pending', 1, 'tenant_68f78b02ddc6f_house renting system.pdf', '2025-10-21 13:30:42'),
(5, 14, NULL, 'afreen', 'afreen@gmail.com', '01234567890', '1234688', '2025-11-02', 'cash', 'dhaka', '0123456789', 'ff', NULL, 'pending', 1, 'tenant_68f78b047c5ed_house renting system.pdf', '2025-10-21 13:30:44');

-- --------------------------------------------------------

--
-- Table structure for table `rent_settings`
--

CREATE TABLE `rent_settings` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `base_rent` decimal(10,2) DEFAULT NULL,
  `include_electricity` tinyint(1) DEFAULT 0,
  `electricity_bill` decimal(10,2) DEFAULT 0.00,
  `include_water` tinyint(1) DEFAULT 0,
  `water_bill` decimal(10,2) DEFAULT 0.00,
  `include_gas` tinyint(1) DEFAULT 0,
  `gas_bill` decimal(10,2) DEFAULT 0.00,
  `include_service` tinyint(1) DEFAULT 0,
  `service_charge` decimal(10,2) DEFAULT 0.00,
  `include_other` tinyint(1) DEFAULT 0,
  `other_charges` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rent_settings`
--

INSERT INTO `rent_settings` (`id`, `property_id`, `base_rent`, `include_electricity`, `electricity_bill`, `include_water`, `water_bill`, `include_gas`, `gas_bill`, `include_service`, `service_charge`, `include_other`, `other_charges`) VALUES
(1, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00),
(2, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00),
(3, 12, 1000.00, 1, 2000.00, 1, 100.00, 1, 500.00, 1, 100.00, 1, 50.00),
(4, 13, 1000.00, 1, 2000.00, 1, 100.00, 1, 500.00, 1, 100.00, 1, 50.00),
(5, 14, 1000.00, 0, 2000.00, 0, 100.00, 0, 500.00, 0, 100.00, 0, 50.00),
(6, 15, 1000.00, 1, 2000.00, 1, 100.00, 1, 500.00, 1, 100.00, 1, 50.00),
(7, 16, 1000.00, 1, 2000.00, 1, 100.00, 0, 500.00, 1, 100.00, 1, 50.00),
(8, 17, 1000.00, 1, 2000.00, 1, 100.00, 1, 500.00, 1, 100.00, 1, 50.00),
(9, 18, 1000.00, 0, 2000.00, 0, 100.00, 0, 500.00, 0, 100.00, 0, 50.00);

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
  `phoneVerified` tinyint(1) DEFAULT 0,
  `emailVerified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `role` varchar(20) NOT NULL,
  `nid_number` varchar(50) DEFAULT NULL,
  `nid_front` varchar(255) DEFAULT NULL,
  `nid_back` varchar(255) DEFAULT NULL,
  `is_landlord_verified` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `phoneVerified`, `emailVerified`, `created_at`, `is_verified`, `role`, `nid_number`, `nid_front`, `nid_back`, `is_landlord_verified`, `status`) VALUES
(1, 'Afreen ', 'afreen@gmail.com', '$2y$10$41p7IIaZq1CsYC/4Az1.MeTW4mKidgv2kCUSabgJcSxCO6ND/Xw6m', NULL, 0, 0, '2025-09-18 14:25:33', 1, 'tenant', NULL, NULL, NULL, 0, 'pending'),
(3, 'Afreen ', 'afreen1@gmail.com', '$2y$10$3UXVsxxSefeYX46L9WSkU.0FXu5NIrmIMcFl6sx.K9ynJkqCo5BXe', NULL, 0, 0, '2025-09-18 14:30:43', 1, 'landlord', NULL, NULL, NULL, 0, 'pending');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_properties_created_at` (`created_at`);

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
-- Indexes for table `rent_settings`
--
ALTER TABLE `rent_settings`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rental_requests`
--
ALTER TABLE `rental_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rent_settings`
--
ALTER TABLE `rent_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
