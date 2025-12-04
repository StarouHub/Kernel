<?php
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../controller/categoriecontroller.php');
include_once(__DIR__ . '/../components/office-switch.php');

$projetController = new ProjetController();
$categorieController = new CategorieController();

// Récupérer tous les projets
$projets = $projetController->listProjets();
$categories = $categorieController->listCategories();

// Fonction pour calculer le pourcentage de financement
function calculatePercentage($actuel, $requis) {
    if ($requis == 0) return 0;
    return min(100, round(($actuel / $requis) * 100));
}

// Fonction pour obtenir le badge du statut
function getStatusBadge($statut) {
    $badges = [
        'idee' => ['text' => '💡 Idée', 'color' => '#F59E0B'],
        'prototype' => ['text' => '🔧 Prototype', 'color' => '#2563EB'],
        'mvp' => ['text' => '🚀 MVP', 'color' => '#7C3AED'],
        'production' => ['text' => '✓ Production', 'color' => '#10B981']
    ];
    return $badges[$statut] ?? ['text' => '⭐ Nouveau', 'color' => '#F59E0B'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Projets Innovants - Kernel</title>
  
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
    
    .page-header {
      background: white;
      padding: 40px 0;
      margin-bottom: 30px;
      border-bottom: 1px solid #E5E7EB;
    }
    
    .page-header h1 {
      font-size: 36px;
      font-weight: 700;
      color: var(--dark-color);
      margin-bottom: 10px;
      font-family: 'Raleway', sans-serif;
    }
    
    .search-bar {
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      margin-bottom: 30px;
    }
    
    .search-input {
      position: relative;
      flex: 1;
    }
    
    .search-input input {
      width: 100%;
      padding: 12px 45px 12px 15px;
      border: 2px solid #E5E7EB;
      border-radius: 10px;
      transition: all 0.3s;
    }
    
    .search-input input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .search-input i {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #6B7280;
    }
    
    .btn-new-project {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 12px 25px;
      border-radius: 10px;
      border: none;
      font-weight: 600;
      white-space: nowrap;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
    }
    
    .btn-new-project:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
      color: white;
    }
    
    .results-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .results-count {
      color: #6B7280;
      font-size: 14px;
    }
    
    .project-card {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      transition: all 0.3s;
      margin-bottom: 20px;
      cursor: pointer;
      text-decoration: none;
      color: inherit;
      display: block;
    }
    
    .project-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .project-image {
      width: 100%;
      height: 220px;
      object-fit: cover;
      position: relative;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
      font-weight: 700;
    }
    
    .project-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: var(--accent-color);
      color: white;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .project-content {
      padding: 25px;
    }
    
    .project-category {
      display: inline-block;
      padding: 4px 12px;
      background: var(--light-bg);
      color: var(--primary-color);
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
      margin-bottom: 12px;
    }
    
    .project-title {
      font-size: 20px;
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 10px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .project-description {
      color: #6B7280;
      font-size: 14px;
      margin-bottom: 15px;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .project-funding {
      margin-bottom: 15px;
    }
    
    .funding-progress {
      height: 8px;
      background: #E5E7EB;
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 8px;
    }
    
    .funding-bar {
      height: 100%;
      background: var(--primary-color);
      border-radius: 10px;
    }
    
    .funding-info {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
      color: #6B7280;
    }
    
    .funding-amount {
      font-weight: 600;
      color: var(--primary-color);
    }
    
    .project-stats {
      display: flex;
      gap: 20px;
    }
    
    .stat-item {
      display: flex;
      align-items: center;
      gap: 5px;
      color: #6B7280;
      font-size: 13px;
    }
    
    .stat-item i {
      color: var(--primary-color);
    }
    
    @media (max-width: 991px) {
      .navmenu { display: none; }
    }
  </style>
</head>

<body>
  <?php echo renderOfficeSwitch('front', 'projet'); ?>
  
  <header class="header d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo">
        <i class="bi bi-hexagon-fill"></i> Kernel
      </a>

      <nav class="navmenu">
        <ul>
          <li><a href="index.html">Accueil</a></li>
          <li><a href="listeprojet.php" style="color: var(--accent-color);">Projets</a></li>
          <li><a href="evenements-list.html">Événements</a></li>
          <li><a href="forum.html">Forum</a></li>
          <li><a href="profil-utilisateur.html">Profil</a></li>
        </ul>
      </nav>

      <a class="btn-getstarted" href="login.html">Connexion</a>
    </div>
  </header>

  <div class="page-header">
    <div class="container">
      <h1><i class="bi bi-lightbulb"></i> Projets Innovants</h1>
      <p>Découvrez et soutenez les projets technologiques de demain</p>
    </div>
  </div>

  <div class="container">
    <div class="search-bar">
      <div class="row align-items-center">
        <div class="col-lg-9 mb-3 mb-lg-0">
          <div class="search-input">
            <input type="text" placeholder="Rechercher un projet, une technologie, un créateur...">
            <i class="bi bi-search"></i>
          </div>
        </div>
        <div class="col-lg-3">
          <a href="ajoutprojet.php" class="btn-new-project w-100">
            <i class="bi bi-plus-circle me-2"></i> Nouveau Projet
          </a>
        </div>
      </div>
    </div>

    <div class="results-header">
      <div class="results-count">
        <strong><?php echo count($projets); ?></strong> projets trouvés
      </div>
    </div>

    <div class="row">
      <?php if (empty($projets)): ?>
        <div class="col-12">
          <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>
            Aucun projet disponible pour le moment. <a href="ajoutprojet.php">Créez le premier projet !</a>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($projets as $projet): 
          $percentage = calculatePercentage($projet['budget_actuel'], $projet['budget_requis']);
          $badge = getStatusBadge($projet['statut']);
          
          // Récupérer les catégories du projet
          $projetCategories = $projetController->getProjetCategories($projet['id']);
          $categoryName = !empty($projetCategories) ? $projetCategories[0]['nom'] : 'Non catégorisé';
        ?>
          <div class="col-md-6 col-lg-4">
            <a href="detailsprojet.php?id=<?php echo $projet['id']; ?>" class="project-card">
              <div style="position: relative;">
                <div class="project-image">
                  <?php echo htmlspecialchars(substr($projet['titre'], 0, 20)); ?>
                </div>
                <span class="project-badge" style="position: absolute; background: <?php echo $badge['color']; ?>;">
                  <?php echo $badge['text']; ?>
                </span>
              </div>
              <div class="project-content">
                <span class="project-category"><?php echo htmlspecialchars($categoryName); ?></span>
                <h3 class="project-title"><?php echo htmlspecialchars($projet['titre']); ?></h3>
                <p class="project-description"><?php echo htmlspecialchars(substr($projet['description'], 0, 150)) . '...'; ?></p>
                
                <?php if ($projet['budget_requis'] > 0): ?>
                <div class="project-funding">
                  <div class="funding-progress">
                    <div class="funding-bar" style="width: <?php echo $percentage; ?>%;"></div>
                  </div>
                  <div class="funding-info">
                    <span class="funding-amount"><?php echo number_format($projet['budget_actuel'], 0, ',', ' '); ?> TND</span>
                    <span>sur <?php echo number_format($projet['budget_requis'], 0, ',', ' '); ?> TND (<?php echo $percentage; ?>%)</span>
                  </div>
                </div>
                <?php endif; ?>
                
                <div class="project-stats">
                  <span class="stat-item">
                    <i class="bi bi-calendar"></i> 
                    <?php echo date('d/m/Y', strtotime($projet['date_creation'])); ?>
                  </span>
                  <span class="stat-item">
                    <i class="bi bi-eye"></i> Voir détails
                  </span>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <script src="liste.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
