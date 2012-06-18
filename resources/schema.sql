--
-- NeoInvoice Database
--

CREATE TABLE `affiliate` (
   `id` int(10) unsigned not null auto_increment,
   `name` varchar(63) not null,
   `email` varchar(63) not null,
   `phone` varchar(15) not null,
   `commission` decimal(3,2) not null default '0.00',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `ci_sessions` (
   `session_id` varchar(40) not null default '0',
   `ip_address` varchar(16) not null default '0',
   `user_agent` varchar(50) not null,
   `last_activity` int(10) unsigned not null default '0',
   `user_data` varchar(512) not null,
   PRIMARY KEY (`session_id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

CREATE TABLE `client` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `active` tinyint(3) unsigned not null default '1',
   `name` varchar(64) not null,
   `email` varchar(64) not null,
   `phone` varchar(16) not null,
   `address` varchar(64) not null,
   `created` timestamp not null default CURRENT_TIMESTAMP,
   `modified` timestamp not null default '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   KEY `company_id` (`company_id`,`created`,`modified`),
   KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `company` (
   `id` int(10) unsigned not null auto_increment,
   `name` varchar(127) not null,
   `service_id` int(10) unsigned not null default '1',
   `coupon_id` int(10) unsigned,
   `service_expire` timestamp,
   `delete_date` date,
   `preferences` text not null,
   `invoice_address` varchar(512) not null,
   `created` timestamp not null default CURRENT_TIMESTAMP,
   `modified` timestamp not null default '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   KEY `owner_id` (`service_id`),
   KEY `service_expire` (`service_expire`),
   KEY `coupon_id` (`coupon_id`),
   KEY `delete_date` (`delete_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `coupon` (
   `id` int(10) unsigned not null auto_increment,
   `name` varchar(16) not null,
   `price` decimal(5,2) not null,
   `default_service_id` int(10) unsigned not null default '0',
   `default_service_expire` mediumint(8) unsigned not null default '0',
   `affiliate_id` int(10) unsigned,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`name`),
   KEY `default_service_id` (`default_service_id`),
   KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `emailsent` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `invoice_id` int(10) unsigned,
   `email` varchar(63) not null,
   `created` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `invoice_id` (`invoice_id`),
   KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `expense` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `project_id` int(10) unsigned not null,
   `invoice_id` int(10) unsigned,
   `expensetype_id` int(10) unsigned not null,
   `billable` tinyint(3) unsigned not null default '1',
   `amount` decimal(8,2) not null default '0.00',
   `date` date not null default '0000-00-00',
   `content` text not null,
   PRIMARY KEY (`id`),
   KEY `project_id` (`project_id`,`billable`,`date`),
   KEY `invoice_id` (`invoice_id`),
   KEY `company_id` (`company_id`),
   KEY `expensetype_id` (`expensetype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `expensetype` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `name` varchar(64) not null,
   `content` text not null,
   `taxable` int(2) not null,
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
   `company_id` int(10) unsigned not null,
   `project_id` int(10) unsigned not null,
   `user_id` int(10) unsigned not null,
   `worktype_id` int(10) unsigned not null,
   `invoice_id` int(10) unsigned,
   `ticket_id` int(10) unsigned,
   `billable` tinyint(3) unsigned not null default '1',
   `date` date not null default '0000-00-00',
   `time_start` time not null default '00:00:00',
   `duration` time not null default '00:00:00',
   `content` text not null,
   PRIMARY KEY (`id`),
   KEY `project_id` (`project_id`,`user_id`,`worktype_id`,`billable`,`date`,`time_start`),
   KEY `invoice_id` (`invoice_id`),
   KEY `company_id` (`company_id`),
   KEY `user_id` (`user_id`),
   KEY `worktype_id` (`worktype_id`),
   KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `service` (
   `id` int(10) unsigned not null auto_increment,
   `name` varchar(127) not null,
   `price` decimal(6,2) unsigned not null default '0.00',
   `pref_max_user` mediumint(9) unsigned not null,
   `pref_max_email` mediumint(8) unsigned not null default '0',
   `pref_custom_logo` tinyint(4) not null default '0',
   `pref_custom_motd` tinyint(4) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `service` (`id`, `name`, `price`, `pref_max_user`, `pref_max_email`, `pref_custom_logo`, `pref_custom_motd`) VALUES 
('1', 'Free', '0.00', '10', '100', '1', '0'),
('2', 'Agency', '9.99', '100', '250', '1', '0'),
('3', 'Corporation', '49.99', '1000', '1500', '1', '1');

CREATE TABLE `ticket` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `project_id` int(10) unsigned not null,
   `assigned_user_id` int(10) unsigned,
   `assigned_usergroup_id` int(10) unsigned,
   `created_user_id` int(10) unsigned,
   `ticket_stage_id` int(10) unsigned,
   `ticket_category_id` int(10) unsigned,
   `name` varchar(127) not null,
   `description` text not null,
   `due` date,
   `closed` timestamp,
   `created` timestamp not null default CURRENT_TIMESTAMP,
   `modified` timestamp not null default '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   KEY `company_id` (`company_id`),
   KEY `project_id` (`project_id`),
   KEY `assigned_user_id` (`assigned_user_id`),
   KEY `assigned_usergroup_id` (`assigned_usergroup_id`),
   KEY `created_user_id` (`created_user_id`),
   KEY `ticket_stage_id` (`ticket_stage_id`),
   KEY `ticket_category_id` (`ticket_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `ticket_category` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `name` varchar(127) not null,
   `description` text not null,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`company_id`,`name`),
   KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `ticket_stage` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `ticket_category_id` int(10) unsigned not null,
   `name` varchar(127) not null,
   `closed` tinyint(1) not null default '0',
   `description` text not null,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`ticket_category_id`,`name`),
   KEY `company_id` (`company_id`,`ticket_category_id`),
   KEY `closed` (`closed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `user` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `usergroup_id` int(10) unsigned,
   `active` tinyint(3) unsigned not null default '1',
   `username` varchar(32) not null,
   `password` varchar(40) not null,
   `lost_password` varchar(40) not null,
   `name` varchar(64) not null,
   `permissions` text not null,
   `preferences` text not null,
   `email` varchar(64) not null,
   `warning` int(10) unsigned not null default '0',
   `created` timestamp not null default CURRENT_TIMESTAMP,
   `modified` timestamp not null default '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`username`,`email`),
   KEY `company_id` (`company_id`),
   KEY `active` (`active`),
   KEY `usergroup_id` (`usergroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `usergroup` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(11) unsigned not null,
   `name` varchar(127) not null,
   `content` text not null,
   `permissions` text not null,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`company_id`,`name`),
   KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `worktype` (
   `id` int(10) unsigned not null auto_increment,
   `company_id` int(10) unsigned not null,
   `name` varchar(64) not null,
   `content` text not null,
   `hourlyrate` double(6,2) not null,
   PRIMARY KEY (`id`),
   KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;