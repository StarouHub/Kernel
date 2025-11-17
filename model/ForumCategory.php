<?php
class ForumCategory {
    public $id;
    public $name;

    public function __construct($id = null, $name = null) {
        $this->id = $id;
        $this->name = $name;
    }

    public static function getAll($pdo) {
        $stmt = $pdo->query("SELECT * FROM forum_categories");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function create($pdo, $name) {
        $stmt = $pdo->prepare("INSERT INTO forum_categories (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM forum_categories WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>
