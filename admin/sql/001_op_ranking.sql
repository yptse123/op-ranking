-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2023-05-28 12:26:23
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
CREATE DATABASE IF NOT EXISTS `op_ranking` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `op_ranking`;

-- --------------------------------------------------------

--
-- 資料表結構 `ranking`
--

CREATE TABLE `ranking` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `thumbnail_url` text NOT NULL,
  `rank` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 傾印資料表的資料 `ranking`
--

INSERT INTO `ranking` (`id`, `title`, `url`, `thumbnail_url`, `rank`, `created_at`, `updated_at`) VALUES
(1, '救世少女', 'http://localhost/op-ranking-page/images/game1.png', 'http://localhost/op-ranking-page/images/game1.png', 999, '2023-05-28 08:29:56', '2023-05-28 08:58:52'),
(2, '寶石姬', 'http://localhost/op-ranking-page/images/game2.png', 'http://localhost/op-ranking-page/images/game2.png', 998, '2023-05-28 08:57:56', '2023-05-28 08:58:02');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `ranking`
--
ALTER TABLE `ranking`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `ranking`
--
ALTER TABLE `ranking`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
