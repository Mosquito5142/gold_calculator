-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Apr 19, 2025 at 09:14 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recipenecklace`
--

-- --------------------------------------------------------

--
-- Table structure for table `gold_type`
--

CREATE TABLE `gold_type` (
  `gold_type_id` int NOT NULL,
  `gold_percentage` decimal(5,2) NOT NULL,
  `gold_density` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `gold_type`
--

INSERT INTO `gold_type` (`gold_type_id`, `gold_percentage`, `gold_density`) VALUES
(1, 96.50, 18.73),
(2, 99.99, 19.30);

-- --------------------------------------------------------

--
-- Table structure for table `necklace_detail`
--

CREATE TABLE `necklace_detail` (
  `necklace_detail_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'ลายสร้อย',
  `type` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'ประเภทลวด',
  `ptt_thick` decimal(10,2) DEFAULT NULL COMMENT 'สร้อยต้นแบบ.หนา',
  `ptt_core` decimal(10,2) DEFAULT NULL COMMENT 'สร้อยต้นแบบ.ไส้',
  `ptt_ratio` decimal(5,2) DEFAULT NULL COMMENT 'สร้อยต้นแบบ.อัตราส่วน',
  `agpt_thick` decimal(10,2) DEFAULT NULL COMMENT 'ยังไม่สกัด.รูลวด',
  `agpt_core` decimal(10,2) DEFAULT NULL COMMENT 'ยังไม่สกัด.นน.ลวดก่อนสกัด',
  `agpt_ratio` decimal(5,2) DEFAULT NULL COMMENT 'ยังไม่สกัด.ค.ยาวลวด',
  `true_length` decimal(10,2) DEFAULT NULL COMMENT 'นน.ทองอย่างเดียว.สร้อยยาว',
  `true_weight` decimal(10,2) DEFAULT NULL COMMENT 'นน.ทองอย่างเดียว.น้ำหนัก',
  `comment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'หมายเหตุ',
  `image` varchar(255) NOT NULL,
  `updated_users_id` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `necklace_detail`
--

INSERT INTO `necklace_detail` (`necklace_detail_id`, `name`, `type`, `ptt_thick`, `ptt_core`, `ptt_ratio`, `agpt_thick`, `agpt_core`, `agpt_ratio`, `true_length`, `true_weight`, `comment`, `image`, `updated_users_id`, `updated_at`) VALUES
(1, 'ลูฟ(กล้ากลม2.5)', 'โปร่ง', 0.35, 5.00, 2.25, 2.90, 3.41, 1.20, 6.80, 35.90, '', '', 1, '2025-04-07 03:01:13'),
(2, 'ลูฟ(กล้ากลม2.2)', 'โปร่ง', 0.35, 5.00, 2.27, 2.80, 12.01, 4.60, 3.80, 20.48, '', '', 1, '2025-04-07 02:02:02'),
(3, 'ลูฟ (กล้าคู่ x1.5)', 'โปร่ง', 0.35, 5.00, 2.25, 2.90, 9.67, 3.50, 10.50, 45.96, NULL, '', 1, '2025-04-09 06:02:14'),
(4, 'ลูฟ (กล้าคู่ x 1.2)', 'โปร่ง', 0.35, 5.00, 2.25, 2.50, 7.81, 3.80, 12.00, 42.16, NULL, '', 1, '2025-04-09 06:02:14'),
(5, 'สรวิศ 3ชั้น (พี่อ๋อย)', 'โปร่ง', 0.30, 5.00, 2.41, 1.50, 7.95, 10.80, 1.10, 12.61, '', '', 1, '2025-04-09 06:02:14'),
(6, 'สรวิศ (พี่อ๋อย)', 'โปร่ง', 0.40, 5.00, 2.05, 1.40, 3.63, 5.10, 1.50, 18.00, NULL, '', 1, '2025-04-09 06:02:14'),
(7, 'คดกริชทรงเครื่อง (พี่สน)', 'โปร่ง', 0.25, 5.00, 3.19, 2.10, 5.10, 3.70, 13.40, 23.06, NULL, '', 1, '2025-04-09 06:02:14'),
(8, 'เกลียวฝรั่งเศส (พี่เก่ง)', 'โปร่ง', 0.20, 5.00, 3.25, 1.00, 2.94, 9.70, 13.00, 11.75, NULL, '', 1, '2025-04-09 06:02:14'),
(9, 'ฟิกาโร่', 'โปร่ง', 0.25, 5.00, 2.72, 1.50, 3.54, 5.00, 3.90, 3.57, NULL, '', 1, '2025-04-09 06:02:14'),
(10, 'เบนซ์', 'โปร่ง', 0.30, 5.00, 2.43, 0.62, 1.23, 10.00, 18.00, 13.26, '', '34f46b1504dba02668252fecbe380b03_67f60f8736acf.webp', 1, '2025-04-09 06:11:19'),
(11, 'บิดถี่', 'โปร่ง', 0.30, 5.00, 2.38, 0.90, 2.66, 10.00, 6.80, 6.40, NULL, '', 1, '2025-04-09 06:02:14'),
(12, 'แปดเสา - พี่อ๋อย', 'โปร่ง', 0.30, 5.00, 2.34, 2.30, 68.60, 40.00, 15.50, 292.48, 'กล้า = 6.2 x รูลวด', '', 1, '2025-04-09 06:02:14'),
(13, 'ฟักแค', 'โปร่ง', 0.35, 5.00, 2.39, 1.10, 3.28, 8.00, 1.10, 4.50, NULL, '', 1, '2025-04-09 06:02:14'),
(14, 'คดกริช', 'โปร่ง', 0.30, 5.00, 2.46, 0.60, 1.75, 9.60, 11.50, 3.22, NULL, '', 1, '2025-04-09 06:02:14'),
(15, 'บิดนูน', 'โปร่ง', 0.30, 5.00, 2.49, 0.90, 4.44, 17.00, 12.40, 8.49, NULL, '', 1, '2025-04-09 06:02:14'),
(16, 'ผ่าหวาย', 'โปร่ง', 0.30, 5.00, 2.44, 1.00, 3.09, 9.10, 4.00, 1.64, NULL, '', 1, '2025-04-09 06:02:14'),
(17, 'ฝรั่งเศส', 'ตัน', NULL, NULL, NULL, 1.30, NULL, 1.00, 8.90, 22.32, NULL, '', 1, '2025-04-09 06:02:14'),
(18, 'ทองคำขาว (สร้อยต้นแบบรู1.30 อิตาลี)', 'ตัน', NULL, NULL, NULL, 1.30, NULL, 27.00, 27.02, 88.33, NULL, '', 1, '2025-04-09 06:02:14'),
(19, 'ทองคำขาว (สร้อยต้นแบบ งานทอรู0.80)', 'โปร่ง', 0.40, 5.00, 2.08, 0.80, 4.39, 20.00, 3.80, 1.61, NULL, '', 1, '2025-04-09 06:02:14'),
(20, 'คดกริชทรงเครื่อง (ทอ)', 'ตัน', NULL, NULL, NULL, 1.00, NULL, 1.00, 1.90, 4.62, NULL, '', 1, '2025-04-09 06:02:14'),
(21, 'เกล็ดดาว (ทำจากลูฟ)', 'ตัน', NULL, NULL, NULL, 1.00, NULL, 1.00, 1.20, 10.01, NULL, '', 1, '2025-04-09 06:02:14'),
(22, 'ลอเรนซ์ (พี่แดน)', 'โปร่ง', 0.30, NULL, 2.42, 2.00, 2.40, 1.85, 5.00, 19.70, 'ความโต ตรง', '', 1, '2025-04-09 06:02:14'),
(23, 'กระดูกมังกร(พี่น้อย)', 'โปร่ง', 0.30, 5.00, 2.50, 1.60, 9.88, 12.00, 1.00, 3.95, NULL, '', 1, '2025-04-09 06:02:14'),
(24, 'วีเทค(งานทอ)ก่อนใส่หวาย', 'โปร่ง', 0.25, 5.00, 2.69, 1.25, 7.65, 15.70, 4.30, 5.74, NULL, '', 1, '2025-04-09 06:02:14'),
(25, 'ซูลคาต้า', 'โปร่ง', 0.27, 5.00, 2.48, 1.60, 2.39, 3.00, 1.60, 6.90, NULL, '', 1, '2025-04-09 06:02:14'),
(26, 'มัดกลางขนแมว(ส.พี่อ๋อย/ลวดอ๋อม)', 'โปร่ง', 0.25, 5.00, 2.85, 1.20, 3.29, 7.50, 4.20, 3.95, NULL, '', 1, '2025-04-09 06:02:14'),
(27, 'กระดูกงู(งานทอ)', 'โปร่ง', 0.30, 5.00, 2.36, 0.80, 0.72, 3.60, 6.10, 3.44, NULL, '', 1, '2025-04-09 06:02:14'),
(28, 'โคโค่', 'โปร่ง', 0.30, 5.00, 2.34, 2.00, 1.10, 0.90, 4.00, 7.00, NULL, '', 1, '2025-04-09 06:02:14'),
(29, 'กระดูกมังกร(พี่ตินัย)', 'โปร่ง', 0.30, 5.00, 2.47, 1.80, 10.82, 10.35, 15.60, 68.37, NULL, '', 1, '2025-04-09 06:02:14'),
(30, 'โอลิมปิก(พี่อ๋อย)', 'โปร่ง', 0.30, 5.00, 2.40, 1.80, 5.00, 5.00, 1.00, 8.88, NULL, '', 1, '2025-04-09 06:02:14'),
(31, 'วินเทจ(พี่อ๋อย)', 'โปร่ง', 0.30, 5.00, 2.36, 1.20, 9.00, 20.00, 1.50, 3.39, NULL, '', 1, '2025-04-09 06:02:14'),
(32, 'นาคี(ต๊อกแต๊ก)', 'โปร่ง', 0.50, 5.00, 1.90, 4.50, 35.01, 5.00, 4.50, 53.62, NULL, '', 1, '2025-04-09 06:02:14'),
(33, 'เกล็ดมัจฉา(พี่อ๋อย)', 'โปร่ง', 0.30, 5.00, 2.45, 1.00, 1.43, 4.20, 0.80, 3.22, NULL, '', 1, '2025-04-09 06:02:14'),
(34, 'รุ่งตะวัน(งานทอ)', 'โปร่ง', 0.45, 5.00, 1.97, 1.30, 13.20, 22.19, 2.00, 11.23, NULL, '', 1, '2025-04-09 06:02:14'),
(35, 'บิดถี่งานทอ(10/1/67)', 'โปร่ง', 0.25, 5.00, 2.47, 1.20, 4.57, 10.00, 3.60, 6.60, NULL, '', 1, '2025-04-09 06:02:14'),
(36, 'กระดูกงูประกอบวิยดา(พี่น้อย) - ไม่ทุบ', 'โปร่ง', 0.30, 5.00, 2.33, 1.70, 5.90, 6.20, 1.00, 2.53, NULL, '', 1, '2025-04-09 06:02:14'),
(37, 'กระดูกงูประกอบวิยดา(พี่น้อย) - ทุบ', 'โปร่ง', 0.30, 5.00, 2.33, 1.70, 5.90, 6.20, 1.00, 2.53, NULL, '', 1, '2025-04-09 06:02:14'),
(38, 'โลร็อง(งานทอ) 24/1/67', 'โปร่ง', 0.25, 5.00, 2.50, 1.30, 4.45, 10.00, 6.10, 12.08, NULL, '', 1, '2025-04-09 06:02:14'),
(39, 'โลร็อง(งานทอ) 28/2/67', 'โปร่ง', 0.25, 5.00, 2.51, 0.78, 1.97, 10.00, 1.00, 0.67, NULL, '', 1, '2025-04-09 06:02:14'),
(40, 'อาลีซัน 28/2/67', 'โปร่ง', 0.25, 5.00, 2.51, 0.78, 1.97, 10.00, 1.00, 1.14, NULL, '', 1, '2025-04-09 06:02:14'),
(41, 'อาลีซัน (สร้อยอ๋อม/ลวดพี่อ๋อย)', 'โปร่ง', 0.30, 5.00, 2.36, 1.20, 9.00, 20.00, 1.00, 3.92, NULL, '', 1, '2025-04-09 06:02:14'),
(42, 'อาลีซัน 5/3/67', 'โปร่ง', 0.25, 5.00, 2.41, 1.30, 5.62, 10.10, 1.80, 8.67, NULL, '', 1, '2025-04-09 06:02:14'),
(43, 'อาลีซัน 5/3/67 (V2)', 'โปร่ง', 0.25, 5.00, 2.41, 1.30, 4.45, 10.00, 1.80, 8.67, NULL, '', 1, '2025-04-09 06:02:14'),
(44, 'อาลีซัน (ล่าสุด: 8/3/67)', 'โปร่ง', 0.30, 5.00, 2.31, 1.20, 4.75, 10.00, 1.80, 6.86, NULL, '', 1, '2025-04-09 06:02:14'),
(45, 'หางกระรอก(พี่อ๋อย)', 'โปร่ง', 0.30, 5.00, 2.40, 1.05, 3.84, 10.70, 0.90, 2.50, NULL, '', 1, '2025-04-09 06:02:14'),
(46, 'บิดซ้อนสี่ชั้น', 'โปร่ง', 0.28, 5.00, 2.35, 0.80, 2.05, 10.00, 2.35, 3.44, NULL, '', 1, '2025-04-09 06:02:14'),
(47, 'กระดูกมังกร(พี่ตินัย) V2', 'โปร่ง', 0.30, 5.00, 2.20, 1.60, 8.87, 10.30, 15.50, 68.00, NULL, '', 1, '2025-04-09 06:02:14'),
(48, 'ลูฟ(งานทอ)', 'โปร่ง', 0.30, 5.00, 2.31, 1.20, 4.75, 10.00, 2.60, 2.06, NULL, '', 1, '2025-04-09 06:02:14'),
(49, 'แปดเซียน(งานทอ)', 'โปร่ง', 0.30, 5.00, 2.22, 0.90, 2.66, 10.00, 4.10, 29.16, NULL, '', 1, '2025-04-09 06:02:14'),
(51, 'ฟ้าหญิง1เส้น(ทุบแล้ว) ไว้ทำแพ', 'โปร่ง', 0.35, 5.00, 2.25, 0.60, 1.20, 10.00, 9.00, 2.22, NULL, '', 1, '2025-04-09 06:02:14'),
(52, 'นาคี(เบียร์) 25/7/67', 'โปร่ง', 0.40, 5.00, 1.92, 4.40, 16.61, 2.50, 13.70, 163.19, NULL, '', 1, '2025-04-09 06:02:14'),
(53, 'บิดซ้อน2ชั้น(พี่อ๋อย)', 'โปร่ง', 0.23, 5.00, 2.57, 2.20, 15.42, 10.10, 2.20, 16.96, NULL, '', 1, '2025-04-09 06:02:14'),
(54, 'เกล็ดมังกร', 'โปร่ง', 0.25, 5.00, 2.49, 0.62, 1.22, 10.00, 3.70, 5.34, NULL, '', 1, '2025-04-09 06:02:14'),
(55, 'แปดเสา(แบบห่าง) - พี่อ๋อย', 'โปร่ง', 0.25, 5.00, 2.46, 0.95, 4.40, 15.10, 1.10, 2.93, NULL, '', 1, '2025-04-09 06:02:14'),
(56, 'รุ่งตะวัน (พี่อ๋อย)', 'โปร่ง', 0.27, 5.00, 2.48, 1.30, 5.50, 10.10, 2.10, 8.52, NULL, '', 1, '2025-04-09 06:02:14'),
(57, 'อาลีซัน(โซ่บิด)', 'โปร่ง', 0.35, 5.00, 2.27, 0.73, 2.61, 14.90, 20.10, 27.38, NULL, '', 1, '2025-04-09 06:02:14'),
(58, 'ดิสโก้ตัน', 'ตัน', NULL, NULL, NULL, 0.50, NULL, 1.00, 4.00, 3.05, NULL, '', 1, '2025-04-09 06:02:14'),
(59, 'บิดซ้อนสามชั้น - (พี่อ๋อย)', 'โปร่ง', 0.25, 5.00, 2.59, 2.00, 13.00, 10.10, 3.20, 28.22, NULL, '', 1, '2025-04-09 06:02:14'),
(60, 'บิดตาม้า(ก้อง)', 'ตัน', NULL, NULL, NULL, 1.60, NULL, 1.00, 3.90, 28.30, NULL, '', 1, '2025-04-09 06:02:14'),
(61, 'บิดตาม้า (ช่างนอก) ยังไม่เสร็จ', 'ตัน', NULL, NULL, NULL, 2.00, NULL, 1.00, 3.00, 30.72, NULL, '', 1, '2025-04-09 06:02:14'),
(66, 'ลูฟ(กล้ากลมx2.0)', 'โปร่ง', 0.30, 5.00, 2.25, 2.80, 26.16, 10.00, 3.50, 17.42, 'AAAA', '', 1, '2025-04-09 06:02:14'),
(68, 'รุ่งตะวัน - อังค์', 'โปร่ง', 0.30, 5.00, 2.45, 1.00, 9.85, 5.30, 3.20, 8.37, 'กล้า = รูลวด คูณ 3.2', '', 1, '2025-04-09 06:02:14'),
(75, 'ทดสอบเบนซ์', 'โปร่ง', 0.30, 5.00, 2.43, 0.62, 1.23, 10.00, 18.00, 13.26, '', 'sg-11134201-22100-ihicqiq8ubivd7_67f626fb50f79.webp', 8, '2025-04-09 07:51:23'),
(76, 'ลูฟ (พี่ไปป์) กล้า 3.0', 'โปร่ง', 0.40, 5.00, 2.20, 1.80, 5.60, 12.00, 5.20, 11.36, 'กล้า มาจากรูลวด x3.00 ', 'search_67f72fa3853b6.webp', 6, '2025-04-10 04:09:20'),
(77, 'วชิรวิทย์ ดวงดี', 'โปร่ง', 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 1.00, '', 'product_39648_776415195_fullsize_6801f9b18fc7d.webp', 1, '2025-04-18 07:05:21');

-- --------------------------------------------------------

--
-- Table structure for table `necklace_detail_parts`
--

CREATE TABLE `necklace_detail_parts` (
  `ndp_id` int NOT NULL,
  `pnd_id` int NOT NULL,
  `wire_hole` decimal(10,2) DEFAULT NULL COMMENT 'สร้อย.รู',
  `wire_thick` decimal(10,2) DEFAULT NULL COMMENT 'สร้อย.หนา',
  `wire_core` decimal(10,2) DEFAULT NULL COMMENT 'สร้อย.ไส้',
  `scale_wire_weight` decimal(10,2) DEFAULT NULL COMMENT 'สร้อย.กว้าง(มม.)',
  `scale_wire_thick` decimal(10,2) DEFAULT NULL COMMENT 'สร้อย.หนา(มม.)',
  `parts_weight` decimal(10,2) DEFAULT NULL COMMENT 'อะไหล่.กว้าง(มม.)',
  `parts_height` decimal(10,2) DEFAULT NULL COMMENT 'อะไหล่.สูง(มม.)',
  `parts_thick` decimal(10,2) DEFAULT NULL COMMENT 'อะไหล่.หนา(มม.)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `necklace_detail_parts`
--

INSERT INTO `necklace_detail_parts` (`ndp_id`, `pnd_id`, `wire_hole`, `wire_thick`, `wire_core`, `scale_wire_weight`, `scale_wire_thick`, `parts_weight`, `parts_height`, `parts_thick`) VALUES
(116, 560, NULL, NULL, NULL, NULL, NULL, 7.19, 16.30, 6.30),
(117, 561, NULL, NULL, NULL, NULL, NULL, 5.66, 13.40, 6.24),
(118, 562, NULL, NULL, NULL, NULL, NULL, 4.69, 13.68, 4.69),
(119, 563, NULL, NULL, NULL, NULL, NULL, 3.50, 3.50, 0.92),
(120, 564, 0.57, 0.43, 4.40, 5.00, 9.94, NULL, NULL, NULL),
(125, 575, NULL, NULL, NULL, NULL, NULL, 14.29, 32.40, 12.53),
(126, 576, NULL, NULL, NULL, NULL, NULL, 11.25, 26.63, 12.40),
(127, 577, NULL, NULL, NULL, NULL, NULL, 9.32, 27.20, 9.32),
(128, 578, NULL, NULL, NULL, NULL, NULL, 6.96, 6.96, 1.83),
(129, 579, 0.57, 0.43, 4.40, 9.94, 9.94, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `necklace_proportions`
--

CREATE TABLE `necklace_proportions` (
  `proportions_id` int NOT NULL,
  `necklace_detail_id` int NOT NULL,
  `shapeshape_necklace` enum('วงกลม','สี่เหลี่ยม','') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `proportions_size` decimal(5,2) DEFAULT NULL COMMENT 'รูลวด',
  `proportions_width` decimal(5,2) DEFAULT NULL COMMENT 'หน้ากว้าง(มม.)',
  `proportions_thick` decimal(5,2) DEFAULT NULL COMMENT 'หนา(มม.)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `necklace_proportions`
--

INSERT INTO `necklace_proportions` (`proportions_id`, `necklace_detail_id`, `shapeshape_necklace`, `proportions_size`, `proportions_width`, `proportions_thick`) VALUES
(2, 10, 'สี่เหลี่ยม', 2.25, 9.70, 10.60),
(3, 11, 'สี่เหลี่ยม', 0.90, 7.60, 1.31),
(4, 22, 'สี่เหลี่ยม', 2.00, 10.50, 3.00),
(5, 24, 'สี่เหลี่ยม', 1.25, 6.65, 5.15),
(6, 29, 'สี่เหลี่ยม', 1.80, 6.95, 6.95),
(7, 35, 'สี่เหลี่ยม', 1.60, 12.55, 3.50),
(8, 47, 'สี่เหลี่ยม', 1.60, 6.10, 6.10),
(9, 48, 'สี่เหลี่ยม', 1.30, 4.70, 2.77),
(11, 51, 'สี่เหลี่ยม', 0.60, 1.56, 1.53),
(12, 54, 'สี่เหลี่ยม', 0.62, 5.60, 5.04),
(13, 49, 'วงกลม', 0.90, 15.20, NULL),
(20, 66, 'สี่เหลี่ยม', 2.80, 11.50, 4.05),
(22, 68, 'สี่เหลี่ยม', 1.00, 5.25, 5.25),
(23, 5, 'สี่เหลี่ยม', 2.20, 18.18, 17.79),
(31, 75, 'สี่เหลี่ยม', 2.25, 9.70, 10.60),
(32, 76, 'สี่เหลี่ยม', 1.80, 3.00, 1.85),
(33, 77, 'สี่เหลี่ยม', 5.00, 5.00, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `necklace_tbs`
--

CREATE TABLE `necklace_tbs` (
  `tbs_id` int NOT NULL,
  `tbs_name` varchar(60) DEFAULT NULL,
  `necklace_detail_id` int NOT NULL,
  `tbs_before` decimal(5,2) DEFAULT '1.00',
  `tbs_after` decimal(5,2) DEFAULT '1.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `necklace_tbs`
--

INSERT INTO `necklace_tbs` (`tbs_id`, `tbs_name`, `necklace_detail_id`, `tbs_before`, `tbs_after`) VALUES
(5, 'A', 3, 10.50, 9.80),
(6, 'B', 3, 9.80, 10.80),
(7, 'A', 4, 12.00, 11.40),
(8, 'B', 4, 11.40, 12.40),
(11, 'A', 6, 1.00, 1.00),
(12, 'B', 6, 1.00, 1.00),
(13, 'A', 7, 1.00, 1.00),
(14, 'B', 7, 1.00, 1.00),
(15, 'A', 8, 1.00, 1.00),
(16, 'B', 8, 1.00, 1.00),
(17, 'A', 9, 1.00, 1.00),
(18, 'B', 9, 1.00, 1.00),
(19, 'A', 10, 1.00, 1.00),
(20, 'B', 10, 1.00, 1.00),
(21, 'A', 11, 1.00, 1.00),
(22, 'B', 11, 1.00, 1.00),
(23, 'A', 12, 1.00, 1.00),
(24, 'B', 12, 1.00, 1.00),
(25, 'A', 13, 1.00, 1.00),
(26, 'B', 13, 1.00, 1.00),
(27, 'A', 14, 1.00, 1.00),
(28, 'B', 14, 1.00, 1.00),
(29, 'A', 15, 1.00, 1.00),
(30, 'B', 15, 1.00, 1.00),
(31, 'A', 16, 1.00, 1.00),
(32, 'B', 16, 1.00, 1.00),
(33, 'A', 17, 1.00, 1.00),
(34, 'B', 17, 1.00, 1.00),
(35, 'A', 18, 1.00, 1.00),
(36, 'B', 18, 1.00, 1.00),
(37, 'A', 19, 1.00, 1.00),
(38, 'B', 19, 1.00, 1.00),
(39, 'A', 20, 1.00, 1.00),
(40, 'B', 20, 1.00, 1.00),
(41, 'A', 21, 1.00, 1.00),
(42, 'B', 21, 1.00, 1.00),
(43, 'A', 22, 1.00, 1.00),
(44, 'B', 22, 1.00, 1.00),
(45, 'A', 23, 5.60, 5.80),
(46, 'B', 23, 1.00, 1.00),
(47, 'A', 24, 1.00, 1.00),
(48, 'B', 24, 1.00, 1.00),
(49, 'A', 25, 1.00, 1.00),
(50, 'B', 25, 1.00, 1.00),
(51, 'A', 26, 1.00, 1.00),
(52, 'B', 26, 1.00, 1.00),
(53, 'A', 27, 1.00, 1.00),
(54, 'B', 27, 1.00, 1.00),
(55, 'A', 28, 1.00, 1.00),
(56, 'B', 28, 1.00, 1.00),
(57, 'A', 29, 15.60, 15.80),
(58, 'B', 29, 15.80, 15.70),
(59, 'C', 29, 15.70, 15.80),
(60, 'A', 30, 1.00, 1.00),
(61, 'B', 30, 1.00, 1.00),
(62, 'A', 31, 1.00, 1.00),
(63, 'B', 31, 1.00, 1.00),
(64, 'A', 32, 4.50, 4.30),
(65, 'B', 32, 1.00, 1.00),
(66, 'A', 33, 1.00, 1.00),
(67, 'B', 33, 1.00, 1.00),
(68, 'A', 34, 1.00, 1.00),
(69, 'B', 34, 1.00, 1.00),
(70, 'A', 35, 1.00, 1.00),
(71, 'B', 35, 1.00, 1.00),
(72, 'A', 36, 1.00, 1.00),
(73, 'B', 36, 1.00, 1.00),
(74, 'A', 37, 5.00, 5.90),
(75, 'B', 37, 1.00, 1.00),
(76, 'A', 38, 1.00, 1.00),
(77, 'B', 38, 1.00, 1.00),
(78, 'A', 39, 1.00, 1.00),
(79, 'B', 39, 1.00, 1.00),
(80, 'A', 40, 1.00, 1.00),
(81, 'B', 40, 1.00, 1.00),
(82, 'A', 41, 1.00, 1.00),
(83, 'B', 41, 1.00, 1.00),
(84, 'A', 42, 1.00, 1.00),
(85, 'B', 42, 1.00, 1.00),
(86, 'A', 43, 1.00, 1.00),
(87, 'B', 43, 1.00, 1.00),
(88, 'A', 44, 1.00, 1.00),
(89, 'B', 44, 1.00, 1.00),
(90, 'A', 45, 1.00, 1.00),
(91, 'B', 45, 1.00, 1.00),
(92, 'A', 46, 1.00, 1.00),
(93, 'B', 46, 1.00, 1.00),
(94, 'A', 47, 1.00, 1.00),
(95, 'B', 47, 1.00, 1.00),
(96, 'A', 48, 1.00, 1.00),
(97, 'B', 48, 1.00, 1.00),
(98, 'A', 49, 1.00, 1.00),
(99, 'B', 49, 1.00, 1.00),
(102, 'A', 51, 1.00, 1.00),
(103, 'B', 51, 1.00, 1.00),
(104, 'A', 52, 1.00, 1.00),
(105, 'B', 52, 1.00, NULL),
(106, 'A', 53, 1.00, 1.00),
(107, 'B', 53, 1.00, 1.00),
(108, 'A', 54, 1.00, 1.00),
(109, 'B', 54, 1.00, 1.00),
(110, 'A', 55, 1.00, 1.00),
(111, 'B', 55, 1.00, 1.00),
(112, 'A', 56, 1.00, 1.00),
(113, 'B', 56, 1.00, 1.00),
(114, 'A', 57, 1.00, 1.00),
(115, 'B', 57, 1.00, 1.00),
(116, 'A', 58, 1.00, 1.00),
(117, 'B', 58, 1.00, 1.00),
(118, 'A', 59, 1.00, 1.00),
(119, 'B', 59, 1.00, 1.00),
(120, 'A', 60, 3.90, 4.10),
(121, 'B', 60, 4.10, 4.10),
(122, 'A', 61, 1.00, 1.00),
(123, 'B', 61, 1.00, 1.00),
(138, 'A', 66, 1.00, 1.00),
(139, 'B', 66, 1.00, 1.00),
(142, 'A', 68, 1.00, 1.00),
(143, 'B', 68, 1.00, 1.00),
(144, 'A', 5, 1.00, 1.00),
(145, 'B', 5, 1.00, 1.00),
(160, 'A', 2, 1.00, 1.00),
(161, 'B', 2, 1.00, 1.00),
(162, 'A', 1, 1.00, 1.00),
(163, 'B', 1, 1.00, 1.00),
(172, 'A', 10, 1.00, 1.00),
(173, 'B', 10, 1.00, 1.00),
(174, 'A', 10, 1.00, 1.00),
(175, 'B', 10, 1.00, 1.00),
(176, 'A', 10, 1.00, 1.00),
(177, 'B', 10, 1.00, 1.00),
(182, 'A', 10, 1.00, 1.00),
(183, 'B', 10, 1.00, 1.00),
(188, 'A', 10, 1.00, 1.00),
(189, 'B', 10, 1.00, 1.00),
(190, 'A', 10, 1.00, 1.00),
(191, 'B', 10, 1.00, 1.00),
(194, 'A', 10, 1.00, 1.00),
(195, 'B', 10, 1.00, 1.00),
(196, 'A', 75, 1.00, 1.00),
(197, 'B', 75, 1.00, 1.00),
(198, 'A', 76, 1.00, 1.00),
(199, 'B', 76, 1.00, 1.00),
(200, 'A', 76, 1.00, 1.00),
(201, 'B', 76, 1.00, 1.00),
(202, 'A', 76, 1.00, 1.00),
(203, 'B', 76, 1.00, 1.00),
(204, 'A', 75, 1.00, 1.00),
(205, 'B', 75, 1.00, 1.00),
(206, 'A', 77, 1.00, 1.00),
(207, 'B', 77, 1.00, 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `percent_necklace`
--

CREATE TABLE `percent_necklace` (
  `pn_id` int NOT NULL,
  `pn_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pn_grams` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `users_id` int DEFAULT NULL,
  `pn_status` enum('master','copy') NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `percent_necklace`
--

INSERT INTO `percent_necklace` (`pn_id`, `pn_name`, `pn_grams`, `image`, `users_id`, `pn_status`, `updated_at`) VALUES
(18, 'ม.คดกริชโป่งห้อยหัวใจดิสโก้ (ทดลอง)', 15.20, '1889689_1__67f7437806c36.jpg', 4, 'master', '2025-04-10 04:05:12'),
(47, 'ค.แปดเซียนคั่นงูBIแฟนซีฝังพลอยลงยา venti 001 5บ', 76.00, '1744881180084_6800cbf99009c.jpg', 5, 'master', '2025-04-17 09:38:01'),
(62, 'ทดสอบก็อป (สำเนาของ ค.แปดเซียนคั่นงูBIแฟนซีฝังพลอยลงยา venti 001 5บ)', 152.00, NULL, 7, 'copy', '2025-04-18 04:35:46');

-- --------------------------------------------------------

--
-- Table structure for table `percent_necklace_detail`
--

CREATE TABLE `percent_necklace_detail` (
  `pnd_id` int NOT NULL,
  `pn_id` int NOT NULL,
  `pnd_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `pnd_name` varchar(255) NOT NULL,
  `pnd_weight_grams` decimal(10,2) NOT NULL,
  `pnd_long_inch` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `percent_necklace_detail`
