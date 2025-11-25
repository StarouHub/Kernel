<?php
session_start();
include_once(__DIR__ . '/../../../controller/categoriecontroller.php');

$id = $_GET['id'] ?? null;

if ($id) {
    $categorieController = new CategorieController();
    try {
        $categorieController->deleteCategorie($id);
        $_SESSION['message'] = 'Catégorie supprimée avec succès';
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        $_SESSION['message'] = 'Erreur lors de la suppression: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }
}

header('Location: listeCategorie.php');
exit;
?>
