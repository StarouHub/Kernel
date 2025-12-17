<?php
include_once(__DIR__ . '/../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../components/office-switch.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');

$actualiteController = new ActualiteController();
$projetController = new ProjetController();

// Récupérer tous les projets
$projets = $projetController->listProjets();

// Variables
$errors = [];
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données
    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $type = $_POST['type'] ?? '';
    $projet_id = $_POST['projet_id'] ?? '';
    
    // Validation
    if (empty($titre)) {
        $errors['titre'] = 'Le titre est obligatoire';
    }
    
    if (empty($contenu)) {
        $errors['contenu'] = 'Le contenu est obligatoire';
    }
    
    if (empty($type)) {
        $errors['type'] = 'Le type est obligatoire';
    }
    
    if (empty($projet_id)) {
        $errors['projet_id'] = 'Veuillez sélectionner un projet';
    }
    
    // Si pas d'erreurs, ajouter l'actualité
    if (empty($errors)) {
        try {
            $actualite = new Actualite(
                null,
                $titre,
                $contenu,
                new DateTime(),
                $type,
                intval($projet_id)
            );
            
            if ($actualiteController->addActualite($actualite)) {
                $message = '✓ Actualité publiée avec succès !';
                $messageType = 'success';
                header("refresh:2;url=searchActualites.php");
            } else {
                $message = '✗ Erreur lors de la publication.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = '✗ Erreur : ' . $e->getMessage();
            $messageType = 'error';
        }
    } else {
        $message = '✗ Veuillez corriger les erreurs';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Publier une Actualité - Kernel</title>
  
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
    
    .form-container {
      background: white;
      border-radius: 15px;
      padding: 40px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .form-label {
      font-weight: 500;
      color: var(--dark-color);
      margin-bottom: 8px;
    }
    
    .form-control, .form-select {
      padding: 12px 15px;
      border: 2px solid #E5E7EB;
      border-radius: 10px;
      transition: all 0.3s;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .form-control.error, .form-select.error {
      border-color: #EF4444;
    }
    
    .error-message {
      color: #EF4444;
      font-size: 13px;
      margin-top: 5px;
    }
    
    .btn-submit {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 15px 40px;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s;
    }
    
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }
    
    .alert {
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    
    .alert-success {
      background: #D1FAE5;
      color: #065F46;
      border: 1px solid #10B981;
    }
    
    .alert-error {
      background: #FEE2E2;
      color: #991B1B;
      border: 1px solid #EF4444;
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
      <a class="btn btn-light" href="searchActualites.php">
        <i class="bi bi-arrow-left me-2"></i> Retour
      </a>
    </div>
  </header>

  <div class="page-header">
    <div class="container">
      <h1><i class="bi bi-newspaper"></i> Publier une Actualité</h1>
      <p>Tenez vos investisseurs informés de l'évolution de votre projet</p>
    </div>
  </div>

  <div class="container">
    <?php if ($message): ?>
      <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <form class="form-container" method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Projet *</label>
        <select name="projet_id" class="form-select <?php echo isset($errors['projet_id']) ? 'error' : ''; ?>" >
          <option value="">Sélectionner un projet...</option>
          <?php foreach ($projets as $projet): ?>
            <option value="<?php echo $projet['id']; ?>">
              <?php echo htmlspecialchars($projet['titre']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($errors['projet_id'])): ?>
          <div class="error-message"><?php echo $errors['projet_id']; ?></div>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">Titre de l'actualité *</label>
        <input type="text" name="titre" class="form-control <?php echo isset($errors['titre']) ? 'error' : ''; ?>" placeholder="Ex: Lancement de la version Beta" >
        <?php if (isset($errors['titre'])): ?>
          <div class="error-message"><?php echo $errors['titre']; ?></div>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">Type d'actualité *</label>
        <select name="type" class="form-select <?php echo isset($errors['type']) ? 'error' : ''; ?>" >
          <option value="">Sélectionner...</option>
          <option value="milestone">🎯 Milestone (Étape importante)</option>
          <option value="update">📢 Update (Mise à jour)</option>
          <option value="announcement">📣 Announcement (Annonce)</option>
        </select>
        <?php if (isset($errors['type'])): ?>
          <div class="error-message"><?php echo $errors['type']; ?></div>
        <?php endif; ?>
      </div>

      <div class="mb-4">
        <label class="form-label">Contenu *</label>
        <textarea name="contenu" class="form-control <?php echo isset($errors['contenu']) ? 'error' : ''; ?>" rows="8" placeholder="Décrivez l'actualité en détail..." ></textarea>
        <?php if (isset($errors['contenu'])): ?>
          <div class="error-message"><?php echo $errors['contenu']; ?></div>
        <?php endif; ?>
      </div>

      <div class="d-flex gap-3 justify-content-end">
        <a href="searchActualites.php" class="btn btn-secondary">
          <i class="bi bi-x-circle me-2"></i> Annuler
        </a>
        <button type="submit" class="btn-submit">
          <i class="bi bi-send me-2"></i> Publier l'actualité
        </button>
      </div>
    </form>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
