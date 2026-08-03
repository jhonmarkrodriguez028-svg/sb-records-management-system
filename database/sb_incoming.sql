-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 03, 2026 at 12:01 PM
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
-- Database: `sb_incoming`
--

-- --------------------------------------------------------

--
-- Table structure for table `in_info`
--

CREATE TABLE `in_info` (
  `id` int(11) NOT NULL,
  `DATE_RECIEVER` varchar(100) DEFAULT NULL,
  `SENDER` varchar(100) DEFAULT NULL,
  `FN` varchar(11) DEFAULT NULL,
  `SUBJECT` varchar(200) DEFAULT NULL,
  `ACTION_TAKEN` varchar(200) DEFAULT NULL,
  `REMARKS` varchar(200) DEFAULT NULL,
  `SUBJECT_LINK` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `in_info`
--

INSERT INTO `in_info` (`id`, `DATE_RECIEVER`, `SENDER`, `FN`, `SUBJECT`, `ACTION_TAKEN`, `REMARKS`, `SUBJECT_LINK`) VALUES
(7, '2026-02-25', 'malaya', '1', 'appropriation', 'URGENT', 'PLEASE INCLUDE IN SESSION', 'jhonmark.pdf'),
(9, '2026-02-25', 'malaya', '2', 'appropriation', 'URGENT', 'mayora', 'jhonmark.pdf'),
(10, '2026-02-26', 'undefined', '03', 'MOA', 'URGENT', 'PLEASE INCLUDE IN SESSION', 'NEUST-OJT-F011 Weekly Report (week-2).pdf'),
(12, '2026-04-25', 'mayors office ', '11', 'appropriation', 'URGENT', 'PLEASE INCLUDE IN SESSION', 'NEUST-OJT-F011 Weekly Report (week-2).pdf'),
(13, '2026-08-03', 'mayors office ', '4', 'MOA', 'URGENT', 'PLEASE INCLUDE IN SESSION', 'NEUST-OJT-F011 Weekly Report (week-2).pdf');

-- --------------------------------------------------------

--
-- Table structure for table `out_info`
--

CREATE TABLE `out_info` (
  `id` int(11) NOT NULL,
  `SENT` varchar(100) DEFAULT NULL,
  `ADDRESS` varchar(200) DEFAULT NULL,
  `FN` varchar(11) DEFAULT NULL,
  `SUBJECT` varchar(200) DEFAULT NULL,
  `RECIEVED_BY` varchar(200) DEFAULT NULL,
  `REMARKS` varchar(200) DEFAULT NULL,
  `DATE_RECIEVED` varchar(100) DEFAULT NULL,
  `SUBJECT_LINK` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `out_info`
--

INSERT INTO `out_info` (`id`, `SENT`, `ADDRESS`, `FN`, `SUBJECT`, `RECIEVED_BY`, `REMARKS`, `DATE_RECIEVED`, `SUBJECT_LINK`) VALUES
(9, 'SB', 'STO. ROSARIO', '1', 'MOA', 'JAMES', 'N/A', '2026-02-24', 'jhonmark.pdf'),
(14, 'hahaha', 'STO. ROSARIO', '3', 'appropriation', 'JAMES', 'PLEASE INCLUDE IN SESSION', '2026-02-26', 'jhonmark.pdf'),
(15, 'SB', 'malaya', '12', 'MOA', 'sb pedo', 'PLEASE INCLUDE IN SESSION', '2026-04-28', 'jhonmark.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `created_at`, `role`) VALUES
(4, 'Jhon Mark R. Rodriguez', 'jm@gmail.com', '$2y$10$JWeSvlKy27KBIS8M/clJjujZ.7XH8Mz0tbcsqB2jba5jcjV5qng72', '2026-02-25 02:15:46', 'admin'),
(5, 'James Marl M. Ramos', 'jimboy@gmail.com', '$2y$10$fmlIJmwmau4r5HQdXPOYYul3sWDATIy63EhaWJfAH3v9qZpmyYCPm', '2026-02-25 05:32:35', 'user'),
(6, 'Alfredo Arellano', 'arellano@gmail.com', '$2y$10$.BCg7S3UVAxk3o4alAW07eDGO8wLKISzTzmY11zs/5TjZZj6JJCeS', '2026-02-26 07:48:00', 'user'),
(7, 'Alfredo Arellano', 'jr@gmail.com', '$2y$10$UBBe4uY0nAXJG0njmTzQy.qdHo2xJY3dTImjKKNsSq77oA/j08JOK', '2026-04-25 10:09:53', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `in_info`
--
ALTER TABLE `in_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `out_info`
--
ALTER TABLE `out_info`
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
-- AUTO_INCREMENT for table `in_info`
--
ALTER TABLE `in_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `out_info`
--
ALTER TABLE `out_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
