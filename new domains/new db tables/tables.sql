
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
