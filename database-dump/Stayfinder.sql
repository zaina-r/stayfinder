-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2024 at 07:02 PM
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
-- Database: `stayfinder`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `first_name`, `last_name`, `email`, `password`, `contact_no`, `profile_pic`) VALUES
(5, 'Admin', 'Test', 'admintest@gmail.com', '$2y$10$oM9LFQ4VOBeP0qf1tOLvO.0.pMnjcTrzkZb0oq2elZg6cZzyZyTBu', '0763117229', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `advertiser_plan`
--

CREATE TABLE `advertiser_plan` (
  `advertiser_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advertiser_plan`
--

INSERT INTO `advertiser_plan` (`advertiser_id`, `plan_id`, `end_date`) VALUES
(17, 2, '2025-05-17'),
(18, 3, '2025-11-17'),
(21, 1, '2024-12-18'),
(23, 1, '2024-12-19'),
(25, 3, '2025-11-18'),
(27, 2, '2025-05-18');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'House'),
(2, 'Room'),
(4, 'Apartment'),
(6, 'Hostal');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `rating` enum('1','2','3','4','5') DEFAULT NULL,
  `comment_date` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `ad_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`comment_id`, `comment`, `rating`, `comment_date`, `user_id`, `ad_id`) VALUES
(11, 'Excelent...', '3', '2024-11-19 00:48:55', 20, '673b5e2568408'),
(12, '\"Ideal for university students—just a short walk to campus and all essential amenities', '4', '2024-11-19 23:10:09', 20, '673b5e2568408'),
(13, 'Perfect student pad! Close to public transport, affordable rent, and a study-friendly environment.', '4', '2024-11-19 23:10:31', 20, '673b5f2b34aba'),
(14, 'Spacious rooms with plenty of light—great for group study sessions or relaxing after lectures.', '5', '2024-11-19 23:10:55', 20, '673b606737c81'),
(15, 'Quiet and convenient, this rental offers the perfect environment for focused studies and rest.', '5', '2024-11-19 23:12:35', 22, '673b6290b611d'),
(17, 'Sharing-friendly! Large common areas and multiple bedrooms—ideal for roommates.', '4', '2024-11-19 23:13:34', 22, '673b5f2b34aba'),
(18, 'Furnished and ready for move-in—no stress, just start your student journey with ease!', '4', '2024-11-19 23:13:56', 22, '673b7118936ef'),
(19, 'Budget-friendly rental in a great location—because we know students need great value!', '4', '2024-11-19 23:15:05', 22, '673b749deac92'),
(20, 'Budget-friendly rental in a great location—because we know students need great value!', '4', '2024-11-19 23:15:20', 22, '673b5e2568408'),
(21, 'Ideal for group living! Split the rent with friends and make this space your home away from home.', '5', '2024-11-19 23:17:02', 23, '673b76725e76a'),
(22, 'Looking for a study-friendly home? Quiet surroundings, good light, and comfy living spaces await', '5', '2024-11-19 23:18:19', 24, '673b5e2568408'),
(23, 'Excelent', '5', '2024-11-19 23:18:54', 24, '673b606737c81'),
(24, 'Excelent', '4', '2024-11-19 23:20:49', 26, '673b72fbd28c0');

-- --------------------------------------------------------

--
-- Table structure for table `contactus`
--

CREATE TABLE `contactus` (
  `sender_id` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `district`
--

CREATE TABLE `district` (
  `district_id` int(11) NOT NULL,
  `district_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `district`
--

INSERT INTO `district` (`district_id`, `district_name`) VALUES
(6, 'Colombo'),
(7, 'Gampaha'),
(8, 'Kandy'),
(9, 'Galle'),
(10, 'Matara'),
(11, 'Jaffna'),
(12, 'Rathnapura'),
(13, 'Batticaloa'),
(14, 'Anuradhapura');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `fav_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ad_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`fav_id`, `user_id`, `ad_id`) VALUES
(26, 20, '673b5e2568408'),
(27, 20, '673b5f2b34aba'),
(28, 22, '673b7118936ef'),
(29, 22, '673b606737c81'),
(30, 22, '673b5e2568408'),
(31, 23, '673b76725e76a'),
(33, 24, '673b606737c81'),
(34, 24, '673b749deac92');

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `ad_id` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `address_no` varchar(255) NOT NULL,
  `address_street` varchar(255) NOT NULL,
  `address_city` varchar(255) NOT NULL,
  `price` decimal(6,0) NOT NULL,
  `availability` enum('Available','Not Available') NOT NULL DEFAULT 'Available',
  `approval_status` enum('Approved','Pending','Denied') NOT NULL DEFAULT 'Pending',
  `approval_date` date DEFAULT NULL,
  `advertiser_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `nearestuni_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`ad_id`, `title`, `description`, `address_no`, `address_street`, `address_city`, `price`, `availability`, `approval_status`, `approval_date`, `advertiser_id`, `category_id`, `district_id`, `nearestuni_id`) VALUES
('673b5e2568408', 'House for Rent – 5 Min to University of Colombo', '3-bedroom house, 5-minute walk to campus. Great for sharing with friends. Includes kitchen, laundry, and Wi-Fi!', 'No: 25', 'Independence Avenue', 'Colombo 07', 35000, 'Available', 'Approved', '2024-11-18', 21, 1, 6, 11),
('673b5f2b34aba', 'Cozy 4-Bedroom – 10 Min Walk to University of Moratuwa', 'Spacious 4-bedroom house, 10 minutes from university. Ideal for groups. Fully equipped kitchen and living room!', 'No: 42', 'Katubedda Lane', 'Moratuwa', 40000, 'Available', 'Approved', '2024-11-18', 21, 4, 6, 15),
('673b606737c81', '2-Bedroom House – Near Peradeniya Campus (7 Min)', 'Comfortable 2-bedroom house, 7-minute walk to uni. Perfect for students. Includes Wi-Fi, backyard, and more!', 'No: 15', 'Sarasavi Lane', 'Peradeniya', 30000, 'Available', 'Approved', '2024-11-18', 23, 1, 8, 12),
('673b6290b611d', '3-Rooms in Student House – 8 Min to University of Peradeniya', '3 rooms in a shared house. Wi-Fi and utilities included. 8 minutes to campus. Great community vibe!', 'No: 31', 'Sarasavi Lane', 'Peradeniya', 30000, 'Available', 'Approved', '2024-11-18', 23, 1, 8, 12),
('673b7118936ef', 'Family Home – 12 Min to University of Kelaniya', '3-bedroom house, 12-minute walk to university. Great for students and families. Spacious living and outdoor space!', 'No: 8', 'Dalugama Road', 'Kelaniya', 45000, 'Available', 'Approved', '2024-11-18', 25, 1, 7, 14),
('673b72fbd28c0', 'Room for Rent – 10 Min to University of Kelaniya', 'Spacious room in student-friendly house. 10-minute walk to campus. All utilities and Wi-Fi included.', 'No: 5', 'Kelaniya Road', 'Kelaniya', 13000, 'Available', 'Approved', '2024-11-18', 25, 2, 7, 14),
('673b749deac92', 'Room in Shared House – 9 Min Walk to University of Jaffna', 'Private room in a shared 4-bedroom student house. Wi-Fi and all utilities included. 9 minutes to campus!', 'No: 12', 'Kokuvil Lane', 'Jaffna', 12000, 'Available', 'Approved', '2024-11-18', 27, 2, 11, 16),
('673b76725e76a', 'Shared Hostel Near University of Jaffna', 'Hostel with shared rooms, Wi-Fi, and meals. 9-minute walk to campus. Great student community and budget-friendly.', 'No: 11', 'Kokuvil East Road', 'Jaffna', 8000, 'Available', 'Approved', '2024-11-18', 27, 6, 11, 16),
('673cd08b06b8d', 'Room in Shared House – 5 Min Walk to University of Jaffna', 'Private room in a shared 4-bedroom student house. Wi-Fi and all utilities included. 9 minutes to campus', 'No: 14', 'Kokuvil Lane', 'Jaffna', 30000, 'Available', 'Pending', NULL, 27, 4, 11, 16);

-- --------------------------------------------------------

--
-- Table structure for table `listing_images`
--

CREATE TABLE `listing_images` (
  `image_id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `ad_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listing_images`
--

INSERT INTO `listing_images` (`image_id`, `image_name`, `ad_id`) VALUES
(29, '673b5e25687f2.jpg', '673b5e2568408'),
(30, '673b5e2569c01.jpg', '673b5e2568408'),
(31, '673b5e256afe6.jpg', '673b5e2568408'),
(32, '673b5e256c283.jpg', '673b5e2568408'),
(33, '673b5e256d482.jpg', '673b5e2568408'),
(34, '673b5f2b34eb6.jpg', '673b5f2b34aba'),
(35, '673b5f2b362f4.jpg', '673b5f2b34aba'),
(36, '673b5f2b3756c.jpg', '673b5f2b34aba'),
(37, '673b5f2b387db.jpg', '673b5f2b34aba'),
(38, '673b5f2b39cad.jpg', '673b5f2b34aba'),
(39, '673b606737f9a.jpg', '673b606737c81'),
(40, '673b606739765.jpg', '673b606737c81'),
(41, '673b60673adf4.jpg', '673b606737c81'),
(42, '673b60673c105.jpg', '673b606737c81'),
(43, '673b60673d491.jpg', '673b606737c81'),
(44, '673b6290b665f.jpg', '673b6290b611d'),
(45, '673b6290b7b39.jpg', '673b6290b611d'),
(46, '673b6290b8bde.jpg', '673b6290b611d'),
(47, '673b6290b9e82.jpg', '673b6290b611d'),
(48, '673b6290bb1de.jpg', '673b6290b611d'),
(53, '673b711893bcc.jpg', '673b7118936ef'),
(54, '673b71189552e.jpg', '673b7118936ef'),
(55, '673b711896967.jpg', '673b7118936ef'),
(56, '673b711897cc8.jpg', '673b7118936ef'),
(57, '673b711899102.jpg', '673b7118936ef'),
(58, '673b72fbd2c2d.png', '673b72fbd28c0'),
(59, '673b72fbd32b0.png', '673b72fbd28c0'),
(60, '673b749deb02f.png', '673b749deac92'),
(61, '673b749deb649.png', '673b749deac92'),
(62, '673b749deba3e.png', '673b749deac92'),
(63, '673b76725ea91.jpg', '673b76725e76a'),
(64, '673b7672605cc.jpg', '673b76725e76a'),
(65, '673b767261b71.jpg', '673b76725e76a'),
(66, '673cd08b0702f.jpg', '673cd08b06b8d'),
(67, '673cd08b0957a.jpg', '673cd08b06b8d'),
(68, '673cd08b0b996.jpg', '673cd08b06b8d');

-- --------------------------------------------------------

--
-- Table structure for table `nearestuni`
--

CREATE TABLE `nearestuni` (
  `uni_id` int(11) NOT NULL,
  `uni_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nearestuni`
--

INSERT INTO `nearestuni` (`uni_id`, `uni_name`) VALUES
(11, 'University of Colombo'),
(12, 'University of Peradeniya'),
(13, 'University of Sri Jayewardenepura'),
(14, 'University of Kelaniya'),
(15, 'University of Moratuwa'),
(16, 'University of Jaffna'),
(17, 'University of Ruhuna'),
(18, 'Eastern University, Sri Lanka'),
(19, 'South Eastern University of Sri Lanka');

-- --------------------------------------------------------

--
-- Table structure for table `pwdreset`
--

CREATE TABLE `pwdreset` (
  `pwdResetId` int(11) NOT NULL,
  `pwdResetEmail` varchar(100) NOT NULL,
  `pwdResetSelector` text NOT NULL,
  `pwdResetToken` longtext NOT NULL,
  `pwdResetExpires` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pwdreset`
--

INSERT INTO `pwdreset` (`pwdResetId`, `pwdResetEmail`, `pwdResetSelector`, `pwdResetToken`, `pwdResetExpires`) VALUES
(8, 'imansha.idr@gmail.com', '9b60c19a8d0a9b5f', '$2y$10$KGh//iWOV7R5r/odfY0s2uwvsuy8i2zsKAo83l.TyJui3snqSWHhe', '1731999168');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_payment`
--

CREATE TABLE `subscription_payment` (
  `payment_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `address_no` varchar(255) DEFAULT NULL,
  `address_street` varchar(255) DEFAULT NULL,
  `address_city` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `advertiser_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription_payment`
--

INSERT INTO `subscription_payment` (`payment_id`, `first_name`, `last_name`, `contact_no`, `address_no`, `address_street`, `address_city`, `email`, `payment_amount`, `payment_date`, `advertiser_id`) VALUES
(35, 'Dilshan', 'Imansha', '0761112244', 'No 101', 'Street 1', 'Colombo', 'dilshan@gmail.com', 600.00, '2024-11-18', 21),
(36, 'Mihiran', 'Sandaru', '0761112266', 'No 102', 'Sarasavi Lane', 'Peradeniya', 'mihiran@gmail.com', 3500.00, '2024-11-18', 23),
(37, 'Dananjaya', 'Nadun', '0761112288', 'No: 8', 'Dalugama Road', 'Kelaniya', 'dananjaya@gmail.com', 6500.00, '2024-11-18', 25),
(38, 'Weerakkodi', 'Sachini', '0761112222', 'No: 12', 'Kokuvil Lane', 'Jaffna', 'weerakkodi@gmail.com', 3500.00, '2024-11-18', 27),
(39, 'Mihiran', 'Sandaru', '0761112266', 'No 101', 'Street 2', 'Colombo', 'mihiran@gmail.com', 6500.00, '2024-11-19', 23),
(40, 'Mihiran', 'Sandaru', '0761112266', 'No 8', 'Dalugama Road', 'Kelaniya', 'mihiran@gmail.com', 3500.00, '2024-11-19', 23),
(41, 'Mihiran', 'Sandaru', '0761112266', 'No 102', 'Dalugama Road', 'Kelaniya', 'mihiran@gmail.com', 600.00, '2024-11-19', 23);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `plan_id` int(11) NOT NULL,
  `plan_type` varchar(100) NOT NULL,
  `plan_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`plan_id`, `plan_type`, `plan_price`) VALUES
(1, '1 month', 600.00),
(2, '6 month', 3500.00),
(3, '1 year', 6500.00);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `user_type` enum('seeker','advertiser') NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `email`, `password`, `contact_no`, `user_type`, `profile_pic`) VALUES
(20, 'Imansha', 'Dilshan', 'imansha.idr@gmail.com', '$2y$10$aTcyOs3GFXufV1z5qmj/XefVASZYY61iGSAXiMY5HsyqmHmQGFrMu', '0761112233', 'seeker', '20_1732037856.jpg'),
(21, 'Dilshan', 'Imansha', 'dilshan@gmail.com', '$2y$10$iF524GSAAEs9S7zyc1PAK.loKwuNjYTxEc7fFkGRTpxBnF6DkNXt6', '0761112244', 'advertiser', '21_1732038112.jpg'),
(22, 'Sandaru', 'Mihiran', 'sandaru@gmail.com', '$2y$10$FNNeZYwWd3CunGeC5Rfmj.FKeov9d5pydru2YXjXm9QUorf2cj3o2', '0761112255', 'seeker', '22_1732038259.jpg'),
(23, 'Mihiran', 'Sandaru', 'mihiran@gmail.com', '$2y$10$VVmfpuanBOSdzrnVLcdOTe/BKqmwnhMsQ/EOZfqddQeooDc3axs9.', '0761112266', 'advertiser', '23_1732038372.jpg'),
(24, 'Nadun', 'Dananjaya', 'nadun@gmail.com', '$2y$10$nPWiYADtFx47HdaFgIi2VekE071hvZVoC35Ak38SSe7NUP50d/.0e', '0761112277', 'seeker', '24_1732038470.jpg'),
(25, 'Dananjaya', 'Nadun', 'dananjaya@gmail.com', '$2y$10$kl1PzCxHeoosCNZSjmXh7OVgcVesfy/CwAS9AS8FClQPAWfiEAGwu', '0761112288', 'advertiser', '25_1732038577.jpg'),
(26, 'Sachini', 'Weerakkodi', 'sachini@gmail.com', '$2y$10$IMhuIrEg2u26LkZBjBar3OmJALSz1OZ1wUkHM8KS4EHqW9IUtjqkS', '0761112299', 'seeker', '26_1732038661.jpg'),
(27, 'Weerakkodi', 'Sachini', 'weerakkodi@gmail.com', '$2y$10$PLWHnGC4VXzniwi0ZpuSL.TdGQJWxgzWih4jDnaxMtiTy55xVEp4C', '0761112222', 'advertiser', '27_1732038687.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `advertiser_plan`
--
ALTER TABLE `advertiser_plan`
  ADD PRIMARY KEY (`advertiser_id`,`plan_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ad_id` (`ad_id`);

--
-- Indexes for table `contactus`
--
ALTER TABLE `contactus`
  ADD PRIMARY KEY (`sender_id`);

--
-- Indexes for table `district`
--
ALTER TABLE `district`
  ADD PRIMARY KEY (`district_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`fav_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ad_id` (`ad_id`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`ad_id`),
  ADD KEY `advertiser_id` (`advertiser_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `district_id` (`district_id`),
  ADD KEY `nearestuni_id` (`nearestuni_id`);

--
-- Indexes for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `ad_id` (`ad_id`);

--
-- Indexes for table `nearestuni`
--
ALTER TABLE `nearestuni`
  ADD PRIMARY KEY (`uni_id`);

--
-- Indexes for table `pwdreset`
--
ALTER TABLE `pwdreset`
  ADD PRIMARY KEY (`pwdResetId`);

--
-- Indexes for table `subscription_payment`
--
ALTER TABLE `subscription_payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `advertiser_id` (`advertiser_id`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `sender_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `district`
--
ALTER TABLE `district`
  MODIFY `district_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `fav_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `listing_images`
--
ALTER TABLE `listing_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `nearestuni`
--
ALTER TABLE `nearestuni`
  MODIFY `uni_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `pwdreset`
--
ALTER TABLE `pwdreset`
  MODIFY `pwdResetId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subscription_payment`
--
ALTER TABLE `subscription_payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`ad_id`) REFERENCES `listings` (`ad_id`);

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`ad_id`) REFERENCES `listings` (`ad_id`);

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`advertiser_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `listings_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `listings_ibfk_3` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`),
  ADD CONSTRAINT `listings_ibfk_4` FOREIGN KEY (`nearestuni_id`) REFERENCES `nearestuni` (`uni_id`);

--
-- Constraints for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD CONSTRAINT `listing_images_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `listings` (`ad_id`);

--
-- Constraints for table `subscription_payment`
--
ALTER TABLE `subscription_payment`
  ADD CONSTRAINT `subscription_payment_ibfk_1` FOREIGN KEY (`advertiser_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
