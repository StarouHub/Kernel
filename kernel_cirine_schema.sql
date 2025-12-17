-- ============================================
-- KERNEL CIRINE - SCHEMA DE BASE DE DONNEES
-- ============================================
-- Date: 2025-12-10
-- Database: kernel_cirine
-- Version: 1.0
-- ============================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS kernel_cirine 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE kernel_cirine;

-- ============================================
-- TABLE 1: UTILISATEURS (users)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255),
    role ENUM('user', 'admin', 'technicien') DEFAULT 'user',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME,
    statut ENUM('actif', 'inactif', 'suspendu') DEFAULT 'actif',
    telephone VARCHAR(20),
    service VARCHAR(100),
    avatar VARCHAR(500),
    preferences TEXT,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 2: RECLAMATIONS
-- ============================================
CREATE TABLE IF NOT EXISTS reclamations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    titre VARCHAR(150) NOT NULL,
    description LONGTEXT NOT NULL,
    type ENUM('bug', 'technique', 'contenu', 'suggestion', 'autre') DEFAULT 'autre',
    priorite ENUM('urgente', 'haute', 'normale', 'basse', 'critique') DEFAULT 'normale',
    statut ENUM('en-attente', 'en-cours', 'resolue', 'fermee') DEFAULT 'en-attente',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME ON UPDATE CURRENT_TIMESTAMP,
    date_fermeture DATETIME,
    assigne_a INT,
    temps_estime_minutes INT,
    categorie VARCHAR(50),
    sous_categorie VARCHAR(50),
    environnement VARCHAR(100),
    version_logiciel VARCHAR(50),
    priority_score INT DEFAULT 0,
    priority_reason TEXT,
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigne_a) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_statut (statut),
    INDEX idx_priorite (priorite),
    INDEX idx_type (type),
    INDEX idx_date_creation (date_creation),
    INDEX idx_assigne (assigne_a),
    FULLTEXT INDEX ft_titre_description (titre, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 3: REPONSES / COMMENTAIRES
-- ============================================
CREATE TABLE IF NOT EXISTS reponses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reclamation_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    message LONGTEXT NOT NULL,
    est_admin BOOLEAN DEFAULT FALSE,
    date_reponse DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu_par_utilisateur BOOLEAN DEFAULT FALSE,
    lu_par_admin BOOLEAN DEFAULT FALSE,
    est_interne BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (reclamation_id) REFERENCES reclamations(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_reclamation (reclamation_id),
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_date (date_reponse)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 4: PIECES JOINTES / FICHIERS
-- ============================================
CREATE TABLE IF NOT EXISTS pieces_jointes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reclamation_id INT NOT NULL,
    reponse_id INT,
    nom_original VARCHAR(255) NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin VARCHAR(500) NOT NULL,
    taille_octets INT NOT NULL,
    type_mime VARCHAR(100),
    extension VARCHAR(10),
    date_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    upload_par INT NOT NULL,
    est_valide BOOLEAN DEFAULT TRUE,
    hash_verification VARCHAR(64),
    FOREIGN KEY (reclamation_id) REFERENCES reclamations(id) ON DELETE CASCADE,
    FOREIGN KEY (reponse_id) REFERENCES reponses(id) ON DELETE CASCADE,
    FOREIGN KEY (upload_par) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_reclamation (reclamation_id),
    INDEX idx_reponse (reponse_id),
    INDEX idx_upload_par (upload_par),
    INDEX idx_type (type_mime),
    INDEX idx_date (date_upload)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 5: NOTIFICATIONS
-- ============================================
CREATE TABLE IF NOT EXISTS notifications_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'danger', 'system', 'user_reclamation', 'admin_reply', 'user_reply', 'status_change') DEFAULT 'info',
    category VARCHAR(50),
    reclamation_id INT,
    reponse_id INT,
    metadata TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    is_archived BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reclamation_id) REFERENCES reclamations(id) ON DELETE SET NULL,
    FOREIGN KEY (reponse_id) REFERENCES reponses(id) ON DELETE SET NULL,
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_unread (user_id, is_read),
    INDEX idx_type (type),
    INDEX idx_category (category),
    INDEX idx_reclamation (reclamation_id),
    INDEX idx_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 6: ANALYSE DE PRIORITE (IA)
-- ============================================
CREATE TABLE IF NOT EXISTS priority_analyses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reclamation_id INT NOT NULL,
    priority VARCHAR(50),
    score INT,
    reason TEXT,
    confidence FLOAT,
    keywords TEXT,
    sentiment_score FLOAT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reclamation_id) REFERENCES reclamations(id) ON DELETE CASCADE,
    INDEX idx_reclamation (reclamation_id),
    INDEX idx_priority (priority),
    INDEX idx_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 7: MOTS-CLES DE PRIORITE
