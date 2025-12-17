<?php
session_start();
include_once(__DIR__ . '/../../../controller/actualitecontroller.php');

$actualiteController = new ActualiteController();

// Récupérer l'ID de l'actualité
$actualite_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($actualite_id === 0) {
    $_SESSION['message'] = 'ID d\'actualité invalide';
    $_SESSION['message_type'] = 'danger';
    header('Location: listeActualite.php');
    exit;
}

// Vérifier que l'actualité existe
$actualite = $actualiteController->showActualite($actualite_id);

if (!$actualite) {
    $_SESSION['message'] = 'Actualité introuvable';
    $_SESSION['message_type'] = 'danger';
    header('Location: listeActualite.php');
    exit;
}

// Supprimer l'actualité
try {
    if ($actualiteController->deleteActualite($actualite_id)) {
        $_SESSION['message'] = 'Actualité supprimée avec succès !';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Erreur lors de la suppression de l\'actualité';
        $_SESSION['message_type'] = 'danger';
    }
} catch (Exception $e) {
    $_SESSION['message'] = 'Erreur : ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
}

// Rediriger vers la liste
header('Location: listeActualite.php');
exit;
?>
