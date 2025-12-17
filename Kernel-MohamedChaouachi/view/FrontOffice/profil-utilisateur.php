<?php
include_once(__DIR__ . '/../components/main-navigation.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Mon Profil - Kernel</title>
    
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
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin: 0 auto 20px;
        }
        
        .profile-name {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .profile-role {
            font-size: 18px;
            opacity: 0.9;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            display: block;
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            padding: 15px 0;
            border-bottom: 1px solid #F3F4F6;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .btn-edit {
            background: var(--primary-color);
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
        
        .btn-edit:hover {
            background: #1D4ED8;
            color: white;
            transform: translateY(-2px);
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .quick-link {
            background: #F3F4F6;
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s;
        }
        
        .quick-link:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .quick-link i {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--accent-color);
        }
        
        .quick-link:hover i {
            color: var(--primary-color);
        }
    </style>
</head>

<body>
    <?php echo renderMainNavigation('profil'); ?>
    <?php echo renderChatbotWidget(); ?>
    
    <div class="profile-header">
        <div class="container">
            <div class="profile-avatar">
                <i class="bi bi-person"></i>
            </div>
            <h1 class="profile-name">John Doe</h1>
            <p class="profile-role">Innovateur & Entrepreneur</p>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-number">5</span>
                    <span class="stat-label">Projets créés</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">12</span>
                    <span class="stat-label">Actualités publiées</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">3</span>
                    <span class="stat-label">Projets financés</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">150</span>
                    <span class="stat-label">Points Kernel</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="card-title">
                            <i class="bi bi-person-lines-fill"></i> Informations Personnelles
                        </h3>
                        <a href="#" class="btn-edit">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Nom complet</div>
                            <div class="info-value">John Doe</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">john.doe@kernel.tn</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Téléphone</div>
                            <div class="info-value">+216 XX XXX XXX</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Localisation</div>
                            <div class="info-value">Tunis, Tunisie</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Spécialité</div>
                            <div class="info-value">Intelligence Artificielle</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Membre depuis</div>
                            <div class="info-value">Janvier 2024</div>
                        </div>
                    </div>
                </div>
                
                <div class="profile-card">
                    <h3 class="card-title">
                        <i class="bi bi-file-text"></i> À propos
                    </h3>
                    <p class="text-muted">
                        Passionné par l'innovation technologique et l'entrepreneuriat, je développe des solutions IA 
                        pour résoudre les défis du quotidien. Toujours à la recherche de nouvelles opportunités 
                        de collaboration et d'investissement.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="profile-card">
                    <h3 class="card-title">
                        <i class="bi bi-lightning"></i> Actions Rapides
                    </h3>
                    <div class="quick-links">
                        <a href="mes-taches.php" class="quick-link">
                            <i class="bi bi-list-task"></i>
                            <div>Mes Tâches</div>
                        </a>
                        <a href="ajoutprojet.php" class="quick-link">
                            <i class="bi bi-plus-circle"></i>
                            <div>Nouveau Projet</div>
                        </a>
                        <a href="ajouterActualite.php" class="quick-link">
                            <i class="bi bi-newspaper"></i>
                            <div>Publier Actualité</div>
                        </a>
                        <a href="#" class="quick-link">
                            <i class="bi bi-gear"></i>
                            <div>Paramètres</div>
                        </a>
                    </div>
                </div>
                
                <div class="profile-card">
                    <h3 class="card-title">
                        <i class="bi bi-trophy"></i> Badges & Réalisations
                    </h3>
                    <div class="text-center py-4">
                        <i class="bi bi-award" style="font-size: 48px; color: var(--accent-color);"></i>
                        <h5 class="mt-3">Innovateur Confirmé</h5>
                        <p class="text-muted">5+ projets créés avec succès</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>