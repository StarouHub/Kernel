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

<header class="main-header">
  <div class="d-flex align-items-center gap-4">
    <a href="logout.php" class="btn btn-danger d-flex align-items-center gap-2">
      <i class="bi bi-box-arrow-right"></i> Déconnexion
    </a>
    <a href="home.php" class="logo text-decoration-none">
      <i class="bi bi-hexagon-fill"></i> Kernel
    </a>
  </div>

  <?php session_start(); ?>
  <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
    <a href="../backoffice/admin.php" class="btn-admin">
     <i class="bi bi-person-gear"></i> Administration
    </a>
  <?php endif; ?>
</header>

  <div class="home-content">
    <h1>Bienvenue sur Kernel</h1>
    <p>Votre espace innovateur est prêt.</p>
  </div>

</body>
</html>