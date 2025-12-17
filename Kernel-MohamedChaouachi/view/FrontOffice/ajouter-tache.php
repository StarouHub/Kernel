<?php
/**
 * 📋 Ajouter une Tâche
 * Formulaire de création de tâche
 */

include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../controller/taskcontroller.php');
include_once(__DIR__ . '/../components/main-navigation.php');

$projetController = new ProjetController();
$taskController = new TaskController();

// Récupérer les projets pour le select
$projets = $projetController->listProjets();

// Paramètres par défaut
$selectedProject = $_GET['project'] ?? null;
$defaultStatus = $_GET['status'] ?? 'a_faire';

// Traitement du formulaire
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'projet_id' => (int)$_POST['projet_id'],
            'titre' => trim($_POST['titre']),
            'description' => trim($_POST['description']),
            'statut' => $_POST['statut'],
            'priorite' => $_POST['priorite'],
            'date_echeance' => !empty($_POST['date_echeance']) ? $_POST['date_echeance'] : null,
            'assignee_id' => !empty($_POST['assignee_id']) ? (int)$_POST['assignee_id'] : null,
            'couleur' => $_POST['couleur'] ?? '#3B82F6',
            'tags' => trim($_POST['tags']),
            'temps_estime' => !empty($_POST['temps_estime']) ? (int)$_POST['temps_estime'] : null,
            'created_by' => 1 // Simulation utilisateur connecté
        ];
        
        $result = $taskController->createTask($data);
        
        if ($result['success']) {
            $message = 'Tâche créée avec succès !';
            $messageType = 'success';
            // Redirection après 2 secondes
            header("refresh:2;url=mes-taches.php?view=kanban&project=" . $data['projet_id']);
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = 'Erreur lors de la création de la tâche';
        $messageType = 'error';
        error_log("Erreur création tâche: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Ajouter une Tâche - Kernel</title>
    
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
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #E5E7EB;
        }
        
        .form-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 10px;
            font-family: 'Raleway', sans-serif;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #1D4ED8;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6B7280;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .priority-options {
            display: flex;
            gap: 15px;
        }
        
        .priority-option {
            flex: 1;
            text-align: center;
        }
        
        .priority-option input[type="radio"] {
            display: none;
        }
        
        .priority-option label {
            display: block;
            padding: 10px;
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .priority-option input[type="radio"]:checked + label {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
        }
        
        .priority-basse label {
            border-color: #10B981;
            color: #10B981;
        }
        
        .priority-moyenne label {
            border-color: #F59E0B;
            color: #F59E0B;
        }
        
        .priority-haute label {
            border-color: #EF4444;
            color: #EF4444;
        }
        
        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .color-picker {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .color-option.selected {
            border-color: var(--dark-color);
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <?php echo renderMainNavigation('profil'); ?>
    
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1 class="form-title"><i class="bi bi-plus-circle"></i> Ajouter une Tâche</h1>
                <p>Créez une nouvelle tâche pour organiser votre travail</p>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                <i class="bi bi-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
                <?php if ($messageType === 'success'): ?>
                <br><small>Redirection en cours...</small>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Projet *</label>
                            <select name="projet_id" class="form-control" required>
                                <option value="">Sélectionner un projet</option>
                                <?php foreach ($projets as $projet): ?>
                                <option value="<?php echo $projet['id']; ?>" <?php echo $selectedProject == $projet['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($projet['titre']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-control">
                                <option value="a_faire" <?php echo $defaultStatus === 'a_faire' ? 'selected' : ''; ?>>À Faire</option>
                                <option value="en_cours" <?php echo $defaultStatus === 'en_cours' ? 'selected' : ''; ?>>En Cours</option>
                                <option value="termine" <?php echo $defaultStatus === 'termine' ? 'selected' : ''; ?>>Terminé</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Titre de la tâche *</label>
                    <input type="text" name="titre" class="form-control" required placeholder="Ex: Développer l'interface utilisateur">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Décrivez la tâche en détail..."></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Priorité</label>
                            <div class="priority-options">
                                <div class="priority-option priority-basse">
                                    <input type="radio" name="priorite" value="basse" id="priorite_basse" checked>
                                    <label for="priorite_basse">Basse</label>
                                </div>
                                <div class="priority-option priority-moyenne">
                                    <input type="radio" name="priorite" value="moyenne" id="priorite_moyenne">
                                    <label for="priorite_moyenne">Moyenne</label>
                                </div>
                                <div class="priority-option priority-haute">
                                    <input type="radio" name="priorite" value="haute" id="priorite_haute">
                                    <label for="priorite_haute">Haute</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Date d'échéance</label>
                            <input type="date" name="date_echeance" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Tags (séparés par des virgules)</label>
                            <input type="text" name="tags" class="form-control" placeholder="Ex: Frontend, React, UI">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Temps estimé (heures)</label>
                            <input type="number" name="temps_estime" class="form-control" min="1" placeholder="Ex: 8">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Couleur de la tâche</label>
                    <div class="color-picker">
                        <div class="color-option selected" style="background: #3B82F6;" data-color="#3B82F6"></div>
                        <div class="color-option" style="background: #EF4444;" data-color="#EF4444"></div>
                        <div class="color-option" style="background: #10B981;" data-color="#10B981"></div>
                        <div class="color-option" style="background: #F59E0B;" data-color="#F59E0B"></div>
                        <div class="color-option" style="background: #8B5CF6;" data-color="#8B5CF6"></div>
                        <div class="color-option" style="background: #EC4899;" data-color="#EC4899"></div>
                    </div>
                    <input type="hidden" name="couleur" value="#3B82F6">
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Créer la tâche
                    </button>
                    <a href="mes-taches.php" class="btn btn-secondary ms-3">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Color picker functionality
        document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Update hidden input
                document.querySelector('input[name="couleur"]').value = this.dataset.color;
            });
        });
    </script>
</body>
</html>