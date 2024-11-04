-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 08:37 AM
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
-- Database: `portal_assessment`
--
CREATE DATABASE IF NOT EXISTS `portal_assessment` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `portal_assessment`;

-- --------------------------------------------------------

--
-- Table structure for table `dt_assessment`
--

CREATE TABLE `dt_assessment` (
  `assessment_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `assessment_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `assessment_purpose` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'staff;lecturer;student',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_assessment_option`
--

CREATE TABLE `dt_assessment_option` (
  `assessment_option_id` int(11) NOT NULL,
  `assessment_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `option_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `option_name_eng` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option_value` double NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_question`
--

CREATE TABLE `dt_question` (
  `question_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `question_parent_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assessment_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `question_name` text COLLATE utf8_unicode_ci NOT NULL,
  `question_name_eng` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_number` int(11) NOT NULL,
  `question_status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_question_result`
--

CREATE TABLE `dt_question_result` (
  `question_result_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `assessment_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `la_result_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assessment_option_id` int(11) DEFAULT NULL,
  `result_text` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `result_value` double DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dt_assessment`
--
ALTER TABLE `dt_assessment`
  ADD PRIMARY KEY (`assessment_id`);

--
-- Indexes for table `dt_assessment_option`
--
ALTER TABLE `dt_assessment_option`
  ADD PRIMARY KEY (`assessment_option_id`),
  ADD KEY `assessment_id` (`assessment_id`);

--
-- Indexes for table `dt_question`
--
ALTER TABLE `dt_question`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `question_parent_id` (`question_parent_id`),
  ADD KEY `assessment_id` (`assessment_id`);

--
-- Indexes for table `dt_question_result`
--
ALTER TABLE `dt_question_result`
  ADD PRIMARY KEY (`question_result_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `assessment_option_id` (`assessment_option_id`),
  ADD KEY `assessment_id` (`assessment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dt_assessment_option`
--
ALTER TABLE `dt_assessment_option`
  MODIFY `assessment_option_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dt_assessment_option`
--
ALTER TABLE `dt_assessment_option`
  ADD CONSTRAINT `dt_assessment_option_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `dt_assessment` (`assessment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_question`
--
ALTER TABLE `dt_question`
  ADD CONSTRAINT `dt_question_ibfk_1` FOREIGN KEY (`question_parent_id`) REFERENCES `dt_question` (`question_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_question_ibfk_2` FOREIGN KEY (`assessment_id`) REFERENCES `dt_assessment` (`assessment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_question_result`
--
ALTER TABLE `dt_question_result`
  ADD CONSTRAINT `dt_question_result_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `dt_question` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_question_result_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_question_result_ibfk_3` FOREIGN KEY (`assessment_option_id`) REFERENCES `dt_assessment_option` (`assessment_option_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_question_result_ibfk_4` FOREIGN KEY (`assessment_id`) REFERENCES `dt_assessment` (`assessment_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
