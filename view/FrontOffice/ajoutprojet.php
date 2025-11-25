<?php
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../controller/categoriecontroller.php');

// Initialiser les variables pour conserver les valeurs
$formData = [
    'projectTitle' => '',
    'projectStatus' => '',
    'shortDescription' => '',
    'detailedDescription' => '',
    'category' => '',
    'budget' => '',
    'duration' => ''
];

// Initialiser les erreurs
$errors = [];
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $formData['projectTitle'] = trim($_POST['projectTitle'] ?? '');
    $formData['projectStatus'] = $_POST['projectStatus'] ?? '';
    $formData['shortDescription'] = trim($_POST['shortDescription'] ?? '');
    $formData['detailedDescription'] = trim($_POST['detailedDescription'] ?? '');
    $formData['category'] = $_POST['category'] ?? '';
    $formData['budget'] = $_POST['budget'] ?? '';
    $formData['duration'] = $_POST['duration'] ?? '';
    
    // Validation côté serveur
    
    // 1. Validation du titre
    if (empty($formData['projectTitle'])) {
        $errors['projectTitle'] = 'Le titre du projet est obligatoire';
    } elseif (strlen($formData['projectTitle']) < 5) {
        $errors['projectTitle'] = 'Le titre doit contenir au moins 5 caractères';
    }
    
    // 2. Validation du statut
    if (empty($formData['projectStatus'])) {
        $errors['projectStatus'] = 'Veuillez sélectionner un statut';
    } elseif (!in_array($formData['projectStatus'], ['idee', 'prototype', 'mvp', 'production'])) {
        $errors['projectStatus'] = 'Statut invalide';
    }
    
    // 3. Validation de la description courte
    if (empty($formData['shortDescription'])) {
        $errors['shortDescription'] = 'La description courte est obligatoire';
    } elseif (strlen($formData['shortDescription']) > 150) {
        $errors['shortDescription'] = 'La description ne doit pas dépasser 150 caractères';
    }
    
    // 4. Validation de la description détaillée
    if (empty($formData['detailedDescription'])) {
        $errors['detailedDescription'] = 'La description détaillée est obligatoire';
    } elseif (strlen($formData['detailedDescription']) < 50) {
        $errors['detailedDescription'] = 'La description détaillée doit contenir au moins 50 caractères';
    }
    
    // 5. Validation de la catégorie (DÉSACTIVÉE TEMPORAIREMENT)
    // if (empty($formData['category'])) {
    //     $errors['category'] = 'Veuillez sélectionner une catégorie';
    // }
    
    // 6. Validation du budget (optionnel mais doit être valide si renseigné)
    if (!empty($formData['budget'])) {
        if (!is_numeric($formData['budget']) || floatval($formData['budget']) <= 0) {
            $errors['budget'] = 'Le budget doit être un nombre positif';
        }
    }
    
    // Si pas d'erreurs, enregistrer le projet
    if (empty($errors)) {
        try {
            // Créer l'objet Projet
            $projet = new Projet(
                null,
                $formData['projectTitle'],
                $formData['detailedDescription'],
                !empty($formData['budget']) ? floatval($formData['budget']) : 0,
                0, // budget_actuel
                $formData['projectStatus'],
                new DateTime(),
                1 // user_id temporaire
            );
            
            // Ajouter le projet
            $projetController = new ProjetController();
            $selectedCategories = !empty($formData['category']) ? [$formData['category']] : [];
            $projet_id = $projetController->addProjet($projet, $selectedCategories);
            
            if ($projet_id) {
                $message = '✓ Projet créé avec succès ! Redirection en cours...';
                $messageType = 'success';
                // Rediriger vers la liste après 2 secondes
                header("refresh:2;url=listeprojet.php");
                // Vider les données du formulaire après succès
                $formData = array_fill_keys(array_keys($formData), '');
            } else {
                $message = '✗ Erreur lors de la création du projet.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = '✗ Erreur : ' . $e->getMessage();
            $messageType = 'error';
        }
    } else {
        $message = '✗ Veuillez corriger les erreurs dans le formulaire';
        $messageType = 'error';
    }
}

