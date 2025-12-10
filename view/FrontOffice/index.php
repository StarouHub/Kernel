<?php
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../components/main-navigation.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');

$projetController = new ProjetController();
$actualiteController = new ActualiteController();

// Récupérer les données pour le dashboard
$projets = $projetController->listProjets();
$actualites = $actualiteController->listActualites();

// Statistiques
$totalProjets = count($projets);
$totalActualites = count($actualites);
$projetsRecents = array_slice($projets, 0, 3);
$actualitesRecentes = array_slice($actualites, 0, 3);

// Calculer le budget total
$budgetTotal = 0;
$budgetCollecte = 0;
foreach ($projets as $projet) {
    $budgetTotal += $projet['budget_requis'];
    $budgetCollecte += $projet['budget_actuel'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Accueil - Kernel</title>
    
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
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
        }
        
        .hero-title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            font-family: 'Raleway', sans-serif;
        }
        
        .hero-subtitle {
            font-size: 20px;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .stats-number {
            font-size: 48px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .stats-label {
            color: #6B7280;
            font-weight: 500;
        }
        
        .stats-icon {
            font-size: 32px;
            color: var(--accent-color);
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 40px;
            text-align: center;
            font-family: 'Raleway', sans-serif;
        }
        
        .project-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            margin-bottom: 20px;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .project-image {
            height: 200px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 700;
        }
        
        .project-content {
            padding: 20px;
        }
        
        .project-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .project-description {
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .project-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #9CA3AF;
        }
        
        .news-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .news-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .news-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .news-content {
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .news-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #9CA3AF;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
        }
        
        .btn-action {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
            color: white;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .quick-actions h4 {
            color: var(--dark-color);
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .action-btn {
            background: #F3F4F6;
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .action-btn:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .action-btn i {
            font-size: 24px;
        }
    </style>
</head>

<body>
    <?php echo renderMainNavigation('accueil'); ?>
    <?php echo renderChatbotWidget(); ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="hero-title">Bienvenue sur Kernel</h1>
                    <p class="hero-subtitle">
                        La plateforme collaborative pour l'innovation technologique. 
                        Découvrez, créez et financez les projets de demain.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="listeprojet.php" class="btn-action">
                            <i class="bi bi-lightbulb"></i> Explorer les projets
                        </a>
                        <a href="ajoutprojet.php" class="btn-action" style="background: var(--accent-color);">
                            <i class="bi bi-plus-circle"></i> Créer un projet
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="text-center">
                        <i class="bi bi-rocket" style="font-size: 120px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Statistiques -->
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <div class="stats-number"><?php echo $totalProjets; ?></div>
                    <div class="stats-label">Projets Actifs</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="bi bi-newspaper"></i>
                    </div>
                    <div class="stats-number"><?php echo $totalActualites; ?></div>
                    <div class="stats-label">Actualités</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="stats-number"><?php echo number_format($budgetCollecte, 0, ',', ' '); ?></div>
                    <div class="stats-label">TND Collectés</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stats-number">150+</div>
                    <div class="stats-label">Innovateurs</div>
                </div>
            </div>
        </div>
        
        <!-- Actions Rapides -->
        <div class="quick-actions">
            <h4><i class="bi bi-lightning"></i> Actions Rapides</h4>
            <div class="action-grid">
                <a href="ajoutprojet.php" class="action-btn">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nouveau Projet</span>
                </a>
                <a href="ajouterActualite.php" class="action-btn">
                    <i class="bi bi-newspaper"></i>
                    <span>Publier Actualité</span>
                </a>
                <a href="forum.php" class="action-btn">
                    <i class="bi bi-chat-dots"></i>
                    <span>Rejoindre Forum</span>
                </a>
                <a href="mes-taches.php" class="action-btn">
                    <i class="bi bi-list-task"></i>
                    <span>Mes Tâches</span>
                </a>
            </div>
        </div>
        
        <div class="row">
            <!-- Projets Récents -->
            <div class="col-lg-8">
                <h2 class="section-title">Projets Récents</h2>
                <div class="row">
                    <?php if (empty($projetsRecents)): ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="bi bi-lightbulb" style="font-size: 64px; color: #E5E7EB;"></i>
                                <h4 class="mt-3 text-muted">Aucun projet pour le moment</h4>
                                <p class="text-muted">Soyez le premier à créer un projet innovant !</p>
                                <a href="ajoutprojet.php" class="btn-action">
                                    <i class="bi bi-plus-circle"></i> Créer le premier projet
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($projetsRecents as $projet): ?>
                            <div class="col-md-6">
                                <a href="detailsprojet.php?id=<?php echo $projet['id']; ?>" class="project-card">
                                    <div class="project-image">
                                        <?php echo htmlspecialchars(substr($projet['titre'], 0, 20)); ?>
                                    </div>
                                    <div class="project-content">
                                        <h5 class="project-title"><?php echo htmlspecialchars($projet['titre']); ?></h5>
                                        <p class="project-description">
                                            <?php echo htmlspecialchars(substr($projet['description'], 0, 100)) . '...'; ?>
                                        </p>
                                        <div class="project-meta">
                                            <span><i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($projet['date_creation'])); ?></span>
                                            <span><i class="bi bi-cash"></i> <?php echo number_format($projet['budget_requis'], 0, ',', ' '); ?> TND</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="listeprojet.php" class="btn-action">
                        <i class="bi bi-arrow-right"></i> Voir tous les projets
                    </a>
                </div>
            </div>
            
            <!-- Actualités Récentes -->
            <div class="col-lg-4">
                <h2 class="section-title">Dernières Actualités</h2>
                <?php if (empty($actualitesRecentes)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-newspaper" style="font-size: 48px; color: #E5E7EB;"></i>
                        <h5 class="mt-3 text-muted">Aucune actualité</h5>
                        <p class="text-muted">Les dernières nouvelles apparaîtront ici</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($actualitesRecentes as $actu): ?>
                        <div class="news-card">
                            <h6 class="news-title"><?php echo htmlspecialchars($actu['titre']); ?></h6>
                            <p class="news-content">
                                <?php echo htmlspecialchars(substr($actu['contenu'], 0, 120)) . '...'; ?>
                            </p>
                            <div class="news-meta">
                                <span><i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($actu['date_publication'])); ?></span>
                                <span><i class="bi bi-folder"></i> <?php echo htmlspecialchars($actu['projet_titre']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="listeActualite.php" class="btn-action">
                        <i class="bi bi-arrow-right"></i> Toutes les actualités
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>