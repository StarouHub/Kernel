<?php
session_start();
require_once '../../config.php';

// Si pas connecté → retour à connexion
if (!isset($_SESSION['user'])) {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

$user = $_SESSION['user'];
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../../controller/userController.php');
include_once(__DIR__ . '/../components/main-navigation.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');

// Gestion de l'authentification
$userController = new userController();
$currentUser = $userController->getCurrentUser();
$isLoggedIn = $userController->isLoggedIn();

// Vérifier si l'utilisateur est administrateur
$isAdmin = $isLoggedIn && isset($currentUser['role']) && $currentUser['role'] === 'admin';

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
            --primary-color: #0A4FFF;
            --secondary-color: #4AA8FF;
            --accent-color: #2563EB;
            --success-color: #10B981;
            --warning-color: #F59E0B;
            --danger-color: #EF4444;
            --dark-color: #1F2937;
            --light-bg: #F8FAFC;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: var(--light-bg);
            padding-top: 80px;
            line-height: 1.6;
            color: var(--dark-color);
        }

        /* Header avec boutons sur la gauche */
        .left-header {
            position: fixed;
            top: 20px;
            left: 40px;
            z-index: 1040;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        /* Bouton Déconnexion */
        .btn-logout {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            font-size: 0.95rem;
            border: none;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            position: fixed;
            top: 20px;
            right: 40px;
            z-index: 1040;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
            color: white;
            background: linear-gradient(135deg, #DC2626, #B91C1C);
        }

        /* Bouton Administration / Mon Profil */
        .btn-user-action {
            background: linear-gradient(135deg, #0A4FFF, #4AA8FF);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            font-size: 0.95rem;
            border: none;
            box-shadow: 0 4px 15px rgba(10, 79, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-user-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition);
        }

        .btn-user-action:hover::before {
            left: 100%;
        }

        .btn-user-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(10, 79, 255, 0.4);
            color: white;
        }

        /* Variante verte pour Mon Profil */
        .btn-user-action.profile {
            background: linear-gradient(135deg, #10B981, #059669);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-user-action.profile:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #059669, #047857);
        }

        /* Variante rouge pour Administration */
        .btn-user-action.admin {
            background: linear-gradient(135deg, #DC2626, #B91C1C);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        .btn-user-action.admin:hover {
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
            background: linear-gradient(135deg, #B91C1C, #991B1B);
        }

        /* Bouton Admin FAB (fixe en bas) */
        .admin-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1050;
            background: linear-gradient(135deg, #DC2626, #B91C1C);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
            transition: var(--transition);
            text-decoration: none;
            font-size: 1.5rem;
        }

        .admin-fab:hover {
            transform: scale(1.1) rotate(15deg);
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.5);
            color: white;
        }

        @media (max-width: 992px) {
            .left-header {
                left: 20px;
                gap: 10px;
            }
            
            .btn-logout {
                right: 20px;
            }
        }

        @media (max-width: 768px) {
            .left-header {
                left: 15px;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .btn-user-action,
            .btn-logout {
                padding: 10px 18px;
                font-size: 0.875rem;
            }

            .btn-logout {
                top: 15px;
                right: 15px;
            }

            .admin-fab {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
                font-size: 1.3rem;
            }
        }

        @media (max-width: 576px) {
            .left-header {
                left: 10px;
                top: 15px;
            }
            
            .btn-user-action,
            .btn-logout {
                padding: 8px 16px;
                font-size: 0.825rem;
            }
            
            .btn-logout {
                top: 15px;
                right: 10px;
            }
        }
        
        .hero-section {
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
            color: white;
            padding: 100px 0;
            margin-bottom: 60px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        
        .hero-title {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 24px;
            font-family: 'Raleway', sans-serif;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.3rem);
            opacity: 0.95;
            margin-bottom: 40px;
            font-weight: 400;
            max-width: 600px;
            line-height: 1.6;
        }
        
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 32px 24px;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            margin-bottom: 32px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transition: var(--transition);
        }
        
        .stats-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .stats-card:hover::before {
            transform: scaleX(1);
        }
        
        .stats-number {
            font-size: clamp(2.5rem, 4vw, 3rem);
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
            font-family: 'Raleway', sans-serif;
        }
        
        .stats-label {
            color: #64748B;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.025em;
        }
        
        .stats-icon {
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--accent-color), #F97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            display: block;
        }
        
        .section-title {
            font-size: clamp(1.75rem, 3vw, 2.25rem);
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 48px;
            text-align: center;
            font-family: 'Raleway', sans-serif;
            letter-spacing: -0.025em;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
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
            padding: 16px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            font-size: 1rem;
            letter-spacing: 0.025em;
            position: relative;
            overflow: hidden;
        }
        
        .btn-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition);
        }
        
        .btn-action:hover::before {
            left: 100%;
        }
        
        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(10, 79, 255, 0.4);
            color: white;
        }
        
        .btn-action.secondary {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        }
        
        .btn-action.secondary:hover {
            box-shadow: 0 8px 25px rgba(74, 168, 255, 0.4);
        }
        
        .quick-actions {
            background: white;
            border-radius: var(--border-radius);
            padding: 40px 32px;
            box-shadow: var(--card-shadow);
            margin-bottom: 48px;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        
        .quick-actions h4 {
            color: var(--dark-color);
            margin-bottom: 32px;
            font-weight: 700;
            font-size: 1.5rem;
            font-family: 'Raleway', sans-serif;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .quick-actions h4 i {
            color: var(--primary-color);
            font-size: 1.75rem;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            align-items: stretch;
        }
        
        .action-btn {
            background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
            border: 2px solid #E2E8F0;
            border-radius: 12px;
            padding: 28px 20px;
            text-align: center;
            text-decoration: none;
            color: var(--dark-color);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            position: relative;
            overflow: hidden;
        }
        
        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: var(--transition);
        }
        
        .action-btn:hover::before {
            left: 100%;
        }
        
        .action-btn:hover {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(10, 79, 255, 0.05) 0%, rgba(74, 168, 255, 0.05) 100%);
            color: var(--primary-color);
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .action-btn i {
            font-size: 2rem;
            transition: var(--transition);
        }
        
        .action-btn:hover i {
            transform: scale(1.1);
        }
        
        .action-btn span {
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.025em;
        }
        
        .action-btn.admin {
            border-color: #DC2626;
        }
        
        .action-btn.admin:hover {
            border-color: #B91C1C;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.05) 0%, rgba(185, 28, 28, 0.1) 100%);
            color: #B91C1C;
        }
        
        .action-btn.admin i {
            color: #DC2626;
        }
        
        @media (max-width: 992px) {
            .action-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
                text-align: center;
            }
            
            .stats-card {
                margin-bottom: 20px;
            }
            
            .quick-actions {
                padding: 24px 20px;
                margin-bottom: 32px;
            }
            
            .action-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
            
            .action-btn {
                padding: 20px 16px;
            }
            
            .section-title {
                margin-bottom: 32px;
            }
        }
        
        @media (max-width: 576px) {
            .action-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .hero-section {
                padding: 40px 0;
            }
            
            .quick-actions h4 {
                font-size: 1.25rem;
                margin-bottom: 24px;
            }
        }
        
        .stats-card:nth-child(1) .stats-icon { color: var(--primary-color); }
        .stats-card:nth-child(2) .stats-icon { color: var(--secondary-color); }
        .stats-card:nth-child(3) .stats-icon { color: var(--accent-color); }
        .stats-card:nth-child(4) .stats-icon { color: var(--success-color); }
        
        .action-btn:focus,
        .btn-action:focus {
            outline: 3px solid rgba(10, 79, 255, 0.5);
            outline-offset: 2px;
        }
        
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    
</head>

