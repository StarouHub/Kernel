<?php
// index.php SIMPLIFIÉ
session_start();

// Définir le rôle selon le choix
if (isset($_GET['role'])) {
    if ($_GET['role'] == 'admin') {
        $_SESSION['role'] = 'admin';
        $_SESSION['nom'] = 'Admin';
        $_SESSION['user_id'] = 1; // ID admin par défaut
        header('Location: view/BackOffice/dashboard2.php');
        exit;
    }
    if ($_GET['role'] == 'user') {
        $_SESSION['role'] = 'user';
        $_SESSION['nom'] = 'Utilisateur';
        $_SESSION['user_id'] = 2; // ID utilisateur par défaut
        header('Location: view/FrontOffice/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Accueil</title>
    <style>
        body { font-family: Arial; padding: 50px; text-align: center; }
        .btn { display: inline-block; padding: 15px 30px; margin: 10px; 
               text-decoration: none; color: white; border-radius: 5px; }
        .user { background: blue; }
        .admin { background: green; }
    </style>
</head>
<body>
    <h1>Kernel Platform</h1>
    <p>Choisissez votre interface :</p>
    
    <a href="?role=user" class="btn user">Utilisateur</a>
    <br>
    <a href="?role=admin" class="btn admin">Administrateur</a>
    
    <p style="margin-top: 50px;">
        <a href="view/FrontOffice/dashboard.php">Lien DIRECT vers dashboard utilisateur</a><br>
        <a href="view/BackOffice/dashboard2.php">Lien DIRECT vers dashboard admin</a>
    </p>
</body>
</html>