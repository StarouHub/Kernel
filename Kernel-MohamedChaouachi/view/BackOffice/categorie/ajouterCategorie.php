<?php
session_start();
include_once(__DIR__ . '/../../../controller/categoriecontroller.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categorieController = new CategorieController();
    
    $nom = $_POST['nom'] ?? '';
    $icon = $_POST['icon'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (empty($nom) || empty($icon)) {
        $_SESSION['message'] = 'Le nom et l\'icône sont obligatoires';
        $_SESSION['message_type'] = 'danger';
    } else {
        $categorieController->addCategorie($nom, $icon, $description);
        $_SESSION['message'] = 'Catégorie ajoutée avec succès';
        $_SESSION['message_type'] = 'success';
        header('Location: listeCategorie.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Catégorie - Backoffice Kernel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #2563EB;
            --secondary: #7C3AED;
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
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #E5E7EB;
            padding: 12px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
        }
        
        .icon-preview {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            margin: 20px 0;
        }
    </style>
</head>
<body>
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
                        <a class="nav-link" href="../actualite/listeActualite.php">
                            <i class="bi bi-newspaper me-2"></i> Actualités
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="listeCategorie.php">
                            <i class="bi bi-grid me-2"></i> Catégories
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <div class="page-header">
                    <h1><i class="bi bi-plus-circle text-primary"></i> Ajouter une Catégorie</h1>
                    <p class="text-muted mb-0">Créez une nouvelle catégorie de projet</p>
                </div>

                <div class="card">
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="nom" class="form-label">Nom de la catégorie *</label>
                                        <input type="text" class="form-control" id="nom" name="nom" >
                                    </div>

                                    <div class="mb-3">
                                        <label for="icon" class="form-label">Icône Bootstrap *</label>
                                        <input type="text" class="form-control" id="icon" name="icon" 
                                               placeholder="Ex: robot, cpu, diagram-3" >
                                        <small class="text-muted">
                                            Voir les icônes disponibles sur 
                                            <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Aperçu de l'icône</label>
                                    <div class="icon-preview" id="iconPreview">
                                        <i class="bi bi-question-circle"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i> Enregistrer
                                </button>
                                <a href="listeCategorie.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('icon').addEventListener('input', function() {
            const iconName = this.value;
            const preview = document.getElementById('iconPreview');
            preview.innerHTML = '<i class="bi bi-' + iconName + '"></i>';
        });
    </script>
</body>
</html>
