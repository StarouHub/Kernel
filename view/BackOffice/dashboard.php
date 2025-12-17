<?php
session_start();
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../../controller/categoriecontroller.php');

$projetController = new ProjetController();
$actualiteController = new ActualiteController();
$categorieController = new CategorieController();

$projets = $projetController->listProjets();
$actualites = $actualiteController->listActualites();
$categories = $categorieController->listCategories();

// Statistiques
$totalProjets = count($projets);
$totalActualites = count($actualites);
$totalCategories = count($categories);

// Budget total
$budgetTotal = 0;
$budgetActuel = 0;
foreach ($projets as $projet) {
    $budgetTotal += $projet['budget_requis'];
    $budgetActuel += $projet['budget_actuel'];
}

// Projets par statut
$statutCount = ['idee' => 0, 'prototype' => 0, 'mvp' => 0, 'production' => 0];
foreach ($projets as $projet) {
    if (isset($statutCount[$projet['statut']])) {
        $statutCount[$projet['statut']]++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Backoffice Kernel</title>
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
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        .stat-card h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stat-card p {
            color: #6B7280;
            margin: 0;
            font-size: 14px;
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
        
        .table {
            margin: 0;
        }
        
        .table thead th {
            border-bottom: 2px solid #E5E7EB;
            color: #6B7280;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
        }
        
        .badge {
            padding: 6px 12px;
            font-weight: 500;
            border-radius: 20px;
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
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="projet/listeProjet.php">
                            <i class="bi bi-lightbulb me-2"></i> Projets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="actualite/listeActualite.php">
                            <i class="bi bi-newspaper me-2"></i> Actualités
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categorie/listeCategorie.php">
                            <i class="bi bi-grid me-2"></i> Catégories
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Page Header -->
                <div class="page-header">
                    <h1><i class="bi bi-speedometer2 text-primary"></i> Dashboard</h1>
                    <p class="text-muted mb-0">Vue d'ensemble de la plateforme Kernel</p>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="icon" style="background: linear-gradient(135deg, #2563EB, #7C3AED); color: white;">
                                <i class="bi bi-lightbulb"></i>
                            </div>
                            <h3><?php echo $totalProjets; ?></h3>
                            <p>Projets Total</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="icon" style="background: linear-gradient(135deg, #10B981, #059669); color: white;">
                                <i class="bi bi-newspaper"></i>
                            </div>
                            <h3><?php echo $totalActualites; ?></h3>
                            <p>Actualités</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="icon" style="background: linear-gradient(135deg, #F59E0B, #D97706); color: white;">
                                <i class="bi bi-grid"></i>
                            </div>
                            <h3><?php echo $totalCategories; ?></h3>
                            <p>Catégories</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="icon" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED); color: white;">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <h3><?php echo number_format($budgetActuel, 0, ',', ' '); ?></h3>
                            <p>Budget Collecté (TND)</p>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-bar-chart me-2"></i> Projets par Statut
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><i class="bi bi-lightbulb text-warning"></i> Idée</span>
                                        <strong><?php echo $statutCount['idee']; ?></strong>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-warning" style="width: <?php echo $totalProjets > 0 ? ($statutCount['idee']/$totalProjets*100) : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><i class="bi bi-gear text-info"></i> Prototype</span>
                                        <strong><?php echo $statutCount['prototype']; ?></strong>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-info" style="width: <?php echo $totalProjets > 0 ? ($statutCount['prototype']/$totalProjets*100) : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><i class="bi bi-rocket text-primary"></i> MVP</span>
                                        <strong><?php echo $statutCount['mvp']; ?></strong>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-primary" style="width: <?php echo $totalProjets > 0 ? ($statutCount['mvp']/$totalProjets*100) : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><i class="bi bi-check-circle text-success"></i> Production</span>
                                        <strong><?php echo $statutCount['production']; ?></strong>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" style="width: <?php echo $totalProjets > 0 ? ($statutCount['production']/$totalProjets*100) : 0; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-cash me-2"></i> Budget Global
                            </div>
                            <div class="card-body">
                                <h2 class="text-primary mb-3"><?php echo number_format($budgetActuel, 0, ',', ' '); ?> TND</h2>
                                <p class="text-muted mb-2">Objectif: <?php echo number_format($budgetTotal, 0, ',', ' '); ?> TND</p>
                                <div class="progress mb-3" style="height: 20px;">
                                    <div class="progress-bar bg-primary" style="width: <?php echo $budgetTotal > 0 ? ($budgetActuel/$budgetTotal*100) : 0; ?>%">
                                        <?php echo $budgetTotal > 0 ? round($budgetActuel/$budgetTotal*100) : 0; ?>%
                                    </div>
                                </div>
                                <div class="row text-center mt-4">
                                    <div class="col-6">
                                        <h4 class="text-success"><?php echo number_format($budgetActuel, 0, ',', ' '); ?></h4>
                                        <small class="text-muted">Collecté</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-danger"><?php echo number_format($budgetTotal - $budgetActuel, 0, ',', ' '); ?></h4>
                                        <small class="text-muted">Restant</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Projects -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clock-history me-2"></i> Projets Récents
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Statut</th>
                                        <th>Budget</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $recentProjets = array_slice($projets, 0, 5);
                                    foreach ($recentProjets as $projet): 
                                        $statutBadges = [
                                            'idee' => 'bg-warning',
                                            'prototype' => 'bg-info',
                                            'mvp' => 'bg-primary',
                                            'production' => 'bg-success'
                                        ];
                                    ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($projet['titre']); ?></strong></td>
                                            <td><span class="badge <?php echo $statutBadges[$projet['statut']] ?? 'bg-secondary'; ?>"><?php echo ucfirst($projet['statut']); ?></span></td>
                                            <td><?php echo number_format($projet['budget_actuel'], 0, ',', ' '); ?> / <?php echo number_format($projet['budget_requis'], 0, ',', ' '); ?> TND</td>
                                            <td><?php echo date('d/m/Y', strtotime($projet['date_creation'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
