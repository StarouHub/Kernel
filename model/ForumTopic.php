<?php
class ForumTopic {
    public $id;
    public $titre;
    public $contenu;
    public $date_creation;
    public $categorie_id;

    // Fetch all topics
    public static function all($pdo) {
        $stmt = $pdo->query("SELECT * FROM sujet ORDER BY date_creation DESC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    // Fetch all topics for a given category
    public static function getByCategorie($pdo, $categorie_id) {
        $stmt = $pdo->prepare("SELECT * FROM sujet WHERE categorie_id = ? ORDER BY date_creation DESC");
        $stmt->execute([$categorie_id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    // Fetch a single topic by its ID
    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM sujet WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchObject(self::class);
    }

    // Create a new topic (sujet)
    public static function create($pdo, $titre, $contenu, $categorie_id) {
        $stmt = $pdo->prepare("INSERT INTO sujet (titre, contenu, date_creation, categorie_id) VALUES (?, ?, NOW(), ?)");
        return $stmt->execute([$titre, $contenu, $categorie_id]);
    }

    // Delete a topic by ID
    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM sujet WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
