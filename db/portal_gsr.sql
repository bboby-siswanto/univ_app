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
-- Database: `portal_gsr`
--
CREATE DATABASE IF NOT EXISTS `portal_gsr` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `portal_gsr`;

-- --------------------------------------------------------

--
-- Table structure for table `dt_account_list`
--

CREATE TABLE `dt_account_list` (
  `account_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `account_no_main` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_name` text COLLATE utf8_unicode_ci NOT NULL,
  `account_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `account_marked_strong` tinyint(1) NOT NULL DEFAULT 0,
  `level_of_padd` tinyint(4) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Chart of Account List';

--
-- Dumping data for table `dt_account_list`
--

INSERT INTO `dt_account_list` (`account_no`, `account_no_main`, `account_name`, `account_type`, `account_marked_strong`, `level_of_padd`, `date_added`, `timestamp`) VALUES
('17.04.01', NULL, 'Office Equipment', 'Fixed Asset', 0, 0, '2024-02-02 15:59:56', '2024-02-02 09:25:25'),
('17.04.02', NULL, 'Classroom &amp; Laboratory Equipment', 'Fixed Asset', 0, 0, '2024-02-02 16:00:13', '2024-02-02 09:25:27'),
('17.04.03', NULL, 'IT Equipment', 'Fixed Asset', 0, 0, '2024-02-02 16:00:23', '2024-02-02 09:25:28'),
('17.04.04', NULL, 'Marketing Equipment', 'Fixed Asset', 0, 0, '2024-02-02 16:00:29', '2024-02-02 09:25:30'),
('17.05.01', NULL, 'Book', 'Fixed Asset', 0, 0, '2024-02-02 16:01:34', '2024-02-02 09:25:31'),
('17.05.02', NULL, 'E-Book', 'Fixed Asset', 0, 0, '2024-02-02 16:01:42', '2024-02-02 09:25:32'),
('40.01.02.01', NULL, 'Refund Study Fee', 'Revenue', 1, 0, '2024-04-03 15:00:55', '2024-04-03 08:00:55'),
('60.01.01.01', NULL, 'Salary Full Time  Lecturer', 'Expense', 0, 0, '2024-02-02 16:27:47', '2024-02-02 09:27:47'),
('60.01.01.02', NULL, 'Salary Homebase Lecturer', 'Expense', 0, 0, '2024-02-02 16:27:47', '2024-02-02 09:27:47'),
('60.01.01.03', NULL, 'Honorarium Part Time Lecturer', 'Expense', 0, 0, '2024-02-02 16:27:47', '2024-02-02 09:27:47'),
('60.01.01.04', NULL, 'BPJS TK Lecturer', 'Expense', 0, 0, '2024-02-02 16:27:47', '2024-02-02 09:27:47'),
('60.01.01.05', NULL, 'BPJS Kesehatan Lecturer', 'Expense', 0, 0, '2024-02-02 16:27:47', '2024-02-02 09:27:47'),
('60.01.01.06', NULL, 'Health Insurance Lecturer', 'Expense', 0, 0, '2024-02-02 16:27:47', '2024-02-02 09:27:47'),
('60.01.01.07', NULL, 'Tax - PPh 21 Lecturer', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.01.08', NULL, 'THR - Lecturer', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.02.01', NULL, 'Salary Full Time Staff', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.02.02', NULL, 'Honorarium Staff', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.02.03', NULL, 'BPJS TK Staff', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.02.04', NULL, 'BPJS Kesehatan Staff', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.02.05', NULL, 'Health Insurance Staff', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.02.06', NULL, 'Tax - PPh 21 Staff', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.02.07', NULL, 'THR - Staff', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.03.01', NULL, 'Office Supplies Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.03.02', NULL, 'Laboratory & Practicum Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.03.03', NULL, 'Excursion Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.03.04', NULL, 'Operational English Learning Expense', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.03.05', NULL, 'Dikti Scholarship Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.01', NULL, 'Electricity Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.02', NULL, 'Internet & Webhosting Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.03', NULL, 'Telecomunication Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.04', NULL, 'Marketing & Promotion', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.05', NULL, 'Accreditation Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.06', NULL, 'Consumption Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.07', NULL, 'Legal Issue', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.08', NULL, 'Postage & Expedition', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.09', NULL, 'Transport & Meal Allowance', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.10', NULL, 'Maintance Building Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.11', NULL, 'Maintance Vehicle Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.01.04.12', NULL, 'Maintance Equipment Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.02.01.01', NULL, 'Student Orientation Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.02.01.02', NULL, 'Student Sport Activities Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.02.02.01', NULL, 'Student Contest Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.02.02.02', NULL, 'Student Angklung Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.02.02.03', NULL, 'Student Choir Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.02.03.01', NULL, 'Student Insurance Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.02.03.02', NULL, 'Jackets & T-Shirt Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.02.03.03', NULL, 'SGS Reward Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.02.03.04', NULL, 'Graduation Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-03-01 03:11:07'),
('60.03.01', NULL, 'Research Expense', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.03.02', NULL, 'Community Service Expense', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.03.03', NULL, 'Publication Expense', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.04.01.01', NULL, 'Employee Training Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.04.01.02', NULL, 'Employee Development Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.04.02.01', NULL, 'Depreciation Expense - Vehicles', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.04.02.02', NULL, 'Depreciation Expense - Equipment', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.04.03.01', NULL, 'Building Rent Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.04.03.02', NULL, 'Tax PPh 4 (2) Building Rent Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('60.04.03.03', NULL, 'Depreciation Expense - Building', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('61.01.01.01', NULL, 'Dikti Scholarship Exp.', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('61.02.01', NULL, 'Research Expense - From External', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('61.02.02', NULL, 'Community Service Expense - From External', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('61.02.03', NULL, 'Publication Expense - From External', 'Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('71.01', NULL, 'Bank Charges Exp.', 'Other Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48'),
('71.02', NULL, 'Interest Exp.', 'Other Expense', 0, 0, '2024-02-02 16:27:48', '2024-02-02 09:27:48');

-- --------------------------------------------------------

--
-- Table structure for table `dt_df_details`
--

CREATE TABLE `dt_df_details` (
  `df_details_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `df_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `account_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `df_details_remarks` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `df_details_debet` decimal(15,0) DEFAULT NULL,
  `df_details_kredit` decimal(15,0) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_df_main`
--

CREATE TABLE `dt_df_main` (
  `df_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `gsr_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_id_requested` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_id_checked` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_id_approved` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_id_finishing` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bank_code` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `df_number` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `df_top` enum('Cash','Transfer','Check') COLLATE utf8_unicode_ci DEFAULT NULL,
  `df_date_created` datetime NOT NULL,
  `df_bank_account` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `df_budget_dept` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `df_transaction` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `df_type` enum('Bank Disbursement','Bank Receipt') COLLATE utf8_unicode_ci NOT NULL,
  `df_allow_update` enum('false','true') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_df_note`
--

CREATE TABLE `dt_df_note` (
  `note_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_df_status`
--

CREATE TABLE `dt_df_status` (
  `status_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `df_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_action_id` int(11) NOT NULL,
  `personal_document_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_gsr_attachment`
--

CREATE TABLE `dt_gsr_attachment` (
  `gsr_file_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `gsr_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `document_link` text COLLATE utf8_unicode_ci NOT NULL,
  `document_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `gsr_show` enum('true','false') COLLATE utf8_unicode_ci DEFAULT 'true',
  `df_show` enum('true','false') COLLATE utf8_unicode_ci DEFAULT 'false',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_gsr_details`
--

CREATE TABLE `dt_gsr_details` (
  `gsr_details_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `gsr_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `gsr_details_description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_details_activity_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_details_qty` double NOT NULL DEFAULT 0,
  `gsr_details_price` decimal(15,0) NOT NULL DEFAULT 0,
  `gsr_details_total_price` decimal(15,0) NOT NULL DEFAULT 0,
  `gsr_details_total_price_text` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_details_remarks` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_gsr_main`
--

CREATE TABLE `dt_gsr_main` (
  `gsr_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id_request` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_id_review` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_id_approved` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_id_finishing` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `account_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `gsr_reviewed` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_approved` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_finished` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_number` int(11) DEFAULT NULL,
  `gsr_date_request` datetime NOT NULL DEFAULT current_timestamp(),
  `gsr_budget_proposal_number` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_activity` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_total_amount` decimal(15,0) NOT NULL DEFAULT 0,
  `gsr_total_amount_text` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_finance_note` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `gsr_allow_update` enum('false','true') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_gsr_note`
--

CREATE TABLE `dt_gsr_note` (
  `note_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_gsr_status`
--

CREATE TABLE `dt_gsr_status` (
  `status_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `gsr_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'action by',
  `status_action_id` int(11) NOT NULL,
  `personal_document_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_gsr_action`
--

CREATE TABLE `ref_gsr_action` (
  `action_id` int(11) NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action_access` enum('create','review','check','approval 1','approval 2','approval 3') COLLATE utf8_unicode_ci NOT NULL,
  `action_progress` enum('gsr','dfrf') COLLATE utf8_unicode_ci DEFAULT NULL,
  `action_level` tinyint(4) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_status_action`
--

CREATE TABLE `ref_status_action` (
  `status_action_id` int(11) NOT NULL,
  `status_action` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `current_progress` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `level` tinyint(4) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_status_action`
--

INSERT INTO `ref_status_action` (`status_action_id`, `status_action`, `current_progress`, `level`, `date_added`, `timestamp`) VALUES
(1, 'pending', 'requested', 1, '2022-12-07 14:25:59', '2022-12-07 07:25:59'),
(2, 'reject', 'reviewed', 2, '2022-12-07 14:25:59', '2022-12-07 07:25:59'),
(3, 'approve', 'reviewed', 3, '2022-12-07 14:25:59', '2022-12-07 07:25:59'),
(4, 'reject', 'checked', 4, '2022-12-07 14:25:59', '2023-01-06 03:15:51'),
(5, 'approve', 'checked', 5, '2022-12-07 14:25:59', '2023-01-06 03:15:54'),
(6, 'reject', 'approved', 6, '2022-12-07 14:25:59', '2023-01-06 03:15:57'),
(7, 'approve', 'approved', 7, '2022-12-07 14:25:59', '2023-01-06 03:15:59'),
(8, 'reject', 'finish', 8, '2022-12-07 14:25:59', '2023-01-06 03:16:01'),
(9, 'approve', 'finish', 9, '2022-12-07 14:25:59', '2023-01-06 03:16:03');

-- --------------------------------------------------------

--
-- Table structure for table `ref_unit`
--

CREATE TABLE `ref_unit` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_unit`
--

INSERT INTO `ref_unit` (`unit_id`, `unit_name`, `date_added`, `timestamp`) VALUES
(1, 'Pcs', '2022-06-09 11:04:56', '2022-06-09 04:04:56'),
(2, 'Kg', '2022-06-09 11:05:17', '2022-06-09 04:05:17'),
(3, 'Student', '2022-06-09 11:05:17', '2022-06-09 04:05:17'),
(4, 'Group', '2022-06-09 11:06:18', '2022-06-09 04:06:18'),
(5, 'Person', '2022-06-09 11:06:18', '2022-06-09 04:06:18'),
(6, 'Unit', '2022-06-09 11:06:18', '2022-06-09 04:06:18'),
(7, 'Way', '2022-06-09 11:06:18', '2022-06-09 04:06:18'),
(8, 'Dus', '2022-06-09 11:06:18', '2022-06-09 04:06:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dt_account_list`
--
ALTER TABLE `dt_account_list`
  ADD PRIMARY KEY (`account_no`),
  ADD KEY `account_no_main` (`account_no_main`);

--
-- Indexes for table `dt_df_details`
--
ALTER TABLE `dt_df_details`
  ADD PRIMARY KEY (`df_details_id`),
  ADD KEY `df_id` (`df_id`),
  ADD KEY `account_no` (`account_no`);

--
-- Indexes for table `dt_df_main`
--
ALTER TABLE `dt_df_main`
  ADD PRIMARY KEY (`df_id`),
  ADD KEY `gsr_id` (`gsr_id`),
  ADD KEY `bank_code` (`bank_code`),
  ADD KEY `personal_data_id_approved` (`personal_data_id_approved`),
  ADD KEY `personal_data_id_checked` (`personal_data_id_checked`),
  ADD KEY `personal_data_id_finishing` (`personal_data_id_finishing`),
  ADD KEY `personal_data_id_requested` (`personal_data_id_requested`);

--
-- Indexes for table `dt_df_note`
--
ALTER TABLE `dt_df_note`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `dt_df_note_ibfk_2` (`personal_data_id`);

--
-- Indexes for table `dt_df_status`
--
ALTER TABLE `dt_df_status`
  ADD PRIMARY KEY (`status_id`),
  ADD KEY `df_id` (`df_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `status_action_id` (`status_action_id`),
  ADD KEY `personal_document_id` (`personal_document_id`);

--
-- Indexes for table `dt_gsr_attachment`
--
ALTER TABLE `dt_gsr_attachment`
  ADD PRIMARY KEY (`gsr_file_id`),
  ADD KEY `gsr_id` (`gsr_id`);

--
-- Indexes for table `dt_gsr_details`
--
ALTER TABLE `dt_gsr_details`
  ADD PRIMARY KEY (`gsr_details_id`),
  ADD KEY `gsr_id` (`gsr_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `dt_gsr_main`
--
ALTER TABLE `dt_gsr_main`
  ADD PRIMARY KEY (`gsr_id`),
  ADD KEY `personal_data_id_request` (`personal_data_id_request`),
  ADD KEY `personal_data_id_approved` (`personal_data_id_approved`),
  ADD KEY `personal_data_id_review` (`personal_data_id_review`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `personal_data_id_finishing` (`personal_data_id_finishing`),
  ADD KEY `account_no` (`account_no`);

--
-- Indexes for table `dt_gsr_note`
--
ALTER TABLE `dt_gsr_note`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `dt_gsr_status`
--
ALTER TABLE `dt_gsr_status`
  ADD PRIMARY KEY (`status_id`),
  ADD KEY `gsr_id` (`gsr_id`),
  ADD KEY `status_action_by` (`personal_data_id`),
  ADD KEY `status_action_id` (`status_action_id`),
  ADD KEY `personal_document_id` (`personal_document_id`);

--
-- Indexes for table `ref_gsr_action`
--
ALTER TABLE `ref_gsr_action`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `ref_status_action`
--
ALTER TABLE `ref_status_action`
  ADD PRIMARY KEY (`status_action_id`);

--
-- Indexes for table `ref_unit`
--
ALTER TABLE `ref_unit`
  ADD PRIMARY KEY (`unit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ref_gsr_action`
--
ALTER TABLE `ref_gsr_action`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ref_status_action`
--
ALTER TABLE `ref_status_action`
  MODIFY `status_action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ref_unit`
--
ALTER TABLE `ref_unit`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dt_account_list`
--
ALTER TABLE `dt_account_list`
  ADD CONSTRAINT `dt_account_list_ibfk_1` FOREIGN KEY (`account_no_main`) REFERENCES `dt_account_list` (`account_no`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_df_details`
--
ALTER TABLE `dt_df_details`
  ADD CONSTRAINT `dt_df_details_ibfk_2` FOREIGN KEY (`df_id`) REFERENCES `dt_df_main` (`df_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_df_details_ibfk_3` FOREIGN KEY (`account_no`) REFERENCES `dt_account_list` (`account_no`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_df_main`
--
ALTER TABLE `dt_df_main`
  ADD CONSTRAINT `dt_df_main_ibfk_1` FOREIGN KEY (`gsr_id`) REFERENCES `dt_gsr_main` (`gsr_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_df_main_ibfk_2` FOREIGN KEY (`bank_code`) REFERENCES `portal_main`.`ref_bank` (`bank_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_df_main_ibfk_3` FOREIGN KEY (`personal_data_id_approved`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_df_main_ibfk_4` FOREIGN KEY (`personal_data_id_checked`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_df_main_ibfk_5` FOREIGN KEY (`personal_data_id_finishing`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_df_main_ibfk_6` FOREIGN KEY (`personal_data_id_requested`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_df_note`
--
ALTER TABLE `dt_df_note`
  ADD CONSTRAINT `dt_df_note_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `dt_df_status` (`status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_df_note_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_df_status`
--
ALTER TABLE `dt_df_status`
  ADD CONSTRAINT `dt_df_status_ibfk_1` FOREIGN KEY (`df_id`) REFERENCES `dt_df_main` (`df_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_df_status_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_df_status_ibfk_3` FOREIGN KEY (`status_action_id`) REFERENCES `ref_status_action` (`status_action_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_df_status_ibfk_4` FOREIGN KEY (`personal_document_id`) REFERENCES `portal_main`.`dt_personal_document` (`personal_document_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `dt_gsr_attachment`
--
ALTER TABLE `dt_gsr_attachment`
  ADD CONSTRAINT `dt_gsr_attachment_ibfk_1` FOREIGN KEY (`gsr_id`) REFERENCES `dt_gsr_main` (`gsr_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_gsr_details`
--
ALTER TABLE `dt_gsr_details`
  ADD CONSTRAINT `dt_gsr_details_ibfk_1` FOREIGN KEY (`gsr_id`) REFERENCES `dt_gsr_main` (`gsr_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_gsr_details_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `ref_unit` (`unit_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_gsr_main`
--
ALTER TABLE `dt_gsr_main`
  ADD CONSTRAINT `dt_gsr_main_ibfk_1` FOREIGN KEY (`personal_data_id_request`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_gsr_main_ibfk_2` FOREIGN KEY (`personal_data_id_approved`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_gsr_main_ibfk_3` FOREIGN KEY (`personal_data_id_review`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_gsr_main_ibfk_4` FOREIGN KEY (`department_id`) REFERENCES `portal_main`.`ref_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_gsr_main_ibfk_6` FOREIGN KEY (`personal_data_id_finishing`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_gsr_main_ibfk_7` FOREIGN KEY (`account_no`) REFERENCES `dt_account_list` (`account_no`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_gsr_note`
--
ALTER TABLE `dt_gsr_note`
  ADD CONSTRAINT `dt_gsr_note_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_gsr_note_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `dt_gsr_status` (`status_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_gsr_status`
--
ALTER TABLE `dt_gsr_status`
  ADD CONSTRAINT `dt_gsr_status_ibfk_1` FOREIGN KEY (`gsr_id`) REFERENCES `dt_gsr_main` (`gsr_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_gsr_status_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_gsr_status_ibfk_3` FOREIGN KEY (`status_action_id`) REFERENCES `ref_status_action` (`status_action_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_gsr_status_ibfk_4` FOREIGN KEY (`personal_document_id`) REFERENCES `portal_main`.`dt_personal_document` (`personal_document_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `ref_gsr_action`
--
ALTER TABLE `ref_gsr_action`
  ADD CONSTRAINT `ref_gsr_action_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `portal_main`.`dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
