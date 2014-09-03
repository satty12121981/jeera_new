-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 14, 2013 at 11:00 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `1625`
--

-- --------------------------------------------------------

--
-- Table structure for table `y2m_album`
--

CREATE TABLE IF NOT EXISTS `y2m_album` (
  `album_id` int(11) NOT NULL AUTO_INCREMENT,
  `album_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `album_added_ip_address` int(7) unsigned DEFAULT NULL,
  `album_status` tinyint(1) DEFAULT '0',
  `album_title` varchar(200) DEFAULT NULL,
  `album_discription` text,
  `album_user_id` int(11) DEFAULT NULL,
  `album_cover_photo_id` int(11) DEFAULT NULL,
  `album_location` varchar(200) DEFAULT NULL,
  `album_view_counter` int(11) DEFAULT NULL,
  `album_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `album_modified_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`album_id`),
  KEY `album_user_id` (`album_user_id`),
  KEY `album_cover_photo_id` (`album_cover_photo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `y2m_album`
--

INSERT INTO `y2m_album` (`album_id`, `album_added_timestamp`, `album_added_ip_address`, `album_status`, `album_title`, `album_discription`, `album_user_id`, `album_cover_photo_id`, `album_location`, `album_view_counter`, `album_modified_timestamp`, `album_modified_ip_address`) VALUES
(1, '2013-05-09 13:00:39', NULL, NULL, 'Test 1', NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', NULL),
(2, '2013-05-09 13:00:58', NULL, NULL, 'Test 2', NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', NULL),
(3, '2013-05-09 13:01:18', NULL, NULL, 'Test 3', NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_banned_ip`
--

CREATE TABLE IF NOT EXISTS `y2m_banned_ip` (
  `banned_ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `banned_ip_address` int(7) unsigned DEFAULT NULL,
  `banned_ip_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `banned_ip_reason` text,
  PRIMARY KEY (`banned_ip_id`),
  UNIQUE KEY `banned_ip_address` (`banned_ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_comment`
--

CREATE TABLE IF NOT EXISTS `y2m_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_system_type_id` int(11) DEFAULT NULL,
  `comment_by_user_id` int(11) DEFAULT NULL,
  `comment_status` tinyint(1) DEFAULT '0',
  `comment_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment_refer_id` int(11) DEFAULT NULL,
  `comment_added_ip_address` int(7) unsigned DEFAULT NULL,
  `comment_content` text,
  PRIMARY KEY (`comment_id`),
  KEY `comment_by_user_id` (`comment_by_user_id`),
  KEY `comment_system_type_id` (`comment_system_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_country`
--

CREATE TABLE IF NOT EXISTS `y2m_country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_title` varchar(50) DEFAULT NULL,
  `country_code` char(8) DEFAULT NULL,
  `country_added_ip_address` int(7) unsigned DEFAULT NULL,
  `country_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `country_status` tinyint(1) NOT NULL DEFAULT '0',
  `country_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `country_modified_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`country_id`),
  UNIQUE KEY `country-seo-title` (`country_code`),
  UNIQUE KEY `country_title` (`country_title`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `y2m_country`
--

INSERT INTO `y2m_country` (`country_id`, `country_title`, `country_code`, `country_added_ip_address`, `country_added_timestamp`, `country_status`, `country_modified_timestamp`, `country_modified_ip_address`) VALUES
(1, 'India', 'In', NULL, '2013-08-07 10:11:28', 1, '2013-08-07 10:11:28', NULL),
(2, 'United Arab Emirates', 'UAE', NULL, '2013-05-27 09:19:33', 1, '2013-05-26 20:00:00', NULL),
(4, 'ddfg', 'etert', NULL, '2013-07-30 09:30:13', 1, '2013-07-30 09:30:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group`
--

CREATE TABLE IF NOT EXISTS `y2m_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_title` varchar(100) DEFAULT NULL,
  `group_seo_title` varchar(200) DEFAULT NULL,
  `group_status` tinyint(1) DEFAULT '0',
  `group_discription` text,
  `group_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_added_ip_address` int(7) unsigned DEFAULT NULL,
  `group_parent_group_id` int(11) DEFAULT '0',
  `group_location` varchar(200) DEFAULT NULL,
  `group_photo_id` int(11) DEFAULT NULL,
  `group_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `group_modified_ip_address` int(7) unsigned DEFAULT NULL,
  `group_view_counter` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_title` (`group_title`),
  UNIQUE KEY `group_seo_title` (`group_seo_title`),
  KEY `parent_group_id` (`group_parent_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `y2m_group`
--

INSERT INTO `y2m_group` (`group_id`, `group_title`, `group_seo_title`, `group_status`, `group_discription`, `group_added_timestamp`, `group_added_ip_address`, `group_parent_group_id`, `group_location`, `group_photo_id`, `group_modified_timestamp`, `group_modified_ip_address`, `group_view_counter`) VALUES
(6, 'Music', 'music', 1, 'This is abc text discription for Music.This is abc text discription for Music.This is abc text discription for Music.This is abc text discription for Music.This is abc text discription for Music.This is abc text discription for Music.This is abc text discription for Music.', '2013-05-26 08:01:49', NULL, 0, 'Dubai', 1, '2013-05-25 20:00:00', NULL, NULL),
(7, 'Movies', 'movies', 1, 'This is abc text discription for Movies.This is abc text discription for Movies.This is abc text discription for Movies.This is abc text discription for Movies.This is abc text discription for Movies.This is abc text discription for Movies.This is abc text discription for Movies.', '2013-05-26 08:01:49', NULL, 0, 'Dubai', 1, '2013-05-25 20:00:00', NULL, NULL),
(9, 'Games', 'games', 1, 'This is abc text discription for Games.This is abc text discription for Games.This is abc text discription for Games.This is abc text discription for Games.This is abc text discription for Games.This is abc text discription for Games.This is abc text discription for Games.', '2013-05-26 08:01:49', NULL, 0, 'Dubai', 1, '2013-05-25 20:00:00', NULL, NULL),
(10, 'Cooking', 'cooking', 1, 'This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.', '2013-08-12 13:41:02', 2130706433, 0, 'Dubai', 21, '2013-08-12 13:41:02', NULL, 0),
(11, 'French Musi', 'French-Musi', 1, 'this is test.this is test.this is test.this is test.this is test.this is test.this is test.', '2013-08-12 13:39:04', 2130706433, 6, 'India', 1, '2013-08-12 13:39:04', NULL, 0),
(12, 'Bollywood ', 'bollywood', 1, 'bollywood music', '2013-06-20 08:51:52', NULL, 6, 'Dubai', 1, '0000-00-00 00:00:00', NULL, 0),
(13, 'Hollywood', 'hollywood', 1, 'Hollywood Music', '2013-06-20 08:51:52', NULL, 6, 'dubai', 1, '0000-00-00 00:00:00', NULL, 0),
(14, 'Action', 'Action', 1, 'action', '2013-06-20 09:01:52', NULL, 7, 'dubai', 1, '0000-00-00 00:00:00', NULL, 0),
(15, 'Horror', 'Horror', 1, 'horror', '2013-06-20 09:01:52', NULL, 7, 'dubai', 1, '0000-00-00 00:00:00', NULL, 0),
(16, '2D', '2D', 1, '2d', '2013-06-20 09:02:18', NULL, 9, 'dubai', 1, '0000-00-00 00:00:00', NULL, 0),
(17, '3D', '3D', 1, '3d', '2013-06-20 09:02:18', NULL, 9, 'dubai', 1, '0000-00-00 00:00:00', NULL, 0),
(18, 'fgdfg', 'dfgdfg', 1, 'dfgdfg', '2013-08-07 11:39:32', 2130706433, 0, 'dgdfg', 1, '2013-08-07 11:39:32', NULL, NULL),
(19, 'dfsgdsg', 'dgsdfg', 1, 'fgsdfg', '2013-08-12 13:12:52', 2130706433, 0, 'dfgsd', 1, '2013-08-12 13:12:52', NULL, 0),
(20, 'nnnnnnnnnnnnnnnnn', 'fgdfgdfg', 1, 'fsdfsdfsdf', '2013-08-12 06:58:12', 2130706433, 0, 'sdfsd', 1, '2013-08-12 06:58:12', NULL, 0),
(21, 'Sports', 'sports', 1, 'Sport (or sports) is all forms of usually competitive physical activity which,[1] through casual or organised participation, aim to use, maintain or improve physical ability and skills while providing entertainment to participants, and in some cases, spectators.[2] Hundreds of sports exist, from those requiring only two participants, through to those with hundreds of simultaneous participants, either in teams or competing as individuals.\r\n\r\nSport is generally recognised as activities which are based in physical athleticism or physical dexterity, with the largest major competitions such as the Olympic Games admitting only sports meeting this definition,[3] and other organisations such as the Council of Europe using definitions precluding activities without a physical element from classification as sports.[2] However, a number of competitive, but non-physical, activities claim recognition as mind sports. The International Olympic Committee (through ARISF) recognises both chess and bridge as bona fide sports, and SportAccord, the international sports federation association, recognises five non-physical sports,[4][5] although limits the amount of mind games which can be admitted as sports.[1]\r\n\r\nSports are usually governed by a set of rules or customs, which serve to ensure fair competition, and allow consistent adjudication of the winner. Winning can be determined by physical events such as scoring goals or crossing a line first, or by the determination of judges who are scoring elements of the sporting performance, including objective or subjective measures such as technical performance or artistic impression.\r\n\r\nIn organised sport, records of performance are often kept, and for popular sports, this information may be widely announced or reported in sport news. In addition, sport is a major source of entertainment for non-participants, with spectator sports drawing large crowds to venues, and reaching wider audiences through sports broadcasting.', '2013-08-12 13:33:01', 2130706433, 0, 'Dubai', 19, '2013-08-12 13:33:01', NULL, 0),
(22, 'fdsdfhfghdfgjgfh', 'jfghjfghjgfhjfghj', 1, 'dfghdfgh', '2013-08-12 08:33:10', 2130706433, 0, 'ghdfgh', 9, '2013-08-12 08:33:10', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_activity`
--

CREATE TABLE IF NOT EXISTS `y2m_group_activity` (
  `group_activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_activity_content` text,
  `group_activity_owner_user_id` int(11) DEFAULT NULL,
  `group_activity_group_id` int(11) DEFAULT NULL,
  `group_activity_status` tinyint(1) DEFAULT NULL,
  `group_activity_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_activity_added_ip_address` int(7) unsigned DEFAULT NULL,
  `group_activity_start_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `group_activity_title` varchar(255) DEFAULT NULL,
  `group_activity_location` varchar(255) DEFAULT NULL,
  `group_activity_modifed_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `group_activity_modified_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`group_activity_id`),
  KEY `group_activity_owner_user_id` (`group_activity_owner_user_id`),
  KEY `group_activity_group_id` (`group_activity_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `y2m_group_activity`
--

INSERT INTO `y2m_group_activity` (`group_activity_id`, `group_activity_content`, `group_activity_owner_user_id`, `group_activity_group_id`, `group_activity_status`, `group_activity_added_timestamp`, `group_activity_added_ip_address`, `group_activity_start_timestamp`, `group_activity_title`, `group_activity_location`, `group_activity_modifed_timestamp`, `group_activity_modified_ip_address`) VALUES
(1, 'This is small content for Activity.THis is small content for Activity.THis is small content for Activity.THis is small content for Activity.THis is small content for Activity.THis is small content for Activity.', 27, 11, 1, '2013-06-09 05:52:03', NULL, '2013-06-19 23:21:09', 'Football Match', 'Sharjah', '2013-06-08 20:00:00', NULL),
(2, 'This is small content for event2.This iThis is small content for event2.This is small content for event2.This is small content for event2.This is small content for event2.s small content for event2.This is small content for event2.This is small content for event2.This is small content for event2.This is small content for event2.This is small content for event2.This is small content for event2.This is small content for event2.', 27, 14, 1, '2013-06-24 23:02:22', NULL, '2013-07-19 23:21:09', 'Cricket Match', 'Dubai', '0000-00-00 00:00:00', NULL),
(3, 'There is a party at Jumeriah Beach for the New. ', 27, 16, 1, '2013-06-09 10:14:32', NULL, '2013-06-19 23:21:09', 'Tv Match', 'Sharjah', '0000-00-00 00:00:00', NULL),
(41, ' testing Add Activity again for testing stuff', 28, 11, 1, '2013-07-21 20:00:00', 2130706433, '0000-00-00 00:00:00', NULL, 'Victoria, Australia', '2013-07-22 08:34:00', NULL),
(42, ' vxcv bcb cvbcvb', 51, 11, 1, '2013-07-31 20:00:00', 2130706433, '0000-00-00 00:00:00', NULL, 'b', '2013-08-01 07:00:10', NULL),
(43, ' coffee time', 55, 16, 1, '2013-07-31 20:00:00', 2130706433, '0000-00-00 00:00:00', NULL, 'Dubai - United Arab Emirates', '2013-08-01 11:30:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_activity_invite`
--

CREATE TABLE IF NOT EXISTS `y2m_group_activity_invite` (
  `group_activity_invite_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_activity_invite_sender_user_id` int(11) DEFAULT NULL,
  `group_activity_invite_receiver_user_id` int(11) DEFAULT NULL,
  `group_activity_invite_status` tinyint(1) DEFAULT NULL,
  `group_activity_invite_added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_activity_invite_added_ip_address` int(7) unsigned DEFAULT NULL,
  `group_activity_invite_activity_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_activity_invite_id`),
  KEY `y2m_group_activity_invite_sender_user_id` (`group_activity_invite_sender_user_id`),
  KEY `y2m_group_activity_invite_receiver_user_id` (`group_activity_invite_receiver_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_activity_rsvp`
--

CREATE TABLE IF NOT EXISTS `y2m_group_activity_rsvp` (
  `group_activity_rsvp_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_activity_rsvp_user_id` int(11) DEFAULT NULL,
  `group_activity_rsvp_activity_id` int(11) DEFAULT NULL,
  `group_activity_rsvp_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_activity_rsvp_added_ip_address` int(7) unsigned DEFAULT NULL,
  `group_activity_rsvp_group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_activity_rsvp_id`),
  KEY `group_activity_rsvp_user_id` (`group_activity_rsvp_user_id`),
  KEY `group_activity_rsvp_activity_id` (`group_activity_rsvp_activity_id`),
  KEY `group_activity_rsvp_group_id` (`group_activity_rsvp_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `y2m_group_activity_rsvp`
--

INSERT INTO `y2m_group_activity_rsvp` (`group_activity_rsvp_id`, `group_activity_rsvp_user_id`, `group_activity_rsvp_activity_id`, `group_activity_rsvp_added_timestamp`, `group_activity_rsvp_added_ip_address`, `group_activity_rsvp_group_id`) VALUES
(1, 28, 1, '2013-06-12 09:28:54', NULL, 6),
(2, 28, 3, '2013-06-12 09:28:54', NULL, 7);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_add_suggestion`
--

CREATE TABLE IF NOT EXISTS `y2m_group_add_suggestion` (
  `group_add_suggestion_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_add_suggestion_user_id` int(11) NOT NULL,
  `group_add_suggestion_name` varchar(200) NOT NULL,
  `group_add_suggestion_status` tinyint(4) NOT NULL DEFAULT '0',
  `group_add_suggestion_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_add_suggestion_added_ip_address` int(7) unsigned NOT NULL,
  PRIMARY KEY (`group_add_suggestion_id`),
  KEY `y2m_group_add_suggestion_user_id` (`group_add_suggestion_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_discussion`
--

CREATE TABLE IF NOT EXISTS `y2m_group_discussion` (
  `group_discussion_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_discussion_content` text,
  `group_discussion_owner_user_id` int(11) DEFAULT NULL,
  `group_discussion_group_id` int(11) DEFAULT NULL,
  `group_discussion_status` tinyint(1) DEFAULT NULL,
  `group_discussion_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_discussion_added_ip_address` int(7) unsigned DEFAULT NULL,
  `group_discussion_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `group_discussion_modified_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`group_discussion_id`),
  KEY `group_discussion_owner_user_id` (`group_discussion_owner_user_id`),
  KEY `group_discussion_group_id` (`group_discussion_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_discussion_tagging`
--

CREATE TABLE IF NOT EXISTS `y2m_group_discussion_tagging` (
  `group_discussion_tagging_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_discussion_tagging_sender_user_id` int(11) DEFAULT NULL,
  `group_discussion_tagging_sender_receiver_id` int(11) DEFAULT NULL,
  `group_discussion_tagging_status` tinyint(1) DEFAULT NULL,
  `group_discussion_tagging_added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_discussion_tagging_ip_address` int(7) unsigned DEFAULT NULL,
  `group_discussion_tagging_group_discussion_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_discussion_tagging_id`),
  KEY `group_discussion_tagging_sender_user_id` (`group_discussion_tagging_sender_user_id`),
  KEY `group_discussion_tagging_sender_receiver_id` (`group_discussion_tagging_sender_receiver_id`),
  KEY `group_discussion_tagging_group_id` (`group_discussion_tagging_group_discussion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_ownership`
--

CREATE TABLE IF NOT EXISTS `y2m_group_ownership` (
  `group_ownership_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_ownership_type` enum('permanent','temporary') DEFAULT NULL,
  `group_ownership_group_id` int(11) DEFAULT NULL,
  `group_ownership_user_id` int(11) DEFAULT NULL,
  `group_ownership_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_ownership_added_ip_address` int(7) unsigned DEFAULT NULL,
  `group_ownership_assign_by_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_ownership_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_permission`
--

CREATE TABLE IF NOT EXISTS `y2m_group_permission` (
  `group_permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_permission_group_id` int(11) DEFAULT NULL,
  `group_permission_discussion` tinyint(1) DEFAULT NULL,
  `group_permission_activity` tinyint(1) DEFAULT NULL,
  `group_permission_media` tinyint(1) DEFAULT NULL,
  `group_permission_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_permission_added_ip_address` int(7) unsigned DEFAULT NULL,
  `group_permission_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `group_permission_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`group_permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_photo`
--

CREATE TABLE IF NOT EXISTS `y2m_group_photo` (
  `group_photo_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_photo_photo_id` int(11) DEFAULT NULL,
  `group_photo_group_id` int(11) DEFAULT NULL,
  `group_photo_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_photo_added_ip_address` int(7) unsigned DEFAULT NULL,
  `group_photo_album_id` int(11) DEFAULT NULL,
  `group_cover_photo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_sponsor`
--

CREATE TABLE IF NOT EXISTS `y2m_group_sponsor` (
  `group_sponsor_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_sponsor_sponsor_id` int(11) DEFAULT NULL,
  `group_sponsor_group_id` int(11) DEFAULT NULL,
  `group_sponsor_start_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_sponsor_end_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `group_sponsor_status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`group_sponsor_id`),
  KEY `group_sponsor_sponsor_id` (`group_sponsor_sponsor_id`),
  KEY `group_sponsor_group_id` (`group_sponsor_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_tag`
--

CREATE TABLE IF NOT EXISTS `y2m_group_tag` (
  `group_tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_tag_group_id` int(11) DEFAULT NULL,
  `group_tag_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_tag_added_ip_address` int(7) unsigned DEFAULT NULL,
  `group_tag_tag_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_tag_id`),
  KEY `group_tag_group_id` (`group_tag_group_id`),
  KEY `group_tag_tag_id` (`group_tag_tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `y2m_group_tag`
--

INSERT INTO `y2m_group_tag` (`group_tag_id`, `group_tag_group_id`, `group_tag_added_timestamp`, `group_tag_added_ip_address`, `group_tag_tag_id`) VALUES
(3, 11, '2013-08-07 10:19:30', NULL, 1),
(4, 11, '2013-08-07 09:50:11', NULL, 4),
(5, 16, '2013-08-07 10:13:08', NULL, 10);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_group_video`
--

CREATE TABLE IF NOT EXISTS `y2m_group_video` (
  `group_video_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_video_video_id` int(11) DEFAULT NULL,
  `group_video_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_video_added_ip_adddress` int(7) unsigned DEFAULT NULL,
  `group_video_status` tinyint(1) DEFAULT NULL,
  `group_video_group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_language`
--

CREATE TABLE IF NOT EXISTS `y2m_language` (
  `language_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_title` varchar(20) DEFAULT NULL,
  `language_code` char(5) DEFAULT NULL,
  `language_status` tinyint(1) DEFAULT NULL,
  `language_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `language_added_ip_address` int(7) unsigned DEFAULT NULL,
  `language_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `language_modified_ip_address` int(7) DEFAULT NULL,
  PRIMARY KEY (`language_id`),
  UNIQUE KEY `language_title` (`language_title`),
  UNIQUE KEY `language_code` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_like`
--

CREATE TABLE IF NOT EXISTS `y2m_like` (
  `like_id` int(11) NOT NULL AUTO_INCREMENT,
  `system_type_id` int(11) DEFAULT NULL,
  `like_by_user_id` int(11) DEFAULT NULL,
  `like_status` tinyint(1) DEFAULT NULL,
  `like_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `like_refer_id` int(11) DEFAULT NULL,
  `like_added_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`like_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_log`
--

CREATE TABLE IF NOT EXISTS `y2m_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_user_id` int(11) DEFAULT NULL,
  `log_content` text,
  `log_added_ip_adress` int(7) unsigned DEFAULT NULL,
  `log_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_user_agent` varchar(255) NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_user_id` (`log_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_login_log`
--

CREATE TABLE IF NOT EXISTS `y2m_login_log` (
  `login_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `login_log_user_id` int(11) DEFAULT NULL,
  `login_log_useragent` varchar(255) DEFAULT NULL,
  `login_log_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `login_log_added_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`login_log_id`),
  KEY `login_log_user_id` (`login_log_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_method`
--

CREATE TABLE IF NOT EXISTS `y2m_method` (
  `method_id` int(11) NOT NULL AUTO_INCREMENT,
  `method_title` varchar(100) DEFAULT NULL,
  `method_seo_name` varchar(200) DEFAULT NULL,
  `method_discription` text,
  `method_author_name` varchar(255) DEFAULT NULL,
  `method_author_email` varchar(255) DEFAULT NULL,
  `method_module_id` int(11) DEFAULT NULL,
  `method_code_name` varchar(200) DEFAULT NULL,
  `method_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`method_id`),
  KEY `method_module_id` (`method_module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_module`
--

CREATE TABLE IF NOT EXISTS `y2m_module` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_title` varchar(50) DEFAULT NULL,
  `module_discription` text NOT NULL,
  `module_seo_code` varchar(200) DEFAULT NULL,
  `module_status` tinyint(1) DEFAULT NULL,
  `module_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `module_title` (`module_title`),
  UNIQUE KEY `module_seo_code` (`module_seo_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_notification_type`
--

CREATE TABLE IF NOT EXISTS `y2m_notification_type` (
  `notification_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_type_title` varchar(255) DEFAULT NULL,
  `notification_type_discription` text,
  `notification_type_added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notification_type_added_ip_address` int(7) unsigned DEFAULT NULL,
  `notification_type_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `notification_type_modified_ip_address` int(7) unsigned DEFAULT NULL,
  `notification_type_status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`notification_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `y2m_notification_type`
--

INSERT INTO `y2m_notification_type` (`notification_type_id`, `notification_type_title`, `notification_type_discription`, `notification_type_added_date`, `notification_type_added_ip_address`, `notification_type_modified_timestamp`, `notification_type_modified_ip_address`, `notification_type_status`) VALUES
(1, 'group', 'this will contains notification of all things related to Group', '2013-06-13 10:42:32', NULL, '0000-00-00 00:00:00', NULL, NULL),
(2, 'activity', 'this will contains notification of all things related to Activity', '2013-06-13 10:42:32', NULL, '0000-00-00 00:00:00', NULL, NULL),
(3, 'discussion', 'This will contain all notifications related to Discussion', '2013-06-13 10:43:57', NULL, '0000-00-00 00:00:00', NULL, NULL),
(4, 'user', 'This will contain all notifications related to User', '2013-06-13 10:43:57', NULL, '0000-00-00 00:00:00', NULL, NULL),
(5, 'Photo', NULL, '2013-06-26 11:53:43', NULL, '0000-00-00 00:00:00', NULL, 1),
(6, 'Video', NULL, '2013-06-26 11:53:43', NULL, '0000-00-00 00:00:00', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_permission`
--

CREATE TABLE IF NOT EXISTS `y2m_permission` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_user_type_id` int(11) DEFAULT NULL,
  `permission_access` tinyint(1) DEFAULT '0',
  `permission_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `permission_module_id` int(11) DEFAULT NULL,
  `permission_method_id` int(11) DEFAULT NULL,
  `permission_added_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`permission_id`),
  KEY `permission_user_type_id` (`permission_user_type_id`),
  KEY `permission_module_id` (`permission_module_id`),
  KEY `permission_method_id` (`permission_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_photo`
--

CREATE TABLE IF NOT EXISTS `y2m_photo` (
  `photo_id` int(11) NOT NULL AUTO_INCREMENT,
  `photo_name` varchar(200) DEFAULT NULL,
  `photo_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `photo_added_ip_address` int(7) unsigned DEFAULT NULL,
  `photo_status` tinyint(1) DEFAULT NULL,
  `photo_caption` varchar(200) DEFAULT NULL,
  `photo_discription` text,
  `photo_album_id` int(11) DEFAULT NULL,
  `photo_user_id` int(11) DEFAULT NULL,
  `photo_location` varchar(200) DEFAULT NULL,
  `photo_view_counter` int(11) DEFAULT NULL,
  `photo_visible` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `y2m_photo`
--

INSERT INTO `y2m_photo` (`photo_id`, `photo_name`, `photo_added_timestamp`, `photo_added_ip_address`, `photo_status`, `photo_caption`, `photo_discription`, `photo_album_id`, `photo_user_id`, `photo_location`, `photo_view_counter`, `photo_visible`) VALUES
(1, 'Planet-FrenchMusi-a6s73rni1sg.jpg', '2013-08-12 13:39:04', 2130706433, 1, 'French-Musi', 'this is test.this is test.this is test.this is test.this is test.this is test.this is test.', NULL, 1, '', 0, 1),
(2, 'Chrysanthemum.jpg', '2013-06-16 12:17:29', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(3, 'ad', '2013-08-07 11:57:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(4, 'galaxy-vxcvxzbsdf-2ca7i60fys6.jpg', '2013-08-12 06:54:09', NULL, 1, 'dfgsdfgsdfgsd', 'sdfgsdfg', NULL, NULL, NULL, 0, NULL),
(5, 'galaxy-vxcvxzbsdf-fkztk6boe4g.jpg', '2013-08-12 06:54:34', NULL, 1, 'dfgsdfgsdfgsd', 'sdfgsdfg', NULL, NULL, NULL, 0, NULL),
(6, 'galaxy-nbnvbnvbncvb-4hx2cfhktoi.jpg', '2013-08-12 06:55:02', NULL, 1, 'ncvbncvbncv', 'dfghdfgh', NULL, NULL, NULL, 0, NULL),
(7, 'galaxy-nnnnnnnnnnnnnnnnn-8qhcbsf5w5w.jpg', '2013-08-12 06:58:12', 2130706433, 1, 'fgdfgdfg', 'fsdfsdfsdf', NULL, 1, '', 0, 1),
(8, 'galaxy-bbbbbbbbbbb-dcz7y06y8mg.jpg', '2013-08-12 08:11:28', 2130706433, 1, 'bbbbbbbbbbbbbbb', 'hfghfh', NULL, 1, '', 0, 1),
(9, 'galaxy-fdsdfhfghdfgjgfh-2ldldg5l0si.jpg', '2013-08-12 08:21:42', 2130706433, 1, 'jfghjfghjgfhjfghj', 'dfghdfgh', NULL, 1, '', 0, 1),
(10, 'galaxy-bbbbbbbbbbb-5zstm9vjv28.jpg', '2013-08-12 08:33:20', 2130706433, 1, 'bbbbbbbbbbbbbbb', 'hfghfh', NULL, 1, '', 0, 1),
(11, 'galaxy-bbbbbbbbbbb-5j6kcguf17c.jpg', '2013-08-12 08:33:45', 2130706433, 1, 'bbbbbbbbbbbbbbb', 'hfghfh', NULL, 1, '', 0, 1),
(12, 'galaxy-bbbbbbbbbbb-djhggbcldu0.jpg', '2013-08-12 08:35:25', 2130706433, 1, 'bbbbbbbbbbbbbbb', 'hfghfh', NULL, 1, '', 0, 1),
(13, 'galaxy-bbbbbbbbbbb-5jbmqvpa0zc.jpg', '2013-08-12 08:37:17', 2130706433, 1, 'bbbbbbbbbbbbbbb', 'hfghfh', NULL, 1, '', 0, 1),
(14, 'galaxy-bbbbbbbbbbb-7mpt7ndnq58.jpg', '2013-08-12 08:51:03', 2130706433, 1, 'bbbbbbbbbbbbbbb', 'hfghfh', NULL, 1, '', 0, 1),
(15, 'galaxy-bbbbbbbbbbb-43knjfzrd3q.jpg', '2013-08-12 08:59:28', 2130706433, 1, 'bbbbbbbbbbbbbbb', 'hfghfh', NULL, 1, '', 0, 1),
(16, 'galaxy-bbbbbbbbbbb-8y6g81wnyy0.jpg', '2013-08-12 08:59:33', 2130706433, 1, 'bbbbbbbbbbbbbbb', 'hfghfh', NULL, 1, '', 0, 1),
(17, 'galaxy-bbbbbbbbbbb-40xy21cyr8c.jpg', '2013-08-12 09:03:04', 2130706433, 1, 'bbbbbbbbbbbbbbb', 'hfghfh', NULL, 1, '', 0, 1),
(18, 'galaxy-bbbbbbbbbbb-ac7gj7lif08.jpg', '2013-08-12 09:03:11', 2130706433, 1, 'bbbbbbbbbbbbbbb', 'hfghfh', NULL, 1, '', 0, 1),
(19, 'galaxy-bbgggggggggg-cmesdbyyepw.jpg', '2013-08-12 13:12:59', 2130706433, 1, 'cooking123', 'hfghfh', NULL, 1, '', 0, 1),
(20, 'Galaxy-Cooking-3sgj7r82ex.jpg', '2013-08-12 13:40:54', 2130706433, 1, 'cooking', 'This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.', NULL, 1, '', 0, 1),
(21, 'Galaxy-Cooking-3sgj7r8rqc.jpg', '2013-08-12 13:41:02', 2130706433, 1, 'cooking', 'This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.This is abc text discription for Cooking.', NULL, 1, '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_problem`
--

CREATE TABLE IF NOT EXISTS `y2m_problem` (
  `problem_id` int(11) NOT NULL AUTO_INCREMENT,
  `problem_reason_title` varchar(200) DEFAULT NULL,
  `problem_reason_discription` text,
  `problem_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `problem_added_ip_address` int(7) unsigned DEFAULT NULL,
  `problem_added_user_id` int(11) DEFAULT NULL,
  `problem_system_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`problem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_session`
--

CREATE TABLE IF NOT EXISTS `y2m_session` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_user_id` int(11) DEFAULT NULL,
  `session_useragent` varchar(200) DEFAULT NULL,
  `session_added_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `session_added_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_setting`
--

CREATE TABLE IF NOT EXISTS `y2m_setting` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_title` varchar(50) DEFAULT NULL,
  `setting_value` varchar(200) NOT NULL,
  `setting_discription` text,
  `setting_status` tinyint(1) DEFAULT NULL,
  `setting_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_social`
--

CREATE TABLE IF NOT EXISTS `y2m_social` (
  `social_id` int(11) NOT NULL,
  `social_title` varchar(50) DEFAULT NULL,
  `social_url` varchar(150) DEFAULT NULL,
  `social_login_username` varchar(100) DEFAULT NULL,
  `social_login_password` varchar(50) DEFAULT NULL,
  `social_access_url` varchar(255) DEFAULT NULL,
  `social_added_ip` int(7) unsigned DEFAULT NULL,
  `social_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `social_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`social_id`),
  UNIQUE KEY `social_title` (`social_title`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_social_content`
--

CREATE TABLE IF NOT EXISTS `y2m_social_content` (
  `social_content_id` int(11) NOT NULL AUTO_INCREMENT,
  `social_content_social_id` int(11) DEFAULT NULL,
  `social_content_data` blob,
  `social_content_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `social_content_added_ip_address` int(7) unsigned DEFAULT NULL,
  `social_content_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`social_content_id`),
  KEY `social_id` (`social_content_social_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_spam`
--

CREATE TABLE IF NOT EXISTS `y2m_spam` (
  `spam_id` int(11) NOT NULL AUTO_INCREMENT,
  `spam_system_id` int(11) DEFAULT NULL,
  `spam_problem_id` int(11) DEFAULT NULL,
  `spam_other_content` text,
  `spam_added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `spam_added_ip_address` int(7) unsigned DEFAULT NULL,
  `spam_report_user_id` int(11) DEFAULT NULL,
  `spam_target_user_id` int(11) NOT NULL,
  `spam_refer_id` int(11) NOT NULL,
  PRIMARY KEY (`spam_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_sponsor`
--

CREATE TABLE IF NOT EXISTS `y2m_sponsor` (
  `sponsor_id` int(11) NOT NULL AUTO_INCREMENT,
  `sponsor_title` varchar(50) DEFAULT NULL,
  `sponsor_detail` text,
  `sponsor_added_by_user_id` int(11) DEFAULT NULL,
  `sponsor_added_ip_address` int(7) unsigned NOT NULL,
  PRIMARY KEY (`sponsor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_state`
--

CREATE TABLE IF NOT EXISTS `y2m_state` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `state_title` int(50) NOT NULL,
  `state_addded_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `state_added_ip` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state_modified_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`state_id`),
  UNIQUE KEY `state_title` (`state_title`),
  KEY `state_id` (`state_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_system_type`
--

CREATE TABLE IF NOT EXISTS `y2m_system_type` (
  `system_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `system_type_title` varchar(30) DEFAULT NULL,
  `system_type_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `system_type_added_ip_address` int(7) unsigned DEFAULT NULL,
  `system_type_discription` text,
  `system_type_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `system_type_modified_ip_address` int(7) DEFAULT NULL,
  PRIMARY KEY (`system_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_tag`
--

CREATE TABLE IF NOT EXISTS `y2m_tag` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_title` varchar(200) NOT NULL,
  `tag_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tag_added_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_title` (`tag_title`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `y2m_tag`
--

INSERT INTO `y2m_tag` (`tag_id`, `tag_title`, `tag_added_timestamp`, `tag_added_ip_address`) VALUES
(1, 'Cooking', '2013-08-07 10:11:37', NULL),
(2, 'Outing', '2013-06-17 07:54:54', NULL),
(3, 'Adventure', '2013-06-17 07:55:14', NULL),
(4, 'Thrilling', '2013-06-17 07:55:14', NULL),
(5, 'Walking', '2013-07-25 10:37:02', NULL),
(6, 'Reading', '2013-07-25 10:37:02', NULL),
(7, 'Sports', '2013-07-25 10:37:31', NULL),
(8, 'Cricket', '2013-07-25 10:37:31', NULL),
(10, 'shail', '2013-07-30 11:31:16', NULL),
(11, 'test Again', '2013-08-12 10:34:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user`
--

CREATE TABLE IF NOT EXISTS `y2m_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_given_name` varchar(200) DEFAULT NULL,
  `user_first_name` varchar(200) DEFAULT NULL,
  `user_middle_name` varchar(200) DEFAULT NULL,
  `user_last_name` varchar(200) DEFAULT NULL,
  `user_status` tinyint(1) DEFAULT NULL,
  `user_added_ip_address` int(7) unsigned DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_password` varchar(200) DEFAULT NULL,
  `user_gender` enum('male','female') DEFAULT NULL,
  `user_timeline_photo_id` int(11) DEFAULT NULL,
  `user_language_id` int(11) DEFAULT NULL,
  `user_user_type_id` int(11) DEFAULT NULL,
  `user_profile_photo_id` int(11) DEFAULT NULL,
  `user_friend_request_reject_count` int(11) DEFAULT NULL,
  `user_mobile` varchar(30) DEFAULT NULL,
  `user_verification_key` varchar(200) DEFAULT NULL,
  `user_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_modified_ip_address` int(7) unsigned DEFAULT NULL,
  `user_url_identifier` varchar(255) NOT NULL,
  `user_register_type` enum('facebook','admin','site') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email` (`user_email`),
  KEY `user_language_id` (`user_language_id`),
  KEY `user_user_type_id` (`user_user_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=61 ;

--
-- Dumping data for table `y2m_user`
--

INSERT INTO `y2m_user` (`user_id`, `user_given_name`, `user_first_name`, `user_middle_name`, `user_last_name`, `user_status`, `user_added_ip_address`, `user_email`, `user_password`, `user_gender`, `user_timeline_photo_id`, `user_language_id`, `user_user_type_id`, `user_profile_photo_id`, `user_friend_request_reject_count`, `user_mobile`, `user_verification_key`, `user_added_timestamp`, `user_modified_timestamp`, `user_modified_ip_address`, `user_url_identifier`, `user_register_type`) VALUES
(7, 'kuku', NULL, NULL, NULL, 0, 2130706433, 'k.hachani@hotmail.com', '4e3f6016aab7ca29ecd8f21624f27e26', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-05-29 13:40:52', '2013-05-29 13:40:52', NULL, 'kuku', 'site'),
(27, 'shailesh solanki', NULL, NULL, NULL, 0, 2130706433, 'toshaileshsolanki@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-06-02 09:40:05', '2013-06-02 09:40:05', NULL, 'shaileshsolanki', 'site'),
(28, 'shailesh solanki', NULL, NULL, NULL, 0, 2130706433, 'shaileshsol1@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', 2, NULL, NULL, 1, NULL, NULL, NULL, '2013-06-02 12:21:45', '2013-06-02 12:21:45', NULL, 'shailsolanki', 'site'),
(29, 'jyoti', NULL, NULL, NULL, 0, 2130706433, 'toshaileshsolanki@yahoo.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'female', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-06-25 07:28:32', '2013-06-25 07:28:32', NULL, '', 'site'),
(30, 'jyoti solanki', 'jyoti', NULL, 'solanki', 1, 2130706433, 'jyoti_hoetti@rediffmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'female', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-06-26 11:16:31', '2013-06-26 11:16:31', NULL, '', 'site'),
(31, 'sathish', 'sathish', NULL, NULL, 1, 2130706433, 'safdfdfthi_b2003@yahoo.co.in', '5ae2d14e10e5b05063e77a02a7812a65', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-17 07:58:33', '2013-07-17 07:58:33', NULL, '', 'site'),
(34, 'sathish', NULL, NULL, NULL, 1, 2130706433, 'satfgdfgdfhi_b2003@yahoo.co.in', '5ae2d14e10e5b05063e77a02a7812a65', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-17 08:00:49', '2013-07-17 08:00:49', NULL, '', 'site'),
(35, 'sathish', NULL, NULL, NULL, 1, 2130706433, 'sathi_b2003@yahoo.co.in', '5ae2d14e10e5b05063e77a02a7812a65', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-17 08:02:37', '2013-07-17 08:02:37', NULL, '', 'site'),
(36, 'fdgsdfg fdgd', 'fdgsdfg', NULL, 'fdgd', 1, 2130706433, 'dfgsdg@gmail.com', 'eed8cdc400dfd4ec85dff70a170066b7', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-17 08:26:44', '2013-07-17 08:26:44', NULL, '', 'site'),
(37, 'sathish', 'sathish', NULL, NULL, 1, 2130706433, 'fsdfsdf@gmail.com', '8f60c8102d29fcd525162d02eed4566b', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-17 08:28:31', '2013-07-17 08:28:31', NULL, '', 'site'),
(38, 'sathish  ', 'sathish', NULL, ' ', 1, 2130706433, 'dfgs123dg@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-17 08:39:09', '2013-07-17 08:39:09', NULL, '', 'site'),
(39, '<script>alert(‘XSS%20attack’)</script>', '<script>alert(‘XSS%20attack’)</script>', NULL, NULL, 1, 2130706433, 'test1@test.com', '098f6bcd4621d373cade4e832627b4f6', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-23 09:07:22', '2013-07-23 09:07:22', NULL, '', 'site'),
(41, ''';alert(String.fromCharCode(88,83,83))//'';alert(String.fromCharCode(88,83,83))//"; </SCRIPT>', ''';alert(String.fromCharCode(88,83,83))//'';alert(String.fromCharCode(88,83,83))//";', NULL, '</SCRIPT>', 1, 2130706433, 'shaileshsol@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-23 10:14:55', '2013-07-23 10:14:55', NULL, '', 'site'),
(42, 'shailesh solanki', 'shailesh', NULL, 'solanki', 1, 2130706433, 'shaileshszfsdfsdfolhaha@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-28 08:51:19', '2013-07-28 08:51:19', NULL, '', 'site'),
(43, 'shailesh solanki', 'shailesh', NULL, 'solanki', 1, 2130706433, 'shaileshddgdfgdfgsolhaha@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-28 09:11:49', '2013-07-28 09:11:49', NULL, '', 'site'),
(45, 'shailesh solanki', 'shailesh', NULL, 'solanki', 1, 2130706433, 'shaileshkjhkjhksolhaha@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-28 09:24:49', '2013-07-28 09:24:49', NULL, '', 'site'),
(46, 'shailesh solanki', 'shailesh', NULL, 'solanki', 1, 2130706433, 'shaileshsolgjhghjghaha@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-28 09:27:49', '2013-07-28 09:27:49', NULL, '', 'site'),
(47, 'shailesh solanki', 'shailesh', NULL, 'solanki', 1, 2130706433, 'shaileshdfsdfsfdsolhaha@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-28 09:30:04', '2013-07-28 09:30:04', NULL, '', 'site'),
(48, 'shailesh solanki', 'shailesh', NULL, 'solanki', 1, 2130706433, 'shaileshsolhaha@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-28 09:36:57', '2013-07-28 09:36:57', NULL, '', 'site'),
(49, 'jyoti shailesh suresh solanki', 'jyoti shailesh suresh', NULL, 'solanki', 1, 2130706433, 'jyoti@fff.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-28 09:39:59', '2013-07-28 09:39:59', NULL, '', 'site'),
(50, 'test again', 'test', NULL, 'again', 1, 2130706433, 'kitrd@gmail.com', '$2y$14$KvJjRPA2u1lSSX/wbng.f.9wv36D46d/cP2vyxZ35qHeccfT7pgzS', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-29 07:44:56', '2013-07-29 07:44:56', NULL, '', 'site'),
(51, 'jyoti solanki', 'jyoti', NULL, 'solanki', 1, 2130706433, 'jyoti_@rediffmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-01 06:34:20', '2013-08-01 06:34:20', NULL, '', 'site'),
(52, 'dgdfgdfgfd', 'dgdfgdfgfd', NULL, NULL, 1, 2130706433, 'yaya@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-01 08:22:39', '2013-08-01 08:22:39', NULL, '', 'site'),
(53, 'jyoti solankisfs', 'jyoti', NULL, 'solankisfs', 1, 2130706433, 'testtest@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-01 08:58:50', '2013-08-01 08:58:50', NULL, '', 'site'),
(54, 'karim test', 'karim', NULL, 'test', 1, 2130706433, 'vooo@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-01 11:21:59', '2013-08-01 11:21:59', NULL, '', 'site'),
(55, 'Joe', 'Joe', NULL, NULL, 1, 2130706433, 'joe@hot.com', '81dc9bdb52d04dc20036dbd8313ed055', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-01 11:28:08', '2013-08-01 11:28:08', NULL, '', 'site'),
(56, 'karim hachani', 'karim', NULL, 'hachani', 1, 2130706433, 'k.hachani@pepperweb.co', '4e3f6016aab7ca29ecd8f21624f27e26', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-01 11:38:18', '2013-08-01 11:38:18', NULL, '', 'site'),
(57, 'ffedsd', 'ffedsd', NULL, NULL, 1, 2130706433, 'fdfd@hotmail.com', '76d80224611fc919a5d54f0ff9fba446', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-01 11:44:49', '2013-08-01 11:44:49', NULL, '', 'site'),
(58, 'shail test', 'shail', NULL, 'test', 1, 2130706433, 'testtesttest@gmail.com', '7f125dd8c17fc02ab20338fbcf27abfe', 'female', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-12 11:44:40', '2013-08-12 11:44:40', NULL, '', 'site'),
(59, 'ffgdfhdfhdf', 'ffgdfhdfhdf', NULL, NULL, 1, 2130706433, 'ghdfgh@ggg.com', '8f60c8102d29fcd525162d02eed4566b', 'female', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-13 08:41:31', '2013-08-13 08:41:31', NULL, '', 'site'),
(60, 'wattch now', 'wattch', NULL, 'now', 1, 2130706433, 'watch@watch.com', '098f6bcd4621d373cade4e832627b4f6', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-13 09:04:34', '2013-08-13 09:04:34', NULL, '', 'site');

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_block`
--

CREATE TABLE IF NOT EXISTS `y2m_user_block` (
  `user_block_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_block_user_id` int(11) DEFAULT NULL,
  `user_block_problem_id` int(11) DEFAULT NULL,
  `user_block_added_ip_address` int(7) unsigned DEFAULT NULL,
  `user_block_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_block_type` enum('temperary','parmenent') DEFAULT NULL,
  PRIMARY KEY (`user_block_id`),
  UNIQUE KEY `user_block_user_id_2` (`user_block_user_id`),
  KEY `user_block_user_id` (`user_block_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_friend`
--

CREATE TABLE IF NOT EXISTS `y2m_user_friend` (
  `user_friend_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_friend_sender_user_id` int(11) DEFAULT NULL,
  `user_friend_friend_user_id` int(11) DEFAULT NULL,
  `user_friend_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_friend_added_ip_address` int(7) unsigned DEFAULT NULL,
  `user_friend_status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_friend_id`),
  KEY `user_friend_sender_id` (`user_friend_sender_user_id`),
  KEY `user_friend_friend_id` (`user_friend_friend_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_friend_request`
--

CREATE TABLE IF NOT EXISTS `y2m_user_friend_request` (
  `user_friend_request_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_friend_request_sender_user_id` int(11) DEFAULT NULL,
  `user_friend_request_friend_user_id` int(11) DEFAULT NULL,
  `user_friend_request_status` tinyint(1) DEFAULT '0',
  `user_friend_request_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_friend_request_id`),
  KEY `y2m_user_friend_request_sender_id` (`user_friend_request_sender_user_id`),
  KEY `y2m_user_friend_request_friend_id` (`user_friend_request_friend_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_group`
--

CREATE TABLE IF NOT EXISTS `y2m_user_group` (
  `user_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_user_id` int(11) DEFAULT NULL,
  `user_group_group_id` int(11) DEFAULT NULL,
  `user_group_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_group_added_ip_address` int(7) unsigned DEFAULT NULL,
  `user_group_status` tinyint(1) DEFAULT NULL,
  `user_group_is_owner` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_group_id`),
  KEY `user_group_user_id` (`user_group_user_id`),
  KEY `user_group_group_id` (`user_group_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `y2m_user_group`
--

INSERT INTO `y2m_user_group` (`user_group_id`, `user_group_user_id`, `user_group_group_id`, `user_group_added_timestamp`, `user_group_added_ip_address`, `user_group_status`, `user_group_is_owner`) VALUES
(1, 60, 7, '2013-08-14 07:14:26', 2130706433, 1, 0),
(2, 60, 15, '2013-08-14 07:14:26', 2130706433, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_group_joining_invitation`
--

CREATE TABLE IF NOT EXISTS `y2m_user_group_joining_invitation` (
  `user_group_joining_invitation_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_joining_invitation_sender_user_id` int(11) DEFAULT NULL,
  `user_group_joining_invitation_receiver_id` int(11) DEFAULT NULL,
  `user_group_joining_invitation_status` tinyint(1) DEFAULT '0',
  `user_group_joining_invitation_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_group_joining_invitation_ip_address` int(7) unsigned DEFAULT NULL,
  `user_group_joining_invitation_group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_group_joining_invitation_id`),
  KEY `user_group_joining_invitation_sender_user_id` (`user_group_joining_invitation_sender_user_id`),
  KEY `user_group_joining_invitation_receiver_id` (`user_group_joining_invitation_receiver_id`),
  KEY `user_group_joining_invitation_group_id` (`user_group_joining_invitation_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `y2m_user_group_joining_invitation`
--

INSERT INTO `y2m_user_group_joining_invitation` (`user_group_joining_invitation_id`, `user_group_joining_invitation_sender_user_id`, `user_group_joining_invitation_receiver_id`, `user_group_joining_invitation_status`, `user_group_joining_invitation_added_timestamp`, `user_group_joining_invitation_ip_address`, `user_group_joining_invitation_group_id`) VALUES
(1, 27, 28, 1, '2013-06-23 06:41:13', NULL, 11),
(2, 27, 28, 1, '2013-06-23 06:41:13', NULL, 12),
(3, 27, 28, 0, '2013-06-23 06:41:46', NULL, 13),
(4, 27, 28, 1, '2013-06-23 06:41:46', NULL, 14),
(5, 27, 28, 1, '2013-06-23 06:41:59', NULL, 15),
(6, 27, 28, 1, '2013-06-23 06:41:59', NULL, 16),
(7, 27, 28, 1, '2013-06-23 06:42:05', NULL, 17);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_group_joining_request`
--

CREATE TABLE IF NOT EXISTS `y2m_user_group_joining_request` (
  `user_group_joining_request_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_joining_request_user_id` int(11) DEFAULT NULL,
  `user_group_joining_request_group_id` int(11) DEFAULT NULL,
  `user_group_joining_request_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_group_joining_request_added_ip_address` int(11) DEFAULT NULL,
  `user_group_joining_request_status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`user_group_joining_request_id`),
  KEY `user_group_joining_request_user_id` (`user_group_joining_request_user_id`),
  KEY `user_group_joining_request_group_id` (`user_group_joining_request_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `y2m_user_group_joining_request`
--

INSERT INTO `y2m_user_group_joining_request` (`user_group_joining_request_id`, `user_group_joining_request_user_id`, `user_group_joining_request_group_id`, `user_group_joining_request_added_timestamp`, `user_group_joining_request_added_ip_address`, `user_group_joining_request_status`) VALUES
(14, 28, 16, '2013-07-09 09:32:11', 2130706433, 0),
(15, 28, 16, '2013-07-10 06:33:39', 2130706433, 0),
(16, 28, 16, '2013-07-10 06:33:59', 2130706433, 0),
(17, 28, 16, '2013-07-10 06:34:33', 2130706433, 0),
(18, 28, 16, '2013-07-10 06:35:07', 2130706433, 0),
(19, 28, 16, '2013-07-10 06:56:42', 2130706433, 0),
(20, 28, 16, '2013-07-10 06:57:28', 2130706433, 0),
(21, 28, 16, '2013-07-10 07:32:06', 2130706433, 0),
(22, 28, 16, '2013-07-10 07:33:21', 2130706433, 0),
(23, 28, 16, '2013-07-10 07:33:24', 2130706433, 0),
(24, 28, 16, '2013-07-10 07:33:25', 2130706433, 0),
(25, 28, 16, '2013-07-10 07:33:45', 2130706433, 0),
(26, 28, 16, '2013-07-10 07:38:48', 2130706433, 0),
(27, 28, 16, '2013-07-10 08:04:51', 2130706433, 0),
(28, 28, 16, '2013-07-10 08:05:10', 2130706433, 0),
(29, 28, 16, '2013-07-10 08:05:38', 2130706433, 0),
(30, 28, 16, '2013-07-10 08:06:22', 2130706433, 0),
(31, 28, 16, '2013-07-10 08:06:30', 2130706433, 0),
(32, 28, 16, '2013-07-10 08:07:07', 2130706433, 0),
(33, 28, 16, '2013-07-11 03:43:36', 2130706433, 0),
(34, 28, 16, '2013-07-11 03:44:01', 2130706433, 0),
(35, 41, 16, '2013-08-01 05:33:59', 2130706433, 0),
(36, 41, 16, '2013-08-01 05:36:03', 2130706433, 0),
(37, 53, 17, '2013-08-01 05:37:16', 2130706433, 0),
(38, 53, 16, '2013-08-01 05:37:46', 2130706433, 0),
(39, 53, 16, '2013-08-01 05:45:03', 2130706433, 0),
(40, 53, 16, '2013-08-01 05:45:55', 2130706433, 0),
(41, 53, 15, '2013-08-01 06:07:34', 2130706433, 0),
(42, 53, 16, '2013-08-01 06:51:31', 2130706433, 0),
(43, 53, 16, '2013-08-01 06:57:24', 2130706433, 0);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_group_setting`
--

CREATE TABLE IF NOT EXISTS `y2m_user_group_setting` (
  `user_group_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_setting_user_id` int(11) DEFAULT NULL,
  `user_group_setting_group_id` int(11) DEFAULT NULL,
  `user_group_setting_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_group_setting_added_ip_address` int(7) unsigned DEFAULT NULL,
  `user_group_setting_variable` varchar(255) DEFAULT NULL,
  `user_group_setting_discription` text,
  `user_group_setting_value` varchar(200) DEFAULT NULL,
  `user_group_setting_status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`user_group_setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_message`
--

CREATE TABLE IF NOT EXISTS `y2m_user_message` (
  `user_message_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_message_receiver_id` int(11) DEFAULT NULL,
  `user_message_sender_id` int(11) DEFAULT NULL,
  `user_message_content` text,
  `user_message_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_message_added_ip_address` int(7) unsigned DEFAULT NULL,
  `user_message_status` tinyint(1) DEFAULT NULL,
  `user_message_type` enum('normal','warning') DEFAULT NULL,
  PRIMARY KEY (`user_message_id`),
  KEY `user_message_receiver_id` (`user_message_receiver_id`),
  KEY `user_message_sender_id` (`user_message_sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_notification`
--

CREATE TABLE IF NOT EXISTS `y2m_user_notification` (
  `user_notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_notification_user_id` int(11) DEFAULT NULL,
  `user_notification_content` text,
  `user_notification_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_notification_status` tinyint(1) DEFAULT NULL,
  `user_notification_notification_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_notification_id`),
  KEY `user_notification_user_id` (`user_notification_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `y2m_user_notification`
--

INSERT INTO `y2m_user_notification` (`user_notification_id`, `user_notification_user_id`, `user_notification_content`, `user_notification_added_timestamp`, `user_notification_status`, `user_notification_notification_type_id`) VALUES
(3, 27, 'You send add requeest for Group', '2013-07-10 08:07:07', 0, 1),
(4, 27, 'You send add requeest for Group <a href=''#''>2D</a>', '2013-07-11 03:43:36', 0, 1),
(5, 27, 'You send add requeest for Group <a href=''#''>2D</a>', '2013-07-11 03:44:01', 0, 1),
(6, 27, 'You send add requeest for Planet <a href=''#''><b>2D</b></a>', '2013-08-01 05:33:59', 0, 1),
(7, 27, 'You send add requeest for Planet <a href=''#''><b>2D</b></a>', '2013-08-01 05:36:03', 0, 1),
(8, 27, 'You send add requeest for Planet <a href=''#''><b>2D</b></a>', '2013-08-01 05:37:46', 0, 1),
(9, 27, 'You send add requeest for Planet <a href=''#''><b>2D</b></a>', '2013-08-01 05:45:03', 0, 1),
(10, 27, 'You send add requeest for Planet <a href=''#''><b>2D</b></a>', '2013-08-01 05:45:55', 0, 1),
(11, 27, 'You send add requeest for Planet <a href=''#''><b>2D</b></a>', '2013-08-01 06:51:31', 0, 1),
(12, 27, 'You send add requeest for Planet <a href=''#''><b>2D</b></a>', '2013-08-01 06:57:25', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_photo_tagging`
--

CREATE TABLE IF NOT EXISTS `y2m_user_photo_tagging` (
  `user_photo_taging_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_photo_taging_tagged_user_id` int(11) DEFAULT NULL,
  `user_photo_taging_photo_id` int(11) DEFAULT NULL,
  `user_photo_taging_sender_user_id` int(11) DEFAULT NULL,
  `user_photo_taging_status` tinyint(1) DEFAULT NULL,
  `user_photo_taging_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_photo_taging_added_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_photo_taging_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_profile`
--

CREATE TABLE IF NOT EXISTS `y2m_user_profile` (
  `user_profile_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_profile_dob` date DEFAULT NULL,
  `user_profile_about_me` text,
  `user_profile_profession` varchar(200) DEFAULT NULL,
  `user_profile_profession_at` varchar(200) DEFAULT NULL,
  `user_profile_user_id` int(11) DEFAULT NULL,
  `user_profile_city` varchar(50) DEFAULT NULL,
  `user_profile_state_id` int(11) DEFAULT NULL,
  `user_profile_country_id` int(11) DEFAULT NULL,
  `user_address` text,
  `user_profile_current_location` varchar(80) DEFAULT NULL,
  `user_profile_phone` varchar(20) DEFAULT NULL,
  `user_profile_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_profile_added_ip_address` int(7) unsigned DEFAULT NULL,
  `user_profile_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_profile_modified_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_profile_id`),
  KEY `user_id` (`user_profile_user_id`),
  KEY `user_profile_state_id` (`user_profile_state_id`),
  KEY `user_profile_country_id` (`user_profile_country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `y2m_user_profile`
--

INSERT INTO `y2m_user_profile` (`user_profile_id`, `user_profile_dob`, `user_profile_about_me`, `user_profile_profession`, `user_profile_profession_at`, `user_profile_user_id`, `user_profile_city`, `user_profile_state_id`, `user_profile_country_id`, `user_address`, `user_profile_current_location`, `user_profile_phone`, `user_profile_added_timestamp`, `user_profile_added_ip_address`, `user_profile_modified_timestamp`, `user_profile_modified_ip_address`) VALUES
(2, '1984-12-12', NULL, 'Student', 'DIC', 27, 'Dubai', NULL, 2, NULL, NULL, NULL, '2013-06-02 09:40:05', 2130706433, '2013-06-02 09:40:05', NULL),
(3, '1984-12-12', 'I ma a Honest Guy with New here in Town. I am friendly in Nature and loves Fun. I like outing and making friends. I loves outing specially in Mountains, Sea or some beautiful Paces\r\n\r\nI loves Party and i am ready to be your friend if we think the same', 'Student', 'DIC', 28, 'Dubai', NULL, 2, NULL, NULL, NULL, '2013-06-02 12:21:45', 2130706433, '2013-06-02 12:21:45', NULL),
(4, '1984-07-27', NULL, 'Student', 'Punjab Technical University', 29, 'sharaj', NULL, 2, NULL, NULL, NULL, '2013-06-25 07:28:32', 2130706433, '2013-06-25 07:28:32', NULL),
(5, '1984-07-27', NULL, 'Student', 'Punjab Technical University', 30, 'Dubai', NULL, 2, NULL, NULL, NULL, '2013-06-26 11:16:31', 2130706433, '2013-06-26 11:16:31', NULL),
(6, '1981-12-12', NULL, 'software engineer', 'madras university', 31, 'chennai', NULL, 1, NULL, NULL, NULL, '2013-07-17 07:58:33', 2130706433, '2013-07-17 07:58:33', NULL),
(7, '1981-12-12', NULL, 'software engineer', 'madras university', 34, 'chennai', NULL, 1, NULL, NULL, NULL, '2013-07-17 08:00:49', 2130706433, '2013-07-17 08:00:49', NULL),
(8, '1981-12-12', NULL, 'software engineer', 'madras university', 35, 'chennai', NULL, 1, NULL, NULL, NULL, '2013-07-17 08:02:37', 2130706433, '2013-07-17 08:02:37', NULL),
(9, '1981-12-12', NULL, 'sdfsd', 'sdfsdf', 36, 'sdfs', NULL, 2, NULL, NULL, NULL, '2013-07-17 08:26:44', 2130706433, '2013-07-17 08:26:44', NULL),
(10, '1981-12-12', NULL, 'sdfsd', 'madras university', 37, 'dfsd', NULL, 2, NULL, NULL, NULL, '2013-07-17 08:28:31', 2130706433, '2013-07-17 08:28:31', NULL),
(11, '1981-12-12', NULL, 'software engineer', 'madras university', 38, 'chennai', NULL, 2, NULL, NULL, NULL, '2013-07-17 08:39:09', 2130706433, '2013-07-17 08:39:09', NULL),
(12, '0000-00-00', NULL, '<script>alert(‘XSS%20attack’)</script>', '<script>alert(‘XSS%20attack’)</script>', 39, '<script>alert(‘XSS%20attack’)</script>', NULL, 2, NULL, NULL, NULL, '2013-07-23 09:07:22', 2130706433, '2013-07-23 09:07:22', NULL),
(13, '0000-00-00', NULL, ''';alert(String.fromCharCode(88,83,83))//'';alert(String.fromCharCode(88,83,83))//"; </SCRIPT>', ''';alert(String.fromCharCode(88,83,83))//'';alert(String.fromCharCode(88,83,83))//"; </SCRIPT>', 41, ''';alert(String.fromCharCode(88,83,83))//'';alert(St', NULL, 2, NULL, NULL, NULL, '2013-07-23 10:14:55', 2130706433, '2013-07-23 10:14:55', NULL),
(14, '0000-00-00', NULL, 'software engineer', 'madras university', 42, 'Ujjain', NULL, 1, NULL, NULL, NULL, '2013-07-28 08:51:19', 2130706433, '2013-07-28 08:51:19', NULL),
(15, '0000-00-00', NULL, 'software engineer', 'madras university', 43, 'Ujjain', NULL, 1, NULL, NULL, NULL, '2013-07-28 09:11:49', 2130706433, '2013-07-28 09:11:49', NULL),
(16, '0000-00-00', NULL, 'software engineer', 'madras university', 45, 'Ujjain', NULL, 1, NULL, NULL, NULL, '2013-07-28 09:24:49', 2130706433, '2013-07-28 09:24:49', NULL),
(17, '0000-00-00', NULL, 'software engineer', 'madras university', 46, 'Ujjain', NULL, 1, NULL, NULL, NULL, '2013-07-28 09:27:49', 2130706433, '2013-07-28 09:27:49', NULL),
(18, '0000-00-00', NULL, 'software engineer', 'madras university', 47, 'Ujjain', NULL, 1, NULL, NULL, NULL, '2013-07-28 09:30:04', 2130706433, '2013-07-28 09:30:04', NULL),
(19, '0000-00-00', NULL, 'software engineer', 'madras university', 48, 'Ujjain', NULL, 1, NULL, NULL, NULL, '2013-07-28 09:36:57', 2130706433, '2013-07-28 09:36:57', NULL),
(20, '1981-12-12', NULL, 'software engineer', 'madras university', 49, 'belgaum', NULL, 1, NULL, NULL, NULL, '2013-07-28 09:39:59', 2130706433, '2013-07-28 09:39:59', NULL),
(21, '0000-00-00', NULL, 'software engineer', 'madras university', 50, 'belgaum', NULL, 1, NULL, NULL, NULL, '2013-07-29 07:44:56', 2130706433, '2013-07-29 07:44:56', NULL),
(22, '0000-00-00', NULL, 'student', 'shail', 51, 'Ujjain', NULL, 1, NULL, NULL, NULL, '2013-08-01 06:34:20', 2130706433, '2013-08-01 06:34:20', NULL),
(23, '0000-00-00', NULL, 'student', 'shail', 52, 'Ujjain', NULL, 1, NULL, NULL, NULL, '2013-08-01 08:22:39', 2130706433, '2013-08-01 08:22:39', NULL),
(24, '0000-00-00', NULL, 'student', 'shail', 53, 'dubai', NULL, 1, NULL, NULL, NULL, '2013-08-01 08:58:50', 2130706433, '2013-08-01 08:58:50', NULL),
(25, '0000-00-00', NULL, 'student', 'shail', 54, 'Ujjain', NULL, 1, NULL, NULL, NULL, '2013-08-01 11:21:59', 2130706433, '2013-08-01 11:21:59', NULL),
(26, '0000-00-00', NULL, 'IT', 'y2m', 55, 'dubai', NULL, 2, NULL, NULL, NULL, '2013-08-01 11:28:08', 2130706433, '2013-08-01 11:28:08', NULL),
(27, '0000-00-00', NULL, 'teacher', 'aud', 56, 'dubai', NULL, 1, NULL, NULL, NULL, '2013-08-01 11:38:18', 2130706433, '2013-08-01 11:38:18', NULL),
(28, '0000-00-00', NULL, 'seo', 'aud', 57, 'kochin', NULL, 1, NULL, NULL, NULL, '2013-08-01 11:44:50', 2130706433, '2013-08-01 11:44:50', NULL),
(29, '0000-00-00', NULL, 'student', 'fsdfsdf', 58, 'dubai', NULL, 1, NULL, NULL, NULL, '2013-08-12 11:44:40', 2130706433, '2013-08-12 11:44:40', NULL),
(30, '2013-08-08', NULL, 'student', 'shail', 59, 'dubai', NULL, 2, NULL, NULL, NULL, '2013-08-13 08:41:31', 2130706433, '2013-08-13 08:41:31', NULL),
(31, '2013-08-15', NULL, 'student', 'shail', 60, 'dubai', NULL, 1, NULL, NULL, NULL, '2013-08-13 09:04:34', 2130706433, '2013-08-13 09:04:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_setting`
--

CREATE TABLE IF NOT EXISTS `y2m_user_setting` (
  `user_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_setting_user_id` int(11) DEFAULT NULL,
  `user_setting_setting_id` int(11) DEFAULT NULL,
  `user_setting_value` varchar(200) DEFAULT NULL,
  `user_setting_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_setting_added_ip_address` int(7) unsigned DEFAULT NULL,
  `user_setting_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_setting_modified_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_setting_id`),
  KEY `user_setting_user_id` (`user_setting_user_id`),
  KEY `user_setting_parent_setting_id` (`user_setting_setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_tag`
--

CREATE TABLE IF NOT EXISTS `y2m_user_tag` (
  `user_tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_tag_user_id` int(11) DEFAULT NULL,
  `user_tag_tag_id` int(11) DEFAULT NULL,
  `user_tag_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_tag_added_ip_address` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_tag_id`),
  KEY `user_tag_user_id` (`user_tag_user_id`),
  KEY `user_tag_tag_id` (`user_tag_tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

--
-- Dumping data for table `y2m_user_tag`
--

INSERT INTO `y2m_user_tag` (`user_tag_id`, `user_tag_user_id`, `user_tag_tag_id`, `user_tag_added_timestamp`, `user_tag_added_ip_address`) VALUES
(7, 47, 3, '2013-08-07 10:09:55', NULL),
(8, 48, 1, '2013-07-28 09:36:57', 2130706433),
(9, 48, 2, '2013-07-28 09:36:57', 2130706433),
(10, 49, 1, '2013-07-28 09:39:59', 2130706433),
(11, 49, 2, '2013-07-28 09:39:59', 2130706433),
(12, 49, 3, '2013-08-05 10:23:23', NULL),
(13, 49, 4, '2013-07-28 09:39:59', 2130706433),
(14, 49, 5, '2013-07-28 09:39:59', 2130706433),
(15, 49, 6, '2013-07-28 09:39:59', 2130706433),
(16, 49, 7, '2013-07-28 09:39:59', 2130706433),
(17, 49, 8, '2013-07-28 09:39:59', 2130706433),
(18, 50, 1, '2013-07-29 07:44:56', 2130706433),
(19, 50, 2, '2013-07-29 07:44:56', 2130706433),
(20, 50, 3, '2013-07-29 07:44:56', 2130706433),
(21, 50, 4, '2013-07-29 07:44:56', 2130706433),
(22, 51, 1, '2013-08-01 06:34:20', 2130706433),
(23, 51, 2, '2013-08-01 06:34:20', 2130706433),
(24, 51, 3, '2013-08-01 06:34:20', 2130706433),
(25, 51, 4, '2013-08-01 06:34:20', 2130706433),
(26, 52, 1, '2013-08-01 08:22:39', 2130706433),
(27, 52, 2, '2013-08-01 08:22:39', 2130706433),
(28, 52, 3, '2013-08-01 08:22:39', 2130706433),
(29, 52, 4, '2013-08-01 08:22:39', 2130706433),
(30, 52, 5, '2013-08-01 08:22:39', 2130706433),
(31, 53, 1, '2013-08-01 08:58:50', 2130706433),
(32, 53, 2, '2013-08-01 08:58:50', 2130706433),
(33, 53, 3, '2013-08-01 08:58:50', 2130706433),
(34, 53, 4, '2013-08-01 08:58:50', 2130706433),
(35, 53, 5, '2013-08-01 08:58:50', 2130706433),
(36, 54, 1, '2013-08-01 11:21:59', 2130706433),
(37, 54, 2, '2013-08-01 11:21:59', 2130706433),
(38, 54, 3, '2013-08-01 11:21:59', 2130706433),
(39, 54, 4, '2013-08-01 11:21:59', 2130706433),
(40, 55, 1, '2013-08-01 11:28:08', 2130706433),
(41, 55, 2, '2013-08-01 11:28:08', 2130706433),
(42, 55, 3, '2013-08-01 11:28:08', 2130706433),
(43, 55, 4, '2013-08-01 11:28:08', 2130706433),
(44, 55, 6, '2013-08-01 11:28:08', 2130706433),
(45, 56, 1, '2013-08-01 11:38:18', 2130706433),
(46, 56, 8, '2013-08-01 11:38:18', 2130706433),
(47, 56, 10, '2013-08-01 11:38:18', 2130706433),
(48, 57, 1, '2013-08-01 11:44:50', 2130706433),
(49, 57, 7, '2013-08-01 11:44:50', 2130706433),
(53, 42, 3, '2013-08-07 09:13:34', NULL),
(54, 58, 1, '2013-08-12 11:44:40', 2130706433),
(55, 58, 2, '2013-08-12 11:44:40', 2130706433),
(56, 58, 3, '2013-08-12 11:44:40', 2130706433),
(57, 58, 4, '2013-08-12 11:44:40', 2130706433),
(58, 58, 5, '2013-08-12 11:44:40', 2130706433),
(59, 59, 1, '2013-08-13 08:41:31', 2130706433),
(60, 59, 2, '2013-08-13 08:41:31', 2130706433),
(61, 59, 3, '2013-08-13 08:41:31', 2130706433),
(62, 59, 4, '2013-08-13 08:41:31', 2130706433),
(63, 60, 1, '2013-08-13 09:04:34', 2130706433),
(64, 60, 2, '2013-08-13 09:04:34', 2130706433),
(65, 60, 6, '2013-08-13 09:04:34', 2130706433),
(66, 60, 7, '2013-08-13 09:04:34', 2130706433),
(67, 60, 8, '2013-08-13 09:04:34', 2130706433),
(68, 60, 11, '2013-08-13 09:04:34', 2130706433);

-- --------------------------------------------------------

--
-- Table structure for table `y2m_user_type`
--

CREATE TABLE IF NOT EXISTS `y2m_user_type` (
  `user_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type_title` varchar(50) DEFAULT NULL,
  `user_type_added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_type_status` tinyint(1) DEFAULT '0',
  `user_type_modified_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_type_modified_ip_address` int(7) unsigned DEFAULT NULL,
  `user_type_discription` text,
  PRIMARY KEY (`user_type_id`),
  UNIQUE KEY `user_type_title` (`user_type_title`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `y2m_video`
--

CREATE TABLE IF NOT EXISTS `y2m_video` (
  `video_id` int(11) NOT NULL AUTO_INCREMENT,
  `video_type` enum('upload','youtube') DEFAULT NULL,
  `video_title` varchar(200) DEFAULT NULL,
  `video_discription` text,
  `video_taken_place` varchar(255) DEFAULT NULL,
  `video_added_time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `video_added_ip_address` int(7) unsigned DEFAULT NULL,
  `video_status` tinyint(1) DEFAULT '0',
  `video_user_id` int(11) DEFAULT NULL,
  `video_location` varchar(255) DEFAULT NULL,
  `video_view_counter` int(11) DEFAULT '0',
  `video_visible` tinyint(1) DEFAULT '0',
  `video_url` varchar(255) DEFAULT NULL,
  `youtube_embedded_code` text,
  PRIMARY KEY (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `y2m_album`
--
ALTER TABLE `y2m_album`
  ADD CONSTRAINT `y2m_album_ibfk_1` FOREIGN KEY (`album_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_album_ibfk_2` FOREIGN KEY (`album_cover_photo_id`) REFERENCES `y2m_photo` (`photo_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_comment`
--
ALTER TABLE `y2m_comment`
  ADD CONSTRAINT `y2m_comment_ibfk_1` FOREIGN KEY (`comment_system_type_id`) REFERENCES `y2m_comment` (`comment_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_comment_ibfk_2` FOREIGN KEY (`comment_by_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_group_activity`
--
ALTER TABLE `y2m_group_activity`
  ADD CONSTRAINT `y2m_group_activity_ibfk_1` FOREIGN KEY (`group_activity_owner_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_group_activity_ibfk_2` FOREIGN KEY (`group_activity_group_id`) REFERENCES `y2m_group` (`group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_group_activity_rsvp`
--
ALTER TABLE `y2m_group_activity_rsvp`
  ADD CONSTRAINT `y2m_group_activity_rsvp_ibfk_1` FOREIGN KEY (`group_activity_rsvp_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_group_activity_rsvp_ibfk_2` FOREIGN KEY (`group_activity_rsvp_activity_id`) REFERENCES `y2m_group_activity` (`group_activity_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_group_activity_rsvp_ibfk_3` FOREIGN KEY (`group_activity_rsvp_group_id`) REFERENCES `y2m_group` (`group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_group_discussion`
--
ALTER TABLE `y2m_group_discussion`
  ADD CONSTRAINT `y2m_group_discussion_ibfk_1` FOREIGN KEY (`group_discussion_owner_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_group_discussion_ibfk_2` FOREIGN KEY (`group_discussion_group_id`) REFERENCES `y2m_group` (`group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_group_discussion_tagging`
--
ALTER TABLE `y2m_group_discussion_tagging`
  ADD CONSTRAINT `y2m_group_discussion_tagging_ibfk_1` FOREIGN KEY (`group_discussion_tagging_sender_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_group_discussion_tagging_ibfk_2` FOREIGN KEY (`group_discussion_tagging_sender_receiver_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_group_tag`
--
ALTER TABLE `y2m_group_tag`
  ADD CONSTRAINT `y2m_group_tag_ibfk_2` FOREIGN KEY (`group_tag_group_id`) REFERENCES `y2m_group` (`group_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_group_tag_ibfk_3` FOREIGN KEY (`group_tag_tag_id`) REFERENCES `y2m_tag` (`tag_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_log`
--
ALTER TABLE `y2m_log`
  ADD CONSTRAINT `y2m_log_ibfk_1` FOREIGN KEY (`log_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_login_log`
--
ALTER TABLE `y2m_login_log`
  ADD CONSTRAINT `y2m_login_log_ibfk_1` FOREIGN KEY (`login_log_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_method`
--
ALTER TABLE `y2m_method`
  ADD CONSTRAINT `y2m_method_ibfk_1` FOREIGN KEY (`method_module_id`) REFERENCES `y2m_module` (`module_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_permission`
--
ALTER TABLE `y2m_permission`
  ADD CONSTRAINT `y2m_permission_ibfk_1` FOREIGN KEY (`permission_user_type_id`) REFERENCES `y2m_user_type` (`user_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_permission_ibfk_2` FOREIGN KEY (`permission_module_id`) REFERENCES `y2m_module` (`module_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_permission_ibfk_3` FOREIGN KEY (`permission_method_id`) REFERENCES `y2m_method` (`method_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_social_content`
--
ALTER TABLE `y2m_social_content`
  ADD CONSTRAINT `y2m_social_content_ibfk_1` FOREIGN KEY (`social_content_social_id`) REFERENCES `y2m_social` (`social_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_state`
--
ALTER TABLE `y2m_state`
  ADD CONSTRAINT `y2m_state_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `y2m_country` (`country_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_user`
--
ALTER TABLE `y2m_user`
  ADD CONSTRAINT `y2m_user_ibfk_3` FOREIGN KEY (`user_language_id`) REFERENCES `y2m_language` (`language_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_user_ibfk_4` FOREIGN KEY (`user_user_type_id`) REFERENCES `y2m_user_type` (`user_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_user_block`
--
ALTER TABLE `y2m_user_block`
  ADD CONSTRAINT `y2m_user_block_ibfk_1` FOREIGN KEY (`user_block_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_user_group`
--
ALTER TABLE `y2m_user_group`
  ADD CONSTRAINT `y2m_user_group_ibfk_1` FOREIGN KEY (`user_group_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_user_group_ibfk_3` FOREIGN KEY (`user_group_group_id`) REFERENCES `y2m_group` (`group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_user_group_joining_invitation`
--
ALTER TABLE `y2m_user_group_joining_invitation`
  ADD CONSTRAINT `y2m_user_group_joining_invitation_ibfk_1` FOREIGN KEY (`user_group_joining_invitation_sender_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_user_group_joining_invitation_ibfk_2` FOREIGN KEY (`user_group_joining_invitation_receiver_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_user_group_joining_invitation_ibfk_3` FOREIGN KEY (`user_group_joining_invitation_group_id`) REFERENCES `y2m_group` (`group_id`);

--
-- Constraints for table `y2m_user_group_joining_request`
--
ALTER TABLE `y2m_user_group_joining_request`
  ADD CONSTRAINT `y2m_user_group_joining_request_ibfk_1` FOREIGN KEY (`user_group_joining_request_user_id`) REFERENCES `y2m_user` (`user_id`),
  ADD CONSTRAINT `y2m_user_group_joining_request_ibfk_2` FOREIGN KEY (`user_group_joining_request_group_id`) REFERENCES `y2m_group` (`group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_user_message`
--
ALTER TABLE `y2m_user_message`
  ADD CONSTRAINT `y2m_user_message_ibfk_1` FOREIGN KEY (`user_message_receiver_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_user_message_ibfk_2` FOREIGN KEY (`user_message_sender_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_user_notification`
--
ALTER TABLE `y2m_user_notification`
  ADD CONSTRAINT `y2m_user_notification_ibfk_1` FOREIGN KEY (`user_notification_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_user_profile`
--
ALTER TABLE `y2m_user_profile`
  ADD CONSTRAINT `y2m_user_profile_ibfk_1` FOREIGN KEY (`user_profile_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_user_profile_ibfk_2` FOREIGN KEY (`user_profile_state_id`) REFERENCES `y2m_state` (`state_id`),
  ADD CONSTRAINT `y2m_user_profile_ibfk_3` FOREIGN KEY (`user_profile_country_id`) REFERENCES `y2m_country` (`country_id`);

--
-- Constraints for table `y2m_user_setting`
--
ALTER TABLE `y2m_user_setting`
  ADD CONSTRAINT `y2m_user_setting_ibfk_1` FOREIGN KEY (`user_setting_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_user_setting_ibfk_2` FOREIGN KEY (`user_setting_setting_id`) REFERENCES `y2m_setting` (`setting_id`) ON UPDATE CASCADE;

--
-- Constraints for table `y2m_user_tag`
--
ALTER TABLE `y2m_user_tag`
  ADD CONSTRAINT `y2m_user_tag_ibfk_1` FOREIGN KEY (`user_tag_user_id`) REFERENCES `y2m_user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `y2m_user_tag_ibfk_2` FOREIGN KEY (`user_tag_tag_id`) REFERENCES `y2m_tag` (`tag_id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
