<?php
/**
 * Database Configuration Class
 * Gestion de la connexion à la base de données avec PDO (PHP Data Objects)
 * 
 * Cette classe utilise le pattern Singleton pour garantir une seule instance
 * de connexion à la base de données dans toute l'application.
 * 
 * @uses PDO pour toutes les opérations de base de données
 * @uses PDOException pour la gestion des erreurs
 */
class Database {
    /**
     * Instance unique de la classe (Singleton)
     * 
     * @var Database|null
     */
    private static $instance = null;
    
    /**
     * Connexion PDO à la base de données
     * 
     * @var PDO|null
     */
    private $connection = null;
    
    // Configuration de la base de données
    private $host = 'localhost';
    private $dbname = 'kernel';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    /**
     * Constructeur privé pour empêcher l'instanciation directe (Singleton)
     * 
     * @throws PDOException Si la connexion échoue
     */
    private function __construct() {
        try {
            // Construction du DSN (Data Source Name) pour PDO
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            
            // Options PDO pour une connexion sécurisée et optimale
            $options = [
                // Mode d'erreur : lever des exceptions en cas d'erreur
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                
                // Mode de récupération par défaut : tableaux associatifs
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                
                // Désactiver l'émulation des prepared statements pour plus de sécurité
                PDO::ATTR_EMULATE_PREPARES   => false,
                
                // Activer les requêtes persistantes pour de meilleures performances
                PDO::ATTR_PERSISTENT         => false,
                
                // Désactiver le mode autocommit (pour transactions)
                PDO::ATTR_AUTOCOMMIT         => true,
            ];
            
            // Création de la connexion PDO
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            // Log de l'erreur (en production, utiliser un système de logging)
            error_log("Erreur de connexion PDO: " . $e->getMessage());
            
            // Relancer l'exception avec un message personnalisé
            throw new PDOException(
                "Échec de la connexion à la base de données: " . $e->getMessage(),
                (int)$e->getCode()
            );
        }
    }
    
    /**
     * Obtient l'instance unique de la classe (Singleton)
     * 
     * @return Database L'instance unique de la classe Database
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtient la connexion PDO
     * 
     * @return PDO La connexion PDO à la base de données
     * @throws PDOException Si la connexion n'est pas disponible
     */
    public function getConnection(): PDO {
        if ($this->connection === null) {
            throw new PDOException("La connexion à la base de données n'est pas disponible.");
        }
        return $this->connection;
    }
    
    /**
     * Vérifie si la connexion est active
     * 
     * @return bool True si la connexion est active, false sinon
     */
    public function isConnected(): bool {
        try {
            if ($this->connection === null) {
                return false;
            }
            // Test simple de la connexion
            $this->connection->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Démarre une transaction
     * 
     * @return bool True si la transaction a démarré, false sinon
     */
    public function beginTransaction(): bool {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Valide une transaction
     * 
     * @return bool True si la transaction a été validée, false sinon
     */
    public function commit(): bool {
        return $this->connection->commit();
    }
    
    /**
     * Annule une transaction
     * 
     * @return bool True si la transaction a été annulée, false sinon
     */
    public function rollBack(): bool {
        return $this->connection->rollBack();
    }
    
    /**
     * Exécute une requête SQL simple (sans paramètres)
     * 
     * @param string $sql La requête SQL à exécuter
     * @return PDOStatement Le résultat de la requête
     * @throws PDOException Si la requête échoue
     */
    public function query(string $sql): PDOStatement {
        return $this->connection->query($sql);
    }
    
    /**
     * Prépare une requête SQL avec des paramètres
     * 
     * @param string $sql La requête SQL avec des placeholders
     * @return PDOStatement Le statement préparé
     * @throws PDOException Si la préparation échoue
     */
    public function prepare(string $sql): PDOStatement {
        return $this->connection->prepare($sql);
    }
    
    /**
     * Obtient le dernier ID inséré
     * 
     * @return string Le dernier ID inséré
     */
    public function lastInsertId(): string {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Empêche le clonage de l'instance (Singleton)
     */
    private function __clone() {}
    
    /**
     * Empêche la désérialisation de l'instance (Singleton)
     * 
     * @throws Exception Toujours levée pour empêcher la désérialisation
     */
    public function __wakeup(): void {
        throw new Exception("La désérialisation de cette classe n'est pas autorisée.");
    }
    
    /**
     * Ferme la connexion (utile pour les tests ou le nettoyage)
     * 
     * @return void
     */
    public function close(): void {
        $this->connection = null;
        self::$instance = null;
    }
}
?>

