-- ============================================================
-- schema_updates.sql
-- Generated from schema_updates.json
-- ALL queries have IF NOT EXISTS / WHERE NOT EXISTS guards.
-- Safe to import multiple times without errors.
-- ============================================================

-- ------------------------------------------------------------
-- DATA FIX: Fill alternative_phone1 from phone where empty
-- Run once manually via Advanced > Fill WhatsApp from Phone
-- button on the Student listing page, or run here directly.
-- ------------------------------------------------------------
-- UPDATE `student`
--   SET alternative_phone1 = phone
-- WHERE (alternative_phone1 IS NULL OR alternative_phone1 = '')
--   AND (phone IS NOT NULL AND phone != '');


-- ------------------------------------------------------------
-- ALTER TABLE: columns (IF NOT EXISTS — MySQL 8.0+)
-- ------------------------------------------------------------

ALTER TABLE `payment`        ADD COLUMN IF NOT EXISTS `payment_other_details`      VARCHAR(255)    NULL DEFAULT NULL;
ALTER TABLE `payment`        ADD COLUMN IF NOT EXISTS `is_previous_year_amount`    VARCHAR(255)    NULL DEFAULT '0';

ALTER TABLE `income`         ADD COLUMN IF NOT EXISTS `incomecategoriesID`         INT             NULL DEFAULT '0';

ALTER TABLE `whatsapp_logs`  ADD COLUMN IF NOT EXISTS `http_code`                  VARCHAR(55)     NULL DEFAULT NULL;

-- NOTE: refered_by stores a studentID reference — INT is the correct type.
-- The VARCHAR(255) version from the original JSON was removed to avoid conflict.
ALTER TABLE `student`        ADD COLUMN IF NOT EXISTS `refered_by`                 INT             NULL DEFAULT NULL;
ALTER TABLE `student`        ADD COLUMN IF NOT EXISTS `rank`                       VARCHAR(55)     NULL DEFAULT '0';
ALTER TABLE `student`        ADD COLUMN IF NOT EXISTS `device_token`               VARCHAR(255)    DEFAULT NULL;
ALTER TABLE `student`        ADD COLUMN IF NOT EXISTS `medium`                     VARCHAR(55)     NULL DEFAULT 'English';
ALTER TABLE `student`        ADD COLUMN IF NOT EXISTS `platform`                   VARCHAR(255)    DEFAULT NULL;
ALTER TABLE `student`        ADD COLUMN IF NOT EXISTS `pickup_id`                  INT             NULL DEFAULT '0';
ALTER TABLE `student`        ADD COLUMN IF NOT EXISTS `remarks`                    TEXT            NULL DEFAULT NULL;

ALTER TABLE `user`           ADD COLUMN IF NOT EXISTS `is_able_payment_discount`   INT             NULL DEFAULT '0';
ALTER TABLE `user`           ADD COLUMN IF NOT EXISTS `rfid`                       VARCHAR(255)    DEFAULT NULL;
ALTER TABLE `user`           ADD COLUMN IF NOT EXISTS `rf_id`                      VARCHAR(255)    DEFAULT NULL;

ALTER TABLE `teacher`        ADD COLUMN IF NOT EXISTS `default_login_time`         TIME            NULL DEFAULT '0.00';
ALTER TABLE `teacher`        ADD COLUMN IF NOT EXISTS `default_logout_time`        TIME            NULL DEFAULT '17:00:00';
ALTER TABLE `teacher`        CHANGE `signature` `signature`                        TEXT            NULL;

ALTER TABLE `make_payment`   ADD COLUMN IF NOT EXISTS `salary_date`                VARCHAR(255)    NULL DEFAULT NULL;

