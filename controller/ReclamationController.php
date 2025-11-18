<?php
require_once '../Model/Reclamation.php';

function getAllReclamations() { return getReclamations(); }
function getMyReclamations($user_id) { return getUserReclamations($user_id); }

function addReclamation($data, $file = null) {
    $image = null;
    if ($file && $file['error'] == 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $image = time() . "." . $ext;
        move_uploaded_file($file['tmp_name'], "../uploads/" . $image);
    }
    return createReclamation($data, $image);
}

function updateReclamationStatus($id, $statut) { return updateStatus($id, $statut); }
function deleteReclamation($id) { return deleteRec($id); }

function addResponse($reclamation_id, $message, $is_admin = false) {
    $user_id = $_SESSION['user_id'];
    $type = $is_admin ? 'admin' : 'user';
    return createResponse($reclamation_id, $user_id, $message, $type);
}
?>