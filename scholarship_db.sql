-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2025 at 06:21 AM
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
-- Database: `scholarship_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `name`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$K./3CDwsElBjOrIvIr1d2uFH8SWB.CHhe15COoRRS0gRzS6LC7Wtm', 'System Administrator', 'admin@gmail.com', 'super_admin', '2025-04-29 11:58:46', '2025-04-29 12:18:06');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `scholarship_id` int(11) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `status` enum('Under Review','Approved','Denied') DEFAULT 'Under Review',
  `date_applied` datetime NOT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `student_id`, `scholarship_id`, `document_path`, `status`, `date_applied`, `file_path`) VALUES
(1, 1, 1, 'uploads/68117aef5fb41_Iskolar_ng_Laguna_Scholarship_Application.pdf', 'Approved', '2025-04-30 09:20:47', NULL),
(2, 2, 1, 'uploads/681307f2dd2ae_Iskolar_ng_Laguna_Scholarship_Application.pdf', 'Approved', '2025-05-01 13:34:42', NULL),
(3, 3, 1, 'uploads/681325831dfb0_Iskolar_ng_Laguna_Scholarship_Application.pdf', 'Approved', '2025-05-01 15:40:51', NULL),
(4, 4, 1, 'uploads/6813277f0b524_Iskolar_ng_Laguna_Scholarship_Letter.pdf', 'Denied', '2025-05-01 15:49:19', NULL),
(5, 5, 1, 'uploads/681328af100e1_Iskolar_ng_Laguna_Scholarship_Application.pdf', 'Denied', '2025-05-01 15:54:23', NULL),
(6, 6, 1, 'uploads/681329aca6f32_Iskolar_ng_Laguna_Scholarship_Application.pdf', 'Under Review', '2025-05-01 15:58:36', NULL),
(7, 7, 1, 'uploads/68132b3e3c1c0_Iskolar_ng_Laguna_Scholarship_Application.pdf', 'Under Review', '2025-05-01 16:05:18', NULL),
(8, 8, 1, 'uploads/68132c6d06f94_Iskolar_ng_Laguna_Scholarship_Application.pdf', 'Approved', '2025-05-01 16:10:21', NULL),
(9, 9, 1, 'uploads/68132cdf4f62d_Iskolar_ng_Laguna_Scholarship_Application.pdf', 'Approved', '2025-05-01 16:12:15', NULL),
(10, 10, 1, 'uploads/68132dfce9a76_Iskolar_ng_Laguna_Scholarship_Application.pdf', 'Approved', '2025-05-01 16:17:00', NULL),
(12, 11, 1, 'uploads/6813ed2473ca8_Iskolar_ng_Laguna_Scholarship_Application.pdf', 'Approved', '2025-05-02 05:52:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scholarships`
--

CREATE TABLE `scholarships` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `deadline` date NOT NULL,
  `requirements` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarships`
--

INSERT INTO `scholarships` (`id`, `name`, `amount`, `deadline`, `requirements`) VALUES
(1, 'Iskolar ng Laguna', 15000.00, '2025-05-30', 'Certified True Copy of Grades, Certificate of Registration/Enrollment'),
(2, 'Academicare Scholarship', 11500.00, '2025-05-30', 'GPA of 90% or higher, Incoming Freshmen');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `year_of_study` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `email`, `password`, `first_name`, `last_name`, `phone`, `student_id`, `department`, `year_of_study`, `created_at`, `updated_at`) VALUES
(1, 'student@gmail.com', '$2y$10$UkisZfKX5iT9Km1tc1e/jubieQjJr391/2rSqMwiNiBlYgprGqpvW', 'Juan', 'Dela Cruz', '123-456-7890', 'ST12345', 'Information Technology', '3', '2025-04-29 11:58:46', '2025-04-30 00:57:20'),
(2, 'juliacruz@gmail.com', '$2y$10$yKNywcUerQiPa3fv1g1D5eJinOFVxM.nCyaNlVolOLQOMbPCkCfau', 'Julia', 'Cruz', NULL, NULL, NULL, NULL, '2025-05-01 05:15:05', '2025-05-01 05:15:05'),
(3, 'johnramos@gmail.com', '$2y$10$sXv5LKMvtreg87ectJwhguVpEq/UX0eVoqta.zETl1j8pIQOCutSm', 'John', 'Ramos', NULL, NULL, NULL, NULL, '2025-05-01 07:40:33', '2025-05-01 07:40:33'),
(4, 'ethanhill@gmail.com', '$2y$10$uOHwVBA1WYB3eI2CBWwSPO6sSHKwDAUVM0UDoK7KkskSOO.mCiD5O', 'Ethan', 'Hill', NULL, NULL, NULL, NULL, '2025-05-01 07:48:50', '2025-05-01 07:48:50'),
(5, 'aliceroxas@gmail.com', '$2y$10$ClHtHWu6HhytS5QXyp0/ruOOTT8rf9KxLz4IDVUhR6M3cHw/u5FQC', 'Alice', 'Roxas', NULL, NULL, NULL, NULL, '2025-05-01 07:54:06', '2025-05-01 07:54:06'),
(6, 'jadeco@gmail.com', '$2y$10$7GWTKgm88ZnDdNxfMDbM/O6VzbAkQJ9fRYTXK/K5PkDEcJNIjwwH2', 'Jade', 'Co', NULL, NULL, NULL, NULL, '2025-05-01 07:58:21', '2025-05-01 07:58:21'),
(7, 'lauradoe@gmail.com', '$2y$10$5rxYePGp9KO2Ihlw/n2xq.4xNYIdOYEcz3yX9.JJZfJUjhn8D/M/G', 'Laura', 'Doe', NULL, NULL, NULL, NULL, '2025-05-01 08:05:00', '2025-05-01 08:05:00'),
(8, 'janegarcia@gmail.com', '$2y$10$bfzhdiFBqUeD2yPm9iPwo.WC.zOkSZuSLqOaKlqyW5V6RYgRiNopW', 'Jane', 'Garcia', NULL, NULL, NULL, NULL, '2025-05-01 08:10:05', '2025-05-01 08:10:05'),
(9, 'markreyes@gmail.com', '$2y$10$Du4mzL3469eXN5DukkLcb.5KjJl7xT4tNlOpxVpIpOMFp2i4WsKoa', 'Mark', 'Reyes', NULL, NULL, NULL, NULL, '2025-05-01 08:11:58', '2025-05-01 08:11:58'),
(10, 'joelgo@gmail.com', '$2y$10$JMDHmJfgstSF3rfLpWRVbOqaM/bnhY22bUB65PQ5YuYWjIngbaao.', 'Joel', 'Go', NULL, NULL, NULL, NULL, '2025-05-01 08:16:35', '2025-05-01 08:16:35'),
(11, 'johndoe@gmail.com', '$2y$10$461TXegiNVB1zEfn.1yfseaRyeDofC5MkJUrjI4utHOBkdGd85hLq', 'John', 'Doe', NULL, NULL, NULL, NULL, '2025-05-01 21:51:53', '2025-05-01 21:51:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `scholarship_id` (`scholarship_id`);

--
-- Indexes for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
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
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `scholarships`
--
ALTER TABLE `scholarships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
