-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 01, 2025 at 11:45 AM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u404997496_crm_purewood`
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
  `jc_amt` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_cards`
--

INSERT INTO `job_cards` (`id`, `payment_id`, `jc_number`, `jc_amt`) VALUES
(1, 1, '01', 100.00),
(2, 2, 'JCN/RAJU DAN/04/2025/97', 11694.00),
(5, 4, 'JCN/COMPANY/06/2025/114', 120250.00),
(7, 5, 'JOB/307/24-25/LAXM &amp; JOB/292/24-25/LAXM', 15000.00),
(8, 3, 'JOB/308/24-25/RIHA&amp;JOB/313/24-25/RIHA', 38460.00);

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
(1, 'LEAD-2025-0001', '2025-05-01', 'Via Email', 'test', 'test', '9999999999', 'test@gmail.com', 'India', 'Rajasthan', 'Jaipur', 'new', 1, 'active', '2025-05-22 13:11:33', '2025-05-22 13:11:55'),
(2, 'LEAD-2025-0002', '2025-05-01', 'Email', 'bhupesh', 'bhupesh', '9782027434', 'premad.bhupesh@gmail.com', 'India', 'Rajasthan', 'Jaipur', 'new', 1, 'active', '2025-05-23 11:21:11', '2025-05-26 08:05:29'),
(3, 'LEAD-2025-0003', '2025-05-09', 'Online', 'not registered', 'Kamal', '919999056925    ', 'kamal.s@bluetokaicofee.com', 'India', 'Delhi', 'Delhi', 'new', 0, 'active', '2025-05-26 08:34:02', '2025-05-26 08:34:02'),
(4, 'LEAD-2025-0004', '2025-05-09', 'Online', 'not registered', 'Twinkle', '919999056925', 'kamal.s@bluetokaicofee.com', 'India', 'Delhi', 'Delhi', 'new', 0, 'active', '2025-05-26 08:38:51', '2025-05-26 08:38:51'),
(5, 'LEAD-2025-0005', '2025-05-10', 'Online', 'not registered', 'Joydeep Bhattacharya', '9892202402', 'joydeep.bhattacharya@bain.com', 'India', 'Delhi', 'Delhi', 'new', 0, 'active', '2025-05-26 08:48:31', '2025-05-26 08:48:31'),
(6, 'LEAD-2025-0006', '2025-05-11', 'Online', 'not registered', 'Laksh Jain', '919056172512', 'laksh@sunmerchandising.com', 'Canada', 'Alberta', '', 'new', 0, 'active', '2025-05-26 08:51:20', '2025-05-26 08:51:20'),
(7, 'LEAD-2025-0007', '2025-05-11', 'Online', 'not registered', 'KK', '918886770020', 'kk@gmail.com', 'India', 'Andaman and Nicobar Islands', '', 'new', 0, 'active', '2025-05-26 08:53:12', '2025-05-26 08:53:12'),
(8, 'LEAD-2025-0008', '2025-05-12', 'Online', 'not registered', 'Jitendra Malde', '919987788113', 'jitu240966@gmail.com', 'India', 'Maharashtra', 'Mumbai', 'new', 1, 'active', '2025-05-26 08:55:29', '2025-05-26 08:55:39');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `lead_id`, `pon_number`, `po_amt`, `son_number`, `soa_number`, `jci_number`, `created_at`, `updated_at`) VALUES
(1, NULL, '001', 1000.00, '001', 1000.00, NULL, '2025-06-28 05:25:30', '2025-06-28 05:25:30'),
(2, NULL, '3418', 77900.00, 'SO/04/2025/59', 77900.00, NULL, '2025-06-28 10:21:34', '2025-06-28 10:21:34'),
(3, NULL, '8', 325000.00, '157/24-25', 325000.00, NULL, '2025-06-30 08:18:45', '2025-06-30 08:18:45'),
(4, NULL, 'POA25-26/00260', 1286250.00, 'SO/06/2025/86', 1286250.00, NULL, '2025-06-30 08:47:16', '2025-06-30 08:47:16'),
(5, NULL, '77', 110000.00, '152/24-25', 110000.00, NULL, '2025-06-30 11:19:13', '2025-06-30 11:19:13');

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

