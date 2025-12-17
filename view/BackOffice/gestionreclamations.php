<?php
// view/backoffice/gestionreclamations.php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../controller/ReclamationController.php';

if (!isAdmin()) {
    header('Location: ../frontoffice/dashboard.php');
    exit;
}

$ctrl = new ReclamationController();

// 🏆 1. SYSTÈME D'ESCALADE AUTOMATIQUE DES PRIORITÉS
$autoEscalated = 0;
if (isset($_GET['check_escalation']) || !isset($_GET['skip_auto_check'])) {
    $autoEscalated = $ctrl->checkAndUpdateOverduePriorities();
    if ($autoEscalated > 0) {
        addNotification(
            "$autoEscalated réclamation(s) ont été escaladées automatiquement",
            'warning',
            'auto_escalation'
        );
    }
}

// Mise à jour du statut
if (isset($_POST['update_statut'])) {
    $ctrl->updateStatut($_POST['id'], $_POST['statut']);
    header("Location: gestionreclamations.php");
    exit;
}

// 🥉 3. ESCALADE MANUELLE DE PRIORITÉ (AJAX)
if (isset($_POST['action']) && $_POST['action'] === 'escalate_priority') {
    header('Content-Type: application/json');
    $result = $ctrl->updatePriority($_POST['id'], $_POST['new_priority']);
    echo json_encode($result);
    exit;
}

// Récupérer les réclamations
$reclamations = $ctrl->getAll();

// 🥈 2. STATISTIQUES DES PRIORITÉS POUR LE TABLEAU DE BORD VISUEL
$priorityStats = [
    'critique' => 0,
    'urgente' => 0,
    'haute' => 0,
    'normale' => 0,
    'basse' => 0
];

foreach ($reclamations as $r) {
    $priority = $r['priorite'] ?? 'normale';
    if (isset($priorityStats[$priority])) {
        $priorityStats[$priority]++;
    }
}