--

INSERT INTO `percent_necklace_detail` (`pnd_id`, `pn_id`, `pnd_type`, `pnd_name`, `pnd_weight_grams`, `pnd_long_inch`) VALUES
(270, 18, '', 'เผื่อตัดลาย', -0.50, NULL),
(271, 18, '', 'ตะขอ', 1.20, 0.20),
(272, 18, 'สร้อย', 'สร้อย', 10.60, 4.00),
(273, 18, 'อะไหล่', 'ซาซัว', 0.50, 0.20),
(274, 18, 'อะไหล่', 'หัวใจดิสโก้', 3.40, NULL),
(558, 62, '', 'เผื่อตัดลาย', 0.00, NULL),
(559, 62, '', 'ตะขอ', 7.60, 0.30),
(560, 62, 'อะไหล่', 'หัวงูฝังพลอย', 29.10, 0.50),
(561, 62, 'อะไหล่', 'หางงูฝังพลอย', 16.36, 0.60),
(562, 62, 'อะไหล่', 'หัวจรวด', 18.88, 1.20),
(563, 62, 'อะไหล่', 'ห่วงร่น', 6.30, 0.00),
(564, 62, 'สร้อย', 'แปดเซียน', 73.76, 9.40),
(573, 47, '', 'เผื่อตัดลาย', 0.00, NULL),
(574, 47, '', 'ตะขอ', 3.80, 0.30),
(575, 47, 'อะไหล่', 'หัวงูฝังพลอย', 14.55, 0.50),
(576, 47, 'อะไหล่', 'หางงูฝังพลอย', 8.18, 0.60),
(577, 47, 'อะไหล่', 'หัวจรวด', 9.44, 1.20),
(578, 47, 'อะไหล่', 'ห่วงร่น', 3.15, 0.00),
(579, 47, 'สร้อย', 'แปดเซียน', 36.88, 9.40);

