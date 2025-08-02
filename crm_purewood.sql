-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2025 at 08:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crm.purewood`
--

-- --------------------------------------------------------

--
-- Table structure for table `bom_factory`
--

CREATE TABLE `bom_factory` (
  `id` int(11) NOT NULL,
  `bom_main_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `factory_percentage` decimal(5,2) DEFAULT 15.00,
  `factory_cost` decimal(10,2) DEFAULT NULL,
  `updated_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bom_factory`
--

INSERT INTO `bom_factory` (`id`, `bom_main_id`, `total_amount`, `factory_percentage`, `factory_cost`, `updated_total`) VALUES
(7, 1, 2566.67, 15.00, 385.00, 2951.67),
(33, 2, 49490.10, 15.00, 7423.52, 56913.62),
(57, 4, 88193.15, 15.00, 13228.97, 101422.12),
(61, 3, 156716.95, 15.00, 23507.54, 180224.49),
(67, 5, 101765.63, 15.00, 15264.84, 117030.47),
(85, 6, 15562.50, 15.00, 2334.38, 17896.88),
(99, 7, 73070.50, 15.00, 10960.58, 84031.08),
(105, 8, 48215.00, 15.00, 7232.25, 55447.25);

-- --------------------------------------------------------

--
-- Table structure for table `bom_glow`
--

