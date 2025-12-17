<?php
/**
 * Composant de navigation principale pour le FrontOffice
 * Usage: include_once(__DIR__ . '/../components/main-navigation.php');
 *        echo renderMainNavigation($currentPage);
 * 
 * @param string $currentPage Page actuelle pour highlighting
 */
function renderMainNavigation($currentPage = '') {
    ob_start();
    ?>
    <style>
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
            text-decoration: none;
            font-family: 'Raleway', sans-serif;
            padding: 18px 0;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        
        .navbar-brand i {
            font-size: 28px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        
        .navbar-nav {
            gap: 4px;
            align-items: center;
        }
        
        .navbar-collapse {
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-nav.me-auto {
            margin: 0 auto !important;
            flex-grow: 0;
            justify-content: center;
            gap: 8px;
            padding: 0 40px;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            color: white !important;
            font-weight: 600;
            padding: 14px 16px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 10px;
            margin: 6px 3px;
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            letter-spacing: 0.025em;
            white-space: nowrap;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: #ffffff;
            border-radius: 2px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(-50%);
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
            transform: translateY(-2px);
            backdrop-filter: blur(10px);
        }
        
        .nav-link:hover::before {
            width: 80%;
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff !important;
            backdrop-filter: blur(10px);
        }
        
        .nav-link.active::before {
            width: 80%;
        }
        
        .dropdown-menu {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 10px 0;
            margin-top: 5px;
            min-width: 220px;
        }
        
        .dropdown-item {
            padding: 12px 20px;
            color: #374151;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .dropdown-item:hover {
            background: #F3F4F6;
            color: #0A4FFF;
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            width: 16px;
            text-align: center;
            color: #6B7280;
        }
        
        .dropdown-item:hover i {
            color: #0A4FFF;
        }
        
        .dropdown-divider {
            margin: 8px 0;
            border-color: #E5E7EB;
        }
        
        .badge-count {
            background: #F59E0B;
            color: white;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
        }
        
        .btn-profile {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-size: 0.95rem;
            flex-shrink: 0;
            margin-left: 20px;
        }
        
        .btn-profile:hover {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        .navbar-toggler {
            border: none;
            padding: 8px 12px;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(255, 255, 255, 0.95);
                margin-top: 15px;
                border-radius: 16px;
                padding: 24px;
                backdrop-filter: blur(15px);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            }
            
            .navbar-nav.me-auto {
                padding: 0;
                margin: 0 !important;
            }
            
            .nav-link {
                color: #374151 !important;
                margin: 4px 0;
                padding: 12px 16px !important;
                border-radius: 12px;
            }
            
            .nav-link:hover {
                background: #F3F4F6;
                color: #0A4FFF !important;
            }
            
            .nav-link.active {
                background: linear-gradient(135deg, rgba(10, 79, 255, 0.1), rgba(74, 168, 255, 0.1));
                color: #0A4FFF !important;
            }
            
            .btn-profile {
                margin: 20px 0 0 0;
                justify-content: center;
                background: linear-gradient(135deg, #0A4FFF, #4AA8FF);
                border: none;
            }
            
            .btn-profile:hover {
                background: linear-gradient(135deg, #0940E6, #3A95E6);
                transform: translateY(-1px);
            }
        }
        
        .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: #EF4444;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
    
    <header class="main-header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <!-- Logo à gauche avec espacement -->
                <a class="navbar-brand" href="index.php">
                    <i class="bi bi-hexagon-fill"></i> Kernel
                </a>
                
                <!-- Bouton mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Menu principal centré -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage == 'accueil') ? 'active' : ''; ?>" href="index.php">
                                <i class="bi bi-house"></i> Accueil
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo (in_array($currentPage, ['projets', 'projet-liste', 'projet-details', 'projet-ajout', 'projet-modifier'])) ? 'active' : ''; ?>" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-lightbulb"></i> Projets
                                <span class="badge-count" id="projets-count">0</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="listeprojet.php">
                                    <i class="bi bi-list-ul"></i> Voir tous les projets
                                </a></li>
                                <li><a class="dropdown-item" href="ajoutprojet.php">
                                    <i class="bi bi-plus-circle"></i> Créer un projet
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="listeprojet.php?filter=mes-projets">
                                    <i class="bi bi-person-workspace"></i> Mes projets
                                </a></li>
                                <li><a class="dropdown-item" href="listeprojet.php?filter=favoris">
                                    <i class="bi bi-heart"></i> Projets favoris
                                </a></li>
                            </ul>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo (in_array($currentPage, ['actualites', 'actualite-liste', 'actualite-ajout', 'actualite-modifier'])) ? 'active' : ''; ?>" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-newspaper"></i> Actualités
                                <span class="badge-count" id="actualites-count">0</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="listeActualite.php">
                                    <i class="bi bi-list-ul"></i> Toutes les actualités
                                </a></li>
                                <li><a class="dropdown-item" href="ajouterActualite.php">
                                    <i class="bi bi-plus-circle"></i> Publier une actualité
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="searchActualites.php">
                                    <i class="bi bi-search"></i> Rechercher par projet
                                </a></li>
                                <li><a class="dropdown-item" href="listeActualite.php?filter=mes-actualites">
                                    <i class="bi bi-person-lines-fill"></i> Mes actualités
                                </a></li>
                            </ul>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo ($currentPage == 'evenements') ? 'active' : ''; ?>" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-calendar-event"></i> Événements
                                <div class="notification-dot"></div>
                            </a>
                            <ul class="dropdown-menu">
<<<<<<< HEAD
                                <li><a class="dropdown-item" href="../../index.php">
                                    <i class="bi bi-calendar3"></i> evenements page
=======
                                <li><a class="dropdown-item" href="evenements-list.php">
                                    <i class="bi bi-calendar3"></i> Tous les événements
                                </a></li>
                                <li><a class="dropdown-item" href="evenement-creer.php">
                                    <i class="bi bi-calendar-plus"></i> Créer un événement
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="mes-evenements.php">
                                    <i class="bi bi-calendar-check"></i> Mes événements
                                </a></li>
                                <li><a class="dropdown-item" href="evenements-participes.php">
                                    <i class="bi bi-people"></i> Événements participés
>>>>>>> origin/MohamedChaouachi
                                </a></li>
                            </ul>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo ($currentPage == 'investissement') ? 'active' : ''; ?>" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-cash-coin"></i> Investissement
                            </a>
                            <ul class="dropdown-menu">
<<<<<<< HEAD
                                
                                <li><a class="dropdown-item" href="../FrontOffice/investissement.php">
                                    <i class="bi bi-wallet2"></i> Mes investissements
                                </a></li>
=======
                                <li><a class="dropdown-item" href="investissements.php">
                                    <i class="bi bi-graph-up-arrow"></i> Opportunités d'investissement
                                </a></li>
                                <li><a class="dropdown-item" href="mes-investissements.php">
                                    <i class="bi bi-wallet2"></i> Mes investissements
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="portefeuille.php">
                                    <i class="bi bi-briefcase"></i> Mon portefeuille
                                </a></li>
                                <li><a class="dropdown-item" href="historique-transactions.php">
                                    <i class="bi bi-clock-history"></i> Historique des transactions
                                </a></li>
>>>>>>> origin/MohamedChaouachi
                            </ul>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo ($currentPage == 'forum') ? 'active' : ''; ?>" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-chat-square-dots"></i> Forum
                            </a>
                            <ul class="dropdown-menu">
<<<<<<< HEAD
                                <li><a class="dropdown-item" href="../../public/index.php">
                                    <i class="bi bi-chat-left-text"></i> forum page
                                </a></li>
                                
=======
                                <li><a class="dropdown-item" href="forum.php">
                                    <i class="bi bi-chat-left-text"></i> Discussions générales
                                </a></li>
                                <li><a class="dropdown-item" href="forum-aide.php">
                                    <i class="bi bi-question-circle"></i> Aide & Support
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="mes-discussions.php">
                                    <i class="bi bi-person-lines-fill"></i> Mes discussions
                                </a></li>
                                <li><a class="dropdown-item" href="forum-creer.php">
                                    <i class="bi bi-plus-circle"></i> Nouvelle discussion
                                </a></li>
>>>>>>> origin/MohamedChaouachi
                            </ul>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo ($currentPage == 'reclamation') ? 'active' : ''; ?>" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-exclamation-triangle"></i> Réclamations
                            </a>
                            <ul class="dropdown-menu">
<<<<<<< HEAD
                                <li><a class="dropdown-item" href="../../indexx.php">
                                    <i class="bi bi-plus-circle"></i>  réclamation page
=======
                                <li><a class="dropdown-item" href="reclamation-creer.php">
                                    <i class="bi bi-plus-circle"></i> Nouvelle réclamation
                                </a></li>
                                <li><a class="dropdown-item" href="mes-reclamations.php">
                                    <i class="bi bi-list-check"></i> Mes réclamations
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="faq.php">
                                    <i class="bi bi-question-circle"></i> FAQ
                                </a></li>
                                <li><a class="dropdown-item" href="contact.php">
                                    <i class="bi bi-envelope"></i> Nous contacter
                                </a></li>
>>>>>>> origin/MohamedChaouachi
                            </ul>
                        </li>
                    </ul>
                    
                    <!-- Bouton Profil à droite avec espacement -->
                    <div class="d-flex align-items-center">
                        <div class="nav-item dropdown">
                            <a class="btn-profile dropdown-toggle" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> Profil
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profil-utilisateur.php">
                                    <i class="bi bi-person"></i> Mon profil
                                </a></li>
                                <li><a class="dropdown-item" href="mes-taches.php">
                                    <i class="bi bi-list-task"></i> Mes tâches
                                </a></li>
                                <li><a class="dropdown-item" href="notifications.php">
                                    <i class="bi bi-bell"></i> Notifications
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="parametres.php">
                                    <i class="bi bi-gear"></i> Paramètres
                                </a></li>
                                <li><a class="dropdown-item" href="logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    
    <script>
        // Charger les compteurs dynamiquement
        document.addEventListener('DOMContentLoaded', function() {
            loadCounts();
        });
        
        async function loadCounts() {
            try {
                // Charger le nombre de projets
                const projetsResponse = await fetch('../../api/counts.php?type=projets');
                const projetsData = await projetsResponse.json();
                if (projetsData.success) {
                    document.getElementById('projets-count').textContent = projetsData.count;
                }
                
                // Charger le nombre d'actualités
                const actualitesResponse = await fetch('../../api/counts.php?type=actualites');
                const actualitesData = await actualitesResponse.json();
                if (actualitesData.success) {
                    document.getElementById('actualites-count').textContent = actualitesData.count;
                }
            } catch (error) {
                console.log('Erreur lors du chargement des compteurs:', error);
            }
        }
    </script>
    <?php
    return ob_get_clean();
}
?>