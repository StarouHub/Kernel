<?php
// view/backoffice/gestionreclamations.php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../controller/ReclamationController.php';
require_once __DIR__ . '/../../controller/ReponseController.php';

if (!isAdmin()) {
    header('Location: ../frontoffice/dashboard.php');
    exit;
}

$ctrl = new ReclamationController();
$reponseCtrl = new ReponseController();

// NOUVEAU: Vérifier et mettre à jour les priorités en retard (cron manuel)
if (isset($_GET['check_priorities']) && $_GET['check_priorities'] == '1') {
    $updated = $ctrl->checkAndUpdateOverduePriorities();
    if ($updated > 0) {
        $_SESSION['success_message'] = "{$updated} réclamation(s) ont vu leur priorité escaladée automatiquement.";
    }
    header("Location: gestionreclamations.php");
    exit;
}

// Mise à jour du statut
if (isset($_POST['update_statut'])) {
    $ctrl->updateStatut($_POST['id'], $_POST['statut']);
    header("Location: gestionreclamations.php");
    exit;
}

// CORRECTION : Ajouter une réponse ADMIN
if (isset($_POST['submit_reponse_admin'])) {
    if (!empty(trim($_POST['message']))) {
        $result = $reponseCtrl->ajouter(
            $_POST['reclamation_id'], 
            trim($_POST['message']), 
            true
        );
        
        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }
        header("Location: gestionreclamations.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Le message ne peut pas être vide";
        header("Location: gestionreclamations.php");
        exit;
    }
}

