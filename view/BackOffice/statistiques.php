<?php
// view/backoffice/statistiques.php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/ReclamationController.php';

if (!isAdmin()) {
    header('Location: ../frontoffice/dashboard.php');
    exit;
}

$ctrl = new ReclamationController();

// DEBUG: Tester getStats()
error_log("DEBUG: Appel getStats()");
$stats = $ctrl->getStats();
error_log("DEBUG: Stats retournees: " . json_encode($stats));

// Si stats est vide, utiliser la requête directe
if (!$stats || empty($stats['total'])) {
    error_log("ERREUR: Stats vide, utilisation requete directe");
    $db = Database::getInstance();
    $sql = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN statut = 'en-attente' THEN 1 ELSE 0 END) as en_attente,
            SUM(CASE WHEN statut = 'en-cours' THEN 1 ELSE 0 END) as en_cours,
            SUM(CASE WHEN statut = 'resolue' THEN 1 ELSE 0 END) as resolues,
            SUM(CASE WHEN statut = 'fermee' THEN 1 ELSE 0 END) as fermees,
            SUM(CASE WHEN priorite = 'critique' OR priorite = 'urgente' THEN 1 ELSE 0 END) as urgentes
            FROM reclamations";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $stats = $stmt->fetch();
}

$reclamations = $ctrl->getAll();

// Calculer les statistiques
$typeStats = [];
$prioriteStats = [];
$monthlyStats = [];
$statusStats = [
    'en-attente' => 0,
    'en-cours' => 0,
    'resolue' => 0,
    'fermee' => 0
];

foreach ($reclamations as $r) {
    $type = $r['type'];
    $typeStats[$type] = ($typeStats[$type] ?? 0) + 1;
    
    $priorite = $r['priorite'];
    $prioriteStats[$priorite] = ($prioriteStats[$priorite] ?? 0) + 1;
    
    $month = date('Y-m', strtotime($r['date_creation']));
    $monthlyStats[$month] = ($monthlyStats[$month] ?? 0) + 1;
    
    $status = $r['statut'];
    $statusStats[$status] = ($statusStats[$status] ?? 0) + 1;
}

krsort($monthlyStats);
$monthlyStats = array_slice($monthlyStats, 0, 6, true);

// Statistiques par utilisateur
$userStats = [];
foreach ($reclamations as $r) {
    $userId = $r['utilisateur_id'];
    $userStats[$userId] = ($userStats[$userId] ?? 0) + 1;
}

