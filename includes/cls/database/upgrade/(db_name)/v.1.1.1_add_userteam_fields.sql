ALTER TABLE `userteams` change `status` `status` enum('enable','disable','expire','removed','filter', 'leaved') DEFAULT 'enable';
ALTER TABLE `userteams` ADD `displayname`	varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL;
ALTER TABLE `userteams` ADD `permission`	varchar(1000) DEFAULT NULL;
ALTER TABLE `userteams` ADD `file_id`		int(20) UNSIGNED DEFAULT NULL;
ALTER TABLE `userteams` ADD `chat_id`		int(20) UNSIGNED DEFAULT NULL;
ALTER TABLE `userteams` ADD `name`			varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `family`		varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `father`		varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `birthday`		datetime DEFAULT NULL;
ALTER TABLE `userteams` ADD `usercode`		varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `nationalcode`	varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `from`			varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `nationality`	varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `brithplace`	varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `region`		varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `pasportcode`	varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `marital`		enum('single','marride') DEFAULT NULL;
ALTER TABLE `userteams` ADD `childcount`	smallint(2) DEFAULT NULL;
ALTER TABLE `userteams` ADD `education`		varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `insurancetype`	varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `insurancecode`	varchar(100) DEFAULT NULL;
ALTER TABLE `userteams` ADD `dependantscount`	smallint(4) DEFAULT NULL;
ALTER TABLE `userteams` ADD `postion`			varchar(100) DEFAULT NULL;