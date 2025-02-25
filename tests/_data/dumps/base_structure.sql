-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               8.0.19 - MySQL Community Server - GPL
-- Операционная система:         Win64
-- HeidiSQL Версия:              11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Дамп структуры базы данных bc
CREATE DATABASE IF NOT EXISTS `bc` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `bc`;

-- Дамп структуры для таблица bc.migration
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.phones
CREATE TABLE IF NOT EXISTS `phones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) NOT NULL COMMENT 'Телефон',
  `create_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата регистрации',
  `status` int DEFAULT NULL COMMENT 'Статус',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`),
  KEY `status` (`status`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.relation_users_to_phones
CREATE TABLE IF NOT EXISTS `relation_users_to_phones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `phone_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_phone_id` (`user_id`,`phone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_exceptions
CREATE TABLE IF NOT EXISTS `sys_exceptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  `code` int DEFAULT NULL,
  `statusCode` int DEFAULT NULL COMMENT 'HTTP status code',
  `file` varchar(255) DEFAULT NULL,
  `line` int DEFAULT NULL,
  `message` text,
  `trace` text,
  `get` text COMMENT 'GET',
  `post` text COMMENT 'POST',
  `known` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Known error',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_file_storage
CREATE TABLE IF NOT EXISTS `sys_file_storage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `model_name` varchar(255) DEFAULT NULL,
  `model_key` int DEFAULT NULL,
  `at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `daddy` int DEFAULT NULL,
  `delegate` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sys_file_storage_path` (`path`),
  KEY `sys_file_storage_model_name_model_key` (`model_name`,`model_key`),
  KEY `sys_file_storage_daddy` (`daddy`),
  KEY `sys_file_storage_deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_file_storage_tags
CREATE TABLE IF NOT EXISTS `sys_file_storage_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file` int NOT NULL,
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sys_file_storage_tags_file_tag` (`file`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_history
CREATE TABLE IF NOT EXISTS `sys_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user` int DEFAULT NULL,
  `model_class` varchar(255) DEFAULT NULL,
  `model_key` int DEFAULT NULL,
  `old_attributes` blob COMMENT 'Old serialized attributes',
  `new_attributes` blob COMMENT 'New serialized attributes',
  `relation_model` varchar(255) DEFAULT NULL,
  `scenario` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `operation_identifier` varchar(255) DEFAULT NULL,
  `delegate` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `model_class` (`model_class`),
  KEY `relation_model` (`relation_model`),
  KEY `model_key` (`model_key`),
  KEY `event` (`event`),
  KEY `operation_identifier` (`operation_identifier`),
  KEY `model_class_model_key` (`model_class`,`model_key`),
  KEY `delegate` (`delegate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_history_tags
CREATE TABLE IF NOT EXISTS `sys_history_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `history` int NOT NULL,
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `history_tag` (`history`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_import
CREATE TABLE IF NOT EXISTS `sys_import` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `domain` int NOT NULL,
  `data` blob,
  `processed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `processed` (`processed`),
  KEY `domain` (`domain`),
  KEY `model` (`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_notifications
CREATE TABLE IF NOT EXISTS `sys_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` int NOT NULL DEFAULT '0' COMMENT 'Тип уведомления',
  `initiator` int DEFAULT NULL COMMENT 'автор уведомления, null - система',
  `receiver` int DEFAULT NULL COMMENT 'получатель уведомления, null - определяется типом',
  `object_id` int DEFAULT NULL COMMENT 'идентификатор объекта уведомления, null - определяется типом',
  `comment` text,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_receiver_object_id` (`type`,`receiver`,`object_id`),
  KEY `type` (`type`),
  KEY `initiator` (`initiator`),
  KEY `receiver` (`receiver`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_options
CREATE TABLE IF NOT EXISTS `sys_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `option` varchar(256) NOT NULL COMMENT 'Option name',
  `value` blob COMMENT 'Serialized option value',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sys_options_option` (`option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_permissions
CREATE TABLE IF NOT EXISTS `sys_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL COMMENT 'Название доступа',
  `controller` varchar(255) DEFAULT NULL COMMENT 'Контроллер, к которому устанавливается доступ, null для внутреннего доступа',
  `action` varchar(255) DEFAULT NULL COMMENT 'Действие, для которого устанавливается доступ, null для всех действий контроллера',
  `verb` varchar(255) DEFAULT NULL COMMENT 'REST-метод, для которого устанавливается доступ',
  `module` varchar(255) DEFAULT NULL,
  `comment` text COMMENT 'Описание доступа',
  `priority` int NOT NULL DEFAULT '0' COMMENT 'Приоритет использования (больше - выше)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `controller_action_verb` (`controller`,`action`,`verb`),
  KEY `priority` (`priority`),
  KEY `module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_permissions_collections
CREATE TABLE IF NOT EXISTS `sys_permissions_collections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL COMMENT 'Название группы доступа',
  `comment` text COMMENT 'Описание группы доступа',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_relation_permissions_collections_to_permissions
CREATE TABLE IF NOT EXISTS `sys_relation_permissions_collections_to_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `collection_id` int NOT NULL COMMENT 'Ключ группы доступа',
  `permission_id` int NOT NULL COMMENT 'Ключ правила доступа',
  PRIMARY KEY (`id`),
  UNIQUE KEY `collection_id_permission_id` (`collection_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_relation_permissions_collections_to_permissions_collections
CREATE TABLE IF NOT EXISTS `sys_relation_permissions_collections_to_permissions_collections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `master_id` int NOT NULL,
  `slave_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_id_slave_id` (`master_id`,`slave_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_relation_users_to_permissions
CREATE TABLE IF NOT EXISTS `sys_relation_users_to_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'Ключ объекта доступа',
  `permission_id` int NOT NULL COMMENT 'Ключ правила доступа',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_permission_id` (`user_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_relation_users_to_permissions_collections
CREATE TABLE IF NOT EXISTS `sys_relation_users_to_permissions_collections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'Ключ объекта доступа',
  `collection_id` int NOT NULL COMMENT 'Ключ группы доступа',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_collection_id` (`user_id`,`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_status
CREATE TABLE IF NOT EXISTS `sys_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model_name` varchar(255) DEFAULT NULL,
  `model_key` int DEFAULT NULL,
  `status` int NOT NULL,
  `at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `daddy` int DEFAULT NULL,
  `delegate` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_name_model_key` (`model_name`,`model_key`),
  KEY `daddy` (`daddy`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_users
CREATE TABLE IF NOT EXISTS `sys_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL COMMENT 'Отображаемое имя пользователя',
  `login` varchar(64) NOT NULL COMMENT 'Логин',
  `password` varchar(255) NOT NULL COMMENT 'Хеш пароля',
  `salt` varchar(255) DEFAULT NULL COMMENT 'Unique random salt hash',
  `restore_code` varchar(40) DEFAULT NULL COMMENT 'Код восстановления',
  `is_pwd_outdated` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ожидается смена пароля',
  `email` varchar(255) NOT NULL COMMENT 'email',
  `comment` text COMMENT 'Служебный комментарий пользователя',
  `create_date` datetime NOT NULL COMMENT 'Дата регистрации',
  `daddy` int DEFAULT NULL COMMENT 'ID зарегистрировавшего/проверившего пользователя',
  `deleted` tinyint(1) DEFAULT '0' COMMENT 'Флаг удаления',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `username` (`username`),
  KEY `daddy` (`daddy`),
  KEY `deleted` (`deleted`),
  KEY `is_pwd_outdated` (`is_pwd_outdated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.sys_users_tokens
CREATE TABLE IF NOT EXISTS `sys_users_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'user id foreign key',
  `auth_token` varchar(40) NOT NULL COMMENT 'Bearer auth token',
  `type_id` tinyint NOT NULL COMMENT 'Тип токена',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Таймстамп создания',
  `valid` timestamp NULL DEFAULT NULL COMMENT 'Действует до',
  `ip` varchar(255) DEFAULT NULL COMMENT 'Адрес авторизации',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'User-Agent',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_auth_token` (`user_id`,`auth_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица bc.users_options
CREATE TABLE IF NOT EXISTS `users_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL COMMENT 'System user id',
  `option` varchar(256) NOT NULL COMMENT 'Option name',
  `value` blob COMMENT 'Serialized option value',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_options_user_id_option` (`user_id`,`option`),
  KEY `users_options_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Экспортируемые данные не выделены.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
