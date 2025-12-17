<?php
class Categorie {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM categorie ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categorie WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($name, $color = '#2563EB') {
        $stmt = $this->db->prepare("INSERT INTO categorie (name, color, created_at) VALUES (?, ?, NOW())");
        return $stmt->execute([$name, $color]);
    }
    
    public function update($id, $name, $color = null) {
        $stmt = $this->db->prepare("UPDATE categorie SET name = ?, color = ? WHERE id = ?");
        return $stmt->execute([$name, $color, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM categorie WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
