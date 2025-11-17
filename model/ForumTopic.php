<?php
class ForumTopic {
    public $id;
    public $title;
    public $category_id;
    public $user_id;
    public $created_at;

    public function __construct($id = null, $title = null, $category_id = null, $user_id = null, $created_at = null) {
        $this->id = $id;
        $this->title = $title;
        $this->category_id = $category_id;
        $this->user_id = $user_id;
        $this->created_at = $created_at;
    }

    public static function getByCategory($pdo, $category_id) {
        $stmt = $pdo->prepare("SELECT * FROM forum_topics WHERE category_id = ?");
        $stmt->execute([$category_id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function create($pdo, $title, $category_id, $user_id) {
        $stmt = $pdo->prepare("INSERT INTO forum_topics (title, category_id, user_id, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$title, $category_id, $user_id]);
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM forum_topics WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>
