<?php
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../components/main-navigation.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');

$projetController = new ProjetController();

// Récupérer l'ID du projet depuis l'URL
$projet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($projet_id === 0) {
    header('Location: listeprojet.php');
    exit;
}

// Récupérer les détails du projet
$projet = $projetController->showProjet($projet_id);

if (!$projet) {
    header('Location: listeprojet.php');
    exit;
}

// Récupérer les catégories du projet
$categories = $projetController->getProjetCategories($projet_id);

// Fonction pour calculer le pourcentage
function calculatePercentage($actuel, $requis) {
    if ($requis == 0) return 0;
    return min(100, round(($actuel / $requis) * 100));
}

$percentage = calculatePercentage($projet['budget_actuel'], $projet['budget_requis']);

// Fonction pour obtenir le badge du statut
function getStatusLabel($statut) {
    $labels = [
        'idee' => 'Idée / Concept',
        'prototype' => 'Prototype',
        'mvp' => 'MVP',
        'production' => 'En production'
    ];
    return $labels[$statut] ?? 'Non défini';
}
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
    }
    
    .btn-getstarted {
      background: var(--accent-color);
      color: white;
      padding: 10px 25px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
    }
    
    .project-hero {
      background: white;
      padding: 40px 0;
      margin-bottom: 30px;
    }
    
    .project-cover {
      width: 100%;
      height: 400px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 48px;
      font-weight: 700;
      text-align: center;
      padding: 20px;
    }
    
    .project-header {
      margin-top: 30px;
    }
    
    .project-title {
      font-size: 36px;
      font-weight: 700;
      color: var(--dark-color);
      margin-bottom: 15px;
      font-family: 'Raleway', sans-serif;
    }
    
    .project-meta {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 20px;
      color: #6B7280;
    }
    
    .meta-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .meta-item i {
      color: var(--primary-color);
    }
    
    .tag {
      display: inline-block;
      padding: 5px 15px;
      background: var(--light-bg);
      color: var(--primary-color);
      border-radius: 20px;
      font-size: 14px;
      font-weight: 500;
      margin-right: 8px;
      margin-bottom: 8px;
    }
    
    .content-section {
      background: white;
      border-radius: 15px;
      padding: 30px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .section-title {
      font-size: 24px;
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
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
    
    .funding-goal {
      font-size: 18px;
      opacity: 0.9;
      margin-bottom: 20px;
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
    
    .funding-stats {
      display: flex;
      justify-content: space-between;
      margin-bottom: 25px;
      padding-top: 15px;
      border-top: 1px solid rgba(255,255,255,0.2);
    }
    
    .stat {
      text-align: center;
    }
    
    .stat-number {
      font-size: 24px;
      font-weight: 700;
      display: block;
    }
    
    .stat-label {
      font-size: 12px;
      opacity: 0.8;
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
    
    .btn-follow {
      background: white;
      color: var(--primary-color);
      padding: 12px;
      border-radius: 10px;
      border: none;
      font-weight: 600;
      width: 100%;
      transition: all 0.3s;
      cursor: pointer;
    }

    .btn-follow:hover {
      background: var(--primary-color);
      color: white;
      transform: translateY(-2px);
    }

    .btn-follow.following {
      background: var(--primary-color);
      color: white;
    }

    .btn-pdf {
      background: #DC2626;
      color: white;
      padding: 12px 25px;
      border-radius: 10px;
      border: none;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
    }

    .btn-pdf:hover {
      background: #B91C1C;
      color: white;
      transform: translateY(-2px);
    }

    .btn-back {
      background: white;
      color: var(--primary-color);
      padding: 10px 20px;
      border-radius: 10px;
      border: 2px solid var(--primary-color);
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s;
    }

    .btn-back:hover {
      background: var(--primary-color);
      color: white;
    }

    .action-buttons {
      display: flex;
      gap: 10px;
      margin-top: 20px;
      flex-wrap: wrap;
    }

    .btn-edit {
      background: var(--accent-color);
      color: white;
      padding: 12px 25px;
      border-radius: 10px;
      border: none;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
    }

    .btn-edit:hover {
      background: #E68A00;
      color: white;
      transform: translateY(-2px);
    }

    .btn-delete {
      background: #DC2626;
      color: white;
      padding: 12px 25px;
      border-radius: 10px;
      border: none;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
      cursor: pointer;
    }

    .btn-delete:hover {
      background: #B91C1C;
      transform: translateY(-2px);
    }
    
    @media (max-width: 991px) {
      .navmenu { display: none; }
    }
  </style>
</head>

<body>
  <?php echo renderMainNavigation('projets'); ?>
  <?php echo renderChatbotWidget(); ?>

  <div class="project-hero">
    <div class="container">
      <div class="project-cover">
        <?php echo htmlspecialchars($projet['titre']); ?>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <div class="project-header">
          <a href="listeprojet.php" class="btn-back mb-3">
            <i class="bi bi-arrow-left me-2"></i> Retour à la liste
          </a>
          
          <h1 class="project-title"><?php echo htmlspecialchars($projet['titre']); ?></h1>
          
          <div class="project-meta">
            <div class="meta-item">
              <i class="bi bi-calendar"></i>
              <span>Publié le <?php echo date('d M Y', strtotime($projet['date_creation'])); ?></span>
            </div>
            <div class="meta-item">
              <i class="bi bi-tag"></i>
              <span><?php echo getStatusLabel($projet['statut']); ?></span>
            </div>
          </div>
          
          <div class="mb-3">
            <?php foreach ($categories as $cat): ?>
              <span class="tag"><?php echo htmlspecialchars($cat['nom']); ?></span>
            <?php endforeach; ?>
          </div>

          <div class="action-buttons">
            <a href="modifierprojet.php?id=<?php echo $projet['id']; ?>" class="btn-edit">
              <i class="bi bi-pencil-square"></i> Modifier le projet
            </a>
            <button onclick="confirmDelete(<?php echo $projet['id']; ?>)" class="btn-delete">
              <i class="bi bi-trash"></i> Supprimer le projet
            </button>
          </div>
        </div>

        <div class="content-section">
          <div class="section-title">
            <i class="bi bi-file-text"></i> Description du Projet
          </div>
          <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($projet['description']); ?></p>
        </div>

        <?php if ($projet['budget_requis'] > 0): ?>
        <div class="content-section">
          <div class="section-title">
            <i class="bi bi-cash-coin"></i> Informations Financières
          </div>
          <div class="row">
            <div class="col-md-6">
              <h5>Budget Recherché</h5>
              <p class="h3 text-primary"><?php echo number_format($projet['budget_requis'], 0, ',', ' '); ?> TND</p>
            </div>
            <div class="col-md-6">
              <h5>Budget Actuel</h5>
              <p class="h3 text-success"><?php echo number_format($projet['budget_actuel'], 0, ',', ' '); ?> TND</p>
            </div>
          </div>
          <div class="mt-3">
            <div class="progress" style="height: 25px;">
              <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $percentage; ?>%;" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                <?php echo $percentage; ?>%
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-4">
        <?php if ($projet['budget_requis'] > 0): ?>
        <div class="funding-card">
          <div class="funding-amount"><?php echo number_format($projet['budget_actuel'], 0, ',', ' '); ?> TND</div>
          <div class="funding-goal">sur <?php echo number_format($projet['budget_requis'], 0, ',', ' '); ?> TND</div>
          
          <div class="progress-bar-custom">
            <div class="progress-fill" style="width: <?php echo $percentage; ?>%;"></div>
          </div>
          
          <div class="funding-stats">
            <div class="stat">
              <span class="stat-number"><?php echo $percentage; ?>%</span>
              <span class="stat-label">Financé</span>
            </div>
            <div class="stat">
              <span class="stat-number">0</span>
              <span class="stat-label">Investisseurs</span>
            </div>
            <div class="stat">
              <span class="stat-number">--</span>
              <span class="stat-label">Jours restants</span>
            </div>
          </div>
          
          <button class="btn-invest" onclick="investInProject(<?php echo $projet['id']; ?>)">
            <i class="bi bi-cash-coin me-2"></i> Investir dans ce projet
          </button>
          <button class="btn-follow" onclick="followProject(<?php echo $projet['id']; ?>)" id="followBtn">
            <i class="bi bi-bookmark me-2"></i> Suivre le projet
          </button>
        </div>
        <?php endif; ?>

        <div class="content-section">
          <div class="section-title" style="font-size: 18px;">
            <i class="bi bi-info-circle"></i> Informations
          </div>
          <div class="mb-3">
            <strong>Statut:</strong><br>
            <?php echo getStatusLabel($projet['statut']); ?>
          </div>
          <div class="mb-3">
            <strong>Date de création:</strong><br>
            <?php echo date('d/m/Y à H:i', strtotime($projet['date_creation'])); ?>
          </div>
          <div class="mb-3">
            <strong>Catégories:</strong><br>
            <?php 
            if (!empty($categories)) {
              echo implode(', ', array_map(function($cat) {
                return htmlspecialchars($cat['nom']);
              }, $categories));
            } else {
              echo 'Non catégorisé';
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="details.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script>
    function confirmDelete(projetId) {
      if (confirm('Êtes-vous sûr de vouloir supprimer ce projet ? Cette action est irréversible.')) {
        window.location.href = 'supprimerprojet.php?id=' + projetId;
      }
    }

    function followProject(projetId) {
      const btn = document.getElementById('followBtn');
      const isFollowing = btn.classList.contains('following');
      
      // Désactiver le bouton pendant la requête
      btn.disabled = true;
      btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Chargement...';
      
      // Appel AJAX pour sauvegarder en base
      fetch('../../api/follow-project.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          projet_id: projetId,
          action: 'toggle'
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (data.is_following) {
            btn.classList.add('following');
            btn.innerHTML = '<i class="bi bi-bookmark-fill me-2"></i> Projet suivi (' + data.followers_count + ')';
            showNotification('Vous suivez maintenant ce projet !', 'success');
          } else {
            btn.classList.remove('following');
            btn.innerHTML = '<i class="bi bi-bookmark me-2"></i> Suivre le projet';
            showNotification('Vous ne suivez plus ce projet', 'info');
          }
        } else {
          showNotification(data.message || 'Erreur lors du suivi', 'error');
          // Restaurer l'état précédent
          if (isFollowing) {
            btn.classList.add('following');
            btn.innerHTML = '<i class="bi bi-bookmark-fill me-2"></i> Projet suivi';
          } else {
            btn.classList.remove('following');
            btn.innerHTML = '<i class="bi bi-bookmark me-2"></i> Suivre le projet';
          }
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', 'error');
        // Restaurer l'état précédent
        if (isFollowing) {
          btn.classList.add('following');
          btn.innerHTML = '<i class="bi bi-bookmark-fill me-2"></i> Projet suivi';
        } else {
          btn.classList.remove('following');
          btn.innerHTML = '<i class="bi bi-bookmark me-2"></i> Suivre le projet';
        }
      })
      .finally(() => {
        btn.disabled = false;
      });
    }

    function investInProject(projetId) {
      // Rediriger vers la page d'investissement ou ouvrir un modal
      showNotification('Fonctionnalité d\'investissement en cours de développement', 'info');
      // window.location.href = 'investir.php?id=' + projetId;
    }

    function showNotification(message, type) {
      // Créer une notification toast
      const toast = document.createElement('div');
      toast.className = `alert alert-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'warning'} position-fixed`;
      toast.style.cssText = 'top: 100px; right: 20px; z-index: 9999; min-width: 300px;';
      toast.innerHTML = `
        <div class="d-flex align-items-center">
          <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : 'exclamation-triangle'} me-2"></i>
          ${message}
          <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
      `;
      
      document.body.appendChild(toast);
      
      // Supprimer automatiquement après 5 secondes
      setTimeout(() => {
        if (toast.parentElement) {
          toast.remove();
        }
      }, 5000);
    }

    // Vérifier le statut de suivi au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
      // Ici vous pouvez vérifier si l'utilisateur suit déjà ce projet
      // checkFollowStatus(<?php echo $projet['id']; ?>);
    });
  </script>
</body>
</html>
