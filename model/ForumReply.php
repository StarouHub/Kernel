<?php
class ForumReply {
    public $id, $contenu, $date, $sujet_id;

    public static function getBySujet($pdo, $sujet_id) {
        $stmt = $pdo->prepare("SELECT * FROM reponse WHERE sujet_id = ?");
        $stmt->execute([$sujet_id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function create($pdo, $contenu, $sujet_id) {
        $stmt = $pdo->prepare("INSERT INTO reponse (contenu, date, sujet_id)
                               VALUES (?, NOW(), ?)");
        return $stmt->execute([$contenu, $sujet_id]);
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM reponse WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