// NOUVEAU: Filtres améliorés
$filters = [];
if (isset($_GET['filter_priority'])) {
    $filters['priorite'] = $_GET['filter_priority'];
}
if (isset($_GET['filter_status'])) {
    $filters['statut'] = $_GET['filter_status'];
}
if (isset($_GET['filter_type'])) {
    $filters['type'] = $_GET['filter_type'];
}
if (isset($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

$reclamations = $ctrl->getAll(null, $filters);
$priorityStats = $ctrl->getPriorityStats('7days'); // NOUVEAU: stats de priorité
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Réclamations - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; margin: 0; }
        .sidebar {
            position: fixed; top: 0; left: 0; width: 250px; height: 100vh;
            background: linear-gradient(180deg, #1e3a8a, #4c1d95); color: white; padding: 20px; z-index: 1000;
        }
        .main-content { margin-left: 250px; padding: 30px; }
        .nav-link { color: white; padding: 12px 20px; border-radius: 8px; margin: 5px 0; display: block; text-decoration: none; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.2); }
        .notification-count { position: absolute; top: -8px; right: -8px; background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: flex; align-items: center; justify-content: center; }
        .logo-container { width: 80px; height: 80px; background: white; border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .modal-lg-custom { max-width: 800px; }
        .priority-badge { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
        .status-badge { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
        .action-buttons { display: flex; gap: 0.5rem; }
        .action-buttons .btn { font-size: 0.875rem; padding: 0.25rem 0.5rem; }
        
        /* NOUVEAUX STYLES POUR PRIORITY MANAGER */
        .priority-critical { background: linear-gradient(135deg, #dc3545, #b02a37); color: white; font-weight: bold; }
        .priority-high { background: linear-gradient(135deg, #fd7e14, #d9480f); color: white; }
        .priority-medium { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: white; }
        .priority-low { background: linear-gradient(135deg, #6c757d, #495057); color: white; }
        .priority-score-badge { 
            font-size: 0.7rem; 
            padding: 0.15rem 0.4rem; 
            border-radius: 10px;
            background: rgba(0,0,0,0.2);
            margin-left: 5px;
        }
        .priority-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .priority-critical .priority-indicator { background: #ff6b6b; box-shadow: 0 0 5px #ff6b6b; }
        .priority-high .priority-indicator { background: #ffa94d; box-shadow: 0 0 5px #ffa94d; }
        .priority-medium .priority-indicator { background: #4dabf7; box-shadow: 0 0 5px #4dabf7; }
        .priority-low .priority-indicator { background: #868e96; box-shadow: 0 0 5px #868e96; }
        .filter-card { border-left: 4px solid; }
        .filter-card.critical { border-color: #dc3545; }
        .filter-card.high { border-color: #fd7e14; }
        .filter-card.medium { border-color: #0d6efd; }
        .filter-card.low { border-color: #6c757d; }
    </style>
</head>
<body>

    <!-- Sidebar Admin -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <div class="logo-container mb-3">
                <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
            </div>
            <h3>Kernel Admin</h3>
            <hr style="border-color: rgba(255,255,255,0.3);">
            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-person-fill text-primary" style="font-size: 2rem;"></i>
            </div>
            <div class="mt-2">
                <div class="fw-bold"><?= htmlspecialchars($_SESSION['nom'] ?? 'Admin') ?></div>
                <small>Administrateur</small>
            </div>
        </div>
        <nav>
            <a href="dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="gestionreclamations.php" class="nav-link active"><i class="bi bi-list-check"></i> Gestion Réclamations</a>
            <a href="statistiques.php" class="nav-link"><i class="bi bi-graph-up"></i> Statistiques</a>
            <a href="../../prioritymanager.php" class="nav-link"><i class="bi bi-activity"></i> Priority Manager</a>
        </nav>
        <div class="position-absolute bottom-0 start-0 p-3">
            <div class="dropdown">
                <button class="btn btn-light position-relative" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    <?php if (function_exists('getUnreadNotificationsCount') && getUnreadNotificationsCount() > 0): ?>
                        <span class="notification-count"><?= getUnreadNotificationsCount() ?></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                    <div class="dropdown-header">Notifications</div>
                    <?php foreach ($_SESSION['notifications'] ?? [] as $n): ?>
                        <a class="dropdown-item small <?= !$n['read'] ? 'fw-bold' : '' ?>" href="#">
                            <i class="bi <?= $n['icon'] ?? 'bi-bell' ?> text-<?= $n['color'] ?? 'secondary' ?>"></i>
                            <?= htmlspecialchars($n['message'] ?? '') ?>
                            <small class="d-block text-muted"><?= $n['date'] ?? '' ?></small>
                        </a>
                    <?php endforeach; ?>
                    <?php if (empty($_SESSION['notifications'])): ?>
                        <div class="dropdown-item text-muted small">Aucune notification</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-list-check"></i> Gestion des Réclamations</h2>
            <div>
                <a href="?check_priorities=1" class="btn btn-warning btn-sm" 
                   onclick="return confirm('Vérifier et escalader les priorités en retard ?')">
                    <i class="bi bi-arrow-up-circle"></i> Vérifier les priorités
                </a>
                <a href="../../prioritymanager.php" class="btn btn-info btn-sm">
                    <i class="bi bi-activity"></i> Priority Manager
                </a>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <!-- NOUVEAU: Cartes de statistiques de priorité -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card filter-card critical shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-danger mb-1">Critiques</h6>
                                <?php 
                                $criticalCount = array_reduce($priorityStats, function($carry, $item) {
                                    return $carry + ($item['priorite'] === 'critique' ? $item['total'] : 0);
                                }, 0);
                                ?>
                                <h3 class="mb-0"><?= $criticalCount ?></h3>
                            </div>
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                        </div>
                        <small class="text-muted">Traitement immédiat requis</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card filter-card high shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-warning mb-1">Hautes</h6>
                                <?php 
                                $highCount = array_reduce($priorityStats, function($carry, $item) {
                                    return $carry + ($item['priorite'] === 'haute' ? $item['total'] : 0);
                                }, 0);
                                ?>
                                <h3 class="mb-0"><?= $highCount ?></h3>
                            </div>
                            <i class="bi bi-exclamation-circle text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <small class="text-muted">À traiter sous 24h</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card filter-card medium shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-primary mb-1">Normales</h6>
                                <?php 
                                $mediumCount = array_reduce($priorityStats, function($carry, $item) {
                                    return $carry + ($item['priorite'] === 'normale' ? $item['total'] : 0);
                                }, 0);
                                ?>
                                <h3 class="mb-0"><?= $mediumCount ?></h3>
                            </div>
                            <i class="bi bi-clock text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <small class="text-muted">À traiter sous 48h</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card filter-card low shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-secondary mb-1">Basses</h6>
                                <?php 
                                $lowCount = array_reduce($priorityStats, function($carry, $item) {
                                    return $carry + ($item['priorite'] === 'basse' ? $item['total'] : 0);
                                }, 0);
                                ?>
                                <h3 class="mb-0"><?= $lowCount ?></h3>
                            </div>
                            <i class="bi bi-check-circle text-secondary" style="font-size: 2rem;"></i>
                        </div>
                        <small class="text-muted">À traiter sous 5 jours</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtres améliorés -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtres avancés</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Priorité</label>
                        <select name="filter_priority" class="form-select">
                            <option value="">Toutes les priorités</option>
                            <option value="critique" <?= isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'critique' ? 'selected' : '' ?>>Critique</option>
                            <option value="haute" <?= isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'haute' ? 'selected' : '' ?>>Haute</option>
                            <option value="normale" <?= isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'normale' ? 'selected' : '' ?>>Normale</option>
                            <option value="basse" <?= isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'basse' ? 'selected' : '' ?>>Basse</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Statut</label>
                        <select name="filter_status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="en-attente" <?= isset($_GET['filter_status']) && $_GET['filter_status'] == 'en-attente' ? 'selected' : '' ?>>En attente</option>
                            <option value="en-cours" <?= isset($_GET['filter_status']) && $_GET['filter_status'] == 'en-cours' ? 'selected' : '' ?>>En cours</option>
                            <option value="resolue" <?= isset($_GET['filter_status']) && $_GET['filter_status'] == 'resolue' ? 'selected' : '' ?>>Résolue</option>
                            <option value="fermee" <?= isset($_GET['filter_status']) && $_GET['filter_status'] == 'fermee' ? 'selected' : '' ?>>Fermée</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="filter_type" class="form-select">
                            <option value="">Tous les types</option>
                            <option value="bug" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'bug' ? 'selected' : '' ?>>Bug</option>
                            <option value="technique" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'technique' ? 'selected' : '' ?>>Technique</option>
                            <option value="contenu" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'contenu' ? 'selected' : '' ?>>Contenu</option>
                            <option value="suggestion" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'suggestion' ? 'selected' : '' ?>>Suggestion</option>
                            <option value="autre" <?= isset($_GET['filter_type']) && $_GET['filter_type'] == 'autre' ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Recherche</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Rechercher..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <a href="gestionreclamations.php" class="btn btn-secondary">Réinitialiser</a>
                        <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des réclamations</h5>
                    <span class="badge bg-light text-dark">
                        <?= count($reclamations) ?> réclamation(s)
                    </span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($reclamations)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3">Aucune réclamation</h4>
                        <p class="text-muted">Aucune réclamation ne correspond à vos filtres.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#ID</th>
                                    <th>Titre</th>
                                    <th>Utilisateur</th>
                                    <th>Type</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reclamations as $r): 
                                    // NOUVEAU: Classes CSS pour les priorités
                                    $priority_class = match($r['priorite']) {
                                        'critique' => 'priority-critical',
                                        'haute' => 'priority-high',
                                        'normale' => 'priority-medium',
                                        'basse' => 'priority-low',
                                        default => ''
                                    };
                                    
                                    // NOUVEAU: Récupérer le score de priorité
                                    $priority_score = $r['priority_score'] ?? 0;
                                    $priority_reason = $r['priority_reason'] ?? '';
                                ?>
                                <tr>
                                    <td><strong>#<?= $r['id'] ?></strong></td>
                                    <td>
                                        <a href="detailadmin.php?id=<?= $r['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($r['titre']) ?>
                                        </a>
                                        <?php if (isset($r['nombre_reponses']) && $r['nombre_reponses'] > 0): ?>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-chat-text"></i> <?= $r['nombre_reponses'] ?> réponse(s)
                                            </small>
                                        <?php endif; ?>
                                        <!-- NOUVEAU: Indicateur de retard -->
                                        <?php if (isset($r['est_en_retard']) && $r['est_en_retard']): ?>
                                            <small class="text-danger d-block">
                                                <i class="bi bi-clock-history"></i> En retard
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($r['auteur'] ?? 'Inconnu') ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-tag"></i> <?= ucfirst($r['type']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- NOUVEAU: Affichage amélioré de la priorité -->
                                        <div class="d-flex align-items-center">
                                            <span class="priority-indicator"></span>
                                            <span class="badge <?= $priority_class ?> priority-badge">
                                                <?= ucfirst($r['priorite']) ?>
                                                <?php if ($priority_score > 0): ?>
                                                    <span class="priority-score-badge"><?= $priority_score ?>%</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <!-- NOUVEAU: Info-bulle avec la raison -->
                                        <?php if (!empty($priority_reason)): ?>
                                            <small class="text-muted d-block mt-1" title="<?= htmlspecialchars($priority_reason) ?>">
                                                <i class="bi bi-info-circle"></i> 
                                                <?= strlen($priority_reason) > 30 ? substr($priority_reason, 0, 30) . '...' : $priority_reason ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                            <input type="hidden" name="update_statut" value="1">
                                            <select name="statut" onchange="this.form.submit()" class="form-select form-select-sm" style="width: 140px;">
                                                <option value="en-attente" <?= $r['statut'] === 'en-attente' ? 'selected' : '' ?>>⏳ En attente</option>
                                                <option value="en-cours" <?= $r['statut'] === 'en-cours' ? 'selected' : '' ?>>🔧 En cours</option>
                                                <option value="resolue" <?= $r['statut'] === 'resolue' ? 'selected' : '' ?>>✅ Résolue</option>
                                                <option value="fermee" <?= $r['statut'] === 'fermee' ? 'selected' : '' ?>>🔒 Fermée</option>
                                            </select>
                                        </form>
                                        <!-- NOUVEAU: Indicateur de temps -->
                                        <?php 
                                        $days_old = $r['jours_ecoules'] ?? 0;
                                        if ($r['statut'] === 'en-attente' && $days_old > 0): 
                                        ?>
                                            <small class="text-muted d-block">
                                                Depuis <?= $days_old ?> jour(s)
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($r['date_creation'])) ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('H:i', strtotime($r['date_creation'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="detailadmin.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary" 
                                               title="Voir les détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalRepondre<?= $r['id'] ?>"
                                                    title="Répondre à cette réclamation">
                                                <i class="bi bi-reply"></i>
                                            </button>
                                            <!-- NOUVEAU: Bouton d'escalade de priorité -->
                                            <?php if ($r['priorite'] !== 'critique' && $r['statut'] === 'en-attente'): ?>
                                                <button type="button" class="btn btn-sm btn-warning" 
                                                        onclick="escalatePriority(<?= $r['id'] ?>, '<?= $r['priorite'] ?>')"
                                                        title="Escalader la priorité">
                                                    <i class="bi bi-arrow-up"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- MODAL POUR RÉPONDRE -->
                                <div class="modal fade" id="modalRepondre<?= $r['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg-custom">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-reply"></i> Répondre à la réclamation #<?= $r['id'] ?>
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Réclamation :</label>
                                                        <div class="border p-3 bg-light rounded">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <strong><?= htmlspecialchars($r['titre']) ?></strong>
                                                                <span class="badge <?= $priority_class ?>">
                                                                    <?= ucfirst($r['priorite']) ?>
                                                                </span>
                                                            </div>
                                                            <p class="mb-0 mt-2 text-muted">
                                                                <?= htmlspecialchars(substr($r['description'] ?? 'Aucune description', 0, 200)) ?>
                                                                <?= strlen($r['description'] ?? '') > 200 ? '...' : '' ?>
                                                            </p>
                                                            <div class="mt-2">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-person"></i> 
                                                                    <?= htmlspecialchars($r['auteur'] ?? 'Inconnu') ?> • 
                                                                    <i class="bi bi-calendar"></i> 
                                                                    <?= date('d/m/Y H:i', strtotime($r['date_creation'])) ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Votre réponse :</label>
                                                        <textarea name="message" class="form-control" rows="6" 
                                                                  placeholder="Rédigez votre réponse à l'utilisateur ici..." 
                                                                  required></textarea>
                                                        <div class="form-text">
                                                            Votre réponse sera envoyée à l'utilisateur et marquera la réclamation comme "En cours".
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="reclamation_id" value="<?= $r['id'] ?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle"></i> Annuler
                                                    </button>
                                                    <button type="submit" name="submit_reponse_admin" class="btn btn-success">
                                                        <i class="bi bi-send"></i> Envoyer la réponse
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus sur le textarea quand le modal s'ouvre
        document.addEventListener('DOMContentLoaded', function() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('shown.bs.modal', function() {
                    const textarea = this.querySelector('textarea');
                    if (textarea) {
                        textarea.focus();
                        textarea.style.height = 'auto';
                        textarea.style.height = (textarea.scrollHeight) + 'px';
                    }
                });
            });
            
            // Auto-expand des textarea en édition
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
            
            // Tri automatique par priorité
            sortTableByPriority();
        });
        
        // NOUVEAU: Fonction pour trier par priorité
        function sortTableByPriority() {
            const rows = Array.from(document.querySelectorAll('tbody tr'));
            const priorityOrder = { 'critique': 1, 'haute': 2, 'normale': 3, 'basse': 4 };
            
            rows.sort((a, b) => {
                const aPriority = a.querySelector('.priority-badge').textContent.trim().toLowerCase();
                const bPriority = b.querySelector('.priority-badge').textContent.trim().toLowerCase();
                
                const aScore = parseInt(a.querySelector('.priority-score-badge')?.textContent || '0');
                const bScore = parseInt(b.querySelector('.priority-score-badge')?.textContent || '0');
                
                // Comparer d'abord par niveau de priorité, puis par score
                if (priorityOrder[aPriority] !== priorityOrder[bPriority]) {
                    return priorityOrder[aPriority] - priorityOrder[bPriority];
                }
                return bScore - aScore;
            });
            
            const tbody = document.querySelector('tbody');
            rows.forEach(row => tbody.appendChild(row));
        }
        
        // NOUVEAU: Fonction pour escalader la priorité
        function escalatePriority(reclamationId, currentPriority) {
            const newPriority = getNextPriority(currentPriority);
            
            if (confirm(`Escalader la priorité de "${currentPriority}" à "${newPriority}" ?`)) {
                fetch('../../controller/ReclamationController.php?action=escalate_priority', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        reclamation_id: reclamationId,
                        new_priority: newPriority
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Priorité escaladée avec succès !');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de l\'escalade de priorité');
                });
            }
        }
        
        // NOUVEAU: Fonction pour obtenir la priorité suivante
        function getNextPriority(currentPriority) {
            const priorityChain = {
                'basse': 'normale',
                'normale': 'haute',
                'haute': 'critique',
                'critique': 'critique'
            };
            return priorityChain[currentPriority] || currentPriority;
        }
        
        // NOUVEAU: Mettre en évidence les réclamations critiques
        function highlightCriticalReclamations() {
            document.querySelectorAll('.priority-critical').forEach(critical => {
                const row = critical.closest('tr');
                if (row) {
                    row.style.animation = 'pulse 2s infinite';
                    row.style.backgroundColor = 'rgba(220, 53, 69, 0.05)';
                }
            });
        }
        
        // NOUVEAU: Animation de pulse pour les urgences
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
                70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
                100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
            }
        `;
        document.head.appendChild(style);
        
        // Appliquer les mises en évidence
        highlightCriticalReclamations();
    </script>
</body>
</html>