-- --------------------------------------------------------

--
-- Table structure for table `ratio_data`
--

CREATE TABLE `ratio_data` (
  `ratio_id` int NOT NULL,
  `ratio_thick` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'หนา',
  `ratio_data` decimal(10,2) DEFAULT NULL COMMENT 'อัตราส่วน',
  `ratio_size` decimal(10,2) DEFAULT NULL COMMENT 'รูลวด',
  `ratio_gram` decimal(10,2) DEFAULT NULL COMMENT 'นน.ลวดก่อนสกัด(กรัม)',
  `ratio_inch` decimal(10,2) DEFAULT NULL COMMENT 'ค.ยาวลวด(นิ้ว)',
  `updated_users_id` int DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `ratio_data`
--

INSERT INTO `ratio_data` (`ratio_id`, `ratio_thick`, `ratio_data`, `ratio_size`, `ratio_gram`, `ratio_inch`, `updated_users_id`, `updated_at`) VALUES
(1, '0.30', 2.28, 1.25, 5.38, 10.50, 1, '2025-04-02 04:48:38'),
(2, '0.35', 2.13, 0.80, 2.22, 10.20, 1, '2025-04-02 04:48:38'),
(3, '0.40', 2.07, 1.90, 3.96, 3.20, 1, '2025-04-02 04:48:38'),
(4, '0.45', 1.97, 1.90, 2.81, 2.20, 1, '2025-04-02 04:48:38'),
(6, '0.55', NULL, NULL, NULL, NULL, 1, '2025-04-02 04:48:38'),
(7, '0.60', 1.72, 1.00, 2.90, 8.00, 1, '2025-04-02 04:48:38'),
(8, '0.70', 1.62, 4.60, 20.60, 2.60, 1, '2025-04-02 04:48:38'),
(9, '0.25', 2.50, 1.30, 4.45, 10.00, 1, '2025-04-02 04:48:38'),
(10, '0.20', 3.09, 1.50, 3.93, 5.50, 1, '2025-04-02 04:48:38'),
(11, 'ตัน', 1.00, 1.30, 0.92, 1.00, 1, '2025-04-02 04:48:38'),
(12, '0.43 ลวดกลม', 1.89, 0.65, 2.29, 15.20, 1, '2025-04-02 04:48:38'),
(13, '0.35พี่แดน', 2.25, 2.50, 7.81, 3.80, 1, '2025-04-02 04:48:38'),
(14, '0.35อกาโฟโต้(พี่สน)', 2.27, 2.80, 12.01, 4.60, 1, '2025-04-02 04:48:38'),
(15, '0.30 (อกาโฟโต้ YS)', 2.33, 1.05, 0.70, 1.90, 1, '2025-04-02 04:48:38'),
(16, '0.25ทองแดงพี่สน', 3.19, 2.10, 5.06, 3.70, 1, '2025-04-02 04:48:38'),
(17, '0.20ทองแดงพี่สน', 3.56, 3.20, 12.70, 4.10, 1, '2025-04-02 04:48:38'),
(18, 'หนา0.20ทองแดง', 3.25, 1.00, 2.94, 9.70, 1, '2025-04-02 04:48:38'),
(19, '0.20พี่เก่งเกลียว', 3.60, 1.00, 2.94, 9.70, 1, '2025-04-02 04:48:38'),
(20, '0.41ไส้4.6', 2.07, 0.42, 0.41, 7.00, 1, '2025-04-02 04:48:38'),
(21, '0.43ไส้4.2', 1.88, 0.40, 0.37, 7.00, 1, '2025-04-02 04:48:38'),
(22, '0.43ไส้4.4', 1.94, 0.56, 0.80, 8.00, 1, '2025-04-02 04:48:38'),
(23, '0.43ไส้4.6', 2.05, 0.56, 0.89, 8.00, 1, '2025-04-02 04:48:38'),
(24, '0.45ไส้3.8', 1.81, 0.45, 0.56, 7.00, 1, '2025-04-02 04:48:38'),
(25, '0.45ไส้4.0', 1.78, 0.30, 0.23, 7.00, 1, '2025-04-02 04:48:38'),
(26, '0.45ไส้4.2', 1.82, 0.95, 2.26, 7.00, 1, '2025-04-02 04:48:38'),
(27, '0.50ไส้3.6', 1.56, 0.45, 0.50, 7.00, 1, '2025-04-02 04:48:38'),
(28, '0.25 (11/1/67)', 2.57, 1.20, 4.57, 10.00, 1, '2025-04-02 04:48:38'),
(29, '1.00', 1.40, 4.90, 10.40, 1.10, 1, '2025-04-02 04:48:38'),
(30, '0.28', 2.35, 0.80, 2.05, 10.00, 1, '2025-04-02 04:48:38'),
(31, '0.23(พี่อ๋อย)', 2.57, 2.30, 16.58, 10.00, 1, '2025-04-02 04:48:38'),
(32, '0.27(พี่อ๋อย)', 3.62, 1.05, 3.62, 10.05, 1, '2025-04-02 04:48:38'),
(33, '0.25หลอด', 2.62, 1.10, 4.18, 10.80, 1, '2025-04-02 04:48:38'),
(34, '0.30หลอด', 2.48, 0.90, 3.01, 11.30, 1, '2025-04-02 04:48:38'),
(35, '0.35หลอด', 2.26, 0.73, 2.61, 14.90, 1, '2025-04-02 04:48:38'),
(36, '0.25(20กย67)', 2.49, 0.62, 1.22, 10.00, 1, '2025-04-02 04:48:38'),
(37, '0.65', 2.23, 11.00, 1667.00, 33.00, 1, '2025-04-02 04:48:38'),
(39, '0.50', 1.86, 1.70, 2.35, 2.30, 1, '2025-04-03 09:26:41'),
(40, '0.30(ช่างโอห์ม)', 2.58, 1.90, 12.50, 3.60, 1, '2025-04-04 03:13:29'),
(41, '0.30 - อังค์', 2.56, 1.60, 15.60, 8.40, 1, '2025-04-04 09:02:39'),
(42, 'มอส 0.35', 30.00, 5.00, 30.00, 5.00, 1, '2025-04-07 04:30:13'),
(43, 'มอส 0.35', 30.00, 30.01, 30.00, 30.00, 1, '2025-04-07 04:31:24'),
(44, 'มอส 0.35', 0.35, 4.00, 2.00, 1.00, 1, '2025-04-09 08:22:50'),
(45, '0.30 (ช่างประยงค)', 2.45, 2.00, 8.60, 14.60, 3, '2025-04-09 08:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `users_id` int UNSIGNED NOT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `users_level` enum('User','Admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `users_depart` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `users_status` enum('Enable','Disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`users_id`, `first_name`, `last_name`, `username`, `password`, `users_level`, `users_depart`, `users_status`) VALUES
(1, 'แอดมิน', 'web.dev', 'admin', '$2y$10$fM8U/y7t.0/OsccQgFvHLOVtu/3gI5G2o9nl/mlWwYAoK6nM1No9K', 'Admin', 'admin', 'Enable'),
(2, 'pd1', 'pd1', 'pd1', '$2y$10$G5Ipv0qhODp3X1eCerv20OpAHoL51DQPMCAS5yxPyRRK1ORwR5F6m', 'User', 'SG', 'Enable'),
(3, 'pd2', 'pd2', 'pd2', '$2y$10$JvtB8DDj643jOxYC4dYgYufWCSo3585GezCo1dQu2ugcW1geECv96', 'User', 'SG', 'Enable'),
(4, 'pd3', 'pd3', 'pd3', '$2y$10$cEABcsGf3E1nsuxfSPweTeMBkMmwRg/uXIB8AO6I/0RNcd8hzrLIi', 'User', 'SG', 'Enable'),
(5, 'pd4', 'pd4', 'pd4', '$2y$10$LdC0lZgYB9aUvfFu7aS4Y.vaXHdAohkxVQIdQtjRfh38M6DB9wL82', 'User', 'SG', 'Enable'),
(6, 'pd5', 'pd5', 'pd5', '$2y$10$7/xrqDHk7M/mHB.ytR3L5.1SpNUtHSAJ2VeDpyn/jOPEgzXi.I18.', 'User', 'SG', 'Enable'),
(7, 'YS', 'YS', 'ys', '$2y$10$F/XVFpQQRoUY4eZ4sJw46eJv8fqjdF2AKMLWI/l3pwlNSfEVMpOoK', 'User', 'YS', 'Enable'),
(8, 'ทดสอบบ้านช่าง', 'บ้านช่าง', 'test', '$2y$10$/zHwo4ptkuSNnn2Odf9CwOPRCK58qsdpWXEyIPzrcqVtkiXTqYhoC', 'User', 'บ้านช่าง', 'Enable'),
(9, 'penping', 'penping', 'penping', '$2y$10$j5cj.Xve0GpzZcVpyZ8s0eS1ieE4UAuoV0nMhUApgPq.08EEGVt2K', 'Admin', 'SG', 'Enable');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gold_type`
--
ALTER TABLE `gold_type`
  ADD PRIMARY KEY (`gold_type_id`);

--
-- Indexes for table `necklace_detail`
--
ALTER TABLE `necklace_detail`
  ADD PRIMARY KEY (`necklace_detail_id`);

--
-- Indexes for table `necklace_detail_parts`
--
ALTER TABLE `necklace_detail_parts`
  ADD PRIMARY KEY (`ndp_id`);

--
-- Indexes for table `necklace_proportions`
--
ALTER TABLE `necklace_proportions`
  ADD PRIMARY KEY (`proportions_id`);

--
-- Indexes for table `necklace_tbs`
--
ALTER TABLE `necklace_tbs`
  ADD PRIMARY KEY (`tbs_id`);

--
-- Indexes for table `percent_necklace`
--
ALTER TABLE `percent_necklace`
  ADD PRIMARY KEY (`pn_id`);

--
-- Indexes for table `percent_necklace_detail`
--
ALTER TABLE `percent_necklace_detail`
  ADD PRIMARY KEY (`pnd_id`);

--
-- Indexes for table `ratio_data`
--
ALTER TABLE `ratio_data`
  ADD PRIMARY KEY (`ratio_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gold_type`
--
ALTER TABLE `gold_type`
  MODIFY `gold_type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `necklace_detail`
--
ALTER TABLE `necklace_detail`
  MODIFY `necklace_detail_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `necklace_detail_parts`
--
ALTER TABLE `necklace_detail_parts`
  MODIFY `ndp_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `necklace_proportions`
--
ALTER TABLE `necklace_proportions`
  MODIFY `proportions_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `necklace_tbs`
--
ALTER TABLE `necklace_tbs`
  MODIFY `tbs_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT for table `percent_necklace`
--
ALTER TABLE `percent_necklace`
  MODIFY `pn_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `percent_necklace_detail`
--
ALTER TABLE `percent_necklace_detail`
  MODIFY `pnd_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=580;

--
-- AUTO_INCREMENT for table `ratio_data`
--
ALTER TABLE `ratio_data`
  MODIFY `ratio_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
