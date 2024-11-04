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
-- Database: `portal_hr`
--
CREATE DATABASE IF NOT EXISTS `portal_hr` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `portal_hr`;

-- --------------------------------------------------------

--
-- Table structure for table `dt_employee_account_bank`
--

CREATE TABLE `dt_employee_account_bank` (
  `account_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bank_code` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `account_holder` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_employee_benefit`
--

CREATE TABLE `dt_employee_benefit` (
  `employee_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `benefit_id` int(11) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_employee_partner`
--

CREATE TABLE `dt_employee_partner` (
  `employee_relation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_hid`
--

CREATE TABLE `dt_hid` (
  `hid_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hid_key` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `hid_status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL,
  `hid_should_checkin` time NOT NULL DEFAULT '08:00:00',
  `hid_should_checkout` time NOT NULL DEFAULT '17:00:00',
  `hid_standar_working_hour` time NOT NULL DEFAULT '08:00:00',
  `portal_id` int(11) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_hid_log`
--

CREATE TABLE `dt_hid_log` (
  `log_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `hid_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hid_key` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_date` date NOT NULL,
  `log_checkin` time NOT NULL,
  `log_checkout` time DEFAULT NULL,
  `log_late_in` time NOT NULL DEFAULT '00:00:00',
  `log_early_out` time DEFAULT '00:00:00',
  `log_working_hour` time DEFAULT '00:00:00',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_job_assign`
--

CREATE TABLE `dt_job_assign` (
  `assign_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `report_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assign_as` enum('assignor','recipient') COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_job_report`
--

CREATE TABLE `dt_job_report` (
  `report_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `report_title` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `report_datestart` datetime DEFAULT NULL,
  `report_dateend` datetime DEFAULT NULL,
  `report_allday` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `report_desc` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `report_category` enum('event','working list','birthday','holiday','academic calendar') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'event',
  `report_type` enum('personal','public') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'personal',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_job_report_details`
--

CREATE TABLE `dt_job_report_details` (
  `report_detail_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `report_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `report_start_time` time DEFAULT NULL,
  `report_end_time` time DEFAULT NULL,
  `report_desc` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `report_filepath` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_benefit`
--

CREATE TABLE `ref_benefit` (
  `benefit_id` int(11) NOT NULL,
  `benefit_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `benefit_detail` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dt_employee_account_bank`
--
ALTER TABLE `dt_employee_account_bank`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `bank_code` (`bank_code`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `dt_employee_benefit`
--
ALTER TABLE `dt_employee_benefit`
  ADD KEY `benefit_id` (`benefit_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `dt_hid`
--
ALTER TABLE `dt_hid`
  ADD PRIMARY KEY (`hid_id`),
  ADD UNIQUE KEY `hid_key` (`hid_key`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `dt_hid_log`
--
ALTER TABLE `dt_hid_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `hid_id` (`hid_id`);

--
-- Indexes for table `dt_job_assign`
--
ALTER TABLE `dt_job_assign`
  ADD PRIMARY KEY (`assign_id`),
  ADD KEY `report_id` (`report_id`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `dt_job_report`
--
ALTER TABLE `dt_job_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `dt_job_report_details`
--
ALTER TABLE `dt_job_report_details`
  ADD PRIMARY KEY (`report_detail_id`),
  ADD KEY `report_id` (`report_id`);

--
-- Indexes for table `ref_benefit`
--
ALTER TABLE `ref_benefit`
  ADD PRIMARY KEY (`benefit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ref_benefit`
--
ALTER TABLE `ref_benefit`
  MODIFY `benefit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dt_employee_account_bank`
--
ALTER TABLE `dt_employee_account_bank`
  ADD CONSTRAINT `dt_employee_account_bank_ibfk_1` FOREIGN KEY (`bank_code`) REFERENCES `portal_main`.`ref_bank` (`bank_code`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_employee_account_bank_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `portal_main`.`dt_employee` (`employee_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `dt_employee_benefit`
--
ALTER TABLE `dt_employee_benefit`
  ADD CONSTRAINT `dt_employee_benefit_ibfk_1` FOREIGN KEY (`benefit_id`) REFERENCES `ref_benefit` (`benefit_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_employee_benefit_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `portal_main`.`dt_employee` (`employee_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_hid`
--
ALTER TABLE `dt_hid`
  ADD CONSTRAINT `dt_hid_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_hid_log`
--
ALTER TABLE `dt_hid_log`
  ADD CONSTRAINT `dt_hid_log_ibfk_1` FOREIGN KEY (`hid_id`) REFERENCES `dt_hid` (`hid_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_job_assign`
--
ALTER TABLE `dt_job_assign`
  ADD CONSTRAINT `dt_job_assign_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `dt_job_report` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_job_assign_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_job_report`
--
ALTER TABLE `dt_job_report`
  ADD CONSTRAINT `dt_job_report_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_job_report_details`
--
ALTER TABLE `dt_job_report_details`
  ADD CONSTRAINT `dt_job_report_details_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `dt_job_report` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
