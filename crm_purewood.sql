-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2025 at 01:44 PM
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
-- Table structure for table `bom_items`
--

CREATE TABLE `bom_items` (
  `id` int(11) NOT NULL,
  `bom_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_code` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bom_items`
--

INSERT INTO `bom_items` (`id`, `bom_id`, `product_name`, `product_code`, `quantity`, `unit`, `price`, `total_amount`) VALUES
(2, 5, 'item', '01', 10.00, '', 10.00, 100.00);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bom_main`
--

INSERT INTO `bom_main` (`id`, `bom_number`, `costing_sheet_number`, `client_name`, `prepared_by`, `order_date`, `delivery_date`, `created_at`, `updated_at`) VALUES
(5, 'BOM-2025-0001', 'sheet-001', 'bhupesh2', 'bhupesh2', '2025-06-24', '2025-06-24', '2025-06-24 10:18:12', '2025-06-24 10:18:24');

-- --------------------------------------------------------

--
-- Table structure for table `jci_items`
--

CREATE TABLE `jci_items` (
  `id` int(11) NOT NULL,
  `jci_id` int(11) NOT NULL,
  `contracture_name` varchar(255) NOT NULL,
  `labour_cost` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jci_main`
--

CREATE TABLE `jci_main` (
  `id` int(11) NOT NULL,
  `jci_number` varchar(255) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `jci_type` enum('Contracture','In-House') NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `jci_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sell_order_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'LEAD-2025-0001', '2025-05-01', 'Live', '90', '90', '90', 'a.90@gmail.com', 'Albania', 'Berat District', 'Bashkia Berat', 'new', 1, 'active', '2025-05-22 13:00:05', '2025-05-22 13:00:21'),
(2, 'LEAD-2025-0002', '2025-05-29', 'Email ', 'Test Company', 'Test Name', '0987654321', 'premad.bhupesh@gmail.com', 'United States', 'Alaska', 'Akutan', 'new', 1, 'active', '2025-05-29 05:39:03', '2025-05-29 05:45:08'),
(3, 'LEAD-2025-0003', '2025-06-06', 'io', 'ui', 'ui', 'ui', 'ui@gmail.com', 'Australia', 'Northern Territory', 'Central Desert', 'new', 1, 'active', '2025-06-14 08:21:15', '2025-06-14 08:21:20'),
(4, 'LEAD-2025-0004', '2025-06-12', 'Email', 'Test', 'TEst NAme', '090909', 'test@gmail.com', 'India', 'Rajasthan', 'Jodhpur', 'new', 1, 'active', '2025-06-14 11:22:53', '2025-06-14 11:23:21'),
(5, 'LEAD-2025-0005', '2025-06-04', 'Online', 'test2', 'test2', '0909090909', 'test2@gmail.com', 'India', 'Rajasthan', 'Jaipur', 'new', 0, 'active', '2025-06-21 09:12:01', '2025-06-21 09:17:35');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `pon_number` varchar(255) DEFAULT NULL,
  `po_amt` decimal(15,2) NOT NULL,
  `son_number` varchar(255) NOT NULL,
  `soa_number` decimal(15,2) NOT NULL,
  `jci_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `po_id` int(11) DEFAULT NULL,
  `jci_type` enum('Contracture','In-House') DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `jci_date` date DEFAULT NULL,
  `sell_order_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `jc_number` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `cheque_number` varchar(255) DEFAULT NULL,
  `pd_acc_number` varchar(255) NOT NULL,
  `payment_full_partial` enum('Full','Partial') NOT NULL,
  `partial_amount` decimal(15,2) DEFAULT NULL,
  `outstanding_amount` decimal(15,2) DEFAULT NULL,
  `ptm_amount` decimal(15,2) NOT NULL,
  `payment_invoice_date` date NOT NULL,
  `payment_category` enum('Job Card','Supplier') NOT NULL DEFAULT 'Job Card',
  `cgst_percentage` decimal(5,2) DEFAULT 0.00,
  `cgst_amount` decimal(10,2) DEFAULT 0.00,
  `sgst_percentage` decimal(5,2) DEFAULT 0.00,
  `sgst_amount` decimal(10,2) DEFAULT 0.00,
  `igst_percentage` decimal(5,2) DEFAULT 0.00,
  `igst_amount` decimal(10,2) DEFAULT 0.00,
  `amount` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 7, 'QUOTE-2025-00007', 'PI-2025-00001', 'Generated', NULL, '2025-06-18', NULL, NULL, '2025-06-18 09:41:29', '2025-06-18 09:41:29'),
