
CREATE TABLE IF NOT EXISTS `income_categories` (
  `incomecategoriesID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `note` varchar(200) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT '0',
  `created_on` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`incomecategoriesID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `admission_enquiry` (
  `enquiryID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `email` varchar(40) DEFAULT NULL,
  `address` text,
  `description` text,
  `note` text,
  `date` date DEFAULT NULL,
  `next_follow_up_date` date DEFAULT NULL,
  `assigned_usertypeID` int(11) DEFAULT NULL,
  `assigned_userID` int(11) DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `source` varchar(100) NOT NULL,
  `classesID` int(11) DEFAULT NULL,
  `num_child` int(11) DEFAULT NULL,
  `fee_particulars` text,
  `schoolyearID` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modify_date` datetime NOT NULL,
  `create_userID` int(11) NOT NULL,
  `create_usertypeID` int(11) NOT NULL,
  PRIMARY KEY (`enquiryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `student_siblings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `studentID` INT UNSIGNED NOT NULL,
  `sibling_studentID` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_sibling_pair` (`studentID`, `sibling_studentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 

CREATE TABLE IF NOT EXISTS `student_carry_forward` (
  `id` int(11) NOT NULL,
  `studentID` int(11) NOT NULL,
  `from_schoolyearID` int(11) NOT NULL,
  `to_schoolyearID` int(11) NOT NULL,
  `from_year_name` varchar(128) NOT NULL DEFAULT '',
  `total_fee` double NOT NULL DEFAULT '0',
  `total_discount` double NOT NULL DEFAULT '0',
  `total_paid_in_year` double NOT NULL DEFAULT '0',
  `total_waiver` double NOT NULL DEFAULT '0',
  `carry_forward_due` double NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE  IF NOT EXISTS `youtube_links` (
  `id` int(11) NOT NULL,
  `link` text COLLATE utf8_unicode_ci NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci,
  `subject_id` int(11) NOT NULL DEFAULT '0',
  `section_id` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created_by_usertype` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `record_type` varchar(50) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `performed_by_id` int(11) DEFAULT NULL,
  `performed_by_name` varchar(255) DEFAULT NULL,
  `performed_by_usertype` int(11) DEFAULT NULL,
  `performed_by_usertype_name` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_module_action` (`module`,`action`),
  KEY `idx_record` (`record_id`,`record_type`),
  KEY `idx_performed_by` (`performed_by_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `delete_account_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `reason` text,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `requested_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notification_event_config` (
    `id`                INT(11)      NOT NULL AUTO_INCREMENT,
    `event_key`         VARCHAR(50)  NOT NULL,
    `event_name`        VARCHAR(100) NOT NULL,
    `sms_enabled`       TINYINT(1)   NOT NULL DEFAULT 1,
    `whatsapp_enabled`  TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_event_key` (`event_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `daysheet_opening_balance` (
    `id`             INT(11)      NOT NULL AUTO_INCREMENT,
    `date`           DATE         NOT NULL,
    `account_type`   VARCHAR(50)  NOT NULL,
    `opening_amount` DOUBLE       NOT NULL DEFAULT '0',
    `schoolyearID`   INT(11)      NOT NULL DEFAULT '0',
    `created_by`     INT(11)      NOT NULL DEFAULT '0',
    `created_on`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_date_account_year` (`date`, `account_type`, `schoolyearID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