<body>
    <!-- Header avec boutons sur la gauche -->
    <!-- Header avec boutons sur la gauche -->
    <div class="left-header">
        <?php if ($user['role'] === 'admin'): ?>
            <a href="../../../Kernel/view/BackOffice/admin.php" class="btn-user-action admin">
                <i class="bi bi-shield-lock"></i>
                <span>Administration</span>
            </a>
        <?php else: ?>
            <a href="../../../kernel/view/FrontOffice/profile.php" class="btn-user-action profile">
                <i class="bi bi-person-circle"></i>
                <span>Mon Profil</span>
            </a>
        <?php endif; ?>
    </div>
    
    
    
    <?php echo renderMainNavigation('accueil'); ?>
    <?php echo renderChatbotWidget(); ?>

    <!-- Bouton Admin fixe (visible seulement pour les admins) -->
    <?php if ($isAdmin): ?>
        <a href="../../../Kernel/view/BackOffice/admin.php" class="admin-fab" title="Panneau d'administration">
            <i class="bi bi-shield-lock"></i>
        </a>
    <?php endif; ?>
// ... existing code ...>
    
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
                        <a href="ajoutprojet.php" class="btn-action secondary">
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
                <?php if ($isAdmin): ?>
                    <a href="../../../Kernel/view/BackOffice/admin.php" class="action-btn admin">
                        <i class="bi bi-shield-lock"></i>
                        <span>Administration</span>
                    </a>
                <?php endif; ?>
                
                <a href="ajoutprojet.php" class="action-btn">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nouveau Projet</span>
                </a>
                <a href="ajouterActualite.php" class="action-btn">
                    <i class="bi bi-newspaper"></i>
                    <span>Publier Actualité</span>
                </a>
                <a href="../../../vv/public/index.php" class="action-btn">
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