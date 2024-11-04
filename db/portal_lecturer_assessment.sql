-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 08:38 AM
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
-- Database: `portal_lecturer_assessment`
--
CREATE DATABASE IF NOT EXISTS `portal_lecturer_assessment` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `portal_lecturer_assessment`;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_result`
--

CREATE TABLE `assessment_result` (
  `result_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `result_comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_score` double NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_result_question`
--

CREATE TABLE `assessment_result_question` (
  `result_question_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `result_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `score_result_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_aspect`
--

CREATE TABLE `question_aspect` (
  `question_id` int(11) NOT NULL,
  `question_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `number` tinyint(2) NOT NULL,
  `question_status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `score_result`
--

CREATE TABLE `score_result` (
  `score_result_id` int(11) NOT NULL,
  `score_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_value` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessment_result`
--
ALTER TABLE `assessment_result`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `score_id` (`score_id`);

--
-- Indexes for table `assessment_result_question`
--
ALTER TABLE `assessment_result_question`
  ADD PRIMARY KEY (`result_question_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `score_result_id` (`score_result_id`),
  ADD KEY `result_id` (`result_id`);

--
-- Indexes for table `question_aspect`
--
ALTER TABLE `question_aspect`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `score_result`
--
ALTER TABLE `score_result`
  ADD PRIMARY KEY (`score_result_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `question_aspect`
--
ALTER TABLE `question_aspect`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `score_result`
--
ALTER TABLE `score_result`
  MODIFY `score_result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessment_result`
--
ALTER TABLE `assessment_result`
  ADD CONSTRAINT `assessment_result_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `portal_main`.`dt_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assessment_result_ibfk_2` FOREIGN KEY (`score_id`) REFERENCES `portal_main`.`dt_score` (`score_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessment_result_question`
--
ALTER TABLE `assessment_result_question`
  ADD CONSTRAINT `assessment_result_question_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question_aspect` (`question_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `assessment_result_question_ibfk_2` FOREIGN KEY (`score_result_id`) REFERENCES `score_result` (`score_result_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `assessment_result_question_ibfk_3` FOREIGN KEY (`result_id`) REFERENCES `assessment_result` (`result_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
