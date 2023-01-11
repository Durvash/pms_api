-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 11, 2023 at 11:52 PM
-- Server version: 8.0.18
-- PHP Version: 7.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pms`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_request_log`
--

DROP TABLE IF EXISTS `api_request_log`;
CREATE TABLE IF NOT EXISTS `api_request_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `device` varchar(64) DEFAULT NULL,
  `auth_token` text,
  `user_id` bigint(20) DEFAULT NULL,
  `headers` text,
  `api_name` varchar(64) DEFAULT NULL,
  `request` text,
  `response` longtext,
  `process_time` int(11) DEFAULT NULL,
  `added_date` datetime NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `board_check_list`
--

DROP TABLE IF EXISTS `board_check_list`;
CREATE TABLE IF NOT EXISTS `board_check_list` (
  `check_list_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) NOT NULL,
  `check_list_title` text NOT NULL,
  `assign_to` bigint(20) NOT NULL,
  `report_to` bigint(20) NOT NULL,
  `priority` enum('Low','Medium','High') DEFAULT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`check_list_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `board_list_comments`
--

DROP TABLE IF EXISTS `board_list_comments`;
CREATE TABLE IF NOT EXISTS `board_list_comments` (
  `comment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) NOT NULL,
  `comment` text NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `board_master`
--

DROP TABLE IF EXISTS `board_master`;
CREATE TABLE IF NOT EXISTS `board_master` (
  `board_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `board_name` varchar(64) NOT NULL,
  `board_slug` varchar(64) NOT NULL,
  `board_desc` text,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`board_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `board_tab_list`
--

DROP TABLE IF EXISTS `board_tab_list`;
CREATE TABLE IF NOT EXISTS `board_tab_list` (
  `tab_list_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tab_list_name` varchar(64) NOT NULL,
  `board_id` bigint(20) NOT NULL,
  PRIMARY KEY (`tab_list_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `board_task_list`
--

DROP TABLE IF EXISTS `board_task_list`;
CREATE TABLE IF NOT EXISTS `board_task_list` (
  `task_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tab_list_id` bigint(20) NOT NULL,
  `task_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `task_desc` text,
  `assign_to` bigint(20) NOT NULL,
  `report_to` bigint(20) NOT NULL,
  `priority` enum('Low','Medium','High') DEFAULT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `company_master`
--

DROP TABLE IF EXISTS `company_master`;
CREATE TABLE IF NOT EXISTS `company_master` (
  `company_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(256) NOT NULL,
  `company_info` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_execution`
--

DROP TABLE IF EXISTS `email_execution`;
CREATE TABLE IF NOT EXISTS `email_execution` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `receiver` varchar(64) NOT NULL,
  `cc_email` text,
  `bcc_email` text,
  `subject` text NOT NULL,
  `content` longtext,
  `attachment` text,
  `status` tinyint(1) DEFAULT '0',
  `added_date` datetime NOT NULL,
  `executed_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `general_setting`
--

DROP TABLE IF EXISTS `general_setting`;
CREATE TABLE IF NOT EXISTS `general_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `display_type` enum('text','dropdown','textarea','checkbox','json') DEFAULT NULL,
  `select_type` enum('single','multiple') DEFAULT NULL,
  `source` text,
  `value` text,
  `added_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `login_log`
--

DROP TABLE IF EXISTS `login_log`;
CREATE TABLE IF NOT EXISTS `login_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `token` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `device` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `added_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `project_master`
--

DROP TABLE IF EXISTS `project_master`;
CREATE TABLE IF NOT EXISTS `project_master` (
  `project_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(64) NOT NULL,
  `project_slug` varchar(64) NOT NULL,
  `project_desc` text,
  `lead_by` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

DROP TABLE IF EXISTS `project_members`;
CREATE TABLE IF NOT EXISTS `project_members` (
  `member_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--

DROP TABLE IF EXISTS `user_master`;
CREATE TABLE IF NOT EXISTS `user_master` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `last_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(64) NOT NULL,
  `username` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `password` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_verify` tinyint(1) NOT NULL DEFAULT '0',
  `company_id` bigint(20) DEFAULT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
