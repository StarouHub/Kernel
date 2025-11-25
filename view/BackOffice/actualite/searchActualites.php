<?php
session_start();
include_once(__DIR__ . '/../../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../../../controller/projetcontroller.php');

$actualiteController = new ActualiteController();
$projetController = new ProjetController();

// Récupérer tous les projets pour le select
$projets = $projetController->listProjets();

// Variables
$actualites = [];
$selectedProjetId = null;
$selectedProjetInfo = null;

// Si un projet est sélectionné, récupérer ses actualités (JOINTURE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['projet_id']) && !empty($_POST['projet_id'])) {
    $selectedProjetId = intval($_POST['projet_id']);
    
    // JOINTURE : Récupérer les actualités du projet
    $actualites = $actualiteController->afficherActualites($selectedProjetId);
    
    // Récupérer les infos du projet
    $selectedProjetInfo = $projetController->showProjet($selectedProjetId);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechercher Actualités par Projet - Backoffice Kernel</title>
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
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            font-weight: 600;
        }
        
        .search-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .search-card .form-select {
            padding: 15px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
        }
        
        .search-card .btn {
            padding: 15px 40px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
        }
        
        .actualite-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .actualite-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .actualite-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .actualite-title {
            font-size: 20px;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 5px;
        }
        
        .actualite-content {
            color: #6B7280;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .actualite-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
            font-size: 14px;
            color: #6B7280;
        }
        
        .badge {
            padding: 6px 12px;
            font-weight: 500;
            border-radius: 20px;
        }
        
        .projet-info {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 64px;
            color: #E5E7EB;
            margin-bottom: 20px;
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
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i class="bi bi-search text-primary"></i> Rechercher Actualités par Projet</h1>
                        <p class="text-muted mb-0">Afficher les actualités d'un projet spécifique (JOINTURE SQL)</p>
                    </div>
                    <a href="listeActualite.php" class="btn btn-outline-primary">
                        <i class="bi bi-list me-2"></i> Toutes les Actualités
                    </a>
                </div>

                <!-- Search Form -->
                <div class="search-card">
                    <h4 class="mb-4">
                        <i class="bi bi-funnel me-2"></i> Filtrer par Projet
                    </h4>
                    <form method="POST" action="">
                        <div class="row align-items-end">
                            <div class="col-md-9">
                                <label class="form-label">Sélectionner un projet</label>
                                <select name="projet_id" class="form-select" required>
                                    <option value="">-- Choisir un projet --</option>
                                    <?php foreach ($projets as $projet): ?>
                                        <option value="<?php echo $projet['id']; ?>" 
                                                <?php echo ($selectedProjetId == $projet['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($projet['titre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-light w-100">
                                    <i class="bi bi-search me-2"></i> Rechercher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Results -->
                <?php if ($selectedProjetId): ?>
                    <!-- Projet Info -->
                    <?php if ($selectedProjetInfo): ?>
                        <div class="projet-info">
                            <h5 class="mb-2">
                                <i class="bi bi-folder-open me-2"></i> Projet Sélectionné
                            </h5>
                            <h3 class="mb-0"><?php echo htmlspecialchars($selectedProjetInfo['titre']); ?></h3>
                            <p class="mb-0 mt-2 opacity-75">
                                <?php echo count($actualites); ?> actualité<?php echo count($actualites) > 1 ? 's' : ''; ?> trouvée<?php echo count($actualites) > 1 ? 's' : ''; ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Actualités List -->
                    <?php if (empty($actualites)): ?>
                        <div class="card">
                            <div class="card-body">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h5 class="text-muted">Aucune actualité pour ce projet</h5>
                                    <p class="text-muted">Ce projet n'a pas encore publié d'actualités.</p>
                                    <a href="ajouterActualite.php" class="btn btn-primary mt-3">
                                        <i class="bi bi-plus-circle me-2"></i> Créer une actualité
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($actualites as $actu): 
                            // Badges de type
                            $typeBadges = [
                                'milestone' => ['class' => 'bg-success', 'icon' => 'trophy', 'text' => 'Milestone'],
                                'update' => ['class' => 'bg-primary', 'icon' => 'arrow-repeat', 'text' => 'Update'],
                                'announcement' => ['class' => 'bg-warning', 'icon' => 'megaphone', 'text' => 'Annonce']
                            ];
                            $badge = $typeBadges[$actu['type']] ?? $typeBadges['update'];
                        ?>
                            <div class="actualite-card">
                                <div class="actualite-header">
                                    <div>
                                        <h3 class="actualite-title"><?php echo htmlspecialchars($actu['titre']); ?></h3>
                                        <span class="badge <?php echo $badge['class']; ?>">
                                            <i class="bi bi-<?php echo $badge['icon']; ?> me-1"></i>
                                            <?php echo $badge['text']; ?>
                                        </span>
                                    </div>
                                    <div>
                                        <a href="modifierActualite.php?id=<?php echo $actu['id']; ?>" 
                                           class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="supprimerActualite.php?id=<?php echo $actu['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Supprimer cette actualité ?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="actualite-content">
                                    <?php echo nl2br(htmlspecialchars($actu['contenu'])); ?>
                                </div>
                                
                                <div class="actualite-footer">
                                    <span>
                                        <i class="bi bi-calendar me-1"></i>
                                        <?php echo date('d/m/Y à H:i', strtotime($actu['date_publication'])); ?>
                                    </span>
                                    <span>
                                        <i class="bi bi-folder me-1"></i>
                                        <?php echo htmlspecialchars($actu['projet_titre']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Initial State -->
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                <i class="bi bi-search"></i>
                                <h5 class="text-muted">Sélectionnez un projet</h5>
                                <p class="text-muted">Choisissez un projet dans le menu déroulant ci-dessus pour voir ses actualités.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
