-- Adminer 4.6.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `msg_count` bigint(20) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `username` (`username`),
  KEY `photo` (`photo`),
  KEY `link` (`link`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `groups_history`;
CREATE TABLE `groups_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `photo` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `name` (`name`),
  KEY `username` (`username`),
  KEY `photo` (`photo`),
  KEY `link` (`link`),
  CONSTRAINT `groups_history_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group_admins`;
CREATE TABLE `group_admins` (
  `group_id` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `role` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL,
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `group_admins_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_admins_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group_messages`;
CREATE TABLE `group_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` varchar(64) NOT NULL,
  `tmsg_id` bigint(20) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `reply_to_tmsg_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `group_messages_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_messages_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group_messages_data`;
CREATE TABLE `group_messages_data` (
  `message_id` bigint(20) NOT NULL,
  `text` text,
  `file` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  KEY `message_id` (`message_id`),
  KEY `file` (`file`),
  CONSTRAINT `group_messages_data_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `group_messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group_settings`;
CREATE TABLE `group_settings` (
  `group_id` varchar(64) NOT NULL,
  `welcome_message` text,
  `max_warns` int(11) NOT NULL DEFAULT '3',
  `mute` enum('on','off') NOT NULL DEFAULT 'off',
  `ask` enum('on','off') NOT NULL DEFAULT 'on',
  `shell` enum('on','off') NOT NULL DEFAULT 'off',
  `virtualizor` enum('on','off') NOT NULL DEFAULT 'off',
  `translate` enum('on','off') NOT NULL DEFAULT 'on',
  `ping` enum('on','off') NOT NULL DEFAULT 'on',
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  KEY `group_id` (`group_id`),
  CONSTRAINT `group_settings_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `private_messages`;
CREATE TABLE `private_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tmsg_id` bigint(20) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `reply_to_tmsg_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `private_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `private_messages_data`;
CREATE TABLE `private_messages_data` (
  `message_id` bigint(20) NOT NULL,
  `text` text,
  `file` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  KEY `file` (`file`),
  KEY `message_id` (`message_id`),
  CONSTRAINT `private_messages_data_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `private_messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `sudoers`;
CREATE TABLE `sudoers` (
  `user_id` varchar(64) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `session_active` bigint(20) DEFAULT NULL,
  `session_status` enum('on','off') NOT NULL DEFAULT 'off',
  `expired_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `sudoers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` varchar(64) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `private_message_count` bigint(20) NOT NULL DEFAULT '0',
  `group_message_count` bigint(20) NOT NULL DEFAULT '0',
  `is_bot` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `username` (`username`),
  KEY `photo` (`photo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `users_history`;
CREATE TABLE `users_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(64) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `username` (`username`),
  KEY `photo` (`photo`),
  CONSTRAINT `users_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `user_warning`;
CREATE TABLE `user_warning` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `warned_by` varchar(64) DEFAULT NULL,
  `reason` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `warned_by` (`warned_by`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `user_warning_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_warning_ibfk_3` FOREIGN KEY (`warned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_warning_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2018-06-25 06:45:35
