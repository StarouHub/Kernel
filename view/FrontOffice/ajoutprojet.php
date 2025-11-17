<?php
require_once __DIR__ . '/../../controller/projetcontroller.php';
require_once __DIR__ . '/../../controller/categoriecontroller.php';
require_once __DIR__ . '/../../model/projet.php';

$error = "";
$success = "";
$projetC = new ProjetController();
$categorieC = new CategorieController();
$categories = $categorieC->listCategories();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier les champs obligatoires
    if (!empty($_POST['titre']) && !empty($_POST['description']) && !empty($_POST['statut'])) {
        
        // Vérifier qu'au moins une catégorie est sélectionnée
        $selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : [];
        
        if (empty($selectedCategories)) {
            $error = "Veuillez sélectionner au moins une catégorie";
        } else {
            $projet = new Projet(
                null, // id
                $_POST['titre'],
                $_POST['description'],
                !empty($_POST['budget_requis']) ? (float)$_POST['budget_requis'] : 0,
                0, // budget_actuel initial = 0
                $_POST['statut'],
                new DateTime(),
                1 // user_id (à remplacer par l'ID de session)
            );
            
            // Ajouter le projet
            $projetId = $projetC->addProjet($projet);
            
            if ($projetId) {
                // Ajouter les catégories associées
                foreach ($selectedCategories as $categorieId) {
                    $projetC->addProjetCategorie($projetId, $categorieId);
                }
                
                header('Location: listeprojet.php');
                exit;
            } else {
                $error = "Erreur lors de l'ajout du projet";
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Projet - Kernel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 900px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .category-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .category-card:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .category-card input[type="checkbox"]:checked + label {
            color: #667eea;
            font-weight: 600;
        }
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <main class="container mt-5 mb-5">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0"><i class="bi bi-plus-circle"></i> Ajouter un Nouveau Projet</h2>
            </div>
            <div class="card-body p-4">
                <a href="listeprojet.php" class="btn btn-secondary mb-4">
                    <i class="bi bi-arrow-left"></i> Retour à la liste
                </a>

                <?php if (!empty($error)) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>

                <?php if (!empty($success)) { ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>

                <form action="" method="POST" id="projetForm">
                    
                    <!-- Titre -->
                    <div class="mb-4">
                        <label for="titre" class="form-label">
                            <i class="bi bi-pencil"></i> Titre du Projet *
                        </label>
                        <input type="text" class="form-control form-control-lg" id="titre" name="titre" 
                               placeholder="Ex: Application mobile innovante"
                               value="<?php echo isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : ''; ?>"
                               required>
                        <span id="titre_error" class="text-danger small"></span>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label">
                            <i class="bi bi-file-text"></i> Description *
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="6" 
                                  placeholder="Décrivez votre projet en détail (minimum 20 caractères)..."
                                  required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        <span id="description_error" class="text-danger small"></span>
                    </div>

                    <!-- Budget et Statut -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="budget_requis" class="form-label">
                                <i class="bi bi-currency-dollar"></i> Budget Recherché (TND)
                            </label>
                            <input type="number" class="form-control form-control-lg" id="budget_requis" 
                                   name="budget_requis" step="0.01" min="0" placeholder="0.00"
                                   value="<?php echo isset($_POST['budget_requis']) ? htmlspecialchars($_POST['budget_requis']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="statut" class="form-label">
                                <i class="bi bi-flag"></i> Statut *
                            </label>
                            <select class="form-select form-select-lg" id="statut" name="statut" required>
                                <option value="">Sélectionner un statut...</option>
                                <option value="en_cours" <?php echo (isset($_POST['statut']) && $_POST['statut'] == 'en_cours') ? 'selected' : ''; ?>>
                                    En cours
                                </option>
                                <option value="termine" <?php echo (isset($_POST['statut']) && $_POST['statut'] == 'termine') ? 'selected' : ''; ?>>
                                    Terminé
                                </option>
                                <option value="annule" <?php echo (isset($_POST['statut']) && $_POST['statut'] == 'annule') ? 'selected' : ''; ?>>
                                    Annulé
                                </option>
                            </select>
                            <span id="statut_error" class="text-danger small"></span>
                        </div>
                    </div>

                    <!-- Catégories -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-tag"></i> Catégories * (Sélectionnez au moins une)
                        </label>
                        
                        <?php 
                        if ($categories && $categories->rowCount() > 0) { 
                            $categoriesArray = $categories->fetchAll();
                        ?>
                            <div class="row">
                                <?php foreach($categoriesArray as $cat) { ?>
                                <div class="col-md-6">
                                    <div class="category-card">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="categories[]" 
                                                   value="<?php echo $cat['id']; ?>" 
                                                   id="cat<?php echo $cat['id']; ?>"
                                                   <?php echo (isset($_POST['categories']) && in_array($cat['id'], $_POST['categories'])) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="cat<?php echo $cat['id']; ?>">
                                                <i class="bi <?php echo $cat['icon']; ?> me-2"></i>
                                                <strong><?php echo htmlspecialchars($cat['nom']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($cat['description']); ?></small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                Aucune catégorie disponible. Veuillez d'abord créer des catégories dans la base de données.
                            </div>
                        <?php } ?>
                        
                        <span id="categories_error" class="text-danger small"></span>
                    </div>

                    <!-- Boutons -->
                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle"></i> Ajouter le Projet
                        </button>
                        <a href="listeprojet.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('projetForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            const titre = document.getElementById('titre');
            const description = document.getElementById('description');
            const statut = document.getElementById('statut');
            const categories = document.querySelectorAll('input[name="categories[]"]:checked');
            
            // Effacer les erreurs précédentes
            document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            
            // Validation du titre
            if (titre.value.trim().length < 5) {
                document.getElementById('titre_error').textContent = 'Le titre doit contenir au moins 5 caractères';
                titre.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validation de la description
            if (description.value.trim().length < 20) {
                document.getElementById('description_error').textContent = 'La description doit contenir au moins 20 caractères';
                description.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validation du statut
            if (statut.value === '') {
                document.getElementById('statut_error').textContent = 'Veuillez sélectionner un statut';
                statut.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validation des catégories
            if (categories.length === 0) {
                document.getElementById('categories_error').textContent = 'Veuillez sélectionner au moins une catégorie';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll vers la première erreur
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Animation des cartes de catégories
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', function() {
                const checkbox = this.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
            });
        });
    </script>
</body>
</html>