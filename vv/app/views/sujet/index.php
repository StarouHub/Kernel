<?php
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/view_helpers.php';
$pageTitle = 'Forum Communa';
$currentRole = getRole();
$currentUserId = getUserId();
$currentCategorieId = $_GET['categorie_id'] ?? null;

// Generate consistent color for each category based on ID or use stored color
function getCategoryColor($categorie) {
    if (!empty($categorie['color'])) {
        return $categorie['color'];
    }
    $colors = ['#2563EB', '#7C3AED', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];
    return $colors[$categorie['id'] % count($colors)];
}

require_once __DIR__ . '/../layout/header.php';
?>

<div class="forum-page-container">
    <div class="forum-page-header">
        <div class="forum-page-title">
            <svg class="title-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h1>Forum Communa</h1>
        </div>
        <p class="forum-page-subtitle">Échangez avec la communauté des internautes</p>
    </div>

    <div class="forum-layout">
        <aside class="forum-sidebar-wrapper">
            <div class="forum-sidebar">
                <a href="index.php?controller=sujet&action=create" class="btn-new-topic" aria-label="Créer un nouveau sujet">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Nouveau Sujet
                </a>
                
                <div class="sidebar-title">
                    <span>Catégories</span>
                </div>
                
                <div class="category-list">
                    <div class="category-item <?php echo !$currentCategorieId ? 'active' : ''; ?>" 
                         onclick="window.location='index.php?controller=sujet&action=index'"
                         role="button"
                         tabindex="0"
                         aria-label="Voir tous les sujets">
                        <div class="left">
                            <span class="category-name">Toutes</span>
                        </div>
                        <span class="category-count"><?php echo isset($sujets) ? count($sujets) : 0; ?></span>
                    </div>
                    
                    <?php foreach ($categories as $categorie): ?>
                        <?php 
                        $count = $categoryCounts[$categorie['id']] ?? 0;
                        $isActive = $currentCategorieId == $categorie['id'];
                        $categoryColor = getCategoryColor($categorie);
                        ?>
                        <div class="category-item <?php echo $isActive ? 'active' : ''; ?>" 
                             onclick="window.location='index.php?controller=sujet&action=index&categorie_id=<?php echo $categorie['id']; ?>'"
                             role="button"
                             tabindex="0"
                             aria-label="Voir les sujets de <?php echo htmlspecialchars($categorie['name']); ?>">
                            <div class="left">
                                <span class="color-dot" style="background-color: <?php echo htmlspecialchars($categoryColor); ?>"></span>
                                <span class="category-name"><?php echo htmlspecialchars($categorie['name']); ?></span>
                            </div>
                            <span class="category-count"><?php echo $count; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <main class="forum-main-content">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (empty($sujets)): ?>
                <div class="topic-card empty-state">
                    <p>Aucun sujet trouvé</p>
                </div>
            <?php else: ?>
                <?php foreach ($sujets as $sujet): ?>
                    <?php
                    $sujetCategorieId = $sujet['categorie_id'] ?? null;
                    $badgeColor = '#2563EB';
                    if ($sujetCategorieId) {
                        foreach ($categories as $cat) {
                            if ($cat['id'] == $sujetCategorieId) {
                                $badgeColor = getCategoryColor($cat);
                                break;
                            }
                        }
                    }
                    $sujet_user_id = isset($_SESSION['sujet_owners'][$sujet['id']]) ? $_SESSION['sujet_owners'][$sujet['id']] : ($sujet['user_id'] ?? null);
                    $canEdit = isAdmin() || canEditSujet($sujet_user_id);
                    ?>
                    <article class="topic-card">
                        <div class="topic-card-header">
                            <div class="topic-avatar"><?php echo strtoupper(substr($sujet['titre'], 0, 1)); ?></div>
                            <div class="topic-card-body">
                                <div class="topic-card-title-row">
                                    <h2 class="topic-title">
                                        <a href="index.php?controller=sujet&action=show&id=<?php echo $sujet['id']; ?>" 
                                           onclick="event.stopPropagation();">
                                            <?php echo htmlspecialchars($sujet['titre']); ?>
                                        </a>
                                    </h2>
                                    <?php if ($canEdit): ?>
                                        <div class="topic-admin-actions" onclick="event.stopPropagation();">
                                            <a href="index.php?controller=sujet&action=edit&id=<?php echo $sujet['id']; ?>" 
                                               class="admin-action-btn edit-btn"
                                               aria-label="Modifier le sujet"
                                               onclick="event.stopPropagation();">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                    <path d="M8.75 1.75L11.25 4.25M10.5 2.5L2.625 10.375L1.75 12.25L3.625 11.375L11.5 3.5L10.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </a>
                                            <a href="index.php?controller=sujet&action=delete&id=<?php echo $sujet['id']; ?>" 
                                               class="admin-action-btn delete-btn"
                                               aria-label="Supprimer le sujet"
                                               onclick="event.stopPropagation(); return confirm('Êtes-vous sûr de vouloir supprimer ce sujet ?');">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                    <path d="M3.5 3.5L10.5 10.5M10.5 3.5L3.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                </svg>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="topic-meta">
                                    <span>Par Vous • dans </span>
                                    <span class="badge-category badge-with-dot">
                                        <span class="badge-dot" style="background-color: <?php echo htmlspecialchars($badgeColor); ?>;"></span>
                                        <?php echo htmlspecialchars($sujet['categorie_name'] ?? 'Non catégorisé'); ?>
                                    </span>
                                    <span> • <?php echo timeAgo($sujet['date_creation']); ?></span>
                                </div>
                                <div class="topic-description">
                                    <?php echo htmlspecialchars(truncate($sujet['contenu'], 200)); ?>
                                </div>
                                
                                <?php 
                                $tags = extractHashtags($sujet['contenu']);
                                if (!empty($tags)): 
                                ?>
                                    <div class="topic-tags">
                                        <?php foreach ($tags as $tag): ?>
                                            <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="topic-stats">
                                    <button class="stat-item like-btn" data-sujet-id="<?php echo $sujet['id']; ?>">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M13.333 7.333c0 3.314-2.239 6-5 6s-5-2.686-5-6 2.239-5 5-5 5 1.686 5 5z" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M10.667 7.333L8 4.667 5.333 7.333M8 4.667v5.333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                        <span class="like-count">0</span>
                                    </button>
                                    <div class="stat-item">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M5.333 8h5.334M8 5.333v5.334" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                        <span><?php echo $sujet['reponse_count'] ?? 0; ?> réponses</span>
                                    </div>
                                    <div class="stat-item">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 13.333c3.314 0 6-2.239 6-5 0-2.76-2.686-5-6-5S2 5.573 2 8.333c0 2.761 2.686 5 6 5z" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M8 10.667a2.667 2.667 0 1 0 0-5.334 2.667 2.667 0 0 0 0 5.334z" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                        <span>3 vues</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const countEl = this.querySelector('.like-count');
        let count = parseInt(countEl.textContent) || 0;
        if (this.classList.contains('liked')) {
            count--;
            this.classList.remove('liked');
        } else {
            count++;
            this.classList.add('liked');
        }
        countEl.textContent = count;
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
