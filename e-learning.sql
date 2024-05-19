-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: May 19, 2024 at 05:40 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-learning`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int NOT NULL,
  `course_id` int NOT NULL,
  `course_title` varchar(500) NOT NULL,
  `course_img` varchar(3000) NOT NULL,
  `course_author` varchar(100) NOT NULL,
  `course_price` float NOT NULL,
  `username` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `course_id`, `course_title`, `course_img`, `course_author`, `course_price`, `username`) VALUES
(1, 1, 'HTML tutorial', 'https://scontent.xx.fbcdn.net/v/t1.15752-9/437541553_959933341998257_7336077621742206131_n.png?_nc_cat=104&ccb=1-7&_nc_sid=5f2048&_nc_ohc=Ty6cWtHNPocQ7kNvgGQMsvC&_nc_ad=z-m&_nc_cid=0&_nc_ht=scontent.xx&cb_e2o_trans=q&oh=03_Q7cD1QFnOzrecuEm7UnbXczhXxANgzmv1U2yBqPREwWKPWv7SQ&oe=6651D63A', 'HUST', 500, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `detail` varchar(3000) NOT NULL,
  `slide1` varchar(3000) NOT NULL,
  `slide2` varchar(3000) NOT NULL,
  `slide3` varchar(3000) NOT NULL,
  `author` varchar(50) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `download_link` varchar(1000) NOT NULL,
  `price` float NOT NULL,
  `view` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `title`, `description`, `detail`, `slide1`, `slide2`, `slide3`, `author`, `date_created`, `download_link`, `price`, `view`) VALUES
(1, 'HTML tutorial', 'A tutorial about html', 'A tutorial about html', 'https://scontent.xx.fbcdn.net/v/t1.15752-9/437541553_959933341998257_7336077621742206131_n.png?_nc_cat=104&ccb=1-7&_nc_sid=5f2048&_nc_ohc=Ty6cWtHNPocQ7kNvgGQMsvC&_nc_ad=z-m&_nc_cid=0&_nc_ht=scontent.xx&cb_e2o_trans=q&oh=03_Q7cD1QFnOzrecuEm7UnbXczhXxANgzmv1U2yBqPREwWKPWv7SQ&oe=6651D63A', 'https://scontent.xx.fbcdn.net/v/t1.15752-9/437574790_738962241756900_1268408068783006372_n.png?_nc_cat=106&ccb=1-7&_nc_sid=5f2048&_nc_ohc=PG4IVtsUGSsQ7kNvgHObqyh&_nc_oc=AdiSYm4Rb-ed1qcnMLMVK9_UDrqgINn1i9kFBQVEGZ-Yu2HvCX8pReGYncRpCmNIUx2OeYmfLWwrs4PSHLR3lKLx&_nc_ad=z-m&_nc_cid=0&_nc_ht=scontent.xx&cb_e2o_trans=q&oh=03_Q7cD1QFERi1TGhVwDGBYkMbvkavTLRztWZG_OipEfpPuuxaXLw&oe=6651D5DE', 'https://scontent.xx.fbcdn.net/v/t1.15752-9/437272521_438555378550206_8381312279525518651_n.png?_nc_cat=107&ccb=1-7&_nc_sid=5f2048&_nc_ohc=FXNDjNQVNp4Q7kNvgEHNNef&_nc_ad=z-m&_nc_cid=0&_nc_ht=scontent.xx&cb_e2o_trans=q&oh=03_Q7cD1QG6Jttl-5E2nRliChzGIxAy3OkTFe0XDLv826SvirfsUw&oe=6651CF38', 'HUST', '2024-05-19 17:39:52', '#', 500, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(40) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `address` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_role` varchar(40) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phone`, `address`, `user_role`) VALUES
(2, 'test', 'pholopho315@gmail.com', '5a105e8b9d40e1329780d62ea2265d8a', NULL, NULL, 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
