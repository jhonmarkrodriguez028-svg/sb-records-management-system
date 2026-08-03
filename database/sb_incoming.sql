```sql
-- ============================================================
-- SB Records Management System
-- Database: sb_incoming
-- Public / Demo Database Structure
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

SET time_zone = "+00:00";

SET NAMES utf8mb4;

-- ============================================================
-- Database
-- ============================================================

CREATE DATABASE IF NOT EXISTS `sb_incoming`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `sb_incoming`;

-- ============================================================
-- Table: in_info
-- Incoming Records
-- ============================================================

CREATE TABLE IF NOT EXISTS `in_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `DATE_RECIEVER` varchar(100) DEFAULT NULL,
  `SENDER` varchar(100) DEFAULT NULL,
  `FN` varchar(11) DEFAULT NULL,
  `SUBJECT` varchar(200) DEFAULT NULL,
  `ACTION_TAKEN` varchar(200) DEFAULT NULL,
  `REMARKS` varchar(200) DEFAULT NULL,
  `SUBJECT_LINK` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Table: out_info
-- Outgoing Records
-- ============================================================

CREATE TABLE IF NOT EXISTS `out_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SENT` varchar(100) DEFAULT NULL,
  `ADDRESS` varchar(200) DEFAULT NULL,
  `FN` varchar(11) DEFAULT NULL,
  `SUBJECT` varchar(200) DEFAULT NULL,
  `RECIEVED_BY` varchar(200) DEFAULT NULL,
  `REMARKS` varchar(200) DEFAULT NULL,
  `DATE_RECIEVED` varchar(100) DEFAULT NULL,
  `SUBJECT_LINK` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Table: users
-- System Users
-- ============================================================

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- ============================================================
-- No real user or office records are included.
-- Register a new account through the application.
-- ============================================================

COMMIT;
```
