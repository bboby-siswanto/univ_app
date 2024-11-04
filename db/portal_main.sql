-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 08:40 AM
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
-- Database: `portal_main`
--
CREATE DATABASE IF NOT EXISTS `portal_main` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `portal_main`;

-- --------------------------------------------------------

--
-- Table structure for table `bni_billing`
--

CREATE TABLE `bni_billing` (
  `trx_id` int(11) NOT NULL,
  `trx_amount` decimal(14,0) NOT NULL,
  `client_id` char(3) COLLATE utf8_unicode_ci NOT NULL COMMENT 'diisi dengan 310 (kode IULI)',
  `billing_type` char(1) COLLATE utf8_unicode_ci NOT NULL COMMENT 'diisi dengan (o, c, i, m, n atau x)',
  `customer_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `customer_email` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'bni.finance@iuli.ac.id',
  `customer_phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `virtual_account` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT 'harus 16 digit',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `va_status` tinyint(1) NOT NULL COMMENT 'diisi dengan 1 (active) 2 (inactive)',
  `payment_ntb` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_amount` decimal(14,0) NOT NULL DEFAULT 0,
  `cumulative_payment_amount` decimal(14,0) NOT NULL DEFAULT 0,
  `datetime_created` datetime NOT NULL,
  `datetime_expired` datetime NOT NULL,
  `datetime_payment` datetime DEFAULT NULL,
  `datetime_last_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bni_transactions`
--

CREATE TABLE `bni_transactions` (
  `bni_transactions_id` int(11) NOT NULL,
  `receipt_number` int(11) DEFAULT NULL,
  `receipt_no` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transaction_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `trx_id` int(11) NOT NULL,
  `trx_amount` decimal(14,0) NOT NULL,
  `client_id` char(3) COLLATE utf8_unicode_ci NOT NULL COMMENT 'diisi dengan 310 (kode IULI)',
  `billing_type` char(1) COLLATE utf8_unicode_ci NOT NULL COMMENT 'diisi dengan (o, c, i, m, n atau x)',
  `customer_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `customer_email` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'bni.finance@iuli.ac.id',
  `customer_phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `virtual_account` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT 'harus 16 digit',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `va_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'diisi dengan 1 (active) 2 (inactive)',
  `payment_ntb` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_amount` decimal(14,0) NOT NULL DEFAULT 0,
  `cumulative_payment_amount` decimal(14,0) NOT NULL DEFAULT 0,
  `datetime_created` datetime NOT NULL DEFAULT current_timestamp(),
  `datetime_expired` datetime NOT NULL,
  `datetime_payment` datetime DEFAULT NULL,
  `datetime_last_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bni_transactions_payment`
--

CREATE TABLE `bni_transactions_payment` (
  `payment_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `bni_transactions_id` int(11) DEFAULT NULL,
  `sub_invoice_details_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `payment_amount` double NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dikti_jenis_aktivitas`
--

CREATE TABLE `dikti_jenis_aktivitas` (
  `id_jenis_aktivitas_mahasiswa` int(11) NOT NULL,
  `nama_jenis_aktivitas_mahasiswa` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dikti_jenis_aktivitas`
--

INSERT INTO `dikti_jenis_aktivitas` (`id_jenis_aktivitas_mahasiswa`, `nama_jenis_aktivitas_mahasiswa`, `date_added`, `timestamp`) VALUES
(1, 'Laporan akhir studi', '2020-08-27 14:23:50', '2020-08-27 07:23:50'),
(2, 'Tugas akhir', '2020-08-27 14:23:50', '2020-08-27 07:23:50'),
(3, 'Tesis', '2020-08-27 14:23:50', '2020-08-27 07:23:50'),
(4, 'Disertasi', '2020-08-27 14:23:51', '2020-08-27 07:23:51'),
(5, 'Kuliah kerja nyata', '2020-08-27 14:23:51', '2020-08-27 07:23:51'),
(6, 'Kerja praktek/PKL', '2020-08-27 14:23:51', '2020-08-27 07:23:51'),
(7, 'Bimbingan akademis', '2020-08-27 14:23:51', '2020-08-27 07:23:51'),
(10, 'Aktivitas kemahasiswaan', '2020-08-27 14:23:51', '2020-08-27 07:23:51'),
(11, 'Program kreativitas mahasiswa', '2020-08-27 14:23:51', '2020-08-27 07:23:51'),
(12, 'Kompetisi', '2020-08-27 14:23:51', '2020-08-27 07:23:51'),
(13, 'Magang/Praktik Kerja', '2023-08-03 10:33:02', '2023-08-03 03:33:02'),
(14, 'Asistensi Mengajar di Satuan Pendidikan', '2023-08-03 10:33:02', '2023-08-03 03:33:02'),
(15, 'Penelitian/Riset', '2023-08-03 10:33:02', '2023-08-03 03:33:02'),
(16, 'Proyek Kemanusiaan', '2023-08-03 10:33:02', '2023-08-03 03:33:02'),
(17, 'Kegiatan Wirausaha', '2023-08-03 10:33:02', '2023-08-03 03:33:02'),
(18, 'Studi/Proyek Independen', '2023-08-03 10:33:02', '2023-08-03 03:33:02'),
(19, 'Membangun Desa/Kuliah Kerja Nyata Tematik', '2023-08-03 10:33:02', '2023-08-03 03:33:02'),
(20, 'Bela Negara', '2023-08-03 10:33:02', '2023-08-03 03:33:02'),
(21, 'Pertukaran Pelajar', '2023-08-03 10:33:03', '2023-08-03 03:33:03'),
(22, 'Skripsi', '2023-08-03 10:33:03', '2023-08-03 03:33:03'),
(23, 'Kegiatan Penelitian Reguler', '2023-08-03 10:33:03', '2023-08-03 03:33:03');

-- --------------------------------------------------------

--
-- Table structure for table `dikti_jenis_mata_kuliah`
--

CREATE TABLE `dikti_jenis_mata_kuliah` (
  `id_jenis_mata_kuliah` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `nama_jenis_mata_kuliah` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dikti_jenis_mata_kuliah`
--

INSERT INTO `dikti_jenis_mata_kuliah` (`id_jenis_mata_kuliah`, `nama_jenis_mata_kuliah`, `date_added`, `timestamp`) VALUES
('A', 'WAJIB', '2019-06-25 10:50:55', '2019-06-25 03:50:55'),
('B', 'PILIHAN', '2019-06-25 10:50:55', '2019-06-25 03:50:55'),
('C', 'WAJIB PERMINTAAN', '2019-06-25 10:51:49', '2019-06-25 03:51:49'),
('D', 'PILIHAN PERMINTAAN', '2019-06-25 10:51:49', '2019-06-25 03:51:49'),
('S', 'TUGAS AKHIR / SKRIPSI / TESIS / DISERTASI', '2019-06-25 10:51:49', '2019-06-25 03:51:49');

-- --------------------------------------------------------

--
-- Table structure for table `dikti_kategori_kegiatan`
--

CREATE TABLE `dikti_kategori_kegiatan` (
  `id_kategori_kegiatan` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `nama_kategori_kegiatan` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dikti_kategori_kegiatan`
--

INSERT INTO `dikti_kategori_kegiatan` (`id_kategori_kegiatan`, `nama_kategori_kegiatan`, `date_added`, `timestamp`) VALUES
('110100', 'Melaksanakan perkuliahan/tutorial/perkuliahan praktikum dan membimbing, menguji serta menyelenggarakan pendidikan di laboratorium, praktik keguruan, bengkel/studio/kebun percobaan/teknologi pengajaran dan praktik lapangan', '2020-08-27 11:42:40', '2020-08-27 04:42:40'),
('110200', 'Membimbing seminar mahasiswa', '2020-08-27 11:42:40', '2020-08-27 04:42:40'),
('110300', 'Membimbing kuliah kerja nyata, praktik kerja nyata, atau praktik kerja lapangan', '2020-08-27 11:42:40', '2020-08-27 04:42:40'),
('110400', 'Membimbing dan ikut membimbing dalam menghasilkan disertasi, tesis, skripsi, dan laporan akhir studi yang sesuai dengan bidang penugasannya', '2020-08-27 11:42:41', '2020-08-27 04:42:41'),
('110401', 'Disertasi (pembimbing utama)', '2020-08-31 15:28:29', '2020-08-31 08:33:30'),
('110402', 'Tesis (pembimbing utama)', '2020-08-31 15:28:29', '2020-08-31 08:33:35'),
('110403', 'Skripsi (pembimbing utama)', '2020-08-31 15:28:29', '2020-08-31 08:33:38'),
('110404', 'Laporan akhir (pembimbing utama)', '2020-08-31 15:28:29', '2020-08-31 08:33:42'),
('110405', 'Disertasi (pembimbing pendamping)', '2020-08-31 15:28:29', '2020-08-31 08:33:44'),
('110406', 'Tesis (pembimbing pendamping)', '2020-08-31 15:30:02', '2020-08-31 08:33:49'),
('110407', 'Skripsi (pembimbing pendamping)', '2020-08-31 15:30:02', '2020-08-31 08:33:53'),
('110408', 'Laporan akhir (pembimbing pendamping)', '2020-08-31 15:30:02', '2020-08-31 08:33:57'),
('110500', 'Bertugas sebagai penguji pada ujian akhir', '2020-08-27 11:42:41', '2020-08-27 04:42:41'),
('110501', 'Ketua penguji', '2020-08-31 15:32:46', '2020-08-31 08:34:01'),
('110502', 'Anggota penguji', '2020-08-31 15:32:46', '2020-08-31 08:34:04'),
('110600', 'Membina kegiatan mahasiswa di bidang akademik dan kemahasiswaan', '2020-08-27 11:42:41', '2020-08-27 04:42:41'),
('110700', 'Mengembangkan program kuliah yang mempunyai nilai kebaharuan metode atau substansi', '2020-08-27 11:42:41', '2020-08-27 04:42:41'),
('110800', 'Mengembangkan bahan pengajaran/bahan kuliah yang mempunyai nilai kebaharuan', '2020-08-27 11:42:41', '2020-08-27 04:42:41'),
('110900', 'Menyampaikan orasi ilmiah di tingkat perguruan tinggi', '2020-08-27 11:42:41', '2020-08-27 04:42:41'),
('111000', 'Menduduki jabatan pimpinan perguruan tinggi ', '2020-08-27 11:42:41', '2020-08-27 04:42:41'),
('111100', 'Membimbing dosen yang mempunyai jabatan akademik lebih rendah', '2020-08-27 11:42:41', '2020-08-27 04:42:41'),
('111200', 'Melaksanakan kegiatan detasering dan pencangkokan di luar institusi tempat bekerja', '2020-08-27 11:42:41', '2020-08-27 04:42:41'),
('111300', 'Melakukan kegiatan pengembangan diri untuk meningkatkan kompetensi', '2020-08-27 11:42:41', '2020-08-27 04:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `dikti_kelompok_mata_kuliah`
--

CREATE TABLE `dikti_kelompok_mata_kuliah` (
  `id_kelompok_mata_kuliah` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `nama_kelompok_mata_kuliah` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dikti_kelompok_mata_kuliah`
--

INSERT INTO `dikti_kelompok_mata_kuliah` (`id_kelompok_mata_kuliah`, `nama_kelompok_mata_kuliah`, `date_added`, `timestamp`) VALUES
('A', 'MPK', '2019-06-25 10:58:01', '2019-06-25 03:58:01'),
('B', 'MKK', '2019-06-25 10:58:01', '2019-06-25 03:58:01'),
('C', 'MKB', '2019-06-25 10:58:01', '2019-06-25 03:58:01'),
('D', 'MPB', '2019-06-25 10:58:01', '2019-06-25 03:58:01'),
('E', 'MBB', '2019-06-25 10:58:01', '2019-06-25 03:58:01'),
('F', 'MKU/MKDU', '2019-06-25 10:58:19', '2019-06-25 03:58:19'),
('G', 'MKDK', '2019-06-25 10:58:19', '2019-06-25 03:58:19'),
('H', 'MKK', '2019-06-25 10:58:34', '2019-06-25 03:58:34');

-- --------------------------------------------------------

--
-- Table structure for table `dikti_message`
--

CREATE TABLE `dikti_message` (
  `error_code` int(11) NOT NULL,
  `error_message` varchar(225) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dikti_questions`
--

CREATE TABLE `dikti_questions` (
  `question_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `question_number` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_question_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `question_english_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_type` enum('alumni','company','exchange') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'alumni',
  `is_multiple` tinyint(1) NOT NULL,
  `is_direct_answer` tinyint(1) NOT NULL,
  `is_required` enum('TRUE','FALSE') COLLATE utf8_unicode_ci DEFAULT 'FALSE',
  `is_dikti_question` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=no',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dikti_question_answers`
--

CREATE TABLE `dikti_question_answers` (
  `answer_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `question_section_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `answer_content` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dikti_question_choices`
--

CREATE TABLE `dikti_question_choices` (
  `question_choice_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `question_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `dikti_choice_code` char(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dikti_input_code` char(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_choice_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `question_choice_name_english` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_choice_value` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_choices_order` int(11) NOT NULL DEFAULT 1,
  `has_free_text` tinyint(1) NOT NULL COMMENT '1: just input',
  `question_choice_description` enum('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FALSE',
  `input_position` enum('after_text','before_text') COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dikti_wilayah`
--

CREATE TABLE `dikti_wilayah` (
  `id_wilayah` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_negara` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `nama_wilayah` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `dikti_wilayah_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dikti_wilayah`
--

INSERT INTO `dikti_wilayah` (`id_wilayah`, `id_negara`, `nama_wilayah`, `dikti_wilayah_timestamp`) VALUES
('000000', 'ID', 'Indonesia', '2019-06-10 04:42:07'),
('010000', 'ID', 'Prop. D.K.I. Jakarta', '2019-06-10 04:42:07'),
('010100', 'ID', 'Kab. Kepulauan Seribu', '2019-06-10 04:42:07'),
('010101', 'ID', 'Kec. Kepulauan Seribu Selatan', '2019-06-10 04:42:07'),
('010102', 'ID', 'Kec. Kepulauan Seribu Utara', '2019-06-10 04:42:07'),
('016000', 'ID', 'Kota Jakarta Pusat', '2019-06-10 04:42:07'),
('016001', 'ID', 'Kec. Tanah Abang', '2019-06-10 04:42:07'),
('016002', 'ID', 'Kec. Menteng', '2019-06-10 04:42:07'),
('016003', 'ID', 'Kec. Senen', '2019-06-10 04:42:07'),
('016004', 'ID', 'Kec. Johar Baru', '2019-06-10 04:42:07'),
('016005', 'ID', 'Kec. Cempaka Putih', '2019-06-10 04:42:07'),
('016006', 'ID', 'Kec. Kemayoran', '2019-06-10 04:42:07'),
('016007', 'ID', 'Kec. Sawah Besar', '2019-06-10 04:42:07'),
('016008', 'ID', 'Kec. Gambir', '2019-06-10 04:42:07'),
('016100', 'ID', 'Kota Jakarta Utara', '2019-06-10 04:42:07'),
('016101', 'ID', 'Kec. Penjaringan', '2019-06-10 04:42:07'),
('016102', 'ID', 'Kec. Pademangan', '2019-06-10 04:42:07'),
('016103', 'ID', 'Kec. Tanjung Priok', '2019-06-10 04:42:07'),
('016104', 'ID', 'Kec. Koja', '2019-06-10 04:42:07'),
('016105', 'ID', 'Kec. Kelapa Gading', '2019-06-10 04:42:07'),
('016106', 'ID', 'Kec. Cilincing', '2019-06-10 04:42:07'),
('016200', 'ID', 'Kota Jakarta Barat', '2019-06-10 04:42:07'),
('016201', 'ID', 'Kec. Kembangan', '2019-06-10 04:42:07'),
('016202', 'ID', 'Kec. Kebon Jeruk', '2019-06-10 04:42:07'),
('016203', 'ID', 'Kec. Palmerah', '2019-06-10 04:42:07'),
('016204', 'ID', 'Kec. Grogol Petamburan', '2019-06-10 04:42:07'),
('016205', 'ID', 'Kec. Tambora', '2019-06-10 04:42:07'),
('016206', 'ID', 'Kec. Taman Sari', '2019-06-10 04:42:07'),
('016207', 'ID', 'Kec. Cengkareng', '2019-06-10 04:42:07'),
('016208', 'ID', 'Kec. Kali Deres', '2019-06-10 04:42:07'),
('016300', 'ID', 'Kota Jakarta Selatan', '2019-06-10 04:42:07'),
('016301', 'ID', 'Kec. Jagakarsa', '2019-06-10 04:42:07'),
('016302', 'ID', 'Kec. Pasar Minggu', '2019-06-10 04:42:07'),
('016303', 'ID', 'Kec. Cilandak', '2019-06-10 04:42:07'),
('016304', 'ID', 'Kec. Pesanggrahan', '2019-06-10 04:42:07'),
('016305', 'ID', 'Kec. Kebayoran Lama', '2019-06-10 04:42:07'),
('016306', 'ID', 'Kec. Kebayoran Baru', '2019-06-10 04:42:07'),
('016307', 'ID', 'Kec. Mampang Prapatan', '2019-06-10 04:42:07'),
('016308', 'ID', 'Kec. Pancoran', '2019-06-10 04:42:07'),
('016309', 'ID', 'Kec. Tebet', '2019-06-10 04:42:07'),
('016310', 'ID', 'Kec. Setia Budi', '2019-06-10 04:42:07'),
('016400', 'ID', 'Kota Jakarta Timur', '2019-06-10 04:42:07'),
('016401', 'ID', 'Kec. Pasar Rebo', '2019-06-10 04:42:07'),
('016402', 'ID', 'Kec. Ciracas', '2019-06-10 04:42:07'),
('016403', 'ID', 'Kec. Cipayung', '2019-06-10 04:42:07'),
('016404', 'ID', 'Kec. Makasar', '2019-06-10 04:42:07'),
('016405', 'ID', 'Kec. Kramat Jati', '2019-06-10 04:42:07'),
('016406', 'ID', 'Kec. Jatinegara', '2019-06-10 04:42:07'),
('016407', 'ID', 'Kec. Duren Sawit', '2019-06-10 04:42:07'),
('016408', 'ID', 'Kec. Cakung', '2019-06-10 04:42:07'),
('016409', 'ID', 'Kec. Pulo Gadung', '2019-06-10 04:42:07'),
('016410', 'ID', 'Kec. Matraman', '2019-06-10 04:42:07'),
('020000', 'ID', 'Prop. Jawa Barat', '2019-06-10 04:42:07'),
('020500', 'ID', 'Kab. Bogor', '2019-06-10 04:42:07'),
('020501', 'ID', 'Kec. Nanggung', '2019-06-10 04:42:07'),
('020502', 'ID', 'Kec. Leuwiliang', '2019-06-10 04:42:07'),
('020503', 'ID', 'Kec. Pamijahan', '2019-06-10 04:42:07'),
('020504', 'ID', 'Kec. Cibungbulang', '2019-06-10 04:42:07'),
('020505', 'ID', 'Kec. Ciampea', '2019-06-10 04:42:07'),
('020506', 'ID', 'Kec. Dramaga', '2019-06-10 04:42:07'),
('020507', 'ID', 'Kec. Ciomas', '2019-06-10 04:42:07'),
('020508', 'ID', 'Kec. Cijeruk', '2019-06-10 04:42:07'),
('020509', 'ID', 'Kec. Caringin', '2019-06-10 04:42:07'),
('020510', 'ID', 'Kec. Ciawi', '2019-06-10 04:42:07'),
('020511', 'ID', 'Kec. Cisarua', '2019-06-10 04:42:07'),
('020512', 'ID', 'Kec. Megamendung', '2019-06-10 04:42:07'),
('020513', 'ID', 'Kec. Sukaraja', '2019-06-10 04:42:07'),
('020514', 'ID', 'Kec. Babakan Madang', '2019-06-10 04:42:07'),
('020515', 'ID', 'Kec. Sukamakmur', '2019-06-10 04:42:07'),
('020516', 'ID', 'Kec. Cariu', '2019-06-10 04:42:07'),
('020517', 'ID', 'Kec. Jonggol', '2019-06-10 04:42:07'),
('020518', 'ID', 'Kec. Cileungsi', '2019-06-10 04:42:07'),
('020519', 'ID', 'Kec. Gunungputri', '2019-06-10 04:42:07'),
('020520', 'ID', 'Kec. Citeureup', '2019-06-10 04:42:07'),
('020521', 'ID', 'Kec. Cibinong', '2019-06-10 04:42:07'),
('020522', 'ID', 'Kec. Bojong Gede', '2019-06-10 04:42:07'),
('020523', 'ID', 'Kec. Kemang', '2019-06-10 04:42:07'),
('020524', 'ID', 'Kec. Parung', '2019-06-10 04:42:07'),
('020525', 'ID', 'Kec. Gunung Sindur', '2019-06-10 04:42:07'),
('020526', 'ID', 'Kec. Rumpin', '2019-06-10 04:42:07'),
('020527', 'ID', 'Kec. Cigudeg', '2019-06-10 04:42:07'),
('020528', 'ID', 'Kec. Jasinga', '2019-06-10 04:42:07'),
('020529', 'ID', 'Kec. Tenjo', '2019-06-10 04:42:07'),
('020530', 'ID', 'Kec. Parungpanjang', '2019-06-10 04:42:07'),
('020531', 'ID', 'Kec. Tamansari', '2019-06-10 04:42:07'),
('020532', 'ID', 'Kec. Ciseeng', '2019-06-10 04:42:07'),
('020533', 'ID', 'Kec. Kelapa Nunggal', '2019-06-10 04:42:07'),
('020534', 'ID', 'Kec. Sukajaya', '2019-06-10 04:42:07'),
('020535', 'ID', 'Kec. Ranca Bungur', '2019-06-10 04:42:07'),
('020536', 'ID', 'Kec. Tanjung Sari', '2019-06-10 04:42:07'),
('020537', 'ID', 'Kec. Tajurhalang', '2019-06-10 04:42:07'),
('020538', 'ID', 'Kec. Cigombong', '2019-06-10 04:42:07'),
('020539', 'ID', 'Kec. Leuwisadeng', '2019-06-10 04:42:07'),
('020540', 'ID', 'Kec. Tenjolaya', '2019-06-10 04:42:07'),
('020600', 'ID', 'Kab. Sukabumi', '2019-06-10 04:42:07'),
('020601', 'ID', 'Kec. Ciemas', '2019-06-10 04:42:07'),
('020602', 'ID', 'Kec. Ciracap', '2019-06-10 04:42:07'),
('020603', 'ID', 'Kec. Surade', '2019-06-10 04:42:07'),
('020604', 'ID', 'Kec. Jampang Kulon', '2019-06-10 04:42:07'),
('020605', 'ID', 'Kec. Kalibunder', '2019-06-10 04:42:07'),
('020606', 'ID', 'Kec. Tegalbuleud', '2019-06-10 04:42:07'),
('020607', 'ID', 'Kec. Cidolog', '2019-06-10 04:42:07'),
('020608', 'ID', 'Kec. Sagaranten', '2019-06-10 04:42:07'),
('020609', 'ID', 'Kec. Pabuaran', '2019-06-10 04:42:07'),
('020610', 'ID', 'Kec. Lengkong', '2019-06-10 04:42:07'),
('020611', 'ID', 'Kec. Pelabuhan Ratu', '2019-06-10 04:42:07'),
('020612', 'ID', 'Kec. Warung Kiara', '2019-06-10 04:42:07'),
('020613', 'ID', 'Kec. Jampang Tengah', '2019-06-10 04:42:07'),
('020614', 'ID', 'Kec. Cikembar', '2019-06-10 04:42:07'),
('020615', 'ID', 'Kec. Nyalindung', '2019-06-10 04:42:07'),
('020616', 'ID', 'Kec. Gegerbitung', '2019-06-10 04:42:07'),
('020617', 'ID', 'Kec. Sukaraja', '2019-06-10 04:42:07'),
('020618', 'ID', 'Kec. Sukabumi', '2019-06-10 04:42:07'),
('020619', 'ID', 'Kec. Kadudampit', '2019-06-10 04:42:07'),
('020620', 'ID', 'Kec. Cisaat', '2019-06-10 04:42:07'),
('020621', 'ID', 'Kec. Cibadak', '2019-06-10 04:42:07'),
('020622', 'ID', 'Kec. Nagrak', '2019-06-10 04:42:07'),
('020623', 'ID', 'Kec. Cicurug', '2019-06-10 04:42:07'),
('020624', 'ID', 'Kec. Cidahu', '2019-06-10 04:42:07'),
('020625', 'ID', 'Kec. Parakansalak', '2019-06-10 04:42:07'),
('020626', 'ID', 'Kec. Parungkuda', '2019-06-10 04:42:07'),
('020627', 'ID', 'Kec. Kalapa Nunggal', '2019-06-10 04:42:07'),
('020628', 'ID', 'Kec. Cikidang', '2019-06-10 04:42:07'),
('020629', 'ID', 'Kec. Cisolok', '2019-06-10 04:42:07'),
('020630', 'ID', 'Kec. Kabandungan', '2019-06-10 04:42:07'),
('020631', 'ID', 'Kec. Gunung Guruh', '2019-06-10 04:42:07'),
('020632', 'ID', 'Kec. Cikakak', '2019-06-10 04:42:07'),
('020633', 'ID', 'Kec. Bantar Gadung', '2019-06-10 04:42:07'),
('020634', 'ID', 'Kec. Cicantayan', '2019-06-10 04:42:07'),
('020635', 'ID', 'Kec. Simpenan', '2019-06-10 04:42:07'),
('020636', 'ID', 'Kec. Kebon Pedes', '2019-06-10 04:42:07'),
('020637', 'ID', 'Kec. Cidadap', '2019-06-10 04:42:07'),
('020638', 'ID', 'Kec. Cibitung', '2019-06-10 04:42:07'),
('020639', 'ID', 'Kec. Curugkembar', '2019-06-10 04:42:07'),
('020640', 'ID', 'Kec. Purabaya', '2019-06-10 04:42:07'),
('020641', 'ID', 'Kec. Cireunghas', '2019-06-10 04:42:07'),
('020642', 'ID', 'Kec. Sukalarang', '2019-06-10 04:42:07'),
('020643', 'ID', 'Kec. Caringin', '2019-06-10 04:42:07'),
('020644', 'ID', 'Kec. Bojong Genteng', '2019-06-10 04:42:07'),
('020645', 'ID', 'Kec. Waluran', '2019-06-10 04:42:07'),
('020646', 'ID', 'Kec. Cimanggu', '2019-06-10 04:42:07'),
('020647', 'ID', 'Kec. Ciambar', '2019-06-10 04:42:07'),
('020700', 'ID', 'Kab. Cianjur', '2019-06-10 04:42:07'),
('020701', 'ID', 'Kec. Agrabinta', '2019-06-10 04:42:07'),
('020702', 'ID', 'Kec. Sindang Barang', '2019-06-10 04:42:07'),
('020703', 'ID', 'Kec. Cidaun', '2019-06-10 04:42:07'),
('020704', 'ID', 'Kec. Naringgul', '2019-06-10 04:42:07'),
('020705', 'ID', 'Kec. Cibinong', '2019-06-10 04:42:07'),
('020706', 'ID', 'Kec. Tanggeung', '2019-06-10 04:42:07'),
('020707', 'ID', 'Kec. Kadupandak', '2019-06-10 04:42:07'),
('020708', 'ID', 'Kec. Takokak', '2019-06-10 04:42:07'),
('020709', 'ID', 'Kec. Sukanagara', '2019-06-10 04:42:07'),
('020710', 'ID', 'Kec. Pagelaran', '2019-06-10 04:42:07'),
('020711', 'ID', 'Kec. Campaka', '2019-06-10 04:42:07'),
('020712', 'ID', 'Kec. Cibeber', '2019-06-10 04:42:07'),
('020713', 'ID', 'Kec. Warungkondang', '2019-06-10 04:42:07'),
('020714', 'ID', 'Kec. Cilaku', '2019-06-10 04:42:07'),
('020715', 'ID', 'Kec. Sukaluyu', '2019-06-10 04:42:07'),
('020717', 'ID', 'Kec. Ciranjang', '2019-06-10 04:42:07'),
('020718', 'ID', 'Kec. Mande', '2019-06-10 04:42:07'),
('020719', 'ID', 'Kec. Karang Tengah', '2019-06-10 04:42:07'),
('020720', 'ID', 'Kec. Cianjur', '2019-06-10 04:42:07'),
('020721', 'ID', 'Kec. Cugenang', '2019-06-10 04:42:07'),
('020722', 'ID', 'Kec. Pacet', '2019-06-10 04:42:07'),
('020723', 'ID', 'Kec. Sukaresmi', '2019-06-10 04:42:07'),
('020724', 'ID', 'Kec. Cikalong Kulon', '2019-06-10 04:42:07'),
('020725', 'ID', 'Kec. Bojong Picung', '2019-06-10 04:42:07'),
('020726', 'ID', 'Kec. Campaka Mulya', '2019-06-10 04:42:07'),
('020727', 'ID', 'Kec. Cikadu', '2019-06-10 04:42:07'),
('020728', 'ID', 'Kec. Leles', '2019-06-10 04:42:07'),
('020729', 'ID', 'Kec. Cijati', '2019-06-10 04:42:07'),
('020730', 'ID', 'Kec. Gekbrong', '2019-06-10 04:42:07'),
('020731', 'ID', 'Kec. Cipanas', '2019-06-10 04:42:07'),
('020732', 'ID', 'Kec. Haurwangi', '2019-06-10 04:42:07'),
('020733', 'ID', 'Kec. Pasirkuda', '2019-06-10 04:42:07'),
('020800', 'ID', 'Kab. Bandung', '2019-06-10 04:42:07'),
('020801', 'ID', 'Kec. Ciwidey', '2019-06-10 04:42:07'),
('020802', 'ID', 'Kec. Pasirjambu', '2019-06-10 04:42:07'),
('020803', 'ID', 'Kec. Cimaung', '2019-06-10 04:42:07'),
('020804', 'ID', 'Kec. Pangalengan', '2019-06-10 04:42:07'),
('020805', 'ID', 'Kec. Kertasari', '2019-06-10 04:42:07'),
('020806', 'ID', 'Kec. Pacet', '2019-06-10 04:42:07'),
('020807', 'ID', 'Kec. Ibun', '2019-06-10 04:42:07'),
('020808', 'ID', 'Kec. Paseh', '2019-06-10 04:42:07'),
('020809', 'ID', 'Kec. Cikancung', '2019-06-10 04:42:07'),
('020810', 'ID', 'Kec. Cicalengka', '2019-06-10 04:42:07'),
('020811', 'ID', 'Kec. Rancaekek', '2019-06-10 04:42:07'),
('020812', 'ID', 'Kec. Majalaya', '2019-06-10 04:42:07'),
('020813', 'ID', 'Kec. Ciparay', '2019-06-10 04:42:07'),
('020814', 'ID', 'Kec. Bale Endah', '2019-06-10 04:42:07'),
('020815', 'ID', 'Kec. Arjasari', '2019-06-10 04:42:07'),
('020816', 'ID', 'Kec. Banjaran', '2019-06-10 04:42:07'),
('020817', 'ID', 'Kec. Pameungpeuk', '2019-06-10 04:42:07'),
('020818', 'ID', 'Kec. Ketapang', '2019-06-10 04:42:07'),
('020819', 'ID', 'Kec. Soreang', '2019-06-10 04:42:07'),
('020820', 'ID', 'Kec. Marga Asih', '2019-06-10 04:42:07'),
('020821', 'ID', 'Kec. Margahayu', '2019-06-10 04:42:07'),
('020822', 'ID', 'Kec. Dayeuhkolot', '2019-06-10 04:42:07'),
('020823', 'ID', 'Kec. Bojongsoang', '2019-06-10 04:42:07'),
('020824', 'ID', 'Kec. Cileunyi', '2019-06-10 04:42:07'),
('020825', 'ID', 'Kec. Cilengkrang', '2019-06-10 04:42:07'),
('020826', 'ID', 'Kec. Cimenyan', '2019-06-10 04:42:07'),
('020829', 'ID', 'Kec. Rancabali', '2019-06-10 04:42:07'),
('020830', 'ID', 'Kec. Nagreg', '2019-06-10 04:42:07'),
('020831', 'ID', 'Kec. Solokan Jeruk', '2019-06-10 04:42:07'),
('020832', 'ID', 'Kec. Cangkuang', '2019-06-10 04:42:07'),
('020833', 'ID', 'Kec. Kutawaringin', '2019-06-10 04:42:07'),
('021000', 'ID', 'Kab. Sumedang', '2019-06-10 04:42:07'),
('021001', 'ID', 'Kec. Jatinangor', '2019-06-10 04:42:07'),
('021002', 'ID', 'Kec. Cimanggung', '2019-06-10 04:42:07'),
('021003', 'ID', 'Kec. Tanjungsari', '2019-06-10 04:42:07'),
('021004', 'ID', 'Kec. Rancakalong', '2019-06-10 04:42:07'),
('021005', 'ID', 'Kec. Sumedang Selatan', '2019-06-10 04:42:07'),
('021006', 'ID', 'Kec. Sumedang Utara', '2019-06-10 04:42:07'),
('021007', 'ID', 'Kec. Situraja', '2019-06-10 04:42:07'),
('021008', 'ID', 'Kec. Darmaraja', '2019-06-10 04:42:07'),
('021009', 'ID', 'Kec. Cibugel', '2019-06-10 04:42:07'),
('021010', 'ID', 'Kec. Wado', '2019-06-10 04:42:07'),
('021012', 'ID', 'Kec. Tomo', '2019-06-10 04:42:07'),
('021013', 'ID', 'Kec. Ujung Jaya', '2019-06-10 04:42:07'),
('021014', 'ID', 'Kec. Conggeang', '2019-06-10 04:42:07'),
('021015', 'ID', 'Kec. Paseh', '2019-06-10 04:42:07'),
('021016', 'ID', 'Kec. Cimalaka', '2019-06-10 04:42:07'),
('021017', 'ID', 'Kec. Tanjungkerta', '2019-06-10 04:42:07'),
('021018', 'ID', 'Kec. Buah Dua', '2019-06-10 04:42:07'),
('021019', 'ID', 'Kec. Ganeas', '2019-06-10 04:42:07'),
('021020', 'ID', 'Kec. Jati Gede', '2019-06-10 04:42:07'),
('021023', 'ID', 'Kec. Pamulihan', '2019-06-10 04:42:07'),
('021024', 'ID', 'Kec. Cisitu', '2019-06-10 04:42:07'),
('021025', 'ID', 'Kec. Jatinunggal', '2019-06-10 04:42:07'),
('021026', 'ID', 'Kec. Cisarua', '2019-06-10 04:42:07'),
('021027', 'ID', 'Kec. Tanjungmedar', '2019-06-10 04:42:07'),
('021028', 'ID', 'Kec. Surian', '2019-06-10 04:42:07'),
('021029', 'ID', 'Kec. Sukasari', '2019-06-10 04:42:07'),
('021100', 'ID', 'Kab. Garut', '2019-06-10 04:42:07'),
('021101', 'ID', 'Kec. Talegong', '2019-06-10 04:42:07'),
('021102', 'ID', 'Kec. Cisewu', '2019-06-10 04:42:07'),
('021103', 'ID', 'Kec. Bungbulang', '2019-06-10 04:42:07'),
('021104', 'ID', 'Kec. Pamulihan', '2019-06-10 04:42:07'),
('021105', 'ID', 'Kec. Pakenjeng', '2019-06-10 04:42:07'),
('021106', 'ID', 'Kec. Cikelet', '2019-06-10 04:42:07'),
('021107', 'ID', 'Kec. Pameungpeuk', '2019-06-10 04:42:07'),
('021108', 'ID', 'Kec. Cibalong', '2019-06-10 04:42:07'),
('021109', 'ID', 'Kec. Cisompet', '2019-06-10 04:42:07'),
('021110', 'ID', 'Kec. Peundeuy', '2019-06-10 04:42:07'),
('021111', 'ID', 'Kec. Singajaya', '2019-06-10 04:42:07'),
('021112', 'ID', 'Kec. Cikajang', '2019-06-10 04:42:07'),
('021113', 'ID', 'Kec. Banjarwangi', '2019-06-10 04:42:07'),
('021114', 'ID', 'Kec. Cilawu', '2019-06-10 04:42:07'),
('021115', 'ID', 'Kec. Bayongbong', '2019-06-10 04:42:07'),
('021116', 'ID', 'Kec. Cisurupan', '2019-06-10 04:42:07'),
('021117', 'ID', 'Kec. Samarang', '2019-06-10 04:42:07'),
('021119', 'ID', 'Kec. Garut Kota', '2019-06-10 04:42:07'),
('021120', 'ID', 'Kec. Karangpawitan', '2019-06-10 04:42:07'),
('021121', 'ID', 'Kec. Wanaraja', '2019-06-10 04:42:07'),
('021122', 'ID', 'Kec. Sukawening', '2019-06-10 04:42:07'),
('021123', 'ID', 'Kec. Banyuresmi', '2019-06-10 04:42:07'),
('021124', 'ID', 'Kec. Leles', '2019-06-10 04:42:07'),
('021125', 'ID', 'Kec. Leuwigoong', '2019-06-10 04:42:07'),
('021126', 'ID', 'Kec. Cibatu', '2019-06-10 04:42:07'),
('021127', 'ID', 'Kec. Cibiuk', '2019-06-10 04:42:07'),
('021128', 'ID', 'Kec. Kadungora', '2019-06-10 04:42:07'),
('021129', 'ID', 'Kec. Blubur Limbangan', '2019-06-10 04:42:07'),
('021130', 'ID', 'Kec. Selaawi', '2019-06-10 04:42:07'),
('021131', 'ID', 'Kec. Malangbong', '2019-06-10 04:42:07'),
('021132', 'ID', 'Kec. Mekarmukti', '2019-06-10 04:42:07'),
('021133', 'ID', 'Kec. Caringin', '2019-06-10 04:42:07'),
('021134', 'ID', 'Kec. Cihurip', '2019-06-10 04:42:07'),
('021135', 'ID', 'Kec. Sukaresmi', '2019-06-10 04:42:07'),
('021136', 'ID', 'Kec. Pasirwangi', '2019-06-10 04:42:07'),
('021137', 'ID', 'Kec. Karangtengah', '2019-06-10 04:42:07'),
('021138', 'ID', 'Kec. Kersamanah', '2019-06-10 04:42:07'),
('021139', 'ID', 'Kec. Tarogong Kaler', '2019-06-10 04:42:07'),
('021140', 'ID', 'Kec. Tarogong Kidul', '2019-06-10 04:42:07'),
('021141', 'ID', 'Kec. Cigedug', '2019-06-10 04:42:07'),
('021142', 'ID', 'Kec. Sucinaraja', '2019-06-10 04:42:07'),
('021143', 'ID', 'Kec. Pangatikan', '2019-06-10 04:42:07'),
('021200', 'ID', 'Kab. Tasikmalaya', '2019-06-10 04:42:07'),
('021201', 'ID', 'Kec. Cipatujah', '2019-06-10 04:42:07'),
('021202', 'ID', 'Kec. Karangnunggal', '2019-06-10 04:42:07'),
('021203', 'ID', 'Kec. Cikalong', '2019-06-10 04:42:07'),
('021204', 'ID', 'Kec. Panca Tengah', '2019-06-10 04:42:07'),
('021205', 'ID', 'Kec. Cikatomas', '2019-06-10 04:42:07'),
('021206', 'ID', 'Kec. Cibalong', '2019-06-10 04:42:07'),
('021207', 'ID', 'Kec. Bantarkalong', '2019-06-10 04:42:07'),
('021208', 'ID', 'Kec. Bojong Gambir', '2019-06-10 04:42:07'),
('021209', 'ID', 'Kec. Sodonghilir', '2019-06-10 04:42:07'),
('021210', 'ID', 'Kec. Taraju', '2019-06-10 04:42:07'),
('021211', 'ID', 'Kec. Salawu', '2019-06-10 04:42:07'),
('021212', 'ID', 'Kec. Tanjungjaya', '2019-06-10 04:42:07'),
('021213', 'ID', 'Kec. Sukaraja', '2019-06-10 04:42:07'),
('021214', 'ID', 'Kec. Salopa', '2019-06-10 04:42:07'),
('021215', 'ID', 'Kec. Cineam', '2019-06-10 04:42:07'),
('021216', 'ID', 'Kec. Manonjaya', '2019-06-10 04:42:07'),
('021219', 'ID', 'Kec. Singaparna', '2019-06-10 04:42:07'),
('021220', 'ID', 'Kec. Cigalontang', '2019-06-10 04:42:07'),
('021221', 'ID', 'Kec. Leuwisari', '2019-06-10 04:42:07'),
('021223', 'ID', 'Kec. Cisayong', '2019-06-10 04:42:07'),
('021224', 'ID', 'Kec. Rajapolah', '2019-06-10 04:42:07'),
('021225', 'ID', 'Kec. Jamanis', '2019-06-10 04:42:07'),
('021226', 'ID', 'Kec. Ciawi', '2019-06-10 04:42:07'),
('021227', 'ID', 'Kec. Pagerageung', '2019-06-10 04:42:07'),
('021228', 'ID', 'Kec. Parung Ponteng', '2019-06-10 04:42:07'),
('021229', 'ID', 'Kec. Sariwangi', '2019-06-10 04:42:07'),
('021230', 'ID', 'Kec. Sukaratu', '2019-06-10 04:42:07'),
('021231', 'ID', 'Kec. Sukarame', '2019-06-10 04:42:07'),
('021232', 'ID', 'Kec. Bojong Asih', '2019-06-10 04:42:07'),
('021233', 'ID', 'Kec. Culamega', '2019-06-10 04:42:07'),
('021234', 'ID', 'Kec. Puspahiang', '2019-06-10 04:42:07'),
('021235', 'ID', 'Kec. Jatiwaras', '2019-06-10 04:42:07'),
('021236', 'ID', 'Kec. Mangunreja', '2019-06-10 04:42:07'),
('021237', 'ID', 'Kec. Gunung Tanjung', '2019-06-10 04:42:07'),
('021238', 'ID', 'Kec. Karang Jaya', '2019-06-10 04:42:07'),
('021239', 'ID', 'Kec. Pada Kembang', '2019-06-10 04:42:07'),
('021240', 'ID', 'Kec. Sukahening', '2019-06-10 04:42:07'),
('021241', 'ID', 'Kec. Kadipaten', '2019-06-10 04:42:07'),
('021242', 'ID', 'Kec. Sukaresik', '2019-06-10 04:42:07'),
('021400', 'ID', 'Kab. Ciamis', '2019-06-10 04:42:07'),
('021401', 'ID', 'Kec. Cimerak', '2019-06-10 04:42:07'),
('021402', 'ID', 'Kec. Cijulang', '2019-06-10 04:42:07'),
('021403', 'ID', 'Kec. Cigugur', '2019-06-10 04:42:07'),
('021404', 'ID', 'Kec. Langkaplancar', '2019-06-10 04:42:07'),
('021405', 'ID', 'Kec. Parigi', '2019-06-10 04:42:07'),
('021406', 'ID', 'Kec. Sidamulih', '2019-06-10 04:42:07'),
('021407', 'ID', 'Kec. Pangandaran', '2019-06-10 04:42:07'),
('021408', 'ID', 'Kec. Kalipucang', '2019-06-10 04:42:07'),
('021409', 'ID', 'Kec. Padaherang', '2019-06-10 04:42:07'),
('021410', 'ID', 'Kec. Banjarsari', '2019-06-10 04:42:07'),
('021411', 'ID', 'Kec. Lakbok', '2019-06-10 04:42:07'),
('021412', 'ID', 'Kec. Pamarican', '2019-06-10 04:42:07'),
('021413', 'ID', 'Kec. Cidolog', '2019-06-10 04:42:07'),
('021414', 'ID', 'Kec. Cimaragas', '2019-06-10 04:42:07'),
('021415', 'ID', 'Kec. Cijeungjing', '2019-06-10 04:42:07'),
('021416', 'ID', 'Kec. Cisaga', '2019-06-10 04:42:07'),
('021417', 'ID', 'Kec. Tambaksari', '2019-06-10 04:42:07'),
('021418', 'ID', 'Kec. Rancah', '2019-06-10 04:42:07'),
('021419', 'ID', 'Kec. Rajadesa', '2019-06-10 04:42:07'),
('021420', 'ID', 'Kec. Sukadana', '2019-06-10 04:42:07'),
('021421', 'ID', 'Kec. Ciamis', '2019-06-10 04:42:07'),
('021422', 'ID', 'Kec. Cikoneng', '2019-06-10 04:42:07'),
('021423', 'ID', 'Kec. Cihaurbeuti', '2019-06-10 04:42:07'),
('021424', 'ID', 'Kec. Sadananya', '2019-06-10 04:42:07'),
('021425', 'ID', 'Kec. Cipaku', '2019-06-10 04:42:07'),
('021426', 'ID', 'Kec. Jatinagara', '2019-06-10 04:42:07'),
('021427', 'ID', 'Kec. Panawangan', '2019-06-10 04:42:07'),
('021428', 'ID', 'Kec. Kawali', '2019-06-10 04:42:07'),
('021429', 'ID', 'Kec. Panjalu', '2019-06-10 04:42:07'),
('021430', 'ID', 'Kec. Panumbangan', '2019-06-10 04:42:07'),
('021431', 'ID', 'Kec. Panjalu Utara/Sukamantri', '2019-06-10 04:42:07'),
('021432', 'ID', 'Kec. Sindangkasih', '2019-06-10 04:42:07'),
('021433', 'ID', 'Kec. Purwadadi', '2019-06-10 04:42:07'),
('021434', 'ID', 'Kec. Baregbeg', '2019-06-10 04:42:07'),
('021435', 'ID', 'Kec. Lumbung', '2019-06-10 04:42:07'),
('021436', 'ID', 'Kec. Mangunjaya', '2019-06-10 04:42:07'),
('021437', 'ID', 'Sukamantri', '2019-06-10 04:42:07'),
('021438', 'ID', 'Kec. Banjaranyar', '2020-02-10 03:07:43'),
('021490', 'ID', 'Padaherang', '2019-06-10 04:42:07'),
('021491', 'ID', 'Kalipucang', '2019-06-10 04:42:07'),
('021492', 'ID', 'Pangandaran', '2019-06-10 04:42:07'),
('021493', 'ID', 'Sidamulih', '2019-06-10 04:42:07'),
('021494', 'ID', 'Parigi', '2019-06-10 04:42:07'),
('021495', 'ID', 'Cimerak', '2019-06-10 04:42:07'),
('021496', 'ID', 'Cigugur', '2019-06-10 04:42:07'),
('021497', 'ID', 'Langkaplancar', '2019-06-10 04:42:07'),
('021498', 'ID', 'Mangunjaya', '2019-06-10 04:42:07'),
('021500', 'ID', 'Kab. Kuningan', '2019-06-10 04:42:07'),
('021501', 'ID', 'Kec. Darma', '2019-06-10 04:42:07'),
('021502', 'ID', 'Kec. Kadugede', '2019-06-10 04:42:07'),
('021503', 'ID', 'Kec. Ciniru', '2019-06-10 04:42:07'),
('021504', 'ID', 'Kec. Selajambe', '2019-06-10 04:42:07'),
('021505', 'ID', 'Kec. Subang', '2019-06-10 04:42:07'),
('021506', 'ID', 'Kec. Ciwaru', '2019-06-10 04:42:07'),
('021507', 'ID', 'Kec. Cibingbin', '2019-06-10 04:42:07'),
('021508', 'ID', 'Kec. Luragung', '2019-06-10 04:42:07'),
('021509', 'ID', 'Kec. Cidahu', '2019-06-10 04:42:07'),
('021510', 'ID', 'Kec. Ciawigebang', '2019-06-10 04:42:07'),
('021511', 'ID', 'Kec. Lebakwangi', '2019-06-10 04:42:07'),
('021512', 'ID', 'Kec. Garawangi', '2019-06-10 04:42:07'),
('021513', 'ID', 'Kec. Kuningan', '2019-06-10 04:42:07'),
('021514', 'ID', 'Kec. Cigugur', '2019-06-10 04:42:07'),
('021515', 'ID', 'Kec. Kramatmulya', '2019-06-10 04:42:07'),
('021516', 'ID', 'Kec. Jalaksana', '2019-06-10 04:42:07'),
('021517', 'ID', 'Kec. Cilimus', '2019-06-10 04:42:07'),
('021518', 'ID', 'Kec. Mandirancan', '2019-06-10 04:42:07'),
('021519', 'ID', 'Kec. Pasawahan', '2019-06-10 04:42:07'),
('021520', 'ID', 'Kec. Pancalang', '2019-06-10 04:42:07'),
('021521', 'ID', 'Kec. Cipicung', '2019-06-10 04:42:07'),
('021522', 'ID', 'Kec. Kalimanggis', '2019-06-10 04:42:07'),
('021523', 'ID', 'Kec. Japara', '2019-06-10 04:42:07'),
('021524', 'ID', 'Kec. Karangkancana', '2019-06-10 04:42:07'),
('021525', 'ID', 'Kec. Nusaherang', '2019-06-10 04:42:07'),
('021526', 'ID', 'Kec. Cilebak', '2019-06-10 04:42:07'),
('021527', 'ID', 'Kec. Hantara', '2019-06-10 04:42:07'),
('021528', 'ID', 'Kec. Cimahi', '2019-06-10 04:42:07'),
('021529', 'ID', 'Kec. Cibeureum', '2019-06-10 04:42:07'),
('021530', 'ID', 'Kec. Sindang Agung', '2019-06-10 04:42:07'),
('021531', 'ID', 'Kec. Maleber', '2019-06-10 04:42:07'),
('021532', 'ID', 'Kec. Ciganda Mekar', '2019-06-10 04:42:07'),
('021600', 'ID', 'Kab. Majalengka', '2019-06-10 04:42:07'),
('021601', 'ID', 'Kec. Lemahsugih', '2019-06-10 04:42:07'),
('021602', 'ID', 'Kec. Bantarujeg', '2019-06-10 04:42:07'),
('021603', 'ID', 'Kec. Cikijing', '2019-06-10 04:42:07'),
('021604', 'ID', 'Kec. Talaga', '2019-06-10 04:42:07'),
('021605', 'ID', 'Kec. Argapura', '2019-06-10 04:42:07'),
('021606', 'ID', 'Kec. Maja', '2019-06-10 04:42:07'),
('021607', 'ID', 'Kec. Majalengka', '2019-06-10 04:42:07'),
('021608', 'ID', 'Kec. Cigasong', '2019-06-10 04:42:07'),
('021609', 'ID', 'Kec. Sukahaji', '2019-06-10 04:42:07'),
('021610', 'ID', 'Kec. Rajagaluh', '2019-06-10 04:42:07'),
('021611', 'ID', 'Kec. Sindangwangi', '2019-06-10 04:42:07'),
('021612', 'ID', 'Kec. Leuwimunding', '2019-06-10 04:42:07'),
('021613', 'ID', 'Kec. Palasah', '2019-06-10 04:42:07'),
('021614', 'ID', 'Kec. Jatiwangi', '2019-06-10 04:42:07'),
('021615', 'ID', 'Kec. Dawuan', '2019-06-10 04:42:07'),
('021616', 'ID', 'Kec. Panyingkiran', '2019-06-10 04:42:07'),
('021617', 'ID', 'Kec. Kadipaten', '2019-06-10 04:42:07'),
('021618', 'ID', 'Kec. Kertajati', '2019-06-10 04:42:07'),
('021619', 'ID', 'Kec. Jatitujuh', '2019-06-10 04:42:07'),
('021620', 'ID', 'Kec. Ligung', '2019-06-10 04:42:07'),
('021621', 'ID', 'Kec. Sumberjaya', '2019-06-10 04:42:07'),
('021622', 'ID', 'Kec. Banjaran', '2019-06-10 04:42:07'),
('021623', 'ID', 'Kec. Cingambul', '2019-06-10 04:42:07'),
('021624', 'ID', 'Kec. Mala usma', '2019-06-10 04:42:07'),
('021625', 'ID', 'Kec. Sindang', '2019-06-10 04:42:07'),
('021626', 'ID', 'Kec. Kasokandel', '2019-06-10 04:42:07'),
('021700', 'ID', 'Kab. Cirebon', '2019-06-10 04:42:07'),
('021701', 'ID', 'Kec. Waled', '2019-06-10 04:42:07'),
('021702', 'ID', 'Kec. Ciledug', '2019-06-10 04:42:07'),
('021703', 'ID', 'Kec. Losari', '2019-06-10 04:42:07'),
('021704', 'ID', 'Kec. Babakan', '2019-06-10 04:42:07'),
('021705', 'ID', 'Kec. Karang Sembung', '2019-06-10 04:42:07'),
('021706', 'ID', 'Kec. Lemah Abang', '2019-06-10 04:42:07'),
('021707', 'ID', 'Kec. Sedong', '2019-06-10 04:42:07'),
('021708', 'ID', 'Kec. Astana Japura', '2019-06-10 04:42:07'),
('021709', 'ID', 'Kec. Mundu', '2019-06-10 04:42:07'),
('021710', 'ID', 'Kec. Beber', '2019-06-10 04:42:07'),
('021712', 'ID', 'Kec. Sumber', '2019-06-10 04:42:07'),
('021713', 'ID', 'Kec. Palimanan', '2019-06-10 04:42:07'),
('021714', 'ID', 'Kec. Plumbon', '2019-06-10 04:42:07'),
('021715', 'ID', 'Kec. Weru', '2019-06-10 04:42:07'),
('021718', 'ID', 'Kec. Kapetakan', '2019-06-10 04:42:07'),
('021719', 'ID', 'Kec. Klangenan', '2019-06-10 04:42:07'),
('021720', 'ID', 'Kec. Arjawinangun', '2019-06-10 04:42:07'),
('021721', 'ID', 'Kec. Ciwaringin', '2019-06-10 04:42:07'),
('021722', 'ID', 'Kec. Susukan', '2019-06-10 04:42:07'),
('021723', 'ID', 'Kec. Gegesik', '2019-06-10 04:42:07'),
('021724', 'ID', 'Kec. Susukan Lebak', '2019-06-10 04:42:07'),
('021725', 'ID', 'Kec. Pabedilan', '2019-06-10 04:42:07'),
('021726', 'ID', 'Kec. Dukupuntang', '2019-06-10 04:42:07'),
('021727', 'ID', 'Kec. Panguragan', '2019-06-10 04:42:07'),
('021728', 'ID', 'Kec. Kaliwedi', '2019-06-10 04:42:07'),
('021729', 'ID', 'Kec. Pangenan', '2019-06-10 04:42:07'),
('021730', 'ID', 'Kec. Gebang', '2019-06-10 04:42:07'),
('021731', 'ID', 'Kec. Depok', '2019-06-10 04:42:07'),
('021732', 'ID', 'Kec. Kedawung', '2019-06-10 04:42:07'),
('021733', 'ID', 'Kec. Karang Wereng', '2019-06-10 04:42:07'),
('021734', 'ID', 'Kec. Talun', '2019-06-10 04:42:07'),
('021735', 'ID', 'Kec. Gunung Jati', '2019-06-10 04:42:07'),
('021736', 'ID', 'Kec. Pasaleman', '2019-06-10 04:42:07'),
('021737', 'ID', 'Kec. Pabuaran', '2019-06-10 04:42:07'),
('021738', 'ID', 'Kec. Tengah Tani', '2019-06-10 04:42:07'),
('021739', 'ID', 'Kec. Plered', '2019-06-10 04:42:07'),
('021740', 'ID', 'Kec. Gempol', '2019-06-10 04:42:07'),
('021741', 'ID', 'Kec. Greged', '2019-06-10 04:42:07'),
('021742', 'ID', 'Kec. Suranenggala', '2019-06-10 04:42:07'),
('021743', 'ID', 'Kec. Jamblang', '2019-06-10 04:42:07'),
('021800', 'ID', 'Kab. Indramayu', '2019-06-10 04:42:07'),
('021801', 'ID', 'Kec. Haurgeulis', '2019-06-10 04:42:07'),
('021802', 'ID', 'Kec. Kroya', '2019-06-10 04:42:07'),
('021803', 'ID', 'Kec. Gabuswetan', '2019-06-10 04:42:07'),
('021804', 'ID', 'Kec. Cikedung', '2019-06-10 04:42:07'),
('021805', 'ID', 'Kec. Lelea', '2019-06-10 04:42:07'),
('021806', 'ID', 'Kec. Bangodua', '2019-06-10 04:42:07'),
('021807', 'ID', 'Kec. Widasari', '2019-06-10 04:42:07'),
('021808', 'ID', 'Kec. Kertasemaya', '2019-06-10 04:42:07'),
('021809', 'ID', 'Kec. Krangkeng', '2019-06-10 04:42:07'),
('021810', 'ID', 'Kec. Karangampel', '2019-06-10 04:42:07'),
('021811', 'ID', 'Kec. Juntinyuat', '2019-06-10 04:42:07'),
('021812', 'ID', 'Kec. Sliyeg', '2019-06-10 04:42:07'),
('021813', 'ID', 'Kec. Jatibarang', '2019-06-10 04:42:07'),
('021814', 'ID', 'Kec. Balongan', '2019-06-10 04:42:07'),
('021815', 'ID', 'Kec. Indramayu', '2019-06-10 04:42:07'),
('021816', 'ID', 'Kec. Sindang', '2019-06-10 04:42:07'),
('021817', 'ID', 'Kec. Lohbener', '2019-06-10 04:42:07'),
('021818', 'ID', 'Kec. Losarang', '2019-06-10 04:42:07'),
('021819', 'ID', 'Kec. Kandanghaur', '2019-06-10 04:42:07'),
('021820', 'ID', 'Kec. Bongas', '2019-06-10 04:42:07'),
('021821', 'ID', 'Kec. Anjatan', '2019-06-10 04:42:07'),
('021822', 'ID', 'Kec. Sukra', '2019-06-10 04:42:07'),
('021823', 'ID', 'Kec. Arahan', '2019-06-10 04:42:07'),
('021824', 'ID', 'Kec. Cantigi', '2019-06-10 04:42:07'),
('021825', 'ID', 'Kec. Gantar', '2019-06-10 04:42:07'),
('021826', 'ID', 'Kec. Terisi', '2019-06-10 04:42:07'),
('021827', 'ID', 'Kec. Sukagumiwang', '2019-06-10 04:42:07'),
('021828', 'ID', 'Kec. Kedokan Bunder', '2019-06-10 04:42:07'),
('021829', 'ID', 'Kec. Pasekan', '2019-06-10 04:42:07'),
('021830', 'ID', 'Kec. Tukdana', '2019-06-10 04:42:07'),
('021831', 'ID', 'Kec. Patrol', '2019-06-10 04:42:07'),
('021900', 'ID', 'Kab. Subang', '2019-06-10 04:42:07'),
('021901', 'ID', 'Kec. Sagalaherang', '2019-06-10 04:42:07'),
('021902', 'ID', 'Kec. Jalancagak', '2019-06-10 04:42:07'),
('021903', 'ID', 'Kec. Cisalak', '2019-06-10 04:42:07'),
('021904', 'ID', 'Kec. Tanjung Siang', '2019-06-10 04:42:07'),
('021905', 'ID', 'Kec. Cijambe', '2019-06-10 04:42:07'),
('021906', 'ID', 'Kec. Cibogo', '2019-06-10 04:42:07'),
('021907', 'ID', 'Kec. Subang', '2019-06-10 04:42:07'),
('021908', 'ID', 'Kec. Kalijati', '2019-06-10 04:42:07'),
('021909', 'ID', 'Kec. Cipeundeuy', '2019-06-10 04:42:07'),
('021910', 'ID', 'Kec. Pabuaran', '2019-06-10 04:42:07'),
('021911', 'ID', 'Kec. Patokbeusi', '2019-06-10 04:42:07'),
('021912', 'ID', 'Kec. Purwadadi', '2019-06-10 04:42:07'),
('021913', 'ID', 'Kec. Cikaum', '2019-06-10 04:42:07'),
('021914', 'ID', 'Kec. Pagaden', '2019-06-10 04:42:07'),
('021915', 'ID', 'Kec. Cipunagara', '2019-06-10 04:42:07'),
('021916', 'ID', 'Kec. Compreng', '2019-06-10 04:42:07'),
('021917', 'ID', 'Kec. Binong', '2019-06-10 04:42:07'),
('021918', 'ID', 'Kec. Ciasem', '2019-06-10 04:42:07'),
('021919', 'ID', 'Kec. Pamanukan', '2019-06-10 04:42:07'),
('021920', 'ID', 'Kec. Pusakanagara', '2019-06-10 04:42:07'),
('021921', 'ID', 'Kec. Legon Kulon', '2019-06-10 04:42:07'),
('021922', 'ID', 'Kec. Blanakan', '2019-06-10 04:42:07'),
('021923', 'ID', 'Kec. Dawuan', '2019-06-10 04:42:07'),
('021924', 'ID', 'Kec. Serang Panjang', '2019-06-10 04:42:07'),
('021925', 'ID', 'Kec. Kasomalang', '2019-06-10 04:42:07'),
('021926', 'ID', 'Kec. Tambakdahan', '2019-06-10 04:42:07'),
('021927', 'ID', 'Kec. Pagaden Barat', '2019-06-10 04:42:07'),
('021928', 'ID', 'Kec. Pusakajaya', '2019-06-10 04:42:07'),
('021929', 'ID', 'Kec. Ciater', '2019-06-10 04:42:07'),
('021930', 'ID', 'Kec. Sukasari', '2019-06-10 04:42:07'),
('022000', 'ID', 'Kab. Purwakarta', '2019-06-10 04:42:07'),
('022001', 'ID', 'Kec. Jatiluhur', '2019-06-10 04:42:07'),
('022002', 'ID', 'Kec. Maniis', '2019-06-10 04:42:07'),
('022003', 'ID', 'Kec. Tegalwaru', '2019-06-10 04:42:07'),
('022004', 'ID', 'Kec. Plered', '2019-06-10 04:42:07'),
('022005', 'ID', 'Kec. Sukatani', '2019-06-10 04:42:07'),
('022006', 'ID', 'Kec. Darangdan', '2019-06-10 04:42:07'),
('022007', 'ID', 'Kec. Bojong', '2019-06-10 04:42:07'),
('022008', 'ID', 'Kec. Wanayasa', '2019-06-10 04:42:07'),
('022009', 'ID', 'Kec. Pasawahan', '2019-06-10 04:42:07'),
('022010', 'ID', 'Kec. Purwakarta', '2019-06-10 04:42:07'),
('022011', 'ID', 'Kec. Campaka', '2019-06-10 04:42:07'),
('022012', 'ID', 'Kec. Sukasari', '2019-06-10 04:42:07'),
('022013', 'ID', 'Kec. Kiarapedes', '2019-06-10 04:42:07'),
('022014', 'ID', 'Kec. Babakancikao', '2019-06-10 04:42:07'),
('022015', 'ID', 'Kec. Cibatu', '2019-06-10 04:42:07'),
('022016', 'ID', 'Kec. Bungursari', '2019-06-10 04:42:07'),
('022017', 'ID', 'Kec. Pondok Salam', '2019-06-10 04:42:07'),
('022100', 'ID', 'Kab. Karawang', '2019-06-10 04:42:07'),
('022101', 'ID', 'Kec. Pangkalan', '2019-06-10 04:42:07'),
('022102', 'ID', 'Kec. Ciampel', '2019-06-10 04:42:07'),
('022104', 'ID', 'Kec. Klari', '2019-06-10 04:42:07'),
('022105', 'ID', 'Kec. Cikampek', '2019-06-10 04:42:07'),
('022106', 'ID', 'Kec. Tirtamulya', '2019-06-10 04:42:07'),
('022107', 'ID', 'Kec. Jatisari', '2019-06-10 04:42:07'),
('022109', 'ID', 'Kec. Lemahabang', '2019-06-10 04:42:07'),
('022110', 'ID', 'Kec. Telagasari', '2019-06-10 04:42:07'),
('022111', 'ID', 'Kec. Karawang', '2019-06-10 04:42:07'),
('022112', 'ID', 'Kec. Rawamerta', '2019-06-10 04:42:07'),
('022113', 'ID', 'Kec. Tempuran', '2019-06-10 04:42:07'),
('022114', 'ID', 'Kec. Kutawaluya', '2019-06-10 04:42:07'),
('022115', 'ID', 'Kec. Rengasdengklok', '2019-06-10 04:42:07'),
('022116', 'ID', 'Kec. Pedes', '2019-06-10 04:42:07'),
('022117', 'ID', 'Kec. Cibuaya', '2019-06-10 04:42:07'),
('022118', 'ID', 'Kec. Tirtajaya', '2019-06-10 04:42:07'),
('022119', 'ID', 'Kec. Batujaya', '2019-06-10 04:42:07'),
('022120', 'ID', 'Kec. Pakisjaya', '2019-06-10 04:42:07'),
('022121', 'ID', 'Kec. Majalaya', '2019-06-10 04:42:07'),
('022122', 'ID', 'Kec. Jayakerta', '2019-06-10 04:42:07'),
('022123', 'ID', 'Kec. Cilamaya Kulon', '2019-06-10 04:42:07'),
('022124', 'ID', 'Kec. Banyusari', '2019-06-10 04:42:07'),
('022125', 'ID', 'Kec. Kotabaru', '2019-06-10 04:42:07'),
('022126', 'ID', 'Kec. Cilamaya Wetan', '2019-06-10 04:42:07'),
('022127', 'ID', 'Kec. Purwasari', '2019-06-10 04:42:07'),
('022128', 'ID', 'Kec. Teluk Jambe Barat', '2019-06-10 04:42:07'),
('022129', 'ID', 'Kec. Teluk Jambe Timur', '2019-06-10 04:42:07'),
('022130', 'ID', 'Kec. Karawang Timur', '2019-06-10 04:42:07'),
('022131', 'ID', 'Kec. Tegalwaru', '2019-06-10 04:42:07'),
('022132', 'ID', 'Kec. Cilebar', '2019-06-10 04:42:07'),
('022133', 'ID', 'Kec. Karawang Barat', '2019-06-10 04:42:07'),
('022200', 'ID', 'Kab. Bekasi', '2019-06-10 04:42:07'),
('022201', 'ID', 'Kec. Setu', '2019-06-10 04:42:07'),
('022203', 'ID', 'Kec. Cibarusah', '2019-06-10 04:42:07'),
('022205', 'ID', 'Kec. Kedung Waringin', '2019-06-10 04:42:07'),
('022207', 'ID', 'Kec. Cibitung', '2019-06-10 04:42:07'),
('022209', 'ID', 'Kec. Babelan', '2019-06-10 04:42:07'),
('022210', 'ID', 'Kec. Taruma Jaya', '2019-06-10 04:42:07'),
('022211', 'ID', 'Kec. Tembelang', '2019-06-10 04:42:07'),
('022212', 'ID', 'Kec. Sukatani', '2019-06-10 04:42:07'),
('022213', 'ID', 'Kec. Pebayuran', '2019-06-10 04:42:07'),
('022214', 'ID', 'Kec. Cabangbungin', '2019-06-10 04:42:07'),
('022215', 'ID', 'Kec. Muara Gembong', '2019-06-10 04:42:07'),
('022216', 'ID', 'Kec. Tambun Selatan', '2019-06-10 04:42:07'),
('022217', 'ID', 'Kec. Tambun Utara', '2019-06-10 04:42:07'),
('022218', 'ID', 'Kec. Cikarang Barat', '2019-06-10 04:42:07'),
('022219', 'ID', 'Kec. Karang Bahagia', '2019-06-10 04:42:07'),
('022220', 'ID', 'Kec. Cikarang Utara', '2019-06-10 04:42:07'),
('022221', 'ID', 'Kec. Cikarang Selatan', '2019-06-10 04:42:07'),
('022222', 'ID', 'Kec. Cikarang Timur', '2019-06-10 04:42:07'),
('022223', 'ID', 'Kec. Bojong Mangu', '2019-06-10 04:42:07'),
('022224', 'ID', 'Kec. Cikarang Pusat', '2019-06-10 04:42:07'),
('022225', 'ID', 'Kec. Sukakarya', '2019-06-10 04:42:07'),
('022226', 'ID', 'Kec. Sukawangi', '2019-06-10 04:42:07'),
('022227', 'ID', 'Kec. Serang Baru', '2019-06-10 04:42:07'),
('022228', 'ID', 'Kec. Tarumajaya', '2020-02-10 03:07:43'),
('022300', 'ID', 'Kab. Bandung Barat', '2019-06-10 04:42:07'),
('022301', 'ID', 'Kec. Rongga', '2019-06-10 04:42:07'),
('022302', 'ID', 'Kec. Gununghalu', '2019-06-10 04:42:07'),
('022303', 'ID', 'Kec. Sindangkerta', '2019-06-10 04:42:07'),
('022304', 'ID', 'Kec. Cililin', '2019-06-10 04:42:07'),
('022305', 'ID', 'Kec. Cihampelas', '2019-06-10 04:42:07'),
('022306', 'ID', 'Kec. Cipongkor', '2019-06-10 04:42:07'),
('022307', 'ID', 'Kec. Batujajar', '2019-06-10 04:42:07'),
('022308', 'ID', 'Kec. Cipatat', '2019-06-10 04:42:07'),
('022309', 'ID', 'Kec. Padalarang', '2019-06-10 04:42:07'),
('022310', 'ID', 'Kec. Ngamprah', '2019-06-10 04:42:07'),
('022311', 'ID', 'Kec. Parongpong', '2019-06-10 04:42:07'),
('022312', 'ID', 'Kec. Lembang', '2019-06-10 04:42:07'),
('022313', 'ID', 'Kec. Cisarua', '2019-06-10 04:42:07'),
('022314', 'ID', 'Kec. Cikalong Wetan', '2019-06-10 04:42:07'),
('022315', 'ID', 'Kec. Cipeundeuy', '2019-06-10 04:42:07'),
('022316', 'ID', 'Kec. Saguling', '2020-02-10 03:07:43'),
('022500', 'ID', 'Kab. Pangandaran', '2020-02-10 03:07:43'),
('022551', 'ID', 'Kec. Cijulang', '2020-02-10 03:07:43'),
('022552', 'ID', 'Kec. Cimerak', '2020-02-10 03:07:43'),
('022553', 'ID', 'Kec. Cigugur', '2020-02-10 03:07:43'),
('022554', 'ID', 'Kec. Langkaplancar', '2020-02-10 03:07:43'),
('022555', 'ID', 'Kec. Mangunjaya', '2020-02-10 03:07:43'),
('022556', 'ID', 'Kec. Padaherang', '2020-02-10 03:07:43'),
('022557', 'ID', 'Kec. Kalipucang', '2020-02-10 03:07:43'),
('022558', 'ID', 'Kec. Pangandaran', '2020-02-10 03:07:43'),
('022559', 'ID', 'Kec. Sidamulih', '2020-02-10 03:07:43'),
('022560', 'ID', 'Kec. Parigi', '2020-02-10 03:07:43'),
('026000', 'ID', 'Kota Bandung', '2019-06-10 04:42:07'),
('026001', 'ID', 'Kec. Bandung Kulon', '2019-06-10 04:42:07'),
('026002', 'ID', 'Kec. Babakan Ciparay', '2019-06-10 04:42:07'),
('026003', 'ID', 'Kec. Bojong Loa Kaler', '2019-06-10 04:42:07'),
('026004', 'ID', 'Kec. Bojong Loa Kidul', '2019-06-10 04:42:07'),
('026005', 'ID', 'Kec. Astananyar', '2019-06-10 04:42:07'),
('026006', 'ID', 'Kec. Regol', '2019-06-10 04:42:07'),
('026007', 'ID', 'Kec. Lengkong', '2019-06-10 04:42:07'),
('026008', 'ID', 'Kec. Bandung Kidul', '2019-06-10 04:42:07'),
('026009', 'ID', 'Kec. Buah Batu', '2019-06-10 04:42:07'),
('026010', 'ID', 'Kec. Rancasari', '2019-06-10 04:42:07'),
('026011', 'ID', 'Kec. Cibiru', '2019-06-10 04:42:07'),
('026012', 'ID', 'Kec. Ujungberung', '2019-06-10 04:42:07'),
('026013', 'ID', 'Kec. Arcamanik', '2019-06-10 04:42:07'),
('026015', 'ID', 'Kec. Kiaracondong', '2019-06-10 04:42:07'),
('026016', 'ID', 'Kec. Batununggal', '2019-06-10 04:42:07'),
('026017', 'ID', 'Kec. Sumur Bandung', '2019-06-10 04:42:07'),
('026018', 'ID', 'Kec. Andir', '2019-06-10 04:42:07'),
('026019', 'ID', 'Kec. Cicendo', '2019-06-10 04:42:07'),
('026020', 'ID', 'Kec. Bandung Wetan', '2019-06-10 04:42:07'),
('026021', 'ID', 'Kec. Cibeunying Kidul', '2019-06-10 04:42:07'),
('026022', 'ID', 'Kec. Cibeunying Kaler', '2019-06-10 04:42:07'),
('026023', 'ID', 'Kec. Coblong', '2019-06-10 04:42:07'),
('026024', 'ID', 'Kec. Sukajadi', '2019-06-10 04:42:07'),
('026025', 'ID', 'Kec. Sukasari', '2019-06-10 04:42:07'),
('026026', 'ID', 'Kec. Cidadap', '2019-06-10 04:42:07'),
('026027', 'ID', 'Kec. Gedebage', '2019-06-10 04:42:07'),
('026028', 'ID', 'Kec. Panyileukan', '2019-06-10 04:42:07'),
('026029', 'ID', 'Kec. Cinambo', '2019-06-10 04:42:07'),
('026030', 'ID', 'Kec. Mandalajati', '2019-06-10 04:42:07'),
('026031', 'ID', 'Kec. Antapani', '2019-06-10 04:42:07'),
('026100', 'ID', 'Kota Bogor', '2019-06-10 04:42:07'),
('026101', 'ID', 'Kec. Kota Bogor Selatan', '2019-06-10 04:42:07'),
('026102', 'ID', 'Kec. Kota Bogor Timur', '2019-06-10 04:42:07'),
('026103', 'ID', 'Kec. Kota Bogor Utara', '2019-06-10 04:42:07'),
('026104', 'ID', 'Kec. Kota Bogor Tengah', '2019-06-10 04:42:07'),
('026105', 'ID', 'Kec. Kota Bogor Barat', '2019-06-10 04:42:07'),
('026106', 'ID', 'Kec. Tanah Sereal', '2019-06-10 04:42:07'),
('026200', 'ID', 'Kota Sukabumi', '2019-06-10 04:42:07'),
('026201', 'ID', 'Kec. Baros', '2019-06-10 04:42:07'),
('026202', 'ID', 'Kec. Citamiang', '2019-06-10 04:42:07'),
('026203', 'ID', 'Kec. Warudoyong', '2019-06-10 04:42:07'),
('026204', 'ID', 'Kec. Gunung Puyuh', '2019-06-10 04:42:07'),
('026205', 'ID', 'Kec. Cikole', '2019-06-10 04:42:07'),
('026206', 'ID', 'Kec. Lembur Situ', '2019-06-10 04:42:07'),
('026207', 'ID', 'Kec. Cibeureum', '2019-06-10 04:42:07'),
('026300', 'ID', 'Kota Cirebon', '2019-06-10 04:42:07'),
('026301', 'ID', 'Kec. Harjamukti', '2019-06-10 04:42:07'),
('026302', 'ID', 'Kec. Lemahwungkuk', '2019-06-10 04:42:07'),
('026303', 'ID', 'Kec. Pekalipan', '2019-06-10 04:42:07'),
('026304', 'ID', 'Kec. Kesambi', '2019-06-10 04:42:07'),
('026305', 'ID', 'Kec. Kejaksan', '2019-06-10 04:42:07'),
('026500', 'ID', 'Kota Bekasi', '2019-06-10 04:42:07'),
('026501', 'ID', 'Kec. Pondokgede', '2019-06-10 04:42:07'),
('026502', 'ID', 'Kec. Jatiasih', '2019-06-10 04:42:07'),
('026503', 'ID', 'Kec. Bantargebang', '2019-06-10 04:42:07'),
('026504', 'ID', 'Kec. Bekasi Timur', '2019-06-10 04:42:07'),
('026505', 'ID', 'Kec. Bekasi Selatan', '2019-06-10 04:42:07'),
('026506', 'ID', 'Kec. Bekasi Barat', '2019-06-10 04:42:07'),
('026507', 'ID', 'Kec. Bekasi Utara', '2019-06-10 04:42:07'),
('026508', 'ID', 'Kec. Jati Sampurna', '2019-06-10 04:42:07'),
('026509', 'ID', 'Kec. Medan Satria', '2019-06-10 04:42:07'),
('026510', 'ID', 'Kec. Rawalumbu', '2019-06-10 04:42:07'),
('026511', 'ID', 'Kec. Mustika Jaya', '2019-06-10 04:42:07'),
('026512', 'ID', 'Kec. Pondok Melati', '2019-06-10 04:42:07'),
('026600', 'ID', 'Kota Depok', '2019-06-10 04:42:07'),
('026601', 'ID', 'Kec. Sawangan', '2019-06-10 04:42:07'),
('026602', 'ID', 'Kec. Pancoran Mas', '2019-06-10 04:42:07'),
('026603', 'ID', 'Kec. Sukmajaya', '2019-06-10 04:42:07'),
('026604', 'ID', 'Kec. Cimanggis', '2019-06-10 04:42:07'),
('026605', 'ID', 'Kec. Beji', '2019-06-10 04:42:07'),
('026606', 'ID', 'Kec. Limo', '2019-06-10 04:42:07'),
('026607', 'ID', 'Kec. Cipayung', '2019-06-10 04:42:07'),
('026608', 'ID', 'Kec. Cilodong', '2019-06-10 04:42:07'),
('026609', 'ID', 'Kec. Cinere', '2019-06-10 04:42:07'),
('026610', 'ID', 'Kec. Tapos', '2019-06-10 04:42:07'),
('026611', 'ID', 'Kec. Bojongsari', '2019-06-10 04:42:07'),
('026700', 'ID', 'Kota Cimahi', '2019-06-10 04:42:07'),
('026701', 'ID', 'Kec. Cimahi Selatan', '2019-06-10 04:42:07'),
('026702', 'ID', 'Kec. Cimahi Tengah', '2019-06-10 04:42:07'),
('026703', 'ID', 'Kec. Cimahi Utara', '2019-06-10 04:42:07'),
('026800', 'ID', 'Kota Tasikmalaya', '2019-06-10 04:42:07'),
('026801', 'ID', 'Kec. Cibeureum', '2019-06-10 04:42:07'),
('026802', 'ID', 'Kec. Tamansari', '2019-06-10 04:42:07'),
('026803', 'ID', 'Kec. Kawalu', '2019-06-10 04:42:07'),
('026804', 'ID', 'Kec. Mangkubumi', '2019-06-10 04:42:07'),
('026805', 'ID', 'Kec. Indihiang', '2019-06-10 04:42:07'),
('026806', 'ID', 'Kec. Cipedes', '2019-06-10 04:42:07'),
('026807', 'ID', 'Kec. Cihideung', '2019-06-10 04:42:07'),
('026808', 'ID', 'Kec. Tawang', '2019-06-10 04:42:07'),
('026809', 'ID', 'Kec. Purbaratu', '2019-06-10 04:42:07'),
('026810', 'ID', 'Kec. Bungursari', '2019-06-10 04:42:07'),
('026900', 'ID', 'Kota Banjar', '2019-06-10 04:42:07'),
('026901', 'ID', 'Kec. Banjar', '2019-06-10 04:42:07'),
('026902', 'ID', 'Kec. Purwaharja', '2019-06-10 04:42:07'),
('026903', 'ID', 'Kec. Pataruman', '2019-06-10 04:42:07'),
('026904', 'ID', 'Kec. Langensari', '2019-06-10 04:42:07'),
('030000', 'ID', 'Prop. Jawa Tengah', '2019-06-10 04:42:07'),
('030100', 'ID', 'Kab. Cilacap', '2019-06-10 04:42:07'),
('030101', 'ID', 'Kec. Dayeuhluhur', '2019-06-10 04:42:07'),
('030102', 'ID', 'Kec. Wanareja', '2019-06-10 04:42:07'),
('030103', 'ID', 'Kec. Majenang', '2019-06-10 04:42:07'),
('030104', 'ID', 'Kec. Cimanggu', '2019-06-10 04:42:07'),
('030105', 'ID', 'Kec. Karangpucung', '2019-06-10 04:42:07'),
('030106', 'ID', 'Kec. Cipari', '2019-06-10 04:42:07'),
('030107', 'ID', 'Kec. Sidareja', '2019-06-10 04:42:07'),
('030108', 'ID', 'Kec. Kedungreja', '2019-06-10 04:42:07'),
('030109', 'ID', 'Kec. Patimuan', '2019-06-10 04:42:07'),
('030110', 'ID', 'Kec. Gandrungmangu', '2019-06-10 04:42:07'),
('030111', 'ID', 'Kec. Bantarsari', '2019-06-10 04:42:07'),
('030112', 'ID', 'Kec. Kawunganten', '2019-06-10 04:42:07'),
('030113', 'ID', 'Kec. Kampung Laut', '2019-06-10 04:42:07'),
('030114', 'ID', 'Kec. Jeruklegi', '2019-06-10 04:42:07'),
('030115', 'ID', 'Kec. Kesugihan', '2019-06-10 04:42:07'),
('030116', 'ID', 'Kec. Adipala', '2019-06-10 04:42:07'),
('030117', 'ID', 'Kec. Maos', '2019-06-10 04:42:07'),
('030118', 'ID', 'Kec. Sampang', '2019-06-10 04:42:07'),
('030119', 'ID', 'Kec. Kroya', '2019-06-10 04:42:07'),
('030120', 'ID', 'Kec. Binangun', '2019-06-10 04:42:07'),
('030121', 'ID', 'Kec. Nusawungu', '2019-06-10 04:42:07'),
('030122', 'ID', 'Kec. Cilacap Selatan', '2019-06-10 04:42:07'),
('030123', 'ID', 'Kec. Cilacap Tengah', '2019-06-10 04:42:07'),
('030124', 'ID', 'Kec. Cilacap Utara', '2019-06-10 04:42:07'),
('030200', 'ID', 'Kab. Banyumas', '2019-06-10 04:42:07'),
('030201', 'ID', 'Kec. Lumbir', '2019-06-10 04:42:07'),
('030202', 'ID', 'Kec. Wangon', '2019-06-10 04:42:07'),
('030203', 'ID', 'Kec. Jatilawang', '2019-06-10 04:42:07'),
('030204', 'ID', 'Kec. Rawalo', '2019-06-10 04:42:07'),
('030205', 'ID', 'Kec. Kebasen', '2019-06-10 04:42:07'),
('030206', 'ID', 'Kec. Kemranjen', '2019-06-10 04:42:07'),
('030207', 'ID', 'Kec. Sumpiuh', '2019-06-10 04:42:07'),
('030208', 'ID', 'Kec. Tambak', '2019-06-10 04:42:07'),
('030209', 'ID', 'Kec. Somagede', '2019-06-10 04:42:07'),
('030210', 'ID', 'Kec. Kalibagor', '2019-06-10 04:42:07'),
('030211', 'ID', 'Kec. Banyumas', '2019-06-10 04:42:07'),
('030212', 'ID', 'Kec. Patikraja', '2019-06-10 04:42:07'),
('030213', 'ID', 'Kec. Purwojati', '2019-06-10 04:42:07'),
('030214', 'ID', 'Kec. Ajibarang', '2019-06-10 04:42:07'),
('030215', 'ID', 'Kec. Gumelar', '2019-06-10 04:42:07'),
('030216', 'ID', 'Kec. Pekuncen', '2019-06-10 04:42:07'),
('030217', 'ID', 'Kec. Cilongok', '2019-06-10 04:42:07'),
('030218', 'ID', 'Kec. Karanglewas', '2019-06-10 04:42:07'),
('030219', 'ID', 'Kec. Kedung Banteng', '2019-06-10 04:42:07'),
('030220', 'ID', 'Kec. Baturaden', '2019-06-10 04:42:07'),
('030221', 'ID', 'Kec. Sumbang', '2019-06-10 04:42:07'),
('030222', 'ID', 'Kec. Kembaran', '2019-06-10 04:42:07'),
('030223', 'ID', 'Kec. Sokaraja', '2019-06-10 04:42:07'),
('030224', 'ID', 'Kec. Purwokerto Selatan', '2019-06-10 04:42:07'),
('030225', 'ID', 'Kec. Purwokerto Barat', '2019-06-10 04:42:07'),
('030226', 'ID', 'Kec. Purwokerto Timur', '2019-06-10 04:42:07'),
('030227', 'ID', 'Kec. Purwokerto Utara', '2019-06-10 04:42:07'),
('030300', 'ID', 'Kab. Purbalingga', '2019-06-10 04:42:07'),
('030301', 'ID', 'Kec. Kemangkon', '2019-06-10 04:42:07'),
('030302', 'ID', 'Kec. Bukateja', '2019-06-10 04:42:07'),
('030303', 'ID', 'Kec. Kejobong', '2019-06-10 04:42:07'),
('030304', 'ID', 'Kec. Pengadegan', '2019-06-10 04:42:07'),
('030305', 'ID', 'Kec. Kaligondang', '2019-06-10 04:42:07'),
('030306', 'ID', 'Kec. Purbalingga', '2019-06-10 04:42:07'),
('030307', 'ID', 'Kec. Kalimanah', '2019-06-10 04:42:07'),
('030308', 'ID', 'Kec. Padamara', '2019-06-10 04:42:07'),
('030309', 'ID', 'Kec. Kutasari', '2019-06-10 04:42:07'),
('030310', 'ID', 'Kec. Bojongsari', '2019-06-10 04:42:07'),
('030311', 'ID', 'Kec. Mrebet', '2019-06-10 04:42:07'),
('030312', 'ID', 'Kec. Bobotsari', '2019-06-10 04:42:07'),
('030313', 'ID', 'Kec. Karangreja', '2019-06-10 04:42:07'),
('030314', 'ID', 'Kec. Karanganyar', '2019-06-10 04:42:07'),
('030315', 'ID', 'Kec. Karangmoncol', '2019-06-10 04:42:07'),
('030316', 'ID', 'Kec. Rembang', '2019-06-10 04:42:07'),
('030317', 'ID', 'Kec. Karangjambu', '2019-06-10 04:42:07'),
('030318', 'ID', 'Kec. Kertanegara', '2019-06-10 04:42:07'),
('030400', 'ID', 'Kab. Banjarnegara', '2019-06-10 04:42:07'),
('030401', 'ID', 'Kec. Susukan', '2019-06-10 04:42:07'),
('030402', 'ID', 'Kec. Purworejo/ Klampok', '2019-06-10 04:42:07'),
('030403', 'ID', 'Kec. Mandiraja', '2019-06-10 04:42:07'),
('030404', 'ID', 'Kec. Purwonegara', '2019-06-10 04:42:07'),
('030405', 'ID', 'Kec. Bawang', '2019-06-10 04:42:07'),
('030406', 'ID', 'Kec. Banjarnegara', '2019-06-10 04:42:07'),
('030407', 'ID', 'Kec. Sigaluh', '2019-06-10 04:42:07'),
('030408', 'ID', 'Kec. Madukara', '2019-06-10 04:42:07'),
('030409', 'ID', 'Kec. Banjarmangu', '2019-06-10 04:42:07'),
('030410', 'ID', 'Kec. Wanadadi', '2019-06-10 04:42:07'),
('030411', 'ID', 'Kec. Rakit', '2019-06-10 04:42:07'),
('030412', 'ID', 'Kec. Punggelan', '2019-06-10 04:42:07'),
('030413', 'ID', 'Kec. Karangkobar', '2019-06-10 04:42:07'),
('030414', 'ID', 'Kec. Pagentan', '2019-06-10 04:42:07'),
('030415', 'ID', 'Kec. Pejawaran', '2019-06-10 04:42:07'),
('030416', 'ID', 'Kec. Batur', '2019-06-10 04:42:07'),
('030417', 'ID', 'Kec. Wanayasa', '2019-06-10 04:42:07'),
('030418', 'ID', 'Kec. Kalibening', '2019-06-10 04:42:07'),
('030419', 'ID', 'Kec. Pandan Arum', '2019-06-10 04:42:07'),
('030420', 'ID', 'Kec. Pagedongan', '2019-06-10 04:42:07'),
('030500', 'ID', 'Kab. Kebumen', '2019-06-10 04:42:07'),
('030501', 'ID', 'Kec. Ayah', '2019-06-10 04:42:07'),
('030502', 'ID', 'Kec. Buayan', '2019-06-10 04:42:07'),
('030503', 'ID', 'Kec. Puring', '2019-06-10 04:42:07'),
('030504', 'ID', 'Kec. Petanahan', '2019-06-10 04:42:07'),
('030505', 'ID', 'Kec. Klirong', '2019-06-10 04:42:07'),
('030506', 'ID', 'Kec. Bulupesantren', '2019-06-10 04:42:07'),
('030507', 'ID', 'Kec. Ambal', '2019-06-10 04:42:07'),
('030508', 'ID', 'Kec. Mirit', '2019-06-10 04:42:07'),
('030509', 'ID', 'Kec. Prembun', '2019-06-10 04:42:07'),
('030510', 'ID', 'Kec. Kutowinangun', '2019-06-10 04:42:07'),
('030511', 'ID', 'Kec. Alian', '2019-06-10 04:42:07'),
('030512', 'ID', 'Kec. Kebumen', '2019-06-10 04:42:07'),
('030513', 'ID', 'Kec. Pejagoan', '2019-06-10 04:42:07'),
('030514', 'ID', 'Kec. Sruweng', '2019-06-10 04:42:07'),
('030515', 'ID', 'Kec. Adimulyo', '2019-06-10 04:42:07'),
('030516', 'ID', 'Kec. Kuwarasan', '2019-06-10 04:42:07'),
('030517', 'ID', 'Kec. Rowokele', '2019-06-10 04:42:07'),
('030518', 'ID', 'Kec. Sempor', '2019-06-10 04:42:07'),
('030519', 'ID', 'Kec. Gombong', '2019-06-10 04:42:07'),
('030520', 'ID', 'Kec. Karanganyar', '2019-06-10 04:42:07'),
('030521', 'ID', 'Kec. Karangganyam', '2019-06-10 04:42:07'),
('030522', 'ID', 'Kec. Sadang', '2019-06-10 04:42:07'),
('030523', 'ID', 'Kec. Bonorowo', '2019-06-10 04:42:07'),
('030524', 'ID', 'Kec. Padureso', '2019-06-10 04:42:07'),
('030525', 'ID', 'Kec. Poncowarno', '2019-06-10 04:42:07'),
('030526', 'ID', 'Kec. Karangsambung', '2019-06-10 04:42:07'),
('030600', 'ID', 'Kab. Purworejo', '2019-06-10 04:42:07'),
('030601', 'ID', 'Kec. Grabag', '2019-06-10 04:42:07'),
('030602', 'ID', 'Kec. Ngombol', '2019-06-10 04:42:07'),
('030603', 'ID', 'Kec. Purwodadi', '2019-06-10 04:42:07'),
('030604', 'ID', 'Kec. Bagelen', '2019-06-10 04:42:07'),
('030605', 'ID', 'Kec. Kaligesing', '2019-06-10 04:42:07'),
('030606', 'ID', 'Kec. Purworejo', '2019-06-10 04:42:07'),
('030607', 'ID', 'Kec. Banyu Urip', '2019-06-10 04:42:07'),
('030608', 'ID', 'Kec. Bayan', '2019-06-10 04:42:07'),
('030609', 'ID', 'Kec. Kutoarjo', '2019-06-10 04:42:07'),
('030610', 'ID', 'Kec. Butuh', '2019-06-10 04:42:07'),
('030611', 'ID', 'Kec. Pituruh', '2019-06-10 04:42:07'),
('030612', 'ID', 'Kec. Kemiri', '2019-06-10 04:42:07'),
('030613', 'ID', 'Kec. Bruno', '2019-06-10 04:42:07'),
('030614', 'ID', 'Kec. Gebang', '2019-06-10 04:42:07'),
('030615', 'ID', 'Kec. Loano', '2019-06-10 04:42:07'),
('030616', 'ID', 'Kec. Bener', '2019-06-10 04:42:07'),
('030700', 'ID', 'Kab. Wonosobo', '2019-06-10 04:42:07'),
('030701', 'ID', 'Kec. Wadaslintang', '2019-06-10 04:42:07'),
('030702', 'ID', 'Kec. Kepil', '2019-06-10 04:42:07'),
('030703', 'ID', 'Kec. Sapuran', '2019-06-10 04:42:07'),
('030704', 'ID', 'Kec. Kaliwiro', '2019-06-10 04:42:07'),
('030705', 'ID', 'Kec. Leksono', '2019-06-10 04:42:07'),
('030706', 'ID', 'Kec. Selomerto', '2019-06-10 04:42:07');
INSERT INTO `dikti_wilayah` (`id_wilayah`, `id_negara`, `nama_wilayah`, `dikti_wilayah_timestamp`) VALUES
('030707', 'ID', 'Kec. Kalikajar', '2019-06-10 04:42:07'),
('030708', 'ID', 'Kec. Kertek', '2019-06-10 04:42:07'),
('030709', 'ID', 'Kec. Wonosobo', '2019-06-10 04:42:07'),
('030710', 'ID', 'Kec. Watumalang', '2019-06-10 04:42:07'),
('030711', 'ID', 'Kec. Mojotengah', '2019-06-10 04:42:07'),
('030712', 'ID', 'Kec. Garung', '2019-06-10 04:42:07'),
('030713', 'ID', 'Kec. Kejajar', '2019-06-10 04:42:07'),
('030714', 'ID', 'Kec. Sukoharjo', '2019-06-10 04:42:07'),
('030715', 'ID', 'Kec. Kalibawang', '2019-06-10 04:42:07'),
('030800', 'ID', 'Kab. Magelang', '2019-06-10 04:42:07'),
('030801', 'ID', 'Kec. Salaman', '2019-06-10 04:42:07'),
('030802', 'ID', 'Kec. Borobudur', '2019-06-10 04:42:07'),
('030803', 'ID', 'Kec. Ngluwar', '2019-06-10 04:42:07'),
('030804', 'ID', 'Kec. Salam', '2019-06-10 04:42:07'),
('030805', 'ID', 'Kec. Srumbung', '2019-06-10 04:42:07'),
('030806', 'ID', 'Kec. Dukun', '2019-06-10 04:42:07'),
('030807', 'ID', 'Kec. Muntilan', '2019-06-10 04:42:07'),
('030808', 'ID', 'Kec. Mungkid', '2019-06-10 04:42:07'),
('030809', 'ID', 'Kec. Sawangan', '2019-06-10 04:42:07'),
('030810', 'ID', 'Kec. Candimulyo', '2019-06-10 04:42:07'),
('030811', 'ID', 'Kec. Martoyudan', '2019-06-10 04:42:07'),
('030812', 'ID', 'Kec. Tempuran', '2019-06-10 04:42:07'),
('030813', 'ID', 'Kec. Kajoran', '2019-06-10 04:42:07'),
('030814', 'ID', 'Kec. Kaliangkrik', '2019-06-10 04:42:07'),
('030815', 'ID', 'Kec. Bandongan', '2019-06-10 04:42:07'),
('030816', 'ID', 'Kec. Windusari', '2019-06-10 04:42:07'),
('030817', 'ID', 'Kec. Secang', '2019-06-10 04:42:07'),
('030818', 'ID', 'Kec. Tegalrejo', '2019-06-10 04:42:07'),
('030819', 'ID', 'Kec. Pakis', '2019-06-10 04:42:07'),
('030820', 'ID', 'Kec. Grabag', '2019-06-10 04:42:07'),
('030821', 'ID', 'Kec. Ngablak', '2019-06-10 04:42:07'),
('030900', 'ID', 'Kab. Boyolali', '2019-06-10 04:42:07'),
('030901', 'ID', 'Kec. Selo', '2019-06-10 04:42:07'),
('030902', 'ID', 'Kec. Ampel', '2019-06-10 04:42:07'),
('030903', 'ID', 'Kec. Cepogo', '2019-06-10 04:42:07'),
('030904', 'ID', 'Kec. Musuk', '2019-06-10 04:42:07'),
('030905', 'ID', 'Kec. Boyolali', '2019-06-10 04:42:07'),
('030906', 'ID', 'Kec. Mojosongo', '2019-06-10 04:42:07'),
('030907', 'ID', 'Kec. Teras', '2019-06-10 04:42:07'),
('030908', 'ID', 'Kec. Sawit', '2019-06-10 04:42:07'),
('030909', 'ID', 'Kec. Banyudono', '2019-06-10 04:42:07'),
('030910', 'ID', 'Kec. Sambi', '2019-06-10 04:42:07'),
('030911', 'ID', 'Kec. Ngemplak', '2019-06-10 04:42:07'),
('030912', 'ID', 'Kec. Nogosari', '2019-06-10 04:42:07'),
('030913', 'ID', 'Kec. Simo', '2019-06-10 04:42:07'),
('030914', 'ID', 'Kec. Karanggede', '2019-06-10 04:42:07'),
('030915', 'ID', 'Kec. Klego', '2019-06-10 04:42:07'),
('030916', 'ID', 'Kec. Andong', '2019-06-10 04:42:07'),
('030917', 'ID', 'Kec. Kemusu', '2019-06-10 04:42:07'),
('030918', 'ID', 'Kec. Wonosegoro', '2019-06-10 04:42:07'),
('030919', 'ID', 'Kec. Juwangi', '2019-06-10 04:42:07'),
('031000', 'ID', 'Kab. Klaten', '2019-06-10 04:42:07'),
('031001', 'ID', 'Kec. Prambanan', '2019-06-10 04:42:07'),
('031002', 'ID', 'Kec. Gantiwarno', '2019-06-10 04:42:07'),
('031003', 'ID', 'Kec. Wedi', '2019-06-10 04:42:07'),
('031004', 'ID', 'Kec. Bayat', '2019-06-10 04:42:07'),
('031005', 'ID', 'Kec. Cawas', '2019-06-10 04:42:07'),
('031006', 'ID', 'Kec. Trucuk', '2019-06-10 04:42:07'),
('031007', 'ID', 'Kec. Kalikotes', '2019-06-10 04:42:07'),
('031008', 'ID', 'Kec. Kebonarum', '2019-06-10 04:42:07'),
('031009', 'ID', 'Kec. Jogonalan', '2019-06-10 04:42:07'),
('031010', 'ID', 'Kec. Manisrenggo', '2019-06-10 04:42:07'),
('031011', 'ID', 'Kec. Karangnongko', '2019-06-10 04:42:07'),
('031012', 'ID', 'Kec. Ngawen', '2019-06-10 04:42:07'),
('031013', 'ID', 'Kec. Ceper', '2019-06-10 04:42:07'),
('031014', 'ID', 'Kec. Pedan', '2019-06-10 04:42:07'),
('031015', 'ID', 'Kec. Karangdowo', '2019-06-10 04:42:07'),
('031016', 'ID', 'Kec. Juwiring', '2019-06-10 04:42:07'),
('031017', 'ID', 'Kec. Wonosari', '2019-06-10 04:42:07'),
('031018', 'ID', 'Kec. Delanggu', '2019-06-10 04:42:07'),
('031019', 'ID', 'Kec. Polanharjo', '2019-06-10 04:42:07'),
('031020', 'ID', 'Kec. Karanganom', '2019-06-10 04:42:07'),
('031021', 'ID', 'Kec. Tulung', '2019-06-10 04:42:07'),
('031022', 'ID', 'Kec. Jatinom', '2019-06-10 04:42:07'),
('031023', 'ID', 'Kec. Kemalang', '2019-06-10 04:42:07'),
('031024', 'ID', 'Kec. Klaten Selatan', '2019-06-10 04:42:07'),
('031025', 'ID', 'Kec. Klaten Tengah', '2019-06-10 04:42:07'),
('031026', 'ID', 'Kec. Klaten Utara', '2019-06-10 04:42:07'),
('031100', 'ID', 'Kab. Sukoharjo', '2019-06-10 04:42:07'),
('031101', 'ID', 'Kec. Weru', '2019-06-10 04:42:07'),
('031102', 'ID', 'Kec. Bulu', '2019-06-10 04:42:07'),
('031103', 'ID', 'Kec. Tawangsari', '2019-06-10 04:42:07'),
('031104', 'ID', 'Kec. Sukoharjo', '2019-06-10 04:42:07'),
('031105', 'ID', 'Kec. Nguter', '2019-06-10 04:42:07'),
('031106', 'ID', 'Kec. Bendosari', '2019-06-10 04:42:07'),
('031107', 'ID', 'Kec. Polokarto', '2019-06-10 04:42:07'),
('031108', 'ID', 'Kec. Mojolaban', '2019-06-10 04:42:07'),
('031109', 'ID', 'Kec. Grogol', '2019-06-10 04:42:07'),
('031110', 'ID', 'Kec. Baki', '2019-06-10 04:42:07'),
('031111', 'ID', 'Kec. Gatak', '2019-06-10 04:42:07'),
('031112', 'ID', 'Kec. Kartasura', '2019-06-10 04:42:07'),
('031200', 'ID', 'Kab. Wonogiri', '2019-06-10 04:42:07'),
('031201', 'ID', 'Kec. Pracimantoro', '2019-06-10 04:42:07'),
('031202', 'ID', 'Kec. Paranggupito', '2019-06-10 04:42:07'),
('031203', 'ID', 'Kec. Giritontro', '2019-06-10 04:42:07'),
('031204', 'ID', 'Kec. Giriwoyo', '2019-06-10 04:42:07'),
('031205', 'ID', 'Kec. Batuwarno', '2019-06-10 04:42:07'),
('031206', 'ID', 'Kec. Karangtengah', '2019-06-10 04:42:07'),
('031207', 'ID', 'Kec. Tirtomoyo', '2019-06-10 04:42:07'),
('031208', 'ID', 'Kec. Nguntoronadi', '2019-06-10 04:42:07'),
('031209', 'ID', 'Kec. Baturetno', '2019-06-10 04:42:07'),
('031210', 'ID', 'Kec. Eromoko', '2019-06-10 04:42:07'),
('031211', 'ID', 'Kec. Wuryantoro', '2019-06-10 04:42:07'),
('031212', 'ID', 'Kec. Manyaran', '2019-06-10 04:42:07'),
('031213', 'ID', 'Kec. Selogiri', '2019-06-10 04:42:07'),
('031214', 'ID', 'Kec. Wonogiri', '2019-06-10 04:42:07'),
('031215', 'ID', 'Kec. Ngadirojo', '2019-06-10 04:42:07'),
('031216', 'ID', 'Kec. Sidoharjo', '2019-06-10 04:42:07'),
('031217', 'ID', 'Kec. Jatiroto', '2019-06-10 04:42:07'),
('031218', 'ID', 'Kec. Kismantoro', '2019-06-10 04:42:07'),
('031219', 'ID', 'Kec. Purwantoro', '2019-06-10 04:42:07'),
('031220', 'ID', 'Kec. Bulukerto', '2019-06-10 04:42:07'),
('031221', 'ID', 'Kec. Slogohimo', '2019-06-10 04:42:07'),
('031222', 'ID', 'Kec. Jatisrono', '2019-06-10 04:42:07'),
('031223', 'ID', 'Kec. Jatipurno', '2019-06-10 04:42:07'),
('031224', 'ID', 'Kec. Girimarto', '2019-06-10 04:42:07'),
('031225', 'ID', 'Kec. Puhpelem', '2019-06-10 04:42:07'),
('031300', 'ID', 'Kab. Karanganyar', '2019-06-10 04:42:07'),
('031301', 'ID', 'Kec. Jatipuro', '2019-06-10 04:42:07'),
('031302', 'ID', 'Kec. Jatiyoso', '2019-06-10 04:42:07'),
('031303', 'ID', 'Kec. Jumapolo', '2019-06-10 04:42:07'),
('031304', 'ID', 'Kec. Jumantono', '2019-06-10 04:42:07'),
('031305', 'ID', 'Kec. Matesih', '2019-06-10 04:42:07'),
('031306', 'ID', 'Kec. Tawangmangu', '2019-06-10 04:42:07'),
('031307', 'ID', 'Kec. Ngargoyoso', '2019-06-10 04:42:07'),
('031308', 'ID', 'Kec. Karangpandan', '2019-06-10 04:42:07'),
('031309', 'ID', 'Kec. Karanganyar', '2019-06-10 04:42:07'),
('031310', 'ID', 'Kec. Tasikmadu', '2019-06-10 04:42:07'),
('031311', 'ID', 'Kec. Jaten', '2019-06-10 04:42:07'),
('031312', 'ID', 'Kec. Colomadu', '2019-06-10 04:42:07'),
('031313', 'ID', 'Kec. Gondangrejo', '2019-06-10 04:42:07'),
('031314', 'ID', 'Kec. Kebakkramat', '2019-06-10 04:42:07'),
('031315', 'ID', 'Kec. Mojogedang', '2019-06-10 04:42:07'),
('031316', 'ID', 'Kec. Kerjo', '2019-06-10 04:42:07'),
('031317', 'ID', 'Kec. Jenawi', '2019-06-10 04:42:07'),
('031400', 'ID', 'Kab. Sragen', '2019-06-10 04:42:07'),
('031401', 'ID', 'Kec. Kalijambe', '2019-06-10 04:42:07'),
('031402', 'ID', 'Kec. Plupuh', '2019-06-10 04:42:07'),
('031403', 'ID', 'Kec. Masaran', '2019-06-10 04:42:07'),
('031404', 'ID', 'Kec. Kedawung', '2019-06-10 04:42:07'),
('031405', 'ID', 'Kec. Sambirejo', '2019-06-10 04:42:07'),
('031406', 'ID', 'Kec. Gondang', '2019-06-10 04:42:07'),
('031407', 'ID', 'Kec. Sambung Macan', '2019-06-10 04:42:07'),
('031408', 'ID', 'Kec. Ngrampal', '2019-06-10 04:42:07'),
('031409', 'ID', 'Kec. Karangmalang', '2019-06-10 04:42:07'),
('031410', 'ID', 'Kec. Sragen', '2019-06-10 04:42:07'),
('031411', 'ID', 'Kec. Sidoharjo', '2019-06-10 04:42:07'),
('031412', 'ID', 'Kec. Tanon', '2019-06-10 04:42:07'),
('031413', 'ID', 'Kec. Gemolong', '2019-06-10 04:42:07'),
('031414', 'ID', 'Kec. Miri', '2019-06-10 04:42:07'),
('031415', 'ID', 'Kec. Sumberlawang', '2019-06-10 04:42:07'),
('031416', 'ID', 'Kec. Mondokan', '2019-06-10 04:42:07'),
('031417', 'ID', 'Kec. Sukodono', '2019-06-10 04:42:07'),
('031418', 'ID', 'Kec. Gesi', '2019-06-10 04:42:07'),
('031419', 'ID', 'Kec. Tangen', '2019-06-10 04:42:07'),
('031420', 'ID', 'Kec. Jenar', '2019-06-10 04:42:07'),
('031500', 'ID', 'Kab. Grobogan', '2019-06-10 04:42:07'),
('031501', 'ID', 'Kec. Kedungjati', '2019-06-10 04:42:07'),
('031502', 'ID', 'Kec. Karangrayung', '2019-06-10 04:42:07'),
('031503', 'ID', 'Kec. Penawangan', '2019-06-10 04:42:07'),
('031504', 'ID', 'Kec. Toroh', '2019-06-10 04:42:07'),
('031505', 'ID', 'Kec. Geyer', '2019-06-10 04:42:07'),
('031506', 'ID', 'Kec. Pulokulon', '2019-06-10 04:42:07'),
('031507', 'ID', 'Kec. Kradenan', '2019-06-10 04:42:07'),
('031508', 'ID', 'Kec. Gabus', '2019-06-10 04:42:07'),
('031509', 'ID', 'Kec. Ngaringan', '2019-06-10 04:42:07'),
('031510', 'ID', 'Kec. Wirosari', '2019-06-10 04:42:07'),
('031511', 'ID', 'Kec. Tawangharjo', '2019-06-10 04:42:07'),
('031512', 'ID', 'Kec. Grobogan', '2019-06-10 04:42:07'),
('031513', 'ID', 'Kec. Purwodadi', '2019-06-10 04:42:07'),
('031514', 'ID', 'Kec. Brati', '2019-06-10 04:42:07'),
('031515', 'ID', 'Kec. Klambu', '2019-06-10 04:42:07'),
('031516', 'ID', 'Kec. Godong', '2019-06-10 04:42:07'),
('031517', 'ID', 'Kec. Gubug', '2019-06-10 04:42:07'),
('031518', 'ID', 'Kec. Tegowanu', '2019-06-10 04:42:07'),
('031519', 'ID', 'Kec. Tanggungharjo', '2019-06-10 04:42:07'),
('031600', 'ID', 'Kab. Blora', '2019-06-10 04:42:07'),
('031601', 'ID', 'Kec. Jati', '2019-06-10 04:42:07'),
('031602', 'ID', 'Kec. Randublatung', '2019-06-10 04:42:07'),
('031603', 'ID', 'Kec. Kradenan', '2019-06-10 04:42:07'),
('031604', 'ID', 'Kec. Kedungtuban', '2019-06-10 04:42:07'),
('031605', 'ID', 'Kec. Cepu', '2019-06-10 04:42:07'),
('031606', 'ID', 'Kec. Sambong', '2019-06-10 04:42:07'),
('031607', 'ID', 'Kec. Jiken', '2019-06-10 04:42:07'),
('031608', 'ID', 'Kec. Bogorejo', '2019-06-10 04:42:07'),
('031609', 'ID', 'Kec. Jepon', '2019-06-10 04:42:07'),
('031610', 'ID', 'Kec. Kota Blora', '2019-06-10 04:42:07'),
('031611', 'ID', 'Kec. Banjarejo', '2019-06-10 04:42:07'),
('031612', 'ID', 'Kec. Tunjungan', '2019-06-10 04:42:07'),
('031613', 'ID', 'Kec. Japah', '2019-06-10 04:42:07'),
('031614', 'ID', 'Kec. Ngawen', '2019-06-10 04:42:07'),
('031615', 'ID', 'Kec. Kunduran', '2019-06-10 04:42:07'),
('031616', 'ID', 'Kec. Todanan', '2019-06-10 04:42:07'),
('031700', 'ID', 'Kab. Rembang', '2019-06-10 04:42:07'),
('031701', 'ID', 'Kec. Sumber', '2019-06-10 04:42:07'),
('031702', 'ID', 'Kec. Bulu', '2019-06-10 04:42:07'),
('031703', 'ID', 'Kec. Gunem', '2019-06-10 04:42:07'),
('031704', 'ID', 'Kec. Sale', '2019-06-10 04:42:07'),
('031705', 'ID', 'Kec. Sarang', '2019-06-10 04:42:07'),
('031706', 'ID', 'Kec. Sedan', '2019-06-10 04:42:07'),
('031707', 'ID', 'Kec. Pamotan', '2019-06-10 04:42:07'),
('031708', 'ID', 'Kec. Sulang', '2019-06-10 04:42:07'),
('031709', 'ID', 'Kec. Kaliori', '2019-06-10 04:42:07'),
('031710', 'ID', 'Kec. Rembang', '2019-06-10 04:42:07'),
('031711', 'ID', 'Kec. Pancur', '2019-06-10 04:42:07'),
('031712', 'ID', 'Kec. Kragan', '2019-06-10 04:42:07'),
('031713', 'ID', 'Kec. Sluke', '2019-06-10 04:42:07'),
('031714', 'ID', 'Kec. Lasem', '2019-06-10 04:42:07'),
('031800', 'ID', 'Kab. Pati', '2019-06-10 04:42:07'),
('031801', 'ID', 'Kec. Sukolilo', '2019-06-10 04:42:07'),
('031802', 'ID', 'Kec. Kayen', '2019-06-10 04:42:07'),
('031803', 'ID', 'Kec. Tambakromo', '2019-06-10 04:42:07'),
('031804', 'ID', 'Kec. Winong', '2019-06-10 04:42:07'),
('031805', 'ID', 'Kec. Pucakwangi', '2019-06-10 04:42:07'),
('031806', 'ID', 'Kec. Jaken', '2019-06-10 04:42:07'),
('031807', 'ID', 'Kec. Batangan', '2019-06-10 04:42:07'),
('031808', 'ID', 'Kec. Juwana', '2019-06-10 04:42:07'),
('031809', 'ID', 'Kec. Jakenan', '2019-06-10 04:42:07'),
('031810', 'ID', 'Kec. Pati', '2019-06-10 04:42:07'),
('031811', 'ID', 'Kec. Gabus', '2019-06-10 04:42:07'),
('031812', 'ID', 'Kec. Margorejo', '2019-06-10 04:42:07'),
('031813', 'ID', 'Kec. Gembong', '2019-06-10 04:42:07'),
('031814', 'ID', 'Kec. Tlogowungu', '2019-06-10 04:42:07'),
('031815', 'ID', 'Kec. Wedarijaksa', '2019-06-10 04:42:07'),
('031816', 'ID', 'Kec. Trangkil', '2019-06-10 04:42:07'),
('031817', 'ID', 'Kec. Margoyoso', '2019-06-10 04:42:07'),
('031818', 'ID', 'Kec. Gunung Wungkal', '2019-06-10 04:42:07'),
('031819', 'ID', 'Kec. Cluwak', '2019-06-10 04:42:07'),
('031820', 'ID', 'Kec. Tayu', '2019-06-10 04:42:07'),
('031821', 'ID', 'Kec. Dukuhseti', '2019-06-10 04:42:07'),
('031900', 'ID', 'Kab. Kudus', '2019-06-10 04:42:07'),
('031901', 'ID', 'Kec. Kaliwungu', '2019-06-10 04:42:07'),
('031902', 'ID', 'Kec. Kota Kudus', '2019-06-10 04:42:07'),
('031903', 'ID', 'Kec. Jati', '2019-06-10 04:42:07'),
('031904', 'ID', 'Kec. Undaan', '2019-06-10 04:42:07'),
('031905', 'ID', 'Kec. Mejobo', '2019-06-10 04:42:07'),
('031906', 'ID', 'Kec. Jekulo', '2019-06-10 04:42:07'),
('031907', 'ID', 'Kec. Bae', '2019-06-10 04:42:07'),
('031908', 'ID', 'Kec. Gebog', '2019-06-10 04:42:07'),
('031909', 'ID', 'Kec. Dawe', '2019-06-10 04:42:07'),
('032000', 'ID', 'Kab. Jepara', '2019-06-10 04:42:07'),
('032001', 'ID', 'Kec. Kedung', '2019-06-10 04:42:07'),
('032002', 'ID', 'Kec. Pecangaan', '2019-06-10 04:42:07'),
('032003', 'ID', 'Kec. Welahan', '2019-06-10 04:42:07'),
('032004', 'ID', 'Kec. Mayong', '2019-06-10 04:42:07'),
('032005', 'ID', 'Kec. Nalumsari', '2019-06-10 04:42:07'),
('032006', 'ID', 'Kec. Batealit', '2019-06-10 04:42:07'),
('032007', 'ID', 'Kec. Tahunan', '2019-06-10 04:42:07'),
('032008', 'ID', 'Kec. Jepara', '2019-06-10 04:42:07'),
('032009', 'ID', 'Kec. Mlonggo', '2019-06-10 04:42:07'),
('032010', 'ID', 'Kec. Bangsri', '2019-06-10 04:42:07'),
('032011', 'ID', 'Kec. Keling', '2019-06-10 04:42:07'),
('032012', 'ID', 'Kec. Karimunjawa', '2019-06-10 04:42:07'),
('032013', 'ID', 'Kec. Kalinyamatan', '2019-06-10 04:42:07'),
('032014', 'ID', 'Kec. Kembang', '2019-06-10 04:42:07'),
('032015', 'ID', 'Kec. Donorojo', '2019-06-10 04:42:07'),
('032016', 'ID', 'Kec. Pakis Aji', '2019-06-10 04:42:07'),
('032100', 'ID', 'Kab. Demak', '2019-06-10 04:42:07'),
('032101', 'ID', 'Kec. Mranggen', '2019-06-10 04:42:07'),
('032102', 'ID', 'Kec. Karangawen', '2019-06-10 04:42:07'),
('032103', 'ID', 'Kec. Guntur', '2019-06-10 04:42:07'),
('032104', 'ID', 'Kec. Sayung', '2019-06-10 04:42:07'),
('032105', 'ID', 'Kec. Karang Tengah', '2019-06-10 04:42:07'),
('032106', 'ID', 'Kec. Bonang', '2019-06-10 04:42:07'),
('032107', 'ID', 'Kec. Demak', '2019-06-10 04:42:07'),
('032108', 'ID', 'Kec. Wonosalam', '2019-06-10 04:42:07'),
('032109', 'ID', 'Kec. Dempet', '2019-06-10 04:42:07'),
('032110', 'ID', 'Kec. Gajah', '2019-06-10 04:42:07'),
('032111', 'ID', 'Kec. Karanganyar', '2019-06-10 04:42:07'),
('032112', 'ID', 'Kec. Mijen', '2019-06-10 04:42:07'),
('032113', 'ID', 'Kec. Wedung', '2019-06-10 04:42:07'),
('032114', 'ID', 'Kec. Kebonagung', '2019-06-10 04:42:07'),
('032200', 'ID', 'Kab. Semarang', '2019-06-10 04:42:07'),
('032201', 'ID', 'Kec. Getasan', '2019-06-10 04:42:07'),
('032202', 'ID', 'Kec. Tengaran', '2019-06-10 04:42:07'),
('032203', 'ID', 'Kec. Susukan', '2019-06-10 04:42:07'),
('032204', 'ID', 'Kec. Suruh', '2019-06-10 04:42:07'),
('032205', 'ID', 'Kec. Pabelan', '2019-06-10 04:42:07'),
('032206', 'ID', 'Kec. Tuntang', '2019-06-10 04:42:07'),
('032207', 'ID', 'Kec. Banyubiru', '2019-06-10 04:42:07'),
('032208', 'ID', 'Kec. Jambu', '2019-06-10 04:42:07'),
('032209', 'ID', 'Kec. Sumowono', '2019-06-10 04:42:07'),
('032210', 'ID', 'Kec. Ambarawa', '2019-06-10 04:42:07'),
('032211', 'ID', 'Kec. Bawen', '2019-06-10 04:42:07'),
('032212', 'ID', 'Kec. Bringin', '2019-06-10 04:42:07'),
('032213', 'ID', 'Kec. Pringapus', '2019-06-10 04:42:07'),
('032214', 'ID', 'Kec. Bergas', '2019-06-10 04:42:07'),
('032217', 'ID', 'Kec. Kaliwungu', '2019-06-10 04:42:07'),
('032218', 'ID', 'Kec. Bancak', '2019-06-10 04:42:07'),
('032219', 'ID', 'Kec. Ungaran Barat', '2019-06-10 04:42:07'),
('032220', 'ID', 'Kec. Ungaran Timur', '2019-06-10 04:42:07'),
('032221', 'ID', 'Kec. Bandungan', '2019-06-10 04:42:07'),
('032300', 'ID', 'Kab. Temanggung', '2019-06-10 04:42:07'),
('032301', 'ID', 'Kec. Parakan', '2019-06-10 04:42:07'),
('032302', 'ID', 'Kec. Bulu', '2019-06-10 04:42:07'),
('032303', 'ID', 'Kec. Temanggung', '2019-06-10 04:42:07'),
('032304', 'ID', 'Kec. Tembarak', '2019-06-10 04:42:07'),
('032305', 'ID', 'Kec. Kranggan', '2019-06-10 04:42:07'),
('032306', 'ID', 'Kec. Pringsurat', '2019-06-10 04:42:07'),
('032307', 'ID', 'Kec. Kaloran', '2019-06-10 04:42:07'),
('032308', 'ID', 'Kec. Kandangan', '2019-06-10 04:42:07'),
('032309', 'ID', 'Kec. Kedu', '2019-06-10 04:42:07'),
('032310', 'ID', 'Kec. Ngadirejo', '2019-06-10 04:42:07'),
('032311', 'ID', 'Kec. Jumo', '2019-06-10 04:42:07'),
('032312', 'ID', 'Kec. Candiroto', '2019-06-10 04:42:07'),
('032313', 'ID', 'Kec. Tretep', '2019-06-10 04:42:07'),
('032314', 'ID', 'Kec. Kledung', '2019-06-10 04:42:07'),
('032315', 'ID', 'Kec. Bansari', '2019-06-10 04:42:07'),
('032316', 'ID', 'Kec. Tlogomulyo', '2019-06-10 04:42:07'),
('032317', 'ID', 'Kec. Selopampang', '2019-06-10 04:42:07'),
('032318', 'ID', 'Kec. Gemawang', '2019-06-10 04:42:07'),
('032319', 'ID', 'Kec. Bejen', '2019-06-10 04:42:07'),
('032320', 'ID', 'Kec. Wonoboyo', '2019-06-10 04:42:07'),
('032400', 'ID', 'Kab. Kendal', '2019-06-10 04:42:07'),
('032401', 'ID', 'Kec. Plantungan', '2019-06-10 04:42:07'),
('032402', 'ID', 'Kec. Sukorejo', '2019-06-10 04:42:07'),
('032403', 'ID', 'Kec. Pegeruyung', '2019-06-10 04:42:07'),
('032404', 'ID', 'Kec. Patean', '2019-06-10 04:42:07'),
('032405', 'ID', 'Kec. Singorojo', '2019-06-10 04:42:07'),
('032406', 'ID', 'Kec. Limbangan', '2019-06-10 04:42:07'),
('032407', 'ID', 'Kec. Boja', '2019-06-10 04:42:07'),
('032408', 'ID', 'Kec. Kaliwungu', '2019-06-10 04:42:07'),
('032409', 'ID', 'Kec. Brangsong', '2019-06-10 04:42:07'),
('032410', 'ID', 'Kec. Pegandon', '2019-06-10 04:42:07'),
('032411', 'ID', 'Kec. Gemuh', '2019-06-10 04:42:07'),
('032412', 'ID', 'Kec. Waleri', '2019-06-10 04:42:07'),
('032413', 'ID', 'Kec. Rowosari', '2019-06-10 04:42:07'),
('032414', 'ID', 'Kec. Kangkung', '2019-06-10 04:42:07'),
('032415', 'ID', 'Kec. Cipiring', '2019-06-10 04:42:07'),
('032416', 'ID', 'Kec. Patebon', '2019-06-10 04:42:07'),
('032417', 'ID', 'Kec. Kota Kendal', '2019-06-10 04:42:07'),
('032418', 'ID', 'Kec. Ngampel', '2019-06-10 04:42:07'),
('032419', 'ID', 'Kec. Ringinarum', '2019-06-10 04:42:07'),
('032420', 'ID', 'Kec. Kaliwungu Selatan', '2019-06-10 04:42:07'),
('032500', 'ID', 'Kab. Batang', '2019-06-10 04:42:07'),
('032501', 'ID', 'Kec. Wonotunggal', '2019-06-10 04:42:07'),
('032502', 'ID', 'Kec. Bandar', '2019-06-10 04:42:07'),
('032503', 'ID', 'Kec. Blado', '2019-06-10 04:42:07'),
('032504', 'ID', 'Kec. Reban', '2019-06-10 04:42:07'),
('032505', 'ID', 'Kec. Bawang', '2019-06-10 04:42:07'),
('032506', 'ID', 'Kec. Tersono', '2019-06-10 04:42:07'),
('032507', 'ID', 'Kec. Gringsing', '2019-06-10 04:42:07'),
('032508', 'ID', 'Kec. Limpung', '2019-06-10 04:42:07'),
('032509', 'ID', 'Kec. Subah', '2019-06-10 04:42:07'),
('032510', 'ID', 'Kec. Tulis', '2019-06-10 04:42:07'),
('032511', 'ID', 'Kec. Batang', '2019-06-10 04:42:07'),
('032512', 'ID', 'Kec. Warung Asem', '2019-06-10 04:42:07'),
('032513', 'ID', 'Kec. Banyuputih', '2019-06-10 04:42:07'),
('032514', 'ID', 'Kec. Pecalungan', '2019-06-10 04:42:07'),
('032515', 'ID', 'Kec. Kandeman', '2019-06-10 04:42:07'),
('032600', 'ID', 'Kab. Pekalongan', '2019-06-10 04:42:07'),
('032601', 'ID', 'Kec. Kandang Serang', '2019-06-10 04:42:07'),
('032602', 'ID', 'Kec. Peninggaran', '2019-06-10 04:42:07'),
('032603', 'ID', 'Kec. Lebakbarang', '2019-06-10 04:42:07'),
('032604', 'ID', 'Kec. Petungkriono', '2019-06-10 04:42:07'),
('032605', 'ID', 'Kec. Talun', '2019-06-10 04:42:07'),
('032606', 'ID', 'Kec. Doro', '2019-06-10 04:42:07'),
('032607', 'ID', 'Kec. Karanganyar', '2019-06-10 04:42:07'),
('032608', 'ID', 'Kec. Kajen', '2019-06-10 04:42:07'),
('032609', 'ID', 'Kec. Kesesi', '2019-06-10 04:42:07'),
('032610', 'ID', 'Kec. Sragi', '2019-06-10 04:42:07'),
('032611', 'ID', 'Kec. Bojong', '2019-06-10 04:42:07'),
('032612', 'ID', 'Kec. Wonopringgo', '2019-06-10 04:42:07'),
('032613', 'ID', 'Kec. Kedungwuni', '2019-06-10 04:42:07'),
('032614', 'ID', 'Kec. Buaran', '2019-06-10 04:42:07'),
('032615', 'ID', 'Kec. Tirto', '2019-06-10 04:42:07'),
('032616', 'ID', 'Kec. Wiradesa', '2019-06-10 04:42:07'),
('032617', 'ID', 'Kec. Siwalan', '2019-06-10 04:42:07'),
('032618', 'ID', 'Kec. Karangdadap', '2019-06-10 04:42:07'),
('032619', 'ID', 'Kec. Wonokerto', '2019-06-10 04:42:07'),
('032700', 'ID', 'Kab. Pemalang', '2019-06-10 04:42:07'),
('032701', 'ID', 'Kec. Moga', '2019-06-10 04:42:07'),
('032702', 'ID', 'Kec. Pulosari', '2019-06-10 04:42:07'),
('032703', 'ID', 'Kec. Belik', '2019-06-10 04:42:07'),
('032704', 'ID', 'Kec. Watukumpul', '2019-06-10 04:42:07'),
('032705', 'ID', 'Kec. Bodeh', '2019-06-10 04:42:07'),
('032706', 'ID', 'Kec. Bantarbolang', '2019-06-10 04:42:07'),
('032707', 'ID', 'Kec. Randudongkel', '2019-06-10 04:42:07'),
('032708', 'ID', 'Kec. Pemalang', '2019-06-10 04:42:07'),
('032709', 'ID', 'Kec. Taman', '2019-06-10 04:42:07'),
('032710', 'ID', 'Kec. Petarukan', '2019-06-10 04:42:07'),
('032711', 'ID', 'Kec. Ampelgading', '2019-06-10 04:42:07'),
('032712', 'ID', 'Kec. Comal', '2019-06-10 04:42:07'),
('032713', 'ID', 'Kec. Ulujami', '2019-06-10 04:42:07'),
('032714', 'ID', 'Kec. Warungpring', '2019-06-10 04:42:07'),
('032800', 'ID', 'Kab. Tegal', '2019-06-10 04:42:07'),
('032801', 'ID', 'Kec. Margasari', '2019-06-10 04:42:07'),
('032802', 'ID', 'Kec. Bumijawa', '2019-06-10 04:42:07'),
('032803', 'ID', 'Kec. Bojong', '2019-06-10 04:42:07'),
('032804', 'ID', 'Kec. Balapulang', '2019-06-10 04:42:07'),
('032805', 'ID', 'Kec. Pagerbarang', '2019-06-10 04:42:07'),
('032806', 'ID', 'Kec. Lebaksiu', '2019-06-10 04:42:07'),
('032807', 'ID', 'Kec. Jatinegara', '2019-06-10 04:42:07'),
('032808', 'ID', 'Kec. Kedung Banteng', '2019-06-10 04:42:07'),
('032809', 'ID', 'Kec. Pangkah', '2019-06-10 04:42:07'),
('032810', 'ID', 'Kec. Slawi', '2019-06-10 04:42:07'),
('032811', 'ID', 'Kec. Dukuhwaru', '2019-06-10 04:42:07'),
('032812', 'ID', 'Kec. Adiwerna', '2019-06-10 04:42:07'),
('032813', 'ID', 'Kec. Dukuhturi', '2019-06-10 04:42:07'),
('032814', 'ID', 'Kec. Talang', '2019-06-10 04:42:07'),
('032815', 'ID', 'Kec. Tarub', '2019-06-10 04:42:07'),
('032816', 'ID', 'Kec. Kramat', '2019-06-10 04:42:07'),
('032817', 'ID', 'Kec. Suradadi', '2019-06-10 04:42:07'),
('032818', 'ID', 'Kec. Warureja', '2019-06-10 04:42:07'),
('032900', 'ID', 'Kab. Brebes', '2019-06-10 04:42:07'),
('032901', 'ID', 'Kec. Salem', '2019-06-10 04:42:07'),
('032902', 'ID', 'Kec. Bantarkawung', '2019-06-10 04:42:07'),
('032903', 'ID', 'Kec. Bumiayu', '2019-06-10 04:42:07'),
('032904', 'ID', 'Kec. Paguyangan', '2019-06-10 04:42:07'),
('032905', 'ID', 'Kec. Sirampog', '2019-06-10 04:42:07'),
('032906', 'ID', 'Kec. Tonjong', '2019-06-10 04:42:07'),
('032907', 'ID', 'Kec. Larangan', '2019-06-10 04:42:07'),
('032908', 'ID', 'Kec. Ketanggungan', '2019-06-10 04:42:07'),
('032909', 'ID', 'Kec. Banjarharjo', '2019-06-10 04:42:07'),
('032910', 'ID', 'Kec. Losari', '2019-06-10 04:42:07'),
('032911', 'ID', 'Kec. Tanjung', '2019-06-10 04:42:07'),
('032912', 'ID', 'Kec. Kersana', '2019-06-10 04:42:07'),
('032913', 'ID', 'Kec. Bulakamba', '2019-06-10 04:42:07'),
('032914', 'ID', 'Kec. Wanasari', '2019-06-10 04:42:07'),
('032915', 'ID', 'Kec. Songgom', '2019-06-10 04:42:07'),
('032916', 'ID', 'Kec. Jatibarang', '2019-06-10 04:42:07'),
('032917', 'ID', 'Kec. Brebes', '2019-06-10 04:42:07'),
('036000', 'ID', 'Kota Magelang', '2019-06-10 04:42:07'),
('036001', 'ID', 'Kec. Magelang Selatan', '2019-06-10 04:42:07'),
('036002', 'ID', 'Kec. Magelang Utara', '2019-06-10 04:42:07'),
('036003', 'ID', 'Kec. Magelang Tengah', '2019-06-10 04:42:07'),
('036100', 'ID', 'Kota Surakarta', '2019-06-10 04:42:07'),
('036101', 'ID', 'Kec. Laweyan', '2019-06-10 04:42:07'),
('036102', 'ID', 'Kec. Serengan', '2019-06-10 04:42:07'),
('036103', 'ID', 'Kec. Pasarkliwon', '2019-06-10 04:42:07'),
('036104', 'ID', 'Kec. Jebres', '2019-06-10 04:42:07'),
('036105', 'ID', 'Kec. Banjarsari', '2019-06-10 04:42:07'),
('036200', 'ID', 'Kota Salatiga', '2019-06-10 04:42:07'),
('036201', 'ID', 'Kec. Argomulyo', '2019-06-10 04:42:07'),
('036202', 'ID', 'Kec. Tingkir', '2019-06-10 04:42:07'),
('036203', 'ID', 'Kec. Sidomukti', '2019-06-10 04:42:07'),
('036204', 'ID', 'Kec. Sidorejo', '2019-06-10 04:42:07'),
('036300', 'ID', 'Kota Semarang', '2019-06-10 04:42:07'),
('036301', 'ID', 'Kec. Mijen', '2019-06-10 04:42:07'),
('036302', 'ID', 'Kec. Gunung Pati', '2019-06-10 04:42:07'),
('036303', 'ID', 'Kec. Banyumanik', '2019-06-10 04:42:07'),
('036304', 'ID', 'Kec. Gajah Mungkur', '2019-06-10 04:42:07'),
('036305', 'ID', 'Kec. Semarang Selatan', '2019-06-10 04:42:07'),
('036306', 'ID', 'Kec. Candisari', '2019-06-10 04:42:07'),
('036307', 'ID', 'Kec. Tembalang', '2019-06-10 04:42:07'),
('036308', 'ID', 'Kec. Pedurungan', '2019-06-10 04:42:07'),
('036309', 'ID', 'Kec. Genuk', '2019-06-10 04:42:07'),
('036310', 'ID', 'Kec. Gayamsari', '2019-06-10 04:42:07'),
('036311', 'ID', 'Kec. Semarang Timur', '2019-06-10 04:42:07'),
('036312', 'ID', 'Kec. Semarang Tengah', '2019-06-10 04:42:07'),
('036313', 'ID', 'Kec. Semarang Utara', '2019-06-10 04:42:07'),
('036314', 'ID', 'Kec. Semarang Barat', '2019-06-10 04:42:07'),
('036315', 'ID', 'Kec. Tugu', '2019-06-10 04:42:07'),
('036316', 'ID', 'Kec. Ngaliyan', '2019-06-10 04:42:07'),
('036400', 'ID', 'Kota Pekalongan', '2019-06-10 04:42:07'),
('036401', 'ID', 'Kec. Pekalongan Barat', '2019-06-10 04:42:07'),
('036402', 'ID', 'Kec. Pekalongan Timur', '2019-06-10 04:42:07'),
('036403', 'ID', 'Kec. Pekalongan Selatan', '2019-06-10 04:42:07'),
('036404', 'ID', 'Kec. Pekalongan Utara', '2019-06-10 04:42:07'),
('036500', 'ID', 'Kota Tegal', '2019-06-10 04:42:07'),
('036501', 'ID', 'Kec. Tegal Selatan', '2019-06-10 04:42:07'),
('036502', 'ID', 'Kec. Tegal Timur', '2019-06-10 04:42:07'),
('036503', 'ID', 'Kec. Tegal Barat', '2019-06-10 04:42:07'),
('036504', 'ID', 'Kec. Margadana', '2019-06-10 04:42:07'),
('040000', 'ID', 'Prop. D.I. Yogyakarta', '2019-06-10 04:42:07'),
('040100', 'ID', 'Kab. Bantul', '2019-06-10 04:42:07'),
('040101', 'ID', 'Kec. Srandakan', '2019-06-10 04:42:07'),
('040102', 'ID', 'Kec. Sanden', '2019-06-10 04:42:07'),
('040103', 'ID', 'Kec. Kretek', '2019-06-10 04:42:07'),
('040104', 'ID', 'Kec. Pundong', '2019-06-10 04:42:07'),
('040105', 'ID', 'Kec. Bambang Lipuro', '2019-06-10 04:42:07'),
('040106', 'ID', 'Kec. Pandak', '2019-06-10 04:42:07'),
('040107', 'ID', 'Kec. Bantul', '2019-06-10 04:42:07'),
('040108', 'ID', 'Kec. Jetis', '2019-06-10 04:42:07'),
('040109', 'ID', 'Kec. Imogiri', '2019-06-10 04:42:07'),
('040110', 'ID', 'Kec. Dlingo', '2019-06-10 04:42:07'),
('040111', 'ID', 'Kec. Pleret', '2019-06-10 04:42:07'),
('040112', 'ID', 'Kec. Piyungan', '2019-06-10 04:42:07'),
('040113', 'ID', 'Kec. Banguntapan', '2019-06-10 04:42:07'),
('040114', 'ID', 'Kec. Sewon', '2019-06-10 04:42:07'),
('040115', 'ID', 'Kec. Kasihan', '2019-06-10 04:42:07'),
('040116', 'ID', 'Kec. Pajangan', '2019-06-10 04:42:07'),
('040117', 'ID', 'Kec. Sedayu', '2019-06-10 04:42:07'),
('040200', 'ID', 'Kab. Sleman', '2019-06-10 04:42:07'),
('040201', 'ID', 'Kec. Moyudan', '2019-06-10 04:42:07'),
('040202', 'ID', 'Kec. Minggir', '2019-06-10 04:42:07'),
('040203', 'ID', 'Kec. Seyegan', '2019-06-10 04:42:07'),
('040204', 'ID', 'Kec. Godean', '2019-06-10 04:42:07'),
('040205', 'ID', 'Kec. Gamping', '2019-06-10 04:42:07'),
('040206', 'ID', 'Kec. Mlati', '2019-06-10 04:42:07'),
('040207', 'ID', 'Kec. Depok', '2019-06-10 04:42:07'),
('040208', 'ID', 'Kec. Berbah', '2019-06-10 04:42:07'),
('040209', 'ID', 'Kec. Prambanan', '2019-06-10 04:42:07'),
('040210', 'ID', 'Kec. Kalasan', '2019-06-10 04:42:07'),
('040211', 'ID', 'Kec. Ngemplak', '2019-06-10 04:42:07'),
('040212', 'ID', 'Kec. Ngaglik', '2019-06-10 04:42:07'),
('040213', 'ID', 'Kec. Sleman', '2019-06-10 04:42:07'),
('040214', 'ID', 'Kec. Tempel', '2019-06-10 04:42:07'),
('040215', 'ID', 'Kec. Turi', '2019-06-10 04:42:07'),
('040216', 'ID', 'Kec. Pekem', '2019-06-10 04:42:07'),
('040217', 'ID', 'Kec. Cangkringan', '2019-06-10 04:42:07'),
('040218', 'ID', 'Kec. Gamping', '2020-02-10 03:07:43'),
('040300', 'ID', 'Kab. Gunung Kidul', '2019-06-10 04:42:07'),
('040301', 'ID', 'Kec. Panggang', '2019-06-10 04:42:07'),
('040302', 'ID', 'Kec. Paliyan', '2019-06-10 04:42:07'),
('040303', 'ID', 'Kec. Sapto Sari', '2019-06-10 04:42:07'),
('040304', 'ID', 'Kec. Tepus', '2019-06-10 04:42:07'),
('040305', 'ID', 'Kec. Rongkop', '2019-06-10 04:42:07'),
('040306', 'ID', 'Kec. Semanu', '2019-06-10 04:42:07'),
('040307', 'ID', 'Kec. Ponjong', '2019-06-10 04:42:07'),
('040308', 'ID', 'Kec. Karangmojo', '2019-06-10 04:42:07'),
('040309', 'ID', 'Kec. Wonosari', '2019-06-10 04:42:07'),
('040310', 'ID', 'Kec. Playen', '2019-06-10 04:42:07'),
('040311', 'ID', 'Kec. Patuk', '2019-06-10 04:42:07'),
('040312', 'ID', 'Kec. Gedang Sari', '2019-06-10 04:42:07'),
('040313', 'ID', 'Kec. Nglipar', '2019-06-10 04:42:07'),
('040314', 'ID', 'Kec. Ngawen', '2019-06-10 04:42:07'),
('040315', 'ID', 'Kec. Semin', '2019-06-10 04:42:07'),
('040316', 'ID', 'Kec. Purwosari', '2019-06-10 04:42:07'),
('040317', 'ID', 'Kec. Girisubo', '2019-06-10 04:42:07'),
('040318', 'ID', 'Kec. Tanjungsari', '2019-06-10 04:42:07'),
('040400', 'ID', 'Kab. Kulon Progo', '2019-06-10 04:42:07'),
('040401', 'ID', 'Kec. Temon', '2019-06-10 04:42:07'),
('040402', 'ID', 'Kec. Wates', '2019-06-10 04:42:07'),
('040403', 'ID', 'Kec. Panjatan', '2019-06-10 04:42:07'),
('040404', 'ID', 'Kec. Galur', '2019-06-10 04:42:07'),
('040405', 'ID', 'Kec. Lendah', '2019-06-10 04:42:07'),
('040406', 'ID', 'Kec. Sentolo', '2019-06-10 04:42:07'),
('040407', 'ID', 'Kec. Pengasih', '2019-06-10 04:42:07'),
('040408', 'ID', 'Kec. Kokap', '2019-06-10 04:42:07'),
('040409', 'ID', 'Kec. Girimulyo', '2019-06-10 04:42:07'),
('040410', 'ID', 'Kec. Nanggulan', '2019-06-10 04:42:07'),
('040411', 'ID', 'Kec. Kalibawang', '2019-06-10 04:42:07'),
('040412', 'ID', 'Kec. Samigaluh', '2019-06-10 04:42:07'),
('046000', 'ID', 'Kota Yogyakarta', '2019-06-10 04:42:07'),
('046001', 'ID', 'Kec. Mantrijeron', '2019-06-10 04:42:07'),
('046002', 'ID', 'Kec. Kraton', '2019-06-10 04:42:07'),
('046003', 'ID', 'Kec. Mergangsan', '2019-06-10 04:42:07'),
('046004', 'ID', 'Kec. Umbulharjo', '2019-06-10 04:42:07'),
('046005', 'ID', 'Kec. Kotagede', '2019-06-10 04:42:07'),
('046006', 'ID', 'Kec. Gondokusuman', '2019-06-10 04:42:07'),
('046007', 'ID', 'Kec. Danurejan', '2019-06-10 04:42:07'),
('046008', 'ID', 'Kec. Pakualaman', '2019-06-10 04:42:07'),
('046009', 'ID', 'Kec. Gondomanan', '2019-06-10 04:42:07'),
('046010', 'ID', 'Kec. Ngampilan', '2019-06-10 04:42:07'),
('046011', 'ID', 'Kec. Wirobrajan', '2019-06-10 04:42:07'),
('046012', 'ID', 'Kec. Gedongtengen', '2019-06-10 04:42:07'),
('046013', 'ID', 'Kec. Jetis', '2019-06-10 04:42:07'),
('046014', 'ID', 'Kec. Tegalrejo', '2019-06-10 04:42:07'),
('046015', 'ID', 'Kec. Gamping', '2020-02-10 03:07:43'),
('050000', 'ID', 'Prop. Jawa Timur', '2019-06-10 04:42:07'),
('050100', 'ID', 'Kab. Gresik', '2019-06-10 04:42:07'),
('050101', 'ID', 'Kec. Wringin Anom', '2019-06-10 04:42:07'),
('050102', 'ID', 'Kec. Driyorejo', '2019-06-10 04:42:07'),
('050103', 'ID', 'Kec. Kedamean', '2019-06-10 04:42:07'),
('050104', 'ID', 'Kec. Menganti', '2019-06-10 04:42:07'),
('050105', 'ID', 'Kec. Cerme', '2019-06-10 04:42:07'),
('050106', 'ID', 'Kec. Benjeng', '2019-06-10 04:42:07'),
('050107', 'ID', 'Kec. Balong Panggang', '2019-06-10 04:42:07'),
('050108', 'ID', 'Kec. Duduk Sampeyan', '2019-06-10 04:42:07'),
('050109', 'ID', 'Kec. Kebomas', '2019-06-10 04:42:07'),
('050110', 'ID', 'Kec. Gresik', '2019-06-10 04:42:07'),
('050111', 'ID', 'Kec. Manyar', '2019-06-10 04:42:07'),
('050112', 'ID', 'Kec. Bungah', '2019-06-10 04:42:07'),
('050113', 'ID', 'Kec. Sidayu', '2019-06-10 04:42:07'),
('050114', 'ID', 'Kec. Dukun', '2019-06-10 04:42:07'),
('050115', 'ID', 'Kec. Panceng', '2019-06-10 04:42:07'),
('050116', 'ID', 'Kec. Ujung Pangkah', '2019-06-10 04:42:07'),
('050117', 'ID', 'Kec. Sangkapura', '2019-06-10 04:42:07'),
('050118', 'ID', 'Kec. Tambak', '2019-06-10 04:42:07'),
('050200', 'ID', 'Kab. Sidoarjo', '2019-06-10 04:42:07'),
('050201', 'ID', 'Kec. Tarik', '2019-06-10 04:42:07'),
('050202', 'ID', 'Kec. Prambon', '2019-06-10 04:42:07'),
('050203', 'ID', 'Kec. Krembung', '2019-06-10 04:42:07'),
('050204', 'ID', 'Kec. Porong', '2019-06-10 04:42:07'),
('050205', 'ID', 'Kec. Jabon', '2019-06-10 04:42:07'),
('050206', 'ID', 'Kec. Tanggulangin', '2019-06-10 04:42:07'),
('050207', 'ID', 'Kec. Candi', '2019-06-10 04:42:07'),
('050208', 'ID', 'Kec. Tulangan', '2019-06-10 04:42:07'),
('050209', 'ID', 'Kec. Wonoayu', '2019-06-10 04:42:07'),
('050210', 'ID', 'Kec. Sukodono', '2019-06-10 04:42:07'),
('050211', 'ID', 'Kec. Sidoarjo', '2019-06-10 04:42:07'),
('050212', 'ID', 'Kec. Buduran', '2019-06-10 04:42:07'),
('050213', 'ID', 'Kec. Sedati', '2019-06-10 04:42:07'),
('050214', 'ID', 'Kec. Waru', '2019-06-10 04:42:07'),
('050215', 'ID', 'Kec. Gedangan', '2019-06-10 04:42:07'),
('050216', 'ID', 'Kec. Taman', '2019-06-10 04:42:07'),
('050217', 'ID', 'Kec. Krian', '2019-06-10 04:42:07'),
('050218', 'ID', 'Kec. Balong Bendo', '2019-06-10 04:42:07'),
('050300', 'ID', 'Kab. Mojokerto', '2019-06-10 04:42:07'),
('050301', 'ID', 'Kec. Jatirejo', '2019-06-10 04:42:07'),
('050302', 'ID', 'Kec. Gondang', '2019-06-10 04:42:07'),
('050303', 'ID', 'Kec. Pacet', '2019-06-10 04:42:07'),
('050304', 'ID', 'Kec. Trawas', '2019-06-10 04:42:07'),
('050305', 'ID', 'Kec. Ngoro', '2019-06-10 04:42:07'),
('050306', 'ID', 'Kec. Pungging', '2019-06-10 04:42:07'),
('050307', 'ID', 'Kec. Kutorejo', '2019-06-10 04:42:07'),
('050308', 'ID', 'Kec. Mojosari', '2019-06-10 04:42:07'),
('050309', 'ID', 'Kec. Bangsal', '2019-06-10 04:42:07'),
('050310', 'ID', 'Kec. Dlanggu', '2019-06-10 04:42:07'),
('050311', 'ID', 'Kec. Puri', '2019-06-10 04:42:07'),
('050312', 'ID', 'Kec. Trowulan', '2019-06-10 04:42:07'),
('050313', 'ID', 'Kec. Sooko', '2019-06-10 04:42:07'),
('050314', 'ID', 'Kec. Gedek', '2019-06-10 04:42:07'),
('050315', 'ID', 'Kec. Kemlagi', '2019-06-10 04:42:07'),
('050316', 'ID', 'Kec. Jetis', '2019-06-10 04:42:07'),
('050317', 'ID', 'Kec. Dawar Blandong', '2019-06-10 04:42:07'),
('050318', 'ID', 'Kec. Mojoanyar', '2019-06-10 04:42:07'),
('050400', 'ID', 'Kab. Jombang', '2019-06-10 04:42:07'),
('050401', 'ID', 'Kec. Bandar Kedung Mulyo', '2019-06-10 04:42:07'),
('050402', 'ID', 'Kec. Perak', '2019-06-10 04:42:07'),
('050403', 'ID', 'Kec. Gudo', '2019-06-10 04:42:07'),
('050404', 'ID', 'Kec. Diwek', '2019-06-10 04:42:07'),
('050405', 'ID', 'Kec. Ngoro', '2019-06-10 04:42:07'),
('050406', 'ID', 'Kec. Mojowarno', '2019-06-10 04:42:07'),
('050407', 'ID', 'Kec. Bareng', '2019-06-10 04:42:07'),
('050408', 'ID', 'Kec. Wonosalam', '2019-06-10 04:42:07'),
('050409', 'ID', 'Kec. Mojoagung', '2019-06-10 04:42:07'),
('050410', 'ID', 'Kec. Somobito', '2019-06-10 04:42:07'),
('050411', 'ID', 'Kec. Jogo Roto', '2019-06-10 04:42:07'),
('050412', 'ID', 'Kec. Peterongan', '2019-06-10 04:42:07'),
('050413', 'ID', 'Kec. Jombang', '2019-06-10 04:42:07'),
('050414', 'ID', 'Kec. Megaluh', '2019-06-10 04:42:07'),
('050415', 'ID', 'Kec. Tembelang', '2019-06-10 04:42:07'),
('050416', 'ID', 'Kec. Kesamben', '2019-06-10 04:42:07'),
('050417', 'ID', 'Kec. Kudu', '2019-06-10 04:42:07'),
('050418', 'ID', 'Kec. Ploso', '2019-06-10 04:42:07'),
('050419', 'ID', 'Kec. Kabuh', '2019-06-10 04:42:07'),
('050420', 'ID', 'Kec. Plandaan', '2019-06-10 04:42:07'),
('050421', 'ID', 'Kec. Ngusikan', '2019-06-10 04:42:07'),
('050500', 'ID', 'Kab. Bojonegoro', '2019-06-10 04:42:07'),
('050501', 'ID', 'Kec. Margomulyo', '2019-06-10 04:42:07'),
('050502', 'ID', 'Kec. Ngraho', '2019-06-10 04:42:07'),
('050503', 'ID', 'Kec. Tambakrejo', '2019-06-10 04:42:07'),
('050504', 'ID', 'Kec. Ngambon', '2019-06-10 04:42:07'),
('050505', 'ID', 'Kec. Bubulan', '2019-06-10 04:42:07'),
('050506', 'ID', 'Kec. Temayang', '2019-06-10 04:42:07'),
('050507', 'ID', 'Kec. Sugihwaras', '2019-06-10 04:42:07'),
('050508', 'ID', 'Kec. Kedungadem', '2019-06-10 04:42:07'),
('050509', 'ID', 'Kec. Kepoh Baru', '2019-06-10 04:42:07'),
('050510', 'ID', 'Kec. Baureno', '2019-06-10 04:42:07'),
('050511', 'ID', 'Kec. Kanor', '2019-06-10 04:42:07'),
('050512', 'ID', 'Kec. Sumberrejo', '2019-06-10 04:42:07'),
('050513', 'ID', 'Kec. Balen', '2019-06-10 04:42:07'),
('050514', 'ID', 'Kec. Sukosewu', '2019-06-10 04:42:07'),
('050515', 'ID', 'Kec. Kapas', '2019-06-10 04:42:07'),
('050516', 'ID', 'Kec. Bojonegoro', '2019-06-10 04:42:07'),
('050517', 'ID', 'Kec. Trucuk', '2019-06-10 04:42:07'),
('050518', 'ID', 'Kec. Dander', '2019-06-10 04:42:07'),
('050519', 'ID', 'Kec. Ngasem', '2019-06-10 04:42:07'),
('050520', 'ID', 'Kec. Kalitidu', '2019-06-10 04:42:07'),
('050521', 'ID', 'Kec. Malo', '2019-06-10 04:42:07'),
('050522', 'ID', 'Kec. Purwosari', '2019-06-10 04:42:07'),
('050523', 'ID', 'Kec. Padangan', '2019-06-10 04:42:07'),
('050524', 'ID', 'Kec. Kasiman', '2019-06-10 04:42:07'),
('050525', 'ID', 'Kec. Kedewan', '2019-06-10 04:42:07'),
('050526', 'ID', 'Kec. Gondang', '2019-06-10 04:42:07'),
('050527', 'ID', 'Kec. Sekar', '2019-06-10 04:42:07'),
('050528', 'ID', 'Kec. Gayam', '2020-02-10 03:07:43'),
('050600', 'ID', 'Kab. Tuban', '2019-06-10 04:42:07'),
('050601', 'ID', 'Kec. Kenduruan', '2019-06-10 04:42:07'),
('050602', 'ID', 'Kec. Bangilan', '2019-06-10 04:42:07'),
('050603', 'ID', 'Kec. Senori', '2019-06-10 04:42:07'),
('050604', 'ID', 'Kec. Singgahan', '2019-06-10 04:42:07'),
('050605', 'ID', 'Kec. Montong', '2019-06-10 04:42:07'),
('050606', 'ID', 'Kec. Parengan', '2019-06-10 04:42:07'),
('050607', 'ID', 'Kec. Soko', '2019-06-10 04:42:07'),
('050608', 'ID', 'Kec. Rengel', '2019-06-10 04:42:07'),
('050609', 'ID', 'Kec. Plumpang', '2019-06-10 04:42:07'),
('050610', 'ID', 'Kec. Widang', '2019-06-10 04:42:07'),
('050611', 'ID', 'Kec. Palang', '2019-06-10 04:42:07'),
('050612', 'ID', 'Kec. Semanding', '2019-06-10 04:42:07'),
('050613', 'ID', 'Kec. Tuban', '2019-06-10 04:42:07'),
('050614', 'ID', 'Kec. Jenu', '2019-06-10 04:42:07'),
('050615', 'ID', 'Kec. Merakurak', '2019-06-10 04:42:07'),
('050616', 'ID', 'Kec. Kerek', '2019-06-10 04:42:07'),
('050617', 'ID', 'Kec. Tambakboyo', '2019-06-10 04:42:07'),
('050618', 'ID', 'Kec. Jatirogo', '2019-06-10 04:42:07'),
('050619', 'ID', 'Kec. Bancar', '2019-06-10 04:42:07'),
('050620', 'ID', 'Kec. Grabagan', '2019-06-10 04:42:07'),
('050700', 'ID', 'Kab. Lamongan', '2019-06-10 04:42:07'),
('050701', 'ID', 'Kec. Sukorame', '2019-06-10 04:42:07'),
('050702', 'ID', 'Kec. Bluluk', '2019-06-10 04:42:07'),
('050703', 'ID', 'Kec. Ngimbang', '2019-06-10 04:42:07'),
('050704', 'ID', 'Kec. Sambeng', '2019-06-10 04:42:07'),
('050705', 'ID', 'Kec. Mantup', '2019-06-10 04:42:07'),
('050706', 'ID', 'Kec. Kambang Bahu', '2019-06-10 04:42:07'),
('050707', 'ID', 'Kec. Sugio', '2019-06-10 04:42:07'),
('050708', 'ID', 'Kec. Kedungpring', '2019-06-10 04:42:07'),
('050709', 'ID', 'Kec. Modo', '2019-06-10 04:42:07'),
('050710', 'ID', 'Kec. Babat', '2019-06-10 04:42:07'),
('050711', 'ID', 'Kec. Pucuk', '2019-06-10 04:42:07'),
('050712', 'ID', 'Kec. Sukodadi', '2019-06-10 04:42:07'),
('050713', 'ID', 'Kec. Lamongan', '2019-06-10 04:42:07'),
('050714', 'ID', 'Kec. Tikung', '2019-06-10 04:42:07'),
('050715', 'ID', 'Kec. Deket', '2019-06-10 04:42:07'),
('050716', 'ID', 'Kec. Glagah', '2019-06-10 04:42:07'),
('050717', 'ID', 'Kec. Karangbinangun', '2019-06-10 04:42:07'),
('050718', 'ID', 'Kec. Turi', '2019-06-10 04:42:07'),
('050719', 'ID', 'Kec. Kalitengah', '2019-06-10 04:42:07'),
('050720', 'ID', 'Kec. Karang Geneng', '2019-06-10 04:42:07'),
('050721', 'ID', 'Kec. Sekaran', '2019-06-10 04:42:07'),
('050722', 'ID', 'Kec. Maduran', '2019-06-10 04:42:07'),
('050723', 'ID', 'Kec. Laren', '2019-06-10 04:42:07'),
('050724', 'ID', 'Kec. Solokuro', '2019-06-10 04:42:07'),
('050725', 'ID', 'Kec. Paciran', '2019-06-10 04:42:07'),
('050726', 'ID', 'Kec. Brondong', '2019-06-10 04:42:07'),
('050727', 'ID', 'Kec. Sarirejo', '2019-06-10 04:42:07'),
('050800', 'ID', 'Kab. Madiun', '2019-06-10 04:42:07'),
('050801', 'ID', 'Kec. Kebonsari', '2019-06-10 04:42:07'),
('050802', 'ID', 'Kec. Geger', '2019-06-10 04:42:07'),
('050803', 'ID', 'Kec. Dolopo', '2019-06-10 04:42:07'),
('050804', 'ID', 'Kec. Dagangan', '2019-06-10 04:42:07'),
('050805', 'ID', 'Kec. Wungu', '2019-06-10 04:42:07'),
('050806', 'ID', 'Kec. Kare', '2019-06-10 04:42:07'),
('050807', 'ID', 'Kec. Gemarang', '2019-06-10 04:42:07'),
('050808', 'ID', 'Kec. Saradan', '2019-06-10 04:42:07'),
('050809', 'ID', 'Kec. Pilangkenceng', '2019-06-10 04:42:07'),
('050810', 'ID', 'Kec. Mejayan', '2019-06-10 04:42:07'),
('050811', 'ID', 'Kec. Wonoasri', '2019-06-10 04:42:07'),
('050812', 'ID', 'Kec. Balerejo', '2019-06-10 04:42:07'),
('050813', 'ID', 'Kec. Madiun', '2019-06-10 04:42:07'),
('050814', 'ID', 'Kec. Sawahan', '2019-06-10 04:42:07'),
('050815', 'ID', 'Kec. Jiwan', '2019-06-10 04:42:07'),
('050900', 'ID', 'Kab. Ngawi', '2019-06-10 04:42:07'),
('050901', 'ID', 'Kec. Sine', '2019-06-10 04:42:07'),
('050902', 'ID', 'Kec. Ngrambe', '2019-06-10 04:42:07'),
('050903', 'ID', 'Kec. Jogorogo', '2019-06-10 04:42:07'),
('050904', 'ID', 'Kec. Kendal', '2019-06-10 04:42:07'),
('050905', 'ID', 'Kec. Geneng', '2019-06-10 04:42:07'),
('050906', 'ID', 'Kec. Kwadungan', '2019-06-10 04:42:07'),
('050907', 'ID', 'Kec. Pangkur', '2019-06-10 04:42:07'),
('050908', 'ID', 'Kec. Karangjati', '2019-06-10 04:42:07'),
('050909', 'ID', 'Kec. Bringin', '2019-06-10 04:42:07'),
('050910', 'ID', 'Kec. Padas', '2019-06-10 04:42:07'),
('050911', 'ID', 'Kec. Ngawi', '2019-06-10 04:42:07'),
('050912', 'ID', 'Kec. Paron', '2019-06-10 04:42:07'),
('050913', 'ID', 'Kec. Kedunggalar', '2019-06-10 04:42:07'),
('050914', 'ID', 'Kec. Pitu', '2019-06-10 04:42:07'),
('050915', 'ID', 'Kec. Widodaren', '2019-06-10 04:42:07'),
('050916', 'ID', 'Kec. Mantingan', '2019-06-10 04:42:07'),
('050917', 'ID', 'Kec. Karanganyar', '2019-06-10 04:42:07'),
('050918', 'ID', 'Kec. Gerih', '2019-06-10 04:42:07'),
('050919', 'ID', 'Kec. Kasreman', '2019-06-10 04:42:07'),
('051000', 'ID', 'Kab. Magetan', '2019-06-10 04:42:07'),
('051001', 'ID', 'Kec. Poncol', '2019-06-10 04:42:07'),
('051002', 'ID', 'Kec. Parang', '2019-06-10 04:42:07'),
('051003', 'ID', 'Kec. Lembeyan', '2019-06-10 04:42:07'),
('051004', 'ID', 'Kec. Takeran', '2019-06-10 04:42:07'),
('051005', 'ID', 'Kec. Kawedanan', '2019-06-10 04:42:07'),
('051006', 'ID', 'Kec. Magetan', '2019-06-10 04:42:07'),
('051007', 'ID', 'Kec. Plaosan', '2019-06-10 04:42:07'),
('051008', 'ID', 'Kec. Panekan', '2019-06-10 04:42:07'),
('051009', 'ID', 'Kec. Sukomoro', '2019-06-10 04:42:07'),
('051010', 'ID', 'Kec. Bendo', '2019-06-10 04:42:07'),
('051011', 'ID', 'Kec. Maospati', '2019-06-10 04:42:07'),
('051012', 'ID', 'Kec. Karangrejo', '2019-06-10 04:42:07'),
('051014', 'ID', 'Kec. Barat', '2019-06-10 04:42:07'),
('051015', 'ID', 'Kec. Kartoharjo', '2019-06-10 04:42:07'),
('051016', 'ID', 'Kec. Karas', '2019-06-10 04:42:07'),
('051017', 'ID', 'Kec. Ngariboyo', '2019-06-10 04:42:07'),
('051018', 'ID', 'Kec. Nguntoronadi', '2019-06-10 04:42:07'),
('051019', 'ID', 'Kec. Sidorejo', '2019-06-10 04:42:07'),
('051100', 'ID', 'Kab. Ponorogo', '2019-06-10 04:42:07'),
('051101', 'ID', 'Kec. Ngrayun', '2019-06-10 04:42:07'),
('051102', 'ID', 'Kec. Slahung', '2019-06-10 04:42:07'),
('051103', 'ID', 'Kec. Bungkal', '2019-06-10 04:42:07'),
('051104', 'ID', 'Kec. Sambit', '2019-06-10 04:42:07'),
('051105', 'ID', 'Kec. Sawoo', '2019-06-10 04:42:07'),
('051106', 'ID', 'Kec. Sooko', '2019-06-10 04:42:07'),
('051107', 'ID', 'Kec. Pulung', '2019-06-10 04:42:07'),
('051108', 'ID', 'Kec. Mlarak', '2019-06-10 04:42:07'),
('051109', 'ID', 'Kec. Siman', '2019-06-10 04:42:07'),
('051110', 'ID', 'Kec. Jetis', '2019-06-10 04:42:07'),
('051111', 'ID', 'Kec. Balong', '2019-06-10 04:42:07'),
('051112', 'ID', 'Kec. Kauman', '2019-06-10 04:42:07'),
('051113', 'ID', 'Kec. Jambon', '2019-06-10 04:42:07'),
('051114', 'ID', 'Kec. Badegan', '2019-06-10 04:42:07'),
('051115', 'ID', 'Kec. Sampung', '2019-06-10 04:42:07'),
('051116', 'ID', 'Kec. Sukorejo', '2019-06-10 04:42:07'),
('051117', 'ID', 'Kec. Ponorogo', '2019-06-10 04:42:07'),
('051118', 'ID', 'Kec. Babadan', '2019-06-10 04:42:07'),
('051119', 'ID', 'Kec. Jenangan', '2019-06-10 04:42:07'),
('051120', 'ID', 'Kec. Ngebel', '2019-06-10 04:42:07'),
('051121', 'ID', 'Kec. Pudak', '2019-06-10 04:42:07'),
('051200', 'ID', 'Kab. Pacitan', '2019-06-10 04:42:07'),
('051201', 'ID', 'Kec. Donorejo', '2019-06-10 04:42:07'),
('051202', 'ID', 'Kec. Punung', '2019-06-10 04:42:07'),
('051203', 'ID', 'Kec. Pringkuku', '2019-06-10 04:42:07'),
('051204', 'ID', 'Kec. Pacitan', '2019-06-10 04:42:07'),
('051205', 'ID', 'Kec. Kebon Agung', '2019-06-10 04:42:07'),
('051206', 'ID', 'Kec. Arjosari', '2019-06-10 04:42:07'),
('051207', 'ID', 'Kec. Nawangan', '2019-06-10 04:42:07'),
('051208', 'ID', 'Kec. Bandar', '2019-06-10 04:42:07'),
('051209', 'ID', 'Kec. Tegalombo', '2019-06-10 04:42:07'),
('051210', 'ID', 'Kec. Tulakan', '2019-06-10 04:42:07'),
('051211', 'ID', 'Kec. Ngadirojo', '2019-06-10 04:42:07'),
('051212', 'ID', 'Kec. Sudimoro', '2019-06-10 04:42:07'),
('051300', 'ID', 'Kab. Kediri', '2019-06-10 04:42:07'),
('051301', 'ID', 'Kec. Kras', '2019-06-10 04:42:07'),
('051302', 'ID', 'Kec. Ringinrejo', '2019-06-10 04:42:07'),
('051303', 'ID', 'Kec. Ngancar', '2019-06-10 04:42:07'),
('051304', 'ID', 'Kec. Kepung', '2019-06-10 04:42:07'),
('051305', 'ID', 'Kec. Puncu', '2019-06-10 04:42:07'),
('051306', 'ID', 'Kec. Ploso Klaten', '2019-06-10 04:42:07'),
('051307', 'ID', 'Kec. Wates', '2019-06-10 04:42:07'),
('051308', 'ID', 'Kec. Kandat', '2019-06-10 04:42:07'),
('051309', 'ID', 'Kec. Ngadiluwih', '2019-06-10 04:42:07'),
('051310', 'ID', 'Kec. Mojo', '2019-06-10 04:42:07'),
('051311', 'ID', 'Kec. Semen', '2019-06-10 04:42:07'),
('051312', 'ID', 'Kec. Banyakan', '2019-06-10 04:42:07'),
('051313', 'ID', 'Kec. Tarokan', '2019-06-10 04:42:07'),
('051314', 'ID', 'Kec. Grogol', '2019-06-10 04:42:07'),
('051315', 'ID', 'Kec. Gampengrejo', '2019-06-10 04:42:07'),
('051316', 'ID', 'Kec. Gurah', '2019-06-10 04:42:07'),
('051317', 'ID', 'Kec. Pagu', '2019-06-10 04:42:07'),
('051318', 'ID', 'Kec. Papar', '2019-06-10 04:42:07'),
('051319', 'ID', 'Kec. Plemahan', '2019-06-10 04:42:07'),
('051320', 'ID', 'Kec. Purwoasri', '2019-06-10 04:42:07'),
('051321', 'ID', 'Kec. Kunjang', '2019-06-10 04:42:07'),
('051322', 'ID', 'Kec. Pare', '2019-06-10 04:42:07'),
('051323', 'ID', 'Kec. Kandangan', '2019-06-10 04:42:07'),
('051324', 'ID', 'Kec. Kayen Kidul', '2019-06-10 04:42:07'),
('051325', 'ID', 'Kec. Ngasem', '2019-06-10 04:42:07'),
('051326', 'ID', 'Kec. Badas', '2019-06-10 04:42:07'),
('051400', 'ID', 'Kab. Nganjuk', '2019-06-10 04:42:07'),
('051401', 'ID', 'Kec. Sawahan', '2019-06-10 04:42:07'),
('051402', 'ID', 'Kec. Ngetos', '2019-06-10 04:42:07'),
('051403', 'ID', 'Kec. Berbek', '2019-06-10 04:42:07'),
('051404', 'ID', 'Kec. Loceret', '2019-06-10 04:42:07'),
('051405', 'ID', 'Kec. Pace', '2019-06-10 04:42:07'),
('051406', 'ID', 'Kec. Tanjunganom', '2019-06-10 04:42:07'),
('051407', 'ID', 'Kec. Prambon', '2019-06-10 04:42:07'),
('051408', 'ID', 'Kec. Ngronggot', '2019-06-10 04:42:07'),
('051409', 'ID', 'Kec. Kertosono', '2019-06-10 04:42:07'),
('051410', 'ID', 'Kec. Patianrowo', '2019-06-10 04:42:07'),
('051411', 'ID', 'Kec. Baron', '2019-06-10 04:42:07'),
('051412', 'ID', 'Kec. Gondang', '2019-06-10 04:42:07'),
('051413', 'ID', 'Kec. Sukomoro', '2019-06-10 04:42:07'),
('051414', 'ID', 'Kec. Nganjuk', '2019-06-10 04:42:07'),
('051415', 'ID', 'Kec. Bagor', '2019-06-10 04:42:07'),
('051416', 'ID', 'Kec. Wilangan', '2019-06-10 04:42:07'),
('051417', 'ID', 'Kec. Rejoso', '2019-06-10 04:42:07'),
('051418', 'ID', 'Kec. Ngluyu', '2019-06-10 04:42:07'),
('051419', 'ID', 'Kec. Lengkong', '2019-06-10 04:42:07'),
('051420', 'ID', 'Kec. Jatikalen', '2019-06-10 04:42:07'),
('051500', 'ID', 'Kab. Blitar', '2019-06-10 04:42:07'),
('051501', 'ID', 'Kec. Bakung', '2019-06-10 04:42:07'),
('051502', 'ID', 'Kec. Wonotirto', '2019-06-10 04:42:07'),
('051503', 'ID', 'Kec. Panggungrejo', '2019-06-10 04:42:07'),
('051504', 'ID', 'Kec. Wates', '2019-06-10 04:42:07'),
('051505', 'ID', 'Kec. Binangun', '2019-06-10 04:42:07'),
('051506', 'ID', 'Kec. Sutojayan', '2019-06-10 04:42:07'),
('051507', 'ID', 'Kec. Kademangan', '2019-06-10 04:42:07'),
('051508', 'ID', 'Kec. Kanigoro', '2019-06-10 04:42:07'),
('051509', 'ID', 'Kec. Talun', '2019-06-10 04:42:07'),
('051510', 'ID', 'Kec. Selopuro', '2019-06-10 04:42:07'),
('051511', 'ID', 'Kec. Kesamben', '2019-06-10 04:42:07'),
('051512', 'ID', 'Kec. Selorejo', '2019-06-10 04:42:07'),
('051513', 'ID', 'Kec. Doko', '2019-06-10 04:42:07'),
('051514', 'ID', 'Kec. Wlingi', '2019-06-10 04:42:07'),
('051515', 'ID', 'Kec. Gandusari', '2019-06-10 04:42:07'),
('051516', 'ID', 'Kec. Garum', '2019-06-10 04:42:07'),
('051517', 'ID', 'Kec. Nglegok', '2019-06-10 04:42:07'),
('051518', 'ID', 'Kec. Sanankulon', '2019-06-10 04:42:07'),
('051519', 'ID', 'Kec. Ponggok', '2019-06-10 04:42:07'),
('051520', 'ID', 'Kec. Srengat', '2019-06-10 04:42:07'),
('051521', 'ID', 'Kec. Wonodadi', '2019-06-10 04:42:07'),
('051522', 'ID', 'Kec. Udanawu', '2019-06-10 04:42:07'),
('051600', 'ID', 'Kab. Tulungagung', '2019-06-10 04:42:07'),
('051601', 'ID', 'Kec. Besuki', '2019-06-10 04:42:07'),
('051602', 'ID', 'Kec. Bandung', '2019-06-10 04:42:07'),
('051603', 'ID', 'Kec. Pakel', '2019-06-10 04:42:07'),
('051604', 'ID', 'Kec. Campur Darat', '2019-06-10 04:42:07'),
('051605', 'ID', 'Kec. Tanggung Gunung', '2019-06-10 04:42:07'),
('051606', 'ID', 'Kec. Kalidawir', '2019-06-10 04:42:07'),
('051607', 'ID', 'Kec. Pucang Laban', '2019-06-10 04:42:07'),
('051608', 'ID', 'Kec. Rejotangan', '2019-06-10 04:42:07'),
('051609', 'ID', 'Kec. Ngunut', '2019-06-10 04:42:07'),
('051610', 'ID', 'Kec. Sumbergempol', '2019-06-10 04:42:07'),
('051611', 'ID', 'Kec. Boyolangu', '2019-06-10 04:42:07'),
('051612', 'ID', 'Kec. Tulungagung', '2019-06-10 04:42:07'),
('051613', 'ID', 'Kec. Kedungwaru', '2019-06-10 04:42:07'),
('051614', 'ID', 'Kec. Ngantru', '2019-06-10 04:42:07'),
('051615', 'ID', 'Kec. Karangrejo', '2019-06-10 04:42:07'),
('051616', 'ID', 'Kec. Kauman', '2019-06-10 04:42:07'),
('051617', 'ID', 'Kec. Gondang', '2019-06-10 04:42:07'),
('051618', 'ID', 'Kec. Pagerwojo', '2019-06-10 04:42:07'),
('051619', 'ID', 'Kec. Sendang', '2019-06-10 04:42:07'),
('051700', 'ID', 'Kab. Trenggalek', '2019-06-10 04:42:07'),
('051701', 'ID', 'Kec. Panggul', '2019-06-10 04:42:07'),
('051702', 'ID', 'Kec. Munjungan', '2019-06-10 04:42:07'),
('051703', 'ID', 'Kec. Watulimo', '2019-06-10 04:42:07'),
('051704', 'ID', 'Kec. Kampak', '2019-06-10 04:42:07');
INSERT INTO `dikti_wilayah` (`id_wilayah`, `id_negara`, `nama_wilayah`, `dikti_wilayah_timestamp`) VALUES
('051705', 'ID', 'Kec. Dongko', '2019-06-10 04:42:07'),
('051706', 'ID', 'Kec. Pule', '2019-06-10 04:42:07'),
('051707', 'ID', 'Kec. Karangan', '2019-06-10 04:42:07'),
('051708', 'ID', 'Kec. Gandusari', '2019-06-10 04:42:07'),
('051709', 'ID', 'Kec. Durenan', '2019-06-10 04:42:07'),
('051710', 'ID', 'Kec. Pogalan', '2019-06-10 04:42:07'),
('051711', 'ID', 'Kec. Trenggalek', '2019-06-10 04:42:07'),
('051712', 'ID', 'Kec. Tugu', '2019-06-10 04:42:07'),
('051713', 'ID', 'Kec. Bendungan', '2019-06-10 04:42:07'),
('051714', 'ID', 'Kec. Suruh', '2019-06-10 04:42:07'),
('051800', 'ID', 'Kab. Malang', '2019-06-10 04:42:07'),
('051801', 'ID', 'Kec. Donomulyo', '2019-06-10 04:42:07'),
('051802', 'ID', 'Kec. Kalipare', '2019-06-10 04:42:07'),
('051803', 'ID', 'Kec. Pagak', '2019-06-10 04:42:07'),
('051804', 'ID', 'Kec. Bantur', '2019-06-10 04:42:07'),
('051805', 'ID', 'Kec. Gedangan', '2019-06-10 04:42:07'),
('051806', 'ID', 'Kec. Sumber Manjing Wetan', '2019-06-10 04:42:07'),
('051807', 'ID', 'Kec. Dampit', '2019-06-10 04:42:07'),
('051808', 'ID', 'Kec. Tirto Yudo', '2019-06-10 04:42:07'),
('051809', 'ID', 'Kec. Ampelgading', '2019-06-10 04:42:07'),
('051810', 'ID', 'Kec. Poncokusumo', '2019-06-10 04:42:07'),
('051811', 'ID', 'Kec. Wajak', '2019-06-10 04:42:07'),
('051812', 'ID', 'Kec. Turen', '2019-06-10 04:42:07'),
('051813', 'ID', 'Kec. Pagelaran', '2019-06-10 04:42:07'),
('051814', 'ID', 'Kec. Gondanglegi', '2019-06-10 04:42:07'),
('051815', 'ID', 'Kec. Bululawang', '2019-06-10 04:42:07'),
('051816', 'ID', 'Kec. Kepanjen', '2019-06-10 04:42:07'),
('051817', 'ID', 'Kec. Sumberpucung', '2019-06-10 04:42:07'),
('051818', 'ID', 'Kec. Kromengan', '2019-06-10 04:42:07'),
('051819', 'ID', 'Kec. Wonosari', '2019-06-10 04:42:07'),
('051820', 'ID', 'Kec. Ngajum', '2019-06-10 04:42:07'),
('051821', 'ID', 'Kec. Wagir', '2019-06-10 04:42:07'),
('051822', 'ID', 'Kec. Pakisaji', '2019-06-10 04:42:07'),
('051823', 'ID', 'Kec. Tajinan', '2019-06-10 04:42:07'),
('051824', 'ID', 'Kec. Tumpang', '2019-06-10 04:42:07'),
('051825', 'ID', 'Kec. Pakis', '2019-06-10 04:42:07'),
('051826', 'ID', 'Kec. Jabung', '2019-06-10 04:42:07'),
('051827', 'ID', 'Kec. Lawang', '2019-06-10 04:42:07'),
('051828', 'ID', 'Kec. Singosari', '2019-06-10 04:42:07'),
('051829', 'ID', 'Kec. Karangploso', '2019-06-10 04:42:07'),
('051830', 'ID', 'Kec. Dau', '2019-06-10 04:42:07'),
('051831', 'ID', 'Kec. Pujon', '2019-06-10 04:42:07'),
('051832', 'ID', 'Kec. Ngantang', '2019-06-10 04:42:07'),
('051833', 'ID', 'Kec. Kasembon', '2019-06-10 04:42:07'),
('051900', 'ID', 'Kab. Pasuruan', '2019-06-10 04:42:07'),
('051901', 'ID', 'Kec. Purwodadi', '2019-06-10 04:42:07'),
('051902', 'ID', 'Kec. Tutur', '2019-06-10 04:42:07'),
('051903', 'ID', 'Kec. Puspo', '2019-06-10 04:42:07'),
('051904', 'ID', 'Kec. Tosari', '2019-06-10 04:42:07'),
('051905', 'ID', 'Kec. Lumbang', '2019-06-10 04:42:07'),
('051906', 'ID', 'Kec. Pasrepan', '2019-06-10 04:42:07'),
('051907', 'ID', 'Kec. Kejayan', '2019-06-10 04:42:07'),
('051908', 'ID', 'Kec. Wonorejo', '2019-06-10 04:42:07'),
('051909', 'ID', 'Kec. Purwosari', '2019-06-10 04:42:07'),
('051910', 'ID', 'Kec. Prigen', '2019-06-10 04:42:07'),
('051911', 'ID', 'Kec. Sukorejo', '2019-06-10 04:42:07'),
('051912', 'ID', 'Kec. Pandaan', '2019-06-10 04:42:07'),
('051913', 'ID', 'Kec. Gempol', '2019-06-10 04:42:07'),
('051914', 'ID', 'Kec. Beji', '2019-06-10 04:42:07'),
('051915', 'ID', 'Kec. Bangil', '2019-06-10 04:42:07'),
('051916', 'ID', 'Kec. Rembang', '2019-06-10 04:42:07'),
('051917', 'ID', 'Kec. Kraton', '2019-06-10 04:42:07'),
('051918', 'ID', 'Kec. Pohjentrek', '2019-06-10 04:42:07'),
('051919', 'ID', 'Kec. Gondang Wetan', '2019-06-10 04:42:07'),
('051920', 'ID', 'Kec. Rejoso', '2019-06-10 04:42:07'),
('051921', 'ID', 'Kec. Winongan', '2019-06-10 04:42:07'),
('051922', 'ID', 'Kec. Grati', '2019-06-10 04:42:07'),
('051923', 'ID', 'Kec. Lekok', '2019-06-10 04:42:07'),
('051924', 'ID', 'Kec. Nguling', '2019-06-10 04:42:07'),
('052000', 'ID', 'Kab. Probolinggo', '2019-06-10 04:42:07'),
('052001', 'ID', 'Kec. Sukapura', '2019-06-10 04:42:07'),
('052002', 'ID', 'Kec. Sumber', '2019-06-10 04:42:07'),
('052003', 'ID', 'Kec. Kuripan', '2019-06-10 04:42:07'),
('052004', 'ID', 'Kec. Bantaran', '2019-06-10 04:42:07'),
('052005', 'ID', 'Kec. Leces', '2019-06-10 04:42:07'),
('052006', 'ID', 'Kec. Tegal Siwalan', '2019-06-10 04:42:07'),
('052007', 'ID', 'Kec. Banyu Anyar', '2019-06-10 04:42:07'),
('052008', 'ID', 'Kec. Tiris', '2019-06-10 04:42:07'),
('052009', 'ID', 'Kec. Krucil', '2019-06-10 04:42:07'),
('052010', 'ID', 'Kec. Gading', '2019-06-10 04:42:07'),
('052011', 'ID', 'Kec. Pakuniran', '2019-06-10 04:42:07'),
('052012', 'ID', 'Kec. Kota Anyar', '2019-06-10 04:42:07'),
('052013', 'ID', 'Kec. Paiton', '2019-06-10 04:42:07'),
('052014', 'ID', 'Kec. Besuk', '2019-06-10 04:42:07'),
('052015', 'ID', 'Kec. Kraksaan', '2019-06-10 04:42:07'),
('052016', 'ID', 'Kec. Krejengan', '2019-06-10 04:42:07'),
('052017', 'ID', 'Kec. Pajarakan', '2019-06-10 04:42:07'),
('052018', 'ID', 'Kec. Maron', '2019-06-10 04:42:07'),
('052019', 'ID', 'Kec. Gending', '2019-06-10 04:42:07'),
('052020', 'ID', 'Kec. Dringu', '2019-06-10 04:42:07'),
('052021', 'ID', 'Kec. Wonomerto', '2019-06-10 04:42:07'),
('052022', 'ID', 'Kec. Lumbang', '2019-06-10 04:42:07'),
('052023', 'ID', 'Kec. Tongas', '2019-06-10 04:42:07'),
('052024', 'ID', 'Kec. Sumberasih', '2019-06-10 04:42:07'),
('052100', 'ID', 'Kab. Lumajang', '2019-06-10 04:42:07'),
('052101', 'ID', 'Kec. Tempursari', '2019-06-10 04:42:07'),
('052102', 'ID', 'Kec. Pronojiwo', '2019-06-10 04:42:07'),
('052103', 'ID', 'Kec. Candipuro', '2019-06-10 04:42:07'),
('052104', 'ID', 'Kec. Pasirian', '2019-06-10 04:42:07'),
('052105', 'ID', 'Kec. Tempeh', '2019-06-10 04:42:07'),
('052106', 'ID', 'Kec. Kunir', '2019-06-10 04:42:07'),
('052107', 'ID', 'Kec. Yosowilangun', '2019-06-10 04:42:07'),
('052108', 'ID', 'Kec. Rowokangkung', '2019-06-10 04:42:07'),
('052109', 'ID', 'Kec. Tekung', '2019-06-10 04:42:07'),
('052110', 'ID', 'Kec. Lumajang', '2019-06-10 04:42:07'),
('052111', 'ID', 'Kec. Pasrujambe', '2019-06-10 04:42:07'),
('052112', 'ID', 'Kec. Senduro', '2019-06-10 04:42:07'),
('052113', 'ID', 'Kec. Padang', '2019-06-10 04:42:07'),
('052114', 'ID', 'Kec. Sukodono', '2019-06-10 04:42:07'),
('052115', 'ID', 'Kec. Jatiroto', '2019-06-10 04:42:07'),
('052116', 'ID', 'Kec. Randuagung', '2019-06-10 04:42:07'),
('052117', 'ID', 'Kec. Kedungjajang', '2019-06-10 04:42:07'),
('052118', 'ID', 'Kec. Gucialit', '2019-06-10 04:42:07'),
('052119', 'ID', 'Kec. Klakah', '2019-06-10 04:42:07'),
('052120', 'ID', 'Kec. Ranuyoso', '2019-06-10 04:42:07'),
('052121', 'ID', 'Kec. Sumbersuko', '2019-06-10 04:42:07'),
('052200', 'ID', 'Kab. Bondowoso', '2019-06-10 04:42:07'),
('052201', 'ID', 'Kec. Maesan', '2019-06-10 04:42:07'),
('052202', 'ID', 'Kec. Grujugan', '2019-06-10 04:42:07'),
('052203', 'ID', 'Kec. Tamanan', '2019-06-10 04:42:07'),
('052204', 'ID', 'Kec. Pujer', '2019-06-10 04:42:07'),
('052205', 'ID', 'Kec. Tlogosari', '2019-06-10 04:42:07'),
('052206', 'ID', 'Kec. Sukosari', '2019-06-10 04:42:07'),
('052207', 'ID', 'Kec. Tapen', '2019-06-10 04:42:07'),
('052208', 'ID', 'Kec. Wonosari', '2019-06-10 04:42:07'),
('052209', 'ID', 'Kec. Tenggarang', '2019-06-10 04:42:07'),
('052210', 'ID', 'Kec. Bondowoso', '2019-06-10 04:42:07'),
('052211', 'ID', 'Kec. Curahdami', '2019-06-10 04:42:07'),
('052212', 'ID', 'Kec. Pakem', '2019-06-10 04:42:07'),
('052213', 'ID', 'Kec. Wringin', '2019-06-10 04:42:07'),
('052214', 'ID', 'Kec. Tegalampel', '2019-06-10 04:42:07'),
('052215', 'ID', 'Kec. Klabang', '2019-06-10 04:42:07'),
('052216', 'ID', 'Kec. Prajekan', '2019-06-10 04:42:07'),
('052217', 'ID', 'Kec. Cermee', '2019-06-10 04:42:07'),
('052218', 'ID', 'Kec. Binakal', '2019-06-10 04:42:07'),
('052219', 'ID', 'Kec. Sumber Wringin', '2019-06-10 04:42:07'),
('052220', 'ID', 'Kec. Sempol', '2019-06-10 04:42:07'),
('052221', 'ID', 'Kec. Jambesari Darus Sholah', '2019-06-10 04:42:07'),
('052222', 'ID', 'Kec. Taman Krocok', '2019-06-10 04:42:07'),
('052223', 'ID', 'Kec. Botolinggo', '2019-06-10 04:42:07'),
('052300', 'ID', 'Kab. Situbondo', '2019-06-10 04:42:07'),
('052301', 'ID', 'Kec. Sumber Malang', '2019-06-10 04:42:07'),
('052302', 'ID', 'Kec. Jatibanteng', '2019-06-10 04:42:07'),
('052303', 'ID', 'Kec. Banyuglugur', '2019-06-10 04:42:07'),
('052304', 'ID', 'Kec. Besuki', '2019-06-10 04:42:07'),
('052305', 'ID', 'Kec. Suboh', '2019-06-10 04:42:07'),
('052306', 'ID', 'Kec. Mlandingan', '2019-06-10 04:42:07'),
('052307', 'ID', 'Kec. Bungatan', '2019-06-10 04:42:07'),
('052308', 'ID', 'Kec. Kendit', '2019-06-10 04:42:07'),
('052309', 'ID', 'Kec. Panarukan', '2019-06-10 04:42:07'),
('052310', 'ID', 'Kec. Situbondo', '2019-06-10 04:42:07'),
('052311', 'ID', 'Kec. Mangaran', '2019-06-10 04:42:07'),
('052312', 'ID', 'Kec. Panji', '2019-06-10 04:42:07'),
('052313', 'ID', 'Kec. Kapongan', '2019-06-10 04:42:07'),
('052314', 'ID', 'Kec. Arjasa', '2019-06-10 04:42:07'),
('052315', 'ID', 'Kec. Jangkar', '2019-06-10 04:42:07'),
('052316', 'ID', 'Kec. Asembagus', '2019-06-10 04:42:07'),
('052317', 'ID', 'Kec. Banyuputih', '2019-06-10 04:42:07'),
('052400', 'ID', 'Kab. Jember', '2019-06-10 04:42:07'),
('052401', 'ID', 'Kec. Kencong', '2019-06-10 04:42:07'),
('052402', 'ID', 'Kec. Gumuk Mas', '2019-06-10 04:42:07'),
('052403', 'ID', 'Kec. Puger', '2019-06-10 04:42:07'),
('052404', 'ID', 'Kec. Wuluhan', '2019-06-10 04:42:07'),
('052405', 'ID', 'Kec. Ambulu', '2019-06-10 04:42:07'),
('052406', 'ID', 'Kec. Tempurejo', '2019-06-10 04:42:07'),
('052407', 'ID', 'Kec. Silo', '2019-06-10 04:42:07'),
('052408', 'ID', 'Kec. Mayang', '2019-06-10 04:42:07'),
('052409', 'ID', 'Kec. Mumbulsari', '2019-06-10 04:42:07'),
('052410', 'ID', 'Kec. Jenggawah', '2019-06-10 04:42:07'),
('052411', 'ID', 'Kec. Ajung', '2019-06-10 04:42:07'),
('052412', 'ID', 'Kec. Rambipuji', '2019-06-10 04:42:07'),
('052413', 'ID', 'Kec. Balung', '2019-06-10 04:42:07'),
('052414', 'ID', 'Kec. Umbulsari', '2019-06-10 04:42:07'),
('052415', 'ID', 'Kec. Semboro', '2019-06-10 04:42:07'),
('052416', 'ID', 'Kec. Jombang', '2019-06-10 04:42:07'),
('052417', 'ID', 'Kec. Sumberbaru', '2019-06-10 04:42:07'),
('052418', 'ID', 'Kec. Tanggul', '2019-06-10 04:42:07'),
('052419', 'ID', 'Kec. Bangsalsari', '2019-06-10 04:42:07'),
('052420', 'ID', 'Kec. Panti', '2019-06-10 04:42:07'),
('052421', 'ID', 'Kec. Sukorambi', '2019-06-10 04:42:07'),
('052422', 'ID', 'Kec. Arjasa', '2019-06-10 04:42:07'),
('052423', 'ID', 'Kec. Pakusari', '2019-06-10 04:42:07'),
('052424', 'ID', 'Kec. Kalisat', '2019-06-10 04:42:07'),
('052425', 'ID', 'Kec. Ledok Ombo', '2019-06-10 04:42:07'),
('052426', 'ID', 'Kec. Sumberjambe', '2019-06-10 04:42:07'),
('052427', 'ID', 'Kec. Sukowono', '2019-06-10 04:42:07'),
('052428', 'ID', 'Kec. Jelbuk', '2019-06-10 04:42:07'),
('052429', 'ID', 'Kec. Kaliwates', '2019-06-10 04:42:07'),
('052430', 'ID', 'Kec. Sumbersari', '2019-06-10 04:42:07'),
('052431', 'ID', 'Kec. Patrang', '2019-06-10 04:42:07'),
('052500', 'ID', 'Kab. Banyuwangi', '2019-06-10 04:42:07'),
('052501', 'ID', 'Kec. Pesanggaran', '2019-06-10 04:42:07'),
('052502', 'ID', 'Kec. Bangorejo', '2019-06-10 04:42:07'),
('052503', 'ID', 'Kec. Purwoharjo', '2019-06-10 04:42:07'),
('052504', 'ID', 'Kec. Tegaldlimo', '2019-06-10 04:42:07'),
('052505', 'ID', 'Kec. Muncar', '2019-06-10 04:42:07'),
('052506', 'ID', 'Kec. Cluring', '2019-06-10 04:42:07'),
('052507', 'ID', 'Kec. Gambiran', '2019-06-10 04:42:07'),
('052508', 'ID', 'Kec. Glenmore', '2019-06-10 04:42:07'),
('052509', 'ID', 'Kec. Kalibaru', '2019-06-10 04:42:07'),
('052510', 'ID', 'Kec. Genteng', '2019-06-10 04:42:07'),
('052511', 'ID', 'Kec. Srono', '2019-06-10 04:42:07'),
('052512', 'ID', 'Kec. Rogojampi', '2019-06-10 04:42:07'),
('052513', 'ID', 'Kec. Kabat', '2019-06-10 04:42:07'),
('052514', 'ID', 'Kec. Singojuruh', '2019-06-10 04:42:07'),
('052515', 'ID', 'Kec. Sempu', '2019-06-10 04:42:07'),
('052516', 'ID', 'Kec. Songgon', '2019-06-10 04:42:07'),
('052517', 'ID', 'Kec. Glagah', '2019-06-10 04:42:07'),
('052518', 'ID', 'Kec. Banyuwangi', '2019-06-10 04:42:07'),
('052519', 'ID', 'Kec. Giri', '2019-06-10 04:42:07'),
('052520', 'ID', 'Kec. Kalipuro', '2019-06-10 04:42:07'),
('052521', 'ID', 'Kec. Wongsorejo', '2019-06-10 04:42:07'),
('052522', 'ID', 'Kec. Licin', '2019-06-10 04:42:07'),
('052523', 'ID', 'Kec. Tegalsari', '2019-06-10 04:42:07'),
('052524', 'ID', 'Kec. Siliragung', '2019-06-10 04:42:07'),
('052525', 'ID', 'Kec. Blimbingsari', '2020-02-10 03:07:43'),
('052600', 'ID', 'Kab. Pamekasan', '2019-06-10 04:42:07'),
('052601', 'ID', 'Kec. Tlanakan', '2019-06-10 04:42:07'),
('052602', 'ID', 'Kec. Pademawu', '2019-06-10 04:42:07'),
('052603', 'ID', 'Kec. Galis', '2019-06-10 04:42:07'),
('052604', 'ID', 'Kec. Larangan', '2019-06-10 04:42:07'),
('052605', 'ID', 'Kec. Pamekasan', '2019-06-10 04:42:07'),
('052606', 'ID', 'Kec. Proppo', '2019-06-10 04:42:07'),
('052607', 'ID', 'Kec. Palengaan', '2019-06-10 04:42:07'),
('052608', 'ID', 'Kec. Pegantenan', '2019-06-10 04:42:07'),
('052609', 'ID', 'Kec. Kadur', '2019-06-10 04:42:07'),
('052610', 'ID', 'Kec. Pakong', '2019-06-10 04:42:07'),
('052611', 'ID', 'Kec. Waru', '2019-06-10 04:42:07'),
('052612', 'ID', 'Kec. Batu Marmer', '2019-06-10 04:42:07'),
('052613', 'ID', 'Kec. Pasean', '2019-06-10 04:42:07'),
('052700', 'ID', 'Kab. Sampang', '2019-06-10 04:42:07'),
('052701', 'ID', 'Kec. Sreseh', '2019-06-10 04:42:07'),
('052702', 'ID', 'Kec. Torjun', '2019-06-10 04:42:07'),
('052703', 'ID', 'Kec. Sampang', '2019-06-10 04:42:07'),
('052704', 'ID', 'Kec. Camplong', '2019-06-10 04:42:07'),
('052705', 'ID', 'Kec. Omben', '2019-06-10 04:42:07'),
('052706', 'ID', 'Kec. Kedungdung', '2019-06-10 04:42:07'),
('052707', 'ID', 'Kec. Jrengik', '2019-06-10 04:42:07'),
('052708', 'ID', 'Kec. Tambelangan', '2019-06-10 04:42:07'),
('052709', 'ID', 'Kec. Banyuates', '2019-06-10 04:42:07'),
('052710', 'ID', 'Kec. Robatal', '2019-06-10 04:42:07'),
('052711', 'ID', 'Kec. Ketapang', '2019-06-10 04:42:07'),
('052712', 'ID', 'Kec. Sokobanah', '2019-06-10 04:42:07'),
('052713', 'ID', 'Kec. Karangpenang', '2019-06-10 04:42:07'),
('052714', 'ID', 'Kec. Pangarengan', '2019-06-10 04:42:07'),
('052800', 'ID', 'Kab. Sumenep', '2019-06-10 04:42:07'),
('052801', 'ID', 'Kec. Pragaan', '2019-06-10 04:42:07'),
('052802', 'ID', 'Kec. Bluto', '2019-06-10 04:42:07'),
('052803', 'ID', 'Kec. Saronggi', '2019-06-10 04:42:07'),
('052804', 'ID', 'Kec. Giligenteng', '2019-06-10 04:42:07'),
('052805', 'ID', 'Kec. Talango', '2019-06-10 04:42:07'),
('052806', 'ID', 'Kec. Kalianget', '2019-06-10 04:42:07'),
('052807', 'ID', 'Kec. Kota Sumenep', '2019-06-10 04:42:07'),
('052808', 'ID', 'Kec. Lenteng', '2019-06-10 04:42:07'),
('052809', 'ID', 'Kec. Ganding', '2019-06-10 04:42:07'),
('052810', 'ID', 'Kec. Guluk Guluk', '2019-06-10 04:42:07'),
('052811', 'ID', 'Kec. Pasongsongan', '2019-06-10 04:42:07'),
('052812', 'ID', 'Kec. Ambunten', '2019-06-10 04:42:07'),
('052813', 'ID', 'Kec. Rubaru', '2019-06-10 04:42:07'),
('052814', 'ID', 'Kec. Dasuk', '2019-06-10 04:42:07'),
('052815', 'ID', 'Kec. Manding', '2019-06-10 04:42:07'),
('052816', 'ID', 'Kec. Batuputih', '2019-06-10 04:42:07'),
('052817', 'ID', 'Kec. Gapura', '2019-06-10 04:42:07'),
('052818', 'ID', 'Kec. Batang Batang', '2019-06-10 04:42:07'),
('052819', 'ID', 'Kec. Dungkek', '2019-06-10 04:42:07'),
('052820', 'ID', 'Kec. Nonggunong', '2019-06-10 04:42:07'),
('052821', 'ID', 'Kec. Gayam', '2019-06-10 04:42:07'),
('052822', 'ID', 'Kec. Ra As', '2019-06-10 04:42:07'),
('052823', 'ID', 'Kec. Sapeken', '2019-06-10 04:42:07'),
('052824', 'ID', 'Kec. Arjasa', '2019-06-10 04:42:07'),
('052825', 'ID', 'Kec. Masalembu', '2019-06-10 04:42:07'),
('052827', 'ID', 'Kec. Batuan', '2019-06-10 04:42:07'),
('052828', 'ID', 'Kec. Kangayan', '2019-06-10 04:42:07'),
('052900', 'ID', 'Kab. Bangkalan', '2019-06-10 04:42:07'),
('052901', 'ID', 'Kec. Kamal', '2019-06-10 04:42:07'),
('052902', 'ID', 'Kec. Labang', '2019-06-10 04:42:07'),
('052903', 'ID', 'Kec. Kwanyar', '2019-06-10 04:42:07'),
('052904', 'ID', 'Kec. Modung', '2019-06-10 04:42:07'),
('052905', 'ID', 'Kec. Blega', '2019-06-10 04:42:07'),
('052906', 'ID', 'Kec. Konang', '2019-06-10 04:42:07'),
('052907', 'ID', 'Kec. Galis', '2019-06-10 04:42:07'),
('052908', 'ID', 'Kec. Tanah Merah', '2019-06-10 04:42:07'),
('052909', 'ID', 'Kec. Tragah', '2019-06-10 04:42:07'),
('052910', 'ID', 'Kec. Socah', '2019-06-10 04:42:07'),
('052911', 'ID', 'Kec. Bangkalan', '2019-06-10 04:42:07'),
('052912', 'ID', 'Kec. Burneh', '2019-06-10 04:42:07'),
('052913', 'ID', 'Kec. Arosbaya', '2019-06-10 04:42:07'),
('052914', 'ID', 'Kec. Geger', '2019-06-10 04:42:07'),
('052915', 'ID', 'Kec. Kokop', '2019-06-10 04:42:07'),
('052916', 'ID', 'Kec. Tanjungbumi', '2019-06-10 04:42:07'),
('052917', 'ID', 'Kec. Sepulu', '2019-06-10 04:42:07'),
('052918', 'ID', 'Kec. Klampis', '2019-06-10 04:42:07'),
('056000', 'ID', 'Kota Surabaya', '2019-06-10 04:42:07'),
('056001', 'ID', 'Kec. Karang Pilang', '2019-06-10 04:42:07'),
('056002', 'ID', 'Kec. Jambangan', '2019-06-10 04:42:07'),
('056003', 'ID', 'Kec. Gayungan', '2019-06-10 04:42:07'),
('056004', 'ID', 'Kec. Wonocolo', '2019-06-10 04:42:07'),
('056005', 'ID', 'Kec. Tenggilis Mejoyo', '2019-06-10 04:42:07'),
('056006', 'ID', 'Kec. Gununganyar', '2019-06-10 04:42:07'),
('056007', 'ID', 'Kec. Rungkut', '2019-06-10 04:42:07'),
('056008', 'ID', 'Kec. Sukolilo', '2019-06-10 04:42:07'),
('056009', 'ID', 'Kec. Mulyorejo', '2019-06-10 04:42:07'),
('056010', 'ID', 'Kec. Gubeng', '2019-06-10 04:42:07'),
('056011', 'ID', 'Kec. Wonokromo', '2019-06-10 04:42:07'),
('056012', 'ID', 'Kec. Dukuh Pakis', '2019-06-10 04:42:07'),
('056013', 'ID', 'Kec. Wiyung', '2019-06-10 04:42:07'),
('056014', 'ID', 'Kec. Lakarsantri', '2019-06-10 04:42:07'),
('056015', 'ID', 'Kec. Tandes', '2019-06-10 04:42:07'),
('056016', 'ID', 'Kec. Sukomanunggal', '2019-06-10 04:42:07'),
('056017', 'ID', 'Kec. Sawahan', '2019-06-10 04:42:07'),
('056018', 'ID', 'Kec. Tegal Sari', '2019-06-10 04:42:07'),
('056019', 'ID', 'Kec. Genteng', '2019-06-10 04:42:07'),
('056020', 'ID', 'Kec. Tambaksari', '2019-06-10 04:42:07'),
('056021', 'ID', 'Kec. Kenjeran', '2019-06-10 04:42:07'),
('056022', 'ID', 'Kec. Simokerto', '2019-06-10 04:42:07'),
('056023', 'ID', 'Kec. Semampir', '2019-06-10 04:42:07'),
('056024', 'ID', 'Kec. Pabean Cantian', '2019-06-10 04:42:07'),
('056025', 'ID', 'Kec. Bubutan', '2019-06-10 04:42:07'),
('056026', 'ID', 'Kec. Krembangan', '2019-06-10 04:42:07'),
('056027', 'ID', 'Kec. Asemrowo', '2019-06-10 04:42:07'),
('056028', 'ID', 'Kec. Benowo', '2019-06-10 04:42:07'),
('056029', 'ID', 'Kec. Bulak', '2019-06-10 04:42:07'),
('056030', 'ID', 'Kec. Pakal', '2019-06-10 04:42:07'),
('056031', 'ID', 'Kec. Sambi Kerep', '2019-06-10 04:42:07'),
('056100', 'ID', 'Kota Malang', '2019-06-10 04:42:07'),
('056101', 'ID', 'Kec. Kedungkandang', '2019-06-10 04:42:07'),
('056102', 'ID', 'Kec. Sukun', '2019-06-10 04:42:07'),
('056103', 'ID', 'Kec. Klojen', '2019-06-10 04:42:07'),
('056104', 'ID', 'Kec. Blimbing', '2019-06-10 04:42:07'),
('056105', 'ID', 'Kec. Lowokwaru', '2019-06-10 04:42:07'),
('056200', 'ID', 'Kota Madiun', '2019-06-10 04:42:07'),
('056201', 'ID', 'Kec. Manguharjo', '2019-06-10 04:42:07'),
('056202', 'ID', 'Kec. Taman', '2019-06-10 04:42:07'),
('056203', 'ID', 'Kec. Kartoharjo', '2019-06-10 04:42:07'),
('056300', 'ID', 'Kota Kediri', '2019-06-10 04:42:07'),
('056301', 'ID', 'Kec. Mojoroto', '2019-06-10 04:42:07'),
('056302', 'ID', 'Kec. Kota Kediri', '2019-06-10 04:42:07'),
('056303', 'ID', 'Kec. Pesantren', '2019-06-10 04:42:07'),
('056400', 'ID', 'Kota Mojokerto', '2019-06-10 04:42:07'),
('056401', 'ID', 'Kec. Prajurit Kulon', '2019-06-10 04:42:07'),
('056402', 'ID', 'Kec. Magersari', '2019-06-10 04:42:07'),
('056403', 'ID', 'Kec. Kranggan', '2020-02-10 03:07:43'),
('056500', 'ID', 'Kota Blitar', '2019-06-10 04:42:07'),
('056501', 'ID', 'Kec. Sukorejo', '2019-06-10 04:42:07'),
('056502', 'ID', 'Kec. Kepanjen Kidul', '2019-06-10 04:42:07'),
('056503', 'ID', 'Kec. Sanan Wetan', '2019-06-10 04:42:07'),
('056600', 'ID', 'Kota Pasuruan', '2019-06-10 04:42:07'),
('056601', 'ID', 'Kec. Gadingrejo', '2019-06-10 04:42:07'),
('056602', 'ID', 'Kec. Purworejo', '2019-06-10 04:42:07'),
('056603', 'ID', 'Kec. Bugul Kidul', '2019-06-10 04:42:07'),
('056604', 'ID', 'Panggungrejo', '2019-06-10 04:42:07'),
('056700', 'ID', 'Kota Probolinggo', '2019-06-10 04:42:07'),
('056701', 'ID', 'Kec. Kademangan', '2019-06-10 04:42:07'),
('056702', 'ID', 'Kec. Wonoasih', '2019-06-10 04:42:07'),
('056703', 'ID', 'Kec. Mayangan', '2019-06-10 04:42:07'),
('056704', 'ID', 'Kec. Kedopok', '2019-06-10 04:42:07'),
('056705', 'ID', 'Kec. Kanigaran', '2019-06-10 04:42:07'),
('056800', 'ID', 'Kota Batu', '2019-06-10 04:42:07'),
('056801', 'ID', 'Kec. Batu', '2019-06-10 04:42:07'),
('056802', 'ID', 'Kec. Junrejo', '2019-06-10 04:42:07'),
('056803', 'ID', 'Kec. Bumiaji', '2019-06-10 04:42:07'),
('060000', 'ID', 'Prop. Aceh', '2019-06-10 04:42:07'),
('060100', 'ID', 'Kab. Aceh Besar', '2019-06-10 04:42:07'),
('060101', 'ID', 'Kec. Lhoong', '2019-06-10 04:42:07'),
('060102', 'ID', 'Kec. Lho Nga', '2019-06-10 04:42:07'),
('060103', 'ID', 'Kec. Indrapuri', '2019-06-10 04:42:07'),
('060104', 'ID', 'Kec. Seulimeum', '2019-06-10 04:42:07'),
('060105', 'ID', 'Kec. Mesjid Raya', '2019-06-10 04:42:07'),
('060106', 'ID', 'Kec. Darussalam', '2019-06-10 04:42:07'),
('060107', 'ID', 'Kec. Kuta Baro', '2019-06-10 04:42:07'),
('060108', 'ID', 'Kec. Montasik', '2019-06-10 04:42:07'),
('060109', 'ID', 'Kec. Ingin Jaya', '2019-06-10 04:42:07'),
('060110', 'ID', 'Kec. Suka Makmur', '2019-06-10 04:42:07'),
('060111', 'ID', 'Kec. Darul Imarah', '2019-06-10 04:42:07'),
('060112', 'ID', 'Kec. Peukan Bada', '2019-06-10 04:42:07'),
('060113', 'ID', 'Kec. Pulo Aceh', '2019-06-10 04:42:07'),
('060114', 'ID', 'Kec. Leupung', '2019-06-10 04:42:07'),
('060115', 'ID', 'Kec. Kuta Malaka', '2019-06-10 04:42:07'),
('060116', 'ID', 'Kec. Leumbah Seulewah', '2019-06-10 04:42:07'),
('060117', 'ID', 'Kec. Kota Jantho', '2019-06-10 04:42:07'),
('060118', 'ID', 'Kec. Baitussalam', '2019-06-10 04:42:07'),
('060119', 'ID', 'Kec. Krung Barona Jaya', '2019-06-10 04:42:07'),
('060120', 'ID', 'Kec. Simpang Tiga', '2019-06-10 04:42:07'),
('060121', 'ID', 'Kec. Kuta Cot Glie', '2019-06-10 04:42:07'),
('060122', 'ID', 'Kec. Darul Kamal', '2019-06-10 04:42:07'),
('060123', 'ID', 'Kec. Blang Bintang', '2019-06-10 04:42:07'),
('060200', 'ID', 'Kab. Pidie', '2019-06-10 04:42:07'),
('060201', 'ID', 'Kec. Geumpang', '2019-06-10 04:42:07'),
('060207', 'ID', 'Kec. Glumpang Tiga', '2019-06-10 04:42:07'),
('060208', 'ID', 'Kec. Mutiara', '2019-06-10 04:42:07'),
('060209', 'ID', 'Kec. Tiro/Truseb', '2019-06-10 04:42:07'),
('060210', 'ID', 'Kec. Tangse', '2019-06-10 04:42:07'),
('060212', 'ID', 'Kec. Sakti', '2019-06-10 04:42:07'),
('060213', 'ID', 'Kec. Mila', '2019-06-10 04:42:07'),
('060214', 'ID', 'Kec. Padang Tiji', '2019-06-10 04:42:07'),
('060215', 'ID', 'Kec. Delima', '2019-06-10 04:42:07'),
('060216', 'ID', 'Kec. Indrajaya', '2019-06-10 04:42:07'),
('060217', 'ID', 'Kec. Peukan Baru', '2019-06-10 04:42:07'),
('060218', 'ID', 'Kec. Kembang Tanjung', '2019-06-10 04:42:07'),
('060219', 'ID', 'Kec. Simpang Tiga', '2019-06-10 04:42:07'),
('060220', 'ID', 'Kec. Kota Sigli', '2019-06-10 04:42:07'),
('060221', 'ID', 'Kec. Pidie', '2019-06-10 04:42:07'),
('060222', 'ID', 'Kec. Batee', '2019-06-10 04:42:07'),
('060223', 'ID', 'Kec. Muara Tiga', '2019-06-10 04:42:07'),
('060224', 'ID', 'Kec. Mane', '2019-06-10 04:42:07'),
('060227', 'ID', 'Kec. Grong-Grong', '2019-06-10 04:42:07'),
('060228', 'ID', 'Kec. Mutiara Timur', '2019-06-10 04:42:07'),
('060230', 'ID', 'Kec. Glupang Baro', '2019-06-10 04:42:07'),
('060231', 'ID', 'Kec. Keumala', '2019-06-10 04:42:07'),
('060232', 'ID', 'Kec. Titeuae', '2019-06-10 04:42:07'),
('060300', 'ID', 'Kab. Aceh Utara', '2019-06-10 04:42:07'),
('060301', 'ID', 'Kec. Sawang', '2019-06-10 04:42:07'),
('060302', 'ID', 'Kec. Nisam', '2019-06-10 04:42:07'),
('060303', 'ID', 'Kec. Kuta Makmur', '2019-06-10 04:42:07'),
('060304', 'ID', 'Kec. Syamtalira Bayu', '2019-06-10 04:42:07'),
('060305', 'ID', 'Kec. Meurah Mulia', '2019-06-10 04:42:07'),
('060306', 'ID', 'Kec. Matangkuli', '2019-06-10 04:42:07'),
('060307', 'ID', 'Kec. Cot Girek', '2019-06-10 04:42:07'),
('060308', 'ID', 'Kec. Tanah Jambo Aye', '2019-06-10 04:42:07'),
('060309', 'ID', 'Kec. Seunudon', '2019-06-10 04:42:07'),
('060310', 'ID', 'Kec. Baktiya', '2019-06-10 04:42:07'),
('060312', 'ID', 'Kec. Tanah Luas', '2019-06-10 04:42:07'),
('060313', 'ID', 'Kec. Samudera', '2019-06-10 04:42:07'),
('060314', 'ID', 'Kec. Syamtalira Aron', '2019-06-10 04:42:07'),
('060315', 'ID', 'Kec. Tanah Pasir', '2019-06-10 04:42:07'),
('060316', 'ID', 'Kec. Langkahan', '2019-06-10 04:42:07'),
('060317', 'ID', 'Kec. Baktiya Barat', '2019-06-10 04:42:07'),
('060318', 'ID', 'Kec. Simpang Keramat', '2019-06-10 04:42:07'),
('060319', 'ID', 'Kec. Nibong', '2019-06-10 04:42:07'),
('060320', 'ID', 'Kec. Paya Bakong', '2019-06-10 04:42:07'),
('060321', 'ID', 'Kec. Muara Batu', '2019-06-10 04:42:07'),
('060322', 'ID', 'Kec. Dewantara', '2019-06-10 04:42:07'),
('060323', 'ID', 'Kec. Lhoksukon', '2019-06-10 04:42:07'),
('060326', 'ID', 'Kec. Lapang', '2019-06-10 04:42:07'),
('060327', 'ID', 'Kec. Pirak Timu', '2019-06-10 04:42:07'),
('060328', 'ID', 'Kec. Geureudong Pase', '2019-06-10 04:42:07'),
('060329', 'ID', 'Kec. Banda Baro', '2019-06-10 04:42:07'),
('060330', 'ID', 'Kec. Nisam Antara', '2019-06-10 04:42:07'),
('060400', 'ID', 'Kab. Aceh Timur', '2019-06-10 04:42:07'),
('060408', 'ID', 'Kec. Serba Jadi', '2019-06-10 04:42:07'),
('060409', 'ID', 'Kec. Birem Bayeun', '2019-06-10 04:42:07'),
('060410', 'ID', 'Kec. Rantau Selamat', '2019-06-10 04:42:07'),
('060411', 'ID', 'Kec. Peureulak', '2019-06-10 04:42:07'),
('060412', 'ID', 'Kec. Ranto Peureulak', '2019-06-10 04:42:07'),
('060413', 'ID', 'Kec. Idi Rayeuk', '2019-06-10 04:42:07'),
('060414', 'ID', 'Kec. Darul Aman', '2019-06-10 04:42:07'),
('060415', 'ID', 'Kec. Nurussalam', '2019-06-10 04:42:07'),
('060416', 'ID', 'Kec. Julok', '2019-06-10 04:42:07'),
('060417', 'ID', 'Kec. Pante Beudari', '2019-06-10 04:42:07'),
('060418', 'ID', 'Kec. Simpang Ulim', '2019-06-10 04:42:07'),
('060419', 'ID', 'Kec. Sungai Raya', '2019-06-10 04:42:07'),
('060420', 'ID', 'Kec. Peureulak Timur', '2019-06-10 04:42:07'),
('060421', 'ID', 'Kec. Peureulak Barat', '2019-06-10 04:42:07'),
('060422', 'ID', 'Kec. Banda Alam', '2019-06-10 04:42:07'),
('060423', 'ID', 'Kec. Idi Tunong', '2019-06-10 04:42:07'),
('060424', 'ID', 'Kec. Peudawa', '2019-06-10 04:42:07'),
('060425', 'ID', 'Kec. Indra Makmur', '2019-06-10 04:42:07'),
('060426', 'ID', 'Kec. Madat', '2019-06-10 04:42:07'),
('060428', 'ID', 'Kec. Simpang Jernih', '2019-06-10 04:42:07'),
('060429', 'ID', 'Kec. Darul Ihsan', '2019-06-10 04:42:07'),
('060430', 'ID', 'Kec. Peunaron', '2019-06-10 04:42:07'),
('060431', 'ID', 'Kec. Idi Timur', '2019-06-10 04:42:07'),
('060432', 'ID', 'Kec. Darul Falah', '2019-06-10 04:42:07'),
('060500', 'ID', 'Kab. Aceh Tengah', '2019-06-10 04:42:07'),
('060501', 'ID', 'Kec. Lingge', '2019-06-10 04:42:07'),
('060502', 'ID', 'Kec. Bintang', '2019-06-10 04:42:07'),
('060504', 'ID', 'Kec. Pegasing', '2019-06-10 04:42:07'),
('060505', 'ID', 'Kec. Bebesen', '2019-06-10 04:42:07'),
('060506', 'ID', 'Kec. Silih Nara', '2019-06-10 04:42:07'),
('060512', 'ID', 'Kec. Kuta Panang', '2019-06-10 04:42:07'),
('060513', 'ID', 'Kec. Ketol', '2019-06-10 04:42:07'),
('060514', 'ID', 'Kec. Celala', '2019-06-10 04:42:07'),
('060522', 'ID', 'Kec. Kebayakan', '2019-06-10 04:42:07'),
('060524', 'ID', 'Kec. Laut Tawar', '2019-06-10 04:42:07'),
('060525', 'ID', 'Kec. Atu Lintang', '2019-06-10 04:42:07'),
('060526', 'ID', 'Kec. Jagong Jeget', '2019-06-10 04:42:07'),
('060527', 'ID', 'Kec. Bies', '2019-06-10 04:42:07'),
('060528', 'ID', 'Kec. Rusip Antara', '2019-06-10 04:42:07'),
('060600', 'ID', 'Kab. Aceh Barat', '2019-06-10 04:42:07'),
('060605', 'ID', 'Kec. Johan Pahlawan', '2019-06-10 04:42:07'),
('060606', 'ID', 'Kec. Samatiga', '2019-06-10 04:42:07'),
('060607', 'ID', 'Kec. Woyla', '2019-06-10 04:42:07'),
('060608', 'ID', 'Kec. Kaway XVI', '2019-06-10 04:42:07'),
('060609', 'ID', 'Kec. Sungai Mas', '2019-06-10 04:42:07'),
('060610', 'ID', 'Kec. Bubon', '2019-06-10 04:42:07'),
('060611', 'ID', 'Kec. Arongan Lambalek', '2019-06-10 04:42:07'),
('060612', 'ID', 'Kec. Meureubo', '2019-06-10 04:42:07'),
('060613', 'ID', 'Kec. Pantee Ceureumen', '2019-06-10 04:42:07'),
('060614', 'ID', 'Kec. Woyla Barat', '2019-06-10 04:42:07'),
('060615', 'ID', 'Kec. Woyla Timur', '2019-06-10 04:42:07'),
('060616', 'ID', 'Kec. Panton Reu', '2019-06-10 04:42:07'),
('060700', 'ID', 'Kab. Aceh Selatan', '2019-06-10 04:42:07'),
('060701', 'ID', 'Kec. Trumon', '2019-06-10 04:42:07'),
('060702', 'ID', 'Kec. Bakongan', '2019-06-10 04:42:07'),
('060703', 'ID', 'Kec. Kluet Selatan', '2019-06-10 04:42:07'),
('060704', 'ID', 'Kec. Kluet Utara', '2019-06-10 04:42:07'),
('060705', 'ID', 'Kec. Tapak Tuan', '2019-06-10 04:42:07'),
('060706', 'ID', 'Kec. Samadua', '2019-06-10 04:42:07'),
('060707', 'ID', 'Kec. Sawang', '2019-06-10 04:42:07'),
('060708', 'ID', 'Kec. Meukek', '2019-06-10 04:42:07'),
('060709', 'ID', 'Kec. Labuhan Haji', '2019-06-10 04:42:07'),
('060710', 'ID', 'Kec. Pasie Raja', '2019-06-10 04:42:07'),
('060711', 'ID', 'Kec. Trumon Timur', '2019-06-10 04:42:07'),
('060712', 'ID', 'Kec. Kluet Timur', '2019-06-10 04:42:07'),
('060713', 'ID', 'Kec. Bakongan Timur', '2019-06-10 04:42:07'),
('060714', 'ID', 'Kec. Kluet Tengah', '2019-06-10 04:42:07'),
('060715', 'ID', 'Kec. Labuhan Haji Barat', '2019-06-10 04:42:07'),
('060716', 'ID', 'Kec. Labuhan Haji Timur', '2019-06-10 04:42:07'),
('060717', 'ID', 'Kec. Kota Bahagia', '2019-06-10 04:42:07'),
('060718', 'ID', 'Kec. Trumon Tengah', '2019-06-10 04:42:07'),
('060800', 'ID', 'Kab. Aceh Tenggara', '2019-06-10 04:42:07'),
('060801', 'ID', 'Kec. Lawe Alas', '2019-06-10 04:42:07'),
('060802', 'ID', 'Kec. Lawe Sigala-Gala', '2019-06-10 04:42:07'),
('060803', 'ID', 'Kec. Bambel', '2019-06-10 04:42:07'),
('060804', 'ID', 'Kec. Babussalam', '2019-06-10 04:42:07'),
('060805', 'ID', 'Kec. Badar', '2019-06-10 04:42:07'),
('060806', 'ID', 'Kec. Darul Hasanah', '2019-06-10 04:42:07'),
('060807', 'ID', 'Kec. Babul Makmur', '2019-06-10 04:42:07'),
('060808', 'ID', 'Kec. Lawe Bulan', '2019-06-10 04:42:07'),
('060809', 'ID', 'Kec. Bukit Tusam', '2019-06-10 04:42:07'),
('060810', 'ID', 'Kec. Semadam', '2019-06-10 04:42:07'),
('060811', 'ID', 'Kec. Babul Rahmat', '2019-06-10 04:42:07'),
('060822', 'ID', 'Kec. Ketambe', '2019-06-10 04:42:07'),
('060823', 'ID', 'Kec. Deleng Pokhkisen', '2019-06-10 04:42:07'),
('060824', 'ID', 'Kec. Lawe Sumur', '2019-06-10 04:42:07'),
('060825', 'ID', 'Kec. Tanoh Alas', '2019-06-10 04:42:07'),
('060826', 'ID', 'Kec. Lueser', '2019-06-10 04:42:07'),
('061100', 'ID', 'Kab. Simeulue', '2019-06-10 04:42:07'),
('061101', 'ID', 'Kec. Teupah Selatan', '2019-06-10 04:42:07'),
('061102', 'ID', 'Kec. Simeulue Timur', '2019-06-10 04:42:07'),
('061103', 'ID', 'Kec. Simeulue Tengah', '2019-06-10 04:42:07'),
('061104', 'ID', 'Kec. Salang', '2019-06-10 04:42:07'),
('061105', 'ID', 'Kec. Simeulue Barat', '2019-06-10 04:42:07'),
('061106', 'ID', 'Kec. Teupah Barat', '2019-06-10 04:42:07'),
('061107', 'ID', 'Kec. Teluk Dalam', '2019-06-10 04:42:07'),
('061108', 'ID', 'Kec. Alafan', '2019-06-10 04:42:07'),
('061109', 'ID', 'Kec. Teupah Tengah', '2020-02-10 03:07:43'),
('061110', 'ID', 'Kec. Simeulue Cut', '2020-02-10 03:07:43'),
('061200', 'ID', 'Kab. Bireuen', '2019-06-10 04:42:07'),
('061201', 'ID', 'Kec. Samalanga', '2019-06-10 04:42:07'),
('061202', 'ID', 'Kec. Pandrah', '2019-06-10 04:42:07'),
('061203', 'ID', 'Kec. Jeunib', '2019-06-10 04:42:07'),
('061204', 'ID', 'Kec. Peudada', '2019-06-10 04:42:07'),
('061205', 'ID', 'Kec. Juli', '2019-06-10 04:42:07'),
('061206', 'ID', 'Kec. Jeumpa', '2019-06-10 04:42:07'),
('061207', 'ID', 'Kec. Jangka', '2019-06-10 04:42:07'),
('061208', 'ID', 'Kec. Peusangan', '2019-06-10 04:42:07'),
('061209', 'ID', 'Kec. Makmur', '2019-06-10 04:42:07'),
('061210', 'ID', 'Kec. Ganda Pura', '2019-06-10 04:42:07'),
('061211', 'ID', 'Kec. Simpang Mamplam', '2019-06-10 04:42:07'),
('061212', 'ID', 'Kec. Peulimbang', '2019-06-10 04:42:07'),
('061213', 'ID', 'Kec. Kota Juang', '2019-06-10 04:42:07'),
('061214', 'ID', 'Kec. Kuala', '2019-06-10 04:42:07'),
('061215', 'ID', 'Kec. Peusangan Selatan', '2019-06-10 04:42:07'),
('061216', 'ID', 'Kec. Peusangan Siblah Krueng', '2019-06-10 04:42:07'),
('061217', 'ID', 'Kec. Kuta Blang', '2019-06-10 04:42:07'),
('061300', 'ID', 'Kab. Aceh Singkil', '2019-06-10 04:42:07'),
('061301', 'ID', 'Kec. Pulau Banyak', '2019-06-10 04:42:07'),
('061302', 'ID', 'Kec. Singkil', '2019-06-10 04:42:07'),
('061303', 'ID', 'Kec. Simpang Kanan', '2019-06-10 04:42:07'),
('061305', 'ID', 'Kec. Singkil Utara', '2019-06-10 04:42:07'),
('061306', 'ID', 'Kec. Gunung Mariah', '2019-06-10 04:42:07'),
('061307', 'ID', 'Kec. Danau Paris', '2019-06-10 04:42:07'),
('061308', 'ID', 'Kec. Suro Makmur', '2019-06-10 04:42:07'),
('061312', 'ID', 'Kec. Kuta Baharu', '2019-06-10 04:42:07'),
('061313', 'ID', 'Kec. Singkohor', '2019-06-10 04:42:07'),
('061317', 'ID', 'Kec. Kuala Baru', '2019-06-10 04:42:07'),
('061318', 'ID', 'Pulau Banyak Barat', '2019-06-10 04:42:07'),
('061400', 'ID', 'Kab. Aceh Tamiang', '2019-06-10 04:42:07'),
('061401', 'ID', 'Kec. Tamiang Hulu', '2019-06-10 04:42:07'),
('061402', 'ID', 'Kec. Kejuruan Muda', '2019-06-10 04:42:07'),
('061403', 'ID', 'Kec. Kota Kuala Simpang', '2019-06-10 04:42:07'),
('061404', 'ID', 'Kec. Seruway', '2019-06-10 04:42:07'),
('061405', 'ID', 'Kec. Bendahara', '2019-06-10 04:42:07'),
('061406', 'ID', 'Kec. Karang Baru', '2019-06-10 04:42:07'),
('061407', 'ID', 'Kec. Manyak Payed', '2019-06-10 04:42:07'),
('061408', 'ID', 'Kec. Rantau', '2019-06-10 04:42:07'),
('061412', 'ID', 'Kec. Bandar Mulya', '2019-06-10 04:42:07'),
('061413', 'ID', 'Kec. Bandar Pusaka', '2019-06-10 04:42:07'),
('061414', 'ID', 'Kec. Tenggulun', '2019-06-10 04:42:07'),
('061415', 'ID', 'Kec. Sekerak', '2019-06-10 04:42:07'),
('061500', 'ID', 'Kab. Nagan Raya', '2019-06-10 04:42:07'),
('061501', 'ID', 'Kec. Darul Makmur', '2019-06-10 04:42:07'),
('061502', 'ID', 'Kec. Kuala', '2019-06-10 04:42:07'),
('061503', 'ID', 'Kec. Beutong', '2019-06-10 04:42:07'),
('061504', 'ID', 'Kec. Seunagan', '2019-06-10 04:42:07'),
('061505', 'ID', 'Kec. Seunagan Timur', '2019-06-10 04:42:07'),
('061506', 'ID', 'Kec. Kuala Pesisir', '2019-06-10 04:42:07'),
('061507', 'ID', 'Kec. Tadu Raya', '2019-06-10 04:42:07'),
('061508', 'ID', 'Kec. Suka Makmue', '2019-06-10 04:42:07'),
('061509', 'ID', 'Kec. Tripa Makmur', '2019-06-10 04:42:07'),
('061510', 'ID', 'Kec. Beutong Ateuh Banggalan', '2020-02-10 03:07:43'),
('061600', 'ID', 'Kab. Aceh Jaya', '2019-06-10 04:42:07'),
('061601', 'ID', 'Kec. Teunom', '2019-06-10 04:42:07'),
('061602', 'ID', 'Kec. Krueng Sabee', '2019-06-10 04:42:07'),
('061603', 'ID', 'Kec. Setia Bakti', '2019-06-10 04:42:07'),
('061604', 'ID', 'Kec. Sampoiniet', '2019-06-10 04:42:07'),
('061605', 'ID', 'Kec. Jaya', '2019-06-10 04:42:07'),
('061606', 'ID', 'Kec. Panga', '2019-06-10 04:42:07'),
('061607', 'ID', 'Kec. Indra Jaya', '2019-06-10 04:42:07'),
('061608', 'ID', 'Kec. Darul Hikmah', '2019-06-10 04:42:07'),
('061609', 'ID', 'Kec. Pasie Raya', '2019-06-10 04:42:07'),
('061700', 'ID', 'Kab. Aceh Barat Daya', '2019-06-10 04:42:07'),
('061701', 'ID', 'Kec. Manggeng', '2019-06-10 04:42:07'),
('061702', 'ID', 'Kec. Tangan-Tangan', '2019-06-10 04:42:07'),
('061703', 'ID', 'Kec. Blang Pidie', '2019-06-10 04:42:07'),
('061704', 'ID', 'Kec. Susoh', '2019-06-10 04:42:07'),
('061705', 'ID', 'Kec. Kuala Batee', '2019-06-10 04:42:07'),
('061706', 'ID', 'Kec. Babah Rot', '2019-06-10 04:42:07'),
('061707', 'ID', 'Kec. Lembah Sabil', '2019-06-10 04:42:07'),
('061708', 'ID', 'Kec. Setia Bakti', '2019-06-10 04:42:07'),
('061709', 'ID', 'Kec. Jeumpa', '2019-06-10 04:42:07'),
('061800', 'ID', 'Kab. Gayo Lues', '2019-06-10 04:42:07'),
('061801', 'ID', 'Kec. Blang Kejeran', '2019-06-10 04:42:07'),
('061802', 'ID', 'Kec. Kuta Panjang', '2019-06-10 04:42:07'),
('061803', 'ID', 'Kec. Rikit Gaib', '2019-06-10 04:42:07'),
('061804', 'ID', 'Kec. Terangon', '2019-06-10 04:42:07'),
('061805', 'ID', 'Kec. Pinding', '2019-06-10 04:42:07'),
('061806', 'ID', 'Kec. Blang Jerango', '2019-06-10 04:42:07'),
('061807', 'ID', 'Kec. Puteri Betung', '2019-06-10 04:42:07'),
('061808', 'ID', 'Kec. Dabung Gelang', '2019-06-10 04:42:07'),
('061809', 'ID', 'Kec. Blang Pegayon', '2019-06-10 04:42:07'),
('061810', 'ID', 'Kec. Pantan Cuaca', '2019-06-10 04:42:07'),
('061811', 'ID', 'Kec. Tripe Jaya', '2019-06-10 04:42:07'),
('061900', 'ID', 'Kab. Bener Meriah', '2019-06-10 04:42:07'),
('061901', 'ID', 'Kec. Timang Gajah', '2019-06-10 04:42:07'),
('061902', 'ID', 'Kec. Bukit', '2019-06-10 04:42:07'),
('061903', 'ID', 'Kec. Bandar', '2019-06-10 04:42:07'),
('061904', 'ID', 'Kec. Permata', '2019-06-10 04:42:07'),
('061905', 'ID', 'Kec. Pintu Rime Gayo', '2019-06-10 04:42:07'),
('061906', 'ID', 'Kec. Wih Pesam', '2019-06-10 04:42:07'),
('061907', 'ID', 'Kec. Syiah Utama', '2019-06-10 04:42:07'),
('061908', 'ID', 'Bener Kelipah', '2019-06-10 04:42:07'),
('061909', 'ID', 'Mesidah', '2019-06-10 04:42:07'),
('061910', 'ID', 'Gajah Putih', '2019-06-10 04:42:07'),
('062000', 'ID', 'Kab. Pidie Jaya', '2019-06-10 04:42:07'),
('062001', 'ID', 'Kec. Meureudu', '2019-06-10 04:42:07'),
('062002', 'ID', 'Kec. Ulim', '2019-06-10 04:42:07'),
('062003', 'ID', 'Kec. Jangka Buya', '2019-06-10 04:42:07'),
('062004', 'ID', 'Kec. Bandar Dua', '2019-06-10 04:42:07'),
('062005', 'ID', 'Kec. Meurah Dua', '2019-06-10 04:42:07'),
('062006', 'ID', 'Kec. Bandar Baru', '2019-06-10 04:42:07'),
('062007', 'ID', 'Kec. Panteraja', '2019-06-10 04:42:07'),
('062008', 'ID', 'Kec. Trienggadeng', '2019-06-10 04:42:07'),
('066000', 'ID', 'Kota Sabang', '2019-06-10 04:42:07'),
('066001', 'ID', 'Kec. Sukajaya', '2019-06-10 04:42:07'),
('066002', 'ID', 'Kec. Sukakarya', '2019-06-10 04:42:07'),
('066100', 'ID', 'Kota Banda Aceh', '2019-06-10 04:42:07'),
('066101', 'ID', 'Kec. Meuraxa', '2019-06-10 04:42:07'),
('066102', 'ID', 'Kec. Baiturrahman', '2019-06-10 04:42:07'),
('066103', 'ID', 'Kec. Kuta Alam', '2019-06-10 04:42:07'),
('066104', 'ID', 'Kec. Syiah Kuala', '2019-06-10 04:42:07'),
('066105', 'ID', 'Kec. Kuta Raja', '2019-06-10 04:42:07'),
('066106', 'ID', 'Kec. Ulee Kareng', '2019-06-10 04:42:07'),
('066107', 'ID', 'Kec. Lueng Bata', '2019-06-10 04:42:07'),
('066108', 'ID', 'Kec. Banda Raya', '2019-06-10 04:42:07'),
('066109', 'ID', 'Kec. Jaya Baru', '2019-06-10 04:42:07'),
('066200', 'ID', 'Kota Lhokseumawe', '2019-06-10 04:42:07'),
('066201', 'ID', 'Kec. Blang Mangat', '2019-06-10 04:42:07'),
('066202', 'ID', 'Kec. Muara Dua', '2019-06-10 04:42:07'),
('066203', 'ID', 'Kec. Banda Sakti', '2019-06-10 04:42:07'),
('066204', 'ID', 'Kec. Muara Satu', '2019-06-10 04:42:07'),
('066300', 'ID', 'Kota Langsa', '2019-06-10 04:42:07'),
('066301', 'ID', 'Kec. Langsa Timur', '2019-06-10 04:42:07'),
('066302', 'ID', 'Kec. Langsa Barat', '2019-06-10 04:42:07'),
('066303', 'ID', 'Kec. Langsa Kota', '2019-06-10 04:42:07'),
('066304', 'ID', 'Kec. Langsa Lama', '2019-06-10 04:42:07'),
('066305', 'ID', 'Kec. Langsa Baro', '2019-06-10 04:42:07'),
('066400', 'ID', 'Kab. Sabussalam', '2019-06-10 04:42:07'),
('066401', 'ID', 'Simpang Kiri', '2019-06-10 04:42:07'),
('066402', 'ID', 'Penanggalan', '2019-06-10 04:42:07'),
('066403', 'ID', 'Rundeng', '2019-06-10 04:42:07'),
('066404', 'ID', 'Sultan Daulat', '2019-06-10 04:42:07'),
('066405', 'ID', 'Longkib', '2019-06-10 04:42:07'),
('070000', 'ID', 'Prop. Sumatera Utara', '2019-06-10 04:42:07'),
('070100', 'ID', 'Kab. Deli Serdang', '2019-06-10 04:42:07'),
('070101', 'ID', 'Kec. Gunung Meriah', '2019-06-10 04:42:07'),
('070102', 'ID', 'Kec. Sinembah Tanjung Muda Hul', '2019-06-10 04:42:07'),
('070103', 'ID', 'Kec. Sibolangit', '2019-06-10 04:42:07'),
('070104', 'ID', 'Kec. KutaIimbaru', '2019-06-10 04:42:07'),
('070105', 'ID', 'Kec. Pancur Batu', '2019-06-10 04:42:07'),
('070106', 'ID', 'Kec. Namo Rambe', '2019-06-10 04:42:07'),
('070107', 'ID', 'Kec. Sibiru-biru', '2019-06-10 04:42:07'),
('070109', 'ID', 'Kec. Bangun Purba', '2019-06-10 04:42:07'),
('070119', 'ID', 'Kec. Galang', '2019-06-10 04:42:07'),
('070120', 'ID', 'Kec. Tanjung Morawa', '2019-06-10 04:42:07'),
('070121', 'ID', 'Kec. Patumbak', '2019-06-10 04:42:07'),
('070122', 'ID', 'Kec. Deli Tua', '2019-06-10 04:42:07'),
('070123', 'ID', 'Kec. Sunggal', '2019-06-10 04:42:07'),
('070124', 'ID', 'Kec. Hamparan Perak', '2019-06-10 04:42:07'),
('070125', 'ID', 'Kec. Labuhan Deli', '2019-06-10 04:42:07'),
('070126', 'ID', 'Kec. Percut Sei Tuan', '2019-06-10 04:42:07'),
('070127', 'ID', 'Kec. Batang Kuis', '2019-06-10 04:42:07'),
('070128', 'ID', 'Kec. Pantai Labu', '2019-06-10 04:42:07'),
('070129', 'ID', 'Kec. Beringin', '2019-06-10 04:42:07'),
('070130', 'ID', 'Kec. Lubuk Pakam', '2019-06-10 04:42:07'),
('070131', 'ID', 'Kec. Pagar Marbau', '2019-06-10 04:42:07'),
('070132', 'ID', 'STM Hilir', '2019-06-10 04:42:07'),
('070200', 'ID', 'Kab. Langkat', '2019-06-10 04:42:07'),
('070201', 'ID', 'Kec. Bohorok', '2019-06-10 04:42:07'),
('070202', 'ID', 'Kec. Salapian', '2019-06-10 04:42:07'),
('070203', 'ID', 'Kec. Sei Bingai', '2019-06-10 04:42:07'),
('070204', 'ID', 'Kec. Kuala', '2019-06-10 04:42:07'),
('070205', 'ID', 'Kec. Selesai', '2019-06-10 04:42:07'),
('070206', 'ID', 'Kec. Binjai', '2019-06-10 04:42:07'),
('070207', 'ID', 'Kec. Stabat', '2019-06-10 04:42:07'),
('070208', 'ID', 'Kec. Wampu', '2019-06-10 04:42:07'),
('070209', 'ID', 'Kec. Batang Serangan', '2019-06-10 04:42:07'),
('070210', 'ID', 'Kec. Sawit Seberang', '2019-06-10 04:42:07'),
('070211', 'ID', 'Kec. Padang Tualang', '2019-06-10 04:42:07'),
('070212', 'ID', 'Kec. Hinai', '2019-06-10 04:42:07'),
('070213', 'ID', 'Kec. Secanggang', '2019-06-10 04:42:07'),
('070214', 'ID', 'Kec. Tanjung Pura', '2019-06-10 04:42:07'),
('070215', 'ID', 'Kec. Gebang', '2019-06-10 04:42:07'),
('070216', 'ID', 'Kec. Sei Lepan', '2019-06-10 04:42:07'),
('070217', 'ID', 'Kec. Babalan', '2019-06-10 04:42:07'),
('070218', 'ID', 'Kec. Berandan Barat', '2019-06-10 04:42:07'),
('070219', 'ID', 'Kec. Besitang', '2019-06-10 04:42:07'),
('070220', 'ID', 'Kec. Pangkalan Susu', '2019-06-10 04:42:07'),
('070221', 'ID', 'Kec. Serapit', '2019-06-10 04:42:07'),
('070222', 'ID', 'Kec. Kutambaru', '2019-06-10 04:42:07'),
('070223', 'ID', 'Kec. Pematang Jaya', '2019-06-10 04:42:07'),
('070300', 'ID', 'Kab. Karo', '2019-06-10 04:42:07'),
('070301', 'ID', 'Kec. Mardinding', '2019-06-10 04:42:07'),
('070302', 'ID', 'Kec. Laubaleng', '2019-06-10 04:42:07'),
('070303', 'ID', 'Kec. Tiga Binanga', '2019-06-10 04:42:07'),
('070304', 'ID', 'Kec. Juhar', '2019-06-10 04:42:07'),
('070305', 'ID', 'Kec. Munte', '2019-06-10 04:42:07'),
('070306', 'ID', 'Kec. Kuta Buluh', '2019-06-10 04:42:07'),
('070307', 'ID', 'Kec. Payung', '2019-06-10 04:42:07'),
('070308', 'ID', 'Kec. Simpang Empat', '2019-06-10 04:42:07'),
('070309', 'ID', 'Kec. Kabanjahe', '2019-06-10 04:42:07'),
('070310', 'ID', 'Kec. Berastagi', '2019-06-10 04:42:07'),
('070311', 'ID', 'Kec. Tiga Panah', '2019-06-10 04:42:07'),
('070312', 'ID', 'Kec. Merek', '2019-06-10 04:42:07'),
('070313', 'ID', 'Kec. Barus Jahe', '2019-06-10 04:42:07'),
('070314', 'ID', 'Kec. Tiga Binanga', '2019-06-10 04:42:07'),
('070315', 'ID', 'Kec. Naman Teran', '2019-06-10 04:42:07'),
('070316', 'ID', 'Kec. Merdeka', '2019-06-10 04:42:07'),
('070317', 'ID', 'Kec. Dolat Rayat', '2019-06-10 04:42:07'),
('070390', 'ID', 'Tiganderket', '2019-06-10 04:42:07'),
('070400', 'ID', 'Kab. Simalungun', '2019-06-10 04:42:07'),
('070401', 'ID', 'Kec. Silimakuta', '2019-06-10 04:42:07'),
('070402', 'ID', 'Kec. Purba', '2019-06-10 04:42:07'),
('070403', 'ID', 'Kec. Dolok Pardamean', '2019-06-10 04:42:07'),
('070404', 'ID', 'Kec. Sidamanik', '2019-06-10 04:42:07'),
('070405', 'ID', 'Kec. Girsang Simpangan Bolon', '2019-06-10 04:42:07'),
('070406', 'ID', 'Kec. Tanah Jawa', '2019-06-10 04:42:07'),
('070407', 'ID', 'Kec. Dolok Panribuan', '2019-06-10 04:42:07'),
('070408', 'ID', 'Kec. Jorlang Hataran', '2019-06-10 04:42:07'),
('070409', 'ID', 'Kec. Pane', '2019-06-10 04:42:07'),
('070410', 'ID', 'Kec. Raya', '2019-06-10 04:42:07'),
('070411', 'ID', 'Kec. Dolok Silau', '2019-06-10 04:42:07'),
('070412', 'ID', 'Kec. Silau Kahean', '2019-06-10 04:42:07'),
('070413', 'ID', 'Kec. Raya Kahean', '2019-06-10 04:42:07'),
('070414', 'ID', 'Kec. Tapian Dolok', '2019-06-10 04:42:07'),
('070415', 'ID', 'Kec. Dolok Batu Nanggar', '2019-06-10 04:42:07'),
('070416', 'ID', 'Kec. Siantar', '2019-06-10 04:42:07'),
('070417', 'ID', 'Kec. Hutabayu Raja', '2019-06-10 04:42:07'),
('070418', 'ID', 'Kec. Pematang Bandar', '2019-06-10 04:42:07'),
('070419', 'ID', 'Kec. Bandar', '2019-06-10 04:42:07'),
('070420', 'ID', 'Kec. Bosar Maligas', '2019-06-10 04:42:07'),
('070421', 'ID', 'Kec. Ujung Padang', '2019-06-10 04:42:07'),
('070422', 'ID', 'Kec. Panombeian Pane', '2019-06-10 04:42:07'),
('070423', 'ID', 'Kec. Gunung Malela', '2019-06-10 04:42:07'),
('070424', 'ID', 'Kec. Gunung Maligas', '2019-06-10 04:42:07'),
('070425', 'ID', 'Kec. Bandar Huluan', '2019-06-10 04:42:07'),
('070426', 'ID', 'Kec. Bandar Masilam', '2019-06-10 04:42:07'),
('070427', 'ID', 'Kec. Hatonduhan', '2019-06-10 04:42:07'),
('070428', 'ID', 'Kec. Jawa Maraja Bah Jambi', '2019-06-10 04:42:07'),
('070429', 'ID', 'Kec. Haranggaol Horison', '2019-06-10 04:42:07'),
('070430', 'ID', 'Kec. Pematang Sidamanik', '2019-06-10 04:42:07'),
('070431', 'ID', 'Pamatang Silima Huta', '2019-06-10 04:42:07'),
('070500', 'ID', 'Kab. Dairi', '2019-06-10 04:42:07'),
('070503', 'ID', 'Kec. Sidikalang', '2019-06-10 04:42:07'),
('070504', 'ID', 'Kec. Parbuluan', '2019-06-10 04:42:07'),
('070505', 'ID', 'Kec. Sumbul', '2019-06-10 04:42:07'),
('070506', 'ID', 'Kec. Pegangan Hilir', '2019-06-10 04:42:07'),
('070507', 'ID', 'Kec. Siempat Nempu Hulu', '2019-06-10 04:42:07'),
('070508', 'ID', 'Kec. Siempat Nempu', '2019-06-10 04:42:07'),
('070509', 'ID', 'Kec. Silima Pungga-Pungga', '2019-06-10 04:42:07'),
('070510', 'ID', 'Kec. Siempat Nempu Hilir', '2019-06-10 04:42:07'),
('070511', 'ID', 'Kec. Tigalingga', '2019-06-10 04:42:07'),
('070512', 'ID', 'Kec. Tanah Pinem', '2019-06-10 04:42:07'),
('070513', 'ID', 'Kec. Lae Parira', '2019-06-10 04:42:07'),
('070514', 'ID', 'Kec. Gunung Stember', '2019-06-10 04:42:07'),
('070515', 'ID', 'Kec. Berampu', '2019-06-10 04:42:07'),
('070516', 'ID', 'Kec. Sitinjo', '2019-06-10 04:42:07'),
('070517', 'ID', 'Silahi Sabungan', '2019-06-10 04:42:07'),
('070590', 'ID', 'Merek', '2019-06-10 04:42:07'),
('070600', 'ID', 'Kab. Asahan', '2019-06-10 04:42:07'),
('070601', 'ID', 'Kec. Bandar Pasir Mandoge', '2019-06-10 04:42:07'),
('070602', 'ID', 'Kec. Bandar Pulau', '2019-06-10 04:42:07'),
('070603', 'ID', 'Kec. Pulau Rakyat', '2019-06-10 04:42:07'),
('070604', 'ID', 'Kec. Sei Kepayang', '2019-06-10 04:42:07'),
('070605', 'ID', 'Kec. Tanjung Balai', '2019-06-10 04:42:07'),
('070606', 'ID', 'Kec. Simpang Empat', '2019-06-10 04:42:07'),
('070607', 'ID', 'Kec. Air Batu', '2019-06-10 04:42:07'),
('070608', 'ID', 'Kec. Buntu Pane', '2019-06-10 04:42:07'),
('070609', 'ID', 'Kec. Meranti', '2019-06-10 04:42:07'),
('070610', 'ID', 'Kec. Air Joman', '2019-06-10 04:42:07'),
('070619', 'ID', 'Kec. Aek Kuasan', '2019-06-10 04:42:07'),
('070621', 'ID', 'Kec. Kisaran Barat', '2019-06-10 04:42:07'),
('070622', 'ID', 'Kec. Kisaran Timur', '2019-06-10 04:42:07'),
('070623', 'ID', 'Kec. Aek Songsongan', '2019-06-10 04:42:07'),
('070624', 'ID', 'Kec. Rahuning', '2019-06-10 04:42:07'),
('070625', 'ID', 'Kec. Aek Ledong', '2019-06-10 04:42:07'),
('070626', 'ID', 'Kec. Sei Kepayang Barat', '2019-06-10 04:42:07'),
('070627', 'ID', 'Kec. Sei Kepayang Timur', '2019-06-10 04:42:07'),
('070628', 'ID', 'Kec. Teluk Dalam', '2019-06-10 04:42:07'),
('070629', 'ID', 'Kec. Sei dadap', '2019-06-10 04:42:07'),
('070630', 'ID', 'Kec. Tinggi Raja', '2019-06-10 04:42:07'),
('070631', 'ID', 'Kec. Setia Janji', '2019-06-10 04:42:07'),
('070632', 'ID', 'Kec. Pulo Bandring', '2019-06-10 04:42:07'),
('070633', 'ID', 'Kec. Rawang Panca Arga', '2019-06-10 04:42:07'),
('070634', 'ID', 'Kec. Silo Laut', '2019-06-10 04:42:07'),
('070700', 'ID', 'Kab. Labuhan Batu', '2019-06-10 04:42:07'),
('070705', 'ID', 'Kec. Bilah Hulu', '2019-06-10 04:42:07'),
('070707', 'ID', 'Kec. Pangkatan', '2019-06-10 04:42:07'),
('070708', 'ID', 'Kec. Bilah Barat', '2019-06-10 04:42:07'),
('070713', 'ID', 'Kec. Bilah Hilir', '2019-06-10 04:42:07'),
('070714', 'ID', 'Kec. Panai Hulu', '2019-06-10 04:42:07'),
('070715', 'ID', 'Kec. Panai Tengah', '2019-06-10 04:42:07'),
('070716', 'ID', 'Kec. Panai Hilir', '2019-06-10 04:42:07'),
('070721', 'ID', 'Kec. Rantau Selatan', '2019-06-10 04:42:07'),
('070722', 'ID', 'Kec. Rantau Utara', '2019-06-10 04:42:07'),
('070800', 'ID', 'Kab. Tapanuli Utara', '2019-06-10 04:42:07'),
('070803', 'ID', 'Kec. Parmonangan', '2019-06-10 04:42:07'),
('070804', 'ID', 'Kec. Adian Koting', '2019-06-10 04:42:07'),
('070805', 'ID', 'Kec. Sipoholon', '2019-06-10 04:42:07'),
('070806', 'ID', 'Kec. Tarutung', '2019-06-10 04:42:07'),
('070807', 'ID', 'Kec. Pahae Jae', '2019-06-10 04:42:07'),
('070808', 'ID', 'Kec. Pahae Julu', '2019-06-10 04:42:07'),
('070809', 'ID', 'Kec. Pangaribuan', '2019-06-10 04:42:07'),
('070810', 'ID', 'Kec. Garoga', '2019-06-10 04:42:07'),
('070811', 'ID', 'Kec. Sipahutar', '2019-06-10 04:42:07'),
('070812', 'ID', 'Kec. Siborong-Borong', '2019-06-10 04:42:07'),
('070813', 'ID', 'Kec. Pagaran', '2019-06-10 04:42:07'),
('070818', 'ID', 'Kec. Muara', '2019-06-10 04:42:07'),
('070819', 'ID', 'Kec. Purbatua', '2019-06-10 04:42:07'),
('070822', 'ID', 'Kec. Simangumban', '2019-06-10 04:42:07'),
('070823', 'ID', 'Kec. Siatas Barita', '2019-06-10 04:42:07'),
('070900', 'ID', 'Kab. Tapanuli Tengah', '2019-06-10 04:42:07'),
('070901', 'ID', 'Kec. Lumut', '2019-06-10 04:42:07'),
('070902', 'ID', 'Kec. Sibabangun', '2019-06-10 04:42:07'),
('070903', 'ID', 'Kec. Pandan', '2019-06-10 04:42:07'),
('070904', 'ID', 'Kec. Tapian Nauli', '2019-06-10 04:42:07'),
('070905', 'ID', 'Kec. Kolang', '2019-06-10 04:42:07'),
('070906', 'ID', 'Kec. Sorkam', '2019-06-10 04:42:07'),
('070907', 'ID', 'Kec. Barus', '2019-06-10 04:42:07'),
('070908', 'ID', 'Kec. Manduamas', '2019-06-10 04:42:07'),
('070909', 'ID', 'Kec. Sosor Gadong', '2019-06-10 04:42:07'),
('070910', 'ID', 'Kec. Sorkam Barat', '2019-06-10 04:42:07'),
('070911', 'ID', 'Kec. Andam Dewi', '2019-06-10 04:42:07'),
('070912', 'ID', 'Kec. Badiri', '2019-06-10 04:42:07'),
('070913', 'ID', 'Kec. Sitahuis', '2019-06-10 04:42:07'),
('070914', 'ID', 'Kec. Sirandorung', '2019-06-10 04:42:07'),
('070915', 'ID', 'Kec. Tukka', '2019-06-10 04:42:07'),
('070916', 'ID', 'Kec. Pinang Sori', '2019-06-10 04:42:07'),
('070917', 'ID', 'Kec. Sukabangun', '2019-06-10 04:42:07'),
('070918', 'ID', 'Kec. Sarudik', '2019-06-10 04:42:07'),
('070919', 'ID', 'Kec. Barus Utara', '2019-06-10 04:42:07'),
('070920', 'ID', 'Pasaribu Tobing', '2019-06-10 04:42:07'),
('071000', 'ID', 'Kab. Tapanuli Selatan', '2019-06-10 04:42:07'),
('071001', 'ID', 'Kec. Batang Angkola', '2019-06-10 04:42:07'),
('071010', 'ID', 'Kec. Batang Toru', '2019-06-10 04:42:07'),
('071011', 'ID', 'Kec. Sipirok', '2019-06-10 04:42:07'),
('071012', 'ID', 'Kec. Arse', '2019-06-10 04:42:07'),
('071016', 'ID', 'Kec. Saipar Dolok Hole', '2019-06-10 04:42:07'),
('071025', 'ID', 'Kec. Marancar', '2019-06-10 04:42:07');
INSERT INTO `dikti_wilayah` (`id_wilayah`, `id_negara`, `nama_wilayah`, `dikti_wilayah_timestamp`) VALUES
('071026', 'ID', 'Kec. Sayur Matinggi', '2019-06-10 04:42:07'),
('071027', 'ID', 'Kec. Aek Bilah', '2019-06-10 04:42:07'),
('071029', 'ID', 'Kec. Muaro Batang Toru', '2019-06-10 04:42:07'),
('071030', 'ID', 'Angkola Barat', '2019-06-10 04:42:07'),
('071031', 'ID', 'Angkola Sangkunur', '2019-06-10 04:42:07'),
('071032', 'ID', 'Angkola Selatan', '2019-06-10 04:42:07'),
('071033', 'ID', 'Angkola Timur', '2019-06-10 04:42:07'),
('071034', 'ID', 'Tantom Angkola', '2019-06-10 04:42:07'),
('071100', 'ID', 'Kab. Nias', '2019-06-10 04:42:07'),
('071106', 'ID', 'Kec. Idano Gawo', '2019-06-10 04:42:07'),
('071107', 'ID', 'Kec. Gido', '2019-06-10 04:42:07'),
('071113', 'ID', 'Kec. Hiliduho', '2019-06-10 04:42:07'),
('071119', 'ID', 'Kec. Bawalato', '2019-06-10 04:42:07'),
('071123', 'ID', 'Kec. Ulugawo', '2019-06-10 04:42:07'),
('071125', 'ID', 'Kec. Ma U', '2019-06-10 04:42:07'),
('071126', 'ID', 'Kec. Somolo-Molo', '2019-06-10 04:42:07'),
('071132', 'ID', 'Kec. Hili Serangkai', '2019-06-10 04:42:07'),
('071133', 'ID', 'Kec. Botomuzoi', '2019-06-10 04:42:07'),
('071190', 'ID', 'Sogaeadu', '2019-06-10 04:42:07'),
('071500', 'ID', 'Kab. Mandailing Natal', '2019-06-10 04:42:07'),
('071501', 'ID', 'Kec. Batahan', '2019-06-10 04:42:07'),
('071502', 'ID', 'Kec. Batang Natal', '2019-06-10 04:42:07'),
('071503', 'ID', 'Kec. Kotanopan', '2019-06-10 04:42:07'),
('071504', 'ID', 'Kec. Muara Sipongi', '2019-06-10 04:42:07'),
('071505', 'ID', 'Kec. Panyabungan Kota', '2019-06-10 04:42:07'),
('071506', 'ID', 'Kec. Natal', '2019-06-10 04:42:07'),
('071507', 'ID', 'Kec. Muara Batang Gadis', '2019-06-10 04:42:07'),
('071508', 'ID', 'Kec. Siabu', '2019-06-10 04:42:07'),
('071509', 'ID', 'Kec. Panyabungan Utara', '2019-06-10 04:42:07'),
('071510', 'ID', 'Kec. Panyabungan Barat', '2019-06-10 04:42:07'),
('071511', 'ID', 'Kec. Panyabungan Timur', '2019-06-10 04:42:07'),
('071512', 'ID', 'Kec. Panyabungan Selatan', '2019-06-10 04:42:07'),
('071513', 'ID', 'Kec. Bukit Malintang', '2019-06-10 04:42:07'),
('071514', 'ID', 'Kec. Lembah Sorik Merapi', '2019-06-10 04:42:07'),
('071515', 'ID', 'Kec. Ulu Pungut', '2019-06-10 04:42:07'),
('071516', 'ID', 'Kec. Tambangan', '2019-06-10 04:42:07'),
('071517', 'ID', 'Kec. Langga Bayu', '2019-06-10 04:42:07'),
('071518', 'ID', 'Kec. Ranto Baek', '2020-02-10 03:07:43'),
('071519', 'ID', 'Kec. Puncak Sorik Merapi', '2020-02-10 03:07:43'),
('071520', 'ID', 'Kec. Sinunukan', '2020-02-10 03:07:43'),
('071521', 'ID', 'Kec. Huta Bargot', '2020-02-10 03:07:43'),
('071522', 'ID', 'Kec. Pakantan', '2020-02-10 03:07:43'),
('071523', 'ID', 'Kec. Naga Juang', '2020-02-10 03:07:43'),
('071600', 'ID', 'Kab. Toba Samosir', '2019-06-10 04:42:07'),
('071603', 'ID', 'Kec. Balige', '2019-06-10 04:42:07'),
('071604', 'ID', 'Kec. Lagu Boti', '2019-06-10 04:42:07'),
('071605', 'ID', 'Kec. Habinsaran', '2019-06-10 04:42:07'),
('071606', 'ID', 'Kec. Silaen', '2019-06-10 04:42:07'),
('071607', 'ID', 'Kec. Porsea', '2019-06-10 04:42:07'),
('071608', 'ID', 'Kec. Lumban Julu', '2019-06-10 04:42:07'),
('071616', 'ID', 'Kec. Uluan', '2019-06-10 04:42:07'),
('071617', 'ID', 'Kec. Pintu Pohan Meranti', '2019-06-10 04:42:07'),
('071618', 'ID', 'Kec. Ajibata', '2019-06-10 04:42:07'),
('071619', 'ID', 'Kec. Borbor', '2019-06-10 04:42:07'),
('071620', 'ID', 'Kec. Tampahan', '2019-06-10 04:42:07'),
('071621', 'ID', 'Kec. Nassau', '2019-06-10 04:42:07'),
('071622', 'ID', 'Kec. Sigumpar', '2019-06-10 04:42:07'),
('071623', 'ID', 'Kec. Siantar Narumonda', '2019-06-10 04:42:07'),
('071624', 'ID', 'Kec. Parmaksian', '2019-06-10 04:42:07'),
('071625', 'ID', 'Kec. Bonatua Lunasi', '2019-06-10 04:42:07'),
('071700', 'ID', 'Kab. Nias Selatan', '2019-06-10 04:42:07'),
('071701', 'ID', 'Kec. Pulau-Pulau Batu', '2019-06-10 04:42:07'),
('071702', 'ID', 'Kec. Teluk Dalam', '2019-06-10 04:42:07'),
('071703', 'ID', 'Kec. Amandraya', '2019-06-10 04:42:07'),
('071704', 'ID', 'Kec. Lahusa', '2019-06-10 04:42:07'),
('071705', 'ID', 'Kec. Gomo', '2019-06-10 04:42:07'),
('071706', 'ID', 'Kec. Lolomatua', '2019-06-10 04:42:07'),
('071707', 'ID', 'Kec. Lolowa`U', '2019-06-10 04:42:07'),
('071708', 'ID', 'Kec. Hibala', '2019-06-10 04:42:07'),
('071709', 'ID', 'Kec. Susua', '2019-06-10 04:42:07'),
('071710', 'ID', 'Kec. Maniamolo', '2019-06-10 04:42:07'),
('071711', 'ID', 'Kec. Hilimegai', '2019-06-10 04:42:07'),
('071712', 'ID', 'Kec. Toma', '2019-06-10 04:42:07'),
('071713', 'ID', 'Kec. Mazino', '2019-06-10 04:42:07'),
('071714', 'ID', 'Kec. Umbunasi', '2019-06-10 04:42:07'),
('071715', 'ID', 'Kec. Aramo', '2019-06-10 04:42:07'),
('071716', 'ID', 'Kec. Pulau-Pulau Batu Timur', '2019-06-10 04:42:07'),
('071717', 'ID', 'Kec. Mazo', '2019-06-10 04:42:07'),
('071718', 'ID', 'Kec. Fanayama', '2019-06-10 04:42:07'),
('071719', 'ID', 'Kec. Ulunoyo', '2020-02-10 03:07:43'),
('071720', 'ID', 'Kec. Huruna', '2020-02-10 03:07:43'),
('071721', 'ID', 'Kec. Oou', '2020-02-10 03:07:43'),
('071722', 'ID', 'Kec. Onohazumba', '2020-02-10 03:07:43'),
('071723', 'ID', 'Kec. Hilisalawaahe', '2020-02-10 03:07:43'),
('071724', 'ID', 'Kec. Ulususua', '2020-02-10 03:07:43'),
('071725', 'ID', 'Kec. Siduaori', '2020-02-10 03:07:43'),
('071726', 'ID', 'Kec. Somambawa', '2020-02-10 03:07:43'),
('071727', 'ID', 'Kec. Boronadu', '2020-02-10 03:07:43'),
('071728', 'ID', 'Kec. Ulu Idanotae', '2020-02-10 03:07:43'),
('071729', 'ID', 'Kec. Idanotae', '2020-02-10 03:07:43'),
('071730', 'ID', 'Kec. Luahagundre Maniamolo', '2020-02-10 03:07:43'),
('071731', 'ID', 'Kec. Onolalu', '2020-02-10 03:07:43'),
('071732', 'ID', 'Kec. Pulau-Pulau Batu Utara', '2020-02-10 03:07:43'),
('071733', 'ID', 'Kec. Pulau-Pulau Batu Barat', '2020-02-10 03:07:43'),
('071734', 'ID', 'Kec. Simuk', '2020-02-10 03:07:43'),
('071735', 'ID', 'Kec. Tanah Masa', '2020-02-10 03:07:43'),
('071736', 'ID', 'Kec. Onolalu', '2020-02-10 03:07:43'),
('071737', 'ID', 'Kec. Luahagundre Maniamolo', '2020-02-10 03:07:43'),
('071800', 'ID', 'Kab. Pak pak Bharat', '2019-06-10 04:42:07'),
('071801', 'ID', 'Kec. Salak', '2019-06-10 04:42:07'),
('071802', 'ID', 'Kec. Kerajaan', '2019-06-10 04:42:07'),
('071803', 'ID', 'Kec. Sitelutali Urang Jehe', '2019-06-10 04:42:07'),
('071804', 'ID', 'Kec. Sitelutali Urang Jehe', '2019-06-10 04:42:07'),
('071805', 'ID', 'Kec. Pangindar', '2019-06-10 04:42:07'),
('071806', 'ID', 'Kec. Pergetteng-getteng Sengku', '2019-06-10 04:42:07'),
('071807', 'ID', 'Kec. Tinada', '2019-06-10 04:42:07'),
('071808', 'ID', 'Kec. Siempat Rube', '2019-06-10 04:42:07'),
('071899', 'ID', 'Sitelu Tali Urang Julu', '2019-06-10 04:42:07'),
('071900', 'ID', 'Kab. Humbang Hasudutan', '2019-06-10 04:42:07'),
('071901', 'ID', 'Kec. Pakkat', '2019-06-10 04:42:07'),
('071902', 'ID', 'Kec. Onan Ganjang', '2019-06-10 04:42:07'),
('071903', 'ID', 'Kec. Lintong Nihuta', '2019-06-10 04:42:07'),
('071904', 'ID', 'Kec. Dolok Sanggul', '2019-06-10 04:42:07'),
('071905', 'ID', 'Kec. Parlilitan', '2019-06-10 04:42:07'),
('071906', 'ID', 'Kec. Pollung', '2019-06-10 04:42:07'),
('071907', 'ID', 'Kec. Paranginan', '2019-06-10 04:42:07'),
('071908', 'ID', 'Kec. Bakti Raja', '2019-06-10 04:42:07'),
('071909', 'ID', 'Kec. Sijamapolang', '2019-06-10 04:42:07'),
('071910', 'ID', 'Kec. Tarabintang', '2019-06-10 04:42:07'),
('072000', 'ID', 'Kab. Samosir', '2019-06-10 04:42:07'),
('072001', 'ID', 'Kec. Harian', '2019-06-10 04:42:07'),
('072002', 'ID', 'Kec. Sianjur Mula Mula', '2019-06-10 04:42:07'),
('072003', 'ID', 'Kec. Onan Runggu Timur', '2019-06-10 04:42:07'),
('072004', 'ID', 'Kec. Palipi', '2019-06-10 04:42:07'),
('072005', 'ID', 'Kec. Pangururan', '2019-06-10 04:42:07'),
('072006', 'ID', 'Kec. Simanindo', '2019-06-10 04:42:07'),
('072007', 'ID', 'Kec. Nainggolan', '2019-06-10 04:42:07'),
('072008', 'ID', 'Kec. Ronggur Nihuta', '2019-06-10 04:42:07'),
('072009', 'ID', 'Kec. Sitiotio', '2019-06-10 04:42:07'),
('072100', 'ID', 'Kab. Serdang Bedagai', '2019-06-10 04:42:07'),
('072101', 'ID', 'Kec. Kotarih', '2019-06-10 04:42:07'),
('072102', 'ID', 'Kec. Dolok Masihul', '2019-06-10 04:42:07'),
('072103', 'ID', 'Kec. Sipispis', '2019-06-10 04:42:07'),
('072104', 'ID', 'Kec. Dolok Merawan', '2019-06-10 04:42:07'),
('072105', 'ID', 'Kec. Tebing Tinggi', '2019-06-10 04:42:07'),
('072106', 'ID', 'Kec. Bandar Khalifah', '2019-06-10 04:42:07'),
('072107', 'ID', 'Kec. Tanjung Beringin', '2019-06-10 04:42:07'),
('072108', 'ID', 'Kec. Teluk Mengkudu', '2019-06-10 04:42:07'),
('072109', 'ID', 'Kec. Sei Rampah', '2019-06-10 04:42:07'),
('072110', 'ID', 'Kec. Perbaungan', '2019-06-10 04:42:07'),
('072111', 'ID', 'Kec. Pantai Cermin', '2019-06-10 04:42:07'),
('072112', 'ID', 'Kec. Silinda', '2019-06-10 04:42:07'),
('072113', 'ID', 'Kec. Bintang Bayu', '2019-06-10 04:42:07'),
('072114', 'ID', 'Kec. Serbajadi', '2019-06-10 04:42:07'),
('072115', 'ID', 'Kec. Tebing Syahbandar', '2019-06-10 04:42:07'),
('072116', 'ID', 'Kec. Sei Bamban', '2019-06-10 04:42:07'),
('072117', 'ID', 'Kec. Pegajahan', '2019-06-10 04:42:07'),
('072200', 'ID', 'Kab. Batubara', '2019-06-10 04:42:07'),
('072201', 'ID', 'Kec. Medang Deras', '2019-06-10 04:42:07'),
('072202', 'ID', 'Kec. Air Putih', '2019-06-10 04:42:07'),
('072203', 'ID', 'Kec. Lima Puluh', '2019-06-10 04:42:07'),
('072204', 'ID', 'Kec. Sei Balai', '2019-06-10 04:42:07'),
('072205', 'ID', 'Kec. Sei Suka', '2019-06-10 04:42:07'),
('072206', 'ID', 'Kec. Talawi', '2019-06-10 04:42:07'),
('072207', 'ID', 'Kec. Tanjung Tiram', '2019-06-10 04:42:07'),
('072300', 'ID', 'Kab. Padang Lawas utara', '2019-06-10 04:42:07'),
('072301', 'ID', 'Kec. Padang Bolak Julu', '2019-06-10 04:42:07'),
('072302', 'ID', 'Kec. Padang Bolak', '2019-06-10 04:42:07'),
('072303', 'ID', 'Kec. Halongonan', '2019-06-10 04:42:07'),
('072304', 'ID', 'Kec. Dolok Sxigompulon', '2019-06-10 04:42:07'),
('072305', 'ID', 'Kec. Portibi', '2019-06-10 04:42:07'),
('072306', 'ID', 'Kec. Simangambat', '2019-06-10 04:42:07'),
('072307', 'ID', 'Kec. Batang Onang', '2019-06-10 04:42:07'),
('072308', 'ID', 'Kec. Dolok', '2019-06-10 04:42:07'),
('072390', 'ID', 'Hulu Sihapas', '2019-06-10 04:42:07'),
('072400', 'ID', 'Kab. Padang Lawas', '2019-06-10 04:42:07'),
('072401', 'ID', 'Kec. Barumun', '2019-06-10 04:42:07'),
('072402', 'ID', 'Kec. Sosa', '2019-06-10 04:42:07'),
('072403', 'ID', 'Kec. Barumun Tengah', '2019-06-10 04:42:07'),
('072404', 'ID', 'Kec. Batang Lubu Sutam.', '2019-06-10 04:42:07'),
('072405', 'ID', 'Kec. Huta Raja Tinggi', '2019-06-10 04:42:07'),
('072406', 'ID', 'Kec. Lubuk Barumun', '2019-06-10 04:42:07'),
('072407', 'ID', 'Kec. Huristak', '2019-06-10 04:42:07'),
('072408', 'ID', 'Kec. Ulu Barumun', '2019-06-10 04:42:07'),
('072409', 'ID', 'Kec. Sosopan', '2019-06-10 04:42:07'),
('072410', 'ID', 'Kec. Barumun Selatan', '2019-06-10 04:42:07'),
('072411', 'ID', 'Kec. Aek Nabara Barumun', '2019-06-10 04:42:07'),
('072412', 'ID', 'Kec. Sihapas Barumun', '2020-02-10 03:07:43'),
('072500', 'ID', 'Kab. Labuhan Batu Utara', '2019-06-10 04:42:07'),
('072501', 'ID', 'Kec. Na IX-X', '2019-06-10 04:42:07'),
('072502', 'ID', 'Kec. Aek Natas', '2019-06-10 04:42:07'),
('072503', 'ID', 'Kec. Aek Kuo', '2019-06-10 04:42:07'),
('072504', 'ID', 'Kec. Kualuh Hilir', '2019-06-10 04:42:07'),
('072505', 'ID', 'Kec. Kualuh Selatan', '2019-06-10 04:42:07'),
('072506', 'ID', 'Kec. Kualuh Hulu', '2019-06-10 04:42:07'),
('072507', 'ID', 'Kec. Kualuh Leidong', '2019-06-10 04:42:07'),
('072508', 'ID', 'Kec. Marbau', '2019-06-10 04:42:07'),
('072600', 'ID', 'Kab. Labuhan Batu Selatan', '2019-06-10 04:42:07'),
('072601', 'ID', 'Kec. Sungai Kanan', '2019-06-10 04:42:07'),
('072602', 'ID', 'Kec. Torgamba', '2019-06-10 04:42:07'),
('072603', 'ID', 'Kec. Kota Pinang', '2019-06-10 04:42:07'),
('072604', 'ID', 'Kec. Silangkitang', '2019-06-10 04:42:07'),
('072605', 'ID', 'Kec. Kampung Rakyat', '2019-06-10 04:42:07'),
('072700', 'ID', 'Kab. Nias Barat', '2019-06-10 04:42:07'),
('072701', 'ID', 'Kec. Lolofitu Moi', '2019-06-10 04:42:07'),
('072702', 'ID', 'Kec. Sirombu', '2019-06-10 04:42:07'),
('072703', 'ID', 'Kec. Lahomi', '2019-06-10 04:42:07'),
('072704', 'ID', 'Kec. Mandrehe', '2019-06-10 04:42:07'),
('072705', 'ID', 'Kec. Mandrehe Barat', '2019-06-10 04:42:07'),
('072706', 'ID', 'Kec. Moro O', '2019-06-10 04:42:07'),
('072707', 'ID', 'Kec. Mandrehe Barat', '2019-06-10 04:42:07'),
('072708', 'ID', 'Kec. Ulo Moro O', '2019-06-10 04:42:07'),
('072709', 'ID', 'Kec. Mandrehe Utara', '2020-02-10 03:07:43'),
('072800', 'ID', 'Kab. Nias Utara', '2019-06-10 04:42:07'),
('072801', 'ID', 'Kec. Tuhemberua', '2019-06-10 04:42:07'),
('072802', 'ID', 'Kec. Lotu', '2019-06-10 04:42:07'),
('072803', 'ID', 'Kec. Sitolu Ori', '2019-06-10 04:42:07'),
('072804', 'ID', 'Kec. Sawo', '2019-06-10 04:42:07'),
('072805', 'ID', 'Kec. Alasa', '2019-06-10 04:42:07'),
('072806', 'ID', 'Kec. Namohalu Esiwa', '2019-06-10 04:42:07'),
('072807', 'ID', 'Kec. Alasa Talu Muzoi', '2019-06-10 04:42:07'),
('072808', 'ID', 'Kec. Tugala Oyo', '2019-06-10 04:42:07'),
('072809', 'ID', 'Kec. Lahewa', '2019-06-10 04:42:07'),
('072810', 'ID', 'Kec. Afulu', '2019-06-10 04:42:07'),
('072811', 'ID', 'Kec. Lahewa Timur', '2019-06-10 04:42:07'),
('076000', 'ID', 'Kota Medan', '2019-06-10 04:42:07'),
('076001', 'ID', 'Kec. Medan Tuntungan', '2019-06-10 04:42:07'),
('076002', 'ID', 'Kec. Medan Johor', '2019-06-10 04:42:07'),
('076003', 'ID', 'Kec. Medan Amplas', '2019-06-10 04:42:07'),
('076004', 'ID', 'Kec. Medan Denai', '2019-06-10 04:42:07'),
('076005', 'ID', 'Kec. Medan Area', '2019-06-10 04:42:07'),
('076006', 'ID', 'Kec. Medan Kota', '2019-06-10 04:42:07'),
('076007', 'ID', 'Kec. Medan Maimun', '2019-06-10 04:42:07'),
('076008', 'ID', 'Kec. Medan Polonia', '2019-06-10 04:42:07'),
('076009', 'ID', 'Kec. Medan Baru', '2019-06-10 04:42:07'),
('076010', 'ID', 'Kec. Medan Selayang', '2019-06-10 04:42:07'),
('076011', 'ID', 'Kec. Medan Sunggal', '2019-06-10 04:42:07'),
('076012', 'ID', 'Kec. Medan Helvetia', '2019-06-10 04:42:07'),
('076013', 'ID', 'Kec. Medan Petisah', '2019-06-10 04:42:07'),
('076014', 'ID', 'Kec. Medan Barat', '2019-06-10 04:42:07'),
('076015', 'ID', 'Kec. Medan Timur', '2019-06-10 04:42:07'),
('076016', 'ID', 'Kec. Medan Perjuangan', '2019-06-10 04:42:07'),
('076017', 'ID', 'Kec. Medan Tembung', '2019-06-10 04:42:07'),
('076018', 'ID', 'Kec. Medan Deli', '2019-06-10 04:42:07'),
('076019', 'ID', 'Kec. Medan Labuhan', '2019-06-10 04:42:07'),
('076020', 'ID', 'Kec. Medan Marelan', '2019-06-10 04:42:07'),
('076021', 'ID', 'Kec. Medan Kota Belawan', '2019-06-10 04:42:07'),
('076022', 'ID', 'Kec. Medan Utara', '2020-02-10 03:07:43'),
('076100', 'ID', 'Kota Binjai', '2019-06-10 04:42:07'),
('076101', 'ID', 'Kec. Binjai Selatan', '2019-06-10 04:42:07'),
('076102', 'ID', 'Kec. Binjai Kota', '2019-06-10 04:42:07'),
('076103', 'ID', 'Kec. Binjai Timur', '2019-06-10 04:42:07'),
('076104', 'ID', 'Kec. Binjai Utara', '2019-06-10 04:42:07'),
('076105', 'ID', 'Kec. Binjai Barat', '2019-06-10 04:42:07'),
('076200', 'ID', 'Kota Tebing Tinggi', '2019-06-10 04:42:07'),
('076201', 'ID', 'Kec. Padang Hulu', '2019-06-10 04:42:07'),
('076202', 'ID', 'Kec. Rambutan', '2019-06-10 04:42:07'),
('076203', 'ID', 'Kec. Padang Hilir', '2019-06-10 04:42:07'),
('076204', 'ID', 'Kec. Bajenis', '2019-06-10 04:42:07'),
('076205', 'ID', 'Kec. Tebing Tinggi Kota', '2019-06-10 04:42:07'),
('076300', 'ID', 'Kota Pematang Siantar', '2019-06-10 04:42:07'),
('076301', 'ID', 'Kec. Siantar Marihat', '2019-06-10 04:42:07'),
('076302', 'ID', 'Kec. Siantar Selatan', '2019-06-10 04:42:07'),
('076303', 'ID', 'Kec. Siantar Barat', '2019-06-10 04:42:07'),
('076304', 'ID', 'Kec. Siantar Utara', '2019-06-10 04:42:07'),
('076305', 'ID', 'Kec. Siantar Timur', '2019-06-10 04:42:07'),
('076306', 'ID', 'Kec. Siantar Martoba', '2019-06-10 04:42:07'),
('076307', 'ID', 'Kec. Siantar Marimbun', '2019-06-10 04:42:07'),
('076308', 'ID', 'Kec. Siantar Sitalasari', '2019-06-10 04:42:07'),
('076400', 'ID', 'Kota Tanjung Balai', '2019-06-10 04:42:07'),
('076401', 'ID', 'Kec. Datuk Bandar', '2019-06-10 04:42:07'),
('076402', 'ID', 'Kec. Tanjung Balai Selatan', '2019-06-10 04:42:07'),
('076403', 'ID', 'Kec. Tanjung Balai Utara', '2019-06-10 04:42:07'),
('076404', 'ID', 'Kec. S. Tualang Raso', '2019-06-10 04:42:07'),
('076405', 'ID', 'Kec. Teluk Nibung', '2019-06-10 04:42:07'),
('076406', 'ID', 'Kec. Datuk Bandar Timur', '2019-06-10 04:42:07'),
('076500', 'ID', 'Kota Sibolga', '2019-06-10 04:42:07'),
('076501', 'ID', 'Kec. Sibolga Utara', '2019-06-10 04:42:07'),
('076502', 'ID', 'Kec. Sibolga Kota', '2019-06-10 04:42:07'),
('076503', 'ID', 'Kec. Sibolga Selatan', '2019-06-10 04:42:07'),
('076504', 'ID', 'Kec. Sibolga Sambas', '2019-06-10 04:42:07'),
('076600', 'ID', 'Kota Padang Sidempuan', '2019-06-10 04:42:07'),
('076601', 'ID', 'Kec. Padang Sidimpuan Selatan', '2019-06-10 04:42:07'),
('076602', 'ID', 'Kec. Padang Sidimpuan Utara', '2019-06-10 04:42:07'),
('076603', 'ID', 'Kec. Padang Sidimpuan Batu Nad', '2019-06-10 04:42:07'),
('076604', 'ID', 'Kec. Padang Sidimpuan Hutaimba', '2019-06-10 04:42:07'),
('076605', 'ID', 'Kec. Padang Sidimpuan Tenggara', '2019-06-10 04:42:07'),
('076606', 'ID', 'Kec. Padang Sidimpuan Angkola', '2019-06-10 04:42:07'),
('076700', 'ID', 'Kota Gunung Sitoli', '2019-06-10 04:42:07'),
('076701', 'ID', 'Kec. Gunung Sitoli Idanoi', '2019-06-10 04:42:07'),
('076702', 'ID', 'Kec. Gunung Sitoli   Alo Oa', '2019-06-10 04:42:07'),
('076703', 'ID', 'Kec. Gunung Sitoli', '2019-06-10 04:42:07'),
('076704', 'ID', 'Kec. Gunung Sitoli Selatan', '2019-06-10 04:42:07'),
('076705', 'ID', 'Kec. Gunung Sitoli Barat', '2019-06-10 04:42:07'),
('076706', 'ID', 'Kec. Gunung Sitoli Utara', '2019-06-10 04:42:07'),
('080000', 'ID', 'Prop. Sumatera Barat', '2019-06-10 04:42:07'),
('080100', 'ID', 'Kab. Agam', '2019-06-10 04:42:07'),
('080101', 'ID', 'Kec. Tanjung Mutiara', '2019-06-10 04:42:07'),
('080102', 'ID', 'Kec. Lubuk Basung', '2019-06-10 04:42:07'),
('080103', 'ID', 'Kec. Tanjung Raya', '2019-06-10 04:42:07'),
('080104', 'ID', 'Kec. Matur', '2019-06-10 04:42:07'),
('080105', 'ID', 'Kec. IV Koto', '2019-06-10 04:42:07'),
('080107', 'ID', 'Kec. IV Angkat Candung', '2019-06-10 04:42:07'),
('080108', 'ID', 'Kec. Baso', '2019-06-10 04:42:07'),
('080109', 'ID', 'Kec. Tilatang Kamang', '2019-06-10 04:42:07'),
('080110', 'ID', 'Kec. Palembayan', '2019-06-10 04:42:07'),
('080111', 'ID', 'Kec. Palupuh', '2019-06-10 04:42:07'),
('080113', 'ID', 'Kec. Sungai Pua', '2019-06-10 04:42:07'),
('080114', 'ID', 'Kec. Candung', '2019-06-10 04:42:07'),
('080115', 'ID', 'Kec. Kamang Magek', '2019-06-10 04:42:07'),
('080116', 'ID', 'Kec. Banuhampu', '2019-06-10 04:42:07'),
('080117', 'ID', 'Kec. Ampek Angkek', '2019-06-10 04:42:07'),
('080118', 'ID', 'Kec. Malalak', '2019-06-10 04:42:07'),
('080119', 'ID', 'Ampek Nagari', '2019-06-10 04:42:07'),
('080200', 'ID', 'Kab. Pasaman', '2019-06-10 04:42:07'),
('080207', 'ID', 'Kec. Bonjol', '2019-06-10 04:42:07'),
('080208', 'ID', 'Kec. Lubuk Sikaping', '2019-06-10 04:42:07'),
('080210', 'ID', 'Kec. II Koto', '2019-06-10 04:42:07'),
('080211', 'ID', 'Kec. Panti', '2019-06-10 04:42:07'),
('080212', 'ID', 'Kec. III Nagari', '2019-06-10 04:42:07'),
('080213', 'ID', 'Kec. Rao', '2019-06-10 04:42:07'),
('080214', 'ID', 'Kec. Mapat Tunggul', '2019-06-10 04:42:07'),
('080215', 'ID', 'Kec. Mapat Tunggul Selatan', '2019-06-10 04:42:07'),
('080216', 'ID', 'Kec. Simpang Alahan Mati', '2019-06-10 04:42:07'),
('080217', 'ID', 'Kec. Padang Gelugur', '2019-06-10 04:42:07'),
('080218', 'ID', 'Kec. Rao Utara', '2019-06-10 04:42:07'),
('080219', 'ID', 'Kec. Rao Selatan', '2019-06-10 04:42:07'),
('080300', 'ID', 'Kab. Lima Puluh Koto', '2019-06-10 04:42:07'),
('080301', 'ID', 'Kec. Payakumbuh', '2019-06-10 04:42:07'),
('080302', 'ID', 'Kec. Luak', '2019-06-10 04:42:07'),
('080303', 'ID', 'Kec. Harau', '2019-06-10 04:42:07'),
('080304', 'ID', 'Kec. Guguak', '2019-06-10 04:42:07'),
('080305', 'ID', 'Kec. Suliki', '2019-06-10 04:42:07'),
('080306', 'ID', 'Kec. Gunuang Omeh', '2019-06-10 04:42:07'),
('080307', 'ID', 'Kec. Kapur IX', '2019-06-10 04:42:07'),
('080308', 'ID', 'Kec. Pangkalan Koto Baru', '2019-06-10 04:42:07'),
('080309', 'ID', 'Kec. Bukkt Barisan', '2019-06-10 04:42:07'),
('080310', 'ID', 'Kec. Mungka', '2019-06-10 04:42:07'),
('080311', 'ID', 'Kec. Akabiluru', '2019-06-10 04:42:07'),
('080312', 'ID', 'Kec. Situjuah Limo Nagari', '2019-06-10 04:42:07'),
('080313', 'ID', 'Kec. Lareh Sago Halaban', '2019-06-10 04:42:07'),
('080400', 'ID', 'Kab. Solok', '2019-06-10 04:42:07'),
('080404', 'ID', 'Kec. Pantai Cermin', '2019-06-10 04:42:07'),
('080405', 'ID', 'Kec. Lembah Gumanti', '2019-06-10 04:42:07'),
('080406', 'ID', 'Kec. Payung Sekaki', '2019-06-10 04:42:07'),
('080407', 'ID', 'Kec. Lembang Jaya', '2019-06-10 04:42:07'),
('080408', 'ID', 'Kec. Gunung Talang', '2019-06-10 04:42:07'),
('080409', 'ID', 'Kec. Bukit Sundi', '2019-06-10 04:42:07'),
('080410', 'ID', 'Kec. Kubung', '2019-06-10 04:42:07'),
('080411', 'ID', 'Kec. IX Koto Sungai Lasi', '2019-06-10 04:42:07'),
('080412', 'ID', 'Kec. X Koto Diatas', '2019-06-10 04:42:07'),
('080413', 'ID', 'Kec. X Koto Singkarak', '2019-06-10 04:42:07'),
('080414', 'ID', 'Kec. Junjung Sirih', '2019-06-10 04:42:07'),
('080416', 'ID', 'Kec. Hiliran Gumanti', '2019-06-10 04:42:07'),
('080417', 'ID', 'Kec. Tigo Lurah', '2019-06-10 04:42:07'),
('080418', 'ID', 'Kec. Danau Kembar', '2019-06-10 04:42:07'),
('080500', 'ID', 'Kab. Padang Pariaman', '2019-06-10 04:42:07'),
('080501', 'ID', 'Kec. Batang Anai', '2019-06-10 04:42:07'),
('080502', 'ID', 'Kec. Lubuk Alung', '2019-06-10 04:42:07'),
('080503', 'ID', 'Kec. Ulakan Tapakis', '2019-06-10 04:42:07'),
('080504', 'ID', 'Kec. Nan Sabaris', '2019-06-10 04:42:07'),
('080505', 'ID', 'Kec. 2 x 11 VI Lingkung', '2019-06-10 04:42:07'),
('080506', 'ID', 'Kec. VII Koto Sungai Sarik', '2019-06-10 04:42:07'),
('080507', 'ID', 'Kec. V Koto Kampung Dalam', '2019-06-10 04:42:07'),
('080508', 'ID', 'Kec. Sungai Limau', '2019-06-10 04:42:07'),
('080509', 'ID', 'Kec. Sungai Geringging', '2019-06-10 04:42:07'),
('080510', 'ID', 'Kec. IV Koto Aur Malintang', '2019-06-10 04:42:07'),
('080513', 'ID', 'Kec. Batang Gasan', '2019-06-10 04:42:07'),
('080514', 'ID', 'Kec. V Koto Timur', '2019-06-10 04:42:07'),
('080515', 'ID', 'Kec. Patamuan', '2019-06-10 04:42:07'),
('080516', 'ID', 'Kec. Padang Sago', '2019-06-10 04:42:07'),
('080517', 'ID', 'Kec. 2 x 11 Kayu Tanam', '2019-06-10 04:42:07'),
('080518', 'ID', 'Kec. Sintuk Toboh Gadang', '2019-06-10 04:42:07'),
('080519', 'ID', 'Kec. VI Lingkung', '2019-06-10 04:42:07'),
('080600', 'ID', 'Kab. Pesisir Selatan', '2019-06-10 04:42:07'),
('080601', 'ID', 'Kec. Lunang Silaut', '2019-06-10 04:42:07'),
('080602', 'ID', 'Kec. Basa IV Balai Tapan', '2019-06-10 04:42:07'),
('080603', 'ID', 'Kec. Pancung Soal', '2019-06-10 04:42:07'),
('080604', 'ID', 'Kec. Linggo Saribaganti', '2019-06-10 04:42:07'),
('080605', 'ID', 'Kec. Ranah Pesisir', '2019-06-10 04:42:07'),
('080606', 'ID', 'Kec. Lengayang', '2019-06-10 04:42:07'),
('080607', 'ID', 'Kec. Sutera', '2019-06-10 04:42:07'),
('080608', 'ID', 'Kec. Batang Kapas', '2019-06-10 04:42:07'),
('080609', 'ID', 'Kec. IV Jurai', '2019-06-10 04:42:07'),
('080610', 'ID', 'Kec. Bayang', '2019-06-10 04:42:07'),
('080611', 'ID', 'Kec. Koto XI Terusan', '2019-06-10 04:42:07'),
('080612', 'ID', 'Kec. IV Bayang Utara', '2019-06-10 04:42:07'),
('080613', 'ID', 'Kec. Silaut', '2020-02-10 03:07:43'),
('080614', 'ID', 'Kec. Ranah Ampek Hulu Tapan', '2020-02-10 03:07:43'),
('080615', 'ID', 'Kec. Air Pura', '2020-02-10 03:07:43'),
('080616', 'ID', 'Kec. Air Pura', '2020-02-10 03:07:43'),
('080617', 'ID', 'Kec. Iv Nagari Bayang Utara', '2020-02-10 03:07:43'),
('080700', 'ID', 'Kab. Tanah Datar', '2019-06-10 04:42:07'),
('080701', 'ID', 'Kec. Sepuluh Koto', '2019-06-10 04:42:07'),
('080702', 'ID', 'Kec. Batipuh', '2019-06-10 04:42:07'),
('080703', 'ID', 'Kec. Pariangan', '2019-06-10 04:42:07'),
('080704', 'ID', 'Kec. Rambatan', '2019-06-10 04:42:07'),
('080705', 'ID', 'Kec. Lima Kaum', '2019-06-10 04:42:07'),
('080706', 'ID', 'Kec. Tanjung Mas', '2019-06-10 04:42:07'),
('080707', 'ID', 'Kec. Padang Ganting', '2019-06-10 04:42:07'),
('080708', 'ID', 'Kec. Lintau Buo', '2019-06-10 04:42:07'),
('080709', 'ID', 'Kec. Sungayang', '2019-06-10 04:42:07'),
('080710', 'ID', 'Kec. Sungai Tarab', '2019-06-10 04:42:07'),
('080711', 'ID', 'Kec. Salimpaung', '2019-06-10 04:42:07'),
('080712', 'ID', 'Kec. Batipuh Selatan', '2019-06-10 04:42:07'),
('080713', 'ID', 'Kec. Lintau Buo Utara', '2019-06-10 04:42:07'),
('080714', 'ID', 'Kec. Tanjung Baru', '2019-06-10 04:42:07'),
('080800', 'ID', 'Kab. Sawahlunto/ Sijunjung', '2019-06-10 04:42:07'),
('080805', 'ID', 'Kec. Kamang Baru', '2019-06-10 04:42:07'),
('080806', 'ID', 'Kec. Tanjung Gadang', '2019-06-10 04:42:07'),
('080807', 'ID', 'Kec. Sijunjung', '2019-06-10 04:42:07'),
('080808', 'ID', 'Kec. IV Nagari', '2019-06-10 04:42:07'),
('080809', 'ID', 'Kec. Kupitan', '2019-06-10 04:42:07'),
('080810', 'ID', 'Kec. Koto Tujuh', '2019-06-10 04:42:07'),
('080811', 'ID', 'Kec. Sumpur Kudus', '2019-06-10 04:42:07'),
('080812', 'ID', 'Kec. Lubuk Tarok', '2019-06-10 04:42:07'),
('081000', 'ID', 'Kab. Kepulauan Mentawai', '2019-06-10 04:42:07'),
('081001', 'ID', 'Kec. Pagai Utara', '2019-06-10 04:42:07'),
('081002', 'ID', 'Kec. Sipora Selatan', '2019-06-10 04:42:07'),
('081003', 'ID', 'Kec. Siberut Selatan', '2019-06-10 04:42:07'),
('081004', 'ID', 'Kec. Siberut Utara', '2019-06-10 04:42:07'),
('081005', 'ID', 'Kec. Siberut Barat', '2019-06-10 04:42:07'),
('081006', 'ID', 'Kec. Siberut Barat Daya', '2019-06-10 04:42:07'),
('081007', 'ID', 'Kec. Siberut Tengah', '2019-06-10 04:42:07'),
('081008', 'ID', 'Kec. Sipora Utara', '2019-06-10 04:42:07'),
('081009', 'ID', 'Kec. Sikakap', '2019-06-10 04:42:07'),
('081010', 'ID', 'Kec. Pagai Selatan', '2019-06-10 04:42:07'),
('081011', 'ID', 'Siberut Barat', '2019-06-10 04:42:07'),
('081100', 'ID', 'Kab. Solok Selatan', '2019-06-10 04:42:07'),
('081101', 'ID', 'Kec. Sangir', '2019-06-10 04:42:07'),
('081102', 'ID', 'Kec. Sungai Pagu', '2019-06-10 04:42:07'),
('081103', 'ID', 'Kec. Koto Parik Gadang Diateh', '2019-06-10 04:42:07'),
('081104', 'ID', 'Kec. Sangir Jujuhan', '2019-06-10 04:42:07'),
('081105', 'ID', 'Kec. Sangir Batanghari', '2019-06-10 04:42:07'),
('081106', 'ID', 'Kec. Pauah Duo', '2019-06-10 04:42:07'),
('081107', 'ID', 'Kec. Sangir Balai Janggo', '2019-06-10 04:42:07'),
('081200', 'ID', 'Kab. Dharmas Raya', '2019-06-10 04:42:07'),
('081201', 'ID', 'Kec. Sungai Rumbai', '2019-06-10 04:42:07'),
('081202', 'ID', 'Kec. Koto Baru', '2019-06-10 04:42:07'),
('081203', 'ID', 'Kec. Sitiung', '2019-06-10 04:42:07'),
('081204', 'ID', 'Kec. Pulau Punjung', '2019-06-10 04:42:07'),
('081205', 'ID', 'Kec. Sembilan Koto', '2019-06-10 04:42:07'),
('081206', 'ID', 'Kec. Timpeh', '2019-06-10 04:42:07'),
('081207', 'ID', 'Kec. Koto Salak', '2019-06-10 04:42:07'),
('081208', 'ID', 'Kec. Tiumang', '2019-06-10 04:42:07'),
('081209', 'ID', 'Kec. Padang Laweh', '2019-06-10 04:42:07'),
('081210', 'ID', 'Kec. Asam Jujuhan', '2019-06-10 04:42:07'),
('081211', 'ID', 'Kec. Koto Besar', '2019-06-10 04:42:07'),
('081300', 'ID', 'Kab. Pasaman Barat', '2019-06-10 04:42:07'),
('081301', 'ID', 'Kec. Sungai Beremas', '2019-06-10 04:42:07'),
('081302', 'ID', 'Kec. Ranah Batahan', '2019-06-10 04:42:07'),
('081303', 'ID', 'Kec. Lembah Melintang', '2019-06-10 04:42:07'),
('081304', 'ID', 'Kec. Gunung Tuleh', '2019-06-10 04:42:07'),
('081305', 'ID', 'Kec. Pasaman', '2019-06-10 04:42:07'),
('081306', 'ID', 'Kec. Kinali', '2019-06-10 04:42:07'),
('081307', 'ID', 'Kec. Talamau', '2019-06-10 04:42:07'),
('081308', 'ID', 'Kec. Koto Balingka', '2019-06-10 04:42:07'),
('081309', 'ID', 'Kec. Luhak Nan Duo', '2019-06-10 04:42:07'),
('081310', 'ID', 'Kec. Sasak Ranah Pesisir', '2019-06-10 04:42:07'),
('081311', 'ID', 'Kec. Sungai Aur', '2019-06-10 04:42:07'),
('086000', 'ID', 'Kota Bukittinggi', '2019-06-10 04:42:07'),
('086001', 'ID', 'Kec. Guguk Panjang', '2019-06-10 04:42:07'),
('086002', 'ID', 'Kec. Mandiangin Koto Selayan', '2019-06-10 04:42:07'),
('086003', 'ID', 'Kec. Aur Birugo Tigo Baleh', '2019-06-10 04:42:07'),
('086100', 'ID', 'Kota Padang', '2019-06-10 04:42:07'),
('086101', 'ID', 'Kec. Bungus Teluk Kabung', '2019-06-10 04:42:07'),
('086102', 'ID', 'Kec. Lubuk Kilangan', '2019-06-10 04:42:07'),
('086103', 'ID', 'Kec. Lubuk Begalung', '2019-06-10 04:42:07'),
('086104', 'ID', 'Kec. Padang Selatan', '2019-06-10 04:42:07'),
('086105', 'ID', 'Kec. Padang Timur', '2019-06-10 04:42:07'),
('086106', 'ID', 'Kec. Padang Barat', '2019-06-10 04:42:07'),
('086107', 'ID', 'Kec. Padang Utara', '2019-06-10 04:42:07'),
('086108', 'ID', 'Kec. Nanggalo', '2019-06-10 04:42:07'),
('086109', 'ID', 'Kec. Kuranji', '2019-06-10 04:42:07'),
('086110', 'ID', 'Kec. Pauh', '2019-06-10 04:42:07'),
('086111', 'ID', 'Kec. Koto Tangah', '2019-06-10 04:42:07'),
('086200', 'ID', 'Kota Padang Panjang', '2019-06-10 04:42:07'),
('086201', 'ID', 'Kec. Padang Panjang Barat', '2019-06-10 04:42:07'),
('086202', 'ID', 'Kec. Padang Panjang Timur', '2019-06-10 04:42:07'),
('086300', 'ID', 'Kota Sawah Lunto', '2019-06-10 04:42:07'),
('086301', 'ID', 'Kec. Silungkang', '2019-06-10 04:42:07'),
('086302', 'ID', 'Kec. Lembah Segar', '2019-06-10 04:42:07'),
('086303', 'ID', 'Kec. Barangin', '2019-06-10 04:42:07'),
('086304', 'ID', 'Kec. Talawi', '2019-06-10 04:42:07'),
('086400', 'ID', 'Kota Solok', '2019-06-10 04:42:07'),
('086401', 'ID', 'Kec. Lubuk Sikarah', '2019-06-10 04:42:07'),
('086402', 'ID', 'Kec. Tanjung Harapan', '2019-06-10 04:42:07'),
('086500', 'ID', 'Kota Payakumbuh', '2019-06-10 04:42:07'),
('086501', 'ID', 'Kec. Payakumbuh Barat', '2019-06-10 04:42:07'),
('086502', 'ID', 'Kec. Payakumbuh Timur', '2019-06-10 04:42:07'),
('086503', 'ID', 'Kec. Payakumbuh Utara', '2019-06-10 04:42:07'),
('086504', 'ID', 'Kec. Lamposi Tigo Nagori', '2019-06-10 04:42:07'),
('086505', 'ID', 'Kec. Payakumbuh Selatan', '2019-06-10 04:42:07'),
('086600', 'ID', 'Kota Pariaman', '2019-06-10 04:42:07'),
('086601', 'ID', 'Kec. Pariaman Selatan', '2019-06-10 04:42:07'),
('086602', 'ID', 'Kec. Pariaman Tengah', '2019-06-10 04:42:07'),
('086603', 'ID', 'Kec. Pariaman Utara', '2019-06-10 04:42:07'),
('086604', 'ID', 'Kec. Pariaman Timur', '2019-06-10 04:42:07'),
('090000', 'ID', 'Prop. Riau', '2019-06-10 04:42:07'),
('090100', 'ID', 'Kab. Kampar', '2019-06-10 04:42:07'),
('090101', 'ID', 'Kec. XIII Koto Kampar', '2019-06-10 04:42:07'),
('090102', 'ID', 'Kec. Kampar Kiri', '2019-06-10 04:42:07'),
('090103', 'ID', 'Kec. Kampar', '2019-06-10 04:42:07'),
('090104', 'ID', 'Kec. Tambang', '2019-06-10 04:42:07'),
('090105', 'ID', 'Kec. Bangkinang', '2019-06-10 04:42:07'),
('090106', 'ID', 'Kec. Bangkinang Barat', '2019-06-10 04:42:07'),
('090107', 'ID', 'Kec. Siak Hulu', '2019-06-10 04:42:07'),
('090108', 'ID', 'Kec. Tapung', '2019-06-10 04:42:07'),
('090109', 'ID', 'Kec. Kampar Kiri Hulu', '2019-06-10 04:42:07'),
('090110', 'ID', 'Kec. Kampar Kiri Hilir', '2019-06-10 04:42:07'),
('090111', 'ID', 'Kec. Tapung Hulu', '2019-06-10 04:42:07'),
('090112', 'ID', 'Kec. Tapung Hilir', '2019-06-10 04:42:07'),
('090113', 'ID', 'Kec. Tapung Kiri', '2019-06-10 04:42:07'),
('090114', 'ID', 'Kec. Salo', '2019-06-10 04:42:07'),
('090115', 'ID', 'Kec. Rumbio Jaya', '2019-06-10 04:42:07'),
('090116', 'ID', 'Kec. Bangkinang Seberang', '2019-06-10 04:42:07'),
('090117', 'ID', 'Kec. Perhentian Raja', '2019-06-10 04:42:07'),
('090118', 'ID', 'Kec. Kampar Timur', '2019-06-10 04:42:07'),
('090119', 'ID', 'Kec. Kampar Utara', '2019-06-10 04:42:07'),
('090120', 'ID', 'Kec. Kampar Kiri Tengah', '2019-06-10 04:42:07'),
('090121', 'ID', 'Kec. Gunung Sahilan', '2019-06-10 04:42:07'),
('090190', 'ID', 'Koto Kampar Hulu', '2019-06-10 04:42:07'),
('090200', 'ID', 'Kab. Bengkalis', '2019-06-10 04:42:07'),
('090201', 'ID', 'Kec. Mandau', '2019-06-10 04:42:07'),
('090205', 'ID', 'Kec. Bengkalis', '2019-06-10 04:42:07'),
('090206', 'ID', 'Kec. Bantan', '2019-06-10 04:42:07'),
('090207', 'ID', 'Kec. Bukit Batu', '2019-06-10 04:42:07'),
('090208', 'ID', 'Kec. Rupat', '2019-06-10 04:42:07'),
('090209', 'ID', 'Kec. Rupat Utara', '2019-06-10 04:42:07'),
('090212', 'ID', 'Kec. Siak Kecil', '2019-06-10 04:42:07'),
('090213', 'ID', 'Kec. Pinggir', '2019-06-10 04:42:07'),
('090400', 'ID', 'Kab. Indragiri Hulu', '2019-06-10 04:42:07'),
('090401', 'ID', 'Kec. Peranap', '2019-06-10 04:42:07'),
('090402', 'ID', 'Kec. Pasir Penyu', '2019-06-10 04:42:07'),
('090403', 'ID', 'Kec. Kelayang', '2019-06-10 04:42:07'),
('090404', 'ID', 'Kec. Seberida', '2019-06-10 04:42:07'),
('090405', 'ID', 'Kec. Rengat', '2019-06-10 04:42:07'),
('090406', 'ID', 'Kec. Rengat Barat', '2019-06-10 04:42:07'),
('090407', 'ID', 'Kec. Lirik', '2019-06-10 04:42:07'),
('090408', 'ID', 'Kec. Batang Gansal', '2019-06-10 04:42:07'),
('090409', 'ID', 'Kec. Batang Cenaku', '2019-06-10 04:42:07'),
('090410', 'ID', 'Kec. Batang Peranap', '2019-06-10 04:42:07'),
('090411', 'ID', 'Kec. Lubuk Batu Jaya', '2019-06-10 04:42:07'),
('090412', 'ID', 'Kec. Sei Lala', '2019-06-10 04:42:07'),
('090413', 'ID', 'Kec. Rakit Kulim', '2019-06-10 04:42:07'),
('090414', 'ID', 'Kec. Kuala Cenaku', '2019-06-10 04:42:07'),
('090500', 'ID', 'Kab. Indragiri Hilir', '2019-06-10 04:42:07'),
('090501', 'ID', 'Kec. Keritang', '2019-06-10 04:42:07'),
('090502', 'ID', 'Kec. Reteh', '2019-06-10 04:42:07'),
('090503', 'ID', 'Kec. Enok', '2019-06-10 04:42:07'),
('090504', 'ID', 'Kec. Tanah Merah', '2019-06-10 04:42:07'),
('090505', 'ID', 'Kec. Kuala Indragiri', '2019-06-10 04:42:07'),
('090506', 'ID', 'Kec. Tembilahan', '2019-06-10 04:42:07'),
('090507', 'ID', 'Kec. Tempuling', '2019-06-10 04:42:07'),
('090508', 'ID', 'Kec. Batang Tuaka', '2019-06-10 04:42:07'),
('090509', 'ID', 'Kec. Gaung Anak Serka', '2019-06-10 04:42:07'),
('090510', 'ID', 'Kec. Gaung', '2019-06-10 04:42:07'),
('090511', 'ID', 'Kec. Mandah', '2019-06-10 04:42:07'),
('090512', 'ID', 'Kec. Kateman', '2019-06-10 04:42:07'),
('090513', 'ID', 'Kec. Kemuning', '2019-06-10 04:42:07'),
('090514', 'ID', 'Kec. Pelangiran', '2019-06-10 04:42:07'),
('090515', 'ID', 'Kec. Pulau Burung', '2019-06-10 04:42:07'),
('090516', 'ID', 'Kec. Teluk Blengkong', '2019-06-10 04:42:07'),
('090517', 'ID', 'Kec. Tembilahan Hulu', '2019-06-10 04:42:07'),
('090518', 'ID', 'Kec. Concong', '2019-06-10 04:42:07'),
('090519', 'ID', 'Kec. Kempas', '2019-06-10 04:42:07'),
('090520', 'ID', 'Kec. Sungai Batang', '2019-06-10 04:42:07'),
('090800', 'ID', 'Kab. Pelalawan', '2019-06-10 04:42:07'),
('090801', 'ID', 'Kec. Langgam', '2019-06-10 04:42:07'),
('090802', 'ID', 'Kec. Pangkalan Kuras', '2019-06-10 04:42:07'),
('090803', 'ID', 'Kec. Bunut', '2019-06-10 04:42:07'),
('090804', 'ID', 'Kec. Kuala Kampar', '2019-06-10 04:42:07'),
('090805', 'ID', 'Kec. Pangkalan Kerinci', '2019-06-10 04:42:07'),
('090806', 'ID', 'Kec. Ukui', '2019-06-10 04:42:07'),
('090807', 'ID', 'Kec. Pangkalan Lesung', '2019-06-10 04:42:07'),
('090808', 'ID', 'Kec. Kerumutan', '2019-06-10 04:42:07'),
('090809', 'ID', 'Kec. Pelalawan', '2019-06-10 04:42:07'),
('090810', 'ID', 'Kec. Teluk Meranti', '2019-06-10 04:42:07'),
('090811', 'ID', 'Kec. Bandar Sei Kijang', '2019-06-10 04:42:07'),
('090812', 'ID', 'Kec. Bandar Petalangan', '2019-06-10 04:42:07'),
('090900', 'ID', 'Kab. Rokan Hilir', '2019-06-10 04:42:07'),
('090901', 'ID', 'Kec. Ujung Batu', '2019-06-10 04:42:07'),
('090902', 'ID', 'Kec. Rokan IV Koto', '2019-06-10 04:42:07'),
('090903', 'ID', 'Kec. Rambah', '2019-06-10 04:42:07'),
('090904', 'ID', 'Kec. Tembusai', '2019-06-10 04:42:07'),
('090905', 'ID', 'Kec. Kepenuhan', '2019-06-10 04:42:07'),
('090906', 'ID', 'Kec. Kunto Darussalam', '2019-06-10 04:42:07'),
('090907', 'ID', 'Kec. Rambah Samo', '2019-06-10 04:42:07'),
('090908', 'ID', 'Kec. Rambah Hilir', '2019-06-10 04:42:07'),
('090909', 'ID', 'Kec. Tembusai Utara', '2019-06-10 04:42:07'),
('090910', 'ID', 'Kec. Bangun Purba', '2019-06-10 04:42:07'),
('090911', 'ID', 'Kec. Tandun', '2019-06-10 04:42:07'),
('090912', 'ID', 'Kec. Kabun', '2019-06-10 04:42:07'),
('090913', 'ID', 'Kec. Bonai Darussalam', '2019-06-10 04:42:07'),
('090914', 'ID', 'Kec. Pagaran Tapah Darussalam', '2019-06-10 04:42:07'),
('090916', 'ID', 'Kec. Pendalian IV Koto', '2019-06-10 04:42:07'),
('090917', 'ID', 'Kec. Kepenuhan Hulu', '2019-06-10 04:42:07'),
('091000', 'ID', 'Kab. Siak', '2019-06-10 04:42:07'),
('091001', 'ID', 'Kec. Kubu', '2019-06-10 04:42:07'),
('091002', 'ID', 'Kec. Bangko', '2019-06-10 04:42:07'),
('091003', 'ID', 'Kec. Tanah Putih', '2019-06-10 04:42:07'),
('091004', 'ID', 'Kec. Rimba Melintang', '2019-06-10 04:42:07'),
('091005', 'ID', 'Kec. Bagan Sinembah', '2019-06-10 04:42:07'),
('091006', 'ID', 'Kec. Pasir Limau Kapas', '2019-06-10 04:42:07'),
('091007', 'ID', 'Kec. Sinaboi', '2019-06-10 04:42:07'),
('091008', 'ID', 'Kec. Tanah Putih Tanjung Melaw', '2019-06-10 04:42:07'),
('091009', 'ID', 'Kec. Pujud', '2019-06-10 04:42:07'),
('091010', 'ID', 'Kec. Bangko Pusako', '2019-06-10 04:42:07'),
('091011', 'ID', 'Kec. Simpang Kanan', '2019-06-10 04:42:07'),
('091012', 'ID', 'Kec. Batu Hampar', '2019-06-10 04:42:07'),
('091013', 'ID', 'Kec. Rantau Kopar', '2019-06-10 04:42:07'),
('091014', 'ID', 'Kec. Pekaitan', '2020-02-10 03:07:43'),
('091015', 'ID', 'Kec. Kubu Babussalam', '2020-02-10 03:07:43'),
('091016', 'ID', 'Kec. Bagan Sinembah Jaya', '2020-02-10 03:07:43'),
('091017', 'ID', 'Kec. Balai Jaya', '2020-02-10 03:07:43'),
('091018', 'ID', 'Kec. Tanjung Medan', '2020-02-10 03:07:43'),
('091100', 'ID', 'Kab. Kuantan Singingi', '2019-06-10 04:42:07'),
('091101', 'ID', 'Kec. Minas', '2019-06-10 04:42:07'),
('091102', 'ID', 'Kec. Siak', '2019-06-10 04:42:07'),
('091103', 'ID', 'Kec. Sungai Apit', '2019-06-10 04:42:07'),
('091104', 'ID', 'Kec. Tualang', '2019-06-10 04:42:07'),
('091105', 'ID', 'Kec. Kerinci Kanan', '2019-06-10 04:42:07'),
('091106', 'ID', 'Kec. Dayun', '2019-06-10 04:42:07'),
('091107', 'ID', 'Kec. Bunga Raya', '2019-06-10 04:42:07'),
('091108', 'ID', 'Kec. Sungai Mandau', '2019-06-10 04:42:07'),
('091109', 'ID', 'Kec. Kandis', '2019-06-10 04:42:07'),
('091110', 'ID', 'Kec. Lubuk Dalam', '2019-06-10 04:42:07'),
('091111', 'ID', 'Kec. Koto Gasip', '2019-06-10 04:42:07'),
('091112', 'ID', 'Kec. Pusako', '2019-06-10 04:42:07'),
('091113', 'ID', 'Kec. Sabak Auh', '2019-06-10 04:42:07'),
('091114', 'ID', 'Kec. Sungai Mempura', '2019-06-10 04:42:07'),
('091115', 'ID', 'Mempura', '2019-06-10 04:42:07'),
('091400', 'ID', 'Kab. Rokan Hulu', '2019-06-10 04:42:07'),
('091401', 'ID', 'Kec. Kuantan Mudik', '2019-06-10 04:42:07'),
('091402', 'ID', 'Kec. Kuantan Tengah', '2019-06-10 04:42:07'),
('091403', 'ID', 'Kec. Benai', '2019-06-10 04:42:07'),
('091404', 'ID', 'Kec. Singingi', '2019-06-10 04:42:07'),
('091405', 'ID', 'Kec. Kuantan Hilir', '2019-06-10 04:42:07'),
('091406', 'ID', 'Kec. Cerenti', '2019-06-10 04:42:07'),
('091407', 'ID', 'Kec. Pangean', '2019-06-10 04:42:07'),
('091408', 'ID', 'Kec. Logas Tanah Darat', '2019-06-10 04:42:07'),
('091409', 'ID', 'Kec. Inuman', '2019-06-10 04:42:07'),
('091410', 'ID', 'Kec. Singingi Hilir', '2019-06-10 04:42:07'),
('091411', 'ID', 'Kec. Hulu Kuantan', '2019-06-10 04:42:07'),
('091412', 'ID', 'Kec. Gunung Toar', '2019-06-10 04:42:07'),
('091413', 'ID', 'Kec. Kuantan Hilir Seberang', '2020-02-10 03:07:43'),
('091414', 'ID', 'Kec. Sentajo Raya', '2020-02-10 03:07:43'),
('091415', 'ID', 'Kec. Pucuk Rantau', '2020-02-10 03:07:43'),
('091500', 'ID', 'Kab. Kepulauan Meranti', '2019-06-10 04:42:07'),
('091501', 'ID', 'Kec. Merbau', '2019-06-10 04:42:07'),
('091502', 'ID', 'Kec. Rangsang', '2019-06-10 04:42:07'),
('091503', 'ID', 'Kec. Rangsang Barat', '2019-06-10 04:42:07'),
('091504', 'ID', 'Kec. Tebing Tinggi', '2019-06-10 04:42:07'),
('091505', 'ID', 'Kec. Tebing Tinggi Barat', '2019-06-10 04:42:07'),
('091506', 'ID', 'Kec. Tasik Putri Puyu', '2019-06-10 04:42:07'),
('091507', 'ID', 'Kec. Pulau Merbau', '2019-06-10 04:42:07'),
('091508', 'ID', 'Kec. Tebing Tinggi Timur', '2019-06-10 04:42:07'),
('091509', 'ID', 'Kec. Rangsang Pesisir', '2019-06-10 04:42:07'),
('096000', 'ID', 'Kota Pekanbaru', '2019-06-10 04:42:07'),
('096001', 'ID', 'Kec. Tampan', '2019-06-10 04:42:07'),
('096002', 'ID', 'Kec. Bukit Raya', '2019-06-10 04:42:07'),
('096003', 'ID', 'Kec. Lima Puluh', '2019-06-10 04:42:07'),
('096004', 'ID', 'Kec. Sail', '2019-06-10 04:42:07'),
('096005', 'ID', 'Kec. Pekanbaru Kota', '2019-06-10 04:42:07'),
('096006', 'ID', 'Kec. Sukajadi', '2019-06-10 04:42:07'),
('096007', 'ID', 'Kec. Senapelan', '2019-06-10 04:42:07'),
('096008', 'ID', 'Kec. Rumbai', '2019-06-10 04:42:07'),
('096009', 'ID', 'Kec. Tenayan Raya', '2019-06-10 04:42:07'),
('096010', 'ID', 'Kec. Marpoyan Damai', '2019-06-10 04:42:07'),
('096011', 'ID', 'Kec. Rumbai Pesisir', '2019-06-10 04:42:07'),
('096012', 'ID', 'Kec. Payung Sekaki', '2019-06-10 04:42:07'),
('096200', 'ID', 'Kota Dumai', '2019-06-10 04:42:07'),
('096201', 'ID', 'Kec. Bukit Kapur', '2019-06-10 04:42:07'),
('096202', 'ID', 'Kec. Dumai Barat', '2019-06-10 04:42:07'),
('096203', 'ID', 'Kec. Dumai Timur', '2019-06-10 04:42:07'),
('096204', 'ID', 'Kec. Medang Kampai', '2019-06-10 04:42:07'),
('096205', 'ID', 'Kec. Sungai Sembilan', '2019-06-10 04:42:07'),
('096206', 'ID', 'Kec. Dumai Kota', '2019-06-10 04:42:07'),
('096207', 'ID', 'Kec. Dumai Selatan', '2019-06-10 04:42:07'),
('100000', 'ID', 'Prop. Jambi', '2019-06-10 04:42:07'),
('100100', 'ID', 'Kab. Batang Hari', '2019-06-10 04:42:07'),
('100101', 'ID', 'Kec. Mersan', '2019-06-10 04:42:07'),
('100102', 'ID', 'Kec. Muara Tembesi', '2019-06-10 04:42:07'),
('100103', 'ID', 'Kec. Batin XXIV', '2019-06-10 04:42:07'),
('100104', 'ID', 'Kec. Muara Bulian', '2019-06-10 04:42:07'),
('100105', 'ID', 'Kec. Pemayung', '2019-06-10 04:42:07'),
('100106', 'ID', 'Kec. Maro Sebo Ulu', '2019-06-10 04:42:07'),
('100107', 'ID', 'Kec. Bajubang', '2019-06-10 04:42:07'),
('100108', 'ID', 'Kec. Maro Sebo Ilir', '2019-06-10 04:42:07'),
('100200', 'ID', 'Kab. Bungo', '2019-06-10 04:42:07'),
('100201', 'ID', 'Kec. Tanah Tumbuh', '2019-06-10 04:42:07'),
('100202', 'ID', 'Kec. Tanah Sepenggal', '2019-06-10 04:42:07'),
('100203', 'ID', 'Kec. Jujuhan', '2019-06-10 04:42:07'),
('100204', 'ID', 'Kec. Rantau Pandan', '2019-06-10 04:42:07'),
('100205', 'ID', 'Kec. Pasar Muara Bungo', '2019-06-10 04:42:07'),
('100206', 'ID', 'Kec. Pelepat', '2019-06-10 04:42:07'),
('100208', 'ID', 'Kec. Pelepat Ilir', '2019-06-10 04:42:07'),
('100209', 'ID', 'Kec. Limbur Lubuk Mengkuang', '2019-06-10 04:42:07'),
('100210', 'ID', 'Kec. Bathin II Babeko', '2019-06-10 04:42:07'),
('100211', 'ID', 'Kec. Muko Muko Batin VII', '2019-06-10 04:42:07'),
('100212', 'ID', 'Kec. Bungo Dani', '2019-06-10 04:42:07'),
('100213', 'ID', 'Kec. Bathin III', '2019-06-10 04:42:07'),
('100214', 'ID', 'Kec. Bathin III Ulu', '2019-06-10 04:42:07'),
('100215', 'ID', 'Kec. Tanah Sepenggal Luas', '2019-06-10 04:42:07'),
('100216', 'ID', 'Kec. Bathin II Pelayang', '2019-06-10 04:42:07'),
('100217', 'ID', 'Kec. Jujuhan Ilir', '2019-06-10 04:42:07'),
('100218', 'ID', 'Kec. Rimbo Tengah', '2019-06-10 04:42:07'),
('100299', 'ID', 'Tanah Sepenggal Lintas', '2019-06-10 04:42:07'),
('100300', 'ID', 'Kab. Sarolangun', '2019-06-10 04:42:07'),
('100301', 'ID', 'Kec. Batang Asai', '2019-06-10 04:42:07'),
('100302', 'ID', 'Kec. Limun', '2019-06-10 04:42:07'),
('100303', 'ID', 'Kec. Sarolangon', '2019-06-10 04:42:07'),
('100304', 'ID', 'Kec. Pelawan', '2019-06-10 04:42:07'),
('100305', 'ID', 'Kec. Pauh', '2019-06-10 04:42:07'),
('100306', 'ID', 'Kec. Mandiangin', '2019-06-10 04:42:07'),
('100307', 'ID', 'Kec. Air Hitam', '2019-06-10 04:42:07'),
('100308', 'ID', 'Kec. Bathin VIII', '2019-06-10 04:42:07'),
('100309', 'ID', 'Kec. Singkut', '2019-06-10 04:42:07'),
('100310', 'ID', 'Kec. Cermin Nan Gadang', '2019-06-10 04:42:07'),
('100400', 'ID', 'Kab. Tanjung Jabung Barat', '2019-06-10 04:42:07'),
('100401', 'ID', 'Kec. Tungkal Ulu', '2019-06-10 04:42:07'),
('100402', 'ID', 'Kec. Pengabuan', '2019-06-10 04:42:07'),
('100403', 'ID', 'Kec. Tungkal Ilir', '2019-06-10 04:42:07'),
('100404', 'ID', 'Kec. Betara', '2019-06-10 04:42:07'),
('100405', 'ID', 'Kec. Merlung', '2019-06-10 04:42:07'),
('100406', 'ID', 'Kec. Batang Asam', '2019-06-10 04:42:07'),
('100407', 'ID', 'Kec. Tebing Tinggi', '2019-06-10 04:42:07'),
('100408', 'ID', 'Kec. Renah Mendalu', '2019-06-10 04:42:07'),
('100409', 'ID', 'Kec. Muara Papalik', '2019-06-10 04:42:07'),
('100410', 'ID', 'Kec. Senyerang', '2019-06-10 04:42:07'),
('100411', 'ID', 'Kec. Bram Itam', '2019-06-10 04:42:07'),
('100412', 'ID', 'Kec. Seberang Kota', '2019-06-10 04:42:07'),
('100413', 'ID', 'Kec. Kuala Betara', '2019-06-10 04:42:07'),
('100500', 'ID', 'Kab. Kerinci', '2019-06-10 04:42:07'),
('100501', 'ID', 'Kec. Gunung Raya', '2019-06-10 04:42:07'),
('100502', 'ID', 'Kec. Batang Merangin', '2019-06-10 04:42:07'),
('100503', 'ID', 'Kec. Keliling Danau', '2019-06-10 04:42:07'),
('100504', 'ID', 'Kec. Danau Kerinci', '2019-06-10 04:42:07'),
('100505', 'ID', 'Kec. Sitinjau Laut', '2019-06-10 04:42:07'),
('100507', 'ID', 'Kec. Air Hangat', '2019-06-10 04:42:07'),
('100508', 'ID', 'Kec. Gunung Kerinci', '2019-06-10 04:42:07'),
('100509', 'ID', 'Kec. Kayu Aro', '2019-06-10 04:42:07'),
('100511', 'ID', 'Kec. Air Hangat Timur', '2019-06-10 04:42:07'),
('100515', 'ID', 'Kec. Gunung Tujuh', '2019-06-10 04:42:07'),
('100516', 'ID', 'Kec. Siulak', '2019-06-10 04:42:07'),
('100517', 'ID', 'Kec. Depati Tujuh', '2019-06-10 04:42:07'),
('100518', 'ID', 'Kec. Bukit Kerman', '2020-02-10 03:07:43'),
('100519', 'ID', 'Kec. Air Hangat Barat', '2020-02-10 03:07:43'),
('100520', 'ID', 'Kec. Siulak Mukai', '2020-02-10 03:07:43'),
('100521', 'ID', 'Kec. Kayu Aro Barat', '2020-02-10 03:07:43'),
('100600', 'ID', 'Kab. Tebo', '2019-06-10 04:42:07'),
('100601', 'ID', 'Kec. Rimbo Bujang', '2019-06-10 04:42:07'),
('100602', 'ID', 'Kec. Tebo Ilir', '2019-06-10 04:42:07'),
('100603', 'ID', 'Kec. Tebo Tengah', '2019-06-10 04:42:07'),
('100604', 'ID', 'Kec. Tebo Ulu', '2019-06-10 04:42:07'),
('100605', 'ID', 'Kec. Sumay', '2019-06-10 04:42:07'),
('100606', 'ID', 'Kec. VII Koto', '2019-06-10 04:42:07'),
('100607', 'ID', 'Kec. Rimbo Ilir', '2019-06-10 04:42:07'),
('100608', 'ID', 'Kec. Rimbo Ulu', '2019-06-10 04:42:07'),
('100609', 'ID', 'Kec. Tengah Ilir', '2019-06-10 04:42:07'),
('100610', 'ID', 'Kec. Serai Serumpun', '2019-06-10 04:42:07'),
('100611', 'ID', 'Kec. VII Koto Ilir', '2019-06-10 04:42:07'),
('100612', 'ID', 'Kec. Muara Tabir', '2019-06-10 04:42:07'),
('100700', 'ID', 'Kab. Muaro Jambi', '2019-06-10 04:42:07'),
('100701', 'ID', 'Kec. Jambi Luar Kota', '2019-06-10 04:42:07'),
('100702', 'ID', 'Kec. Mestong', '2019-06-10 04:42:07'),
('100703', 'ID', 'Kec. Kumpeh Ulu', '2019-06-10 04:42:07'),
('100704', 'ID', 'Kec. Sekernan', '2019-06-10 04:42:07'),
('100705', 'ID', 'Kec. Maro Sebo', '2019-06-10 04:42:07'),
('100706', 'ID', 'Kec. Kumpeh', '2019-06-10 04:42:07'),
('100707', 'ID', 'Kec. Sungai Bahar', '2019-06-10 04:42:07'),
('100708', 'ID', 'Kec. Sungai Gelam', '2019-06-10 04:42:07'),
('100709', 'ID', 'Kec. Bahar Utara', '2020-02-10 03:07:43'),
('100710', 'ID', 'Kec. Bahar Selatan', '2020-02-10 03:07:43'),
('100711', 'ID', 'Kec. Taman Rajo', '2020-02-10 03:07:43'),
('100800', 'ID', 'Kab. Tanjung Jabung Timur', '2019-06-10 04:42:07'),
('100801', 'ID', 'Kec. Muara Sabak', '2019-06-10 04:42:07'),
('100802', 'ID', 'Kec. Mendahara', '2019-06-10 04:42:07'),
('100803', 'ID', 'Kec. Dendang', '2019-06-10 04:42:07'),
('100804', 'ID', 'Kec. Nipah Panjang', '2019-06-10 04:42:07'),
('100805', 'ID', 'Kec. Rantau Rasau', '2019-06-10 04:42:07'),
('100806', 'ID', 'Kec. Sadu', '2019-06-10 04:42:07'),
('100807', 'ID', 'Kec. Mendahara Ulu', '2019-06-10 04:42:07'),
('100808', 'ID', 'Kec. Geragai', '2019-06-10 04:42:07'),
('100809', 'ID', 'Kec. Muara Sabak Barat', '2019-06-10 04:42:07'),
('100811', 'ID', 'Kec. Kuala Jambi', '2019-06-10 04:42:07'),
('100812', 'ID', 'Kec. Berbak', '2019-06-10 04:42:07'),
('100890', 'ID', 'Muara Sabak Timur', '2019-06-10 04:42:07'),
('100900', 'ID', 'Kab. Merangin', '2019-06-10 04:42:07'),
('100901', 'ID', 'Kec. Jangkat', '2019-06-10 04:42:07'),
('100902', 'ID', 'Kec. Bangko', '2019-06-10 04:42:07'),
('100903', 'ID', 'Kec. Pamenang', '2019-06-10 04:42:07'),
('100904', 'ID', 'Kec. Muara Siau', '2019-06-10 04:42:07'),
('100905', 'ID', 'Kec. Sungai Manau', '2019-06-10 04:42:07'),
('100906', 'ID', 'Kec. Tabir', '2019-06-10 04:42:07'),
('100907', 'ID', 'Kec. Tabir Ulu', '2019-06-10 04:42:07'),
('100909', 'ID', 'Kec. Lembah Masuarai', '2019-06-10 04:42:07'),
('100910', 'ID', 'Kec. Tabir Selatan', '2019-06-10 04:42:07'),
('100911', 'ID', 'Kec. Bangko Barat', '2019-06-10 04:42:07'),
('100912', 'ID', 'Kec. Nalo Tantan', '2019-06-10 04:42:07'),
('100913', 'ID', 'Kec. Batang Masumai', '2019-06-10 04:42:07'),
('100914', 'ID', 'Kec. Pamenang Barat', '2019-06-10 04:42:07'),
('100915', 'ID', 'Kec. Tabir Ilir', '2019-06-10 04:42:07'),
('100916', 'ID', 'Kec. Tabir Timur', '2019-06-10 04:42:07'),
('100917', 'ID', 'Kec. Renah Pemparap', '2019-06-10 04:42:07'),
('100918', 'ID', 'Kec. Pangkalan Jambu', '2019-06-10 04:42:07'),
('100919', 'ID', 'Kec. Sungai Tenang', '2019-06-10 04:42:07'),
('100920', 'ID', 'Kec. Tiang Pumpung', '2019-06-10 04:42:07'),
('100921', 'ID', 'Kec. Renah Pamenang', '2019-06-10 04:42:07'),
('100922', 'ID', 'Kec. Pamenang Selatan', '2019-06-10 04:42:07'),
('100923', 'ID', 'Kec. Tabir Lintas', '2019-06-10 04:42:07'),
('100924', 'ID', 'Kec. Margo Tabir', '2019-06-10 04:42:07'),
('100925', 'ID', 'Kec. Tabir Barat', '2019-06-10 04:42:07'),
('106000', 'ID', 'Kota Jambi', '2019-06-10 04:42:07'),
('106001', 'ID', 'Kec. Kota Baru', '2019-06-10 04:42:07'),
('106002', 'ID', 'Kec. Jambi Selatan', '2019-06-10 04:42:07'),
('106003', 'ID', 'Kec. Jelutung', '2019-06-10 04:42:07'),
('106004', 'ID', 'Kec. Pasar Jambi', '2019-06-10 04:42:07'),
('106005', 'ID', 'Kec. Telanai Pura', '2019-06-10 04:42:07'),
('106006', 'ID', 'Kec. Danau Teluk', '2019-06-10 04:42:07'),
('106007', 'ID', 'Kec. Pelayangan', '2019-06-10 04:42:07'),
('106008', 'ID', 'Kec. Jambi Timur', '2019-06-10 04:42:07'),
('106009', 'ID', 'Kec. Alam Barajo', '2020-02-10 03:07:43'),
('106010', 'ID', 'Kec. Danau Sipin', '2020-02-10 03:07:43'),
('106011', 'ID', 'Kec. Paal Merah', '2020-02-10 03:07:43'),
('106100', 'ID', 'Kab. Sungai Penuh', '2019-06-10 04:42:07'),
('106101', 'ID', 'Kec. Tanah Kampung', '2019-06-10 04:42:07'),
('106102', 'ID', 'Kec. Sungai Penuh', '2019-06-10 04:42:07'),
('106103', 'ID', 'Kec. Hamparan Rawang', '2019-06-10 04:42:07'),
('106104', 'ID', 'Kec. Pesisir Bukit', '2019-06-10 04:42:07'),
('106105', 'ID', 'Kec. Kumun Debai', '2019-06-10 04:42:07'),
('106106', 'ID', 'Kec. Pondok Tinggi', '2020-02-10 03:07:43'),
('106107', 'ID', 'Kec. Koto Baru', '2020-02-10 03:07:43'),
('106108', 'ID', 'Kec. Sungai Bungkal', '2020-02-10 03:07:43'),
('110000', 'ID', 'Prop. Sumatera Selatan', '2019-06-10 04:42:07'),
('110100', 'ID', 'Kab. Musi Banyu Asin', '2019-06-10 04:42:07'),
('110101', 'ID', 'Kec. Sanga Desa', '2019-06-10 04:42:07'),
('110102', 'ID', 'Kec. Babat Toman', '2019-06-10 04:42:07'),
('110103', 'ID', 'Kec. Sungai Keruh', '2019-06-10 04:42:07'),
('110104', 'ID', 'Kec. Sekayu', '2019-06-10 04:42:07'),
('110109', 'ID', 'Kec. Sungai Lilin', '2019-06-10 04:42:07'),
('110110', 'ID', 'Kec. Bayung Lencir', '2019-06-10 04:42:07'),
('110111', 'ID', 'Kec. Lais', '2019-06-10 04:42:07'),
('110112', 'ID', 'Kec. Batanghari Leko', '2019-06-10 04:42:07'),
('110113', 'ID', 'Kec. Keluang', '2019-06-10 04:42:07'),
('110116', 'ID', 'Kec. Plakat Tinggi', '2019-06-10 04:42:07'),
('110117', 'ID', 'Kec. Lalan', '2019-06-10 04:42:07'),
('110118', 'ID', 'Kec. Tungkal Jaya', '2019-06-10 04:42:07');
INSERT INTO `dikti_wilayah` (`id_wilayah`, `id_negara`, `nama_wilayah`, `dikti_wilayah_timestamp`) VALUES
('110119', 'ID', 'Kec. Lawang Wetan', '2019-06-10 04:42:07'),
('110120', 'ID', 'Kec. Babat Supat', '2019-06-10 04:42:07'),
('110200', 'ID', 'Kab. Ogan Komering Ilir', '2019-06-10 04:42:07'),
('110201', 'ID', 'Kec. Lempuing', '2019-06-10 04:42:07'),
('110202', 'ID', 'Kec. Mesuji', '2019-06-10 04:42:07'),
('110203', 'ID', 'Kec. Tulung Selapan', '2019-06-10 04:42:07'),
('110204', 'ID', 'Kec. Pedamaran', '2019-06-10 04:42:07'),
('110205', 'ID', 'Kec. Tanjung Lubuk', '2019-06-10 04:42:07'),
('110206', 'ID', 'Kec. Kota Ayu Agung', '2019-06-10 04:42:07'),
('110212', 'ID', 'Kec. Sirah Pulau Padang', '2019-06-10 04:42:07'),
('110213', 'ID', 'Kec. Pampangan', '2019-06-10 04:42:07'),
('110214', 'ID', 'Kec. Air Sugihan', '2019-06-10 04:42:07'),
('110216', 'ID', 'Kec. Cengal', '2019-06-10 04:42:07'),
('110218', 'ID', 'Kec. Jejawi', '2019-06-10 04:42:07'),
('110220', 'ID', 'Kec. Sungai Menang', '2019-06-10 04:42:07'),
('110221', 'ID', 'Kec. Lempuing Jaya', '2019-06-10 04:42:07'),
('110222', 'ID', 'Kec. Mesuji Makmur', '2019-06-10 04:42:07'),
('110223', 'ID', 'Kec. Mesuji Raya', '2019-06-10 04:42:07'),
('110224', 'ID', 'Kec. Pedamaran Timur', '2019-06-10 04:42:07'),
('110225', 'ID', 'Kec. Teluk Gelam', '2019-06-10 04:42:07'),
('110226', 'ID', 'Kec. Pangkalan Lapam', '2019-06-10 04:42:07'),
('110300', 'ID', 'Kab. Ogan Komering Ulu', '2019-06-10 04:42:07'),
('110307', 'ID', 'Kec. Sosoh Buay Rayap', '2019-06-10 04:42:07'),
('110308', 'ID', 'Kec. Pengandonan', '2019-06-10 04:42:07'),
('110309', 'ID', 'Kec. Peninjauan', '2019-06-10 04:42:07'),
('110317', 'ID', 'Kec. Lubuk Batang', '2019-06-10 04:42:07'),
('110320', 'ID', 'Kec. Ulu Ogan', '2019-06-10 04:42:07'),
('110321', 'ID', 'Kec. Semidang Aji', '2019-06-10 04:42:07'),
('110322', 'ID', 'Kec. Lengkiti', '2019-06-10 04:42:07'),
('110371', 'ID', 'Kec. Baturaja Timur', '2019-06-10 04:42:07'),
('110372', 'ID', 'Kec. Baturaja Barat', '2019-06-10 04:42:07'),
('110373', 'ID', 'Kec. Sinar Peninjauan', '2019-06-10 04:42:07'),
('110374', 'ID', 'Kec. Lubuk Raja', '2019-06-10 04:42:07'),
('110375', 'ID', 'Kec. Muara Jaya', '2019-06-10 04:42:07'),
('110400', 'ID', 'Kab. Muara Enim', '2019-06-10 04:42:07'),
('110401', 'ID', 'Kec. Semende Darat Laut', '2019-06-10 04:42:07'),
('110402', 'ID', 'Kec. Tanjung Agung', '2019-06-10 04:42:07'),
('110403', 'ID', 'Kec. Lawang Kidul', '2019-06-10 04:42:07'),
('110404', 'ID', 'Kec. Muara Enim', '2019-06-10 04:42:07'),
('110405', 'ID', 'Kec. Gunung Megang', '2019-06-10 04:42:07'),
('110406', 'ID', 'Kec. Talang Ubi', '2019-06-10 04:42:07'),
('110407', 'ID', 'Kec. Gelumbang', '2019-06-10 04:42:07'),
('110409', 'ID', 'Kec. Sungai Rotan', '2019-06-10 04:42:07'),
('110410', 'ID', 'Kec. Lembak', '2019-06-10 04:42:07'),
('110411', 'ID', 'Kec. Benakat', '2019-06-10 04:42:07'),
('110412', 'ID', 'Kec. Ujan Mas', '2019-06-10 04:42:07'),
('110413', 'ID', 'Kec. Penukal', '2019-06-10 04:42:07'),
('110414', 'ID', 'Kec. Penukal Utara', '2019-06-10 04:42:07'),
('110415', 'ID', 'Kec. Tanah Abang', '2019-06-10 04:42:07'),
('110416', 'ID', 'Kec. Lubai', '2019-06-10 04:42:07'),
('110417', 'ID', 'Kec. Rambang', '2019-06-10 04:42:07'),
('110419', 'ID', 'Kec. Semende Darat Ulu', '2019-06-10 04:42:07'),
('110420', 'ID', 'Kec. Semende Darat Tengah', '2019-06-10 04:42:07'),
('110422', 'ID', 'Kec. Rambang Dangku', '2019-06-10 04:42:07'),
('110423', 'ID', 'Kec. Abab', '2019-06-10 04:42:07'),
('110424', 'ID', 'Kec. Kelekar', '2019-06-10 04:42:07'),
('110425', 'ID', 'Kec. Muara Belida', '2019-06-10 04:42:07'),
('110427', 'ID', 'Abab', '2019-06-10 04:42:07'),
('110428', 'ID', 'Talang Ubi', '2019-06-10 04:42:07'),
('110429', 'ID', 'Penukal Utara', '2019-06-10 04:42:07'),
('110430', 'ID', 'Tanah Abang', '2019-06-10 04:42:07'),
('110431', 'ID', 'Penukal', '2019-06-10 04:42:07'),
('110432', 'ID', 'Kec. Lubai Ulu', '2020-02-10 03:07:43'),
('110433', 'ID', 'Kec. Belimbing', '2020-02-10 03:07:43'),
('110434', 'ID', 'Kec. Belimbing', '2020-02-10 03:07:43'),
('110435', 'ID', 'Kec. Belida Darat', '2020-02-10 03:07:43'),
('110436', 'ID', 'Kec. Belida Barat', '2020-02-10 03:07:43'),
('110437', 'ID', 'Kec. Lubai Ulu', '2020-02-10 03:07:43'),
('110500', 'ID', 'Kab. Lahat', '2019-06-10 04:42:07'),
('110504', 'ID', 'Kec. Kota Agung', '2019-06-10 04:42:07'),
('110505', 'ID', 'Kec. Pulau Pinang', '2019-06-10 04:42:07'),
('110506', 'ID', 'Kec. Jarai', '2019-06-10 04:42:07'),
('110511', 'ID', 'Kec. Kikim Timur', '2019-06-10 04:42:07'),
('110512', 'ID', 'Kec. Lahat', '2019-06-10 04:42:07'),
('110514', 'ID', 'Kec. Mulak Ulu', '2019-06-10 04:42:07'),
('110515', 'ID', 'Kec. Pajar Bulan', '2019-06-10 04:42:07'),
('110519', 'ID', 'Kec. Kikim Selatan', '2019-06-10 04:42:07'),
('110520', 'ID', 'Kec. Kikim Barat', '2019-06-10 04:42:07'),
('110521', 'ID', 'Kec. Kikim Tengah', '2019-06-10 04:42:07'),
('110522', 'ID', 'Kec. Tanjung Sakti Pumi', '2019-06-10 04:42:07'),
('110523', 'ID', 'Kec. Tanjung Sakti Pumu', '2019-06-10 04:42:07'),
('110524', 'ID', 'Kec. Merapi Barat', '2019-06-10 04:42:07'),
('110526', 'ID', 'Kec. Pseksu', '2019-06-10 04:42:07'),
('110527', 'ID', 'Kec. Gumay Talang', '2019-06-10 04:42:07'),
('110528', 'ID', 'Kec. Pagar Gunung', '2019-06-10 04:42:07'),
('110529', 'ID', 'Kec. Merapi Timur', '2019-06-10 04:42:07'),
('110530', 'ID', 'Kec. Gumay Ulu', '2019-06-10 04:42:07'),
('110531', 'ID', 'Kec. Merapi Selatan', '2019-06-10 04:42:07'),
('110532', 'ID', 'kec. Tanjung Tebat', '2019-06-10 04:42:07'),
('110533', 'ID', 'Kec. Muara Payang', '2019-06-10 04:42:07'),
('110534', 'ID', 'Kec. Sukamerindu', '2020-02-10 03:07:43'),
('110535', 'ID', 'Kec. Sukamerindu', '2020-02-10 03:07:43'),
('110600', 'ID', 'Kab. Musi Rawas', '2019-06-10 04:42:07'),
('110601', 'ID', 'Kec. Rawas Ulu', '2019-06-10 04:42:07'),
('110602', 'ID', 'Kec. Rupit', '2019-06-10 04:42:07'),
('110603', 'ID', 'Kec. STL  Ulu Terawas', '2019-06-10 04:42:07'),
('110604', 'ID', 'Kec. Tugu Mulyo', '2019-06-10 04:42:07'),
('110605', 'ID', 'Kec. Muara Beliti', '2019-06-10 04:42:07'),
('110606', 'ID', 'Kec. Jayaloka', '2019-06-10 04:42:07'),
('110607', 'ID', 'Kec. Muara Kelingi', '2019-06-10 04:42:07'),
('110608', 'ID', 'Kec. Muara Lakitan', '2019-06-10 04:42:07'),
('110609', 'ID', 'Kec. Megang Sakti', '2019-06-10 04:42:07'),
('110610', 'ID', 'Kec. Rawas Ilir', '2019-06-10 04:42:07'),
('110611', 'ID', 'Kec. Purwodadi', '2019-06-10 04:42:07'),
('110612', 'ID', 'Kec. Selangit', '2019-06-10 04:42:07'),
('110613', 'ID', 'Kec. Karang Jaya', '2019-06-10 04:42:07'),
('110614', 'ID', 'Kec. Karang Dapo', '2019-06-10 04:42:07'),
('110615', 'ID', 'Kec. Bulan Tengah Suku Ulu', '2019-06-10 04:42:07'),
('110616', 'ID', 'Kec. Ulu Rawas', '2019-06-10 04:42:07'),
('110617', 'ID', 'Kec. Nibung', '2019-06-10 04:42:07'),
('110619', 'ID', 'Kec. Tiang Pumpung Kepungut', '2019-06-10 04:42:07'),
('110620', 'ID', 'Kec. Sumber Harta', '2019-06-10 04:42:07'),
('110621', 'ID', 'Kec. Tanah Negeri', '2019-06-10 04:42:07'),
('110622', 'ID', 'Kec. Suka Karya', '2019-06-10 04:42:07'),
('110700', 'ID', 'Kab. Banyuasin', '2019-06-10 04:42:07'),
('110701', 'ID', 'Kec. Rantau Bayur', '2019-06-10 04:42:07'),
('110702', 'ID', 'Kec. Talang Kelapa', '2019-06-10 04:42:07'),
('110703', 'ID', 'Kec. Banyuasin III', '2019-06-10 04:42:07'),
('110704', 'ID', 'Kec. Betung', '2019-06-10 04:42:07'),
('110705', 'ID', 'Kec. Makarti Jaya', '2019-06-10 04:42:07'),
('110706', 'ID', 'Kec. Banyuasin I', '2019-06-10 04:42:07'),
('110707', 'ID', 'Kec. Banyuasin II', '2019-06-10 04:42:07'),
('110709', 'ID', 'Kec. Rambutan', '2019-06-10 04:42:07'),
('110710', 'ID', 'Kec. Muara Telang', '2019-06-10 04:42:07'),
('110711', 'ID', 'Kec. Muara Padang', '2019-06-10 04:42:07'),
('110712', 'ID', 'Kec. Tanjung Lago', '2019-06-10 04:42:07'),
('110713', 'ID', 'Kec. Muara Sugihan', '2019-06-10 04:42:07'),
('110714', 'ID', 'Kec. Air Salek', '2019-06-10 04:42:07'),
('110715', 'ID', 'Kec. Tungkal Ilir', '2019-06-10 04:42:07'),
('110716', 'ID', 'Kec. Suak Tapeh', '2019-06-10 04:42:07'),
('110717', 'ID', 'Kec. Pulau Rimau', '2019-06-10 04:42:07'),
('110718', 'ID', 'Kec. Sembawa', '2019-06-10 04:42:07'),
('110719', 'ID', 'Kec. Sumber Marga Telang', '2020-02-10 03:07:43'),
('110720', 'ID', 'Kec. Air Kumbang', '2020-02-10 03:07:43'),
('110800', 'ID', 'Kab. Ogan Komering Ulu Timur', '2019-06-10 04:42:07'),
('110801', 'ID', 'Kec. Martapura', '2019-06-10 04:42:07'),
('110802', 'ID', 'Kec. Buay Madang', '2019-06-10 04:42:07'),
('110803', 'ID', 'Kec. Cempaka', '2019-06-10 04:42:07'),
('110804', 'ID', 'Kec. Madang Suku I', '2019-06-10 04:42:07'),
('110805', 'ID', 'Kec. Madang Suku II', '2019-06-10 04:42:07'),
('110806', 'ID', 'Kec. Belitang I', '2019-06-10 04:42:07'),
('110807', 'ID', 'Kec. Belitang II', '2019-06-10 04:42:07'),
('110808', 'ID', 'Kec. Belitang III', '2019-06-10 04:42:07'),
('110809', 'ID', 'Kec. Buay Pemuka Peliung', '2019-06-10 04:42:07'),
('110810', 'ID', 'Kec. Semendawai Suku III', '2019-06-10 04:42:07'),
('110811', 'ID', 'Kec. Bunga Mayang', '2019-06-10 04:42:07'),
('110812', 'ID', 'Kec. Buay Madang Timur', '2019-06-10 04:42:07'),
('110813', 'ID', 'Kec. Madang Suku III', '2019-06-10 04:42:07'),
('110814', 'ID', 'Kec. Semendawai Barat', '2019-06-10 04:42:07'),
('110815', 'ID', 'Kec. Semendawai Timur', '2019-06-10 04:42:07'),
('110816', 'ID', 'Kec. Jayapura', '2019-06-10 04:42:07'),
('110817', 'ID', 'Kec. Belitang Jaya', '2019-06-10 04:42:07'),
('110818', 'ID', 'Kec. Belitang Madang Raya', '2019-06-10 04:42:07'),
('110819', 'ID', 'Kec. Belitang Mulia', '2019-06-10 04:42:07'),
('110820', 'ID', 'Kec. Buay Pemuka Bangsa Raja', '2019-06-10 04:42:07'),
('110890', 'ID', 'Belitang', '2019-06-10 04:42:07'),
('110900', 'ID', 'Kab. Ogan Komering Ulu Selatan', '2019-06-10 04:42:07'),
('110901', 'ID', 'Kec. Banding Agung', '2019-06-10 04:42:07'),
('110902', 'ID', 'Kec. Pulau Beringin', '2019-06-10 04:42:07'),
('110903', 'ID', 'Kec. Muaradua Kisam', '2019-06-10 04:42:07'),
('110904', 'ID', 'Kec. Muaradua', '2019-06-10 04:42:07'),
('110905', 'ID', 'Kec. Simpang', '2019-06-10 04:42:07'),
('110906', 'ID', 'Kec. Buay Sandang Aji', '2019-06-10 04:42:07'),
('110907', 'ID', 'Kec. Buay Runjung', '2019-06-10 04:42:07'),
('110908', 'ID', 'Kec. Buay Pemaca', '2019-06-10 04:42:07'),
('110909', 'ID', 'Kec. Mekakau Ilir', '2019-06-10 04:42:07'),
('110910', 'ID', 'Kec. Kisam Tinggi', '2019-06-10 04:42:07'),
('110911', 'ID', 'Kec. Kisam Ilir', '2019-06-10 04:42:07'),
('110912', 'ID', 'Kec. Buay Pematang Ribu Ranau', '2019-06-10 04:42:07'),
('110913', 'ID', 'Kec. Warkuk Ranau Selatan', '2019-06-10 04:42:07'),
('110914', 'ID', 'Kec. Runjung Agung', '2019-06-10 04:42:07'),
('110915', 'ID', 'Kec. Sungai Are', '2019-06-10 04:42:07'),
('110916', 'ID', 'Kec. Sidang Danau', '2019-06-10 04:42:07'),
('110917', 'ID', 'Kec. Buana Pemaca', '2019-06-10 04:42:07'),
('110918', 'ID', 'Kec. Tiga Dihaji', '2019-06-10 04:42:07'),
('110919', 'ID', 'Kec. Buay Rawan', '2019-06-10 04:42:07'),
('111000', 'ID', 'Kab. Ogan Ilir', '2019-06-10 04:42:07'),
('111001', 'ID', 'Kec. Tanjung Raja', '2019-06-10 04:42:07'),
('111002', 'ID', 'Kec. Muara Kuang', '2019-06-10 04:42:07'),
('111003', 'ID', 'Kec. Tanjung Batu', '2019-06-10 04:42:07'),
('111004', 'ID', 'Kec. Indralaya', '2019-06-10 04:42:07'),
('111005', 'ID', 'Kec. Pemulutan', '2019-06-10 04:42:07'),
('111006', 'ID', 'Kec. Rantau Alai', '2019-06-10 04:42:07'),
('111007', 'ID', 'Kec. Rambang Kuang', '2019-06-10 04:42:07'),
('111008', 'ID', 'Kec. Lubuk Keliat', '2019-06-10 04:42:07'),
('111009', 'ID', 'Kec. Payaraman', '2019-06-10 04:42:07'),
('111010', 'ID', 'Kec. Kandis', '2019-06-10 04:42:07'),
('111011', 'ID', 'Kec. Pemulutan Selatan', '2019-06-10 04:42:07'),
('111012', 'ID', 'Kec. Pemulutan Barat', '2019-06-10 04:42:07'),
('111013', 'ID', 'Kec. Indralaya Selatan', '2019-06-10 04:42:07'),
('111014', 'ID', 'Kec. Indralaya Utara', '2019-06-10 04:42:07'),
('111015', 'ID', 'Kec. Rantau Panjang', '2019-06-10 04:42:07'),
('111016', 'ID', 'Kec. Sungai Pinang', '2019-06-10 04:42:07'),
('111100', 'ID', 'Kab. Empat Lawang', '2019-06-10 04:42:07'),
('111101', 'ID', 'Kec. Lintang Kanan', '2019-06-10 04:42:07'),
('111102', 'ID', 'Kec. Muara Pinang', '2019-06-10 04:42:07'),
('111103', 'ID', 'Kec. Pendopo', '2019-06-10 04:42:07'),
('111104', 'ID', 'Kec. Ulu Musi', '2019-06-10 04:42:07'),
('111105', 'ID', 'Kec. Tebing Tinggi', '2019-06-10 04:42:07'),
('111106', 'ID', 'Kec. Talang Padang', '2019-06-10 04:42:07'),
('111107', 'ID', 'Kec. Pasemah Air Keruh', '2019-06-10 04:42:07'),
('111108', 'ID', 'Kec. Sikap Dalam', '2019-06-10 04:42:07'),
('111109', 'ID', 'Kec. Saling', '2020-02-10 03:07:43'),
('111110', 'ID', 'Kec. Pendopo Barat', '2020-02-10 03:07:43'),
('111200', 'ID', 'Kab. Penukal Abab Lematang Ilir', '2020-02-10 03:07:43'),
('111221', 'ID', 'Kec. Penukal Utara', '2020-02-10 03:07:43'),
('111223', 'ID', 'Kec. Abab', '2020-02-10 03:07:43'),
('111224', 'ID', 'Kec. Tanah Abang', '2020-02-10 03:07:43'),
('111225', 'ID', 'Kec. Talang Ubi', '2020-02-10 03:07:43'),
('111226', 'ID', 'Kec. Penukal', '2020-02-10 03:07:43'),
('111300', 'ID', 'Kab. Musi Rawas Utara', '2020-02-10 03:07:43'),
('111301', 'ID', 'Kec. Rupit', '2020-02-10 03:07:43'),
('111302', 'ID', 'Kec. Rawas Ulu', '2020-02-10 03:07:43'),
('111303', 'ID', 'Kec. Nibung', '2020-02-10 03:07:43'),
('111304', 'ID', 'Kec. Rawas Iilir', '2020-02-10 03:07:43'),
('111305', 'ID', 'Kec. Karang Dapo', '2020-02-10 03:07:43'),
('111306', 'ID', 'Kec. Karang Jaya', '2020-02-10 03:07:43'),
('111307', 'ID', 'Kec. Ulu Rawas', '2020-02-10 03:07:43'),
('116000', 'ID', 'Kota Palembang', '2019-06-10 04:42:07'),
('116001', 'ID', 'Kec. Ilir Barat II', '2019-06-10 04:42:07'),
('116002', 'ID', 'Kec. Seberang Ulu I', '2019-06-10 04:42:07'),
('116003', 'ID', 'Kec. Seberang Ulu II', '2019-06-10 04:42:07'),
('116004', 'ID', 'Kec. Ilir Barat I', '2019-06-10 04:42:07'),
('116005', 'ID', 'Kec. Ilir Timur I', '2019-06-10 04:42:07'),
('116006', 'ID', 'Kec. Ilir Timur II', '2019-06-10 04:42:07'),
('116007', 'ID', 'Kec. Sako', '2019-06-10 04:42:07'),
('116008', 'ID', 'Kec. Sukarami', '2019-06-10 04:42:07'),
('116009', 'ID', 'Kec. Kemuning', '2019-06-10 04:42:07'),
('116010', 'ID', 'Kec. Plaju', '2019-06-10 04:42:07'),
('116011', 'ID', 'Kec. Kertapati', '2019-06-10 04:42:07'),
('116012', 'ID', 'Kec. Gandus', '2019-06-10 04:42:07'),
('116013', 'ID', 'Kec. Bukit Kecil', '2019-06-10 04:42:07'),
('116014', 'ID', 'Kec. Kalidoni', '2019-06-10 04:42:07'),
('116015', 'ID', 'Kec. Alang-Alang Lebar', '2019-06-10 04:42:07'),
('116016', 'ID', 'Kec. Sematang Borang', '2019-06-10 04:42:07'),
('116100', 'ID', 'Kota Prabumulih', '2019-06-10 04:42:07'),
('116103', 'ID', 'Kec. Prabumulih Timur', '2019-06-10 04:42:07'),
('116104', 'ID', 'Kec. Prabumulih Barat', '2019-06-10 04:42:07'),
('116105', 'ID', 'Kec. Cambai', '2019-06-10 04:42:07'),
('116106', 'ID', 'Kec. Rambang Kapak Tengah', '2019-06-10 04:42:07'),
('116107', 'ID', 'Kec. Prabumulih Utara', '2019-06-10 04:42:07'),
('116108', 'ID', 'Kec. Prabumulih Selatan', '2019-06-10 04:42:07'),
('116200', 'ID', 'Kota Lubuk Linggau', '2019-06-10 04:42:07'),
('116201', 'ID', 'Kec. Lubuk Linggau Barat I', '2019-06-10 04:42:07'),
('116202', 'ID', 'Kec. Lubuk Linggau Timur I', '2019-06-10 04:42:07'),
('116203', 'ID', 'Kec. Lubuk Linggau Selatan I', '2019-06-10 04:42:07'),
('116204', 'ID', 'Kec. Lubuk Linggau Utara I', '2019-06-10 04:42:07'),
('116205', 'ID', 'Kec. Lubuk Linggau Barat II', '2019-06-10 04:42:07'),
('116206', 'ID', 'Kec. Lubuk Linggau Timur II', '2019-06-10 04:42:07'),
('116207', 'ID', 'Kec. Lubuk Linggau Selatan II', '2019-06-10 04:42:07'),
('116208', 'ID', 'Kec. Lubuk Linggau Utara II', '2019-06-10 04:42:07'),
('116300', 'ID', 'Kota Pagar Alam', '2019-06-10 04:42:07'),
('116301', 'ID', 'Kec. Pagar Alam Utara', '2019-06-10 04:42:07'),
('116302', 'ID', 'Kec. Pagar Alam Selatan', '2019-06-10 04:42:07'),
('116303', 'ID', 'Kec. Dempo Utara', '2019-06-10 04:42:07'),
('116304', 'ID', 'Kec. Dempo Selatan', '2019-06-10 04:42:07'),
('116305', 'ID', 'Kec. Dempo Tengah', '2019-06-10 04:42:07'),
('120000', 'ID', 'Prop. Lampung', '2019-06-10 04:42:07'),
('120100', 'ID', 'Kab. Lampung Selatan', '2019-06-10 04:42:07'),
('120106', 'ID', 'Kec. Natar', '2019-06-10 04:42:07'),
('120107', 'ID', 'Kec. Jati Agung', '2019-06-10 04:42:07'),
('120108', 'ID', 'Kec. Tanjung Bintang', '2019-06-10 04:42:07'),
('120109', 'ID', 'Kec. Katibung', '2019-06-10 04:42:07'),
('120110', 'ID', 'Kec. Sidomulyo', '2019-06-10 04:42:07'),
('120112', 'ID', 'Kec. Palas', '2019-06-10 04:42:07'),
('120113', 'ID', 'Kec. Penengahan', '2019-06-10 04:42:07'),
('120116', 'ID', 'Kec. Merbau Mataram', '2019-06-10 04:42:07'),
('120117', 'ID', 'Kec. Candipuro', '2019-06-10 04:42:07'),
('120118', 'ID', 'Kec. Rajabasa', '2019-06-10 04:42:07'),
('120119', 'ID', 'Kec. Sragi', '2019-06-10 04:42:07'),
('120120', 'ID', 'Kec. Ketapang', '2019-06-10 04:42:07'),
('120121', 'ID', 'Kec. Katibung', '2019-06-10 04:42:07'),
('120122', 'ID', 'Kec. Bakauheni', '2019-06-10 04:42:07'),
('120123', 'ID', 'Kec. Tanjung Sari', '2019-06-10 04:42:07'),
('120124', 'ID', 'Kec. Way Sulan', '2019-06-10 04:42:07'),
('120125', 'ID', 'Kec. Way Panji', '2019-06-10 04:42:07'),
('120126', 'ID', 'Kalianda', '2019-06-10 04:42:07'),
('120200', 'ID', 'Kab. Lampung Tengah', '2019-06-10 04:42:07'),
('120201', 'ID', 'Kec. Padang Ratu', '2019-06-10 04:42:07'),
('120202', 'ID', 'Kec. Kalirejo', '2019-06-10 04:42:07'),
('120203', 'ID', 'Kec. Bangunrejo', '2019-06-10 04:42:07'),
('120204', 'ID', 'Kec. Gunung Sugih', '2019-06-10 04:42:07'),
('120205', 'ID', 'Kec. Trimurjo', '2019-06-10 04:42:07'),
('120206', 'ID', 'Kec. Punggur', '2019-06-10 04:42:07'),
('120207', 'ID', 'Kec. Seputih Raman', '2019-06-10 04:42:07'),
('120208', 'ID', 'Kec. Terbanggi Besar', '2019-06-10 04:42:07'),
('120209', 'ID', 'Kec. Terusan Nunyai', '2019-06-10 04:42:07'),
('120210', 'ID', 'Kec. Seputih Mataram', '2019-06-10 04:42:07'),
('120211', 'ID', 'Kec. Seputih Banyak', '2019-06-10 04:42:07'),
('120212', 'ID', 'Kec. Rumbia', '2019-06-10 04:42:07'),
('120213', 'ID', 'Kec. Seputih Surabaya', '2019-06-10 04:42:07'),
('120214', 'ID', 'Kec. Bumi Ratu Nuban', '2019-06-10 04:42:07'),
('120215', 'ID', 'Kec. Way Pengubuan', '2019-06-10 04:42:07'),
('120216', 'ID', 'Kec. Seputih Agung', '2019-06-10 04:42:07'),
('120217', 'ID', 'Kec. Bandar Mataram', '2019-06-10 04:42:07'),
('120218', 'ID', 'Kec. Sendang Agung', '2019-06-10 04:42:07'),
('120219', 'ID', 'Kec. Anak Tuha', '2019-06-10 04:42:07'),
('120220', 'ID', 'Kec. Pubian', '2019-06-10 04:42:07'),
('120221', 'ID', 'Kec. Bandar Surabaya', '2019-06-10 04:42:07'),
('120222', 'ID', 'Kec. Bumi Nabung', '2019-06-10 04:42:07'),
('120223', 'ID', 'Kec. Way Seputih', '2019-06-10 04:42:07'),
('120224', 'ID', 'Kec. Kota Gajah', '2019-06-10 04:42:07'),
('120225', 'ID', 'Kec. Selagai Lingga', '2019-06-10 04:42:07'),
('120226', 'ID', 'Kec. Bekri', '2019-06-10 04:42:07'),
('120227', 'ID', 'Kec. Anak Ratu Aji', '2019-06-10 04:42:07'),
('120228', 'ID', 'Kec. Putra Rumbia', '2019-06-10 04:42:07'),
('120300', 'ID', 'Kab. Lampung Utara', '2019-06-10 04:42:07'),
('120301', 'ID', 'Kec. Bukit Kemuning', '2019-06-10 04:42:07'),
('120302', 'ID', 'Kec. Tanjung Raja', '2019-06-10 04:42:07'),
('120303', 'ID', 'Kec. Abung Barat', '2019-06-10 04:42:07'),
('120304', 'ID', 'Kec. Kotabumi Kota', '2019-06-10 04:42:07'),
('120305', 'ID', 'Kec. Abung Selatan', '2019-06-10 04:42:07'),
('120306', 'ID', 'Kec. Abung Timur', '2019-06-10 04:42:07'),
('120307', 'ID', 'Kec. Sungkai Selatan', '2019-06-10 04:42:07'),
('120308', 'ID', 'Kec. Sungkai Utara', '2019-06-10 04:42:07'),
('120309', 'ID', 'Kec. Abung Tinggi', '2019-06-10 04:42:07'),
('120310', 'ID', 'Kec. Abung Tengah', '2019-06-10 04:42:07'),
('120311', 'ID', 'Kec. Kotabumi Utara', '2019-06-10 04:42:07'),
('120312', 'ID', 'Kec. Kotabumi Selatan', '2019-06-10 04:42:07'),
('120313', 'ID', 'Kec. Abung Semuli', '2019-06-10 04:42:07'),
('120314', 'ID', 'Kec. Abung Surakarta', '2019-06-10 04:42:07'),
('120315', 'ID', 'Kec. Muara Sungkai', '2019-06-10 04:42:07'),
('120316', 'ID', 'Kec. Bunga Mayang', '2019-06-10 04:42:07'),
('120317', 'ID', 'Kec. Hulu Sungkai', '2019-06-10 04:42:07'),
('120318', 'ID', 'Kec. Sungkai Tengah', '2019-06-10 04:42:07'),
('120319', 'ID', 'Kec. Abung Pekurun', '2019-06-10 04:42:07'),
('120320', 'ID', 'Kec. Sungkai Jaya', '2019-06-10 04:42:07'),
('120321', 'ID', 'Kec. Sungkai Barat', '2019-06-10 04:42:07'),
('120322', 'ID', 'Kec. Abung Kunang', '2019-06-10 04:42:07'),
('120323', 'ID', 'Kec. Blambangan Pagar', '2019-06-10 04:42:07'),
('120400', 'ID', 'Kab. Lampung Barat', '2019-06-10 04:42:07'),
('120401', 'ID', 'Kec. Pesisir Selatan', '2019-06-10 04:42:07'),
('120402', 'ID', 'Kec. Pesisir Tengah', '2019-06-10 04:42:07'),
('120403', 'ID', 'Kec. Pesisir Utara', '2019-06-10 04:42:07'),
('120404', 'ID', 'Kec. Balik Bukit', '2019-06-10 04:42:07'),
('120405', 'ID', 'Kec. Belalau', '2019-06-10 04:42:07'),
('120406', 'ID', 'Kec. Sumber Jaya', '2019-06-10 04:42:07'),
('120407', 'ID', 'Kec. Sekincau', '2019-06-10 04:42:07'),
('120408', 'ID', 'Kec. Bengkunat', '2019-06-10 04:42:07'),
('120409', 'ID', 'Kec. Batu Brak', '2019-06-10 04:42:07'),
('120410', 'ID', 'Kec. Karyapenggawa', '2019-06-10 04:42:07'),
('120411', 'ID', 'Kec. Lemong', '2019-06-10 04:42:07'),
('120412', 'ID', 'Kec. Waytenong', '2019-06-10 04:42:07'),
('120413', 'ID', 'Kec. Sukau', '2019-06-10 04:42:07'),
('120414', 'ID', 'Kec. Suoh', '2019-06-10 04:42:07'),
('120415', 'ID', 'Kec. Gedung Surian', '2019-06-10 04:42:07'),
('120416', 'ID', 'Kec. Bengkunat Belimbing', '2019-06-10 04:42:07'),
('120417', 'ID', 'Kec. Ngambur', '2019-06-10 04:42:07'),
('120490', 'ID', 'Pagar Dewa', '2019-06-10 04:42:07'),
('120491', 'ID', 'Lumbok Seminung', '2019-06-10 04:42:07'),
('120492', 'ID', 'Kec. Kebun Tebu', '2020-02-10 03:07:43'),
('120493', 'ID', 'Kec. Air Hitam', '2020-02-10 03:07:43'),
('120494', 'ID', 'Kec. Batu Ketulis', '2020-02-10 03:07:43'),
('120495', 'ID', 'Kec. Bandar Negeri Suoh', '2020-02-10 03:07:43'),
('120496', 'ID', 'Kec. Way Krui', '2020-02-10 03:07:43'),
('120497', 'ID', 'Kec. Krui Selatan', '2020-02-10 03:07:43'),
('120498', 'ID', 'Kec. Pulau Pisang', '2020-02-10 03:07:43'),
('120499', 'ID', 'Kec. Pagar Dewa', '2020-02-10 03:07:43'),
('120500', 'ID', 'Kab. Tulang Bawang', '2019-06-10 04:42:07'),
('120503', 'ID', 'Kec. Banjar Agung', '2019-06-10 04:42:07'),
('120504', 'ID', 'Kec. Gedung Aji', '2019-06-10 04:42:07'),
('120505', 'ID', 'Kec. Manggala', '2019-06-10 04:42:07'),
('120509', 'ID', 'Kec. Penawartama', '2019-06-10 04:42:07'),
('120513', 'ID', 'Kec. Rawajitu Selatan', '2019-06-10 04:42:07'),
('120514', 'ID', 'Kec. Gedung Meneng', '2019-06-10 04:42:07'),
('120519', 'ID', 'Kec. Banjar Margo', '2019-06-10 04:42:07'),
('120520', 'ID', 'Kec. Penawar Aji', '2019-06-10 04:42:07'),
('120521', 'ID', 'Kec. Rawa Pitu', '2019-06-10 04:42:07'),
('120522', 'ID', 'Kec. Rawajitu Timur', '2019-06-10 04:42:07'),
('120525', 'ID', 'Kec. Meraksa Aji', '2019-06-10 04:42:07'),
('120526', 'ID', 'Kec. Gedung AJI Baru', '2019-06-10 04:42:07'),
('120527', 'ID', 'Kec. Dente Teladas', '2019-06-10 04:42:07'),
('120528', 'ID', 'Kec. Banjar Baru', '2019-06-10 04:42:07'),
('120529', 'ID', 'Kec. Menggala Timur', '2019-06-10 04:42:07'),
('120590', 'ID', 'Menggala', '2019-06-10 04:42:07'),
('120600', 'ID', 'Kab. Tenggamus', '2019-06-10 04:42:07'),
('120601', 'ID', 'Kec. Wonosobo', '2019-06-10 04:42:07'),
('120602', 'ID', 'Kec. Kota Agung', '2019-06-10 04:42:07'),
('120603', 'ID', 'Kec. Pulau Panggung', '2019-06-10 04:42:07'),
('120604', 'ID', 'Kec. Talang Padang', '2019-06-10 04:42:07'),
('120605', 'ID', 'Kec. Pugung', '2019-06-10 04:42:07'),
('120611', 'ID', 'Kec. Cukuh Balak', '2019-06-10 04:42:07'),
('120612', 'ID', 'Kec. Pematang Sawa', '2019-06-10 04:42:07'),
('120613', 'ID', 'Kec. Sumberejo', '2019-06-10 04:42:07'),
('120614', 'ID', 'Kec. Semaka', '2019-06-10 04:42:07'),
('120615', 'ID', 'Kec. Ulu Belu', '2019-06-10 04:42:07'),
('120617', 'ID', 'Kec. Kelumbayan', '2019-06-10 04:42:07'),
('120618', 'ID', 'Kota Agung Barat', '2019-06-10 04:42:07'),
('120619', 'ID', 'Kota Agung Timur', '2019-06-10 04:42:07'),
('120620', 'ID', 'Kec. Gisting', '2019-06-10 04:42:07'),
('120621', 'ID', 'Kec. Gunung Alip', '2019-06-10 04:42:07'),
('120624', 'ID', 'Kec. Limau', '2019-06-10 04:42:07'),
('120625', 'ID', 'Kec. Bandar Negeri Semuong', '2019-06-10 04:42:07'),
('120626', 'ID', 'Kec. Air Naningan', '2019-06-10 04:42:07'),
('120627', 'ID', 'Kec. Bulok', '2019-06-10 04:42:07'),
('120628', 'ID', 'Kec. Kelumbayan Barat', '2019-06-10 04:42:07'),
('120700', 'ID', 'Kab. Lampung Timur', '2019-06-10 04:42:07'),
('120701', 'ID', 'Kec. Metro Kibang', '2019-06-10 04:42:07'),
('120702', 'ID', 'Kec. Batanghari', '2019-06-10 04:42:07'),
('120703', 'ID', 'Kec. Sekampung', '2019-06-10 04:42:07'),
('120704', 'ID', 'Kec. Margatiga', '2019-06-10 04:42:07'),
('120705', 'ID', 'Kec. Sekampung Udik', '2019-06-10 04:42:07'),
('120706', 'ID', 'Kec. Jabung', '2019-06-10 04:42:07'),
('120707', 'ID', 'Kec. Labuhan Maringgai', '2019-06-10 04:42:07'),
('120708', 'ID', 'Kec. Way Jepara', '2019-06-10 04:42:07'),
('120709', 'ID', 'Kec. Sukadana', '2019-06-10 04:42:07'),
('120710', 'ID', 'Kec. Pekalongan', '2019-06-10 04:42:07'),
('120711', 'ID', 'Kec. Raman Utara', '2019-06-10 04:42:07'),
('120712', 'ID', 'Kec. Purbolinggo', '2019-06-10 04:42:07'),
('120713', 'ID', 'Kec. Bumi Agung', '2019-06-10 04:42:07'),
('120716', 'ID', 'Kec. Braja Slebah', '2019-06-10 04:42:07'),
('120717', 'ID', 'Kec. Bandar Sribawono', '2019-06-10 04:42:07'),
('120718', 'ID', 'Kec. Mataram Baru', '2019-06-10 04:42:07'),
('120719', 'ID', 'Kec. Melinting', '2019-06-10 04:42:07'),
('120720', 'ID', 'Kec. Gunung Pelindung', '2019-06-10 04:42:07'),
('120721', 'ID', 'Kec. Waway Karya', '2019-06-10 04:42:07'),
('120722', 'ID', 'Kec. Pasir Sakti', '2019-06-10 04:42:07'),
('120723', 'ID', 'Kec. Labuhan Ratu', '2019-06-10 04:42:07'),
('120724', 'ID', 'Kec. Batanghari Nuban', '2019-06-10 04:42:07'),
('120725', 'ID', 'Kec. Way Bungur', '2019-06-10 04:42:07'),
('120726', 'ID', 'Kec. Marga Sekampung', '2019-06-10 04:42:07'),
('120800', 'ID', 'Kab. Way Kanan', '2019-06-10 04:42:07'),
('120801', 'ID', 'Kec. Banjit', '2019-06-10 04:42:07'),
('120802', 'ID', 'Kec. Baradatu', '2019-06-10 04:42:07'),
('120803', 'ID', 'Kec. Kasui', '2019-06-10 04:42:07'),
('120804', 'ID', 'Kec. Blambangan Umpu', '2019-06-10 04:42:07'),
('120805', 'ID', 'Kec. Bahuga', '2019-06-10 04:42:07'),
('120806', 'ID', 'Kec. Pakuan Ratu', '2019-06-10 04:42:07'),
('120807', 'ID', 'Kec. Gunung Labuhan', '2019-06-10 04:42:07'),
('120808', 'ID', 'Kec. Rebang Tangkas', '2019-06-10 04:42:07'),
('120809', 'ID', 'Kec. Way Tuba', '2019-06-10 04:42:07'),
('120810', 'ID', 'Kec. Negeri Agung', '2019-06-10 04:42:07'),
('120811', 'ID', 'Kec. Negara Batin', '2019-06-10 04:42:07'),
('120812', 'ID', 'Kec. Negeri Besar', '2019-06-10 04:42:07'),
('120813', 'ID', 'Kec. Buay Bahuga', '2019-06-10 04:42:07'),
('120814', 'ID', 'Kec. Bumi Agung', '2019-06-10 04:42:07'),
('120900', 'ID', 'Kab. Pasawaran', '2019-06-10 04:42:07'),
('120901', 'ID', 'Kec. Padang Cermin', '2019-06-10 04:42:07'),
('120902', 'ID', 'Kec. Punduh Pedada', '2019-06-10 04:42:07'),
('120903', 'ID', 'Kec. Kedondong', '2019-06-10 04:42:07'),
('120904', 'ID', 'Kec. Way Lima', '2019-06-10 04:42:07'),
('120905', 'ID', 'Kec. Gedung Tataan', '2019-06-10 04:42:07'),
('120906', 'ID', 'Kec. Negeri Katon', '2019-06-10 04:42:07'),
('120907', 'ID', 'Kec. Tegineneng', '2019-06-10 04:42:07'),
('120908', 'ID', 'Kec. Marga Punduh', '2020-02-10 03:07:43'),
('120909', 'ID', 'Kec. Way Khilau', '2020-02-10 03:07:43'),
('120910', 'ID', 'Kec. Teluk Pandan', '2020-02-10 03:07:43'),
('120911', 'ID', 'Kec. Way Ratai', '2020-02-10 03:07:43'),
('121000', 'ID', 'Kab. Tulang Bawang Barat', '2019-06-10 04:42:07'),
('121001', 'ID', 'Kec. Pagelaran', '2019-06-10 04:42:07'),
('121002', 'ID', 'Kec. Sukoharjo', '2019-06-10 04:42:07'),
('121003', 'ID', 'Kec. Adiluwih', '2019-06-10 04:42:07'),
('121004', 'ID', 'Kec. Banyumas', '2019-06-10 04:42:07'),
('121005', 'ID', 'Kec. Pringsewu', '2019-06-10 04:42:07'),
('121006', 'ID', 'Kec. Ambarawa', '2019-06-10 04:42:07'),
('121007', 'ID', 'Kec. Gadingrejo', '2019-06-10 04:42:07'),
('121008', 'ID', 'Kec. Pardasuka', '2019-06-10 04:42:07'),
('121009', 'ID', 'Kec. Pagelaran Utara', '2020-02-10 03:07:43'),
('121100', 'ID', 'Kab. Mesuji', '2019-06-10 04:42:07'),
('121101', 'ID', 'Kec. Mesuji', '2019-06-10 04:42:07'),
('121102', 'ID', 'Kec. Tanjung Raya', '2019-06-10 04:42:07'),
('121103', 'ID', 'Kec. Rawajitu Utara', '2019-06-10 04:42:07'),
('121104', 'ID', 'Kec. Mesuji Timur', '2019-06-10 04:42:07'),
('121105', 'ID', 'Kec. Simpang Pematang', '2019-06-10 04:42:07'),
('121106', 'ID', 'Kec. Way Serdang', '2019-06-10 04:42:07'),
('121107', 'ID', 'Kec. Panca Jaya', '2019-06-10 04:42:07'),
('121200', 'ID', 'Kab. Pringsewu', '2019-06-10 04:42:07'),
('121201', 'ID', 'Kec. Tulang Bawang Udik', '2019-06-10 04:42:07'),
('121202', 'ID', 'Kec. Tumijajar', '2019-06-10 04:42:07'),
('121203', 'ID', 'Kec. Tulang Bawang Tengah', '2019-06-10 04:42:07'),
('121204', 'ID', 'Kec. Lambu Kibang', '2019-06-10 04:42:07'),
('121205', 'ID', 'Kec. Pagar Dewa', '2019-06-10 04:42:07'),
('121206', 'ID', 'Kec. Way Kenanga', '2019-06-10 04:42:07'),
('121207', 'ID', 'Kec. Gunung Terang', '2019-06-10 04:42:07'),
('121208', 'ID', 'Kec. Gunung Agung', '2019-06-10 04:42:07'),
('121300', 'ID', 'Kab. Pesisir Barat', '2020-02-10 03:07:43'),
('121301', 'ID', 'Kec. Bangkunat', '2020-02-10 03:07:43'),
('121331', 'ID', 'Kec. Pesisir Selatan', '2020-02-10 03:07:43'),
('121332', 'ID', 'Kec. Ngambur', '2020-02-10 03:07:43'),
('121333', 'ID', 'Kec. Lemong', '2020-02-10 03:07:43'),
('121334', 'ID', 'Kec. Ngaras', '2020-02-10 03:07:43'),
('121335', 'ID', 'Kec. Karyapenggawa', '2020-02-10 03:07:43'),
('121336', 'ID', 'Kec. Pesisir Tengah', '2020-02-10 03:07:43'),
('121337', 'ID', 'Kec. Pesisir Utara', '2020-02-10 03:07:43'),
('121338', 'ID', 'Kec. Krui Selatan', '2020-02-10 03:07:43'),
('121339', 'ID', 'Kec. Pulau Pisang', '2020-02-10 03:07:43'),
('121340', 'ID', 'Kec. Way Krui', '2020-02-10 03:07:43'),
('126000', 'ID', 'Kota Bandar Lampung', '2019-06-10 04:42:07'),
('126001', 'ID', 'Kec. Teluk Betung Barat', '2019-06-10 04:42:07'),
('126002', 'ID', 'Kec. Teluk Betung Selatan', '2019-06-10 04:42:07'),
('126003', 'ID', 'Kec. Panjang', '2019-06-10 04:42:07'),
('126004', 'ID', 'Kec. Tanjung Karang Timur', '2019-06-10 04:42:07'),
('126005', 'ID', 'Kec. Teluk Betung Utara', '2019-06-10 04:42:07'),
('126006', 'ID', 'Kec. Tanjung Karang Pusat', '2019-06-10 04:42:07'),
('126007', 'ID', 'Kec. Tanjung Karang Barat', '2019-06-10 04:42:07'),
('126008', 'ID', 'Kec. Kedaton', '2019-06-10 04:42:07'),
('126009', 'ID', 'Kec. Sukarame', '2019-06-10 04:42:07'),
('126010', 'ID', 'Kec. Kemiling', '2019-06-10 04:42:07'),
('126011', 'ID', 'Kec. Rajabasa', '2019-06-10 04:42:07'),
('126012', 'ID', 'Kec. Tanjung Senang', '2019-06-10 04:42:07'),
('126013', 'ID', 'Kec. Sukabumi', '2019-06-10 04:42:07'),
('126014', 'ID', 'Kec. Labuhan Ratu', '2020-02-10 03:07:43'),
('126015', 'ID', 'Kec. Way Halim', '2020-02-10 03:07:43'),
('126016', 'ID', 'Kec. Kedamaian', '2020-02-10 03:07:43'),
('126017', 'ID', 'Kec. Enggal', '2020-02-10 03:07:43'),
('126018', 'ID', 'Kec. Langkapura', '2020-02-10 03:07:43'),
('126019', 'ID', 'Kec. Bumi Waras', '2020-02-10 03:07:43'),
('126020', 'ID', 'Kec. Teluk Betung Timur', '2020-02-10 03:07:43'),
('126100', 'ID', 'Kota Metro', '2019-06-10 04:42:07'),
('126101', 'ID', 'Kec. Metro Pusat', '2019-06-10 04:42:07'),
('126102', 'ID', 'Kec. Metro Utara', '2019-06-10 04:42:07'),
('126103', 'ID', 'Kec. Metro Barat', '2019-06-10 04:42:07'),
('126104', 'ID', 'Kec. Metro Timur', '2019-06-10 04:42:07'),
('126105', 'ID', 'Kec. Metro Selatan', '2019-06-10 04:42:07'),
('130000', 'ID', 'Prop. Kalimantan Barat', '2019-06-10 04:42:07'),
('130100', 'ID', 'Kab. Sambas', '2019-06-10 04:42:07'),
('130101', 'ID', 'Kec. Selakau', '2019-06-10 04:42:07'),
('130102', 'ID', 'Kec. Pemangkat', '2019-06-10 04:42:07'),
('130103', 'ID', 'Kec. Tebas', '2019-06-10 04:42:07'),
('130104', 'ID', 'Kec. Sambas', '2019-06-10 04:42:07'),
('130105', 'ID', 'Kec. Jawai', '2019-06-10 04:42:07'),
('130106', 'ID', 'Kec. Teluk Keramat', '2019-06-10 04:42:07'),
('130107', 'ID', 'Kec. Sejangkung', '2019-06-10 04:42:07'),
('130108', 'ID', 'Kec. Sajingan Besar', '2019-06-10 04:42:07'),
('130109', 'ID', 'Kec. Paloh', '2019-06-10 04:42:07'),
('130110', 'ID', 'Kec. Subah', '2019-06-10 04:42:07'),
('130111', 'ID', 'Kec. Galing', '2019-06-10 04:42:07'),
('130112', 'ID', 'Kec. Semparuk', '2019-06-10 04:42:07'),
('130113', 'ID', 'Kec. Tekarang', '2019-06-10 04:42:07'),
('130114', 'ID', 'Kec. Sajad', '2019-06-10 04:42:07'),
('130115', 'ID', 'Kec. Sebawi', '2019-06-10 04:42:07'),
('130116', 'ID', 'Kec. Jawai Selatan', '2019-06-10 04:42:07'),
('130117', 'ID', 'Kec. Tangaran', '2019-06-10 04:42:07'),
('130118', 'ID', 'Kec. Selakau Tua', '2019-06-10 04:42:07'),
('130119', 'ID', 'Kec. Salatiga', '2019-06-10 04:42:07'),
('130190', 'ID', 'Selakau Timur', '2019-06-10 04:42:07'),
('130200', 'ID', 'Kab. Pontianak', '2019-06-10 04:42:07'),
('130208', 'ID', 'Kec. Siantan', '2019-06-10 04:42:07'),
('130209', 'ID', 'Kec. Sungai Pinyuh', '2019-06-10 04:42:07'),
('130210', 'ID', 'Kec. Mempawah Hilir', '2019-06-10 04:42:07'),
('130211', 'ID', 'Kec. Sungai Kunyit', '2019-06-10 04:42:07'),
('130212', 'ID', 'Kec. Toho', '2019-06-10 04:42:07'),
('130216', 'ID', 'Kec. Segedong', '2019-06-10 04:42:07'),
('130217', 'ID', 'Kec. Anjongan', '2019-06-10 04:42:07'),
('130218', 'ID', 'Kec. Sadaniang', '2019-06-10 04:42:07'),
('130219', 'ID', 'Kec. Mempawah Timur', '2019-06-10 04:42:07'),
('130220', 'ID', 'Kec. Ambawang', '2019-06-10 04:42:07'),
('130300', 'ID', 'Kab. Sanggau', '2019-06-10 04:42:07'),
('130301', 'ID', 'Kec. Toba', '2019-06-10 04:42:07'),
('130306', 'ID', 'Kec. Sanggau Kapuas', '2019-06-10 04:42:07'),
('130307', 'ID', 'Kec. Mokok', '2019-06-10 04:42:07'),
('130312', 'ID', 'Kec. Jangkang', '2019-06-10 04:42:07'),
('130313', 'ID', 'Kec. Bonti', '2019-06-10 04:42:07'),
('130314', 'ID', 'Kec. Parindu', '2019-06-10 04:42:07'),
('130315', 'ID', 'Kec. Tayan Hilir', '2019-06-10 04:42:07'),
('130316', 'ID', 'Kec. Balai', '2019-06-10 04:42:07'),
('130317', 'ID', 'Kec. Tayan Hulu', '2019-06-10 04:42:07'),
('130318', 'ID', 'Kec. Kembayan', '2019-06-10 04:42:07'),
('130319', 'ID', 'Kec. Beduwai', '2019-06-10 04:42:07'),
('130320', 'ID', 'Kec. Noyan', '2019-06-10 04:42:07'),
('130321', 'ID', 'Kec. Sekayan', '2019-06-10 04:42:07'),
('130322', 'ID', 'Kec. Entikong', '2019-06-10 04:42:07'),
('130323', 'ID', 'Meliau', '2019-06-10 04:42:07'),
('130324', 'ID', 'Kapuas', '2019-06-10 04:42:07'),
('130400', 'ID', 'Kab. Sintang', '2019-06-10 04:42:07'),
('130406', 'ID', 'Kec. Nanga Serawai', '2019-06-10 04:42:07'),
('130407', 'ID', 'Kec. Ambalau', '2019-06-10 04:42:07'),
('130408', 'ID', 'Kec. Kayan Hulu', '2019-06-10 04:42:07'),
('130411', 'ID', 'Kec. Sepauk', '2019-06-10 04:42:07'),
('130412', 'ID', 'Kec. Tempunak', '2019-06-10 04:42:07'),
('130413', 'ID', 'Kec. Sungai Tebelian', '2019-06-10 04:42:07'),
('130414', 'ID', 'Kec. Sintang', '2019-06-10 04:42:07'),
('130415', 'ID', 'Kec. Dedai', '2019-06-10 04:42:07'),
('130416', 'ID', 'Kec. Kayan Hilir', '2019-06-10 04:42:07'),
('130417', 'ID', 'Kec. Kelam Permai', '2019-06-10 04:42:07'),
('130418', 'ID', 'Kec. Binjai Hulu', '2019-06-10 04:42:07'),
('130419', 'ID', 'Kec. Ketungau Hilir', '2019-06-10 04:42:07'),
('130420', 'ID', 'Kec. Ketungau Tengah', '2019-06-10 04:42:07'),
('130421', 'ID', 'Kec. Ketungau Hulu', '2019-06-10 04:42:07'),
('130500', 'ID', 'Kab. Kapuas Hulu', '2019-06-10 04:42:07'),
('130501', 'ID', 'Kec. Silat Hilir', '2019-06-10 04:42:07'),
('130502', 'ID', 'Kec. Silat Hulu', '2019-06-10 04:42:07'),
('130503', 'ID', 'Kec. Hulu Gurung', '2019-06-10 04:42:07'),
('130504', 'ID', 'Kec. Bunut Hulu', '2019-06-10 04:42:07'),
('130505', 'ID', 'Kec. Mentebah', '2019-06-10 04:42:07'),
('130507', 'ID', 'Kec. Kalis', '2019-06-10 04:42:07'),
('130509', 'ID', 'Kec. Embaloh Hilir', '2019-06-10 04:42:07'),
('130510', 'ID', 'Kec. Bunut Hilir', '2019-06-10 04:42:07'),
('130511', 'ID', 'Kec. Boyan Tanjung', '2019-06-10 04:42:07'),
('130514', 'ID', 'Kec. Selimbau', '2019-06-10 04:42:07'),
('130515', 'ID', 'Kec. Suhaid', '2019-06-10 04:42:07'),
('130516', 'ID', 'Kec. Seberuang', '2019-06-10 04:42:07'),
('130517', 'ID', 'Kec. Semitau', '2019-06-10 04:42:07'),
('130518', 'ID', 'Kec. Empanang', '2019-06-10 04:42:07'),
('130519', 'ID', 'Kec. Puring Kencana', '2019-06-10 04:42:07'),
('130520', 'ID', 'Kec. Badau', '2019-06-10 04:42:07'),
('130521', 'ID', 'Kec. Batang Lupar', '2019-06-10 04:42:07'),
('130522', 'ID', 'Kec. Embaloh Hulu', '2019-06-10 04:42:07'),
('130524', 'ID', 'Kec. Hulu Kapuas', '2019-06-10 04:42:07'),
('130526', 'ID', 'Kec. Putussibau Utara', '2019-06-10 04:42:07'),
('130527', 'ID', 'Kec. Bika', '2019-06-10 04:42:07'),
('130528', 'ID', 'Kec. Jongkong', '2019-06-10 04:42:07'),
('130529', 'ID', 'Kec. Putussibau Selatan', '2019-06-10 04:42:07'),
('130530', 'ID', 'Kec. Pengkadan', '2019-06-10 04:42:07'),
('130531', 'ID', 'Kec. Danau Setarum', '2019-06-10 04:42:07'),
('130600', 'ID', 'Kab. Ketapang', '2019-06-10 04:42:07'),
('130601', 'ID', 'Kec. Kendawangan', '2019-06-10 04:42:07'),
('130602', 'ID', 'Kec. Manis Mata', '2019-06-10 04:42:07'),
('130603', 'ID', 'Kec. Marau', '2019-06-10 04:42:07'),
('130604', 'ID', 'Kec. Jelai Hulu', '2019-06-10 04:42:07'),
('130605', 'ID', 'Kec. Tumbang Titi', '2019-06-10 04:42:07'),
('130606', 'ID', 'Kec. Matan Hilir Selatan', '2019-06-10 04:42:07'),
('130607', 'ID', 'Kec. Matan Hilir Utara', '2019-06-10 04:42:07'),
('130609', 'ID', 'Kec. Nanga Tayap', '2019-06-10 04:42:07'),
('130610', 'ID', 'Kec. Sandai', '2019-06-10 04:42:07'),
('130611', 'ID', 'Kec. Sungai Laur', '2019-06-10 04:42:07'),
('130612', 'ID', 'Kec. Simpang Hulu', '2019-06-10 04:42:07'),
('130618', 'ID', 'Kec. Muara Pawan', '2019-06-10 04:42:07'),
('130619', 'ID', 'Kec. Delta Pawan', '2019-06-10 04:42:07'),
('130620', 'ID', 'Kec. Simpang Dua', '2019-06-10 04:42:07'),
('130621', 'ID', 'Kec. Benua Kayong', '2019-06-10 04:42:07'),
('130622', 'ID', 'Kec. Hulu Sungai', '2019-06-10 04:42:07'),
('130623', 'ID', 'Kec. Air Upas', '2019-06-10 04:42:07'),
('130624', 'ID', 'Kec. Singkup', '2019-06-10 04:42:07'),
('130625', 'ID', 'Kec. Pemahan', '2019-06-10 04:42:07'),
('130626', 'ID', 'Kec. Sungai Melayu Rayek', '2019-06-10 04:42:07'),
('130800', 'ID', 'Kab. Bengkayang', '2019-06-10 04:42:07'),
('130801', 'ID', 'Kec. Sungai Raya', '2019-06-10 04:42:07'),
('130802', 'ID', 'Kec. Capkala', '2019-06-10 04:42:07'),
('130803', 'ID', 'Kec. Samalantan', '2019-06-10 04:42:07'),
('130804', 'ID', 'Kec. Bengkayang', '2019-06-10 04:42:07'),
('130805', 'ID', 'Kec. Ledo', '2019-06-10 04:42:07'),
('130806', 'ID', 'Kec. Sanggau Ledo', '2019-06-10 04:42:07'),
('130807', 'ID', 'Kec. Seluas', '2019-06-10 04:42:07'),
('130808', 'ID', 'Kec. Jagoi Babang', '2019-06-10 04:42:07'),
('130809', 'ID', 'Kec. Teriak', '2019-06-10 04:42:07'),
('130810', 'ID', 'Kec. Monterado', '2019-06-10 04:42:07'),
('130811', 'ID', 'Kec. Suti Semarang', '2019-06-10 04:42:07'),
('130812', 'ID', 'Kec. Siding', '2019-06-10 04:42:07'),
('130813', 'ID', 'Kec. Lumar', '2019-06-10 04:42:07'),
('130814', 'ID', 'Kec. Sungai Betung', '2019-06-10 04:42:07'),
('130815', 'ID', 'Kec. Sungai Raya Kepulauan', '2019-06-10 04:42:07'),
('130816', 'ID', 'Kec. Lembah Bawang', '2019-06-10 04:42:07'),
('130817', 'ID', 'Kec. Tujuh Belas', '2019-06-10 04:42:07'),
('130900', 'ID', 'Kab. Landak', '2019-06-10 04:42:07'),
('130902', 'ID', 'Kec. Sebangki', '2019-06-10 04:42:07'),
('130903', 'ID', 'Kec. Ngabang', '2019-06-10 04:42:07'),
('130904', 'ID', 'Kec. Sengah Temila', '2019-06-10 04:42:07'),
('130905', 'ID', 'Kec. Mandor', '2019-06-10 04:42:07'),
('130906', 'ID', 'Kec. Menjalin', '2019-06-10 04:42:07'),
('130907', 'ID', 'Kec. Mempawah Hulu', '2019-06-10 04:42:07'),
('130908', 'ID', 'Kec. Menyuke', '2019-06-10 04:42:07'),
('130909', 'ID', 'Kec. Meranti', '2019-06-10 04:42:07'),
('130910', 'ID', 'Kec. Kuala Behe', '2019-06-10 04:42:07'),
('130911', 'ID', 'Kec. Air Besar', '2019-06-10 04:42:07'),
('130912', 'ID', 'Kec. Jelimpo', '2019-06-10 04:42:07'),
('130913', 'ID', 'Kec. Sompak', '2019-06-10 04:42:07'),
('130914', 'ID', 'Kec. Banyuke Hulu', '2019-06-10 04:42:07'),
('131000', 'ID', 'Kab. Sekadau', '2019-06-10 04:42:07'),
('131001', 'ID', 'Kec. Nanga Mahap', '2019-06-10 04:42:07'),
('131002', 'ID', 'Kec. Nanga Taman', '2019-06-10 04:42:07'),
('131003', 'ID', 'Kec. Sekadau Hulu', '2019-06-10 04:42:07'),
('131004', 'ID', 'Kec. Sekadau Hilir', '2019-06-10 04:42:07'),
('131005', 'ID', 'Kec. Belitang', '2019-06-10 04:42:07'),
('131006', 'ID', 'Kec. Belitang Hilir', '2019-06-10 04:42:07'),
('131007', 'ID', 'Kec. Belitang Hulu', '2019-06-10 04:42:07'),
('131100', 'ID', 'Kab. Melawi', '2019-06-10 04:42:07'),
('131101', 'ID', 'Kec. Sokan', '2019-06-10 04:42:07'),
('131102', 'ID', 'Kec. Tanah Pinoh', '2019-06-10 04:42:07'),
('131103', 'ID', 'Kec. Sayan', '2019-06-10 04:42:07'),
('131104', 'ID', 'Kec. Ella Hilir', '2019-06-10 04:42:07'),
('131105', 'ID', 'Kec. Menukung', '2019-06-10 04:42:07'),
('131108', 'ID', 'Kec. Nanga Pinoh', '2019-06-10 04:42:07'),
('131109', 'ID', 'Kec. Belimbing', '2019-06-10 04:42:07'),
('131110', 'ID', 'Kec. Tanah Pinoh Barat', '2019-06-10 04:42:07'),
('131111', 'ID', 'Kec. Belimbing Hulu', '2019-06-10 04:42:07'),
('131112', 'ID', 'Kec. Pinoh Selatan', '2019-06-10 04:42:07'),
('131113', 'ID', 'Kec. Pinoh Utara', '2019-06-10 04:42:07'),
('131200', 'ID', 'Kab. Kayong Utara', '2019-06-10 04:42:07'),
('131201', 'ID', 'Kec. Pulau Maya Karimata', '2019-06-10 04:42:07'),
('131202', 'ID', 'Kec. Simpang Hilir', '2019-06-10 04:42:07'),
('131203', 'ID', 'Kec. Sukadana', '2019-06-10 04:42:07'),
('131204', 'ID', 'Kec. Teluk Batang', '2019-06-10 04:42:07'),
('131205', 'ID', 'Kec. Seponti', '2019-06-10 04:42:07'),
('131206', 'ID', 'Kec. Kepulauan Karimata', '2020-02-10 03:07:43'),
('131300', 'ID', 'Kab. Kuburaya', '2019-06-10 04:42:07'),
('131302', 'ID', 'Kec. Kuala Mandor B', '2019-06-10 04:42:07'),
('131304', 'ID', 'Kec. Sungai Ambawang', '2019-06-10 04:42:07'),
('131305', 'ID', 'Kec. Sungai Kakap', '2019-06-10 04:42:07'),
('131308', 'ID', 'Kec. Telok Pakedai', '2019-06-10 04:42:07'),
('131309', 'ID', 'Kec. Terentang', '2019-06-10 04:42:07'),
('131310', 'ID', 'Kec. Sungai Raya', '2019-06-10 04:42:07'),
('131311', 'ID', 'Kec. Batu Ampar', '2019-06-10 04:42:07'),
('131312', 'ID', 'Kec. Kubu', '2019-06-10 04:42:07'),
('131313', 'ID', 'Kec. Rasau Jaya', '2019-06-10 04:42:07'),
('136000', 'ID', 'Kota Pontianak', '2019-06-10 04:42:07'),
('136001', 'ID', 'Kec. Pontianak Selatan', '2019-06-10 04:42:07'),
('136002', 'ID', 'Kec. Pontianak Timur', '2019-06-10 04:42:07'),
('136003', 'ID', 'Kec. Pontianak Barat', '2019-06-10 04:42:07'),
('136004', 'ID', 'Kec. Pontianak Utara', '2019-06-10 04:42:07'),
('136005', 'ID', 'Kec. Pontianak Kota', '2019-06-10 04:42:07'),
('136006', 'ID', 'Kec. Pontianak Tenggara', '2019-06-10 04:42:07'),
('136100', 'ID', 'Kota Singkawang', '2019-06-10 04:42:07'),
('136101', 'ID', 'Kec. Singkawang Selatan', '2019-06-10 04:42:07'),
('136102', 'ID', 'Kec. Singkawang Timur', '2019-06-10 04:42:07'),
('136103', 'ID', 'Kec. Singkawang Utara', '2019-06-10 04:42:07'),
('136104', 'ID', 'Kec. Singkawang Barat', '2019-06-10 04:42:07'),
('136105', 'ID', 'Kec. Singkawang Tengah', '2019-06-10 04:42:07'),
('140000', 'ID', 'Prop. Kalimantan Tengah', '2019-06-10 04:42:07'),
('140100', 'ID', 'Kab. Kapuas', '2019-06-10 04:42:07'),
('140102', 'ID', 'Kec. Kapuas Kuala', '2019-06-10 04:42:07'),
('140103', 'ID', 'Kec. Kapuas Timur', '2019-06-10 04:42:07'),
('140104', 'ID', 'Kec. Selat', '2019-06-10 04:42:07'),
('140107', 'ID', 'Kec. Basarang', '2019-06-10 04:42:07'),
('140108', 'ID', 'Kec. Kapuas Hilir', '2019-06-10 04:42:07'),
('140109', 'ID', 'Kec. Pulau Petak', '2019-06-10 04:42:07'),
('140110', 'ID', 'Kec. Kapuas Murung', '2019-06-10 04:42:07'),
('140111', 'ID', 'Kec. Kapuas Barat', '2019-06-10 04:42:07'),
('140115', 'ID', 'Kec. Mantangai', '2019-06-10 04:42:07'),
('140116', 'ID', 'Kec. Timpah', '2019-06-10 04:42:07'),
('140117', 'ID', 'Kec. Kapuas Tengah', '2019-06-10 04:42:07'),
('140118', 'ID', 'Kec. Kapuas Hulu', '2019-06-10 04:42:07'),
('140119', 'ID', 'Kec. Bataguh', '2020-02-10 03:07:43'),
('140120', 'ID', 'Kec. Dadahup', '2020-02-10 03:07:43'),
('140121', 'ID', 'Kec. Tamban Catur', '2020-02-10 03:07:43'),
('140122', 'ID', 'Kec. Pasak Talawang', '2020-02-10 03:07:43'),
('140123', 'ID', 'Kec. Mandau Talawang', '2020-02-10 03:07:43'),
('140200', 'ID', 'Kab. Barito Selatan', '2019-06-10 04:42:07'),
('140201', 'ID', 'Kec. Dusun Hilir', '2019-06-10 04:42:07'),
('140202', 'ID', 'Kec. Jenamas', '2019-06-10 04:42:07'),
('140209', 'ID', 'Kec. Karau Kuala', '2019-06-10 04:42:07'),
('140210', 'ID', 'Kec. Dusun Selatan', '2019-06-10 04:42:07'),
('140211', 'ID', 'Kec. Dusun Utara', '2019-06-10 04:42:07'),
('140212', 'ID', 'Kec. Gunung Bintang Awai', '2019-06-10 04:42:07'),
('140300', 'ID', 'Kab. Barito Utara', '2019-06-10 04:42:07'),
('140301', 'ID', 'Kec. Montallat', '2019-06-10 04:42:07'),
('140302', 'ID', 'Kec. Gunung Timang', '2019-06-10 04:42:07'),
('140303', 'ID', 'Kec. Gunung Purei', '2019-06-10 04:42:07'),
('140304', 'ID', 'Kec. Teweh Timur', '2019-06-10 04:42:07'),
('140305', 'ID', 'Kec. Teweh Tengah', '2019-06-10 04:42:07'),
('140306', 'ID', 'Kec. Lahei', '2019-06-10 04:42:07'),
('140307', 'ID', 'Teweh Baru', '2019-06-10 04:42:07'),
('140308', 'ID', 'Teweh Selatan', '2019-06-10 04:42:07'),
('140309', 'ID', 'Lahei Barat', '2019-06-10 04:42:07'),
('140400', 'ID', 'Kab. Kotawaringin Timur', '2019-06-10 04:42:07'),
('140402', 'ID', 'Kec. Mentaya Hilir Selatan', '2019-06-10 04:42:07'),
('140405', 'ID', 'Kec. Pulau Hanaut', '2019-06-10 04:42:07'),
('140406', 'ID', 'Kec. Mentawa Baru/Ketapang', '2019-06-10 04:42:07'),
('140407', 'ID', 'Kec. Mentaya Hilir Utara', '2019-06-10 04:42:07'),
('140411', 'ID', 'Kec. Kota Besi', '2019-06-10 04:42:07'),
('140412', 'ID', 'Kec. Baamang', '2019-06-10 04:42:07'),
('140419', 'ID', 'Kec. Cempaga', '2019-06-10 04:42:07'),
('140420', 'ID', 'Kec. Parenggean', '2019-06-10 04:42:07'),
('140421', 'ID', 'Kec. Mentaya Hulu', '2019-06-10 04:42:07'),
('140423', 'ID', 'Kec. Antang Kalang', '2019-06-10 04:42:07'),
('140425', 'ID', 'Kec. Teluk Sampit', '2019-06-10 04:42:07'),
('140426', 'ID', 'Kec. Seranau', '2019-06-10 04:42:07'),
('140427', 'ID', 'Kec. Cempaga Hulu', '2019-06-10 04:42:07'),
('140428', 'ID', 'Kec. Bukit Santuei', '2019-06-10 04:42:07'),
('140429', 'ID', 'Kec. Telawang', '2019-06-10 04:42:07'),
('140430', 'ID', 'Kec. Tualan Hulu', '2019-06-10 04:42:07'),
('140431', 'ID', 'Kec. Telaga Antang', '2019-06-10 04:42:07'),
('140432', 'ID', 'Tualan Hulu', '2019-06-10 04:42:07'),
('140433', 'ID', 'Telaga Antang', '2019-06-10 04:42:07'),
('140500', 'ID', 'Kab. Kotawaringin Barat', '2019-06-10 04:42:07'),
('140504', 'ID', 'Kec. Kotawaringin Lama', '2019-06-10 04:42:07'),
('140505', 'ID', 'Kec. Arut Selatan', '2019-06-10 04:42:07'),
('140506', 'ID', 'Kec. Kumai', '2019-06-10 04:42:07'),
('140507', 'ID', 'Kec. Arut Utara', '2019-06-10 04:42:07'),
('140508', 'ID', 'Kec. Pangkalan Banteng', '2019-06-10 04:42:07'),
('140509', 'ID', 'Kec. Pangkalan Lada', '2019-06-10 04:42:07'),
('140600', 'ID', 'Kab. Katingan', '2019-06-10 04:42:07'),
('140601', 'ID', 'Kec. Katingan Kuala', '2019-06-10 04:42:07'),
('140602', 'ID', 'Kec. Mendawai', '2019-06-10 04:42:07'),
('140603', 'ID', 'Kec. Kampiang', '2019-06-10 04:42:07'),
('140604', 'ID', 'Kec. Tasik Payawan', '2019-06-10 04:42:07'),
('140605', 'ID', 'Kec. Katingan Hilir', '2019-06-10 04:42:07'),
('140606', 'ID', 'Kec. Tewang Sangalang Garing', '2019-06-10 04:42:07'),
('140607', 'ID', 'Kec. Pulau Malan', '2019-06-10 04:42:07'),
('140608', 'ID', 'Kec. Katingan Tengah', '2019-06-10 04:42:07'),
('140609', 'ID', 'Kec. Katingan Hulu', '2019-06-10 04:42:07'),
('140610', 'ID', 'Kec. Marikit', '2019-06-10 04:42:07'),
('140611', 'ID', 'Kec. Sanaman Mantikei', '2019-06-10 04:42:07'),
('140612', 'ID', 'Kec. Petak Malai', '2019-06-10 04:42:07'),
('140613', 'ID', 'Kec. Bukit Raya', '2019-06-10 04:42:07'),
('140700', 'ID', 'Kab. Seruyan', '2019-06-10 04:42:07'),
('140701', 'ID', 'Kec. Seruyan Hilir', '2019-06-10 04:42:07'),
('140702', 'ID', 'Kec. Danau Sembuluh', '2019-06-10 04:42:07'),
('140703', 'ID', 'Kec. Hanau', '2019-06-10 04:42:07'),
('140704', 'ID', 'Kec. Seruyan Tengah', '2019-06-10 04:42:07'),
('140705', 'ID', 'Kec. Seruyan Hulu', '2019-06-10 04:42:07'),
('140706', 'ID', 'Kec. Seruyan Hilir Timur', '2019-06-10 04:42:07'),
('140707', 'ID', 'Kec. Seruyan Raya', '2019-06-10 04:42:07'),
('140708', 'ID', 'Kec. Danau Seluluk', '2019-06-10 04:42:07'),
('140709', 'ID', 'Kec. Batu Ampar', '2019-06-10 04:42:07'),
('140710', 'ID', 'Kec. Suling Tambun', '2019-06-10 04:42:07'),
('140711', 'ID', 'Kec. Sembuluh Raya', '2019-06-10 04:42:07'),
('140712', 'ID', 'Kec. Natai Kelampai', '2019-06-10 04:42:07'),
('140713', 'ID', 'Kec. Sepan Biha', '2019-06-10 04:42:07'),
('140714', 'ID', 'Kec. Seruyan Hulu Utara', '2019-06-10 04:42:07'),
('140790', 'ID', 'Natai Kelampai', '2019-06-10 04:42:07'),
('140791', 'ID', 'Sepan Biha', '2019-06-10 04:42:07'),
('140792', 'ID', 'Seruyan Hulu Utara', '2019-06-10 04:42:07'),
('140799', 'ID', 'Sembuluh Raya', '2019-06-10 04:42:07'),
('140800', 'ID', 'Kab. Sukamara', '2019-06-10 04:42:07'),
('140801', 'ID', 'Kec. Jelai', '2019-06-10 04:42:07'),
('140802', 'ID', 'Kec. Sukamara', '2019-06-10 04:42:07'),
('140803', 'ID', 'Kec. Balai Riam', '2019-06-10 04:42:07'),
('140804', 'ID', 'Kec. Pantai Lunci', '2019-06-10 04:42:07'),
('140805', 'ID', 'Kec. Permata Kecubung', '2019-06-10 04:42:07'),
('140900', 'ID', 'Kab. Lamandau', '2019-06-10 04:42:07'),
('140901', 'ID', 'Kec. Bulik', '2019-06-10 04:42:07'),
('140902', 'ID', 'Kec. Lamandau', '2019-06-10 04:42:07'),
('140903', 'ID', 'Kec. Delang', '2019-06-10 04:42:07'),
('140904', 'ID', 'Kec. Bulik Timur', '2019-06-10 04:42:07'),
('140905', 'ID', 'Kec. Mentobi Raya', '2019-06-10 04:42:07'),
('140906', 'ID', 'Kec. Sematu Jaya', '2019-06-10 04:42:07'),
('140907', 'ID', 'Kec. Belantikan Raya', '2019-06-10 04:42:07'),
('140908', 'ID', 'Kec. Batang Kawa', '2019-06-10 04:42:07'),
('141000', 'ID', 'Kab. Gunung Mas', '2019-06-10 04:42:07'),
('141001', 'ID', 'Kec. Tewah', '2019-06-10 04:42:07'),
('141002', 'ID', 'Kec. Kurun', '2019-06-10 04:42:07'),
('141003', 'ID', 'Kec. Sepang Simin', '2019-06-10 04:42:07'),
('141004', 'ID', 'Kec. Rungan', '2019-06-10 04:42:07'),
('141005', 'ID', 'Kec. Manuhing', '2019-06-10 04:42:07'),
('141006', 'ID', 'Kec. Kahayan Hulu Utara', '2019-06-10 04:42:07'),
('141007', 'ID', 'Kec. Mihing Raya', '2019-06-10 04:42:07'),
('141008', 'ID', 'Kec. Damang Batu', '2019-06-10 04:42:07'),
('141009', 'ID', 'Kec. Miri Manasa', '2019-06-10 04:42:07'),
('141010', 'ID', 'Kec. Rungan Hulu', '2019-06-10 04:42:07'),
('141011', 'ID', 'Kec. Manuhing Raya', '2019-06-10 04:42:07'),
('141012', 'ID', 'Kec. Rungan Barat', '2020-02-10 03:07:43'),
('141100', 'ID', 'Kab. Pulang Pisau', '2019-06-10 04:42:07'),
('141101', 'ID', 'Kec. Kahayan Kuala', '2019-06-10 04:42:07'),
('141102', 'ID', 'Kec. Pandih Batu', '2019-06-10 04:42:07'),
('141103', 'ID', 'Kec. Maliku', '2019-06-10 04:42:07'),
('141104', 'ID', 'Kec. Kahayan Hilir', '2019-06-10 04:42:07'),
('141105', 'ID', 'Kec. Kahayan Tengah', '2019-06-10 04:42:07'),
('141106', 'ID', 'Kec. Banama Tingan', '2019-06-10 04:42:07'),
('141107', 'ID', 'Kec. Sebangau Kuala', '2019-06-10 04:42:07'),
('141108', 'ID', 'Kec. Jabiren Raya', '2019-06-10 04:42:07'),
('141200', 'ID', 'Kab. Murung Raya', '2019-06-10 04:42:07'),
('141201', 'ID', 'Kec. Laung Tuhup', '2019-06-10 04:42:07');
INSERT INTO `dikti_wilayah` (`id_wilayah`, `id_negara`, `nama_wilayah`, `dikti_wilayah_timestamp`) VALUES
('141202', 'ID', 'Kec. Murung', '2019-06-10 04:42:07'),
('141203', 'ID', 'Kec. Permata Intan', '2019-06-10 04:42:07'),
('141204', 'ID', 'Kec. Tanah Siang', '2019-06-10 04:42:07'),
('141205', 'ID', 'Kec. Sumber Barito', '2019-06-10 04:42:07'),
('141206', 'ID', 'Kec. Barito Tuhup Raya', '2019-06-10 04:42:07'),
('141207', 'ID', 'Kec. Tanah Siang Selatan', '2019-06-10 04:42:07'),
('141208', 'ID', 'Kec. Sungai Babuat', '2019-06-10 04:42:07'),
('141209', 'ID', 'Kec. Seribu Riam', '2019-06-10 04:42:07'),
('141210', 'ID', 'Kec. Uut Murung', '2019-06-10 04:42:07'),
('141300', 'ID', 'Kab. Barito Timur', '2019-06-10 04:42:07'),
('141301', 'ID', 'Kec. Dusun Timur', '2019-06-10 04:42:07'),
('141302', 'ID', 'Kec. Benua Lima', '2019-06-10 04:42:07'),
('141303', 'ID', 'Kec. Patangkep Tutui', '2019-06-10 04:42:07'),
('141304', 'ID', 'Kec. Awang', '2019-06-10 04:42:07'),
('141305', 'ID', 'Kec. Dusun Tengah', '2019-06-10 04:42:07'),
('141306', 'ID', 'Kec. Pematang Karau', '2019-06-10 04:42:07'),
('141307', 'ID', 'Kec. Paju Epat', '2019-06-10 04:42:07'),
('141308', 'ID', 'Kec. Reren Batuah', '2019-06-10 04:42:07'),
('141309', 'ID', 'Kec. Paku', '2019-06-10 04:42:07'),
('141310', 'ID', 'Kec. Karusen Janang', '2019-06-10 04:42:07'),
('146000', 'ID', 'Kota Palangka Raya', '2019-06-10 04:42:07'),
('146001', 'ID', 'Kec. Pahandut', '2019-06-10 04:42:07'),
('146002', 'ID', 'Kec. Bukit Batu', '2019-06-10 04:42:07'),
('146003', 'ID', 'Kec. Sabangau', '2019-06-10 04:42:07'),
('146004', 'ID', 'Kec. Jekan Raya', '2019-06-10 04:42:07'),
('146005', 'ID', 'Kec. Rakumpit', '2019-06-10 04:42:07'),
('150000', 'ID', 'Prop. Kalimantan Selatan', '2019-06-10 04:42:07'),
('150100', 'ID', 'Kab. Banjar', '2019-06-10 04:42:07'),
('150101', 'ID', 'Kec. Aluh-Aluh', '2019-06-10 04:42:07'),
('150102', 'ID', 'Kec. Gambut', '2019-06-10 04:42:07'),
('150103', 'ID', 'Kec. Kertak Hanyar', '2019-06-10 04:42:07'),
('150104', 'ID', 'Kec. Sungai Tabuk', '2019-06-10 04:42:07'),
('150105', 'ID', 'Kec. Martapura', '2019-06-10 04:42:07'),
('150106', 'ID', 'Kec. Astambul', '2019-06-10 04:42:07'),
('150107', 'ID', 'Kec. Karang Intan', '2019-06-10 04:42:07'),
('150108', 'ID', 'Kec. Aranio', '2019-06-10 04:42:07'),
('150109', 'ID', 'Kec. Sungai Pinang', '2019-06-10 04:42:07'),
('150110', 'ID', 'Kec. Pengaron', '2019-06-10 04:42:07'),
('150111', 'ID', 'Kec. Mataraman', '2019-06-10 04:42:07'),
('150112', 'ID', 'Kec. Simpang Empat', '2019-06-10 04:42:07'),
('150113', 'ID', 'Kec. Beruntung Baru', '2019-06-10 04:42:07'),
('150114', 'ID', 'Kec. Martapura Barat', '2019-06-10 04:42:07'),
('150115', 'ID', 'Kec. Martapura Timur', '2019-06-10 04:42:07'),
('150116', 'ID', 'Kec. Paramasan', '2019-06-10 04:42:07'),
('150117', 'ID', 'Kec. Tatah Makmur', '2019-06-10 04:42:07'),
('150118', 'ID', 'Kec. Sambung Makmur', '2019-06-10 04:42:07'),
('150119', 'ID', 'Kec. Telaga Bauntung', '2019-06-10 04:42:07'),
('150200', 'ID', 'Kab. Tanah Laut', '2019-06-10 04:42:07'),
('150201', 'ID', 'Kec. Panyipatan', '2019-06-10 04:42:07'),
('150202', 'ID', 'Kec. Takisung', '2019-06-10 04:42:07'),
('150203', 'ID', 'Kec. Kurau', '2019-06-10 04:42:07'),
('150204', 'ID', 'Kec. Bati-Bati', '2019-06-10 04:42:07'),
('150205', 'ID', 'Kec. Tambang Ulang', '2019-06-10 04:42:07'),
('150206', 'ID', 'Kec. Pelaihari', '2019-06-10 04:42:07'),
('150207', 'ID', 'Kec. Batu Ampar', '2019-06-10 04:42:07'),
('150208', 'ID', 'Kec. Jorong', '2019-06-10 04:42:07'),
('150209', 'ID', 'Kec. Kintap', '2019-06-10 04:42:07'),
('150210', 'ID', 'Kec. Harapan Bumi Makmur', '2019-06-10 04:42:07'),
('150211', 'ID', 'Kec. Bajuin', '2019-06-10 04:42:07'),
('150300', 'ID', 'Kab. Barito Kuala', '2019-06-10 04:42:07'),
('150301', 'ID', 'Kec. Tabunganen', '2019-06-10 04:42:07'),
('150302', 'ID', 'Kec. Tamban', '2019-06-10 04:42:07'),
('150303', 'ID', 'Kec. Mekarsari', '2019-06-10 04:42:07'),
('150304', 'ID', 'Kec. Anjir Pasar', '2019-06-10 04:42:07'),
('150305', 'ID', 'Kec. Anjir Muara', '2019-06-10 04:42:07'),
('150306', 'ID', 'Kec. Alalak', '2019-06-10 04:42:07'),
('150307', 'ID', 'Kec. Mandastana', '2019-06-10 04:42:07'),
('150308', 'ID', 'Kec. Belawang', '2019-06-10 04:42:07'),
('150309', 'ID', 'Kec. Wanaraya', '2019-06-10 04:42:07'),
('150310', 'ID', 'Kec. Barambai', '2019-06-10 04:42:07'),
('150311', 'ID', 'Kec. Rantau Badauh', '2019-06-10 04:42:07'),
('150312', 'ID', 'Kec. Cerbon', '2019-06-10 04:42:07'),
('150313', 'ID', 'Kec. Bakumpai', '2019-06-10 04:42:07'),
('150314', 'ID', 'Kec. Marabahan', '2019-06-10 04:42:07'),
('150315', 'ID', 'Kec. Tabukan', '2019-06-10 04:42:07'),
('150316', 'ID', 'Kec. Kuripan', '2019-06-10 04:42:07'),
('150317', 'ID', 'Kec. Jejangkit', '2019-06-10 04:42:07'),
('150400', 'ID', 'Kab. Tapin', '2019-06-10 04:42:07'),
('150401', 'ID', 'Kec. Binuang', '2019-06-10 04:42:07'),
('150402', 'ID', 'Kec. Tapin Selatan', '2019-06-10 04:42:07'),
('150403', 'ID', 'Kec. Tapin Tengah', '2019-06-10 04:42:07'),
('150404', 'ID', 'Kec. Bungur', '2019-06-10 04:42:07'),
('150405', 'ID', 'Kec. Piani', '2019-06-10 04:42:07'),
('150406', 'ID', 'Kec. Lokpaikat', '2019-06-10 04:42:07'),
('150407', 'ID', 'Kec. Tapin Utara', '2019-06-10 04:42:07'),
('150408', 'ID', 'Kec. Bakarangan', '2019-06-10 04:42:07'),
('150409', 'ID', 'Kec. Candi Laras Selatan', '2019-06-10 04:42:07'),
('150410', 'ID', 'Kec. Candi Laras Utara', '2019-06-10 04:42:07'),
('150411', 'ID', 'Kec. Hatungun', '2019-06-10 04:42:07'),
('150412', 'ID', 'Kec. Salam Babaris', '2019-06-10 04:42:07'),
('150500', 'ID', 'Kab. Hulu Sungai Selatan', '2019-06-10 04:42:07'),
('150501', 'ID', 'Kec. Padang Batung', '2019-06-10 04:42:07'),
('150502', 'ID', 'Kec. Loksado', '2019-06-10 04:42:07'),
('150503', 'ID', 'Kec. Telaga Langsat', '2019-06-10 04:42:07'),
('150504', 'ID', 'Kec. Angkinang', '2019-06-10 04:42:07'),
('150505', 'ID', 'Kec. Kandangan', '2019-06-10 04:42:07'),
('150506', 'ID', 'Kec. Sungai Raya', '2019-06-10 04:42:07'),
('150507', 'ID', 'Kec. Simpur', '2019-06-10 04:42:07'),
('150508', 'ID', 'Kec. Kalumpang', '2019-06-10 04:42:07'),
('150509', 'ID', 'Kec. Daha Selatan', '2019-06-10 04:42:07'),
('150510', 'ID', 'Kec. Daha Utara', '2019-06-10 04:42:07'),
('150511', 'ID', 'Kec. Daha Barat', '2019-06-10 04:42:07'),
('150600', 'ID', 'Kab. Hulu Sungai Tengah', '2019-06-10 04:42:07'),
('150601', 'ID', 'Kec. Haruyan', '2019-06-10 04:42:07'),
('150602', 'ID', 'Kec. Batu Benawa', '2019-06-10 04:42:07'),
('150603', 'ID', 'Kec. Hantakan', '2019-06-10 04:42:07'),
('150604', 'ID', 'Kec. Batang Alai Selatan', '2019-06-10 04:42:07'),
('150605', 'ID', 'Kec. Barabai', '2019-06-10 04:42:07'),
('150606', 'ID', 'Kec. Labuan Amas Selatan', '2019-06-10 04:42:07'),
('150607', 'ID', 'Kec. Labuan Amas Utara', '2019-06-10 04:42:07'),
('150608', 'ID', 'Kec. Pendawan', '2019-06-10 04:42:07'),
('150609', 'ID', 'Kec. Batang Alai Utara', '2019-06-10 04:42:07'),
('150610', 'ID', 'Kec. Batang Alai Timur', '2019-06-10 04:42:07'),
('150611', 'ID', 'Kec. Limpasu', '2019-06-10 04:42:07'),
('150700', 'ID', 'Kab. Hulu Sungai Utara', '2019-06-10 04:42:07'),
('150701', 'ID', 'Kec. Danau Panggang', '2019-06-10 04:42:07'),
('150702', 'ID', 'Kec. Babirik', '2019-06-10 04:42:07'),
('150703', 'ID', 'Kec. Sungai Pandan', '2019-06-10 04:42:07'),
('150704', 'ID', 'Kec. Amuntai Selatan', '2019-06-10 04:42:07'),
('150705', 'ID', 'Kec. Amuntai Tengah', '2019-06-10 04:42:07'),
('150706', 'ID', 'Kec. Banjang', '2019-06-10 04:42:07'),
('150707', 'ID', 'Kec. Amuntai Utara', '2019-06-10 04:42:07'),
('150708', 'ID', 'Kec. Paminggir', '2019-06-10 04:42:07'),
('150709', 'ID', 'Kec. Sungai Tabukan', '2019-06-10 04:42:07'),
('150710', 'ID', 'Kec. Haur Gading', '2019-06-10 04:42:07'),
('150800', 'ID', 'Kab. Tabalong', '2019-06-10 04:42:07'),
('150801', 'ID', 'Kec. Banua Lawas', '2019-06-10 04:42:07'),
('150802', 'ID', 'Kec. Pugaan', '2019-06-10 04:42:07'),
('150803', 'ID', 'Kec. Kelua', '2019-06-10 04:42:07'),
('150804', 'ID', 'Kec. Muara Harus', '2019-06-10 04:42:07'),
('150805', 'ID', 'Kec. Tanta', '2019-06-10 04:42:07'),
('150806', 'ID', 'Kec. Tanjung', '2019-06-10 04:42:07'),
('150807', 'ID', 'Kec. Murung Pudak', '2019-06-10 04:42:07'),
('150808', 'ID', 'Kec. Haruai', '2019-06-10 04:42:07'),
('150809', 'ID', 'Kec. Upau', '2019-06-10 04:42:07'),
('150810', 'ID', 'Kec. Muara Uya', '2019-06-10 04:42:07'),
('150811', 'ID', 'Kec. Jaro', '2019-06-10 04:42:07'),
('150812', 'ID', 'Kec. Bintang Ara', '2019-06-10 04:42:07'),
('150900', 'ID', 'Kab. Kota Baru', '2019-06-10 04:42:07'),
('150901', 'ID', 'Kec. Pulau Sembilan', '2019-06-10 04:42:07'),
('150902', 'ID', 'Kec. Pulau Laut Barat', '2019-06-10 04:42:07'),
('150903', 'ID', 'Kec. Pulau Laut Selatan', '2019-06-10 04:42:07'),
('150904', 'ID', 'Kec. Pulau Laut Timur', '2019-06-10 04:42:07'),
('150905', 'ID', 'Kec. Pulau Sebuku', '2019-06-10 04:42:07'),
('150906', 'ID', 'Kec. Pulau Laut Utara', '2019-06-10 04:42:07'),
('150907', 'ID', 'Kec. Pulau Laut Tengah', '2019-06-10 04:42:07'),
('150908', 'ID', 'Kec. Kelumpang Hilir', '2019-06-10 04:42:07'),
('150909', 'ID', 'Kec. Kelumpang Barat', '2019-06-10 04:42:07'),
('150912', 'ID', 'Kec. Kelumpang Selatan', '2019-06-10 04:42:07'),
('150913', 'ID', 'Kec. Kelumpang Hulu', '2019-06-10 04:42:07'),
('150914', 'ID', 'Kec. Hampang', '2019-06-10 04:42:07'),
('150915', 'ID', 'Kec. Sungai Durian', '2019-06-10 04:42:07'),
('150916', 'ID', 'Kec. Kelumpang Tengah', '2019-06-10 04:42:07'),
('150917', 'ID', 'Kec. Kelumpang Utara', '2019-06-10 04:42:07'),
('150918', 'ID', 'Kec. Pamukan Selatan', '2019-06-10 04:42:07'),
('150919', 'ID', 'Kec. Sampanahan', '2019-06-10 04:42:07'),
('150920', 'ID', 'Kec. Pamukan Utara', '2019-06-10 04:42:07'),
('150921', 'ID', 'Kec. Pulau Laut Kepulauan', '2019-06-10 04:42:07'),
('150922', 'ID', 'Kec. Pamukan Barat', '2019-06-10 04:42:07'),
('150923', 'ID', 'Kec. Pulau Laut Tanjung Selayar', '2020-02-10 03:07:43'),
('151000', 'ID', 'Kab. Balangan', '2019-06-10 04:42:07'),
('151001', 'ID', 'Kec. Lampihong', '2019-06-10 04:42:07'),
('151002', 'ID', 'Kec. Batu Mandi', '2019-06-10 04:42:07'),
('151003', 'ID', 'Kec. Awayan', '2019-06-10 04:42:07'),
('151004', 'ID', 'Kec. Paringin', '2019-06-10 04:42:07'),
('151005', 'ID', 'Kec. Juai', '2019-06-10 04:42:07'),
('151006', 'ID', 'Kec. Halong', '2019-06-10 04:42:07'),
('151007', 'ID', 'Kec. Tebing Tinggi', '2019-06-10 04:42:07'),
('151008', 'ID', 'Kec. Paringin Selatan', '2019-06-10 04:42:07'),
('151100', 'ID', 'Kab. Tanah Bumbu', '2019-06-10 04:42:07'),
('151101', 'ID', 'Kec. Kusan Hilir', '2019-06-10 04:42:07'),
('151102', 'ID', 'Kec. Sungai Loban', '2019-06-10 04:42:07'),
('151103', 'ID', 'Kec. Satui', '2019-06-10 04:42:07'),
('151104', 'ID', 'Kec. Kusan Hulu', '2019-06-10 04:42:07'),
('151105', 'ID', 'Kec. Batu Licin', '2019-06-10 04:42:07'),
('151106', 'ID', 'Kec. Simpang Empat', '2019-06-10 04:42:07'),
('151107', 'ID', 'Kec. Karang Bintang', '2019-06-10 04:42:07'),
('151108', 'ID', 'Kec. Mantewe', '2019-06-10 04:42:07'),
('151109', 'ID', 'Kec. Angsana', '2019-06-10 04:42:07'),
('151110', 'ID', 'Kec. Kuranji', '2019-06-10 04:42:07'),
('156000', 'ID', 'Kota Banjarmasin', '2019-06-10 04:42:07'),
('156001', 'ID', 'Kec. Banjarmasin Selatan', '2019-06-10 04:42:07'),
('156002', 'ID', 'Kec. Banjarmasin Timur', '2019-06-10 04:42:07'),
('156003', 'ID', 'Kec. Banjarmasin Barat', '2019-06-10 04:42:07'),
('156004', 'ID', 'Kec. Banjarmasin Utara', '2019-06-10 04:42:07'),
('156005', 'ID', 'Kec. Banjarmasin Tengah', '2019-06-10 04:42:07'),
('156100', 'ID', 'Kota Banjarbaru', '2019-06-10 04:42:07'),
('156101', 'ID', 'Kec. Landasan Ulin', '2019-06-10 04:42:07'),
('156102', 'ID', 'Kec. Cempaka', '2019-06-10 04:42:07'),
('156104', 'ID', 'Kec. Banjarbaru Utara', '2019-06-10 04:42:07'),
('156105', 'ID', 'Kec. Banjarbaru Selatan', '2019-06-10 04:42:07'),
('156106', 'ID', 'Kec. Liang Anggang', '2019-06-10 04:42:07'),
('160000', 'ID', 'Prop. Kalimantan Timur', '2019-06-10 04:42:07'),
('160100', 'ID', 'Kab. Pasir', '2019-06-10 04:42:07'),
('160101', 'ID', 'Kec. Batu Sopang', '2019-06-10 04:42:07'),
('160102', 'ID', 'Kec. Tanjung Harapan', '2019-06-10 04:42:07'),
('160103', 'ID', 'Kec. Pasir Balengkong', '2019-06-10 04:42:07'),
('160104', 'ID', 'Kec. Tanah Grogot', '2019-06-10 04:42:07'),
('160105', 'ID', 'Kec. Kuaro', '2019-06-10 04:42:07'),
('160106', 'ID', 'Kec. Long Ikis', '2019-06-10 04:42:07'),
('160107', 'ID', 'Kec. Muara Komam', '2019-06-10 04:42:07'),
('160108', 'ID', 'Kec. Long Kali', '2019-06-10 04:42:07'),
('160109', 'ID', 'Kec. Muara Samu', '2019-06-10 04:42:07'),
('160110', 'ID', 'Kec. Batu Engau', '2019-06-10 04:42:07'),
('160200', 'ID', 'Kab. Kutai Kartanegara', '2019-06-10 04:42:07'),
('160201', 'ID', 'Kec. Semboja', '2019-06-10 04:42:07'),
('160202', 'ID', 'Kec. Muara Jawa', '2019-06-10 04:42:07'),
('160203', 'ID', 'Kec. Sanga-Sanga', '2019-06-10 04:42:07'),
('160204', 'ID', 'Kec. Loa Janan', '2019-06-10 04:42:07'),
('160205', 'ID', 'Kec. Loa Kulu', '2019-06-10 04:42:07'),
('160206', 'ID', 'Kec. Muara Muntai', '2019-06-10 04:42:07'),
('160207', 'ID', 'Kec. Muara Wis', '2019-06-10 04:42:07'),
('160208', 'ID', 'Kec. Kota Bangun', '2019-06-10 04:42:07'),
('160209', 'ID', 'Kec. Tenggarong', '2019-06-10 04:42:07'),
('160210', 'ID', 'Kec. Sebulu', '2019-06-10 04:42:07'),
('160211', 'ID', 'Kec. Tenggarong Seberang', '2019-06-10 04:42:07'),
('160212', 'ID', 'Kec. Anggana', '2019-06-10 04:42:07'),
('160213', 'ID', 'Kec. Muara Badak', '2019-06-10 04:42:07'),
('160214', 'ID', 'Kec. Marang Kayu', '2019-06-10 04:42:07'),
('160215', 'ID', 'Kec. Muara Kaman', '2019-06-10 04:42:07'),
('160216', 'ID', 'Kec. Kenohan', '2019-06-10 04:42:07'),
('160217', 'ID', 'Kec. Kembang Janggut', '2019-06-10 04:42:07'),
('160218', 'ID', 'Kec. Tabang', '2019-06-10 04:42:07'),
('160300', 'ID', 'Kab. Berau', '2019-06-10 04:42:07'),
('160301', 'ID', 'Kec. Kelay', '2019-06-10 04:42:07'),
('160302', 'ID', 'Kec. Talisayan', '2019-06-10 04:42:07'),
('160303', 'ID', 'Kec. Biduk Biduk', '2019-06-10 04:42:07'),
('160304', 'ID', 'Kec. Pulau Derawan', '2019-06-10 04:42:07'),
('160305', 'ID', 'Kec. Sambaliung', '2019-06-10 04:42:07'),
('160306', 'ID', 'Kec. Tanjung Redeb', '2019-06-10 04:42:07'),
('160307', 'ID', 'Kec. Gunung Tabur', '2019-06-10 04:42:07'),
('160308', 'ID', 'Kec. Segah', '2019-06-10 04:42:07'),
('160309', 'ID', 'Kec. Teluk Bayur', '2019-06-10 04:42:07'),
('160312', 'ID', 'Kec. Tubalar', '2019-06-10 04:42:07'),
('160313', 'ID', 'Kec. Pulau Maratua', '2019-06-10 04:42:07'),
('160314', 'ID', 'Kec. Batu Putih', '2019-06-10 04:42:07'),
('160315', 'ID', 'Kec. Biatan', '2019-06-10 04:42:07'),
('160400', 'ID', 'Kab. Bulongan', '2019-06-10 04:42:07'),
('160402', 'ID', 'Kec. Tanjung Palas', '2019-06-10 04:42:07'),
('160403', 'ID', 'Kec. Sekatak', '2019-06-10 04:42:07'),
('160405', 'ID', 'Kec. Pulau Bunyu', '2019-06-10 04:42:07'),
('160406', 'ID', 'Kec. Tanjung Palas Barat', '2019-06-10 04:42:07'),
('160407', 'ID', 'Kec. Tanjung Palas Utara', '2019-06-10 04:42:07'),
('160408', 'ID', 'Kec. Tanjung Palas Timur', '2019-06-10 04:42:07'),
('160409', 'ID', 'Kec. Tanjung Selor', '2019-06-10 04:42:07'),
('160410', 'ID', 'Kec. Tanjung Palas Tengah', '2019-06-10 04:42:07'),
('160411', 'ID', 'Kec. Peso Hilir', '2019-06-10 04:42:07'),
('160413', 'ID', 'Kec. Peso', '2019-06-10 04:42:07'),
('160701', 'ID', 'Kec. Kayan Hulu', '2019-06-10 04:42:07'),
('160702', 'ID', 'Kec. Kayan Hilir', '2019-06-10 04:42:07'),
('160703', 'ID', 'Kec. Pujungan', '2019-06-10 04:42:07'),
('160704', 'ID', 'Kec. Malinau Kota', '2019-06-10 04:42:07'),
('160705', 'ID', 'Kec. Mentarang', '2019-06-10 04:42:07'),
('160706', 'ID', 'Kec. Sungai Boh', '2019-06-10 04:42:07'),
('160707', 'ID', 'Kec. Malinau Selatan', '2019-06-10 04:42:07'),
('160708', 'ID', 'Kec. Malinau Barat', '2019-06-10 04:42:07'),
('160709', 'ID', 'Kec. Malinau Utara', '2019-06-10 04:42:07'),
('160710', 'ID', 'Kec. Kayan Selatan', '2019-06-10 04:42:07'),
('160711', 'ID', 'Kec. Bahau Hulu', '2019-06-10 04:42:07'),
('160712', 'ID', 'Kec. Mentarang Hulu', '2019-06-10 04:42:07'),
('160801', 'ID', 'Kec. Kerayan', '2019-06-10 04:42:07'),
('160802', 'ID', 'Kec. Lumbis', '2019-06-10 04:42:07'),
('160803', 'ID', 'Kec. Sembakung', '2019-06-10 04:42:07'),
('160804', 'ID', 'Kec. Nunukan', '2019-06-10 04:42:07'),
('160805', 'ID', 'Kec. Sebatik', '2019-06-10 04:42:07'),
('160806', 'ID', 'Kec. Sebuku', '2019-06-10 04:42:07'),
('160807', 'ID', 'Kec. Krayan Selatan', '2019-06-10 04:42:07'),
('160808', 'ID', 'Kec. Sebatik Barat', '2019-06-10 04:42:07'),
('160809', 'ID', 'Kec. Nunukan Selatan', '2019-06-10 04:42:07'),
('160814', 'ID', 'Nunukan', '2019-06-10 04:42:07'),
('160815', 'ID', 'Krayan Selatan', '2019-06-10 04:42:07'),
('160816', 'ID', 'Krayan', '2019-06-10 04:42:07'),
('160817', 'ID', 'Nunukan Selatan', '2019-06-10 04:42:07'),
('160900', 'ID', 'Kab. Kutai Barat', '2019-06-10 04:42:07'),
('160901', 'ID', 'Kec. Bongan', '2019-06-10 04:42:07'),
('160902', 'ID', 'Kec. Jempang', '2019-06-10 04:42:07'),
('160903', 'ID', 'Kec. Penyinggahan', '2019-06-10 04:42:07'),
('160904', 'ID', 'Kec. Muara Pahu', '2019-06-10 04:42:07'),
('160905', 'ID', 'Kec. Muara Lawa', '2019-06-10 04:42:07'),
('160906', 'ID', 'Kec. Damai', '2019-06-10 04:42:07'),
('160907', 'ID', 'Kec. Barong Tongkok', '2019-06-10 04:42:07'),
('160908', 'ID', 'Kec. Melak', '2019-06-10 04:42:07'),
('160909', 'ID', 'Kec. Long Iram', '2019-06-10 04:42:07'),
('160910', 'ID', 'Kec. Long Hubung', '2019-06-10 04:42:07'),
('160911', 'ID', 'Kec. Long Bagun', '2019-06-10 04:42:07'),
('160912', 'ID', 'Kec. Long Pahangai', '2019-06-10 04:42:07'),
('160913', 'ID', 'Kec. Long Apari', '2019-06-10 04:42:07'),
('160915', 'ID', 'Kec. Bentian Besar', '2019-06-10 04:42:07'),
('160916', 'ID', 'Kec. Linggang Bingung', '2019-06-10 04:42:07'),
('160917', 'ID', 'Kec. Manor Bulatn', '2019-06-10 04:42:07'),
('160918', 'ID', 'Kec. Laham', '2019-06-10 04:42:07'),
('160919', 'ID', 'Kec. Nyuatan', '2019-06-10 04:42:07'),
('160920', 'ID', 'Kec. Sekolaq Darat', '2019-06-10 04:42:07'),
('160921', 'ID', 'Kec. Tering', '2019-06-10 04:42:07'),
('160923', 'ID', 'Kec. Siluq Ngurai', '2019-06-10 04:42:07'),
('160924', 'ID', 'Long Bagun', '2019-06-10 04:42:07'),
('160990', 'ID', 'Long Apari', '2019-06-10 04:42:07'),
('160991', 'ID', 'Long Pahangai', '2019-06-10 04:42:07'),
('160992', 'ID', 'Long Hubung', '2019-06-10 04:42:07'),
('160993', 'ID', 'Laham', '2019-06-10 04:42:07'),
('161000', 'ID', 'Kab. Kutai Timur', '2019-06-10 04:42:07'),
('161001', 'ID', 'Kec. Muara Ancalong', '2019-06-10 04:42:07'),
('161002', 'ID', 'Kec. Muara Wahau', '2019-06-10 04:42:07'),
('161003', 'ID', 'Kec. Muara Bengkal', '2019-06-10 04:42:07'),
('161004', 'ID', 'Kec. Sengata Utara', '2019-06-10 04:42:07'),
('161005', 'ID', 'Kec. Sangkulirang', '2019-06-10 04:42:07'),
('161006', 'ID', 'Kec. Kaliorang', '2019-06-10 04:42:07'),
('161008', 'ID', 'Kec. Kombeng', '2019-06-10 04:42:07'),
('161009', 'ID', 'Kec. Bengalon', '2019-06-10 04:42:07'),
('161010', 'ID', 'Kec. Busang', '2019-06-10 04:42:07'),
('161011', 'ID', 'Kec. Sandaran', '2019-06-10 04:42:07'),
('161012', 'ID', 'Kec. Telen', '2019-06-10 04:42:07'),
('161013', 'ID', 'Kec. Sengata Selatan', '2019-06-10 04:42:07'),
('161014', 'ID', 'Kec. Teluk Pandan', '2019-06-10 04:42:07'),
('161015', 'ID', 'Kec. Rantau Pulung', '2019-06-10 04:42:07'),
('161016', 'ID', 'Kec. Kaubun', '2019-06-10 04:42:07'),
('161017', 'ID', 'Kec. Karangan', '2019-06-10 04:42:07'),
('161018', 'ID', 'Kec. Batu Ampar', '2019-06-10 04:42:07'),
('161019', 'ID', 'Kec. Long Mesangat', '2019-06-10 04:42:07'),
('161100', 'ID', 'Kab. Penajam Paser Utara', '2019-06-10 04:42:07'),
('161101', 'ID', 'Kec. Babulu', '2019-06-10 04:42:07'),
('161102', 'ID', 'Kec. Waru', '2019-06-10 04:42:07'),
('161103', 'ID', 'Kec. Penajam', '2019-06-10 04:42:07'),
('161104', 'ID', 'Kec. Sepaku', '2019-06-10 04:42:07'),
('161200', 'ID', 'Kab. Mahakam Ulu', '2020-02-10 03:07:43'),
('161221', 'ID', 'Kec. Long Hubung', '2020-02-10 03:07:43'),
('161222', 'ID', 'Kec. Laham', '2020-02-10 03:07:43'),
('161223', 'ID', 'Kec. Long Apari', '2020-02-10 03:07:43'),
('161224', 'ID', 'Kec. Long Pahangai', '2020-02-10 03:07:43'),
('161225', 'ID', 'Kec. Long Bagun', '2020-02-10 03:07:43'),
('165400', 'ID', 'Kab. Tanah Tidung', '2019-06-10 04:42:07'),
('166000', 'ID', 'Kota Samarinda', '2019-06-10 04:42:07'),
('166001', 'ID', 'Kec. Palaran', '2019-06-10 04:42:07'),
('166002', 'ID', 'Kec. Samarinda Ilir', '2019-06-10 04:42:07'),
('166003', 'ID', 'Kec. Samarinda Seberang', '2019-06-10 04:42:07'),
('166004', 'ID', 'Kec. Sungai Kunjang', '2019-06-10 04:42:07'),
('166005', 'ID', 'Kec. Samarinda Ulu', '2019-06-10 04:42:07'),
('166006', 'ID', 'Kec. Samarinda Utara', '2019-06-10 04:42:07'),
('166007', 'ID', 'Kec. Sambutan', '2019-06-10 04:42:07'),
('166008', 'ID', 'Kec. Sungai Pinang', '2019-06-10 04:42:07'),
('166009', 'ID', 'Kec. Samarinda Kota', '2019-06-10 04:42:07'),
('166010', 'ID', 'Kec. Loa Janan Ilir', '2019-06-10 04:42:07'),
('166100', 'ID', 'Kota Balikpapan', '2019-06-10 04:42:07'),
('166101', 'ID', 'Kec. Balikpapan Selatan', '2019-06-10 04:42:07'),
('166102', 'ID', 'Kec. Balikpapan Timur', '2019-06-10 04:42:07'),
('166103', 'ID', 'Kec. Balikpapan Utara', '2019-06-10 04:42:07'),
('166104', 'ID', 'Kec. Balikpapan Tengah', '2019-06-10 04:42:07'),
('166105', 'ID', 'Kec. Balikpapan Barat', '2019-06-10 04:42:07'),
('166106', 'ID', 'Kec. Balikpapan Kota', '2020-02-10 03:07:43'),
('166201', 'ID', 'Kec. Tarakan Timur', '2019-06-10 04:42:07'),
('166202', 'ID', 'Kec. Tarakan Tengah', '2019-06-10 04:42:07'),
('166203', 'ID', 'Kec. Tarakan Barat', '2019-06-10 04:42:07'),
('166204', 'ID', 'Kec. Tarakan Utara', '2019-06-10 04:42:07'),
('166300', 'ID', 'Kota Bontang', '2019-06-10 04:42:07'),
('166301', 'ID', 'Kec. Bontang Selatan', '2019-06-10 04:42:07'),
('166302', 'ID', 'Kec. Bontang Utara', '2019-06-10 04:42:07'),
('166303', 'ID', 'Kec. Bontang Barat', '2019-06-10 04:42:07'),
('166404', 'ID', 'Sesayap', '2019-06-10 04:42:07'),
('166412', 'ID', 'Sesayap Hilir', '2019-06-10 04:42:07'),
('166414', 'ID', 'Tana Lia', '2019-06-10 04:42:07'),
('170000', 'ID', 'Prop. Sulawesi Utara', '2019-06-10 04:42:07'),
('170100', 'ID', 'Kab. Bolaang Mongondaw', '2019-06-10 04:42:07'),
('170106', 'ID', 'Kec. Lolayan', '2019-06-10 04:42:07'),
('170109', 'ID', 'Kec. Poigar', '2019-06-10 04:42:07'),
('170110', 'ID', 'Kec. Bolaang', '2019-06-10 04:42:07'),
('170111', 'ID', 'Kec. Lolak', '2019-06-10 04:42:07'),
('170112', 'ID', 'Kec. Sangtombolang', '2019-06-10 04:42:07'),
('170117', 'ID', 'Kec. Dumoga Utara', '2019-06-10 04:42:07'),
('170119', 'ID', 'Kec. Dumoga Barat', '2019-06-10 04:42:07'),
('170120', 'ID', 'Kec. Dumoga Timur', '2019-06-10 04:42:07'),
('170128', 'ID', 'Kec. Passi Barat', '2019-06-10 04:42:07'),
('170129', 'ID', 'Kec. Passi Timur', '2019-06-10 04:42:07'),
('170130', 'ID', 'Kec. Bilalang', '2019-06-10 04:42:07'),
('170131', 'ID', 'Kec. Sangtombolang', '2019-06-10 04:42:07'),
('170133', 'ID', 'Bolaang Timur', '2019-06-10 04:42:07'),
('170134', 'ID', 'Kec. Dumoga', '2020-02-10 03:07:43'),
('170135', 'ID', 'Kec. Dumoga Tenggara', '2020-02-10 03:07:43'),
('170136', 'ID', 'Kec. Dumoga Tengah', '2020-02-10 03:07:43'),
('170200', 'ID', 'Kab. Minahasa', '2019-06-10 04:42:07'),
('170209', 'ID', 'Kec. Langowan Timur', '2019-06-10 04:42:07'),
('170211', 'ID', 'Kec. Tompaso', '2019-06-10 04:42:07'),
('170212', 'ID', 'Kec. Kawangkoan', '2019-06-10 04:42:07'),
('170213', 'ID', 'Kec. Sonder', '2019-06-10 04:42:07'),
('170216', 'ID', 'Kec. Tombariri', '2019-06-10 04:42:07'),
('170217', 'ID', 'Kec. Pineleng', '2019-06-10 04:42:07'),
('170219', 'ID', 'Kec. Tondano Timur', '2019-06-10 04:42:07'),
('170220', 'ID', 'Kec. Remboken', '2019-06-10 04:42:07'),
('170221', 'ID', 'Kec. Kakas', '2019-06-10 04:42:07'),
('170222', 'ID', 'Kec. Lembean Timur', '2019-06-10 04:42:07'),
('170223', 'ID', 'Kec. Eris', '2019-06-10 04:42:07'),
('170224', 'ID', 'Kec. Kombi', '2019-06-10 04:42:07'),
('170233', 'ID', 'Kec. Langowan Barat', '2019-06-10 04:42:07'),
('170234', 'ID', 'Kec. Tombulu', '2019-06-10 04:42:07'),
('170235', 'ID', 'Kec. Tondano Barat', '2019-06-10 04:42:07'),
('170236', 'ID', 'Kec. Tondano Utara', '2019-06-10 04:42:07'),
('170237', 'ID', 'Kec. Langowan Selatan', '2019-06-10 04:42:07'),
('170238', 'ID', 'Kec. Tondano Selatan', '2019-06-10 04:42:07'),
('170239', 'ID', 'Kec. Langowan Utara', '2019-06-10 04:42:07'),
('170240', 'ID', 'Kec. Kakas Barat', '2020-02-10 03:07:43'),
('170241', 'ID', 'Kec. Kawangkoan Utara', '2020-02-10 03:07:43'),
('170242', 'ID', 'Kec. Tombariri Timur', '2020-02-10 03:07:43'),
('170243', 'ID', 'Kec. Kawangkoan Barat', '2020-02-10 03:07:43'),
('170244', 'ID', 'Kec. Mandolang', '2020-02-10 03:07:43'),
('170245', 'ID', 'Kec. Tompaso Barat', '2020-02-10 03:07:43'),
('170300', 'ID', 'Kab. Kep. Sangihe', '2019-06-10 04:42:07'),
('170304', 'ID', 'Kec. Manganitu Selatan', '2019-06-10 04:42:07'),
('170305', 'ID', 'Kec. Tamako', '2019-06-10 04:42:07'),
('170306', 'ID', 'Kec. Tabukan Selatan', '2019-06-10 04:42:07'),
('170307', 'ID', 'Kec. Tabukan Tengah', '2019-06-10 04:42:07'),
('170308', 'ID', 'Kec. Manganitu', '2019-06-10 04:42:07'),
('170309', 'ID', 'Kec. Tahuna', '2019-06-10 04:42:07'),
('170310', 'ID', 'Kec. Tabukan Utara', '2019-06-10 04:42:07'),
('170311', 'ID', 'Kec. Kendahe', '2019-06-10 04:42:07'),
('170313', 'ID', 'Kec. Tatoareng', '2019-06-10 04:42:07'),
('170317', 'ID', 'Kec. Nusa Tabukan', '2019-06-10 04:42:07'),
('170319', 'ID', 'Kec. Tabukan Selatan Tengah', '2019-06-10 04:42:07'),
('170320', 'ID', 'Kec. Tabukan Selatan Tenggara', '2019-06-10 04:42:07'),
('170323', 'ID', 'Kec. Tahuna Timur', '2019-06-10 04:42:07'),
('170324', 'ID', 'Kec. Tahuna Barat', '2019-06-10 04:42:07'),
('170390', 'ID', 'Kepulauan Marore', '2019-06-10 04:42:07'),
('170400', 'ID', 'Kab. Kepulauan Talaud', '2019-06-10 04:42:07'),
('170401', 'ID', 'Kec. Kabaruan', '2019-06-10 04:42:07'),
('170402', 'ID', 'Kec. Lirung', '2019-06-10 04:42:07'),
('170403', 'ID', 'Kec. Melonguane', '2019-06-10 04:42:07'),
('170404', 'ID', 'Kec. Beo', '2019-06-10 04:42:07'),
('170405', 'ID', 'Kec. Rainis', '2019-06-10 04:42:07'),
('170406', 'ID', 'Kec. Essang', '2019-06-10 04:42:07'),
('170407', 'ID', 'Kec. Nanusa', '2019-06-10 04:42:07'),
('170408', 'ID', 'Kec. Gemeh', '2019-06-10 04:42:07'),
('170409', 'ID', 'Kec. Damau', '2019-06-10 04:42:07'),
('170410', 'ID', 'Kec. Tanpa Namma', '2019-06-10 04:42:07'),
('170411', 'ID', 'Kec. Lirung Selatan', '2019-06-10 04:42:07'),
('170412', 'ID', 'Kec. Kalongan', '2019-06-10 04:42:07'),
('170413', 'ID', 'Kec. Moronge', '2019-06-10 04:42:07'),
('170414', 'ID', 'Kec. Melonguane Timur', '2019-06-10 04:42:07'),
('170415', 'ID', 'Kec. Beo Utara', '2019-06-10 04:42:07'),
('170416', 'ID', 'Kec. Beo Selatan', '2019-06-10 04:42:07'),
('170417', 'ID', 'Kec. Pulutan', '2019-06-10 04:42:07'),
('170418', 'ID', 'Kec. Essang Selatan', '2019-06-10 04:42:07'),
('170419', 'ID', 'Kec. Miangas', '2019-06-10 04:42:07'),
('170420', 'ID', 'Salibabu', '2019-06-10 04:42:07'),
('170500', 'ID', 'Kab. Minahasa Selatan', '2019-06-10 04:42:07'),
('170501', 'ID', 'Kec. Modoinding', '2019-06-10 04:42:07'),
('170502', 'ID', 'Kec. Tompaso Baru', '2019-06-10 04:42:07'),
('170503', 'ID', 'Kec. Ranoyapo', '2019-06-10 04:42:07'),
('170504', 'ID', 'Kec. Motoling', '2019-06-10 04:42:07'),
('170505', 'ID', 'Kec. Tenga', '2019-06-10 04:42:07'),
('170509', 'ID', 'Kec. Amurang', '2019-06-10 04:42:07'),
('170510', 'ID', 'Kec. Tareran', '2019-06-10 04:42:07'),
('170515', 'ID', 'Kec. Kumelembuai', '2019-06-10 04:42:07'),
('170516', 'ID', 'Kec. Maesaan', '2019-06-10 04:42:07'),
('170517', 'ID', 'Kec. Amurang Barat', '2019-06-10 04:42:07'),
('170518', 'ID', 'Kec. Amurang Timur', '2019-06-10 04:42:07'),
('170519', 'ID', 'Kec. Tatapan', '2019-06-10 04:42:07'),
('170520', 'ID', 'Kec. Motoling Barat', '2019-06-10 04:42:07'),
('170521', 'ID', 'Kec. Motoling Timur', '2019-06-10 04:42:07'),
('170522', 'ID', 'Kec. Sulta', '2019-06-10 04:42:07'),
('170523', 'ID', 'Kec. Tumpaan', '2019-06-10 04:42:07'),
('170524', 'ID', 'Sinonsayang', '2019-06-10 04:42:07'),
('170529', 'ID', 'Kec. Ratatotok', '2019-06-10 04:42:07'),
('170530', 'ID', 'Kec. Pusomaen', '2019-06-10 04:42:07'),
('170531', 'ID', 'Kec. Suluun Tareran', '2020-02-10 03:07:43'),
('170600', 'ID', 'Kab. Minahasa Utara', '2019-06-10 04:42:07'),
('170601', 'ID', 'Kec. Kauditan', '2019-06-10 04:42:07'),
('170602', 'ID', 'Kec. Airmadidi', '2019-06-10 04:42:07'),
('170603', 'ID', 'Kec. Dimembe', '2019-06-10 04:42:07'),
('170604', 'ID', 'Kec. Wori', '2019-06-10 04:42:07'),
('170605', 'ID', 'Kec. Likupang Timur', '2019-06-10 04:42:07'),
('170606', 'ID', 'Kec. Kema', '2019-06-10 04:42:07'),
('170607', 'ID', 'Kec. Likupang Barat', '2019-06-10 04:42:07'),
('170608', 'ID', 'Kec. Kalawat', '2019-06-10 04:42:07'),
('170609', 'ID', 'Kec. Talawaan', '2019-06-10 04:42:07'),
('170610', 'ID', 'Kec. Likupang Selatan', '2019-06-10 04:42:07'),
('170800', 'ID', 'Kab. Bolaang Mongondow Utara', '2019-06-10 04:42:07'),
('170801', 'ID', 'Kec. Bintauna', '2019-06-10 04:42:07'),
('170802', 'ID', 'Kec. Bolaang Itang Timur', '2019-06-10 04:42:07'),
('170803', 'ID', 'Kec. Bolaang Itang Barat', '2019-06-10 04:42:07'),
('170804', 'ID', 'Kec. Kaidipang', '2019-06-10 04:42:07'),
('170805', 'ID', 'Kec. Pinogaluman', '2019-06-10 04:42:07'),
('170806', 'ID', 'Kec. Sangkub', '2019-06-10 04:42:07'),
('170900', 'ID', 'Kab. Kepulauan Sitaro', '2019-06-10 04:42:07'),
('170901', 'ID', 'Kec. Biaro', '2019-06-10 04:42:07'),
('170902', 'ID', 'Kec. Siau Barat', '2019-06-10 04:42:07'),
('170903', 'ID', 'Kec. Siau Barat Selatan', '2019-06-10 04:42:07'),
('170904', 'ID', 'Kec. Siau Barat Utara', '2019-06-10 04:42:07'),
('170905', 'ID', 'Kec. Siau Tengah', '2019-06-10 04:42:07'),
('170906', 'ID', 'Kec. Siau Timur', '2019-06-10 04:42:07'),
('170907', 'ID', 'Kec. Siau Timur Selatan', '2019-06-10 04:42:07'),
('170908', 'ID', 'Kec. Tagulandang', '2019-06-10 04:42:07'),
('170909', 'ID', 'Kec. Tagulandang Selatan', '2019-06-10 04:42:07'),
('170910', 'ID', 'Kec. Tagulandang Utara', '2019-06-10 04:42:07'),
('171000', 'ID', 'Kab. Minahasa Tenggara', '2019-06-10 04:42:07'),
('171001', 'ID', 'Kec. Ratatotok', '2019-06-10 04:42:07'),
('171002', 'ID', 'Kec. Pusomaen', '2019-06-10 04:42:07'),
('171003', 'ID', 'Kec. Belang', '2019-06-10 04:42:07'),
('171004', 'ID', 'Kec. Ratahan', '2019-06-10 04:42:07'),
('171005', 'ID', 'Kec. Tombatu', '2019-06-10 04:42:07'),
('171006', 'ID', 'Kec. Touluaan', '2019-06-10 04:42:07'),
('171007', 'ID', 'Kec. Touluaan Selatan', '2019-06-10 04:42:07'),
('171008', 'ID', 'Kec. Silian Raya', '2019-06-10 04:42:07'),
('171009', 'ID', 'Kec. Tombatu Timur', '2019-06-10 04:42:07'),
('171010', 'ID', 'Kec. Tombatu Utara', '2019-06-10 04:42:07'),
('171011', 'ID', 'Kec. Pasan', '2019-06-10 04:42:07'),
('171012', 'ID', 'Kec. Ratahan Timur', '2019-06-10 04:42:07'),
('171100', 'ID', 'Kab. Bolaang Mongondaw Timur', '2019-06-10 04:42:07'),
('171101', 'ID', 'Kec. Kotabunan', '2019-06-10 04:42:07'),
('171102', 'ID', 'Kec. Nuangan', '2019-06-10 04:42:07'),
('171103', 'ID', 'Kec.Tutuyan', '2019-06-10 04:42:07'),
('171104', 'ID', 'Kec. Modayag', '2019-06-10 04:42:07'),
('171105', 'ID', 'Kec. Modayag Barat', '2019-06-10 04:42:07'),
('171200', 'ID', 'Kab. Bolaang Mongondaw Selatan', '2019-06-10 04:42:07'),
('171201', 'ID', 'Kec. Bolaang Uki', '2019-06-10 04:42:07'),
('171202', 'ID', 'Kec. Posigadan', '2019-06-10 04:42:07'),
('171203', 'ID', 'Kec. Pinolosian', '2019-06-10 04:42:07'),
('171204', 'ID', 'Kec. Pinolosian Timur', '2019-06-10 04:42:07'),
('171205', 'ID', 'Kec. Pinolosian Tengah', '2019-06-10 04:42:07'),
('171206', 'ID', 'Kec. Helumo', '2020-02-10 03:07:43'),
('176000', 'ID', 'Kota Manado', '2019-06-10 04:42:07'),
('176001', 'ID', 'Kec. Malalayang', '2019-06-10 04:42:07'),
('176002', 'ID', 'Kec. Sario', '2019-06-10 04:42:07'),
('176003', 'ID', 'Kec. Wenang', '2019-06-10 04:42:07'),
('176004', 'ID', 'Kec. Mapanget', '2019-06-10 04:42:07'),
('176005', 'ID', 'Kec. Bunaken', '2019-06-10 04:42:07'),
('176006', 'ID', 'Kec. Wanea', '2019-06-10 04:42:07'),
('176007', 'ID', 'Kec. Tikala', '2019-06-10 04:42:07'),
('176008', 'ID', 'Kec. Tuminting', '2019-06-10 04:42:07'),
('176009', 'ID', 'Kec. Singkil', '2019-06-10 04:42:07'),
('176010', 'ID', 'Kec. Paal Dua', '2020-02-10 03:07:43'),
('176011', 'ID', 'Kec. Bunaken Kepulauan', '2020-02-10 03:07:43'),
('176100', 'ID', 'Kota Bitung', '2019-06-10 04:42:07'),
('176101', 'ID', 'Kec. Ranowulu', '2019-06-10 04:42:07'),
('176102', 'ID', 'Kec. Matuari', '2019-06-10 04:42:07'),
('176103', 'ID', 'Kec. Girian', '2019-06-10 04:42:07'),
('176104', 'ID', 'Kec. Madidir', '2019-06-10 04:42:07'),
('176105', 'ID', 'Kec. Maesa', '2019-06-10 04:42:07'),
('176106', 'ID', 'Kec. Aertembaga', '2019-06-10 04:42:07'),
('176107', 'ID', 'Kec. Lembeh Utara', '2019-06-10 04:42:07'),
('176108', 'ID', 'Kec. Lembeh Selatan', '2019-06-10 04:42:07'),
('176200', 'ID', 'Kota Tomohon', '2019-06-10 04:42:07'),
('176201', 'ID', 'Kec. Tomohon Utara', '2019-06-10 04:42:07'),
('176202', 'ID', 'Kec. Tomohon Tengah', '2019-06-10 04:42:07'),
('176203', 'ID', 'Kec. Tomohon Selatan', '2019-06-10 04:42:07'),
('176204', 'ID', 'Kec. Tomohon Timur', '2019-06-10 04:42:07'),
('176205', 'ID', 'Kec. Tomohon Barat', '2019-06-10 04:42:07'),
('176300', 'ID', 'Kota. Kotamobagu', '2019-06-10 04:42:07'),
('176301', 'ID', 'Kec. Kotamobagu Barat', '2019-06-10 04:42:07'),
('176302', 'ID', 'Kec. Kotamobagu Timur', '2019-06-10 04:42:07'),
('176303', 'ID', 'Kec. Kotamobagu Utara', '2019-06-10 04:42:07'),
('176304', 'ID', 'Kec. Kotamobagu Selatan', '2019-06-10 04:42:07'),
('180000', 'ID', 'Prop. Sulawesi Tengah', '2019-06-10 04:42:07'),
('180100', 'ID', 'Kab. Banggai Kepulauan', '2019-06-10 04:42:07'),
('180101', 'ID', 'Kec. Labobo', '2019-06-10 04:42:07'),
('180102', 'ID', 'Kec. Banggai', '2019-06-10 04:42:07'),
('180103', 'ID', 'Kec. Totikum', '2019-06-10 04:42:07'),
('180104', 'ID', 'Kec. Tinangkung', '2019-06-10 04:42:07'),
('180105', 'ID', 'Kec. Liang', '2019-06-10 04:42:07'),
('180106', 'ID', 'Kec. Bulagi', '2019-06-10 04:42:07'),
('180107', 'ID', 'Kec. Buko', '2019-06-10 04:42:07'),
('180109', 'ID', 'Kec. Bokan Kepulauan', '2019-06-10 04:42:07'),
('180110', 'ID', 'Kec. Bulagi Selatan', '2019-06-10 04:42:07'),
('180111', 'ID', 'Kec. Bangkurung', '2019-06-10 04:42:07'),
('180112', 'ID', 'Kec. Banggai Utara', '2019-06-10 04:42:07'),
('180113', 'ID', 'Kec. Banggai Tengah', '2019-06-10 04:42:07'),
('180114', 'ID', 'Kec. Banggai Selatan', '2019-06-10 04:42:07'),
('180115', 'ID', 'Kec. Selatan', '2019-06-10 04:42:07'),
('180116', 'ID', 'Kec. Tinangkung Selatan', '2019-06-10 04:42:07'),
('180117', 'ID', 'Kec. Tinangkung Utara', '2019-06-10 04:42:07'),
('180118', 'ID', 'Kec. Peling Tengah', '2019-06-10 04:42:07'),
('180119', 'ID', 'Kec. Bulagi Utara', '2019-06-10 04:42:07'),
('180120', 'ID', 'Kec. Buko Selatan', '2019-06-10 04:42:07'),
('180121', 'ID', 'Banggai Utara', '2019-06-10 04:42:07'),
('180122', 'ID', 'Banggai', '2019-06-10 04:42:07'),
('180190', 'ID', 'Labobo', '2019-06-10 04:42:07'),
('180191', 'ID', 'Bokan Kepulauan', '2019-06-10 04:42:07'),
('180192', 'ID', 'Bangkurung', '2019-06-10 04:42:07'),
('180193', 'ID', 'Banggai Tengah', '2019-06-10 04:42:07'),
('180194', 'ID', 'Banggai Selatan', '2019-06-10 04:42:07'),
('180199', 'ID', 'Totikum Selatan', '2019-06-10 04:42:07'),
('180200', 'ID', 'Kab. Donggala', '2019-06-10 04:42:07'),
('180208', 'ID', 'Kec. Banawa', '2019-06-10 04:42:07'),
('180209', 'ID', 'Kec. Labuan', '2019-06-10 04:42:07'),
('180210', 'ID', 'Kec. Sindue', '2019-06-10 04:42:07'),
('180212', 'ID', 'Kec. Sirenja', '2019-06-10 04:42:07'),
('180213', 'ID', 'Kec. Balaesang', '2019-06-10 04:42:07'),
('180214', 'ID', 'Kec. Damsol', '2019-06-10 04:42:07'),
('180216', 'ID', 'Kec. Sojol', '2019-06-10 04:42:07'),
('180220', 'ID', 'Kec. Rio Pakava', '2019-06-10 04:42:07'),
('180224', 'ID', 'Kec. Banawa Selatan', '2019-06-10 04:42:07'),
('180225', 'ID', 'Kec. Tanantovea', '2019-06-10 04:42:07'),
('180227', 'ID', 'Kec. Pinembani', '2019-06-10 04:42:07'),
('180233', 'ID', 'Kec. Banawa Tengah', '2019-06-10 04:42:07'),
('180234', 'ID', 'Kec. Sindue Tombusabora', '2019-06-10 04:42:07'),
('180235', 'ID', 'Kec. Sindue Tobata', '2019-06-10 04:42:07'),
('180236', 'ID', 'Kec. Sojol Utara', '2019-06-10 04:42:07'),
('180237', 'ID', 'Kec. Balaesang Tanjung', '2019-06-10 04:42:07'),
('180290', 'ID', 'Dompelas Sojol', '2019-06-10 04:42:07'),
('180300', 'ID', 'Kab. Parigi Mautong', '2019-06-10 04:42:07'),
('180301', 'ID', 'Kec. Pamona Selatan', '2019-06-10 04:42:07'),
('180302', 'ID', 'Kec. Lore Selatan', '2019-06-10 04:42:07'),
('180303', 'ID', 'Kec. Pamona Utara', '2019-06-10 04:42:07'),
('180304', 'ID', 'Kec. Lore Utara', '2019-06-10 04:42:07'),
('180305', 'ID', 'Kec. Poso Pesisir', '2019-06-10 04:42:07'),
('180306', 'ID', 'Kec. Lage', '2019-06-10 04:42:07'),
('180307', 'ID', 'Kec. Poso Kota', '2019-06-10 04:42:07'),
('180314', 'ID', 'Kec. Pamona Timur', '2019-06-10 04:42:07'),
('180315', 'ID', 'Kec. Lore Tengah', '2019-06-10 04:42:07'),
('180316', 'ID', 'Kec. Pamona Barat', '2019-06-10 04:42:07'),
('180317', 'ID', 'Kec. Poso Pesisir Selatan', '2019-06-10 04:42:07'),
('180318', 'ID', 'Kec. Poso Pesisir Utara', '2019-06-10 04:42:07'),
('180319', 'ID', 'Kec. Poso Kota Utara', '2019-06-10 04:42:07'),
('180320', 'ID', 'Kec. Lore Barat', '2019-06-10 04:42:07'),
('180321', 'ID', 'Kec. Poso Kota Selatan', '2019-06-10 04:42:07'),
('180322', 'ID', 'Pamona Puselemba', '2019-06-10 04:42:07'),
('180323', 'ID', 'Kec. Lore Timur', '2019-06-10 04:42:07'),
('180324', 'ID', 'Kec. Lore Peore', '2019-06-10 04:42:07'),
('180325', 'ID', 'Pamona Tenggara', '2019-06-10 04:42:07'),
('180400', 'ID', 'Kab. Banggai', '2019-06-10 04:42:07'),
('180401', 'ID', 'Kec. Toili', '2019-06-10 04:42:07'),
('180402', 'ID', 'Kec. Batui', '2019-06-10 04:42:07'),
('180403', 'ID', 'Kec. Bunta', '2019-06-10 04:42:07'),
('180404', 'ID', 'Kec. Kintom', '2019-06-10 04:42:07'),
('180405', 'ID', 'Kec. Luwuk', '2019-06-10 04:42:07'),
('180406', 'ID', 'Kec. Pagimana', '2019-06-10 04:42:07'),
('180407', 'ID', 'Kec. Lamala', '2019-06-10 04:42:07'),
('180408', 'ID', 'Kec. Balantak', '2019-06-10 04:42:07'),
('180409', 'ID', 'Kec. Bualemo', '2019-06-10 04:42:07'),
('180410', 'ID', 'Kec. Toili Barat', '2019-06-10 04:42:07'),
('180411', 'ID', 'Kec. Nuhon', '2019-06-10 04:42:07'),
('180412', 'ID', 'Kec. Luwuk Timur', '2019-06-10 04:42:07'),
('180413', 'ID', 'Kec. Masama', '2019-06-10 04:42:07'),
('180414', 'ID', 'Kec. Moilong', '2020-02-10 03:07:43'),
('180415', 'ID', 'Kec. Batui Selatan', '2020-02-10 03:07:43'),
('180416', 'ID', 'Kec. Luwuk Utara', '2020-02-10 03:07:43'),
('180417', 'ID', 'Kec. Luwuk Selatan', '2020-02-10 03:07:43'),
('180418', 'ID', 'Kec. Nambo', '2020-02-10 03:07:43'),
('180419', 'ID', 'Kec. Mantoh', '2020-02-10 03:07:43'),
('180420', 'ID', 'Kec. Balantak Utara', '2020-02-10 03:07:43'),
('180421', 'ID', 'Kec. Balantak Selatan', '2020-02-10 03:07:43'),
('180422', 'ID', 'Kec. Lobu', '2020-02-10 03:07:43'),
('180423', 'ID', 'Kec. Simpang Raya', '2020-02-10 03:07:43'),
('180500', 'ID', 'Kab. Buol', '2019-06-10 04:42:07'),
('180501', 'ID', 'Kec. Biau', '2019-06-10 04:42:07'),
('180502', 'ID', 'Kec. Momunu', '2019-06-10 04:42:07'),
('180503', 'ID', 'Kec. Bokat', '2019-06-10 04:42:07'),
('180504', 'ID', 'Kec. Bunobogu', '2019-06-10 04:42:07'),
('180505', 'ID', 'Kec. Paleleh', '2019-06-10 04:42:07'),
('180506', 'ID', 'Kec. Tiloan', '2019-06-10 04:42:07'),
('180507', 'ID', 'Kec. Bukal', '2019-06-10 04:42:07'),
('180508', 'ID', 'Kec. Gadung', '2019-06-10 04:42:07'),
('180509', 'ID', 'Kec. Lipunoto', '2019-06-10 04:42:07'),
('180510', 'ID', 'Kec. Karamat', '2019-06-10 04:42:07'),
('180511', 'ID', 'Kec. Paleleh Barat', '2019-06-10 04:42:07'),
('180512', 'ID', 'Lakea', '2019-06-10 04:42:07'),
('180600', 'ID', 'Kab. Toli-Toli', '2019-06-10 04:42:07'),
('180601', 'ID', 'Kec. Dampal Selatan', '2019-06-10 04:42:07'),
('180602', 'ID', 'Kec. Dampal Utara', '2019-06-10 04:42:07'),
('180603', 'ID', 'Kec. Dondo', '2019-06-10 04:42:07'),
('180604', 'ID', 'Kec. Baolan', '2019-06-10 04:42:07'),
('180605', 'ID', 'Kec. Galang', '2019-06-10 04:42:07'),
('180606', 'ID', 'Kec. Utara Toli-Toli', '2019-06-10 04:42:07'),
('180607', 'ID', 'Kec. Ogo Deide', '2019-06-10 04:42:07'),
('180608', 'ID', 'Kec. Basidondo', '2019-06-10 04:42:07'),
('180609', 'ID', 'Kec. Lampasio', '2019-06-10 04:42:07'),
('180610', 'ID', 'Kec. Dako Pemean', '2019-06-10 04:42:07'),
('180700', 'ID', 'Kab. Marowali', '2019-06-10 04:42:07'),
('180701', 'ID', 'Kec. Menui Kepulauan', '2019-06-10 04:42:07'),
('180702', 'ID', 'Kec. Bungku Selatan', '2019-06-10 04:42:07'),
('180703', 'ID', 'Kec. Bungku Tengah', '2019-06-10 04:42:07'),
('180704', 'ID', 'Kec. Bungku Barat', '2019-06-10 04:42:07'),
('180705', 'ID', 'Kec. Lembo', '2019-06-10 04:42:07'),
('180706', 'ID', 'Kec. Mori Atas', '2019-06-10 04:42:07'),
('180707', 'ID', 'Kec. Petasia', '2019-06-10 04:42:07'),
('180708', 'ID', 'Kec. Bungku Utara', '2019-06-10 04:42:07'),
('180709', 'ID', 'Kec. Bahodopi', '2019-06-10 04:42:07'),
('180710', 'ID', 'Kec. Soyo Jaya', '2019-06-10 04:42:07'),
('180711', 'ID', 'Kec. Witaponda', '2019-06-10 04:42:07'),
('180712', 'ID', 'Kec. Mamosalato', '2019-06-10 04:42:07'),
('180713', 'ID', 'Kec. Bumi Raya', '2019-06-10 04:42:07'),
('180714', 'ID', 'Kec. Petasia Barat', '2020-02-10 03:07:43'),
('180715', 'ID', 'Kec. Bungku Timur', '2020-02-10 03:07:43'),
('180716', 'ID', 'Kec. Bungku Pesisir', '2020-02-10 03:07:43'),
('180717', 'ID', 'Kec. Lembo Raya', '2020-02-10 03:07:43'),
('180718', 'ID', 'Kec. Mori Utara', '2020-02-10 03:07:43'),
('180719', 'ID', 'Kec. Petasia Timur', '2020-02-10 03:07:43'),
('180800', 'ID', 'Kab. Poso', '2019-06-10 04:42:07'),
('180801', 'ID', 'Kec. Sausu', '2019-06-10 04:42:07'),
('180802', 'ID', 'Kec. Parigi', '2019-06-10 04:42:07'),
('180803', 'ID', 'Kec. Ampibabo', '2019-06-10 04:42:07'),
('180804', 'ID', 'Kec. Tinombo', '2019-06-10 04:42:07'),
('180805', 'ID', 'Kec. Tomini', '2019-06-10 04:42:07'),
('180806', 'ID', 'Kec. Moutong', '2019-06-10 04:42:07'),
('180807', 'ID', 'Kec. Bolano Lambunu', '2019-06-10 04:42:07'),
('180808', 'ID', 'Kec. Kasimbar', '2019-06-10 04:42:07'),
('180809', 'ID', 'Kec. Torue', '2019-06-10 04:42:07'),
('180810', 'ID', 'Kec. Tinombo Selatan', '2019-06-10 04:42:07'),
('180811', 'ID', 'Kec. Parigi Selatan', '2019-06-10 04:42:07'),
('180812', 'ID', 'Kec. Mepanga', '2019-06-10 04:42:07'),
('180813', 'ID', 'Kec. Malakosa', '2019-06-10 04:42:07'),
('180814', 'ID', 'Kec. Parigi Barat', '2019-06-10 04:42:07'),
('180815', 'ID', 'Kec. Parigi Utara', '2019-06-10 04:42:07'),
('180816', 'ID', 'Kec. Ribulu', '2019-06-10 04:42:07'),
('180817', 'ID', 'Kec. Siniu', '2019-06-10 04:42:07'),
('180818', 'ID', 'Kec. Palasa', '2019-06-10 04:42:07'),
('180819', 'ID', 'Kec. Taopa', '2019-06-10 04:42:07'),
('180820', 'ID', 'Kec. Parigi Tengah', '2019-06-10 04:42:07'),
('180821', 'ID', 'Balinggi', '2019-06-10 04:42:07'),
('180890', 'ID', 'Ongka Malino', '2019-06-10 04:42:07'),
('180891', 'ID', 'Bolano', '2019-06-10 04:42:07'),
('180892', 'ID', 'Lambunu', '2019-06-10 04:42:07'),
('180893', 'ID', 'Kec. Sidoan', '2020-02-10 03:07:43'),
('180900', 'ID', 'Kab. Tojo Una-Una', '2019-06-10 04:42:07'),
('180901', 'ID', 'Kec. Tojo', '2019-06-10 04:42:07'),
('180902', 'ID', 'Kec. Ulubongka', '2019-06-10 04:42:07'),
('180903', 'ID', 'Kec. Ampana Tete', '2019-06-10 04:42:07'),
('180904', 'ID', 'Kec. Ampana Kota', '2019-06-10 04:42:07'),
('180905', 'ID', 'Kec. Una - Una', '2019-06-10 04:42:07'),
('180906', 'ID', 'Kec. Walea Kepulauan', '2019-06-10 04:42:07'),
('180907', 'ID', 'Kec. Togean', '2019-06-10 04:42:07'),
('180908', 'ID', 'Kec. Tojo Barat', '2019-06-10 04:42:07'),
('180909', 'ID', 'Kec. Walea Besar', '2019-06-10 04:42:07'),
('180910', 'ID', 'Kec. Ratolindo', '2020-02-10 03:07:43'),
('180911', 'ID', 'Kec. Batudaka', '2020-02-10 03:07:43'),
('180912', 'ID', 'Kec. Talatako', '2020-02-10 03:07:43'),
('181000', 'ID', 'Kab. Sigi', '2019-06-10 04:42:07'),
('181001', 'ID', 'Kec. Kulawi', '2019-06-10 04:42:07'),
('181002', 'ID', 'Kec. Pipikoro', '2019-06-10 04:42:07'),
('181003', 'ID', 'Kec. Kulawi Selatan', '2019-06-10 04:42:07'),
('181004', 'ID', 'Kec. Lindu', '2019-06-10 04:42:07'),
('181005', 'ID', 'Kec. Palolo', '2019-06-10 04:42:07'),
('181006', 'ID', 'Kec. Nokilalaki', '2019-06-10 04:42:07'),
('181007', 'ID', 'Kec. Dolo', '2019-06-10 04:42:07'),
('181008', 'ID', 'Kec. Dolo Selatan', '2019-06-10 04:42:07'),
('181009', 'ID', 'Kec. Dolo Barat', '2019-06-10 04:42:07'),
('181010', 'ID', 'Kec. Marawola', '2019-06-10 04:42:07'),
('181011', 'ID', 'Kec. Kinovaru', '2019-06-10 04:42:07'),
('181012', 'ID', 'Kec. Marawola Barat', '2019-06-10 04:42:07'),
('181013', 'ID', 'Kec. Sigibiromaru', '2019-06-10 04:42:07'),
('181014', 'ID', 'Kec. Gumbasa', '2019-06-10 04:42:07'),
('181015', 'ID', 'Kec. Tanambulava', '2019-06-10 04:42:07'),
('181100', 'ID', 'Kab. Banggai Laut', '2020-02-10 03:07:43'),
('181112', 'ID', 'Kec. Bokan Kepulauan', '2020-02-10 03:07:43'),
('181113', 'ID', 'Kec. Bangkurung', '2020-02-10 03:07:43'),
('181114', 'ID', 'Kec. Labobo', '2020-02-10 03:07:43'),
('181115', 'ID', 'Kec. Banggai Selatan', '2020-02-10 03:07:43'),
('181116', 'ID', 'Kec. Banggai Tengah', '2020-02-10 03:07:43'),
('181117', 'ID', 'Kec. Banggai', '2020-02-10 03:07:43'),
('181118', 'ID', 'Kec. Banggai Utara', '2020-02-10 03:07:43'),
('181119', 'ID', 'Kec. Banggai', '2020-02-10 03:07:43'),
('181120', 'ID', 'Kec. Banggai Utara', '2020-02-10 03:07:43'),
('181200', 'ID', 'Kab. Morowali Utara', '2020-02-10 03:07:43'),
('181201', 'ID', 'Kec. Bungku Utara', '2020-02-10 03:07:43'),
('181202', 'ID', 'Kec. Lembo', '2020-02-10 03:07:43'),
('181203', 'ID', 'Kec. Lembo Raya', '2020-02-10 03:07:43'),
('181204', 'ID', 'Kec. Mamosalato', '2020-02-10 03:07:43'),
('181205', 'ID', 'Kec. Mori Atas', '2020-02-10 03:07:43'),
('181206', 'ID', 'Kec. Mori Utara', '2020-02-10 03:07:43'),
('181207', 'ID', 'Kec. Petasia', '2020-02-10 03:07:43'),
('181208', 'ID', 'Kec. Petasia Timur', '2020-02-10 03:07:44'),
('181209', 'ID', 'Kec. Soyo Jaya', '2020-02-10 03:07:44'),
('181210', 'ID', 'Kec. Petasia Barat', '2020-02-10 03:07:44'),
('186000', 'ID', 'Kota Palu', '2019-06-10 04:42:07'),
('186001', 'ID', 'Kec. Palu Barat', '2019-06-10 04:42:07'),
('186002', 'ID', 'Kec. Palu Selatan', '2019-06-10 04:42:07'),
('186003', 'ID', 'Kec. Palu Timur', '2019-06-10 04:42:07'),
('186004', 'ID', 'Kec. Palu Utara', '2019-06-10 04:42:07'),
('186005', 'ID', 'Kec. Ulujadi', '2020-02-10 03:07:44'),
('186006', 'ID', 'Kec. Tatanga', '2020-02-10 03:07:44'),
('186007', 'ID', 'Kec. Tawaeli', '2020-02-10 03:07:44'),
('186008', 'ID', 'Kec. Mantikulore', '2020-02-10 03:07:44'),
('190000', 'ID', 'Prop. Sulawesi Selatan', '2019-06-10 04:42:07'),
('190100', 'ID', 'Kab. Maros', '2019-06-10 04:42:07'),
('190101', 'ID', 'Kec. Mandai', '2019-06-10 04:42:07'),
('190102', 'ID', 'Kec. Maros Baru', '2019-06-10 04:42:07'),
('190103', 'ID', 'Kec. Maros Utara', '2019-06-10 04:42:07'),
('190104', 'ID', 'Kec. Bantimurung', '2019-06-10 04:42:07'),
('190105', 'ID', 'Kec. Tanralili', '2019-06-10 04:42:07'),
('190106', 'ID', 'Kec. Camba', '2019-06-10 04:42:07'),
('190107', 'ID', 'Kec. Mallawa', '2019-06-10 04:42:07'),
('190108', 'ID', 'Kec. Moncongloe', '2019-06-10 04:42:07'),
('190109', 'ID', 'Kec. Turikale', '2019-06-10 04:42:07'),
('190110', 'ID', 'Kec. Marusu', '2019-06-10 04:42:07'),
('190111', 'ID', 'Kec. Lau', '2019-06-10 04:42:07'),
('190112', 'ID', 'Kec. Simbang', '2019-06-10 04:42:07'),
('190113', 'ID', 'Kec. Tompobulu', '2019-06-10 04:42:07'),
('190114', 'ID', 'Kec. Cenrana', '2019-06-10 04:42:07'),
('190115', 'ID', 'Bontoa', '2019-06-10 04:42:07'),
('190200', 'ID', 'Kab. Pangkajene Kepulauan', '2019-06-10 04:42:07'),
('190201', 'ID', 'Kec. Liukang Tangaya', '2019-06-10 04:42:07'),
('190202', 'ID', 'Kec. Liukang Kalukuang Masalim', '2019-06-10 04:42:07'),
('190203', 'ID', 'Kec. Liukang Tapabiring', '2019-06-10 04:42:07'),
('190204', 'ID', 'Kec. Pangkajene', '2019-06-10 04:42:07'),
('190205', 'ID', 'Kec. Balocci', '2019-06-10 04:42:07'),
('190206', 'ID', 'Kec. Bungoro', '2019-06-10 04:42:07'),
('190207', 'ID', 'Kec. Labakkang', '2019-06-10 04:42:07'),
('190208', 'ID', 'Kec. Ma`rang', '2019-06-10 04:42:07'),
('190209', 'ID', 'Kec. Sigeri', '2019-06-10 04:42:07'),
('190210', 'ID', 'Kec. Minasatene', '2019-06-10 04:42:07'),
('190211', 'ID', 'Kec. Tondong Tallasa', '2019-06-10 04:42:07'),
('190212', 'ID', 'Kec. Mandalle', '2019-06-10 04:42:07'),
('190213', 'ID', 'Liukang Tupabbiring Utara', '2019-06-10 04:42:07'),
('190300', 'ID', 'Kab. Gowa', '2019-06-10 04:42:07'),
('190301', 'ID', 'Kec. Bontonompo', '2019-06-10 04:42:07'),
('190302', 'ID', 'Kec. Bajeng', '2019-06-10 04:42:07'),
('190303', 'ID', 'Kec. Pallangga', '2019-06-10 04:42:07'),
('190304', 'ID', 'Kec. Somba Opu', '2019-06-10 04:42:07'),
('190305', 'ID', 'Kec. Bontomarannu', '2019-06-10 04:42:07'),
('190306', 'ID', 'Kec. Parang Loe', '2019-06-10 04:42:07'),
('190307', 'ID', 'Kec. Tinggi Moncong', '2019-06-10 04:42:07'),
('190308', 'ID', 'Kec. Bungaya', '2019-06-10 04:42:07'),
('190309', 'ID', 'Kec. Tompobulu', '2019-06-10 04:42:07'),
('190310', 'ID', 'Kec. Barombong', '2019-06-10 04:42:07'),
('190311', 'ID', 'Kec. Biring Bulu', '2019-06-10 04:42:07'),
('190312', 'ID', 'Kec. Tombolo Pao', '2019-06-10 04:42:07'),
('190313', 'ID', 'Kec. Manuju', '2019-06-10 04:42:07'),
('190314', 'ID', 'Kec. Bontolempangan', '2019-06-10 04:42:07'),
('190315', 'ID', 'Kec. Pattallassang', '2019-06-10 04:42:07'),
('190316', 'ID', 'Kec. Bontonompo Selatan', '2019-06-10 04:42:07'),
('190317', 'ID', 'Kec. Parigi', '2019-06-10 04:42:07'),
('190318', 'ID', 'Kec. Bajeng Barat', '2019-06-10 04:42:07'),
('190399', 'ID', 'Pattalasang', '2019-06-10 04:42:07'),
('190400', 'ID', 'Kab. Takalar', '2019-06-10 04:42:07'),
('190401', 'ID', 'Kec. Mangarabombang', '2019-06-10 04:42:07'),
('190402', 'ID', 'Kec. Mappakasunggu', '2019-06-10 04:42:07'),
('190403', 'ID', 'Kec. Polombangkeng Selatan', '2019-06-10 04:42:07'),
('190404', 'ID', 'Kec. Polombangkeng Utara', '2019-06-10 04:42:07'),
('190405', 'ID', 'Kec. Galesong Selatan', '2019-06-10 04:42:07'),
('190406', 'ID', 'Kec. Galesong Utara', '2019-06-10 04:42:07'),
('190408', 'ID', 'Kec. Pattallassang', '2019-06-10 04:42:07'),
('190409', 'ID', 'Kec. Sanrobone', '2019-06-10 04:42:07'),
('190410', 'ID', 'Kec. Galesong', '2019-06-10 04:42:07'),
('190500', 'ID', 'Kab. Jeneponto', '2019-06-10 04:42:07'),
('190501', 'ID', 'Kec. Bangkala', '2019-06-10 04:42:07'),
('190502', 'ID', 'Kec. Tamalatea', '2019-06-10 04:42:07'),
('190503', 'ID', 'Kec. Binamu', '2019-06-10 04:42:07'),
('190504', 'ID', 'Kec. Batang', '2019-06-10 04:42:07'),
('190505', 'ID', 'Kec. Kelara', '2019-06-10 04:42:07'),
('190506', 'ID', 'Kec. Bangkala Barat', '2019-06-10 04:42:07'),
('190507', 'ID', 'Kec. Bontoramba', '2019-06-10 04:42:07'),
('190508', 'ID', 'Kec. Turatea', '2019-06-10 04:42:07'),
('190509', 'ID', 'Kec. Arungkeke', '2019-06-10 04:42:07'),
('190510', 'ID', 'Kec. Rumbia', '2019-06-10 04:42:07'),
('190511', 'ID', 'Kec. Tarowang', '2019-06-10 04:42:07'),
('190600', 'ID', 'Kab. Barru', '2019-06-10 04:42:07'),
('190601', 'ID', 'Kec. Tanete Riaja', '2019-06-10 04:42:07'),
('190602', 'ID', 'Kec. Tanete Rilau', '2019-06-10 04:42:07'),
('190603', 'ID', 'Kec. Barru', '2019-06-10 04:42:07'),
('190604', 'ID', 'Kec. Soppeng Riaja', '2019-06-10 04:42:07'),
('190605', 'ID', 'Kec. Mallusetasi', '2019-06-10 04:42:07'),
('190606', 'ID', 'Kec. Balusu', '2019-06-10 04:42:07'),
('190607', 'ID', 'Kec. Pujananting', '2019-06-10 04:42:07'),
('190700', 'ID', 'Kab. Bone', '2019-06-10 04:42:07'),
('190701', 'ID', 'Kec. Bontocani', '2019-06-10 04:42:07'),
('190702', 'ID', 'Kec. Kahu', '2019-06-10 04:42:07'),
('190703', 'ID', 'Kec. Kajuara', '2019-06-10 04:42:07'),
('190704', 'ID', 'Kec. Selomekko', '2019-06-10 04:42:07');
INSERT INTO `dikti_wilayah` (`id_wilayah`, `id_negara`, `nama_wilayah`, `dikti_wilayah_timestamp`) VALUES
('190705', 'ID', 'Kec. Tonra', '2019-06-10 04:42:07'),
('190706', 'ID', 'Kec. Patimpeng', '2019-06-10 04:42:07'),
('190707', 'ID', 'Kec. Libureng', '2019-06-10 04:42:07'),
('190708', 'ID', 'Kec. Mare', '2019-06-10 04:42:07'),
('190709', 'ID', 'Kec. Sibulue', '2019-06-10 04:42:07'),
('190710', 'ID', 'Kec. Cina', '2019-06-10 04:42:07'),
('190711', 'ID', 'Kec. Barebbo', '2019-06-10 04:42:07'),
('190712', 'ID', 'Kec. Ponre', '2019-06-10 04:42:07'),
('190713', 'ID', 'Kec. Lappariaja', '2019-06-10 04:42:07'),
('190714', 'ID', 'Kec. Lamuru', '2019-06-10 04:42:07'),
('190715', 'ID', 'Kec. Bengo', '2019-06-10 04:42:07'),
('190716', 'ID', 'Kec. Ulaweng', '2019-06-10 04:42:07'),
('190717', 'ID', 'Kec. Palakka', '2019-06-10 04:42:07'),
('190718', 'ID', 'Kec. Awangpone', '2019-06-10 04:42:07'),
('190719', 'ID', 'Kec. Tellu Siattinge', '2019-06-10 04:42:07'),
('190720', 'ID', 'Kec. Amali', '2019-06-10 04:42:07'),
('190721', 'ID', 'Kec. Ajangale', '2019-06-10 04:42:07'),
('190722', 'ID', 'Kec. Dua Boccoe', '2019-06-10 04:42:07'),
('190723', 'ID', 'Kec. Cenrana', '2019-06-10 04:42:07'),
('190724', 'ID', 'Kec. Tanete Riattang Barat', '2019-06-10 04:42:07'),
('190725', 'ID', 'Kec. Tanete Riattang', '2019-06-10 04:42:07'),
('190726', 'ID', 'Kec. Tanete Riattang Timur', '2019-06-10 04:42:07'),
('190727', 'ID', 'Kec. Tellu Limpoe', '2019-06-10 04:42:07'),
('190800', 'ID', 'Kab. Wajo', '2019-06-10 04:42:07'),
('190801', 'ID', 'Kec. Sabbangparu', '2019-06-10 04:42:07'),
('190802', 'ID', 'Kec. Tempe', '2019-06-10 04:42:07'),
('190803', 'ID', 'Kec. Pammana', '2019-06-10 04:42:07'),
('190804', 'ID', 'Kec. Bola', '2019-06-10 04:42:07'),
('190805', 'ID', 'Kec. Takkalala', '2019-06-10 04:42:07'),
('190806', 'ID', 'Kec. Sajo Anging', '2019-06-10 04:42:07'),
('190807', 'ID', 'Kec. Majauleng', '2019-06-10 04:42:07'),
('190808', 'ID', 'Kec. Tanasitolo', '2019-06-10 04:42:07'),
('190809', 'ID', 'Kec. Belawa', '2019-06-10 04:42:07'),
('190810', 'ID', 'Kec. Maniang Pajo', '2019-06-10 04:42:07'),
('190811', 'ID', 'Kec. Keera', '2019-06-10 04:42:07'),
('190812', 'ID', 'Kec. Pitumpanua', '2019-06-10 04:42:07'),
('190813', 'ID', 'Kec. Gilireng', '2019-06-10 04:42:07'),
('190814', 'ID', 'Kec. Penrang', '2019-06-10 04:42:07'),
('190900', 'ID', 'Kab. Soppeng', '2019-06-10 04:42:07'),
('190901', 'ID', 'Kec. Mariowiwawo', '2019-06-10 04:42:07'),
('190902', 'ID', 'Kec. Lalabata', '2019-06-10 04:42:07'),
('190903', 'ID', 'Kec. Liliriaja', '2019-06-10 04:42:07'),
('190904', 'ID', 'Kec. Lili Rilau', '2019-06-10 04:42:07'),
('190905', 'ID', 'Kec. Donri-Donri', '2019-06-10 04:42:07'),
('190906', 'ID', 'Kec. Mario Riawa', '2019-06-10 04:42:07'),
('190907', 'ID', 'Kec. Ganra', '2019-06-10 04:42:07'),
('190908', 'ID', 'Kec. Citta', '2019-06-10 04:42:07'),
('191000', 'ID', 'Kab. Bantaeng', '2019-06-10 04:42:07'),
('191001', 'ID', 'Kec. Bissappu', '2019-06-10 04:42:07'),
('191002', 'ID', 'Kec. Bantaeng', '2019-06-10 04:42:07'),
('191003', 'ID', 'Kec. Tompobulu', '2019-06-10 04:42:07'),
('191004', 'ID', 'Kec. Ulu Ere', '2019-06-10 04:42:07'),
('191005', 'ID', 'Kec. Ere Merasa', '2019-06-10 04:42:07'),
('191006', 'ID', 'Kec. Pa`jukukang', '2019-06-10 04:42:07'),
('191007', 'ID', 'Kec. Gantarang Keke', '2019-06-10 04:42:07'),
('191008', 'ID', 'Kec. Sinoa', '2019-06-10 04:42:07'),
('191100', 'ID', 'Kab. Bulukumba', '2019-06-10 04:42:07'),
('191101', 'ID', 'Kec. Gantarang', '2019-06-10 04:42:07'),
('191102', 'ID', 'Kec. Ujung Bulu', '2019-06-10 04:42:07'),
('191103', 'ID', 'Kec. Bontobahari', '2019-06-10 04:42:07'),
('191104', 'ID', 'Kec. Bontotiro', '2019-06-10 04:42:07'),
('191105', 'ID', 'Kec. Hero Lange-Lange', '2019-06-10 04:42:07'),
('191106', 'ID', 'Kec. Kajang', '2019-06-10 04:42:07'),
('191107', 'ID', 'Kec. Bulukumba', '2019-06-10 04:42:07'),
('191108', 'ID', 'Kec. Rilau Ale', '2019-06-10 04:42:07'),
('191109', 'ID', 'Kec. Kindang', '2019-06-10 04:42:07'),
('191110', 'ID', 'Kec. Ujung Loe', '2019-06-10 04:42:07'),
('191200', 'ID', 'Kab. Sinjai', '2019-06-10 04:42:07'),
('191201', 'ID', 'Kec. Sinjai Barat', '2019-06-10 04:42:07'),
('191202', 'ID', 'Kec. Sinjai Borong', '2019-06-10 04:42:07'),
('191203', 'ID', 'Kec. Sinjai Selatan', '2019-06-10 04:42:07'),
('191204', 'ID', 'Kec. Tellu Limpoe', '2019-06-10 04:42:07'),
('191205', 'ID', 'Kec. Sinjai Timur', '2019-06-10 04:42:07'),
('191206', 'ID', 'Kec. Sinjai Tengah', '2019-06-10 04:42:07'),
('191207', 'ID', 'Kec. Sinjai Utara', '2019-06-10 04:42:07'),
('191208', 'ID', 'Kec. Bulupoddo', '2019-06-10 04:42:07'),
('191209', 'ID', 'Kec. Pulau Sembilan', '2019-06-10 04:42:07'),
('191300', 'ID', 'Kab. Selayar', '2019-06-10 04:42:07'),
('191301', 'ID', 'Kec. Pasimarannu', '2019-06-10 04:42:07'),
('191302', 'ID', 'Kec. Pasimassunggu', '2019-06-10 04:42:07'),
('191303', 'ID', 'Kec. Bontosikuyu', '2019-06-10 04:42:07'),
('191304', 'ID', 'Kec. Bontoharu', '2019-06-10 04:42:07'),
('191305', 'ID', 'Kec. Bontomatene', '2019-06-10 04:42:07'),
('191306', 'ID', 'Kec. Bontomanai', '2019-06-10 04:42:07'),
('191307', 'ID', 'Kec. Benteng', '2019-06-10 04:42:07'),
('191308', 'ID', 'Kec. Taka Bonerate', '2019-06-10 04:42:07'),
('191309', 'ID', 'Kec. Pasilambena', '2019-06-10 04:42:07'),
('191310', 'ID', 'Kec. Pasimasunggu Timur', '2019-06-10 04:42:07'),
('191311', 'ID', 'Kec. Buki', '2019-06-10 04:42:07'),
('191390', 'ID', 'Pasimasunggu', '2019-06-10 04:42:07'),
('191391', 'ID', 'Kec. Pasimasunggu', '2020-02-10 03:07:44'),
('191392', 'ID', 'Kec. Pasimasunggu', '2020-02-10 03:07:44'),
('191393', 'ID', 'Kec. Pasimasunggu', '2020-02-10 03:07:44'),
('191394', 'ID', 'Kec. Pasimasunggu', '2020-02-10 03:07:44'),
('191400', 'ID', 'Kab. Pinrang', '2019-06-10 04:42:07'),
('191401', 'ID', 'Kec. Suppa', '2019-06-10 04:42:07'),
('191402', 'ID', 'Kec. Mattiro Sompe', '2019-06-10 04:42:07'),
('191403', 'ID', 'Kec. Mattiro Bulu', '2019-06-10 04:42:07'),
('191404', 'ID', 'Kec. Watang Sawitto', '2019-06-10 04:42:07'),
('191405', 'ID', 'Kec. Patampanua', '2019-06-10 04:42:07'),
('191406', 'ID', 'Kec. Cempa', '2019-06-10 04:42:07'),
('191407', 'ID', 'Kec. Duampanua', '2019-06-10 04:42:07'),
('191408', 'ID', 'Kec. Lembang', '2019-06-10 04:42:07'),
('191409', 'ID', 'Kec. Lanrisang', '2019-06-10 04:42:07'),
('191410', 'ID', 'Kec. Tiroang', '2019-06-10 04:42:07'),
('191411', 'ID', 'Kec. Paleteang', '2019-06-10 04:42:07'),
('191412', 'ID', 'Kec. Batulappa', '2019-06-10 04:42:07'),
('191500', 'ID', 'Kab. Sidenreng Rappang', '2019-06-10 04:42:07'),
('191501', 'ID', 'Kec. Panca Lautang', '2019-06-10 04:42:07'),
('191502', 'ID', 'Kec. Tellulimpoe', '2019-06-10 04:42:07'),
('191503', 'ID', 'Kec. Watangpulu', '2019-06-10 04:42:07'),
('191504', 'ID', 'Kec. Baranti', '2019-06-10 04:42:07'),
('191505', 'ID', 'Kec. Panca Rijang', '2019-06-10 04:42:07'),
('191506', 'ID', 'Kec. Maritengae', '2019-06-10 04:42:07'),
('191507', 'ID', 'Kec. Pitu Riawa', '2019-06-10 04:42:07'),
('191508', 'ID', 'Kec. Duapitue', '2019-06-10 04:42:07'),
('191509', 'ID', 'Kec. Kulo', '2019-06-10 04:42:07'),
('191510', 'ID', 'Kec. Pitu Riase', '2019-06-10 04:42:07'),
('191511', 'ID', 'Kec. Watang Sidenreng', '2019-06-10 04:42:07'),
('191600', 'ID', 'Kab. Enrekang', '2019-06-10 04:42:07'),
('191601', 'ID', 'Kec. Maiwa', '2019-06-10 04:42:07'),
('191602', 'ID', 'Kec. Enrekang', '2019-06-10 04:42:07'),
('191603', 'ID', 'Kec. Barakka', '2019-06-10 04:42:07'),
('191604', 'ID', 'Kec. Anggeraja', '2019-06-10 04:42:07'),
('191605', 'ID', 'Kec. Alla', '2019-06-10 04:42:07'),
('191606', 'ID', 'Kec. Bungin', '2019-06-10 04:42:07'),
('191607', 'ID', 'Kec. Cendana', '2019-06-10 04:42:07'),
('191608', 'ID', 'Kec. Curio', '2019-06-10 04:42:07'),
('191609', 'ID', 'Kec. Malua', '2019-06-10 04:42:07'),
('191610', 'ID', 'Kec. Buntu Batu', '2019-06-10 04:42:07'),
('191611', 'ID', 'Kec. Masale', '2019-06-10 04:42:07'),
('191612', 'ID', 'Kec. Baroko', '2019-06-10 04:42:07'),
('191700', 'ID', 'Kab. Luwu', '2019-06-10 04:42:07'),
('191701', 'ID', 'Kec. Larompong', '2019-06-10 04:42:07'),
('191702', 'ID', 'Kec. Suli', '2019-06-10 04:42:07'),
('191703', 'ID', 'Kec. Belopa', '2019-06-10 04:42:07'),
('191704', 'ID', 'Kec. Bajo', '2019-06-10 04:42:07'),
('191705', 'ID', 'Kec. Bassesangtempe', '2019-06-10 04:42:07'),
('191706', 'ID', 'Kec. Buaponrang', '2019-06-10 04:42:07'),
('191707', 'ID', 'Kec. Bua', '2019-06-10 04:42:07'),
('191708', 'ID', 'Kec. Walenrang', '2019-06-10 04:42:07'),
('191709', 'ID', 'Kec. Lamasi', '2019-06-10 04:42:07'),
('191710', 'ID', 'Kec. Latimojong', '2019-06-10 04:42:07'),
('191711', 'ID', 'Kec. Larompong Selatan', '2019-06-10 04:42:07'),
('191712', 'ID', 'Kec. Kamanre', '2019-06-10 04:42:07'),
('191713', 'ID', 'Kec. Walenrang Barat', '2019-06-10 04:42:07'),
('191714', 'ID', 'Kec. Walenrang Utara', '2019-06-10 04:42:07'),
('191715', 'ID', 'Kec. Walenrang Timur', '2019-06-10 04:42:07'),
('191716', 'ID', 'Kec. Lamasi Timur', '2019-06-10 04:42:07'),
('191717', 'ID', 'Kec. Suli Barat', '2019-06-10 04:42:07'),
('191718', 'ID', 'Kec. Bajo Barat', '2019-06-10 04:42:07'),
('191719', 'ID', 'Kec. Ponrang Selatan', '2019-06-10 04:42:07'),
('191720', 'ID', 'Kec. Ponrang', '2019-06-10 04:42:07'),
('191721', 'ID', 'Kec. Bolopa Utara', '2019-06-10 04:42:07'),
('191722', 'ID', 'Kec. Bassesangtempe Utara', '2020-02-10 03:07:44'),
('191800', 'ID', 'Kab. Tana Toraja', '2019-06-10 04:42:07'),
('191801', 'ID', 'Kec. Bonggakaradeng', '2019-06-10 04:42:07'),
('191802', 'ID', 'Kec. Mengkendek', '2019-06-10 04:42:07'),
('191803', 'ID', 'Kec. Sangalla', '2019-06-10 04:42:07'),
('191804', 'ID', 'Kec. Makale', '2019-06-10 04:42:07'),
('191805', 'ID', 'Kec. Saluputti', '2019-06-10 04:42:07'),
('191810', 'ID', 'Kec. Simbuang', '2019-06-10 04:42:07'),
('191811', 'ID', 'Kec. Rantetayo', '2019-06-10 04:42:07'),
('191812', 'ID', 'Kec. Bittuang', '2019-06-10 04:42:07'),
('191813', 'ID', 'Kec. Ranomeeto', '2019-06-10 04:42:07'),
('191814', 'ID', 'Kec. Mappak', '2019-06-10 04:42:07'),
('191815', 'ID', 'Kec. Gadang Batu Silanan', '2019-06-10 04:42:07'),
('191816', 'ID', 'Kec. Sangala Selatan', '2019-06-10 04:42:07'),
('191817', 'ID', 'Kec. Sangala Utara', '2019-06-10 04:42:07'),
('191818', 'ID', 'Kec. Makale Selatan', '2019-06-10 04:42:07'),
('191819', 'ID', 'Kec. Makale Utara', '2019-06-10 04:42:07'),
('191820', 'ID', 'Kec. Rembon', '2019-06-10 04:42:07'),
('191821', 'ID', 'Kec. Masanda', '2019-06-10 04:42:07'),
('191822', 'ID', 'Kec. Malimbong Balepe', '2019-06-10 04:42:07'),
('191823', 'ID', 'Kec. Kurra', '2019-06-10 04:42:07'),
('192400', 'ID', 'Kab. Luwu Utara', '2019-06-10 04:42:07'),
('192401', 'ID', 'Kec. Sabbang', '2019-06-10 04:42:07'),
('192402', 'ID', 'Kec. Baebunta', '2019-06-10 04:42:07'),
('192403', 'ID', 'Kec. Malangke', '2019-06-10 04:42:07'),
('192404', 'ID', 'Kec. Sukamaju', '2019-06-10 04:42:07'),
('192405', 'ID', 'Kec. Bone-Bone', '2019-06-10 04:42:07'),
('192412', 'ID', 'Kec. Massamba', '2019-06-10 04:42:07'),
('192413', 'ID', 'Kec. Limbong', '2019-06-10 04:42:07'),
('192415', 'ID', 'Kec. Mappedeceng', '2019-06-10 04:42:07'),
('192416', 'ID', 'Kec. Sekko', '2019-06-10 04:42:07'),
('192417', 'ID', 'Kec. Rampi', '2019-06-10 04:42:07'),
('192418', 'ID', 'Kec. Malangke Barat', '2019-06-10 04:42:07'),
('192490', 'ID', 'Tana Lili', '2019-06-10 04:42:07'),
('192600', 'ID', 'Kab. Luwu Timur', '2019-06-10 04:42:07'),
('192601', 'ID', 'Kec. Burau', '2019-06-10 04:42:07'),
('192602', 'ID', 'Kec. Tomoni', '2019-06-10 04:42:07'),
('192603', 'ID', 'Kec. Wotu', '2019-06-10 04:42:07'),
('192604', 'ID', 'Kec. Malili', '2019-06-10 04:42:07'),
('192605', 'ID', 'Kec. Nuha', '2019-06-10 04:42:07'),
('192606', 'ID', 'Kec. Mangkutana', '2019-06-10 04:42:07'),
('192607', 'ID', 'Kec. Towuti', '2019-06-10 04:42:07'),
('192608', 'ID', 'Kec. Angkona', '2019-06-10 04:42:07'),
('192609', 'ID', 'Kec. Tomoni Timur', '2019-06-10 04:42:07'),
('192610', 'ID', 'Kec. Kalaena', '2019-06-10 04:42:07'),
('192611', 'ID', 'Kec. Wasuponda', '2019-06-10 04:42:07'),
('192700', 'ID', 'Kab. Toraja Utara', '2019-06-10 04:42:07'),
('192701', 'ID', 'Kec. Awan Rante karua', '2019-06-10 04:42:07'),
('192702', 'ID', 'Kec. Balusu', '2019-06-10 04:42:07'),
('192703', 'ID', 'Kec. Bengkelekila', '2019-06-10 04:42:07'),
('192704', 'ID', 'Kec. Baruppu', '2019-06-10 04:42:07'),
('192705', 'ID', 'Kec. Buntao', '2019-06-10 04:42:07'),
('192706', 'ID', 'Kec. Kapala Pitu', '2019-06-10 04:42:07'),
('192707', 'ID', 'Kec. Kesu', '2019-06-10 04:42:07'),
('192708', 'ID', 'Kec. Nanggala', '2019-06-10 04:42:07'),
('192709', 'ID', 'Kec. Rantebua', '2019-06-10 04:42:07'),
('192710', 'ID', 'Kec. Rantepao', '2019-06-10 04:42:07'),
('192711', 'ID', 'Kec. Rindingallo', '2019-06-10 04:42:07'),
('192712', 'ID', 'Kec. Sa`dan', '2019-06-10 04:42:07'),
('192713', 'ID', 'Kec. Sanggalangi', '2019-06-10 04:42:07'),
('192714', 'ID', 'Kec. Sesean', '2019-06-10 04:42:07'),
('192715', 'ID', 'Kec. Sesean Suloara', '2019-06-10 04:42:07'),
('192716', 'ID', 'Kec. Sopai', '2019-06-10 04:42:07'),
('192717', 'ID', 'Kec. Tallunglipu', '2019-06-10 04:42:07'),
('192718', 'ID', 'Kec. Tikala', '2019-06-10 04:42:07'),
('192719', 'ID', 'Kec. Tondon', '2019-06-10 04:42:07'),
('192720', 'ID', 'Kec. Dende` Piongan Napo', '2019-06-10 04:42:07'),
('192721', 'ID', 'Kec. Buntu Pepasan', '2019-06-10 04:42:07'),
('196000', 'ID', 'Kota Makassar', '2019-06-10 04:42:07'),
('196001', 'ID', 'Kec. Mariso', '2019-06-10 04:42:07'),
('196002', 'ID', 'Kec. Mamajang', '2019-06-10 04:42:07'),
('196003', 'ID', 'Kec. Tamalate', '2019-06-10 04:42:07'),
('196004', 'ID', 'Kec. Makasar', '2019-06-10 04:42:07'),
('196005', 'ID', 'Kec. Ujung Pandang', '2019-06-10 04:42:07'),
('196006', 'ID', 'Kec. Wajo', '2019-06-10 04:42:07'),
('196007', 'ID', 'Kec. Bontoala', '2019-06-10 04:42:07'),
('196008', 'ID', 'Kec. Ujung Tanah', '2019-06-10 04:42:07'),
('196009', 'ID', 'Kec. Tallo', '2019-06-10 04:42:07'),
('196010', 'ID', 'Kec. Panakukkang', '2019-06-10 04:42:07'),
('196011', 'ID', 'Kec. Biringkanaya', '2019-06-10 04:42:07'),
('196012', 'ID', 'Kec. Tamalanrea', '2019-06-10 04:42:07'),
('196013', 'ID', 'Kec. Rappocini', '2019-06-10 04:42:07'),
('196014', 'ID', 'Kec. Manggala', '2019-06-10 04:42:07'),
('196100', 'ID', 'Kota Pare-Pare', '2019-06-10 04:42:07'),
('196101', 'ID', 'Kec. Bacukiki', '2019-06-10 04:42:07'),
('196102', 'ID', 'Kec. Ujung', '2019-06-10 04:42:07'),
('196103', 'ID', 'Kec. Soreang', '2019-06-10 04:42:07'),
('196104', 'ID', 'Kec. Bacukiki Barat', '2019-06-10 04:42:07'),
('196200', 'ID', 'Kota Palopo', '2019-06-10 04:42:07'),
('196201', 'ID', 'Kec. Wara', '2019-06-10 04:42:07'),
('196202', 'ID', 'Kec. Wara Utara', '2019-06-10 04:42:07'),
('196203', 'ID', 'Kec. Wara Selatan', '2019-06-10 04:42:07'),
('196204', 'ID', 'Kec. Telluwanua', '2019-06-10 04:42:07'),
('196205', 'ID', 'Kec. Wara Timur', '2019-06-10 04:42:07'),
('196206', 'ID', 'Kec. Wara Barat', '2019-06-10 04:42:07'),
('196207', 'ID', 'Kec. Sendana', '2019-06-10 04:42:07'),
('196208', 'ID', 'Kec. Mungkajang', '2019-06-10 04:42:07'),
('196209', 'ID', 'Kec. Bara', '2019-06-10 04:42:07'),
('200000', 'ID', 'Prop. Sulawesi Tenggara', '2019-06-10 04:42:07'),
('200100', 'ID', 'Kab. Konawe', '2019-06-10 04:42:07'),
('200108', 'ID', 'Kec. Soropia', '2019-06-10 04:42:07'),
('200109', 'ID', 'Kec. Sampara', '2019-06-10 04:42:07'),
('200112', 'ID', 'Kec. Lambuya', '2019-06-10 04:42:07'),
('200114', 'ID', 'Kec. Pondidaha', '2019-06-10 04:42:07'),
('200115', 'ID', 'Kec. Wawotobi', '2019-06-10 04:42:07'),
('200116', 'ID', 'Kec. Unaaha', '2019-06-10 04:42:07'),
('200118', 'ID', 'Kec. Abuki', '2019-06-10 04:42:07'),
('200124', 'ID', 'Kec. BondoAla', '2019-06-10 04:42:07'),
('200125', 'ID', 'Kec. Uepai', '2019-06-10 04:42:07'),
('200126', 'ID', 'Kec. Wonggeduku', '2019-06-10 04:42:07'),
('200128', 'ID', 'Kec. Tongauna', '2019-06-10 04:42:07'),
('200130', 'ID', 'Kec. Latoma', '2019-06-10 04:42:07'),
('200131', 'ID', 'Kec. Besulutu', '2019-06-10 04:42:07'),
('200132', 'ID', 'Kec. Puriala', '2019-06-10 04:42:07'),
('200133', 'ID', 'Kec. Meluhu', '2019-06-10 04:42:07'),
('200134', 'ID', 'Kec. Amonggedo', '2019-06-10 04:42:07'),
('200135', 'ID', 'Kec. Wawonii Barat', '2019-06-10 04:42:07'),
('200136', 'ID', 'Kec. Wawonii Timur', '2019-06-10 04:42:07'),
('200137', 'ID', 'Kec. Wawonii Selatan', '2019-06-10 04:42:07'),
('200138', 'ID', 'Kec. Wawonii Utara', '2019-06-10 04:42:07'),
('200139', 'ID', 'Kec. Routa', '2019-06-10 04:42:07'),
('200140', 'ID', 'Kec. Anggaberi', '2019-06-10 04:42:07'),
('200141', 'ID', 'Kec. Wawoni Tengah', '2019-06-10 04:42:07'),
('200142', 'ID', 'Kec. Kapoiala', '2019-06-10 04:42:07'),
('200143', 'ID', 'Kec. Konawe', '2019-06-10 04:42:07'),
('200144', 'ID', 'Asinua', '2019-06-10 04:42:07'),
('200145', 'ID', 'Wawonii Tenggara', '2019-06-10 04:42:07'),
('200146', 'ID', 'Wawonii Timur Laut', '2019-06-10 04:42:07'),
('200147', 'ID', 'Lalonggasumeeto', '2019-06-10 04:42:07'),
('200148', 'ID', 'Onembute', '2019-06-10 04:42:07'),
('200149', 'ID', 'Kec. Anggalomoare', '2020-02-10 03:07:44'),
('200150', 'ID', 'Kec. Morosi', '2020-02-10 03:07:44'),
('200151', 'ID', 'Kec. Wonggeduku Barat', '2020-02-10 03:07:44'),
('200152', 'ID', 'Kec. Padangguni', '2020-02-10 03:07:44'),
('200153', 'ID', 'Kec. Tongauna Utara', '2020-02-10 03:07:44'),
('200154', 'ID', 'Kec. Anggotoa', '2020-02-10 03:07:44'),
('200200', 'ID', 'Kab. Muna', '2019-06-10 04:42:07'),
('200201', 'ID', 'Kec. Tongkuno', '2019-06-10 04:42:07'),
('200202', 'ID', 'Kec. Parigi', '2019-06-10 04:42:07'),
('200203', 'ID', 'Kec. Kabawo', '2019-06-10 04:42:07'),
('200205', 'ID', 'Kec. Lawa', '2019-06-10 04:42:07'),
('200206', 'ID', 'Kec. Kusambi', '2019-06-10 04:42:07'),
('200207', 'ID', 'Kec. Katobu', '2019-06-10 04:42:07'),
('200208', 'ID', 'Kec. Napabalano', '2019-06-10 04:42:07'),
('200209', 'ID', 'Kec. Wakorumba Selatan', '2019-06-10 04:42:07'),
('200215', 'ID', 'Kec. Lohia', '2019-06-10 04:42:07'),
('200216', 'ID', 'Kec. Kontunaga', '2019-06-10 04:42:07'),
('200217', 'ID', 'Kec. Sawerigadi', '2019-06-10 04:42:07'),
('200218', 'ID', 'Kec. Maginti', '2019-06-10 04:42:07'),
('200219', 'ID', 'Kec. Kabangka', '2019-06-10 04:42:07'),
('200220', 'ID', 'Kec. Maligano', '2019-06-10 04:42:07'),
('200224', 'ID', 'Kec. Tiworo Tengah', '2019-06-10 04:42:07'),
('200225', 'ID', 'Kec. Barangka', '2019-06-10 04:42:07'),
('200226', 'ID', 'Kec. Watopute', '2019-06-10 04:42:07'),
('200227', 'ID', 'Kec. Batalaiworu', '2019-06-10 04:42:07'),
('200228', 'ID', 'Kec. Duruka', '2019-06-10 04:42:07'),
('200229', 'ID', 'Kec. Lasalepa', '2019-06-10 04:42:07'),
('200230', 'ID', 'Kec. Pasir Putih', '2019-06-10 04:42:07'),
('200232', 'ID', 'Kec. Tiworo Kepulauan', '2019-06-10 04:42:07'),
('200233', 'ID', 'Kec. Bone', '2019-06-10 04:42:07'),
('200234', 'ID', 'Kontu Kowuna', '2019-06-10 04:42:07'),
('200235', 'ID', 'Marobo', '2019-06-10 04:42:07'),
('200236', 'ID', 'Tongkuno Selatan', '2019-06-10 04:42:07'),
('200237', 'ID', 'Pasi Kolaga', '2019-06-10 04:42:07'),
('200238', 'ID', 'Batukara', '2019-06-10 04:42:07'),
('200239', 'ID', 'Wa Daga', '2019-06-10 04:42:07'),
('200240', 'ID', 'Napano Kusambi', '2019-06-10 04:42:07'),
('200241', 'ID', 'Towea', '2019-06-10 04:42:07'),
('200242', 'ID', 'Tiworo Selatan', '2019-06-10 04:42:07'),
('200243', 'ID', 'Tiworo Utara', '2019-06-10 04:42:07'),
('200244', 'ID', 'Kec. Kusambi', '2020-02-10 03:07:44'),
('200300', 'ID', 'Kab. Buton', '2019-06-10 04:42:07'),
('200305', 'ID', 'Kec. Lasalimu', '2019-06-10 04:42:07'),
('200306', 'ID', 'Kec. Pasar Wajo', '2019-06-10 04:42:07'),
('200307', 'ID', 'Kec. Sampolawa', '2019-06-10 04:42:07'),
('200308', 'ID', 'Kec. Batauga', '2019-06-10 04:42:07'),
('200311', 'ID', 'Kec. Kapontori', '2019-06-10 04:42:07'),
('200312', 'ID', 'Kec. Gu', '2019-06-10 04:42:07'),
('200313', 'ID', 'Kec. Lakudo', '2019-06-10 04:42:07'),
('200314', 'ID', 'Kec. Mawasangka', '2019-06-10 04:42:07'),
('200321', 'ID', 'Kec. Lasalimu Selatan', '2019-06-10 04:42:07'),
('200322', 'ID', 'Kec. Batu Atas', '2019-06-10 04:42:07'),
('200323', 'ID', 'Kec. Siompu', '2019-06-10 04:42:07'),
('200324', 'ID', 'Kec. Kadatua', '2019-06-10 04:42:07'),
('200325', 'ID', 'Kec. Mawasangka Timur', '2019-06-10 04:42:07'),
('200326', 'ID', 'Kec. Talaga Raya', '2019-06-10 04:42:07'),
('200328', 'ID', 'Kec. Mawasangka Tengah', '2019-06-10 04:42:07'),
('200329', 'ID', 'Kec. Sangia Wambulu', '2019-06-10 04:42:07'),
('200330', 'ID', 'Kec. Siontapia', '2019-06-10 04:42:07'),
('200331', 'ID', 'Kec. Wolowa', '2019-06-10 04:42:07'),
('200332', 'ID', 'Kec. Wabula', '2019-06-10 04:42:07'),
('200333', 'ID', 'Kec. Lapandewa', '2019-06-10 04:42:07'),
('200334', 'ID', 'Kec. Siompu Barat', '2019-06-10 04:42:07'),
('200400', 'ID', 'Kab. Kolaka', '2019-06-10 04:42:07'),
('200401', 'ID', 'Kec. Watubangga', '2019-06-10 04:42:07'),
('200402', 'ID', 'Kec. Pomalaa', '2019-06-10 04:42:07'),
('200403', 'ID', 'Kec. Wundulako', '2019-06-10 04:42:07'),
('200404', 'ID', 'Kec. Ladongi', '2019-06-10 04:42:07'),
('200405', 'ID', 'Kec. Tirawuta', '2019-06-10 04:42:07'),
('200406', 'ID', 'Kec. Kolaka', '2019-06-10 04:42:07'),
('200407', 'ID', 'Kec. Wolo', '2019-06-10 04:42:07'),
('200408', 'ID', 'Kec. Mowewe', '2019-06-10 04:42:07'),
('200412', 'ID', 'Kec. Tanggetada', '2019-06-10 04:42:07'),
('200413', 'ID', 'Kec. Baula', '2019-06-10 04:42:07'),
('200414', 'ID', 'Kec. Lambandia', '2019-06-10 04:42:07'),
('200415', 'ID', 'Kec. Latambaga', '2019-06-10 04:42:07'),
('200416', 'ID', 'Kec. Samaturu', '2019-06-10 04:42:07'),
('200417', 'ID', 'Kec. Uluiwoi', '2019-06-10 04:42:07'),
('200418', 'ID', 'Kec. Tinondo', '2019-06-10 04:42:07'),
('200419', 'ID', 'Kec. Poli-Polia', '2019-06-10 04:42:07'),
('200420', 'ID', 'Kec. Lalolae', '2019-06-10 04:42:07'),
('200421', 'ID', 'Kec. Toari', '2019-06-10 04:42:07'),
('200422', 'ID', 'Kec. Polinggona', '2019-06-10 04:42:07'),
('200423', 'ID', 'Kec. Loela', '2019-06-10 04:42:07'),
('200424', 'ID', 'Tirawuta', '2019-06-10 04:42:07'),
('200425', 'ID', 'Ladongi', '2019-06-10 04:42:07'),
('200426', 'ID', 'Poli-polia', '2019-06-10 04:42:07'),
('200427', 'ID', 'Lambandia', '2019-06-10 04:42:07'),
('200428', 'ID', 'Uluiwoi', '2019-06-10 04:42:07'),
('200429', 'ID', 'Mowewe', '2019-06-10 04:42:07'),
('200430', 'ID', 'Kec. Iwoimendaa', '2020-02-10 03:07:44'),
('200500', 'ID', 'Kab. Konawe Selatan', '2019-06-10 04:42:07'),
('200501', 'ID', 'Kec. Tinanggea', '2019-06-10 04:42:07'),
('200502', 'ID', 'Kec. Palangga', '2019-06-10 04:42:07'),
('200503', 'ID', 'Kec. Konda', '2019-06-10 04:42:07'),
('200504', 'ID', 'Kec. Lainea', '2019-06-10 04:42:07'),
('200505', 'ID', 'Kec. Moramo', '2019-06-10 04:42:07'),
('200506', 'ID', 'Kec. Ranomeeto', '2019-06-10 04:42:07'),
('200507', 'ID', 'Kec. Landono', '2019-06-10 04:42:07'),
('200508', 'ID', 'Kec. Kolono', '2019-06-10 04:42:07'),
('200509', 'ID', 'Kec. Andolo', '2019-06-10 04:42:07'),
('200510', 'ID', 'Kec. Laonti', '2019-06-10 04:42:07'),
('200511', 'ID', 'Kec. Angata', '2019-06-10 04:42:07'),
('200512', 'ID', 'Kec. Lalembuu', '2019-06-10 04:42:07'),
('200513', 'ID', 'Kec. Buke', '2019-06-10 04:42:07'),
('200514', 'ID', 'Kec. Palangga Selatan', '2019-06-10 04:42:07'),
('200515', 'ID', 'Kec. Baito', '2019-06-10 04:42:07'),
('200516', 'ID', 'Kec. Laeya', '2019-06-10 04:42:07'),
('200517', 'ID', 'Kec. Moramo Utara', '2019-06-10 04:42:07'),
('200518', 'ID', 'Kec. Wolasi', '2019-06-10 04:42:07'),
('200519', 'ID', 'Kec. Ranomeeto Barat', '2019-06-10 04:42:07'),
('200520', 'ID', 'Kec. Mowila', '2019-06-10 04:42:07'),
('200521', 'ID', 'Kec. Benua', '2019-06-10 04:42:07'),
('200522', 'ID', 'Kec. Basala', '2019-06-10 04:42:07'),
('200600', 'ID', 'Kab. Wakatobi', '2019-06-10 04:42:07'),
('200601', 'ID', 'Kec. Binongko', '2019-06-10 04:42:07'),
('200602', 'ID', 'Kec. Tomia', '2019-06-10 04:42:07'),
('200603', 'ID', 'Kec. Kaledupa', '2019-06-10 04:42:07'),
('200604', 'ID', 'Kec. Wangi-Wangi', '2019-06-10 04:42:07'),
('200605', 'ID', 'Kec. Wangi-Wangi Selatan', '2019-06-10 04:42:07'),
('200606', 'ID', 'Kec. Kaledupa Selatan', '2019-06-10 04:42:07'),
('200607', 'ID', 'Kec. Tomia Timur', '2019-06-10 04:42:07'),
('200608', 'ID', 'Kec. Togo Binongko', '2019-06-10 04:42:07'),
('200700', 'ID', 'Kab. Bombana', '2019-06-10 04:42:07'),
('200701', 'ID', 'Kec. Kabaena', '2019-06-10 04:42:07'),
('200702', 'ID', 'Kec. Kabaena Timur', '2019-06-10 04:42:07'),
('200703', 'ID', 'Kec. Poleang', '2019-06-10 04:42:07'),
('200704', 'ID', 'Kec. Poleang Timur', '2019-06-10 04:42:07'),
('200705', 'ID', 'Kec. Rumbia', '2019-06-10 04:42:07'),
('200706', 'ID', 'Kec. Rarowatu', '2019-06-10 04:42:07'),
('200707', 'ID', 'Kec. Poleang Barat', '2019-06-10 04:42:07'),
('200708', 'ID', 'Kec. Mataelo', '2019-06-10 04:42:07'),
('200709', 'ID', 'Kec. Rarowatu Utara', '2019-06-10 04:42:07'),
('200710', 'ID', 'Kec. Poleang Utara', '2019-06-10 04:42:07'),
('200711', 'ID', 'Kec. Poleang Selatan', '2019-06-10 04:42:07'),
('200712', 'ID', 'Kec. Poleang Tenggara', '2019-06-10 04:42:07'),
('200713', 'ID', 'Kec. Kabaena Selatan', '2019-06-10 04:42:07'),
('200714', 'ID', 'Kec. Kabaena Barat', '2019-06-10 04:42:07'),
('200715', 'ID', 'Kec. Kabaena Utara', '2019-06-10 04:42:07'),
('200716', 'ID', 'Kec. Kabaena Tengah', '2019-06-10 04:42:07'),
('200717', 'ID', 'Kec. Kep. Masaloka Raya', '2019-06-10 04:42:07'),
('200718', 'ID', 'Kec. Rumbia Tengah', '2019-06-10 04:42:07'),
('200719', 'ID', 'Kec. Poleang Tengah', '2019-06-10 04:42:07'),
('200720', 'ID', 'Kec. Tatonuwu', '2019-06-10 04:42:07'),
('200721', 'ID', 'Kec. Lantari Jaya', '2019-06-10 04:42:07'),
('200722', 'ID', 'Kec. Mata Usu', '2019-06-10 04:42:07'),
('200800', 'ID', 'Kab. Kolaka Utara', '2019-06-10 04:42:07'),
('200801', 'ID', 'Kec. Lasusua', '2019-06-10 04:42:07'),
('200802', 'ID', 'Kec. Pakue', '2019-06-10 04:42:07'),
('200803', 'ID', 'Kec. Batu Putih', '2019-06-10 04:42:07'),
('200804', 'ID', 'Kec. Ranteangin', '2019-06-10 04:42:07'),
('200805', 'ID', 'Kec. Kodeoha', '2019-06-10 04:42:07'),
('200806', 'ID', 'Kec. Ngapa', '2019-06-10 04:42:07'),
('200807', 'ID', 'Kec. Wawo', '2019-06-10 04:42:07'),
('200808', 'ID', 'Kec. Lambai', '2019-06-10 04:42:07'),
('200809', 'ID', 'Kec. Watunohu', '2019-06-10 04:42:07'),
('200810', 'ID', 'Kec. Pakue Tengah', '2019-06-10 04:42:07'),
('200811', 'ID', 'Kec. Pakue Utara', '2019-06-10 04:42:07'),
('200812', 'ID', 'Kec. Porehu', '2019-06-10 04:42:07'),
('200813', 'ID', 'Kec. Katoi', '2019-06-10 04:42:07'),
('200814', 'ID', 'Kec. Tiwu', '2019-06-10 04:42:07'),
('200815', 'ID', 'Kec. Katoi', '2019-06-10 04:42:07'),
('200890', 'ID', 'Tolala', '2019-06-10 04:42:07'),
('200900', 'ID', 'Kab. Konawe Utara', '2019-06-10 04:42:07'),
('200901', 'ID', 'Kec. Langkima', '2019-06-10 04:42:07'),
('200902', 'ID', 'Kec. Molawe', '2019-06-10 04:42:07'),
('200903', 'ID', 'Kec. Lembo', '2019-06-10 04:42:07'),
('200904', 'ID', 'Kec. Asera', '2019-06-10 04:42:07'),
('200905', 'ID', 'Kec. Wiwirano', '2019-06-10 04:42:07'),
('200906', 'ID', 'Kec. Lasolo', '2019-06-10 04:42:07'),
('200907', 'ID', 'Kec. Sawa', '2019-06-10 04:42:07'),
('200908', 'ID', 'Kec. Oheo', '2019-06-10 04:42:07'),
('200909', 'ID', 'Kec. Andowia', '2019-06-10 04:42:07'),
('200910', 'ID', 'Kec. Motui', '2019-06-10 04:42:07'),
('200911', 'ID', 'Kec. Landawe', '2020-02-10 03:07:44'),
('200912', 'ID', 'Kec. Lasolo Kepulauan', '2020-02-10 03:07:44'),
('200913', 'ID', 'Kec. Wawolesea', '2020-02-10 03:07:44'),
('201000', 'ID', 'Kab. Buton Utara', '2019-06-10 04:42:07'),
('201001', 'ID', 'Kec. Kulisusu', '2019-06-10 04:42:07'),
('201002', 'ID', 'Kec. Kulisusu Barat', '2019-06-10 04:42:07'),
('201003', 'ID', 'Kec. Kulisusu Utara', '2019-06-10 04:42:07'),
('201004', 'ID', 'Kec. Kambowa', '2019-06-10 04:42:07'),
('201005', 'ID', 'Kec. Bonenugu', '2019-06-10 04:42:07'),
('201006', 'ID', 'Kec. Wakorumba Utara', '2019-06-10 04:42:07'),
('201099', 'ID', 'Wakorumba Utara', '2019-06-10 04:42:07'),
('201100', 'ID', 'Kab. Kolaka Timur', '2020-02-10 03:07:44'),
('201111', 'ID', 'Loea', '2019-06-10 04:42:07'),
('201112', 'ID', 'Kec. Ladongi', '2020-02-10 03:07:44'),
('201113', 'ID', 'Kec. Poli-Polia', '2020-02-10 03:07:44'),
('201114', 'ID', 'Kec. Lambandia', '2020-02-10 03:07:44'),
('201115', 'ID', 'Lalolae', '2019-06-10 04:42:07'),
('201116', 'ID', 'Kec. Mowewe', '2020-02-10 03:07:44'),
('201117', 'ID', 'Kec. Uluiwoi', '2020-02-10 03:07:44'),
('201118', 'ID', 'Kec. Tinondo', '2020-02-10 03:07:44'),
('201119', 'ID', 'Kec. Tirawuta', '2020-02-10 03:07:44'),
('201120', 'ID', 'Kec. Ueesi', '2020-02-10 03:07:44'),
('201121', 'ID', 'Kec. Aere', '2020-02-10 03:07:44'),
('201122', 'ID', 'Kec. Dangia', '2020-02-10 03:07:44'),
('201200', 'ID', 'Kab. Konawe Kepulauan', '2020-02-10 03:07:44'),
('201201', 'ID', 'Kec. Wawonii Barat', '2020-02-10 03:07:44'),
('201202', 'ID', 'Kec. Wawonii Utara', '2020-02-10 03:07:44'),
('201203', 'ID', 'Kec. Wawonii Timur Laut', '2020-02-10 03:07:44'),
('201204', 'ID', 'Kec. Wawonii Timur', '2020-02-10 03:07:44'),
('201205', 'ID', 'Kec. Wawonii Tenggara', '2020-02-10 03:07:44'),
('201206', 'ID', 'Kec. Wawonii Selatan', '2020-02-10 03:07:44'),
('201207', 'ID', 'Kec. Wawonii Tengah', '2020-02-10 03:07:44'),
('201300', 'ID', 'Kab. Muna Barat', '2020-02-10 03:07:44'),
('201301', 'ID', 'Kec. Sawerigadi', '2020-02-10 03:07:44'),
('201302', 'ID', 'Kec. Barangka', '2020-02-10 03:07:44'),
('201303', 'ID', 'Kec. Lawa', '2020-02-10 03:07:44'),
('201304', 'ID', 'Kec. Wadaga', '2020-02-10 03:07:44'),
('201305', 'ID', 'Kec. Tiworo Selatan', '2020-02-10 03:07:44'),
('201306', 'ID', 'Kec. Maginti', '2020-02-10 03:07:44'),
('201307', 'ID', 'Kec. Tiworo Tengah', '2020-02-10 03:07:44'),
('201308', 'ID', 'Kec. Tiworo Tengah', '2020-02-10 03:07:44'),
('201309', 'ID', 'Kec. Tiworo Utara', '2020-02-10 03:07:44'),
('201310', 'ID', 'Kec. Tiworo Kepulauan', '2020-02-10 03:07:44'),
('201311', 'ID', 'Kec. Napano Kusambi', '2020-02-10 03:07:44'),
('201312', 'ID', 'Kec. Kusambi', '2020-02-10 03:07:44'),
('201400', 'ID', 'Kab. Buton Selatan', '2020-02-10 03:07:44'),
('201401', 'ID', 'Kec. Batauga', '2020-02-10 03:07:44'),
('201402', 'ID', 'Kec. Sampolawa', '2020-02-10 03:07:44'),
('201403', 'ID', 'Kec. Lapandewa', '2020-02-10 03:07:44'),
('201404', 'ID', 'Kec. Batu Atas', '2020-02-10 03:07:44'),
('201405', 'ID', 'Kec. Siompu Barat', '2020-02-10 03:07:44'),
('201406', 'ID', 'Kec. Siompu', '2020-02-10 03:07:44'),
('201407', 'ID', 'Kec. Kadatua', '2020-02-10 03:07:44'),
('201600', 'ID', 'Kab. Buton Tengah', '2020-02-10 03:07:44'),
('201601', 'ID', 'Kec. Lakudo', '2020-02-10 03:07:44'),
('201602', 'ID', 'Kec. Mawasangka Timur', '2020-02-10 03:07:44'),
('201603', 'ID', 'Kec. Mawasangka Tengah', '2020-02-10 03:07:44'),
('201604', 'ID', 'Kec. Mawasangka', '2020-02-10 03:07:44'),
('201605', 'ID', 'Kec.Talaga Raya', '2020-02-10 03:07:44'),
('201606', 'ID', 'Kec. Gu', '2020-02-10 03:07:44'),
('201607', 'ID', 'Kec. Sangia Wambulu', '2020-02-10 03:07:44'),
('206000', 'ID', 'Kota Kendari', '2019-06-10 04:42:07'),
('206001', 'ID', 'Kec. Mandonga', '2019-06-10 04:42:07'),
('206002', 'ID', 'Kec. Poasia', '2019-06-10 04:42:07'),
('206003', 'ID', 'Kec. Kendari', '2019-06-10 04:42:07'),
('206004', 'ID', 'Kec. Baruga', '2019-06-10 04:42:07'),
('206005', 'ID', 'Kec. Kendari Barat', '2019-06-10 04:42:07'),
('206006', 'ID', 'Kec. Abeli', '2019-06-10 04:42:07'),
('206007', 'ID', 'Kec. Puwato', '2019-06-10 04:42:07'),
('206008', 'ID', 'Kec. Kadia', '2019-06-10 04:42:07'),
('206009', 'ID', 'Kec. Wua-Wua', '2019-06-10 04:42:07'),
('206010', 'ID', 'Kec. Kambu', '2019-06-10 04:42:07'),
('206100', 'ID', 'Kota Baubau', '2019-06-10 04:42:07'),
('206101', 'ID', 'Kec. Betoambari', '2019-06-10 04:42:07'),
('206102', 'ID', 'Kec. Wolio', '2019-06-10 04:42:07'),
('206103', 'ID', 'Kec. Sorowalio', '2019-06-10 04:42:07'),
('206104', 'ID', 'Kec. Bungi', '2019-06-10 04:42:07'),
('206105', 'ID', 'Kec. Murhum', '2019-06-10 04:42:07'),
('206106', 'ID', 'Kec. Kokalukuna', '2019-06-10 04:42:07'),
('206107', 'ID', 'Kec. Lea-Lea', '2019-06-10 04:42:07'),
('206108', 'ID', 'Kec. Batupoaro', '2020-02-10 03:07:44'),
('210000', 'ID', 'Prop. Maluku', '2019-06-10 04:42:07'),
('210100', 'ID', 'Kab. Maluku Tengah', '2019-06-10 04:42:07'),
('210101', 'ID', 'Kec. Banda', '2019-06-10 04:42:07'),
('210104', 'ID', 'Kec. Tehoru', '2019-06-10 04:42:07'),
('210105', 'ID', 'Kec. Amahai', '2019-06-10 04:42:07'),
('210106', 'ID', 'Kec. Teon Nila Serua', '2019-06-10 04:42:07'),
('210108', 'ID', 'Kec. Saparua', '2019-06-10 04:42:07'),
('210109', 'ID', 'Kec. Pulau Haruku', '2019-06-10 04:42:07'),
('210110', 'ID', 'Kec. Salahutu', '2019-06-10 04:42:07'),
('210111', 'ID', 'Kec. Leihitu', '2019-06-10 04:42:07'),
('210114', 'ID', 'Kec. Seram Utara', '2019-06-10 04:42:07'),
('210116', 'ID', 'Kec. Masohi', '2019-06-10 04:42:07'),
('210117', 'ID', 'Kec. Nusa Laut', '2019-06-10 04:42:07'),
('210118', 'ID', 'Kec. Teluk Elpaputih', '2019-06-10 04:42:07'),
('210119', 'ID', 'Kec. Seram Utara Barat', '2019-06-10 04:42:07'),
('210120', 'ID', 'Kec. Leihitu Barat', '2019-06-10 04:42:07'),
('210121', 'ID', 'Telutih', '2019-06-10 04:42:07'),
('210122', 'ID', 'Seram Utara Timur Seti', '2019-06-10 04:42:07'),
('210123', 'ID', 'Seram Utara Timur Kobi', '2019-06-10 04:42:07'),
('210124', 'ID', 'Kec. Saparua Timur', '2020-02-10 03:07:44'),
('210200', 'ID', 'Kab. Maluku Tenggara', '2019-06-10 04:42:07'),
('210201', 'ID', 'Kec. Kei Kecil', '2019-06-10 04:42:07'),
('210202', 'ID', 'Kec. Kei Besar', '2019-06-10 04:42:07'),
('210205', 'ID', 'Kec. Kei Besar Selatan', '2019-06-10 04:42:07'),
('210206', 'ID', 'Kec. Kei Besar Utara Timur', '2019-06-10 04:42:07'),
('210212', 'ID', 'Kec. Kei Kecil Barat', '2019-06-10 04:42:07'),
('210213', 'ID', 'Kec. Kei Kecil Timur', '2019-06-10 04:42:07'),
('210214', 'ID', 'Kec. Manyeuw', '2020-02-10 03:07:44'),
('210215', 'ID', 'Kec. Hoat Sorbay', '2020-02-10 03:07:44'),
('210216', 'ID', 'Kec. Kei Besar Utara Barat', '2020-02-10 03:07:44'),
('210217', 'ID', 'Kec. Kei Besar Selatan Barat', '2020-02-10 03:07:44'),
('210218', 'ID', 'Kec. Kei Kecil Timur Selatan', '2020-02-10 03:07:44'),
('210300', 'ID', 'Kab. Buru', '2019-06-10 04:42:07'),
('210304', 'ID', 'Kec. Air Buaya', '2019-06-10 04:42:07'),
('210307', 'ID', 'Kec. Waeapo', '2019-06-10 04:42:07'),
('210310', 'ID', 'Kec. Namlea', '2019-06-10 04:42:07'),
('210316', 'ID', 'Kec. Waplau', '2019-06-10 04:42:07'),
('210317', 'ID', 'Kec. Bata Baul', '2019-06-10 04:42:07'),
('210318', 'ID', 'Kec. Lolong Guba', '2020-02-10 03:07:44'),
('210319', 'ID', 'Kec. Waelata', '2020-02-10 03:07:44'),
('210320', 'ID', 'Kec. Fena Leisela', '2020-02-10 03:07:44'),
('210321', 'ID', 'Kec. Teluk Kaiely', '2020-02-10 03:07:44'),
('210322', 'ID', 'Kec. Lilialy', '2020-02-10 03:07:44'),
('210400', 'ID', 'Kab. Maluku Tenggara Barat', '2019-06-10 04:42:07'),
('210401', 'ID', 'Kec. Pulau-pulau Terselatan', '2019-06-10 04:42:07'),
('210403', 'ID', 'Kec. Pulau-Pulau Babar', '2019-06-10 04:42:07'),
('210404', 'ID', 'Kec. Tanimbar Selatan', '2019-06-10 04:42:07'),
('210405', 'ID', 'Kec. Tanimbar Utara', '2019-06-10 04:42:07'),
('210406', 'ID', 'Kec. Damer', '2019-06-10 04:42:07'),
('210411', 'ID', 'Kec. Wer Tamrian', '2019-06-10 04:42:07'),
('210412', 'ID', 'Kec. Wer Maktian', '2019-06-10 04:42:07'),
('210413', 'ID', 'Kec. Selaru', '2019-06-10 04:42:07'),
('210414', 'ID', 'Kec. Yaru', '2019-06-10 04:42:07'),
('210415', 'ID', 'Kec. Wuar Labobar', '2019-06-10 04:42:07'),
('210416', 'ID', 'Kec. Nirun Mas', '2019-06-10 04:42:07'),
('210417', 'ID', 'Kec. Kormomolin', '2019-06-10 04:42:07'),
('210418', 'ID', 'Kec. Molu Maru', '2019-06-10 04:42:07'),
('210500', 'ID', 'Kab. Seram Bagian Barat', '2019-06-10 04:42:07'),
('210501', 'ID', 'Kec. Kairatu', '2019-06-10 04:42:07'),
('210502', 'ID', 'Kec. Seram Barat', '2019-06-10 04:42:07'),
('210503', 'ID', 'Kec. Taniwel', '2019-06-10 04:42:07'),
('210504', 'ID', 'Kec. Waisala', '2019-06-10 04:42:07'),
('210505', 'ID', 'Huamual', '2019-06-10 04:42:07'),
('210506', 'ID', 'Kec. Amalatu', '2020-02-10 03:07:44'),
('210590', 'ID', 'Inamosol', '2019-06-10 04:42:07'),
('210591', 'ID', 'Elpaputih', '2019-06-10 04:42:07'),
('210592', 'ID', 'Taniwel Timur', '2019-06-10 04:42:07'),
('210593', 'ID', 'Huamual Belakang', '2019-06-10 04:42:07'),
('210594', 'ID', 'Kepulauan Manipa', '2019-06-10 04:42:07'),
('210599', 'ID', 'Kairatu Barat', '2019-06-10 04:42:07'),
('210600', 'ID', 'Kab. Seram Bagian Timur', '2019-06-10 04:42:07'),
('210601', 'ID', 'Kec. Seram Timur', '2019-06-10 04:42:07'),
('210602', 'ID', 'Kec. Werinama', '2019-06-10 04:42:07'),
('210603', 'ID', 'Kec. Bula', '2019-06-10 04:42:07'),
('210604', 'ID', 'Kec. Pulau-pulau Gorom', '2019-06-10 04:42:07'),
('210605', 'ID', 'Kec. Wakate', '2019-06-10 04:42:07'),
('210606', 'ID', 'Kec. Tutuk Tolu', '2019-06-10 04:42:07'),
('210607', 'ID', 'Kec. Siwalalat', '2020-02-10 03:07:44'),
('210608', 'ID', 'Kec. Kilmury', '2020-02-10 03:07:44'),
('210609', 'ID', 'Kec. Pulau Panjang', '2020-02-10 03:07:44'),
('210610', 'ID', 'Kec. Teor', '2020-02-10 03:07:44'),
('210611', 'ID', 'Kec. Gorom Timur', '2020-02-10 03:07:44'),
('210612', 'ID', 'Kec. Bula Barat', '2020-02-10 03:07:44'),
('210613', 'ID', 'Kec. Kian Darat', '2020-02-10 03:07:44'),
('210614', 'ID', 'Kec. Siritaun Wida Timur', '2020-02-10 03:07:44'),
('210615', 'ID', 'Kec. Teluk Waru', '2020-02-10 03:07:44'),
('210616', 'ID', 'Kec. Lian Vitu', '2020-02-10 03:07:44'),
('210617', 'ID', 'Kec. Ukar Sengan', '2020-02-10 03:07:44'),
('210700', 'ID', 'Kab. Kepulauan Aru', '2019-06-10 04:42:07'),
('210701', 'ID', 'Kec. Pulau-Pulau Aru', '2019-06-10 04:42:07'),
('210702', 'ID', 'Kec. Aru Tengah', '2019-06-10 04:42:07'),
('210703', 'ID', 'Kec. Aru Selatan', '2019-06-10 04:42:07'),
('210704', 'ID', 'Kec. Aru Selatan Timur', '2019-06-10 04:42:07'),
('210705', 'ID', 'Kec. Aru Tengah Timur', '2019-06-10 04:42:07'),
('210706', 'ID', 'Kec. Aru Tengah Selatan', '2019-06-10 04:42:07'),
('210707', 'ID', 'Kec. Aru Utara', '2019-06-10 04:42:07'),
('210708', 'ID', 'Kec. Batuley', '2019-06-10 04:42:07'),
('210709', 'ID', 'Kec. Sir-Sir', '2019-06-10 04:42:07'),
('210710', 'ID', 'Kec. Aru Selatan Utara', '2019-06-10 04:42:07'),
('210800', 'ID', 'Kab. Maluku Barat Daya', '2019-06-10 04:42:07'),
('210801', 'ID', 'Kec. Babar Timur', '2019-06-10 04:42:07'),
('210803', 'ID', 'Kec. Mdona Hiera', '2019-06-10 04:42:07'),
('210804', 'ID', 'Kec. Moa Lakor', '2019-06-10 04:42:07'),
('210805', 'ID', 'Kec. Pulau Letti', '2019-06-10 04:42:07'),
('210808', 'ID', 'Kec. Wetar', '2019-06-10 04:42:07'),
('210809', 'ID', 'Damer', '2019-06-10 04:42:07'),
('210810', 'ID', 'Pulau-pulau Babar', '2019-06-10 04:42:07'),
('210811', 'ID', 'Pulau-pulau Terselatan', '2019-06-10 04:42:07'),
('210812', 'ID', 'Kec. Pulau Masela', '2020-02-10 03:07:44'),
('210813', 'ID', 'Kec. Dawelor Dawera', '2020-02-10 03:07:44'),
('210814', 'ID', 'Kec. Pulau Wetang', '2020-02-10 03:07:44'),
('210815', 'ID', 'Kec. Pulau Lakor', '2020-02-10 03:07:44'),
('210816', 'ID', 'Kec. Wetar Utara', '2020-02-10 03:07:44'),
('210817', 'ID', 'Kec. Wetar Barat', '2020-02-10 03:07:44'),
('210818', 'ID', 'Kec. Wetar Timur', '2020-02-10 03:07:44'),
('210819', 'ID', 'Kec. Kepulauan Romang', '2020-02-10 03:07:44'),
('210820', 'ID', 'Kec. Kisar Utara', '2020-02-10 03:07:44'),
('210900', 'ID', 'Kab. Buru Selatan', '2019-06-10 04:42:07'),
('210901', 'ID', 'Kec. Ambalau', '2019-06-10 04:42:07'),
('210902', 'ID', 'Kec. Kepala Madan', '2019-06-10 04:42:07'),
('210903', 'ID', 'Kec. Leksula', '2019-06-10 04:42:07'),
('210904', 'ID', 'Kec. Namrole', '2019-06-10 04:42:07'),
('210905', 'ID', 'Kec. Waesama', '2019-06-10 04:42:07'),
('210906', 'ID', 'Kec. Fena Fafan', '2020-02-10 03:07:44'),
('216000', 'ID', 'Kota Ambon', '2019-06-10 04:42:07'),
('216001', 'ID', 'Kec. Nusaniwe', '2019-06-10 04:42:07'),
('216002', 'ID', 'Kec. Sirimau', '2019-06-10 04:42:07'),
('216003', 'ID', 'Kec. Teluk Ambon', '2019-06-10 04:42:07'),
('216005', 'ID', 'Kec. Baguala', '2019-06-10 04:42:07'),
('216006', 'ID', 'Kec. Lei Timur Selatan', '2019-06-10 04:42:07'),
('216100', 'ID', 'Kota. Tual', '2019-06-10 04:42:07'),
('216101', 'ID', 'Kec. PP Kur Mangur', '2019-06-10 04:42:07'),
('216102', 'ID', 'Kec. Pulau Dullah Selatan', '2019-06-10 04:42:07'),
('216103', 'ID', 'Kec. Pulau Dullah Utara', '2019-06-10 04:42:07'),
('216104', 'ID', 'Kec. Tayando Tam', '2019-06-10 04:42:07'),
('216105', 'ID', 'Kec. Kur Selatan', '2020-02-10 03:07:44'),
('220000', 'ID', 'Prop. Bali', '2019-06-10 04:42:07'),
('220100', 'ID', 'Kab. Buleleng', '2019-06-10 04:42:07'),
('220101', 'ID', 'Kec. Gerokgak', '2019-06-10 04:42:07'),
('220102', 'ID', 'Kec. Seririt', '2019-06-10 04:42:07'),
('220103', 'ID', 'Kec. Busungbiu', '2019-06-10 04:42:07'),
('220104', 'ID', 'Kec. Banjar', '2019-06-10 04:42:07'),
('220105', 'ID', 'Kec. Sukasada', '2019-06-10 04:42:07'),
('220106', 'ID', 'Kec. Buleleng', '2019-06-10 04:42:07'),
('220107', 'ID', 'Kec. Sawan', '2019-06-10 04:42:07'),
('220108', 'ID', 'Kec. Kubutambahan', '2019-06-10 04:42:07'),
('220109', 'ID', 'Kec. Tejakula', '2019-06-10 04:42:07'),
('220200', 'ID', 'Kab. Jembrana', '2019-06-10 04:42:07'),
('220201', 'ID', 'Kec. Melaya', '2019-06-10 04:42:07'),
('220202', 'ID', 'Kec. Negara', '2019-06-10 04:42:07'),
('220203', 'ID', 'Kec. Mendoyo', '2019-06-10 04:42:07'),
('220204', 'ID', 'Kec. Pekutatan', '2019-06-10 04:42:07'),
('220205', 'ID', 'Kec. Jembrana', '2019-06-10 04:42:07'),
('220300', 'ID', 'Kab. Tabanan', '2019-06-10 04:42:07'),
('220301', 'ID', 'Kec. Selemadeg', '2019-06-10 04:42:07'),
('220302', 'ID', 'Kec. Kerambitan', '2019-06-10 04:42:07'),
('220303', 'ID', 'Kec. Tabanan', '2019-06-10 04:42:07'),
('220304', 'ID', 'Kec. Kediri', '2019-06-10 04:42:07'),
('220305', 'ID', 'Kec. Marga', '2019-06-10 04:42:07'),
('220306', 'ID', 'Kec. Baturiti', '2019-06-10 04:42:07'),
('220307', 'ID', 'Kec. Penebel', '2019-06-10 04:42:07'),
('220308', 'ID', 'Kec. Pupuan', '2019-06-10 04:42:07'),
('220309', 'ID', 'Kec. Selemadeg Barat', '2019-06-10 04:42:07'),
('220310', 'ID', 'Kec. Selemadeg Timur', '2019-06-10 04:42:07'),
('220400', 'ID', 'Kab. Badung', '2019-06-10 04:42:07'),
('220401', 'ID', 'Kec. Kuta Selatan', '2019-06-10 04:42:07'),
('220402', 'ID', 'Kec. Kuta', '2019-06-10 04:42:07'),
('220403', 'ID', 'Kec. Kuta Utara', '2019-06-10 04:42:07'),
('220404', 'ID', 'Kec. Mengwi', '2019-06-10 04:42:07'),
('220405', 'ID', 'Kec. Abiansemal', '2019-06-10 04:42:07'),
('220406', 'ID', 'Kec. Petang', '2019-06-10 04:42:07'),
('220500', 'ID', 'Kab. Gianyar', '2019-06-10 04:42:07'),
('220501', 'ID', 'Kec. Sukawati', '2019-06-10 04:42:07'),
('220502', 'ID', 'Kec. Blahbatuh', '2019-06-10 04:42:07'),
('220503', 'ID', 'Kec. Gianyar', '2019-06-10 04:42:07'),
('220504', 'ID', 'Kec. Tampak siring', '2019-06-10 04:42:07'),
('220505', 'ID', 'Kec. Ubud', '2019-06-10 04:42:07'),
('220506', 'ID', 'Kec. Tegallalang', '2019-06-10 04:42:07'),
('220507', 'ID', 'Kec. Payangan', '2019-06-10 04:42:07'),
('220600', 'ID', 'Kab. Klungkung', '2019-06-10 04:42:07'),
('220601', 'ID', 'Kec. Nusapenida', '2019-06-10 04:42:07'),
('220602', 'ID', 'Kec. Banjarangkan', '2019-06-10 04:42:07'),
('220603', 'ID', 'Kec. Klungkung', '2019-06-10 04:42:07'),
('220604', 'ID', 'Kec. Dawan', '2019-06-10 04:42:07'),
('220700', 'ID', 'Kab. Bangli', '2019-06-10 04:42:07'),
('220701', 'ID', 'Kec. Susut', '2019-06-10 04:42:07'),
('220702', 'ID', 'Kec. Bangli', '2019-06-10 04:42:07'),
('220703', 'ID', 'Kec. Tembuku', '2019-06-10 04:42:07'),
('220704', 'ID', 'Kec. Kintamani', '2019-06-10 04:42:07'),
('220800', 'ID', 'Kab. Karang Asem', '2019-06-10 04:42:07'),
('220801', 'ID', 'Kec. Rendang', '2019-06-10 04:42:07'),
('220802', 'ID', 'Kec. Sidemen', '2019-06-10 04:42:07'),
('220803', 'ID', 'Kec. Manggis', '2019-06-10 04:42:07'),
('220804', 'ID', 'Kec. Karang asem', '2019-06-10 04:42:07'),
('220805', 'ID', 'Kec. Abang', '2019-06-10 04:42:07'),
('220806', 'ID', 'Kec. Bebandem', '2019-06-10 04:42:07'),
('220807', 'ID', 'Kec. Selat', '2019-06-10 04:42:07'),
('220808', 'ID', 'Kec. Kubu', '2019-06-10 04:42:07'),
('226000', 'ID', 'Kota Denpasar', '2019-06-10 04:42:07'),
('226001', 'ID', 'Kec. Denpasar Selatan', '2019-06-10 04:42:07'),
('226002', 'ID', 'Kec. Denpasar Timur', '2019-06-10 04:42:07'),
('226003', 'ID', 'Kec. Denpasar Barat', '2019-06-10 04:42:07'),
('226004', 'ID', 'Kec. Denpasar Utara', '2019-06-10 04:42:07'),
('230000', 'ID', 'Prop. Nusa Tenggara Barat', '2019-06-10 04:42:07'),
('230100', 'ID', 'Kab. Lombok Barat', '2019-06-10 04:42:07'),
('230101', 'ID', 'Kec. Sekotong Tengah', '2019-06-10 04:42:07'),
('230102', 'ID', 'Kec. Gerung', '2019-06-10 04:42:07'),
('230103', 'ID', 'Kec. Labuapi', '2019-06-10 04:42:07'),
('230104', 'ID', 'Kec. Kediri', '2019-06-10 04:42:07'),
('230105', 'ID', 'Kec. Narmada', '2019-06-10 04:42:07'),
('230106', 'ID', 'Kec. Gunung Sari', '2019-06-10 04:42:07'),
('230110', 'ID', 'Kec. Kuripan', '2019-06-10 04:42:07'),
('230112', 'ID', 'Kec. Lembar', '2019-06-10 04:42:07'),
('230114', 'ID', 'Kec. Batu Layar', '2019-06-10 04:42:07'),
('230115', 'ID', 'Kec. Lingsar', '2019-06-10 04:42:07'),
('230200', 'ID', 'Kab. Lombok Tengah', '2019-06-10 04:42:07'),
('230201', 'ID', 'Kec. Praya Barat', '2019-06-10 04:42:07'),
('230202', 'ID', 'Kec. Pujut', '2019-06-10 04:42:07'),
('230203', 'ID', 'Kec. Praya Timur', '2019-06-10 04:42:07'),
('230204', 'ID', 'Kec. Janapria', '2019-06-10 04:42:07'),
('230205', 'ID', 'Kec. Kopang', '2019-06-10 04:42:07'),
('230206', 'ID', 'Kec. Praya', '2019-06-10 04:42:07'),
('230207', 'ID', 'Kec. Jonggat', '2019-06-10 04:42:07'),
('230208', 'ID', 'Kec. Pringgarata', '2019-06-10 04:42:07'),
('230209', 'ID', 'Kec. Batukliang', '2019-06-10 04:42:07'),
('230210', 'ID', 'Kec. Batukliang Utara', '2019-06-10 04:42:07'),
('230211', 'ID', 'Kec. Praya Barat Daya', '2019-06-10 04:42:07'),
('230212', 'ID', 'Kec. Praya Tengah', '2019-06-10 04:42:07'),
('230300', 'ID', 'Kab. Lombok Timur', '2019-06-10 04:42:07'),
('230301', 'ID', 'Kec. Keruak', '2019-06-10 04:42:07'),
('230302', 'ID', 'Kec. Sakra', '2019-06-10 04:42:07'),
('230303', 'ID', 'Kec. Terara', '2019-06-10 04:42:07'),
('230304', 'ID', 'Kec. Sikur', '2019-06-10 04:42:07'),
('230305', 'ID', 'Kec. Masbagik', '2019-06-10 04:42:07'),
('230306', 'ID', 'Kec. Sukamulia', '2019-06-10 04:42:07'),
('230307', 'ID', 'Kec. Selong', '2019-06-10 04:42:07'),
('230308', 'ID', 'Kec. Pringgabaya', '2019-06-10 04:42:07'),
('230309', 'ID', 'Kec. Aikmel', '2019-06-10 04:42:07'),
('230310', 'ID', 'Kec. Sambelia', '2019-06-10 04:42:07'),
('230311', 'ID', 'Kec. Labuhan Haji', '2019-06-10 04:42:07'),
('230312', 'ID', 'Kec. Suralaga', '2019-06-10 04:42:07'),
('230313', 'ID', 'Kec. Sakra Timur', '2019-06-10 04:42:07'),
('230314', 'ID', 'Kec. Sakra Barat', '2019-06-10 04:42:07'),
('230315', 'ID', 'Kec. Jerowaru', '2019-06-10 04:42:07'),
('230316', 'ID', 'Kec. Pringgasela', '2019-06-10 04:42:07'),
('230317', 'ID', 'Kec. Montong Gading', '2019-06-10 04:42:07'),
('230318', 'ID', 'Kec. Wanasaba', '2019-06-10 04:42:07'),
('230319', 'ID', 'Kec. Sembalun', '2019-06-10 04:42:07'),
('230320', 'ID', 'Kec. Suela', '2019-06-10 04:42:07'),
('230400', 'ID', 'Kab. Sumbawa', '2019-06-10 04:42:07'),
('230402', 'ID', 'Kec. Lunyuk', '2019-06-10 04:42:07'),
('230405', 'ID', 'Kec. Alas', '2019-06-10 04:42:07'),
('230407', 'ID', 'Kec. Batu Lanten', '2019-06-10 04:42:07'),
('230408', 'ID', 'Kec. Sumbawa', '2019-06-10 04:42:07'),
('230409', 'ID', 'Kec. Moyo Hilir', '2019-06-10 04:42:07'),
('230410', 'ID', 'Kec. Moyo Hulu', '2019-06-10 04:42:07'),
('230411', 'ID', 'Kec. Ropang', '2019-06-10 04:42:07'),
('230413', 'ID', 'Kec. Plampang', '2019-06-10 04:42:07'),
('230414', 'ID', 'Kec. Empang', '2019-06-10 04:42:07'),
('230415', 'ID', 'Kec. Labuhan Badas', '2019-06-10 04:42:07'),
('230416', 'ID', 'Kec. Alas Barat', '2019-06-10 04:42:07'),
('230419', 'ID', 'Kec. Labangka', '2019-06-10 04:42:07'),
('230420', 'ID', 'Kec. Unter Iwes', '2019-06-10 04:42:07'),
('230421', 'ID', 'Kec. Rhee', '2019-06-10 04:42:07'),
('230422', 'ID', 'Kec. Buer', '2019-06-10 04:42:07'),
('230423', 'ID', 'Kec. Moyo Utara', '2019-06-10 04:42:07'),
('230424', 'ID', 'Kec. Maronge', '2019-06-10 04:42:07'),
('230425', 'ID', 'Kec. Tarano', '2019-06-10 04:42:07'),
('230426', 'ID', 'Kec. Lopok', '2019-06-10 04:42:07'),
('230427', 'ID', 'Kec. Lenangguar', '2019-06-10 04:42:07'),
('230428', 'ID', 'Kec. Orong Telu', '2019-06-10 04:42:07'),
('230429', 'ID', 'Kec. Utan', '2019-06-10 04:42:07'),
('230430', 'ID', 'Kec. Lape', '2019-06-10 04:42:07'),
('230431', 'ID', 'Kec. Lantung', '2019-06-10 04:42:07'),
('230500', 'ID', 'Kab. Dompu', '2019-06-10 04:42:07'),
('230501', 'ID', 'Kec. Hu`u', '2019-06-10 04:42:07'),
('230502', 'ID', 'Kec. Dompu', '2019-06-10 04:42:07'),
('230503', 'ID', 'Kec. Woja', '2019-06-10 04:42:07'),
('230504', 'ID', 'Kec. Kilo', '2019-06-10 04:42:07'),
('230505', 'ID', 'Kec. Kempo', '2019-06-10 04:42:07'),
('230506', 'ID', 'Kec. Pekat', '2019-06-10 04:42:07'),
('230507', 'ID', 'Kec. Pajo', '2019-06-10 04:42:07'),
('230508', 'ID', 'Kec. Manggelewa', '2019-06-10 04:42:07'),
('230600', 'ID', 'Kab. Bima', '2019-06-10 04:42:07'),
('230601', 'ID', 'Kec. Monta', '2019-06-10 04:42:07'),
('230602', 'ID', 'Kec. Bolo', '2019-06-10 04:42:07'),
('230603', 'ID', 'Kec. Woha', '2019-06-10 04:42:07'),
('230604', 'ID', 'Kec. Belo', '2019-06-10 04:42:07'),
('230605', 'ID', 'Kec. Wawo', '2019-06-10 04:42:07'),
('230606', 'ID', 'Kec. Sape', '2019-06-10 04:42:07'),
('230607', 'ID', 'Kec. Wera', '2019-06-10 04:42:07'),
('230608', 'ID', 'Kec. Donggo', '2019-06-10 04:42:07'),
('230609', 'ID', 'Kec. Sanggar', '2019-06-10 04:42:07'),
('230610', 'ID', 'Kec. Lambu', '2019-06-10 04:42:07'),
('230611', 'ID', 'Kec. Tambora', '2019-06-10 04:42:07'),
('230612', 'ID', 'Kec. Ambalawi', '2019-06-10 04:42:07'),
('230613', 'ID', 'Kec. Mada pangga', '2019-06-10 04:42:07'),
('230614', 'ID', 'Kec. Langgudu', '2019-06-10 04:42:07'),
('230615', 'ID', 'Kec. Soromandi', '2019-06-10 04:42:07'),
('230616', 'ID', 'Kec. Parado', '2019-06-10 04:42:07'),
('230617', 'ID', 'Kec. Lambitu', '2019-06-10 04:42:07'),
('230618', 'ID', 'Kec. Palibelo', '2019-06-10 04:42:07'),
('230700', 'ID', 'Kab. Sumbawa Barat', '2019-06-10 04:42:07'),
('230701', 'ID', 'Kec. Jereweh', '2019-06-10 04:42:07'),
('230702', 'ID', 'Kec. Taliwang', '2019-06-10 04:42:07'),
('230703', 'ID', 'Kec. Seteluk', '2019-06-10 04:42:07'),
('230704', 'ID', 'Kec. Brang Rea', '2019-06-10 04:42:07'),
('230705', 'ID', 'Kec. Sekongkang', '2019-06-10 04:42:07'),
('230706', 'ID', 'Kec. Maluk', '2019-06-10 04:42:07'),
('230707', 'ID', 'Kec. Brang Ene', '2019-06-10 04:42:07'),
('230708', 'ID', 'Kec. Poto Tano', '2019-06-10 04:42:07'),
('230800', 'ID', 'Kab. Lombok Utara', '2019-06-10 04:42:07'),
('230801', 'ID', 'Kec. Tanjung', '2019-06-10 04:42:07'),
('230802', 'ID', 'Kec. Gangga', '2019-06-10 04:42:07'),
('230803', 'ID', 'Kec. Bayan', '2019-06-10 04:42:07'),
('230804', 'ID', 'Kec. Pemenang', '2019-06-10 04:42:07'),
('230805', 'ID', 'Kec. Kayangan', '2019-06-10 04:42:07'),
('236000', 'ID', 'Kota Mataram', '2019-06-10 04:42:07'),
('236001', 'ID', 'Kec. Ampenan', '2019-06-10 04:42:07'),
('236002', 'ID', 'Kec. Mataram', '2019-06-10 04:42:07'),
('236003', 'ID', 'Kec. Cakranegara', '2019-06-10 04:42:07'),
('236004', 'ID', 'Kec. Sekarbela', '2019-06-10 04:42:07'),
('236005', 'ID', 'Kec. Selaperang', '2019-06-10 04:42:07'),
('236006', 'ID', 'Kec. Sandubaya', '2019-06-10 04:42:07'),
('236100', 'ID', 'Kota Bima', '2019-06-10 04:42:07'),
('236101', 'ID', 'Kec. RasanaE Barat', '2019-06-10 04:42:07'),
('236102', 'ID', 'Kec. RasanaE Timur', '2019-06-10 04:42:07'),
('236103', 'ID', 'Kec. Asakota', '2019-06-10 04:42:07'),
('236104', 'ID', 'Kec. Raba', '2019-06-10 04:42:07'),
('236105', 'ID', 'Kec. Mpunda', '2019-06-10 04:42:07'),
('240000', 'ID', 'Prop. Nusa Tenggara Timur', '2019-06-10 04:42:07'),
('240100', 'ID', 'Kab. Kupang', '2019-06-10 04:42:07'),
('240101', 'ID', 'Kec. Raijua', '2019-06-10 04:42:07'),
('240102', 'ID', 'Kec. Sabu Barat', '2019-06-10 04:42:07'),
('240103', 'ID', 'Kec. Sabu Timur', '2019-06-10 04:42:07'),
('240110', 'ID', 'Kec. Semau', '2019-06-10 04:42:07');
INSERT INTO `dikti_wilayah` (`id_wilayah`, `id_negara`, `nama_wilayah`, `dikti_wilayah_timestamp`) VALUES
('240111', 'ID', 'Kec. Kupang Barat', '2019-06-10 04:42:07'),
('240112', 'ID', 'Kec. Kupang Tengah', '2019-06-10 04:42:07'),
('240113', 'ID', 'Kec. Amarasi', '2019-06-10 04:42:07'),
('240114', 'ID', 'Kec. Kupang Timur', '2019-06-10 04:42:07'),
('240115', 'ID', 'Kec. Sulamu', '2019-06-10 04:42:07'),
('240116', 'ID', 'Kec. Fatuleu', '2019-06-10 04:42:07'),
('240117', 'ID', 'Kec. Takari', '2019-06-10 04:42:07'),
('240118', 'ID', 'Kec. Amfoang Selatan', '2019-06-10 04:42:07'),
('240119', 'ID', 'Kec. Amfoang Utara', '2019-06-10 04:42:07'),
('240120', 'ID', 'Kec. Sabu Tengah', '2019-06-10 04:42:07'),
('240121', 'ID', 'Kec. Nekamese', '2019-06-10 04:42:07'),
('240122', 'ID', 'Kec. Amabi Oefeto Timur', '2019-06-10 04:42:07'),
('240123', 'ID', 'Kec. Amarasi Selatan', '2019-06-10 04:42:07'),
('240124', 'ID', 'Kec. Amarasi Timur', '2019-06-10 04:42:07'),
('240125', 'ID', 'Kec. Amarasi Barat', '2019-06-10 04:42:07'),
('240126', 'ID', 'Kec. Amfoang Barat Daya', '2019-06-10 04:42:07'),
('240127', 'ID', 'Kec. Amfoang Barat Laut', '2019-06-10 04:42:07'),
('240128', 'ID', 'Kec. Sabu Liae', '2019-06-10 04:42:07'),
('240129', 'ID', 'Kec. Hawu Mehara', '2019-06-10 04:42:07'),
('240130', 'ID', 'Kec. Semau Selatan', '2019-06-10 04:42:07'),
('240131', 'ID', 'Kec. Taebenu', '2019-06-10 04:42:07'),
('240132', 'ID', 'Kec. Amabi Oefeto', '2019-06-10 04:42:07'),
('240133', 'ID', 'Kec. Fatuleu Tengah', '2019-06-10 04:42:07'),
('240134', 'ID', 'Kec. Fatuleu Barat', '2019-06-10 04:42:07'),
('240135', 'ID', 'Kec. Amfoang Timur', '2019-06-10 04:42:07'),
('240136', 'ID', 'Kec. Amfoang Tengah', '2019-06-10 04:42:07'),
('240300', 'ID', 'Kab. Timor Tengah Selatan', '2019-06-10 04:42:07'),
('240301', 'ID', 'Kec. Mollo Utara', '2019-06-10 04:42:07'),
('240302', 'ID', 'Kec. Mollo Selatan', '2019-06-10 04:42:07'),
('240303', 'ID', 'Kec. Kota Soe', '2019-06-10 04:42:07'),
('240304', 'ID', 'Kec. Amanuban Barat', '2019-06-10 04:42:07'),
('240305', 'ID', 'Kec. Amanuban Selatan', '2019-06-10 04:42:07'),
('240306', 'ID', 'Kec. Kuanfatu', '2019-06-10 04:42:07'),
('240307', 'ID', 'Kec. Amanuban Tengah', '2019-06-10 04:42:07'),
('240308', 'ID', 'Kec. Amanuban Timur', '2019-06-10 04:42:07'),
('240309', 'ID', 'Kec. Kie', '2019-06-10 04:42:07'),
('240310', 'ID', 'Kec. Amanatun Selatan', '2019-06-10 04:42:07'),
('240311', 'ID', 'Kec. Amanatun Utara', '2019-06-10 04:42:07'),
('240312', 'ID', 'Kec. Fatumnasi', '2019-06-10 04:42:07'),
('240313', 'ID', 'Kec. Polen', '2019-06-10 04:42:07'),
('240314', 'ID', 'Kec. BatuPutih', '2019-06-10 04:42:07'),
('240315', 'ID', 'Kec. Boking', '2019-06-10 04:42:07'),
('240316', 'ID', 'Kec. Noebana', '2019-06-10 04:42:07'),
('240317', 'ID', 'Kec. Nunkolo', '2019-06-10 04:42:07'),
('240318', 'ID', 'Kec. Kot`Olin', '2019-06-10 04:42:07'),
('240319', 'ID', 'Kec. Oenino', '2019-06-10 04:42:07'),
('240320', 'ID', 'Kec. Kolbano', '2019-06-10 04:42:07'),
('240321', 'ID', 'Kec. Kualin', '2019-06-10 04:42:07'),
('240322', 'ID', 'Kec. Toianas', '2019-06-10 04:42:07'),
('240323', 'ID', 'Kec. Mollo Barat', '2019-06-10 04:42:07'),
('240324', 'ID', 'Kec. Kok Baun', '2019-06-10 04:42:07'),
('240325', 'ID', 'Kec. Tobu', '2019-06-10 04:42:07'),
('240326', 'ID', 'Kec. Nunbena', '2019-06-10 04:42:07'),
('240327', 'ID', 'Kec. Mollo Tengah', '2019-06-10 04:42:07'),
('240328', 'ID', 'Kec. Kuatnana', '2019-06-10 04:42:07'),
('240329', 'ID', 'Kec. Noebeba', '2019-06-10 04:42:07'),
('240330', 'ID', 'Kec. Fautmolo', '2019-06-10 04:42:07'),
('240331', 'ID', 'Kec. Fatukopa', '2019-06-10 04:42:07'),
('240332', 'ID', 'Kec. Santian', '2019-06-10 04:42:07'),
('240400', 'ID', 'Kab. Timor Tengah Utara', '2019-06-10 04:42:07'),
('240401', 'ID', 'Kec. Miomafo Barat', '2019-06-10 04:42:07'),
('240402', 'ID', 'Kec. Miomafo Timur', '2019-06-10 04:42:07'),
('240403', 'ID', 'Kec. Kota Kefamenanu', '2019-06-10 04:42:07'),
('240404', 'ID', 'Kec. Insana', '2019-06-10 04:42:07'),
('240405', 'ID', 'Kec. Biboki Selatan', '2019-06-10 04:42:07'),
('240406', 'ID', 'Kec. Biboki Utara', '2019-06-10 04:42:07'),
('240407', 'ID', 'Kec. Noemuti', '2019-06-10 04:42:07'),
('240408', 'ID', 'Kec. Insana Utara', '2019-06-10 04:42:07'),
('240409', 'ID', 'Kec. Biboki Anleu', '2019-06-10 04:42:07'),
('240410', 'ID', 'Kec. Noemuti Timur', '2019-06-10 04:42:07'),
('240411', 'ID', 'Kec. Miomafo Tengah', '2019-06-10 04:42:07'),
('240412', 'ID', 'Kec. Musi', '2019-06-10 04:42:07'),
('240413', 'ID', 'Kec. Mutis', '2019-06-10 04:42:07'),
('240414', 'ID', 'Kec. Bikomi Selatan', '2019-06-10 04:42:07'),
('240415', 'ID', 'Kec. Bikomi Tengah', '2019-06-10 04:42:07'),
('240416', 'ID', 'Kec. Bikomi Nilulat', '2019-06-10 04:42:07'),
('240417', 'ID', 'Kec. Bikomi Utara', '2019-06-10 04:42:07'),
('240418', 'ID', 'Kec. Naibenu', '2019-06-10 04:42:07'),
('240419', 'ID', 'Kec. Insana Fafinesu', '2019-06-10 04:42:07'),
('240420', 'ID', 'Kec. Insana Barat', '2019-06-10 04:42:07'),
('240421', 'ID', 'Kec. Insana Tengah', '2019-06-10 04:42:07'),
('240422', 'ID', 'Kec. Biboki Tanpah', '2019-06-10 04:42:07'),
('240423', 'ID', 'Kec. Biboki Moenleu', '2019-06-10 04:42:07'),
('240424', 'ID', 'Kec. Biboki Feotleu', '2019-06-10 04:42:07'),
('240500', 'ID', 'Kab. Belu', '2019-06-10 04:42:07'),
('240501', 'ID', 'Kec. Malaka Barat', '2019-06-10 04:42:07'),
('240502', 'ID', 'Kec. Malaka Tengah', '2019-06-10 04:42:07'),
('240503', 'ID', 'Kec. Malaka Timur', '2019-06-10 04:42:07'),
('240504', 'ID', 'Kec. Kobalima', '2019-06-10 04:42:07'),
('240505', 'ID', 'Kec. Tasifeto Barat', '2019-06-10 04:42:07'),
('240506', 'ID', 'Kec. Kota Atambua', '2019-06-10 04:42:07'),
('240507', 'ID', 'Kec. Tasifeto Timur', '2019-06-10 04:42:07'),
('240508', 'ID', 'Kec. Lamakmen', '2019-06-10 04:42:07'),
('240509', 'ID', 'Kec. Kakuluk Mesak', '2019-06-10 04:42:07'),
('240510', 'ID', 'Kec. Raihat', '2019-06-10 04:42:07'),
('240511', 'ID', 'Kec. Rinhat', '2019-06-10 04:42:07'),
('240512', 'ID', 'Kec. Sasita Mean', '2019-06-10 04:42:07'),
('240513', 'ID', 'Kec. Weliman', '2019-06-10 04:42:07'),
('240514', 'ID', 'Kec. Wewiku', '2019-06-10 04:42:07'),
('240515', 'ID', 'Kec. Rai Manuk', '2019-06-10 04:42:07'),
('240516', 'ID', 'Kec. Laen Manen', '2019-06-10 04:42:07'),
('240517', 'ID', 'Kec. Lasiolat', '2019-06-10 04:42:07'),
('240518', 'ID', 'Kec. Lamakmen Selatan', '2019-06-10 04:42:07'),
('240519', 'ID', 'Kec. Lo Kufeu', '2019-06-10 04:42:07'),
('240520', 'ID', 'Kec. Botin Leo Bele', '2019-06-10 04:42:07'),
('240521', 'ID', 'Kec. Atambua Barat', '2019-06-10 04:42:07'),
('240522', 'ID', 'Kec. Atambua Selatan', '2019-06-10 04:42:07'),
('240523', 'ID', 'Kec. Nanaet Duabesi', '2019-06-10 04:42:07'),
('240524', 'ID', 'Kec. Kobalima Timur', '2019-06-10 04:42:07'),
('240525', 'ID', 'Malaka Barat', '2019-06-10 04:42:07'),
('240526', 'ID', 'Malaka Timur', '2019-06-10 04:42:07'),
('240527', 'ID', 'Kobalima', '2019-06-10 04:42:07'),
('240528', 'ID', 'Sasitamean', '2019-06-10 04:42:07'),
('240529', 'ID', 'Laen Manen', '2019-06-10 04:42:07'),
('240530', 'ID', 'Io Kufeu', '2019-06-10 04:42:07'),
('240531', 'ID', 'Botin Leo Bele', '2019-06-10 04:42:07'),
('240532', 'ID', 'Kobalima Timur', '2019-06-10 04:42:07'),
('240533', 'ID', 'Rinhat', '2019-06-10 04:42:07'),
('240534', 'ID', 'Wewiku', '2019-06-10 04:42:07'),
('240535', 'ID', 'Weliman', '2019-06-10 04:42:07'),
('240600', 'ID', 'Kab. Alor', '2019-06-10 04:42:07'),
('240602', 'ID', 'Kec. Alor Barat Daya', '2019-06-10 04:42:07'),
('240603', 'ID', 'Kec. Alor Selatan', '2019-06-10 04:42:07'),
('240604', 'ID', 'Kec. Alor Timur', '2019-06-10 04:42:07'),
('240605', 'ID', 'Kec. Teluk Mutiara', '2019-06-10 04:42:07'),
('240606', 'ID', 'Kec. Alor Barat Laut', '2019-06-10 04:42:07'),
('240607', 'ID', 'Kec. Pantar', '2019-06-10 04:42:07'),
('240608', 'ID', 'Kec. Alor Timur Laut', '2019-06-10 04:42:07'),
('240609', 'ID', 'Kec. Alor Tengah Utara', '2019-06-10 04:42:07'),
('240610', 'ID', 'Kec. Pantar Barat', '2019-06-10 04:42:07'),
('240611', 'ID', 'Kec. Pantar Timur', '2019-06-10 04:42:07'),
('240612', 'ID', 'Kec. Pantar Barat Laut', '2019-06-10 04:42:07'),
('240613', 'ID', 'Kec. Pantar Tengah', '2019-06-10 04:42:07'),
('240614', 'ID', 'Kec. Mataru', '2019-06-10 04:42:07'),
('240615', 'ID', 'Kec. Pureman', '2019-06-10 04:42:07'),
('240616', 'ID', 'Kec. Kabola', '2019-06-10 04:42:07'),
('240617', 'ID', 'Kec. Pulau Pura', '2019-06-10 04:42:07'),
('240618', 'ID', 'Kec. Lembur', '2019-06-10 04:42:07'),
('240700', 'ID', 'Kab. Flores Timur', '2019-06-10 04:42:07'),
('240701', 'ID', 'Kec. Wulang Gitang', '2019-06-10 04:42:07'),
('240702', 'ID', 'Kec. Tanjung Bunga', '2019-06-10 04:42:07'),
('240703', 'ID', 'Kec. Larantuka', '2019-06-10 04:42:07'),
('240704', 'ID', 'Kec. Solor Barat', '2019-06-10 04:42:07'),
('240705', 'ID', 'Kec. Solor Timur', '2019-06-10 04:42:07'),
('240706', 'ID', 'Kec. Adonara Barat', '2019-06-10 04:42:07'),
('240707', 'ID', 'Kec. Adonara Timur', '2019-06-10 04:42:07'),
('240708', 'ID', 'Kec. Titehena', '2019-06-10 04:42:07'),
('240709', 'ID', 'Kec. Ile Boleng', '2019-06-10 04:42:07'),
('240710', 'ID', 'Kec. Witihama', '2019-06-10 04:42:07'),
('240711', 'ID', 'Kec. Kelubagolit', '2019-06-10 04:42:07'),
('240712', 'ID', 'Kec. Wotan Ulumado', '2019-06-10 04:42:07'),
('240713', 'ID', 'Kec. Ile Mandiri', '2019-06-10 04:42:07'),
('240714', 'ID', 'Kec. Demon Pagong', '2019-06-10 04:42:07'),
('240715', 'ID', 'Kec. Lewolema', '2019-06-10 04:42:07'),
('240716', 'ID', 'Kec. Ilebura', '2019-06-10 04:42:07'),
('240717', 'ID', 'Kec. Adonara', '2019-06-10 04:42:07'),
('240718', 'ID', 'Kec. Adonara Tengah', '2019-06-10 04:42:07'),
('240719', 'ID', 'Kec. Solor Selatan', '2019-06-10 04:42:07'),
('240800', 'ID', 'Kab. Sikka', '2019-06-10 04:42:07'),
('240801', 'ID', 'Kec. Paga', '2019-06-10 04:42:07'),
('240802', 'ID', 'Kec. Lela', '2019-06-10 04:42:07'),
('240803', 'ID', 'Kec. Bola', '2019-06-10 04:42:07'),
('240804', 'ID', 'Kec. Talibura', '2019-06-10 04:42:07'),
('240805', 'ID', 'Kec. Kewapante', '2019-06-10 04:42:07'),
('240806', 'ID', 'Kec. Nelle', '2019-06-10 04:42:07'),
('240807', 'ID', 'Kec. Nitta', '2019-06-10 04:42:07'),
('240808', 'ID', 'Kec. Alok', '2019-06-10 04:42:07'),
('240809', 'ID', 'Kec. Mego', '2019-06-10 04:42:07'),
('240810', 'ID', 'Kec. Waigete', '2019-06-10 04:42:07'),
('240811', 'ID', 'Kec. Palue', '2019-06-10 04:42:07'),
('240812', 'ID', 'Kec. Waiblama', '2019-06-10 04:42:07'),
('240813', 'ID', 'Kec. Alok Barat', '2019-06-10 04:42:07'),
('240814', 'ID', 'Kec. Alok Timur', '2019-06-10 04:42:07'),
('240815', 'ID', 'Kec. Magependa', '2019-06-10 04:42:07'),
('240816', 'ID', 'Kec. Koting', '2019-06-10 04:42:07'),
('240817', 'ID', 'Kec. Tana Wawo', '2019-06-10 04:42:07'),
('240818', 'ID', 'Kec. Hewokloang', '2019-06-10 04:42:07'),
('240819', 'ID', 'Kec. Kangae', '2019-06-10 04:42:07'),
('240820', 'ID', 'Kec. Doreng', '2019-06-10 04:42:07'),
('240821', 'ID', 'Kec. Mapitara', '2019-06-10 04:42:07'),
('240900', 'ID', 'Kab. Ende', '2019-06-10 04:42:07'),
('240901', 'ID', 'Kec. Nangapanda', '2019-06-10 04:42:07'),
('240902', 'ID', 'Kec. Ende', '2019-06-10 04:42:07'),
('240903', 'ID', 'Kec. Ende Selatan', '2019-06-10 04:42:07'),
('240904', 'ID', 'Kec. Ndona', '2019-06-10 04:42:07'),
('240905', 'ID', 'Kec. Wolowaru', '2019-06-10 04:42:07'),
('240906', 'ID', 'Kec. Maurole', '2019-06-10 04:42:07'),
('240907', 'ID', 'Kec. Detusoko', '2019-06-10 04:42:07'),
('240909', 'ID', 'Kec. Maukaro', '2019-06-10 04:42:07'),
('240910', 'ID', 'Kec. Wewaria', '2019-06-10 04:42:07'),
('240911', 'ID', 'Kec. Wolojita', '2019-06-10 04:42:07'),
('240912', 'ID', 'Kec. Pulau Ende', '2019-06-10 04:42:07'),
('240913', 'ID', 'Kec. Kota Baru', '2019-06-10 04:42:07'),
('240914', 'ID', 'Kec. Ndona Timur', '2019-06-10 04:42:07'),
('240915', 'ID', 'Kec. Kelimutu', '2019-06-10 04:42:07'),
('240916', 'ID', 'Kec. Lio Timur', '2019-06-10 04:42:07'),
('240917', 'ID', 'Kec. Detukeli', '2019-06-10 04:42:07'),
('240918', 'ID', 'Kec. Ndori', '2019-06-10 04:42:07'),
('240919', 'ID', 'Kec. Ende Utara', '2019-06-10 04:42:07'),
('240920', 'ID', 'Kec. Ende Tengah', '2019-06-10 04:42:07'),
('240921', 'ID', 'Kec. Ende Timur', '2019-06-10 04:42:07'),
('240922', 'ID', 'Kec. Lepembusu Kelisoke', '2019-06-10 04:42:07'),
('241000', 'ID', 'Kab. Ngada', '2019-06-10 04:42:07'),
('241001', 'ID', 'Kec. Aimere', '2019-06-10 04:42:07'),
('241002', 'ID', 'Kec. Bajawa', '2019-06-10 04:42:07'),
('241003', 'ID', 'Kec. Golewa', '2019-06-10 04:42:07'),
('241004', 'ID', 'Kec. Golewa Barat', '2020-02-10 03:07:44'),
('241007', 'ID', 'Kec. Bajawa Utara', '2019-06-10 04:42:07'),
('241008', 'ID', 'Kec. Riung', '2019-06-10 04:42:07'),
('241012', 'ID', 'Kec. Riung Barat', '2019-06-10 04:42:07'),
('241013', 'ID', 'Kec. Soa', '2019-06-10 04:42:07'),
('241015', 'ID', 'Kec. Jerebuu', '2019-06-10 04:42:07'),
('241017', 'ID', 'Kec. Riung Selatan', '2019-06-10 04:42:07'),
('241018', 'ID', 'Kec. Inerie', '2020-02-10 03:07:44'),
('241019', 'ID', 'Kec. Golewa Selatan', '2020-02-10 03:07:44'),
('241100', 'ID', 'Kab. Manggarai', '2019-06-10 04:42:07'),
('241104', 'ID', 'Kec. Satarmese', '2019-06-10 04:42:07'),
('241111', 'ID', 'Kec. Langke Rembong', '2019-06-10 04:42:07'),
('241112', 'ID', 'Kec. Ruteng', '2019-06-10 04:42:07'),
('241113', 'ID', 'Kec. Cibal', '2019-06-10 04:42:07'),
('241114', 'ID', 'Kec. Reok', '2019-06-10 04:42:07'),
('241117', 'ID', 'Kec. Wae Ri`I', '2019-06-10 04:42:07'),
('241118', 'ID', 'Kec. Satar Mese Barat', '2019-06-10 04:42:07'),
('241119', 'ID', 'Kec. Rahong Utara', '2019-06-10 04:42:07'),
('241120', 'ID', 'Kec. Lelak', '2019-06-10 04:42:07'),
('241121', 'ID', 'Kec. Reok Barat', '2020-02-10 03:07:44'),
('241122', 'ID', 'Kec. Cibal Barat', '2020-02-10 03:07:44'),
('241123', 'ID', 'Kec. Landu Leko', '2020-02-10 03:07:44'),
('241200', 'ID', 'Kab. Sumba Timur', '2019-06-10 04:42:07'),
('241201', 'ID', 'Kec. Lewa', '2019-06-10 04:42:07'),
('241202', 'ID', 'Kec. Tabundung', '2019-06-10 04:42:07'),
('241203', 'ID', 'Kec. Paberiwai', '2019-06-10 04:42:07'),
('241204', 'ID', 'Kec. Pahunga Lodu', '2019-06-10 04:42:07'),
('241205', 'ID', 'Kec. Rindi', '2019-06-10 04:42:07'),
('241206', 'ID', 'Kec. Pandawai', '2019-06-10 04:42:07'),
('241207', 'ID', 'Kec. Kota Waingapu', '2019-06-10 04:42:07'),
('241208', 'ID', 'Kec. Haharu', '2019-06-10 04:42:07'),
('241209', 'ID', 'Kec. Nggaha Ori Angu', '2019-06-10 04:42:07'),
('241210', 'ID', 'Kec. Karera', '2019-06-10 04:42:07'),
('241211', 'ID', 'Kec. Umalulu', '2019-06-10 04:42:07'),
('241212', 'ID', 'Kec. Kahaungu Eti', '2019-06-10 04:42:07'),
('241213', 'ID', 'Kec. Matawai La Pawu', '2019-06-10 04:42:07'),
('241214', 'ID', 'Kec. Pinu Pahar', '2019-06-10 04:42:07'),
('241215', 'ID', 'Kec. Wulla Waijelu', '2019-06-10 04:42:07'),
('241216', 'ID', 'Kec. Katala Hamu Lingu', '2019-06-10 04:42:07'),
('241217', 'ID', 'Kec. Mahu', '2019-06-10 04:42:07'),
('241218', 'ID', 'Kec. Ngadu Ngala', '2019-06-10 04:42:07'),
('241219', 'ID', 'Kec. Kambata Mapambuhang', '2019-06-10 04:42:07'),
('241220', 'ID', 'Kec. Kambera', '2019-06-10 04:42:07'),
('241221', 'ID', 'Kec. Kanatang', '2019-06-10 04:42:07'),
('241222', 'ID', 'Kec. Lewa Tidahu', '2019-06-10 04:42:07'),
('241300', 'ID', 'Kab. Sumba Barat', '2019-06-10 04:42:07'),
('241305', 'ID', 'Kec. Loli', '2019-06-10 04:42:07'),
('241306', 'ID', 'Kec. Kota Waikabubak', '2019-06-10 04:42:07'),
('241310', 'ID', 'Kec. Wanokaka', '2019-06-10 04:42:07'),
('241311', 'ID', 'Kec. Lamboya', '2019-06-10 04:42:07'),
('241318', 'ID', 'Kec. Tana Righu', '2019-06-10 04:42:07'),
('241319', 'ID', 'Kec. Laboya Barat', '2019-06-10 04:42:07'),
('241400', 'ID', 'Kab. Lembata', '2019-06-10 04:42:07'),
('241401', 'ID', 'Kec. Naga Wutung', '2019-06-10 04:42:07'),
('241402', 'ID', 'Kec. Atadei', '2019-06-10 04:42:07'),
('241403', 'ID', 'Kec. Ile Ape', '2019-06-10 04:42:07'),
('241404', 'ID', 'Kec. Lebatukan', '2019-06-10 04:42:07'),
('241405', 'ID', 'Kec. Nubatukan', '2019-06-10 04:42:07'),
('241406', 'ID', 'Kec. Omesuri', '2019-06-10 04:42:07'),
('241407', 'ID', 'Kec. Buyasuri', '2019-06-10 04:42:07'),
('241408', 'ID', 'Kec. Wulandoni', '2019-06-10 04:42:07'),
('241409', 'ID', 'Kec. Ile Ape Timur', '2019-06-10 04:42:07'),
('241500', 'ID', 'Kab. Rote-Ndao', '2019-06-10 04:42:07'),
('241504', 'ID', 'Kec. Rote Barat Daya', '2019-06-10 04:42:07'),
('241505', 'ID', 'Kec. Rote Barat Laut', '2019-06-10 04:42:07'),
('241506', 'ID', 'Kec. Lobalain', '2019-06-10 04:42:07'),
('241507', 'ID', 'Kec. Rote Tengah', '2019-06-10 04:42:07'),
('241508', 'ID', 'Kec. Pantai Baru', '2019-06-10 04:42:07'),
('241509', 'ID', 'Kec. Rote Timur', '2019-06-10 04:42:07'),
('241510', 'ID', 'Kec. Rote Barat', '2019-06-10 04:42:07'),
('241511', 'ID', 'Kec. Rote Selatan', '2019-06-10 04:42:07'),
('241512', 'ID', 'Kec. Ndao Nuse', '2020-02-10 03:07:44'),
('241513', 'ID', 'Kec. Landu Leko', '2020-02-10 03:07:44'),
('241514', 'ID', 'Kec. Ndau Nuse', '2020-02-10 03:07:44'),
('241600', 'ID', 'Kab. Manggarai Barat', '2019-06-10 04:42:07'),
('241601', 'ID', 'Kec. Komodo', '2019-06-10 04:42:07'),
('241602', 'ID', 'Kec. Sano Nggoang', '2019-06-10 04:42:07'),
('241603', 'ID', 'Kec. Lembor', '2019-06-10 04:42:07'),
('241604', 'ID', 'Kec. Kuwus', '2019-06-10 04:42:07'),
('241605', 'ID', 'Kec. Macang Pacar', '2019-06-10 04:42:07'),
('241606', 'ID', 'Kec. Boleng', '2019-06-10 04:42:07'),
('241607', 'ID', 'Kec. Welak', '2019-06-10 04:42:07'),
('241608', 'ID', 'Kec. Ndoso', '2019-06-10 04:42:07'),
('241609', 'ID', 'Kec. Lembor Selatan', '2019-06-10 04:42:07'),
('241610', 'ID', 'Kec. Mbeliling', '2019-06-10 04:42:07'),
('241700', 'ID', 'Kab. Nagakeo', '2019-06-10 04:42:07'),
('241701', 'ID', 'Kec. Aesesa', '2019-06-10 04:42:07'),
('241702', 'ID', 'Kec. Boawae', '2019-06-10 04:42:07'),
('241703', 'ID', 'Kec. Keo Tengah', '2019-06-10 04:42:07'),
('241704', 'ID', 'Kec. Mauponggo', '2019-06-10 04:42:07'),
('241705', 'ID', 'Kec. Nangaroro', '2019-06-10 04:42:07'),
('241706', 'ID', 'Kec. Wolowae', '2019-06-10 04:42:07'),
('241708', 'ID', 'Kec. Aesesa Selatan', '2019-06-10 04:42:07'),
('241800', 'ID', 'Kab. Sumba Tengah', '2019-06-10 04:42:07'),
('241801', 'ID', 'Kec. Katiku Tana', '2019-06-10 04:42:07'),
('241802', 'ID', 'Kec. Mamboro', '2019-06-10 04:42:07'),
('241803', 'ID', 'Kec. Umbu Ratu Nggay', '2019-06-10 04:42:07'),
('241804', 'ID', 'Kec. Umbu Ratu Nggay Barat', '2019-06-10 04:42:07'),
('241805', 'ID', 'Kec. Katiku Tana Selatan', '2019-06-10 04:42:07'),
('241900', 'ID', 'Kab. Sumba Barat Daya', '2019-06-10 04:42:07'),
('241901', 'ID', 'Kec. Kodi', '2019-06-10 04:42:07'),
('241902', 'ID', 'Kec. Kodi Bangedo', '2019-06-10 04:42:07'),
('241903', 'ID', 'Kec. Loura', '2019-06-10 04:42:07'),
('241904', 'ID', 'Kec. Wewewa Barat', '2019-06-10 04:42:07'),
('241905', 'ID', 'Kec. Wewewa Selatan', '2019-06-10 04:42:07'),
('241906', 'ID', 'Kec. Wewewa Timur', '2019-06-10 04:42:07'),
('241907', 'ID', 'Kec. Wewewa Utara', '2019-06-10 04:42:07'),
('241908', 'ID', 'Kec. Kodi Utara', '2019-06-10 04:42:07'),
('241909', 'ID', 'Kec. Kota Tambolaka', '2020-02-10 03:07:44'),
('241910', 'ID', 'Kec. Wewewa Tengah', '2020-02-10 03:07:44'),
('241911', 'ID', 'Kec. Kodi Balaghar', '2020-02-10 03:07:44'),
('242000', 'ID', 'Kab. Manggarai Timur', '2019-06-10 04:42:07'),
('242001', 'ID', 'Kec. Elar', '2019-06-10 04:42:07'),
('242002', 'ID', 'Kec. Kota Komba', '2019-06-10 04:42:07'),
('242003', 'ID', 'Kec. Lamba Leda', '2019-06-10 04:42:07'),
('242004', 'ID', 'Kec. Poco Ranaka', '2019-06-10 04:42:07'),
('242005', 'ID', 'Kec. Sambi Rampas', '2019-06-10 04:42:07'),
('242006', 'ID', 'Kec. Borong', '2019-06-10 04:42:07'),
('242007', 'ID', 'Kec. Rana Mese', '2020-02-10 03:07:44'),
('242008', 'ID', 'Kec. Poco Ranaka Timur', '2020-02-10 03:07:44'),
('242009', 'ID', 'Kec. Elar Selatan', '2020-02-10 03:07:44'),
('242100', 'ID', 'Kab. Sabu Raijua', '2019-06-10 04:42:07'),
('242111', 'ID', 'Hawu Mehara', '2019-06-10 04:42:07'),
('242112', 'ID', 'Sabu Tengah', '2019-06-10 04:42:07'),
('242113', 'ID', 'Raijua', '2019-06-10 04:42:07'),
('242114', 'ID', 'Sabu Liae', '2019-06-10 04:42:07'),
('242115', 'ID', 'Sabu Barat', '2019-06-10 04:42:07'),
('242116', 'ID', 'Sabu Timur', '2019-06-10 04:42:07'),
('242200', 'ID', 'Kab. Malaka', '2020-02-10 03:07:44'),
('242221', 'ID', 'Kec. Malaka Barat', '2020-02-10 03:07:44'),
('242222', 'ID', 'Kec. Wewiku', '2020-02-10 03:07:44'),
('242223', 'ID', 'Kec. Weliman', '2020-02-10 03:07:44'),
('242224', 'ID', 'Kec. Rinhat', '2020-02-10 03:07:44'),
('242225', 'ID', 'Kec. Io Kufeu', '2020-02-10 03:07:44'),
('242226', 'ID', 'Kec. Sasitamean', '2020-02-10 03:07:44'),
('242227', 'ID', 'Kec. Laenmanen', '2020-02-10 03:07:44'),
('242228', 'ID', 'Kec. Malaka Timur', '2020-02-10 03:07:44'),
('242229', 'ID', 'Kec. Kobalima Timur', '2020-02-10 03:07:44'),
('242230', 'ID', 'Kec. Kobalima', '2020-02-10 03:07:44'),
('242231', 'ID', 'Kec. Botin Leobele', '2020-02-10 03:07:44'),
('242232', 'ID', 'Kec. Malaka Tengah', '2020-02-10 03:07:44'),
('246000', 'ID', 'Kota Kupang', '2019-06-10 04:42:07'),
('246001', 'ID', 'Kec. Alak', '2019-06-10 04:42:07'),
('246002', 'ID', 'Kec. Maulafa', '2019-06-10 04:42:07'),
('246003', 'ID', 'Kec. Oebodo', '2019-06-10 04:42:07'),
('246004', 'ID', 'Kec. Kelapa Lima', '2019-06-10 04:42:07'),
('246005', 'ID', 'Kec. Kota Raja', '2019-06-10 04:42:07'),
('246006', 'ID', 'Kec. Kota Lama', '2019-06-10 04:42:07'),
('250000', 'ID', 'Prop. Papua', '2019-06-10 04:42:07'),
('250100', 'ID', 'Kab. Jayapura', '2019-06-10 04:42:07'),
('250108', 'ID', 'Kec. Kaureh', '2019-06-10 04:42:07'),
('250114', 'ID', 'Kec. Kemtuk', '2019-06-10 04:42:07'),
('250115', 'ID', 'Kec. Kemtuk Gresie', '2019-06-10 04:42:07'),
('250116', 'ID', 'Kec. Nimboran', '2019-06-10 04:42:07'),
('250117', 'ID', 'Kec. Nimbokrang', '2019-06-10 04:42:07'),
('250118', 'ID', 'Kec. Unurum Guay', '2019-06-10 04:42:07'),
('250120', 'ID', 'Kec. Demta', '2019-06-10 04:42:07'),
('250121', 'ID', 'Kec. Depapre', '2019-06-10 04:42:07'),
('250122', 'ID', 'Kec. Sentani Barat', '2019-06-10 04:42:07'),
('250123', 'ID', 'Kec. Sentani', '2019-06-10 04:42:07'),
('250124', 'ID', 'Kec. Sentani Timur', '2019-06-10 04:42:07'),
('250125', 'ID', 'Kec. Airu', '2019-06-10 04:42:07'),
('250126', 'ID', 'Kec. Yapsi', '2019-06-10 04:42:07'),
('250127', 'ID', 'Kec. Nimboran Timur/Namblong', '2019-06-10 04:42:07'),
('250128', 'ID', 'Kec. Waibu', '2019-06-10 04:42:07'),
('250129', 'ID', 'Kec. Ebungfau', '2019-06-10 04:42:07'),
('250130', 'ID', 'Kec. Yokari', '2020-02-10 03:07:44'),
('250131', 'ID', 'Kec. Ravenirara', '2020-02-10 03:07:44'),
('250132', 'ID', 'Kec. Gresi Selatan', '2020-02-10 03:07:44'),
('250200', 'ID', 'Kab. Biak Numfor', '2019-06-10 04:42:07'),
('250201', 'ID', 'Kec. Numfor Barat', '2019-06-10 04:42:07'),
('250202', 'ID', 'Kec. Numfor Timur', '2019-06-10 04:42:07'),
('250203', 'ID', 'Kec. Padaido', '2019-06-10 04:42:07'),
('250204', 'ID', 'Kec. Biak Timur', '2019-06-10 04:42:07'),
('250205', 'ID', 'Kec. Biak Kota', '2019-06-10 04:42:07'),
('250206', 'ID', 'Kec. Samofa', '2019-06-10 04:42:07'),
('250207', 'ID', 'Kec. Yendidori', '2019-06-10 04:42:07'),
('250208', 'ID', 'Kec. Biak Utara', '2019-06-10 04:42:07'),
('250209', 'ID', 'Kec. Warsa', '2019-06-10 04:42:07'),
('250210', 'ID', 'Kec. Biak Barat', '2019-06-10 04:42:07'),
('250211', 'ID', 'Kec. Swandiwe', '2019-06-10 04:42:07'),
('250212', 'ID', 'Kec. Orkeri', '2019-06-10 04:42:07'),
('250213', 'ID', 'Kec. Bruyandori', '2019-06-10 04:42:07'),
('250214', 'ID', 'Kec. Poiru', '2019-06-10 04:42:07'),
('250215', 'ID', 'Kec. Ainando Padaido', '2019-06-10 04:42:07'),
('250216', 'ID', 'Kec. Oridek', '2019-06-10 04:42:07'),
('250217', 'ID', 'Kec. Andey', '2019-06-10 04:42:07'),
('250218', 'ID', 'Kec. Yawosi', '2019-06-10 04:42:07'),
('250219', 'ID', 'Kec. Bondifuar', '2019-06-10 04:42:07'),
('250300', 'ID', 'Kab. Yapen Waropen', '2019-06-10 04:42:07'),
('250304', 'ID', 'Kec. Yapen Timur', '2019-06-10 04:42:07'),
('250305', 'ID', 'Kec. Angkaisera', '2019-06-10 04:42:07'),
('250306', 'ID', 'Kec. Yapen Selatan', '2019-06-10 04:42:07'),
('250307', 'ID', 'Kec. Yapen Barat', '2019-06-10 04:42:07'),
('250308', 'ID', 'Kec. Poom', '2019-06-10 04:42:07'),
('250309', 'ID', 'Kec. Kosiwo', '2019-06-10 04:42:07'),
('250310', 'ID', 'Kec. Yapen Utara', '2019-06-10 04:42:07'),
('250311', 'ID', 'Kec. Raimbawi', '2019-06-10 04:42:07'),
('250312', 'ID', 'Kec. Teluk Ampimoi', '2019-06-10 04:42:07'),
('250313', 'ID', 'Kec. Kepulauan Ambai', '2019-06-10 04:42:07'),
('250314', 'ID', 'Kec. Wonawa', '2019-06-10 04:42:07'),
('250315', 'ID', 'Kec. Windesi', '2019-06-10 04:42:07'),
('250316', 'ID', 'Kec. Pulau Kurudu', '2020-02-10 03:07:44'),
('250317', 'ID', 'Kec. Pulau Yerui', '2020-02-10 03:07:44'),
('250700', 'ID', 'Kab. Merauke', '2019-06-10 04:42:07'),
('250701', 'ID', 'Kec. Kimaam', '2019-06-10 04:42:07'),
('250702', 'ID', 'Kec. Okaba', '2019-06-10 04:42:07'),
('250703', 'ID', 'Kec. Kurik', '2019-06-10 04:42:07'),
('250704', 'ID', 'Kec. Merauke', '2019-06-10 04:42:07'),
('250705', 'ID', 'Kec. Muting', '2019-06-10 04:42:07'),
('250724', 'ID', 'Kec. Distrik Ulilin', '2019-06-10 04:42:07'),
('250725', 'ID', 'Kec. Semangga', '2019-06-10 04:42:07'),
('250726', 'ID', 'Kec. Tanah Miring', '2019-06-10 04:42:07'),
('250727', 'ID', 'Kec. Jagebob', '2019-06-10 04:42:07'),
('250728', 'ID', 'Kec. Sota', '2019-06-10 04:42:07'),
('250729', 'ID', 'Kec. Eligobel', '2019-06-10 04:42:07'),
('250730', 'ID', 'Kec. Naukenjerai', '2019-06-10 04:42:07'),
('250731', 'ID', 'Kec. Animha', '2019-06-10 04:42:07'),
('250732', 'ID', 'Kec. Malind', '2019-06-10 04:42:07'),
('250733', 'ID', 'Kec. Tubang', '2019-06-10 04:42:07'),
('250734', 'ID', 'Kec. Ngunti', '2019-06-10 04:42:07'),
('250736', 'ID', 'Kec. Kaptel', '2019-06-10 04:42:07'),
('250737', 'ID', 'Kec. Tabonji', '2019-06-10 04:42:07'),
('250738', 'ID', 'Kec. Waan', '2019-06-10 04:42:07'),
('250739', 'ID', 'Kec. Ilwayab', '2019-06-10 04:42:07'),
('250800', 'ID', 'Kab. Jayawijaya', '2019-06-10 04:42:07'),
('250818', 'ID', 'Kec. Asologaima', '2019-06-10 04:42:07'),
('250819', 'ID', 'Kec. Kurulu', '2019-06-10 04:42:07'),
('250821', 'ID', 'Kec. Abenaho', '2019-06-10 04:42:07'),
('250839', 'ID', 'Kec. Walelagama', '2019-06-10 04:42:07'),
('250841', 'ID', 'Kec. Musatfak', '2019-06-10 04:42:07'),
('250843', 'ID', 'Kec. Asolokobal', '2019-06-10 04:42:07'),
('250848', 'ID', 'Kec. Pelebaga', '2019-06-10 04:42:07'),
('250849', 'ID', 'Kec. Yalengga', '2019-06-10 04:42:07'),
('250854', 'ID', 'Kec. Wamena', '2019-06-10 04:42:07'),
('250860', 'ID', 'Kec. Hubikosi', '2019-06-10 04:42:07'),
('250862', 'ID', 'Kec. Bolakme', '2019-06-10 04:42:07'),
('250863', 'ID', 'Kec. Wollo', '2019-06-10 04:42:07'),
('250864', 'ID', 'Kec. Wesaput', '2020-02-10 03:07:44'),
('250865', 'ID', 'Kec. Trikora', '2020-02-10 03:07:44'),
('250866', 'ID', 'Kec. Walaik', '2020-02-10 03:07:44'),
('250867', 'ID', 'Kec. Wouma', '2020-02-10 03:07:44'),
('250868', 'ID', 'Kec. Ibele', '2020-02-10 03:07:44'),
('250869', 'ID', 'Kec. Taelarek', '2020-02-10 03:07:44'),
('250870', 'ID', 'Kec. Wame', '2020-02-10 03:07:44'),
('250871', 'ID', 'Kec. Napua', '2020-02-10 03:07:44'),
('250872', 'ID', 'Kec. Hubikiak', '2020-02-10 03:07:44'),
('250873', 'ID', 'Kec. Itlay Hisage', '2020-02-10 03:07:44'),
('250874', 'ID', 'Kec. Pisugi', '2020-02-10 03:07:44'),
('250875', 'ID', 'Kec. Molagalome', '2020-02-10 03:07:44'),
('250876', 'ID', 'Kec. Tagineri', '2020-02-10 03:07:44'),
('250877', 'ID', 'Kec. Silo Karno Doga', '2020-02-10 03:07:44'),
('250878', 'ID', 'Kec. Siepkosi', '2020-02-10 03:07:44'),
('250879', 'ID', 'Kec. Usilimo', '2020-02-10 03:07:44'),
('250880', 'ID', 'Kec. Libarek', '2020-02-10 03:07:44'),
('250881', 'ID', 'Kec. Wadangku', '2020-02-10 03:07:44'),
('250882', 'ID', 'Kec. Koragi', '2020-02-10 03:07:44'),
('250883', 'ID', 'Kec. Tagime', '2020-02-10 03:07:44'),
('250884', 'ID', 'Kec. Piramid', '2020-02-10 03:07:44'),
('250885', 'ID', 'Kec. Muliama', '2020-02-10 03:07:44'),
('250886', 'ID', 'Kec. Bugi', '2020-02-10 03:07:44'),
('250887', 'ID', 'Kec. Bipiri', '2020-02-10 03:07:44'),
('250888', 'ID', 'Kec. Welesi', '2020-02-10 03:07:44'),
('250889', 'ID', 'Kec. Asotipo', '2020-02-10 03:07:44'),
('250890', 'ID', 'Kec. Maima', '2020-02-10 03:07:44'),
('250891', 'ID', 'Kec. Wita Waya', '2020-02-10 03:07:44'),
('250892', 'ID', 'Kec. Popugoba', '2020-02-10 03:07:44'),
('250900', 'ID', 'Kab. Nabire', '2019-06-10 04:42:07'),
('250905', 'ID', 'Kec. Uwapa', '2019-06-10 04:42:07'),
('250906', 'ID', 'Kec. Yaur', '2019-06-10 04:42:07'),
('250907', 'ID', 'Kec. Wanggar', '2019-06-10 04:42:07'),
('250908', 'ID', 'Kec. Nabire', '2019-06-10 04:42:07'),
('250909', 'ID', 'Kec. Napan', '2019-06-10 04:42:07'),
('250910', 'ID', 'Kec. Siriwo', '2019-06-10 04:42:07'),
('250911', 'ID', 'Kec. Teluk Umar', '2019-06-10 04:42:07'),
('250912', 'ID', 'Kec. Makimi', '2019-06-10 04:42:07'),
('250916', 'ID', 'Kec. Teluk Kimi', '2019-06-10 04:42:07'),
('250917', 'ID', 'Kec. Yarokibisay', '2019-06-10 04:42:07'),
('250918', 'ID', 'Kec. Kepulauan Moora', '2020-02-10 03:07:44'),
('250922', 'ID', 'Kec. Nabire Barat', '2019-06-10 04:42:07'),
('250923', 'ID', 'Kec. Wapoga', '2019-06-10 04:42:07'),
('250924', 'ID', 'Kec. Menou', '2020-02-10 03:07:44'),
('250990', 'ID', 'Dipha', '2019-06-10 04:42:07'),
('250999', 'ID', 'Yaro', '2019-06-10 04:42:07'),
('251000', 'ID', 'Kab. Paniai', '2019-06-10 04:42:07'),
('251003', 'ID', 'Kec. Paniai Timur', '2019-06-10 04:42:07'),
('251004', 'ID', 'Kec. Bibida', '2019-06-10 04:42:07'),
('251008', 'ID', 'Kec. Paniai Barat', '2019-06-10 04:42:07'),
('251009', 'ID', 'Kec. Bogoboida', '2019-06-10 04:42:07'),
('251014', 'ID', 'Kec. Yatamo', '2019-06-10 04:42:07'),
('251015', 'ID', 'Kec. Kebo', '2019-06-10 04:42:07'),
('251016', 'ID', 'Kec. Dumadama', '2019-06-10 04:42:07'),
('251018', 'ID', 'Kec. Ekadide', '2019-06-10 04:42:07'),
('251019', 'ID', 'Kec. Siriwo', '2019-06-10 04:42:07'),
('251020', 'ID', 'Kec. Weege Bino', '2020-02-10 03:07:44'),
('251090', 'ID', 'Aradide', '2019-06-10 04:42:07'),
('251091', 'ID', 'Kec. Aweida', '2020-02-10 03:07:44'),
('251092', 'ID', 'Kec. Baya Biru', '2020-02-10 03:07:44'),
('251093', 'ID', 'Kec. Deiyai Miyo', '2020-02-10 03:07:44'),
('251094', 'ID', 'Kec. Dogomo', '2020-02-10 03:07:44'),
('251095', 'ID', 'Kec. Muye', '2020-02-10 03:07:44'),
('251096', 'ID', 'Kec. Nakama', '2020-02-10 03:07:44'),
('251097', 'ID', 'Kec. Pugo Dagi', '2020-02-10 03:07:44'),
('251098', 'ID', 'Kec. Teluk Deya', '2020-02-10 03:07:44'),
('251099', 'ID', 'Kec. Topiyai', '2020-02-10 03:07:44'),
('251100', 'ID', 'Kab. Puncak Jaya', '2019-06-10 04:42:07'),
('251104', 'ID', 'Kec. Fawi', '2019-06-10 04:42:07'),
('251105', 'ID', 'Kec. Mulia', '2019-06-10 04:42:07'),
('251106', 'ID', 'Kec. Ilu', '2019-06-10 04:42:07'),
('251110', 'ID', 'Kec. Mewoluk', '2019-06-10 04:42:07'),
('251111', 'ID', 'Kec. Yamo', '2019-06-10 04:42:07'),
('251113', 'ID', 'Kec. Torere', '2019-06-10 04:42:07'),
('251114', 'ID', 'Kec. Jigonikme', '2019-06-10 04:42:07'),
('251115', 'ID', 'Kec. Tingginambut', '2019-06-10 04:42:07'),
('251116', 'ID', 'Kec. Nume', '2020-02-10 03:07:44'),
('251117', 'ID', 'Kec. Pagaleme', '2020-02-10 03:07:44'),
('251118', 'ID', 'Kec. Gurage', '2020-02-10 03:07:44'),
('251119', 'ID', 'Kec. Irimuli', '2020-02-10 03:07:44'),
('251120', 'ID', 'Kec. Muara', '2020-02-10 03:07:44'),
('251121', 'ID', 'Kec. Ilamburawi', '2020-02-10 03:07:44'),
('251122', 'ID', 'Kec. Yambi', '2020-02-10 03:07:44'),
('251123', 'ID', 'Kec. Lumo', '2020-02-10 03:07:44'),
('251124', 'ID', 'Kec. Molanikime', '2020-02-10 03:07:44'),
('251125', 'ID', 'Kec. Dokome', '2020-02-10 03:07:44'),
('251126', 'ID', 'Kec. Kalome', '2020-02-10 03:07:44'),
('251127', 'ID', 'Kec. Wanwi', '2020-02-10 03:07:44'),
('251128', 'ID', 'Kec. Yamoneri', '2020-02-10 03:07:44'),
('251129', 'ID', 'Kec. Waegi', '2020-02-10 03:07:44'),
('251130', 'ID', 'Kec. Nioga', '2020-02-10 03:07:44'),
('251131', 'ID', 'Kec. Gubume', '2020-02-10 03:07:44'),
('251132', 'ID', 'Kec. Taganombak', '2020-02-10 03:07:44'),
('251133', 'ID', 'Kec. Dagai', '2020-02-10 03:07:44'),
('251134', 'ID', 'Kec. Kiyage', '2020-02-10 03:07:44'),
('251200', 'ID', 'Kab. Mimika', '2019-06-10 04:42:07'),
('251201', 'ID', 'Kec. Mimika Barat', '2019-06-10 04:42:07'),
('251202', 'ID', 'Kec. Mimika Timur', '2019-06-10 04:42:07'),
('251203', 'ID', 'Kec. Mimika Baru', '2019-06-10 04:42:07'),
('251204', 'ID', 'Kec. Agimuga', '2019-06-10 04:42:07'),
('251205', 'ID', 'Kec. Mimika Barat Jauh', '2019-06-10 04:42:07'),
('251206', 'ID', 'Kec. Mimika Barat Tengah', '2019-06-10 04:42:07'),
('251209', 'ID', 'Kec. Tembagapura', '2019-06-10 04:42:07'),
('251210', 'ID', 'Kec. Jila', '2019-06-10 04:42:07'),
('251211', 'ID', 'Kec. Jita', '2019-06-10 04:42:07'),
('251212', 'ID', 'Kec. Kuala Kencana', '2019-06-10 04:42:07'),
('251213', 'ID', 'Kec. Mimika Timur Tengah', '2019-06-10 04:42:07'),
('251214', 'ID', 'Kec. Mimika Timur Jauh', '2019-06-10 04:42:07'),
('251215', 'ID', 'Kec. Mimika Tengah', '2020-02-10 03:07:44'),
('251216', 'ID', 'Kec. Kwamki Narama', '2020-02-10 03:07:44'),
('251217', 'ID', 'Kec. Hoya', '2020-02-10 03:07:44'),
('251218', 'ID', 'Kec. Iwaka', '2020-02-10 03:07:44'),
('251219', 'ID', 'Kec. Wania', '2020-02-10 03:07:44'),
('251220', 'ID', 'Kec. Amar', '2020-02-10 03:07:44'),
('251221', 'ID', 'Kec. Alama', '2020-02-10 03:07:44'),
('251300', 'ID', 'Kab. Boven Digoel', '2019-06-10 04:42:07'),
('251301', 'ID', 'Kec. Jair', '2019-06-10 04:42:07'),
('251302', 'ID', 'Kec. Mindiptana', '2019-06-10 04:42:07'),
('251303', 'ID', 'Kec. Mandobo', '2019-06-10 04:42:07'),
('251304', 'ID', 'Kec. Kouh', '2019-06-10 04:42:07'),
('251305', 'ID', 'Kec. Waropko', '2019-06-10 04:42:07'),
('251306', 'ID', 'Kec. Distrik Bomakia', '2019-06-10 04:42:07'),
('251307', 'ID', 'Kec. Subur', '2019-06-10 04:42:07'),
('251308', 'ID', 'Kec. Iniyandit', '2019-06-10 04:42:07'),
('251309', 'ID', 'Kec. Fofi', '2019-06-10 04:42:07'),
('251310', 'ID', 'Kec. Arimop', '2019-06-10 04:42:07'),
('251311', 'ID', 'Kec. Firiwage', '2019-06-10 04:42:07'),
('251312', 'ID', 'Kec. Manggelum', '2019-06-10 04:42:07'),
('251313', 'ID', 'Kec. Yaniruma', '2019-06-10 04:42:07'),
('251314', 'ID', 'Kec. Ambatkwi', '2019-06-10 04:42:07'),
('251315', 'ID', 'Kombut', '2019-06-10 04:42:07'),
('251316', 'ID', 'Kec. Ninati', '2020-02-10 03:07:44'),
('251317', 'ID', 'Kec. Sesnuk', '2020-02-10 03:07:44'),
('251318', 'ID', 'Kec. Ki', '2020-02-10 03:07:44'),
('251319', 'ID', 'Kec. Kombay', '2020-02-10 03:07:44'),
('251320', 'ID', 'Kec. Kawagit', '2020-02-10 03:07:44'),
('251321', 'ID', 'Kec. Syahcame', '2020-02-10 03:07:44'),
('251400', 'ID', 'Kab. Mappi', '2019-06-10 04:42:07'),
('251401', 'ID', 'Kec. Nambioman Bapai', '2019-06-10 04:42:07'),
('251402', 'ID', 'Kec. Edera', '2019-06-10 04:42:07'),
('251403', 'ID', 'Kec. Obaa', '2019-06-10 04:42:07'),
('251404', 'ID', 'Kec. Haju', '2019-06-10 04:42:07'),
('251405', 'ID', 'Kec. Assue', '2019-06-10 04:42:07'),
('251406', 'ID', 'Kec. Citakmitak', '2019-06-10 04:42:07'),
('251407', 'ID', 'Kec. Minyamur', '2019-06-10 04:42:07'),
('251408', 'ID', 'Kec. Venaha', '2019-06-10 04:42:07'),
('251409', 'ID', 'Kec. Passue', '2019-06-10 04:42:07'),
('251410', 'ID', 'Kec. Kaibar', '2019-06-10 04:42:07'),
('251411', 'ID', 'Kec. Syahcame', '2020-02-10 03:07:44'),
('251412', 'ID', 'Kec. Yakomi', '2020-02-10 03:07:44'),
('251413', 'ID', 'Kec. Bamgi', '2020-02-10 03:07:44'),
('251414', 'ID', 'Kec. Passue Bawah', '2020-02-10 03:07:44'),
('251415', 'ID', 'Kec. Ti Zain', '2020-02-10 03:07:44'),
('251500', 'ID', 'Kab. Asmat', '2019-06-10 04:42:07'),
('251501', 'ID', 'Kec. Pantai Kasuari', '2019-06-10 04:42:07'),
('251502', 'ID', 'Kec. Fayit', '2019-06-10 04:42:07'),
('251503', 'ID', 'Kec. Atsy', '2019-06-10 04:42:07'),
('251504', 'ID', 'Kec. Suator', '2019-06-10 04:42:07'),
('251505', 'ID', 'Kec. Akat', '2019-06-10 04:42:07'),
('251506', 'ID', 'Kec. Agats', '2019-06-10 04:42:07'),
('251507', 'ID', 'Kec. Sawaerma', '2019-06-10 04:42:07'),
('251508', 'ID', 'Kec. Kopay', '2020-02-10 03:07:44'),
('251509', 'ID', 'Kec. Safan', '2020-02-10 03:07:44'),
('251510', 'ID', 'Kec. Sirets', '2020-02-10 03:07:44'),
('251511', 'ID', 'Kec. Joerat', '2020-02-10 03:07:44'),
('251512', 'ID', 'Kec. Pulau Tiga', '2020-02-10 03:07:44'),
('251513', 'ID', 'Kec. Der Koumur', '2020-02-10 03:07:44'),
('251514', 'ID', 'Kec. Ayip', '2020-02-10 03:07:44'),
('251515', 'ID', 'Kec. Betcbamu', '2020-02-10 03:07:44'),
('251516', 'ID', 'Kec. Suru-Suru', '2020-02-10 03:07:44'),
('251517', 'ID', 'Kec. Kolf Braza', '2020-02-10 03:07:44'),
('251518', 'ID', 'Kec. Jetsy', '2020-02-10 03:07:44'),
('251519', 'ID', 'Kec. Unir Sirau', '2020-02-10 03:07:44'),
('251520', 'ID', 'Kec. Sirau', '2020-02-10 03:07:44'),
('251600', 'ID', 'Kab. Yahukimo', '2019-06-10 04:42:07'),
('251601', 'ID', 'Kec. Kurima', '2019-06-10 04:42:07'),
('251602', 'ID', 'Kec. Ninia', '2019-06-10 04:42:07'),
('251603', 'ID', 'Kec. Anggruk', '2019-06-10 04:42:07'),
('251604', 'ID', 'Kec. Dekai', '2019-06-10 04:42:07'),
('251605', 'ID', 'Kec. Obio', '2019-06-10 04:42:07'),
('251606', 'ID', 'Kec. Suru Suru', '2019-06-10 04:42:07'),
('251607', 'ID', 'Kec. Wusuma', '2019-06-10 04:42:07'),
('251608', 'ID', 'Kec. Amuma', '2019-06-10 04:42:07'),
('251609', 'ID', 'Kec. Wusaik', '2019-06-10 04:42:07'),
('251610', 'ID', 'Kec. Pasema', '2019-06-10 04:42:07'),
('251611', 'ID', 'Kec. Hogio', '2019-06-10 04:42:07'),
('251612', 'ID', 'Kec. Mogi', '2019-06-10 04:42:07'),
('251613', 'ID', 'Kec. Soba', '2019-06-10 04:42:07'),
('251614', 'ID', 'Kec. Werima', '2019-06-10 04:42:07'),
('251615', 'ID', 'Kec. Tangma', '2019-06-10 04:42:07'),
('251616', 'ID', 'Kec. Ukha', '2019-06-10 04:42:07'),
('251617', 'ID', 'Kec. Panggema', '2019-06-10 04:42:07'),
('251618', 'ID', 'Kec. Kosarek', '2019-06-10 04:42:07'),
('251619', 'ID', 'Kec. Nipsan', '2019-06-10 04:42:07'),
('251620', 'ID', 'Kec. Ubahak', '2019-06-10 04:42:07'),
('251621', 'ID', 'Kec. Pronggoli', '2019-06-10 04:42:07'),
('251622', 'ID', 'Kec. Walma', '2019-06-10 04:42:07'),
('251623', 'ID', 'Kec. Yahuliambut', '2019-06-10 04:42:07'),
('251624', 'ID', 'Kec. Puldama', '2019-06-10 04:42:07'),
('251625', 'ID', 'Kec. Hereapingi', '2019-06-10 04:42:07'),
('251626', 'ID', 'Kec. Ubahili', '2019-06-10 04:42:07'),
('251627', 'ID', 'Kec. Talambo', '2019-06-10 04:42:07'),
('251628', 'ID', 'Kec. Endomen', '2019-06-10 04:42:07'),
('251629', 'ID', 'Kec. Kona', '2019-06-10 04:42:07'),
('251630', 'ID', 'Kec. Dirwemna', '2019-06-10 04:42:07'),
('251631', 'ID', 'Kec. Holuwon', '2019-06-10 04:42:07'),
('251632', 'ID', 'Kec. Lolat', '2019-06-10 04:42:07'),
('251633', 'ID', 'Kec. Soloikma', '2019-06-10 04:42:07'),
('251634', 'ID', 'Kec. Sela', '2019-06-10 04:42:07'),
('251635', 'ID', 'Kec. Korupun', '2019-06-10 04:42:07'),
('251636', 'ID', 'Kec. Langda', '2019-06-10 04:42:07'),
('251637', 'ID', 'Kec. Bomela', '2019-06-10 04:42:07'),
('251638', 'ID', 'Kec. Suntamon', '2019-06-10 04:42:07'),
('251639', 'ID', 'Kec. Seredela', '2019-06-10 04:42:07'),
('251640', 'ID', 'Kec. Sobaham', '2019-06-10 04:42:07'),
('251641', 'ID', 'Kec. Kabianggama', '2019-06-10 04:42:07'),
('251642', 'ID', 'Kec. Kwelamdua', '2019-06-10 04:42:07'),
('251643', 'ID', 'Kec. Kwikma', '2019-06-10 04:42:07'),
('251644', 'ID', 'Kec. Hilipuk', '2019-06-10 04:42:07'),
('251645', 'ID', 'Kec. Duram', '2019-06-10 04:42:07'),
('251646', 'ID', 'Kec. Yogosem', '2019-06-10 04:42:07'),
('251647', 'ID', 'Kec. Kayo', '2019-06-10 04:42:07'),
('251648', 'ID', 'Kec. Sumo', '2019-06-10 04:42:07'),
('251649', 'ID', 'Kec. Silimo', '2019-06-10 04:42:07'),
('251650', 'ID', 'Kec. Samenage', '2019-06-10 04:42:07'),
('251651', 'ID', 'Kec. Nalca', '2019-06-10 04:42:07'),
('251652', 'ID', 'Kec. Tolikapura 2', '2020-02-10 03:07:44'),
('251699', 'ID', 'Seradala', '2019-06-10 04:42:07'),
('251700', 'ID', 'Kab. Pegunungan Bintang', '2019-06-10 04:42:07'),
('251701', 'ID', 'Kec. Okiwur', '2019-06-10 04:42:07'),
('251702', 'ID', 'Kec. Oksibil', '2019-06-10 04:42:07'),
('251703', 'ID', 'Kec. Borme', '2019-06-10 04:42:07'),
('251704', 'ID', 'Kec. Okbibab', '2019-06-10 04:42:07'),
('251705', 'ID', 'Kec. Kiwirok', '2019-06-10 04:42:07'),
('251706', 'ID', 'Kec. Batom', '2019-06-10 04:42:07'),
('251707', 'ID', 'Kec. Pepera', '2019-06-10 04:42:07'),
('251708', 'ID', 'Kec. Bime', '2019-06-10 04:42:07'),
('251709', 'ID', 'Kec. Aboy', '2019-06-10 04:42:07'),
('251710', 'ID', 'Kec. Kiwirok Timur', '2019-06-10 04:42:07'),
('251711', 'ID', 'Kec. Kawor', '2019-06-10 04:42:07'),
('251712', 'ID', 'Kec. Tarup', '2019-06-10 04:42:07'),
('251713', 'ID', 'Kec. Alemsom', '2019-06-10 04:42:07'),
('251714', 'ID', 'Kec. Serambakon', '2019-06-10 04:42:07'),
('251715', 'ID', 'Kec. Kalomdom', '2019-06-10 04:42:07'),
('251716', 'ID', 'Kec. Oksop', '2019-06-10 04:42:07'),
('251717', 'ID', 'Kec. Epumek', '2019-06-10 04:42:07'),
('251718', 'ID', 'Kec. Weime', '2019-06-10 04:42:07'),
('251719', 'ID', 'Kec. Okbab', '2019-06-10 04:42:07'),
('251720', 'ID', 'Kec. Teiraplu', '2019-06-10 04:42:07'),
('251721', 'ID', 'Kec. Sopsebang', '2019-06-10 04:42:07'),
('251722', 'ID', 'Kec. Hokhika', '2019-06-10 04:42:07'),
('251723', 'ID', 'Kec.Oklip', '2019-06-10 04:42:07'),
('251724', 'ID', 'Kec. Oksamol', '2019-06-10 04:42:07'),
('251725', 'ID', 'Kec. Bemta', '2019-06-10 04:42:07'),
('251726', 'ID', 'Okbape', '2019-06-10 04:42:07'),
('251727', 'ID', 'Ok Aom', '2019-06-10 04:42:07'),
('251728', 'ID', 'Awinbon', '2019-06-10 04:42:07'),
('251729', 'ID', 'Batani', '2019-06-10 04:42:07'),
('251730', 'ID', 'Murkim', '2019-06-10 04:42:07'),
('251731', 'ID', 'Mofinop', '2019-06-10 04:42:07'),
('251732', 'ID', 'Jetfa', '2019-06-10 04:42:07'),
('251733', 'ID', 'Nongme', '2019-06-10 04:42:07'),
('251734', 'ID', 'Pamek', '2019-06-10 04:42:07'),
('251737', 'ID', 'Iwur', '2019-06-10 04:42:07'),
('251790', 'ID', 'Okbemtau', '2019-06-10 04:42:07'),
('251791', 'ID', 'Oksebang', '2019-06-10 04:42:07'),
('251800', 'ID', 'Kab. Tolikara', '2019-06-10 04:42:07'),
('251801', 'ID', 'Kec. Kanggime', '2019-06-10 04:42:07'),
('251802', 'ID', 'Kec. Karubaga', '2019-06-10 04:42:07'),
('251803', 'ID', 'Kec. Bokondini', '2019-06-10 04:42:07'),
('251804', 'ID', 'Kec. Kembu', '2019-06-10 04:42:07'),
('251805', 'ID', 'Kec. Goyage', '2019-06-10 04:42:07'),
('251806', 'ID', 'Kec. Kubu', '2019-06-10 04:42:07'),
('251807', 'ID', 'Kec. Geya', '2019-06-10 04:42:07'),
('251808', 'ID', 'Kec. Numba', '2019-06-10 04:42:07'),
('251809', 'ID', 'Kec. Dundu', '2019-06-10 04:42:07'),
('251810', 'ID', 'Kec. Gudage', '2019-06-10 04:42:07'),
('251811', 'ID', 'Kec. Timori', '2019-06-10 04:42:07'),
('251812', 'ID', 'Kec. Konda', '2019-06-10 04:42:07'),
('251813', 'ID', 'Kec. Nelawi', '2019-06-10 04:42:07'),
('251814', 'ID', 'Kec. Kuari', '2019-06-10 04:42:07'),
('251815', 'ID', 'Kec. Bokoneri', '2019-06-10 04:42:07'),
('251816', 'ID', 'Kec. Bewani', '2019-06-10 04:42:07'),
('251817', 'ID', 'Kec. Komboneri', '2019-06-10 04:42:07'),
('251818', 'ID', 'Kec. Nabunage', '2019-06-10 04:42:07'),
('251819', 'ID', 'Kec. Wakuo', '2019-06-10 04:42:07'),
('251820', 'ID', 'Kec. Nunggawi', '2019-06-10 04:42:07'),
('251821', 'ID', 'Kec. Woniki', '2019-06-10 04:42:07'),
('251822', 'ID', 'Kec. Wunin', '2019-06-10 04:42:07'),
('251823', 'ID', 'Kec. Wina', '2019-06-10 04:42:07'),
('251824', 'ID', 'Kec. Panaga', '2019-06-10 04:42:07'),
('251825', 'ID', 'Kec. Poganeri', '2019-06-10 04:42:07'),
('251826', 'ID', 'Kec. Dow', '2019-06-10 04:42:07'),
('251827', 'ID', 'Kec. Wari/Taiyeve', '2019-06-10 04:42:07'),
('251830', 'ID', 'Kec. Umagi', '2019-06-10 04:42:07'),
('251831', 'ID', 'Kec. Gilungbandu', '2019-06-10 04:42:07'),
('251832', 'ID', 'Kec. Yuneri', '2019-06-10 04:42:07'),
('251833', 'ID', 'Kec. Taginire', '2019-06-10 04:42:07'),
('251834', 'ID', 'Kec. Egiam', '2019-06-10 04:42:07'),
('251835', 'ID', 'Kec. Air Garam', '2019-06-10 04:42:07'),
('251836', 'ID', 'Gika', '2019-06-10 04:42:07'),
('251837', 'ID', 'Telenggeme', '2019-06-10 04:42:07'),
('251838', 'ID', 'Anawi', '2019-06-10 04:42:07'),
('251839', 'ID', 'Wenam', '2019-06-10 04:42:07'),
('251840', 'ID', 'Wugi', '2019-06-10 04:42:07'),
('251841', 'ID', 'Danime', '2019-06-10 04:42:07'),
('251842', 'ID', 'Tagime', '2019-06-10 04:42:07'),
('251843', 'ID', 'tidak ada', '2019-06-10 04:42:07'),
('251844', 'ID', 'Aweku', '2019-06-10 04:42:07'),
('251845', 'ID', 'Bogonuk', '2019-06-10 04:42:07'),
('251846', 'ID', 'Li Anogomma', '2019-06-10 04:42:07'),
('251847', 'ID', 'Biuk', '2019-06-10 04:42:07'),
('251848', 'ID', 'Yuko', '2019-06-10 04:42:07'),
('251890', 'ID', 'Kamboneri', '2019-06-10 04:42:07'),
('251891', 'ID', 'Kec. Kai', '2020-02-10 03:07:44'),
('251900', 'ID', 'Kab. Sarmi', '2019-06-10 04:42:07'),
('251902', 'ID', 'Kec. Pantai Timur', '2019-06-10 04:42:07'),
('251903', 'ID', 'Kec. Bonggo', '2019-06-10 04:42:07'),
('251904', 'ID', 'Kec. Tor Atas', '2019-06-10 04:42:07'),
('251905', 'ID', 'Kec. Sarmi', '2019-06-10 04:42:07'),
('251906', 'ID', 'Kec. Pantai Barat', '2019-06-10 04:42:07'),
('251907', 'ID', 'Kec. Pantai Timur Bagian Barat', '2019-06-10 04:42:07'),
('251910', 'ID', 'Kec. Bonggo Timur', '2019-06-10 04:42:07'),
('251911', 'ID', 'Kec. Sarmi Timur', '2019-06-10 04:42:07'),
('251912', 'ID', 'Kec. Sarmi Barat', '2019-06-10 04:42:07'),
('251913', 'ID', 'Kec. Apawert Hulu', '2019-06-10 04:42:07'),
('251914', 'ID', 'Sarmi Selatan', '2019-06-10 04:42:07'),
('251915', 'ID', 'Kec. Ismari', '2020-02-10 03:07:44'),
('251916', 'ID', 'Kec. Muara Tor', '2020-02-10 03:07:44'),
('251917', 'ID', 'Kec. Sobey', '2020-02-10 03:07:44'),
('251918', 'ID', 'Kec. Walani', '2020-02-10 03:07:44'),
('252000', 'ID', 'Kab. Keerom', '2019-06-10 04:42:07'),
('252001', 'ID', 'Kec. Web', '2019-06-10 04:42:07'),
('252002', 'ID', 'Kec. Senggi', '2019-06-10 04:42:07'),
('252003', 'ID', 'Kec. Waris', '2019-06-10 04:42:07'),
('252004', 'ID', 'Kec. Arso', '2019-06-10 04:42:07'),
('252005', 'ID', 'Kec. Skamto', '2019-06-10 04:42:07'),
('252006', 'ID', 'Kec. Towe Hitam', '2019-06-10 04:42:07'),
('252007', 'ID', 'Kec. Arso Timur', '2019-06-10 04:42:07'),
('252008', 'ID', 'Kec. Arso Barat', '2020-02-10 03:07:44'),
('252009', 'ID', 'Kec. Kaisenar', '2020-02-10 03:07:44'),
('252010', 'ID', 'Kec. Mannem', '2020-02-10 03:07:44'),
('252011', 'ID', 'Kec. Yaffi', '2020-02-10 03:07:44'),
('252012', 'ID', 'Kec. Yaffi', '2020-02-10 03:07:44'),
('252600', 'ID', 'Kab. Waropen', '2019-06-10 04:42:07'),
('252601', 'ID', 'Kec. Waropen Bawah', '2019-06-10 04:42:07'),
('252602', 'ID', 'Kec. Masirei', '2019-06-10 04:42:07'),
('252604', 'ID', 'Kec. Inggerus', '2019-06-10 04:42:07'),
('252605', 'ID', 'Kec. Ureifaisei', '2019-06-10 04:42:07'),
('252607', 'ID', 'Kec. Risei Sayati', '2019-06-10 04:42:07'),
('252609', 'ID', 'Kec. Kirihi', '2019-06-10 04:42:07'),
('252610', 'ID', 'Wapoga', '2019-06-10 04:42:07'),
('252611', 'ID', 'Kec. Demba', '2020-02-10 03:07:44'),
('252612', 'ID', 'Kec. Oudate', '2020-02-10 03:07:44'),
('252613', 'ID', 'Kec. Wonti', '2020-02-10 03:07:44'),
('252614', 'ID', 'Kec. Soyoi Mambai', '2020-02-10 03:07:44'),
('252615', 'ID', 'Kec. Walani', '2020-02-10 03:07:44'),
('252700', 'ID', 'Kab. Supiori', '2019-06-10 04:42:07'),
('252701', 'ID', 'Kec. Supiori Selatan', '2019-06-10 04:42:07'),
('252702', 'ID', 'Kec. Yenggarbun', '2019-06-10 04:42:07'),
('252703', 'ID', 'Kec. Supiori Timur', '2019-06-10 04:42:07'),
('252704', 'ID', 'Kec. Kepulauan Aruri', '2019-06-10 04:42:07'),
('252705', 'ID', 'Kec. Supiori Barat', '2019-06-10 04:42:07'),
('252790', 'ID', 'Supiori Utara', '2019-06-10 04:42:07'),
('252800', 'ID', 'Kab. Memberamo Raya', '2019-06-10 04:42:07'),
('252803', 'ID', 'Kec. Waropen Atas', '2019-06-10 04:42:07'),
('252804', 'ID', 'Kec. Mamberamo Ilir', '2019-06-10 04:42:07'),
('252805', 'ID', 'Kec. Mamberamo Tengah', '2019-06-10 04:42:07'),
('252806', 'ID', 'Kec. Mamberamo Tengah Timur', '2019-06-10 04:42:07'),
('252807', 'ID', 'Kec. Rufaer', '2019-06-10 04:42:07'),
('252808', 'ID', 'Kec. Mamberamo Ulu', '2019-06-10 04:42:07'),
('252809', 'ID', 'Kec. Benuki', '2019-06-10 04:42:07'),
('252810', 'ID', 'Kec. Sawai', '2019-06-10 04:42:07'),
('252890', 'ID', 'Mamberamo Hulu', '2019-06-10 04:42:07'),
('252891', 'ID', 'Mamberamo Hilir', '2019-06-10 04:42:07'),
('252900', 'ID', 'Kab. Nduga', '2019-06-10 04:42:07'),
('252901', 'ID', 'Kec. Wosak', '2019-06-10 04:42:07'),
('252902', 'ID', 'Kec. Kenyam', '2019-06-10 04:42:07'),
('252903', 'ID', 'Kec. Geselma', '2019-06-10 04:42:07'),
('252904', 'ID', 'Kec. Mapenduma', '2019-06-10 04:42:07'),
('252905', 'ID', 'Kec. Mugi', '2019-06-10 04:42:07'),
('252906', 'ID', 'Kec. Yigi', '2019-06-10 04:42:07'),
('252907', 'ID', 'Kec. Mbuwa', '2019-06-10 04:42:07'),
('252908', 'ID', 'Kec. Gearek', '2019-06-10 04:42:07'),
('252909', 'ID', 'Kec. Kegayem', '2020-02-10 03:07:44'),
('252910', 'ID', 'Kec. Paro', '2020-02-10 03:07:44'),
('252911', 'ID', 'Kec. Mebarok', '2020-02-10 03:07:44'),
('252912', 'ID', 'Kec. Kilmid', '2020-02-10 03:07:44'),
('252913', 'ID', 'Kec. Koroptak', '2020-02-10 03:07:44'),
('252914', 'ID', 'Kec. Yenggelo', '2020-02-10 03:07:44'),
('252915', 'ID', 'Kec. Alama', '2020-02-10 03:07:44'),
('252916', 'ID', 'Kec. Yal', '2020-02-10 03:07:44'),
('252917', 'ID', 'Kec. Mam', '2020-02-10 03:07:44'),
('252918', 'ID', 'Kec. Dal', '2020-02-10 03:07:44'),
('252919', 'ID', 'Kec. Nirkuri', '2020-02-10 03:07:44'),
('252920', 'ID', 'Kec. Iniye', '2020-02-10 03:07:44'),
('252921', 'ID', 'Kec. Mbua Tengah', '2020-02-10 03:07:44'),
('252922', 'ID', 'Kec. Inikgal', '2020-02-10 03:07:44'),
('252923', 'ID', 'Kec. Mbulmu Yalma', '2020-02-10 03:07:44'),
('252924', 'ID', 'Kec. Embetpen', '2020-02-10 03:07:44'),
('252925', 'ID', 'Kec. Kora', '2020-02-10 03:07:44'),
('252926', 'ID', 'Kec. Wusi', '2020-02-10 03:07:44'),
('252927', 'ID', 'Kec. Nenggeagin', '2020-02-10 03:07:44'),
('252928', 'ID', 'Kec. Pasir Putih', '2020-02-10 03:07:44'),
('252929', 'ID', 'Kec. Pija', '2020-02-10 03:07:44'),
('252930', 'ID', 'Kec. Moba', '2020-02-10 03:07:44'),
('252931', 'ID', 'Kec. Wutpaga', '2020-02-10 03:07:44'),
('252932', 'ID', 'Kec. Krepkuri', '2020-02-10 03:07:44'),
('253000', 'ID', 'Kab. Lanny Jaya', '2019-06-10 04:42:07'),
('253001', 'ID', 'Kec. Makki', '2019-06-10 04:42:07'),
('253002', 'ID', 'Kec. Pirime', '2019-06-10 04:42:07'),
('253003', 'ID', 'Kec. Tiom', '2019-06-10 04:42:07'),
('253004', 'ID', 'Kec. Balingga', '2019-06-10 04:42:07'),
('253005', 'ID', 'Kec. Kuyawage', '2019-06-10 04:42:07'),
('253006', 'ID', 'Kec. Malagaineri', '2019-06-10 04:42:07'),
('253007', 'ID', 'Kec. Tiomneri', '2019-06-10 04:42:07'),
('253008', 'ID', 'Kec. Dimba', '2019-06-10 04:42:07'),
('253009', 'ID', 'Kec. Gamelia', '2019-06-10 04:42:07'),
('253010', 'ID', 'Kec. Poga', '2019-06-10 04:42:07'),
('253011', 'ID', 'Kec. Awina', '2020-02-10 03:07:44'),
('253012', 'ID', 'Kec. Ayumnati', '2020-02-10 03:07:44'),
('253013', 'ID', 'Kec. Balingga Barat', '2020-02-10 03:07:44'),
('253014', 'ID', 'Kec. Bruwa', '2020-02-10 03:07:44'),
('253015', 'ID', 'Kec. Buguk Gona', '2020-02-10 03:07:44'),
('253016', 'ID', 'Kec. Gelok Beam', '2020-02-10 03:07:44'),
('253017', 'ID', 'Kec. Goa Balim', '2020-02-10 03:07:44'),
('253018', 'ID', 'Kec. Gollo', '2020-02-10 03:07:44'),
('253019', 'ID', 'Kec. Guna', '2020-02-10 03:07:44'),
('253020', 'ID', 'Kec. Gupura', '2020-02-10 03:07:44'),
('253021', 'ID', 'Kec. Karu', '2020-02-10 03:07:44'),
('253022', 'ID', 'Kec. Kelulome', '2020-02-10 03:07:44'),
('253023', 'ID', 'Kec. Kolawa', '2020-02-10 03:07:44'),
('253024', 'ID', 'Kec. Kuly Lanny', '2020-02-10 03:07:44'),
('253025', 'ID', 'Kec. Lannyna', '2020-02-10 03:07:44'),
('253026', 'ID', 'Kec. Melagi', '2020-02-10 03:07:44'),
('253027', 'ID', 'Kec. Milimbo', '2020-02-10 03:07:44'),
('253028', 'ID', 'Kec. Mokoni', '2020-02-10 03:07:44'),
('253029', 'ID', 'Kec. Muara', '2020-02-10 03:07:44'),
('253030', 'ID', 'Kec. Nikogwe', '2020-02-10 03:07:44'),
('253031', 'ID', 'Kec. Niname', '2020-02-10 03:07:44'),
('253032', 'ID', 'Kec. Nogi', '2020-02-10 03:07:44'),
('253033', 'ID', 'Kec. Tiom Ollo', '2020-02-10 03:07:44'),
('253034', 'ID', 'Kec. Wano Barat', '2020-02-10 03:07:44'),
('253035', 'ID', 'Kec. Wereka', '2020-02-10 03:07:44');
INSERT INTO `dikti_wilayah` (`id_wilayah`, `id_negara`, `nama_wilayah`, `dikti_wilayah_timestamp`) VALUES
('253036', 'ID', 'Kec. Wiringgambut', '2020-02-10 03:07:44'),
('253037', 'ID', 'Kec. Yiginua', '2020-02-10 03:07:44'),
('253038', 'ID', 'Kec. Yiluk', '2020-02-10 03:07:44'),
('253039', 'ID', 'Kec. Yugungwi', '2020-02-10 03:07:44'),
('253100', 'ID', 'Kab. Membramo Tengah', '2019-06-10 04:42:07'),
('253101', 'ID', 'Kec. Kobakma', '2019-06-10 04:42:07'),
('253102', 'ID', 'Kec. Ilugwa', '2019-06-10 04:42:07'),
('253103', 'ID', 'Kec. Kelila', '2019-06-10 04:42:07'),
('253104', 'ID', 'Kec. Eragayam', '2019-06-10 04:42:07'),
('253105', 'ID', 'Kec. Megambilis', '2019-06-10 04:42:07'),
('253200', 'ID', 'Kab. Yalimo', '2019-06-10 04:42:07'),
('253201', 'ID', 'Kec. Welarek', '2019-06-10 04:42:07'),
('253202', 'ID', 'Kec. Apalapsili', '2019-06-10 04:42:07'),
('253203', 'ID', 'Kec. Abenaho', '2019-06-10 04:42:07'),
('253204', 'ID', 'Kec. Elelim', '2019-06-10 04:42:07'),
('253205', 'ID', 'Kec. Benawa', '2019-06-10 04:42:07'),
('253300', 'ID', 'kab. Puncak', '2019-06-10 04:42:07'),
('253301', 'ID', 'Kec. Agadugume', '2019-06-10 04:42:07'),
('253302', 'ID', 'Kec. Gome', '2019-06-10 04:42:07'),
('253303', 'ID', 'Kec. Ilaga', '2019-06-10 04:42:07'),
('253304', 'ID', 'Kec. Sinak', '2019-06-10 04:42:07'),
('253305', 'ID', 'Kec. Pogoma', '2019-06-10 04:42:07'),
('253306', 'ID', 'Kec. Wangbe', '2019-06-10 04:42:07'),
('253307', 'ID', 'Kec. Beoga', '2019-06-10 04:42:07'),
('253308', 'ID', 'Kec. Doufo', '2019-06-10 04:42:07'),
('253309', 'ID', 'Kec. Dervos', '2020-02-10 03:07:44'),
('253310', 'ID', 'Kec. Beoga Barat', '2020-02-10 03:07:44'),
('253311', 'ID', 'Kec. Beoga Timur', '2020-02-10 03:07:44'),
('253312', 'ID', 'Kec. Ogamanim', '2020-02-10 03:07:44'),
('253313', 'ID', 'Kec. Kembru', '2020-02-10 03:07:44'),
('253314', 'ID', 'Kec. Bina', '2020-02-10 03:07:44'),
('253315', 'ID', 'Kec. Sinak Barat', '2020-02-10 03:07:44'),
('253316', 'ID', 'Kec. Mage`abume', '2020-02-10 03:07:44'),
('253317', 'ID', 'Kec. Yugumuak', '2020-02-10 03:07:44'),
('253318', 'ID', 'Kec. Ilaga Utara', '2020-02-10 03:07:44'),
('253319', 'ID', 'Kec. Mabugi', '2020-02-10 03:07:44'),
('253320', 'ID', 'Kec. Omukia', '2020-02-10 03:07:44'),
('253321', 'ID', 'Kec. Lambewi', '2020-02-10 03:07:44'),
('253322', 'ID', 'Kec. Oneri', '2020-02-10 03:07:44'),
('253323', 'ID', 'Kec. Amungkalpia', '2020-02-10 03:07:44'),
('253324', 'ID', 'Kec. Gome Utara', '2020-02-10 03:07:44'),
('253325', 'ID', 'Kec. Erelmakawia', '2020-02-10 03:07:44'),
('253400', 'ID', 'Kab. Dogiyai', '2019-06-10 04:42:07'),
('253401', 'ID', 'Kec. Piyaiye', '2019-06-10 04:42:07'),
('253402', 'ID', 'Kec. Mapia Barat', '2019-06-10 04:42:07'),
('253403', 'ID', 'Kec. Mapia', '2019-06-10 04:42:07'),
('253404', 'ID', 'Kec. Dogiyai', '2019-06-10 04:42:07'),
('253405', 'ID', 'Kec. Kamu Selatan', '2019-06-10 04:42:07'),
('253406', 'ID', 'Kec. Kamu', '2019-06-10 04:42:07'),
('253407', 'ID', 'Kec. Mapia Tengah', '2019-06-10 04:42:07'),
('253408', 'ID', 'Kec. Kamu Tmur', '2019-06-10 04:42:07'),
('253409', 'ID', 'Kec. Kamu Utara', '2019-06-10 04:42:07'),
('253410', 'ID', 'Kec. Sukikai Selatan', '2019-06-10 04:42:07'),
('253500', 'ID', 'Kab. Deiyai', '2019-06-10 04:42:07'),
('253501', 'ID', 'Kec. Tigi', '2019-06-10 04:42:07'),
('253502', 'ID', 'Kec. Tigi Barat', '2019-06-10 04:42:07'),
('253503', 'ID', 'Kec. Tigi Timur', '2019-06-10 04:42:07'),
('253504', 'ID', 'Kec. Bowobado', '2019-06-10 04:42:07'),
('253505', 'ID', 'Kec. Kapiraya', '2019-06-10 04:42:07'),
('253600', 'ID', 'Kab. Intan Jaya', '2019-06-10 04:42:07'),
('253601', 'ID', 'Kec. Sugapa', '2019-06-10 04:42:07'),
('253602', 'ID', 'Kec. Hitadipa', '2019-06-10 04:42:07'),
('253603', 'ID', 'Kec. Homeyo', '2019-06-10 04:42:07'),
('253604', 'ID', 'Kec. Biandoga', '2019-06-10 04:42:07'),
('253605', 'ID', 'Kec. Wandai', '2019-06-10 04:42:07'),
('253606', 'ID', 'Kec. Agisiga', '2019-06-10 04:42:07'),
('253691', 'ID', 'Sugapa*', '2019-06-10 04:42:07'),
('253692', 'ID', 'Hitadipa*', '2019-06-10 04:42:07'),
('253693', 'ID', 'Homeyo*', '2019-06-10 04:42:07'),
('253694', 'ID', 'Biandoga*', '2019-06-10 04:42:07'),
('253695', 'ID', 'Wandai*', '2019-06-10 04:42:07'),
('253696', 'ID', 'Agisiga*', '2019-06-10 04:42:07'),
('253697', 'ID', 'Kec. Ugimba', '2020-02-10 03:07:44'),
('253698', 'ID', 'Kec. Tomosiga', '2020-02-10 03:07:44'),
('256000', 'ID', 'Kota Jayapura', '2019-06-10 04:42:07'),
('256001', 'ID', 'Kec. Muara Tami', '2019-06-10 04:42:07'),
('256002', 'ID', 'Kec. Abepura', '2019-06-10 04:42:07'),
('256003', 'ID', 'Kec. Jayapura Selatan', '2019-06-10 04:42:07'),
('256004', 'ID', 'Kec. Jayapura Utara', '2019-06-10 04:42:07'),
('256005', 'ID', 'Kec. Heram', '2019-06-10 04:42:07'),
('256090', 'ID', 'Kotaraja', '2019-06-10 04:42:07'),
('260000', 'ID', 'Prop. Bengkulu', '2019-06-10 04:42:07'),
('260100', 'ID', 'Kab. Bengkulu Utara', '2019-06-10 04:42:07'),
('260101', 'ID', 'Kec. Enggano', '2019-06-10 04:42:07'),
('260105', 'ID', 'Kec. Kerkap', '2019-06-10 04:42:07'),
('260106', 'ID', 'Kec. Arga Makmur', '2019-06-10 04:42:07'),
('260107', 'ID', 'Kec. Lais', '2019-06-10 04:42:07'),
('260108', 'ID', 'Kec. Padang Jaya', '2019-06-10 04:42:07'),
('260109', 'ID', 'Kec. Ketahun', '2019-06-10 04:42:07'),
('260110', 'ID', 'Kec. Putri Hijau', '2019-06-10 04:42:07'),
('260117', 'ID', 'Kec. Air Napal', '2019-06-10 04:42:07'),
('260118', 'ID', 'Kec. Air Besi', '2019-06-10 04:42:07'),
('260119', 'ID', 'Kec. Batik Nau', '2019-06-10 04:42:07'),
('260120', 'ID', 'Kec. Giri Mulia', '2019-06-10 04:42:07'),
('260121', 'ID', 'Kec. Napal Putih', '2019-06-10 04:42:07'),
('260122', 'ID', 'Hulu Palik', '2019-06-10 04:42:07'),
('260123', 'ID', 'Air Padang', '2019-06-10 04:42:07'),
('260124', 'ID', 'Kec. Arma Jaya', '2020-02-10 03:07:44'),
('260125', 'ID', 'Kec. Tanjungagung Palik', '2020-02-10 03:07:44'),
('260126', 'ID', 'Kec. Ulok Kupai', '2020-02-10 03:07:44'),
('260127', 'ID', 'Kec. Marga Sakti Sebelat', '2020-02-10 03:07:44'),
('260128', 'ID', 'Kec. Pinang Raya', '2020-02-10 03:07:44'),
('260200', 'ID', 'Kab. Rejang Lebong', '2019-06-10 04:42:07'),
('260202', 'ID', 'Kec. Kota Padang', '2019-06-10 04:42:07'),
('260203', 'ID', 'Kec. Padang Ulang Tanding', '2019-06-10 04:42:07'),
('260204', 'ID', 'Kec. Curup', '2019-06-10 04:42:07'),
('260210', 'ID', 'Kec. Sindang Kelingi', '2019-06-10 04:42:07'),
('260211', 'ID', 'Kec. Bermani Ulu', '2019-06-10 04:42:07'),
('260212', 'ID', 'Kec. Selupu Rejang', '2019-06-10 04:42:07'),
('260217', 'ID', 'Kec. Sindang Beliti Ilir', '2019-06-10 04:42:07'),
('260218', 'ID', 'Kec. Bindu Riang', '2019-06-10 04:42:07'),
('260219', 'ID', 'Kec. Sindang Beliti Ulu', '2019-06-10 04:42:07'),
('260220', 'ID', 'Kec. Sindang Dataran', '2019-06-10 04:42:07'),
('260221', 'ID', 'Kec. Curup Selatan', '2019-06-10 04:42:07'),
('260222', 'ID', 'Kec. Curup Tengah', '2019-06-10 04:42:07'),
('260223', 'ID', 'Kec. Bermani Ulu Raya', '2019-06-10 04:42:07'),
('260224', 'ID', 'Kec. Curup Utara', '2019-06-10 04:42:07'),
('260225', 'ID', 'Kec. Curup Timur', '2019-06-10 04:42:07'),
('260300', 'ID', 'Kab. Bengkulu Selatan', '2019-06-10 04:42:07'),
('260304', 'ID', 'Kec. Manna', '2019-06-10 04:42:07'),
('260305', 'ID', 'Kec. Seginim', '2019-06-10 04:42:07'),
('260306', 'ID', 'Kec. Pino', '2019-06-10 04:42:07'),
('260314', 'ID', 'Kec. Kota Manna', '2019-06-10 04:42:07'),
('260315', 'ID', 'Kec. Pinoraya', '2019-06-10 04:42:07'),
('260318', 'ID', 'Kec. Kedurang', '2019-06-10 04:42:07'),
('260319', 'ID', 'Kec. Bunga Mas', '2019-06-10 04:42:07'),
('260320', 'ID', 'Kec. Pasar Manna', '2019-06-10 04:42:07'),
('260321', 'ID', 'Kec. Kedurang Ilir', '2019-06-10 04:42:07'),
('260322', 'ID', 'Kec. Air Nipis', '2019-06-10 04:42:07'),
('260323', 'ID', 'Kec. Ulu Manna', '2019-06-10 04:42:07'),
('260400', 'ID', 'Kab. Muko-muko', '2019-06-10 04:42:07'),
('260401', 'ID', 'Kec. Muko-Muko Selatan', '2019-06-10 04:42:07'),
('260402', 'ID', 'Kec. Teras Terunjam', '2019-06-10 04:42:07'),
('260403', 'ID', 'Kec. Muko-Muko Utara', '2019-06-10 04:42:07'),
('260404', 'ID', 'Kec. Pondok Suguh', '2019-06-10 04:42:07'),
('260405', 'ID', 'Kec. Lubuk Pinang', '2019-06-10 04:42:07'),
('260406', 'ID', 'Kec. Air Rami', '2019-06-10 04:42:07'),
('260407', 'ID', 'Kec. Malin Deman', '2019-06-10 04:42:07'),
('260408', 'ID', 'Kec. Sungai Rumbai', '2019-06-10 04:42:07'),
('260409', 'ID', 'Kec. Teramang Jaya', '2019-06-10 04:42:07'),
('260410', 'ID', 'Kec. Penarik', '2019-06-10 04:42:07'),
('260411', 'ID', 'Kec. Selagan Raya', '2019-06-10 04:42:07'),
('260412', 'ID', 'Kec. Air Dikit', '2019-06-10 04:42:07'),
('260413', 'ID', 'Kec. XIV Koto', '2019-06-10 04:42:07'),
('260414', 'ID', 'Kec. Air Manjunto', '2019-06-10 04:42:07'),
('260415', 'ID', 'Kec. V Koto', '2019-06-10 04:42:07'),
('260490', 'ID', 'Kota Mukomuko', '2019-06-10 04:42:07'),
('260491', 'ID', 'Ipuh', '2019-06-10 04:42:07'),
('260500', 'ID', 'Kab. Kepahiang', '2019-06-10 04:42:07'),
('260501', 'ID', 'Kec. Kepahiang', '2019-06-10 04:42:07'),
('260502', 'ID', 'Kec. Bermani Ilir', '2019-06-10 04:42:07'),
('260503', 'ID', 'Kec. Tebat Karai', '2019-06-10 04:42:07'),
('260504', 'ID', 'Kec. Ujan Mas', '2019-06-10 04:42:07'),
('260505', 'ID', 'Kec. Muara Kemumu', '2019-06-10 04:42:07'),
('260506', 'ID', 'Kec. Seberang Musi', '2019-06-10 04:42:07'),
('260507', 'ID', 'Kec. Kaba Wetan', '2019-06-10 04:42:07'),
('260508', 'ID', 'Kec. Merigi', '2019-06-10 04:42:07'),
('260600', 'ID', 'Kab. Lebong', '2019-06-10 04:42:07'),
('260601', 'ID', 'Kec. Lebong Selatan', '2019-06-10 04:42:07'),
('260602', 'ID', 'Kec. Lebong Utara', '2019-06-10 04:42:07'),
('260603', 'ID', 'Kec. Rimbo Pegadang', '2019-06-10 04:42:07'),
('260604', 'ID', 'Kec. Lebong Tengah', '2019-06-10 04:42:07'),
('260605', 'ID', 'Kec. Lebong Atas', '2019-06-10 04:42:07'),
('260607', 'ID', 'Topos', '2019-06-10 04:42:07'),
('260608', 'ID', 'Bingin Kuning', '2019-06-10 04:42:07'),
('260609', 'ID', 'Lebong Sakti', '2019-06-10 04:42:07'),
('260610', 'ID', 'Pelabai', '2019-06-10 04:42:07'),
('260611', 'ID', 'Amen', '2019-06-10 04:42:07'),
('260612', 'ID', 'Uram Jaya', '2019-06-10 04:42:07'),
('260613', 'ID', 'Pinang Belapis', '2019-06-10 04:42:07'),
('260614', 'ID', 'Kec. Padang Bano', '2020-02-10 03:07:44'),
('260700', 'ID', 'Kab. Kaur', '2019-06-10 04:42:07'),
('260701', 'ID', 'Kec. Kaur Selatan', '2019-06-10 04:42:07'),
('260702', 'ID', 'Kec. Kaur Tengah', '2019-06-10 04:42:07'),
('260703', 'ID', 'Kec. Kaur Utara', '2019-06-10 04:42:07'),
('260704', 'ID', 'Kec. Maje', '2019-06-10 04:42:07'),
('260705', 'ID', 'Kec. Nasal', '2019-06-10 04:42:07'),
('260706', 'ID', 'Kec. Kinal', '2019-06-10 04:42:07'),
('260707', 'ID', 'Kec. Tanjung Kemuning', '2019-06-10 04:42:07'),
('260708', 'ID', 'Kec. Muara Tetap', '2019-06-10 04:42:07'),
('260709', 'ID', 'Kec. Luas', '2019-06-10 04:42:07'),
('260710', 'ID', 'Kec. Muara Sahung', '2019-06-10 04:42:07'),
('260711', 'ID', 'Kec. Semidang Gumai', '2019-06-10 04:42:07'),
('260712', 'ID', 'Kec. Kelam Tengah', '2019-06-10 04:42:07'),
('260713', 'ID', 'Kec. Padang Guci Hilir', '2019-06-10 04:42:07'),
('260714', 'ID', 'Kec. Padang Guci Ulu', '2019-06-10 04:42:07'),
('260715', 'ID', 'Kec. Lungkang Kule', '2019-06-10 04:42:07'),
('260800', 'ID', 'Kab. Seluma', '2019-06-10 04:42:07'),
('260801', 'ID', 'Kec. Talo', '2019-06-10 04:42:07'),
('260802', 'ID', 'Kec. Seluma', '2019-06-10 04:42:07'),
('260803', 'ID', 'Kec. Sukaraja', '2019-06-10 04:42:07'),
('260804', 'ID', 'Kec. Semidang Alas Maras', '2019-06-10 04:42:07'),
('260805', 'ID', 'Kec. Semidang Alas', '2019-06-10 04:42:07'),
('260806', 'ID', 'Kec. Ilir Talo', '2019-06-10 04:42:07'),
('260807', 'ID', 'Kec. Talo Kecil', '2019-06-10 04:42:07'),
('260808', 'ID', 'Kec. Ulu Talo', '2019-06-10 04:42:07'),
('260809', 'ID', 'Kec. Seluma Selatan', '2019-06-10 04:42:07'),
('260810', 'ID', 'Kec. Seluma Barat', '2019-06-10 04:42:07'),
('260811', 'ID', 'Kec. Seluma Timur', '2019-06-10 04:42:07'),
('260812', 'ID', 'Kec. Seluma Utara', '2019-06-10 04:42:07'),
('260813', 'ID', 'Kec. Air Periukan', '2019-06-10 04:42:07'),
('260814', 'ID', 'Kec. Lubuk Sandi', '2019-06-10 04:42:07'),
('260900', 'ID', 'Kab. Bengkulu Tengah', '2019-06-10 04:42:07'),
('260901', 'ID', 'Kec. Talang Empat', '2019-06-10 04:42:07'),
('260902', 'ID', 'Kec. Karang Tinggi', '2019-06-10 04:42:07'),
('260903', 'ID', 'Kec. Taba Penanjung', '2019-06-10 04:42:07'),
('260904', 'ID', 'Kec. Pagar Jati', '2019-06-10 04:42:07'),
('260905', 'ID', 'Kec. Pondok Kelapa', '2019-06-10 04:42:07'),
('260906', 'ID', 'Kec. Pematang Tiga', '2019-06-10 04:42:07'),
('260907', 'ID', 'Kec. Merigi Kelindang', '2019-06-10 04:42:07'),
('260908', 'ID', 'Kec. Merigi Sakti', '2019-06-10 04:42:07'),
('260909', 'ID', 'Kec. Pondok Kubang', '2019-06-10 04:42:07'),
('260910', 'ID', 'Kec. Bang Haji', '2019-06-10 04:42:07'),
('266000', 'ID', 'Kota Bengkulu', '2019-06-10 04:42:07'),
('266001', 'ID', 'Kec. Selebar', '2019-06-10 04:42:07'),
('266002', 'ID', 'Kec. Gading Cempaka', '2019-06-10 04:42:07'),
('266003', 'ID', 'Kec. Teluk Segara', '2019-06-10 04:42:07'),
('266004', 'ID', 'Kec. Muara Bangkahulu', '2019-06-10 04:42:07'),
('266005', 'ID', 'Kec. Kampung Melayu', '2019-06-10 04:42:07'),
('266006', 'ID', 'Kec. Ratu Agung', '2019-06-10 04:42:07'),
('266007', 'ID', 'Kec. Ratu Samban', '2019-06-10 04:42:07'),
('266008', 'ID', 'Kec. Sungai  Serut', '2019-06-10 04:42:07'),
('266009', 'ID', 'Kec. Singaran Pati', '2019-06-10 04:42:07'),
('270000', 'ID', 'Prop. Maluku Utara', '2019-06-10 04:42:07'),
('270100', 'ID', 'Kab. Pulau Taliabu', '2020-02-10 03:07:44'),
('270101', 'ID', 'Kec. Taliabu Barat', '2020-02-10 03:07:44'),
('270102', 'ID', 'Kec. Taliabu Barat Laut', '2020-02-10 03:07:44'),
('270103', 'ID', 'Kec. Lede', '2020-02-10 03:07:44'),
('270104', 'ID', 'Kec. Taliabu Utara', '2020-02-10 03:07:44'),
('270105', 'ID', 'Kec. Taliabu Timur', '2020-02-10 03:07:44'),
('270106', 'ID', 'Kec. Taliabu Timur Selatan', '2020-02-10 03:07:44'),
('270107', 'ID', 'Kec. Taliabu Selatan', '2020-02-10 03:07:44'),
('270108', 'ID', 'Kec. Tabona', '2020-02-10 03:07:44'),
('270200', 'ID', 'Kab. Halmahera Tengah', '2019-06-10 04:42:07'),
('270203', 'ID', 'Kec. Weda', '2019-06-10 04:42:07'),
('270204', 'ID', 'Kec. Pulau Gebe', '2019-06-10 04:42:07'),
('270206', 'ID', 'Kec. Patani', '2019-06-10 04:42:07'),
('270207', 'ID', 'Kec. Weda Utara', '2019-06-10 04:42:07'),
('270208', 'ID', 'Kec. Weda Selatan', '2019-06-10 04:42:07'),
('270209', 'ID', 'Kec. Patani Utara', '2019-06-10 04:42:07'),
('270210', 'ID', 'Weda Tengah', '2019-06-10 04:42:07'),
('270211', 'ID', 'Patani Barat', '2019-06-10 04:42:07'),
('270212', 'ID', 'Kec. Weda Timur', '2020-02-10 03:07:44'),
('270213', 'ID', 'Kec. Patani Timur', '2020-02-10 03:07:44'),
('270300', 'ID', 'Kab. Halmahera Barat', '2019-06-10 04:42:07'),
('270302', 'ID', 'Kec. Sahu', '2019-06-10 04:42:07'),
('270303', 'ID', 'Kec. Ibu', '2019-06-10 04:42:07'),
('270304', 'ID', 'Kec. Loloda', '2019-06-10 04:42:07'),
('270305', 'ID', 'Kec. Jailolo Selatan', '2019-06-10 04:42:07'),
('270306', 'ID', 'Kec. Jailolo', '2019-06-10 04:42:07'),
('270307', 'ID', 'Kec. Sahu Timur', '2019-06-10 04:42:07'),
('270308', 'ID', 'Kec. Ibu Selatan', '2019-06-10 04:42:07'),
('270309', 'ID', 'Kec. Ibu Utara', '2019-06-10 04:42:07'),
('270310', 'ID', 'Jailolo Timur', '2019-06-10 04:42:07'),
('270311', 'ID', 'Kec. Tabaru', '2020-02-10 03:07:44'),
('270400', 'ID', 'Kab. halmahera Utara', '2019-06-10 04:42:07'),
('270401', 'ID', 'Kec. Tobelo', '2019-06-10 04:42:07'),
('270402', 'ID', 'Kec. Tobelo Selatan', '2019-06-10 04:42:07'),
('270403', 'ID', 'Kec. Kao', '2019-06-10 04:42:07'),
('270404', 'ID', 'Kec. Galela', '2019-06-10 04:42:07'),
('270408', 'ID', 'Kec. Loloda Utara', '2019-06-10 04:42:07'),
('270409', 'ID', 'Kec. Malifut', '2019-06-10 04:42:07'),
('270410', 'ID', 'Kec. Tobelo Utara', '2019-06-10 04:42:07'),
('270411', 'ID', 'Kec. Tobelo Tengah', '2019-06-10 04:42:07'),
('270412', 'ID', 'Kec. Tobelo Timur', '2019-06-10 04:42:07'),
('270413', 'ID', 'Kec. Tobelo Barat', '2019-06-10 04:42:07'),
('270414', 'ID', 'Kec. Galela Barat', '2019-06-10 04:42:07'),
('270415', 'ID', 'Kec. Galela Selatan', '2019-06-10 04:42:07'),
('270416', 'ID', 'Kec. Galela Utara', '2019-06-10 04:42:07'),
('270419', 'ID', 'Kec. Loloda Kepulauan', '2019-06-10 04:42:07'),
('270420', 'ID', 'Kec. Kao Utara', '2019-06-10 04:42:07'),
('270421', 'ID', 'Kec. Kao Barat', '2019-06-10 04:42:07'),
('270422', 'ID', 'Kec. Kao Teluk', '2019-06-10 04:42:07'),
('270500', 'ID', 'Kab. Halmahera Selatan', '2019-06-10 04:42:07'),
('270502', 'ID', 'Kec. Bacan Timur', '2019-06-10 04:42:07'),
('270503', 'ID', 'Kec. Bacan Barat', '2019-06-10 04:42:07'),
('270504', 'ID', 'Kec. Bacan', '2019-06-10 04:42:07'),
('270505', 'ID', 'Kec. Obi', '2019-06-10 04:42:07'),
('270506', 'ID', 'Kec. Gane Barat', '2019-06-10 04:42:07'),
('270507', 'ID', 'Kec. Gane Timur', '2019-06-10 04:42:07'),
('270508', 'ID', 'Kec. Kayoa', '2019-06-10 04:42:07'),
('270509', 'ID', 'Kec. Pulau Makian', '2019-06-10 04:42:07'),
('270510', 'ID', 'Kec. Obi Selatan', '2019-06-10 04:42:07'),
('270511', 'ID', 'Kec. Obi Barat', '2019-06-10 04:42:07'),
('270512', 'ID', 'Kec. Obi Timur', '2019-06-10 04:42:07'),
('270513', 'ID', 'Kec. Obi Utara', '2019-06-10 04:42:07'),
('270514', 'ID', 'Kec. Mandioli Selatan', '2019-06-10 04:42:07'),
('270515', 'ID', 'Kec. Mandioli Utara', '2019-06-10 04:42:07'),
('270516', 'ID', 'Kec. Bancan Selatan', '2019-06-10 04:42:07'),
('270517', 'ID', 'Kec. Batang Lomang', '2019-06-10 04:42:07'),
('270518', 'ID', 'Kec. Bacan Timur Selatan', '2019-06-10 04:42:07'),
('270519', 'ID', 'Kec. Bacan Timur Tengah', '2019-06-10 04:42:07'),
('270520', 'ID', 'Kec. Kasiruta Barat', '2019-06-10 04:42:07'),
('270521', 'ID', 'Kec. Kasiruta Timur', '2019-06-10 04:42:07'),
('270522', 'ID', 'Kec. Bacan Barat Utara', '2019-06-10 04:42:07'),
('270523', 'ID', 'Kec. Kayoa Barat', '2019-06-10 04:42:07'),
('270524', 'ID', 'Kec. Kayoa Selatan', '2019-06-10 04:42:07'),
('270525', 'ID', 'Kec. Kayoa Utara', '2019-06-10 04:42:07'),
('270526', 'ID', 'Kec. Makian Barat', '2019-06-10 04:42:07'),
('270527', 'ID', 'Kec. Gane Selatan', '2019-06-10 04:42:07'),
('270528', 'ID', 'Kec. Gane Utara', '2019-06-10 04:42:07'),
('270529', 'ID', 'Kec. Kepulauan Joronga', '2019-06-10 04:42:07'),
('270530', 'ID', 'Kec. Gane Timur Tengah', '2019-06-10 04:42:07'),
('270531', 'ID', 'Kec. Gane Timur Selatan', '2019-06-10 04:42:07'),
('270590', 'ID', 'Gane Barat Selatan', '2019-06-10 04:42:07'),
('270591', 'ID', 'Gane Barat Utara', '2019-06-10 04:42:07'),
('270600', 'ID', 'Kab. Halmahera Timur', '2019-06-10 04:42:07'),
('270601', 'ID', 'Kec. Maba Selatan', '2019-06-10 04:42:07'),
('270602', 'ID', 'Kec. Wasile Selatan', '2019-06-10 04:42:07'),
('270603', 'ID', 'Kec. Wasile', '2019-06-10 04:42:07'),
('270604', 'ID', 'Kec. Maba', '2019-06-10 04:42:07'),
('270605', 'ID', 'Kec. Wasile Tengah', '2019-06-10 04:42:07'),
('270606', 'ID', 'Kec. Wasile Utara', '2019-06-10 04:42:07'),
('270607', 'ID', 'Kec. Wasile Timur', '2019-06-10 04:42:07'),
('270608', 'ID', 'Kec. Maba Tengah', '2019-06-10 04:42:07'),
('270609', 'ID', 'Kec. Maba Utara', '2019-06-10 04:42:07'),
('270610', 'ID', 'Kec. Kota Maba', '2019-06-10 04:42:07'),
('270700', 'ID', 'Kab. Kepulauan Sula', '2019-06-10 04:42:07'),
('270701', 'ID', 'Kec. Sanana', '2019-06-10 04:42:07'),
('270702', 'ID', 'Kec. Sula Besi Barat', '2019-06-10 04:42:07'),
('270703', 'ID', 'Kec. Mangoli Timur', '2019-06-10 04:42:07'),
('270704', 'ID', 'Kec. Taliabu Barat', '2019-06-10 04:42:07'),
('270705', 'ID', 'Kec. Taliabu Timur', '2019-06-10 04:42:07'),
('270706', 'ID', 'Kec. Mangoli Barat', '2019-06-10 04:42:07'),
('270707', 'ID', 'Kec. Sula Besi Tengah', '2019-06-10 04:42:07'),
('270708', 'ID', 'Kec. Sula Besi Timur', '2019-06-10 04:42:07'),
('270709', 'ID', 'Kec. Mangoli Tengah', '2019-06-10 04:42:07'),
('270710', 'ID', 'Kec. Mangoli Utara Timur', '2019-06-10 04:42:07'),
('270711', 'ID', 'Kec. Mangoli Utara', '2019-06-10 04:42:07'),
('270712', 'ID', 'Kec. Mangoli Selatan', '2019-06-10 04:42:07'),
('270713', 'ID', 'Kec. Taliabu Timur Selatan', '2019-06-10 04:42:07'),
('270714', 'ID', 'Kec. Taliabu Utara', '2019-06-10 04:42:07'),
('270715', 'ID', 'Kec. Sula Besi Selatan', '2019-06-10 04:42:07'),
('270716', 'ID', 'Kec. Sanana Utara', '2019-06-10 04:42:07'),
('270717', 'ID', 'Kec. Taliabu Barat Laut', '2019-06-10 04:42:07'),
('270718', 'ID', 'Kec. Lede', '2019-06-10 04:42:07'),
('270719', 'ID', 'Kec. Taliabu Selatan', '2019-06-10 04:42:07'),
('270720', 'ID', 'Mangoli Tengah', '2019-06-10 04:42:07'),
('270721', 'ID', 'Taliabu Utara', '2019-06-10 04:42:07'),
('270722', 'ID', 'Taliabu Timur Selatan', '2019-06-10 04:42:07'),
('270723', 'ID', 'Taliabu -Timur', '2019-06-10 04:42:07'),
('270724', 'ID', 'Taliabu Selatan', '2019-06-10 04:42:07'),
('270725', 'ID', 'Taliabu Barat Laut', '2019-06-10 04:42:07'),
('270726', 'ID', 'Taliabu Barat', '2019-06-10 04:42:07'),
('270727', 'ID', 'Lede', '2019-06-10 04:42:07'),
('270790', 'ID', 'Tabona', '2019-06-10 04:42:07'),
('270800', 'ID', 'Kab. Kepulauan Morotai', '2019-06-10 04:42:07'),
('270801', 'ID', 'Kec. Morotai Selatan Barat', '2019-06-10 04:42:07'),
('270802', 'ID', 'Kec. Morotai Selatan', '2019-06-10 04:42:07'),
('270803', 'ID', 'Kec. Morotai Utara', '2019-06-10 04:42:07'),
('270804', 'ID', 'Kec. Morotai Jaya', '2019-06-10 04:42:07'),
('270805', 'ID', 'Kec. Morotai Timur', '2019-06-10 04:42:07'),
('276000', 'ID', 'Kota Ternate', '2019-06-10 04:42:07'),
('276001', 'ID', 'Kec. Pulau Ternate', '2019-06-10 04:42:07'),
('276002', 'ID', 'Kec. Ternate Selatan', '2019-06-10 04:42:07'),
('276003', 'ID', 'Kec. Ternate Utara', '2019-06-10 04:42:07'),
('276004', 'ID', 'Kec. Moti', '2019-06-10 04:42:07'),
('276005', 'ID', 'Kec. Pulau Batang Dua', '2019-06-10 04:42:07'),
('276006', 'ID', 'Kec. Ternate Tengah', '2019-06-10 04:42:07'),
('276007', 'ID', 'Kec.  Pulau Hiri', '2019-06-10 04:42:07'),
('276100', 'ID', 'Kota Tidore Kepulauan', '2019-06-10 04:42:07'),
('276102', 'ID', 'Kec. Tidore Selatan', '2019-06-10 04:42:07'),
('276103', 'ID', 'Kec. Tidore Utara', '2019-06-10 04:42:07'),
('276104', 'ID', 'Kec. Oba', '2019-06-10 04:42:07'),
('276105', 'ID', 'Kec. Oba Utara', '2019-06-10 04:42:07'),
('276106', 'ID', 'Kec. Oba Tengah', '2019-06-10 04:42:07'),
('276107', 'ID', 'Kec. Oba Selatan', '2019-06-10 04:42:07'),
('276108', 'ID', 'Kec. Tidore', '2019-06-10 04:42:07'),
('276109', 'ID', 'Kec. Tidore Timur', '2019-06-10 04:42:07'),
('280000', 'ID', 'Prop. Banten', '2019-06-10 04:42:07'),
('280100', 'ID', 'Kab. Pandeglang', '2019-06-10 04:42:07'),
('280101', 'ID', 'Kec. Sumur', '2019-06-10 04:42:07'),
('280102', 'ID', 'Kec. Cimanggu', '2019-06-10 04:42:07'),
('280103', 'ID', 'Kec. Cibaliung', '2019-06-10 04:42:07'),
('280104', 'ID', 'Kec. Cikeusik', '2019-06-10 04:42:07'),
('280105', 'ID', 'Kec. Cigeulis', '2019-06-10 04:42:07'),
('280106', 'ID', 'Kec. Panimbang', '2019-06-10 04:42:07'),
('280107', 'ID', 'Kec. Munjul', '2019-06-10 04:42:07'),
('280108', 'ID', 'Kec. Picung', '2019-06-10 04:42:07'),
('280109', 'ID', 'Kec. Bojong', '2019-06-10 04:42:07'),
('280110', 'ID', 'Kec. Saketi', '2019-06-10 04:42:07'),
('280111', 'ID', 'Kec. Pagelaran', '2019-06-10 04:42:07'),
('280112', 'ID', 'Kec. Labuan', '2019-06-10 04:42:07'),
('280113', 'ID', 'Kec. Jiput', '2019-06-10 04:42:07'),
('280114', 'ID', 'Kec. Menes', '2019-06-10 04:42:07'),
('280115', 'ID', 'Kec. Mandalawangi', '2019-06-10 04:42:07'),
('280116', 'ID', 'Kec. Cimanuk', '2019-06-10 04:42:07'),
('280117', 'ID', 'Kec. Banjar', '2019-06-10 04:42:07'),
('280118', 'ID', 'Kec. Pandeglang', '2019-06-10 04:42:07'),
('280119', 'ID', 'Kec. Cadasari', '2019-06-10 04:42:07'),
('280120', 'ID', 'Kec. Angsana', '2019-06-10 04:42:07'),
('280121', 'ID', 'Kec. Karang Tanjung', '2019-06-10 04:42:07'),
('280122', 'ID', 'Kec. Kaduhejo', '2019-06-10 04:42:07'),
('280123', 'ID', 'Kec. Cikedal', '2019-06-10 04:42:07'),
('280124', 'ID', 'Kec. Cipeucang', '2019-06-10 04:42:07'),
('280125', 'ID', 'Kec. Cisata', '2019-06-10 04:42:07'),
('280126', 'ID', 'Kec. Patia', '2019-06-10 04:42:07'),
('280127', 'ID', 'Kec. Carita', '2019-06-10 04:42:07'),
('280132', 'ID', 'Kec. Sukaresmi', '2019-06-10 04:42:07'),
('280133', 'ID', 'Kec. Mekarjaya', '2019-06-10 04:42:07'),
('280134', 'ID', 'Kec. Sindangresmi', '2019-06-10 04:42:07'),
('280135', 'ID', 'Kec. Pulosari', '2019-06-10 04:42:07'),
('280136', 'ID', 'Kec. Koroncong', '2019-06-10 04:42:07'),
('280138', 'ID', 'Kec. Cibitung', '2019-06-10 04:42:07'),
('280139', 'ID', 'Kec. Majasari', '2019-06-10 04:42:07'),
('280140', 'ID', 'Kec. Sobang', '2019-06-10 04:42:07'),
('280200', 'ID', 'Kab. Lebak', '2019-06-10 04:42:07'),
('280201', 'ID', 'Kec. Malingping', '2019-06-10 04:42:07'),
('280202', 'ID', 'Kec. Panggarangan', '2019-06-10 04:42:07'),
('280203', 'ID', 'Kec. Bayah', '2019-06-10 04:42:07'),
('280204', 'ID', 'Kec. Cibeber', '2019-06-10 04:42:07'),
('280205', 'ID', 'Kec. Cijaku', '2019-06-10 04:42:07'),
('280206', 'ID', 'Kec. Banjarsari', '2019-06-10 04:42:07'),
('280207', 'ID', 'Kec. Cileles', '2019-06-10 04:42:07'),
('280208', 'ID', 'Kec. Gunung kencana', '2019-06-10 04:42:07'),
('280209', 'ID', 'Kec. Bojongmanik', '2019-06-10 04:42:07'),
('280210', 'ID', 'Kec. Leuwidamar', '2019-06-10 04:42:07'),
('280211', 'ID', 'Kec. Muncang', '2019-06-10 04:42:07'),
('280212', 'ID', 'Kec. Cipanas', '2019-06-10 04:42:07'),
('280213', 'ID', 'Kec. Sajira', '2019-06-10 04:42:07'),
('280214', 'ID', 'Kec. Cimarga', '2019-06-10 04:42:07'),
('280215', 'ID', 'Kec. Cikulur', '2019-06-10 04:42:07'),
('280216', 'ID', 'Kec. Warung gunung', '2019-06-10 04:42:07'),
('280217', 'ID', 'Kec. Cibadak', '2019-06-10 04:42:07'),
('280218', 'ID', 'Kec. Rangkasbitung', '2019-06-10 04:42:07'),
('280219', 'ID', 'Kec. Maja', '2019-06-10 04:42:07'),
('280220', 'ID', 'Kec. Curug bitung', '2019-06-10 04:42:07'),
('280221', 'ID', 'Kec. Sobang', '2019-06-10 04:42:07'),
('280222', 'ID', 'Kec. Wanasalam', '2019-06-10 04:42:07'),
('280223', 'ID', 'Kec. Cilograng', '2019-06-10 04:42:07'),
('280224', 'ID', 'Kec. Cihara', '2019-06-10 04:42:07'),
('280225', 'ID', 'Kec. Cigemblong', '2019-06-10 04:42:07'),
('280226', 'ID', 'Kec. Cirinten', '2019-06-10 04:42:07'),
('280227', 'ID', 'Kec. Lebakgedong', '2019-06-10 04:42:07'),
('280228', 'ID', 'Kec. Karanganyar', '2019-06-10 04:42:07'),
('280300', 'ID', 'Kab. Tangerang', '2019-06-10 04:42:07'),
('280301', 'ID', 'Kec. Cisoka', '2019-06-10 04:42:07'),
('280302', 'ID', 'Kec. Tiga raksa', '2019-06-10 04:42:07'),
('280303', 'ID', 'Kec. Cikupa', '2019-06-10 04:42:07'),
('280304', 'ID', 'Kec. Panongan', '2019-06-10 04:42:07'),
('280305', 'ID', 'Kec. Curug', '2019-06-10 04:42:07'),
('280306', 'ID', 'Kec. Legok', '2019-06-10 04:42:07'),
('280307', 'ID', 'Kec. Pagedangan', '2019-06-10 04:42:07'),
('280312', 'ID', 'Kec. Pasar Kemis', '2019-06-10 04:42:07'),
('280313', 'ID', 'Kec. Balaraja', '2019-06-10 04:42:07'),
('280314', 'ID', 'Kec. Kresek', '2019-06-10 04:42:07'),
('280315', 'ID', 'Kec. Kronjo', '2019-06-10 04:42:07'),
('280316', 'ID', 'Kec. Mauk', '2019-06-10 04:42:07'),
('280317', 'ID', 'Kec. Rajeg', '2019-06-10 04:42:07'),
('280318', 'ID', 'Kec. Sepatan', '2019-06-10 04:42:07'),
('280319', 'ID', 'Kec. Pakuhaji', '2019-06-10 04:42:07'),
('280320', 'ID', 'Kec. Teluk naga', '2019-06-10 04:42:07'),
('280321', 'ID', 'Kec. Kosambi', '2019-06-10 04:42:07'),
('280322', 'ID', 'Kec. Jayanti', '2019-06-10 04:42:07'),
('280323', 'ID', 'Kec. Jambe', '2019-06-10 04:42:07'),
('280324', 'ID', 'Kec. Cisauk', '2019-06-10 04:42:07'),
('280325', 'ID', 'Kec. Kemeri', '2019-06-10 04:42:07'),
('280326', 'ID', 'Kec. Sukadiri', '2019-06-10 04:42:07'),
('280333', 'ID', 'Kec. Sukamulya', '2019-06-10 04:42:07'),
('280334', 'ID', 'Kec. Kelapa Dua', '2019-06-10 04:42:07'),
('280335', 'ID', 'Kec. Sindang Jaya', '2019-06-10 04:42:07'),
('280336', 'ID', 'Kec. Sepatan Timur', '2019-06-10 04:42:07'),
('280337', 'ID', 'Kec. Solear', '2019-06-10 04:42:07'),
('280338', 'ID', 'Kec. Gunung Kaler', '2019-06-10 04:42:07'),
('280339', 'ID', 'Kec. Mekar Baru', '2019-06-10 04:42:07'),
('280400', 'ID', 'Kab. Serang', '2019-06-10 04:42:07'),
('280401', 'ID', 'Kec. Cinangka', '2019-06-10 04:42:07'),
('280402', 'ID', 'Kec. Padarincang', '2019-06-10 04:42:07'),
('280403', 'ID', 'Kec. Ciomas', '2019-06-10 04:42:07'),
('280404', 'ID', 'Kec. Pabuaran', '2019-06-10 04:42:07'),
('280405', 'ID', 'Kec. Baros', '2019-06-10 04:42:07'),
('280406', 'ID', 'Kec. Petir', '2019-06-10 04:42:07'),
('280408', 'ID', 'Kec. Cikeusal', '2019-06-10 04:42:07'),
('280409', 'ID', 'Kec. Pamarayan', '2019-06-10 04:42:07'),
('280410', 'ID', 'Kec. Jawilan', '2019-06-10 04:42:07'),
('280411', 'ID', 'Kec. Kopo', '2019-06-10 04:42:07'),
('280412', 'ID', 'Kec. Cikande', '2019-06-10 04:42:07'),
('280413', 'ID', 'Kec. Kragilan', '2019-06-10 04:42:07'),
('280416', 'ID', 'Kec. Serang', '2019-06-10 04:42:07'),
('280418', 'ID', 'Kec. Waringinkurung', '2019-06-10 04:42:07'),
('280419', 'ID', 'Kec. Mancak', '2019-06-10 04:42:07'),
('280420', 'ID', 'Kec. Anyar', '2019-06-10 04:42:07'),
('280421', 'ID', 'Kec. Bojonegara', '2019-06-10 04:42:07'),
('280422', 'ID', 'Kec. Kramatwatu', '2019-06-10 04:42:07'),
('280424', 'ID', 'Kec. Ciruas', '2019-06-10 04:42:07'),
('280425', 'ID', 'Kec. Pontang', '2019-06-10 04:42:07'),
('280426', 'ID', 'Kec. Carenang', '2019-06-10 04:42:07'),
('280427', 'ID', 'Kec. Tirtayasa', '2019-06-10 04:42:07'),
('280428', 'ID', 'Kec. Tunjung Teja', '2019-06-10 04:42:07'),
('280429', 'ID', 'Kec. Kibin', '2019-06-10 04:42:07'),
('280430', 'ID', 'Kec. Pulo Ampel', '2019-06-10 04:42:07'),
('280431', 'ID', 'Kec. Binuang', '2019-06-10 04:42:07'),
('280432', 'ID', 'Kec. Tanara', '2019-06-10 04:42:07'),
('280433', 'ID', 'Kec. Gunung Sari', '2019-06-10 04:42:07'),
('280434', 'ID', 'Kec. Bandung', '2019-06-10 04:42:07'),
('280435', 'ID', 'Kec. Lebak Wangi', '2020-02-10 03:07:44'),
('286000', 'ID', 'Kota Cilegon', '2019-06-10 04:42:07'),
('286001', 'ID', 'Kec. Ciwandan', '2019-06-10 04:42:07'),
('286002', 'ID', 'Kec. Pulomerak', '2019-06-10 04:42:07'),
('286003', 'ID', 'Kec. Cilegon', '2019-06-10 04:42:07'),
('286004', 'ID', 'Kec. Cibeber', '2019-06-10 04:42:07'),
('286005', 'ID', 'Kec. Gerogol', '2019-06-10 04:42:07'),
('286006', 'ID', 'Kec. Purwakarta', '2019-06-10 04:42:07'),
('286007', 'ID', 'Kec. Jombang', '2019-06-10 04:42:07'),
('286008', 'ID', 'Kec. Citangkil', '2019-06-10 04:42:07'),
('286100', 'ID', 'Kota Tangerang', '2019-06-10 04:42:07'),
('286101', 'ID', 'Kec. Ciledug', '2019-06-10 04:42:07'),
('286102', 'ID', 'Kec. Cipondoh', '2019-06-10 04:42:07'),
('286103', 'ID', 'Kec. Tangerang', '2019-06-10 04:42:07'),
('286104', 'ID', 'Kec. Jati uwung', '2019-06-10 04:42:07'),
('286105', 'ID', 'Kec. Batuceper', '2019-06-10 04:42:07'),
('286106', 'ID', 'Kec. Benda', '2019-06-10 04:42:07'),
('286107', 'ID', 'Kec. Larangan', '2019-06-10 04:42:07'),
('286108', 'ID', 'Kec. Karang Tengah', '2019-06-10 04:42:07'),
('286109', 'ID', 'Kec. Pinang', '2019-06-10 04:42:07'),
('286110', 'ID', 'Kec. Karawaci', '2019-06-10 04:42:07'),
('286111', 'ID', 'Kec. Cibodas', '2019-06-10 04:42:07'),
('286112', 'ID', 'Kec. Periuk', '2019-06-10 04:42:07'),
('286113', 'ID', 'Kec. Neglasari', '2019-06-10 04:42:07'),
('286200', 'ID', 'Kota Serang', '2019-06-10 04:42:07'),
('286201', 'ID', 'Kec. Cipocok Jaya', '2019-06-10 04:42:07'),
('286202', 'ID', 'Kec. Curug', '2019-06-10 04:42:07'),
('286203', 'ID', 'Kec. Kasemen', '2019-06-10 04:42:07'),
('286205', 'ID', 'Kec. Taktakan', '2019-06-10 04:42:07'),
('286206', 'ID', 'Kec. Walantaka', '2019-06-10 04:42:07'),
('286207', 'ID', 'Serang', '2019-06-10 04:42:07'),
('286300', 'ID', 'Kota tangerang Selatan', '2019-06-10 04:42:07'),
('286301', 'ID', 'Kec. Ciputat', '2019-06-10 04:42:07'),
('286302', 'ID', 'Kec. Ciputat Timur', '2019-06-10 04:42:07'),
('286303', 'ID', 'Kec. Pamulang', '2019-06-10 04:42:07'),
('286304', 'ID', 'Kec. Pondok Aren', '2019-06-10 04:42:07'),
('286305', 'ID', 'Kec. Serpong', '2019-06-10 04:42:07'),
('286306', 'ID', 'Kec. Serpong Utara', '2019-06-10 04:42:07'),
('286307', 'ID', 'Kec. Setu', '2019-06-10 04:42:07'),
('290000', 'ID', 'Prop. Bangka Belitung', '2019-06-10 04:42:07'),
('290100', 'ID', 'Kab. Bangka', '2019-06-10 04:42:07'),
('290107', 'ID', 'Kec. Mendo Barat', '2019-06-10 04:42:07'),
('290108', 'ID', 'Kec. Merawang', '2019-06-10 04:42:07'),
('290109', 'ID', 'Kec. Sungai Liat', '2019-06-10 04:42:07'),
('290113', 'ID', 'Kec. Belinyu', '2019-06-10 04:42:07'),
('290114', 'ID', 'Kec. Puding Besar', '2019-06-10 04:42:07'),
('290115', 'ID', 'Kec. Bakam', '2019-06-10 04:42:07'),
('290116', 'ID', 'Kec. Pemali', '2019-06-10 04:42:07'),
('290117', 'ID', 'Kec. Riau Silip', '2019-06-10 04:42:07'),
('290200', 'ID', 'Kab. Belitung', '2019-06-10 04:42:07'),
('290201', 'ID', 'Kec. Membalong', '2019-06-10 04:42:07'),
('290206', 'ID', 'Kec. Tanjung Pandan', '2019-06-10 04:42:07'),
('290207', 'ID', 'Kec. Sijuk', '2019-06-10 04:42:07'),
('290208', 'ID', 'Kec. Badau', '2019-06-10 04:42:07'),
('290209', 'ID', 'Kec. Selat Nasik', '2019-06-10 04:42:07'),
('290225', 'ID', 'Kec. Air Gegas', '2019-06-10 04:42:07'),
('290300', 'ID', 'Kab. Bangka Tengah', '2019-06-10 04:42:07'),
('290301', 'ID', 'Kec. Koba', '2019-06-10 04:42:07'),
('290302', 'ID', 'Kec. Pangkalan Baru', '2019-06-10 04:42:07'),
('290303', 'ID', 'Kec. Sungai Selan', '2019-06-10 04:42:07'),
('290304', 'ID', 'Kec. Simpang Katis', '2019-06-10 04:42:07'),
('290305', 'ID', 'Kec. Lubuk Besar', '2019-06-10 04:42:07'),
('290306', 'ID', 'Kec. Namang', '2019-06-10 04:42:07'),
('290400', 'ID', 'Kab. Bangka Barat', '2019-06-10 04:42:07'),
('290401', 'ID', 'Kec. Kelapa', '2019-06-10 04:42:07'),
('290402', 'ID', 'Kec. Tempilang', '2019-06-10 04:42:07'),
('290403', 'ID', 'Kec. Mentok', '2019-06-10 04:42:07'),
('290404', 'ID', 'Kec. Simpang Teritip', '2019-06-10 04:42:07'),
('290405', 'ID', 'Kec. Jebus', '2019-06-10 04:42:07'),
('290406', 'ID', 'Kec. Parittiga', '2019-06-10 04:42:07'),
('290500', 'ID', 'Kab. Bangka Selatan', '2019-06-10 04:42:07'),
('290501', 'ID', 'Kec. Payung', '2019-06-10 04:42:07'),
('290502', 'ID', 'Kec. Simpang Rimba', '2019-06-10 04:42:07'),
('290503', 'ID', 'Kec. Toboali', '2019-06-10 04:42:07'),
('290505', 'ID', 'Kec. Lepar Pongok', '2019-06-10 04:42:07'),
('290506', 'ID', 'Kec. Pulau Besar', '2019-06-10 04:42:07'),
('290507', 'ID', 'Kec. Tukak Sadai', '2019-06-10 04:42:07'),
('290508', 'ID', 'Air Gegas', '2019-06-10 04:42:07'),
('290509', 'ID', 'Kec. Kep. Pongok', '2020-02-10 03:07:44'),
('290600', 'ID', 'Kab. Belitung Timur', '2019-06-10 04:42:07'),
('290601', 'ID', 'Kec. Dendang', '2019-06-10 04:42:07'),
('290602', 'ID', 'Kec. Gantung', '2019-06-10 04:42:07'),
('290603', 'ID', 'Kec. Manggar', '2019-06-10 04:42:07'),
('290604', 'ID', 'Kec. Kelapa Kampit', '2019-06-10 04:42:07'),
('290605', 'ID', 'Kec. Damar', '2019-06-10 04:42:07'),
('290606', 'ID', 'Kec. Simpang Renggiang', '2019-06-10 04:42:07'),
('290607', 'ID', 'Kec. Simpang Pesak', '2019-06-10 04:42:07'),
('296000', 'ID', 'Kota Pangkalpinang', '2019-06-10 04:42:07'),
('296001', 'ID', 'Kec. Rangkui', '2019-06-10 04:42:07'),
('296002', 'ID', 'Kec. Bukit Intan', '2019-06-10 04:42:07'),
('296003', 'ID', 'Kec. Pangkal Balam', '2019-06-10 04:42:07'),
('296004', 'ID', 'Kec. Taman Sari', '2019-06-10 04:42:07'),
('296005', 'ID', 'Kec. Gerunggang', '2019-06-10 04:42:07'),
('296090', 'ID', 'Girimaya', '2019-06-10 04:42:07'),
('296091', 'ID', 'Kec. Gabek', '2020-02-10 03:07:44'),
('300000', 'ID', 'Prop. Gorontalo', '2019-06-10 04:42:07'),
('300100', 'ID', 'Kab. Boalemo', '2019-06-10 04:42:07'),
('300104', 'ID', 'Kec. Tilamuta', '2019-06-10 04:42:07'),
('300106', 'ID', 'Kec. Paguyaman', '2019-06-10 04:42:07'),
('300107', 'ID', 'Kec. Mananggu', '2019-06-10 04:42:07'),
('300108', 'ID', 'Kec. Dulupi', '2019-06-10 04:42:07'),
('300109', 'ID', 'Kec. Wonosari', '2019-06-10 04:42:07'),
('300110', 'ID', 'Kec. Botumoita', '2019-06-10 04:42:07'),
('300111', 'ID', 'Kec. Paguyaman Pantai', '2019-06-10 04:42:07'),
('300200', 'ID', 'Kab. Gorontalo', '2019-06-10 04:42:07'),
('300201', 'ID', 'Kec. Batudaa Pantai', '2019-06-10 04:42:07'),
('300202', 'ID', 'Kec. Batudaa', '2019-06-10 04:42:07'),
('300203', 'ID', 'Kec. Tibawa', '2019-06-10 04:42:07'),
('300204', 'ID', 'Kec. Boliyohuto', '2019-06-10 04:42:07'),
('300207', 'ID', 'Kec. Limboto', '2019-06-10 04:42:07'),
('300208', 'ID', 'Kec. Telaga', '2019-06-10 04:42:07'),
('300210', 'ID', 'Kec. Bongomeme', '2019-06-10 04:42:07'),
('300211', 'ID', 'Kec. Pulubala', '2019-06-10 04:42:07'),
('300215', 'ID', 'Kec. Tolangohula', '2019-06-10 04:42:07'),
('300216', 'ID', 'Kec. Mootilango', '2019-06-10 04:42:07'),
('300218', 'ID', 'Kec. Telaga Biru', '2019-06-10 04:42:07'),
('300219', 'ID', 'Kec. Limboto Barat', '2019-06-10 04:42:07'),
('300220', 'ID', 'Kec. Biluhu', '2019-06-10 04:42:07'),
('300221', 'ID', 'Kec. Tabongo', '2019-06-10 04:42:07'),
('300222', 'ID', 'Kec. Asparaga', '2019-06-10 04:42:07'),
('300223', 'ID', 'Kec. Tilango', '2019-06-10 04:42:07'),
('300224', 'ID', 'Kec. Telaga Jaya', '2019-06-10 04:42:07'),
('300225', 'ID', 'Kec. Bilato', '2019-06-10 04:42:07'),
('300226', 'ID', 'Bilato', '2019-06-10 04:42:07'),
('300227', 'ID', 'Kec. Dungaliyo', '2020-02-10 03:07:44'),
('300300', 'ID', 'Kab. Pohuwato', '2019-06-10 04:42:07'),
('300301', 'ID', 'Kec. Popayato', '2019-06-10 04:42:07'),
('300302', 'ID', 'Kec. Marisa', '2019-06-10 04:42:07'),
('300303', 'ID', 'Kec. Paguat', '2019-06-10 04:42:07'),
('300304', 'ID', 'Kec. Lemito', '2019-06-10 04:42:07'),
('300305', 'ID', 'Kec. Randangan', '2019-06-10 04:42:07'),
('300306', 'ID', 'Kec. Patilanggio', '2019-06-10 04:42:07'),
('300307', 'ID', 'Kec. Taluditi', '2019-06-10 04:42:07'),
('300308', 'ID', 'Kec. Popayato Barat', '2019-06-10 04:42:07'),
('300309', 'ID', 'Kec. Popayato Timur', '2019-06-10 04:42:07'),
('300310', 'ID', 'Kec. Wanggarasi', '2019-06-10 04:42:07'),
('300311', 'ID', 'Kec. Buntulia', '2019-06-10 04:42:07'),
('300312', 'ID', 'Kec. Duhiadaa', '2019-06-10 04:42:07'),
('300313', 'ID', 'Kec. Dengilo', '2019-06-10 04:42:07'),
('300400', 'ID', 'Kab. Bone Bolango', '2019-06-10 04:42:07'),
('300401', 'ID', 'Kec. Tapa', '2019-06-10 04:42:07'),
('300402', 'ID', 'Kec. Kabila', '2019-06-10 04:42:07'),
('300403', 'ID', 'Kec. Suwawa', '2019-06-10 04:42:07'),
('300404', 'ID', 'Kec. Bonepantai', '2019-06-10 04:42:07'),
('300405', 'ID', 'Kec. Bulango Utara', '2019-06-10 04:42:07'),
('300406', 'ID', 'Kec. Tilongkabila', '2019-06-10 04:42:07'),
('300407', 'ID', 'Kec. Botupingge', '2019-06-10 04:42:07'),
('300408', 'ID', 'Kec. Kabila Bone', '2019-06-10 04:42:07'),
('300409', 'ID', 'Kec. Bone', '2019-06-10 04:42:07'),
('300410', 'ID', 'Kec. Bone Raya', '2019-06-10 04:42:07'),
('300411', 'ID', 'Kec. Bulango Selatan', '2019-06-10 04:42:07'),
('300412', 'ID', 'Kec. Bulango Timur', '2019-06-10 04:42:07'),
('300413', 'ID', 'Kec. Bulango Ulu', '2019-06-10 04:42:07'),
('300414', 'ID', 'Kec. Suwawa Selatan', '2019-06-10 04:42:07'),
('300415', 'ID', 'Kec. Suwawa Timur', '2019-06-10 04:42:07'),
('300416', 'ID', 'Kec. Suwawa Tengah', '2019-06-10 04:42:07'),
('300417', 'ID', 'Kec. Bulawa', '2019-06-10 04:42:07'),
('300418', 'ID', 'Kec. Pinogu', '2020-02-10 03:07:44'),
('300500', 'ID', 'Kab. Gorontalo Utara', '2019-06-10 04:42:07'),
('300501', 'ID', 'Kec. Anggrek', '2019-06-10 04:42:07'),
('300502', 'ID', 'Kec. Atinggola', '2019-06-10 04:42:07'),
('300503', 'ID', 'Kec. Kwandang', '2019-06-10 04:42:07'),
('300504', 'ID', 'Kec. Sumalata', '2019-06-10 04:42:07'),
('300505', 'ID', 'Kec. Tolinggula', '2019-06-10 04:42:07'),
('300506', 'ID', 'Kec. Gentuma Raya', '2019-06-10 04:42:07'),
('300507', 'ID', 'Tomilito', '2019-06-10 04:42:07'),
('300508', 'ID', 'Ponelo Kepulauan', '2019-06-10 04:42:07'),
('300509', 'ID', 'Monano', '2019-06-10 04:42:07'),
('300511', 'ID', 'Biau', '2019-06-10 04:42:07'),
('300512', 'ID', 'Kec. Sumalata Timur', '2020-02-10 03:07:44'),
('300513', 'ID', 'Kec. Sumalata Timur', '2020-02-10 03:07:44'),
('306000', 'ID', 'Kota Gorontalo', '2019-06-10 04:42:07'),
('306001', 'ID', 'Kec. Kota Barat', '2019-06-10 04:42:07'),
('306002', 'ID', 'Kec. Kota Selatan', '2019-06-10 04:42:07'),
('306003', 'ID', 'Kec. Kota Utara', '2019-06-10 04:42:07'),
('306004', 'ID', 'Kec. Kota Timur', '2019-06-10 04:42:07'),
('306005', 'ID', 'Kec. Dungingi', '2019-06-10 04:42:07'),
('306006', 'ID', 'Kec. Kota Tengah', '2019-06-10 04:42:07'),
('306007', 'ID', 'Kec. Sipatana', '2019-06-10 04:42:07'),
('306008', 'ID', 'Kec. Dumbo Raya', '2019-06-10 04:42:07'),
('306009', 'ID', 'Kec. Hulonthalangi', '2019-06-10 04:42:07'),
('310000', 'ID', 'Prop. Kepulauan Riau', '2019-06-10 04:42:07'),
('310100', 'ID', 'Kab. Bintan', '2019-06-10 04:42:07'),
('310101', 'ID', 'Kec. Bintan Utara', '2019-06-10 04:42:07'),
('310102', 'ID', 'Kec. Gunung Kijang', '2019-06-10 04:42:07'),
('310103', 'ID', 'Kec. Tambelan', '2019-06-10 04:42:07'),
('310104', 'ID', 'Kec. Teluk Bintan', '2019-06-10 04:42:07'),
('310105', 'ID', 'Kec. Teluk Sebong', '2019-06-10 04:42:07'),
('310106', 'ID', 'Kec. Toapaya', '2019-06-10 04:42:07'),
('310107', 'ID', 'Kec. Mantang', '2019-06-10 04:42:07'),
('310108', 'ID', 'Kec. Bintan Pesisir', '2019-06-10 04:42:07'),
('310109', 'ID', 'Kec. Seri Kuala Loban', '2019-06-10 04:42:07'),
('310110', 'ID', 'Kec. Bintan Timur', '2019-06-10 04:42:07'),
('310200', 'ID', 'Kab. Karimun', '2019-06-10 04:42:07'),
('310201', 'ID', 'Kec. Moro', '2019-06-10 04:42:07'),
('310202', 'ID', 'Kec. Kundur', '2019-06-10 04:42:07'),
('310203', 'ID', 'Kec. Karimun', '2019-06-10 04:42:07'),
('310204', 'ID', 'Kec. Meral', '2019-06-10 04:42:07'),
('310205', 'ID', 'Kec. Tebing', '2019-06-10 04:42:07'),
('310206', 'ID', 'Kec. Buru', '2019-06-10 04:42:07'),
('310207', 'ID', 'Kec. Kundur Utara', '2019-06-10 04:42:07'),
('310208', 'ID', 'Kec. Kundur Barat', '2019-06-10 04:42:07'),
('310209', 'ID', 'Kec. Durai', '2019-06-10 04:42:07'),
('310210', 'ID', 'Kec. Meral Barat', '2020-02-10 03:07:44'),
('310211', 'ID', 'Kec. Ungar', '2020-02-10 03:07:44'),
('310212', 'ID', 'Kec. Belat', '2020-02-10 03:07:44'),
('310300', 'ID', 'Kab. Natuna', '2019-06-10 04:42:07'),
('310303', 'ID', 'Kec. Midai', '2019-06-10 04:42:07'),
('310304', 'ID', 'Kec. Bunguran Barat', '2019-06-10 04:42:07'),
('310305', 'ID', 'Kec. Bunguran Timur', '2019-06-10 04:42:07'),
('310306', 'ID', 'Kec. Serasan', '2019-06-10 04:42:07'),
('310309', 'ID', 'Kec. Subi', '2019-06-10 04:42:07'),
('310311', 'ID', 'Kec. Bunguran Utara', '2019-06-10 04:42:07'),
('310312', 'ID', 'Kec. Pulau Laut', '2019-06-10 04:42:07'),
('310313', 'ID', 'Kec. Pulau Tiga', '2019-06-10 04:42:07'),
('310317', 'ID', 'Kec. Bunguran Timur Laut', '2019-06-10 04:42:07'),
('310318', 'ID', 'Kec. Bunguran Tengah', '2019-06-10 04:42:07'),
('310320', 'ID', 'Kec. Bunguran Selatan', '2019-06-10 04:42:07'),
('310321', 'ID', 'Kec. Serasan Timur', '2019-06-10 04:42:07'),
('310400', 'ID', 'Kab. Lingga', '2019-06-10 04:42:07'),
('310401', 'ID', 'Kec. Singkep', '2019-06-10 04:42:07'),
('310402', 'ID', 'Kec. Lingga', '2019-06-10 04:42:07'),
('310403', 'ID', 'Kec. Senayang', '2019-06-10 04:42:07'),
('310404', 'ID', 'Kec. Singkep Barat', '2019-06-10 04:42:07'),
('310405', 'ID', 'Kec. Lingga Utara', '2019-06-10 04:42:07'),
('310406', 'ID', 'Kec. Singkep Pesisir', '2020-02-10 03:07:44'),
('310407', 'ID', 'Kec. Singkep Selatan', '2020-02-10 03:07:44'),
('310408', 'ID', 'Kec. Lingga Timur', '2020-02-10 03:07:44'),
('310409', 'ID', 'Kec. Selayar', '2020-02-10 03:07:44'),
('310410', 'ID', 'Kec. Kepulauan Posek', '2020-02-10 03:07:44'),
('310500', 'ID', 'Kab. Kepulauan Anambas', '2019-06-10 04:42:07'),
('310501', 'ID', 'Kec. Jemaja', '2019-06-10 04:42:07'),
('310502', 'ID', 'Kec. Jemaja Timur', '2019-06-10 04:42:07'),
('310503', 'ID', 'Kec. Siantan', '2019-06-10 04:42:07'),
('310504', 'ID', 'Kec. Palmatak', '2019-06-10 04:42:07'),
('310505', 'ID', 'Kec. Siantan Selatan', '2019-06-10 04:42:07'),
('310506', 'ID', 'Kec. Siantan Timur', '2019-06-10 04:42:07'),
('310507', 'ID', 'Kec. Siantan Tengah', '2019-06-10 04:42:07'),
('316000', 'ID', 'Kota Batam', '2019-06-10 04:42:07'),
('316001', 'ID', 'Kec. Belakang Padang', '2019-06-10 04:42:07'),
('316002', 'ID', 'Kec. Sekupang', '2019-06-10 04:42:07'),
('316003', 'ID', 'Kec. Sei Beduk', '2019-06-10 04:42:07'),
('316004', 'ID', 'Kec. Bulang', '2019-06-10 04:42:07'),
('316005', 'ID', 'Kec. Lubuk Baja', '2019-06-10 04:42:07'),
('316006', 'ID', 'Kec. Batu Ampar', '2019-06-10 04:42:07'),
('316007', 'ID', 'Kec. Nongsa', '2019-06-10 04:42:07'),
('316008', 'ID', 'Kec. Galang', '2019-06-10 04:42:07'),
('316011', 'ID', 'Kec. Bengkong', '2019-06-10 04:42:07'),
('316012', 'ID', 'Kec. Batam Kota', '2019-06-10 04:42:07'),
('316013', 'ID', 'Kec. Sagulung', '2019-06-10 04:42:07'),
('316014', 'ID', 'Kec. Batu Aji', '2019-06-10 04:42:07'),
('316100', 'ID', 'Kota Tanjungpinang', '2019-06-10 04:42:07'),
('316101', 'ID', 'Kec. Tanjung Pinang Barat', '2019-06-10 04:42:07'),
('316102', 'ID', 'Kec. Tanjung Pinang Timur', '2019-06-10 04:42:07'),
('316103', 'ID', 'Kec. Bukit Bestari', '2019-06-10 04:42:07'),
('316104', 'ID', 'Kec. Tanjung Pinang Kota', '2019-06-10 04:42:07'),
('320000', 'ID', 'Prop. Papua Barat', '2019-06-10 04:42:07'),
('320100', 'ID', 'Kab. Fak-Fak', '2019-06-10 04:42:07'),
('320101', 'ID', 'Kec.  Fak-Fak Timur', '2019-06-10 04:42:07'),
('320102', 'ID', 'Kec.  Karas', '2019-06-10 04:42:07'),
('320103', 'ID', 'Kec.  Fak-Fak', '2019-06-10 04:42:07'),
('320104', 'ID', 'Kec.  Fak-Fak Tengah', '2019-06-10 04:42:07'),
('320105', 'ID', 'Kec.  Fak-Fak Barat', '2019-06-10 04:42:07'),
('320106', 'ID', 'Kec.  Kokas', '2019-06-10 04:42:07'),
('320107', 'ID', 'Kec.  Teluk Patipi', '2019-06-10 04:42:07'),
('320108', 'ID', 'Kec.  Kramongmongga', '2019-06-10 04:42:07'),
('320109', 'ID', 'Kec.  Bomberay', '2019-06-10 04:42:07'),
('320110', 'ID', 'Kec. Pariwari', '2020-02-10 03:07:44'),
('320111', 'ID', 'Kec. Wartutin', '2020-02-10 03:07:44'),
('320112', 'ID', 'Kec. Fakfak Timur Tengah', '2020-02-10 03:07:44'),
('320113', 'ID', 'Kec. Arguni', '2020-02-10 03:07:44'),
('320114', 'ID', 'Kec. Mbahamdandara', '2020-02-10 03:07:44'),
('320115', 'ID', 'Kec. Kayauni', '2020-02-10 03:07:44'),
('320116', 'ID', 'Kec. Furwagi', '2020-02-10 03:07:44'),
('320117', 'ID', 'Kec. Tomage', '2020-02-10 03:07:44'),
('320200', 'ID', 'Kab. Kaimana', '2019-06-10 04:42:07'),
('320201', 'ID', 'Kec.  Buruway', '2019-06-10 04:42:07'),
('320202', 'ID', 'Kec.  Teluk Arguni (Atas)', '2019-06-10 04:42:07'),
('320203', 'ID', 'Kec.  Teluk Arguni Bawah', '2019-06-10 04:42:07'),
('320204', 'ID', 'Kec.  Kaimana', '2019-06-10 04:42:07'),
('320205', 'ID', 'Kec.  Kambrau', '2019-06-10 04:42:07'),
('320206', 'ID', 'Kec.  Teluk Etna', '2019-06-10 04:42:07'),
('320207', 'ID', 'Kec.  Yamor', '2019-06-10 04:42:07'),
('320300', 'ID', 'Kab. Teluk Wondama', '2019-06-10 04:42:07'),
('320301', 'ID', 'Kec.  Naikere', '2019-06-10 04:42:07'),
('320302', 'ID', 'Kec.  Wondiboy', '2019-06-10 04:42:07'),
('320303', 'ID', 'Kec.  Rasiei', '2019-06-10 04:42:07'),
('320304', 'ID', 'Kec.  Kuri Wamesa', '2019-06-10 04:42:07'),
('320305', 'ID', 'Kec.  Wasior', '2019-06-10 04:42:07'),
('320306', 'ID', 'Kec.  Teluk Duairi', '2019-06-10 04:42:07'),
('320307', 'ID', 'Kec.  Roon', '2019-06-10 04:42:07'),
('320308', 'ID', 'Kec.  Windesi', '2019-06-10 04:42:07'),
('320309', 'ID', 'Kec.  Wamesa', '2019-06-10 04:42:07'),
('320310', 'ID', 'Kec.  Roswar', '2019-06-10 04:42:07'),
('320311', 'ID', 'Kec.  Rumberpon', '2019-06-10 04:42:07'),
('320312', 'ID', 'Kec.  Soug Jaya', '2019-06-10 04:42:07'),
('320313', 'ID', 'Kec.  Nikiwar', '2019-06-10 04:42:07'),
('320390', 'ID', 'Sough Wepu', '2019-06-10 04:42:07'),
('320400', 'ID', 'Kab. Teluk Bintuni', '2019-06-10 04:42:07'),
('320401', 'ID', 'Kec. Irorutu/Fafuwar', '2019-06-10 04:42:07'),
('320402', 'ID', 'Kec. Babo', '2019-06-10 04:42:07'),
('320403', 'ID', 'Kec. Sumuri', '2019-06-10 04:42:07'),
('320404', 'ID', 'Kec. Aroba', '2019-06-10 04:42:07'),
('320405', 'ID', 'Kec. Kaitaro', '2019-06-10 04:42:07'),
('320406', 'ID', 'Kec. Kuri', '2019-06-10 04:42:07'),
('320407', 'ID', 'Kec. Idoor', '2019-06-10 04:42:07'),
('320408', 'ID', 'Kec. Bintuni', '2019-06-10 04:42:07'),
('320409', 'ID', 'Kec. Manimeri', '2019-06-10 04:42:07'),
('320410', 'ID', 'Kec. Tuhiba', '2019-06-10 04:42:07'),
('320411', 'ID', 'Kec. Dataran Beimes', '2019-06-10 04:42:07'),
('320412', 'ID', 'Kec. Tembuni', '2019-06-10 04:42:07'),
('320413', 'ID', 'Kec. Aranday', '2019-06-10 04:42:07'),
('320414', 'ID', 'Kec. Komundan', '2019-06-10 04:42:07'),
('320415', 'ID', 'Kec. Tomu', '2019-06-10 04:42:07'),
('320416', 'ID', 'Kec. Weriagar', '2019-06-10 04:42:07'),
('320417', 'ID', 'Kec. Moskona Selatan', '2019-06-10 04:42:07'),
('320418', 'ID', 'Kec. Meyado', '2019-06-10 04:42:07'),
('320419', 'ID', 'Kec. Moskona Barat', '2019-06-10 04:42:07'),
('320420', 'ID', 'Kec. Merdey', '2019-06-10 04:42:07'),
('320421', 'ID', 'Kec. Biscoop', '2019-06-10 04:42:07'),
('320422', 'ID', 'Kec. Masyeta', '2019-06-10 04:42:07'),
('320423', 'ID', 'Kec. Moskona Utara', '2019-06-10 04:42:07'),
('320424', 'ID', 'Kec. Moskona Timur', '2019-06-10 04:42:07'),
('320425', 'ID', 'Wamesa', '2019-06-10 04:42:07'),
('320500', 'ID', 'Kab. Manokwari', '2019-06-10 04:42:07'),
('320501', 'ID', 'Kec. Ransiki', '2019-06-10 04:42:07'),
('320502', 'ID', 'Kec. Momi Waren', '2019-06-10 04:42:07'),
('320503', 'ID', 'Kec. Nenei', '2019-06-10 04:42:07'),
('320504', 'ID', 'Kec. Sururay', '2019-06-10 04:42:07'),
('320505', 'ID', 'Kec. Anggi', '2019-06-10 04:42:07'),
('320506', 'ID', 'Kec. Taige', '2019-06-10 04:42:07'),
('320507', 'ID', 'Kec. Membey', '2019-06-10 04:42:07'),
('320508', 'ID', 'Kec. Oransbari', '2019-06-10 04:42:07'),
('320509', 'ID', 'Kec. Warmare', '2019-06-10 04:42:07'),
('320510', 'ID', 'Kec. Prafi', '2019-06-10 04:42:07'),
('320511', 'ID', 'Kec. Menyambouw', '2019-06-10 04:42:07'),
('320512', 'ID', 'Kec. Catubouw', '2019-06-10 04:42:07'),
('320513', 'ID', 'Kec. Manokwari Barat', '2019-06-10 04:42:07'),
('320514', 'ID', 'Kec. Manokwari Timur', '2019-06-10 04:42:07'),
('320515', 'ID', 'Kec. Manokwari Utara', '2019-06-10 04:42:07'),
('320516', 'ID', 'Kec. Manokwari Selatan', '2019-06-10 04:42:07'),
('320517', 'ID', 'Kec. Testega', '2019-06-10 04:42:07'),
('320518', 'ID', 'Kec. Tanah Rubu', '2019-06-10 04:42:07'),
('320519', 'ID', 'Kec. Kebar', '2019-06-10 04:42:07'),
('320520', 'ID', 'Kec. Senopi', '2019-06-10 04:42:07'),
('320521', 'ID', 'Kec. Amberbaken', '2019-06-10 04:42:07'),
('320522', 'ID', 'Kec. Murbani/Arfu', '2019-06-10 04:42:07'),
('320523', 'ID', 'Kec. Masni', '2019-06-10 04:42:07'),
('320524', 'ID', 'Kec. Sidey', '2019-06-10 04:42:07'),
('320525', 'ID', 'Kec. Tahosta', '2019-06-10 04:42:07'),
('320526', 'ID', 'Kec. Didohu', '2019-06-10 04:42:07'),
('320527', 'ID', 'Kec. Dataran Isim', '2019-06-10 04:42:07'),
('320528', 'ID', 'Kec. Anggi Gida', '2019-06-10 04:42:07'),
('320529', 'ID', 'Kec. Hingk', '2019-06-10 04:42:07'),
('320530', 'ID', 'Neney', '2019-06-10 04:42:07'),
('320531', 'ID', 'Momi - Waren', '2019-06-10 04:42:07'),
('320532', 'ID', 'Tohota', '2019-06-10 04:42:07'),
('320533', 'ID', 'Taige', '2019-06-10 04:42:07'),
('320534', 'ID', 'Membey', '2019-06-10 04:42:07'),
('320535', 'ID', 'Anggi Gida', '2019-06-10 04:42:07'),
('320536', 'ID', 'Didohu', '2019-06-10 04:42:07'),
('320537', 'ID', 'Dataran Isim', '2019-06-10 04:42:07'),
('320538', 'ID', 'Catubouw', '2019-06-10 04:42:07'),
('320539', 'ID', 'Hink', '2019-06-10 04:42:07'),
('320590', 'ID', 'Ransiki', '2019-06-10 04:42:07'),
('320591', 'ID', 'Kec. Anggi', '2020-02-10 03:07:44'),
('320600', 'ID', 'Kab. Sorong Selatan', '2019-06-10 04:42:07'),
('320601', 'ID', 'Kec.  Inanwatan', '2019-06-10 04:42:07'),
('320602', 'ID', 'Kec. Kokoda', '2019-06-10 04:42:07'),
('320603', 'ID', 'Kec. Metemeini Kais', '2019-06-10 04:42:07'),
('320608', 'ID', 'Kec. Moswaren', '2019-06-10 04:42:07'),
('320610', 'ID', 'Kec. Seremuk', '2019-06-10 04:42:07'),
('320611', 'ID', 'Kec. Wayer', '2019-06-10 04:42:07'),
('320612', 'ID', 'Kec. Sawiat', '2019-06-10 04:42:07');
INSERT INTO `dikti_wilayah` (`id_wilayah`, `id_negara`, `nama_wilayah`, `dikti_wilayah_timestamp`) VALUES
('320616', 'ID', 'Kec.  Kais', '2019-06-10 04:42:07'),
('320617', 'ID', 'Kec.  Konda', '2019-06-10 04:42:07'),
('320618', 'ID', 'Kec.  Kokoda Utara', '2019-06-10 04:42:07'),
('320619', 'ID', 'Kec.  Saifi', '2019-06-10 04:42:07'),
('320620', 'ID', 'Kec.  Fokour', '2019-06-10 04:42:07'),
('320621', 'ID', 'Kec.  Teminabuan', '2019-06-10 04:42:07'),
('320622', 'ID', 'Kec. Kais Darat', '2020-02-10 03:07:44'),
('320623', 'ID', 'Kec. Salkma', '2020-02-10 03:07:44'),
('320700', 'ID', 'Kab. Sorong', '2019-06-10 04:42:07'),
('320705', 'ID', 'Kec. Moraid', '2019-06-10 04:42:07'),
('320706', 'ID', 'Kec. Makbon', '2019-06-10 04:42:07'),
('320707', 'ID', 'Kec. Beraur', '2019-06-10 04:42:07'),
('320708', 'ID', 'Kec. Klamono', '2019-06-10 04:42:07'),
('320709', 'ID', 'Kec. Salawati', '2019-06-10 04:42:07'),
('320710', 'ID', 'Kec. Manyamuk', '2019-06-10 04:42:07'),
('320711', 'ID', 'Kec. Seget', '2019-06-10 04:42:07'),
('320712', 'ID', 'Kec. Segun', '2019-06-10 04:42:07'),
('320713', 'ID', 'Kec. Salawati Selatan', '2019-06-10 04:42:07'),
('320714', 'ID', 'Kec. Aimas', '2019-06-10 04:42:07'),
('320715', 'ID', 'Kec. Sayosa', '2019-06-10 04:42:07'),
('320716', 'ID', 'Kec.  Klabot', '2019-06-10 04:42:07'),
('320717', 'ID', 'Kec.  Klawak', '2019-06-10 04:42:07'),
('320718', 'ID', 'Kec.  Maudus', '2019-06-10 04:42:07'),
('320719', 'ID', 'Kec.  Mariat', '2019-06-10 04:42:07'),
('320720', 'ID', 'Kec.  Klayili', '2019-06-10 04:42:07'),
('320721', 'ID', 'Kec.  Klaso', '2019-06-10 04:42:07'),
('320722', 'ID', 'Kec.  Moisegen', '2019-06-10 04:42:07'),
('320723', 'ID', 'Kec. Mega', '2020-02-10 03:07:44'),
('320724', 'ID', 'Kec. Moraid/Mega', '2020-02-10 03:07:44'),
('320725', 'ID', 'Kec. Salawati Timur', '2020-02-10 03:07:44'),
('320726', 'ID', 'Kec. Sorong', '2020-02-10 03:07:44'),
('320800', 'ID', 'Kab. Raja Ampat', '2019-06-10 04:42:07'),
('320801', 'ID', 'Kec.  Misool Selatan', '2019-06-10 04:42:07'),
('320802', 'ID', 'Kec.  Misool (Misool Utara)', '2019-06-10 04:42:07'),
('320803', 'ID', 'Kec.  Kofiau', '2019-06-10 04:42:07'),
('320804', 'ID', 'Kec.  Misool Timur', '2019-06-10 04:42:07'),
('320805', 'ID', 'Kec.  Salawati Utara', '2019-06-10 04:42:07'),
('320807', 'ID', 'Kec.  Waigeo Selatan', '2019-06-10 04:42:07'),
('320808', 'ID', 'Kec.  Teluk Mayalibit', '2019-06-10 04:42:07'),
('320809', 'ID', 'Kec.  Meos Mansar', '2019-06-10 04:42:07'),
('320810', 'ID', 'Kec.  Waigeo Barat', '2019-06-10 04:42:07'),
('320811', 'ID', 'Kec.  Waigeo Utara', '2019-06-10 04:42:07'),
('320812', 'ID', 'Kec.  Kepulauan Ayau', '2019-06-10 04:42:07'),
('320813', 'ID', 'Kec.  Waigeo Timur', '2019-06-10 04:42:07'),
('320814', 'ID', 'Kec.  Warwarbomi', '2019-06-10 04:42:07'),
('320815', 'ID', 'Kec.  Waigeo Barat Kepulauan', '2019-06-10 04:42:07'),
('320816', 'ID', 'Kec.  Misool Barat', '2019-06-10 04:42:07'),
('320817', 'ID', 'Kec.  Kepulauan Sembilan', '2019-06-10 04:42:07'),
('320818', 'ID', 'Kec.  Kota Waisai', '2019-06-10 04:42:07'),
('320819', 'ID', 'Kec.  Tiplol Mayalibit', '2019-06-10 04:42:07'),
('320820', 'ID', 'Kec.  Batanta Utara', '2019-06-10 04:42:07'),
('320821', 'ID', 'Kec.  Salawati Barat', '2019-06-10 04:42:07'),
('320822', 'ID', 'Kec.  Salawati Tengah', '2019-06-10 04:42:07'),
('320823', 'ID', 'Kec.  Supnin', '2019-06-10 04:42:07'),
('320824', 'ID', 'Kec.  Ayau', '2019-06-10 04:42:07'),
('320825', 'ID', 'Kec.  Batanta Selatan', '2019-06-10 04:42:07'),
('320890', 'ID', 'Selat Sagawin', '2019-06-10 04:42:07'),
('320900', 'ID', 'Kab. Tambrauw', '2019-06-10 04:42:07'),
('320901', 'ID', 'Kec.  Fef', '2019-06-10 04:42:07'),
('320902', 'ID', 'Kec.  Miyah', '2019-06-10 04:42:07'),
('320903', 'ID', 'Kec.  Yembun', '2019-06-10 04:42:07'),
('320904', 'ID', 'Kec.  Kwoor', '2019-06-10 04:42:07'),
('320905', 'ID', 'Kec.  Sausapor', '2019-06-10 04:42:07'),
('320906', 'ID', 'Kec.  Abun', '2019-06-10 04:42:07'),
('320907', 'ID', 'Kec. Amberbaken', '2020-02-10 03:07:44'),
('320908', 'ID', 'Kec. Kebar', '2020-02-10 03:07:44'),
('320909', 'ID', 'Kec. Senopi', '2020-02-10 03:07:44'),
('320910', 'ID', 'Kec. Mubrani', '2020-02-10 03:07:44'),
('320911', 'ID', 'Kec. Syujak', '2020-02-10 03:07:44'),
('320912', 'ID', 'Kec. Moraid', '2020-02-10 03:07:44'),
('320913', 'ID', 'Kec. Bikar', '2020-02-10 03:07:44'),
('320914', 'ID', 'Kec. Bamusbama', '2020-02-10 03:07:44'),
('320915', 'ID', 'Kec. Ases', '2020-02-10 03:07:44'),
('320916', 'ID', 'Kec. Miyah Selatan', '2020-02-10 03:07:44'),
('320917', 'ID', 'Kec. Ireres', '2020-02-10 03:07:44'),
('320918', 'ID', 'Kec. Tobouw', '2020-02-10 03:07:44'),
('320919', 'ID', 'Kec. Wilhem Roumbouts', '2020-02-10 03:07:44'),
('320920', 'ID', 'Kec. Kebar Timur', '2020-02-10 03:07:44'),
('320921', 'ID', 'Kec. Tinggouw', '2020-02-10 03:07:44'),
('320922', 'ID', 'Kec. Kwesefo', '2020-02-10 03:07:44'),
('320923', 'ID', 'Kec. Mawabuan', '2020-02-10 03:07:44'),
('320924', 'ID', 'Kec. Kebar Selatan', '2020-02-10 03:07:44'),
('320925', 'ID', 'Kec. Manekar', '2020-02-10 03:07:44'),
('320926', 'ID', 'Kec. Mpur', '2020-02-10 03:07:44'),
('320927', 'ID', 'Kec. Amberbaken Barat', '2020-02-10 03:07:44'),
('320928', 'ID', 'Kec. Kasi', '2020-02-10 03:07:44'),
('320929', 'ID', 'Kec. Selemkai', '2020-02-10 03:07:44'),
('321000', 'ID', 'Kab. Maybrat', '2019-06-10 04:42:07'),
('321001', 'ID', 'Kec.  Aifat', '2019-06-10 04:42:07'),
('321002', 'ID', 'Kec.  Aifat Utara', '2019-06-10 04:42:07'),
('321003', 'ID', 'Kec.  Aifat Timur', '2019-06-10 04:42:07'),
('321004', 'ID', 'Kec.  Aifat Selatan', '2019-06-10 04:42:07'),
('321005', 'ID', 'Kec.  Aitinyo Barat', '2019-06-10 04:42:07'),
('321006', 'ID', 'Kec.  Aitinyo', '2019-06-10 04:42:07'),
('321007', 'ID', 'Kec.  Aitinyo Utara', '2019-06-10 04:42:07'),
('321008', 'ID', 'Kec.  Ayamaru', '2019-06-10 04:42:07'),
('321009', 'ID', 'Kec.  Ayamaru Utara', '2019-06-10 04:42:07'),
('321010', 'ID', 'Kec.  Ayamaru Timur', '2019-06-10 04:42:07'),
('321011', 'ID', 'Kec.  Mare', '2019-06-10 04:42:07'),
('321012', 'ID', 'Aitinyo Tengah', '2019-06-10 04:42:07'),
('321013', 'ID', 'Aifat Timur Selatan', '2019-06-10 04:42:07'),
('321014', 'ID', 'Kec. Ayamaru Barat', '2020-02-10 03:07:44'),
('321015', 'ID', 'Aitinyo Raya', '2019-06-10 04:42:07'),
('321016', 'ID', 'Kec. Ayamaru Tengah', '2020-02-10 03:07:44'),
('321017', 'ID', 'Kec. Ayamaru Selatan', '2020-02-10 03:07:44'),
('321018', 'ID', 'Kec. Ayamaru Utara Timur', '2020-02-10 03:07:44'),
('321019', 'ID', 'Kec. Ayamaru Selatan Jaya', '2020-02-10 03:07:44'),
('321020', 'ID', 'Kec. Mare Selatan', '2020-02-10 03:07:44'),
('321090', 'ID', 'Ayamaru Timur Selatan', '2019-06-10 04:42:07'),
('321091', 'ID', 'Aifat Timur Tengah', '2019-06-10 04:42:07'),
('321092', 'ID', 'Aifat Timur Jauh', '2019-06-10 04:42:07'),
('321099', 'ID', 'Ayamaru Jaya', '2019-06-10 04:42:07'),
('321100', 'ID', 'Kab. Pegunungan Arfak', '2020-02-10 03:07:44'),
('321111', 'ID', 'Kec. Anggi Gida', '2020-02-10 03:07:44'),
('321112', 'ID', 'Kec. Membey', '2020-02-10 03:07:44'),
('321113', 'ID', 'Kec. Sururay', '2020-02-10 03:07:44'),
('321114', 'ID', 'Kec. Didohu', '2020-02-10 03:07:44'),
('321115', 'ID', 'Kec. Taige', '2020-02-10 03:07:44'),
('321116', 'ID', 'Kec. Catubouw', '2020-02-10 03:07:44'),
('321117', 'ID', 'Kec. Testega', '2020-02-10 03:07:44'),
('321118', 'ID', 'Kec. Menyambouw', '2020-02-10 03:07:44'),
('321119', 'ID', 'Kec. Hingk', '2020-02-10 03:07:44'),
('321120', 'ID', 'Kec. Anggi', '2020-02-10 03:07:44'),
('321200', 'ID', 'Kab. Manokwari Selatan', '2020-02-10 03:07:44'),
('321206', 'ID', 'Kec. Ransiki', '2020-02-10 03:07:44'),
('321221', 'ID', 'Kec. Oransbari', '2020-02-10 03:07:44'),
('321222', 'ID', 'Kec. Nenei', '2020-02-10 03:07:44'),
('321223', 'ID', 'Kec. Dataran Isim', '2020-02-10 03:07:44'),
('321224', 'ID', 'Kec. Momi Waren', '2020-02-10 03:07:44'),
('321225', 'ID', 'Kec. Tahota', '2020-02-10 03:07:44'),
('326000', 'ID', 'Kota Sorong', '2019-06-10 04:42:07'),
('326002', 'ID', 'Kec.  Sorong Timur', '2019-06-10 04:42:07'),
('326003', 'ID', 'Kec.  Sorong', '2019-06-10 04:42:07'),
('326006', 'ID', 'Kec.  Klablim', '2019-06-10 04:42:07'),
('326007', 'ID', 'Kec.  Klawalu', '2019-06-10 04:42:07'),
('326008', 'ID', 'Kec.  Giwu', '2019-06-10 04:42:07'),
('326009', 'ID', 'Kec.  Klamana', '2019-06-10 04:42:07'),
('326010', 'ID', 'Kec. Klaurung', '2020-02-10 03:07:44'),
('326011', 'ID', 'Kec. Malaimsimsa', '2020-02-10 03:07:44'),
('326012', 'ID', 'Kec. Maladomes', '2020-02-10 03:07:44'),
('326013', 'ID', 'Kec. Sorong', '2020-02-10 03:07:44'),
('330000', 'ID', 'Prop. Sulawesi Barat', '2019-06-10 04:42:07'),
('330100', 'ID', 'Kab. Mamuju', '2019-06-10 04:42:07'),
('330101', 'ID', 'Kec. Tapalang', '2019-06-10 04:42:07'),
('330102', 'ID', 'Kec. Mamuju', '2019-06-10 04:42:07'),
('330103', 'ID', 'Kec. Kalukku', '2019-06-10 04:42:07'),
('330104', 'ID', 'Kec. Kalumpang', '2019-06-10 04:42:07'),
('330105', 'ID', 'Kec. Budong-Budong', '2019-06-10 04:42:07'),
('330106', 'ID', 'Kec. Tapalang Barat', '2019-06-10 04:42:07'),
('330107', 'ID', 'Kec. Papalang', '2019-06-10 04:42:07'),
('330108', 'ID', 'Kec. Sampaga', '2019-06-10 04:42:07'),
('330109', 'ID', 'Kec. Pangale', '2019-06-10 04:42:07'),
('330110', 'ID', 'Kec. Tommo', '2019-06-10 04:42:07'),
('330111', 'ID', 'Kec. Bonehau', '2019-06-10 04:42:07'),
('330112', 'ID', 'Kec. Topoyo', '2019-06-10 04:42:07'),
('330113', 'ID', 'Kec. Tobadak', '2019-06-10 04:42:07'),
('330114', 'ID', 'Kec. Karossa', '2019-06-10 04:42:07'),
('330115', 'ID', 'Kec. Simboro Kepulauan', '2019-06-10 04:42:07'),
('330116', 'ID', 'Kec. Kep. Bala Balakang', '2019-06-10 04:42:07'),
('330117', 'ID', 'Topoyo', '2019-06-10 04:42:07'),
('330190', 'ID', 'Budong-budong', '2019-06-10 04:42:07'),
('330191', 'ID', 'Pangale', '2019-06-10 04:42:07'),
('330200', 'ID', 'Kab. Mamuju Utara', '2019-06-10 04:42:07'),
('330201', 'ID', 'Kec. Pasangkayu', '2019-06-10 04:42:07'),
('330202', 'ID', 'Kec. Sarudu', '2019-06-10 04:42:07'),
('330203', 'ID', 'Kec. Baras', '2019-06-10 04:42:07'),
('330204', 'ID', 'Kec. Bambalamutu', '2019-06-10 04:42:07'),
('330205', 'ID', 'Kec. Dapurang', '2019-06-10 04:42:07'),
('330206', 'ID', 'Kec. Duripoku', '2019-06-10 04:42:07'),
('330207', 'ID', 'Kec. Bulu Taba', '2019-06-10 04:42:07'),
('330208', 'ID', 'Kec. Lariang', '2019-06-10 04:42:07'),
('330209', 'ID', 'Kec. Tikke Raya', '2019-06-10 04:42:07'),
('330210', 'ID', 'Kec. Pedongga', '2019-06-10 04:42:07'),
('330211', 'ID', 'Kec. Bambaira', '2019-06-10 04:42:07'),
('330212', 'ID', 'Kec. Sarjo', '2019-06-10 04:42:07'),
('330292', 'ID', 'Karossa', '2019-06-10 04:42:07'),
('330293', 'ID', 'Simboro Dan Kepulauan', '2019-06-10 04:42:07'),
('330294', 'ID', 'Tobadak', '2019-06-10 04:42:07'),
('330300', 'ID', 'Kab. Polewali Mamasa', '2019-06-10 04:42:07'),
('330301', 'ID', 'Kec. Tinambung', '2019-06-10 04:42:07'),
('330302', 'ID', 'Kec. Tutallu', '2019-06-10 04:42:07'),
('330303', 'ID', 'Kec. Campalagian', '2019-06-10 04:42:07'),
('330304', 'ID', 'Kec. Wonomulyo', '2019-06-10 04:42:07'),
('330305', 'ID', 'Kec. Polewali', '2019-06-10 04:42:07'),
('330306', 'ID', 'Kec. Limboro', '2019-06-10 04:42:07'),
('330307', 'ID', 'Kec. Balanipa', '2019-06-10 04:42:07'),
('330308', 'ID', 'Kec. Luyo', '2019-06-10 04:42:07'),
('330309', 'ID', 'Kec. Allu', '2019-06-10 04:42:07'),
('330310', 'ID', 'Kec. Mapili', '2019-06-10 04:42:07'),
('330311', 'ID', 'Kec. Matakali', '2019-06-10 04:42:07'),
('330312', 'ID', 'Kec. Binuang', '2019-06-10 04:42:07'),
('330313', 'ID', 'Kec. Anreapi', '2019-06-10 04:42:07'),
('330314', 'ID', 'Kec. Tapango', '2019-06-10 04:42:07'),
('330315', 'ID', 'Kec. Matangnga', '2019-06-10 04:42:07'),
('330316', 'ID', 'Kec. Bulo', '2019-06-10 04:42:07'),
('330317', 'ID', 'Tutar', '2019-06-10 04:42:07'),
('330400', 'ID', 'Kab. Mamasa', '2019-06-10 04:42:07'),
('330401', 'ID', 'Kec. Sumarorong', '2019-06-10 04:42:07'),
('330402', 'ID', 'Kec. Pana', '2019-06-10 04:42:07'),
('330403', 'ID', 'Kec. Mamasa', '2019-06-10 04:42:07'),
('330404', 'ID', 'Kec. Mambi', '2019-06-10 04:42:07'),
('330405', 'ID', 'Kec. Tabulahan', '2019-06-10 04:42:07'),
('330406', 'ID', 'Kec. Tabang', '2019-06-10 04:42:07'),
('330407', 'ID', 'Kec. Messawa', '2019-06-10 04:42:07'),
('330408', 'ID', 'Kec. Sesenapadang', '2019-06-10 04:42:07'),
('330409', 'ID', 'Kec. Tandukalua', '2019-06-10 04:42:07'),
('330410', 'ID', 'Kec. Aralle', '2019-06-10 04:42:07'),
('330411', 'ID', 'Kec. Nosu', '2019-06-10 04:42:07'),
('330412', 'ID', 'Kec. Bambang', '2019-06-10 04:42:07'),
('330413', 'ID', 'Kec. Balla', '2019-06-10 04:42:07'),
('330414', 'ID', 'Kec. Tawalian', '2019-06-10 04:42:07'),
('330415', 'ID', 'Kec. Rantebulahan Timur', '2019-06-10 04:42:07'),
('330416', 'ID', 'Kec. Buntumalangka', '2019-06-10 04:42:07'),
('330417', 'ID', 'Kec. Mehalaan', '2019-06-10 04:42:07'),
('330500', 'ID', 'Kab. Majene', '2019-06-10 04:42:07'),
('330502', 'ID', 'Kec. Pamboang', '2019-06-10 04:42:07'),
('330505', 'ID', 'Kec. Ulumunda', '2019-06-10 04:42:07'),
('330506', 'ID', 'Kec. Tammerodo Sendana', '2019-06-10 04:42:07'),
('330507', 'ID', 'Kec. Tubo Sendana', '2019-06-10 04:42:07'),
('330509', 'ID', 'Kec. Banggai', '2019-06-10 04:42:07'),
('330510', 'ID', 'Kec. Sendana', '2019-06-10 04:42:07'),
('330511', 'ID', 'Kec. Malunda', '2019-06-10 04:42:07'),
('330512', 'ID', 'Kec. Banggai Timur', '2019-06-10 04:42:07'),
('330600', 'ID', 'Kab. Mamuju Tengah', '2020-02-10 03:07:44'),
('330601', 'ID', 'Kec. Budong-Budong', '2020-02-10 03:07:44'),
('330602', 'ID', 'Kec. Karossa', '2020-02-10 03:07:44'),
('330603', 'ID', 'Kec. Topoyo', '2020-02-10 03:07:44'),
('330604', 'ID', 'Kec. Tobadak', '2020-02-10 03:07:44'),
('330605', 'ID', 'Kec. Pangale', '2020-02-10 03:07:44'),
('340000', 'ID', 'Prop. Kalimantan Utara', '2019-06-10 04:42:07'),
('340001', 'ID', 'Tanjung Palas Utara', '2019-06-10 04:42:07'),
('340002', 'ID', 'Peso Ilir', '2019-06-10 04:42:07'),
('340003', 'ID', 'Tanjung Palas', '2019-06-10 04:42:07'),
('340004', 'ID', 'Tanjung Palas Barat', '2019-06-10 04:42:07'),
('340005', 'ID', 'Tanjung Selor', '2019-06-10 04:42:07'),
('340006', 'ID', 'Peso', '2019-06-10 04:42:07'),
('340007', 'ID', 'Tanjung Palas Tengah', '2019-06-10 04:42:07'),
('340008', 'ID', 'Bunyu', '2019-06-10 04:42:07'),
('340009', 'ID', 'Tanjung Palas Timur', '2019-06-10 04:42:07'),
('340010', 'ID', 'Sekatak', '2019-06-10 04:42:07'),
('340100', 'ID', 'Kab. Malinau', '2019-06-10 04:42:07'),
('340101', 'ID', 'Tarakan Utara', '2019-06-10 04:42:07'),
('340102', 'ID', 'Tarakan Tengah', '2019-06-10 04:42:07'),
('340103', 'ID', 'Tarakan Timur', '2019-06-10 04:42:07'),
('340104', 'ID', 'Tarakan Barat', '2019-06-10 04:42:07'),
('340105', 'ID', 'Kec. Mentarang', '2020-02-10 03:07:44'),
('340106', 'ID', 'Kec. Sungai Boh', '2020-02-10 03:07:44'),
('340107', 'ID', 'Kec. Malinau Selatan', '2020-02-10 03:07:44'),
('340108', 'ID', 'Kec. Malinau Barat', '2020-02-10 03:07:44'),
('340109', 'ID', 'Kec. Malinau Utara', '2020-02-10 03:07:44'),
('340110', 'ID', 'Kec. Kayan Selatan', '2020-02-10 03:07:44'),
('340111', 'ID', 'Kec. Bahau Hulu', '2020-02-10 03:07:44'),
('340112', 'ID', 'Kec. Mentarang Hulu', '2020-02-10 03:07:44'),
('340113', 'ID', 'Kec. Malinau Selatan Hilir', '2020-02-10 03:07:44'),
('340114', 'ID', 'Kec. Malinau Selatan Hulu', '2020-02-10 03:07:44'),
('340115', 'ID', 'Kec. Sungai Tubu', '2020-02-10 03:07:44'),
('340200', 'ID', 'Kab. Bulungan', '2019-06-10 04:42:07'),
('340202', 'ID', 'Kec. Tanjung Palas', '2020-02-10 03:07:44'),
('340203', 'ID', 'Kec. Sekatak', '2020-02-10 03:07:44'),
('340205', 'ID', 'Kec. Pulau Bunyu', '2020-02-10 03:07:44'),
('340206', 'ID', 'Kec. Tanjung Palas Barat', '2020-02-10 03:07:44'),
('340207', 'ID', 'Kec. Tanjung Palas Utara', '2020-02-10 03:07:44'),
('340208', 'ID', 'Kec. Tanjung Palas Timur', '2020-02-10 03:07:44'),
('340209', 'ID', 'Kec. Tanjung Selor', '2020-02-10 03:07:44'),
('340210', 'ID', 'Kec. Tanjung Palas Tengah', '2020-02-10 03:07:44'),
('340211', 'ID', 'Kec. Peso Hilir', '2020-02-10 03:07:44'),
('340213', 'ID', 'Kec. Peso', '2020-02-10 03:07:44'),
('340300', 'ID', 'Kab. Tana Tidung', '2019-06-10 04:42:07'),
('340301', 'ID', 'Kec. Sesayap', '2020-02-10 03:07:44'),
('340302', 'ID', 'Kec. Sesayap Hilir', '2020-02-10 03:07:44'),
('340303', 'ID', 'Kec. Tanah Lia', '2020-02-10 03:07:44'),
('340304', 'ID', 'Kec. Betayau', '2020-02-10 03:07:44'),
('340305', 'ID', 'Kec. Muruk Rian', '2020-02-10 03:07:44'),
('340500', 'ID', 'Kab. Nunukan', '2019-06-10 04:42:07'),
('340501', 'ID', 'Kec. Krayan', '2020-02-10 03:07:44'),
('340502', 'ID', 'Kec. Lumbis', '2020-02-10 03:07:44'),
('340503', 'ID', 'Kec. Sembakung', '2020-02-10 03:07:44'),
('340504', 'ID', 'Kec. Nunukan', '2020-02-10 03:07:44'),
('340505', 'ID', 'Kec. Sebatik', '2020-02-10 03:07:44'),
('340506', 'ID', 'Kec. Sebuku', '2020-02-10 03:07:44'),
('340507', 'ID', 'Kec. Krayan Selatan', '2020-02-10 03:07:44'),
('340508', 'ID', 'Kec. Sebatik Barat', '2020-02-10 03:07:44'),
('340509', 'ID', 'Kec. Nunukan Selatan', '2020-02-10 03:07:44'),
('340514', 'ID', 'Kec. Nunukan', '2020-02-10 03:07:44'),
('340515', 'ID', 'Kec. Krayan Selatan', '2020-02-10 03:07:44'),
('340516', 'ID', 'Kec. Krayan', '2020-02-10 03:07:44'),
('340517', 'ID', 'Kec. Nunukan Selatan', '2020-02-10 03:07:44'),
('340518', 'ID', 'Kec. Sebatik Timur', '2020-02-10 03:07:44'),
('340519', 'ID', 'Kec. Sebatik Utara', '2020-02-10 03:07:44'),
('340520', 'ID', 'Kec. Sebatik Tengah', '2020-02-10 03:07:44'),
('340521', 'ID', 'Kec. Sei Menggaris', '2020-02-10 03:07:44'),
('340522', 'ID', 'Kec. Tulin Onsoi', '2020-02-10 03:07:44'),
('340523', 'ID', 'Kec. Lumbis Ogong', '2020-02-10 03:07:44'),
('340524', 'ID', 'Kec. Sembakung Atulai', '2020-02-10 03:07:44'),
('346000', 'ID', 'Kota Tarakan', '2019-06-10 04:42:07'),
('346001', 'ID', 'Kec. Tarakan Timur', '2020-02-10 03:07:44'),
('346002', 'ID', 'Kec. Tarakan Tengah', '2020-02-10 03:07:44'),
('346003', 'ID', 'Kec. Tarakan Barat', '2020-02-10 03:07:44'),
('346004', 'ID', 'Kec. Tarakan Utara', '2020-02-10 03:07:44'),
('999999', 'ID', 'tidak ada', '2019-06-10 04:42:07'),
('AD000000', 'AD', 'Andorra', '2019-06-10 04:42:07'),
('AE000000', 'AE', 'United Arab Emirates', '2019-06-10 04:42:07'),
('AF000000', 'AF', 'Afghanistan', '2019-06-10 04:42:07'),
('AG000000', 'AG', 'Antigua And Barbuda', '2019-06-10 04:42:07'),
('AI000000', 'AI', 'Anguilla', '2019-06-10 04:42:07'),
('AL000000', 'AL', 'Albania', '2019-06-10 04:42:07'),
('AM000000', 'AM', 'Armenia', '2019-06-10 04:42:07'),
('AN000000', 'AN', 'Netherlands Antilles', '2019-06-10 04:42:07'),
('AO000000', 'AO', 'Angola', '2019-06-10 04:42:07'),
('AQ000000', 'AQ', 'Antarctica', '2019-06-10 04:42:07'),
('AR000000', 'AR', 'Argentina', '2019-06-10 04:42:07'),
('AS000000', 'AS', 'American Samoa', '2019-06-10 04:42:07'),
('AT000000', 'AT', 'Austria', '2019-06-10 04:42:07'),
('AU000000', 'AU', 'Australia', '2019-06-10 04:42:07'),
('AW000000', 'AW', 'Aruba', '2019-06-10 04:42:07'),
('AX000000', 'AX', 'Aland Islands', '2019-06-10 04:42:07'),
('AZ000000', 'AZ', 'Azerbaijan', '2019-06-10 04:42:07'),
('BA000000', 'BA', 'Bosnia And Herzegovina', '2019-06-10 04:42:07'),
('BB000000', 'BB', 'Barbados', '2019-06-10 04:42:07'),
('BD000000', 'BD', 'Bangladesh', '2019-06-10 04:42:07'),
('BE000000', 'BE', 'Belgium', '2019-06-10 04:42:07'),
('BF000000', 'BF', 'Burkina Faso', '2019-06-10 04:42:07'),
('BG000000', 'BG', 'Bulgaria', '2019-06-10 04:42:07'),
('BH000000', 'BH', 'Bahrain', '2019-06-10 04:42:07'),
('BI000000', 'BI', 'Burundi', '2019-06-10 04:42:07'),
('BJ000000', 'BJ', 'Benin', '2019-06-10 04:42:07'),
('BL000000', 'BL', 'Saint Bartelemey', '2019-06-10 04:42:07'),
('BM000000', 'BM', 'Bermuda', '2019-06-10 04:42:07'),
('BN000000', 'BN', 'Brunei Darussalam', '2019-06-10 04:42:07'),
('BO000000', 'BO', 'Bolivia', '2019-06-10 04:42:07'),
('BQ000000', 'BQ', 'Bonaire, Saint Eustatius and Saba', '2019-06-10 04:42:07'),
('BR000000', 'BR', 'Brazil', '2019-06-10 04:42:07'),
('BS000000', 'BS', 'Bahamas', '2019-06-10 04:42:07'),
('BT000000', 'BT', 'Bhutan', '2019-06-10 04:42:07'),
('BV000000', 'BV', 'Bouvet Island', '2019-06-10 04:42:07'),
('BW000000', 'BW', 'Botswana', '2019-06-10 04:42:07'),
('BY000000', 'BY', 'Belarus', '2019-06-10 04:42:07'),
('BZ000000', 'BZ', 'Belize', '2019-06-10 04:42:07'),
('CA000000', 'CA', 'Canada', '2019-06-10 04:42:07'),
('CC000000', 'CC', 'Cocos (Keeling) Islands', '2019-06-10 04:42:07'),
('CD000000', 'CD', 'Congo, The Democratic Republic Of The', '2019-06-10 04:42:07'),
('CF000000', 'CF', 'Central African Republic', '2019-06-10 04:42:07'),
('CG000000', 'CG', 'Congo', '2019-06-10 04:42:07'),
('CH000000', 'CH', 'Switzerland', '2019-06-10 04:42:07'),
('CI000000', 'CI', 'Cote D\'Ivoire', '2019-06-10 04:42:07'),
('CK000000', 'CK', 'Cook Islands', '2019-06-10 04:42:07'),
('CL000000', 'CL', 'Chile', '2019-06-10 04:42:07'),
('CM000000', 'CM', 'Cameroon', '2019-06-10 04:42:07'),
('CN000000', 'CN', 'China', '2019-06-10 04:42:07'),
('CO000000', 'CO', 'Colombia', '2019-06-10 04:42:07'),
('CR000000', 'CR', 'Costa Rica', '2019-06-10 04:42:07'),
('CS000000', 'CS', 'Serbia And Montenegro', '2019-06-10 04:42:07'),
('CU000000', 'CU', 'Cuba', '2019-06-10 04:42:07'),
('CV000000', 'CV', 'Cape Verde', '2019-06-10 04:42:07'),
('CW000000', 'CW', 'Curacao', '2019-06-10 04:42:07'),
('CX000000', 'CX', 'Christmas Island', '2019-06-10 04:42:07'),
('CY000000', 'CY', 'Cyprus', '2019-06-10 04:42:07'),
('CZ000000', 'CZ', 'Czech Republic', '2019-06-10 04:42:07'),
('DE000000', 'DE', 'Germany', '2019-06-10 04:42:07'),
('DJ000000', 'DJ', 'Djibouti', '2019-06-10 04:42:07'),
('DK000000', 'DK', 'Denmark', '2019-06-10 04:42:07'),
('DM000000', 'DM', 'Dominica', '2019-06-10 04:42:07'),
('DO000000', 'DO', 'Dominican Republic', '2019-06-10 04:42:07'),
('DZ000000', 'DZ', 'Algeria', '2019-06-10 04:42:07'),
('EC000000', 'EC', 'Ecuador', '2019-06-10 04:42:07'),
('EE000000', 'EE', 'Estonia', '2019-06-10 04:42:07'),
('EG000000', 'EG', 'Egypt', '2019-06-10 04:42:07'),
('EH000000', 'EH', 'Western Sahara', '2019-06-10 04:42:07'),
('ER000000', 'ER', 'Eritrea', '2019-06-10 04:42:07'),
('ES000000', 'ES', 'Spain', '2019-06-10 04:42:07'),
('ET000000', 'ET', 'Ethiopia', '2019-06-10 04:42:07'),
('FI000000', 'FI', 'Finland', '2019-06-10 04:42:07'),
('FJ000000', 'FJ', 'Fiji', '2019-06-10 04:42:07'),
('FK000000', 'FK', 'Falkland Islands (Malvinas)', '2019-06-10 04:42:07'),
('FM000000', 'FM', 'Micronesia, Federated States Of', '2019-06-10 04:42:07'),
('FO000000', 'FO', 'Faroe Islands', '2019-06-10 04:42:07'),
('FR000000', 'FR', 'France', '2019-06-10 04:42:07'),
('GA000000', 'GA', 'Gabon', '2019-06-10 04:42:07'),
('GB000000', 'GB', 'United Kingdom', '2019-06-10 04:42:07'),
('GD000000', 'GD', 'Grenada', '2019-06-10 04:42:07'),
('GE000000', 'GE', 'Georgia', '2019-06-10 04:42:07'),
('GF000000', 'GF', 'French Guiana', '2019-06-10 04:42:07'),
('GG000000', 'GG', 'Guernsey', '2019-06-10 04:42:07'),
('GH000000', 'GH', 'Ghana', '2019-06-10 04:42:07'),
('GI000000', 'GI', 'Gibraltar', '2019-06-10 04:42:07'),
('GL000000', 'GL', 'Greenland', '2019-06-10 04:42:07'),
('GM000000', 'GM', 'Gambia', '2019-06-10 04:42:07'),
('GN000000', 'GN', 'Guinea', '2019-06-10 04:42:07'),
('GP000000', 'GP', 'Guadeloupe', '2019-06-10 04:42:07'),
('GQ000000', 'GQ', 'Equatorial Guinea', '2019-06-10 04:42:07'),
('GR000000', 'GR', 'Greece', '2019-06-10 04:42:07'),
('GS000000', 'GS', 'South Georgia And The South Sandwich Islands', '2019-06-10 04:42:07'),
('GT000000', 'GT', 'Guatemala', '2019-06-10 04:42:07'),
('GU000000', 'GU', 'Guam', '2019-06-10 04:42:07'),
('GW000000', 'GW', 'Guinea-Bissau', '2019-06-10 04:42:07'),
('GY000000', 'GY', 'Guyana', '2019-06-10 04:42:07'),
('HK000000', 'HK', 'Hong Kong', '2019-06-10 04:42:07'),
('HM000000', 'HM', 'Heard Island And Mcdonald Islands', '2019-06-10 04:42:07'),
('HN000000', 'HN', 'Honduras', '2019-06-10 04:42:07'),
('HR000000', 'HR', 'Croatia', '2019-06-10 04:42:07'),
('HT000000', 'HT', 'Haiti', '2019-06-10 04:42:07'),
('HU000000', 'HU', 'Hungary', '2019-06-10 04:42:07'),
('IE000000', 'IE', 'Ireland', '2019-06-10 04:42:07'),
('IL000000', 'IL', 'Israel', '2019-06-10 04:42:07'),
('IM000000', 'IM', 'Isle Of Man', '2019-06-10 04:42:07'),
('IN000000', 'IN', 'India', '2019-06-10 04:42:07'),
('IO000000', 'IO', 'British Indian Ocean Territory', '2019-06-10 04:42:07'),
('IQ000000', 'IQ', 'Iraq', '2019-06-10 04:42:07'),
('IR000000', 'IR', 'Iran, Islamic Republic Of', '2019-06-10 04:42:07'),
('IS000000', 'IS', 'Iceland', '2019-06-10 04:42:07'),
('IT000000', 'IT', 'Italy', '2019-06-10 04:42:07'),
('JE000000', 'JE', 'Jersey', '2019-06-10 04:42:07'),
('JM000000', 'JM', 'Jamaica', '2019-06-10 04:42:07'),
('JO000000', 'JO', 'Jordan', '2019-06-10 04:42:07'),
('JP000000', 'JP', 'Japan', '2019-06-10 04:42:07'),
('KE000000', 'KE', 'Kenya', '2019-06-10 04:42:07'),
('KG000000', 'KG', 'Kyrgyzstan', '2019-06-10 04:42:07'),
('KH000000', 'KH', 'Cambodia', '2019-06-10 04:42:07'),
('KI000000', 'KI', 'Kiribati', '2019-06-10 04:42:07'),
('KM000000', 'KM', 'Comoros', '2019-06-10 04:42:07'),
('KN000000', 'KN', 'Saint Kitts And Nevis', '2019-06-10 04:42:07'),
('KP000000', 'KP', 'Korea, Democratic People\'S Republic Of', '2019-06-10 04:42:07'),
('KR000000', 'KR', 'Korea, Republic Of', '2019-06-10 04:42:07'),
('KW000000', 'KW', 'Kuwait', '2019-06-10 04:42:07'),
('KY000000', 'KY', 'Cayman Islands', '2019-06-10 04:42:07'),
('KZ000000', 'KZ', 'Kazakhstan', '2019-06-10 04:42:07'),
('LA000000', 'LA', 'Lao People\'S Democratic Republic', '2019-06-10 04:42:07'),
('LB000000', 'LB', 'Lebanon', '2019-06-10 04:42:07'),
('LC000000', 'LC', 'Saint Lucia', '2019-06-10 04:42:07'),
('LI000000', 'LI', 'Liechtenstein', '2019-06-10 04:42:07'),
('LK000000', 'LK', 'Sri Lanka', '2019-06-10 04:42:07'),
('LR000000', 'LR', 'Liberia', '2019-06-10 04:42:07'),
('LS000000', 'LS', 'Lesotho', '2019-06-10 04:42:07'),
('LT000000', 'LT', 'Lithuania', '2019-06-10 04:42:07'),
('LU000000', 'LU', 'Luxembourg', '2019-06-10 04:42:07'),
('LV000000', 'LV', 'Latvia', '2019-06-10 04:42:07'),
('LY000000', 'LY', 'Libyan Arab Jamahiriya', '2019-06-10 04:42:07'),
('MA000000', 'MA', 'Morocco', '2019-06-10 04:42:07'),
('MC000000', 'MC', 'Monaco', '2019-06-10 04:42:07'),
('MD000000', 'MD', 'Moldova, Republic Of', '2019-06-10 04:42:07'),
('ME000000', 'ME', 'Montenegro', '2019-06-10 04:42:07'),
('MF000000', 'MF', 'Saint Martin', '2019-06-10 04:42:07'),
('MG000000', 'MG', 'Madagascar', '2019-06-10 04:42:07'),
('MH000000', 'MH', 'Marshall Islands', '2019-06-10 04:42:07'),
('MK000000', 'MK', 'Macedonia, The Former Yugoslav Republic Of', '2019-06-10 04:42:07'),
('ML000000', 'ML', 'Mali', '2019-06-10 04:42:07'),
('MM000000', 'MM', 'Myanmar', '2019-06-10 04:42:07'),
('MN000000', 'MN', 'Mongolia', '2019-06-10 04:42:07'),
('MO000000', 'MO', 'Macao', '2019-06-10 04:42:07'),
('MP000000', 'MP', 'Northern Mariana Islands', '2019-06-10 04:42:07'),
('MQ000000', 'MQ', 'Martinique', '2019-06-10 04:42:07'),
('MR000000', 'MR', 'Mauritania', '2019-06-10 04:42:07'),
('MS000000', 'MS', 'Montserrat', '2019-06-10 04:42:07'),
('MT000000', 'MT', 'Malta', '2019-06-10 04:42:07'),
('MU000000', 'MU', 'Mauritius', '2019-06-10 04:42:07'),
('MV000000', 'MV', 'Maldives', '2019-06-10 04:42:07'),
('MW000000', 'MW', 'Malawi', '2019-06-10 04:42:07'),
('MX000000', 'MX', 'Mexico', '2019-06-10 04:42:07'),
('MY000000', 'MY', 'Malaysia', '2019-06-10 04:42:07'),
('MZ000000', 'MZ', 'Mozambique', '2019-06-10 04:42:07'),
('NA000000', 'NA', 'Namibia', '2019-06-10 04:42:07'),
('NC000000', 'NC', 'New Caledonia', '2019-06-10 04:42:07'),
('NE000000', 'NE', 'Niger', '2019-06-10 04:42:07'),
('NF000000', 'NF', 'Norfolk Island', '2019-06-10 04:42:07'),
('NG000000', 'NG', 'Nigeria', '2019-06-10 04:42:07'),
('NI000000', 'NI', 'Nicaragua', '2019-06-10 04:42:07'),
('NL000000', 'NL', 'Netherlands', '2019-06-10 04:42:07'),
('NO000000', 'NO', 'Norway', '2019-06-10 04:42:07'),
('NP000000', 'NP', 'Nepal', '2019-06-10 04:42:07'),
('NR000000', 'NR', 'Nauru', '2019-06-10 04:42:07'),
('NU000000', 'NU', 'Niue', '2019-06-10 04:42:07'),
('NZ000000', 'NZ', 'New Zealand', '2019-06-10 04:42:07'),
('OM000000', 'OM', 'Oman', '2019-06-10 04:42:07'),
('PA000000', 'PA', 'Panama', '2019-06-10 04:42:07'),
('PE000000', 'PE', 'Peru', '2019-06-10 04:42:07'),
('PF000000', 'PF', 'French Polynesia', '2019-06-10 04:42:07'),
('PG000000', 'PG', 'Papua New Guinea', '2019-06-10 04:42:07'),
('PH000000', 'PH', 'Philippines', '2019-06-10 04:42:07'),
('PK000000', 'PK', 'Pakistan', '2019-06-10 04:42:07'),
('PL000000', 'PL', 'Poland', '2019-06-10 04:42:07'),
('PM000000', 'PM', 'Saint Pierre And Miquelon', '2019-06-10 04:42:07'),
('PN000000', 'PN', 'Pitcairn', '2019-06-10 04:42:07'),
('PR000000', 'PR', 'Puerto Rico', '2019-06-10 04:42:07'),
('PS000000', 'PS', 'Palestinian Territory, Occupied', '2019-06-10 04:42:07'),
('PT000000', 'PT', 'Portugal', '2019-06-10 04:42:07'),
('PW000000', 'PW', 'Palau', '2019-06-10 04:42:07'),
('PY000000', 'PY', 'Paraguay', '2019-06-10 04:42:07'),
('QA000000', 'QA', 'Qatar', '2019-06-10 04:42:07'),
('RE000000', 'RE', 'Reunion', '2019-06-10 04:42:07'),
('RO000000', 'RO', 'Romania', '2019-06-10 04:42:07'),
('RS000000', 'RS', 'Serbia', '2019-06-10 04:42:07'),
('RU000000', 'RU', 'Russian Federation', '2019-06-10 04:42:07'),
('RW000000', 'RW', 'Rwanda', '2019-06-10 04:42:07'),
('SA000000', 'SA', 'Saudi Arabia', '2019-06-10 04:42:07'),
('SB000000', 'SB', 'Solomon Islands', '2019-06-10 04:42:07'),
('SC000000', 'SC', 'Seychelles', '2019-06-10 04:42:07'),
('SD000000', 'SD', 'Sudan', '2019-06-10 04:42:07'),
('SE000000', 'SE', 'Sweden', '2019-06-10 04:42:07'),
('SG000000', 'SG', 'Singapore', '2019-06-10 04:42:07'),
('SH000000', 'SH', 'Saint Helena', '2019-06-10 04:42:07'),
('SI000000', 'SI', 'Slovenia', '2019-06-10 04:42:07'),
('SJ000000', 'SJ', 'Svalbard And Jan Mayen', '2019-06-10 04:42:07'),
('SK000000', 'SK', 'Slovakia', '2019-06-10 04:42:07'),
('SL000000', 'SL', 'Sierra Leone', '2019-06-10 04:42:07'),
('SM000000', 'SM', 'San Marino', '2019-06-10 04:42:07'),
('SN000000', 'SN', 'Senegal', '2019-06-10 04:42:07'),
('SO000000', 'SO', 'Somalia', '2019-06-10 04:42:07'),
('SR000000', 'SR', 'Suriname', '2019-06-10 04:42:07'),
('SS000000', 'SS', 'South Sudan', '2019-06-10 04:42:07'),
('ST000000', 'ST', 'Sao Tome And Principe', '2019-06-10 04:42:07'),
('SV000000', 'SV', 'El Salvador', '2019-06-10 04:42:07'),
('SX000000', 'SX', 'Sint Maarten', '2019-06-10 04:42:07'),
('SY000000', 'SY', 'Syrian Arab Republic', '2019-06-10 04:42:07'),
('SZ000000', 'SZ', 'Swaziland', '2019-06-10 04:42:07'),
('TC000000', 'TC', 'Turks And Caicos Islands', '2019-06-10 04:42:07'),
('TD000000', 'TD', 'Chad', '2019-06-10 04:42:07'),
('TF000000', 'TF', 'French Southern Territories', '2019-06-10 04:42:07'),
('TG000000', 'TG', 'Togo', '2019-06-10 04:42:07'),
('TH000000', 'TH', 'Thailand', '2019-06-10 04:42:07'),
('TJ000000', 'TJ', 'Tajikistan', '2019-06-10 04:42:07'),
('TK000000', 'TK', 'Tokelau', '2019-06-10 04:42:07'),
('TL000000', 'TL', 'Timor-Leste', '2019-06-10 04:42:07'),
('TM000000', 'TM', 'Turkmenistan', '2019-06-10 04:42:07'),
('TN000000', 'TN', 'Tunisia', '2019-06-10 04:42:07'),
('TO000000', 'TO', 'Tonga', '2019-06-10 04:42:07'),
('TR000000', 'TR', 'Turkey', '2019-06-10 04:42:07'),
('TT000000', 'TT', 'Trinidad And Tobago', '2019-06-10 04:42:07'),
('TV000000', 'TV', 'Tuvalu', '2019-06-10 04:42:07'),
('TW000000', 'TW', 'Taiwan, Province Of China', '2019-06-10 04:42:07'),
('TZ000000', 'TZ', 'Tanzania, United Republic Of', '2019-06-10 04:42:07'),
('UA000000', 'UA', 'Ukraine', '2019-06-10 04:42:07'),
('UG000000', 'UG', 'Uganda', '2019-06-10 04:42:07'),
('UM000000', 'UM', 'United States Minor Outlying Islands', '2019-06-10 04:42:07'),
('US000000', 'US', 'United States', '2019-06-10 04:42:07'),
('UY000000', 'UY', 'Uruguay', '2019-06-10 04:42:07'),
('UZ000000', 'UZ', 'Uzbekistan', '2019-06-10 04:42:07'),
('VA000000', 'VA', 'Holy See (Vatican City State)', '2019-06-10 04:42:07'),
('VC000000', 'VC', 'Saint Vincent And The Grenadines', '2019-06-10 04:42:07'),
('VE000000', 'VE', 'Venezuela', '2019-06-10 04:42:07'),
('VG000000', 'VG', 'Virgin Islands, British', '2019-06-10 04:42:07'),
('VI000000', 'VI', 'Virgin Islands, U.S.', '2019-06-10 04:42:07'),
('VN000000', 'VN', 'Viet Nam', '2019-06-10 04:42:07'),
('VU000000', 'VU', 'Vanuatu', '2019-06-10 04:42:07'),
('WF000000', 'WF', 'Wallis And Futuna', '2019-06-10 04:42:07'),
('WS000000', 'WS', 'Samoa', '2019-06-10 04:42:07'),
('YE000000', 'YE', 'Yemen', '2019-06-10 04:42:07'),
('YT000000', 'YT', 'Mayotte', '2019-06-10 04:42:07'),
('ZA000000', 'ZA', 'South Africa', '2019-06-10 04:42:07'),
('ZM000000', 'ZM', 'Zambia', '2019-06-10 04:42:07'),
('ZW000000', 'ZW', 'Zimbabwe', '2019-06-10 04:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `dt_absence_student`
--

CREATE TABLE `dt_absence_student` (
  `absence_student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject_delivered_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `signed_personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `absence_status` enum('PRESENT','SICK','EXCUSE','ABSENT') COLLATE utf8_unicode_ci NOT NULL,
  `absence_note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `absence_description` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'string json nilai quiz',
  `absence_attachment` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_academic_history`
--

CREATE TABLE `dt_academic_history` (
  `academic_history_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `institution_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `study_program_feeder_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'from ws dikti (api)',
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `occupation_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `academic_history_graduation_year` int(4) DEFAULT NULL,
  `academic_history_major` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `academic_history_gpa` decimal(4,2) DEFAULT NULL,
  `academic_year_start_date` date DEFAULT NULL,
  `academic_year_end_date` date DEFAULT NULL,
  `academic_history_main` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `academic_history_this_job` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_academic_year`
--

CREATE TABLE `dt_academic_year` (
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_intake_status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_candidates_counter` int(11) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dt_academic_year`
--

INSERT INTO `dt_academic_year` (`academic_year_id`, `academic_year_intake_status`, `academic_year_candidates_counter`, `date_added`, `timestamp`) VALUES
('2015', 'inactive', 0, '2019-05-31 10:41:38', '2019-05-31 03:41:38'),
('2016', 'inactive', 0, '2019-05-31 10:41:38', '2019-05-31 03:41:38'),
('2017', 'inactive', 0, '2019-05-31 10:41:59', '2019-05-31 03:41:59'),
('2018', 'inactive', 0, '2019-05-31 10:41:59', '2019-05-31 03:41:59'),
('2019', 'inactive', 31, '2019-05-31 10:41:59', '2019-09-20 10:11:10'),
('2020', 'inactive', 1308, '2019-05-31 10:41:59', '2020-10-01 02:50:30'),
('2021', 'inactive', 1069, '2020-07-13 16:30:38', '2021-10-01 00:43:49'),
('2022', 'inactive', 778, '2021-06-03 11:16:16', '2022-08-31 22:41:14'),
('2023', 'inactive', 532, '2021-10-01 07:44:08', '2023-09-06 03:50:15'),
('2024', 'inactive', 162, '2022-10-17 10:28:56', '2024-10-07 14:49:01'),
('2025', 'active', 0, '2023-09-06 10:49:41', '2024-10-07 14:48:56');

-- --------------------------------------------------------

--
-- Table structure for table `dt_activity_lecturer`
--

CREATE TABLE `dt_activity_lecturer` (
  `activity_lecturer_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `activity_study_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_kategori_kegiatan` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `activity_lecturer_sequence` tinyint(2) NOT NULL,
  `activity_lecturer_type` enum('adviser','examiner') COLLATE utf8_unicode_ci NOT NULL,
  `feeder_sync` int(11) DEFAULT 1 COMMENT '0=success, 1=failed',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_activity_student`
--

CREATE TABLE `dt_activity_student` (
  `activity_student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `activity_study_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `role_type` enum('1','2','3') COLLATE utf8_unicode_ci NOT NULL COMMENT '1=ketua, 2=anggota, 3=personal',
  `feeder_sync` int(11) DEFAULT 1,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_activity_study`
--

CREATE TABLE `dt_activity_study` (
  `activity_study_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `program_id` int(11) NOT NULL DEFAULT 1,
  `study_program_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_jenis_aktivitas_mahasiswa` int(11) NOT NULL,
  `activity_member_type` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '0=personal, 1=group',
  `activity_title` text COLLATE utf8_unicode_ci NOT NULL,
  `activity_location` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activity_sk_number` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activity_sk_date` date DEFAULT NULL,
  `activity_remarks` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `feeder_sync` int(11) DEFAULT 1,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_address`
--

CREATE TABLE `dt_address` (
  `address_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `dikti_wilayah_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_rt` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_rw` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_province` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_city` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_zipcode` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_district` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_sub_district` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_phonenumber` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_cellular` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_api`
--

CREATE TABLE `dt_api` (
  `api_access_token` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `access_token_mode` enum('live','sandbox') COLLATE utf8_unicode_ci DEFAULT 'live',
  `api_secret_token` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_whitelist_ip` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dt_api`
--

INSERT INTO `dt_api` (`api_access_token`, `access_token_mode`, `api_secret_token`, `api_whitelist_ip`, `api_name`, `date_added`, `timestamp`) VALUES
('5099826e-2e2b-4e40-82f4-8cab39ab99aa', 'sandbox', NULL, NULL, 'PMB-NI', '2021-02-28 10:48:30', '2021-03-06 10:23:48'),
('82953acc-5433-4aac-bd8e-94909c148cb8', 'live', NULL, NULL, 'PMB-NI', '2021-02-28 10:48:30', '2021-03-06 10:23:55'),
('J63QH37SYK3LYXXB', 'live', NULL, NULL, '', '2019-11-01 14:24:39', '2021-03-02 08:27:45'),
('PMBIULIACID', 'live', '28af8b90-81e0-11e9-bdfc-5254005d90f6', NULL, 'PMB-IULI', '2019-05-29 14:05:36', '2021-03-02 08:27:47'),
('PMBPARTNERSRH', 'live', '7460a741-81f4-4169-9685-cf06b63cc4b7', NULL, 'PMB-SRH-IULI', '2019-05-29 14:05:36', '2021-09-21 08:41:11');

-- --------------------------------------------------------

--
-- Table structure for table `dt_candidate_answer`
--

CREATE TABLE `dt_candidate_answer` (
  `exam_candidate_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `exam_question_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `question_option_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_class_group`
--

CREATE TABLE `dt_class_group` (
  `class_group_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `sign_personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sign_datetime` datetime DEFAULT NULL,
  `class_group_name` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `lms_id` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_class_group_lecturer`
--

CREATE TABLE `dt_class_group_lecturer` (
  `class_group_lecturer_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `class_group_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id_reported` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_allocation` decimal(4,2) NOT NULL DEFAULT 0.00,
  `credit_charged` decimal(4,2) NOT NULL DEFAULT 0.00,
  `credit_realization` decimal(4,2) NOT NULL DEFAULT 0.00,
  `class_group_lecturer_status` enum('reserve','active') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `class_group_lecturer_preferable_day` enum('MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY') COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_group_lecturer_preferable_time` enum('07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00') COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_group_lecturer_priority` enum('HIGH','NORMAL','LOW') COLLATE utf8_unicode_ci DEFAULT 'HIGH',
  `is_reported_to_feeder` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_class_group_subject`
--

CREATE TABLE `dt_class_group_subject` (
  `class_group_subject_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `class_group_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `offered_subject_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='koneksi kelas kuliah ke feeder';

-- --------------------------------------------------------

--
-- Table structure for table `dt_class_master`
--

CREATE TABLE `dt_class_master` (
  `class_master_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `sign_personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_master_name` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `class_master_link_exam` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_master_link_exam_available` enum('disable','enable') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'disable',
  `has_submited_score` enum('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FALSE',
  `lms_id` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_class_master_class`
--

CREATE TABLE `dt_class_master_class` (
  `class_master_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `class_group_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_class_master_lecturer`
--

CREATE TABLE `dt_class_master_lecturer` (
  `class_master_lecturer_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `class_master_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id_reported` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_allocation` decimal(4,2) NOT NULL DEFAULT 0.00,
  `credit_charged` decimal(4,2) NOT NULL DEFAULT 0.00,
  `credit_realization` decimal(4,2) NOT NULL DEFAULT 0.00,
  `class_master_lecturer_status` enum('reserve','active') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `class_master_lecturer_preferable_day` enum('MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY') COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_master_lecturer_preferable_time` enum('07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00') COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_master_lecturer_priority` enum('HIGH','NORMAL','LOW') COLLATE utf8_unicode_ci DEFAULT 'HIGH',
  `is_reported_to_feeder` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_class_subject_delivered`
--

CREATE TABLE `dt_class_subject_delivered` (
  `subject_delivered_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `class_master_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_group_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject_delivered_time_start` datetime NOT NULL,
  `subject_delivered_time_end` datetime NOT NULL,
  `subject_delivered_description` text COLLATE utf8_unicode_ci NOT NULL,
  `number_of_meeting` tinyint(2) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_employee`
--

CREATE TABLE `dt_employee` (
  `employee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `employee_id_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_code_number` int(11) DEFAULT NULL,
  `employee_email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `employee_phone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employment_group` enum('ACADEMIC','NONACADEMIC') COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_job_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `employment_status` enum('PERMANENT','NON-PERMANENT') COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_join_date` date DEFAULT NULL,
  `employee_is_lecturer` enum('YES','NO') COLLATE utf8_unicode_ci NOT NULL,
  `employee_academic_rank` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_lecturer_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_lecturer_number_type` enum('NIDN','NUPN','NIDK','OTHERS') COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_lecturer_is_reported` enum('TRUE','FALSE') COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_homebase_status` enum('homebase','non-homebase') COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_pkpt` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_working_hour_status` enum('Semi Full Time','Part Time','Full Time') COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_leave_allowance` int(11) NOT NULL,
  `status` enum('ACTIVE','RESIGN','IN ACTIVE','STUDY PERMIT','PART TIME') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `employee_resignation_reason` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_note` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_date_of_work` date DEFAULT NULL,
  `last_date_of_employment` date DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dt_employee`
--

INSERT INTO `dt_employee` (`employee_id`, `personal_data_id`, `department_id`, `employee_id_number`, `employee_code_number`, `employee_email`, `employee_phone`, `employment_group`, `employee_job_title`, `employment_status`, `employee_join_date`, `employee_is_lecturer`, `employee_academic_rank`, `employee_lecturer_number`, `employee_lecturer_number_type`, `employee_lecturer_is_reported`, `employee_homebase_status`, `employee_pkpt`, `employee_working_hour_status`, `employee_leave_allowance`, `status`, `employee_resignation_reason`, `employee_note`, `last_date_of_work`, `last_date_of_employment`, `date_added`, `timestamp`, `portal_id`) VALUES
('7178bd02-89df-11ef-8f45-0068eb6957a0', '47013ff8-89df-11ef-8f45-0068eb6957a0', NULL, NULL, NULL, 'employee@company.ac.id', NULL, NULL, NULL, NULL, NULL, 'NO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'ACTIVE', NULL, NULL, NULL, NULL, '2024-10-14 10:50:24', '2024-10-14 03:50:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dt_employee_department`
--

CREATE TABLE `dt_employee_department` (
  `employee_department_id` int(11) NOT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_employee_pages`
--

CREATE TABLE `dt_employee_pages` (
  `employee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `roles_pages_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dt_employee_pages`
--

INSERT INTO `dt_employee_pages` (`employee_id`, `roles_pages_id`, `date_added`, `timestamp`) VALUES
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'f9ba70c6-e4cd-410b-9769-51b1a692e561', '2021-02-10 12:53:00', '2021-02-10 05:53:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '5aa9cc75-c441-422a-aeb9-bc9a28744fde', '2024-04-22 11:24:00', '2024-04-22 04:24:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'd1e6d79e-dcd1-4e3f-8e77-ba02646e4481', '2020-08-31 20:18:00', '2020-08-31 13:18:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '59e49232-f838-49d6-ba43-2a952f199a68', '2020-02-13 11:24:00', '2020-02-13 04:24:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '8099c110-eb6d-49aa-9e33-2eb4830f6e31', '2023-01-06 09:59:00', '2023-01-06 02:59:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'd8cd47fc-1282-4160-8a90-e10a662d52dc', '2020-03-10 14:01:00', '2020-03-10 07:01:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'c771a7e5-de21-4737-814b-3ab3556aadd2', '2024-05-20 10:18:00', '2024-05-20 03:18:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '2edcfc71-98af-40ef-b8d1-82662d8ca58b', '2023-01-06 09:54:00', '2023-01-06 02:54:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'e2381afd-5a20-478e-9385-ca11a5a7ae31', '2023-01-06 09:55:00', '2023-01-06 02:55:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '24867951-5674-41fd-b695-7f2f6171ed22', '2024-03-19 15:07:00', '2024-03-19 08:07:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '066ef733-2745-4445-b219-62ed38425f11', '2024-03-05 14:56:00', '2024-03-05 07:56:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '5c1fdc89-a77c-42d7-87bc-b5c6b63a829c', '2024-06-04 14:46:00', '2024-06-04 07:46:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'bf5ca50d-2f91-4044-bff4-13231909a3c2', '2023-09-19 16:09:00', '2023-09-19 09:09:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '636ca513-c5a1-4c0f-aa16-35721f199637', '2021-10-01 15:17:00', '2021-10-01 08:17:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '77fd2f55-6912-4f99-a05f-9d3395bed3b5', '2022-06-08 15:25:00', '2022-06-08 08:25:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'df92e449-72ef-46e6-8c2d-4dbf293269f6', '2023-10-06 10:12:00', '2023-10-09 08:37:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '2b70936c-3435-4e18-8a46-7ed7b8532a6c', '2023-02-22 08:45:00', '2023-02-22 01:45:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '3e40065b-b697-453d-b9fa-f13ebd1133e0', '2022-02-20 01:05:00', '2022-02-19 18:05:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '682b13e4-71a2-4929-9037-79349eca74b2', '2020-10-05 17:46:00', '2020-10-05 10:46:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'ae0adc1f-8775-4baa-aa6b-ac4dc8510ef1', '2022-03-22 15:53:00', '2022-03-22 08:53:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '3d47fbdc-b900-4da8-acc7-c2570236b50c', '2022-11-08 12:14:00', '2022-11-08 05:14:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '4b29b8dd-f0b7-45aa-8d6b-b5dc93e7a146', '2022-07-12 13:13:00', '2022-07-12 06:13:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '694bb043-2bcf-4344-920f-b2c23c50003b', '2023-02-03 14:21:00', '2023-02-03 07:21:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'fdc4670f-e366-49b3-8ebc-163a5de0f4b4', '2020-12-25 08:53:00', '2020-12-25 01:53:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '51820d0c-9748-41fb-86a5-ec1aeced40ec', '2024-02-22 15:20:00', '2024-02-22 08:20:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'b20ae534-9785-41a1-88b8-beb5ab8b9752', '2021-02-24 21:00:00', '2021-02-24 14:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '96491d93-1869-4d34-89d6-ab16654c613b', '2020-02-13 11:28:00', '2020-02-13 04:28:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '9b385b88-d2b6-46b8-8af5-e78b65b4b21e', '2022-10-11 11:25:00', '2022-10-11 04:25:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'e86efec5-cc18-4e32-86ce-7e30a9253842', '2023-01-27 11:07:00', '2023-01-27 04:07:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '200b5b34-3ae9-45cc-bcdd-1cce7810d3a7', '2021-04-09 15:40:00', '2021-04-09 08:40:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'fcc42851-63db-4567-a34a-b622bcaa5cf9', '2024-07-01 11:55:00', '2024-07-01 04:55:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '4b3ffbe4-15b5-4ec1-9306-0285c2233b91', '2022-11-09 14:54:00', '2022-11-09 07:54:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '6c8aa031-9ed2-475f-b016-3db0f6d06644', '2023-02-28 11:01:00', '2023-02-28 04:01:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '1e47a296-1932-4907-9528-a073b1db0431', '2022-06-08 15:25:00', '2022-06-08 08:25:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '5deca500-0ccc-451a-aacb-5d84143eaad2', '2022-08-29 11:30:00', '2022-08-29 04:30:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '9ed1c93a-94f5-4c0c-b335-c00682fdc411', '2023-01-06 09:53:00', '2023-01-06 02:53:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '0a4a83b4-6049-4dfd-967f-a67e5fddad83', '2022-12-09 11:35:00', '2022-12-09 04:35:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '589b02ae-354c-4372-bc06-ff6c5442d273', '2020-02-17 16:09:00', '2020-02-17 09:09:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '94b25864-3308-4c8c-a872-7a283f556e7b', '2024-03-05 13:27:00', '2024-03-05 07:22:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '55e42bd1-df63-4293-aea3-219121f94337', '2023-03-14 15:47:00', '2023-03-14 08:47:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '02e27789-4ac0-47e5-a0e6-0d7f4250ba8a', '2020-02-13 11:23:00', '2020-02-13 04:23:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'a1482c6e-7981-4536-b8b6-fcb8864ef04e', '2023-08-04 10:25:00', '2023-08-04 03:25:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '62fc5471-835a-48a4-95c9-b5d142307b91', '2023-01-06 09:53:00', '2023-01-06 02:53:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '8799ba10-af94-4f2d-9adc-c4f561677869', '2023-01-06 09:54:00', '2023-01-06 02:54:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'd5c1fee2-7c93-4130-89d3-8280fa4835b8', '2020-02-04 10:43:00', '2020-02-07 02:53:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'f74a8328-4a9a-43b1-8b9a-886d13e9d9ab', '2020-02-04 10:43:00', '2020-02-07 02:53:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '62b3c71d-68b4-4508-8275-589579e3b34d', '2020-02-04 10:43:00', '2020-02-07 02:54:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'a6ebf1ad-875b-45b1-9a38-18e0ae1a379c', '2020-02-04 10:43:00', '2020-02-07 02:55:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '09c70170-9464-4600-9a42-d60c0d8e221d', '2020-02-04 10:43:00', '2020-02-07 02:55:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '6980f30c-3d2c-4f65-8c86-cbcacf4ac613', '2020-02-04 10:43:00', '2020-02-07 02:55:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '7bdcbcdd-b54b-4966-86d9-a95fa15179ce', '2020-02-04 10:43:00', '2020-02-07 02:57:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'f1e9be9c-9f87-466f-9ed7-71e4b49d1d25', '2020-02-04 10:43:00', '2020-02-07 02:57:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '2af5f0da-738f-461b-b70e-373fb899d887', '2020-02-04 10:43:00', '2020-02-07 02:58:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '00e51dbe-2616-4297-835b-5e24e88a6db9', '2020-02-04 10:43:00', '2020-02-07 02:58:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '9ccd8cbe-a854-421f-a08b-7ab18b4b4d51', '2020-02-04 10:43:00', '2020-02-07 02:58:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '7a58b09e-730a-4dda-8a1f-05ec79b8c30f', '2020-02-04 10:43:00', '2020-02-07 02:58:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '7a4310b3-84f3-463c-819b-4d2849089bfc', '2020-02-04 10:43:00', '2020-02-07 02:59:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'dedd3fb8-14dd-438b-9b1b-94b1341ab224', '2020-02-04 10:43:00', '2020-02-07 02:59:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '74192d82-92d0-4b68-b579-23e715c4debe', '2020-02-04 10:43:00', '2020-02-07 02:59:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'f98db453-a042-4992-ad7c-c4592d373d5d', '2020-02-04 10:43:00', '2020-02-07 02:59:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'da84f249-a017-4c67-89d7-836dcc4088b5', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'faffbd26-27ad-47e3-a6f6-84c1b57f5ca2', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '374f90f1-9bb7-418d-8390-81854bdad9d0', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'fb592428-ccb3-4ef8-83dd-f726939339a0', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '69886a7b-9f61-422a-ae08-05d9f0c0aba3', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'd74652f8-f704-4763-bbf5-1c83a0098e72', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'd9a51c5f-553b-4d6d-ac3a-5aae4b5749e8', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '0f7e7a36-5186-4804-80b5-e5aa8d2f2391', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '05765a0c-9a94-45e6-a992-5e9d16b81ad4', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '178ad455-857e-4d60-ab2d-296a38147d90', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'f56778ea-2062-4206-8530-de7f43aafaa8', '2020-02-04 10:43:00', '2020-02-07 03:01:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'dbc8fa1d-bbb2-4d43-9e64-45b3170782c2', '2020-02-04 10:43:00', '2020-02-07 03:01:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '62a98218-aab5-4049-9e74-b5df7315814f', '2020-03-17 12:05:00', '2020-03-17 05:05:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '413e42bf-bb50-4e14-8978-b7523e56b422', '2023-06-04 11:08:00', '2023-06-04 04:08:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '7bb6fcd0-28d7-4056-abf4-a9539628af6b', '2020-02-18 10:20:00', '2020-02-18 03:20:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'b8df120a-20b5-4961-81fc-a17193c2796b', '2022-07-12 13:14:00', '2022-07-12 06:14:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '98d4debd-fe0f-465c-8906-27e05ce03f1c', '2022-06-09 09:39:00', '2024-01-22 06:56:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'd7556ab9-1901-4338-b115-af930b8a74c4', '2024-03-26 14:14:00', '2024-03-26 07:14:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'fc841cce-62f1-4734-a580-7ba3872f5298', '2022-01-27 09:56:00', '2022-01-27 02:56:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '04e12de9-be68-4594-b824-302dae7262c4', '2023-10-09 15:46:00', '2023-10-09 08:47:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', 'b0e5e426-b59b-4f78-9c69-02f20f65008e', '2023-08-10 11:28:00', '2023-08-10 04:28:00'),
('7178bd02-89df-11ef-8f45-0068eb6957a0', '13c52001-4e0a-4432-b57d-c258e1f245da', '2023-05-26 15:17:00', '2023-05-26 08:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `dt_event`
--

CREATE TABLE `dt_event` (
  `event_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `event_slug` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `event_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `event_venue` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `event_run_down` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `event_start_date` datetime NOT NULL,
  `event_end_date` datetime NOT NULL,
  `event_is_public` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'publish event',
  `event_type` enum('pmb','general','form') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pmb',
  `event_submit_test_automaticly` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `event_has_bookings` tinyint(1) NOT NULL DEFAULT 1,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_event_bookings`
--

CREATE TABLE `dt_event_bookings` (
  `booking_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `event_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `booking_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `booking_photo` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `booking_email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `booking_phone` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `booking_seat` tinyint(4) NOT NULL,
  `booking_participation` enum('pending','present') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `booking_origin` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `booking_reference` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `booking_grade` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `booking_graduate_year` year(4) DEFAULT NULL,
  `booking_period` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `booking_clothing_size` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_event_field`
--

CREATE TABLE `dt_event_field` (
  `event_field_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `event_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_title` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_option` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_input_type` enum('text','number','textarea') COLLATE utf8_unicode_ci DEFAULT 'text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_exam_candidate`
--

CREATE TABLE `dt_exam_candidate` (
  `exam_candidate_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `booking_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exam_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `total_time` time DEFAULT NULL,
  `correct_answer` tinyint(3) DEFAULT NULL,
  `wrong_answer` tinyint(3) DEFAULT NULL,
  `total_question` tinyint(3) DEFAULT NULL,
  `filled_question` tinyint(3) DEFAULT NULL,
  `token` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `candidate_exam_status` enum('FINISH','CANCEL','PENDING','PROGRESS') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'PENDING',
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_exam_candidate_section`
--

CREATE TABLE `dt_exam_candidate_section` (
  `exam_candidate_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `exam_section_id` int(11) DEFAULT NULL,
  `section_time_start` datetime NOT NULL,
  `section_time_end` datetime NOT NULL,
  `section_has_filled` enum('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FALSE',
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_exam_period`
--

CREATE TABLE `dt_exam_period` (
  `exam_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `exam_period_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exam_start_time` datetime NOT NULL,
  `exam_end_time` datetime NOT NULL,
  `exam_random_question` enum('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FALSE',
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_exam_question`
--

CREATE TABLE `dt_exam_question` (
  `exam_question_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `exam_question_number` int(11) NOT NULL,
  `exam_section_id` int(11) DEFAULT NULL,
  `exam_question_part` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exam_question_type` enum('LISTENING','NONLISTENING') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NONLISTENING',
  `exam_question_description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `exam_question_file` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exam_question_status` enum('ACTIVE','INACTIVE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_exam_question_option`
--

CREATE TABLE `dt_exam_question_option` (
  `question_option_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `exam_question_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `exam_question_option_number` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `question_option_description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `option_this_answer` enum('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FALSE',
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_exam_section`
--

CREATE TABLE `dt_exam_section` (
  `exam_section_id` int(11) NOT NULL,
  `exam_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `exam_section_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `exam_section_limit_minute` int(11) NOT NULL,
  `exam_listening_file` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_family`
--

CREATE TABLE `dt_family` (
  `family_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_family_member`
--

CREATE TABLE `dt_family_member` (
  `family_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `family_member_status` enum('father','mother','guardian','child') COLLATE utf8_unicode_ci NOT NULL,
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_fee`
--

CREATE TABLE `dt_fee` (
  `fee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `payment_type_code` char(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `scholarship_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL COMMENT 'batch',
  `semester_id` int(11) DEFAULT NULL,
  `fee_amount` double NOT NULL,
  `fee_amount_type` enum('main','additional') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'main',
  `fee_amount_number_type` enum('number','percentage') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'number',
  `fee_amount_sign_type` enum('positive','negative') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'positive',
  `fee_nationality` enum('WNI','WNA') COLLATE utf8_unicode_ci DEFAULT NULL,
  `fee_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fee_alt_description` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fee_special` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_institution_contact`
--

CREATE TABLE `dt_institution_contact` (
  `institution_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_invoice`
--

CREATE TABLE `dt_invoice` (
  `invoice_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `semester_type_id` tinyint(1) DEFAULT NULL,
  `invoice_customer` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_amount_paid` double NOT NULL DEFAULT 0,
  `invoice_amount_fined` double NOT NULL DEFAULT 0,
  `invoice_fined_count` int(11) NOT NULL DEFAULT 0,
  `invoice_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_allow_fine` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `invoice_allow_reminder` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `invoice_status` enum('created','pending','paid','cancelled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'created',
  `invoice_note` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_fine_status` enum('default','fined') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `invoice_datetime_paid_off` datetime DEFAULT NULL,
  `invoice_admission_reminder` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'no',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_invoice_details`
--

CREATE TABLE `dt_invoice_details` (
  `invoice_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_details_amount` double DEFAULT NULL,
  `invoice_details_amount_number_type` enum('number','percentage') COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_details_amount_sign_type` enum('positive','negative') COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_iuli_info`
--

CREATE TABLE `dt_iuli_info` (
  `info_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `info_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `info_message` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `info_status` enum('publish','draft','deleted') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'draft',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_job_vacancy`
--

CREATE TABLE `dt_job_vacancy` (
  `job_vacancy_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `institution_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `occupation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `job_description` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `job_vacancy_email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `job_vacancy_site` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `requirements` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_status` enum('open','close','deleted') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_letter_number`
--

CREATE TABLE `dt_letter_number` (
  `letter_number_id` int(11) NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `letter_type_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `semester_type_id` tinyint(1) DEFAULT NULL,
  `letter_number_result` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `letter_purpose` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `letter_number` int(11) DEFAULT NULL,
  `letter_char` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `letter_description` text COLLATE utf8_unicode_ci NOT NULL,
  `letter_month` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `letter_year` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `letter_date` date DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_letter_number_target`
--

CREATE TABLE `dt_letter_number_target` (
  `letter_number_id` int(11) NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `target_type` enum('student','lecturer') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'lecturer',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_offered_subject`
--

CREATE TABLE `dt_offered_subject` (
  `offered_subject_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `curriculum_subject_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `program_id` int(11) NOT NULL,
  `study_program_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ofse_period_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ofse_status` enum('mandatory','elective_uni','elective_fac') COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_ofse`
--

CREATE TABLE `dt_ofse` (
  `ofse_period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `study_plan_ofse_start_date` datetime NOT NULL,
  `study_plan_ofse_end_date` datetime NOT NULL,
  `ofse_period_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ofse_status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'inactive',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_ofse_evaluation`
--

CREATE TABLE `dt_ofse_evaluation` (
  `score_evaluation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_examiner_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `subject_question_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_sequence` tinyint(4) NOT NULL DEFAULT 1,
  `score` decimal(5,2) DEFAULT NULL,
  `comment` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_ofse_exam`
--

CREATE TABLE `dt_ofse_exam` (
  `ofse_exam_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `exam_room` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `exam_zoom_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exam_zoom_passcode` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exam_time_start` time DEFAULT NULL,
  `exam_time_end` time DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_ofse_examiner`
--

CREATE TABLE `dt_ofse_examiner` (
  `student_examiner_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `advisor_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `examiner_type` enum('examiner_1','examiner_2','examiner_3','examiner_4') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'examiner_1',
  `examiner_attendance` enum('ABSENT','PRESENT','EXCUSE','SICK') COLLATE utf8_unicode_ci DEFAULT NULL,
  `examiner_score` decimal(5,2) DEFAULT NULL,
  `examiner_lock_evaluation` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_ofse_subject_question`
--

CREATE TABLE `dt_ofse_subject_question` (
  `subject_question_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ofse_period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `subject_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ofse_subject_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ofse_question_sequence` tinyint(4) DEFAULT NULL,
  `subject_fname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_pick` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `subject_number_question` tinyint(4) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_personal_address`
--

CREATE TABLE `dt_personal_address` (
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `address_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_address_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_address_text` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_address_type` enum('primary','alternative') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'primary',
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_personal_data`
--

CREATE TABLE `dt_personal_data` (
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `country_of_birth` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'country_id',
  `citizenship_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'country_id',
  `religion_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ocupation_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `scholarship_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_title_prefix` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_title_suffix` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_cellular` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id_card_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_id_card_type` enum('national_id','driver_license','passport') COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_id_card_valid_from` date DEFAULT NULL,
  `personal_data_id_card_valid` date DEFAULT NULL,
  `personal_data_npwp_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_place_of_birth` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_date_of_birth` date DEFAULT NULL,
  `personal_data_gender` enum('M','F') COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_blood_group` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_nationality` enum('WNI','WNA') COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_marital_status` enum('single','married') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'single',
  `personal_data_mother_maiden_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_password_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_password_token_expired` datetime DEFAULT NULL,
  `personal_data_email_confirmation` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `personal_data_email_confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_reference_code` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_last_education` enum('-','SD','SMP','SLTA','D1','D2','D3','S1','S2','S3') COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_ptkp` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_data_path` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_referrer_agent` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = yes, agen ref. 0 = no.',
  `personal_data_contact_for_urgent` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_completed_personal_data` tinyint(4) DEFAULT NULL,
  `has_completed_parents_data` tinyint(4) DEFAULT NULL,
  `has_completed_school_data` tinyint(4) DEFAULT NULL,
  `has_completed_employment_data` tinyint(4) DEFAULT NULL,
  `finance_min_payment` double DEFAULT NULL,
  `portal_status` enum('open','blocked') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dt_personal_data`
--

INSERT INTO `dt_personal_data` (`personal_data_id`, `country_of_birth`, `citizenship_id`, `religion_id`, `ocupation_id`, `scholarship_id`, `personal_data_title_prefix`, `personal_data_name`, `personal_data_title_suffix`, `personal_data_email`, `personal_data_phone`, `personal_data_cellular`, `personal_data_id_card_number`, `personal_data_id_card_type`, `personal_data_id_card_valid_from`, `personal_data_id_card_valid`, `personal_data_npwp_number`, `personal_data_place_of_birth`, `personal_data_date_of_birth`, `personal_data_gender`, `personal_data_blood_group`, `personal_data_nationality`, `personal_data_marital_status`, `personal_data_mother_maiden_name`, `personal_data_password`, `personal_data_password_token`, `personal_data_password_token_expired`, `personal_data_email_confirmation`, `personal_data_email_confirmation_token`, `personal_data_reference_code`, `personal_data_last_education`, `personal_data_ptkp`, `personal_data_path`, `is_referrer_agent`, `personal_data_contact_for_urgent`, `has_completed_personal_data`, `has_completed_parents_data`, `has_completed_school_data`, `has_completed_employment_data`, `finance_min_payment`, `portal_status`, `pmb_sync`, `date_added`, `timestamp`, `portal_id`) VALUES
('47013ff8-89df-11ef-8f45-0068eb6957a0', NULL, NULL, NULL, NULL, NULL, NULL, 'Admin PT', NULL, 'bboby.siswanto@gmail.com', NULL, '0343', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'M', NULL, NULL, 'single', NULL, '$2y$10$cP5n69Nm8NLpp5F3zWOyEOu80rjOj5/zdCGJzuTLXbqgIQwtbG.em', NULL, NULL, 'no', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'open', '1', '2024-10-14 10:49:13', '2024-10-16 08:07:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dt_personal_data_contact`
--

CREATE TABLE `dt_personal_data_contact` (
  `personal_contact_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `contact_type` enum('phone','email','facebook','twitter','linkedin') COLLATE utf8_unicode_ci NOT NULL,
  `contact_fill` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_personal_data_covid_vaccine`
--

CREATE TABLE `dt_personal_data_covid_vaccine` (
  `vaccine_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `document_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `certificate_id` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vaccine_type` enum('Sinovac','PT Bio Farma','Oxford-AstraZeneca','Novavax','Pfizer-BioNTech','Moderna','Sinopharm') COLLATE utf8_unicode_ci DEFAULT NULL,
  `vaccine_date` date DEFAULT NULL,
  `vaccine_number` tinyint(1) NOT NULL,
  `vaccine_status` enum('vaccinated','not vaccinated') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'vaccinated',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_personal_data_document`
--

CREATE TABLE `dt_personal_data_document` (
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `document_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `document_requirement_link` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `document_mime` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_personal_data_record`
--

CREATE TABLE `dt_personal_data_record` (
  `record_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `record_department` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `record_category` enum('positive','negative') COLLATE utf8_unicode_ci NOT NULL,
  `record_comment` text COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_personal_data_record_files`
--

CREATE TABLE `dt_personal_data_record_files` (
  `record_file_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `record_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `record_file_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `record_file_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_personal_data_scholarship`
--

CREATE TABLE `dt_personal_data_scholarship` (
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `scholarship_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id_sibling_with` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `scholarship_fee_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sibling_type` enum('student','employee') COLLATE utf8_unicode_ci DEFAULT NULL,
  `semester_fee` double DEFAULT NULL,
  `scholarship_status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_personal_document`
--

CREATE TABLE `dt_personal_document` (
  `personal_document_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id_generated` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id_target` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `letter_number_id` int(11) DEFAULT NULL,
  `key_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `key_table` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'database.table',
  `document_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `document_link` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_questionnaire_answers`
--

CREATE TABLE `dt_questionnaire_answers` (
  `question_answer_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question_section_id` int(11) NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `answer_value` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_question_section_group`
--

CREATE TABLE `dt_question_section_group` (
  `question_section_group_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question_section_id` int(11) NOT NULL,
  `question_section_number_order` tinyint(4) NOT NULL,
  `question_show` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'true',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_reference`
--

CREATE TABLE `dt_reference` (
  `referrer_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'personal_data_id',
  `referenced_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'personal_data_id',
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_registration_scholarship`
--

CREATE TABLE `dt_registration_scholarship` (
  `registration_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `scholarship_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_roles_pages`
--

CREATE TABLE `dt_roles_pages` (
  `roles_pages_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `roles_id` int(11) NOT NULL,
  `pages_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dt_roles_pages`
--

INSERT INTO `dt_roles_pages` (`roles_pages_id`, `roles_id`, `pages_id`, `date_added`, `timestamp`) VALUES
('00e51dbe-2616-4297-835b-5e24e88a6db9', 3, 'd0e3d270-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:58:00'),
('02e27789-4ac0-47e5-a0e6-0d7f4250ba8a', 3, 'ac943df3-cea5-4d20-9148-240a0d8a84bd', '2020-02-13 11:23:00', '2020-02-13 04:23:00'),
('04e12de9-be68-4594-b824-302dae7262c4', 3, 'f8cdccc4-1ec6-4656-9f70-9e88f3b1ad13', '2023-10-09 15:46:00', '2023-10-09 08:47:00'),
('05765a0c-9a94-45e6-a992-5e9d16b81ad4', 3, 'd0e3ecec-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('066ef733-2745-4445-b219-62ed38425f11', 3, '40c4bae9-c5f8-4b2c-9b3f-8b88f765f47f', '2024-03-05 14:56:00', '2024-03-05 07:56:00'),
('09c70170-9464-4600-9a42-d60c0d8e221d', 3, 'd0e3ca5a-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:55:00'),
('0a4a83b4-6049-4dfd-967f-a67e5fddad83', 3, 'a264beb2-fa21-4aa0-a3e6-8d6a6ea97a4e', '2022-12-09 11:35:00', '2022-12-09 04:35:00'),
('0f7e7a36-5186-4804-80b5-e5aa8d2f2391', 3, 'd0e3eae4-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('13c52001-4e0a-4432-b57d-c258e1f245da', 3, 'fa69a65f-77b7-4b7f-84d5-d3ea0c73fd79', '2023-05-26 15:17:00', '2023-05-26 08:17:00'),
('178ad455-857e-4d60-ab2d-296a38147d90', 3, 'd0e3ee22-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('1e47a296-1932-4907-9528-a073b1db0431', 3, '8e1ff88c-7667-43ef-b9bc-07672f37844b', '2022-06-08 15:25:00', '2022-06-08 08:25:00'),
('200b5b34-3ae9-45cc-bcdd-1cce7810d3a7', 3, '882df9ac-156f-4794-846d-f7364dfc2709', '2021-04-09 15:40:00', '2021-04-09 08:40:00'),
('24867951-5674-41fd-b695-7f2f6171ed22', 3, '3ebe3e2c-737b-44f0-94eb-6b9d90f50f14', '2024-03-19 15:07:00', '2024-03-19 08:07:00'),
('2af5f0da-738f-461b-b70e-373fb899d887', 3, 'd0e3d126-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:58:00'),
('2b70936c-3435-4e18-8a46-7ed7b8532a6c', 3, '53240870-854b-4b26-9cde-b012d5725fff', '2023-02-22 08:45:00', '2023-02-22 01:45:00'),
('2edcfc71-98af-40ef-b8d1-82662d8ca58b', 3, '3c9cbf12-2771-4440-ac4a-d084c8f4e94d', '2023-01-06 09:54:00', '2023-01-06 02:54:00'),
('374f90f1-9bb7-418d-8390-81854bdad9d0', 3, 'd0e3e274-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('3d47fbdc-b900-4da8-acc7-c2570236b50c', 3, '6a392908-21ce-4fca-a06b-fc7549dca731', '2022-11-08 12:14:00', '2022-11-08 05:14:00'),
('3e40065b-b697-453d-b9fa-f13ebd1133e0', 3, '541dd4ea-7d01-48f8-82d9-9028b39db1a8', '2022-02-20 01:05:00', '2022-02-19 18:05:00'),
('413e42bf-bb50-4e14-8978-b7523e56b422', 3, 'e1ad266d-a002-40b8-809c-823aa94f007a', '2023-06-04 11:08:00', '2023-06-04 04:08:00'),
('4b29b8dd-f0b7-45aa-8d6b-b5dc93e7a146', 3, '6be542aa-9d66-4692-8161-f942a70c1522', '2022-07-12 13:13:00', '2022-07-12 06:13:00'),
('4b3ffbe4-15b5-4ec1-9306-0285c2233b91', 3, '8a4b0b43-a084-4583-bdd0-7715c6362477', '2022-11-09 14:54:00', '2022-11-09 07:54:00'),
('51820d0c-9748-41fb-86a5-ec1aeced40ec', 3, '80277393-5447-4cc7-b65c-f2371a94605b', '2024-02-22 15:20:00', '2024-02-22 08:20:00'),
('55e42bd1-df63-4293-aea3-219121f94337', 3, 'abe82a03-9688-47b1-92e9-0b642ca7e2ed', '2023-03-14 15:47:00', '2023-03-14 08:47:00'),
('589b02ae-354c-4372-bc06-ff6c5442d273', 3, 'a90da69f-1c2b-450f-9b46-3816dbcc378a', '2020-02-17 16:09:00', '2020-02-17 09:09:00'),
('59e49232-f838-49d6-ba43-2a952f199a68', 3, '1c671456-a5ad-4e41-b6fd-e476a079967c', '2020-02-13 11:24:00', '2020-02-13 04:24:00'),
('5aa9cc75-c441-422a-aeb9-bc9a28744fde', 3, '0561a8cc-be81-4bb0-ae3f-26b175da09ee', '2024-04-22 11:24:00', '2024-04-22 04:24:00'),
('5c1fdc89-a77c-42d7-87bc-b5c6b63a829c', 3, '40e2e52c-affd-4a5f-b654-c114897fb566', '2024-06-04 14:46:00', '2024-06-04 07:46:00'),
('5deca500-0ccc-451a-aacb-5d84143eaad2', 3, '91bc9300-8cab-41fa-85bc-c5f36722d798', '2022-08-29 11:30:00', '2022-08-29 04:30:00'),
('62a98218-aab5-4049-9e74-b5df7315814f', 3, 'db6023ef-0b58-42fe-80a3-c1eaaf2ffaf1', '2020-03-17 12:05:00', '2020-03-17 05:05:00'),
('62b3c71d-68b4-4508-8275-589579e3b34d', 3, 'd0e3c6b8-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:54:00'),
('62fc5471-835a-48a4-95c9-b5d142307b91', 3, 'c2f8791d-0dc1-4bda-9222-7b2ec219b3f2', '2023-01-06 09:53:00', '2023-01-06 02:53:00'),
('636ca513-c5a1-4c0f-aa16-35721f199637', 3, '4baa952f-ef46-4dc6-bf5b-203f4fdc25e8', '2021-10-01 15:17:00', '2021-10-01 08:17:00'),
('682b13e4-71a2-4929-9037-79349eca74b2', 3, '5abf02b6-403d-4418-98e0-b7cb040fa93a', '2020-10-05 17:46:00', '2020-10-05 10:46:00'),
('694bb043-2bcf-4344-920f-b2c23c50003b', 3, '72ebfcf3-dd98-40ca-8292-5e366bffab34', '2023-02-03 14:21:00', '2023-02-03 07:21:00'),
('6980f30c-3d2c-4f65-8c86-cbcacf4ac613', 3, 'd0e3cba4-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:55:00'),
('69886a7b-9f61-422a-ae08-05d9f0c0aba3', 3, 'd0e3e620-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('6c8aa031-9ed2-475f-b016-3db0f6d06644', 3, '8b0d19d3-7d76-4930-8f33-cfd9358a809b', '2023-02-28 11:01:00', '2023-02-28 04:01:00'),
('74192d82-92d0-4b68-b579-23e715c4debe', 3, 'd0e3da7c-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:59:00'),
('77fd2f55-6912-4f99-a05f-9d3395bed3b5', 3, '4fe1d7ea-bf63-4034-b32c-c1a88b4d5daf', '2022-06-08 15:25:00', '2022-06-08 08:25:00'),
('7a4310b3-84f3-463c-819b-4d2849089bfc', 3, 'd0e3d720-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:59:00'),
('7a58b09e-730a-4dda-8a1f-05ec79b8c30f', 3, 'd0e3d5ea-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:58:00'),
('7bb6fcd0-28d7-4056-abf4-a9539628af6b', 3, 'e747d623-3afb-47a8-99a5-c23391b52ba7', '2020-02-18 10:20:00', '2020-02-18 03:20:00'),
('7bdcbcdd-b54b-4966-86d9-a95fa15179ce', 3, 'd0e3cdac-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:57:00'),
('8099c110-eb6d-49aa-9e33-2eb4830f6e31', 3, '252c1ea8-8636-4f9f-972b-d802b6885c55', '2023-01-06 09:59:00', '2023-01-06 02:59:00'),
('8799ba10-af94-4f2d-9adc-c4f561677869', 3, 'cf0fa5d3-cc4d-4ad1-bbd7-1e817d39fb03', '2023-01-06 09:54:00', '2023-01-06 02:54:00'),
('94b25864-3308-4c8c-a872-7a283f556e7b', 3, 'a98504ea-e25c-418c-bae3-31a37e5dc68a', '2024-03-05 13:27:00', '2024-03-05 07:22:00'),
('96491d93-1869-4d34-89d6-ab16654c613b', 3, '841de6d9-f58f-4c30-9203-638ee0dba0ee', '2020-02-13 11:28:00', '2020-02-13 04:28:00'),
('98d4debd-fe0f-465c-8906-27e05ce03f1c', 3, 'f3b1ccaf-d159-45d8-a860-4c4ab687e138', '2022-06-09 09:39:00', '2024-01-22 06:56:00'),
('9b385b88-d2b6-46b8-8af5-e78b65b4b21e', 3, '84b669a0-b68a-484c-b6fc-5e673603c5cd', '2022-10-11 11:25:00', '2022-10-11 04:25:00'),
('9ccd8cbe-a854-421f-a08b-7ab18b4b4d51', 3, 'd0e3d39c-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:58:00'),
('9ed1c93a-94f5-4c0c-b335-c00682fdc411', 3, '96699ef8-de4b-4077-b9a7-3d4b0ceb0e1b', '2023-01-06 09:53:00', '2023-01-06 02:53:00'),
('a1482c6e-7981-4536-b8b6-fcb8864ef04e', 3, 'bb80eb4e-f8cf-4d5a-8b32-f1cac1cbfddc', '2023-08-04 10:25:00', '2023-08-04 03:25:00'),
('a6ebf1ad-875b-45b1-9a38-18e0ae1a379c', 3, 'd0e3c802-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:55:00'),
('ae0adc1f-8775-4baa-aa6b-ac4dc8510ef1', 3, '648ce8fd-48f9-4ea4-ba93-19f08435ef74', '2022-03-22 15:53:00', '2022-03-22 08:53:00'),
('b0e5e426-b59b-4f78-9c69-02f20f65008e', 3, 'f941e59b-5dd4-4cdd-a853-4a903446b22b', '2023-08-10 11:28:00', '2023-08-10 04:28:00'),
('b20ae534-9785-41a1-88b8-beb5ab8b9752', 3, '80d1643c-c1fc-4644-9546-3e2955e48e6a', '2021-02-24 21:00:00', '2021-02-24 14:00:00'),
('b8df120a-20b5-4961-81fc-a17193c2796b', 3, 'e760fb13-3e95-4d01-b134-a9f1c54b1772', '2022-07-12 13:14:00', '2022-07-12 06:14:00'),
('bf5ca50d-2f91-4044-bff4-13231909a3c2', 3, '4b306511-b5e6-4ec1-98b6-d2d5b961fd61', '2023-09-19 16:09:00', '2023-09-19 09:09:00'),
('c771a7e5-de21-4737-814b-3ab3556aadd2', 3, '2fc32137-cfd7-4622-b78f-9114572ffb1c', '2024-05-20 10:18:00', '2024-05-20 03:18:00'),
('d1e6d79e-dcd1-4e3f-8e77-ba02646e4481', 3, '148a1877-20fb-45a0-b223-f9fb3c4c3678', '2020-08-31 20:18:00', '2020-08-31 13:18:00'),
('d5c1fee2-7c93-4130-89d3-8280fa4835b8', 3, 'd0e3c15e-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:53:00'),
('d74652f8-f704-4763-bbf5-1c83a0098e72', 3, 'd0e3e76a-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('d7556ab9-1901-4338-b115-af930b8a74c4', 3, 'f5d2bf93-a4c6-4339-8f8a-e6dd7afe34a1', '2024-03-26 14:14:00', '2024-03-26 07:14:00'),
('d8cd47fc-1282-4160-8a90-e10a662d52dc', 3, '2df04001-af64-4f7f-9c3b-f044290443d7', '2020-03-10 14:01:00', '2020-03-10 07:01:00'),
('d9a51c5f-553b-4d6d-ac3a-5aae4b5749e8', 3, 'd0e3e9a4-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('da84f249-a017-4c67-89d7-836dcc4088b5', 3, 'd0e3def0-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('dbc8fa1d-bbb2-4d43-9e64-45b3170782c2', 3, 'd0e3f1d8-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:01:00'),
('dedd3fb8-14dd-438b-9b1b-94b1341ab224', 3, 'd0e3d93c-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:59:00'),
('df92e449-72ef-46e6-8c2d-4dbf293269f6', 3, '529c3c76-5279-443e-bc05-030b227f86ba', '2023-10-06 10:12:00', '2023-10-09 08:37:00'),
('e2381afd-5a20-478e-9385-ca11a5a7ae31', 3, '3d5b2d00-875a-4339-ada1-87ced9f955db', '2023-01-06 09:55:00', '2023-01-06 02:55:00'),
('e86efec5-cc18-4e32-86ce-7e30a9253842', 3, '84d6ccf4-ff8f-487e-9c14-b6bfca113859', '2023-01-27 11:07:00', '2023-01-27 04:07:00'),
('f1e9be9c-9f87-466f-9ed7-71e4b49d1d25', 3, 'd0e3cee2-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:57:00'),
('f56778ea-2062-4206-8530-de7f43aafaa8', 3, 'd0e3ef58-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:01:00'),
('f74a8328-4a9a-43b1-8b9a-886d13e9d9ab', 3, 'd0e3c4ba-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:53:00'),
('f98db453-a042-4992-ad7c-c4592d373d5d', 3, 'd0e3dc8e-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 02:59:00'),
('f9ba70c6-e4cd-410b-9769-51b1a692e561', 3, '046df049-820c-470b-9ff9-5fabcf267d48', '2021-02-10 12:53:00', '2021-02-10 05:53:00'),
('faffbd26-27ad-47e3-a6f6-84c1b57f5ca2', 3, 'd0e3e12a-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('fb592428-ccb3-4ef8-83dd-f726939339a0', 3, 'd0e3e4e0-46fd-11ea-b77f-2e728ce88125', '2020-02-04 10:43:00', '2020-02-07 03:00:00'),
('fc841cce-62f1-4734-a580-7ba3872f5298', 3, 'f63403b4-5964-4069-b5d6-34723ea88332', '2022-01-27 09:56:00', '2022-01-27 02:56:00'),
('fcc42851-63db-4567-a34a-b622bcaa5cf9', 3, '888333bc-1101-4732-9fb6-89ac6f65659d', '2024-07-01 11:55:00', '2024-07-01 04:55:00'),
('fdc4670f-e366-49b3-8ebc-163a5de0f4b4', 3, '74345797-1456-43c0-91c1-d785c10c14e9', '2020-12-25 08:53:00', '2020-12-25 01:53:00');

-- --------------------------------------------------------

--
-- Table structure for table `dt_score`
--

CREATE TABLE `dt_score` (
  `score_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `class_group_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_master_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `curriculum_subject_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ofse_period_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `institution_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'transfer_credit',
  `original_subject` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'transfer_credit',
  `original_credit` double DEFAULT NULL COMMENT 'transfer_credit',
  `score_quiz` double DEFAULT NULL,
  `score_quiz1` double DEFAULT NULL,
  `score_quiz2` double DEFAULT NULL,
  `score_quiz3` double DEFAULT NULL,
  `score_quiz4` double DEFAULT NULL,
  `score_quiz5` double DEFAULT NULL,
  `score_quiz6` double DEFAULT NULL,
  `score_true_average_quiz` double DEFAULT NULL,
  `score_true_sum` double DEFAULT NULL,
  `score_final_exam` double DEFAULT NULL,
  `score_repetition_exam` double DEFAULT NULL,
  `score_mark_for_repetition` tinyint(1) DEFAULT NULL,
  `score_sum` double NOT NULL,
  `score_grade` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `score_grade_point` double NOT NULL,
  `score_ects` double NOT NULL,
  `score_absence` double NOT NULL DEFAULT 0,
  `score_merit` double NOT NULL,
  `score_approval` enum('approved','rejected','pending','canceled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `score_display` enum('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TRUE',
  `score_mark_flying_faculty` tinyint(1) DEFAULT NULL COMMENT '1=marked',
  `score_examiner` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Triggers `dt_score`
--
DELIMITER $$
CREATE TRIGGER `check_score_zero` AFTER UPDATE ON `dt_score` FOR EACH ROW IF new.score_sum = 0 AND old.score_sum != 0 THEN
        INSERT INTO a_table_testing(score_id,old_score_sum, new_score_sum)
        VALUES(old.score_id, old.score_sum, new.score_sum);
    END IF
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `score_removed` AFTER DELETE ON `dt_score` FOR EACH ROW INSERT INTO a_score_removed (score_id, class_group_id, class_master_id, student_id, curriculum_subject_id, semester_id, semester_type_id, academic_year_id, score_quiz, score_quiz1, score_quiz2, score_quiz3, score_quiz4, score_quiz5, score_quiz6, score_true_average_quiz, score_true_sum, score_final_exam, score_repetition_exam, score_mark_for_repetition, score_sum, score_grade, score_grade_point, score_ects, score_absence, score_merit, score_approval, score_display, score_examiner, date_added, timestamp, portal_id) VALUES (old.score_id, old.class_group_id, old.class_master_id, old.student_id, old.curriculum_subject_id, old.semester_id, old.semester_type_id, old.academic_year_id, old.score_quiz, old.score_quiz1, old.score_quiz2, old.score_quiz3, old.score_quiz4, old.score_quiz5, old.score_quiz6, old.score_true_average_quiz, old.score_true_sum, old.score_final_exam, old.score_repetition_exam, old.score_mark_for_repetition, old.score_sum, old.score_grade, old.score_grade_point, old.score_ects, old.score_absence, old.score_merit, old.score_approval, old.score_display, old.score_examiner, old.date_added, old.timestamp, old.portal_id)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `dt_semester_settings`
--

CREATE TABLE `dt_semester_settings` (
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `semester_start_date` datetime DEFAULT NULL,
  `semester_end_date` datetime DEFAULT NULL,
  `offer_subject_end_date` datetime DEFAULT NULL,
  `offer_subject_short_semester_end_date` datetime DEFAULT NULL,
  `offer_subject_ofse_end_date` datetime DEFAULT NULL,
  `study_plan_start_date` datetime DEFAULT NULL,
  `study_plan_end_date` datetime DEFAULT NULL,
  `study_plan_short_semester_start_date` datetime DEFAULT NULL,
  `study_plan_short_semester_end_date` datetime DEFAULT NULL,
  `study_plan_ofse_start_date` datetime DEFAULT NULL,
  `study_plan_ofse_end_date` datetime DEFAULT NULL,
  `study_plan_approval_end_date` datetime DEFAULT NULL,
  `study_plan_approval_short_semester_end_date` datetime DEFAULT NULL,
  `study_plan_approval_ofse_end_date` datetime DEFAULT NULL,
  `repetition_registration_start_date` datetime DEFAULT NULL,
  `repetition_registration_end_date` datetime DEFAULT NULL,
  `repetition_registration_ofse_start_date` datetime DEFAULT NULL,
  `repetition_registration_ofse_end_date` datetime DEFAULT NULL,
  `dikti_report_deadline` datetime DEFAULT NULL,
  `semester_status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dt_semester_settings`
--

INSERT INTO `dt_semester_settings` (`academic_year_id`, `semester_type_id`, `semester_start_date`, `semester_end_date`, `offer_subject_end_date`, `offer_subject_short_semester_end_date`, `offer_subject_ofse_end_date`, `study_plan_start_date`, `study_plan_end_date`, `study_plan_short_semester_start_date`, `study_plan_short_semester_end_date`, `study_plan_ofse_start_date`, `study_plan_ofse_end_date`, `study_plan_approval_end_date`, `study_plan_approval_short_semester_end_date`, `study_plan_approval_ofse_end_date`, `repetition_registration_start_date`, `repetition_registration_end_date`, `repetition_registration_ofse_start_date`, `repetition_registration_ofse_end_date`, `dikti_report_deadline`, `semester_status`) VALUES
('2024', 1, '2024-09-08 15:13:35', '2024-12-27 15:13:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `dt_semester_setting_request`
--

CREATE TABLE `dt_semester_setting_request` (
  `request_semester_setting_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `personal_data_id_request` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id_approve` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_json` text COLLATE utf8_unicode_ci NOT NULL,
  `request_note` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `request_datetime` datetime NOT NULL,
  `approve_datetime` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student`
--

CREATE TABLE `dt_student` (
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `program_id` int(11) DEFAULT 1,
  `study_program_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_majoring_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_id_alt_1` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_id_alt_2` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `finance_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `student_registration_scholarship_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_nisn` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_date_enrollment` datetime DEFAULT current_timestamp(),
  `student_type` enum('regular','transfer') COLLATE utf8_unicode_ci DEFAULT 'regular',
  `student_class_type` enum('karyawan','regular','exchange','course') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'regular',
  `student_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_alumni_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `graduated_year_id` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_date_graduated` date DEFAULT NULL,
  `student_date_resign` datetime DEFAULT NULL,
  `student_date_active` datetime DEFAULT NULL COMMENT 'first time active date',
  `student_resign_note` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_ipk` decimal(4,2) DEFAULT NULL,
  `student_un_status` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `student_status` enum('register','candidate','pending','active','inactive','dropout','resign','graduated','onleave','cancel') COLLATE utf8_unicode_ci DEFAULT 'register',
  `student_has_siblings` tinyint(1) NOT NULL DEFAULT 0,
  `has_to_pay_enrollment_fee` enum('no','yes') COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_candidate_cancel_note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_not_reported_to_feeder` tinyint(1) DEFAULT NULL,
  `student_portal_blocked` enum('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FALSE',
  `student_portal_blocked_message` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_send_transcript` enum('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TRUE',
  `student_transcript_token` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_mark_completed_defense` tinyint(1) DEFAULT NULL COMMENT '1 = marked',
  `student_mark_submitted_thesis_proposal` tinyint(4) DEFAULT NULL COMMENT '1 = marked',
  `student_ofse_eligibility` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `student_approved_payment_delay` tinyint(4) DEFAULT NULL,
  `student_pin_number` varchar(17) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_thesis_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_last_gpa` double DEFAULT NULL,
  `student_graduation_registration` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `portal_id` int(11) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_aid`
--

CREATE TABLE `dt_student_aid` (
  `request_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `bank_code` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `aid_period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `request_amount` double NOT NULL,
  `request_amount_accepted` double DEFAULT 0,
  `request_bank_branch` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_account_number` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `request_beneficiary` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `request_note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_aid_files`
--

CREATE TABLE `dt_student_aid_files` (
  `request_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `request_receipt_bill_file` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `request_receipt_bill_file_mime` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_aid_setting`
--

CREATE TABLE `dt_student_aid_setting` (
  `aid_period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `aid_period_month` tinyint(2) NOT NULL,
  `aid_period_year` year(4) NOT NULL,
  `aid_period_datetime_start` datetime NOT NULL,
  `aid_period_datetime_end` datetime NOT NULL,
  `aid_period_status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'inactive',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_alumni`
--

CREATE TABLE `dt_student_alumni` (
  `alumni_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `alumni_fullname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alumni_nickname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alumni_date_of_birth` date DEFAULT NULL,
  `alumni_place_of_birth` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alumni_after_graduated` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alumni_personal_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alumni_personal_cellular` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alumni_gender` enum('M','F') COLLATE utf8_unicode_ci DEFAULT NULL,
  `alumni_marital_status` enum('single','married') COLLATE utf8_unicode_ci DEFAULT NULL,
  `alumni_has_filled_tracer` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `alumni_has_filled_job` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_document_token`
--

CREATE TABLE `dt_student_document_token` (
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `document_type` enum('transcript_halfway','transcript_graduation','transcript_semester') COLLATE utf8_unicode_ci DEFAULT NULL,
  `document_sign` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_exchange`
--

CREATE TABLE `dt_student_exchange` (
  `exchange_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `program_id` int(11) NOT NULL,
  `institution_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL COMMENT 'current_semester',
  `academic_year_id` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `semester_type_id` tinyint(1) DEFAULT NULL,
  `faculty_name` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_name` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_gpa` double DEFAULT NULL,
  `passport_number` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exchange_type` enum('in','out') COLLATE utf8_unicode_ci DEFAULT 'out',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='student abroad';

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_exchange_doc`
--

CREATE TABLE `dt_student_exchange_doc` (
  `exchange_file_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `exchange_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `document_type` enum('certificate_degree','transcript','other_file') COLLATE utf8_unicode_ci NOT NULL,
  `document_link` text COLLATE utf8_unicode_ci NOT NULL,
  `document_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_internship`
--

CREATE TABLE `dt_student_internship` (
  `internship_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `institution_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `supervisor_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_internship_doc`
--

CREATE TABLE `dt_student_internship_doc` (
  `internship_file_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `internship_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `document_type` enum('assessment','logsheet','report','other_doc_1','other_doc_2') COLLATE utf8_unicode_ci DEFAULT NULL,
  `document_link` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `document_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_partner`
--

CREATE TABLE `dt_student_partner` (
  `student_partner_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `program_study_program_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_program_id` int(11) NOT NULL,
  `partner_period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_partner_status` enum('on_going','reject','accepted','pending','resign') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'on_going',
  `student_partner_number` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_partner_note` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_partner_enrollment_date` date NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_semester`
--

CREATE TABLE `dt_student_semester` (
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `institution_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'f5220e90-c7b6-iuli-b9d8-5254005d90f6' COMMENT 'study_location',
  `student_semester_gp` double NOT NULL DEFAULT 0,
  `student_semester_gpa` double NOT NULL DEFAULT 0,
  `student_semester_status` enum('register','candidate','pending','active','inactive','dropout','resign','graduated','onleave') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'inactive',
  `student_semester_activity` enum('study_regular','double_degree','internship','waiting_exam') COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_semester_transcript_token` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_semester_thesis_title` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_semester_pin` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_semester_thesis_result` enum('FAIL','SUCCESS') COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_semester_locked_krs` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_supplement`
--

CREATE TABLE `dt_student_supplement` (
  `supplement_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `academic_year_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `semester_type_id` tinyint(1) DEFAULT NULL,
  `supplement_comment` text COLLATE utf8_unicode_ci NOT NULL,
  `supplement_category` enum('positive','negative') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'positive',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_student_supplement_doc`
--

CREATE TABLE `dt_student_supplement_doc` (
  `supplement_doc_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `supplement_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `supplement_doc_link` text COLLATE utf8_unicode_ci NOT NULL,
  `supplement_doc_fname` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_sub_invoice`
--

CREATE TABLE `dt_sub_invoice` (
  `sub_invoice_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sub_invoice_amount` double NOT NULL,
  `sub_invoice_amount_paid` double NOT NULL DEFAULT 0,
  `sub_invoice_amount_total` double NOT NULL,
  `sub_invoice_status` enum('default','paid') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `sub_invoice_type` enum('full','installment') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'full',
  `sub_invoice_datetime_paid_off` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_sub_invoice_details`
--

CREATE TABLE `dt_sub_invoice_details` (
  `sub_invoice_details_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `trx_id` int(11) DEFAULT NULL,
  `sub_invoice_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sub_invoice_details_amount` double NOT NULL,
  `sub_invoice_details_amount_fined` double NOT NULL DEFAULT 0,
  `sub_invoice_details_amount_total` double NOT NULL,
  `sub_invoice_details_amount_paid` double NOT NULL DEFAULT 0,
  `sub_invoice_details_va_number` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `sub_invoice_details_deadline` datetime NOT NULL,
  `sub_invoice_details_real_datetime_deadline` datetime DEFAULT NULL,
  `sub_invoice_details_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sub_invoice_details_remarks` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sub_invoice_details_status` enum('default','paid','fined') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `sub_invoice_details_datetime_paid_off` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_testimonial`
--

CREATE TABLE `dt_testimonial` (
  `testimonial_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `testimoni` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_user_notes`
--

CREATE TABLE `dt_user_notes` (
  `notes_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_target_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_reporter_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `notes_comment` text COLLATE utf8_unicode_ci NOT NULL,
  `notes_category` enum('positive','negative') COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='catatan untuk user memberikan catatan kepada user lain';

-- --------------------------------------------------------

--
-- Table structure for table `ref_bank`
--

CREATE TABLE `ref_bank` (
  `bank_code` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `bank_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_confirm_validation`
--

CREATE TABLE `ref_confirm_validation` (
  `confirmation_id` int(11) NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `vaccine_confirmation` enum('confirmed','not_confirmed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'not_confirmed',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_country`
--

CREATE TABLE `ref_country` (
  `country_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `country_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country_phone_code` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_country`
--

INSERT INTO `ref_country` (`country_id`, `country_code`, `country_name`, `country_phone_code`, `date_added`, `timestamp`) VALUES
('1adf3687-0f2f-45fc-af74-374223c1e7cc', 'BL', 'Saint Bartelemey', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('2b47a8c6-1db1-45d4-a73b-0448234500c8', 'TL', 'Timor-Leste', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('2b818684-d176-47dd-a914-8f8bd58c2bb4', 'IM', 'Isle Of Man', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('727c9293-afce-4161-92fe-9348504b0cab', 'ME', 'Montenegro', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('9bb7205c-8b22-11e9-973e-52540001273f', 'AF', 'Afghanistan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb721d3-8b22-11e9-973e-52540001273f', 'AR', 'Argentina', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72248-8b22-11e9-973e-52540001273f', 'IS', 'Iceland', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7229f-8b22-11e9-973e-52540001273f', 'IN', 'India', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb722f5-8b22-11e9-973e-52540001273f', 'ID', 'Indonesia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72345-8b22-11e9-973e-52540001273f', 'IR', 'Iran', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72398-8b22-11e9-973e-52540001273f', 'IQ', 'Iraq', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb723ea-8b22-11e9-973e-52540001273f', 'IE', 'Ireland', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72438-8b22-11e9-973e-52540001273f', 'IL', 'Israel', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7248b-8b22-11e9-973e-52540001273f', 'IT', 'Italy', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb724dc-8b22-11e9-973e-52540001273f', 'JM', 'Jamaica', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7252d-8b22-11e9-973e-52540001273f', 'JP', 'Japan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7257a-8b22-11e9-973e-52540001273f', 'AM', 'Armenia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb725cc-8b22-11e9-973e-52540001273f', 'XJ', 'Jersey', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7261c-8b22-11e9-973e-52540001273f', 'JO', 'Jordan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7266d-8b22-11e9-973e-52540001273f', 'KZ', 'Kazakhstan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb726bd-8b22-11e9-973e-52540001273f', 'KE', 'Kenya', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7270d-8b22-11e9-973e-52540001273f', 'KI', 'Kiribati', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7275e-8b22-11e9-973e-52540001273f', 'KP', 'Korea North', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb727b0-8b22-11e9-973e-52540001273f', 'KR', 'Korea South', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7284e-8b22-11e9-973e-52540001273f', 'KW', 'Kuwait', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb728a4-8b22-11e9-973e-52540001273f', 'KG', 'Kyrgyzstan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb728f6-8b22-11e9-973e-52540001273f', 'LA', 'Laos', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72946-8b22-11e9-973e-52540001273f', 'AW', 'Aruba', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72993-8b22-11e9-973e-52540001273f', 'LV', 'Latvia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb729e2-8b22-11e9-973e-52540001273f', 'LB', 'Lebanon', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72a35-8b22-11e9-973e-52540001273f', 'LS', 'Lesotho', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72a86-8b22-11e9-973e-52540001273f', 'LR', 'Liberia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72ad5-8b22-11e9-973e-52540001273f', 'LY', 'Libya', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72b23-8b22-11e9-973e-52540001273f', 'LI', 'Liechtenstein', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72b78-8b22-11e9-973e-52540001273f', 'LT', 'Lithuania', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72bc9-8b22-11e9-973e-52540001273f', 'LU', 'Luxembourg', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72d9c-8b22-11e9-973e-52540001273f', 'MO', 'Macau S.A.R.', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72df4-8b22-11e9-973e-52540001273f', 'MK', 'Macedonia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72e47-8b22-11e9-973e-52540001273f', 'AU', 'Australia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72e98-8b22-11e9-973e-52540001273f', 'MG', 'Madagascar', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72ee6-8b22-11e9-973e-52540001273f', 'MW', 'Malawi', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72f36-8b22-11e9-973e-52540001273f', 'MY', 'Malaysia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72f87-8b22-11e9-973e-52540001273f', 'MV', 'Maldives', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb72fd8-8b22-11e9-973e-52540001273f', 'ML', 'Mali', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73026-8b22-11e9-973e-52540001273f', 'MT', 'Malta', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73075-8b22-11e9-973e-52540001273f', 'XM', 'Man (Isle of)', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb730c7-8b22-11e9-973e-52540001273f', 'MH', 'Marshall Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb735bd-8b22-11e9-973e-52540001273f', 'MQ', 'Martinique', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7363e-8b22-11e9-973e-52540001273f', 'MR', 'Mauritania', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb736a2-8b22-11e9-973e-52540001273f', 'AT', 'Austria', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73707-8b22-11e9-973e-52540001273f', 'MU', 'Mauritius', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73768-8b22-11e9-973e-52540001273f', 'YT', 'Mayotte', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb737b9-8b22-11e9-973e-52540001273f', 'MX', 'Mexico', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7380b-8b22-11e9-973e-52540001273f', 'FM', 'Micronesia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7385f-8b22-11e9-973e-52540001273f', 'MD', 'Moldova', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb738b2-8b22-11e9-973e-52540001273f', 'MC', 'Monaco', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73902-8b22-11e9-973e-52540001273f', 'MN', 'Mongolia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73953-8b22-11e9-973e-52540001273f', 'MS', 'Montserrat', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb739a8-8b22-11e9-973e-52540001273f', 'MA', 'Morocco', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb739fb-8b22-11e9-973e-52540001273f', 'MZ', 'Mozambique', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73a4d-8b22-11e9-973e-52540001273f', 'AZ', 'Azerbaijan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73a9f-8b22-11e9-973e-52540001273f', 'MM', 'Myanmar', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73af2-8b22-11e9-973e-52540001273f', 'NA', 'Namibia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73b44-8b22-11e9-973e-52540001273f', 'NR', 'Nauru', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73b96-8b22-11e9-973e-52540001273f', 'NP', 'Nepal', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73be7-8b22-11e9-973e-52540001273f', 'AN', 'Netherlands Antilles', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73c3d-8b22-11e9-973e-52540001273f', 'NL', 'Netherlands The', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73c90-8b22-11e9-973e-52540001273f', 'NC', 'New Caledonia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73ce3-8b22-11e9-973e-52540001273f', 'NZ', 'New Zealand', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb73d38-8b22-11e9-973e-52540001273f', 'NI', 'Nicaragua', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7404d-8b22-11e9-973e-52540001273f', 'NE', 'Niger', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb740a4-8b22-11e9-973e-52540001273f', 'BS', 'Bahamas The', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb740f6-8b22-11e9-973e-52540001273f', 'NG', 'Nigeria', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7414a-8b22-11e9-973e-52540001273f', 'NU', 'Niue', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb741f0-8b22-11e9-973e-52540001273f', 'NF', 'Norfolk Island', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74258-8b22-11e9-973e-52540001273f', 'MP', 'Northern Mariana Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb742ac-8b22-11e9-973e-52540001273f', 'NO', 'Norway', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74300-8b22-11e9-973e-52540001273f', 'OM', 'Oman', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74352-8b22-11e9-973e-52540001273f', 'PK', 'Pakistan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb743a4-8b22-11e9-973e-52540001273f', 'PW', 'Palau', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb743f4-8b22-11e9-973e-52540001273f', 'PS', 'Palestinian Territory Occupied', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74449-8b22-11e9-973e-52540001273f', 'PA', 'Panama', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7449b-8b22-11e9-973e-52540001273f', 'BH', 'Bahrain', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb744ee-8b22-11e9-973e-52540001273f', 'PG', 'Papua new Guinea', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7453e-8b22-11e9-973e-52540001273f', 'PY', 'Paraguay', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74590-8b22-11e9-973e-52540001273f', 'PE', 'Peru', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb745e4-8b22-11e9-973e-52540001273f', 'PH', 'Philippines', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74638-8b22-11e9-973e-52540001273f', 'PN', 'Pitcairn Island', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7468a-8b22-11e9-973e-52540001273f', 'PL', 'Poland', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb746da-8b22-11e9-973e-52540001273f', 'PT', 'Portugal', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7472d-8b22-11e9-973e-52540001273f', 'PR', 'Puerto Rico', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7477f-8b22-11e9-973e-52540001273f', 'QA', 'Qatar', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb747ce-8b22-11e9-973e-52540001273f', 'RE', 'Reunion', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74820-8b22-11e9-973e-52540001273f', 'BD', 'Bangladesh', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74875-8b22-11e9-973e-52540001273f', 'RO', 'Romania', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb748c7-8b22-11e9-973e-52540001273f', 'RU', 'Russia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74917-8b22-11e9-973e-52540001273f', 'RW', 'Rwanda', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74968-8b22-11e9-973e-52540001273f', 'SH', 'Saint Helena', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb749be-8b22-11e9-973e-52540001273f', 'KN', 'Saint Kitts And Nevis', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74a12-8b22-11e9-973e-52540001273f', 'LC', 'Saint Lucia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74a62-8b22-11e9-973e-52540001273f', 'PM', 'Saint Pierre and Miquelon', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74ab7-8b22-11e9-973e-52540001273f', 'VC', 'Saint Vincent And The Grenadines', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74b0b-8b22-11e9-973e-52540001273f', 'WS', 'Samoa', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74b5d-8b22-11e9-973e-52540001273f', 'SM', 'San Marino', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74baf-8b22-11e9-973e-52540001273f', 'BB', 'Barbados', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74c03-8b22-11e9-973e-52540001273f', 'ST', 'Sao Tome and Principe', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74c58-8b22-11e9-973e-52540001273f', 'SA', 'Saudi Arabia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb74cae-8b22-11e9-973e-52540001273f', 'SN', 'Senegal', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb750cc-8b22-11e9-973e-52540001273f', 'RS', 'Serbia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb75128-8b22-11e9-973e-52540001273f', 'SC', 'Seychelles', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7517d-8b22-11e9-973e-52540001273f', 'SL', 'Sierra Leone', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb751d2-8b22-11e9-973e-52540001273f', 'SG', 'Singapore', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb75224-8b22-11e9-973e-52540001273f', 'SK', 'Slovakia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb75275-8b22-11e9-973e-52540001273f', 'SI', 'Slovenia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb752c6-8b22-11e9-973e-52540001273f', 'XG', 'Smaller Territories of the UK', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7531b-8b22-11e9-973e-52540001273f', 'AL', 'Albania', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7536b-8b22-11e9-973e-52540001273f', 'BY', 'Belarus', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb753bd-8b22-11e9-973e-52540001273f', 'SB', 'Solomon Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb75413-8b22-11e9-973e-52540001273f', 'SO', 'Somalia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb75465-8b22-11e9-973e-52540001273f', 'ZA', 'South Africa', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb754b7-8b22-11e9-973e-52540001273f', 'GS', 'South Georgia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb75508-8b22-11e9-973e-52540001273f', 'SS', 'South Sudan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7555b-8b22-11e9-973e-52540001273f', 'ES', 'Spain', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb755ad-8b22-11e9-973e-52540001273f', 'LK', 'Sri Lanka', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb755ff-8b22-11e9-973e-52540001273f', 'SD', 'Sudan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7564f-8b22-11e9-973e-52540001273f', 'SR', 'Suriname', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb756a3-8b22-11e9-973e-52540001273f', 'SJ', 'Svalbard And Jan Mayen Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb756f8-8b22-11e9-973e-52540001273f', 'BE', 'Belgium', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7574a-8b22-11e9-973e-52540001273f', 'SZ', 'Swaziland', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb7579c-8b22-11e9-973e-52540001273f', 'SE', 'Sweden', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bb757ef-8b22-11e9-973e-52540001273f', 'CH', 'Switzerland', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc621e3-8b22-11e9-973e-52540001273f', 'SY', 'Syria', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc622f5-8b22-11e9-973e-52540001273f', 'TW', 'Taiwan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62357-8b22-11e9-973e-52540001273f', 'TJ', 'Tajikistan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc623b3-8b22-11e9-973e-52540001273f', 'TZ', 'Tanzania', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6240e-8b22-11e9-973e-52540001273f', 'TH', 'Thailand', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62465-8b22-11e9-973e-52540001273f', 'TG', 'Togo', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc624b8-8b22-11e9-973e-52540001273f', 'TK', 'Tokelau', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62512-8b22-11e9-973e-52540001273f', 'BZ', 'Belize', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62568-8b22-11e9-973e-52540001273f', 'TO', 'Tonga', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc625bb-8b22-11e9-973e-52540001273f', 'TT', 'Trinidad And Tobago', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62610-8b22-11e9-973e-52540001273f', 'TN', 'Tunisia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62665-8b22-11e9-973e-52540001273f', 'TR', 'Turkey', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc626ba-8b22-11e9-973e-52540001273f', 'TM', 'Turkmenistan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6270f-8b22-11e9-973e-52540001273f', 'TC', 'Turks And Caicos Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62766-8b22-11e9-973e-52540001273f', 'TV', 'Tuvalu', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc627bc-8b22-11e9-973e-52540001273f', 'UG', 'Uganda', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62811-8b22-11e9-973e-52540001273f', 'UA', 'Ukraine', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6286a-8b22-11e9-973e-52540001273f', 'AE', 'United Arab Emirates', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc628c5-8b22-11e9-973e-52540001273f', 'BJ', 'Benin', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62ce1-8b22-11e9-973e-52540001273f', 'GB', 'United Kingdom', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62d3e-8b22-11e9-973e-52540001273f', 'US', 'United States', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62d92-8b22-11e9-973e-52540001273f', 'UM', 'United States Minor Outlying Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62de8-8b22-11e9-973e-52540001273f', 'UY', 'Uruguay', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62e3d-8b22-11e9-973e-52540001273f', 'UZ', 'Uzbekistan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62e93-8b22-11e9-973e-52540001273f', 'VU', 'Vanuatu', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62ee3-8b22-11e9-973e-52540001273f', 'VA', 'Vatican City State (Holy See)', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62f39-8b22-11e9-973e-52540001273f', 'VE', 'Venezuela', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62f8f-8b22-11e9-973e-52540001273f', 'VN', 'Vietnam', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc62fe4-8b22-11e9-973e-52540001273f', 'VG', 'Virgin Islands (British)', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63037-8b22-11e9-973e-52540001273f', 'BM', 'Bermuda', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6308b-8b22-11e9-973e-52540001273f', 'VI', 'Virgin Islands (US)', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc630e1-8b22-11e9-973e-52540001273f', 'WF', 'Wallis And Futuna Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63138-8b22-11e9-973e-52540001273f', 'EH', 'Western Sahara', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6318b-8b22-11e9-973e-52540001273f', 'YE', 'Yemen', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc631dd-8b22-11e9-973e-52540001273f', 'YU', 'Yugoslavia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63234-8b22-11e9-973e-52540001273f', 'ZM', 'Zambia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63288-8b22-11e9-973e-52540001273f', 'ZW', 'Zimbabwe', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc632db-8b22-11e9-973e-52540001273f', 'BT', 'Bhutan', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6332d-8b22-11e9-973e-52540001273f', 'BO', 'Bolivia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63382-8b22-11e9-973e-52540001273f', 'BA', 'Bosnia and Herzegovina', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc633d7-8b22-11e9-973e-52540001273f', 'BW', 'Botswana', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63429-8b22-11e9-973e-52540001273f', 'BV', 'Bouvet Island', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6347a-8b22-11e9-973e-52540001273f', 'DZ', 'Algeria', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc634ce-8b22-11e9-973e-52540001273f', 'BR', 'Brazil', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63520-8b22-11e9-973e-52540001273f', 'IO', 'British Indian Ocean Territory', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63572-8b22-11e9-973e-52540001273f', 'BN', 'Brunei', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63642-8b22-11e9-973e-52540001273f', 'BG', 'Bulgaria', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc636aa-8b22-11e9-973e-52540001273f', 'BF', 'Burkina Faso', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc636ff-8b22-11e9-973e-52540001273f', 'BI', 'Burundi', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63751-8b22-11e9-973e-52540001273f', 'KH', 'Cambodia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc637a3-8b22-11e9-973e-52540001273f', 'CM', 'Cameroon', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc637f7-8b22-11e9-973e-52540001273f', 'CA', 'Canada', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6384d-8b22-11e9-973e-52540001273f', 'CV', 'Cape Verde', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6389d-8b22-11e9-973e-52540001273f', 'AS', 'American Samoa', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc638f3-8b22-11e9-973e-52540001273f', 'KY', 'Cayman Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63947-8b22-11e9-973e-52540001273f', 'CF', 'Central African Republic', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6399e-8b22-11e9-973e-52540001273f', 'TD', 'Chad', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc639ef-8b22-11e9-973e-52540001273f', 'CL', 'Chile', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63a41-8b22-11e9-973e-52540001273f', 'CN', 'China', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63a93-8b22-11e9-973e-52540001273f', 'CX', 'Christmas Island', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63ae7-8b22-11e9-973e-52540001273f', 'CC', 'Cocos (Keeling) Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63b39-8b22-11e9-973e-52540001273f', 'CO', 'Colombia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63b8b-8b22-11e9-973e-52540001273f', 'KM', 'Comoros', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63bdf-8b22-11e9-973e-52540001273f', 'CG', 'Congo', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc63c35-8b22-11e9-973e-52540001273f', 'AD', 'Andorra', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6403d-8b22-11e9-973e-52540001273f', 'CD', 'Congo The Democratic Republic Of The', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64095-8b22-11e9-973e-52540001273f', 'CK', 'Cook Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc640eb-8b22-11e9-973e-52540001273f', 'CR', 'Costa Rica', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64140-8b22-11e9-973e-52540001273f', 'CI', 'Cote D\'Ivoire (Ivory Coast)', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64194-8b22-11e9-973e-52540001273f', 'HR', 'Croatia (Hrvatska)', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc641ea-8b22-11e9-973e-52540001273f', 'CU', 'Cuba', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64240-8b22-11e9-973e-52540001273f', 'CY', 'Cyprus', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64293-8b22-11e9-973e-52540001273f', 'CZ', 'Czech Republic', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc642e4-8b22-11e9-973e-52540001273f', 'DK', 'Denmark', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6437f-8b22-11e9-973e-52540001273f', 'DJ', 'Djibouti', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc643da-8b22-11e9-973e-52540001273f', 'AO', 'Angola', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6442d-8b22-11e9-973e-52540001273f', 'DM', 'Dominica', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6447f-8b22-11e9-973e-52540001273f', 'DO', 'Dominican Republic', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc644d4-8b22-11e9-973e-52540001273f', 'TP', 'East Timor', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64529-8b22-11e9-973e-52540001273f', 'EC', 'Ecuador', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6457d-8b22-11e9-973e-52540001273f', 'EG', 'Egypt', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc645ce-8b22-11e9-973e-52540001273f', 'SV', 'El Salvador', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64621-8b22-11e9-973e-52540001273f', 'GQ', 'Equatorial Guinea', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64675-8b22-11e9-973e-52540001273f', 'ER', 'Eritrea', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc646c9-8b22-11e9-973e-52540001273f', 'EE', 'Estonia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6471a-8b22-11e9-973e-52540001273f', 'ET', 'Ethiopia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6476c-8b22-11e9-973e-52540001273f', 'AI', 'Anguilla', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc647bf-8b22-11e9-973e-52540001273f', 'XA', 'External Territories of Australia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64815-8b22-11e9-973e-52540001273f', 'FK', 'Falkland Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64866-8b22-11e9-973e-52540001273f', 'FO', 'Faroe Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc648b9-8b22-11e9-973e-52540001273f', 'FJ', 'Fiji Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6490d-8b22-11e9-973e-52540001273f', 'FI', 'Finland', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64960-8b22-11e9-973e-52540001273f', 'FR', 'France', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc649b1-8b22-11e9-973e-52540001273f', 'GF', 'French Guiana', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64a03-8b22-11e9-973e-52540001273f', 'PF', 'French Polynesia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64a57-8b22-11e9-973e-52540001273f', 'TF', 'French Southern Territories', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64aab-8b22-11e9-973e-52540001273f', 'GA', 'Gabon', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64afc-8b22-11e9-973e-52540001273f', 'AQ', 'Antarctica', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64b50-8b22-11e9-973e-52540001273f', 'GM', 'Gambia The', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64ba4-8b22-11e9-973e-52540001273f', 'GE', 'Georgia', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64bf8-8b22-11e9-973e-52540001273f', 'DE', 'Germany', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64c49-8b22-11e9-973e-52540001273f', 'GH', 'Ghana', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64c9a-8b22-11e9-973e-52540001273f', 'GI', 'Gibraltar', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64cee-8b22-11e9-973e-52540001273f', 'GR', 'Greece', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64d42-8b22-11e9-973e-52540001273f', 'GL', 'Greenland', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64d93-8b22-11e9-973e-52540001273f', 'GD', 'Grenada', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64de4-8b22-11e9-973e-52540001273f', 'GP', 'Guadeloupe', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64e38-8b22-11e9-973e-52540001273f', 'GU', 'Guam', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc64e8c-8b22-11e9-973e-52540001273f', 'AG', 'Antigua And Barbuda', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6531a-8b22-11e9-973e-52540001273f', 'GT', 'Guatemala', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc65372-8b22-11e9-973e-52540001273f', 'XU', 'Guernsey and Alderney', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc653c6-8b22-11e9-973e-52540001273f', 'GN', 'Guinea', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6541a-8b22-11e9-973e-52540001273f', 'GW', 'Guinea-Bissau', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6546a-8b22-11e9-973e-52540001273f', 'GY', 'Guyana', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc654bd-8b22-11e9-973e-52540001273f', 'HT', 'Haiti', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc65511-8b22-11e9-973e-52540001273f', 'HM', 'Heard and McDonald Islands', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc65567-8b22-11e9-973e-52540001273f', 'HN', 'Honduras', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc655b8-8b22-11e9-973e-52540001273f', 'HK', 'Hong Kong S.A.R.', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9bc6560b-8b22-11e9-973e-52540001273f', 'HU', 'Hungary', NULL, '2019-06-10 08:52:19', '2019-06-10 01:53:56'),
('9da19ea5-a542-4c42-a843-d78bc31d540c', 'JE', 'Jersey', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('a9374fdc-1a1c-4e02-ab12-03bb2e03b26d', 'BQ', 'Bonaire, Saint Eustatius and Saba', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('ab0b9bfd-afcc-4f28-bc42-ba3654fe56e6', 'SX', 'Sint Maarten', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('c32ba12b-ecd3-42ff-b903-66cdc3f4db1f', 'CS', 'Serbia And Montenegro', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('c82d1f2f-1400-4d26-977c-7d7bb644cfb5', 'AX', 'Aland Islands', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('c85266c6-401e-4ea3-99dd-e743b185248e', 'MF', 'Saint Martin', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('eff4a2a7-78be-47c5-8c08-13def50c6af4', 'GG', 'Guernsey', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52'),
('f4cd5f92-dcbb-447e-88bb-2c62911d965f', 'CW', 'Curacao', NULL, '0000-00-00 00:00:00', '2022-10-18 06:52:52');

-- --------------------------------------------------------

--
-- Table structure for table `ref_curriculum`
--

CREATE TABLE `ref_curriculum` (
  `curriculum_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `study_program_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `program_id` int(11) NOT NULL,
  `academic_year_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `valid_academic_year` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `curriculum_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `curriculum_total_credit_mandatory` int(11) NOT NULL DEFAULT 0,
  `curriculum_total_credit_mandatory_fixed` int(11) NOT NULL DEFAULT 0,
  `curriculum_total_credit_elective` int(11) NOT NULL DEFAULT 0,
  `curriculum_total_credit_elective_fixed` int(11) NOT NULL DEFAULT 0,
  `curriculum_total_credit_extracurricular` int(11) NOT NULL DEFAULT 0,
  `curriculum_total_credit_extracurricular_fixed` int(11) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_curriculum_semester`
--

CREATE TABLE `ref_curriculum_semester` (
  `curriculum_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `semester_id` int(11) NOT NULL,
  `curriculum_semester_total_credit_mandatory` int(11) NOT NULL DEFAULT 0,
  `curriculum_semester_total_credit_mandatory_fixed` int(11) NOT NULL DEFAULT 0,
  `curriculum_semester_total_credit_elective` int(11) NOT NULL DEFAULT 0,
  `curriculum_semester_total_credit_elective_fixed` int(11) NOT NULL DEFAULT 0,
  `curriculum_semester_total_credit_extracurricular` int(11) NOT NULL DEFAULT 0,
  `curriculum_semester_total_credit_extracurricular_fixed` int(11) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_curriculum_subject`
--

CREATE TABLE `ref_curriculum_subject` (
  `curriculum_subject_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `curriculum_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `semester_id` int(11) NOT NULL,
  `subject_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `curriculum_subject_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `curriculum_subject_credit` int(11) NOT NULL,
  `curriculum_subject_ects` double NOT NULL,
  `curriculum_subject_category` enum('regular semester','ofse','research semester') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'regular semester',
  `curriculum_subject_type` enum('mandatory','elective','extracurricular') COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_department`
--

CREATE TABLE `ref_department` (
  `department_id` int(11) NOT NULL,
  `employee_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'head of department',
  `department_name` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `department_abbreviation` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_document`
--

CREATE TABLE `ref_document` (
  `document_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `document_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `document_weight` int(11) NOT NULL DEFAULT 999,
  `document_general` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'yes',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_document_type`
--

CREATE TABLE `ref_document_type` (
  `document_type_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `document_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `document_type_name` enum('general','un','non_un','transfer_student','international_school','wna') COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_event_field`
--

CREATE TABLE `ref_event_field` (
  `field_id` int(11) NOT NULL,
  `field_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `field_title_default` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_type` enum('input','option') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'input',
  `field_option_default` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_input_type_default` enum('text','textarea','number','email') COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_faculty`
--

CREATE TABLE `ref_faculty` (
  `faculty_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `deans_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'personal_data_id',
  `faculty_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `faculty_deans_sk_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `faculty_name_feeder` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `faculty_abbreviation` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_institution`
--

CREATE TABLE `ref_institution` (
  `institution_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `institution_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `institution_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `institution_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `institution_phone_number` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `institution_type` enum('highschool','university','office') COLLATE utf8_unicode_ci DEFAULT NULL,
  `institution_is_international` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT NULL,
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `date_added` datetime DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_invoice_setting`
--

CREATE TABLE `ref_invoice_setting` (
  `setting_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `fined_per_month` double DEFAULT NULL,
  `fined_type` enum('flat','increase') COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_letter_type`
--

CREATE TABLE `ref_letter_type` (
  `letter_type_id` int(11) NOT NULL,
  `letter_name` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `letter_description` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `letter_abbreviation` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_letter_type_template`
--

CREATE TABLE `ref_letter_type_template` (
  `template_id` int(11) NOT NULL,
  `letter_type_id` int(11) NOT NULL,
  `filename` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `template_filelink` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_available_generated` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_master_program`
--

CREATE TABLE `ref_master_program` (
  `master_program_id` int(11) NOT NULL,
  `master_program_code` char(5) COLLATE utf8_unicode_ci NOT NULL,
  `master_program_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `master_program_level` enum('undergraduate','graduate','post-graduate') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'undergraduate',
  `master_program_main_id` int(11) DEFAULT NULL,
  `master_program_is_active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_master_program`
--

INSERT INTO `ref_master_program` (`master_program_id`, `master_program_code`, `master_program_name`, `master_program_level`, `master_program_main_id`, `master_program_is_active`, `date_added`, `timestamp`) VALUES
(1, 'GII', 'German International Institute', 'undergraduate', NULL, 'yes', '2021-03-16 10:23:17', '2021-03-15 20:23:17'),
(2, 'EXP', 'Executive Program', 'undergraduate', NULL, 'no', '2021-03-16 10:23:17', '2021-03-15 20:23:17'),
(3, 'NI', 'National Institute', 'undergraduate', NULL, 'yes', '2021-03-16 10:23:46', '2021-03-15 20:23:46');

-- --------------------------------------------------------

--
-- Table structure for table `ref_ocupation`
--

CREATE TABLE `ref_ocupation` (
  `ocupation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ocupation_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `pmb_sync` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=sudah sync',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_pages`
--

CREATE TABLE `ref_pages` (
  `pages_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pages_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pages_description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `pages_top_bar` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pages_uri` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_pages`
--

INSERT INTO `ref_pages` (`pages_id`, `pages_name`, `pages_description`, `pages_top_bar`, `pages_uri`, `date_added`, `timestamp`) VALUES
('046df049-820c-470b-9ff9-5fabcf267d48', 'alumni_dikti_tracer', 'Dikti Tracer Study', 'staff_alumni', 'alumni/lists_tracer', '2021-02-10 12:53:19', '2021-02-10 05:53:19'),
('0561a8cc-be81-4bb0-ae3f-26b175da09ee', 'finance_payment_list', 'Payment List', 'finance', 'finance/invoice/payment_lists', '2024-04-22 11:24:50', '2024-04-22 04:24:50'),
('148a1877-20fb-45a0-b223-f9fb3c4c3678', 'academic_student_activity_dikti', 'academic_student_activity_dikti', 'academic', 'academic/activity_study/activity_study_list', '2020-08-31 20:18:23', '2020-08-31 13:18:23'),
('1c671456-a5ad-4e41-b6fd-e476a079967c', 'roles_list', 'List Roles Devs', 'ite', 'https://staging.iuli.ac.id/portal2/devs/roles/roles_list', '2020-02-13 11:24:14', '2020-02-13 04:24:14'),
('252c1ea8-8636-4f9f-972b-d802b6885c55', 'academic_thesis', 'Thesis', 'academic', '#', '2023-01-06 09:59:43', '2023-01-06 02:59:43'),
('2df04001-af64-4f7f-9c3b-f044290443d7', 'candidate_questionnaire', 'Page', 'admission', '', '2020-03-10 14:01:53', '2020-03-10 07:01:53'),
('2fc32137-cfd7-4622-b78f-9114572ffb1c', 'script_list', '', 'ite', '', '2024-05-20 10:18:45', '2024-05-20 03:18:45'),
('3c9cbf12-2771-4440-ac4a-d084c8f4e94d', 'final_submission_academic', 'Thesis Final Submission', 'academic', 'thesis/final_submission', '2023-01-06 09:54:21', '2023-01-06 02:54:21'),
('3d5b2d00-875a-4339-ada1-87ced9f955db', 'advisor_examiner_list_academic', 'Advisor/Examiner List', 'academic', 'thesis/advisor_examiner_list', '2023-01-06 09:55:09', '2023-01-06 02:55:09'),
('3ebe3e2c-737b-44f0-94eb-6b9d90f50f14', 'addmission_student_course', 'Student List Course', 'admission', '', '2024-03-19 15:07:52', '2024-03-19 08:07:52'),
('40c4bae9-c5f8-4b2c-9b3f-8b88f765f47f', 'kurikulum_matkul_feeder', 'Kurikulum Matkul', 'ite', 'feeder/report/kurikulum_matkul', '2024-03-05 14:56:02', '2024-03-05 07:56:02'),
('40e2e52c-affd-4a5f-b654-c114897fb566', 'graduation_registration', 'Graduation Registration', 'apps', 'academic/student_academic/graduation_registration', '2024-06-04 14:46:47', '2024-06-04 07:46:47'),
('4b306511-b5e6-4ec1-98b6-d2d5b961fd61', 'prodi_list', 'Study Program', 'ite', 'devs/prodi_list', '2023-09-19 16:09:57', '2023-09-19 09:09:57'),
('4baa952f-ef46-4dc6-bf5b-203f4fdc25e8', 'employee_list', 'Employee List', 'hris', 'hris/employee_list', '2021-10-01 15:17:31', '2021-10-01 08:17:31'),
('4fe1d7ea-bf63-4034-b32c-c1a88b4d5daf', 'chart_list', 'Chart of Account List (COA)', 'gsr', 'apps/gsr/chart_list', '2022-06-08 15:25:02', '2022-06-08 08:25:02'),
('529c3c76-5279-443e-bc05-030b227f86ba', 'finance_recaps_billing', 'Tuition Fee Billing', 'finance', 'finance', '2023-10-06 10:12:29', '2023-10-09 08:37:34'),
('53240870-854b-4b26-9cde-b012d5725fff', 'internship_document', 'Internship Document', 'academic', 'student/internship/document_list', '2023-02-22 08:45:08', '2023-02-22 01:45:08'),
('541dd4ea-7d01-48f8-82d9-9028b39db1a8', 'proposal_submission', 'Proposal Submission', 'staff_thesis', 'thesis/proposal_submission', '2022-02-20 01:05:54', '2022-02-19 18:05:54'),
('5abf02b6-403d-4418-98e0-b7cb040fa93a', 'finance_student_aid', 'Student Aid', 'finance', 'finance/student_finance/aid', '2020-10-05 17:46:54', '2020-10-05 10:46:54'),
('648ce8fd-48f9-4ea4-ba93-19f08435ef74', 'final_submission', 'Final Submission', 'staff_thesis', 'thesis/final_submission', '2022-03-22 15:53:20', '2022-03-22 08:53:20'),
('6a392908-21ce-4fca-a06b-fc7549dca731', 'io_student_list', 'Student List', 'staff_international_office', 'student/io_student_list', '2022-11-08 12:14:34', '2022-11-08 05:14:34'),
('6be542aa-9d66-4692-8161-f942a70c1522', 'list_lecturer', 'List Lecturer', 'lecturer_assessment', 'validation_requirement/lecturer_assesment/list_lecturer', '2022-07-12 13:13:55', '2022-07-12 06:13:55'),
('72ebfcf3-dd98-40ca-8292-5e366bffab34', 'list_lecturer', 'List Lecturer Accreditation', 'academic', 'accreditation/lecturer_teaching', '2023-02-03 14:21:07', '2023-02-03 07:21:07'),
('74345797-1456-43c0-91c1-d785c10c14e9', 'finance_student_list', 'Finance Student List', 'finance', 'student/lists', '2020-12-25 08:53:07', '2020-12-25 01:53:07'),
('80277393-5447-4cc7-b65c-f2371a94605b', 'defense_list', 'Defense List', 'academic', '', '2024-02-22 15:20:03', '2024-02-22 08:20:03'),
('80d1643c-c1fc-4644-9546-3e2955e48e6a', 'alumni_list', 'List Alumni', 'staff_alumni', '', '2021-02-24 21:00:00', '2021-02-24 14:00:00'),
('841de6d9-f58f-4c30-9203-638ee0dba0ee', 'employee_list', 'List Employee Devs', 'ite', 'https://staging.iuli.ac.id/portal2/devs/devs_employee/employee_list', '2020-02-13 11:28:19', '2020-02-13 04:28:19'),
('84b669a0-b68a-484c-b6fc-5e673603c5cd', 'df_list', 'Disbursement List', 'gsr', 'apps/gsr/df_list', '2022-10-11 11:25:44', '2022-10-11 04:25:44'),
('84d6ccf4-ff8f-487e-9c14-b6bfca113859', 'thesis_student', 'Thesis', 'academic', 'thesis/thesis_student', '2023-01-27 11:07:01', '2023-01-27 04:07:01'),
('882df9ac-156f-4794-846d-f7364dfc2709', 'company_survey', 'Company Survey', 'staff_alumni', 'alumni/list_survey', '2021-04-09 15:40:51', '2021-04-09 08:40:51'),
('888333bc-1101-4732-9fb6-89ac6f65659d', 'department', 'Department', 'hris', 'hris/department', '2024-07-01 11:55:18', '2024-07-01 04:55:18'),
('8a4b0b43-a084-4583-bdd0-7715c6362477', 'addmission_student_karyawan', 'Student List Karyawan', 'admission', 'student/lists_karyawan', '2022-11-09 14:54:00', '2022-11-09 07:54:00'),
('8b0d19d3-7d76-4930-8f33-cfd9358a809b', 'student_satisfaction', 'Student Satisfaction', 'lecturer_assessment', 'validation_requirement/university_assessment/student_satisfaction', '2023-02-28 11:01:08', '2023-02-28 04:01:08'),
('8e1ff88c-7667-43ef-b9bc-07672f37844b', 'request_list', 'Request List', 'gsr', 'apps/gsr/request_list', '2022-06-08 15:25:22', '2022-06-08 08:25:22'),
('91bc9300-8cab-41fa-85bc-c5f36722d798', 'advisor_examiner_list', 'Advisor/Examiner List', 'staff_thesis', 'thesis/advisor_examiner_list', '2022-08-29 11:30:10', '2022-08-29 04:30:10'),
('96699ef8-de4b-4077-b9a7-3d4b0ceb0e1b', 'proposal_submission_academic', 'Proposal Submission', 'academic', 'thesis/proposal_submission', '2023-01-06 09:53:37', '2023-01-06 02:53:37'),
('a264beb2-fa21-4aa0-a3e6-8d6a6ea97a4e', 'rf_list', 'Receiptment List', 'gsr', 'apps/gsr/rf_list', '2022-12-09 11:35:59', '2022-12-09 04:35:59'),
('a90da69f-1c2b-450f-9b46-3816dbcc378a', 'academic_document', 'Academic Document', 'academic', 'https://staging.iuli.ac.id/portal2/academic/document/list', '2020-02-17 16:09:52', '2020-02-17 09:09:52'),
('a98504ea-e25c-418c-bae3-31a37e5dc68a', 'student_feeder', 'Student Report Feeder', 'ite', 'feeder/report/student_feeder', '2024-03-05 13:27:03', '2024-03-05 07:22:37'),
('abe82a03-9688-47b1-92e9-0b642ca7e2ed', 'io_student_abroad', 'Student Abroad', 'staff_international_office', 'admission/international_office/io_student_abroad', '2023-03-14 15:47:38', '2023-03-14 08:47:38'),
('ac943df3-cea5-4d20-9148-240a0d8a84bd', 'pages_list', 'List Pages Devs', 'ite', 'https://staging.iuli.ac.id/portal2/devs/pages/pages_list', '2020-02-13 11:23:37', '2020-02-13 04:23:37'),
('bb80eb4e-f8cf-4d5a-8b32-f1cac1cbfddc', 'student_document_page', '', 'student_document', '', '2023-08-04 10:25:05', '2023-08-04 03:25:05'),
('c2f8791d-0dc1-4bda-9222-7b2ec219b3f2', 'work_submission_academic', 'Thesis Work Submission', 'academic', 'thesis/work_submission', '2023-01-06 09:53:58', '2023-01-06 02:53:58'),
('cf0fa5d3-cc4d-4ad1-bbd7-1e817d39fb03', 'thesis_defense_academic', 'Thesis Defense', 'academic', 'thesis/thesis_defense', '2023-01-06 09:54:43', '2023-01-06 02:54:43'),
('d0e3c15e-46fd-11ea-b77f-2e728ce88125', 'finance_invoice_list', 'Invoice List', 'finance', 'https://staging.iuli.ac.id/portal2/finance/invoice/lists', '2020-02-04 10:43:39', '2020-02-07 02:53:42'),
('d0e3c4ba-46fd-11ea-b77f-2e728ce88125', 'finance_fee_list', 'Fee List', 'finance', 'https://staging.iuli.ac.id/portal2/finance/fee/lists', '2020-02-04 10:43:39', '2020-02-07 02:53:53'),
('d0e3c6b8-46fd-11ea-b77f-2e728ce88125', 'finance_payment_code_reference', 'Payment Code Reference', 'finance', 'https://staging.iuli.ac.id/portal2/finance/reference/payment_code', '2020-02-04 10:43:39', '2020-02-07 02:54:37'),
('d0e3c802-46fd-11ea-b77f-2e728ce88125', 'addmission_student_list', 'Admission Student Lists', 'admission', 'https://staging.iuli.ac.id/portal2/student/lists', '2020-02-04 10:43:39', '2020-02-07 02:55:16'),
('d0e3ca5a-46fd-11ea-b77f-2e728ce88125', 'addmission_institution_list', 'Institution List', 'admission', 'https://staging.iuli.ac.id/portal2/institution/lists', '2020-02-04 10:43:39', '2020-02-07 02:55:29'),
('d0e3cba4-46fd-11ea-b77f-2e728ce88125', 'addmission_refferal_list', 'Referral List', 'admission', 'https://staging.iuli.ac.id/portal2/admission/referral/lists', '2020-02-04 10:43:39', '2020-02-07 02:55:59'),
('d0e3cdac-46fd-11ea-b77f-2e728ce88125', 'academic_student_list', 'Academic Student Lists', 'academic', 'https://staging.iuli.ac.id/portal2/academic/student_academic/student_lists', '2020-02-04 10:43:39', '2020-02-07 02:57:41'),
('d0e3cee2-46fd-11ea-b77f-2e728ce88125', 'student_profile', 'Personal Data', 'profile', 'https://staging.iuli.ac.id/portal2/personal_data/profile/$personal_data_id', '2020-02-04 10:43:39', '2020-02-07 02:57:50'),
('d0e3d126-46fd-11ea-b77f-2e728ce88125', 'student_settings', 'Student Settings', 'academic', 'https://staging.iuli.ac.id/portal2/academic/student_academic/student_setting/$student_id', '2020-02-04 10:43:39', '2020-02-07 02:58:26'),
('d0e3d270-46fd-11ea-b77f-2e728ce88125', 'student_transfer_credit', 'Transfer Credit', 'academic', 'https://staging.iuli.ac.id/portal2/academic/transfer_credit/transfer_student/$student_id', '2020-02-04 10:43:39', '2020-02-07 02:58:44'),
('d0e3d39c-46fd-11ea-b77f-2e728ce88125', 'student_score', 'Student Score', 'academic', 'https://staging.iuli.ac.id/portal2/academic/score/student_score/$personal_data_id', '2020-02-04 10:43:39', '2020-02-07 02:58:50'),
('d0e3d5ea-46fd-11ea-b77f-2e728ce88125', 'academic_semester_list', 'Semester List', 'academic', 'https://staging.iuli.ac.id/portal2/academic/semester/semester_lists', '2020-02-04 10:43:39', '2020-02-07 02:58:55'),
('d0e3d720-46fd-11ea-b77f-2e728ce88125', 'academic_semester_periode_update', 'Semester Periode', 'academic', 'https://staging.iuli.ac.id/portal2/academic/semester/semester_lists/$academic_year_id/$s_semester_type_id', '2020-02-04 10:43:39', '2020-02-07 02:59:00'),
('d0e3d93c-46fd-11ea-b77f-2e728ce88125', 'academic_subject_list', 'Subject Lists', 'academic', 'https://staging.iuli.ac.id/portal2/academic/subject/subject_lists', '2020-02-04 10:43:39', '2020-02-07 02:59:31'),
('d0e3da7c-46fd-11ea-b77f-2e728ce88125', 'academic_curriculum_list', 'Curriculum Lists', 'academic', 'https://staging.iuli.ac.id/portal2/academic/curriculum/curriculum_lists', '2020-02-04 10:43:39', '2020-02-07 02:59:43'),
('d0e3dc8e-46fd-11ea-b77f-2e728ce88125', 'academic_curriculum_semester_list', 'Curriculum Semester', 'academic', 'https://staging.iuli.ac.id/portal2/academic/curriculum/curriculum_lists/$curriculum_id', '2020-02-04 10:43:39', '2020-02-07 02:59:53'),
('d0e3def0-46fd-11ea-b77f-2e728ce88125', 'academic_curriculum_subject_list', 'Curriculum Subject', 'academic', 'https://staging.iuli.ac.id/portal2/academic/curriculum/curriculum_lists/$curriculum_id/$semester_id', '2020-02-04 10:43:39', '2020-02-07 03:00:00'),
('d0e3e12a-46fd-11ea-b77f-2e728ce88125', 'academic_offered_subject', 'Offered Subject', 'academic', 'https://staging.iuli.ac.id/portal2/academic/offered_subject/offered_subject_lists', '2020-02-04 10:43:39', '2020-02-07 03:00:03'),
('d0e3e274-46fd-11ea-b77f-2e728ce88125', 'academic_class_group_list', 'Class Group', 'academic', 'https://staging.iuli.ac.id/portal2/academic/class_group/class_group_lists', '2020-02-04 10:43:39', '2020-02-07 03:00:07'),
('d0e3e4e0-46fd-11ea-b77f-2e728ce88125', 'academic_class_group_member', 'Class Group Member', 'academic', 'https://staging.iuli.ac.id/portal2/academic/class_group/class_group_lists/$class_master_id', '2020-02-04 10:43:39', '2020-02-07 03:00:18'),
('d0e3e620-46fd-11ea-b77f-2e728ce88125', 'academic_class_group_absence', 'Class Group Absence', 'academic', 'https://staging.iuli.ac.id/portal2/academic/class_group/class_absence/$class_master_id', '2020-02-04 10:43:39', '2020-02-07 03:00:23'),
('d0e3e76a-46fd-11ea-b77f-2e728ce88125', 'academic_ofse_list', 'OFSE', 'academic', 'https://staging.iuli.ac.id/portal2/academic/ofse/ofse_lists', '2020-02-04 10:43:39', '2020-02-07 03:00:27'),
('d0e3e9a4-46fd-11ea-b77f-2e728ce88125', 'academic_ofse_member', 'OFSE Member', 'academic', 'https://staging.iuli.ac.id/portal2/academic/ofse/ofse_lists/$class_group_id', '2020-02-04 10:43:39', '2020-02-07 03:00:35'),
('d0e3eae4-46fd-11ea-b77f-2e728ce88125', 'academic_krs_approval_student_list', 'KRS Lists', 'academic', 'https://staging.iuli.ac.id/portal2/krs/krs_approval', '2020-02-04 10:43:39', '2020-02-07 03:00:52'),
('d0e3ecec-46fd-11ea-b77f-2e728ce88125', 'academic_krs_student', 'KRS Student', 'academic', 'https://staging.iuli.ac.id/portal2/krs/krs_approval/$academic_year_id/$semester_id/$personal_data_id', '2020-02-04 10:43:39', '2020-02-07 03:00:47'),
('d0e3ee22-46fd-11ea-b77f-2e728ce88125', 'alumni_vacancy_lists', 'Vacancy', 'staff_alumni', 'https://staging.iuli.ac.id/portal2/alumni/vacancy/lists_vacancy', '2020-02-04 10:43:39', '2020-02-07 03:00:58'),
('d0e3ef58-46fd-11ea-b77f-2e728ce88125', 'alumni_testimonial_lists', 'Testimonial', 'staff_alumni', 'https://staging.iuli.ac.id/portal2/alumni/testimonial/testimonial_lists', '2020-02-04 10:43:39', '2020-02-07 03:01:02'),
('d0e3f1d8-46fd-11ea-b77f-2e728ce88125', 'alumni_iuli_info', 'INFO', 'staff_alumni', 'https://staging.iuli.ac.id/portal2/alumni/iuli_info/info_lists', '2020-02-04 10:43:39', '2020-02-07 03:01:04'),
('db6023ef-0b58-42fe-80a3-c1eaaf2ffaf1', 'entrance_test_online', 'Entrance Test', 'admission', 'addmission/entrance_test', '2020-03-17 12:05:27', '2020-03-17 05:05:27'),
('e1ad266d-a002-40b8-809c-823aa94f007a', 'tracer_dikti', 'Tracer Study', 'accreditation', 'accreditation/tracer_dikti', '2023-06-04 11:08:47', '2023-06-04 04:08:47'),
('e747d623-3afb-47a8-99a5-c23391b52ba7', 'admission_event_list', 'Event List', 'admission', '', '2020-02-18 10:20:54', '2020-02-18 03:20:54'),
('e760fb13-3e95-4d01-b134-a9f1c54b1772', 'list_repondent', 'Respondent', 'lecturer_assessment', 'validation_requirement/lecturer_assesment/list_respondent', '2022-07-12 13:14:22', '2022-07-12 06:14:22'),
('f3b1ccaf-d159-45d8-a860-4c4ab687e138', 'letter_numbering', 'Letter Number', 'apps', 'apps/letter_numbering/list_number_of_letter', '2022-06-09 09:39:11', '2024-01-22 06:56:36'),
('f5d2bf93-a4c6-4339-8f8a-e6dd7afe34a1', 'addmission_student_national', 'National Student', 'admission', '', '2024-03-26 14:14:35', '2024-03-26 07:14:35'),
('f63403b4-5964-4069-b5d6-34723ea88332', 'work_submission', 'Work Submission', 'staff_thesis', 'thesis/work_submission', '2022-01-27 09:56:41', '2022-01-27 02:56:41'),
('f8cdccc4-1ec6-4656-9f70-9e88f3b1ad13', 'finance_report_tuitionfee', 'Tuition Fee Report', 'finance', 'finance/invoice/report_tuition_fee', '2023-10-09 15:46:52', '2023-10-09 08:47:01'),
('f941e59b-5dd4-4cdd-a853-4a903446b22b', 'academic_student_supplement', 'Supplement List', 'academic', 'academic/student_academic/supplement_list', '2023-08-10 11:28:54', '2023-08-10 04:28:54'),
('fa69a65f-77b7-4b7f-84d5-d3ea0c73fd79', 'student_status', 'Student Status', 'accreditation', 'accreditation/student_status', '2023-05-26 15:17:55', '2023-05-26 08:17:55');

-- --------------------------------------------------------

--
-- Table structure for table `ref_partner`
--

CREATE TABLE `ref_partner` (
  `partner_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `partner_code` char(5) COLLATE utf8_unicode_ci NOT NULL,
  `partner_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_partner_period`
--

CREATE TABLE `ref_partner_period` (
  `partner_period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `partner_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `partner_period` int(11) NOT NULL,
  `partner_period_start_date` date NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_payment_type`
--

CREATE TABLE `ref_payment_type` (
  `payment_type_code` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `payment_type_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_program`
--

CREATE TABLE `ref_program` (
  `program_id` int(11) NOT NULL,
  `stream_id` tinyint(1) NOT NULL,
  `master_program_id` int(11) DEFAULT NULL,
  `program_code` char(5) COLLATE utf8_unicode_ci NOT NULL,
  `partner_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `program_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `program_level` enum('undergraduate','graduate','post-graduate','non-graduate') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'undergraduate',
  `program_main_id` int(11) DEFAULT NULL,
  `type_of_admission_code` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type_of_admission_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `program_description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `is_institute` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table institute';

--
-- Dumping data for table `ref_program`
--

INSERT INTO `ref_program` (`program_id`, `stream_id`, `master_program_id`, `program_code`, `partner_id`, `program_name`, `program_level`, `program_main_id`, `type_of_admission_code`, `type_of_admission_name`, `program_description`, `is_active`, `is_institute`, `date_added`, `timestamp`) VALUES
(1, 1, 1, 'GII', NULL, 'German International Institute', 'undergraduate', NULL, 'GII', 'German International Institute', NULL, 'yes', 'yes', '2019-07-12 15:14:19', '2021-04-08 03:25:50'),
(2, 1, 2, 'EXP', NULL, 'Executive Program', 'undergraduate', NULL, NULL, NULL, NULL, 'no', 'no', '2019-07-12 15:14:19', '2021-04-08 03:25:53'),
(3, 1, 3, 'NI', NULL, 'National Institute', 'undergraduate', NULL, 'NI', 'National Institute', NULL, 'yes', 'yes', '2019-07-12 15:14:33', '2021-04-08 03:25:56'),
(4, 1, NULL, 'NFU', NULL, 'NFU 3+2 Program', 'post-graduate', 1, NULL, NULL, 'get master degree in taiwan NFU', 'yes', 'no', '2019-07-12 15:14:33', '2023-02-28 03:04:42'),
(5, 2, NULL, 'MBA', 'b4b57aa6-90b6-11eb-9a90-52540001273f', 'SRH Master of Business Administration', 'post-graduate', NULL, NULL, 'SRH MBA', NULL, 'no', 'yes', '2021-09-18 14:16:28', '2021-09-21 07:31:57'),
(6, 1, 1, 'DD', NULL, 'Double Degree', 'undergraduate', 1, NULL, NULL, NULL, 'no', 'no', '2022-10-20 10:57:47', '2022-10-20 03:57:47'),
(7, 1, 1, 'EXC', NULL, 'Student Exchange', 'undergraduate', 1, NULL, NULL, NULL, 'no', 'no', '2022-10-20 10:57:47', '2022-10-20 03:59:53'),
(8, 1, 1, 'IJD', NULL, 'Joint Degree', 'undergraduate', 1, NULL, NULL, NULL, 'yes', 'no', '2023-02-28 10:31:45', '2023-02-28 03:31:51'),
(9, 0, 1, 'HOC', NULL, 'Hotel Course (Vocational)', 'non-graduate', NULL, NULL, NULL, NULL, 'yes', 'no', '2024-03-19 12:54:55', '2024-03-19 05:57:48');

-- --------------------------------------------------------

--
-- Table structure for table `ref_program_study_program`
--

CREATE TABLE `ref_program_study_program` (
  `program_study_program_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `program_id` int(11) NOT NULL,
  `study_program_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `degree_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `degree_ijd_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `degree_abbreviation` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `degree_ijd_abbreviation` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_program_study_program`
--

INSERT INTO `ref_program_study_program` (`program_study_program_id`, `program_id`, `study_program_id`, `degree_name`, `degree_ijd_name`, `degree_abbreviation`, `degree_ijd_abbreviation`, `is_active`, `date_added`, `timestamp`) VALUES
('0532420d-5406-45f5-81b2-dfb2d65e98c0', 1, '7da8cd1e-8f0e-41f4-89dd-361c29801087', 'Sarjana Teknik', 'Bachelor of Science', 'S.T.', 'B.Sc.', 'yes', '2019-07-15 08:45:57', '2021-10-07 07:20:16'),
('06c8ef0b-c6e5-4c9d-9cf1-499360c3122b', 3, '2f5ecc6d-4a67-47f8-80aa-9c3ef8e9b8d8', NULL, NULL, NULL, NULL, 'yes', '2021-01-20 15:26:59', '2021-09-21 07:35:55'),
('0a7fe52c-3dc6-4ec5-88c0-ec745cf0da1f', 1, '01a781d9-81cd-11e9-bdfc-5254005d90f6', 'Sarjana Teknik', 'Bachelor of Science', 'S.T.', 'B.Sc.', 'yes', '2019-07-15 08:47:14', '2021-11-17 08:38:35'),
('0adf0ad0-72b4-41ef-a92a-40520ac9f675', 4, '7ca09ca3-ef1a-4c08-83b7-5d1bb63a633b', NULL, NULL, NULL, NULL, 'yes', '2021-01-20 13:23:32', '2021-09-21 07:35:55'),
('13cda514-917c-49dc-86b1-744106c5adf4', 1, '417bc155-81cd-11e9-bdfc-5254005d90f6', 'Sarjana Teknik', 'Bachelor of Science', 'S.T.', 'B.Sc.', 'yes', '2019-07-15 08:45:57', '2021-11-17 08:38:50'),
('163eddf4-1c99-4747-bd2a-d300c7578b48', 4, '6ce5bc8b-10f5-456d-855d-aef18dc641f4', NULL, NULL, NULL, NULL, 'yes', '2021-01-20 13:23:32', '2021-10-12 07:35:58'),
('1b8b681c-d92d-4a04-a172-77f397859fe2', 4, '46675bdb-83af-47e7-bef6-07566108fd21', NULL, NULL, NULL, NULL, 'no', '2020-09-29 12:46:01', '2021-09-21 07:35:55'),
('1f88762d-644d-4acf-81e9-7c974048013a', 4, '12c9ec75-af4a-46a1-ae12-b1ba4bf75c89', NULL, NULL, NULL, NULL, 'yes', '2020-09-29 12:49:23', '2021-09-21 07:35:55'),
('235dbbdb-2f09-4ed9-8070-98c68933dfc1', 4, 'ed375a1a-81cc-11e9-bdfc-5254005d90f6', NULL, NULL, NULL, NULL, 'yes', '2020-09-29 12:46:01', '2021-09-21 07:35:55'),
('29e5a063-8a64-4a84-9aff-55825a94e739', 1, '7ca09ca3-ef1a-4c08-83b7-5d1bb63a633b', 'Sarjana Teknologi Pangan', 'Bachelor of Science', 'S.T.P.', 'B.Sc.', 'yes', '2019-07-15 08:49:31', '2021-10-07 07:20:18'),
('3a7bf973-6912-45ab-a266-990b0df35fe5', 3, 'e0c165f7-a2f8-4372-aa6b-20e3dbc61f32', NULL, NULL, NULL, NULL, 'yes', '2021-01-20 15:27:34', '2021-09-21 07:35:55'),
('3c4b1b29-84ac-46b3-b942-855642488308', 4, '7da8cd1e-8f0e-41f4-89dd-361c29801087', NULL, NULL, NULL, NULL, 'yes', '2020-09-29 12:42:06', '2021-09-21 07:35:55'),
('439dd537-c5cd-4aaa-b8fc-a42efbc9484c', 4, '226f91bc-81cd-11e9-bdfc-5254005d90f6', NULL, NULL, NULL, NULL, 'yes', '2020-09-29 12:49:23', '2021-09-21 07:35:55'),
('45b5178b-b5a5-4697-8690-7f59172995c8', 4, '417bc155-81cd-11e9-bdfc-5254005d90f6', NULL, NULL, NULL, NULL, 'yes', '2020-09-29 12:46:01', '2021-09-21 07:35:55'),
('509c57f0-0b75-4460-8eaa-1ec3186dee31', 1, '2f5ecc6d-4a67-47f8-80aa-9c3ef8e9b8d8', 'Sarjana Komputer', 'Bachelor of Science', 'S.Kom.', 'B.Sc.', 'yes', '2019-07-15 08:47:14', '2021-11-17 08:39:53'),
('521f0853-5c26-47c7-9c47-dff8f5720340', 4, '208c8d88-2560-4640-a1b2-bfd42b0e7c16', NULL, NULL, NULL, NULL, 'yes', '2020-09-29 12:46:01', '2021-09-21 07:35:55'),
('55823984-4bb1-4378-bb7c-a3a223fdac31', 1, '903eb8ee-159e-406b-8f7e-38d63a961ea4', 'Sarjana Pariwisata', 'Bachelor of Arts', 'S.Par.', 'B.A.', 'yes', '2019-07-15 08:49:31', '2021-11-17 08:40:16'),
('5797a29d-25cd-47a7-bb01-79d189debae2', 3, '12c9ec75-af4a-46a1-ae12-b1ba4bf75c89', NULL, NULL, NULL, NULL, 'yes', '2021-01-20 15:26:30', '2021-09-21 07:35:55'),
('5c8bf3ee-7e4b-4543-9416-1ecce05e390a', 1, '6266e096-63ad-4b77-82b0-17216155a70e', 'Sarjana Sosial', 'Bachelor of Arts', 'S.Sos.', 'B.A.', 'yes', '2019-07-15 08:49:31', '2021-10-07 07:21:11'),
('67c84414-7bb8-4b81-992c-1ac93f2b7cd6', 4, '01a781d9-81cd-11e9-bdfc-5254005d90f6', NULL, NULL, NULL, NULL, 'yes', '2021-01-20 14:00:47', '2021-09-21 07:35:55'),
('6ae1d78d-caad-46d6-90db-1c3b2e8da1f1', 5, '12c9ec75-af4a-46a1-ae12-b1ba4bf75c89', NULL, NULL, NULL, NULL, 'yes', '2021-09-21 14:47:00', '2021-09-21 07:47:00'),
('7104db6b-14b2-48a5-82a4-03d5973cd114', 4, '903eb8ee-159e-406b-8f7e-38d63a961ea4', NULL, NULL, NULL, NULL, 'yes', '2020-09-29 12:49:23', '2021-09-21 07:35:55'),
('7f01a17c-600f-4221-a97b-20cbbd1bd66e', 1, '208c8d88-2560-4640-a1b2-bfd42b0e7c16', 'Sarjana Teknik', 'Bachelor of Science', 'S.T.', 'B.Sc.', 'yes', '2019-07-15 08:49:31', '2021-10-07 07:20:25'),
('7fb87a99-f440-4c35-a47a-b09eb632c33e', 1, '226f91bc-81cd-11e9-bdfc-5254005d90f6', 'Sarjana Manajemen', 'Bachelor of Arts', 'S.M.', 'B.A.', 'yes', '2019-07-15 08:47:14', '2022-05-27 07:02:12'),
('8799fca4-fd41-41ef-b087-960a84afa75c', 3, '46675bdb-83af-47e7-bef6-07566108fd21', NULL, NULL, NULL, NULL, 'yes', '2021-01-20 15:27:34', '2021-09-21 07:35:55'),
('8d9b9bf8-2d0d-4ce2-a67b-e043858e256d', 4, '6266e096-63ad-4b77-82b0-17216155a70e', NULL, NULL, NULL, NULL, 'yes', '2020-09-29 12:49:23', '2021-09-21 07:35:55'),
('99927456-70d7-45f6-aaae-6c8ee49241a6', 1, 'c395b273-5acb-41c6-9d44-bccabd93d312', 'Sarjana Manajemen', 'Bachelor of Arts', 'S.M.', 'B.A.', 'yes', '2019-07-15 08:49:31', '2021-11-17 08:41:14'),
('a493cd1c-ee21-4f4d-bf01-fcacc1ad91e3', 3, '208c8d88-2560-4640-a1b2-bfd42b0e7c16', NULL, NULL, NULL, NULL, 'no', '2021-01-20 15:26:30', '2021-09-21 07:35:55'),
('a767bc79-3039-411e-9e24-8f913163be4e', 1, 'e0c165f7-a2f8-4372-aa6b-20e3dbc61f32', 'Sarjana Teknik', 'Bachelor of Science', 'S.T.', 'B.Sc.', 'no', '2019-07-15 08:47:14', '2021-10-07 07:20:27'),
('b6949467-63cb-11ef-bd34-52540039e1c3', 2, 'c395b273-5acb-41c6-9d44-bccabd93d312', NULL, NULL, NULL, NULL, 'yes', '2024-08-26 23:53:25', '2024-08-26 16:53:25'),
('bd857a30-b96b-4c85-b8d9-b334b1bb5dc8', 4, 'e0c165f7-a2f8-4372-aa6b-20e3dbc61f32', NULL, NULL, NULL, NULL, 'yes', '2020-09-29 12:46:01', '2021-09-21 07:35:55'),
('c03e66c4-9401-4e90-9b5d-5179dab26337', 3, '7da8cd1e-8f0e-41f4-89dd-361c29801087', NULL, NULL, NULL, NULL, 'yes', '2021-01-20 15:27:34', '2021-09-21 07:35:55'),
('c62d5424-86bb-42f2-aaf5-fffcaaed1063', 1, '6ce5bc8b-10f5-456d-855d-aef18dc641f4', 'Sarjana Teknik', 'Bachelor of Science', 'S.T.', 'B.Sc.', 'yes', '2019-07-15 08:47:14', '2021-10-07 07:20:29'),
('ce47a071-ce4b-4d71-9860-379ef9451be4', 1, '12c9ec75-af4a-46a1-ae12-b1ba4bf75c89', 'Sarjana Administrasi Bisnis', 'Bachelor of Arts', 'S.A.B.', 'B.A.', 'yes', '2019-07-15 08:49:31', '2021-10-07 07:21:19'),
('d126218e-0cee-4c54-9477-bcf3b78e6cb4', 1, 'ed375a1a-81cc-11e9-bdfc-5254005d90f6', 'Sarjana Teknik', 'Bachelor of Science', 'S.T.', 'B.Sc.', 'yes', '2019-07-15 08:49:31', '2021-11-17 08:41:28'),
('e2b898df-51be-4dbd-90e6-a3d692682628', 1, '46675bdb-83af-47e7-bef6-07566108fd21', 'Sarjana Teknik', 'Bachelor of Science', 'S.T.', 'B.Sc.', 'no', '2019-07-15 08:49:31', '2021-10-07 07:20:32'),
('f6812322-7b57-44d8-9027-af70a4f32878', 4, '2f5ecc6d-4a67-47f8-80aa-9c3ef8e9b8d8', NULL, NULL, NULL, NULL, 'yes', '2020-09-29 12:46:01', '2021-09-21 07:35:55'),
('f989a82c-0ad5-4a7f-86a1-7a4146568470', 3, '226f91bc-81cd-11e9-bdfc-5254005d90f6', NULL, NULL, NULL, NULL, 'yes', '2021-01-20 15:26:59', '2021-09-21 07:35:55');

-- --------------------------------------------------------

--
-- Table structure for table `ref_questions`
--

CREATE TABLE `ref_questions` (
  `question_id` int(11) NOT NULL,
  `question_content` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `question_has_free_text` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_question_sections`
--

CREATE TABLE `ref_question_sections` (
  `question_section_id` int(11) NOT NULL,
  `question_section_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_religion`
--

CREATE TABLE `ref_religion` (
  `religion_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `religion_feeder_id` int(11) NOT NULL,
  `religion_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_religion`
--

INSERT INTO `ref_religion` (`religion_id`, `religion_feeder_id`, `religion_name`, `date_added`, `timestamp`) VALUES
('223e769b-cc54-48e4-8446-574377083120', 4, 'Hindu', '2019-05-29 10:31:30', '2019-05-29 03:31:30'),
('53b17ff0-e4c0-4fc9-8735-bbb8c7054048', 1, 'Islam', '2019-05-29 10:31:30', '2019-05-29 03:31:30'),
('d5c8f0fd-fdb0-4dfa-8e2f-e863d96e98cd', 3, 'Katholik', '2019-05-29 10:31:30', '2019-05-29 03:31:30'),
('e3e29f7e-400b-49d9-88b2-760f8ce26cb5', 6, 'Konghucu', '2019-05-29 10:31:30', '2019-05-29 03:31:30'),
('e703430a-e6bc-491b-8d75-75024ed80551', 2, 'Kristen', '2019-05-29 10:31:30', '2019-05-29 03:31:30'),
('fc389367-54a8-42a4-99bc-7ffa2d1a3e42', 5, 'Budha', '2019-05-29 10:31:30', '2019-05-29 03:31:30'),
('fc865c74-a84f-41f5-953b-30ced05bfa77', 99, 'Lainnya', '2019-05-29 10:31:30', '2019-05-29 03:31:30');

-- --------------------------------------------------------

--
-- Table structure for table `ref_roles`
--

CREATE TABLE `ref_roles` (
  `roles_id` int(11) NOT NULL,
  `roles_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_roles`
--

INSERT INTO `ref_roles` (`roles_id`, `roles_name`, `date_added`, `timestamp`) VALUES
(1, 'Rector', '2020-02-04 09:11:55', '2020-02-12 02:16:21'),
(2, 'Deans', '2020-02-04 09:11:55', '2020-02-12 02:23:58'),
(3, 'IS&T', '2020-02-10 08:37:07', '2020-02-12 02:24:16'),
(4, 'Head of Study Program', '2020-02-04 09:14:43', '2020-02-12 02:29:36'),
(5, 'Addmission', '2020-02-04 09:14:43', '2020-02-12 02:24:38'),
(6, 'Human Resource', '2020-02-04 09:14:43', '2020-02-12 02:24:43'),
(7, 'Finance', '2020-02-04 09:14:43', '2020-02-12 02:24:48'),
(8, 'Examination Office', '2020-02-10 15:39:09', '2020-02-12 02:24:51'),
(9, 'Academic', '2020-02-04 09:14:43', '2020-02-12 02:24:54'),
(10, 'Lecturer', '2020-02-04 09:14:43', '2020-02-12 02:25:41'),
(11, 'Other', '2020-02-10 08:52:15', '2020-02-12 02:25:46'),
(12, 'Student and Alumni Affair', '2020-12-14 12:59:43', '2020-12-14 05:59:43'),
(13, 'International Office', '2023-03-14 15:48:28', '2023-03-14 08:48:28');

-- --------------------------------------------------------

--
-- Table structure for table `ref_room`
--

CREATE TABLE `ref_room` (
  `id_room` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `room_number` int(11) NOT NULL,
  `room_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `room_size` double NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_scholarship`
--

CREATE TABLE `ref_scholarship` (
  `scholarship_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `scholarship_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `scholarship_description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `scholarship_status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `scholarship_type` enum('scholarship','discount') COLLATE utf8_unicode_ci DEFAULT NULL,
  `scholarship_main_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `specific_user` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `cut_of_tuition_fee` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `scholarship_fee_type` enum('main','additional') COLLATE utf8_unicode_ci DEFAULT 'additional',
  `scholarship_target` enum('student','candidate') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'candidate',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_semester`
--

CREATE TABLE `ref_semester` (
  `semester_id` int(11) NOT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `semester_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `semester_number` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_semester`
--

INSERT INTO `ref_semester` (`semester_id`, `semester_type_id`, `semester_name`, `semester_number`, `date_added`, `timestamp`) VALUES
(1, 1, 'First Semester', '1', '2019-07-09 10:14:07', '2019-07-09 03:14:07'),
(2, 2, 'Second Semester', '2', '2019-07-09 10:14:07', '2019-07-09 03:14:07'),
(3, 1, 'Third Semester', '3', '2019-07-09 10:14:07', '2019-07-09 03:14:07'),
(4, 2, 'Fourth Semester', '4', '2019-07-09 10:14:07', '2019-07-09 03:14:07'),
(5, 1, 'Fifth Semester', '5', '2019-07-09 10:14:07', '2019-07-09 03:14:07'),
(6, 2, 'Sixth Semester', '6', '2019-07-09 10:14:07', '2019-07-09 03:14:07'),
(7, 1, 'Seventh Semester', '7', '2019-07-09 10:14:07', '2019-07-09 03:14:07'),
(8, 2, 'Eighth Semester', '8', '2019-07-09 10:14:07', '2019-07-09 03:14:07'),
(9, 1, 'Ninth Semester', '9', '2019-07-09 10:14:07', '2019-07-09 03:14:07'),
(10, 2, 'Tenth Semester', '10', '2019-07-09 10:14:07', '2019-07-09 03:14:07'),
(11, 3, 'Short Semester 2.5', '2.5', '2019-07-09 10:17:16', '2019-07-09 03:17:16'),
(12, 3, 'Short Semester 3.5', '3.5', '2019-07-09 10:17:16', '2019-07-09 03:17:16'),
(13, 3, 'Short Semester 1.5', '1.5', '2019-07-09 10:17:16', '2019-07-09 03:17:16'),
(14, 3, 'Short Semester 4.5', '4.5', '2019-07-09 10:17:16', '2019-07-09 03:17:16'),
(15, 3, 'Short Semester 5.5', '5.5', '2019-07-09 10:17:16', '2019-07-09 03:17:16'),
(16, 3, 'Short Semester 6.5', '6.5', '2019-07-09 10:17:16', '2019-07-09 03:17:16'),
(17, 4, 'OFSE', 'OFSE', '2019-07-09 10:17:16', '2019-07-09 03:17:16'),
(18, 5, 'Transfer Credit', 'TRCR', '2019-07-09 10:17:16', '2019-07-09 03:17:16'),
(19, 3, 'Short Semester 7.5', '7.5', '2019-07-09 10:17:16', '2019-07-09 03:17:16'),
(20, 4, 'OFSE-R', 'OFSE', '2019-07-09 10:17:16', '2019-07-09 03:17:16'),
(21, 3, 'Short Semester 8.5', '8.5', '2019-07-09 10:17:28', '2019-07-09 03:17:28'),
(22, 3, 'Short Semester 9.5', '9.5', '2020-01-06 15:00:14', '2020-01-06 08:00:14'),
(23, 1, 'Eleventh Semester', '11', '2020-09-05 00:00:00', '2020-09-04 17:00:00'),
(24, 2, 'Twelfth Semester', '12', '2020-09-21 11:30:03', '2020-09-21 04:30:03'),
(25, 1, 'Thirteenth Semester', '13', '2020-09-21 11:30:03', '2020-09-21 04:31:24'),
(26, 2, 'Fourteenth Semester', '14', '2020-09-21 11:30:03', '2020-09-21 04:31:24'),
(27, 3, 'Short Semester 10.5', '10.5', '2021-01-22 13:37:50', '2021-01-22 06:38:10'),
(28, 3, 'Short Semester 11.5', '11.5', '2021-01-22 13:37:50', '2021-01-22 06:38:15'),
(29, 3, 'Short Semester 12.5', '12.5', '2021-01-22 13:42:36', '2021-01-22 06:42:36'),
(30, 3, 'Short Semester 13.5', '13.5', '2021-01-22 13:44:22', '2021-01-22 06:44:22'),
(31, 3, 'Short Semester 14.5', '14.5', '2021-01-22 13:44:22', '2021-01-22 06:44:22');

-- --------------------------------------------------------

--
-- Table structure for table `ref_semester_type`
--

CREATE TABLE `ref_semester_type` (
  `semester_type_id` tinyint(1) NOT NULL,
  `semester_type_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `semester_type_indonesian_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `semester_type_master` tinyint(1) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_semester_type`
--

INSERT INTO `ref_semester_type` (`semester_type_id`, `semester_type_name`, `semester_type_indonesian_name`, `semester_type_master`, `date_added`, `timestamp`) VALUES
(1, 'ODD', 'Ganjil', NULL, '2019-06-25 09:58:30', '2021-09-14 16:40:10'),
(2, 'EVEN', 'Genap', NULL, '2019-06-25 09:58:30', '2021-09-14 16:40:12'),
(3, 'BETWEEN', NULL, NULL, '2019-06-25 09:58:52', '2019-06-25 02:58:52'),
(4, 'ODD-OFSE', NULL, 1, '2019-06-25 09:58:52', '2019-10-16 02:32:52'),
(5, 'TRCR', NULL, NULL, '2019-06-25 09:59:01', '2019-06-25 02:59:01'),
(6, 'EVEN-OFSE', NULL, 2, '2019-06-25 09:58:52', '2019-10-16 02:32:24'),
(7, 'SHORT SEMESTER (ODD)', 'Short Semester Ganjil', 3, '2019-12-17 10:42:59', '2021-09-14 16:40:16'),
(8, 'SHORT SEMESTER (EVEN)', 'Short Semester Genap', 3, '2019-12-17 10:42:59', '2021-09-14 16:40:19');

-- --------------------------------------------------------

--
-- Table structure for table `ref_stream`
--

CREATE TABLE `ref_stream` (
  `stream_id` tinyint(1) NOT NULL,
  `stream_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table degree';

--
-- Dumping data for table `ref_stream`
--

INSERT INTO `ref_stream` (`stream_id`, `stream_name`, `date_added`, `timestamp`) VALUES
(0, 'non-degree', '2024-03-19 12:43:58', '2024-03-19 05:43:58'),
(1, 'bachelor', '2021-03-09 19:51:41', '2021-03-09 12:51:41'),
(2, 'master', '2021-03-09 19:51:41', '2021-03-09 12:51:41'),
(3, 'doctorate', '2021-03-09 19:51:51', '2021-03-09 12:51:51');

-- --------------------------------------------------------

--
-- Table structure for table `ref_study_program`
--

CREATE TABLE `ref_study_program` (
  `study_program_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_substansi` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `faculty_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_main_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `head_of_study_program_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'personal_data_id',
  `head_of_study_program_sk_number` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_sk_accreditation` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_code` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `study_program_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_name_feeder` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_gii_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'gii: German International Institute',
  `study_program_ni_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ni: National Institute',
  `study_program_exp_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'EXP: Kelas Karyawan',
  `study_program_abbreviation` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_ni_abbreviation` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_is_active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `dikti_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ref_study_program`
--

INSERT INTO `ref_study_program` (`study_program_id`, `id_substansi`, `faculty_id`, `study_program_main_id`, `head_of_study_program_id`, `head_of_study_program_sk_number`, `study_program_sk_accreditation`, `study_program_code`, `study_program_name`, `study_program_name_feeder`, `study_program_gii_name`, `study_program_ni_name`, `study_program_exp_name`, `study_program_abbreviation`, `study_program_ni_abbreviation`, `study_program_is_active`, `dikti_code`, `date_added`, `timestamp`) VALUES
('01a781d9-81cd-11e9-bdfc-5254005d90f6', '3660d2f3-a50b-4847-8661-e9fd5a68b092', '51e2f6ff-c394-44c1-8658-7bd9dd46c654', 'e0c165f7-a2f8-4372-aa6b-20e3dbc61f32', '089a7b36-a1cd-4683-adcc-a2e8e8d5606d', 'SK/Rec/0531/IX/2018', '9594/SK/BAN-PT/Ak.KP/S/XI/2022', '02', 'Biomedical Engineering', 'Teknik Elektro', 'Teknik Biomedis', NULL, NULL, 'BME', NULL, 'yes', NULL, '2019-05-29 11:48:30', '2023-08-28 09:36:14'),
('12c9ec75-af4a-46a1-ae12-b1ba4bf75c89', '53598df4-5ca2-4845-9d81-e5a4c3bb1f51', 'f9f52242-983d-4e4c-8d5c-7c4575de0679', NULL, 'ec163051-643e-4d8c-9c10-4addb68e7f95', 'a', NULL, '08', 'International Business Administration', 'Administrasi Bisnis', 'Bisnis Internasional', 'Administrasi Bisnis Nasional', NULL, 'IBA', 'NBA', 'yes', '63211', '2019-05-29 11:45:52', '2024-02-24 03:15:51'),
('208c8d88-2560-4640-a1b2-bfd42b0e7c16', 'e504e971-dfb1-4ad1-afd6-6431e75edf6d', '301a3e19-348d-4398-b640-c9d2acc491fa', NULL, '8bad8835-8294-4a85-be8a-54b03c55a945', NULL, NULL, '04', 'Industrial Engineering', 'Teknik Industri', 'Teknik Industri', NULL, NULL, 'INE', NULL, 'yes', '26201', '2019-05-29 11:46:24', '2024-02-24 03:17:19'),
('226f91bc-81cd-11e9-bdfc-5254005d90f6', 'e19c1b37-683e-462e-9f87-b81a26c75cdc', 'f9f52242-983d-4e4c-8d5c-7c4575de0679', 'c395b273-5acb-41c6-9d44-bccabd93d312', '84e914f3-4ce5-4aa2-8ca4-b8fae986a0df', NULL, NULL, '09', 'Aviation Management', 'Manajemen', 'Manajemen', 'Manajemen (Manajemen Penerbangan)', NULL, 'AVM', 'AMG', 'yes', NULL, '2019-05-29 11:49:25', '2024-02-24 03:17:01'),
('2f5ecc6d-4a67-47f8-80aa-9c3ef8e9b8d8', '2b7e9f57-5551-46e6-a916-e87e9d54bbe1', '301a3e19-348d-4398-b640-c9d2acc491fa', NULL, '8ad35388-0e41-4731-9cc3-fcded4c1ed7b', 'SK/Rec/0656/XII/2016', '2936/SK/BAN-PT/Ak/S/VII/2023', '12', 'Computer Science', 'Teknik Informatika', 'Teknik Informatika', 'Teknik Informatika', NULL, 'CSE', 'CSE', 'yes', '55201', '2019-05-29 11:46:33', '2024-02-19 05:10:09'),
('417bc155-81cd-11e9-bdfc-5254005d90f6', 'f6ebbc5d-39fa-44df-a89e-af25f49b2e63', '301a3e19-348d-4398-b640-c9d2acc491fa', '46675bdb-83af-47e7-bef6-07566108fd21', '5a9e49ce-729a-4be2-98f4-723c6bf2b87a', 'SK/Rec/0472/VIII/2019', '9801/SK/BAN-PT/Ak.KP/S/XI/2022', '03', 'Automotive Engineering', 'Teknik Mesin', 'Teknik Automotif', NULL, NULL, 'AUE', NULL, 'no', NULL, '2019-05-29 11:50:17', '2023-08-28 09:36:55'),
('46675bdb-83af-47e7-bef6-07566108fd21', 'f6ebbc5d-39fa-44df-a89e-af25f49b2e63', '301a3e19-348d-4398-b640-c9d2acc491fa', NULL, '5a9e49ce-729a-4be2-98f4-723c6bf2b87a', 'SK/Rec/0472/VIII/2019', '9801/SK/BAN-PT/Ak.KP/S/XI/2022', '03', 'Mechanical Engineering', 'Teknik Mesin', 'Teknik Mesin', 'Teknik Mesin', NULL, 'MEE', 'MEE', 'no', '21201', '2019-05-29 11:46:37', '2023-08-28 09:36:59'),
('6266e096-63ad-4b77-82b0-17216155a70e', 'd4bbe5b6-17fc-4bc2-b153-14002707fc3d', 'f9f52242-983d-4e4c-8d5c-7c4575de0679', NULL, '3c2d8766-0454-4c7e-a28a-848d5434b181', NULL, '9346/SK/BAN-PT/Ak.KP/S/XI/2022', '07', 'International Relations', 'Ilmu Hubungan Internasional', 'Hubungan Internasional', NULL, NULL, 'INR', NULL, 'yes', '64201', '2019-05-29 11:45:59', '2023-08-28 09:35:31'),
('6ce5bc8b-10f5-456d-855d-aef18dc641f4', '4d0f1cc4-dd65-4323-88f8-e1c9c3a606a9', '51e2f6ff-c394-44c1-8658-7bd9dd46c654', NULL, '988de612-6a98-4c60-a680-5be0350e2f97', 'SK/Rec/0530/IX/2018', '9289/SK/BAN-PT/Ak.KP/S/2022', '06', 'Chemical Engineering', 'Teknik Kimia', 'Teknik Kimia', NULL, NULL, 'CHE', NULL, 'yes', '48201', '2019-05-29 11:47:06', '2024-02-19 05:11:49'),
('7ca09ca3-ef1a-4c08-83b7-5d1bb63a633b', 'b5e23277-90bc-44d2-a956-0eaed2d19ebf', '51e2f6ff-c394-44c1-8658-7bd9dd46c654', NULL, '8966a466-df35-4fcc-a34a-91be78363a73', 'SK/Rec/0529/IX/2018', '9391/SK/BAN-PT/Ak.KP/S/XI/2022', '05', 'Food Technology', 'Teknologi Pangan', 'Teknologi Pangan', NULL, NULL, 'FTE', NULL, 'yes', '41221', '2019-05-29 11:47:01', '2023-08-28 09:37:11'),
('7da8cd1e-8f0e-41f4-89dd-361c29801087', '16e17113-316b-4362-918d-85337bdfc600', '301a3e19-348d-4398-b640-c9d2acc491fa', NULL, '9a16bd46-f179-4f27-b2ef-503f6d649b3f', NULL, '9425/SK/BAN-PT/Ak.KP/S/XI/2022', '01', 'Aviation Engineering', 'Teknik Penerbangan', 'Teknik Penerbangan', 'Teknik Penerbangan', NULL, 'AVE', 'AVE', 'yes', '40201', '2019-05-29 11:46:47', '2024-02-19 05:09:23'),
('903eb8ee-159e-406b-8f7e-38d63a961ea4', '7ca28612-8f25-45fd-bd7e-b4ad3d5c56f1', 'f9f52242-983d-4e4c-8d5c-7c4575de0679', NULL, '6d2c9f65-0f6a-4fc2-8a26-61a204d5b156', NULL, NULL, '10', 'Hotel and Tourism Management', 'Manajemen Perhotelan dan Pariwisata', 'Manajemen Hotel & Pariwisata', NULL, NULL, 'HTM', NULL, 'yes', '93202', '2019-05-29 11:46:07', '2024-02-24 03:16:26'),
('c395b273-5acb-41c6-9d44-bccabd93d312', 'e19c1b37-683e-462e-9f87-b81a26c75cdc', 'f9f52242-983d-4e4c-8d5c-7c4575de0679', NULL, '84e914f3-4ce5-4aa2-8ca4-b8fae986a0df', NULL, '9780/SK/BAN-PT/Ak.KP/S/XI/2022', '09', 'Management', 'Manajemen', 'Manajemen Internasional', NULL, 'International Business Management', 'MGT', NULL, 'yes', '61201', '2019-05-29 11:46:13', '2024-08-26 16:53:01'),
('e0c165f7-a2f8-4372-aa6b-20e3dbc61f32', '3660d2f3-a50b-4847-8661-e9fd5a68b092', '301a3e19-348d-4398-b640-c9d2acc491fa', NULL, 'a1773100-e058-4614-8a4e-d9b65ebfbf1b', 'SK/Rec/0082/II/2019', '9594/SK/BAN-PT/Ak.KP/S/XI/2022', '02', 'Electrical Engineering', 'Teknik Elektro', 'Teknik Elektro', 'Teknik Elektro', NULL, 'ELE', 'ELE', 'no', '20201', '2019-05-29 11:46:54', '2023-08-28 09:36:17'),
('ed375a1a-81cc-11e9-bdfc-5254005d90f6', '3660d2f3-a50b-4847-8661-e9fd5a68b092', '301a3e19-348d-4398-b640-c9d2acc491fa', 'e0c165f7-a2f8-4372-aa6b-20e3dbc61f32', 'a1773100-e058-4614-8a4e-d9b65ebfbf1b', 'SK/Rec/0082/II/2019', '9594/SK/BAN-PT/Ak.KP/S/XI/2022', '02', 'Mechatronics Engineering', 'Teknik Elektro', 'Teknik Mekatronika', NULL, NULL, 'MTE', NULL, 'yes', NULL, '2019-05-29 11:47:56', '2023-08-28 09:36:19');

-- --------------------------------------------------------

--
-- Table structure for table `ref_study_program_majoring`
--

CREATE TABLE `ref_study_program_majoring` (
  `study_program_majoring_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `study_program_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `majoring_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `majoring_name_indo` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_subject`
--

CREATE TABLE `ref_subject` (
  `subject_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `subject_name_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `subject_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `study_program_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `program_id` int(11) NOT NULL,
  `id_jenis_mata_kuliah` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_kelompok_mata_kuliah` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject_credit` int(11) NOT NULL DEFAULT 0,
  `subject_credit_tm` int(11) NOT NULL DEFAULT 0 COMMENT 'tatap muka',
  `subject_credit_p` int(11) NOT NULL DEFAULT 0 COMMENT 'praktikum',
  `subject_credit_pl` int(11) NOT NULL DEFAULT 0 COMMENT 'praktek lapangan',
  `subject_credit_s` int(11) NOT NULL DEFAULT 0 COMMENT 'simulasi',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_subject_name`
--

CREATE TABLE `ref_subject_name` (
  `subject_name_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `subject_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject_name_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `portal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_advisor`
--

CREATE TABLE `thesis_advisor` (
  `advisor_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `personal_data_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `institution_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `advisor_status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `advisor_main` enum('TRUE','FALSE') COLLATE utf8_unicode_ci DEFAULT NULL,
  `advisor_role` enum('advisor','examiner') COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_defense`
--

CREATE TABLE `thesis_defense` (
  `thesis_defense_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_students_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `semester_type_id` tinyint(1) DEFAULT NULL,
  `thesis_defense_date` date NOT NULL,
  `thesis_defense_time_start` time NOT NULL,
  `thesis_defense_time_end` time NOT NULL,
  `thesis_defense_room` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_defense_zoom_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_defense_zoom_passcode` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `score_evaluation_average` decimal(5,2) DEFAULT NULL,
  `score_presentation_average` decimal(5,2) DEFAULT NULL,
  `score_final` decimal(5,2) DEFAULT NULL,
  `score_grade` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'F',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_defenses_absence`
--

CREATE TABLE `thesis_defenses_absence` (
  `thesis_absence_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_defense_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_advisor_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_examiner_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attendance` enum('ABSENT','PRESENT','EXCUSE','SICK') COLLATE utf8_unicode_ci NOT NULL,
  `attendance_remarks` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `absence_by` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'personal_data_id',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_defense_absence`
--

CREATE TABLE `thesis_defense_absence` (
  `thesis_absence_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_defense_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_students_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_advisor_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_examiner_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attendance` enum('ABSENT','PRESENT','EXCUSE','SICK') COLLATE utf8_unicode_ci NOT NULL,
  `attendance_remarks` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `absence_by` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'personal_data_id',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_period`
--

CREATE TABLE `thesis_period` (
  `period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `semester_type_id` tinyint(1) NOT NULL,
  `period_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_score`
--

CREATE TABLE `thesis_score` (
  `thesis_score_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_defense_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_advisor_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_examiner_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `students_advisor_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `students_examiner_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `score_evaluation` decimal(5,2) DEFAULT NULL,
  `score_presentation` decimal(5,2) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_score_evaluation`
--

CREATE TABLE `thesis_score_evaluation` (
  `score_evaluation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_score_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_evaluation_format` decimal(5,2) DEFAULT NULL,
  `score_working_process` decimal(5,2) DEFAULT NULL,
  `score_subject` decimal(5,2) DEFAULT NULL,
  `score_user` decimal(5,2) DEFAULT NULL,
  `score_academic` decimal(5,2) DEFAULT NULL,
  `score_total` decimal(5,2) DEFAULT NULL,
  `score_status` enum('open','closed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_score_evaluation_content`
--

CREATE TABLE `thesis_score_evaluation_content` (
  `score_evaluation_content_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_evaluation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `problem_statement` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `research_question` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `analytical_framework` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `methods` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `result` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `discussion` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `conclusion` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `literature` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `iuli_infrastructure` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_score_evaluation_format`
--

CREATE TABLE `thesis_score_evaluation_format` (
  `score_evaluation_format_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_evaluation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `text_style` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `summary` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `chapter_structur` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `citations` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `table_figure` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `layout` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `reference` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_score_evaluation_potential_user`
--

CREATE TABLE `thesis_score_evaluation_potential_user` (
  `score_evaluation_potential_user_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_evaluation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `applicable_for_user` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `benefit_for_user` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `will_employ_student` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_score_evaluation_subject`
--

CREATE TABLE `thesis_score_evaluation_subject` (
  `score_evaluation_subject_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_evaluation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `identification_objective` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `understanding_specific_topic` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `method_project_plan` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_dificulty` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `similar_thesis` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_score_evaluation_working_process`
--

CREATE TABLE `thesis_score_evaluation_working_process` (
  `score_evaluation_working_process_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `score_evaluation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `identification_problem` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `independence` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `progress` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_score_presentation`
--

CREATE TABLE `thesis_score_presentation` (
  `score_presentation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_score_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `presentation_remarks` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `argumentation_remarks` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `presentation_score` decimal(5,2) DEFAULT NULL,
  `argumentation_score` decimal(5,2) DEFAULT NULL,
  `score_total` decimal(5,2) DEFAULT NULL,
  `score_status` enum('open','closed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_student`
--

CREATE TABLE `thesis_student` (
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `current_progress` enum('proposal','work','final','finish') COLLATE utf8_unicode_ci DEFAULT NULL,
  `current_status` enum('pending','approved_hsp','approved','revision','rejected') COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_students`
--

CREATE TABLE `thesis_students` (
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `current_progress` enum('proposal','work','final','finish') COLLATE utf8_unicode_ci NOT NULL,
  `current_status` enum('pending','approved_hsp','approved','revision','rejected') COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_students_advisor`
--

CREATE TABLE `thesis_students_advisor` (
  `student_advisor_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `advisor_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `advisor_type` enum('proposed_advisor_1','proposed_advisor_2','proposed_advisor_3','proposed_advisor_4','approved_advisor_1','approved_advisor_2','approved_advisor_3','approved_advisor_4','approved_advisor_5') COLLATE utf8_unicode_ci NOT NULL,
  `advisor_section` enum('iuli_advisor','iuli_co_advisor','partner_advisor','partner_co_advisor') COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_students_examiner`
--

CREATE TABLE `thesis_students_examiner` (
  `student_examiner_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `advisor_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `examiner_type` enum('examiner_1','examiner_2','examiner_3','examiner_4','examiner_5','examiner_6','examiner_7') COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_students_file`
--

CREATE TABLE `thesis_students_file` (
  `thesis_file_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_log_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_filetype` enum('thesis_proposal_file','thesis_work_file','thesis_work_plagiate_check','thesis_work_log','thesis_work_other_doc','thesis_final_file','thesis_final_journal_publication','thesis_final_other_doc') COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_filename` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_students_log_status`
--

CREATE TABLE `thesis_students_log_status` (
  `thesis_log_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `academic_year_id` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `semester_type_id` tinyint(1) DEFAULT NULL,
  `thesis_log_type` enum('proposal','work','revision','final') COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_status` enum('pending','approved_hsp','approved','revision','rejected') COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_approved_hsp` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `thesis_approved_deans` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_student_advisor`
--

CREATE TABLE `thesis_student_advisor` (
  `student_advisor_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `advisor_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `advisor_type` enum('proposed_advisor_1','proposed_advisor_2','proposed_advisor_3','proposed_advisor_4','approved_advisor_1','approved_advisor_2','approved_advisor_3','approved_advisor_4') COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_student_examiner`
--

CREATE TABLE `thesis_student_examiner` (
  `student_examiner_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `advisor_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `examiner_type` enum('examiner_1','examiner_2','examiner_3','examiner_4','examiner_5','examiner_6','examiner_7') COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_student_log_notes`
--

CREATE TABLE `thesis_student_log_notes` (
  `thesis_log_note_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_log_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_logs_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` longtext COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thesis_student_log_status`
--

CREATE TABLE `thesis_student_log_status` (
  `thesis_log_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesis_student_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_work_fname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_plagiate_check_fname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_log_fname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_other_doc_fname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_proposal_fname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_revision_fname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_final_fname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_final_other_fname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_log_type` enum('proposal','work','revision','final') COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_status` enum('pending','approved_hsp','approved','revision','rejected') COLLATE utf8_unicode_ci DEFAULT NULL,
  `thesis_approved_hsp` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `thesis_approved_deans` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vote_member`
--

CREATE TABLE `vote_member` (
  `member_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vote_paslon`
--

CREATE TABLE `vote_paslon` (
  `paslon_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `paslon_chairman` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'student_id',
  `paslon_vice_chairman` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'student_id',
  `period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `paslon_chairman_pict` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paslon_vice_chairman_pict` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nomor_urut` int(11) NOT NULL,
  `vision` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `mision` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `paslon_result` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vote_period`
--

CREATE TABLE `vote_period` (
  `period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `study_program_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `period_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `period_status` enum('active','inactive','pending') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `period_voting_start` datetime NOT NULL,
  `period_voting_end` datetime NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vote_voting`
--

CREATE TABLE `vote_voting` (
  `voting_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `period_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `has_pick` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bni_billing`
--
ALTER TABLE `bni_billing`
  ADD PRIMARY KEY (`trx_id`);

--
-- Indexes for table `bni_transactions`
--
ALTER TABLE `bni_transactions`
  ADD PRIMARY KEY (`bni_transactions_id`),
  ADD KEY `trx_id` (`trx_id`);

--
-- Indexes for table `bni_transactions_payment`
--
ALTER TABLE `bni_transactions_payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `bni_transactions_id` (`bni_transactions_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `sub_invoice_details_id` (`sub_invoice_details_id`);

--
-- Indexes for table `dikti_jenis_aktivitas`
--
ALTER TABLE `dikti_jenis_aktivitas`
  ADD PRIMARY KEY (`id_jenis_aktivitas_mahasiswa`);

--
-- Indexes for table `dikti_jenis_mata_kuliah`
--
ALTER TABLE `dikti_jenis_mata_kuliah`
  ADD PRIMARY KEY (`id_jenis_mata_kuliah`);

--
-- Indexes for table `dikti_kategori_kegiatan`
--
ALTER TABLE `dikti_kategori_kegiatan`
  ADD PRIMARY KEY (`id_kategori_kegiatan`);

--
-- Indexes for table `dikti_kelompok_mata_kuliah`
--
ALTER TABLE `dikti_kelompok_mata_kuliah`
  ADD PRIMARY KEY (`id_kelompok_mata_kuliah`);

--
-- Indexes for table `dikti_message`
--
ALTER TABLE `dikti_message`
  ADD PRIMARY KEY (`error_code`);

--
-- Indexes for table `dikti_questions`
--
ALTER TABLE `dikti_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `parent_question_id` (`parent_question_id`);

--
-- Indexes for table `dikti_question_answers`
--
ALTER TABLE `dikti_question_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `question_section_id` (`question_section_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `dikti_question_choices`
--
ALTER TABLE `dikti_question_choices`
  ADD PRIMARY KEY (`question_choice_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `dikti_wilayah`
--
ALTER TABLE `dikti_wilayah`
  ADD PRIMARY KEY (`id_wilayah`);

--
-- Indexes for table `dt_absence_student`
--
ALTER TABLE `dt_absence_student`
  ADD PRIMARY KEY (`absence_student_id`),
  ADD KEY `subject_delivered_id` (`subject_delivered_id`),
  ADD KEY `dt_absence_student_ibfk_1` (`score_id`),
  ADD KEY `signed_personal_data_id` (`signed_personal_data_id`);

--
-- Indexes for table `dt_academic_history`
--
ALTER TABLE `dt_academic_history`
  ADD PRIMARY KEY (`academic_history_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `institution_id` (`institution_id`),
  ADD KEY `occupation_id` (`occupation_id`);

--
-- Indexes for table `dt_academic_year`
--
ALTER TABLE `dt_academic_year`
  ADD PRIMARY KEY (`academic_year_id`);

--
-- Indexes for table `dt_activity_lecturer`
--
ALTER TABLE `dt_activity_lecturer`
  ADD PRIMARY KEY (`activity_lecturer_id`),
  ADD KEY `activity_study_id` (`activity_study_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `id_kategori_kegiatan` (`id_kategori_kegiatan`),
  ADD KEY `feeder_sync` (`feeder_sync`);

--
-- Indexes for table `dt_activity_student`
--
ALTER TABLE `dt_activity_student`
  ADD PRIMARY KEY (`activity_student_id`),
  ADD KEY `id_activity_study` (`activity_study_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `dt_activity_student_ibfk_3` (`feeder_sync`);

--
-- Indexes for table `dt_activity_study`
--
ALTER TABLE `dt_activity_study`
  ADD PRIMARY KEY (`activity_study_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `id_jenis_aktivitas_mahasiswa` (`id_jenis_aktivitas_mahasiswa`),
  ADD KEY `semester_type_id` (`semester_type_id`),
  ADD KEY `study_program_id` (`study_program_id`),
  ADD KEY `dt_activity_study_ibfk_5` (`feeder_sync`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `dt_address`
--
ALTER TABLE `dt_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `dikti_wilayah_id` (`dikti_wilayah_id`);

--
-- Indexes for table `dt_api`
--
ALTER TABLE `dt_api`
  ADD PRIMARY KEY (`api_access_token`);

--
-- Indexes for table `dt_candidate_answer`
--
ALTER TABLE `dt_candidate_answer`
  ADD KEY `exam_candidate_id` (`exam_candidate_id`),
  ADD KEY `exam_question_id` (`exam_question_id`),
  ADD KEY `question_option_id` (`question_option_id`);

--
-- Indexes for table `dt_class_group`
--
ALTER TABLE `dt_class_group`
  ADD PRIMARY KEY (`class_group_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`),
  ADD KEY `sign_personal_data_id` (`sign_personal_data_id`);

--
-- Indexes for table `dt_class_group_lecturer`
--
ALTER TABLE `dt_class_group_lecturer`
  ADD PRIMARY KEY (`class_group_lecturer_id`),
  ADD KEY `class_group_id` (`class_group_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `employee_id_reported` (`employee_id_reported`);

--
-- Indexes for table `dt_class_group_subject`
--
ALTER TABLE `dt_class_group_subject`
  ADD PRIMARY KEY (`class_group_subject_id`),
  ADD KEY `class_group_id` (`class_group_id`),
  ADD KEY `offered_subject_id` (`offered_subject_id`);

--
-- Indexes for table `dt_class_master`
--
ALTER TABLE `dt_class_master`
  ADD PRIMARY KEY (`class_master_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`),
  ADD KEY `sign_personal_data_id` (`sign_personal_data_id`);

--
-- Indexes for table `dt_class_master_class`
--
ALTER TABLE `dt_class_master_class`
  ADD KEY `class_group_id` (`class_group_id`),
  ADD KEY `class_master_id` (`class_master_id`);

--
-- Indexes for table `dt_class_master_lecturer`
--
ALTER TABLE `dt_class_master_lecturer`
  ADD PRIMARY KEY (`class_master_lecturer_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `employee_id_reported` (`employee_id_reported`),
  ADD KEY `class_master_id` (`class_master_id`) USING BTREE;

--
-- Indexes for table `dt_class_subject_delivered`
--
ALTER TABLE `dt_class_subject_delivered`
  ADD PRIMARY KEY (`subject_delivered_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `dt_class_master_subject_delivered_ibfk_1` (`class_master_id`);

--
-- Indexes for table `dt_employee`
--
ALTER TABLE `dt_employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `dt_employee_department`
--
ALTER TABLE `dt_employee_department`
  ADD PRIMARY KEY (`employee_department_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `dt_employee_pages`
--
ALTER TABLE `dt_employee_pages`
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `roles_pages_id` (`roles_pages_id`);

--
-- Indexes for table `dt_event`
--
ALTER TABLE `dt_event`
  ADD PRIMARY KEY (`event_id`),
  ADD UNIQUE KEY `event_slug` (`event_slug`);

--
-- Indexes for table `dt_event_bookings`
--
ALTER TABLE `dt_event_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `dt_event_field`
--
ALTER TABLE `dt_event_field`
  ADD PRIMARY KEY (`event_field_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `field_id` (`field_id`);

--
-- Indexes for table `dt_exam_candidate`
--
ALTER TABLE `dt_exam_candidate`
  ADD PRIMARY KEY (`exam_candidate_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `dt_exam_candidate_section`
--
ALTER TABLE `dt_exam_candidate_section`
  ADD KEY `exam_candidate_id` (`exam_candidate_id`),
  ADD KEY `dt_exam_candidate_section_ibfk_2` (`exam_section_id`);

--
-- Indexes for table `dt_exam_period`
--
ALTER TABLE `dt_exam_period`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `dt_exam_question`
--
ALTER TABLE `dt_exam_question`
  ADD PRIMARY KEY (`exam_question_id`),
  ADD KEY `exam_section_id` (`exam_section_id`);

--
-- Indexes for table `dt_exam_question_option`
--
ALTER TABLE `dt_exam_question_option`
  ADD PRIMARY KEY (`question_option_id`);

--
-- Indexes for table `dt_exam_section`
--
ALTER TABLE `dt_exam_section`
  ADD PRIMARY KEY (`exam_section_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `dt_family`
--
ALTER TABLE `dt_family`
  ADD PRIMARY KEY (`family_id`);

--
-- Indexes for table `dt_family_member`
--
ALTER TABLE `dt_family_member`
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `family_id` (`family_id`);

--
-- Indexes for table `dt_fee`
--
ALTER TABLE `dt_fee`
  ADD PRIMARY KEY (`fee_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `study_program_id` (`study_program_id`),
  ADD KEY `payment_type_code` (`payment_type_code`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `scholarship_id` (`scholarship_id`);

--
-- Indexes for table `dt_institution_contact`
--
ALTER TABLE `dt_institution_contact`
  ADD KEY `institution_id` (`institution_id`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `dt_invoice`
--
ALTER TABLE `dt_invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`);

--
-- Indexes for table `dt_invoice_details`
--
ALTER TABLE `dt_invoice_details`
  ADD KEY `fee_id` (`fee_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `dt_iuli_info`
--
ALTER TABLE `dt_iuli_info`
  ADD PRIMARY KEY (`info_id`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `dt_job_vacancy`
--
ALTER TABLE `dt_job_vacancy`
  ADD PRIMARY KEY (`job_vacancy_id`),
  ADD KEY `occupation_id` (`occupation_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `institution_id` (`institution_id`);

--
-- Indexes for table `dt_letter_number`
--
ALTER TABLE `dt_letter_number`
  ADD PRIMARY KEY (`letter_number_id`),
  ADD UNIQUE KEY `letter_number_result` (`letter_number_result`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `letter_type_id` (`letter_type_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`);

--
-- Indexes for table `dt_letter_number_target`
--
ALTER TABLE `dt_letter_number_target`
  ADD KEY `letter_number_id` (`letter_number_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `dt_offered_subject`
--
ALTER TABLE `dt_offered_subject`
  ADD PRIMARY KEY (`offered_subject_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `curriculum_subject_id` (`curriculum_subject_id`),
  ADD KEY `semester_type_id` (`semester_type_id`),
  ADD KEY `study_program_id` (`study_program_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `ofse_period_id` (`ofse_period_id`);

--
-- Indexes for table `dt_ofse`
--
ALTER TABLE `dt_ofse`
  ADD PRIMARY KEY (`ofse_period_id`);

--
-- Indexes for table `dt_ofse_evaluation`
--
ALTER TABLE `dt_ofse_evaluation`
  ADD PRIMARY KEY (`score_evaluation_id`),
  ADD KEY `student_examiner_id` (`student_examiner_id`),
  ADD KEY `subject_question_id` (`subject_question_id`);

--
-- Indexes for table `dt_ofse_exam`
--
ALTER TABLE `dt_ofse_exam`
  ADD PRIMARY KEY (`ofse_exam_id`),
  ADD KEY `score_id` (`score_id`);

--
-- Indexes for table `dt_ofse_examiner`
--
ALTER TABLE `dt_ofse_examiner`
  ADD PRIMARY KEY (`student_examiner_id`),
  ADD KEY `score_id` (`score_id`),
  ADD KEY `dt_ofse_examiner_ibfk_2` (`advisor_id`);

--
-- Indexes for table `dt_ofse_subject_question`
--
ALTER TABLE `dt_ofse_subject_question`
  ADD PRIMARY KEY (`subject_question_id`),
  ADD KEY `ofse_period_id` (`ofse_period_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `dt_personal_address`
--
ALTER TABLE `dt_personal_address`
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `dt_personal_data`
--
ALTER TABLE `dt_personal_data`
  ADD PRIMARY KEY (`personal_data_id`),
  ADD KEY `citizenship_id` (`citizenship_id`),
  ADD KEY `country_of_birth` (`country_of_birth`),
  ADD KEY `religion_id` (`religion_id`),
  ADD KEY `ocupation_id` (`ocupation_id`),
  ADD KEY `scholarship_id` (`scholarship_id`);

--
-- Indexes for table `dt_personal_data_contact`
--
ALTER TABLE `dt_personal_data_contact`
  ADD PRIMARY KEY (`personal_contact_id`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `dt_personal_data_covid_vaccine`
--
ALTER TABLE `dt_personal_data_covid_vaccine`
  ADD PRIMARY KEY (`vaccine_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `dt_personal_data_document`
--
ALTER TABLE `dt_personal_data_document`
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `dt_personal_data_document_ibfk_1` (`document_id`);

--
-- Indexes for table `dt_personal_data_record`
--
ALTER TABLE `dt_personal_data_record`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `dt_personal_data_record_files`
--
ALTER TABLE `dt_personal_data_record_files`
  ADD PRIMARY KEY (`record_file_id`),
  ADD KEY `record_id` (`record_id`);

--
-- Indexes for table `dt_personal_data_scholarship`
--
ALTER TABLE `dt_personal_data_scholarship`
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `scholarship_id` (`scholarship_id`),
  ADD KEY `personal_data_id_sibling_with` (`personal_data_id_sibling_with`),
  ADD KEY `scholarship_fee_id` (`scholarship_fee_id`);

--
-- Indexes for table `dt_personal_document`
--
ALTER TABLE `dt_personal_document`
  ADD PRIMARY KEY (`personal_document_id`),
  ADD KEY `personal_data_id_generated` (`personal_data_id_generated`),
  ADD KEY `personal_data_id_target` (`personal_data_id_target`),
  ADD KEY `letter_number_id` (`letter_number_id`);

--
-- Indexes for table `dt_questionnaire_answers`
--
ALTER TABLE `dt_questionnaire_answers`
  ADD PRIMARY KEY (`question_answer_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `question_section_id` (`question_section_id`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `dt_question_section_group`
--
ALTER TABLE `dt_question_section_group`
  ADD PRIMARY KEY (`question_section_group_id`),
  ADD KEY `dt_question_section_group_ibfk_1` (`question_id`),
  ADD KEY `dt_question_section_group_ibfk_2` (`question_section_id`);

--
-- Indexes for table `dt_reference`
--
ALTER TABLE `dt_reference`
  ADD KEY `referrer_id` (`referrer_id`),
  ADD KEY `referenced_id` (`referenced_id`);

--
-- Indexes for table `dt_registration_scholarship`
--
ALTER TABLE `dt_registration_scholarship`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `scholarship_id` (`scholarship_id`);

--
-- Indexes for table `dt_roles_pages`
--
ALTER TABLE `dt_roles_pages`
  ADD PRIMARY KEY (`roles_pages_id`),
  ADD KEY `pages_id` (`pages_id`),
  ADD KEY `roles_id` (`roles_id`);

--
-- Indexes for table `dt_score`
--
ALTER TABLE `dt_score`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `curriculum_subject_id` (`curriculum_subject_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `dt_score_ibfk_1` (`class_group_id`),
  ADD KEY `dt_score_ibfk_7` (`semester_type_id`),
  ADD KEY `dt_score_ibfk_6` (`class_master_id`),
  ADD KEY `ofse_period_id` (`ofse_period_id`);

--
-- Indexes for table `dt_semester_settings`
--
ALTER TABLE `dt_semester_settings`
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`);

--
-- Indexes for table `dt_semester_setting_request`
--
ALTER TABLE `dt_semester_setting_request`
  ADD PRIMARY KEY (`request_semester_setting_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`),
  ADD KEY `personal_data_id_request` (`personal_data_id_request`),
  ADD KEY `personal_data_id_approve` (`personal_data_id_approve`);

--
-- Indexes for table `dt_student`
--
ALTER TABLE `dt_student`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `dt_student_ibfk_1` (`study_program_id`),
  ADD KEY `dt_student_ibfk_2` (`personal_data_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `finance_year_id` (`finance_year_id`),
  ADD KEY `graduated_year_id` (`graduated_year_id`),
  ADD KEY `study_program_majoring_id` (`study_program_majoring_id`),
  ADD KEY `student_registration_scholarship_id` (`student_registration_scholarship_id`),
  ADD KEY `study_program_id_alt_1` (`study_program_id_alt_1`),
  ADD KEY `study_program_id_alt_2` (`study_program_id_alt_2`);

--
-- Indexes for table `dt_student_aid`
--
ALTER TABLE `dt_student_aid`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `bank_code` (`bank_code`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `aid_period_id` (`aid_period_id`);

--
-- Indexes for table `dt_student_aid_files`
--
ALTER TABLE `dt_student_aid_files`
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `dt_student_aid_setting`
--
ALTER TABLE `dt_student_aid_setting`
  ADD PRIMARY KEY (`aid_period_id`);

--
-- Indexes for table `dt_student_alumni`
--
ALTER TABLE `dt_student_alumni`
  ADD PRIMARY KEY (`alumni_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `dt_student_document_token`
--
ALTER TABLE `dt_student_document_token`
  ADD UNIQUE KEY `token` (`document_sign`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `dt_student_exchange`
--
ALTER TABLE `dt_student_exchange`
  ADD PRIMARY KEY (`exchange_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `dt_student_exchange_ibfk_2` (`institution_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`);

--
-- Indexes for table `dt_student_exchange_doc`
--
ALTER TABLE `dt_student_exchange_doc`
  ADD PRIMARY KEY (`exchange_file_id`),
  ADD KEY `exchange_id` (`exchange_id`);

--
-- Indexes for table `dt_student_internship`
--
ALTER TABLE `dt_student_internship`
  ADD PRIMARY KEY (`internship_id`),
  ADD KEY `institution_id` (`institution_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `dt_student_internship_doc`
--
ALTER TABLE `dt_student_internship_doc`
  ADD PRIMARY KEY (`internship_file_id`),
  ADD KEY `internship_id` (`internship_id`);

--
-- Indexes for table `dt_student_partner`
--
ALTER TABLE `dt_student_partner`
  ADD PRIMARY KEY (`student_partner_id`),
  ADD KEY `partner_program_id` (`partner_program_id`),
  ADD KEY `partner_period_id` (`partner_period_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `program_study_program_id` (`program_study_program_id`);

--
-- Indexes for table `dt_student_semester`
--
ALTER TABLE `dt_student_semester`
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `student_semester_study_locatiion` (`institution_id`);

--
-- Indexes for table `dt_student_supplement`
--
ALTER TABLE `dt_student_supplement`
  ADD PRIMARY KEY (`supplement_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `semester_type_id` (`semester_type_id`);

--
-- Indexes for table `dt_student_supplement_doc`
--
ALTER TABLE `dt_student_supplement_doc`
  ADD PRIMARY KEY (`supplement_doc_id`),
  ADD KEY `supplement_id` (`supplement_id`);

--
-- Indexes for table `dt_sub_invoice`
--
ALTER TABLE `dt_sub_invoice`
  ADD PRIMARY KEY (`sub_invoice_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `dt_sub_invoice_details`
--
ALTER TABLE `dt_sub_invoice_details`
  ADD PRIMARY KEY (`sub_invoice_details_id`),
  ADD KEY `sub_invoice_id` (`sub_invoice_id`),
  ADD KEY `trx_id` (`trx_id`);

--
-- Indexes for table `dt_testimonial`
--
ALTER TABLE `dt_testimonial`
  ADD PRIMARY KEY (`testimonial_id`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `dt_user_notes`
--
ALTER TABLE `dt_user_notes`
  ADD PRIMARY KEY (`notes_id`),
  ADD KEY `personal_data_target_id` (`personal_data_target_id`),
  ADD KEY `personal_data_reporter_id` (`personal_data_reporter_id`);

--
-- Indexes for table `ref_bank`
--
ALTER TABLE `ref_bank`
  ADD PRIMARY KEY (`bank_code`);

--
-- Indexes for table `ref_confirm_validation`
--
ALTER TABLE `ref_confirm_validation`
  ADD PRIMARY KEY (`confirmation_id`),
  ADD KEY `personal_data_id` (`personal_data_id`);

--
-- Indexes for table `ref_country`
--
ALTER TABLE `ref_country`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `ref_curriculum`
--
ALTER TABLE `ref_curriculum`
  ADD PRIMARY KEY (`curriculum_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `study_program_id` (`study_program_id`),
  ADD KEY `valid_academic_year` (`valid_academic_year`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `ref_curriculum_semester`
--
ALTER TABLE `ref_curriculum_semester`
  ADD KEY `curriculum_id` (`curriculum_id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `ref_curriculum_subject`
--
ALTER TABLE `ref_curriculum_subject`
  ADD PRIMARY KEY (`curriculum_subject_id`),
  ADD KEY `curriculum_id` (`curriculum_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `ref_department`
--
ALTER TABLE `ref_department`
  ADD PRIMARY KEY (`department_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `ref_document`
--
ALTER TABLE `ref_document`
  ADD PRIMARY KEY (`document_id`);

--
-- Indexes for table `ref_document_type`
--
ALTER TABLE `ref_document_type`
  ADD PRIMARY KEY (`document_type_id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `ref_event_field`
--
ALTER TABLE `ref_event_field`
  ADD PRIMARY KEY (`field_id`);

--
-- Indexes for table `ref_faculty`
--
ALTER TABLE `ref_faculty`
  ADD PRIMARY KEY (`faculty_id`),
  ADD KEY `deans_id` (`deans_id`);

--
-- Indexes for table `ref_institution`
--
ALTER TABLE `ref_institution`
  ADD PRIMARY KEY (`institution_id`),
  ADD KEY `address_id` (`address_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `ref_invoice_setting`
--
ALTER TABLE `ref_invoice_setting`
  ADD PRIMARY KEY (`setting_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`);

--
-- Indexes for table `ref_letter_type`
--
ALTER TABLE `ref_letter_type`
  ADD PRIMARY KEY (`letter_type_id`);

--
-- Indexes for table `ref_letter_type_template`
--
ALTER TABLE `ref_letter_type_template`
  ADD PRIMARY KEY (`template_id`),
  ADD KEY `letter_type_id` (`letter_type_id`);

--
-- Indexes for table `ref_master_program`
--
ALTER TABLE `ref_master_program`
  ADD PRIMARY KEY (`master_program_id`),
  ADD KEY `master_program_main_id` (`master_program_main_id`);

--
-- Indexes for table `ref_ocupation`
--
ALTER TABLE `ref_ocupation`
  ADD PRIMARY KEY (`ocupation_id`);

--
-- Indexes for table `ref_pages`
--
ALTER TABLE `ref_pages`
  ADD PRIMARY KEY (`pages_id`);

--
-- Indexes for table `ref_partner`
--
ALTER TABLE `ref_partner`
  ADD PRIMARY KEY (`partner_id`);

--
-- Indexes for table `ref_partner_period`
--
ALTER TABLE `ref_partner_period`
  ADD PRIMARY KEY (`partner_period_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Indexes for table `ref_payment_type`
--
ALTER TABLE `ref_payment_type`
  ADD PRIMARY KEY (`payment_type_code`);

--
-- Indexes for table `ref_program`
--
ALTER TABLE `ref_program`
  ADD PRIMARY KEY (`program_id`),
  ADD UNIQUE KEY `program_code` (`program_code`),
  ADD KEY `program_main_id` (`program_main_id`),
  ADD KEY `stream_id` (`stream_id`),
  ADD KEY `master_program_id` (`master_program_id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Indexes for table `ref_program_study_program`
--
ALTER TABLE `ref_program_study_program`
  ADD PRIMARY KEY (`program_study_program_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `study_program_id` (`study_program_id`);

--
-- Indexes for table `ref_questions`
--
ALTER TABLE `ref_questions`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `ref_question_sections`
--
ALTER TABLE `ref_question_sections`
  ADD PRIMARY KEY (`question_section_id`);

--
-- Indexes for table `ref_religion`
--
ALTER TABLE `ref_religion`
  ADD PRIMARY KEY (`religion_id`);

--
-- Indexes for table `ref_roles`
--
ALTER TABLE `ref_roles`
  ADD PRIMARY KEY (`roles_id`);

--
-- Indexes for table `ref_room`
--
ALTER TABLE `ref_room`
  ADD PRIMARY KEY (`id_room`);

--
-- Indexes for table `ref_scholarship`
--
ALTER TABLE `ref_scholarship`
  ADD PRIMARY KEY (`scholarship_id`),
  ADD KEY `scholarship_main_id` (`scholarship_main_id`);

--
-- Indexes for table `ref_semester`
--
ALTER TABLE `ref_semester`
  ADD PRIMARY KEY (`semester_id`),
  ADD KEY `semester_type_id` (`semester_type_id`);

--
-- Indexes for table `ref_semester_type`
--
ALTER TABLE `ref_semester_type`
  ADD PRIMARY KEY (`semester_type_id`),
  ADD KEY `semester_type_master` (`semester_type_master`);

--
-- Indexes for table `ref_stream`
--
ALTER TABLE `ref_stream`
  ADD PRIMARY KEY (`stream_id`);

--
-- Indexes for table `ref_study_program`
--
ALTER TABLE `ref_study_program`
  ADD PRIMARY KEY (`study_program_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `study_program_main_id` (`study_program_main_id`),
  ADD KEY `head_of_study_program_id` (`head_of_study_program_id`);

--
-- Indexes for table `ref_study_program_majoring`
--
ALTER TABLE `ref_study_program_majoring`
  ADD PRIMARY KEY (`study_program_majoring_id`),
  ADD KEY `study_program_id` (`study_program_id`);

--
-- Indexes for table `ref_subject`
--
ALTER TABLE `ref_subject`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `subject_name_id` (`subject_name_id`),
  ADD KEY `study_program_id` (`study_program_id`),
  ADD KEY `id_jenis_mata_kuliah` (`id_jenis_mata_kuliah`),
  ADD KEY `id_kelompok_mata_kuliah` (`id_kelompok_mata_kuliah`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `ref_subject_name`
--
ALTER TABLE `ref_subject_name`
  ADD PRIMARY KEY (`subject_name_id`),
  ADD UNIQUE KEY `subject_name` (`subject_name`);

--
-- Indexes for table `thesis_advisor`
--
ALTER TABLE `thesis_advisor`
  ADD PRIMARY KEY (`advisor_id`),
  ADD KEY `personal_data_id` (`personal_data_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `institution_id` (`institution_id`);

--
-- Indexes for table `thesis_defense`
--
ALTER TABLE `thesis_defense`
  ADD PRIMARY KEY (`thesis_defense_id`),
  ADD KEY `thesis_student_id` (`thesis_student_id`),
  ADD KEY `thesis_students_id` (`thesis_students_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`);

--
-- Indexes for table `thesis_defenses_absence`
--
ALTER TABLE `thesis_defenses_absence`
  ADD PRIMARY KEY (`thesis_absence_id`),
  ADD KEY `thesis_defense_id` (`thesis_defense_id`),
  ADD KEY `thesis_student_id` (`thesis_student_id`),
  ADD KEY `student_advisor_id` (`student_advisor_id`),
  ADD KEY `student_examiner_id` (`student_examiner_id`);

--
-- Indexes for table `thesis_defense_absence`
--
ALTER TABLE `thesis_defense_absence`
  ADD PRIMARY KEY (`thesis_absence_id`),
  ADD KEY `thesis_defense_id` (`thesis_defense_id`),
  ADD KEY `thesis_student_id` (`thesis_student_id`),
  ADD KEY `student_advisor_id` (`student_advisor_id`),
  ADD KEY `student_examiner_id` (`student_examiner_id`),
  ADD KEY `absence_by` (`absence_by`);

--
-- Indexes for table `thesis_period`
--
ALTER TABLE `thesis_period`
  ADD PRIMARY KEY (`period_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`);

--
-- Indexes for table `thesis_score`
--
ALTER TABLE `thesis_score`
  ADD PRIMARY KEY (`thesis_score_id`),
  ADD KEY `thesis_defense_id` (`thesis_defense_id`),
  ADD KEY `thesis_score_ibfk_2` (`student_advisor_id`),
  ADD KEY `thesis_score_ibfk_3` (`student_examiner_id`),
  ADD KEY `students_advisor_id` (`students_advisor_id`),
  ADD KEY `students_examiner_id` (`students_examiner_id`);

--
-- Indexes for table `thesis_score_evaluation`
--
ALTER TABLE `thesis_score_evaluation`
  ADD PRIMARY KEY (`score_evaluation_id`),
  ADD KEY `thesis_score_id` (`thesis_score_id`);

--
-- Indexes for table `thesis_score_evaluation_content`
--
ALTER TABLE `thesis_score_evaluation_content`
  ADD PRIMARY KEY (`score_evaluation_content_id`),
  ADD KEY `score_evaluation_id` (`score_evaluation_id`);

--
-- Indexes for table `thesis_score_evaluation_format`
--
ALTER TABLE `thesis_score_evaluation_format`
  ADD PRIMARY KEY (`score_evaluation_format_id`),
  ADD KEY `score_evaluation_id` (`score_evaluation_id`);

--
-- Indexes for table `thesis_score_evaluation_potential_user`
--
ALTER TABLE `thesis_score_evaluation_potential_user`
  ADD PRIMARY KEY (`score_evaluation_potential_user_id`),
  ADD KEY `score_evaluation_id` (`score_evaluation_id`);

--
-- Indexes for table `thesis_score_evaluation_subject`
--
ALTER TABLE `thesis_score_evaluation_subject`
  ADD PRIMARY KEY (`score_evaluation_subject_id`),
  ADD KEY `score_evaluation_id` (`score_evaluation_id`);

--
-- Indexes for table `thesis_score_evaluation_working_process`
--
ALTER TABLE `thesis_score_evaluation_working_process`
  ADD PRIMARY KEY (`score_evaluation_working_process_id`),
  ADD KEY `score_evaluation_id` (`score_evaluation_id`);

--
-- Indexes for table `thesis_score_presentation`
--
ALTER TABLE `thesis_score_presentation`
  ADD PRIMARY KEY (`score_presentation_id`),
  ADD KEY `thesis_score_id` (`thesis_score_id`);

--
-- Indexes for table `thesis_student`
--
ALTER TABLE `thesis_student`
  ADD PRIMARY KEY (`thesis_student_id`),
  ADD KEY `period_id` (`period_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `thesis_students`
--
ALTER TABLE `thesis_students`
  ADD PRIMARY KEY (`thesis_student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `thesis_students_advisor`
--
ALTER TABLE `thesis_students_advisor`
  ADD PRIMARY KEY (`student_advisor_id`),
  ADD KEY `advisor_id` (`advisor_id`),
  ADD KEY `thesis_student_id` (`thesis_student_id`);

--
-- Indexes for table `thesis_students_examiner`
--
ALTER TABLE `thesis_students_examiner`
  ADD PRIMARY KEY (`student_examiner_id`),
  ADD KEY `advisor_id` (`advisor_id`),
  ADD KEY `thesis_student_id` (`thesis_student_id`);

--
-- Indexes for table `thesis_students_file`
--
ALTER TABLE `thesis_students_file`
  ADD PRIMARY KEY (`thesis_file_id`),
  ADD KEY `thesis_log_id` (`thesis_log_id`);

--
-- Indexes for table `thesis_students_log_status`
--
ALTER TABLE `thesis_students_log_status`
  ADD PRIMARY KEY (`thesis_log_id`),
  ADD KEY `thesis_students_log_status_ibfk_1` (`thesis_student_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `semester_type_id` (`semester_type_id`);

--
-- Indexes for table `thesis_student_advisor`
--
ALTER TABLE `thesis_student_advisor`
  ADD PRIMARY KEY (`student_advisor_id`),
  ADD KEY `advisor_id` (`advisor_id`),
  ADD KEY `thesis_student_id` (`thesis_student_id`);

--
-- Indexes for table `thesis_student_examiner`
--
ALTER TABLE `thesis_student_examiner`
  ADD PRIMARY KEY (`student_examiner_id`),
  ADD KEY `thesis_student_id` (`thesis_student_id`),
  ADD KEY `advisor_id` (`advisor_id`);

--
-- Indexes for table `thesis_student_log_notes`
--
ALTER TABLE `thesis_student_log_notes`
  ADD PRIMARY KEY (`thesis_log_note_id`),
  ADD KEY `thesis_log_id` (`thesis_log_id`),
  ADD KEY `thesis_logs_id` (`thesis_logs_id`);

--
-- Indexes for table `thesis_student_log_status`
--
ALTER TABLE `thesis_student_log_status`
  ADD PRIMARY KEY (`thesis_log_id`),
  ADD KEY `thesis_student_log_status_ibfk_1` (`thesis_student_id`);

--
-- Indexes for table `vote_member`
--
ALTER TABLE `vote_member`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `period_id` (`period_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `vote_paslon`
--
ALTER TABLE `vote_paslon`
  ADD PRIMARY KEY (`paslon_id`),
  ADD KEY `paslon_chairman` (`paslon_chairman`),
  ADD KEY `paslon_vice_chairman` (`paslon_vice_chairman`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `vote_period`
--
ALTER TABLE `vote_period`
  ADD PRIMARY KEY (`period_id`),
  ADD KEY `study_program_id` (`study_program_id`);

--
-- Indexes for table `vote_voting`
--
ALTER TABLE `vote_voting`
  ADD PRIMARY KEY (`voting_id`),
  ADD KEY `period_id` (`period_id`),
  ADD KEY `vote_voting_ibfk_2` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bni_transactions`
--
ALTER TABLE `bni_transactions`
  MODIFY `bni_transactions_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dt_employee_department`
--
ALTER TABLE `dt_employee_department`
  MODIFY `employee_department_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dt_exam_section`
--
ALTER TABLE `dt_exam_section`
  MODIFY `exam_section_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dt_letter_number`
--
ALTER TABLE `dt_letter_number`
  MODIFY `letter_number_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dt_questionnaire_answers`
--
ALTER TABLE `dt_questionnaire_answers`
  MODIFY `question_answer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dt_question_section_group`
--
ALTER TABLE `dt_question_section_group`
  MODIFY `question_section_group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ref_confirm_validation`
--
ALTER TABLE `ref_confirm_validation`
  MODIFY `confirmation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ref_department`
--
ALTER TABLE `ref_department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ref_event_field`
--
ALTER TABLE `ref_event_field`
  MODIFY `field_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ref_letter_type`
--
ALTER TABLE `ref_letter_type`
  MODIFY `letter_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ref_letter_type_template`
--
ALTER TABLE `ref_letter_type_template`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ref_master_program`
--
ALTER TABLE `ref_master_program`
  MODIFY `master_program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ref_program`
--
ALTER TABLE `ref_program`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ref_questions`
--
ALTER TABLE `ref_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ref_question_sections`
--
ALTER TABLE `ref_question_sections`
  MODIFY `question_section_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ref_roles`
--
ALTER TABLE `ref_roles`
  MODIFY `roles_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ref_semester`
--
ALTER TABLE `ref_semester`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `ref_semester_type`
--
ALTER TABLE `ref_semester_type`
  MODIFY `semester_type_id` tinyint(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bni_transactions_payment`
--
ALTER TABLE `bni_transactions_payment`
  ADD CONSTRAINT `bni_transactions_payment_ibfk_1` FOREIGN KEY (`bni_transactions_id`) REFERENCES `bni_transactions` (`bni_transactions_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bni_transactions_payment_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bni_transactions_payment_ibfk_3` FOREIGN KEY (`sub_invoice_details_id`) REFERENCES `dt_sub_invoice_details` (`sub_invoice_details_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dikti_questions`
--
ALTER TABLE `dikti_questions`
  ADD CONSTRAINT `dikti_questions_ibfk_1` FOREIGN KEY (`parent_question_id`) REFERENCES `dikti_questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dikti_question_answers`
--
ALTER TABLE `dikti_question_answers`
  ADD CONSTRAINT `dikti_question_answers_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `dikti_question_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `dikti_questions` (`question_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `dikti_question_answers_ibfk_3` FOREIGN KEY (`question_section_id`) REFERENCES `dikti_question_choices` (`question_choice_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `dikti_question_answers_ibfk_4` FOREIGN KEY (`student_id`) REFERENCES `dt_student` (`student_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dikti_question_choices`
--
ALTER TABLE `dikti_question_choices`
  ADD CONSTRAINT `dikti_question_choices_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `dikti_questions` (`question_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `dt_absence_student`
--
ALTER TABLE `dt_absence_student`
  ADD CONSTRAINT `dt_absence_student_ibfk_1` FOREIGN KEY (`score_id`) REFERENCES `dt_score` (`score_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_absence_student_ibfk_2` FOREIGN KEY (`subject_delivered_id`) REFERENCES `dt_class_subject_delivered` (`subject_delivered_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_absence_student_ibfk_3` FOREIGN KEY (`signed_personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_academic_history`
--
ALTER TABLE `dt_academic_history`
  ADD CONSTRAINT `dt_academic_history_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_academic_history_ibfk_2` FOREIGN KEY (`institution_id`) REFERENCES `ref_institution` (`institution_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_academic_history_ibfk_3` FOREIGN KEY (`occupation_id`) REFERENCES `ref_ocupation` (`ocupation_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_activity_lecturer`
--
ALTER TABLE `dt_activity_lecturer`
  ADD CONSTRAINT `dt_activity_lecturer_ibfk_1` FOREIGN KEY (`activity_study_id`) REFERENCES `dt_activity_study` (`activity_study_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_activity_lecturer_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `dt_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_activity_lecturer_ibfk_3` FOREIGN KEY (`id_kategori_kegiatan`) REFERENCES `dikti_kategori_kegiatan` (`id_kategori_kegiatan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_activity_lecturer_ibfk_4` FOREIGN KEY (`feeder_sync`) REFERENCES `dikti_message` (`error_code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_activity_student`
--
ALTER TABLE `dt_activity_student`
  ADD CONSTRAINT `dt_activity_student_ibfk_1` FOREIGN KEY (`activity_study_id`) REFERENCES `dt_activity_study` (`activity_study_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_activity_student_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `dt_student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_activity_student_ibfk_3` FOREIGN KEY (`feeder_sync`) REFERENCES `dikti_message` (`error_code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_activity_study`
--
ALTER TABLE `dt_activity_study`
  ADD CONSTRAINT `dt_activity_study_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_activity_study_ibfk_2` FOREIGN KEY (`id_jenis_aktivitas_mahasiswa`) REFERENCES `dikti_jenis_aktivitas` (`id_jenis_aktivitas_mahasiswa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_activity_study_ibfk_3` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_activity_study_ibfk_4` FOREIGN KEY (`study_program_id`) REFERENCES `ref_study_program` (`study_program_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_activity_study_ibfk_5` FOREIGN KEY (`feeder_sync`) REFERENCES `dikti_message` (`error_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_activity_study_ibfk_6` FOREIGN KEY (`program_id`) REFERENCES `ref_program` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_address`
--
ALTER TABLE `dt_address`
  ADD CONSTRAINT `dt_address_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `ref_country` (`country_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_address_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `ref_country` (`country_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_address_ibfk_3` FOREIGN KEY (`dikti_wilayah_id`) REFERENCES `dikti_wilayah` (`id_wilayah`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_candidate_answer`
--
ALTER TABLE `dt_candidate_answer`
  ADD CONSTRAINT `dt_candidate_answer_ibfk_1` FOREIGN KEY (`exam_candidate_id`) REFERENCES `dt_exam_candidate` (`exam_candidate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_candidate_answer_ibfk_2` FOREIGN KEY (`exam_question_id`) REFERENCES `dt_exam_question` (`exam_question_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_candidate_answer_ibfk_3` FOREIGN KEY (`question_option_id`) REFERENCES `dt_exam_question_option` (`question_option_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_class_group`
--
ALTER TABLE `dt_class_group`
  ADD CONSTRAINT `dt_class_group_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_group_ibfk_2` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_group_ibfk_3` FOREIGN KEY (`sign_personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_class_group_lecturer`
--
ALTER TABLE `dt_class_group_lecturer`
  ADD CONSTRAINT `dt_class_group_lecturer_ibfk_1` FOREIGN KEY (`class_group_id`) REFERENCES `dt_class_group` (`class_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_group_lecturer_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `dt_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_group_lecturer_ibfk_3` FOREIGN KEY (`employee_id_reported`) REFERENCES `dt_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_class_group_subject`
--
ALTER TABLE `dt_class_group_subject`
  ADD CONSTRAINT `dt_class_group_subject_ibfk_1` FOREIGN KEY (`class_group_id`) REFERENCES `dt_class_group` (`class_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_group_subject_ibfk_2` FOREIGN KEY (`offered_subject_id`) REFERENCES `dt_offered_subject` (`offered_subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_class_master`
--
ALTER TABLE `dt_class_master`
  ADD CONSTRAINT `dt_class_master_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_master_ibfk_2` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_master_ibfk_3` FOREIGN KEY (`sign_personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_class_master_class`
--
ALTER TABLE `dt_class_master_class`
  ADD CONSTRAINT `dt_class_master_class_ibfk_1` FOREIGN KEY (`class_group_id`) REFERENCES `dt_class_group` (`class_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_master_class_ibfk_2` FOREIGN KEY (`class_master_id`) REFERENCES `dt_class_master` (`class_master_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_class_master_lecturer`
--
ALTER TABLE `dt_class_master_lecturer`
  ADD CONSTRAINT `dt_class_master_lecturer_ibfk_1` FOREIGN KEY (`class_master_id`) REFERENCES `dt_class_master` (`class_master_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_master_lecturer_ibfk_2` FOREIGN KEY (`employee_id_reported`) REFERENCES `dt_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_master_lecturer_ibfk_3` FOREIGN KEY (`employee_id`) REFERENCES `dt_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_class_subject_delivered`
--
ALTER TABLE `dt_class_subject_delivered`
  ADD CONSTRAINT `dt_class_subject_delivered_ibfk_1` FOREIGN KEY (`class_master_id`) REFERENCES `dt_class_master` (`class_master_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_class_subject_delivered_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `dt_employee` (`employee_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_employee`
--
ALTER TABLE `dt_employee`
  ADD CONSTRAINT `dt_employee_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_employee_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `ref_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_employee_department`
--
ALTER TABLE `dt_employee_department`
  ADD CONSTRAINT `dt_employee_department_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `ref_department` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_employee_department_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `dt_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_employee_pages`
--
ALTER TABLE `dt_employee_pages`
  ADD CONSTRAINT `dt_employee_pages_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `dt_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_employee_pages_ibfk_2` FOREIGN KEY (`roles_pages_id`) REFERENCES `dt_roles_pages` (`roles_pages_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_event_bookings`
--
ALTER TABLE `dt_event_bookings`
  ADD CONSTRAINT `dt_event_bookings_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `dt_event` (`event_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_event_field`
--
ALTER TABLE `dt_event_field`
  ADD CONSTRAINT `dt_event_field_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `dt_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_event_field_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `ref_event_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_exam_candidate`
--
ALTER TABLE `dt_exam_candidate`
  ADD CONSTRAINT `dt_exam_candidate_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `dt_exam_period` (`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_exam_candidate_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `dt_student` (`student_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_exam_candidate_ibfk_3` FOREIGN KEY (`booking_id`) REFERENCES `dt_event_bookings` (`booking_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_exam_candidate_section`
--
ALTER TABLE `dt_exam_candidate_section`
  ADD CONSTRAINT `dt_exam_candidate_section_ibfk_1` FOREIGN KEY (`exam_candidate_id`) REFERENCES `dt_exam_candidate` (`exam_candidate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_exam_candidate_section_ibfk_2` FOREIGN KEY (`exam_section_id`) REFERENCES `dt_exam_section` (`exam_section_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_exam_question`
--
ALTER TABLE `dt_exam_question`
  ADD CONSTRAINT `dt_exam_question_ibfk_1` FOREIGN KEY (`exam_section_id`) REFERENCES `dt_exam_section` (`exam_section_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_exam_section`
--
ALTER TABLE `dt_exam_section`
  ADD CONSTRAINT `dt_exam_section_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `dt_exam_period` (`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_family_member`
--
ALTER TABLE `dt_family_member`
  ADD CONSTRAINT `dt_family_member_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_family_member_ibfk_2` FOREIGN KEY (`family_id`) REFERENCES `dt_family` (`family_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_fee`
--
ALTER TABLE `dt_fee`
  ADD CONSTRAINT `dt_fee_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_fee_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `ref_semester` (`semester_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_fee_ibfk_3` FOREIGN KEY (`study_program_id`) REFERENCES `ref_study_program` (`study_program_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_fee_ibfk_4` FOREIGN KEY (`payment_type_code`) REFERENCES `ref_payment_type` (`payment_type_code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_fee_ibfk_5` FOREIGN KEY (`program_id`) REFERENCES `ref_program` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_fee_ibfk_6` FOREIGN KEY (`scholarship_id`) REFERENCES `ref_scholarship` (`scholarship_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_institution_contact`
--
ALTER TABLE `dt_institution_contact`
  ADD CONSTRAINT `dt_institution_contact_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `ref_institution` (`institution_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_institution_contact_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_invoice`
--
ALTER TABLE `dt_invoice`
  ADD CONSTRAINT `dt_invoice_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_invoice_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_invoice_ibfk_4` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_invoice_details`
--
ALTER TABLE `dt_invoice_details`
  ADD CONSTRAINT `dt_invoice_details_ibfk_1` FOREIGN KEY (`fee_id`) REFERENCES `dt_fee` (`fee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_invoice_details_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `dt_invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_iuli_info`
--
ALTER TABLE `dt_iuli_info`
  ADD CONSTRAINT `dt_iuli_info_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_job_vacancy`
--
ALTER TABLE `dt_job_vacancy`
  ADD CONSTRAINT `dt_job_vacancy_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `ref_institution` (`institution_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_job_vacancy_ibfk_2` FOREIGN KEY (`occupation_id`) REFERENCES `ref_ocupation` (`ocupation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_job_vacancy_ibfk_3` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_letter_number`
--
ALTER TABLE `dt_letter_number`
  ADD CONSTRAINT `dt_letter_number_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_letter_number_ibfk_2` FOREIGN KEY (`letter_type_id`) REFERENCES `ref_letter_type` (`letter_type_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_letter_number_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `ref_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_letter_number_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_letter_number_ibfk_5` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_letter_number_target`
--
ALTER TABLE `dt_letter_number_target`
  ADD CONSTRAINT `dt_letter_number_target_ibfk_1` FOREIGN KEY (`letter_number_id`) REFERENCES `dt_letter_number` (`letter_number_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_letter_number_target_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_letter_number_target_ibfk_3` FOREIGN KEY (`template_id`) REFERENCES `ref_letter_type_template` (`template_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_offered_subject`
--
ALTER TABLE `dt_offered_subject`
  ADD CONSTRAINT `dt_offered_subject_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_offered_subject_ibfk_2` FOREIGN KEY (`curriculum_subject_id`) REFERENCES `ref_curriculum_subject` (`curriculum_subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_offered_subject_ibfk_3` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_offered_subject_ibfk_4` FOREIGN KEY (`study_program_id`) REFERENCES `ref_study_program` (`study_program_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_offered_subject_ibfk_5` FOREIGN KEY (`program_id`) REFERENCES `ref_program` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_offered_subject_ibfk_6` FOREIGN KEY (`ofse_period_id`) REFERENCES `dt_ofse` (`ofse_period_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_ofse_evaluation`
--
ALTER TABLE `dt_ofse_evaluation`
  ADD CONSTRAINT `dt_ofse_evaluation_ibfk_1` FOREIGN KEY (`student_examiner_id`) REFERENCES `dt_ofse_examiner` (`student_examiner_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_ofse_evaluation_ibfk_2` FOREIGN KEY (`subject_question_id`) REFERENCES `dt_ofse_subject_question` (`subject_question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_ofse_exam`
--
ALTER TABLE `dt_ofse_exam`
  ADD CONSTRAINT `dt_ofse_exam_ibfk_1` FOREIGN KEY (`score_id`) REFERENCES `dt_score` (`score_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_ofse_examiner`
--
ALTER TABLE `dt_ofse_examiner`
  ADD CONSTRAINT `dt_ofse_examiner_ibfk_1` FOREIGN KEY (`score_id`) REFERENCES `dt_score` (`score_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_ofse_examiner_ibfk_2` FOREIGN KEY (`advisor_id`) REFERENCES `thesis_advisor` (`advisor_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_ofse_subject_question`
--
ALTER TABLE `dt_ofse_subject_question`
  ADD CONSTRAINT `dt_ofse_subject_question_ibfk_2` FOREIGN KEY (`ofse_period_id`) REFERENCES `dt_ofse` (`ofse_period_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_ofse_subject_question_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `ref_subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_personal_address`
--
ALTER TABLE `dt_personal_address`
  ADD CONSTRAINT `dt_personal_address_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_address_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `dt_address` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_personal_data`
--
ALTER TABLE `dt_personal_data`
  ADD CONSTRAINT `dt_personal_data_ibfk_1` FOREIGN KEY (`citizenship_id`) REFERENCES `ref_country` (`country_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_data_ibfk_2` FOREIGN KEY (`country_of_birth`) REFERENCES `ref_country` (`country_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_data_ibfk_3` FOREIGN KEY (`religion_id`) REFERENCES `ref_religion` (`religion_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_data_ibfk_4` FOREIGN KEY (`ocupation_id`) REFERENCES `ref_ocupation` (`ocupation_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_data_ibfk_5` FOREIGN KEY (`scholarship_id`) REFERENCES `ref_scholarship` (`scholarship_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_personal_data_contact`
--
ALTER TABLE `dt_personal_data_contact`
  ADD CONSTRAINT `dt_personal_data_contact_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_personal_data_covid_vaccine`
--
ALTER TABLE `dt_personal_data_covid_vaccine`
  ADD CONSTRAINT `dt_personal_data_covid_vaccine_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_data_covid_vaccine_ibfk_2` FOREIGN KEY (`document_id`) REFERENCES `ref_document` (`document_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_personal_data_document`
--
ALTER TABLE `dt_personal_data_document`
  ADD CONSTRAINT `dt_personal_data_document_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `ref_document` (`document_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_data_document_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_personal_data_record`
--
ALTER TABLE `dt_personal_data_record`
  ADD CONSTRAINT `dt_personal_data_record_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `dt_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_data_record_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_personal_data_record_files`
--
ALTER TABLE `dt_personal_data_record_files`
  ADD CONSTRAINT `dt_personal_data_record_files_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `dt_personal_data_record` (`record_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_personal_data_scholarship`
--
ALTER TABLE `dt_personal_data_scholarship`
  ADD CONSTRAINT `dt_personal_data_scholarship_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_data_scholarship_ibfk_2` FOREIGN KEY (`scholarship_id`) REFERENCES `ref_scholarship` (`scholarship_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_data_scholarship_ibfk_3` FOREIGN KEY (`personal_data_id_sibling_with`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_data_scholarship_ibfk_4` FOREIGN KEY (`scholarship_fee_id`) REFERENCES `dt_fee` (`fee_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_personal_document`
--
ALTER TABLE `dt_personal_document`
  ADD CONSTRAINT `dt_personal_document_ibfk_1` FOREIGN KEY (`personal_data_id_generated`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_personal_document_ibfk_2` FOREIGN KEY (`personal_data_id_target`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `dt_personal_document_ibfk_3` FOREIGN KEY (`letter_number_id`) REFERENCES `dt_letter_number` (`letter_number_id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `dt_questionnaire_answers`
--
ALTER TABLE `dt_questionnaire_answers`
  ADD CONSTRAINT `dt_questionnaire_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `ref_questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_questionnaire_answers_ibfk_2` FOREIGN KEY (`question_section_id`) REFERENCES `ref_question_sections` (`question_section_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_questionnaire_answers_ibfk_3` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_question_section_group`
--
ALTER TABLE `dt_question_section_group`
  ADD CONSTRAINT `dt_question_section_group_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `ref_questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_question_section_group_ibfk_2` FOREIGN KEY (`question_section_id`) REFERENCES `ref_question_sections` (`question_section_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_reference`
--
ALTER TABLE `dt_reference`
  ADD CONSTRAINT `dt_reference_ibfk_1` FOREIGN KEY (`referrer_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_reference_ibfk_2` FOREIGN KEY (`referenced_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_registration_scholarship`
--
ALTER TABLE `dt_registration_scholarship`
  ADD CONSTRAINT `dt_registration_scholarship_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_registration_scholarship_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_registration_scholarship_ibfk_3` FOREIGN KEY (`scholarship_id`) REFERENCES `ref_scholarship` (`scholarship_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_roles_pages`
--
ALTER TABLE `dt_roles_pages`
  ADD CONSTRAINT `dt_roles_pages_ibfk_1` FOREIGN KEY (`pages_id`) REFERENCES `ref_pages` (`pages_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_roles_pages_ibfk_2` FOREIGN KEY (`roles_id`) REFERENCES `ref_roles` (`roles_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_score`
--
ALTER TABLE `dt_score`
  ADD CONSTRAINT `dt_score_ibfk_1` FOREIGN KEY (`class_group_id`) REFERENCES `dt_class_group` (`class_group_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_score_ibfk_2` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_score_ibfk_3` FOREIGN KEY (`curriculum_subject_id`) REFERENCES `ref_curriculum_subject` (`curriculum_subject_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_score_ibfk_4` FOREIGN KEY (`semester_id`) REFERENCES `ref_semester` (`semester_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_score_ibfk_5` FOREIGN KEY (`student_id`) REFERENCES `dt_student` (`student_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_score_ibfk_6` FOREIGN KEY (`class_master_id`) REFERENCES `dt_class_master` (`class_master_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_score_ibfk_7` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_score_ibfk_8` FOREIGN KEY (`ofse_period_id`) REFERENCES `dt_ofse` (`ofse_period_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_semester_settings`
--
ALTER TABLE `dt_semester_settings`
  ADD CONSTRAINT `dt_semester_settings_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_semester_settings_ibfk_2` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_semester_setting_request`
--
ALTER TABLE `dt_semester_setting_request`
  ADD CONSTRAINT `dt_semester_setting_request_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_semester_setting_request_ibfk_2` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_semester_setting_request_ibfk_3` FOREIGN KEY (`personal_data_id_request`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_semester_setting_request_ibfk_4` FOREIGN KEY (`personal_data_id_approve`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_student`
--
ALTER TABLE `dt_student`
  ADD CONSTRAINT `dt_student_ibfk_1` FOREIGN KEY (`study_program_id`) REFERENCES `ref_study_program` (`study_program_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_ibfk_10` FOREIGN KEY (`study_program_id_alt_2`) REFERENCES `ref_study_program` (`study_program_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_ibfk_4` FOREIGN KEY (`program_id`) REFERENCES `ref_program` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_ibfk_5` FOREIGN KEY (`finance_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_ibfk_6` FOREIGN KEY (`graduated_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_ibfk_7` FOREIGN KEY (`study_program_majoring_id`) REFERENCES `ref_study_program_majoring` (`study_program_majoring_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_ibfk_8` FOREIGN KEY (`student_registration_scholarship_id`) REFERENCES `ref_scholarship` (`scholarship_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_ibfk_9` FOREIGN KEY (`study_program_id_alt_1`) REFERENCES `ref_study_program` (`study_program_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_aid`
--
ALTER TABLE `dt_student_aid`
  ADD CONSTRAINT `dt_student_aid_ibfk_1` FOREIGN KEY (`bank_code`) REFERENCES `ref_bank` (`bank_code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_aid_ibfk_2` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_aid_ibfk_3` FOREIGN KEY (`aid_period_id`) REFERENCES `dt_student_aid_setting` (`aid_period_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_aid_files`
--
ALTER TABLE `dt_student_aid_files`
  ADD CONSTRAINT `dt_student_aid_files_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `dt_student_aid` (`request_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_alumni`
--
ALTER TABLE `dt_student_alumni`
  ADD CONSTRAINT `dt_student_alumni_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `dt_student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_document_token`
--
ALTER TABLE `dt_student_document_token`
  ADD CONSTRAINT `dt_student_document_token_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `dt_student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_exchange`
--
ALTER TABLE `dt_student_exchange`
  ADD CONSTRAINT `dt_student_exchange_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `dt_student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_exchange_ibfk_2` FOREIGN KEY (`institution_id`) REFERENCES `ref_institution` (`institution_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_exchange_ibfk_3` FOREIGN KEY (`program_id`) REFERENCES `ref_program` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_exchange_ibfk_4` FOREIGN KEY (`semester_id`) REFERENCES `ref_semester` (`semester_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_exchange_ibfk_5` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_exchange_ibfk_6` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_exchange_doc`
--
ALTER TABLE `dt_student_exchange_doc`
  ADD CONSTRAINT `dt_student_exchange_doc_ibfk_1` FOREIGN KEY (`exchange_id`) REFERENCES `dt_student_exchange` (`exchange_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_internship`
--
ALTER TABLE `dt_student_internship`
  ADD CONSTRAINT `dt_student_internship_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `ref_institution` (`institution_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_internship_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `dt_student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_internship_doc`
--
ALTER TABLE `dt_student_internship_doc`
  ADD CONSTRAINT `dt_student_internship_doc_ibfk_1` FOREIGN KEY (`internship_id`) REFERENCES `dt_student_internship` (`internship_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_partner`
--
ALTER TABLE `dt_student_partner`
  ADD CONSTRAINT `dt_student_partner_ibfk_1` FOREIGN KEY (`partner_program_id`) REFERENCES `ref_program` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_partner_ibfk_2` FOREIGN KEY (`partner_period_id`) REFERENCES `ref_partner_period` (`partner_period_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_partner_ibfk_3` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_partner_ibfk_4` FOREIGN KEY (`program_study_program_id`) REFERENCES `ref_program_study_program` (`program_study_program_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_semester`
--
ALTER TABLE `dt_student_semester`
  ADD CONSTRAINT `dt_student_semester_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_semester_ibfk_2` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_semester_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `dt_student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_semester_ibfk_4` FOREIGN KEY (`semester_id`) REFERENCES `ref_semester` (`semester_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_semester_ibfk_5` FOREIGN KEY (`institution_id`) REFERENCES `ref_institution` (`institution_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_supplement`
--
ALTER TABLE `dt_student_supplement`
  ADD CONSTRAINT `dt_student_supplement_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_supplement_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `dt_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_supplement_ibfk_3` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_student_supplement_ibfk_4` FOREIGN KEY (`student_id`) REFERENCES `dt_student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_student_supplement_doc`
--
ALTER TABLE `dt_student_supplement_doc`
  ADD CONSTRAINT `dt_student_supplement_doc_ibfk_1` FOREIGN KEY (`supplement_id`) REFERENCES `dt_student_supplement` (`supplement_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_sub_invoice`
--
ALTER TABLE `dt_sub_invoice`
  ADD CONSTRAINT `dt_sub_invoice_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `dt_invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_sub_invoice_details`
--
ALTER TABLE `dt_sub_invoice_details`
  ADD CONSTRAINT `dt_sub_invoice_details_ibfk_1` FOREIGN KEY (`sub_invoice_id`) REFERENCES `dt_sub_invoice` (`sub_invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_sub_invoice_details_ibfk_2` FOREIGN KEY (`trx_id`) REFERENCES `bni_billing` (`trx_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_testimonial`
--
ALTER TABLE `dt_testimonial`
  ADD CONSTRAINT `dt_testimonial_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dt_user_notes`
--
ALTER TABLE `dt_user_notes`
  ADD CONSTRAINT `dt_user_notes_ibfk_1` FOREIGN KEY (`personal_data_target_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_user_notes_ibfk_2` FOREIGN KEY (`personal_data_reporter_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_confirm_validation`
--
ALTER TABLE `ref_confirm_validation`
  ADD CONSTRAINT `ref_confirm_validation_ibfk_1` FOREIGN KEY (`personal_data_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_curriculum`
--
ALTER TABLE `ref_curriculum`
  ADD CONSTRAINT `ref_curriculum_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ref_curriculum_ibfk_2` FOREIGN KEY (`study_program_id`) REFERENCES `ref_study_program` (`study_program_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ref_curriculum_ibfk_3` FOREIGN KEY (`valid_academic_year`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ref_curriculum_ibfk_4` FOREIGN KEY (`program_id`) REFERENCES `ref_program` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_curriculum_semester`
--
ALTER TABLE `ref_curriculum_semester`
  ADD CONSTRAINT `ref_curriculum_semester_ibfk_1` FOREIGN KEY (`curriculum_id`) REFERENCES `ref_curriculum` (`curriculum_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ref_curriculum_semester_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `ref_semester` (`semester_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_curriculum_subject`
--
ALTER TABLE `ref_curriculum_subject`
  ADD CONSTRAINT `ref_curriculum_subject_ibfk_1` FOREIGN KEY (`curriculum_id`) REFERENCES `ref_curriculum` (`curriculum_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ref_curriculum_subject_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `ref_semester` (`semester_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ref_curriculum_subject_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `ref_subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_department`
--
ALTER TABLE `ref_department`
  ADD CONSTRAINT `ref_department_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `dt_employee` (`employee_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ref_document_type`
--
ALTER TABLE `ref_document_type`
  ADD CONSTRAINT `ref_document_type_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `ref_document` (`document_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_faculty`
--
ALTER TABLE `ref_faculty`
  ADD CONSTRAINT `ref_faculty_ibfk_1` FOREIGN KEY (`deans_id`) REFERENCES `dt_personal_data` (`personal_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_institution`
--
ALTER TABLE `ref_institution`
  ADD CONSTRAINT `ref_institution_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `dt_address` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ref_institution_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `ref_country` (`country_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ref_invoice_setting`
--
ALTER TABLE `ref_invoice_setting`
  ADD CONSTRAINT `ref_invoice_setting_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ref_invoice_setting_ibfk_2` FOREIGN KEY (`semester_type_id`) REFERENCES `ref_semester_type` (`semester_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_letter_type_template`
--
ALTER TABLE `ref_letter_type_template`
  ADD CONSTRAINT `ref_letter_type_template_ibfk_1` FOREIGN KEY (`letter_type_id`) REFERENCES `ref_letter_type` (`letter_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_master_program`
--
ALTER TABLE `ref_master_program`
  ADD CONSTRAINT `ref_master_program_ibfk_1` FOREIGN KEY (`master_program_main_id`) REFERENCES `ref_master_program` (`master_program_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ref_partner_period`
--
ALTER TABLE `ref_partner_period`
  ADD CONSTRAINT `ref_partner_period_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `dt_academic_year` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ref_partner_period_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `ref_partner` (`partner_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
