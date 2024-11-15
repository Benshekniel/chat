-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 11, 2024 at 12:56 PM
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
-- Database: `mychat_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `sender` bigint NOT NULL,
  `receiver` bigint NOT NULL,
  `message` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `files` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `date` datetime NOT NULL,
  `seen` int NOT NULL DEFAULT '0',
  `received` int NOT NULL DEFAULT '0',
  `deleted_sender` tinyint NOT NULL DEFAULT '0',
  `deleted_receiver` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sender` (`sender`),
  KEY `receiver` (`receiver`),
  KEY `date` (`date`),
  KEY `seen` (`seen`),
  KEY `deleted_sender` (`deleted_sender`),
  KEY `deleted_receiver` (`deleted_receiver`)
) ENGINE=InnoDB AUTO_INCREMENT=208 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `sender`, `receiver`, `message`, `files`, `date`, `seen`, `received`, `deleted_sender`, `deleted_receiver`) VALUES
(151, 3, 4, 'supper', NULL, '2024-11-07 17:53:20', 1, 1, 0, 1),
(152, 4, 3, '?', NULL, '2024-11-07 17:53:42', 1, 1, 0, 0),
(153, 4, 3, 'hello', NULL, '2024-11-07 18:00:52', 1, 1, 1, 0),
(154, 2, 3, 'okay', NULL, '2024-11-07 18:17:12', 1, 1, 0, 0),
(155, 3, 2, 'hey', NULL, '2024-11-07 18:17:39', 1, 1, 0, 0),
(156, 3, 4, 'hello', NULL, '2024-11-08 08:23:07', 1, 1, 0, 0),
(157, 3, 4, 'hi da', NULL, '2024-11-08 08:23:49', 1, 1, 1, 0),
(158, 3, 4, 'hello', NULL, '2024-11-08 08:26:16', 1, 1, 0, 0),
(159, 3, 4, 'hello', NULL, '2024-11-08 08:30:08', 1, 1, 0, 0),
(161, 4, 3, 'Benshekniel', NULL, '2024-11-08 08:45:38', 1, 1, 0, 0),
(162, 4, 2, 'deleted?', NULL, '2024-11-08 08:46:11', 1, 0, 0, 1),
(163, 4, 2, 'hello ?', NULL, '2024-11-08 08:47:01', 1, 0, 1, 0),
(164, 4, 2, 'how are you?', NULL, '2024-11-08 08:47:09', 1, 0, 1, 0),
(165, 4, 2, 'hello', NULL, '2024-11-08 08:49:03', 1, 0, 1, 1),
(166, 4, 2, 'hello', NULL, '2024-11-08 08:50:27', 1, 0, 0, 0),
(167, 4, 3, 'sender', NULL, '2024-11-08 08:50:41', 1, 1, 0, 0),
(168, 4, 2, 'sender', NULL, '2024-11-08 08:50:50', 1, 0, 1, 0),
(169, 2, 4, 'hello', NULL, '2024-11-08 09:08:25', 1, 1, 1, 1),
(170, 4, 2, 'howare you?', NULL, '2024-11-08 09:09:11', 1, 0, 1, 0),
(171, 4, 2, 'asdf', NULL, '2024-11-08 09:16:56', 1, 0, 0, 1),
(172, 2, 4, 'asdggeesfasdgds', NULL, '2024-11-08 09:17:42', 1, 1, 1, 1),
(173, 2, 4, 'asdfgggee', NULL, '2024-11-08 09:18:19', 1, 1, 1, 1),
(174, 4, 2, 'hello', NULL, '2024-11-08 09:19:52', 1, 0, 0, 1),
(175, 4, 2, 'asdfggg', NULL, '2024-11-08 09:20:40', 1, 0, 1, 0),
(176, 2, 4, 'hello dletes', NULL, '2024-11-08 09:33:41', 1, 1, 0, 1),
(177, 4, 2, 'hello', NULL, '2024-11-08 09:34:24', 1, 0, 0, 1),
(178, 2, 4, 'hello now delete?', NULL, '2024-11-08 09:40:44', 1, 1, 1, 0),
(179, 2, 4, 'yes Isee', NULL, '2024-11-08 09:40:51', 1, 1, 0, 1),
(180, 4, 2, 'it is working', NULL, '2024-11-08 09:42:02', 1, 0, 1, 0),
(181, 2, 4, 'Good', NULL, '2024-11-08 09:44:00', 1, 1, 0, 0),
(182, 2, 4, 'hello sie', NULL, '2024-11-08 09:56:35', 1, 1, 0, 0),
(183, 2, 3, 'asdf', NULL, '2024-11-08 10:20:11', 1, 1, 0, 0),
(184, 2, 4, 'zxcv', NULL, '2024-11-08 10:20:23', 1, 1, 0, 0),
(185, 2, 4, 'asdf', NULL, '2024-11-08 10:22:17', 1, 1, 0, 0),
(186, 2, 4, 'asdfgg', NULL, '2024-11-08 10:23:06', 1, 1, 0, 0),
(187, 2, 4, 'hello', NULL, '2024-11-08 10:23:16', 1, 1, 0, 0),
(188, 2, 4, 'hi', NULL, '2024-11-08 10:23:23', 1, 1, 0, 0),
(189, 2, 4, 'asdfwwee', NULL, '2024-11-08 10:23:38', 1, 1, 0, 0),
(190, 2, 4, 'asde', NULL, '2024-11-08 10:24:45', 1, 1, 0, 1),
(191, 2, 4, 'tehsdle', NULL, '2024-11-08 10:24:54', 1, 1, 1, 0),
(192, 4, 1, 'hello', NULL, '2024-11-08 10:59:45', 0, 0, 1, 0),
(195, 4, 2, '$%#4', NULL, '2024-11-08 11:06:38', 1, 0, 0, 0),
(196, 4, 2, '$#2', NULL, '2024-11-08 11:06:51', 1, 0, 0, 0),
(197, 4, 2, '<insert>', NULL, '2024-11-08 13:12:14', 0, 0, 1, 0),
(198, 4, 2, 'sory', NULL, '2024-11-08 13:12:22', 0, 0, 0, 0),
(199, 4, 2, 'insert', NULL, '2024-11-08 13:12:33', 0, 0, 1, 0),
(200, 4, 3, 'chat', NULL, '2024-11-08 14:29:08', 1, 0, 0, 1),
(201, 4, 3, 'hclaksjdf', NULL, '2024-11-08 14:29:41', 1, 0, 1, 0),
(202, 4, 3, 'hello da', NULL, '2024-11-11 09:52:16', 0, 0, 0, 0),
(203, 4, 3, 'ss', NULL, '2024-11-11 09:56:55', 0, 0, 0, 0),
(204, 4, 3, '', NULL, '2024-11-11 09:57:06', 0, 0, 0, 0),
(205, 4, 2, 'hello', NULL, '2024-11-11 09:59:25', 0, 0, 0, 0),
(206, 4, 2, 'ere', NULL, '2024-11-11 09:59:47', 0, 0, 0, 0),
(207, 4, 2, 'jlkjasdfe', NULL, '2024-11-11 10:00:20', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gender` varchar(6) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `gender` (`gender`),
  KEY `date` (`date`),
  KEY `state` (`state`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `userid`, `username`, `email`, `gender`, `password`, `date`, `image`, `state`) VALUES
(1, 239152703, 'Eathorne', 'eathorne@yahoo.com', 'Male', 'password', '2020-12-25 15:31:32', 'uploads/afro-beautiful-black-women-fashion-Favim.com-3980589.jpg', 0),
(2, 89701890839882223, 'Maran', 'mary@yahoo.com', 'male', 'password', '2020-12-25 15:31:49', NULL, 1),
(3, 1148711, 'John', 'john@yahoo.com', 'Male', 'password', '2020-12-25 15:32:10', 'uploads/handsome-adult-black-man-successful-business-african-person-117063782.jpg', 1),
(4, 553245684553, 'Benshekniel', 'benshekniel@gmail.com', 'Male', 'core1234', '2024-11-06 08:53:30', 'uploads/IMG-20240614-WA0012.jpg', 1),
(6, NULL, 'Relam', 'relam@yahoo.com', 'Male', '$2y$10$tyxJVqxxBfi/kN.Yt.jKCuGU7bSAZQBGh2Bq4fCZzSOcOQs5seq2a', '2024-11-09 12:58:34', NULL, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
