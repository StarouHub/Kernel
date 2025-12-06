<?php
session_start();
require_once '../../config.php';  // Pour avoir accès à la BDD
require_once '../../controller/userController.php';  

$controller = new userController();

// 1. Supprime les tokens "remember me" de l'utilisateur connecté
if (isset($_SESSION['user']['id'])) {
    $controller->deleteRememberTokens($_SESSION['user']['id']);
}

// 2. Supprime le cookie côté client
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

// 3. Détruit la session
session_destroy();

// 4. Redirige vers la page de connexion
header('Location: connexion.php');
exit;