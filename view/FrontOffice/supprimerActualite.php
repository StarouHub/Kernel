<?php
include_once(__DIR__ . '/../../controller/actualitecontroller.php');

$actualiteController = new ActualiteController();

// Récupérer l'ID de l'actualité
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header('Location: listeActualite.php');
    exit;
}

// Récupérer l'actualité pour afficher les détails
$actualiteData = $actualiteController->showActualite($id);

if (!$actualiteData) {
    header('Location: listeActualite.php');
    exit;
}

// Variables
$message = '';
$messageType = '';

// Traitement de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        try {
            if ($actualiteController->deleteActualite($id)) {
                // Redirection avec message de succès
                header('Location: listeActualite.php?deleted=1');
                exit;
            } else {
                $message = '✗ Erreur lors de la suppression.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = '✗ Erreur : ' . $e->getMessage();
            $messageType = 'error';
        }
    } else {
        // Annulation
        header('Location: listeActualite.php');
        exit;
    }
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

$badge = getTypeBadge($actualiteData['type']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Supprimer l'Actualité - Kernel</title>
  
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #2563EB;
      --secondary-color: #7C3AED;
      --danger-color: #EF4444;
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
      color: var(--danger-color);
      margin-bottom: 10px;
      font-family: 'Raleway', sans-serif;
    }
    
    .delete-container {
      background: white;
      border-radius: 15px;
      padding: 40px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      max-width: 700px;
      margin: 0 auto;
    }
    
    .warning-box {
      background: #FEF2F2;
      border: 2px solid var(--danger-color);
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
    }
    
    .warning-box h3 {
      color: var(--danger-color);
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 10px;
    }
    
    .actualite-preview {
      background: #F9FAFB;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
    }
    
    .actualite-badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      color: white;
      display: inline-block;
      margin-bottom: 10px;
    }
    
    .btn-danger {
      background: var(--danger-color);
      color: white;
      padding: 15px 40px;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s;
    }
    
    .btn-danger:hover {
      background: #DC2626;
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
    }
    
    .alert {
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    
    .alert-error {
      background: #FEE2E2;
      color: #991B1B;
      border: 1px solid #EF4444;
    }
  </style>
</head>

<body>
  <header class="header d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo">
        <i class="bi bi-hexagon-fill"></i> Kernel
      </a>
      <a class="btn btn-light" href="listeActualite.php">
        <i class="bi bi-arrow-left me-2"></i> Retour
      </a>
    </div>
  </header>

  <div class="page-header">
    <div class="container text-center">
      <h1><i class="bi bi-trash"></i> Supprimer l'Actualité</h1>
      <p>Cette action est irréversible</p>
    </div>
  </div>

  <div class="container">
    <?php if ($message): ?>
      <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <div class="delete-container">
      <div class="warning-box">
        <h3><i class="bi bi-exclamation-triangle-fill me-2"></i> Attention !</h3>
        <p class="mb-0">Vous êtes sur le point de supprimer définitivement cette actualité. Cette action ne peut pas être annulée.</p>
      </div>

      <div class="actualite-preview">
        <span class="actualite-badge" style="background: <?php echo $badge['color']; ?>;">
          <?php echo $badge['text']; ?>
        </span>
        <h3 class="mb-3"><?php echo htmlspecialchars($actualiteData['titre']); ?></h3>
        <p class="text-muted mb-2">
          <i class="bi bi-folder me-1"></i>
          <strong>Projet :</strong> <?php echo htmlspecialchars($actualiteData['projet_titre']); ?>
        </p>
        <p class="text-muted mb-2">
          <i class="bi bi-calendar me-1"></i>
          <strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($actualiteData['date_publication'])); ?>
        </p>
        <p class="mt-3"><?php echo nl2br(htmlspecialchars(substr($actualiteData['contenu'], 0, 200))); ?>...</p>
      </div>

      <form method="POST" action="">
        <div class="d-flex gap-3 justify-content-center">
          <button type="submit" name="confirm" value="no" class="btn btn-secondary btn-lg">
            <i class="bi bi-x-circle me-2"></i> Annuler
          </button>
          <button type="submit" name="confirm" value="yes" class="btn-danger btn-lg">
            <i class="bi bi-trash me-2"></i> Confirmer la suppression
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
