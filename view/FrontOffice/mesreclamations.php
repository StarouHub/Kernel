<?php
// view/FrontOffice/mesreclamations.php
// CORRECTION : Chemin absolu
define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/init.php';

// Démarrer la session si pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification de session
if (!isset($_SESSION['user_id'])) {
    header('Location: dashboard.php?error=not_logged_in');
    exit;
}

// Charger le contrôleur
try {
    require_once ROOT_PATH . '/controller/ReclamationController.php';
    $ctrl = new ReclamationController();
    
    // Vérifier si la méthode existe
    if (method_exists($ctrl, 'getAll')) {
        $reclamations = $ctrl->getAll($_SESSION['user_id']);
    } else {
        $reclamations = [];
    }
} catch (Exception $e) {
    error_log("Erreur mesreclamations: " . $e->getMessage());
    $reclamations = [];
}

// Message de succès si une réclamation vient d'être créée
$creationSuccess = isset($_GET['success']) && $_GET['success'] === 'created';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Réclamations - Kernel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            background: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 120px;
        }
        .header {
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
            color: white;
            padding: 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
        }
        .logo-container {
            width: 80px; 
            height: 80px; 
            background: white; 
            border-radius: 20px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .card-reclamation {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            border: 1px solid #e9ecef;
            height: 100%;
        }
        .card-reclamation:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 15px 40px rgba(0,0,0,0.12); 
            border-color: #cfe2ff;
        }
        .priority-indicator {
            width: 4px;
            position: absolute;
            left: 0; 
            top: 0; 
            bottom: 0;
            border-radius: 4px 0 0 4px;
        }
        .priority-urgente { background: linear-gradient(to bottom, #dc3545, #b02a37); }
        .priority-haute { background: linear-gradient(to bottom, #fd7e14, #e96b00); }
        .priority-normale { background: linear-gradient(to bottom, #0d6efd, #0b5ed7); }
        .priority-basse { background: linear-gradient(to bottom, #6c757d, #5c636a); }
        
        .empty-state {
            padding: 5rem 2rem;
            text-align: center;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            border-left: 5px solid #1e3a8a;
        }
        
        .badge-pill-custom {
            border-radius: 50rem;
            padding: 0.35em 0.65em;
            font-weight: 500;
        }
        
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-4">
                    <div class="logo-container">
                        <i class="bi bi-hexagon-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <div>
                        <h2 class="m-0" style="font-size: 32px; font-weight: 800; color: white; font-family: 'Raleway', sans-serif; display: flex; align-items: center; gap: 12px;">Mes Réclamations</h2>
                        <p class="mb-0 opacity-90">Kernel Platform</p>
                    </div>
                </div>
                <div>
                    <!-- CORRECTION : Chemin relatif dans le même dossier -->
                    <a href="nouvellereclamation.php" class="btn btn-light me-2">
                        <i class="bi bi-plus-circle"></i> Nouvelle réclamation
                    </a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="../BackOffice/gestionreclamations.php" class="btn btn-warning me-2">
                        <i class="bi bi-arrow-right-circle"></i> BackOffice
                    </a>
                    <?php endif; ?>
                    <a href="dashboard.php" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <?php if ($creationSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                <div class="flex-grow-1">
                    <strong>Succès !</strong> Votre réclamation a été créée avec succès.
                    <?php if (isset($_GET['id'])): ?>
                        <br><small class="opacity-75">Référence : #<?= htmlspecialchars($_GET['id']) ?></small>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <?php if (!empty($reclamations)): ?>
        <?php
        $stats = [
            'total' => count($reclamations),
            'en_attente' => 0,
            'en_cours' => 0,
            'resolues' => 0,
            'fermees' => 0
        ];
        
        foreach ($reclamations as $r) {
            switch ($r['statut'] ?? 'en-attente') {
                case 'en-attente': $stats['en_attente']++; break;
                case 'en-cours': $stats['en_cours']++; break;
                case 'resolue': $stats['resolues']++; break;
                case 'fermee': $stats['fermees']++; break;
            }
        }
        ?>
        <div class="stats-card">
            <div class="row text-center">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="fw-bold fs-3 text-primary"><?= $stats['total'] ?></div>
                    <small class="text-muted">Total</small>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="fw-bold fs-3 text-warning"><?= $stats['en_attente'] ?></div>
                    <small class="text-muted">En attente</small>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="fw-bold fs-3 text-info"><?= $stats['en_cours'] ?></div>
                    <small class="text-muted">En cours</small>
                </div>
                <div class="col-md-3">
                    <div class="fw-bold fs-3 text-success"><?= $stats['resolues'] + $stats['fermees'] ?></div>
                    <small class="text-muted">Traitées</small>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (empty($reclamations)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox display-1 text-muted mb-4"></i>
                <h3 class="mb-3">Aucune réclamation pour le moment</h3>
                <p class="text-muted mb-4">Vous n'avez pas encore créé de réclamation. Créez votre première réclamation pour commencer.</p>
                <!-- CORRECTION : Chemin relatif -->
                <a href="nouvellereclamation.php" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-plus-circle me-2"></i> Créer ma première réclamation
                </a>
                <div class="mt-4 text-muted">
                    <small><i class="bi bi-info-circle me-1"></i> Besoin d'aide ? <a href="#" class="text-decoration-none">Consultez notre guide</a></small>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($reclamations as $r): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card-reclamation h-100">
                            <div class="priority-indicator priority-<?= $r['priorite'] ?? 'normale' ?>"></div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1 text-truncate-2" title="<?= htmlspecialchars($r['titre'] ?? '') ?>">
                                            <?= htmlspecialchars($r['titre'] ?? '') ?>
                                        </h5>
                                        <small class="text-muted">
                                            <i class="bi bi-hash"></i> #<?= $r['id'] ?? 'N/A' ?>
                                        </small>
                                    </div>
                                    <span class="badge <?= 
                                        ($r['statut'] ?? 'en-attente') == 'en-attente' ? 'bg-warning text-dark' : 
                                        (($r['statut'] ?? 'en-attente') == 'en-cours' ? 'bg-info' : 
                                        (($r['statut'] ?? 'en-attente') == 'resolue' ? 'bg-success' : 'bg-secondary')) 
                                    ?> badge-pill-custom ms-2">
                                        <?= ucfirst(str_replace('-', ' ', $r['statut'] ?? 'en-attente')) ?>
                                    </span>
                                </div>
                                <p class="card-text text-muted mb-3" style="min-height: 60px;">
                                    <?= htmlspecialchars(substr($r['description'] ?? '', 0, 120)) ?>
                                    <?= strlen($r['description'] ?? '') > 120 ? '...' : '' ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-secondary me-1">
                                            <i class="bi bi-tag me-1"></i><?= ucfirst($r['type'] ?? 'autre') ?>
                                        </span>
                                        <span class="badge <?= 
                                            ($r['priorite'] ?? 'normale') == 'urgente' ? 'bg-danger' : 
                                            (($r['priorite'] ?? 'normale') == 'haute' ? 'bg-warning text-dark' : 
                                            (($r['priorite'] ?? 'normale') == 'normale' ? 'bg-primary' : 'bg-dark')) 
                                        ?>">
                                            <i class="bi bi-flag me-1"></i><?= ucfirst($r['priorite'] ?? 'normale') ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i><?= isset($r['date_creation']) ? date('d/m/Y', strtotime($r['date_creation'])) : 'N/A' ?>
                                    </small>
                                </div>
                                <div class="d-grid">
                                    <!-- CORRECTION : Chemin relatif -->
                                    <a href="detailreclamation.php?id=<?= $r['id'] ?? 0 ?>" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-2"></i> Voir les détails
                                        <?php if (isset($r['nombre_reponses']) && $r['nombre_reponses'] > 0): ?>
                                        <span class="badge bg-primary rounded-pill ms-2"><?= $r['nombre_reponses'] ?></span>
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <?php if (isset($r['jours_ecoules']) && $r['jours_ecoules'] > 7 && ($r['statut'] ?? '') == 'en-attente'): ?>
                                <div class="mt-3">
                                    <small class="text-warning">
                                        <i class="bi bi-clock-history me-1"></i>En attente depuis <?= $r['jours_ecoules'] ?> jours
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-5 text-center">
                <p class="text-muted">
                    <i class="bi bi-info-circle me-1"></i> Affichage de <?= count($reclamations) ?> réclamation<?= count($reclamations) > 1 ? 's' : '' ?>
                </p>
                <!-- CORRECTION : Chemin relatif -->
                <a href="nouvellereclamation.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i> Créer une nouvelle réclamation
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>