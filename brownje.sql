-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 17, 2015 at 04:04 PM
-- Server version: 5.5.35
-- PHP Version: 5.4.23

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `brownje`
--

-- --------------------------------------------------------

--
-- Table structure for table `Games`
--

CREATE TABLE IF NOT EXISTS `Games` (
  `GameID` int(11) NOT NULL AUTO_INCREMENT,
  `Turn` int(11) NOT NULL,
  `Board1` text CHARACTER SET utf8 NOT NULL,
  `Board2` text CHARACTER SET utf8 NOT NULL,
  `Player1` text CHARACTER SET utf8 NOT NULL,
  `Player2` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`GameID`),
  UNIQUE KEY `GameID` (`GameID`),
  KEY `GameID_2` (`GameID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=124 ;

--
-- Dumping data for table `Games`
--

INSERT INTO `Games` (`GameID`, `Turn`, `Board1`, `Board2`, `Player1`, `Player2`) VALUES
(122, 2, 'WSSSSSWWWWWWSSWWWWWWWWWWWWWWWWWWWWWWWWWWWWMWWWWWW', 'MWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWHSSSSSS', 'acnmnm', 'nnmmmm');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `Username` text CHARACTER SET utf8 NOT NULL,
  `Password` text CHARACTER SET utf8 NOT NULL,
  `BoardLength` int(11) NOT NULL,
  `NumShips` int(11) NOT NULL,
  `GameID` int(11) NOT NULL,
  `InGame` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`Username`, `Password`, `BoardLength`, `NumShips`, `GameID`, `InGame`) VALUES
('acnmnm', '', 7, 2, 122, 1),
('nnmmmm', '', 7, 2, 122, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
