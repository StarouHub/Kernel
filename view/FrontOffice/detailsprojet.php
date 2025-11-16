<?php
include '../../controller/projetcontroller.php';
$projetC = new ProjetController();

if(isset($_GET['id'])) {
    $projet = $projetC->showProjet($_GET['id']);
    $categories = $projetC->getProjetCategories($_GET['id']);
} else {
    header('Location: projetsList.php');
    exit();
}

$percentage = $projet['budget_requis'] > 0 
    ? ($projet['budget_actuel'] / $projet['budget_requis']) * 100 
    : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo htmlspecialchars($projet['titre']); ?> - Kernel</title>
  
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
    
    .project-hero {
      background: white;
      padding: 40px 0;
      margin-bottom: 30px;
    }
    
    .project-cover {
      width: 100%;
      height: 400px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 48px;
      font-weight: bold;
    }
    
    .project-title {
      font-size: 36px;
      font-weight: 700;
      color: var(--dark-color);
      margin: 30px 0 15px;
      font-family: 'Raleway', sans-serif;
    }
    
    .content-section {
      background: white;
      border-radius: 15px;
      padding: 30px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .funding-card {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 20px;
      position: sticky;
      top: 100px;
    }
    
    .funding-amount {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 10px;
    }
    
    .progress-bar-custom {
      height: 15px;
      background: rgba(255,255,255,0.3);
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 15px;
    }
    
    .progress-fill {
      height: 100%;
      background: var(--accent-color);
      border-radius: 10px;
      transition: width 1s ease;
    }
    
    .btn-invest {
      background: var(--accent-color);
      color: white;
      padding: 15px;
      border-radius: 10px;
      border: none;
      font-weight: 600;
      width: 100%;
      font-size: 16px;
      margin-bottom: 10px;
      transition: all 0.3s;
    }
    
    .btn-invest:hover {
      background: #E68A00;
      transform: translateY(-2px);
    }
  </style>
</head>

<body>
  <header class="header d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo">
        <i class="bi bi-hexagon-fill"></i> Kernel
      </a>

      <nav class="navmenu">
        <ul>
          <li><a href="index.html">Accueil</a></li>
          <li><a href="projetsList.php" style="color: var(--accent-color);">Projets</a></li>
          <li><a href="evenements-list.html">Événements</a></li>
          <li><a href="forum.html">Forum</a></li>
          <li><a href="profil-utilisateur.html">Profil</a></li>
        </ul>
      </nav>

      <a class="btn-getstarted" href="login.html">Connexion</a>
    </div>
  </header>

  <div class="project-hero">
    <div class="container">
      <div class="project-cover">
        <?php echo substr(htmlspecialchars($projet['titre']), 0, 20); ?>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1 class="project-title"><?php echo htmlspecialchars($projet['titre']); ?></h1>
        
        <div class="project-meta mb-4">
          <span class="badge bg-<?php 
            echo $projet['statut'] == 'en_cours' ? 'warning' : 
                 ($projet['statut'] == 'termine' ? 'success' : 'danger'); 
          ?>">
            <?php 
              echo $projet['statut'] == 'en_cours' ? 'En cours' : 
                   ($projet['statut'] == 'termine' ? 'Terminé' : 'Annulé'); 
            ?>
          </span>
          <span class="text-muted ms-3">
            <i class="bi bi-calendar"></i> 
            Publié le <?php echo date('d/m/Y', strtotime($projet['date_creation'])); ?>
          </span>
        </div>

        <?php if(!empty($categories)): ?>
        <div class="mb-3">
          <?php foreach($categories as $cat): ?>
            <span class="badge bg-primary me-2"><?php echo htmlspecialchars($cat['nom']); ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="content-section">
          <h3><i class="bi bi-file-text"></i> Description du Projet</h3>
          <p><?php echo nl2br(htmlspecialchars($projet['description'])); ?></p>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="funding-card">
          <div class="funding-amount"><?php echo number_format($projet['budget_actuel'], 0); ?> TND</div>
          <div class="funding-goal">sur <?php echo number_format($projet['budget_requis'], 0); ?> TND</div>
          
          <div class="progress-bar-custom">
            <div class="progress-fill" style="width: <?php echo min($percentage, 100); ?>%;"></div>
          </div>
          
          <div class="funding-stats mb-4">
            <div class="d-flex justify-content-between text-white">
              <div class="text-center">
                <div class="fs-4 fw-bold"><?php echo round($percentage); ?>%</div>
                <small>Financé</small>
              </div>
              <div class="text-center">
                <div class="fs-4 fw-bold">
                  <?php echo round(($projet['budget_actuel'] / 500)); ?>
                </div>
                <small>Investisseurs</small>
              </div>
            </div>
          </div>
          
          <button class="btn-invest">
            <i class="bi bi-cash-coin me-2"></i> Investir dans ce projet
          </button>
          <button class="btn btn-light w-100">
            <i class="bi bi-bookmark me-2"></i> Suivre le projet
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script src="details.js"></script>
</body>
</html>