--
-- Dumping data for table `payment_details`
--

INSERT INTO `payment_details` (`id`, `payment_id`, `jc_number`, `payment_type`, `cheque_number`, `pd_acc_number`, `payment_full_partial`, `ptm_amount`, `payment_invoice_date`, `payment_category`, `cgst_percentage`, `cgst_amount`, `sgst_percentage`, `sgst_amount`, `igst_percentage`, `igst_amount`, `amount`) VALUES
(1, 1, '', 'NEFT', '01', '0001', 'Full', 10.00, '2025-06-03', 'Job Card', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(2, 2, '', 'Online', 'Online', '777705091719', 'Full', 0.00, '2025-06-13', 'Job Card', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(5, 4, '', 'CHEQU', '000309', '777705091719', 'Full', 0.00, '2025-06-28', 'Supplier', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(7, 5, '', '000370', '777705091719', '3979', 'Partial', 0.00, '2024-10-19', 'Job Card', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(8, 3, '', 'CHEQUE', '000370', '777705091719', 'Partial', 0.00, '2024-10-19', 'Job Card', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

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
  `item_code` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `purchase_glow`
--

CREATE TABLE `purchase_glow` (
  `id` int(11) NOT NULL,
  `glowtype` varchar(50) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `purchase_main_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_glow`
--

INSERT INTO `purchase_glow` (`id`, `glowtype`, `quantity`, `price`, `total`, `purchase_main_id`) VALUES
(1, 'Favical', 1.00, 100.00, 100.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_hardware`
--

CREATE TABLE `purchase_hardware` (
  `id` int(11) NOT NULL,
  `itemname` varchar(100) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `totalprice` decimal(10,2) DEFAULT NULL,
  `purchase_main_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_main`
--

CREATE TABLE `purchase_main` (
  `id` int(11) NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `sell_order_number` varchar(50) DEFAULT NULL,
  `jci_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_main`
--

INSERT INTO `purchase_main` (`id`, `po_number`, `sell_order_number`, `jci_number`, `created_at`, `updated_at`) VALUES
(1, 'po01', 'sell01', 'jc01', '2025-06-18 04:28:46', '2025-06-18 04:28:46');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_plynydf`
--

CREATE TABLE `purchase_plynydf` (
  `id` int(11) NOT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT NULL,
  `length` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `purchase_main_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_wood`
--

CREATE TABLE `purchase_wood` (
  `id` int(11) NOT NULL,
  `woodtype` varchar(50) DEFAULT NULL,
  `length_ft` decimal(10,2) DEFAULT NULL,
  `width_ft` decimal(10,2) DEFAULT NULL,
  `thickness_inch` decimal(10,2) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `cft` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `purchase_main_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_wood`
--

INSERT INTO `purchase_wood` (`id`, `woodtype`, `length_ft`, `width_ft`, `thickness_inch`, `quantity`, `price`, `cft`, `total`, `purchase_main_id`) VALUES
(1, 'Mango', 10.00, 1.00, 1.00, 1.00, 10.00, 0.83, 8.33, 1);

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
(5, 4, 'No record', '2025-06-02', '2025-06-14 11:23:52', '2025-06-14 11:23:52');

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

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `payment_id`, `supplier_name`, `invoice_number`, `invoice_amount`) VALUES
(1, 1, 'supplier 01', 'invoive 01', 100.00),
(2, 2, 'Ashapurna Enterprises', 'AE/2526/103', 22988.00),
(3, 0, 'Amayra Impex Pvt Ltd', '2025-26/175', 10438.00),
(4, 0, 'Maha Ambe Exports', '25-26/198', 1938.00),
(7, 4, 'DEVAN ENTERPRISES', '434/25-26', 110785.00),
(9, 5, 'Shree J Industries', '2194', 59880.00),
(10, 3, 'Shree Ji Industries', '2194', 59880.00);

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
-- Dumping data for table `supplier_items`
--

INSERT INTO `supplier_items` (`id`, `supplier_id`, `item_name`, `item_quantity`, `item_price`, `item_amount`) VALUES
(1, 1, 'item 01', 1, 10.00, 10.00),
(2, 2, 'Mango Wood 2.75&#039;x1&quot;', 35, 660.00, 22987.80),
(3, 0, 'Mango Wood', 15, 680.00, 10438.00),
(4, 0, 'Mdf In Sqft', 4, 431.64, 1933.75),
(11, 7, 'BABOOL WOOD 4&#039; &amp; 4.25&#039;', 144, 650.00, 93886.00),
(12, 7, 'CGST &amp; SGST', 1, 16899.48, 16899.48),
(15, 9, 'Babool Wood', 5, 635.00, 3175.00),
(16, 9, 'CGST &amp; SGST', 1, 606.90, 606.90),
(17, 10, 'BABOOL WOOD 5.5&#039;X1.5&quot;', 57, 735.00, 41895.00),
(18, 10, 'BABOOL WOOD 6.5&#039;X1.5&quot;', 7, 855.00, 5985.00),
(19, 10, 'CGST AND SGST', 1, 8527.20, 8527.20);

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
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `fk_purchase_glow_main` (`purchase_main_id`);

--
-- Indexes for table `purchase_hardware`
--
ALTER TABLE `purchase_hardware`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_purchase_hardware_main` (`purchase_main_id`);

--
-- Indexes for table `purchase_main`
--
ALTER TABLE `purchase_main`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_po_number` (`po_number`);

--
-- Indexes for table `purchase_plynydf`
--
ALTER TABLE `purchase_plynydf`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_purchase_plynydf_main` (`purchase_main_id`);

--
-- Indexes for table `purchase_wood`
--
ALTER TABLE `purchase_wood`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_purchase_wood_main` (`purchase_main_id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_items`
--
ALTER TABLE `supplier_items`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bom_items`
--
ALTER TABLE `bom_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bom_main`
--
ALTER TABLE `bom_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jci_items`
--
ALTER TABLE `jci_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jci_main`
--
ALTER TABLE `jci_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pi`
--
ALTER TABLE `pi`
  MODIFY `pi_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `po_main`
--
ALTER TABLE `po_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_glow`
--
ALTER TABLE `purchase_glow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_hardware`
--
ALTER TABLE `purchase_hardware`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_main`
--
ALTER TABLE `purchase_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_plynydf`
--
ALTER TABLE `purchase_plynydf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_wood`
--
ALTER TABLE `purchase_wood`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_products`
--
ALTER TABLE `quotation_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_status`
--
ALTER TABLE `quotation_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sell_order`
--
ALTER TABLE `sell_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `supplier_items`
--
ALTER TABLE `supplier_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bom_items`
--
ALTER TABLE `bom_items`
  ADD CONSTRAINT `bom_items_ibfk_1` FOREIGN KEY (`bom_id`) REFERENCES `bom_main` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jci_items`
--
ALTER TABLE `jci_items`
  ADD CONSTRAINT `jci_items_ibfk_1` FOREIGN KEY (`jci_id`) REFERENCES `jci_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `po_items`
--
ALTER TABLE `po_items`
  ADD CONSTRAINT `po_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `po_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_glow`
--
ALTER TABLE `purchase_glow`
  ADD CONSTRAINT `fk_purchase_glow_main` FOREIGN KEY (`purchase_main_id`) REFERENCES `purchase_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_hardware`
--
ALTER TABLE `purchase_hardware`
  ADD CONSTRAINT `fk_purchase_hardware_main` FOREIGN KEY (`purchase_main_id`) REFERENCES `purchase_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_plynydf`
--
ALTER TABLE `purchase_plynydf`
  ADD CONSTRAINT `fk_purchase_plynydf_main` FOREIGN KEY (`purchase_main_id`) REFERENCES `purchase_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_wood`
--
ALTER TABLE `purchase_wood`
  ADD CONSTRAINT `fk_purchase_wood_main` FOREIGN KEY (`purchase_main_id`) REFERENCES `purchase_main` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sell_order`
--
ALTER TABLE `sell_order`
  ADD CONSTRAINT `sell_order_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `po_main` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
