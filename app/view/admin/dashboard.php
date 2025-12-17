<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="forum-page-container">
    <div class="forum-page-header">
        <div class="forum-page-title">
            <svg class="title-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <h1>Tableau de bord Admin</h1>
        </div>
        <p class="forum-page-subtitle">Gérez votre forum depuis cette interface d'administration</p>
    </div>

    <div class="forum-layout">
        <div class="forum-sidebar-wrapper">
            <div class="forum-sidebar">
                <h3 class="sidebar-title">Navigation Admin</h3>
                <div class="category-list">
                    <a href="index.php?controller=admin&action=categories" class="category-item">
                        <div class="category-item-content">
                            <span class="category-name">Catégories</span>
                        </div>
                        <span class="category-count"><?php echo $totalCategories; ?></span>
                    </a>
                    <a href="index.php?controller=admin&action=sujets" class="category-item">
                        <div class="category-item-content">
                            <span class="category-name">Sujets</span>
                        </div>
                        <span class="category-count"><?php echo $totalSujets; ?></span>
                    </a>
                    <a href="index.php?controller=admin&action=reponses" class="category-item">
                        <div class="category-item-content">
                            <span class="category-name">Réponses</span>
                        </div>
                        <span class="category-count"><?php echo $totalReponses; ?></span>
                    </a>
                </div>
            </div>
        </div>

        <div class="forum-main-content">
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">Statistiques</h2>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div style="padding: 24px; background: linear-gradient(135deg, #2563EB, #7C3AED); border-radius: 12px; color: white;">
                        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total Catégories</div>
                        <div style="font-size: 32px; font-weight: 700;"><?php echo $totalCategories; ?></div>
                    </div>
                    <div style="padding: 24px; background: linear-gradient(135deg, #7C3AED, #9333EA); border-radius: 12px; color: white;">
                        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total Sujets</div>
                        <div style="font-size: 32px; font-weight: 700;"><?php echo $totalSujets; ?></div>
                    </div>
                    <div style="padding: 24px; background: linear-gradient(135deg, #9333EA, #C026D3); border-radius: 12px; color: white;">
                        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total Réponses</div>
                        <div style="font-size: 32px; font-weight: 700;"><?php echo $totalReponses; ?></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">Actions rapides</h2>
                </div>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="index.php?controller=categorie&action=create" class="button-gradient">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nouvelle Catégorie
                    </a>
                    <a href="index.php?controller=sujet&action=create" class="button-gradient">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nouveau Sujet
                    </a>
                    <a href="index.php?controller=forum&action=index&set_mode=user" class="button-gradient" style="background: linear-gradient(135deg, #6B7280, #9CA3AF);">
                        Retour au Forum
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