arsort($userStats);
$topUsers = array_slice($userStats, 0, 5, true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --admin-primary: #1e3a8a;
            --admin-secondary: #4c1d95;
            --admin-accent: #7c3aed;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--admin-primary), var(--admin-secondary));
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 250px;
            box-shadow: 5px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
            background: #f8fafc;
            min-height: 100vh;
        }
        
        .nav-link {
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 5px 10px;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(5px);
        }
        
        .logo-container {
            width: 60px; height: 60px; background: white; border-radius: 15px; 
            display: flex; align-items: center; justify-content: center; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            margin: 0 auto 15px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            border: none;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(90deg, var(--admin-primary), var(--admin-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }
        
        .chart-container {
            height: 300px;
            position: relative;
        }
        
        .notification-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 11px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-accent));
            color: white;
            padding: 2rem 0;
            box-shadow: 0 10px 30px rgba(30,58,138,0.3);
            margin-bottom: 2rem;
        }
        
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .progress-thin {
            height: 8px;
        }
        
        .stat-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-4">
            <div class="text-center mb-4">
                <div class="logo-container">
                    <i class="bi bi-shield-check text-primary" style="font-size: 2rem;"></i>
                </div>
                <h4>Kernel Admin</h4>
                <small class="text-light">Panel statistiques</small>
            </div>
            
            <div class="mb-4">
                <div class="d-flex align-items-center bg-white bg-opacity-10 p-3 rounded">
                    <div class="rounded-circle bg-white p-2 me-2">
                        <i class="bi bi-person-fill text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold"><?= htmlspecialchars($_SESSION['nom']) ?></div>
                        <small class="text-light">Administrateur</small>
                    </div>
                </div>
            </div>
            
            <nav class="nav flex-column">
                <a href="dashboard.php" class="nav-link">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a href="gestionreclamations.php" class="nav-link">
                    <i class="bi bi-list-task me-2"></i> Gestion réclamations
                </a>
                <a href="statistiques.php" class="nav-link active">
                    <i class="bi bi-bar-chart me-2"></i> Statistiques
                </a>
                <hr class="text-white my-3">
                <div class="dropdown mb-3">
                    <button class="btn btn-light position-relative w-100" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i> Notifications
                        <?php if (getUnreadNotificationsCount() > 0): ?>
                            <span class="notification-count"><?= getUnreadNotificationsCount() ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end w-100">
                        <div class="dropdown-header">Notifications récentes</div>
                        <?php foreach (array_slice($_SESSION['notifications'] ?? [], 0, 5) as $n): ?>
                            <a class="dropdown-item small" href="#">
                                <i class="bi <?= $n['icon'] ?? 'bi-bell' ?> text-<?= $n['color'] ?? 'secondary' ?>"></i>
                                <?= htmlspecialchars($n['message'] ?? '') ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="../frontoffice/dashboard.php" class="nav-link">
                    <i class="bi bi-arrow-left-right me-2"></i> Interface utilisateur
                </a>
                <a href="../../logout.php" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                </a>
            </nav>
        </div>
    </div>

    <div class="main-content">
        <!-- En-tête -->
        <div class="dashboard-header rounded-3 mb-4">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="fw-bold mb-2">
                            <i class="bi bi-bar-chart me-2"></i> Statistiques détaillées
                        </h1>
                        <p class="mb-0 opacity-90">Analyse complète des réclamations</p>
                    </div>
                    <div class="text-end">
                        <div class="text-light opacity-75">Mis à jour : <?= date('d/m/Y H:i') ?></div>
                        <small class="text-light opacity-75">Total réclamations : <?= $stats['total'] ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques principales -->
        <div class="stat-grid mb-5">
            <div class="stat-card">
                <i class="bi bi-list-ul text-primary fs-1 mb-3"></i>
                <div class="stat-number"><?= $stats['total'] ?></div>
                <h5 class="text-muted fw-semibold">Total réclamations</h5>
                <small class="text-muted">Depuis le début</small>
            </div>
            
            <div class="stat-card">
                <i class="bi bi-hourglass-split text-warning fs-1 mb-3"></i>
                <div class="stat-number"><?= $stats['en_attente'] ?></div>
                <h5 class="text-muted fw-semibold">En attente</h5>
                <small class="text-muted">En attente de traitement</small>
            </div>
            
            <div class="stat-card">
                <i class="bi bi-arrow-repeat text-info fs-1 mb-3"></i>
                <div class="stat-number"><?= $stats['en_cours'] ?></div>
                <h5 class="text-muted fw-semibold">En cours</h5>
                <small class="text-muted">En cours de traitement</small>
            </div>
            
            <div class="stat-card">
                <i class="bi bi-check-circle text-success fs-1 mb-3"></i>
                <div class="stat-number"><?= $stats['resolues'] + $stats['fermees'] ?></div>
                <h5 class="text-muted fw-semibold">Terminées</h5>
                <small class="text-muted">Résolues + fermées</small>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-pie-chart me-2"></i> Répartition par type
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="typeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-success text-white border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart-line me-2"></i> Répartition par priorité
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="priorityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Évolution mensuelle -->
        <div class="card shadow border-0 mb-5">
            <div class="card-header bg-dark text-white border-0">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-month me-2"></i> Évolution mensuelle
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 400px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tableaux de données -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-tag me-2"></i> Statistiques par type
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Nombre</th>
                                        <th>Pourcentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total = $stats['total'];
                                    foreach ($typeStats as $type => $count): 
                                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                        $typeLabels = [
                                            'bug' => 'Bug',
                                            'technique' => 'Technique',
                                            'contenu' => 'Contenu',
                                            'suggestion' => 'Suggestion',
                                            'autre' => 'Autre'
                                        ];
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary stat-badge">
                                                <i class="bi bi-tag"></i> <?= $typeLabels[$type] ?? ucfirst($type) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold"><?= $count ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2 progress-thin">
                                                    <div class="progress-bar bg-primary" 
                                                         style="width: <?= $percentage ?>%"></div>
                                                </div>
                                                <span class="fw-bold"><?= $percentage ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-success text-white border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-flag me-2"></i> Statistiques par priorité
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Priorité</th>
                                        <th>Nombre</th>
                                        <th>Pourcentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $prioriteLabels = [
                                        'urgente' => 'Urgente',
                                        'haute' => 'Haute',
                                        'normale' => 'Normale',
                                        'basse' => 'Basse'
                                    ];
                                    $prioriteColors = [
                                        'urgente' => 'danger',
                                        'haute' => 'warning',
                                        'normale' => 'primary',
                                        'basse' => 'secondary'
                                    ];
                                    
                                    foreach ($prioriteStats as $priorite => $count): 
                                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?= $prioriteColors[$priorite] ?? 'secondary' ?> stat-badge">
                                                <i class="bi bi-flag"></i> <?= $prioriteLabels[$priorite] ?? ucfirst($priorite) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold"><?= $count ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2 progress-thin">
                                                    <div class="progress-bar bg-<?= $prioriteColors[$priorite] ?? 'secondary' ?>" 
                                                         style="width: <?= $percentage ?>%"></div>
                                                </div>
                                                <span class="fw-bold"><?= $percentage ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques de statut et top utilisateurs -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-info text-white border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-hourglass-split me-2"></i> Répartition par statut
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-warning text-dark border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-trophy me-2"></i> Top 5 des utilisateurs
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Utilisateur</th>
                                        <th>Réclamations</th>
                                        <th>Pourcentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    foreach ($topUsers as $userId => $count): 
                                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td class="fw-bold"><?= $i++ ?></td>
                                        <td>
                                            <span class="badge bg-primary stat-badge">
                                                <i class="bi bi-person"></i> Utilisateur #<?= $userId ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold"><?= $count ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2 progress-thin">
                                                    <div class="progress-bar bg-warning" 
                                                         style="width: <?= $percentage ?>%"></div>
                                                </div>
                                                <span class="fw-bold"><?= $percentage ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Résumé des statistiques -->
        <div class="card shadow border-0 mt-5">
            <div class="card-header bg-dark text-white border-0">
                <h5 class="mb-0">
                    <i class="bi bi-clipboard-data me-2"></i> Résumé des statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-info-circle text-primary me-2"></i> Points clés</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Taux de résolution : <strong>
                                    <?= $total > 0 ? round((($stats['resolues'] + $stats['fermees']) / $total) * 100, 1) : 0 ?>%
                                </strong>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-clock text-warning me-2"></i>
                                Réclamations en attente : <strong><?= $stats['en_attente'] ?></strong>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                                Réclamations urgentes : <strong><?= $stats['urgentes'] ?? 0 ?></strong>
                            </li>
                            <li>
                                <i class="bi bi-paperclip text-info me-2"></i>
                                Avec pièces jointes : <strong><?= $stats['avec_pieces_jointes'] ?? 0 ?></strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-lightning-charge text-warning me-2"></i> Performances</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-chat-left-text text-primary me-2"></i>
                                Réclamations sans réponse : <strong><?= $stats['sans_reponse'] ?? 0 ?></strong>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-alarm text-danger me-2"></i>
                                Réclamations en retard : <strong><?= $stats['en_retard'] ?? 0 ?></strong>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-people text-success me-2"></i>
                                Utilisateurs actifs : <strong><?= count($userStats) ?></strong>
                            </li>
                            <li>
                                <i class="bi bi-calendar-check text-info me-2"></i>
                                Moyenne mensuelle : <strong>
                                    <?= count($monthlyStats) > 0 ? round(array_sum($monthlyStats) / count($monthlyStats), 1) : 0 ?>
                                </strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Graphique des types
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        const typeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_map(function($type) {
                    $labels = [
                        'bug' => 'Bug',
                        'technique' => 'Technique',
                        'contenu' => 'Contenu',
                        'suggestion' => 'Suggestion',
                        'autre' => 'Autre'
                    ];
                    return $labels[$type] ?? ucfirst($type);
                }, array_keys($typeStats))) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($typeStats)) ?>,
                    backgroundColor: [
                        '#FF6384', // Rouge pour Bug
                        '#36A2EB', // Bleu pour Technique
                        '#FFCE56', // Jaune pour Contenu
                        '#4BC0C0', // Turquoise pour Suggestion
                        '#9966FF'  // Violet pour Autre
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Graphique des priorités
        const priorityCtx = document.getElementById('priorityChart').getContext('2d');
        const priorityChart = new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: ['Urgente', 'Haute', 'Normale', 'Basse'],
                datasets: [{
                    label: 'Nombre de réclamations',
                    data: [
                        <?= $prioriteStats['urgente'] ?? 0 ?>,
                        <?= $prioriteStats['haute'] ?? 0 ?>,
                        <?= $prioriteStats['normale'] ?? 0 ?>,
                        <?= $prioriteStats['basse'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        '#dc3545', // Rouge pour Urgente
                        '#fd7e14', // Orange pour Haute
                        '#0d6efd', // Bleu pour Normale
                        '#6c757d'  // Gris pour Basse
                    ],
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Graphique mensuel
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_map(function($month) {
                    $date = new Date($month + '-01');
                    return dateToFrenchMonth(date);
                }, array_keys($monthlyStats))) ?>,
                datasets: [{
                    label: 'Réclamations',
                    data: <?= json_encode(array_values($monthlyStats)) ?>,
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#7c3aed',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });

        // Graphique des statuts
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'polarArea',
            data: {
                labels: ['En attente', 'En cours', 'Résolue', 'Fermée'],
                datasets: [{
                    data: [
                        <?= $statusStats['en-attente'] ?? 0 ?>,
                        <?= $statusStats['en-cours'] ?? 0 ?>,
                        <?= $statusStats['resolue'] ?? 0 ?>,
                        <?= $statusStats['fermee'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',   // Jaune pour En attente
                        'rgba(13, 110, 253, 0.8)',  // Bleu pour En cours
                        'rgba(25, 135, 84, 0.8)',   // Vert pour Résolue
                        'rgba(108, 117, 125, 0.8)'  // Gris pour Fermée
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    r: {
                        ticks: {
                            display: false
                        }
                    }
                }
            }
        });

        // Fonction pour formater les mois en français
        function dateToFrenchMonth(date) {
            const months = [
                'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin',
                'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'
            ];
            return months[date.getMonth()] + ' ' + date.getFullYear();
        }

        // Animation pour les cartes de statistiques
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = (index * 0.1) + 's';
                card.classList.add('animate__animated', 'animate__fadeInUp');
            });
            
            // Mettre à jour l'heure toutes les minutes
            setInterval(() => {
                const now = new Date();
                document.querySelector('.text-light.opacity-75').textContent = 
                    'Mis à jour : ' + formatDate(now);
            }, 60000);
        });

        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }
    </script>

    <!-- Animation CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>