(2, 3, 'QUOTE-2025-00003', 'PI-2025-00002', 'Generated', NULL, '2025-06-18', NULL, NULL, '2025-06-18 09:41:43', '2025-06-18 09:41:43'),
(3, 10, 'QUOTE-2025-00010', 'PI-2025-00003', 'Generated', NULL, '2025-06-21', NULL, NULL, '2025-06-21 09:13:41', '2025-06-21 09:13:41');

-- --------------------------------------------------------

--
-- Table structure for table `po_items`
--

CREATE TABLE `po_items` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_code` varchar(100) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `item_code` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `po_items`
--

INSERT INTO `po_items` (`id`, `po_id`, `product_code`, `product_name`, `item_code`, `quantity`, `unit`, `price`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 4, 'pro01', 'pro02', '10', 10.00, '10', 100.00, 1000.00, '2025-06-24 15:49:02', '2025-06-24 15:49:02');

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
  `jci_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `po_main`
--

INSERT INTO `po_main` (`id`, `po_number`, `client_name`, `prepared_by`, `order_date`, `delivery_date`, `sell_order_id`, `status`, `is_locked`, `created_at`, `updated_at`, `sell_order_number`, `jci_number`) VALUES
(4, '001', 'bhupesh', 'bhupesh', '2025-06-24', '2025-06-05', 1, 'Locked', 1, '2025-06-24 10:19:02', '2025-06-24 10:46:45', 'SALE-2025-0001', 'JCI-2025-0001');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_glow`
--

CREATE TABLE `purchase_glow` (
  `id` int(11) NOT NULL,
  `glowtype` varchar(50) NOT NULL DEFAULT '',
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchase_main_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_glow`
--

INSERT INTO `purchase_glow` (`id`, `glowtype`, `quantity`, `price`, `total`, `purchase_main_id`) VALUES
(1, 'fevicole', 10.00, 100.00, 1000.00, 11);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_hardware`
--

CREATE TABLE `purchase_hardware` (
  `id` int(11) NOT NULL,
  `itemname` varchar(100) NOT NULL DEFAULT '',
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `totalprice` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchase_main_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_hardware`
--

INSERT INTO `purchase_hardware` (`id`, `itemname`, `quantity`, `price`, `totalprice`, `purchase_main_id`) VALUES
(1, 'screw', 10.00, 10.00, 100.00, 11);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_main`
--

CREATE TABLE `purchase_main` (
  `id` int(11) NOT NULL,
  `po_main_id` int(11) NOT NULL,
  `sell_order_number` varchar(50) DEFAULT NULL,
  `jci_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_main`
--

INSERT INTO `purchase_main` (`id`, `po_main_id`, `sell_order_number`, `jci_number`, `created_at`, `updated_at`) VALUES
(11, 4, 'SALE-2025-0001', 'JCI-2025-0001', '2025-06-24 11:53:14', '2025-06-24 11:53:14');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_plynydf`
--

