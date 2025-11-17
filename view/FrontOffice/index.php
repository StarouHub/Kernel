<?php
// Main front controller for MVC
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/ForumController.php';

// Parse the route (simple example: ?route=admin/categories)
$route = $_GET['route'] ?? '';
$controller = new ForumController($pdo);

switch ($route) {
    case 'admin/categories':
        $categories = $controller->indexCategories();
        include __DIR__ . '/views/backoffice/gestion-categories.html';
        break;
    case 'admin/topics':
        // You may implement passing categories as GET param
        // $topics = $controller->indexTopics($category_id);
        include __DIR__ . '/views/backoffice/gestion-topics.html';
        break;
    case 'admin/replies':
        // $replies = $controller->indexReplies($topic_id);
        include __DIR__ . '/views/backoffice/gestion-replies.html';
        break;
    case 'admin/dashboard':
        include __DIR__ . '/views/backoffice/dashboard-forum.html';
        break;
    default:
        // Default: load the frontoffice forum (or adapt as needed)
        include __DIR__ . '/views/frontoffice/forum.html';
        break;
}
?>
