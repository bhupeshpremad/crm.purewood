-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2025 at 05:21 AM
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
(7, 1, 6344.44, 15.00, 951.67, 7296.11),
(15, 2, 30786.67, 15.00, 4618.00, 35404.67),
(22, 3, 721.70, 15.00, 108.26, 829.96);

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
(1, 1, 'Favicol', 0.500, 100.00, 50.00),
(2, 2, 'test', 0.200, 100.00, 20.00),
(3, 3, '5', 5.000, 5.00, 25.00);

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
(1, 1, 'test hardware', 10, 10.00, 100.00),
(2, 2, 'test ', 10, 20.00, 200.00),
(3, 3, '5', 5, 5.00, 25.00);

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
(1, 1, 'Test Labour', 5, 100.00, 500.00),
(2, 2, 'test', 10, 100.00, 1000.00),
(3, 3, '5', 5, 5.00, 25.00);

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
(1, 'BOM-2025-0001', 'Test', 'test', '01', '2025-07-01', '2025-07-01', '2025-07-11 04:57:25', '2025-07-11 05:00:39', NULL, NULL, NULL, 7344.44, 1),
(2, 'BOM-2025-0002', 'Test 3', 'Test 3', 'Test 3', '2025-07-12', '2025-07-12', '2025-07-12 02:38:47', '2025-07-12 02:42:04', NULL, NULL, NULL, 36436.34, 1),
(3, 'BOM-2025-0003', '5', '5', '5', '2025-07-17', '2025-07-17', '2025-07-17 09:57:09', '2025-07-17 09:57:48', NULL, NULL, NULL, 731.70, 0);

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
(7, 1, 6844.44, 15.00, 500.00, 7344.44),
(15, 2, 35404.67, 15.00, 5310.70, 40715.37),
(22, 3, 726.70, 15.00, 5.00, 731.70);

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
(1, 1, 5, 10.00, 10.00, 10.00, 5000.00),
(2, 2, 3, 20.00, 20.00, 20.00, 24000.00),
(3, 3, 5, 5.00, 5.00, 5.00, 625.00);

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
(1, 1, 'Mango', 10.00, 0.83, 10.00, 10, 10.00, 69.44, 694.44),
(3, 2, 'Mango', 20.00, 1.67, 20.00, 10, 10.00, 556.67, 5566.67),
(4, 3, 'Babool', 5.00, 0.42, 5.00, 5, 5.00, 4.34, 21.70);

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
(2, 4, 'JOB-2025-0001-1', 1, 'Fluting Round Table', 'F-FLU-12', 10.00, 100.00, 10, 1000.00, '2025-07-31', '2025-07-11', 'Contracture', 'test'),
(3, 5, 'JOB-2025-0002-1', 2, 'test pro 4', 'test pro 3', 10.00, 200.00, 10, 2000.00, '2025-07-01', '2025-07-02', 'Contracture', 'test con');

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
(4, 'JCI-2025-0001', 3, 1, 'Contracture', 'test', '2025-07-11', '2025-07-11 05:02:57', '2025-07-11 05:47:02', '\r\n                                    SALE-2025-00', 1, 1),
(5, 'JCI-2025-0002', 4, 2, 'Contracture', 'test pro02', '2025-07-12', '2025-07-12 02:42:04', '2025-07-12 02:45:50', '\r\n                                    SALE-2025-00', 1, 1);

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
(1, 'LEAD-2025-0001', '2025-07-01', 'Website', 'Test', 'Test', '090909', 'test@gmail.com', 'India', 'Rajasthan', 'Jaipur', 'new', 1, 'active', '2025-07-11 12:39:02', '2025-07-11 17:13:48'),
(2, 'LEAD-2025-0002', '2025-07-01', 'Website', 'user test', 'user test', '9898989895', 'testuser@gmail.com', 'India', 'Himachal Pradesh', 'Gagret', 'new', 0, 'active', '2025-07-11 14:57:19', '2025-07-11 14:57:56'),
(3, 'LEAD-2025-0003', '2025-07-02', 'hduisiah', 'test5', 'tests6', '98989889', 'testw@gmail.cpm', 'Ivory Coast', 'Abidjan', 'Abidjan', 'new', 0, 'active', '2025-07-11 17:13:24', '2025-07-11 17:13:24');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `jci_number`, `po_number`, `sell_order_number`, `created_at`, `updated_at`, `total_amount`) VALUES
(1, 'JCI-2025-0001', 'PO-0001', 'SALE-2025-0001', '2025-07-11 05:47:02', '2025-07-11 05:47:02', 0.00),
(2, 'JCI-2025-0002', 'testpo-001', 'SALE-2025-0002', '2025-07-12 02:45:50', '2025-07-12 02:45:50', 0.00);

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
(1, 1, 'JCI-2025-0001', 'Cheque', '100', '1000', 1000.00, '0000-00-00', '2025-07-01', 'Job Card', 1000.00, '2025-07-11 05:47:02', '2025-07-11 05:47:02'),
(2, 1, '', 'RTGS', '101', '16', 15.50, '2025-07-01', '2025-07-16', 'Supplier', 15.50, '2025-07-11 05:47:02', '2025-07-11 05:47:02'),
(3, 1, '', 'RTGS', '102', '10', 10.00, '2025-07-23', '2025-07-29', 'Supplier', 10.00, '2025-07-11 05:47:02', '2025-07-11 05:47:02'),
(4, 2, 'JCI-2025-0002', 'RTGS', '200', '2000', 2000.00, '0000-00-00', '2025-07-02', 'Job Card', 2000.00, '2025-07-12 02:45:50', '2025-07-12 02:45:50'),
(5, 2, '', 'RTGS', '200', '11', 10.20, '2025-07-01', '2025-07-22', 'Supplier', 10.20, '2025-07-12 02:45:50', '2025-07-12 02:45:50'),
(6, 2, '', 'RTGS', '200', '13', 13.00, '2025-07-03', '2025-07-02', 'Supplier', 13.00, '2025-07-12 02:45:50', '2025-07-12 02:45:50');

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

