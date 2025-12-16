<?php
require_once __DIR__.'/../model/ForumCategory.php';
require_once __DIR__.'/../model/ForumTopic.php';
require_once __DIR__.'/../model/ForumReply.php';
// new forumcontroller -> bd
class ForumController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Categories t,+,- 
    public function indexCategories() {
        return ForumCategorie::getAll($this->pdo);
    }
    public function addCategory($name) {
        return ForumCategorie::create($this->pdo, $name);
    }
    public function removeCategory($id) {
        return ForumCategorie::delete($this->pdo, $id);
    }

    // Topics
    public function indexTopics($category_id) {
        return ForumTopic::getByCategory($this->pdo, $category_id);
    }
    public function addTopic($titre, $contenu, $categorie_id) {
    return ForumTopic::create($this->pdo, $titre, $contenu, $categorie_id);
    }

    public function removeTopic($id) {
        return ForumTopic::delete($this->pdo, $id);
    }

    // Replies
    public function indexReplies($topic_id) {
        return ForumReply::getBySujet($this->pdo, $topic_id);
    }
    
    public function addReply($topic_id, $user_id, $content) {
        return ForumReply::create($this->pdo, $topic_id, $user_id, $content);
    }
    public function removeReply($id) {
        return ForumReply::delete($this->pdo, $id);
    }
}
?>
