-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 08:39 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portal_logs`
--
CREATE DATABASE IF NOT EXISTS `portal_logs` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `portal_logs`;

-- --------------------------------------------------------

--
-- Table structure for table `apps_log`
--

CREATE TABLE `apps_log` (
  `access_log_id` int(11) NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_log_user_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_log_application` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `access_log_source_ip` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `access_log_user_agent` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_log_referrer` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_log_details` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_log_session_data` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_log_module` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_log_class` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `access_log_method` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `access_log_uri_string` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `access_log_post_data` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_log_php_raw_input` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_log_get_data` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_log_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apps_log`
--
ALTER TABLE `apps_log`
  ADD PRIMARY KEY (`access_log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apps_log`
--
ALTER TABLE `apps_log`
  MODIFY `access_log_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
