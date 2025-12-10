<?php
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../components/office-switch.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');

$projetController = new ProjetController();
$actualiteController = new ActualiteController();

// Récupérer tous les projets pour le select
$projets = $projetController->listProjets();

// Variables
$actualites = [];
$selectedProjetId = null;
$selectedProjetTitre = '';

// Si un projet est sélectionné, récupérer ses actualités (JOINTURE)
if (isset($_POST['projet_id']) && !empty($_POST['projet_id'])) {
    $selectedProjetId = intval($_POST['projet_id']);
    $actualites = $actualiteController->afficherActualites($selectedProjetId);
    
    // Récupérer le titre du projet sélectionné
    $projetData = $projetController->showProjet($selectedProjetId);
    $selectedProjetTitre = $projetData['titre'] ?? '';
}

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
  <title>Actualités par Projet - Kernel</title>
  
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
    
    .search-box {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      margin-bottom: 30px;
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
    
    .btn-primary {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      padding: 12px 30px;
      border-radius: 10px;
      font-weight: 600;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
    }
  </style>
</head>

<body>
  <?php echo renderOfficeSwitch('front', 'actualite'); ?>
  <?php echo renderChatbotWidget(); ?>
  
  <header class="header d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo">
        <i class="bi bi-hexagon-fill"></i> Kernel
      </a>
      <a class="btn btn-light" href="listeprojet.php">
        <i class="bi bi-arrow-left me-2"></i> Retour aux projets
      </a>
    </div>
  </header>

  <div class="page-header">
    <div class="container">
      <h1><i class="bi bi-newspaper"></i> Actualités par Projet</h1>
      <p>Suivez les dernières nouvelles et mises à jour des projets</p>
    </div>
  </div>

  <div class="container">
    <!-- Formulaire de recherche -->
    <div class="search-box">
      <form method="POST" action="">
        <div class="row align-items-end">
          <div class="col-md-9">
            <label class="form-label fw-bold">Sélectionner un projet</label>
            <select name="projet_id" class="form-select form-select-lg" >
              <option value="">Choisir un projet...</option>
              <?php foreach ($projets as $projet): ?>
                <option value="<?php echo $projet['id']; ?>" <?php echo ($selectedProjetId == $projet['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($projet['titre']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-search me-2"></i> Rechercher
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Résultats -->
    <?php if ($selectedProjetId): ?>
      <div class="mb-4">
        <h3 class="mb-3">
          Actualités du projet : <span class="text-primary"><?php echo htmlspecialchars($selectedProjetTitre); ?></span>
        </h3>
        <p class="text-muted">
          <strong><?php echo count($actualites); ?></strong> actualité<?php echo count($actualites) > 1 ? 's' : ''; ?> trouvée<?php echo count($actualites) > 1 ? 's' : ''; ?>
        </p>
      </div>

      <?php if (empty($actualites)): ?>
        <div class="alert alert-info text-center">
          <i class="bi bi-info-circle me-2"></i>
          Aucune actualité publiée pour ce projet pour le moment.
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
              <?php echo nl2br(htmlspecialchars($actu['contenu'])); ?>
            </div>
            
            <div class="actualite-footer">
              <span>
                <i class="bi bi-calendar me-1"></i>
                <?php echo date('d/m/Y à H:i', strtotime($actu['date_publication'])); ?>
              </span>
              <div class="d-flex gap-2 align-items-center">
                <span>
                  <i class="bi bi-folder me-1"></i>
                  <?php echo htmlspecialchars($actu['projet_titre']); ?>
                </span>
                <a href="modifierActualite.php?id=<?php echo $actu['id']; ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="supprimerActualite.php?id=<?php echo $actu['id']; ?>" class="btn btn-sm btn-outline-danger" title="Supprimer">
                  <i class="bi bi-trash"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php else: ?>
      <div class="text-center py-5">
        <i class="bi bi-search" style="font-size: 64px; color: #E5E7EB;"></i>
        <p class="text-muted mt-3">Sélectionnez un projet pour voir ses actualités</p>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
