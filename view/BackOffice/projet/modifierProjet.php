<?php
session_start();
include_once(__DIR__ . '/../../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../../controller/categoriecontroller.php');
include_once(__DIR__ . '/../../components/office-switch.php');

$projetController = new ProjetController();
$categorieController = new CategorieController();

// Récupérer l'ID du projet
$projet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($projet_id === 0) {
    $_SESSION['message'] = 'ID de projet invalide';
    $_SESSION['message_type'] = 'danger';
    header('Location: listeProjet.php');
    exit;
}

// Récupérer le projet
$projetData = $projetController->showProjet($projet_id);

if (!$projetData) {
    $_SESSION['message'] = 'Projet introuvable';
    $_SESSION['message_type'] = 'danger';
    header('Location: listeProjet.php');
    exit;
}

// Récupérer les catégories du projet
$projetCategories = $projetController->getProjetCategories($projet_id);
$selectedCategoryId = !empty($projetCategories) ? $projetCategories[0]['id'] : '';

// Récupérer toutes les catégories
$categories = $categorieController->listCategories();

// Variables
$errors = [];
$formData = [
    'titre' => $projetData['titre'],
    'description' => $projetData['description'],
    'budget_requis' => $projetData['budget_requis'],
    'statut' => $projetData['statut'],
    'category' => $selectedCategoryId
];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données
    $formData['titre'] = trim($_POST['titre'] ?? '');
    $formData['description'] = trim($_POST['description'] ?? '');
    $formData['budget_requis'] = $_POST['budget_requis'] ?? '';
    $formData['statut'] = $_POST['statut'] ?? '';
    $formData['category'] = $_POST['category'] ?? '';
    
    // Validation
    if (empty($formData['titre'])) {
        $errors['titre'] = 'Le titre est obligatoire';
    } elseif (strlen($formData['titre']) < 5) {
        $errors['titre'] = 'Le titre doit contenir au moins 5 caractères';
    }
    
    if (empty($formData['description'])) {
        $errors['description'] = 'La description est obligatoire';
    } elseif (strlen($formData['description']) < 50) {
        $errors['description'] = 'La description doit contenir au moins 50 caractères';
    }
    
    if (empty($formData['statut'])) {
        $errors['statut'] = 'Le statut est obligatoire';
    }
    
    if (empty($formData['category'])) {
        $errors['category'] = 'Veuillez sélectionner une catégorie';
    }
    
    if (!empty($formData['budget_requis'])) {
        if (!is_numeric($formData['budget_requis']) || floatval($formData['budget_requis']) <= 0) {
            $errors['budget_requis'] = 'Le budget doit être un nombre positif';
        }
    }
    
    // Si pas d'erreurs, modifier le projet
    if (empty($errors)) {
        try {
            $projet = new Projet(
                null,
                $formData['titre'],
                $formData['description'],
                !empty($formData['budget_requis']) ? floatval($formData['budget_requis']) : 0,
                $projetData['budget_actuel'],
                $formData['statut'],
                new DateTime($projetData['date_creation']),
                $projetData['user_id']
            );
            
            if ($projetController->updateProjet($projet, $projet_id)) {
                // Mettre à jour les catégories
                $db = config::getConnexion();
                $sqlDelete = "DELETE FROM projet_categorie WHERE projet_id = :projet_id";
                $reqDelete = $db->prepare($sqlDelete);
                $reqDelete->execute(['projet_id' => $projet_id]);
                
                if (!empty($formData['category'])) {
                    $projetController->addProjetCategorie($projet_id, $formData['category']);
                }
                
                $_SESSION['message'] = 'Projet modifié avec succès !';
                $_SESSION['message_type'] = 'success';
                header('Location: listeProjet.php');
                exit;
            } else {
                $errors['general'] = 'Erreur lors de la modification';
            }
        } catch (Exception $e) {
            $errors['general'] = 'Erreur : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Projet - Backoffice Kernel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #2563EB;
            --secondary: #7C3AED;
            --accent: #F59E0B;
        }
        
        body {
            background: #F9FAFB;
            overflow-x: hidden;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            min-height: 100vh;
            color: white;
            position: fixed;
            width: 250px;
            padding: 20px;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        
        .sidebar h3 {
            font-weight: 700;
            margin-bottom: 30px;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
            width: calc(100% - 250px);
            overflow-x: hidden;
        }
        
        .page-header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--accent), #DC2626);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            font-weight: 600;
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #EF4444;
        }
        
        .invalid-feedback {
            color: #EF4444;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }
        
        . {
            color: #EF4444;
        }
    </style>
</head>
<body>
    <?php echo renderOfficeSwitch('back', 'projet', $projet_id); ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="sidebar">
                <h3><i class="bi bi-hexagon-fill"></i> KERNEL</h3>
                <p class="text-white-50 small">Backoffice Admin</p>
                <hr style="border-color: rgba(255,255,255,0.2);">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="listeProjet.php">
                            <i class="bi bi-lightbulb me-2"></i> Projets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../actualite/listeActualite.php">
                            <i class="bi bi-newspaper me-2"></i> Actualités
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../categorie/listeCategorie.php">
                            <i class="bi bi-grid me-2"></i> Catégories
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Page Header -->
                <div class="page-header">
                    <h1><i class="bi bi-pencil-square text-warning"></i> Modifier un Projet</h1>
                    <p class="text-muted mb-0">Projet #<?php echo $projet_id; ?></p>
                </div>

                <!-- Error Alert -->
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($errors['general']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-pencil-square me-2"></i> Formulaire de Modification
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="row">
                                <!-- Titre -->
                                <div class="col-md-8 mb-4">
                                    <label class="form-label">
                                        Titre du Projet <span class="">*</span>
                                    </label>
                                    <input type="text" 
                                           name="titre" 
                                           class="form-control <?php echo isset($errors['titre']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($formData['titre']); ?>"
                                           >
                                    <?php if (isset($errors['titre'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['titre']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Statut -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">
                                        Statut <span class="">*</span>
                                    </label>
                                    <select name="statut" 
                                            class="form-select <?php echo isset($errors['statut']) ? 'is-invalid' : ''; ?>" 
                                            >
                                        <option value="">Sélectionner...</option>
                                        <option value="idee" <?php echo ($formData['statut'] == 'idee') ? 'selected' : ''; ?>>💡 Idée</option>
                                        <option value="prototype" <?php echo ($formData['statut'] == 'prototype') ? 'selected' : ''; ?>>🔧 Prototype</option>
                                        <option value="mvp" <?php echo ($formData['statut'] == 'mvp') ? 'selected' : ''; ?>>🚀 MVP</option>
                                        <option value="production" <?php echo ($formData['statut'] == 'production') ? 'selected' : ''; ?>>✓ Production</option>
                                    </select>
                                    <?php if (isset($errors['statut'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['statut']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Description <span class="">*</span>
                                </label>
                                <textarea name="description" 
                                          class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" 
                                          rows="6" 
                                          ><?php echo htmlspecialchars($formData['description']); ?></textarea>
                                <?php if (isset($errors['description'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['description']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <!-- Catégorie -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">
                                        Catégorie <span class="">*</span>
                                    </label>
                                    <select name="category" 
                                            class="form-select <?php echo isset($errors['category']) ? 'is-invalid' : ''; ?>" 
                                            >
                                        <option value="">Sélectionner...</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" 
                                                    <?php echo ($formData['category'] == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['nom']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['category'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['category']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Budget -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">
                                        Budget Recherché (TND)
                                    </label>
                                    <input type="number" 
                                           name="budget_requis" 
                                           class="form-control <?php echo isset($errors['budget_requis']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($formData['budget_requis']); ?>">
                                    <?php if (isset($errors['budget_requis'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['budget_requis']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Info Budget Actuel -->
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Budget actuel :</strong> <?php echo number_format($projetData['budget_actuel'], 0, ',', ' '); ?> TND
                                (non modifiable ici)
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="listeProjet.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i> Mettre à Jour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
