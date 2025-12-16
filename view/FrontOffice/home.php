<?php
session_start();
require_once '../../config.php';

// Si pas connecté → retour à connexion
if (!isset($_SESSION['user'])) {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accueil - Kernel</title>
  
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- HEADER FIXE -->
  <header class="main-header">
    <div class="d-flex align-items-center gap-4">
      <a href="logout.php" class="btn btn-danger d-flex align-items-center gap-2">
        Déconnexion
      </a>
      <a href="home.php" class="logo text-decoration-none">
        Kernel
      </a>
    </div>

    <div>
      <!-- BOUTON SELON LE RÔLE -->
      <?php if ($user['role'] === 'admin'): ?>
        <a href="../backoffice/admin.php" class="btn-admin">
          Administration
        </a>
      <?php else: ?>
        <a href="profile.php" class="btn-admin" style="background: linear-gradient(135deg, #10b981, #059669);">
          Mon Profil
        </a>
      <?php endif; ?>
    </div>
  </header>

  <!-- CONTENU ACCUEIL -->
  <div class="home-content text-center text-white">
    <h1>Bienvenue, <?php echo htmlspecialchars($user['prenom']); ?> !</h1>
    <p>Vous êtes connecté en tant que <strong><?php echo $user['role'] === 'admin' ? 'Administrateur' : 'Utilisateur'; ?></strong></p>
    
    <div class="mt-5">
      <?php if ($user['role'] === 'admin'): ?>
        <p class="fs-3">Accédez à la gestion complète du système</p>
      <?php else: ?>
        <p class="fs-3">Consultez et modifiez vos informations personnelles</p>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>