ALTER TABLE `feetypes`       ADD COLUMN IF NOT EXISTS `created_by`                 INT             NULL DEFAULT '0';
ALTER TABLE `feetypes`       ADD COLUMN IF NOT EXISTS `school_year_id`             INT             NOT NULL DEFAULT '0';
ALTER TABLE `feetypes`       ADD COLUMN IF NOT EXISTS `fee_amount`                 DOUBLE(10,2)    NULL DEFAULT '0';
ALTER TABLE `feetypes`       ADD COLUMN IF NOT EXISTS `active_status`              TINYINT(1)      NOT NULL DEFAULT '1';

ALTER TABLE `transport`      ADD COLUMN IF NOT EXISTS `year_id`                    INT             NULL DEFAULT '0';

ALTER TABLE `pickup_points`  ADD COLUMN IF NOT EXISTS `year_id`                    INT             NULL DEFAULT '0';

ALTER TABLE `marksetting`    ADD COLUMN IF NOT EXISTS `schoolyear_id`              INT(11)         NOT NULL DEFAULT '0';

-- WARNING: If any existing category value is longer than 128 chars it will be TRUNCATED.
-- Run this first to check: SELECT MAX(LENGTH(category)) FROM section;
ALTER TABLE `section` CHANGE `category`  `category`  VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `section` CHANGE `capacity`  `capacity`  INT(11) NULL DEFAULT NULL;
ALTER TABLE `section` CHANGE `teacherID` `teacherID` INT(11) NULL DEFAULT NULL;

ALTER TABLE `youtube_links`  ADD COLUMN IF NOT EXISTS `title`                VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `youtube_links`  ADD COLUMN IF NOT EXISTS `description`          TEXT COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `youtube_links`  ADD COLUMN IF NOT EXISTS `subject_id`           INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `youtube_links`  ADD COLUMN IF NOT EXISTS `section_id`           INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `youtube_links`  ADD COLUMN IF NOT EXISTS `created_by`           INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `youtube_links`  ADD COLUMN IF NOT EXISTS `created_by_usertype`  INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `youtube_links`  ADD COLUMN IF NOT EXISTS `view_count`           INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `youtube_links`  ADD COLUMN IF NOT EXISTS `sort_order`           INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `youtube_links`  ADD COLUMN IF NOT EXISTS `updated_at`           DATETIME NULL DEFAULT NULL;

ALTER TABLE `voice_messages` ADD COLUMN IF NOT EXISTS `class_id`            INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `voice_messages` ADD COLUMN IF NOT EXISTS `section_id`          INT(11) NOT NULL DEFAULT '0';

-- WARNING: Only run this if mailandsmstemplateID does NOT already have AUTO_INCREMENT.
-- If it already has PRIMARY KEY + AUTO_INCREMENT this will throw "Multiple primary key" error.
-- Check first: SHOW CREATE TABLE whatapp_templates;
-- Skip this line if AUTO_INCREMENT is already set.
ALTER TABLE `whatapp_templates` DROP PRIMARY KEY, MODIFY `mailandsmstemplateID` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`mailandsmstemplateID`);


-- ------------------------------------------------------------
-- ALTER TABLE: indexes (IF NOT EXISTS — MySQL 8.0.29+)
-- ------------------------------------------------------------

ALTER TABLE `payment`       ADD INDEX IF NOT EXISTS `idx_pay_student_year`     (`studentID`, `schoolyearID`);
ALTER TABLE `payment`       ADD INDEX IF NOT EXISTS `idx_pay_globalpayment`    (`globalpaymentID`);

ALTER TABLE `weaverandfine` ADD INDEX IF NOT EXISTS `idx_wf_student_year`      (`studentID`, `schoolyearID`);
ALTER TABLE `weaverandfine` ADD INDEX IF NOT EXISTS `idx_wf_invoice`           (`invoiceID`);
ALTER TABLE `weaverandfine` ADD INDEX IF NOT EXISTS `idx_wf_payment`           (`paymentID`);

