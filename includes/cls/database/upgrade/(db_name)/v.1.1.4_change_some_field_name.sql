ALTER TABLE `userteams` CHANGE `fistname` `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `user_status` `user_status` ENUM('active','awaiting','deactive','removed','filter','unreachable') NULL DEFAULT 'awaiting';