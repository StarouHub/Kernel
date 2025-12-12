<?php
class Reponse {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAll() {
        $stmt = $this->db->query("
            SELECT r.*, s.titre as sujet_titre 
            FROM reponse r 
            LEFT JOIN sujet s ON r.sujet_id = s.id 
            ORDER BY r.date DESC
        ");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT r.*, s.titre as sujet_titre 
            FROM reponse r 
            LEFT JOIN sujet s ON r.sujet_id = s.id 
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getBySujet($sujet_id) {
        $stmt = $this->db->prepare("
            SELECT r.*, s.titre as sujet_titre 
            FROM reponse r 
            LEFT JOIN sujet s ON r.sujet_id = s.id 
            WHERE r.sujet_id = ?
            ORDER BY r.date ASC
        ");
        $stmt->execute([$sujet_id]);
        return $stmt->fetchAll();
    }
    
    public function create($contenu, $sujet_id, $user_id = null) {
        if ($user_id === null) {
            $user_id = $_SESSION['user_id'] ?? 1;
        }

        if (!isset($_SESSION['reponse_owners'])) {
            $_SESSION['reponse_owners'] = [];
        }
        
        $stmt = $this->db->prepare("INSERT INTO reponse (contenu, sujet_id, date) VALUES (?, ?, NOW())");
        $result = $stmt->execute([$contenu, $sujet_id]);
        
        if ($result) {
            $newId = $this->db->lastInsertId();
            $_SESSION['reponse_owners'][$newId] = $user_id;
        }
        
        return $result;
    }
    
    public function update($id, $contenu) {
        $stmt = $this->db->prepare("UPDATE reponse SET contenu = ? WHERE id = ?");
        return $stmt->execute([$contenu, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM reponse WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

    public function isOwner($reponse_id, $user_id) {
        // Check ownership from session mapping
        if (isset($_SESSION['reponse_owners'][$reponse_id])) {
            return $_SESSION['reponse_owners'][$reponse_id] == $user_id;
        }
        // Fallback: try database column if it exists
        try {
            $stmt = $this->db->prepare("SELECT user_id FROM reponse WHERE id = ?");
            $stmt->execute([$reponse_id]);
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