$totalReclamations = count($reclamations);
$criticalCount = $priorityStats['critique'];
$urgentCount = $priorityStats['urgente'];
$highCount = $priorityStats['haute'];
$normalCount = $priorityStats['normale'];
$lowCount = $priorityStats['basse'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Réclamations - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { 
            background: #f8f9fa; 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0; 
        }
        
        .sidebar {
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 250px; 
            height: 100vh;
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%); /* Dégradé bleu */
            color: white; 
            padding: 20px; 
            z-index: 1000;
            box-shadow: 0 4px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
        }
        
        .navbar-brand {
            font-size: 32px;
            font-weight: 800;
            color: white !important;
            font-family: 'Raleway', sans-serif;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: opacity 0.3s;
            margin-bottom: 20px;
        }
        
        .navbar-brand:hover {
            opacity: 0.9;
            color: white !important;
        }
        
        .navbar-brand i {
            font-size: 36px;
        }
        .main-content { 
            margin-left: 250px; 
            padding: 30px; 
        }
        
        .nav-link { 
            color: white; 
            padding: 12px 20px; 
            border-radius: 8px; 
            margin: 5px 0; 
            display: block; 
            text-decoration: none; 
        }
        .nav-link:hover, .nav-link.active { 
            background: rgba(255,255,255,0.2); 
        }
        .notification-count { 
            position: absolute; 
            top: -8px; 
            right: -8px; 
            background: #dc3545; 
            color: white; 
            border-radius: 50%; 
            width: 20px; 
            height: 20px; 
            font-size: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
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
    </style>
</head>
<body>

    <!-- Sidebar Admin -->
    <div class="sidebar">
        <!-- Logo Kernel -->
        <a class="navbar-brand" href="indexx.php">
            <i class="bi bi-hexagon-fill"></i> Kernel
        </a>
        
        <div class="text-center mb-4">
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
            <a href="dashboard2.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="gestionreclamations.php" class="nav-link active"><i class="bi bi-list-check"></i> Gestion Réclamations</a>
            <a href="statistiques.php" class="nav-link"><i class="bi bi-graph-up"></i> Statistiques</a>
        </nav>
        <div class="position-absolute bottom-0 start-0 p-3">
            <?php 
            // Inclure le composant de notifications
            $notifications_path = __DIR__ . '/../components/notifications-panel.php';
            if (file_exists($notifications_path)) {
                include $notifications_path;
            }
            ?>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-list-check"></i> Gestion des Réclamations</h2>
            <div class="d-flex gap-2">
                <a href="../FrontOffice/mesreclamations.php" class="btn btn-info" title="Voir les réclamations côté utilisateur">
                    <i class="bi bi-arrow-right-circle"></i> Voir FrontOffice
                </a>
                <button class="btn btn-warning" onclick="checkAutoEscalation()" title="Vérifier et escalader automatiquement les priorités en retard">
                    <i class="bi bi-arrow-up-circle"></i> Vérifier Escalade Auto
                </button>
            </div>
        </div>

        <?php if ($autoEscalated > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong><?= $autoEscalated ?></strong> réclamation(s) ont été escaladées automatiquement.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- 🥈 2. TABLEAU DE BORD VISUEL DES PRIORITÉS -->
        <div class="row mb-4">
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card border-danger shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title text-danger mb-1">🔴 Critiques</h6>
                        <h3 class="mb-0 text-danger"><?= $criticalCount ?></h3>
                        <small class="text-muted">Traitement immédiat requis</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title text-warning mb-1">🟠 Urgentes</h6>
                        <h3 class="mb-0 text-warning"><?= $urgentCount ?></h3>
                        <small class="text-muted">À traiter rapidement</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card border-info shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title text-info mb-1">🟡 Hautes</h6>
                        <h3 class="mb-0 text-info"><?= $highCount ?></h3>
                        <small class="text-muted">Priorité importante</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title text-primary mb-1">🔵 Normales</h6>
                        <h3 class="mb-0 text-primary"><?= $normalCount ?></h3>
                        <small class="text-muted">Traitement standard</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card border-secondary shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title text-secondary mb-1">🟢 Basses</h6>
                        <h3 class="mb-0 text-secondary"><?= $lowCount ?></h3>
                        <small class="text-muted">Peut attendre</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card border-dark shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title text-dark mb-1">📊 Total</h6>
                        <h3 class="mb-0 text-dark"><?= $totalReclamations ?></h3>
                        <small class="text-muted">Réclamations actives</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table des réclamations -->
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#ID</th>
                                <th>Titre</th>
                                <th>Utilisateur</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reclamations as $r): 
                                $priority = $r['priorite'] ?? 'normale';
                                // Déterminer la classe de priorité
                                if ($priority === 'critique' || $priority === 'urgente') {
                                    $priorityClass = 'danger';
                                } elseif ($priority === 'haute') {
                                    $priorityClass = 'warning';
                                } elseif ($priority === 'normale') {
                                    $priorityClass = 'info';
                                } else {
                                    $priorityClass = 'secondary';
                                }
                                $isOverdue = $r['est_en_retard'] ?? 0;
                            ?>
                            <tr class="<?= $isOverdue ? 'table-warning' : '' ?>">
                                <td><strong>#<?= $r['id'] ?></strong></td>
                                <td>
                                    <a href="detailadmin.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['titre']) ?></a>
                                    <?php if ($isOverdue): ?>
                                        <span class="badge bg-danger ms-1" title="En retard">⚠️ Retard</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($r['auteur'] ?? 'Inconnu') ?></td>
                                <td>
                                    <span class="badge bg-<?= $priorityClass ?>">
                                        <?= ucfirst($priority) ?>
                                    </span>
                                    <!-- 🥉 3. ACTIONS RAPIDES ET ESCALADE MANUELLE -->
                                    <?php if ($priority !== 'critique'): ?>
                                    <button class="btn btn-sm btn-outline-danger ms-1" 
                                            onclick="escalatePriority(<?= $r['id'] ?>, '<?= $priority ?>')" 
                                            title="Escalader la priorité">
                                        <i class="bi bi-arrow-up"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <select name="statut" onchange="this.form.submit()" class="form-select form-select-sm">
                                            <option value="en-attente" <?= $r['statut'] === 'en-attente' ? 'selected' : '' ?>>En attente</option>
                                            <option value="en-cours" <?= $r['statut'] === 'en-cours' ? 'selected' : '' ?>>En cours</option>
                                            <option value="resolue" <?= $r['statut'] === 'resolue' ? 'selected' : '' ?>>Résolue</option>
                                            <option value="fermee" <?= $r['statut'] === 'fermee' ? 'selected' : '' ?>>Fermée</option>
                                        </select>
                                        <input type="hidden" name="update_statut" value="1">
                                    </form>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($r['date_creation'])) ?>
                                    <?php if (isset($r['heures_ecoulees'])): ?>
                                        <br><small class="text-muted"><?= $r['heures_ecoulees'] ?>h</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="detailadmin.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 🥉 3. ACTIONS RAPIDES ET ESCALADE MANUELLE
        function getNextPriority(currentPriority) {
            const priorityHierarchy = {
                'basse': 'normale',
                'normale': 'haute',
                'haute': 'urgente',
                'urgente': 'critique',
                'critique': 'critique'
            };
            return priorityHierarchy[currentPriority] || currentPriority;
        }

        function escalatePriority(reclamationId, currentPriority) {
            const newPriority = getNextPriority(currentPriority);
            
            if (newPriority === currentPriority) {
                alert('Cette réclamation est déjà à la priorité maximale (critique)');
                return;
            }
            
            if (!confirm(`Escalader la priorité de "${currentPriority}" à "${newPriority}" ?`)) {
                return;
            }
            
            // Désactiver le bouton pendant le traitement
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            // Appel AJAX
            const formData = new URLSearchParams();
            formData.append('action', 'escalate_priority');
            formData.append('id', reclamationId);
            formData.append('new_priority', newPriority);
            
            fetch('gestionreclamations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher un message de succès
                    showNotification(`✅ Priorité escaladée: ${data.old_priority} → ${data.new_priority}`, 'success');
                    // Recharger la page après 1 seconde
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Erreur: ' + data.message);
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'escalade de la priorité');
                button.disabled = false;
                button.innerHTML = originalHTML;
            });
        }

        // 🏆 1. Vérification automatique de l'escalade
        function checkAutoEscalation() {
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Vérification...';
            
            window.location.href = 'gestionreclamations.php?check_escalation=1';
        }

        // Fonction pour afficher des notifications toast
        function showNotification(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            // Supprimer après 5 secondes
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
</body>
</html>