-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2016 at 11:58 PM
-- Server version: 5.5.27
-- PHP Version: 5.3.8-ZS5.5.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `lang_repo`
--

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `is_default` varchar(10) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `is_default`) VALUES
(1, 'Arabic', 'no'),
(2, 'Bularian', 'no'),
(3, 'Catalan', 'no'),
(4, 'Czech', 'no'),
(5, 'Danish', 'no'),
(6, 'Dutch', 'no'),
(7, 'English', 'yes'),
(8, 'Filipino', 'no'),
(9, 'French', 'no'),
(10, 'German', 'no'),
(11, 'Gujarati', 'no'),
(12, 'Hindi', 'no'),
(13, 'Hungarian', 'no'),
(14, 'Indonesian', 'no'),
(15, 'Italian', 'no'),
(16, 'Japanese', 'no'),
(17, 'Khmer', 'no'),
(18, 'Korean', 'no'),
(19, 'Norwegian', 'no'),
(20, 'Persian', 'no'),
(21, 'Polish', 'no'),
(22, 'Portuguese', 'no'),
(23, 'Romanian', 'no'),
(24, 'Russian', 'no'),
(25, 'Simplified-chinese', 'no'),
(26, 'Spanish', 'no'),
(27, 'Swedish', 'no'),
(28, 'Tamil', 'no'),
(29, 'Thai', 'no'),
(30, 'Traditional-chinese', 'no'),
(31, 'Turkish', 'no'),
(32, 'Ukrainian', 'no'),
(33, 'Urdu', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `revisions`
--

CREATE TABLE IF NOT EXISTS `revisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `revision_name` varchar(100) NOT NULL,
  `master_lang_id` int(11) NOT NULL,
  `lang_root_dir` text NOT NULL,
  `master_lang_root_dir` varchar(255) NOT NULL,
  `target_ci_url` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_ended` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`, `description`) VALUES
(1, 'admin', 'Global Admin', 'Super user who has all previlleges for all users.'),
(2, 'moderator', 'Moderator', 'Dedicated for 1 language, able to read accounts for translator and proof reader in his language'),
(3, 'translator', 'Translator', 'Dedicated for 1 language, able to translate for his language'),
(4, 'proofer', 'Proof Reader', 'Dedicated for 1 language, able to proof the translation for his language');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refer_id` int(11) NOT NULL,
  `is_global_admin` varchar(10) NOT NULL DEFAULT 'no',
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `language_ids` text NOT NULL,
  `pwd_token` char(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `refer_id`, `is_global_admin`, `username`, `password`, `email`, `language_ids`, `pwd_token`) VALUES
(1, 0, 'yes', 'Administrator', 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec', 'gadmin@24online.com', '[]', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
