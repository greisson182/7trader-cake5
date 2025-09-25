-- --------------------------------------------------------
-- Servidor:                     localhost
-- Versão do servidor:           10.4.32-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Copiando estrutura para tabela 7trader.accounts
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_accounts_active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela 7trader.accounts: ~2 rows (aproximadamente)
INSERT INTO `accounts` (`id`, `name`, `active`, `created`, `modified`) VALUES
	(1, 'Simulador', 1, '2025-09-23 12:49:14', '2025-09-23 13:00:32'),
	(2, 'Real', 1, '2025-09-23 12:49:14', '2025-09-23 13:00:37');

-- Copiando estrutura para tabela 7trader.courses
DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(500) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 0,
  `difficulty` enum('Iniciante','Intermediário','Avançado') DEFAULT 'Iniciante',
  `category` varchar(100) DEFAULT NULL,
  `instructor` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `is_free` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `order_position` int(11) DEFAULT 0,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_difficulty` (`difficulty`),
  KEY `idx_active` (`is_active`),
  KEY `idx_order` (`order_position`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela 7trader.courses: ~3 rows (aproximadamente)
INSERT INTO `courses` (`id`, `title`, `description`, `thumbnail`, `duration_minutes`, `difficulty`, `category`, `instructor`, `price`, `is_free`, `is_active`, `order_position`, `created`, `modified`) VALUES
	(1, 'Fundamentos do Trading', 'Aprenda os conceitos básicos do trading e análise técnica', '/adm/images/course.jpg', 180, 'Iniciante', 'Trading Básico', 'Prof. João Silva', 0.00, 1, 1, 1, '2025-09-23 16:05:10', '2025-09-23 17:27:34'),
	(4, 'Introdução ao Trading', 'Curso básico para iniciantes no mundo do trading', '/adm/images/course.jpg', 0, 'Iniciante', 'Trading B??sico', 'Jo??o Silva', NULL, 1, 1, 1, '2025-09-23 16:25:06', '2025-09-23 19:26:34'),
	(5, 'Análise Técnica Avançada', 'técnicas avançadas de Análise Técnica voltadas para traders mais experientes.', '/adm/images/course.jpg', 0, '', 'An??lise T??cnica', 'Maria Santos', 299.90, 0, 1, 2, '2025-09-23 16:25:06', '2025-09-23 19:26:27');

-- Copiando estrutura para tabela 7trader.course_enrollments
DROP TABLE IF EXISTS `course_enrollments`;
CREATE TABLE IF NOT EXISTS `course_enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrolled_at` datetime DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `progress_percentage` decimal(5,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_enrollment` (`student_id`,`course_id`),
  KEY `idx_student` (`student_id`),
  KEY `idx_course` (`course_id`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `course_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela 7trader.course_enrollments: ~1 rows (aproximadamente)
INSERT INTO `course_enrollments` (`id`, `student_id`, `course_id`, `enrolled_at`, `completed_at`, `progress_percentage`, `is_active`, `created`, `modified`) VALUES
	(1, 1, 1, '2025-09-25 05:41:16', NULL, 0.00, 1, '2025-09-25 05:41:16', '2025-09-25 05:41:16');

-- Copiando estrutura para tabela 7trader.course_videos
DROP TABLE IF EXISTS `course_videos`;
CREATE TABLE IF NOT EXISTS `course_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(500) NOT NULL,
  `video_type` enum('youtube','vimeo','direct','embed') DEFAULT 'youtube',
  `duration_seconds` int(11) DEFAULT 0,
  `order_position` int(11) DEFAULT 0,
  `is_preview` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_course` (`course_id`),
  KEY `idx_order` (`order_position`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `course_videos_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela 7trader.course_videos: ~7 rows (aproximadamente)
INSERT INTO `course_videos` (`id`, `course_id`, `title`, `description`, `video_url`, `video_type`, `duration_seconds`, `order_position`, `is_preview`, `is_active`, `created`, `modified`) VALUES
	(1, 1, 'Introdução ao Trading', 'O que é trading e como começar', 'https://vimeo.com/1106614061', 'vimeo', 900, 1, 1, 1, '2025-09-23 16:05:10', '2025-09-23 18:32:23'),
	(2, 1, 'Tipos de Mercado', 'Forex, Ações, Commodities e Criptomoedas', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'youtube', 1200, 2, 0, 1, '2025-09-23 16:05:10', '2025-09-23 16:05:10'),
	(3, 1, 'Plataformas de Trading', 'Como escolher e usar uma plataforma', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'youtube', 800, 3, 0, 1, '2025-09-23 16:05:10', '2025-09-23 16:05:10'),
	(4, 1, 'O que ?? Trading?', 'Introdu????o b??sica ao conceito de trading', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'youtube', 600, 1, 1, 1, '2025-09-23 16:25:06', '2025-09-23 16:25:06'),
	(5, 1, 'Tipos de Mercado', 'Conhecendo os diferentes tipos de mercado financeiro', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'youtube', 900, 2, 0, 1, '2025-09-23 16:25:06', '2025-09-23 16:25:06'),
	(6, 1, 'Plataformas de Trading', 'Como escolher e usar plataformas de trading', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'youtube', 1200, 3, 0, 1, '2025-09-23 16:25:06', '2025-09-23 16:25:06'),
	(7, 4, 'O que são gatilhos?', '', 'https://www.youtube.com/watch?v=gHQCzGIjj7w', 'youtube', 1020, 1, 0, 1, '2025-09-23 19:56:10', '2025-09-23 19:56:10');

-- Copiando estrutura para tabela 7trader.files
DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `name_simple` varchar(255) DEFAULT NULL,
  `slug` varchar(300) DEFAULT NULL,
  `url` varchar(1500) DEFAULT NULL,
  `realpeth` varchar(1500) DEFAULT NULL,
  `size` float DEFAULT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `content_type` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `file_key` varchar(255) DEFAULT NULL,
  `file_log` text DEFAULT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `others_info` varchar(255) DEFAULT NULL,
  `phase` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `occult` int(11) DEFAULT 0,
  `user_id` int(11) DEFAULT 0,
  `status` enum('a','i','d') DEFAULT 'a',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=849 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Copiando dados para a tabela 7trader.files: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela 7trader.groupps
DROP TABLE IF EXISTS `groupps`;
CREATE TABLE IF NOT EXISTS `groupps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_groupps_types` (`type_id`),
  CONSTRAINT `FK_groupps_types` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Copiando dados para a tabela 7trader.groupps: ~3 rows (aproximadamente)
INSERT INTO `groupps` (`id`, `name`, `created`, `modified`, `type_id`) VALUES
	(1, '7 Trader', '2025-09-24 18:26:08', '2025-09-24 18:26:09', 1),
	(2, 'Alunos', '2025-09-24 18:26:54', '2025-09-24 18:26:55', 3),
	(3, 'Professor', '2025-09-24 18:32:53', '2025-09-24 18:32:53', 2);

-- Copiando estrutura para tabela 7trader.markets
DROP TABLE IF EXISTS `markets`;
CREATE TABLE IF NOT EXISTS `markets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(50) NOT NULL DEFAULT 'forex',
  `currency` varchar(3) NOT NULL DEFAULT 'BRL',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `symbol` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela 7trader.markets: ~2 rows (aproximadamente)
INSERT INTO `markets` (`id`, `name`, `code`, `description`, `active`, `type`, `currency`, `created_at`, `updated_at`) VALUES
	(1, 'Índice Futuro', 'WINFUT', '', 1, 'indice', 'BRL', '2025-09-23 12:49:14', '2025-09-23 13:00:59'),
	(2, 'Dólar Futuro', 'WDOFUT', '', 1, 'indice', 'BRL', '2025-09-23 12:49:14', '2025-09-23 13:01:02');

-- Copiando estrutura para tabela 7trader.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `status` enum('a','i','d') DEFAULT 'a',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Copiando dados para a tabela 7trader.permissions: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela 7trader.permissions_profiles
DROP TABLE IF EXISTS `permissions_profiles`;
CREATE TABLE IF NOT EXISTS `permissions_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_permissoes_has_perfis_perfis1_idx` (`profile_id`),
  KEY `fk_permissoes_has_perfis_permissoes1_idx` (`permission_id`),
  CONSTRAINT `fk_permissoes_has_perfis_perfis1` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`),
  CONSTRAINT `fk_permissoes_has_perfis_permissoes1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Copiando dados para a tabela 7trader.permissions_profiles: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela 7trader.profiles
DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Copiando dados para a tabela 7trader.profiles: ~2 rows (aproximadamente)
INSERT INTO `profiles` (`id`, `name`, `type`) VALUES
	(1, 'Administrador', NULL),
	(2, 'Aluno', NULL);

-- Copiando estrutura para tabela 7trader.settings
DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maintenance` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `face_app_id` varchar(255) DEFAULT NULL,
  `face_author` varchar(255) DEFAULT NULL,
  `face_publisher` varchar(255) DEFAULT NULL,
  `google_publisher` varchar(255) DEFAULT NULL,
  `google_author` varchar(255) DEFAULT NULL,
  `aws_api` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Copiando dados para a tabela 7trader.settings: ~0 rows (aproximadamente)
