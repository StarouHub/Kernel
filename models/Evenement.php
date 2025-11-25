<?php
/**
 * Evenement Model
 * Handles all database operations for events
 */

require_once __DIR__ . '/../config/database.php';

class Evenement {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all events
     * @param string $type Optional filter by type
     * @param string $search Optional search term
     * @return array
     */
    public function getAll($type = null, $search = null) {
        $sql = "SELECT * FROM evenements WHERE 1=1";
        $params = [];
        
        if ($type && $type !== 'Tous') {
            $sql .= " AND type = :type";
            $params[':type'] = $type;
        }
        
        if ($search) {
            $sql .= " AND (titre LIKE :search OR description LIKE :search OR lieu LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        $sql .= " ORDER BY date ASC, created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get event by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT * FROM evenements WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Create a new event
     * @param array $data
     * @return int|false Event ID on success, false on failure
     */
    public function create($data) {
        $sql = "INSERT INTO evenements (titre, type, date, lieu, capacite, user_id, description) 
                VALUES (:titre, :type, :date, :lieu, :capacite, :user_id, :description)";
        
        $stmt = $this->db->prepare($sql);
        
        // Convert date format from DD/MM/YYYY to YYYY-MM-DD
        $dateFormatted = $this->formatDate($data['date']);
        
        $result = $stmt->execute([
            ':titre' => $data['titre'],
            ':type' => $data['type'],
            ':date' => $dateFormatted,
            ':lieu' => $data['lieu'],
            ':capacite' => (int)$data['capacite'],
            ':user_id' => (int)$data['user_id'],
            ':description' => $data['description']
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update an existing event
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE evenements 
                SET titre = :titre, type = :type, date = :date, lieu = :lieu, 
                    capacite = :capacite, user_id = :user_id, description = :description
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        // Convert date format from DD/MM/YYYY to YYYY-MM-DD
        $dateFormatted = $this->formatDate($data['date']);
        
        return $stmt->execute([
            ':id' => $id,
            ':titre' => $data['titre'],
            ':type' => $data['type'],
            ':date' => $dateFormatted,
            ':lieu' => $data['lieu'],
            ':capacite' => (int)$data['capacite'],
            ':user_id' => (int)$data['user_id'],
            ':description' => $data['description']
        ]);
    }
    
    /**
     * Delete an event
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM evenements WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Format date from DD/MM/YYYY to YYYY-MM-DD
     * @param string $date
     * @return string
     */
    private function formatDate($date) {
        if (empty($date)) {
            return date('Y-m-d');
        }
        
        // Check if already in YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        
        // Convert from DD/MM/YYYY to YYYY-MM-DD
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        
        return date('Y-m-d');
    }
    
    /**
     * Format date from YYYY-MM-DD to DD/MM/YYYY for display
     * @param string $date
     * @return string
     */
    public static function formatDateForDisplay($date) {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = strtotime($date);
        return date('d/m/Y', $timestamp);
    }
    
    /**
     * Get formatted date with day name
     * @param string $date
     * @return string
     */
    public static function formatDateWithDay($date) {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = strtotime($date);
        $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $months = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        
        $dayName = $days[date('w', $timestamp)];
        $day = date('d', $timestamp);
        $month = $months[(int)date('m', $timestamp)];
        $year = date('Y', $timestamp);
        
        return "$dayName $day $month $year";
    }
}

