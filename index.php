<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/ForumController.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get the route (if any)
$route = $_GET['route'] ?? '';

// --- ROUTING LOGIC ---
switch ($route) {
    case 'ajout-sujet':
        // Handle form submission for 'Nouveau Sujet'
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get data from POST
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $categorie_id = 1; // Use a real category system in production

            // Basic validation (optional: add more)
            if ($titre && $contenu) {
                require_once __DIR__ . '/models/ForumTopic.php';
                // Insert new topic in DB
                ForumTopic::create($pdo, $titre, $contenu, $categorie_id);
                // Redirect to homepage after insertion to prevent form resubmission
                header("Location: ?route=");
                exit;
            }
            // Optionally set up a $form_error message here for missing fields
        }
        include __DIR__ . '/views/FrontOffice/ajout-sujet.html';
        break;

    case 'admin/dashboard':
        include __DIR__ . '/views/BackOffice/dashboard-forum.html';
        break;

    case 'admin/categories':
        include __DIR__ . '/views/BackOffice/gestion-categories.html';
        break;

    case 'admin/topics':
        include __DIR__ . '/views/BackOffice/gestion-topics.html';
        break;

    case 'admin/replies':
        include __DIR__ . '/views/BackOffice/gestion-replies.html';
        break;

    // Add more cases for details, replies, etc...

    default:
        // Forum homepage
        include __DIR__ . '/views/FrontOffice/forum.html';
}
?>