CREATE TABLE `bom_glow` (
  `id` int(11) NOT NULL,
  `bom_main_id` int(11) NOT NULL,
  `glowtype` varchar(255) DEFAULT NULL,
  `quantity` decimal(10,3) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bom_glow`
--

INSERT INTO `bom_glow` (`id`, `bom_main_id`, `glowtype`, `quantity`, `price`, `total`) VALUES
(1, 1, 'glue', 0.500, 100.00, 50.00),
(4, 2, 'GLUE', 10.000, 250.00, 2500.00),
(6, 3, 'glow', 24.000, 160.00, 3840.00),
(8, 4, 'GLUE', 18.800, 50.00, 940.00),
(10, 5, 'GLUE', 25.000, 50.00, 1250.00),
(12, 6, 'GLUE', 2.000, 160.00, 320.00),
(13, 7, 'GLUE', 7.800, 160.00, 1248.00),
(14, 8, 'GLUE', 5.000, 160.00, 800.00);

-- --------------------------------------------------------

--
-- Table structure for table `bom_hardware`
--

CREATE TABLE `bom_hardware` (
  `id` int(11) NOT NULL,
  `bom_main_id` int(11) NOT NULL,
  `itemname` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `totalprice` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bom_hardware`
--

INSERT INTO `bom_hardware` (`id`, `bom_main_id`, `itemname`, `quantity`, `price`, `totalprice`) VALUES
(1, 1, 'hardware', 1, 100.00, 100.00),
(4, 2, 'HARDWARE', 30, 175.00, 5250.00),
(6, 3, 'Hardware', 75, 110.00, 8250.00),
(7, 4, 'HARWARE', 1, 4700.00, 4700.00),
(8, 5, 'HARDWARE', 25, 1350.00, 33750.00),
(13, 6, '5*80 GRANDER PAPER', 20, 17.50, 350.00),
(14, 6, 'BOND', 10, 13.00, 130.00),
(15, 7, 'HARDWARE', 1, 3750.00, 3750.00),
(16, 8, 'HARDWARE', 1, 2000.00, 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `bom_labour`
--

CREATE TABLE `bom_labour` (
  `id` int(11) NOT NULL,
  `bom_main_id` int(11) NOT NULL,
  `itemname` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `totalprice` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bom_labour`
--

INSERT INTO `bom_labour` (`id`, `bom_main_id`, `itemname`, `quantity`, `price`, `totalprice`) VALUES
(1, 1, 'test labour ', 10, 100.00, 1000.00),
(6, 2, 'LAXMAN JI ', 10, 700.00, 7000.00),
(10, 4, 'LAXMAN JI', 47, 550.00, 25850.00),
(11, 3, 'RAJU DAN JI', 75, 410.00, 30750.00),
(12, 5, 'RAJU DAN JI', 25, 700.00, 17500.00),
(14, 6, 'RAJU TEST', 10, 550.00, 5500.00),
(16, 7, 'RAJU DAN', 25, 700.00, 17500.00),
(17, 8, 'RAJU DAN JI', 20, 812.00, 16240.00);

-- --------------------------------------------------------

--
-- Table structure for table `bom_main`
--

CREATE TABLE `bom_main` (
  `id` int(11) NOT NULL,
  `bom_number` varchar(50) NOT NULL,
  `costing_sheet_number` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `prepared_by` varchar(255) NOT NULL,
  `order_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `labour_cost` decimal(10,2) DEFAULT NULL,
  `factory_cost` decimal(10,2) DEFAULT NULL,
  `margin` decimal(10,2) DEFAULT NULL,
  `grand_total_amount` decimal(10,2) DEFAULT NULL,
  `jci_assigned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bom_main`
--

INSERT INTO `bom_main` (`id`, `bom_number`, `costing_sheet_number`, `client_name`, `prepared_by`, `order_date`, `delivery_date`, `created_at`, `updated_at`, `labour_cost`, `factory_cost`, `margin`, `grand_total_amount`, `jci_assigned`) VALUES
(1, 'BOM-2025-0001', 'test 01', 'test 01', 'test', '2025-07-11', '2025-07-11', '2025-07-11 06:51:36', '2025-07-11 06:55:30', NULL, NULL, NULL, 2966.67, 1),
(2, 'BOM-2025-0002', 'DUMMY', 'APL', 'JS CHOUHAN', '2025-07-11', '2025-07-11', '2025-07-11 10:52:41', '2025-07-12 03:25:13', NULL, NULL, NULL, 63490.10, 1),
(3, 'BOM-2025-0003', '1', '7 Seas II', 'Js Chouhan', '2025-07-17', '2025-07-17', '2025-07-17 09:18:14', '2025-07-26 11:36:03', NULL, NULL, NULL, 210337.50, 1),
(4, 'BOM-2025-0004', '2', '7 Seas II', 'JS CHOUHAN', '2025-07-21', '2025-07-21', '2025-07-21 10:06:42', '2025-07-26 11:20:36', NULL, NULL, NULL, 121612.11, 1),
(5, 'BOM-2025-0005', '3', 'APL', 'JS CHOUHAN', '2025-07-24', '2025-07-24', '2025-07-30 10:10:48', '2025-07-30 10:15:12', NULL, NULL, NULL, 134585.04, 0),
(6, 'BOM-2025-0006', '6', 'TEST PD', 'JS CHOUHANQ', '2025-07-30', '2025-07-30', '2025-07-30 10:32:54', '2025-07-30 10:52:56', NULL, NULL, NULL, 20466.88, 1),
(7, 'BOM-2025-0007', '7', 'APL', 'JS CHOUHAN', '2025-07-31', '2025-07-31', '2025-07-31 09:06:22', '2025-07-31 10:49:25', NULL, NULL, NULL, 94042.12, 1),
(8, 'BOM-2025-0008', '8', 'APL', 'JS CHOUHAN', '2025-07-31', '2025-07-31', '2025-07-31 11:32:52', '2025-07-31 11:40:12', NULL, NULL, NULL, 58800.70, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bom_margin`
--

CREATE TABLE `bom_margin` (
  `id` int(11) NOT NULL,
  `bom_main_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `margin_percentage` decimal(5,2) DEFAULT 15.00,
  `margin_cost` decimal(10,2) DEFAULT NULL,
  `updated_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bom_margin`
--

INSERT INTO `bom_margin` (`id`, `bom_main_id`, `total_amount`, `margin_percentage`, `margin_cost`, `updated_total`) VALUES
(7, 1, 2766.67, 15.00, 200.00, 2966.67),
(33, 2, 53490.10, 15.00, 10000.00, 63490.10),
(57, 4, 101422.12, 15.00, 15213.32, 116635.44),
(61, 3, 179886.99, 15.00, 30113.01, 210000.00),
(67, 5, 117030.47, 15.00, 17554.57, 134585.04),
(85, 6, 17122.50, 15.00, 2570.00, 19692.50),
(99, 7, 83739.23, 15.00, 10011.04, 93750.27),
(105, 8, 55567.25, 15.00, 3233.45, 58800.70);

-- --------------------------------------------------------

--
-- Table structure for table `bom_plynydf`
--

CREATE TABLE `bom_plynydf` (
  `id` int(11) NOT NULL,
  `bom_main_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT NULL,
  `length` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bom_plynydf`
--

INSERT INTO `bom_plynydf` (`id`, `bom_main_id`, `quantity`, `width`, `length`, `price`, `total`) VALUES
(1, 1, 1, 10.00, 10.00, 10.00, 1000.00),
(2, 2, 2, 10.00, 10.00, 10.00, 2000.00),
(5, 6, 10, 1.50, 2.00, 42.00, 1260.00);

-- --------------------------------------------------------

--
-- Table structure for table `bom_wood`
--

CREATE TABLE `bom_wood` (
  `id` int(11) NOT NULL,
  `bom_main_id` int(11) NOT NULL,
  `woodtype` varchar(255) DEFAULT NULL,
  `length_ft` decimal(10,2) DEFAULT NULL,
  `width_ft` decimal(10,2) DEFAULT NULL,
  `thickness_inch` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `cft` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bom_wood`
--

INSERT INTO `bom_wood` (`id`, `bom_main_id`, `woodtype`, `length_ft`, `width_ft`, `thickness_inch`, `quantity`, `price`, `cft`, `total`) VALUES
(1, 1, 'Mango', 10.00, 0.83, 10.00, 3, 20.00, 20.83, 416.67),
(16, 2, 'Mango', 4.50, 2.83, 2.00, 20, 690.00, 42.45, 29290.50),
(17, 2, 'Mango', 1.75, 0.33, 1.60, 80, 560.00, 6.16, 3449.60),
(40, 3, 'Mango', 3.00, 1.67, 1.50, 225, 650.00, 140.91, 91589.06),
(41, 3, 'Mango', 2.75, 0.25, 1.50, 300, 650.00, 25.78, 16757.81),
(42, 3, 'Mango', 2.75, 0.33, 1.50, 75, 650.00, 8.51, 5530.08),
(43, 4, 'Mango', 1.75, 1.58, 1.00, 94, 560.00, 21.66, 12129.13),
(44, 4, 'Mango', 1.75, 1.92, 1.00, 94, 560.00, 26.32, 14739.20),
(45, 4, 'Mango', 1.75, 0.42, 1.00, 188, 560.00, 11.52, 6448.40),
(46, 4, 'Mango', 1.50, 0.42, 1.00, 188, 560.00, 9.87, 5527.20),
(47, 4, 'Mango', 1.75, 0.13, 1.00, 94, 560.00, 1.78, 997.97),
(48, 4, 'Mango', 1.50, 1.25, 1.00, 141, 560.00, 22.03, 12337.50),
(49, 4, 'Mango', 1.50, 0.25, 1.00, 188, 560.00, 5.88, 3290.00),
(50, 4, '', 1.50, 0.25, 1.50, 47, 560.00, 2.20, 1233.75),
(54, 5, 'Mango', 2.75, 1.92, 1.00, 100, 570.00, 43.92, 25036.46),
(55, 5, 'Mango', 2.50, 0.33, 2.00, 200, 690.00, 27.78, 19166.67),
(56, 5, 'Mango', 1.50, 0.33, 1.50, 150, 540.00, 9.38, 5062.50),
(61, 6, 'Mango', 2.00, 0.33, 1.50, 50, 560.00, 4.13, 2310.00),
(62, 6, 'Mango', 2.50, 0.33, 1.50, 80, 690.00, 8.25, 5692.50),
(69, 7, 'Mango', 2.50, 1.92, 1.00, 100, 660.00, 40.00, 26400.00),
(70, 7, 'Mango', 2.50, 0.33, 2.00, 200, 690.00, 27.50, 18975.00),
(71, 7, 'Mango', 1.50, 0.33, 1.50, 150, 560.00, 9.28, 5197.50),
(72, 8, 'Mango', 2.25, 1.58, 1.00, 140, 560.00, 41.56, 23275.00),
(73, 8, 'Mango', 1.50, 2.00, 2.00, 20, 590.00, 10.00, 5900.00);

-- --------------------------------------------------------

--
-- Table structure for table `jci_items`
--

CREATE TABLE `jci_items` (
  `id` int(11) NOT NULL,
  `jci_id` int(11) NOT NULL,
  `job_card_number` varchar(255) DEFAULT NULL,
  `po_product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `original_po_quantity` decimal(10,2) DEFAULT NULL,
  `labour_cost` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_date` date NOT NULL,
  `job_card_date` date DEFAULT NULL,
  `job_card_type` enum('Contracture','In-House') DEFAULT NULL,
  `contracture_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jci_items`
--

INSERT INTO `jci_items` (`id`, `jci_id`, `job_card_number`, `po_product_id`, `product_name`, `item_code`, `original_po_quantity`, `labour_cost`, `quantity`, `total_amount`, `delivery_date`, `job_card_date`, `job_card_type`, `contracture_name`) VALUES
(1, 1, 'JOB-2025-0001-1', 1, 'test pro', 'pro01', 10.00, 100.00, 10, 1000.00, '2025-07-01', '2025-07-16', 'Contracture', 'test con'),
(2, 2, 'JOB-2025-0002-1', 2, 'COFFEE TABLE 120X65X43CM', 'AHFL-CT180', 20.00, 700.00, 20, 14000.00, '2025-07-31', '2025-07-14', 'Contracture', 'LAXMAN JI'),
(5, 4, 'JOB-2025-0004-1', 4, 'C Side Table Raw Section', 'CBRN001', 47.00, 550.00, 47, 25850.00, '2025-07-21', '2025-08-10', 'Contracture', 'LAXMAN JI'),
(7, 3, 'JOB-2025-0003-1', 3, 'Burano Chair Mango Wood Frame Raw', 'NA', 75.00, 0.00, 75, 0.00, '2025-07-26', '2025-08-10', 'In-House', NULL),
(9, 5, 'JOB-2025-0005-1', 5, 'TEST PERFECT STUDY TABLE', 'TEST CODE-001', 10.00, 550.00, 10, 5500.00, '2025-08-20', '2025-07-30', 'Contracture', 'RAJU TEST'),
(10, 6, 'JOB-2025-0006-1', 7, '\" 124311 AHDU DT220MB Wooden  Base\"', 'ADC-410', 25.00, 700.00, 25, 17500.00, '2025-08-20', '2025-07-24', 'Contracture', 'RAJU DAN JI'),
(11, 7, 'JOB-2025-0007-1', 8, 'Moda Side Table', 'BS-MOD02N', 20.00, 812.00, 20, 16240.00, '2025-08-25', '2025-07-31', 'Contracture', 'RAJU DAN JI');

-- --------------------------------------------------------

--
-- Table structure for table `jci_main`
--

CREATE TABLE `jci_main` (
  `id` int(11) NOT NULL,
  `jci_number` varchar(255) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `bom_id` int(11) DEFAULT NULL,
  `jci_type` enum('Contracture','In-House') NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `jci_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sell_order_number` varchar(50) DEFAULT NULL,
  `purchase_created` tinyint(1) DEFAULT 0,
  `payment_completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jci_main`
--

INSERT INTO `jci_main` (`id`, `jci_number`, `po_id`, `bom_id`, `jci_type`, `created_by`, `jci_date`, `created_at`, `updated_at`, `sell_order_number`, `purchase_created`, `payment_completed`) VALUES
(1, 'JCI-2025-0001', 1, 1, 'Contracture', 'test 01', '2025-07-11', '2025-07-11 06:55:30', '2025-07-11 11:46:44', '\r\n                                    SALE-2025-00', 1, 1),
(2, 'JCI-2025-0002', 2, 2, 'Contracture', 'JS CHOUHAN', '2025-07-11', '2025-07-11 11:03:42', '2025-07-11 11:03:42', '\r\n                                    SALE-2025-00', 0, 0),
(3, 'JCI-2025-0003', 3, 3, 'Contracture', 'Js Chouhan', '2025-07-17', '2025-07-17 10:03:55', '2025-07-21 08:38:28', '\r\n                                    SALE-2025-00', 0, 0),
(4, 'JCI-2025-0004', 4, 4, 'Contracture', 'JS CHOUHAN', '2025-07-21', '2025-07-21 10:24:09', '2025-07-21 10:24:09', '\r\n                                    SALE-2025-00', 0, 0),
(5, 'JCI-2025-0005', 5, 6, 'Contracture', 'JS', '2025-07-30', '2025-07-30 10:46:08', '2025-07-31 06:50:35', '\r\n                                    SALE-2025-00', 1, 1),
(6, 'JCI-2025-0006', 6, 7, 'Contracture', 'JS CHOUHAN', '2025-07-24', '2025-07-31 09:18:37', '2025-07-31 11:16:55', '\r\n                                    SALE-2025-00', 1, 1),
(7, 'JCI-2025-0007', 7, 8, 'Contracture', 'JS CHOUHAN', '2025-07-31', '2025-07-31 11:40:12', '2025-07-31 11:40:12', '\r\n                                    SALE-2025-00', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `job_cards`
--

CREATE TABLE `job_cards` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `jc_number` varchar(255) NOT NULL,
  `jc_amt` decimal(15,2) NOT NULL,
  `jc_type` varchar(255) DEFAULT NULL,
  `contracture_name` varchar(255) DEFAULT NULL,
  `labour_cost` decimal(15,2) DEFAULT 0.00,
  `quantity` int(11) DEFAULT 0,
  `total_amount` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `lead_number` varchar(50) NOT NULL,
  `entry_date` date NOT NULL,
  `lead_source` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `contact_name` varchar(255) NOT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_email` varchar(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `created_status` varchar(50) DEFAULT 'new',
  `approve` tinyint(1) DEFAULT 0,
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `lead_number`, `entry_date`, `lead_source`, `company_name`, `contact_name`, `contact_phone`, `contact_email`, `country`, `state`, `city`, `created_status`, `approve`, `status`, `created_at`, `updated_at`) VALUES
(1, 'LEAD-2025-0001', '2025-07-09', 'Ravindra Sir', 'ABC', 'Mr. Shawn Purewood', '+01 9875432101', 'Shawn@gmail.com', 'United States', 'New York', 'Airmont', 'new', 1, 'active', '2025-07-17 02:33:25', '2025-07-17 03:02:09'),
(2, 'LEAD-2025-0002', '2025-07-09', 'Ravi Sir', 'ABC', 'Mr. Shawn Purewood', '+01 9875432101', 'Shawn@gmail.com', 'United States', 'Alaska', 'Akutan', 'new', 1, 'active', '2025-07-17 19:12:01', '2025-07-17 19:12:13'),
(3, 'LEAD-2025-0003', '2025-07-07', 'Ravindra Sir', 'VIG USA', 'VIG', '1234567890', 'VIG@gmail.com', 'United States', 'Alabama', 'Abbeville', 'new', 1, 'active', '2025-07-18 02:11:55', '2025-07-18 02:46:37'),
(4, 'LEAD-2025-0004', '2025-07-15', 'Ravindra Sir', 'VIG USA', 'VIG', '1234567890', 'VIG@gmail.com', 'United States', 'Alabama', 'Abbeville', 'new', 1, 'active', '2025-07-18 03:10:04', '2025-07-18 03:10:11'),
(5, 'LEAD-2025-0005', '2025-07-23', 'Ravindra Sir', 'Vaya Group', 'Anthony', '1234567890', 'spitnsawdustuk@gmail.com', 'United Kingdom', 'England', 'London', 'new', 1, 'active', '2025-07-23 18:42:27', '2025-07-23 18:42:38'),
(6, 'LEAD-2025-0006', '2025-07-17', 'Ravindra Sir', 'Avi Homes', 'Avi Homes', '1234567891', 'xxx@gmail.com', 'United Kingdom', 'England', 'Manchester', 'new', 1, 'active', '2025-07-25 07:50:44', '2025-07-25 07:51:01'),
(7, 'LEAD-2025-0007', '2025-07-30', 'Ravi Sir', 'Halland buyer', 'Xyz Holland', '1234567891', 'Holland@gamil.com', 'Netherlands', 'South Holland', 'Boskoop', 'new', 1, 'active', '2025-07-30 12:50:14', '2025-07-30 12:52:52'),
(8, 'LEAD-2025-0007', '2025-07-30', 'Ravi Sir', 'Halland buyer', 'Xyz Holland', '1234567891', 'Holland@gamil.com', 'Netherlands', 'South Holland', 'Boskoop', 'new', 0, 'active', '2025-07-30 12:50:14', '2025-07-30 12:50:14'),
(9, 'LEAD-2025-0007', '2025-07-30', 'Ravi Sir', 'Halland buyer', 'Xyz Holland', '1234567891', 'Holland@gamil.com', 'Netherlands', 'South Holland', 'Boskoop', 'new', 0, 'active', '2025-07-30 12:50:14', '2025-07-30 12:50:14'),
(10, 'LEAD-2025-0007', '2025-07-30', 'Ravi Sir', 'Halland buyer', 'Xyz Holland', '1234567891', 'Holland@gamil.com', 'Netherlands', 'South Holland', 'Boskoop', 'new', 0, 'active', '2025-07-30 12:50:14', '2025-07-30 12:50:14'),
(11, 'LEAD-2025-0007', '2025-07-30', 'Ravi Sir', 'Halland buyer', 'Xyz Holland', '1234567891', 'Holland@gamil.com', 'Netherlands', 'South Holland', 'Boskoop', 'new', 0, 'active', '2025-07-30 12:50:14', '2025-07-30 12:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `jci_number` varchar(50) NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `sell_order_number` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `jci_number`, `po_number`, `sell_order_number`, `created_at`, `updated_at`) VALUES
(2, 'JCI-2025-0001', 'test 001', 'SALE-2025-0001', '2025-07-11 11:46:44', '2025-07-11 11:46:44'),
(3, 'JCI-2025-0005', 'TEST-PD001', 'SALE-2025-0005', '2025-07-31 06:50:35', '2025-07-31 06:50:35'),
(4, 'JCI-2025-0006', '3764', 'SALE-2025-0006', '2025-07-31 11:16:55', '2025-08-02 06:20:22');

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `jc_number` varchar(50) DEFAULT NULL,
  `payment_type` varchar(20) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `pd_acc_number` varchar(50) DEFAULT NULL,
  `ptm_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_invoice_date` date DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_category` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_details`
--

INSERT INTO `payment_details` (`id`, `payment_id`, `jc_number`, `payment_type`, `cheque_number`, `pd_acc_number`, `ptm_amount`, `payment_invoice_date`, `payment_date`, `payment_category`, `amount`, `created_at`, `updated_at`) VALUES
(8, 2, 'JCI-2025-0001', 'Cheque', '100', '1234', 1000.00, '0000-00-00', '2025-07-01', 'Job Card', 1000.00, '2025-07-11 11:46:44', '2025-07-11 11:46:44'),
(9, 2, '', 'RTGS', '101', '1234', 1.50, '2025-07-18', '2025-07-17', 'Supplier', 1.50, '2025-07-11 11:46:44', '2025-07-11 11:46:44'),
(10, 2, '', 'RTGS', '100', '124', 4.00, '2025-07-15', '2025-07-08', 'Supplier', 4.00, '2025-07-11 11:46:44', '2025-07-11 11:46:44'),
(11, 3, '', 'Cheque', '000356', '7777', 320.00, '2025-08-01', '2025-07-31', 'Supplier', 320.00, '2025-07-31 06:50:35', '2025-07-31 06:50:35'),
(12, 4, '', 'Cheque', '000405', '777705091719', 5464.80, '2025-07-26', '2025-08-25', 'Supplier', 5464.80, '2025-07-31 11:16:55', '2025-07-31 11:16:55');

-- --------------------------------------------------------

--
-- Table structure for table `pi`
--

CREATE TABLE `pi` (
  `pi_id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `quotation_number` varchar(50) NOT NULL,
  `pi_number` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Generated',
  `inspection` text DEFAULT NULL,
  `date_of_pi_raised` date DEFAULT NULL,
  `sample_approval_date` date DEFAULT NULL,
  `detailed_seller_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `po_items`
--

CREATE TABLE `po_items` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_code` varchar(100) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `po_items`
--

INSERT INTO `po_items` (`id`, `po_id`, `product_code`, `product_name`, `quantity`, `unit`, `price`, `total_amount`, `product_image`, `created_at`, `updated_at`) VALUES
(1, 1, 'pro01', 'test pro', 10.00, '', 100.00, 1000.00, '6870b517caa75.png', '2025-07-11 06:54:15', '2025-07-11 06:54:15'),
(2, 2, 'AHFL-CT180', 'COFFEE TABLE 120X65X43CM', 20.00, '', 4000.00, 80000.00, '6870eed0323c9.jpeg', '2025-07-11 11:00:32', '2025-07-11 11:00:32'),
(3, 3, 'NA', 'Burano Chair Mango Wood Frame Raw', 75.00, '', 2800.00, 210000.00, '', '2025-07-16 11:31:58', '2025-07-16 11:31:58'),
(4, 4, 'CBRN001', 'C Side Table Raw Section', 47.00, '', 2500.00, 117500.00, '', '2025-07-21 10:15:16', '2025-07-21 10:15:16'),
(5, 5, 'TEST CODE-001', 'TEST PERFECT STUDY TABLE', 10.00, '', 1980.00, 19800.00, '', '2025-07-30 10:40:45', '2025-07-30 10:40:45'),
(7, 6, 'ADC-410', '\" 124311 AHDU DT220MB Wooden  Base\"', 25.00, '', 3750.00, 93750.00, '', '2025-07-31 09:16:59', '2025-07-31 09:16:59'),
(8, 7, 'BS-MOD02N', 'Moda Side Table', 20.00, '', 2940.00, 58800.00, '', '2025-07-31 11:37:10', '2025-07-31 11:37:10');

-- --------------------------------------------------------

--
-- Table structure for table `po_main`
--

CREATE TABLE `po_main` (
  `id` int(11) NOT NULL,
  `po_number` varchar(100) NOT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `prepared_by` varchar(255) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `sell_order_id` int(11) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sell_order_number` varchar(50) DEFAULT NULL,
  `jci_number` varchar(50) DEFAULT NULL,
  `jci_assigned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `po_main`
--

INSERT INTO `po_main` (`id`, `po_number`, `client_name`, `prepared_by`, `order_date`, `delivery_date`, `sell_order_id`, `status`, `is_locked`, `created_at`, `updated_at`, `sell_order_number`, `jci_number`, `jci_assigned`) VALUES
(1, 'test 001', 'test po', 'test', '2025-07-08', '2025-07-28', 1, 'Locked', 1, '2025-07-11 06:54:15', '2025-07-11 06:55:30', 'SALE-2025-0001', '', 1),
(2, '546', 'APL', 'JS CHOUHAN', '2025-07-11', '2025-07-31', 2, 'Locked', 1, '2025-07-11 11:00:32', '2025-07-11 11:03:42', 'SALE-2025-0002', '', 1),
(3, '2128', '7 Seas II', 'Js chouhan', '2025-07-15', '2025-08-05', 3, 'Approved', 0, '2025-07-16 11:31:58', '2025-07-17 10:03:55', 'SALE-2025-0003', '', 1),
(4, '2145', '7 SEAS II', 'JS CHOUHAN', '2025-07-21', '2025-08-10', 4, 'Approved', 0, '2025-07-21 10:15:16', '2025-07-21 10:24:09', 'SALE-2025-0004', '', 1),
(5, 'TEST-PD001', 'TEST PD', 'JS', '2025-07-30', '2025-08-19', 5, 'Approved', 0, '2025-07-30 10:40:45', '2025-07-30 10:46:08', 'SALE-2025-0005', '', 1),
(6, '3764', 'APL', 'JS CHOUHAN', '2025-07-08', '2025-08-22', 6, 'Locked', 1, '2025-07-31 09:16:52', '2025-07-31 09:18:37', 'SALE-2025-0006', '', 1),
(7, '4244', 'APL', 'JS CHOUHAN', '2025-07-31', '2025-08-25', 7, 'Approved', 0, '2025-07-31 11:37:10', '2025-07-31 11:40:12', 'SALE-2025-0007', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_main_id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `product_type` varchar(100) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `job_card_number` varchar(50) NOT NULL,
  `assigned_quantity` decimal(10,3) NOT NULL DEFAULT 0.000,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date` date DEFAULT NULL,
  `invoice_number` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `invoice_image` varchar(255) DEFAULT NULL,
  `builty_number` varchar(100) DEFAULT NULL,
  `builty_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_main_id`, `supplier_name`, `product_type`, `product_name`, `job_card_number`, `assigned_quantity`, `price`, `total`, `created_at`, `updated_at`, `date`, `invoice_number`, `amount`, `invoice_image`, `builty_number`, `builty_image`) VALUES
(13, 1, 'test suppi1', 'Glow', 'glue', 'JOB-2025-0001-1', 0.300, 0.30, 0.30, '2025-07-12 03:26:23', '2025-07-12 03:26:23', NULL, NULL, NULL, NULL, NULL, NULL),
(14, 1, 'test suppi1', 'Hardware', 'hardware', 'JOB-2025-0001-1', 1.000, 1.00, 1.00, '2025-07-12 03:26:23', '2025-07-12 03:26:23', NULL, NULL, NULL, NULL, NULL, NULL),
(15, 1, 'test suppi2', 'Plynydf', 'Plynydf', 'JOB-2025-0001-1', 1.000, 1.00, 1.00, '2025-07-12 03:26:23', '2025-07-12 03:26:23', NULL, NULL, NULL, NULL, NULL, NULL),
(16, 1, 'test suppi2', 'Wood', 'Mango', 'JOB-2025-0001-1', 3.000, 3.00, 3.00, '2025-07-12 03:26:23', '2025-07-12 03:26:23', NULL, NULL, NULL, NULL, NULL, NULL),
(34, 2, 'ARPIT SALES', 'Glow', 'GLUE', 'JOB-2025-0005-1', 2.000, 160.00, 320.00, '2025-07-30 11:51:40', '2025-07-31 06:39:59', '2025-08-01', 'TEST', 260.00, 'invoice_688a085943ceb_WhatsApp Image 2025-07-28 at 6.36.34 PM.jpeg', 'NA', 'builty_688a085943ced_WhatsApp Image 2025-07-28 at 6.36.34 PM.jpeg'),
(35, 2, 'MARWAR SUPPLIERS', 'Hardware', '5*80 GRANDER PAPER', 'JOB-2025-0005-1', 20.000, 17.50, 350.00, '2025-07-30 11:51:40', '2025-07-31 06:40:49', NULL, NULL, NULL, NULL, NULL, NULL),
(36, 2, 'MARWAR SUPPLIERS', 'Hardware', 'BOND', 'JOB-2025-0005-1', 10.000, 13.00, 130.00, '2025-07-30 11:51:40', '2025-07-31 06:41:16', NULL, NULL, NULL, NULL, NULL, NULL),
(37, 2, 'MAHA AMBE', 'Plynydf', 'Plynydf', 'JOB-2025-0005-1', 10.000, 160.00, 1600.00, '2025-07-30 11:51:40', '2025-07-31 06:41:55', NULL, NULL, NULL, NULL, NULL, NULL),
(38, 2, 'MK TIMBER', 'Wood', 'Mango', 'JOB-2025-0005-1', 8.330, 590.00, 4914.00, '2025-07-30 11:51:40', '2025-07-31 06:44:30', NULL, NULL, NULL, NULL, NULL, NULL),
(39, 2, 'MK TIMBER', 'Wood', 'Mango', 'JOB-2025-0005-1', 6.000, 660.00, 3960.00, '2025-07-30 11:51:40', '2025-07-31 06:46:22', NULL, NULL, NULL, NULL, NULL, NULL),
(46, 3, 'Ashapurna Enterprises', 'Wood', 'Mango', 'JOB-2025-0006-1', 8.280, 660.00, 5464.80, '2025-07-31 10:55:05', '2025-07-31 11:15:31', '2025-07-26', 'AE/2526/196', 80388.00, 'invoice_688b5053664c5_Ashapurna Enterprises 196.jpeg', '732', 'builty_688b5053664c7_WhatsApp Image 2025-07-31 at 4.43.25 PM.jpeg'),
(47, 3, 'JAI GURUDEV ENTERPRISES', 'Wood', 'Mango', 'JOB-2025-0006-1', 31.750, 690.00, 21907.50, '2025-07-31 10:55:05', '2025-07-31 10:55:05', NULL, NULL, NULL, NULL, NULL, NULL),
(48, 3, 'Maruti Art & Crafts', 'Wood', 'Mango', 'JOB-2025-0006-1', 6.380, 560.00, 3572.80, '2025-07-31 10:55:05', '2025-07-31 10:55:05', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_main`
--

CREATE TABLE `purchase_main` (
  `id` int(11) NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `jci_number` varchar(50) NOT NULL,
  `sell_order_number` varchar(50) NOT NULL,
  `bom_number` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_main`
--

INSERT INTO `purchase_main` (`id`, `po_number`, `jci_number`, `sell_order_number`, `bom_number`, `created_at`, `updated_at`) VALUES
(1, 'test 001', 'JCI-2025-0001', '\n                                    SALE-2025-00', 'BOM-2025-0001', '2025-07-11 06:57:23', '2025-07-12 03:26:23'),
(2, 'TEST-PD001', 'JCI-2025-0005', '\n                                    SALE-2025-00', 'BOM-2025-0006', '2025-07-30 10:55:46', '2025-07-30 11:51:40'),
(3, '3764', 'JCI-2025-0006', '\n                                    SALE-2025-00', 'BOM-2025-0007', '2025-07-31 09:43:54', '2025-07-31 10:55:05');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `quotation_date` date NOT NULL,
  `quotation_number` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `delivery_term` varchar(255) DEFAULT NULL,
  `terms_of_delivery` varchar(255) DEFAULT NULL,
  `quotation_image` varchar(255) DEFAULT NULL,
  `approve` tinyint(1) NOT NULL DEFAULT 0,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `lead_id`, `quotation_date`, `quotation_number`, `customer_name`, `customer_email`, `customer_phone`, `delivery_term`, `terms_of_delivery`, `quotation_image`, `approve`, `locked`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-07-17', 'QUOTE-2025-00001', 'ABC', 'Shawn@gmail.com', '+01 9875432101', '30/70', '85 Days', NULL, 0, 0, '2025-07-17 09:09:37', '2025-07-17 09:51:56'),
(2, 2, '2025-07-17', 'QUOTE-2025-00002', 'ABC', 'Shawn@gmail.com', '+01 9875432101', '30/70', '85 Days', NULL, 0, 0, '2025-07-18 01:37:23', '2025-07-18 01:37:23'),
(3, 3, '2025-07-18', 'QUOTE-2025-00003', 'VIG USA', 'VIG@gmail.com', '1234567890', '30/70', '85 Days', NULL, 0, 0, '2025-07-18 02:53:33', '2025-07-18 02:53:33'),
(4, 4, '2025-07-18', 'QUOTE-2025-00004', 'VIG USA', 'VIG@gmail.com', '1234567890', '30/70', '85 Days', NULL, 0, 0, '2025-07-18 03:19:18', '2025-07-18 03:19:18'),
(5, 6, '2025-07-25', 'QUOTE-2025-00005', 'Avi Homes', 'xxx@gmail.com', '1234567891', '30/70', '85 Days', NULL, 0, 0, '2025-07-25 07:56:14', '2025-07-25 07:56:14'),
(22, 1, '2025-08-01', 'QUOTE-2025-00006', 'ABC', 'Shawn@gmail.com', '+01 9875432101', '70-30', '70', NULL, 0, 0, '2025-08-01 06:08:56', '2025-08-01 06:08:56'),
(23, 7, '2025-08-01', 'QUOTE-2025-00007', 'Halland buyer', 'Holland@gamil.com', '1234567891', '30/70', '85 Days', NULL, 0, 0, '2025-08-01 06:11:44', '2025-08-01 06:11:44'),
(24, 7, '2025-08-01', 'QUOTE-2025-00007', 'Halland buyer', 'Holland@gamil.com', '1234567891', '30/70', '85 Days', NULL, 0, 0, '2025-08-01 06:11:47', '2025-08-01 06:11:47'),
(26, 1, '2025-08-01', 'QUOTE-2025-00008', 'ABC', 'Shawn@gmail.com', '+01 9875432101', '70-30', '70', NULL, 0, 0, '2025-08-01 07:45:58', '2025-08-01 07:45:58');

-- --------------------------------------------------------

--
-- Table structure for table `quotation_products`
--

CREATE TABLE `quotation_products` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `assembly` varchar(255) DEFAULT NULL,
  `item_h` decimal(10,2) DEFAULT NULL,
  `item_w` decimal(10,2) DEFAULT NULL,
  `item_d` decimal(10,2) DEFAULT NULL,
  `box_h` decimal(10,2) DEFAULT NULL,
  `box_w` decimal(10,2) DEFAULT NULL,
  `box_d` decimal(10,2) DEFAULT NULL,
  `cbm` decimal(10,3) DEFAULT NULL,
  `wood_type` varchar(255) DEFAULT NULL,
  `no_of_packet` int(11) DEFAULT NULL,
  `iron_gauge` varchar(100) DEFAULT NULL,
  `mdf_finish` varchar(255) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price_usd` decimal(10,2) NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `product_image_name` varchar(255) DEFAULT NULL,
  `total_price_usd` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotation_products`
--

INSERT INTO `quotation_products` (`id`, `quotation_id`, `item_name`, `item_code`, `description`, `assembly`, `item_h`, `item_w`, `item_d`, `box_h`, `box_w`, `box_d`, `cbm`, `wood_type`, `no_of_packet`, `iron_gauge`, `mdf_finish`, `quantity`, `price_usd`, `comments`, `created_at`, `updated_at`, `product_image_name`, `total_price_usd`) VALUES
(17, 1, 'ORCHID DAY BED', '', '', 'Fix', 71.00, 203.00, 86.30, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 280.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 5600.00),
(18, 1, 'ORCHID KING BED', '', '', 'KD', 122.00, 216.00, 194.30, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 470.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 9400.00),
(19, 1, 'ORCHID ROUND SIDE TABLE', '', '', 'Fix', 46.00, 40.60, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 55.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 1100.00),
(20, 1, 'ORCHID ROUND DINING TABLE', '', '', 'KD', 76.20, 120.00, 120.00, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 220.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 4400.00),
(21, 1, 'ORCHID OVAL DINING TABLE', '', '', 'KD', 76.20, 203.20, 101.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 314.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 6280.00),
(22, 1, 'ORCHID OPEN NIGHT STAND', '', '', 'Fix', 61.00, 81.20, 50.80, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 110.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 2200.00),
(23, 1, 'ORCHID CONSOLE TABLE', '', '', 'KD', 76.20, 120.00, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 142.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 2840.00),
(24, 1, 'ORCHID COFFEE TABLE', '', '', 'Fix', 40.60, 101.60, 50.80, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 94.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 1880.00),
(25, 1, 'ORCHID 6 DRAWER DRESSER', '', '', 'Fix', 84.00, 152.40, 51.00, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 300.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 6000.00),
(26, 1, 'ORCHID 5 DRAWER DRESSER', '', '', 'Fix', 114.30, 92.00, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 240.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 4800.00),
(27, 1, 'ORCHID 2 DOOR 3 DRAWER SIDEBOARD', '', '', 'Fix', 81.30, 183.00, 50.80, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 281.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 5620.00),
(28, 1, 'ORCHID 2 DOOR 1 DRAWER LOW MEDIA CONSOLE', '', '', 'Fix', 45.70, 183.00, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 184.00, '', '2025-07-17 09:52:43', '2025-07-17 09:52:43', NULL, 3680.00),
(57, 2, 'CAMBRIDGE WHITE MARBLE AND MANGO TALL BUFFET', '', '', 'KD', 80.00, 111.50, 38.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 162.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', NULL, 3240.00),
(58, 2, 'Cambridge White Marble & Mango 5 Drawer Chest', '', '', 'KD', 120.00, 51.50, 40.40, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 227.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', NULL, 4540.00),
(59, 2, 'Cambridge White Marble & Mango 3 Drawer Dresser', '', '', 'KD', 85.60, 92.00, 45.50, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 218.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', NULL, 4360.00),
(60, 2, 'Cambridge White Marble & Mango 2 Drawer Nightstand', '', '', 'KD', 57.40, 61.00, 43.00, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 118.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', NULL, 2360.00),
(61, 2, 'Cambridge White Marble & Mango Dresser', '', '', 'KD', 85.60, 152.40, 45.50, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 340.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', NULL, 6800.00),
(62, 2, 'Cambridge White Marble & Mango Round End Table', '', '', 'Fix', 45.70, 40.60, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 60.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', NULL, 1200.00),
(63, 2, 'Cambridge White Marble & Mango Round Coffee Table', '', '', 'Fix', 40.60, 80.00, 80.00, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 135.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', NULL, 2700.00),
(64, 2, 'Cambridge White Marble & Mango Console Table', '', '', 'KD', 76.20, 119.40, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 125.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', NULL, 2500.00),
(65, 2, 'Cambridge White Marble & Mango Round Dining Table', '', '', 'KD', 76.20, 119.40, 119.40, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 255.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', NULL, 5100.00),
(66, 2, 'Cambridge White Marble & Mango Rectangular Dining Table', '', '', 'KD', 76.20, 200.70, 99.10, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 320.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', NULL, 6400.00),
(67, 2, 'Cambridge White Marble & Mango Tall Buffet', '', '', 'KD', 119.40, 101.60, 49.50, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 308.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', 'product_2_10_1752804108.png', 6160.00),
(68, 2, 'Cambridge White Marble & Mango tv stand', '', '', 'KD', 45.70, 134.60, 55.90, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 198.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', 'product_2_11_1752804108.png', 3960.00),
(69, 2, 'Cambridge White Marble & Mango Buffet', '', '', 'KD', 78.70, 150.00, 49.50, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Natural', 20.00, 272.00, '', '2025-07-18 02:01:48', '2025-07-18 02:01:48', 'product_2_12_1752804108.png', 5440.00),
(100, 3, 'ORCHID DAY BED', '', '', 'Fix', 71.10, 203.20, 86.40, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 280.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', NULL, 5600.00),
(101, 3, 'ORCHID KING BED', '', '', 'KD', 122.00, 215.90, 19.30, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 470.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', NULL, 9400.00),
(102, 3, 'ORCHID ROUND SIDE TABLE', '', '', 'Fix', 45.70, 40.60, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 55.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', NULL, 1100.00),
(103, 3, 'ORCHID ROUND DINING TABLE', '', '', 'KD', 76.20, 119.40, 119.40, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 220.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', NULL, 4400.00),
(104, 3, 'ORCHID OVAL DINING TABLE', '', '', 'KD', 76.20, 203.20, 101.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 314.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', NULL, 6280.00),
(105, 3, 'ORCHID OPEN NIGHT STAND', '', '', 'Fix', 61.00, 81.30, 50.80, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 110.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', NULL, 2200.00),
(106, 3, 'ORCHID CONSOLE TABLE', '', '', 'KD', 76.20, 119.40, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 142.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', NULL, 2840.00),
(107, 3, 'ORCHID COFFEE TABLE', '', '', 'Fix', 40.60, 101.60, 50.80, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 94.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', NULL, 1880.00),
(108, 3, 'ORCHID 6 DRAWER DRESSER', '', '', 'Fix', 83.80, 152.40, 50.80, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 300.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', NULL, 6000.00),
(109, 3, 'ORCHID 5 DRAWER DRESSER', '', '', 'Fix', 114.30, 100.00, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 240.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', NULL, 4800.00),
(110, 3, 'ORCHID 2 DOOR 3 DRAWER SIDEBOARD', '', '', 'Fix', 81.30, 182.90, 50.80, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 281.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', 'product_3_10_1752808118.png', 5620.00),
(111, 3, 'ORCHID 2 DOOR 1 DRAWER LOW MEDIA CONSOLE', '', '', 'Fix', 45.70, 182.90, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, '', 'Light Oak', 20.00, 184.00, '', '2025-07-18 03:08:38', '2025-07-18 03:08:38', 'product_3_11_1752808118.png', 3680.00),
(139, 5, 'Sofa Chair', 'NA', '', 'Fix', 31.00, 30.50, 29.50, 33.00, 32.50, 31.50, 0.034, 'Oak', 1, '', 'Same as picture', 120.00, 220.00, '', '2025-07-25 07:56:14', '2025-07-25 07:56:14', 'product_5_0_1753430174.png', 26400.00),
(140, 4, 'CAMBRIDGE WHITE MARBLE AND MANGO TALL BUFFET', '', NULL, 'KD', 80.00, 111.50, 38.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 162.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', 'prod_4_1_1753784303.jpg', 3240.00),
(141, 4, 'Cambridge White Marble & Mango 5 Drawer Chest', '', NULL, 'Fix', 119.90, 51.10, 40.40, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 227.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', 'prod_4_2_1753784303.jpg', 4540.00),
(142, 4, 'Cambridge White Marble & Mango 3 Drawer Dresser', '', NULL, 'KD', 85.60, 91.90, 45.50, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 218.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', '', 4360.00),
(143, 4, 'Cambridge White Marble & Mango 2 Drawer Nightstand', '', NULL, 'KD', 57.40, 61.00, 42.90, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 118.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', '', 2360.00),
(144, 4, 'Cambridge White Marble & Mango Dresser', '', NULL, 'KD', 85.60, 152.40, 45.50, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 340.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', '', 6800.00),
(145, 4, 'Cambridge White Marble & Mango Round End Table', '', NULL, 'Fix', 45.70, 40.60, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 60.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', '', 1200.00),
(146, 4, 'Cambridge White Marble & Mango Round Coffee Table', '', NULL, 'Fix', 40.60, 80.00, 80.00, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 135.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', '', 2700.00),
(147, 4, 'Cambridge White Marble & Mango Console Table', '', NULL, 'KD', 76.20, 119.40, 40.60, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 125.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', '', 2500.00),
(148, 4, 'Cambridge White Marble & Mango Round Dining Table', '', NULL, 'KD', 76.20, 119.40, 119.40, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 255.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', '', 5100.00),
(149, 4, 'Cambridge White Marble & Mango Rectangular Dining Table', '', NULL, 'KD', 76.20, 200.70, 99.10, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 320.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', 'product_4_9_1752809427.png', 6400.00),
(150, 4, 'Cambridge White Marble & Mango Tall Buffet', '', NULL, 'KD', 119.40, 101.60, 49.50, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 308.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', 'product_4_10_1752809427.png', 6160.00),
(151, 4, 'Cambridge White Marble & Mango tv stand', '', NULL, 'KD', 45.70, 134.60, 55.90, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 198.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', 'product_4_11_1752809427.png', 3960.00),
(152, 4, 'Cambridge White Marble & Mango Buffet', '', NULL, 'KD', 78.70, 150.00, 49.50, 0.00, 0.00, 0.00, 0.000, 'Mango', 1, NULL, NULL, 20.00, 272.00, '', '2025-07-29 10:18:23', '2025-07-29 10:18:23', 'product_4_12_1752809427.png', 5440.00),
(373, 24, 'Senegal Century Leather Sofa', 'SenegalSofa-1 Purewood', NULL, 'Fix', 96.00, 68.00, 241.00, 104.00, 76.00, 249.00, 1.968, 'Wood,Leather', 1, NULL, NULL, 10.00, 544.00, '', '2025-08-01 06:43:09', '2025-08-01 06:43:09', '', 5440.00),
(374, 24, 'Olifants Jordan Leather Sofa', 'OlifantsJordansofa-2 Purewood', NULL, 'KD', 85.00, 71.00, 212.00, 93.00, 79.00, 220.00, 1.616, 'Metal, Leather', 1, NULL, NULL, 10.00, 459.00, '', '2025-08-01 06:43:09', '2025-08-01 06:43:09', '', 4590.00),
(375, 24, 'Nene Leather Chesterfield Sofa', 'NeneChesterfieldsofa-3 Purewood', NULL, 'Fix', 93.00, 76.00, 226.00, 101.00, 84.00, 234.00, 1.985, 'Wood,Leather', 1, NULL, NULL, 10.00, 494.00, '', '2025-08-01 06:43:09', '2025-08-01 06:43:09', '', 4940.00),
(376, 24, 'Niger Arm Chair', 'NigerarmChair-4 Purewood', NULL, 'Fix', 94.00, 94.00, 65.00, 100.00, 100.00, 71.00, 0.710, 'Metal,Leather', 1, NULL, NULL, 10.00, 140.00, '', '2025-08-01 06:43:09', '2025-08-01 06:43:09', '', 1400.00),
(377, 24, 'Ural Joy Leather Chair', 'UralJoyChair-5 Purewood', NULL, 'Fix', 57.00, 48.00, 58.00, 63.00, 54.00, 64.00, 0.218, 'Wood,Leather', 1, NULL, NULL, 10.00, 110.00, '', '2025-08-01 06:43:09', '2025-08-01 06:43:09', '', 1100.00),
(378, 24, 'Old World Dining Table Top', 'Oldworlddiningtabletop-6 Purewood', NULL, 'KD', 3.50, 99.00, 208.00, 12.00, 109.00, 218.00, 0.285, 'wood', 1, NULL, NULL, 10.00, 423.00, '', '2025-08-01 06:43:09', '2025-08-01 06:43:09', '', 4230.00),
(385, 26, 'Product A', 'P-001', NULL, 'Yes', 19.00, 10.00, 17.00, 7.00, 21.00, 26.00, 0.004, 'Oak', 1, NULL, NULL, 6.00, 18.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_1_1754034358.png', 108.00),
(386, 26, 'Product B', 'P-002', NULL, 'No', 20.00, 11.00, 18.00, 8.00, 22.00, 27.00, 0.005, 'Marble', 2, NULL, NULL, 7.00, 19.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_2_1754034358.png', 133.00),
(387, 26, 'Product A', 'P-003', NULL, 'Yes', 21.00, 12.00, 19.00, 9.00, 23.00, 28.00, 0.006, 'Oak', 3, NULL, NULL, 8.00, 20.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_3_1754034358.png', 160.00),
(388, 26, 'Product B', 'P-004', NULL, 'No', 22.00, 13.00, 20.00, 10.00, 24.00, 29.00, 0.007, 'Marble', 4, NULL, NULL, 9.00, 21.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_4_1754034358.png', 189.00),
(389, 26, 'Product A', 'P-005', NULL, 'Yes', 23.00, 14.00, 21.00, 11.00, 25.00, 30.00, 0.008, 'Oak', 5, NULL, NULL, 10.00, 22.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_5_1754034358.png', 220.00),
(390, 26, 'Product B', 'P-006', NULL, 'No', 24.00, 15.00, 22.00, 12.00, 26.00, 31.00, 0.010, 'Marble', 6, NULL, NULL, 11.00, 23.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_6_1754034358.png', 253.00),
(391, 26, 'Product A', 'P-007', NULL, 'Yes', 25.00, 16.00, 23.00, 13.00, 27.00, 32.00, 0.011, 'Oak', 7, NULL, NULL, 12.00, 24.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_7_1754034358.png', 288.00),
(392, 26, 'Product B', 'P-008', NULL, 'No', 26.00, 17.00, 24.00, 14.00, 28.00, 33.00, 0.013, 'Marble', 8, NULL, NULL, 13.00, 25.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_8_1754034358.png', 325.00),
(393, 26, 'Product A', 'P-009', NULL, 'Yes', 27.00, 18.00, 25.00, 15.00, 29.00, 34.00, 0.015, 'Oak', 9, NULL, NULL, 14.00, 26.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_9_1754034358.png', 364.00),
(394, 26, 'Product B', 'P-010', NULL, 'No', 28.00, 19.00, 26.00, 16.00, 30.00, 35.00, 0.017, 'Marble', 10, NULL, NULL, 15.00, 27.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_10_1754034358.png', 405.00),
(395, 26, 'Product A', 'P-011', NULL, 'Yes', 29.00, 20.00, 27.00, 17.00, 31.00, 36.00, 0.019, 'Oak', 11, NULL, NULL, 16.00, 28.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_11_1754034358.png', 448.00),
(396, 26, 'Product B', 'P-012', NULL, 'No', 30.00, 21.00, 28.00, 18.00, 32.00, 37.00, 0.021, 'Marble', 12, NULL, NULL, 17.00, 29.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_12_1754034358.png', 493.00),
(397, 26, 'Product A', 'P-013', NULL, 'Yes', 31.00, 22.00, 29.00, 19.00, 33.00, 38.00, 0.024, 'Oak', 13, NULL, NULL, 18.00, 30.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_13_1754034358.png', 540.00),
(398, 26, 'Product B', 'P-014', NULL, 'No', 32.00, 23.00, 30.00, 20.00, 34.00, 39.00, 0.027, 'Marble', 14, NULL, NULL, 19.00, 31.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_14_1754034358.png', 589.00),
(399, 26, 'Product A', 'P-015', NULL, 'Yes', 33.00, 24.00, 31.00, 21.00, 35.00, 40.00, 0.029, 'Oak', 15, NULL, NULL, 20.00, 32.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_15_1754034358.png', 640.00),
(400, 26, 'Product B', 'P-016', NULL, 'No', 34.00, 25.00, 32.00, 22.00, 36.00, 41.00, 0.033, 'Marble', 16, NULL, NULL, 21.00, 33.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_16_1754034358.png', 693.00),
(401, 26, 'Product A', 'P-017', NULL, 'Yes', 35.00, 26.00, 33.00, 23.00, 37.00, 42.00, 0.036, 'Oak', 17, NULL, NULL, 22.00, 34.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_17_1754034358.png', 748.00),
(402, 26, 'Product B', 'P-018', NULL, 'No', 36.00, 27.00, 34.00, 24.00, 38.00, 43.00, 0.039, 'Marble', 18, NULL, NULL, 23.00, 35.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_18_1754034358.png', 805.00),
(403, 26, 'Product A', 'P-019', NULL, 'Yes', 37.00, 28.00, 35.00, 25.00, 39.00, 44.00, 0.043, 'Oak', 19, NULL, NULL, 24.00, 36.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_19_1754034358.png', 864.00),
(404, 26, 'Product B', 'P-020', NULL, 'No', 38.00, 29.00, 36.00, 26.00, 40.00, 45.00, 0.047, 'Marble', 20, NULL, NULL, 25.00, 37.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_20_1754034358.png', 925.00),
(405, 26, 'Product A', 'P-021', NULL, 'Yes', 39.00, 30.00, 37.00, 27.00, 41.00, 46.00, 0.051, 'Oak', 21, NULL, NULL, 26.00, 38.00, 'Handle with care', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_21_1754034358.png', 988.00),
(406, 26, 'Product B', 'P-022', NULL, 'No', 40.00, 31.00, 38.00, 28.00, 42.00, 47.00, 0.055, 'Marble', 22, NULL, NULL, 27.00, 39.00, 'Fragile', '2025-08-01 07:45:58', '2025-08-01 07:45:58', 'prod_26_22_1754034358.png', 1053.00),
(407, 23, 'Senegal Century Leather Sofa', 'SenegalSofa-1 Purewood', NULL, 'Fix', 96.00, 241.00, 68.00, 104.00, 249.00, 76.00, 1.968, 'Wood,Leather', 1, NULL, NULL, 10.00, 544.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_1_1754034849.png', 5440.00),
(408, 23, 'Olifants Jordan Leather Sofa', 'OlifantsJordansofa-2 Purewood', NULL, 'KD', 85.00, 212.00, 71.00, 93.00, 220.00, 79.00, 1.616, 'Metal, Leather', 1, NULL, NULL, 10.00, 459.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_2_1754034849.png', 4590.00),
(409, 23, 'Nene Leather Chesterfield Sofa', 'NeneChesterfieldsofa-3 Purewood', NULL, 'Fix', 93.00, 226.00, 76.00, 101.00, 234.00, 84.00, 1.985, 'Wood,Leather', 1, NULL, NULL, 10.00, 494.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_3_1754034849.png', 4940.00),
(410, 23, 'Niger Arm Chair', 'NigerarmChair-4 Purewood', NULL, 'Fix', 94.00, 65.00, 94.00, 100.00, 71.00, 100.00, 0.710, 'Metal,Leather', 1, NULL, NULL, 10.00, 140.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_4_1754034849.png', 1400.00),
(411, 23, 'Ural Joy Leather Chair', 'UralJoyChair-5 Purewood', NULL, 'Fix', 57.00, 58.00, 48.00, 63.00, 64.00, 54.00, 0.218, 'Wood,Leather', 1, NULL, NULL, 10.00, 110.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_5_1754034849.png', 1100.00),
(412, 23, 'Old World Dining Table Top', 'Oldworlddiningtabletop-6 Purewood', NULL, 'KD', 3.50, 208.00, 99.00, 12.00, 218.00, 109.00, 0.285, 'wood', 1, NULL, NULL, 10.00, 423.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_6_1754034849.png', 4230.00),
(413, 23, 'Old World Dining Table base', 'Oldworlddiningtablebase-7 Purewood', NULL, 'KD', 74.00, 185.00, 35.00, 76.00, 193.00, 40.00, 0.587, 'Iron', 1, NULL, NULL, 10.00, 0.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_7_1754034849.png', 0.00),
(414, 23, 'Falcon Dining Table Top', 'FalconDiningtabletop-8 Purewood', NULL, 'KD', 3.50, 213.00, 101.00, 12.00, 221.00, 109.00, 0.289, 'wood', 1, NULL, NULL, 10.00, 450.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_8_1754034849.png', 4500.00),
(415, 23, 'Falcon Dining Table Base', 'FalconDiningtablebase-9 Purewood', NULL, 'KD', 74.00, 150.00, 90.00, 76.00, 160.00, 99.00, 1.204, 'Iron', 1, NULL, NULL, 10.00, 0.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_9_1754034849.png', 0.00),
(416, 23, 'Turnstone Dining Table Top', 'Truestonediningtabletop-10 Purewood', NULL, 'KD', 2.50, 213.00, 101.00, 12.00, 221.00, 109.00, 0.289, 'wood', 1, NULL, NULL, 10.00, 402.50, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_10_1754034849.png', 4025.00),
(417, 23, 'Turnstone Dining Table base', 'Truestonediningtablebase-11 Purewood', NULL, 'KD', 74.00, 150.00, 80.00, 76.00, 160.00, 95.00, 1.155, 'Iron', 1, NULL, NULL, 10.00, 0.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_11_1754034849.png', 0.00),
(418, 23, 'Hobby Dining Table top', 'HobbyDiningTabletop-12 Purewood', NULL, 'KD', 3.50, 101.00, 101.00, 12.00, 109.00, 109.00, 0.143, 'wood', 1, NULL, NULL, 10.00, 346.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_12_1754034849.png', 3460.00),
(419, 23, 'Hobby Dining Table base', 'HobbyDiningTablebase-13 Purewood', NULL, 'KD', 74.00, 50.00, 50.00, 76.00, 55.00, 55.00, 0.230, 'Iron', 1, NULL, NULL, 10.00, 0.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_13_1754034849.png', 0.00),
(420, 23, 'Kookaburra Dining Table', 'KookaburraDiningTable-14 Purewood', NULL, 'KD', 76.00, 243.00, 204.00, 26.00, 251.00, 112.00, 0.731, 'Iron , Wood', 1, NULL, NULL, 10.00, 365.00, '', '2025-08-01 07:54:09', '2025-08-01 07:54:09', 'prod_23_14_1754034849.png', 3650.00);

-- --------------------------------------------------------

--
-- Table structure for table `quotation_status`
--

CREATE TABLE `quotation_status` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `status_text` varchar(255) NOT NULL,
  `status_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotation_status`
--

INSERT INTO `quotation_status` (`id`, `quotation_id`, `status_text`, `status_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'Quotation created', '2025-07-17', '2025-07-17 09:09:37', '2025-07-17 09:09:37'),
(2, 1, 'Quotation updated', '2025-07-17', '2025-07-17 09:09:57', '2025-07-17 09:09:57'),
(3, 1, 'Quotation updated', '2025-07-17', '2025-07-17 09:18:09', '2025-07-17 09:18:09'),
(4, 1, 'Quotation updated', '2025-07-17', '2025-07-17 09:18:24', '2025-07-17 09:18:24'),
(5, 1, 'Quotation updated', '2025-07-17', '2025-07-17 09:51:56', '2025-07-17 09:51:56'),
(6, 1, 'Quotation updated', '2025-07-17', '2025-07-17 09:52:43', '2025-07-17 09:52:43'),
(7, 2, 'Quotation created', '2025-07-18', '2025-07-18 01:37:23', '2025-07-18 01:37:23'),
(8, 2, 'Quotation updated', '2025-07-18', '2025-07-18 01:37:37', '2025-07-18 01:37:37'),
(9, 2, 'Quotation updated', '2025-07-18', '2025-07-18 01:48:37', '2025-07-18 01:48:37'),
(10, 2, 'Quotation updated', '2025-07-18', '2025-07-18 01:52:16', '2025-07-18 01:52:16'),
(11, 2, 'Quotation updated', '2025-07-18', '2025-07-18 01:54:41', '2025-07-18 01:54:41'),
(12, 2, 'Quotation updated', '2025-07-18', '2025-07-18 02:01:48', '2025-07-18 02:01:48'),
(13, 3, 'Quotation created', '2025-07-18', '2025-07-18 02:53:33', '2025-07-18 02:53:33'),
(14, 3, 'Quotation updated', '2025-07-18', '2025-07-18 02:57:40', '2025-07-18 02:57:40'),
(15, 3, 'Quotation updated', '2025-07-18', '2025-07-18 03:04:49', '2025-07-18 03:04:49'),
(16, 3, 'Quotation updated', '2025-07-18', '2025-07-18 03:05:06', '2025-07-18 03:05:06'),
(17, 3, 'Quotation updated', '2025-07-18', '2025-07-18 03:08:38', '2025-07-18 03:08:38'),
(18, 4, 'Quotation created', '2025-07-18', '2025-07-18 03:19:18', '2025-07-18 03:19:18'),
(19, 4, 'Quotation updated', '2025-07-18', '2025-07-18 03:24:47', '2025-07-18 03:24:47'),
(20, 4, 'Quotation updated', '2025-07-18', '2025-07-18 03:30:27', '2025-07-18 03:30:27'),
(21, 5, 'Quotation created', '2025-07-25', '2025-07-25 07:56:14', '2025-07-25 07:56:14');

-- --------------------------------------------------------

--
-- Table structure for table `sell_order`
--

CREATE TABLE `sell_order` (
  `id` int(11) NOT NULL,
  `sell_order_number` varchar(50) NOT NULL,
  `po_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `jci_created` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sell_order`
--

INSERT INTO `sell_order` (`id`, `sell_order_number`, `po_id`, `created_at`, `updated_at`, `jci_created`) VALUES
(1, 'SALE-2025-0001', 1, '2025-07-11 06:54:39', '2025-07-11 06:55:30', 1),
(2, 'SALE-2025-0002', 2, '2025-07-11 11:01:13', '2025-07-11 11:03:42', 1),
(3, 'SALE-2025-0003', 3, '2025-07-16 11:32:13', '2025-07-17 10:03:55', 1),
(4, 'SALE-2025-0004', 4, '2025-07-21 10:16:18', '2025-07-21 10:24:09', 1),
(5, 'SALE-2025-0005', 5, '2025-07-30 10:44:19', '2025-07-30 10:46:08', 1),
(6, 'SALE-2025-0006', 6, '2025-07-31 09:17:12', '2025-07-31 09:18:37', 1),
(7, 'SALE-2025-0007', 7, '2025-07-31 11:37:22', '2025-07-31 11:40:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `status_text` varchar(255) NOT NULL,
  `status_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `invoice_amount` decimal(15,2) NOT NULL,
  `invoice_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_items`
--

CREATE TABLE `supplier_items` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_quantity` int(11) NOT NULL,
  `item_price` decimal(15,2) NOT NULL,
  `item_amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bom_factory`
--
ALTER TABLE `bom_factory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bom_main_id` (`bom_main_id`);

--
-- Indexes for table `bom_glow`
--
ALTER TABLE `bom_glow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bom_main_id` (`bom_main_id`);

--
-- Indexes for table `bom_hardware`
--
ALTER TABLE `bom_hardware`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bom_main_id` (`bom_main_id`);

--
-- Indexes for table `bom_labour`
--
ALTER TABLE `bom_labour`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bom_main_id` (`bom_main_id`);

--
-- Indexes for table `bom_main`
--
ALTER TABLE `bom_main`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bom_number` (`bom_number`);

--
-- Indexes for table `bom_margin`
--
ALTER TABLE `bom_margin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bom_main_id` (`bom_main_id`);

--
-- Indexes for table `bom_plynydf`
--
ALTER TABLE `bom_plynydf`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bom_main_id` (`bom_main_id`);

--
-- Indexes for table `bom_wood`
--
ALTER TABLE `bom_wood`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bom_main_id` (`bom_main_id`);

--
-- Indexes for table `jci_items`
--
ALTER TABLE `jci_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jci_id` (`jci_id`);

--
-- Indexes for table `jci_main`
--
ALTER TABLE `jci_main`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jci_number` (`jci_number`),
  ADD KEY `fk_jci_po` (`po_id`),
  ADD KEY `idx_jci_sell_order_number` (`sell_order_number`);

--
-- Indexes for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_details_payment_id` (`payment_id`);

--
-- Indexes for table `pi`
--
ALTER TABLE `pi`
  ADD PRIMARY KEY (`pi_id`),
  ADD UNIQUE KEY `pi_number` (`pi_number`),
  ADD UNIQUE KEY `unique_pi_number` (`pi_number`),
  ADD KEY `quotation_id` (`quotation_id`);

--
-- Indexes for table `po_items`
--
ALTER TABLE `po_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `po_main`
--
ALTER TABLE `po_main`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_po_sell_order_number` (`sell_order_number`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_main_id` (`purchase_main_id`);

--
-- Indexes for table `purchase_main`
--
ALTER TABLE `purchase_main`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`);

--
-- Indexes for table `quotation_products`
--
ALTER TABLE `quotation_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_id` (`quotation_id`);

--
-- Indexes for table `quotation_status`
--
ALTER TABLE `quotation_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_id` (`quotation_id`);

--
-- Indexes for table `sell_order`
--
ALTER TABLE `sell_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sell_order_number` (`sell_order_number`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `supplier_items`
--
ALTER TABLE `supplier_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bom_factory`
--
ALTER TABLE `bom_factory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `bom_glow`
--
ALTER TABLE `bom_glow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `bom_hardware`
--
ALTER TABLE `bom_hardware`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `bom_labour`
--
ALTER TABLE `bom_labour`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `bom_main`
--
ALTER TABLE `bom_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bom_margin`
--
ALTER TABLE `bom_margin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `bom_plynydf`
--
ALTER TABLE `bom_plynydf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bom_wood`
--
ALTER TABLE `bom_wood`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `jci_items`
--
ALTER TABLE `jci_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `jci_main`
--
ALTER TABLE `jci_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pi`
--
ALTER TABLE `pi`
  MODIFY `pi_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `po_main`
--
ALTER TABLE `po_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `purchase_main`
--
ALTER TABLE `purchase_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `quotation_products`
--
ALTER TABLE `quotation_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=421;

--
-- AUTO_INCREMENT for table `quotation_status`
--
ALTER TABLE `quotation_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `sell_order`
--
ALTER TABLE `sell_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_items`
--
ALTER TABLE `supplier_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jci_items`
--
ALTER TABLE `jci_items`
  ADD CONSTRAINT `jci_items_ibfk_1` FOREIGN KEY (`jci_id`) REFERENCES `jci_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jci_main`
--
ALTER TABLE `jci_main`
  ADD CONSTRAINT `fk_jci_po` FOREIGN KEY (`po_id`) REFERENCES `po_main` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD CONSTRAINT `job_cards_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD CONSTRAINT `fk_payment_details_payment_id` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pi`
--
ALTER TABLE `pi`
  ADD CONSTRAINT `pi_ibfk_1` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `po_items`
--
ALTER TABLE `po_items`
  ADD CONSTRAINT `po_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `po_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_main_id`) REFERENCES `purchase_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotations_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`);

--
-- Constraints for table `quotation_products`
--
ALTER TABLE `quotation_products`
  ADD CONSTRAINT `quotation_products_ibfk_1` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`);

--
-- Constraints for table `quotation_status`
--
ALTER TABLE `quotation_status`
  ADD CONSTRAINT `quotation_status_ibfk_1` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sell_order`
--
ALTER TABLE `sell_order`
  ADD CONSTRAINT `sell_order_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `po_main` (`id`);

--
-- Constraints for table `status`
--
ALTER TABLE `status`
  ADD CONSTRAINT `status_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_items`
--
ALTER TABLE `supplier_items`
  ADD CONSTRAINT `supplier_items_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
