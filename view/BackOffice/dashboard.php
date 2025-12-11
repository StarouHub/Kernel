<?php
// view/BackOffice/dashboard.php - VERSION CORRIGÉE
// Chemin correct pour init.php
require_once __DIR__ . '/../../init.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Rediriger vers l'accueil
    header('Location: ../../index.php?message=Accès réservé aux administrateurs');
    exit;
}

// Données simulées pour TEST (remplace par tes vrais contrôleurs)
$stats = [
    'total' => 12,
    'en_attente' => 4,
    'resolues' => 6,
    'urgentes' => 2
];

$reclamations = [
    ['id' => 1, 'titre' => 'Problème de connexion', 'auteur' => 'Jean Dupont', 'priorite' => 'urgente', 'statut' => 'en-attente', 'date_creation' => '2024-01-15'],
    ['id' => 2, 'titre' => 'Bug formulaire', 'auteur' => 'Marie Curie', 'priorite' => 'haute', 'statut' => 'en-cours', 'date_creation' => '2024-01-14'],
    ['id' => 3, 'titre' => 'Suggestion amélioration', 'auteur' => 'Paul Martin', 'priorite' => 'normale', 'statut' => 'resolue', 'date_creation' => '2024-01-10'],
    ['id' => 4, 'titre' => 'Problème de facturation', 'auteur' => 'Sophie Bernard', 'priorite' => 'urgente', 'statut' => 'en-attente', 'date_creation' => '2024-01-13'],
    ['id' => 5, 'titre' => 'Demande de fonctionnalité', 'auteur' => 'Luc Petit', 'priorite' => 'normale', 'statut' => 'en-cours', 'date_creation' => '2024-01-12'],
    ['id' => 6, 'titre' => 'Erreur système', 'auteur' => 'Admin Test', 'priorite' => 'haute', 'statut' => 'resolue', 'date_creation' => '2024-01-09']
];

