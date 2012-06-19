--
-- NeoInvoice Database
--
DROP DATABASE IF EXISTS neoinvoice;
CREATE DATABASE neoinvoice;

USE neoinvoice;

CREATE TABLE `affiliate` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `name` VARCHAR(63) NOT NULL,
   `email` VARCHAR(63) NOT NULL,
   `phone` VARCHAR(15) NOT NULL,
   `commission` DECIMAL(3,2) NOT NULL DEFAULT '0.00',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`email`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `ci_sessions` (
   `session_id` VARCHAR(40) NOT NULL DEFAULT '0',
   `ip_address` VARCHAR(16) NOT NULL DEFAULT '0',
   `user_agent` VARCHAR(50) NOT NULL,
   `last_activity` INT(10) UNSIGNED NOT NULL DEFAULT '0',
   `user_data` VARCHAR(512) NOT NULL,
   PRIMARY KEY (`session_id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

CREATE TABLE `client` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `company_id` INT(10) UNSIGNED NOT NULL,
   `active` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
   `name` VARCHAR(64) NOT NULL,
   `email` VARCHAR(64) NOT NULL,
   `phone` VARCHAR(16) NOT NULL,
   `address` VARCHAR(64) NOT NULL,
   `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   KEY `company_id` (`company_id`,`created`,`modified`),
   KEY `active` (`active`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `company` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `name` VARCHAR(127) NOT NULL,
   `service_id` INT(10) UNSIGNED NOT NULL DEFAULT '1',
   `coupon_id` INT(10) UNSIGNED,
   `created` TIMESTAMP NOT NULL DEFAULT NOW(),
   `modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
   `service_expire` TIMESTAMP,
   `delete_date` DATE,
   `preferences` TEXT NOT NULL,
   `invoice_address` VARCHAR(512) NOT NULL,
   PRIMARY KEY (`id`),
   KEY `owner_id` (`service_id`),
   KEY `service_expire` (`service_expire`),
   KEY `coupon_id` (`coupon_id`),
   KEY `delete_date` (`delete_date`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `coupon` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `name` VARCHAR(16) NOT NULL,
   `price` DECIMAL(5,2) NOT NULL,
   `default_service_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
   `default_service_expire` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
   `affiliate_id` INT(10) UNSIGNED,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`name`),
   KEY `default_service_id` (`default_service_id`),
   KEY `affiliate_id` (`affiliate_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `emailsent` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `company_id` INT(10) UNSIGNED NOT NULL,
   `invoice_id` INT(10) UNSIGNED,
   `email` VARCHAR(63) NOT NULL,
   `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `invoice_id` (`invoice_id`),
   KEY `company_id` (`company_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `expense` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `company_id` INT(10) UNSIGNED NOT NULL,
   `project_id` INT(10) UNSIGNED NOT NULL,
   `invoice_id` INT(10) UNSIGNED,
   `expensetype_id` INT(10) UNSIGNED NOT NULL,
   `billable` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
   `amount` DECIMAL(8,2) NOT NULL DEFAULT '0.00',
   `date` DATE NOT NULL DEFAULT '0000-00-00',
   `content` TEXT NOT NULL,
   PRIMARY KEY (`id`),
   KEY `project_id` (`project_id`,`billable`,`date`),
   KEY `invoice_id` (`invoice_id`),
   KEY `company_id` (`company_id`),
   KEY `expensetype_id` (`expensetype_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `expensetype` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `company_id` INT(10) UNSIGNED NOT NULL,
   `name` VARCHAR(64) NOT NULL,
   `content` TEXT NOT NULL,
   `taxable` INT(2) NOT NULL,
   PRIMARY KEY (`id`),
   KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `invoice` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `client_id` int(10) unsigned not null,
   `name` varchar(127) not null,
   `paid` tinyint(3) unsigned not null default '0',
   `sent` tinyint(3) unsigned not null default '0',
   `remind` tinyint(4) not null default '0',
   `duedate` date not null default '0000-00-00',
   `paiddate` date not null default '0000-00-00',
   `itemize` tinyint(3) unsigned not null default '0',
   `amount` decimal(8,2) unsigned not null,
   `content` text not null,
   `created` timestamp not null default CURRENT_TIMESTAMP,
   `modified` timestamp not null default '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   KEY `project_id` (`paid`,`duedate`,`amount`,`created`,`modified`),
   KEY `company_id` (`company_id`),
   KEY `sent` (`sent`),
   KEY `client_id` (`client_id`),
   KEY `remind` (`remind`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `language` (
   `id` int(10) unsigned not null auto_increment,
   `name` varchar(63) not null,
   `filename` varchar(63) not null,
   `code` char(2) not null,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3;

INSERT INTO `language` (`id`, `name`, `filename`, `code`) VALUES 
('1', 'English', 'english', 'en'),
('2', 'Deutsch', 'german', 'de');

CREATE TABLE `payment` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `invoice_id` int(10) unsigned not null,
   `amount` decimal(8,2) not null,
   `content` text not null,
   `date_received` date not null,
   PRIMARY KEY (`id`),
   KEY `company_id` (`company_id`),
   KEY `invoice_id` (`invoice_id`),
   KEY `date_received` (`date_received`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `project` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `client_id` int(10) unsigned not null,
   `active` tinyint(3) unsigned not null default '1',
   `name` varchar(127) not null,
   `content` text not null,
   `created` timestamp not null default CURRENT_TIMESTAMP,
   `modified` timestamp not null default '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   KEY `company_id` (`company_id`),
   KEY `client_id` (`client_id`),
   KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `segment` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` INT(10) UNSIGNED NOT NULL,
   `project_id` INT(10) UNSIGNED NOT NULL,
   `user_id` INT(10) UNSIGNED NOT NULL,
   `worktype_id` INT(10) UNSIGNED NOT NULL,
   `invoice_id` INT(10) UNSIGNED,
   `ticket_id` INT(10) UNSIGNED,
   `billable` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
   `date` DATE NOT NULL DEFAULT '0000-00-00',
   `time_start` TIME NOT NULL DEFAULT '00:00:00',
   `duration` TIME NOT NULL DEFAULT '00:00:00',
   `content` TEXT NOT NULL,
   PRIMARY KEY (`id`),
   KEY `project_id` (`project_id`,`user_id`,`worktype_id`,`billable`,`date`,`time_start`),
   KEY `invoice_id` (`invoice_id`),
   KEY `company_id` (`company_id`),
   KEY `user_id` (`user_id`),
   KEY `worktype_id` (`worktype_id`),
   KEY `ticket_id` (`ticket_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `service` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `name` VARCHAR(127) NOT NULL,
   `price` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
   `pref_max_user` MEDIUMINT(9) UNSIGNED NOT NULL,
   `pref_max_email` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
   `pref_custom_logo` TINYINT(4) NOT NULL DEFAULT '0',
   `pref_custom_motd` TINYINT(4) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `service` (`id`, `name`, `price`, `pref_max_user`, `pref_max_email`, `pref_custom_logo`, `pref_custom_motd`) VALUES 
('1', 'Free', '0.00', '10', '100', '1', '0'),
('2', 'Agency', '9.99', '100', '250', '1', '0'),
('3', 'Corporation', '49.99', '1000', '1500', '1', '1');

CREATE TABLE `ticket` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `company_id` INT(10) UNSIGNED NOT NULL,
   `project_id` INT(10) UNSIGNED NOT NULL,
   `assigned_user_id` INT(10) UNSIGNED,
   `assigned_usergroup_id` INT(10) UNSIGNED,
   `created_user_id` INT(10) UNSIGNED,
   `ticket_stage_id` INT(10) UNSIGNED,
   `ticket_category_id` INT(10) UNSIGNED,
   `name` VARCHAR(127) NOT NULL,
   `description` TEXT NOT NULL,
   `due` DATE,
   `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
   `closed` TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `company_id` (`company_id`),
   KEY `project_id` (`project_id`),
   KEY `assigned_user_id` (`assigned_user_id`),
   KEY `assigned_usergroup_id` (`assigned_usergroup_id`),
   KEY `created_user_id` (`created_user_id`),
   KEY `ticket_stage_id` (`ticket_stage_id`),
   KEY `ticket_category_id` (`ticket_category_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `ticket_category` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `company_id` INT(10) UNSIGNED NOT NULL,
   `name` VARCHAR(127) NOT NULL,
   `description` TEXT NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `company_id_name`(`company_id`,`name`),
   KEY `company_id` (`company_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `ticket_stage` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `company_id` INT(10) UNSIGNED NOT NULL,
   `ticket_category_id` INT(10) UNSIGNED NOT NULL,
   `name` VARCHAR(127) NOT NULL,
   `closed` TINYINT(1) NOT NULL DEFAULT '0',
   `description` TEXT NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`ticket_category_id`,`name`),
   KEY `company_id` (`company_id`,`ticket_category_id`),
   KEY `closed` (`closed`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `user` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `company_id` INT(10) UNSIGNED NOT NULL,
   `usergroup_id` INT(10) UNSIGNED,
   `active` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
   `username` VARCHAR(32) NOT NULL,
   `password` VARCHAR(40) NOT NULL,
   `lost_password` VARCHAR(40) NOT NULL,
   `name` VARCHAR(64) NOT NULL,
   `permissions` TEXT NOT NULL,
   `preferences` TEXT NOT NULL,
   `email` VARCHAR(64) NOT NULL,
   `warning` INT(10) UNSIGNED NOT NULL DEFAULT '0',
   `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`username`,`email`),
   KEY `company_id` (`company_id`),
   KEY `active` (`active`),
   KEY `usergroup_id` (`usergroup_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `usergroup` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `company_id` INT(11) UNSIGNED NOT NULL,
   `name` VARCHAR(127) NOT NULL,
   `content` TEXT NOT NULL,
   `permissions` TEXT NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `company_id_name` (`company_id`,`name`),
   KEY `company_id` (`company_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE `worktype` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `company_id` INT(10) UNSIGNED NOT NULL,
   `name` VARCHAR(64) NOT NULL,
   `content` TEXT NOT NULL,
   `hourlyrate` DOUBLE(6,2) NOT NULL,
   PRIMARY KEY (`id`),
   KEY `company_id` (`company_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;