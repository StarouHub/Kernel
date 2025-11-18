<?php
require_once '../config.php';

function getReclamations() {
    global $pdo;
    $sql = "SELECT r.*, u.nom FROM reclamations r JOIN users u ON r.user_id = u.id ORDER BY r.date_creation DESC";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getUserReclamations($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM reclamations WHERE user_id = ? ORDER BY date_creation DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createReclamation($data, $image) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO reclamations (user_id, titre, description, type, priorite, image, statut) 
                           VALUES (?, ?, ?, ?, ?, ?, 'en-attente')");
    return $stmt->execute([$data['user_id'], $data['titre'], $data['description'], $data['type'], $data['priorite'], $image]);
}

function updateStatus($id, $statut) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE reclamations SET statut = ? WHERE id = ?");
    return $stmt->execute([$statut, $id]);
}

function deleteRec($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM reclamations WHERE id = ?");
    return $stmt->execute([$id]);
}

function createResponse($reclamation_id, $user_id, $message, $type) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO reponses (reclamation_id, user_id, message, type) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$reclamation_id, $user_id, $message, $type]);
}

function getResponses($reclamation_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT re.*, u.nom FROM reponses re JOIN users u ON re.user_id = u.id WHERE reclamation_id = ? ORDER BY date_envoi");
    $stmt->execute([$reclamation_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>