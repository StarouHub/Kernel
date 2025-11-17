<?php
class ForumReply {
    public $id;
    public $topic_id;
    public $user_id;
    public $content;
    public $created_at;

    public function __construct($id = null, $topic_id = null, $user_id = null, $content = null, $created_at = null) {
        $this->id = $id;
        $this->topic_id = $topic_id;
        $this->user_id = $user_id;
        $this->content = $content;
        $this->created_at = $created_at;
    }

    public static function getByTopic($pdo, $topic_id) {
        $stmt = $pdo->prepare("SELECT * FROM forum_replies WHERE topic_id = ?");
        $stmt->execute([$topic_id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function create($pdo, $topic_id, $user_id, $content) {
        $stmt = $pdo->prepare("INSERT INTO forum_replies (topic_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$topic_id, $user_id, $content]);
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM forum_replies WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>