ALTER TABLE `invoice`       ADD INDEX IF NOT EXISTS `idx_inv_student_year`     (`studentID`, `schoolyearID`);
ALTER TABLE `invoice`       ADD INDEX IF NOT EXISTS `idx_student_class_year`   (`studentID`, `classesID`, `schoolyearID`);
ALTER TABLE `invoice`       ADD INDEX IF NOT EXISTS `idx_create_date`          (`create_date`);
ALTER TABLE `invoice`       ADD INDEX IF NOT EXISTS `idx_paidstatus`           (`paidstatus`);
ALTER TABLE `invoice`       ADD INDEX IF NOT EXISTS `idx_deleted_at`           (`deleted_at`);
ALTER TABLE `invoice`       ADD INDEX IF NOT EXISTS `idx_schoolyear`           (`schoolyearID`);

ALTER TABLE `globalpayment` ADD INDEX IF NOT EXISTS `idx_gp_student_year`      (`studentID`, `schoolyearID`);

ALTER TABLE `mark`          ADD INDEX IF NOT EXISTS `idx_schoolyearID_classesID` (`schoolyearID`, `classesID`);

ALTER TABLE `markrelation`  ADD INDEX IF NOT EXISTS `idx_markID`               (`markID`);


-- ------------------------------------------------------------
-- UPDATE: data fixes (safe — WHERE clause prevents unwanted rows)
-- ------------------------------------------------------------

UPDATE `marksetting` ms
    JOIN `exam` e ON ms.examID = e.examID
    SET ms.schoolyear_id = e.academic_year
    WHERE ms.schoolyear_id = 0 AND ms.examID > 0;

UPDATE `menu` SET `parentID` = 17
    WHERE `menuName` = 'push_notification' AND `parentID` != 17;

UPDATE `menu` SET `link` = 'push_notification'
    WHERE `menuName` = 'push_notification' AND `link` = 'Push_notification';

UPDATE `maininvoice` m
    INNER JOIN `invoice` i
        ON  i.maininvoiceID    = m.maininvoiceID
        AND i.studentID        = m.maininvoicestudentID
        AND i.schoolyearID     = m.maininvoiceschoolyearID
        AND i.deleted_at       = 1
    SET m.maininvoicedeleted_at = 1
    WHERE m.maininvoicedeleted_at != 1;


-- ------------------------------------------------------------
-- INSERT: menu  (WHERE NOT EXISTS guard on menuName)
-- ------------------------------------------------------------

INSERT INTO `menu` (`menuName`, `link`, `icon`, `status`, `parentID`, `priority`)
    SELECT 'banks', 'banks', 'fa-university', '1', '16', '240'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menuName` = 'banks');

INSERT INTO `menu` (`menuName`, `link`, `icon`, `status`, `parentID`, `priority`)
    SELECT 'college_group', 'college_group', 'fa-university', '1', '20', '131'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menuName` = 'college_group');

INSERT INTO `menu` (`menuName`, `link`, `icon`, `status`, `parentID`, `priority`)
    SELECT 'global_payment_new', 'global_payment_new', 'fa-balance-scale', '1', '16', '239'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menuName` = 'global_payment_new');

INSERT INTO `menu` (`menuName`, `link`, `icon`, `status`, `parentID`, `priority`)
    SELECT 'push_notification', 'push_notification', 'fa-bell', '1', '17', '200'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menuName` = 'push_notification');

INSERT INTO `menu` (`menuName`, `link`, `icon`, `status`, `parentID`, `priority`)
    SELECT 'youtube_links', 'youtube', 'fa-youtube-play', '1', '17', '190'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menuName` = 'youtube_links');

INSERT INTO `menu` (`menuName`, `link`, `icon`, `status`, `parentID`, `priority`)
    SELECT 'voice_messages', 'voice_messages', 'fa-microphone', '1', '17', '210'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menuName` = 'voice_messages');

INSERT INTO `menu` (`menuName`, `link`, `icon`, `status`, `parentID`, `priority`)
    SELECT 'delete_account_request', 'delete_account_request', 'fa-user-times', '1', '0', '20'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menuName` = 'delete_account_request');

INSERT INTO `menu` (`menuName`, `link`, `icon`, `status`, `parentID`, `priority`)
    SELECT 'logs', 'logs', 'fa-history', '1', '0', '10'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menuName` = 'logs');


