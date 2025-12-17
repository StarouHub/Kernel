<?php
// view/FrontOffice/dashboard.php - VERSION COMPLÈTE
session_start();

if (!isset($_SESSION['role'])) {
    header('Location: ../../indexx.php');
    exit;
}

$nom_utilisateur = $_SESSION['nom'] ?? 'Utilisateur';
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Utilisateur - Kernel Platform</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Raleway -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- CSS Personnalisé -->
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #5b21b6;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }
        
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }
        
        /* Header */
        .main-header {
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
            padding: 0;
            position: fixed;
            width: 100%;
            top: 0;
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
        }
        
        .navbar-kernel {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 4px 20px rgba(30, 58, 138, 0.3);
            padding: 1rem 0;
        }
        
        .logo-container {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .logo-icon {
            color: var(--primary-color);
            font-size: 1.8rem;
        }
        
        /* Cartes */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.8rem;
            color: white;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Boutons */
        .btn-kernel {
            background: linear-gradient(135deg, var(--accent-color), #2563eb);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-kernel:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
            color: white;
        }
        
        /* Table */
        .table-kernel {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .table-kernel thead {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .table-kernel th {
            border: none;
            padding: 15px;
            font-weight: 600;
        }
        
        .table-kernel td {
            padding: 15px;
            vertical-align: middle;
        }
        
        /* Badges */
        .badge-critique { background-color: var(--danger-color); }
        .badge-haute { background-color: var(--warning-color); }
        .badge-normale { background-color: var(--accent-color); }
        .badge-basse { background-color: #6b7280; }
        
        .badge-en-attente { background-color: var(--warning-color); }
        .badge-en-cours { background-color: #3b82f6; }
        .badge-resolue { background-color: var(--success-color); }
        .badge-fermee { background-color: #6b7280; }
        
        /* Section bienvenue */
        .welcome-section {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        /* Actions rapides */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        
        .action-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border: 2px solid transparent;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-color);
            text-decoration: none;
            color: inherit;
        }
        
        .action-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--accent-color);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .stat-number {
                font-size: 2rem;
            }
            
            .welcome-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="main-header navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <!-- Logo/Brand -->
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-hexagon-fill"></i> Kernel
            </a>

            <!-- Toggler pour mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapse content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <!-- Lien Nouvelle Réclamation -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="nouvellereclamation.php">
                            <i class="bi bi-plus-circle"></i> Nouvelle Réclamation
                        </a>
                    </li>

                    <!-- Lien Mes Réclamations -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="mesreclamations.php">
                            <i class="bi bi-file-text"></i> Mes Réclamations
                        </a>
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item position-relative">
                        <?php 
                        // Inclure le composant de notifications
                        $notifications_path = __DIR__ . '/../components/notifications-panel.php';
                        if (file_exists($notifications_path)) {
                            include $notifications_path;
                        }
                        ?>
                    </li>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($nom_utilisateur) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><h6 class="dropdown-header">Connecté en tant que <?= $role ?></h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="profil.php"><i class="bi bi-person me-2"></i>Mon Profil</a></li>
                            <li><a class="dropdown-item" href="mesreclamations.php"><i class="bi bi-list-check me-2"></i>Mes Réclamations</a></li>
                            <?php if ($role === 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../BackOffice/dashboard2.php"><i class="bi bi-gear me-2"></i>BackOffice Admin</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../FrontOffice/index.php"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container py-4" style="margin-top: 80px;">

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--accent-color);">
                        <i class="bi bi-list-ul"></i>
                    </div>
                    <div class="stat-number">8</div>
                    <p class="text-muted mb-0">Réclamations totales</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--warning-color);">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="stat-number">3</div>
                    <p class="text-muted mb-0">En attente</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--danger-color);">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="stat-number">1</div>
                    <p class="text-muted mb-0">Urgentes</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--success-color);">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-number">4</div>
                    <p class="text-muted mb-0">Résolues</p>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="quick-actions">
            <a href="nouvellereclamation.php" class="action-card">
                <div class="action-icon">
                    <i class="bi bi-plus-lg"></i>
                </div>
                <h6>Nouvelle réclamation</h6>
                <small class="text-muted">Créer une nouvelle demande</small>
            </a>
            
            <a href="mesreclamations.php" class="action-card">
                <div class="action-icon">
                    <i class="bi bi-list-check"></i>
                </div>
                <h6>Mes réclamations</h6>
                <small class="text-muted">Voir toutes mes demandes</small>
            </a>
            
            <a href="../../indexx.php" class="action-card">
                <div class="action-icon">
                    <i class="bi bi-house"></i>
                </div>
                <h6>Accueil</h6>
                <small class="text-muted">Retour à la page d'accueil</small>
            </a>
            
            <a href="../../logout.php" class="action-card">
                <div class="action-icon">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <h6>Déconnexion</h6>
                <small class="text-muted">Quitter la session</small>
            </a>
        </div>

        <!-- Réclamations récentes -->
        <div class="card border-0 shadow mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-inbox me-2"></i>Réclamations récentes</h5>
                    <a href="mesreclamations.php" class="btn btn-sm btn-outline-primary">
                        Voir tout <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-kernel">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th>Titre</th>
                                <th width="120">Type</th>
                                <th width="120">Priorité</th>
                                <th width="120">Statut</th>
                                <th width="100">Date</th>
                                <th width="80">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Réclamation 1 -->
                            <tr>
                                <td><strong>#12</strong></td>
                                <td>Problème de connexion au serveur</td>
                                <td><span class="badge bg-secondary">Technique</span></td>
                                <td>
                                    <span class="badge badge-critique">Critique</span>
                                </td>
                                <td>
                                    <span class="badge badge-en-cours">En cours</span>
                                </td>
                                <td>15/01/2024</td>
                                <td>
                                    <a href="detailreclamation.php?id=12" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Réclamation 2 -->
                            <tr>
                                <td><strong>#11</strong></td>
                                <td>Bug dans le formulaire de contact</td>
                                <td><span class="badge bg-secondary">Bug</span></td>
                                <td>
                                    <span class="badge badge-haute">Haute</span>
                                </td>
                                <td>
                                    <span class="badge badge-en-attente">En attente</span>
                                </td>
                                <td>14/01/2024</td>
                                <td>
                                    <a href="detailreclamation.php?id=11" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Réclamation 3 -->
                            <tr>
                                <td><strong>#10</strong></td>
                                <td>Suggestion d'amélioration de l'interface</td>
                                <td><span class="badge bg-secondary">Suggestion</span></td>
                                <td>
                                    <span class="badge badge-normale">Normale</span>
                                </td>
                                <td>
                                    <span class="badge badge-resolue">Résolue</span>
                                </td>
                                <td>10/01/2024</td>
                                <td>
                                    <a href="detailreclamation.php?id=10" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Réclamation 4 -->
                            <tr>
                                <td><strong>#9</strong></td>
                                <td>Problème d'affichage sur mobile</td>
                                <td><span class="badge bg-secondary">Interface</span></td>
                                <td>
                                    <span class="badge badge-basse">Basse</span>
                                </td>
                                <td>
                                    <span class="badge badge-fermee">Fermée</span>
                                </td>
                                <td>08/01/2024</td>
                                <td>
                                    <a href="detailreclamation.php?id=9" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Informations système -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Activité récente</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Réclamation #12 assignée à l'équipe technique</h6>
                                    <small class="text-muted">Aujourd'hui à 10:30</small>
                                </div>
                            </div>
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Réponse reçue sur la réclamation #8</h6>
                                    <small class="text-muted">Hier à 14:20</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Réclamation #10 marquée comme résolue</h6>
                                    <small class="text-muted">12/01/2024 à 16:45</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informations</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Session ID</small>
                            <code class="small"><?php echo session_id(); ?></code>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Rôle</small>
                            <span class="badge bg-primary"><?php echo $role; ?></span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Interface</small>
                            <span class="badge bg-success">Utilisateur</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Kernel Platform</h5>
                    <p class="mb-0 text-muted">Système de gestion des réclamations</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">© 2024 Tous droits réservés</p>
                    <small class="text-muted">Version 1.0.0</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts personnalisés -->
    <script>
        // Initialiser les tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Mettre à jour l'heure actuelle
            function updateTime() {
                const now = new Date();
                document.getElementById('current-time').textContent = 
                    now.toLocaleDateString('fr-FR') + ' ' + now.toLocaleTimeString('fr-FR');
            }
            
            setInterval(updateTime, 1000);
            updateTime();
        });
    </script>
</body>
</html>