CREATE TABLE `purchase_plynydf` (
  `id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `width` decimal(10,2) NOT NULL DEFAULT 0.00,
  `length` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchase_main_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_plynydf`
--

INSERT INTO `purchase_plynydf` (`id`, `quantity`, `width`, `length`, `price`, `total`, `purchase_main_id`) VALUES
(1, 10.00, 10.00, 10.00, 10.00, 10000.00, 11);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_wood`
--

CREATE TABLE `purchase_wood` (
  `id` int(11) NOT NULL,
  `woodtype` varchar(50) NOT NULL DEFAULT '',
  `length_ft` decimal(10,2) NOT NULL DEFAULT 0.00,
  `width_ft` decimal(10,2) NOT NULL DEFAULT 0.00,
  `thickness_inch` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cft` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchase_main_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_wood`
--

INSERT INTO `purchase_wood` (`id`, `woodtype`, `length_ft`, `width_ft`, `thickness_inch`, `quantity`, `price`, `cft`, `total`, `purchase_main_id`) VALUES
(3, 'Mango', 10.00, 0.25, 3.00, 10.00, 10.00, 6.25, 62.50, 11);

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
(1, 1, '2025-05-22', 'QUOTE-2025-00001', '90', 'a.90@gmail.com', '90', '90', '90', NULL, 1, 1, '2025-05-22 13:01:00', '2025-06-02 08:20:21'),
(2, 1, '2025-05-28', 'QUOTE-2025-00002', '90', 'a.90@gmail.com', '90', '90', '90', NULL, 1, 1, '2025-05-28 13:02:04', '2025-06-02 08:23:45'),
(3, 2, '2025-05-29', 'QUOTE-2025-00003', 'Test Company', 'premad.bhupesh@gmail.com', '0987654321', '30-70', 'FOB', NULL, 1, 0, '2025-05-29 05:50:02', '2025-06-18 09:41:43'),
(4, 2, '2025-05-29', 'QUOTE-2025-00004', 'Test Company', 'premad.bhupesh@gmail.com', '0987654321', '09', '09', NULL, 1, 1, '2025-05-29 08:12:22', '2025-06-14 03:59:08'),
(5, 1, '2025-06-02', 'QUOTE-2025-00005', '90', 'a.90@gmail.com', '90', '78', '78', NULL, 1, 1, '2025-06-02 11:47:40', '2025-06-02 11:49:54'),
(6, 1, '2025-06-11', 'QUOTE-2025-00006', '90', 'a.90@gmail.com', '90', '67', '67', NULL, 1, 0, '2025-06-11 09:17:46', '2025-06-11 09:18:03'),
(7, 1, '2025-06-13', 'QUOTE-2025-00007', '90', 'a.90@gmail.com', '90', '90', '90', NULL, 1, 1, '2025-06-13 18:57:16', '2025-06-18 13:31:20'),
(8, 3, '2025-06-14', 'QUOTE-2025-00008', 'ui', 'ui@gmail.com', 'ui', '70', '30', NULL, 1, 1, '2025-06-14 08:22:34', '2025-06-14 08:23:56'),
(9, 4, '2025-06-14', 'QUOTE-2025-00009', 'Test', 'test@gmail.com', '090909', '70', '30', NULL, 1, 1, '2025-06-14 11:25:58', '2025-06-14 11:28:33'),
(10, 5, '2025-06-21', 'QUOTE-2025-00010', 'test2', 'test2@gmail.com', '0909090909', '70%', '30%', NULL, 1, 1, '2025-06-21 09:13:24', '2025-06-21 09:13:50');

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
(1, 10, 'test2', 'test2', 'test2', 'yes', 100.00, 100.00, 100.00, 0.00, 0.00, 0.00, 0.000, '100', 100, '100', '100', 100.00, 100.00, '100', '2025-06-21 09:13:24', '2025-06-21 09:13:24', 'product_10_0_1750497204.png', 10000.00);

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
(1, 1, 'asd', '2025-06-07', '2025-06-18 09:06:08', '2025-06-18 09:06:08'),
(2, 10, 'Quotation created', '2025-06-21', '2025-06-21 09:13:24', '2025-06-21 09:13:24');

-- --------------------------------------------------------

--
-- Table structure for table `sell_order`
--

CREATE TABLE `sell_order` (
  `id` int(11) NOT NULL,
  `sell_order_number` varchar(50) NOT NULL,
  `po_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sell_order`
--

INSERT INTO `sell_order` (`id`, `sell_order_number`, `po_id`, `created_at`, `updated_at`) VALUES
(1, 'SALE-2025-0001', 4, '2025-06-24 15:49:26', '2025-06-24 15:49:26');

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

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `lead_id`, `status_text`, `status_date`, `created_at`, `updated_at`) VALUES
(1, 1, '90', '2025-05-01', '2025-05-22 13:00:18', '2025-05-22 13:00:18'),
(2, 2, 'called Customer Send Catlog', '2025-05-29', '2025-05-29 05:41:11', '2025-05-29 05:41:11'),
(3, 2, 'Catlog Send ', '2025-05-29', '2025-05-29 05:41:49', '2025-05-29 05:41:49'),
(4, 3, 'asd', '2025-06-10', '2025-06-14 10:54:54', '2025-06-14 10:54:54'),
(5, 4, 'No record', '2025-06-02', '2025-06-14 11:23:52', '2025-06-14 11:23:52'),
(6, 5, 'NEW', '2025-06-12', '2025-06-21 09:17:31', '2025-06-21 09:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `invoice_amount` decimal(15,2) NOT NULL
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
-- Indexes for table `bom_items`
--
ALTER TABLE `bom_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bom_id` (`bom_id`);

--
-- Indexes for table `bom_main`
--
ALTER TABLE `bom_main`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bom_number` (`bom_number`);

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
  ADD KEY `payment_id` (`payment_id`);

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
-- Indexes for table `purchase_glow`
--
ALTER TABLE `purchase_glow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_glow_purchase_main` (`purchase_main_id`);

--
-- Indexes for table `purchase_hardware`
--
ALTER TABLE `purchase_hardware`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hardware_purchase_main` (`purchase_main_id`);

--
-- Indexes for table `purchase_main`
--
ALTER TABLE `purchase_main`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_purchase_po_main` (`po_main_id`);

--
-- Indexes for table `purchase_plynydf`
--
ALTER TABLE `purchase_plynydf`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_plynydf_purchase_main` (`purchase_main_id`);

--
-- Indexes for table `purchase_wood`
--
ALTER TABLE `purchase_wood`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_wood_purchase_main` (`purchase_main_id`);

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
-- AUTO_INCREMENT for table `bom_items`
--
ALTER TABLE `bom_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bom_main`
--
ALTER TABLE `bom_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jci_items`
--
ALTER TABLE `jci_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jci_main`
--
ALTER TABLE `jci_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pi`
--
ALTER TABLE `pi`
  MODIFY `pi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `po_main`
--
ALTER TABLE `po_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchase_glow`
--
ALTER TABLE `purchase_glow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_hardware`
--
ALTER TABLE `purchase_hardware`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_main`
--
ALTER TABLE `purchase_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `purchase_plynydf`
--
ALTER TABLE `purchase_plynydf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_wood`
--
ALTER TABLE `purchase_wood`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quotation_products`
--
ALTER TABLE `quotation_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quotation_status`
--
ALTER TABLE `quotation_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sell_order`
--
ALTER TABLE `sell_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `supplier_items`
--
ALTER TABLE `supplier_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bom_items`
--
ALTER TABLE `bom_items`
  ADD CONSTRAINT `bom_items_ibfk_1` FOREIGN KEY (`bom_id`) REFERENCES `bom_main` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bom_id` FOREIGN KEY (`bom_id`) REFERENCES `bom_main` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `payment_details_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `purchase_glow`
--
ALTER TABLE `purchase_glow`
  ADD CONSTRAINT `fk_glow_purchase_main` FOREIGN KEY (`purchase_main_id`) REFERENCES `purchase_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_hardware`
--
ALTER TABLE `purchase_hardware`
  ADD CONSTRAINT `fk_hardware_purchase_main` FOREIGN KEY (`purchase_main_id`) REFERENCES `purchase_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_main`
--
ALTER TABLE `purchase_main`
  ADD CONSTRAINT `fk_purchase_po_main` FOREIGN KEY (`po_main_id`) REFERENCES `po_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_plynydf`
--
ALTER TABLE `purchase_plynydf`
  ADD CONSTRAINT `fk_plynydf_purchase_main` FOREIGN KEY (`purchase_main_id`) REFERENCES `purchase_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_wood`
--
ALTER TABLE `purchase_wood`
  ADD CONSTRAINT `fk_wood_purchase_main` FOREIGN KEY (`purchase_main_id`) REFERENCES `purchase_main` (`id`) ON DELETE CASCADE;

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
