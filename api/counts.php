<?php
/**
 * API pour récupérer les compteurs (nombre de projets, actualités, etc.)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include_once(__DIR__ . '/../controller/projetcontroller.php');
include_once(__DIR__ . '/../controller/actualitecontroller.php');

// Vérifier le type demandé
$type = $_GET['type'] ?? '';

try {
    switch ($type) {
        case 'projets':
            $projetController = new ProjetController();
            $projets = $projetController->listProjets();
            echo json_encode([
                'success' => true,
                'count' => count($projets),
                'type' => 'projets'
            ]);
            break;
            
        case 'actualites':
            $actualiteController = new ActualiteController();
            $actualites = $actualiteController->listActualites();
            echo json_encode([
                'success' => true,
                'count' => count($actualites),
                'type' => 'actualites'
            ]);
            break;
            
        case 'all':
            $projetController = new ProjetController();
            $actualiteController = new ActualiteController();
            
            $projets = $projetController->listProjets();
            $actualites = $actualiteController->listActualites();
            
            echo json_encode([
                'success' => true,
                'counts' => [
                    'projets' => count($projets),
                    'actualites' => count($actualites)
                ]
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Type non spécifié ou invalide'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>