CREATE TABLE `session` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `sessionId` varchar(200) NOT NULL,
                           `ip` varchar(45) DEFAULT NULL,
                           `userId` int(11) DEFAULT NULL,
                           `expirationDate` datetime NOT NULL,
                           `token` varchar(200) DEFAULT NULL,
                           PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
