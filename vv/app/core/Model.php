<?php
/**
 * Base Model Class
 * Provides common database operations for all models
 */
class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get all records
     * @param array $conditions Optional WHERE conditions
     * @param string $orderBy Optional ORDER BY clause
     * @return array
     */
    public function getAll($conditions = [], $orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get a single record by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Find records by a specific field
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function findBy($field, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $field = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }
    
    /**
     * Find a single record by a specific field
     * @param string $field
     * @param mixed $value
     * @return array|false
     */
    public function findOneBy($field, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $field = ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }
    
    /**
     * Create a new record
     * @param array $data Associative array of column => value
     * @return int|false Returns the inserted ID on success, false on failure
     */
    public function create($data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $values = array_values($data);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        try {
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($values);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Database error in create: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a record
     * @param int $id
     * @param array $data Associative array of column => value
     * @return bool
     */
    public function update($id, $data) {
        $columns = array_keys($data);
        $set = [];
        foreach ($columns as $column) {
            $set[] = "$column = ?";
        }
        $values = array_values($data);
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Database error in update: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a record
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        try {
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Database error in delete: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count records
     * @param array $conditions Optional WHERE conditions
     * @return int
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)($result['count'] ?? 0);
    }
    
    /**
     * Execute a custom query
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Begin a transaction
     * @return bool
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * @return bool
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Rollback a transaction
     * @return bool
     */
    public function rollback() {
        return $this->db->rollBack();
    }
    
    /**
     * Get the database connection
     * @return PDO
     */
    public function getDB() {
        return $this->db;
    }
}
?>

