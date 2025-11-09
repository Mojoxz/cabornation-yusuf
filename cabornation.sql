-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 09, 2025 at 11:42 AM
-- Server version: 8.4.3
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cabornation`
--

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `team_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `sport` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `team_desc` text COLLATE utf8mb4_general_ci,
  `active_tournaments` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `user_id`, `team_name`, `sport`, `team_desc`, `active_tournaments`, `created_at`) VALUES
(1, 16, 'unesa', 'Futsal', 'aduh', 0, '2025-11-06 05:27:44');

-- --------------------------------------------------------

--
-- Table structure for table `teams_backup`
--

CREATE TABLE `teams_backup` (
  `id` int NOT NULL DEFAULT '0',
  `user_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `team_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sport` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `team_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `active_tournaments` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teams_backup`
--

INSERT INTO `teams_backup` (`id`, `user_email`, `team_name`, `sport`, `team_desc`, `active_tournaments`, `created_at`) VALUES
(1, 'yusuframadhan0505@gmail.com', 'unesa', 'Futsal', 'aduh', 0, '2025-11-06 05:27:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `overview` text COLLATE utf8mb4_general_ci,
  `role` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'official',
  `team_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sport` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `active_tournaments` int DEFAULT '0',
  `team_desc` text COLLATE utf8mb4_general_ci,
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `overview`, `role`, `team_name`, `sport`, `active_tournaments`, `team_desc`, `joined_at`) VALUES
(11, 'Yusuf Ramadhan Morti Sondang Simanjuntak', 'uwawtugas@gmail.com', 'sup123', NULL, 'official', NULL, NULL, 0, NULL, '2025-10-26 16:05:09'),
(12, '', '', 'sleep12', 'sangar', 'official', 'unesa', 'basket', 1, 'sangar', '2025-10-26 16:05:09'),
(13, 'Ptraaaa', 'dhifaput12@gmail.com', 'putrakediri', '', 'official', NULL, NULL, 0, NULL, '2025-10-26 16:05:09'),
(14, 'Salmaaa', 'salma12@gmail.com', 'salma12', NULL, 'official', NULL, NULL, 0, NULL, '2025-10-26 16:05:09'),
(15, 'indraa', 'indrabctr@gmail.com', 'indra123', 'aku sangar se', 'official', NULL, NULL, 0, NULL, '2025-10-26 16:05:09'),
(16, 'Diexcst', 'yusuframadhan0505@gmail.com', '123456', 'sangar hcl', 'official', 'unesa', 'basket', 2, 'losss', '2025-11-03 02:09:13');

-- --------------------------------------------------------

--
-- Table structure for table `user_photos`
--

CREATE TABLE `user_photos` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_photos`
--

INSERT INTO `user_photos` (`id`, `user_id`, `photo_path`, `uploaded_at`) VALUES
(1, 15, 'assets/foto_profil/1762688391_pdip-1-1024x491.jpg', '2025-11-09 11:39:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_photos`
--
ALTER TABLE `user_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_photos`
--
ALTER TABLE `user_photos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_photos`
--
ALTER TABLE `user_photos`
  ADD CONSTRAINT `user_photos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
