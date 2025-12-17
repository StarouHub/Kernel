<?php
// Updated: Header with centered Forum link, role switch pills on right, improved spacing, and back button
require_once 'config.php';
require_once __DIR__ . '/../../helpers/auth.php';
$currentRole = getRole();
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
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        .btn-back:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
        }
        
        .btn-back svg {
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div style="display: flex; align-items: center; gap: 20px;">
                <a href="../view/FrontOffice/index.php" class="btn-back">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Retour
                </a>
                
                <a href="index.php?controller=sujet&action=index" class="logo">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M12 2L3 6L3 12L12 16L21 12L21 6L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <circle cx="12" cy="9" r="2" fill="currentColor"/>
                    </svg>
                    Kernel
                </a>
            </div>
            
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
                <?php endif; ?>
            </nav>
            
            <div class="header-actions">
                <?php 
                $currentRole = getRole();
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
            </div>
        </div>
    </header>