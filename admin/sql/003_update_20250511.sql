-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-05-11 08:01:49
-- 伺服器版本： 10.4.21-MariaDB
-- PHP 版本： 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫: `op_ranking`
--

-- --------------------------------------------------------

--
-- 資料表結構 `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url` text NOT NULL,
  `image_url` text NOT NULL,
  `rank` int(11) NOT NULL,
  `impression` int(11) NOT NULL DEFAULT 0,
  `click` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `banner_click_log`
--

CREATE TABLE `banner_click_log` (
  `id` int(11) NOT NULL,
  `banner_id` int(11) NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `banner_impression_log`
--

CREATE TABLE `banner_impression_log` (
  `id` int(11) NOT NULL,
  `banner_id` int(11) NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `banner_click_log`
--
ALTER TABLE `banner_click_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_CLICK_BANNER` (`banner_id`);

--
-- 資料表索引 `banner_impression_log`
--
ALTER TABLE `banner_impression_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_IMPRESSIONN_BANNER` (`banner_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `banner_click_log`
--
ALTER TABLE `banner_click_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `banner_impression_log`
--
ALTER TABLE `banner_impression_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `banner_click_log`
--
ALTER TABLE `banner_click_log`
  ADD CONSTRAINT `FK_CLICK_BANNER` FOREIGN KEY (`banner_id`) REFERENCES `banner` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 資料表的限制式 `banner_impression_log`
--
ALTER TABLE `banner_impression_log`
  ADD CONSTRAINT `FK_IMPRESSIONN_BANNER` FOREIGN KEY (`banner_id`) REFERENCES `banner` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