// Récupérer les catégories pour l'affichage
$categorieController = new CategorieController();
$categories = $categorieController->listCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Ajouter un Projet - Kernel</title>
  
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
    
    .btn-getstarted {
      background: var(--accent-color);
      color: white;
      padding: 10px 25px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
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
    
    .section-title {
      font-size: 20px;
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--primary-color);
      display: flex;
      align-items: center;
      gap: 10px;
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
      display: block;
      color: #EF4444;
      font-size: 13px;
      margin-top: 5px;
      min-height: 18px;
    }

    .success-message {
      color: #10B981;
    }
    
    .category-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .category-card {
      border: 2px solid #E5E7EB;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .category-card:hover {
      border-color: var(--primary-color);
      background: rgba(37, 99, 235, 0.05);
    }
    
    .category-card.selected {
      border-color: var(--primary-color);
      background: var(--primary-color);
      color: white;
    }
    
    .category-card i {
      font-size: 32px;
      margin-bottom: 10px;
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
    
    @media (max-width: 991px) {
      .navmenu { display: none; }
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
          <li><a href="listeprojet.php" style="color: var(--accent-color);">Projets</a></li>
          <li><a href="evenements-list.html">Événements</a></li>
          <li><a href="forum.html">Forum</a></li>
          <li><a href="profil-utilisateur.html">Profil</a></li>
        </ul>
      </nav>

      <a class="btn-getstarted" href="profil-utilisateur.html">Mon Espace</a>
    </div>
  </header>

  <div class="page-header">
    <div class="container">
      <h1><i class="bi bi-lightbulb"></i> Créer un Nouveau Projet</h1>
      <p>Partagez votre innovation avec la communauté Kernel</p>
    </div>
  </div>

  <div class="container">
    <?php if ($message): ?>
      <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <form id="projectForm" class="form-container" method="POST" action="">
      <!-- Section 1: Informations Générales -->
      <div class="section-title">
        <i class="bi bi-info-circle"></i> Informations Générales
      </div>

      <div class="row mb-3">
        <div class="col-md-8">
          <label class="form-label">Titre du Projet *</label>
          <input type="text" name="projectTitle" id="projectTitle" class="form-control <?php echo isset($errors['projectTitle']) ? 'error' : ''; ?>" placeholder="Ex: Assistant IA Intelligent pour PME" value="<?php echo htmlspecialchars($formData['projectTitle']); ?>" required>
          <span id="projectTitle_error" class="error-message"><?php echo $errors['projectTitle'] ?? ''; ?></span>
        </div>
        <div class="col-md-4">
          <label class="form-label">Statut du Projet *</label>
          <select name="projectStatus" id="projectStatus" class="form-select <?php echo isset($errors['projectStatus']) ? 'error' : ''; ?>" required>
            <option value="">Sélectionner...</option>
            <option value="idee" <?php echo $formData['projectStatus'] === 'idee' ? 'selected' : ''; ?>>Idée / Concept</option>
            <option value="prototype" <?php echo $formData['projectStatus'] === 'prototype' ? 'selected' : ''; ?>>Prototype</option>
            <option value="mvp" <?php echo $formData['projectStatus'] === 'mvp' ? 'selected' : ''; ?>>MVP</option>
            <option value="production" <?php echo $formData['projectStatus'] === 'production' ? 'selected' : ''; ?>>En production</option>
          </select>
          <span id="projectStatus_error" class="error-message"><?php echo $errors['projectStatus'] ?? ''; ?></span>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Description Courte *</label>
        <textarea name="shortDescription" id="shortDescription" class="form-control <?php echo isset($errors['shortDescription']) ? 'error' : ''; ?>" rows="2" placeholder="Résumé du projet en une phrase" required><?php echo htmlspecialchars($formData['shortDescription']); ?></textarea>
        <small class="text-muted">150 caractères maximum</small>
        <span id="shortDescription_error" class="error-message"><?php echo $errors['shortDescription'] ?? ''; ?></span>
      </div>

      <div class="mb-4">
        <label class="form-label">Description Détaillée *</label>
        <textarea name="detailedDescription" id="detailedDescription" class="form-control <?php echo isset($errors['detailedDescription']) ? 'error' : ''; ?>" rows="6" placeholder="Décrivez votre projet en détail : problématique, solution, valeur ajoutée..." required><?php echo htmlspecialchars($formData['detailedDescription']); ?></textarea>
        <span id="detailedDescription_error" class="error-message"><?php echo $errors['detailedDescription'] ?? ''; ?></span>
      </div>

      <!-- Section 2: Catégories -->
      <div class="section-title mt-4">
        <i class="bi bi-grid"></i> Catégorie
      </div>

      <label class="form-label">Sélectionnez la catégorie principale (optionnel)</label>
      <input type="hidden" name="category" id="categoryInput" value="<?php echo htmlspecialchars($formData['category']); ?>">
      <div class="category-grid mb-4" id="categoryGrid">
        <?php
        $icons = [
          'ai' => 'bi-robot',
          'iot' => 'bi-cpu',
          'blockchain' => 'bi-diagram-3',
          'web' => 'bi-code-slash',
          'data' => 'bi-database',
          'security' => 'bi-shield-check'
        ];
        $colors = [
          'ai' => '#2563EB',
          'iot' => '#7C3AED',
          'blockchain' => '#F59E0B',
          'web' => '#10B981',
          'data' => '#06B6D4',
          'security' => '#EF4444'
        ];
        
        if ($categories):
          foreach ($categories as $cat):
            $catName = strtolower($cat['nom']);
            $icon = $icons[$catName] ?? 'bi-star';
            $color = $colors[$catName] ?? '#2563EB';
            $isSelected = ($formData['category'] == $cat['id']) ? 'selected' : '';
        ?>
          <div class="category-card <?php echo $isSelected; ?>" data-category="<?php echo $cat['id']; ?>">
            <i class="bi <?php echo $icon; ?>" style="color: <?php echo $color; ?>;"></i>
            <div><?php echo htmlspecialchars($cat['nom']); ?></div>
          </div>
        <?php 
          endforeach;
        endif;
        ?>
      </div>
      <span id="category_error" class="error-message"><?php echo $errors['category'] ?? ''; ?></span>

      <!-- Section 3: Financement -->
      <div class="section-title mt-4">
        <i class="bi bi-cash-coin"></i> Financement et Budget
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Budget Recherché (en TND)</label>
          <input type="number" name="budget" id="budget" class="form-control <?php echo isset($errors['budget']) ? 'error' : ''; ?>" placeholder="Ex: 50000" value="<?php echo htmlspecialchars($formData['budget']); ?>">
          <span id="budget_error" class="error-message"><?php echo $errors['budget'] ?? ''; ?></span>
        </div>
        <div class="col-md-6">
          <label class="form-label">Durée du Projet</label>
          <select name="duration" id="duration" class="form-select">
            <option value="">Sélectionner...</option>
            <option value="1-3" <?php echo $formData['duration'] === '1-3' ? 'selected' : ''; ?>>1-3 mois</option>
            <option value="3-6" <?php echo $formData['duration'] === '3-6' ? 'selected' : ''; ?>>3-6 mois</option>
            <option value="6-12" <?php echo $formData['duration'] === '6-12' ? 'selected' : ''; ?>>6-12 mois</option>
            <option value="12+" <?php echo $formData['duration'] === '12+' ? 'selected' : ''; ?>>Plus de 12 mois</option>
          </select>
          <span id="duration_error" class="error-message"></span>
        </div>
      </div>

      <!-- Buttons -->
      <div class="d-flex gap-3 justify-content-end mt-5">
        <a href="listeprojet.php" class="btn btn-secondary">
          <i class="bi bi-arrow-left me-2"></i> Retour à la liste
        </a>
        <button type="submit" id="submitBtn" class="btn-submit">
          <i class="bi bi-check-circle me-2"></i> Publier le Projet
        </button>
      </div>
    </form>
  </div>

  <script src="script.js"></script>
  <script>
    // Pré-sélectionner la catégorie si elle existe (après soumission avec erreur)
    document.addEventListener('DOMContentLoaded', function() {
      const categoryInput = document.getElementById('categoryInput');
      if (categoryInput && categoryInput.value) {
        selectedCategory = categoryInput.value;
        // La catégorie est déjà marquée comme selected via PHP
      }
      
      // Scroll vers la première erreur si elle existe
      const firstError = document.querySelector('.error');
      if (firstError) {
        setTimeout(function() {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
      }
    });
  </script>
</body>
</html>
