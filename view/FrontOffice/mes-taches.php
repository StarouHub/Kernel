<?php
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../components/main-navigation.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');

$projetController = new ProjetController();
$actualiteController = new ActualiteController();

// Récupérer les données de l'utilisateur (simulation avec user_id = 1)
$userId = 1;
$projets = $projetController->listProjets();
$actualites = $actualiteController->listActualites();

// Filtrer les projets et actualités de l'utilisateur (simulation)
$mesProjets = array_slice($projets, 0, 5); // Simulation
$mesActualites = array_slice($actualites, 0, 5); // Simulation
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Mes Tâches - Kernel</title>
    
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
        
        .page-header {
            background: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .page-title {
            font-size: 36px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 10px;
            font-family: 'Raleway', sans-serif;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #E5E7EB;
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }
        
        .card-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-primary-sm {
            background: var(--primary-color);
            color: white;
            border: 1px solid var(--primary-color);
        }
        
        .btn-primary-sm:hover {
            background: #1D4ED8;
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-success-sm {
            background: #10B981;
            color: white;
            border: 1px solid #10B981;
        }
        
        .btn-success-sm:hover {
            background: #059669;
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-warning-sm {
            background: var(--accent-color);
            color: white;
            border: 1px solid var(--accent-color);
        }
        
        .btn-warning-sm:hover {
            background: #E68A00;
            color: white;
            transform: translateY(-1px);
        }
        
        .item-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .item-list li {
            padding: 15px 0;
            border-bottom: 1px solid #F3F4F6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .item-list li:last-child {
            border-bottom: none;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .item-meta {
            font-size: 13px;
            color: #6B7280;
            display: flex;
            gap: 15px;
        }
        
        .item-actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .action-btn.view {
            background: #EBF8FF;
            color: var(--primary-color);
        }
        
        .action-btn.edit {
            background: #FEF3C7;
            color: var(--accent-color);
        }
        
        .action-btn.delete {
            background: #FEE2E2;
            color: #EF4444;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6B7280;
        }
        
        .empty-state i {
            font-size: 48px;
            color: #E5E7EB;
            margin-bottom: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .quick-action-card {
            background: white;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s;
        }
        
        .quick-action-card:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary-color);
            transform: translateY(-3px);
        }
        
        .quick-action-card i {
            font-size: 32px;
            margin-bottom: 15px;
            color: var(--accent-color);
        }
        
        .quick-action-card:hover i {
            color: var(--primary-color);
        }
    </style>
</head>

<body>
    <?php echo renderMainNavigation('profil'); ?>
    <?php echo renderChatbotWidget(); ?>
    
    <div class="page-header">
        <div class="container">
            <h1 class="page-title"><i class="bi bi-list-task"></i> Mes Tâches</h1>
            <p>Gérez tous vos projets et actualités depuis un seul endroit</p>
        </div>
    </div>
    
    <div class="container">
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($mesProjets); ?></div>
                <div class="stat-label">Mes Projets</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #10B981, #059669);">
                <div class="stat-number"><?php echo count($mesActualites); ?></div>
                <div class="stat-label">Mes Actualités</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #F59E0B, #E68A00);">
                <div class="stat-number">12</div>
                <div class="stat-label">Tâches en cours</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #EF4444, #DC2626);">
                <div class="stat-number">3</div>
                <div class="stat-label">Actions urgentes</div>
            </div>
        </div>
        
        <!-- Actions Rapides -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-lightning"></i> Actions Rapides
                </h3>
            </div>
            <div class="quick-actions-grid">
                <a href="ajoutprojet.php" class="quick-action-card">
                    <i class="bi bi-plus-circle"></i>
                    <h5>Nouveau Projet</h5>
                    <p class="text-muted">Créer un nouveau projet innovant</p>
                </a>
                <a href="ajouterActualite.php" class="quick-action-card">
                    <i class="bi bi-newspaper"></i>
                    <h5>Publier Actualité</h5>
                    <p class="text-muted">Partager les dernières nouvelles</p>
                </a>
                <a href="listeprojet.php" class="quick-action-card">
                    <i class="bi bi-search"></i>
                    <h5>Explorer Projets</h5>
                    <p class="text-muted">Découvrir de nouveaux projets</p>
                </a>
                <a href="forum.php" class="quick-action-card">
                    <i class="bi bi-chat-dots"></i>
                    <h5>Rejoindre Forum</h5>
                    <p class="text-muted">Participer aux discussions</p>
                </a>
            </div>
        </div>
        
        <div class="row">
            <!-- Mes Projets -->
            <div class="col-lg-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-lightbulb"></i> Mes Projets
                        </h3>
                        <div class="card-actions">
                            <a href="ajoutprojet.php" class="btn-sm-action btn-success-sm">
                                <i class="bi bi-plus"></i> Nouveau
                            </a>
                            <a href="listeprojet.php?filter=mes-projets" class="btn-sm-action btn-primary-sm">
                                <i class="bi bi-list"></i> Voir tout
                            </a>
                        </div>
                    </div>
                    
                    <?php if (empty($mesProjets)): ?>
                        <div class="empty-state">
                            <i class="bi bi-lightbulb"></i>
                            <h5>Aucun projet créé</h5>
                            <p>Commencez par créer votre premier projet innovant</p>
                            <a href="ajoutprojet.php" class="btn-sm-action btn-primary-sm">
                                <i class="bi bi-plus"></i> Créer un projet
                            </a>
                        </div>
                    <?php else: ?>
                        <ul class="item-list">
                            <?php foreach ($mesProjets as $projet): ?>
                                <li>
                                    <div class="item-info">
                                        <div class="item-title"><?php echo htmlspecialchars($projet['titre']); ?></div>
                                        <div class="item-meta">
                                            <span><i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($projet['date_creation'])); ?></span>
                                            <span><i class="bi bi-cash"></i> <?php echo number_format($projet['budget_requis'], 0, ',', ' '); ?> TND</span>
                                            <span><i class="bi bi-tag"></i> <?php echo ucfirst($projet['statut']); ?></span>
                                        </div>
                                    </div>
                                    <div class="item-actions">
                                        <a href="detailsprojet.php?id=<?php echo $projet['id']; ?>" class="action-btn view" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="modifierprojet.php?id=<?php echo $projet['id']; ?>" class="action-btn edit" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="supprimerprojet.php?id=<?php echo $projet['id']; ?>" class="action-btn delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr ?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Mes Actualités -->
            <div class="col-lg-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-newspaper"></i> Mes Actualités
                        </h3>
                        <div class="card-actions">
                            <a href="ajouterActualite.php" class="btn-sm-action btn-success-sm">
                                <i class="bi bi-plus"></i> Nouvelle
                            </a>
                            <a href="listeActualite.php?filter=mes-actualites" class="btn-sm-action btn-primary-sm">
                                <i class="bi bi-list"></i> Voir tout
                            </a>
                        </div>
                    </div>
                    
                    <?php if (empty($mesActualites)): ?>
                        <div class="empty-state">
                            <i class="bi bi-newspaper"></i>
                            <h5>Aucune actualité publiée</h5>
                            <p>Partagez les dernières nouvelles de vos projets</p>
                            <a href="ajouterActualite.php" class="btn-sm-action btn-primary-sm">
                                <i class="bi bi-plus"></i> Publier une actualité
                            </a>
                        </div>
                    <?php else: ?>
                        <ul class="item-list">
                            <?php foreach ($mesActualites as $actu): ?>
                                <li>
                                    <div class="item-info">
                                        <div class="item-title"><?php echo htmlspecialchars($actu['titre']); ?></div>
                                        <div class="item-meta">
                                            <span><i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($actu['date_publication'])); ?></span>
                                            <span><i class="bi bi-folder"></i> <?php echo htmlspecialchars($actu['projet_titre']); ?></span>
                                            <span><i class="bi bi-tag"></i> <?php echo ucfirst($actu['type']); ?></span>
                                        </div>
                                    </div>
                                    <div class="item-actions">
                                        <a href="detailsprojet.php?id=<?php echo $actu['projet_id']; ?>" class="action-btn view" title="Voir projet">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="modifierActualite.php?id=<?php echo $actu['id']; ?>" class="action-btn edit" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="supprimerActualite.php?id=<?php echo $actu['id']; ?>" class="action-btn delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr ?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>