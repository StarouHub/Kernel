-- ============================================
-- Database Schema for Kernel Forum
-- ============================================
-- Database: kernel
-- Character Set: utf8mb4
-- Collation: utf8mb4_unicode_ci
-- ============================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `kernel` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `kernel`;

-- ============================================
-- Table: categories
-- Description: Categories for organizing forum topics
-- ============================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `color` VARCHAR(7) DEFAULT '#2563EB',
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_name` (`name`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: sujets
-- Description: Forum topics/subjects
-- ============================================
CREATE TABLE IF NOT EXISTS `sujets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titre` VARCHAR(255) NOT NULL,
    `contenu` TEXT NOT NULL,
    `categorie_id` INT NOT NULL,
    `user_id` INT NULL,
    `views` INT DEFAULT 0,
    `is_pinned` TINYINT(1) DEFAULT 0,
    `is_locked` TINYINT(1) DEFAULT 0,
    `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`categorie_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_categorie_id` (`categorie_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_date_creation` (`date_creation`),
    INDEX `idx_is_pinned` (`is_pinned`),
    FULLTEXT INDEX `idx_search` (`titre`, `contenu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: reponses
-- Description: Replies/responses to forum topics
-- ============================================
CREATE TABLE IF NOT EXISTS `reponses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `contenu` TEXT NOT NULL,
    `sujet_id` INT NOT NULL,
    `user_id` INT NULL,
    `likes` INT DEFAULT 0,
    `is_edited` TINYINT(1) DEFAULT 0,
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `date_modification` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`sujet_id`) REFERENCES `sujets`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_sujet_id` (`sujet_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_date` (`date`),
    INDEX `idx_likes` (`likes`),
    FULLTEXT INDEX `idx_search` (`contenu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: users (Optional - for future user management)
-- Description: User accounts
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'moderator', 'admin') DEFAULT 'user',
    `avatar` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    INDEX `idx_username` (`username`),
    INDEX `idx_email` (`email`),
    INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert Sample Data (Optional)
-- ============================================

-- Sample categories
INSERT INTO `categories` (`name`, `color`, `description`) VALUES
('Général', '#2563EB', 'Discussions générales sur le forum'),
('Technique', '#10B981', 'Questions et discussions techniques'),
('Aide', '#F59E0B', 'Besoin d''aide ? Posez vos questions ici'),
('Annonces', '#EF4444', 'Annonces importantes du forum')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- ============================================
-- Views (Optional - for complex queries)
-- ============================================

-- View: sujet_with_stats
CREATE OR REPLACE VIEW `sujet_with_stats` AS
SELECT 
    s.*,
    c.name as categorie_name,
    c.color as categorie_color,
    COUNT(r.id) as reponse_count,
    MAX(r.date) as last_reponse_date
FROM `sujets` s
LEFT JOIN `categories` c ON s.categorie_id = c.id
LEFT JOIN `reponses` r ON s.id = r.sujet_id
GROUP BY s.id;

-- View: categorie_with_stats
CREATE OR REPLACE VIEW `categorie_with_stats` AS
SELECT 
    c.*,
    COUNT(DISTINCT s.id) as sujet_count,
    COUNT(DISTINCT r.id) as reponse_count,
    MAX(s.date_creation) as last_sujet_date
FROM `categories` c
LEFT JOIN `sujets` s ON c.id = s.categorie_id
LEFT JOIN `reponses` r ON s.id = r.sujet_id
GROUP BY c.id;

-- ============================================
-- Stored Procedures (Optional)
-- ============================================

DELIMITER //

-- Procedure: Get sujet with all related data
CREATE PROCEDURE IF NOT EXISTS `sp_get_sujet_full`(IN sujet_id INT)
BEGIN
    SELECT 
        s.*,
        c.name as categorie_name,
        c.color as categorie_color,
        (SELECT COUNT(*) FROM reponses WHERE sujet_id = s.id) as reponse_count
    FROM sujets s
    LEFT JOIN categories c ON s.categorie_id = c.id
    WHERE s.id = sujet_id;
END //

-- Procedure: Get recent sujets
CREATE PROCEDURE IF NOT EXISTS `sp_get_recent_sujets`(IN limit_count INT)
BEGIN
    SELECT 
        s.*,
        c.name as categorie_name,
        c.color as categorie_color,
        (SELECT COUNT(*) FROM reponses WHERE sujet_id = s.id) as reponse_count
    FROM sujets s
    LEFT JOIN categories c ON s.categorie_id = c.id
    ORDER BY s.date_creation DESC
    LIMIT limit_count;
END //

DELIMITER ;

-- ============================================
-- Triggers (Optional)
-- ============================================

DELIMITER //

-- Trigger: Update sujet modification date when reponse is added
CREATE TRIGGER IF NOT EXISTS `trg_reponse_insert` 
AFTER INSERT ON `reponses`
FOR EACH ROW
BEGIN
    UPDATE `sujets` 
    SET `date_modification` = NOW() 
    WHERE `id` = NEW.sujet_id;
END //

DELIMITER ;

-- ============================================
-- End of Schema
-- ============================================

