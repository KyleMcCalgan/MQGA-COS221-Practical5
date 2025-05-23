-- phpMyAdmin SQL Dump
-- version 5.0.4deb2~bpo10+1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 23, 2025 at 12:26 PM
-- Server version: 10.3.39-MariaDB-0+deb10u2
-- PHP Version: 7.3.31-1~deb10u7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u24566552_MQGA`
--

-- --------------------------------------------------------

--
-- Table structure for table `ADMINS`
--

CREATE TABLE `ADMINS` (
  `id` int(11) NOT NULL,
  `store_id` int(11) DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `BOOK_CATS`
--

CREATE TABLE `BOOK_CATS` (
  `book_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `CATEGORIES`
--

CREATE TABLE `CATEGORIES` (
  `category_id` int(11) NOT NULL,
  `searchable` tinyint(1) DEFAULT NULL,
  `genre` varchar(100) NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `PRODUCTS`
--

CREATE TABLE `PRODUCTS` (
  `id` int(11) NOT NULL,
  `tempID` varchar(50) NOT NULL,
  `title` text NOT NULL,
  `description` text DEFAULT NULL,
  `isbn13` varchar(13) DEFAULT NULL,
  `publishedDate` date DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `pageCount` int(11) DEFAULT NULL,
  `maturityRating` varchar(50) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `smallThumbnail` varchar(512) DEFAULT NULL,
  `thumbnail` varchar(512) DEFAULT NULL,
  `accessibleIn` varchar(100) DEFAULT NULL,
  `ratingsCount` int(11) DEFAULT 0
) ;

-- --------------------------------------------------------

--
-- Table structure for table `RATINGS`
--

CREATE TABLE `RATINGS` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` smallint(6) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5)
);

-- --------------------------------------------------------

--
-- Table structure for table `REVIEWS`
--

CREATE TABLE `REVIEWS` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review` text DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `STORES`
--

CREATE TABLE `STORES` (
  `store_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `logo` varchar(512) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `STORE_INFO`
--

CREATE TABLE `STORE_INFO` (
  `book_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL CHECK (`rating` is null or `rating` >= 1.00 and `rating` <= 5.00)
);

-- --------------------------------------------------------

--
-- Table structure for table `USERS`
--

CREATE TABLE `USERS` (
  `id` int(11) NOT NULL,
  `apikey` varchar(64) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(64) NOT NULL,
  `user_type` enum('super','regular','admin') NOT NULL
);


--
-- Indexes for table `ADMINS`
--
ALTER TABLE `ADMINS`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admins_store_id` (`store_id`);

--
-- Indexes for table `BOOK_CATS`
--
ALTER TABLE `BOOK_CATS`
  ADD PRIMARY KEY (`book_id`,`category_id`),
  ADD KEY `idx_bookcats_category_book` (`category_id`,`book_id`);

--
-- Indexes for table `CATEGORIES`
--
ALTER TABLE `CATEGORIES`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `genre` (`genre`),
  ADD KEY `idx_categories_searchable` (`searchable`);

--
-- Indexes for table `PRODUCTS`
--
ALTER TABLE `PRODUCTS`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tempID` (`tempID`),
  ADD UNIQUE KEY `isbn13` (`isbn13`),
  ADD KEY `idx_products_author` (`author`),
  ADD KEY `idx_products_publisher` (`publisher`);

--
-- Indexes for table `RATINGS`
--
ALTER TABLE `RATINGS`
  ADD PRIMARY KEY (`id`,`book_id`,`user_id`),
  ADD UNIQUE KEY `uq_user_book_rating` (`user_id`,`book_id`),
  ADD KEY `idx_ratings_book_user` (`book_id`,`user_id`);

--
-- Indexes for table `REVIEWS`
--
ALTER TABLE `REVIEWS`
  ADD PRIMARY KEY (`id`,`book_id`,`user_id`),
  ADD UNIQUE KEY `uq_user_book_review` (`user_id`,`book_id`),
  ADD KEY `idx_reviews_book_user` (`book_id`,`user_id`);

--
-- Indexes for table `STORES`
--
ALTER TABLE `STORES`
  ADD PRIMARY KEY (`store_id`),
  ADD UNIQUE KEY `domain` (`domain`),
  ADD KEY `idx_stores_name` (`name`);

--
-- Indexes for table `STORE_INFO`
--
ALTER TABLE `STORE_INFO`
  ADD PRIMARY KEY (`store_id`,`book_id`),
  ADD KEY `idx_storeinfo_book_store` (`book_id`,`store_id`),
  ADD KEY `idx_storeinfo_price` (`price`),
  ADD KEY `idx_storeinfo_rating` (`rating`);

--
-- Indexes for table `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `apikey` (`apikey`);

--
-- AUTO_INCREMENT for table `CATEGORIES`
--
ALTER TABLE `CATEGORIES`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PRODUCTS`
--
ALTER TABLE `PRODUCTS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RATINGS`
--
ALTER TABLE `RATINGS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `REVIEWS`
--
ALTER TABLE `REVIEWS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `STORES`
--
ALTER TABLE `STORES`
  MODIFY `store_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `USERS`
--
ALTER TABLE `USERS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for table `ADMINS`
--
ALTER TABLE `ADMINS`
  ADD CONSTRAINT `ADMINS_ibfk_1` FOREIGN KEY (`id`) REFERENCES `USERS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ADMINS_ibfk_2` FOREIGN KEY (`store_id`) REFERENCES `STORES` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `BOOK_CATS`
--
ALTER TABLE `BOOK_CATS`
  ADD CONSTRAINT `fk_bookcats_book` FOREIGN KEY (`book_id`) REFERENCES `PRODUCTS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookcats_category` FOREIGN KEY (`category_id`) REFERENCES `CATEGORIES` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `RATINGS`
--
ALTER TABLE `RATINGS`
  ADD CONSTRAINT `fk_rating_book` FOREIGN KEY (`book_id`) REFERENCES `PRODUCTS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rating_user` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `REVIEWS`
--
ALTER TABLE `REVIEWS`
  ADD CONSTRAINT `fk_review_book` FOREIGN KEY (`book_id`) REFERENCES `PRODUCTS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `STORE_INFO`
--
ALTER TABLE `STORE_INFO`
  ADD CONSTRAINT `fk_storeinfo_book` FOREIGN KEY (`book_id`) REFERENCES `PRODUCTS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_storeinfo_store` FOREIGN KEY (`store_id`) REFERENCES `STORES` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
