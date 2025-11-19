<?php
include_once(__DIR__ . '/../../controller/actualitecontroller.php');

$actualiteController = new ActualiteController();

// Récupérer toutes les actualités avec jointure
$actualites = $actualiteController->listActualites();

// Fonction pour obtenir le badge du type
function getTypeBadge($type) {
    $badges = [
        'milestone' => ['text' => '🎯 Milestone', 'color' => '#10B981'],
        'update' => ['text' => '📢 Update', 'color' => '#2563EB'],
        'announcement' => ['text' => '📣 Annonce', 'color' => '#F59E0B']
    ];
    return $badges[$type] ?? ['text' => '📰 News', 'color' => '#6B7280'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Toutes les Actualités - Kernel</title>
  
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
      padding-bottom: 50px;
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
    
    .actualite-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      transition: all 0.3s;
    }
    
    .actualite-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .actualite-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 15px;
    }
    
    .actualite-title {
      font-size: 20px;
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 5px;
    }
    
    .actualite-badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      color: white;
    }
    
    .actualite-content {
      color: #6B7280;
      line-height: 1.6;
      margin-bottom: 15px;
    }
    
    .actualite-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 15px;
      border-top: 1px solid #E5E7EB;
      font-size: 14px;
      color: #6B7280;
    }
    
    .projet-link {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
    }
    
    .projet-link:hover {
      text-decoration: underline;
    }
    
    .btn-add {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 12px 25px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      display: inline-block;
      transition: all 0.3s;
    }
    
    .btn-add:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
      color: white;
    }
  </style>
</head>

<body>
  <header class="header d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo">
        <i class="bi bi-hexagon-fill"></i> Kernel
      </a>
      <div class="d-flex gap-2">
        <a class="btn btn-light" href="searchActualites.php">
          <i class="bi bi-search me-2"></i> Rechercher par projet
        </a>
        <a class="btn btn-warning" href="ajouterActualite.php">
          <i class="bi bi-plus-circle me-2"></i> Nouvelle actualité
        </a>
      </div>
    </div>
  </header>

  <div class="page-header">
    <div class="container">
      <h1><i class="bi bi-newspaper"></i> Toutes les Actualités</h1>
      <p>Suivez toutes les dernières nouvelles des projets Kernel</p>
    </div>
  </div>

  <div class="container">
    <div class="mb-4">
      <p class="text-muted">
        <strong><?php echo count($actualites); ?></strong> actualité<?php echo count($actualites) > 1 ? 's' : ''; ?> publiée<?php echo count($actualites) > 1 ? 's' : ''; ?>
      </p>
    </div>

    <?php if (empty($actualites)): ?>
      <div class="alert alert-info text-center">
        <i class="bi bi-info-circle me-2"></i>
        Aucune actualité publiée pour le moment. 
        <a href="ajouterActualite.php">Publiez la première actualité !</a>
      </div>
    <?php else: ?>
      <?php foreach ($actualites as $actu): 
        $badge = getTypeBadge($actu['type']);
      ?>
        <div class="actualite-card">
          <div class="actualite-header">
            <div>
              <h3 class="actualite-title"><?php echo htmlspecialchars($actu['titre']); ?></h3>
              <span class="actualite-badge" style="background: <?php echo $badge['color']; ?>;">
                <?php echo $badge['text']; ?>
              </span>
            </div>
          </div>
          
          <div class="actualite-content">
            <?php echo nl2br(htmlspecialchars(substr($actu['contenu'], 0, 300))); ?>
            <?php if (strlen($actu['contenu']) > 300): ?>
              <span class="text-muted">...</span>
            <?php endif; ?>
          </div>
          
          <div class="actualite-footer">
            <span>
              <i class="bi bi-calendar me-1"></i>
              <?php echo date('d/m/Y à H:i', strtotime($actu['date_publication'])); ?>
            </span>
            <a href="detailsprojet.php?id=<?php echo $actu['projet_id']; ?>" class="projet-link">
              <i class="bi bi-folder me-1"></i>
              <?php echo htmlspecialchars($actu['projet_titre']); ?>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
