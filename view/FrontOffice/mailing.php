<?php
include_once(__DIR__ . '/../../services/MailingService.php');
include_once(__DIR__ . '/../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../components/office-switch.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');

$mailingService = new MailingService();
$actualiteController = new ActualiteController();
$projetController = new ProjetController();

// Récupérer les actualités et projets
$actualites = $actualiteController->listActualites();
$projets = $projetController->listProjets();

$message = '';
$messageType = '';

// Traiter l'envoi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'notify_new') {
        $actualiteId = intval($_POST['actualite_id'] ?? 0);
        $projetId = intval($_POST['projet_id'] ?? 0);
        
        if ($actualiteId && $projetId) {
            $result = $mailingService->notifyNewActualite($actualiteId, $projetId);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
        }
    } elseif ($action === 'notify_update') {
        $actualiteId = intval($_POST['actualite_id'] ?? 0);
        $projetId = intval($_POST['projet_id'] ?? 0);
        
        if ($actualiteId && $projetId) {
            $result = $mailingService->notifyUpdatedActualite($actualiteId, $projetId);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
        }
    } elseif ($action === 'weekly_digest') {
        $result = $mailingService->sendWeeklyDigest();
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Système de Mailing - Kernel</title>
  
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #2563EB;
      --secondary-color: #7C3AED;
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
    
    .card {
      background: white;
      border-radius: 15px;
      padding: 30px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      transition: all 0.3s;
    }
    
    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .card-icon {
      font-size: 48px;
      margin-bottom: 15px;
    }
    
    .card h3 {
      font-size: 22px;
      font-weight: 600;
      margin-bottom: 15px;
      color: var(--dark-color);
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      padding: 12px 30px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
    }
    
    .info-box {
      background: #EFF6FF;
      border-left: 4px solid var(--primary-color);
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    
    .warning-box {
      background: #FEF3C7;
      border-left: 4px solid #F59E0B;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    
    .form-select {
      padding: 12px;
      border: 2px solid #E5E7EB;
      border-radius: 10px;
      margin-bottom: 15px;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
    }
    
    .stat-card h4 {
      font-size: 32px;
      font-weight: 700;
      margin: 10px 0;
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
      <a class="btn btn-light" href="listeActualite.php">
        <i class="bi bi-arrow-left me-2"></i> Retour
      </a>
    </div>
  </header>

  <div class="page-header">
    <div class="container">
      <h1><i class="bi bi-envelope-at"></i> Système de Mailing</h1>
      <p>Notifiez vos utilisateurs des actualités et mises à jour</p>
    </div>
  </div>

  <div class="container">
    <?php if ($message): ?>
      <div class="alert alert-<?php echo $messageType; ?>">
        <i class="bi bi-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <div class="info-box">
      <strong><i class="bi bi-info-circle me-2"></i>Mode Simulation</strong><br>
      Les emails sont enregistrés dans <code>logs/emails_sent.log</code> pour le développement.
      En production, configurez un serveur SMTP pour l'envoi réel.
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <i class="bi bi-newspaper"></i>
        <h4><?php echo count($actualites); ?></h4>
        <p>Actualités</p>
      </div>
      <div class="stat-card">
        <i class="bi bi-folder"></i>
        <h4><?php echo count($projets); ?></h4>
        <p>Projets</p>
      </div>
      <div class="stat-card">
        <i class="bi bi-envelope"></i>
        <h4>
          <?php 
          $logFile = __DIR__ . '/../../logs/emails_sent.log';
          echo file_exists($logFile) ? substr_count(file_get_contents($logFile), 'Date:') : 0;
          ?>
        </h4>
        <p>Emails envoyés</p>
      </div>
    </div>

    <div class="row">
      <!-- Notifier nouvelle actualité -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-icon text-primary">
            <i class="bi bi-bell"></i>
          </div>
          <h3>Notifier Nouvelle Actualité</h3>
          <p>Envoie un email aux abonnés d'un projet pour une nouvelle actualité</p>
          
          <form method="POST" action="">
            <input type="hidden" name="action" value="notify_new">
            
            <label class="form-label">Sélectionner une actualité :</label>
            <select name="actualite_id" class="form-select" required onchange="updateProjetId(this, 'projet_new')">
              <option value="">Choisir...</option>
              <?php foreach ($actualites as $actu): ?>
                <option value="<?php echo $actu['id']; ?>" data-projet="<?php echo $actu['projet_id']; ?>">
                  <?php echo htmlspecialchars($actu['titre']); ?> - <?php echo htmlspecialchars($actu['projet_titre']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            
            <input type="hidden" name="projet_id" id="projet_new">
            
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-send me-2"></i> Envoyer les notifications
            </button>
          </form>
        </div>
      </div>

      <!-- Notifier actualité modifiée -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-icon text-warning">
            <i class="bi bi-arrow-repeat"></i>
          </div>
          <h3>Notifier Mise à Jour</h3>
          <p>Informe les abonnés qu'une actualité a été modifiée</p>
          
          <form method="POST" action="">
            <input type="hidden" name="action" value="notify_update">
            
            <label class="form-label">Sélectionner une actualité :</label>
            <select name="actualite_id" class="form-select" required onchange="updateProjetId(this, 'projet_update')">
              <option value="">Choisir...</option>
              <?php foreach ($actualites as $actu): ?>
                <option value="<?php echo $actu['id']; ?>" data-projet="<?php echo $actu['projet_id']; ?>">
                  <?php echo htmlspecialchars($actu['titre']); ?> - <?php echo htmlspecialchars($actu['projet_titre']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            
            <input type="hidden" name="projet_id" id="projet_update">
            
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-send me-2"></i> Envoyer les notifications
            </button>
          </form>
        </div>
      </div>

      <!-- Digest hebdomadaire -->
      <div class="col-md-12">
        <div class="card">
          <div class="card-icon text-success">
            <i class="bi bi-calendar-week"></i>
          </div>
          <h3>Digest Hebdomadaire</h3>
          <p>Envoie un résumé des actualités de la semaine à tous les utilisateurs actifs</p>
          
          <div class="warning-box">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Cette action enverra un email à tous les utilisateurs de la plateforme.
          </div>
          
          <form method="POST" action="" onsubmit="return confirm('Envoyer le digest à tous les utilisateurs ?');">
            <input type="hidden" name="action" value="weekly_digest">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-send me-2"></i> Envoyer le Digest Hebdomadaire
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Voir les logs -->
    <div class="card">
      <h3><i class="bi bi-file-text me-2"></i>Logs d'envoi</h3>
      <p>Consultez les emails envoyés (mode simulation)</p>
      <a href="../../logs/emails_sent.log" target="_blank" class="btn btn-outline-primary">
        <i class="bi bi-eye me-2"></i> Voir les logs
      </a>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script>
    function updateProjetId(select, targetId) {
      const selectedOption = select.options[select.selectedIndex];
      const projetId = selectedOption.getAttribute('data-projet');
      document.getElementById(targetId).value = projetId;
    }
  </script>
</body>
</html>
