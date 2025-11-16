<?php
include '../../Controller/ProjetController.php';
$projetC = new ProjetController();

// Gestion de la recherche et des filtres
$keyword = isset($_GET['search']) ? $_GET['search'] : '';
$statut = isset($_GET['statut']) ? $_GET['statut'] : '';

if ($keyword) {
    $list = $projetC->searchProjets($keyword);
} elseif ($statut) {
    $list = $projetC->getProjetsByStatut($statut);
} else {
    $list = $projetC->listProjets();
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
    
    .project-card {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      transition: all 0.3s;
      margin-bottom: 20px;
      cursor: pointer;
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
      font-weight: bold;
    }
    
    .project-content {
      padding: 25px;
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

  <div class="page-header">
    <div class="container">
      <h1><i class="bi bi-lightbulb"></i> Projets Innovants</h1>
      <p>Découvrez et soutenez les projets technologiques de demain</p>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-lg-12 mb-4">
        <form method="GET" action="" class="d-flex gap-2">
          <input type="text" name="search" class="form-control" 
                 placeholder="Rechercher un projet..." 
                 value="<?php echo htmlspecialchars($keyword); ?>">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i> Rechercher
          </button>
        </form>
      </div>
    </div>

    <div class="row">
      <?php 
      if (count($list) > 0) {
        foreach($list as $projet) {
          $percentage = $projet['budget_requis'] > 0 
            ? ($projet['budget_actuel'] / $projet['budget_requis']) * 100 
            : 0;
      ?>
      <div class="col-md-6 col-lg-4">
        <div class="project-card" onclick="window.location.href='projetDetails.php?id=<?php echo $projet['id']; ?>'">
          <div class="project-image">
            <?php echo substr(htmlspecialchars($projet['titre']), 0, 30); ?>
          </div>
          <div class="project-content">
            <h3 class="project-title"><?php echo htmlspecialchars($projet['titre']); ?></h3>
            <p class="project-description">
              <?php echo htmlspecialchars(substr($projet['description'], 0, 120)) . '...'; ?>
            </p>
            
            <div class="funding-progress">
              <div class="funding-bar" style="width: <?php echo min($percentage, 100); ?>%;"></div>
            </div>
            <div class="funding-info">
              <span class="funding-amount">
                <?php echo number_format($projet['budget_actuel'], 0); ?> TND
              </span>
              <span>
                sur <?php echo number_format($projet['budget_requis'], 0); ?> TND 
                (<?php echo round($percentage); ?>%)
              </span>
            </div>
            
            <div class="mt-3">
              <span class="badge bg-<?php 
                echo $projet['statut'] == 'en_cours' ? 'warning' : 
                     ($projet['statut'] == 'termine' ? 'success' : 'danger'); 
              ?>">
                <?php 
                  echo $projet['statut'] == 'en_cours' ? 'En cours' : 
                       ($projet['statut'] == 'termine' ? 'Terminé' : 'Annulé'); 
                ?>
              </span>
            </div>
          </div>
        </div>
      </div>
      <?php 
        }
      } else {
        echo '<div class="col-12"><div class="alert alert-info">Aucun projet trouvé.</div></div>';
      }
      ?>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>