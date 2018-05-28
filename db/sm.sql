-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2018 at 02:41 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sm`
--

-- --------------------------------------------------------

--
-- Table structure for table `locks`
--

CREATE TABLE IF NOT EXISTS `locks` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `lockType` varchar(20) NOT NULL,
  `lockSrc` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `lockType` (`lockType`,`lockSrc`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `locks`
--

INSERT INTO `locks` (`ID`, `lockType`, `lockSrc`) VALUES
(1, 'normalLocked', 'img/locks/lockLocked.png'),
(2, 'normalUnlocked', 'img/locks/lockUnlocked.png'),
(3, 'overlockLocked', 'img/locks/overlockLocked.png'),
(4, 'overlockUnlocked', 'img/locks/overlockUnlocked.png'),
(5, 'unavailableLocked', 'img/locks/unavailableLocked.png'),
(6, 'unavailableUnlocked', 'img/locks/unavailableUnlocked.png');

-- --------------------------------------------------------

--
-- Table structure for table `romsjekk`
--

CREATE TABLE IF NOT EXISTS `romsjekk` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `roomNum` int(4) NOT NULL,
  `poster` tinyint(1) NOT NULL DEFAULT '0',
  `depth` float DEFAULT NULL,
  `width` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `lockType` int(10) NOT NULL DEFAULT '2',
  `notes` text NOT NULL,
  `moveOutDate` date DEFAULT NULL,
  `price` int(10) DEFAULT NULL,
  `sizeCode` decimal(4,0) DEFAULT NULL,
  `available` tinyint(1) DEFAULT NULL,
  `roomClass` varchar(1) DEFAULT NULL,
  `measuredDate` datetime(6) DEFAULT NULL,
  `roomCheckActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `roomNum` (`roomNum`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;

--
-- Dumping data for table `romsjekk`
--

INSERT INTO `romsjekk` (`ID`, `roomNum`, `poster`, `depth`, `width`, `height`, `lockType`, `notes`, `moveOutDate`, `price`, `sizeCode`, `available`, `roomClass`, `measuredDate`, `roomCheckActive`) VALUES
(44, 2122, 1, 2, 3, 0, 4, 'Fint rom da', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 0),
(46, 2413, 0, 0, 0, 0, 4, 'Empty room', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 1),
(55, 0, 0, 0, 0, 0, 1, '', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 0),
(56, 3132, 0, 0, 0, 0, 2, '', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 0),
(57, 3122, 0, 0, 0, 0, 1, '', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 0),
(58, 3123, 0, 0, 0, 0, 2, '', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 0),
(59, 3413, 0, 0, 0, 0, 2, '', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 0),
(60, 2222, 0, 0, 0, 0, 1, '', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 1),
(61, 2112, 0, 0, 0, 0, 2, '', '1970-01-01', NULL, NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `roomNum` int(4) NOT NULL,
  `depth` int(4) DEFAULT NULL,
  `width` int(4) DEFAULT NULL,
  `height` int(4) DEFAULT NULL,
  `measuredDate` date DEFAULT NULL,
  `poster` tinyint(1) DEFAULT '0',
  `sizeCode` int(2) DEFAULT NULL,
  `price` int(10) DEFAULT NULL,
  `available` tinyint(1) NOT NULL DEFAULT '1',
  `roomClass` varchar(1) NOT NULL DEFAULT 'B',
  `moveOutDate` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roomNum` (`roomNum`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
