<?php
require_once __DIR__.'/../models/ForumCategory.php';
require_once __DIR__.'/../models/ForumTopic.php';
require_once __DIR__.'/../models/ForumReply.php';

class ForumController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Categories
    public function indexCategories() {
        return ForumCategory::getAll($this->pdo);
    }
    public function addCategory($name) {
        return ForumCategory::create($this->pdo, $name);
    }
    public function removeCategory($id) {
        return ForumCategory::delete($this->pdo, $id);
    }

    // Topics
    public function indexTopics($category_id) {
        return ForumTopic::getByCategory($this->pdo, $category_id);
    }
    public function addTopic($title, $category_id, $user_id) {
        return ForumTopic::create($this->pdo, $title, $category_id, $user_id);
    }
    public function removeTopic($id) {
        return ForumTopic::delete($this->pdo, $id);
    }

    // Replies
    public function indexReplies($topic_id) {
        return ForumReply::getByTopic($this->pdo, $topic_id);
    }
    public function addReply($topic_id, $user_id, $content) {
        return ForumReply::create($this->pdo, $topic_id, $user_id, $content);
    }
    public function removeReply($id) {
        return ForumReply::delete($this->pdo, $id);
    }
}
?>
