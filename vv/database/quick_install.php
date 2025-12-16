<?php
/**
 * Quick Database Installation Script
 * Creates tables directly without parsing SQL file
 */

// Connect to MySQL
try {
    $dsn = "mysql:host=localhost;port=3306;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("Error: Cannot connect to MySQL. " . $e->getMessage());
}

echo "=== Quick Database Installation ===\n\n";

try {
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `kernel` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `kernel`");
    echo "✓ Database 'kernel' created/selected\n\n";
    
    // Create categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `categories` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `color` VARCHAR(7) DEFAULT '#2563EB',
        `description` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_name` (`name`),
        INDEX `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Table 'categories' created\n";
    
    // Create sujets table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `sujets` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Table 'sujets' created\n";
    
    // Create reponses table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `reponses` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Table 'reponses' created\n";
    
    // Create users table (optional)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Table 'users' created\n";
    
    // Insert sample categories
    $pdo->exec("INSERT IGNORE INTO `categories` (`name`, `color`, `description`) VALUES
        ('Général', '#2563EB', 'Discussions générales sur le forum'),
        ('Technique', '#10B981', 'Questions et discussions techniques'),
        ('Aide', '#F59E0B', 'Besoin d''aide ? Posez vos questions ici'),
        ('Annonces', '#EF4444', 'Annonces importantes du forum')");
    echo "✓ Sample categories inserted\n";
    
    echo "\n✅ All tables created successfully!\n";
    echo "\nYou can now use the forum application.\n";
    
} catch (PDOException $e) {
    die("\n✗ Error: " . $e->getMessage() . "\n");
}
?>