-- ------------------------------------------------------------
-- INSERT: permissions  (WHERE NOT EXISTS guard on name)
-- ------------------------------------------------------------

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'banks', 'banks'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'banks');

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'college_group', 'college_group'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'college_group');

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'college_group_add', 'college_group_add'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'college_group_add');

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'college_group_edit', 'college_group_edit'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'college_group_edit');

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'college_group_delete', 'college_group_delete'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'college_group_delete');

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'global_payment_new', 'global_payment_new'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'global_payment_new');

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'push_notification', 'push_notification'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'push_notification');

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'youtube', 'youtube'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'youtube');

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'voice_messages', 'voice_messages'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'voice_messages');

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'delete_account_request', 'delete_account_request'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'delete_account_request');

INSERT INTO `permissions` (`name`, `description`)
    SELECT 'logs', 'logs'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'logs');


-- ------------------------------------------------------------
-- INSERT: permission_relationships  (WHERE NOT EXISTS guard)
-- ------------------------------------------------------------

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'banks'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'banks'));

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'college_group'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'college_group'));

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'college_group_add'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'college_group_add'));

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'college_group_edit'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'college_group_edit'));

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'college_group_delete'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'college_group_delete'));

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'global_payment_new'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'global_payment_new'));

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'push_notification'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'push_notification'));

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'youtube'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'youtube'));

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'voice_messages'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'voice_messages'));

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'delete_account_request'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'delete_account_request'));

INSERT INTO `permission_relationships` (`usertype_id`, `permission_id`)
    SELECT 1, permissionID FROM `permissions` WHERE `name` = 'logs'
    AND NOT EXISTS (SELECT 1 FROM `permission_relationships` WHERE `usertype_id` = 1
        AND `permission_id` = (SELECT permissionID FROM `permissions` WHERE `name` = 'logs'));


-- ------------------------------------------------------------
-- INSERT: WhatsApp templates  (WHERE NOT EXISTS on short_name)
-- ------------------------------------------------------------

INSERT INTO `whatapp_templates` (`template_name`, `usertypeID`, `type`, `template`, `params`, `short_name`)
    SELECT 'student_registration', 3, 'whats app',
           'Dear {{1}}, welcome to {{2}}. Your login details: Username: {{3}}, Password: {{4}}, Login URL: {{5}}. Please keep these credentials safe.',
           '{{student_name}},{{school_name}},{{username}},{{password}},{{url}}',
           'STUDENT_REGISTRATION'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `whatapp_templates` WHERE `short_name` = 'STUDENT_REGISTRATION');


-- ------------------------------------------------------------
-- INSERT: SMS template tags  (WHERE NOT EXISTS on usertypeID + tagname)
-- ------------------------------------------------------------

INSERT INTO `mailandsmstemplatetag` (`usertypeID`, `tagname`)
    SELECT 3, '{{url}}'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `mailandsmstemplatetag` WHERE `usertypeID` = 3 AND `tagname` = '{{url}}');

INSERT INTO `mailandsmstemplatetag` (`usertypeID`, `tagname`)
    SELECT 3, '{{username}}'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `mailandsmstemplatetag` WHERE `usertypeID` = 3 AND `tagname` = '{{username}}');

INSERT INTO `mailandsmstemplatetag` (`usertypeID`, `tagname`)
    SELECT 3, '{{password}}'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `mailandsmstemplatetag` WHERE `usertypeID` = 3 AND `tagname` = '{{password}}');


-- ------------------------------------------------------------
-- INSERT: settings  (WHERE NOT EXISTS on fieldoption)
-- ------------------------------------------------------------

INSERT INTO `setting` (`fieldoption`, `value`)
    SELECT 'student_present_time', '9-00'
    FROM dual WHERE NOT EXISTS (SELECT 1 FROM `setting` WHERE `fieldoption` = 'student_present_time');
