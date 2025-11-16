<?php
include '../../controller/projetcontroller.php';
include '../../controller/categoriecontroller.php';
require_once __DIR__ . '/../../model/projet.php';

$error = "";
$projetC = new ProjetController();
$categorieC = new CategorieController();
$categories = $categorieC->listCategories();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier les champs obligatoires
    if (!empty($_POST['titre']) && !empty($_POST['description']) && !empty($_POST['statut'])) {
        
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
        
        // Récupérer les catégories sélectionnées
        $selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : [];
        
        $projetC->addProjet($projet, $selectedCategories);
        header('Location: projetList.php');
        exit;
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
</head>
<body>
    <main class="container mt-5">
        <header class="mb-4">
            <h2><i class="bi bi-plus-circle"></i> Ajouter un Nouveau Projet</h2>
            <a href="projetList.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </header>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <div class="card">
            <div class="card-body">
                <form action="" method="POST" id="projetForm">
                    
                    <!-- Titre -->
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre du Projet *</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                        <span id="titre_error" class="text-danger"></span>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="6" required></textarea>
                        <span id="description_error" class="text-danger"></span>
                    </div>

                    <!-- Budget requis -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="budget_requis" class="form-label">Budget Recherché (TND)</label>
                            <input type="number" class="form-control" id="budget_requis" name="budget_requis" step="0.01" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="statut" class="form-label">Statut *</label>
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="">Sélectionner...</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminé</option>
                                <option value="annule">Annulé</option>
                            </select>
                            <span id="statut_error" class="text-danger"></span>
                        </div>
                    </div>

                    <!-- Catégories (checkboxes) -->
                    <div class="mb-3">
                        <label class="form-label">Catégories *</label>
                        <div class="row">
                            <?php foreach($categories as $cat) { ?>
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" 
                                           value="<?php echo $cat['id']; ?>" id="cat<?php echo $cat['id']; ?>">
                                    <label class="form-check-label" for="cat<?php echo $cat['id']; ?>">
                                        <i class="bi <?php echo $cat['icon']; ?>"></i>
                                        <?php echo $cat['nom']; ?>
                                    </label>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <span id="categories_error" class="text-danger"></span>
                    </div>

                    <!-- Boutons -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Ajouter le Projet
                        </button>
                        <a href="projetList.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation simple
        document.getElementById('projetForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            const titre = document.getElementById('titre');
            const description = document.getElementById('description');
            const statut = document.getElementById('statut');
            const categories = document.querySelectorAll('input[name="categories[]"]:checked');
            
            // Effacer les erreurs précédentes
            document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');
            
            if (titre.value.length < 5) {
                document.getElementById('titre_error').textContent = 'Le titre doit contenir au moins 5 caractères';
                isValid = false;
            }
            
            if (description.value.length < 20) {
                document.getElementById('description_error').textContent = 'La description doit contenir au moins 20 caractères';
                isValid = false;
            }
            
            if (statut.value === '') {
                document.getElementById('statut_error').textContent = 'Veuillez sélectionner un statut';
                isValid = false;
            }
            
            if (categories.length === 0) {
                document.getElementById('categories_error').textContent = 'Veuillez sélectionner au moins une catégorie';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>