-- ============================================
CREATE TABLE IF NOT EXISTS priority_keywords (
    id INT PRIMARY KEY AUTO_INCREMENT,
    keyword VARCHAR(100) UNIQUE NOT NULL,
    priority_level ENUM('critique', 'haute', 'normale', 'basse') DEFAULT 'normale',
    frequency INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_keyword (keyword),
    INDEX idx_priority (priority_level),
    INDEX idx_frequency (frequency)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 8: MOTS SENTIMENTS
-- ============================================
CREATE TABLE IF NOT EXISTS sentiment_words (
    id INT PRIMARY KEY AUTO_INCREMENT,
    word VARCHAR(100) NOT NULL UNIQUE,
    sentiment ENUM('positif', 'negatif', 'neutre') DEFAULT 'neutre',
    weight FLOAT DEFAULT 1.0,
    language VARCHAR(5) DEFAULT 'fr',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_word (word),
    INDEX idx_sentiment (sentiment),
    INDEX idx_language (language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 9: STATISTIQUES ET LOGS
-- ============================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 10: SESSIONS
-- ============================================
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload LONGTEXT NOT NULL,
    last_activity INT,
    expires_at INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DONNEES INITIALES
-- ============================================

-- Insérer les utilisateurs par défaut
INSERT IGNORE INTO users (id, nom, email, password_hash, role, statut) VALUES
(1, 'Administrateur', 'admin@kernel.com', '$2y$10$YOixf8XeoewI3kwixzK5/.RVLt6L.mPKzb0l4nqYYJ9rE.RsqXtOC', 'admin', 'actif'),
(2, 'Utilisateur Test', 'user@kernel.com', '$2y$10$YOixf8XeoewI3kwixzK5/.RVLt6L.mPKzb0l4nqYYJ9rE.RsqXtOC', 'user', 'actif'),
(3, 'Technicien Support', 'tech@kernel.com', '$2y$10$YOixf8XeoewI3kwixzK5/.RVLt6L.mPKzb0l4nqYYJ9rE.RsqXtOC', 'technicien', 'actif');

-- Insérer les mots-clés de priorité
INSERT IGNORE INTO priority_keywords (keyword, priority_level, frequency) VALUES
-- Mots critiques
('urgent', 'critique', 45),
('critique', 'critique', 38),
('bloqué', 'critique', 32),
('crash', 'critique', 40),
('down', 'critique', 25),
('panne', 'critique', 28),
('grave', 'critique', 22),
('danger', 'critique', 18),
('sécurité', 'critique', 35),
('immédiat', 'critique', 20),

-- Mots haute priorité
('bug', 'haute', 60),
('erreur', 'haute', 55),
('ne marche pas', 'haute', 30),
('impossible', 'haute', 22),
('problème', 'haute', 50),
('défaut', 'haute', 18),
('échec', 'haute', 20),
('broken', 'haute', 15),
('failed', 'haute', 12),
('lent', 'haute', 25),

-- Mots normale priorité
('question', 'normale', 35),
('demande', 'normale', 28),
('information', 'normale', 32),
('suggestion', 'normale', 30),
('amélioration', 'normale', 22),
('idée', 'normale', 18),
('aide', 'normale', 25),
('support', 'normale', 20),
('assistance', 'normale', 15),

-- Mots basse priorité
('félicitation', 'basse', 10),
('remerciement', 'basse', 12),
('compliment', 'basse', 8),
('futur', 'basse', 5),
('optionnel', 'basse', 6);

-- Insérer les mots sentiments
INSERT IGNORE INTO sentiment_words (word, sentiment, weight, language) VALUES
-- Sentiments positifs (français)
('merci', 'positif', 1.5, 'fr'),
('super', 'positif', 1.2, 'fr'),
('bien', 'positif', 1.0, 'fr'),
('excellent', 'positif', 1.8, 'fr'),
('bravo', 'positif', 1.5, 'fr'),
('magnifique', 'positif', 1.7, 'fr'),
('génial', 'positif', 1.6, 'fr'),
('parfait', 'positif', 1.5, 'fr'),

-- Sentiments négatifs (français)
('mauvais', 'negatif', -1.5, 'fr'),
('horrible', 'negatif', -2.0, 'fr'),
('terrible', 'negatif', -1.8, 'fr'),
('nul', 'negatif', -1.6, 'fr'),
('catastrophe', 'negatif', -2.2, 'fr'),
('déception', 'negatif', -1.4, 'fr'),
('perte', 'negatif', -1.7, 'fr'),
('destruction', 'negatif', -2.0, 'fr'),

-- Sentiments positifs (anglais)
('good', 'positif', 1.0, 'en'),
('great', 'positif', 1.2, 'en'),
('awesome', 'positif', 1.3, 'en'),
('excellent', 'positif', 1.4, 'en'),
('wonderful', 'positif', 1.5, 'en'),

-- Sentiments négatifs (anglais)
('bad', 'negatif', -1.0, 'en'),
('terrible', 'negatif', -1.5, 'en'),
('awful', 'negatif', -1.6, 'en'),
('horrible', 'negatif', -1.7, 'en'),
('worst', 'negatif', -1.8, 'en');

-- ============================================
-- VUES UTILES
-- ============================================

-- Vue: Réclamations non résolues
CREATE OR REPLACE VIEW v_reclamations_non_resolues AS
SELECT 
    r.id,
    r.titre,
    r.statut,
    r.priorite,
    r.priority_score,
    u.nom as utilisateur,
    u.email,
    DATE_FORMAT(r.date_creation, '%d/%m/%Y %H:%i') as date_creation
FROM reclamations r
LEFT JOIN users u ON r.utilisateur_id = u.id
WHERE r.statut IN ('en-attente', 'en-cours')
ORDER BY r.priority_score DESC, r.date_creation ASC;

-- Vue: Réclamations par priorité
CREATE OR REPLACE VIEW v_reclamations_par_priorite AS
SELECT 
    priorite,
    COUNT(*) as total,
    SUM(CASE WHEN statut = 'en-attente' THEN 1 ELSE 0 END) as en_attente,
    SUM(CASE WHEN statut = 'en-cours' THEN 1 ELSE 0 END) as en_cours,
    SUM(CASE WHEN statut = 'resolue' THEN 1 ELSE 0 END) as resolues,
    AVG(priority_score) as score_moyen
FROM reclamations
GROUP BY priorite;

-- Vue: Utilisateurs avec statistiques
CREATE OR REPLACE VIEW v_utilisateurs_stats AS
SELECT 
    u.id,
    u.nom,
    u.email,
    u.role,
    COUNT(r.id) as total_reclamations,
    SUM(CASE WHEN r.statut = 'resolue' THEN 1 ELSE 0 END) as reclamations_resolues,
    SUM(CASE WHEN r.statut = 'en-attente' THEN 1 ELSE 0 END) as reclamations_en_attente
FROM users u
LEFT JOIN reclamations r ON u.id = r.utilisateur_id
GROUP BY u.id, u.nom, u.email, u.role;

-- ============================================
-- INDICES POUR LES PERFORMANCES
-- ============================================
ALTER TABLE reclamations ADD INDEX idx_priority_date (priority_score, date_creation);
ALTER TABLE reclamations ADD INDEX idx_user_statut (utilisateur_id, statut);
ALTER TABLE notifications_history ADD INDEX idx_user_unread (user_id, is_read, created_at);
ALTER TABLE pieces_jointes ADD INDEX idx_reclamation_date (reclamation_id, date_upload);

-- ============================================
-- PROCEDURES STOCKEES
-- ============================================

-- Procédure pour archiver les anciennes réclamations
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS sp_archive_old_reclamations(IN days INT)
BEGIN
    UPDATE reclamations 
    SET statut = 'fermee' 
    WHERE statut = 'resolue' 
    AND DATE_ADD(date_fermeture, INTERVAL days DAY) < NOW();
END //
DELIMITER ;

-- Procédure pour mettre à jour les scores de priorité
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS sp_update_priority_scores()
BEGIN
    UPDATE reclamations r
    INNER JOIN priority_analyses pa ON r.id = pa.reclamation_id
    SET r.priority_score = pa.score,
        r.priority_reason = pa.reason
    WHERE pa.created_at = (
        SELECT MAX(created_at) 
        FROM priority_analyses 
        WHERE reclamation_id = r.id
    );
END //
DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger pour mettre à jour la date de modification
DELIMITER //
CREATE TRIGGER IF NOT EXISTS tr_reclamations_update
BEFORE UPDATE ON reclamations
FOR EACH ROW
BEGIN
    SET NEW.date_modification = NOW();
END //
DELIMITER ;

-- Trigger pour archiver les notifications lues
DELIMITER //
CREATE TRIGGER IF NOT EXISTS tr_notifications_archive
AFTER UPDATE ON notifications_history
FOR EACH ROW
BEGIN
    IF NEW.is_read = TRUE THEN
        UPDATE notifications_history 
        SET read_at = NOW() 
        WHERE id = NEW.id AND read_at IS NULL;
    END IF;
END //
DELIMITER ;

-- ============================================
-- FIN DU SCHEMA
-- ============================================
-- Moteur: InnoDB
-- Charset: utf8mb4 (support des émojis et caractères spéciaux)
-- Collation: utf8mb4_unicode_ci (sensibilité à la casse)
-- Date: 2025-12-10
-- ============================================
