<?php
class Reponse {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAll() {
        $stmt = $this->db->query("
            SELECT r.*, s.titre as sujet_titre 
            FROM reponses r 
            LEFT JOIN sujets s ON r.sujet_id = s.id 
            ORDER BY r.date DESC
        ");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT r.*, s.titre as sujet_titre 
            FROM reponses r 
            LEFT JOIN sujets s ON r.sujet_id = s.id 
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getBySujet($sujet_id, $sortBy = 'date') {
        // Always order by date in SQL (likes are stored in localStorage, so JavaScript will handle sorting)
        $orderBy = 'r.date ASC';
        
        $stmt = $this->db->prepare("
            SELECT r.*, s.titre as sujet_titre 
            FROM reponses r 
            LEFT JOIN sujets s ON r.sujet_id = s.id 
            WHERE r.sujet_id = ?
            ORDER BY $orderBy
        ");
        $stmt->execute([$sujet_id]);
        $reponses = $stmt->fetchAll();
        
        // Note: When sorting by likes, JavaScript will handle it client-side
        // since likes are stored in localStorage, not in the database
        // PHP just returns responses in date order, and JavaScript re-sorts them
        
        return $reponses;
    }
    
    public function updateLikes($id, $likes) {
        // Try to update likes column if it exists
        try {
            $stmt = $this->db->prepare("UPDATE reponses SET likes = ? WHERE id = ?");
            return $stmt->execute([$likes, $id]);
        } catch (PDOException $e) {
            // Column doesn't exist, return true anyway (we'll use localStorage)
            return true;
        }
    }
    
    public function getLikes($id) {
        try {
            $stmt = $this->db->prepare("SELECT likes FROM reponses WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return $result ? ($result['likes'] ?? 0) : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    public function create($contenu, $sujet_id, $user_id = null) {
        if ($user_id === null) {
            $user_id = $_SESSION['user_id'] ?? 1;
        }
        // Store ownership mapping in session
        if (!isset($_SESSION['reponse_owners'])) {
            $_SESSION['reponse_owners'] = [];
        }
        
        $stmt = $this->db->prepare("INSERT INTO reponses (contenu, sujet_id, date) VALUES (?, ?, NOW())");
        $result = $stmt->execute([$contenu, $sujet_id]);
        
        if ($result) {
            $newId = $this->db->lastInsertId();
            $_SESSION['reponse_owners'][$newId] = $user_id;
        }
        
        return $result;
    }
    
    public function update($id, $contenu) {
        $stmt = $this->db->prepare("UPDATE reponses SET contenu = ? WHERE id = ?");
        return $stmt->execute([$contenu, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM reponses WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function isOwner($reponse_id, $user_id) {
        // Check ownership from session mapping
        if (isset($_SESSION['reponse_owners'][$reponse_id])) {
            return $_SESSION['reponse_owners'][$reponse_id] == $user_id;
        }
        // Fallback: try database column if it exists
        try {
            $stmt = $this->db->prepare("SELECT user_id FROM reponses WHERE id = ?");
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
