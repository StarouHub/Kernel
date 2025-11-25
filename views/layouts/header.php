<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo isset($pageTitle) ? $pageTitle . ' - Kernel' : 'Kernel'; ?></title>

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    :root {
      --primary-color: #2563EB;
      --secondary-color: #7C3AED;
      --accent-color: #F59E0B;
      --dark-color: #1F2937;
      --light-bg: #F9FAFB;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background: var(--light-bg);
      padding-top: 80px;
    }

    .header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      padding: 15px 0;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .logo {
      font-size: 28px;
      font-weight: 700;
      color: white;
      text-decoration: none;
      font-family: 'Raleway', sans-serif;
    }

    .navmenu ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      gap: 30px;
      align-items: center;
    }

    .navmenu a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s;
    }

    .navmenu a:hover {
      color: var(--accent-color);
    }

    .btn-getstarted {
      background: var(--accent-color);
      color: white;
      padding: 10px 25px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s;
    }

    .alert {
      border-radius: 10px;
      margin-bottom: 20px;
    }

    @media (max-width: 991px) {
      .navmenu { display: none; }
    }
  </style>
</head>
<body>
<header class="header d-flex align-items-center">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="index.php" class="logo">
      <i class="bi bi-hexagon-fill"></i> Kernel
    </a>

    <nav class="navmenu">
      <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="index.php">Projets</a></li>
        <li><a href="index.php" style="color: var(--accent-color);">Événements</a></li>
        <li><a href="index.php">Forum</a></li>
        <li><a href="index.php">Profil</a></li>
      </ul>
    </nav>

    <?php if (isset($_SESSION['user_role'])): ?>
      <div class="d-flex align-items-center gap-3">
        <span style="color: white; font-weight: 500;">
          <i class="bi bi-<?php echo $_SESSION['user_role'] === 'admin' ? 'shield-check' : 'person'; ?>"></i>
          <?php echo $_SESSION['user_role'] === 'admin' ? 'Administrateur' : 'Utilisateur'; ?>
        </span>
        <a class="btn-getstarted" href="index.php?action=logout" style="background: #DC2626;">
          <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
      </div>
    <?php else: ?>
      <a class="btn-getstarted" href="index.php?action=login">Connexion</a>
    <?php endif; ?>
  </div>
</header>

<div class="container mt-3">
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['error']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
</div>

