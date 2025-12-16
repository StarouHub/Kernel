<?php
class Sujet {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAll() {
        $stmt = $this->db->query("
            SELECT s.*, c.name as categorie_name,
                   (SELECT COUNT(*) FROM reponses WHERE sujet_id = s.id) as reponse_count
            FROM sujets s 
            LEFT JOIN categories c ON s.categorie_id = c.id 
            ORDER BY s.date_creation DESC
        ");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as categorie_name,
                   (SELECT COUNT(*) FROM reponses WHERE sujet_id = s.id) as reponse_count
            FROM sujets s 
            LEFT JOIN categories c ON s.categorie_id = c.id 
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByCategorie($categorie_id) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as categorie_name,
                   (SELECT COUNT(*) FROM reponses WHERE sujet_id = s.id) as reponse_count
            FROM sujets s 
            LEFT JOIN categories c ON s.categorie_id = c.id 
            WHERE s.categorie_id = ?
            ORDER BY s.date_creation DESC
        ");
        $stmt->execute([$categorie_id]);
        return $stmt->fetchAll();
    }
    
    public function create($titre, $contenu, $categorie_id, $user_id = null) {
        // Store user_id in session for permission tracking
        // Since user_id column may not exist, we'll track ownership via session
        if ($user_id === null) {
            $user_id = $_SESSION['user_id'] ?? 1;
        }
        // Store ownership mapping in session
        if (!isset($_SESSION['sujet_owners'])) {
            $_SESSION['sujet_owners'] = [];
        }
        
        $stmt = $this->db->prepare("INSERT INTO sujets (titre, contenu, categorie_id, date_creation) VALUES (?, ?, ?, NOW())");
        $result = $stmt->execute([$titre, $contenu, $categorie_id]);
        
        if ($result) {
            $newId = $this->db->lastInsertId();
            $_SESSION['sujet_owners'][$newId] = $user_id;
        }
        
        return $result;
    }
    
    public function update($id, $titre, $contenu, $categorie_id) {
        $stmt = $this->db->prepare("UPDATE sujets SET titre = ?, contenu = ?, categorie_id = ? WHERE id = ?");
        return $stmt->execute([$titre, $contenu, $categorie_id, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM sujets WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getCountByCategorie($categorie_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM sujets WHERE categorie_id = ?");
        $stmt->execute([$categorie_id]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    public function isOwner($sujet_id, $user_id) {
        // Check ownership from session mapping
        if (isset($_SESSION['sujet_owners'][$sujet_id])) {
            return $_SESSION['sujet_owners'][$sujet_id] == $user_id;
        }
        // Fallback: try database column if it exists
        try {
            $stmt = $this->db->prepare("SELECT user_id FROM sujets WHERE id = ?");
            $stmt->execute([$sujet_id]);
            $result = $stmt->fetch();
            if ($result && isset($result['user_id'])) {
                return $result['user_id'] == $user_id;
            }
        } catch (PDOException $e) {
            // Column doesn't exist, use session
        }
        return false;
    }
}
?>
