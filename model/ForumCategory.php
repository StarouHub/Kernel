<?php
class ForumCategorie {
    public $id, $name, $created_at;

    public static function all($pdo) {
        $stmt = $pdo->query("SELECT * FROM forum_categories ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function create($pdo, $name) {
        $stmt = $pdo->prepare("INSERT INTO forum_categories (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM forum_categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