INSERT INTO `settings` (`id`, `maintenance`, `title`, `description`, `image`, `email`, `face_app_id`, `face_author`, `face_publisher`, `google_publisher`, `google_author`, `aws_api`, `created`, `modified`) VALUES
	(1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- Copiando estrutura para tabela 7trader.students
DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela 7trader.students: ~2 rows (aproximadamente)
INSERT INTO `students` (`id`, `name`, `email`, `phone`, `created`, `modified`) VALUES
	(1, 'Greisson Silva', 'greisson182@gmail.com', '(41) 99275-2998', '2025-09-23 12:49:14', '2025-09-23 13:03:19'),
	(2, 'Luan Silva', 'luanlp@gmail.com', NULL, '2025-09-23 13:13:50', '2025-09-23 13:13:58');

-- Copiando estrutura para tabela 7trader.student_progress
DROP TABLE IF EXISTS `student_progress`;
CREATE TABLE IF NOT EXISTS `student_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `watched_seconds` int(11) DEFAULT 0,
  `completed` tinyint(1) DEFAULT 0,
  `last_watched` datetime DEFAULT current_timestamp(),
  `watched_at` datetime DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT current_timestamp(),
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_progress` (`student_id`,`video_id`),
  KEY `idx_student` (`student_id`),
  KEY `idx_course` (`course_id`),
  KEY `idx_video` (`video_id`),
  KEY `idx_completed` (`completed`),
  CONSTRAINT `student_progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_progress_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_progress_ibfk_3` FOREIGN KEY (`video_id`) REFERENCES `course_videos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela 7trader.student_progress: ~1 rows (aproximadamente)
INSERT INTO `student_progress` (`id`, `student_id`, `course_id`, `video_id`, `watched_seconds`, `completed`, `last_watched`, `watched_at`, `completed_at`, `created`, `modified`) VALUES
	(1, 1, 1, 1, 0, 0, '2025-09-25 05:41:07', '2025-09-25 05:55:38', '2025-09-25 05:41:07', '2025-09-25 05:41:07', '2025-09-25 05:55:38');

-- Copiando estrutura para tabela 7trader.studies
DROP TABLE IF EXISTS `studies`;
CREATE TABLE IF NOT EXISTS `studies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `market_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `study_date` date NOT NULL,
  `wins` int(11) NOT NULL DEFAULT 0,
  `losses` int(11) NOT NULL DEFAULT 0,
  `profit_loss` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_studies_student` (`student_id`),
  KEY `idx_studies_market` (`market_id`),
  KEY `idx_studies_account` (`account_id`),
  KEY `idx_studies_dates` (`study_date`),
  CONSTRAINT `studies_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `studies_ibfk_2` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `studies_ibfk_3` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela 7trader.studies: ~17 rows (aproximadamente)
INSERT INTO `studies` (`id`, `student_id`, `market_id`, `account_id`, `study_date`, `wins`, `losses`, `profit_loss`, `notes`, `created`, `modified`) VALUES
	(1, 1, 1, 1, '2025-01-02', 5, 1, 330.00, '', '2025-09-23 13:13:10', '2025-09-23 13:31:34'),
	(2, 2, 2, 1, '2025-02-03', 5, 2, 305.00, '', '2025-09-23 13:16:19', '2025-09-23 13:31:37'),
	(3, 1, 1, 1, '2025-01-03', 4, 9, -185.00, '', '2025-09-23 13:26:07', '2025-09-23 13:31:38'),
	(4, 1, 1, 1, '2025-01-06', 12, 17, -140.50, '', '2025-09-23 13:26:58', '2025-09-23 13:31:38'),
	(5, 1, 1, 1, '2025-01-07', 3, 0, 204.00, '', '2025-09-23 13:29:34', '2025-09-23 13:31:39'),
	(6, 1, 1, 1, '2025-01-08', 3, 9, -169.00, '', '2025-09-23 13:30:18', '2025-09-23 13:31:40'),
	(7, 1, 1, 1, '2025-01-09', 11, 10, -69.00, '', '2025-09-23 13:47:58', '2025-09-23 13:48:07'),
	(8, 1, 1, 1, '2025-01-10', 12, 10, 16.00, '', '2025-09-23 13:48:28', '2025-09-23 13:48:28'),
	(9, 1, 1, 1, '2025-01-13', 8, 6, 226.00, '', '2025-09-23 13:48:52', '2025-09-23 13:48:52'),
	(10, 1, 1, 1, '2025-01-14', 7, 2, 205.00, '', '2025-09-23 13:49:24', '2025-09-23 13:49:24'),
	(11, 1, 1, 1, '2025-01-15', 4, 6, -162.00, '', '2025-09-23 13:49:47', '2025-09-23 13:49:47'),
	(12, 1, 1, 1, '2025-01-16', 12, 5, 222.00, '', '2025-09-23 13:50:19', '2025-09-23 13:50:19'),
	(13, 1, 1, 1, '2025-01-17', 2, 9, 109.00, '', '2025-09-23 13:50:44', '2025-09-23 13:50:44'),
	(14, 1, 1, 1, '2025-01-20', 9, 4, 166.00, '', '2025-09-23 13:51:10', '2025-09-23 13:51:10'),
	(15, 1, 1, 1, '2025-02-03', 2, 1, 100.00, '', '2025-09-23 14:28:56', '2025-09-23 14:28:56'),
	(16, 1, 1, 1, '2025-03-03', 3, 1, 100.00, '', '2025-09-23 14:33:36', '2025-09-23 14:33:36'),
	(19, 1, 1, 1, '2025-09-02', 2, 1, 100.00, '', '2025-09-25 03:45:02', '2025-09-25 03:45:02');

-- Copiando estrutura para tabela 7trader.types
DROP TABLE IF EXISTS `types`;
CREATE TABLE IF NOT EXISTS `types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Copiando dados para a tabela 7trader.types: ~3 rows (aproximadamente)
INSERT INTO `types` (`id`, `name`) VALUES
	(1, 'Administrador'),
	(2, 'Professor'),
	(3, 'Aluno');

-- Copiando estrutura para tabela 7trader.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'student',
  `student_id` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `blocked` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('a','i','d') NOT NULL DEFAULT 'a',
  `last_login` datetime NOT NULL DEFAULT current_timestamp(),
  `groupp_id` int(11) DEFAULT NULL,
  `profile_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `FK_users_groupps` (`groupp_id`),
  KEY `FK_users_profiles` (`profile_id`),
  CONSTRAINT `FK_users_groupps` FOREIGN KEY (`groupp_id`) REFERENCES `groupps` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_users_profiles` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela 7trader.users: ~3 rows (aproximadamente)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `salt`, `role`, `student_id`, `active`, `blocked`, `created`, `modified`, `status`, `last_login`, `groupp_id`, `profile_id`) VALUES
	(1, 'Administrador', 'admin', 'admin@backtest.com', '$2y$10$E3belYzSnodJDLc1zZ5s.OdyqLyNwzsxdaFIngjPPpzSJlSIAyuz2', '0c3ec867000fccadfb6171', 'admin', NULL, 1, 0, '2025-09-23 12:49:14', '2025-09-25 04:27:39', 'a', '2025-09-25 04:07:14', 1, 1),
	(2, 'Greisson Silva', 'greisson', 'greisson182@gmail.com', '$2y$10$T9vDnM.lbxCFAoEaKHoqj.mvqIyUnm/SYaIzer7eZf23IPaNZ8sPm', '0c3ec867000fccadfb6171', 'student', 1, 1, 0, '2025-09-23 12:56:38', '2025-09-25 05:55:38', 'a', '2025-09-25 04:29:51', 2, 2),
	(3, 'Luan', 'luan', 'luanlp@gmail.com', '$2y$10$E3belYzSnodJDLc1zZ5s.OdyqLyNwzsxdaFIngjPPpzSJlSIAyuz2', '0c3ec867000fccadfb6171', 'student', 2, 1, 0, '2025-09-23 13:14:15', '2025-09-25 04:27:40', 'a', '2025-09-25 00:05:37', 2, 2);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
