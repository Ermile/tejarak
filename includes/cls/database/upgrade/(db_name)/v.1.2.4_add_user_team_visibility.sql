ALTER TABLE `userteams` CHANGE `status` `status` ENUM('active','deactive','disable','filter','leave','delete','parent','suspended') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'active';
ALTER TABLE `userteams` ADD `visibility` ENUM('visible','hidden') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'visible';