$nom_admin = $_SESSION['nom'] ?? 'Administrateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration - Kernel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --header-bg: linear-gradient(135deg, #1e3a8a 0%, #5b21b6 100%);
            --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
        }
        .header {
            background: var(--header-bg);
            color: white;
            padding: 2rem 0;
            box-shadow: 0 10px 30px rgba(30,58,138,0.3);
        }
        .logo-container {
            width: 80px; height: 80px; background: white; border-radius: 20px; 
            display: flex; align-items: center; justify-content: center; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
        }
        .stat-card:hover { transform: translateY(-8px); }
        .stat-icon {
            width: 70px; height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.2rem;
            color: white;
        }
        .stat-number {
            font-size: 2.8rem;
            font-weight: 800;
            margin: 0.5rem 0;
        }
        .content-row {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2rem;
        }
        @media (max-width: 1200px) {
            .content-row {
                grid-template-columns: 1fr;
            }
        }
        .card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        .card-header {
            background: #f1f5f9;
            padding: 1.5rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .action-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.3rem 1.8rem;
            background: white;
            border: none;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            width: 100%;
            text-align: left;
            font-weight: 600;
            margin-bottom: 1rem;
            transition: all 0.3s;
            cursor: pointer;
        }
        .action-btn:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 35px rgba(59,130,246,0.2);
            text-decoration: none;
        }
        .action-icon {
            width: 55px; height: 55px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: white;
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
        .table th {
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }
        .table td {
            vertical-align: middle;
            padding: 1rem;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-4">
                    <div class="logo-container">
                        <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <div>
                        <h2 class="mb-0 fw-bold">Administration Kernel</h2>
                        <p class="mb-0 opacity-90">Bienvenue, <?php echo htmlspecialchars($nom_admin); ?></p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="btn btn-light position-relative rounded-pill px-4 py-2" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <?php if (getUnreadNotificationsCount() > 0): ?>
                                <span class="notification-count"><?php echo getUnreadNotificationsCount(); ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-0" style="width: 360px;">
                            <div class="bg-primary text-white p-3 rounded-top">
                                <h6 class="mb-0">Notifications</h6>
                            </div>
                            <div class="p-3" style="max-height: 300px; overflow-y: auto;">
                                <?php foreach (array_slice($_SESSION['notifications'] ?? [], 0, 5) as $n): ?>
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="fw-semibold small"><?php echo htmlspecialchars($n['message']); ?></div>
                                        <div class="text-muted" style="font-size: 0.8rem;">
                                            <?php echo date('d/m/Y H:i', strtotime($n['date'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <a href="../../logout.php" class="btn btn-outline-light rounded-pill px-4">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Message de debug -->
        <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle"></i> Interface administrateur fonctionnelle - Session: <?php echo session_id(); ?>
        </div>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #3b82f6;"><i class="bi bi-list-ul"></i></div>
                <div class="stat-number text-primary"><?php echo $stats['total']; ?></div>
                <div class="text-muted fw-semibold">Total Réclamations</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #ef4444;"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-number text-danger"><?php echo $stats['urgentes']; ?></div>
                <div class="text-muted fw-semibold">Urgentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #f59e0b;"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-number text-warning"><?php echo $stats['en_attente']; ?></div>
                <div class="text-muted fw-semibold">En attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #10b981;"><i class="bi bi-check-circle"></i></div>
                <div class="stat-number text-success"><?php echo $stats['resolues']; ?></div>
                <div class="text-muted fw-semibold">Résolues</div>
            </div>
        </div>

        <!-- Contenu -->
        <div class="content-row">
            <!-- Réclamations récentes -->
            <div class="card">
                <div class="card-header">Réclamations récentes</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Titre</th>
                                <th>Utilisateur</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reclamations as $r): ?>
                            <tr>
                                <td><strong>#<?php echo $r['id']; ?></strong></td>
                                <td>
                                    <a href="detailadmin.php?id=<?php echo $r['id']; ?>">
                                        <?php echo htmlspecialchars($r['titre']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($r['auteur']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $r['priorite'] == 'urgente' ? 'danger' : 
                                        ($r['priorite'] == 'haute' ? 'warning' : 'info'); 
                                    ?>">
                                        <?php echo ucfirst($r['priorite']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $r['statut'] == 'en-attente' ? 'warning' : 
                                        ($r['statut'] == 'en-cours' ? 'info' : 'success'); 
                                    ?>">
                                        <?php echo ucfirst(str_replace('-', ' ', $r['statut'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($r['date_creation'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <a href="gestionreclamations.php" class="btn btn-primary">
                        <i class="bi bi-list-check"></i> Gérer toutes les réclamations
                    </a>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">Actions rapides</div>
                <div class="p-4">
                    <a href="gestionreclamations.php" class="action-btn text-decoration-none">
                        <div class="action-icon" style="background: #3b82f6;">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Gestion des Réclamations</div>
                            <small class="text-muted">Voir et gérer toutes les réclamations</small>
                        </div>
                    </a>
                    
                    <a href="statistiques.php" class="action-btn text-decoration-none">
                        <div class="action-icon" style="background: #10b981;">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Statistiques</div>
                            <small class="text-muted">Analyser les données et tendances</small>
                        </div>
                    </a>
                    
                    <a href="../../prioritymanager.php" class="action-btn text-decoration-none">
                        <div class="action-icon" style="background: #8b5cf6;">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Priority Manager</div>
                            <small class="text-muted">Gestion intelligente des priorités IA</small>
                        </div>
                    </a>
                    
                    <a href="../../index.php" class="action-btn text-decoration-none">
                        <div class="action-icon" style="background: #6b7280;">
                            <i class="bi bi-house"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Retour à l'accueil</div>
                            <small class="text-muted">Page de sélection des interfaces</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Informations de session -->
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Informations de session</h5>
            </div>
            <div class="card-body">
                <p><strong>Session ID :</strong> <?php echo session_id(); ?></p>
                <p><strong>Rôle :</strong> <?php echo $_SESSION['role'] ?? 'non défini'; ?></p>
                <p><strong>Nom :</strong> <?php echo $_SESSION['nom'] ?? 'non défini'; ?></p>
                <p><strong>User ID :</strong> <?php echo $_SESSION['user_id'] ?? 'non défini'; ?></p>
                <p><strong>Chemin du fichier :</strong> <?php echo __FILE__; ?></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>