<?php
// Updated: Header with centered Forum link, role switch pills on right, improved spacing
require_once __DIR__ . '/../../helpers/auth.php';

$currentRole = getRole();
$isAdminInKernelDB = isAdminInKernelDB(); // Vérifier si l'utilisateur est admin dans Kernel DB
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Forum'; ?></title>
    <link rel="stylesheet" href="assets/css/forum.css">
    <link rel="stylesheet" href="assets/css/ajout-sujet.css">
    <link rel="stylesheet" href="assets/css/discussion.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&family=Raleway:wght@600;700&display=swap" rel="stylesheet">
    <style>
        .return-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 9999;
            background-color: #dc3545;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .return-button:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
            color: white;
            text-decoration: none;
        }
        .return-button svg {
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- Red Return Button - Top Left -->
    <a href="../../Kernel-MohamedChaouachi/view/FrontOffice/index.php" class="return-button">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Retour
    </a>
    
    <header class="header">
        <div class="header-container">
            <a href="index.php?controller=sujet&action=index" class="logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                    <path d="M12 2L3 6L3 12L12 16L21 12L21 6L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <circle cx="12" cy="9" r="2" fill="currentColor"/>
                </svg>
                Kernel
            </a>
            <nav class="navmenu">
                <?php if ($currentRole === 'admin'): ?>
                    <a href="index.php?controller=admin&action=dashboard" 
                       class="<?php echo (isset($_GET['controller']) && $_GET['controller'] === 'admin' && isset($_GET['action']) && $_GET['action'] === 'dashboard') ? 'active' : ''; ?>">
                        Dashboard
                    </a>
                    <a href="index.php?controller=admin&action=categories" 
                       class="<?php echo (isset($_GET['controller']) && $_GET['controller'] === 'admin' && isset($_GET['action']) && $_GET['action'] === 'categories') ? 'active' : ''; ?>">
                        Catégories
                    </a>
                    <a href="index.php?controller=admin&action=sujets" 
                       class="<?php echo (isset($_GET['controller']) && $_GET['controller'] === 'admin' && isset($_GET['action']) && $_GET['action'] === 'sujets') ? 'active' : ''; ?>">
                        Sujets
                    </a>
                    <a href="index.php?controller=admin&action=reponses" 
                       class="<?php echo (isset($_GET['controller']) && $_GET['controller'] === 'admin' && isset($_GET['action']) && $_GET['action'] === 'reponses') ? 'active' : ''; ?>">
                        Réponses
                    </a>
                <?php else: ?>
                    <a href="index.php?controller=sujet&action=index" 
                       class="<?php echo (isset($_GET['controller']) && $_GET['controller'] === 'sujet' && (!isset($_GET['action']) || $_GET['action'] === 'index')) ? 'active' : ''; ?>">
                        Forum
                    </a>
                    <a href="#" class="">Profil</a>
                <?php endif; ?>
            </nav>
            <div class="header-actions">
                <?php 
                // Afficher le bouton seulement si l'utilisateur est admin dans Kernel DB
                if ($isAdminInKernelDB): 
                    // Store current URL in session for redirect after role switch
                    $currentUrl = $_SERVER['REQUEST_URI'];
                    // Remove query parameters that shouldn't be preserved
                    $cleanUrl = preg_replace('/[?&](controller|action|switch_role|set_mode)=[^&]*/', '', $currentUrl);
                    $cleanUrl = rtrim($cleanUrl, '?&');
                    if (empty($cleanUrl) || $cleanUrl === '/') {
                        $cleanUrl = 'index.php?controller=sujet&action=index';
                    }
                    $_SESSION['redirect_after_role_switch'] = $cleanUrl;
                    
                    // Build the switch URL
                    $switchUrl = 'index.php?controller=role&action=switchRole';
                    ?>
                    <?php if ($currentRole === 'user'): ?>
                        <a href="<?php echo htmlspecialchars($switchUrl); ?>" class="btn-role-switch">
                            🛠 Passer en Admin
                        </a>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($switchUrl); ?>" class="btn-role-switch">
                            👤 Passer en User
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </header>