--
-- Dumping data for table `pi`
--

INSERT INTO `pi` (`pi_id`, `quotation_id`, `quotation_number`, `pi_number`, `status`, `inspection`, `date_of_pi_raised`, `sample_approval_date`, `detailed_seller_address`, `created_at`, `updated_at`) VALUES
(1, 1, 'QUOTE-2025-00001', 'PI-2025-001', 'Active', NULL, '2025-07-11', NULL, NULL, '2025-07-11 16:10:33', '2025-07-11 16:10:33'),
(2, 2, 'QUOTE-2025-00002', 'PI-2025-002', 'Active', NULL, '2025-07-11', NULL, NULL, '2025-07-11 17:15:17', '2025-07-11 17:15:17');

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
(1, 3, 'F-FLU-12', 'Fluting Round Table', 10.00, '', 200.00, 2000.00, '', '2025-07-11 10:29:35', '2025-07-11 10:29:35'),
(2, 4, 'test pro 3', 'test pro 4', 10.00, '', 100.00, 1000.00, '6871cb3eafcb2.png', '2025-07-12 08:11:02', '2025-07-12 08:11:02'),
(3, 5, '5', '5', 5.00, '', 5.00, 25.00, '6878c93666f15.png', '2025-07-17 15:28:14', '2025-07-17 15:28:14');

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
(1, 'APL-4017', 'APL', 'RAJENDRRA', '2025-07-01', '2025-07-21', 1, 'Locked', 1, '2025-07-10 11:25:22', '2025-07-10 17:16:14', 'SALE-2025-0001', '', 1),
(2, 'PO-0001', 'test', 'test', '2025-07-01', '2025-07-20', 2, 'Locked', 1, '2025-07-10 20:12:07', '2025-07-11 03:27:13', 'SALE-2025-0002', '', 1),
(3, 'PO-0001', 'APL', 'RAJENDRRA', '2025-07-11', '2025-07-31', 1, 'Locked', 1, '2025-07-11 04:59:35', '2025-07-11 05:00:39', 'SALE-2025-0001', '', 1),
(4, 'testpo-001', 'test 4', 'test 4', '2025-07-12', '2025-08-27', 2, 'Locked', 1, '2025-07-12 02:41:02', '2025-07-12 02:42:04', 'SALE-2025-0002', '', 1),
(5, '5', '5', '5', '2025-07-17', '2025-08-06', 3, 'Locked', 1, '2025-07-17 09:58:14', '2025-07-17 09:58:28', 'SALE-2025-0003', '', 0);

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
(1, 2, 'test 1', 'Glow', 'Favicol', 'JOB-2025-0001-1', 0.500, 0.50, 0.50, '2025-07-11 05:03:52', '2025-07-11 05:04:44', '2025-07-01', 'invoice-1', 100.00, 'invoice_68709b6cce07b_0eecc8ff-8f36-4160-81c7-55c8171fe829.png', 'builty-1', 'builty_68709b6cce08b_WhatsApp Image 2025-06-30 at 18.32.04_53c96b08.jpg'),
(2, 2, 'test 2', 'Hardware', 'test hardware', 'JOB-2025-0001-1', 10.000, 10.00, 10.00, '2025-07-11 05:03:52', '2025-07-11 05:05:12', '2025-07-23', 'invoice-2', 200.00, 'invoice_68709b88baff2_WhatsApp Image 2025-06-30 at 18.17.29_9ae25cb8.jpg', 'builty-2', 'builty_68709b88bb01f_WhatsApp Image 2025-06-30 at 18.32.04_53c96b08.jpg'),
(3, 2, 'test 1', 'Plynydf', 'Plynydf', 'JOB-2025-0001-1', 5.000, 5.00, 5.00, '2025-07-11 05:03:52', '2025-07-11 05:05:35', '2025-07-01', 'invoice-1', 100.00, 'invoice_68709b9f39fc8_200fa4ba-8222-4f92-b043-a0f48d82abf9.png', 'builty-1', 'builty_68709b9f39fd7_WhatsApp Image 2025-06-30 at 18.32.04_53c96b08.jpg'),
(4, 2, 'test 1', 'Wood', 'Mango', 'JOB-2025-0001-1', 10.000, 10.00, 10.00, '2025-07-11 05:03:52', '2025-07-11 05:05:55', '2025-07-01', 'invoice-1', 100.00, 'invoice_68709bb34882b_200fa4ba-8222-4f92-b043-a0f48d82abf9.png', 'builty-1', 'builty_68709bb348843_WhatsApp Image 2025-06-30 at 18.32.04_53c96b08.jpg'),
(5, 1, 'Supplier 1', 'Glow', 'test', 'JOB-2025-0002-1', 0.200, 0.20, 0.20, '2025-07-12 02:42:53', '2025-07-12 02:43:19', '2025-07-01', '100', 100.00, 'invoice_6871cbc75b767_WhatsApp Image 2025-06-30 at 18.32.04_53c96b08.jpg', '100', 'builty_6871cbc75b771_WhatsApp Image 2025-06-30 at 18.32.04_53c96b08.jpg'),
(6, 1, 'Supplier 1', 'Hardware', 'test ', 'JOB-2025-0002-1', 10.000, 10.00, 10.00, '2025-07-12 02:42:53', '2025-07-12 02:43:59', '2025-07-01', '100', 100.00, 'invoice_6871cbef67bb6_WhatsApp Image 2025-06-30 at 18.32.04_53c96b08.jpg', '100', 'builty_6871cbef67bc8_WhatsApp Image 2025-06-30 at 18.32.04_53c96b08.jpg'),
(7, 1, 'Supplier 2', 'Plynydf', 'Plynydf', 'JOB-2025-0002-1', 3.000, 3.00, 3.00, '2025-07-12 02:42:53', '2025-07-12 02:44:20', '2025-07-03', '200', 200.00, 'invoice_6871cc0428847_WhatsApp Image 2025-06-30 at 18.17.29_9ae25cb8.jpg', '200', 'builty_6871cc0428852_0eecc8ff-8f36-4160-81c7-55c8171fe829.png'),
(8, 1, 'Supplier 2', 'Wood', 'Mango', 'JOB-2025-0002-1', 10.000, 10.00, 10.00, '2025-07-12 02:42:53', '2025-07-12 02:44:38', '2025-07-03', '200', 200.00, 'invoice_6871cc162bb2d_WhatsApp Image 2025-06-30 at 18.32.04_53c96b08.jpg', '200', 'builty_6871cc162bb3e_WhatsApp Image 2025-06-30 at 18.32.04_53c96b08.jpg');

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
(1, 'testpo-001', 'JCI-2025-0002', '\n                                    SALE-2025-00', 'BOM-2025-0002', '2025-07-11 03:38:58', '2025-07-12 02:42:53'),
(2, 'PO-0001', 'JCI-2025-0001', '\n                                    SALE-2025-00', 'BOM-2025-0001', '2025-07-11 05:03:52', '2025-07-11 05:03:52');

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
(1, 1, '2025-07-11', 'QUOTE-2025-00001', 'Test', 'test@gmail.com', '090909', '70-30', 'test', NULL, 1, 1, '2025-07-11 16:09:36', '2025-07-11 16:10:33'),
(2, 1, '2025-07-11', 'QUOTE-2025-00002', 'Test', 'test@gmail.com', '090909', '70-30%', '45 Days', NULL, 1, 1, '2025-07-11 17:14:55', '2025-07-11 17:15:17'),
(3, 1, '2025-07-17', 'QUOTE-2025-00003', 'Test', 'test@gmail.com', '090909', '70-30', 'test', NULL, 0, 0, '2025-07-17 09:01:11', '2025-07-17 09:01:11');

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
(1, 1, 'Test Chair', 'Item-100', '', 'Yes', 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 1.000, 'Mango', 1, '100', 'yes', 5.00, 100.00, 'No Comments', '2025-07-11 16:09:36', '2025-07-11 16:09:36', 'product_1_0_1752250176.png', 500.00),
(2, 2, 'gygy', 'yeh1', '', 'ye', 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 1.000, '100', 100, '100', '100', 100.00, 100.00, 'kklk', '2025-07-11 17:14:55', '2025-07-11 17:14:55', 'product_2_0_1752254095.png', 10000.00),
(4, 3, 'test2', '100', '', '100', 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 1.000, '100', 100, '100', '100', 100.00, 100.00, '100', '2025-07-17 09:08:10', '2025-07-17 09:08:10', 'product_3_0_1752743290.jpg', 10000.00);

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
(1, 1, 'Quotation created', '2025-07-11', '2025-07-11 16:09:36', '2025-07-11 16:09:36'),
(2, 1, 'yes', '2025-07-10', '2025-07-11 16:10:00', '2025-07-11 16:10:00'),
(3, 2, 'Quotation created', '2025-07-11', '2025-07-11 17:14:55', '2025-07-11 17:14:55'),
(4, 3, 'Quotation created', '2025-07-17', '2025-07-17 09:01:11', '2025-07-17 09:01:11'),
(5, 3, 'Quotation updated', '2025-07-17', '2025-07-17 09:08:10', '2025-07-17 09:08:10');

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
(1, 'SALE-2025-0001', 3, '2025-07-11 10:29:44', '2025-07-11 10:30:39', 1),
(2, 'SALE-2025-0002', 4, '2025-07-12 08:11:09', '2025-07-12 08:12:04', 1),
(3, 'SALE-2025-0003', 5, '2025-07-17 15:28:25', '2025-07-17 15:28:25', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `bom_glow`
--
ALTER TABLE `bom_glow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bom_hardware`
--
ALTER TABLE `bom_hardware`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bom_labour`
--
ALTER TABLE `bom_labour`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bom_main`
--
ALTER TABLE `bom_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bom_margin`
--
ALTER TABLE `bom_margin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `bom_plynydf`
--
ALTER TABLE `bom_plynydf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bom_wood`
--
ALTER TABLE `bom_wood`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jci_items`
--
ALTER TABLE `jci_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jci_main`
--
ALTER TABLE `jci_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pi`
--
ALTER TABLE `pi`
  MODIFY `pi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `po_main`
--
ALTER TABLE `po_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `purchase_main`
--
ALTER TABLE `purchase_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quotation_products`
--
ALTER TABLE `quotation_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quotation_status`
--
ALTER TABLE `quotation_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sell_order`
--
ALTER TABLE `sell_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
