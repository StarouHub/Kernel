<?php
include_once(__DIR__ . '/../../controller/projetcontroller.php');

// Récupérer l'ID du projet depuis l'URL
$projet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($projet_id === 0) {
    header('Location: listeprojet.php?error=invalid_id');
    exit;
}

$projetController = new ProjetController();

// Vérifier que le projet existe
$projet = $projetController->showProjet($projet_id);

if (!$projet) {
    header('Location: listeprojet.php?error=not_found');
    exit;
}

// Supprimer le projet
$projetController->deleteProjet($projet_id);

// Rediriger vers la liste avec un message de succès
header('Location: listeprojet.php?success=deleted');
exit;
?>
