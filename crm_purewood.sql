-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2025 at 11:04 AM
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
(1, 6, 'test', '01', 10.00, '', 100.00, 1000.00);

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
(6, 'BOM-2025-0001', '01', 'test', 'test', '2025-07-02', '2025-07-02', '2025-07-02 06:35:44', '2025-07-02 06:35:44');

-- --------------------------------------------------------

--
-- Table structure for table `jci_items`
--

CREATE TABLE `jci_items` (
  `id` int(11) NOT NULL,
  `jci_id` int(11) NOT NULL,
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

INSERT INTO `jci_items` (`id`, `jci_id`, `po_product_id`, `product_name`, `item_code`, `original_po_quantity`, `labour_cost`, `quantity`, `total_amount`, `delivery_date`, `job_card_date`, `job_card_type`, `contracture_name`) VALUES
(1, 1, 1, 'test01', 'test01', 100.00, 100.00, 100, 10000.00, '2025-07-28', '2025-07-25', 'Contracture', 'raju'),
(2, 2, 2, 'test02', 'test02', 100.00, 100.00, 100, 10000.00, '2025-07-29', '2025-07-01', 'In-House', '');

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

--
-- Dumping data for table `jci_main`
--

INSERT INTO `jci_main` (`id`, `jci_number`, `po_id`, `jci_type`, `created_by`, `jci_date`, `created_at`, `updated_at`, `sell_order_number`) VALUES
(1, 'JOB-2025-0001-1', 1, 'Contracture', 'test', '2025-07-25', '2025-07-02 06:56:39', '2025-07-02 06:56:39', '001'),
(2, 'JOB-2025-0001-2', 1, 'In-House', 'test', '2025-07-01', '2025-07-02 06:56:39', '2025-07-02 06:56:39', '001');

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
(1, 1, '001', 'test01', 'test01', 100.00, '10', 100.00, 10000.00, '2025-07-02 12:06:37', '2025-07-02 12:06:37'),
(2, 1, '002', 'test02', 'test02', 100.00, '10', 100.00, 10000.00, '2025-07-02 12:06:37', '2025-07-02 12:06:37'),
(4, 2, 'test02', 'test02', 'test02', 100.00, '10', 100.00, 10000.00, '2025-07-02 12:52:49', '2025-07-02 12:52:49'),
(5, 2, 'test03', 'test03', 'test03', 100.00, '10', 100.00, 10000.00, '2025-07-02 12:52:49', '2025-07-02 12:52:49');

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
(1, '001', 'test', 'test', '2025-07-01', '2025-07-10', 1, 'Locked', 1, '2025-07-02 06:36:37', '2025-07-02 06:36:53', 'SALE-2025-0001', ''),
(2, 'test02', 'test02', 'test02', '2025-07-02', '2025-07-18', 2, 'Locked', 1, '2025-07-02 07:22:19', '2025-07-02 07:23:16', 'SALE-2025-0002', '');

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

--
-- Dumping data for table `sell_order`
--

INSERT INTO `sell_order` (`id`, `sell_order_number`, `po_id`, `created_at`, `updated_at`) VALUES
(1, 'SALE-2025-0001', 1, '2025-07-02 12:06:48', '2025-07-02 12:06:48'),
(2, 'SALE-2025-0002', 2, '2025-07-02 12:53:12', '2025-07-02 12:53:12');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bom_main`
--
ALTER TABLE `bom_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jci_items`
--
ALTER TABLE `jci_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pi`
--
ALTER TABLE `pi`
  MODIFY `pi_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `po_main`
--
ALTER TABLE `po_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_glow`
--
ALTER TABLE `purchase_glow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_hardware`
--
ALTER TABLE `purchase_hardware`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_main`
--
ALTER TABLE `purchase_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_plynydf`
--
ALTER TABLE `purchase_plynydf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_wood`
--
ALTER TABLE `purchase_wood`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
