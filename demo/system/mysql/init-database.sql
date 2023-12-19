--
-- база данных демо-сайта
--

SET NAMES "utf8mb4";

-- создать базы данных:
CREATE SCHEMA IF NOT EXISTS `phpapp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE SCHEMA IF NOT EXISTS `phpapp_auth` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- создать юзеров:
CREATE USER IF NOT EXISTS 'phpappuser'@'%' IDENTIFIED BY 'mypassword';
GRANT SELECT,INSERT,UPDATE,DELETE ON `phpapp`.* TO 'phpappuser'@'%';
GRANT SELECT,INSERT,UPDATE,DELETE ON `phpapp_auth`.* TO 'phpappuser'@'%';

-- создать некоторые таблицы:
-- таблица сессий
-- сессии храним в бд, а не в файлах, чтобы в случае репликации по серверам юзер логинился на всех серверах сразу
CREATE TABLE `phpapp_auth`.`sessions` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`sid` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`data` BLOB,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `index1` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- таблица юзеров
CREATE TABLE `phpapp_auth`.`users` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`login` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`email` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`password` CHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`email_confirmed` TINYINT UNSIGNED DEFAULT NULL,
	`is_admin` TINYINT UNSIGNED DEFAULT 0,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `index1` (`email`,`email_confirmed`),
	UNIQUE KEY `index2` (`login`,`email_confirmed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- таблица токенов
CREATE TABLE `phpapp_auth`.`tokens` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT UNSIGNED NOT NULL,
	`action` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`used` TINYINT UNSIGNED NOT NULL DEFAULT '0',
	`hash` CHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`last_sent` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `index1` (`hash`),
	KEY `fk_tokens_1_idx` (`user_id`),
	CONSTRAINT `fk_tokens_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- статьи
CREATE TABLE `phpapp`.`articles` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT UNSIGNED NOT NULL,
	`title` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
	`content` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `fk_articles_1_idx` (`user_id`),
	CONSTRAINT `fk_articles_1` FOREIGN KEY (`user_id`) REFERENCES `phpapp_auth`.`users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- данные
INSERT INTO `phpapp_auth`.`users` VALUES (1,'Админ',NULL,'admin@mysite.ru','ce8635b4dad92b0565821fa9359974cc',1,1,'2000-01-01 00:00:00');
INSERT INTO `phpapp_auth`.`users` VALUES (2,'Юзер А',NULL,'usera@mail.ru','ce8635b4dad92b0565821fa9359974cc',1,0,NOW());
INSERT INTO `phpapp_auth`.`users` VALUES (3,'Юзер Б',NULL,'userb@mail.ru','ce8635b4dad92b0565821fa9359974cc',1,0,NOW());
INSERT INTO `phpapp`.`articles` VALUES (1,1,'Название 1','Контент 1',NOW(),NOW());
INSERT INTO `phpapp`.`articles` VALUES (2,1,'Название 2','Контент 2',NOW(),NOW());
INSERT INTO `phpapp`.`articles` VALUES (3,1,'Название 3','Контент 3',NOW(),NOW());
INSERT INTO `phpapp`.`articles` VALUES (4,1,'Название 4','Контент 4',NOW(),NOW());
INSERT INTO `phpapp`.`articles` VALUES (5,1,'Название 5','Контент 5',NOW(),NOW());
INSERT INTO `phpapp`.`articles` VALUES (6,2,'Название 6','Контент 6',NOW(),NOW());
INSERT INTO `phpapp`.`articles` VALUES (7,2,'Название 7','Контент 7',NOW(),NOW());
INSERT INTO `phpapp`.`articles` VALUES (8,2,'Название 8','Контент 8',NOW(),NOW());
INSERT INTO `phpapp`.`articles` VALUES (9,3,'Название 9','Контент 9',NOW(),NOW());
INSERT INTO `phpapp`.`articles` VALUES (10,3,'Название 10','Контент 10',NOW(),NOW());
