<?php
// config/database.php
class Database {
    private static $instance = null;
    private $conn;
    private $host = "localhost";
    private $dbname = "kernel_cirine";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false, // Changé à false pour éviter les problèmes de connexion
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::MYSQL_ATTR_FOUND_ROWS => true,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_TIMEOUT => 30
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            // Configurer le fuseau horaire
            $this->conn->exec("SET time_zone = '+01:00'");
            
            // Configurer les options de session SQL
            $this->conn->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
            
        } catch (PDOException $e) {
            // Si la base de données n'existe pas, la créer
            if ($e->getCode() == 1049) { // Unknown database
                $this->createDatabaseIfNotExists();
            } else {
                $this->logError($e);
                // Réessayer après création
                try {
                    $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
                    $this->conn = new PDO($dsn, $this->username, $this->password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]);
                } catch (PDOException $e2) {
                    $this->logError($e2);
                    die("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
                }
            }
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function createDatabaseIfNotExists() {
        try {
            // Se connecter sans base de données spécifique
            $dsn = "mysql:host={$this->host};charset={$this->charset}";
            $tempConn = new PDO($dsn, $this->username, $this->password);
            $tempConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Créer la base de données si elle n'existe pas
            $sql = "CREATE DATABASE IF NOT EXISTS {$this->dbname} 
                    CHARACTER SET utf8mb4 
                    COLLATE utf8mb4_unicode_ci";
            $tempConn->exec($sql);
            
            // Recréer la connexion avec la base de données (sans appeler __construct pour éviter la boucle)
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
            // Configurer le fuseau horaire
            $this->conn->exec("SET time_zone = '+01:00'");
            
            // Créer les tables
            $this->createTables();
            
            error_log("Base de données et tables créées avec succès");
            
        } catch (PDOException $e) {
            $this->logError($e);
            die("Erreur critique: Impossible de créer la base de données. Contactez l'administrateur.");
        }
    }

    public function createTables() {
        try {
            // Lire le fichier SQL
            $sqlFile = __DIR__ . '/../sql/schema.sql';
            
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                
                // Exécuter les commandes SQL
                $this->conn->exec($sql);
                
                error_log("Tables créées avec succès à partir du fichier SQL");
            } else {
                // Créer les tables de base si le fichier SQL n'existe pas
                $this->createBasicTables();
            }
            
            return true;
            
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }

    private function createBasicTables() {
        $tables = [
            // Table users
            "CREATE TABLE IF NOT EXISTS users (
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
                INDEX idx_role (role)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            
            // Table reclamations
            "CREATE TABLE IF NOT EXISTS reclamations (
                id INT PRIMARY KEY AUTO_INCREMENT,
                utilisateur_id INT NOT NULL,
                titre VARCHAR(100) NOT NULL,
                description TEXT NOT NULL,
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
                INDEX idx_assigne (assigne_a)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            
            // Table reponses
            "CREATE TABLE IF NOT EXISTS reponses (
                id INT PRIMARY KEY AUTO_INCREMENT,
                reclamation_id INT NOT NULL,
                utilisateur_id INT NOT NULL,
                message TEXT NOT NULL,
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            
            // Table pieces_jointes
            "CREATE TABLE IF NOT EXISTS pieces_jointes (
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
                INDEX idx_type (type_mime)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            
            // Table notifications_history
            "CREATE TABLE IF NOT EXISTS notifications_history (
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
                INDEX idx_reclamation (reclamation_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        ];
        
        foreach ($tables as $tableSql) {
            $this->conn->exec($tableSql);
        }
        
        // Ajouter les colonnes manquantes si la table existe déjà
        $this->addMissingColumns();
        
        // Insérer les données de base
        $this->insertInitialData();
    }

    private function addMissingColumns() {
        try {
            // Vérifier et ajouter priority_score si nécessaire
            $checkSql = "SHOW COLUMNS FROM reclamations LIKE 'priority_score'";
            $result = $this->conn->query($checkSql);
            if ($result->rowCount() == 0) {
                $this->conn->exec("ALTER TABLE reclamations ADD COLUMN priority_score INT DEFAULT 0 AFTER version_logiciel");
            }
            
            // Vérifier et ajouter priority_reason si nécessaire
            $checkSql = "SHOW COLUMNS FROM reclamations LIKE 'priority_reason'";
            $result = $this->conn->query($checkSql);
            if ($result->rowCount() == 0) {
                $this->conn->exec("ALTER TABLE reclamations ADD COLUMN priority_reason TEXT AFTER priority_score");
            }
            
            // Ajouter 'critique' à l'ENUM priorite si nécessaire
            $checkSql = "SHOW COLUMNS FROM reclamations WHERE Field = 'priorite'";
            $result = $this->conn->query($checkSql);
            $column = $result->fetch();
            if ($column && strpos($column['Type'], 'critique') === false) {
                $this->conn->exec("ALTER TABLE reclamations MODIFY COLUMN priorite ENUM('urgente', 'haute', 'normale', 'basse', 'critique') DEFAULT 'normale'");
            }
        } catch (Exception $e) {
            error_log("Erreur ajout colonnes: " . $e->getMessage());
        }
    }

    private function insertInitialData() {
        try {
            // Vérifier si des utilisateurs existent déjà
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                // Insérer l'utilisateur admin
                $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
                $this->conn->exec("INSERT INTO users (nom, email, password_hash, role) VALUES 
                    ('Administrateur', 'admin@kernel.com', '$passwordHash', 'admin'),
                    ('Utilisateur Test', 'user@kernel.com', '$passwordHash', 'user')");
                
                error_log("Utilisateurs initiaux créés");
            }
            
        } catch (PDOException $e) {
            $this->logError($e);
        }
    }

    public function backupDatabase($backupPath = null) {
        try {
            if ($backupPath === null) {
                $backupPath = __DIR__ . '/../backups/';
            }
            
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0777, true);
            }
            
            $backupFile = $backupPath . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            
            $tables = [];
            $stmt = $this->conn->query("SHOW TABLES");
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            
            $output = "-- Kernel Platform Database Backup\n";
            $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $output .= "-- Database: {$this->dbname}\n\n";
            
            foreach ($tables as $table) {
                $output .= "--\n-- Table structure for table `$table`\n--\n\n";
                $output .= "DROP TABLE IF EXISTS `$table`;\n";
                
                $stmt = $this->conn->query("SHOW CREATE TABLE `$table`");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $output .= $row[1] . ";\n\n";
                
                $output .= "--\n-- Dumping data for table `$table`\n--\n\n";
                
                $stmt = $this->conn->query("SELECT * FROM `$table`");
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);
                
                if (count($rows) > 0) {
                    $columns = [];
                    $columnCount = $stmt->columnCount();
                    for ($i = 0; $i < $columnCount; $i++) {
                        $meta = $stmt->getColumnMeta($i);
                        $columns[] = $meta['name'];
                    }
                    
                    foreach ($rows as $row) {
                        $output .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (";
                        $values = [];
                        foreach ($row as $value) {
                            $values[] = is_null($value) ? 'NULL' : $this->conn->quote($value);
                        }
                        $output .= implode(', ', $values) . ");\n";
                    }
                    $output .= "\n";
                }
            }
            
            file_put_contents($backupFile, $output);
            
            // Compresser le fichier
            if (function_exists('gzcompress')) {
                $compressed = gzcompress($output, 9);
                file_put_contents($backupFile . '.gz', $compressed);
                unlink($backupFile);
                $backupFile .= '.gz';
            }
            
            error_log("Backup créé: $backupFile");
            return $backupFile;
            
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }

    public function optimizeTables() {
        try {
            $tables = [];
            $stmt = $this->conn->query("SHOW TABLES");
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            
            foreach ($tables as $table) {
                $this->conn->exec("OPTIMIZE TABLE `$table`");
            }
            
            error_log("Tables optimisées avec succès");
            return true;
            
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }

    public function getDatabaseSize() {
        try {
            $stmt = $this->conn->query("
                SELECT 
                    table_schema as 'database',
                    SUM(data_length + index_length) as 'size_bytes',
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as 'size_mb'
                FROM information_schema.TABLES
                WHERE table_schema = '{$this->dbname}'
                GROUP BY table_schema
            ");
            
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            return ['size_bytes' => 0, 'size_mb' => 0];
        }
    }

    public function getTableStats() {
        try {
            $stmt = $this->conn->query("
                SELECT 
                    TABLE_NAME as table_name,
                    TABLE_ROWS as row_count,
                    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as size_mb,
                    UPDATE_TIME as last_update
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = '{$this->dbname}'
                ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
            ");
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            return [];
        }
    }

    public function checkConnection() {
        try {
            $this->conn->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ping() {
        return $this->checkConnection();
    }

    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollBack() {
        return $this->conn->rollBack();
    }

    public function lastInsertId($name = null) {
        return $this->conn->lastInsertId($name);
    }

    public function quote($string, $parameter_type = PDO::PARAM_STR) {
        return $this->conn->quote($string, $parameter_type);
    }

    private function logError(PDOException $e) {
        $errorMsg = "[" . date('Y-m-d H:i:s') . "] Database Error: " . $e->getMessage() . "\n";
        $errorMsg .= "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        $errorMsg .= "Code: " . $e->getCode() . "\n";
        
        error_log($errorMsg);
        
        // Écrire aussi dans un fichier log dédié
        $logFile = __DIR__ . '/../logs/database_errors.log';
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
        file_put_contents($logFile, $errorMsg, FILE_APPEND);
    }

    public function getErrorInfo() {
        return $this->conn->errorInfo();
    }

    public function prepare($sql, $options = []) {
        return $this->conn->prepare($sql, $options);
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function exec($sql) {
        return $this->conn->exec($sql);
    }

    // Méthode pour exécuter une requête avec retour des résultats
    public function fetchAll($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError($e);
            return [];
        }
    }

    // Méthode pour exécuter une requête avec retour d'une seule ligne
    public function fetchOne($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            $this->logError($e);
            return null;
        }
    }

    // Méthode pour exécuter une requête d'insertion/mise à jour
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }

    // Méthode pour obtenir le nombre de lignes affectées
    public function rowCount($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->logError($e);
            return 0;
        }
    }

    // Destructeur
    public function __destruct() {
        $this->conn = null;
    }
}

// Fonction utilitaire pour obtenir une instance de PDO
function getPDO() {
    return Database::getInstance()->getConnection();
}

// Fonction utilitaire pour exécuter une transaction
function executeTransaction(callable $callback) {
    $db = Database::getInstance();
    try {
        $db->beginTransaction();
        $result = $callback($db);
        $db->commit();
        return $result;
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

// Vérifier et créer la base de données au chargement
if (php_sapi_name() !== 'cli') {
    try {
        $database = Database::getInstance();
        $database->getConnection(); // Force la connexion
    } catch (Exception $e) {
        // Le constructeur gère déjà les erreurs
    }
}
?>