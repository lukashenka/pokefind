-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 22, 2016 at 01:33 AM
-- Server version: 5.6.30-0ubuntu0.14.04.1-log
-- PHP Version: 5.5.9-1ubuntu4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `pgl`
--

-- --------------------------------------------------------

--
-- Table structure for table `generation_log`
--

CREATE TABLE IF NOT EXISTS `generation_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `update_location_id` int(11) DEFAULT '0',
    `steps` int(11) NOT NULL DEFAULT '0',
    `current_step` int(11) NOT NULL DEFAULT '0',
    `done` int(11) NOT NULL DEFAULT '0',
    `fail` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `generation_log_update_location_id_done_index` (`update_location_id`,`done`),
    KEY `done` (`done`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `location_for_update`
--

CREATE TABLE IF NOT EXISTS `location_for_update` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `lat` float(10,6) DEFAULT NULL,
    `lng` float(10,6) DEFAULT NULL,
    `blocked` tinyint(1) DEFAULT '0',
    `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `user_session_id` int(11) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `created` (`created`,`blocked`),
    KEY `location_for_update_user_session_id_index` (`user_session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `pokemon`
--

CREATE TABLE IF NOT EXISTS `pokemon` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `pokeuid` varchar(255) DEFAULT NULL,
    `new_column` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `pokemon_name_uindex` (`name`),
    KEY `pokemon_pokeuid_index` (`pokeuid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=161 ;

-- --------------------------------------------------------

--
-- Table structure for table `pokemon_location`
--

CREATE TABLE IF NOT EXISTS `pokemon_location` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `pokemon_id` int(11) NOT NULL,
    `lat` float(10,6) DEFAULT NULL,
    `lng` float(10,6) DEFAULT NULL,
    `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `expired` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `pokemon_location_lat_lng_index` (`lat`,`lng`),
    KEY `pokemon_id` (`pokemon_id`),
    KEY `pokemon_created` (`created`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=85 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `guid` varchar(255) NOT NULL,
    `ip` int(10) NOT NULL DEFAULT '0',
    `ip_string` varchar(20) NOT NULL,
    `created` datetime DEFAULT '0000-00-00 00:00:00',
    `updated` datetime DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `user_sessions_guid_index` (`guid`),
    KEY `ip` (`ip`),
    KEY `created` (`created`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_session_track`
--

CREATE TABLE IF NOT EXISTS `user_session_track` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_session_id` int(11) DEFAULT '11',
    `lat` float(10,8) NOT NULL DEFAULT '0.00000000',
    `lng` float(10,8) DEFAULT '0.00000000',
    `updated` datetime DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `user_session_track_user_session_id_index` (`user_session_id`),
    KEY `user_session_track_lat_lng_index` (`lat`,`lng`),
    KEY `user_session_track_updated_index` (`updated`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;
