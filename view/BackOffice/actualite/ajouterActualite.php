<?php
session_start();
include_once(__DIR__ . '/../../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../components/office-switch.php');

$actualiteController = new ActualiteController();
$projetController = new ProjetController();

// Récupérer tous les projets pour le select
$projets = $projetController->listProjets();

// Variables
$errors = [];
$formData = [
    'titre' => '',
    'contenu' => '',
    'type' => '',
    'projet_id' => ''
];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données
    $formData['titre'] = trim($_POST['titre'] ?? '');
    $formData['contenu'] = trim($_POST['contenu'] ?? '');
    $formData['type'] = $_POST['type'] ?? '';
    $formData['projet_id'] = $_POST['projet_id'] ?? '';
    
    // Validation
    if (empty($formData['titre'])) {
        $errors['titre'] = 'Le titre est obligatoire';
    } elseif (strlen($formData['titre']) < 5) {
        $errors['titre'] = 'Le titre doit contenir au moins 5 caractères';
    }
    
    if (empty($formData['contenu'])) {
        $errors['contenu'] = 'Le contenu est obligatoire';
    } elseif (strlen($formData['contenu']) < 20) {
        $errors['contenu'] = 'Le contenu doit contenir au moins 20 caractères';
    }
    
    if (empty($formData['type'])) {
        $errors['type'] = 'Le type est obligatoire';
    }
    
    if (empty($formData['projet_id'])) {
        $errors['projet_id'] = 'Veuillez sélectionner un projet';
    }
    
    // Si pas d'erreurs, ajouter l'actualité
    if (empty($errors)) {
        try {
            $actualite = new Actualite(
                null,
                $formData['titre'],
                $formData['contenu'],
                new DateTime(),
                $formData['type'],
                intval($formData['projet_id'])
            );
            
            if ($actualiteController->addActualite($actualite)) {
                $_SESSION['message'] = 'Actualité ajoutée avec succès !';
                $_SESSION['message_type'] = 'success';
                header('Location: listeActualite.php');
                exit;
            } else {
                $errors['general'] = 'Erreur lors de l\'ajout de l\'actualité';
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
    <title>Ajouter une Actualité - Backoffice Kernel</title>
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
            background: linear-gradient(135deg, var(--primary), var(--secondary));
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

    </style>
</head>
<body>
    <?php echo renderOfficeSwitch('back', 'actualite'); ?>
    
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
                        <a class="nav-link" href="../projet/listeProjet.php">
                            <i class="bi bi-lightbulb me-2"></i> Projets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="listeActualite.php">
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
                    <h1><i class="bi bi-plus-circle text-primary"></i> Ajouter une Actualité</h1>
                    <p class="text-muted mb-0">Publiez une nouvelle actualité pour un projet</p>
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
                        <i class="bi bi-pencil-square me-2"></i> Formulaire d'Ajout
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="" id="actualiteForm">
                            <!-- Projet -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Projet <span class="">*</span>
                                </label>
                                <select name="projet_id" 
                                        class="form-select <?php echo isset($errors['projet_id']) ? 'is-invalid' : ''; ?>" 
                                        >
                                    <option value="">-- Sélectionner un projet --</option>
                                    <?php foreach ($projets as $projet): ?>
                                        <option value="<?php echo $projet['id']; ?>" 
                                                <?php echo ($formData['projet_id'] == $projet['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($projet['titre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['projet_id'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?php echo $errors['projet_id']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Titre -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Titre <span class="">*</span>
                                </label>
                                <input type="text" 
                                       name="titre" 
                                       class="form-control <?php echo isset($errors['titre']) ? 'is-invalid' : ''; ?>" 
                                       placeholder="Ex: Lancement de la version Beta"
                                       value="<?php echo htmlspecialchars($formData['titre']); ?>"
                                       minlength="5"
                                       >
                                <?php if (isset($errors['titre'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['titre']; ?>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted">Minimum 5 caractères</small>
                            </div>

                            <!-- Type -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Type d'actualité <span class="">*</span>
                                </label>
                                <select name="type" 
                                        class="form-select <?php echo isset($errors['type']) ? 'is-invalid' : ''; ?>" 
                                        >
                                    <option value="">-- Sélectionner un type --</option>
                                    <option value="milestone" <?php echo ($formData['type'] == 'milestone') ? 'selected' : ''; ?>>
                                        🎯 Milestone (Étape importante)
                                    </option>
                                    <option value="update" <?php echo ($formData['type'] == 'update') ? 'selected' : ''; ?>>
                                        📢 Update (Mise à jour)
                                    </option>
                                    <option value="announcement" <?php echo ($formData['type'] == 'announcement') ? 'selected' : ''; ?>>
                                        📣 Announcement (Annonce)
                                    </option>
                                </select>
                                <?php if (isset($errors['type'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['type']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Contenu -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Contenu <span class="">*</span>
                                </label>
                                <textarea name="contenu" 
                                          class="form-control <?php echo isset($errors['contenu']) ? 'is-invalid' : ''; ?>" 
                                          rows="8" 
                                          placeholder="Décrivez l'actualité en détail..."
                                          minlength="20"
                                          ><?php echo htmlspecialchars($formData['contenu']); ?></textarea>
                                <?php if (isset($errors['contenu'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['contenu']; ?>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted">Minimum 20 caractères</small>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="listeActualite.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i> Ajouter l'Actualité
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation JavaScript en temps réel
        document.getElementById('actualiteForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validation titre
            const titre = document.querySelector('[name="titre"]');
            if (titre.value.trim().length < 5) {
                titre.classList.add('is-invalid');
                isValid = false;
            } else {
                titre.classList.remove('is-invalid');
            }
            
            // Validation contenu
            const contenu = document.querySelector('[name="contenu"]');
            if (contenu.value.trim().length < 20) {
                contenu.classList.add('is-invalid');
                isValid = false;
            } else {
                contenu.classList.remove('is-invalid');
            }
            
            // Validation type
            const type = document.querySelector('[name="type"]');
            if (!type.value) {
                type.classList.add('is-invalid');
                isValid = false;
            } else {
                type.classList.remove('is-invalid');
            }
            
            // Validation projet
            const projet = document.querySelector('[name="projet_id"]');
            if (!projet.value) {
                projet.classList.add('is-invalid');
                isValid = false;
            } else {
                projet.classList.remove('is-invalid');
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez corriger les erreurs dans le formulaire');
            }
        });
    </script>
</body>
</html>
