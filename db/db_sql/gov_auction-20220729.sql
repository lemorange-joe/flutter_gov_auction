-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jul 29, 2022 at 09:00 AM
-- Server version: 5.7.34
-- PHP Version: 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gov_auction`
--

-- --------------------------------------------------------

--
-- Table structure for table `Auction`
--

CREATE TABLE `Auction` (
  `auction_id` int(11) NOT NULL,
  `auction_date` datetime NOT NULL,
  `auction_num` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `location_id` int(11) NOT NULL DEFAULT '0',
  `item_list_pdf_en` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `item_list_pdf_tc` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `item_list_pdf_sc` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `result_pdf_en` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `result_pdf_tc` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `result_pdf_sc` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `auction_status` char(1) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'P' COMMENT 'P: pending, C: confirmed, X: cancelled, F: finished',
  `status` char(1) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'A' COMMENT 'A: active, I: inactive',
  `last_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `Auction`
--

INSERT INTO `Auction` (`auction_id`, `auction_date`, `auction_num`, `location_id`, `item_list_pdf_en`, `item_list_pdf_tc`, `item_list_pdf_sc`, `result_pdf_en`, `result_pdf_tc`, `result_pdf_sc`, `auction_status`, `status`, `last_update`) VALUES
(1, '2022-06-16 10:30:00', '1/2022', 1, 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-List-1-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-List-1-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-List-1-2022-en-zh-cn.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-Result-1-2022-en.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-Result-1-2022-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-Result-1-2022-zh-cn.pdf', 'F', 'A', '2022-07-29 15:50:26'),
(2, '2022-07-14 10:30:00', '2/2022', 1, 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-List-2-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-List-2-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-List-2-2022-en-zh-cn.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-Result-2-2022-en.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-Result-2-2022-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/Auction-Result-2-2022-zh-cn.pdf', 'F', 'A', '2022-07-29 15:55:56');

-- --------------------------------------------------------

--
-- Table structure for table `AuctionItem`
--

CREATE TABLE `AuctionItem` (
  `item_id` int(11) NOT NULL,
  `lot_id` int(11) NOT NULL,
  `seq` int(11) NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `description_en` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `description_tc` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `description_sc` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `quantity` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `unit_en` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `unit_tc` varchar(5) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `unit_sc` varchar(5) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `AuctionItem`
--

INSERT INTO `AuctionItem` (`item_id`, `lot_id`, `seq`, `icon`, `description_en`, `description_tc`, `description_sc`, `quantity`, `unit_en`, `unit_tc`, `unit_sc`) VALUES
(1, 1, 1, 'fontawesome.ring', 'Bracelet/Bangle', '??????/??????', '??????/??????', '59', 'Nos.', '???', '???'),
(2, 1, 2, 'fontawesome.box', 'Brassiere', '??????', '??????', '81', 'Nos.', '???', '???'),
(3, 1, 3, 'fontawesome.shirt', 'Clothing, Various', '??????', '??????', '48', 'Nos.', '???', '???'),
(4, 1, 4, 'fontawesome.ring-diamond', 'Finger Ring', '??????', '??????', '25', 'Nos.', '???', '???'),
(5, 2, 1, 'fontawesome.gem', 'Natural Amber', '????????????', '????????????', '4', 'Kg', '??????', '??????'),
(6, 2, 2, 'fontawesome.gem', 'Nature Rough Yellow Agate (7 Numbers)', '?????????????????????', '?????????????????????', '1.058', 'Kg', '??????', '??????'),
(7, 2, 3, 'fontawesome.gem', 'Nature Rough Yellow Agate (5 Numbers)', '?????????????????????', '?????????????????????', '0.909', 'Kg', '??????', '??????'),
(8, 3, 1, 'fontawesome.kitchen-set', 'Water Heater (Brand: TGC, Model: RS68B) (Dismantled)', '?????????(?????????)', '?????????(?????????)', '1', 'No.', '???', '???'),
(9, 3, 2, 'fontawesome.kitchen-set', 'Gas Stove (Brand: TGC, Model: SUZH2H) (Dismantled)', '???????????????(?????????)', '???????????????(?????????)', '1', 'No.', '???', '???'),
(10, 4, 1, 'fontawesome.redhat', 'Cap/Hat', '???', '???', '7', 'Nos.', '???', '???'),
(11, 4, 2, 'fontawesome.shirt', 'Clothing, Various', '??????', '??????', '51', 'Nos.', '???', '???'),
(12, 4, 3, 'fontawesome.box', 'Handbag/Bag', '??????/???', '??????/???', '85', 'Nos.', '???', '???'),
(13, 5, 1, 'fontawesome.box', 'Clothing, Various', '??????', '??????', '40', 'Nos.', '???', '???'),
(14, 5, 2, 'fontawesome.box', 'Coloured Stone', '?????????', '?????????', '76', 'Nos.', '???', '???'),
(15, 6, 1, 'fontawesome.fax', 'Facsimile Machine', '?????????', '?????????', '1', 'No.', '???', '???'),
(16, 6, 2, 'fontawesome.shredder', 'Paper Shredder', '?????????', '?????????', '1', 'No.', '???', '???'),
(17, 6, 3, 'fontawesome.box', 'Hub with Management Module Software (Brand: Superstack)', '??????????????????????????????', '??????????????????????????????', '2', 'Nos.', '???', '???');

-- --------------------------------------------------------

--
-- Table structure for table `AuctionListPdf`
--

CREATE TABLE `AuctionListPdf` (
  `pdf_id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `url_en` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `url_tc` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `url_sc` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `AuctionListPdf`
--

INSERT INTO `AuctionListPdf` (`pdf_id`, `auction_id`, `type_id`, `url_en`, `url_tc`, `url_sc`) VALUES
(1, 1, 1, 'https://www.gld.gov.hk/assets/gld/download-files/auction/C-1-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/C-1-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/C-1-2022-en-zh-cn.pdf'),
(2, 1, 2, 'https://www.gld.gov.hk/assets/gld/download-files/auction/UP-1-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/UP-1-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/UP-1-2022-en-zh-cn.pdf'),
(3, 1, 3, 'https://www.gld.gov.hk/assets/gld/download-files/auction/M-1-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/M-1-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/M-1-2022-en-zh-cn.pdf'),
(4, 1, 4, 'https://www.gld.gov.hk/assets/gld/download-files/auction/MS-1-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/MS-1-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/MS-1-2022-en-zh-cn.pdf'),
(5, 2, 1, 'https://www.gld.gov.hk/assets/gld/download-files/auction/C-2-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/C-2-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/C-2-2022-en-zh-cn.pdf'),
(6, 2, 2, 'https://www.gld.gov.hk/assets/gld/download-files/auction/UP-2-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/UP-2-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/UP-2-2022-en-zh-cn.pdf'),
(7, 2, 3, 'https://www.gld.gov.hk/assets/gld/download-files/auction/M-2-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/M-2-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/M-2-2022-en-zh-cn.pdf'),
(8, 2, 4, 'https://www.gld.gov.hk/assets/gld/download-files/auction/MS-2-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/MS-2-2022-en-zh-hk.pdf', 'https://www.gld.gov.hk/assets/gld/download-files/auction/MS-2-2022-en-zh-cn.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `AuctionLot`
--

CREATE TABLE `AuctionLot` (
  `lot_id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `lot_num` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `seq` int(11) NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `photo_url` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `photo_real` bit(1) NOT NULL DEFAULT b'0',
  `transaction_currency` char(3) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `transaction_price` float NOT NULL DEFAULT '0',
  `transaction_status` char(1) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'N' COMMENT 'S: sold, N: not-sold',
  `status` char(1) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'A' COMMENT 'A: active, I: inactive',
  `last_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `AuctionLot`
--

INSERT INTO `AuctionLot` (`lot_id`, `auction_id`, `type_id`, `lot_num`, `seq`, `icon`, `photo_url`, `photo_real`, `transaction_currency`, `transaction_price`, `transaction_status`, `status`, `last_update`) VALUES
(1, 1, 1, 'C-401', 1, 'fontawesome.box', '', b'0', 'HKD', 3800, 'S', 'A', '2022-07-29 16:02:15'),
(2, 1, 1, 'C-402', 2, 'fontawesome.box', '', b'0', '', 0, 'N', 'A', '2022-07-29 15:03:15'),
(3, 1, 2, 'UP-701', 3, 'fontawesome.kitchen-set', '', b'0', '', 0, 'N', 'A', '2022-07-29 16:04:39'),
(4, 2, 1, 'C-401', 1, 'fontawesome.box', '', b'0', 'HKD', 1300, 'S', 'A', '2022-07-29 08:06:37'),
(5, 2, 1, 'C-402', 2, 'fontawesome.box', '', b'0', 'HKD', 4600, 'S', 'A', '2022-07-29 16:07:37'),
(6, 2, 3, 'M-101', 3, 'fontawesome.fax', '', b'0', '', 0, 'N', 'A', '2022-07-29 08:08:38');

-- --------------------------------------------------------

--
-- Table structure for table `FetchHistory`
--

CREATE TABLE `FetchHistory` (
  `fetch_id` int(11) NOT NULL,
  `url_en` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `url_tc` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `url_sc` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `file_path_en` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `file_path_tc` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `file_path_sc` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `status` char(1) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'S: success, F: failed',
  `fetch_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `FetchHistory`
--

INSERT INTO `FetchHistory` (`fetch_id`, `url_en`, `url_tc`, `url_sc`, `file_path_en`, `file_path_tc`, `file_path_sc`, `status`, `fetch_datetime`) VALUES
(1, 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionList_1-2022_EN.csv', 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionList_1-2022_TC.csv', 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionList_1-2022_SC.csv', '/var/data_files/csv/20220729-165400-auctionList_1-2022_EN.csv', '/var/data_files/csv/20220729-165400-auctionList_1-2022_TC.csv', '/var/data_files/csv/20220729-165400-auctionList_1-2022_SC.csv', 'S', '2022-07-29 17:53:07'),
(2, 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionList_2-2022_EN.csv', 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionList_2-2022_TC.csv', 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionList_2-2022_SC.csv', '/var/data_files/csv/20220729-165500-auctionList_2-2022_EN.csv', '/var/data_files/csv/20220729-165500-auctionList_2-2022_TC.csv', '/var/data_files/csv/20220729-165500-auctionList_2-2022_SC.csv', 'S', '2022-07-29 16:56:54'),
(3, 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionResult_1-2022_EN.csv', 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionResult_1-2022_TC.csv', 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionResult_1-2022_TC.csv', '/var/data_files/csv/20220729-165900-auctionResult_1-2022_EN.csv', '/var/data_files/csv/20220729-165900-auctionResult_1-2022_TC.csv', '/var/data_files/csv/20220729-165900-auctionResult_1-2022_SC.csv', 'S', '2022-07-29 16:57:36'),
(4, 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionResult_2-2022_EN.csv', 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionResult_2-2022_TC.csv', 'https://www.gld.gov.hk/datagovhk/supplies-mgmt/auctionResult_2-2022_SC.csv', '/var/data_files/csv/20220729-170200-auctionResult_2-2022_EN.csv', '/var/data_files/csv/20220729-170200-auctionResult_2-2022_TC.csv', '/var/data_files/csv/20220729-170200-auctionResult_2-2022_SC.csv', 'S', '2022-07-29 16:57:36');

-- --------------------------------------------------------

--
-- Table structure for table `ItemType`
--

CREATE TABLE `ItemType` (
  `type_id` int(11) NOT NULL,
  `code` varchar(2) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `seq` int(11) NOT NULL,
  `description_en` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `description_tc` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `description_sc` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `ItemType`
--

INSERT INTO `ItemType` (`type_id`, `code`, `seq`, `description_en`, `description_tc`, `description_sc`) VALUES
(1, 'C', 1, 'Confiscated Goods', '????????????', '????????????'),
(2, 'UP', 2, 'Unclaimed Properties', '??????????????????', '??????????????????'),
(3, 'M', 3, 'Unserviceable Stores', '???????????????????????????', '???????????????????????????'),
(4, 'MS', 4, 'Surplus Serviceable Stores', '??????????????????????????????????????????', '??????????????????????????????????????????');

-- --------------------------------------------------------

--
-- Table structure for table `Location`
--

CREATE TABLE `Location` (
  `location_id` int(11) NOT NULL,
  `address_en` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `address_tc` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `address_sc` text COLLATE utf8mb4_unicode_520_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `Location`
--

INSERT INTO `Location` (`location_id`, `address_en`, `address_tc`, `address_sc`) VALUES
(0, 'To be confirmed', '??????', '??????'),
(1, '1/F, Government Logistics Centre,\r\n11 Chong Fu Road, Chai Wan, Hong Kong', '????????????\r\n?????????11???\r\n????????????????????????1???', '????????????\r\n?????????11???\r\n????????????????????????1???'),
(2, 'Government Logistics Department reception counter\r\n10/F., North Point Government Offices\r\n333 Java Road\r\nHong Kong', '????????????\r\n?????????333???\r\n??????????????????10???\r\n??????????????????????????????', '????????????\r\n?????????333???\r\n??????????????????10???\r\n??????????????????????????????');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Auction`
--
ALTER TABLE `Auction`
  ADD PRIMARY KEY (`auction_id`);

--
-- Indexes for table `AuctionItem`
--
ALTER TABLE `AuctionItem`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `AuctionListPdf`
--
ALTER TABLE `AuctionListPdf`
  ADD PRIMARY KEY (`pdf_id`);

--
-- Indexes for table `AuctionLot`
--
ALTER TABLE `AuctionLot`
  ADD PRIMARY KEY (`lot_id`);

--
-- Indexes for table `FetchHistory`
--
ALTER TABLE `FetchHistory`
  ADD PRIMARY KEY (`fetch_id`);

--
-- Indexes for table `ItemType`
--
ALTER TABLE `ItemType`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `Location`
--
ALTER TABLE `Location`
  ADD PRIMARY KEY (`location_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Auction`
--
ALTER TABLE `Auction`
  MODIFY `auction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `AuctionItem`
--
ALTER TABLE `AuctionItem`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `AuctionListPdf`
--
ALTER TABLE `AuctionListPdf`
  MODIFY `pdf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `AuctionLot`
--
ALTER TABLE `AuctionLot`
  MODIFY `lot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `FetchHistory`
--
ALTER TABLE `FetchHistory`
  MODIFY `fetch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ItemType`
--
ALTER TABLE `ItemType`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
