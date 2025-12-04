<?php
require_once __DIR__ . '/../layout/header.php';

function getCategoryColor($categorie) {
    if (!empty($categorie['color'])) {
        return $categorie['color'];
    }
    $colors = ['#2563EB', '#7C3AED', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];
    return $colors[$categorie['id'] % count($colors)];
}
?>

<div class="forum-page-container">
    <div class="forum-page-header">
        <div class="forum-page-title">
            <svg class="title-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            <h1>Gestion des Sujets</h1>
        </div>
        <p class="forum-page-subtitle">Gérez tous les sujets du forum</p>
    </div>

    <?php if (empty($sujets)): ?>
        <div class="card empty-state">
            <p>Aucun sujet trouvé</p>
        </div>
    <?php else: ?>
        <div class="forum-main-content">
            <?php foreach ($sujets as $sujet): ?>
                <?php
                $sujetCategorieId = $sujet['categorie_id'] ?? null;
                $badgeColor = '#2563EB';
                $categorieName = 'Non catégorisé';
                
                if ($sujetCategorieId && isset($categoryMap[$sujetCategorieId])) {
                    $cat = $categoryMap[$sujetCategorieId];
                    $badgeColor = getCategoryColor($cat);
                    $categorieName = $cat['name'];
                }
                
                $sujet_user_id = isset($_SESSION['sujet_owners'][$sujet['id']]) ? $_SESSION['sujet_owners'][$sujet['id']] : ($sujet['user_id'] ?? null);
                $auteur = 'Utilisateur #' . ($sujet_user_id ?? 'N/A');
                ?>
                <article class="topic-card">
                    <div class="topic-card-header">
                        <div class="topic-avatar"><?php echo strtoupper(substr($sujet['titre'], 0, 1)); ?></div>
                        <div class="topic-card-body">
                            <div class="topic-card-title-row">
                                <h2 class="topic-title">
                                    <a href="index.php?controller=sujet&action=show&id=<?php echo $sujet['id']; ?>">
                                        <?php echo htmlspecialchars($sujet['titre']); ?>
                                    </a>
                                </h2>
                                <div class="topic-admin-actions">
                                    <a href="index.php?controller=sujet&action=edit&id=<?php echo $sujet['id']; ?>" 
                                       class="admin-action-btn edit-btn">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                            <path d="M8.75 1.75L11.25 4.25M10.5 2.5L2.625 10.375L1.75 12.25L3.625 11.375L11.5 3.5L10.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                    <a href="index.php?controller=sujet&action=delete&id=<?php echo $sujet['id']; ?>" 
                                       class="admin-action-btn delete-btn"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce sujet ?');">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                            <path d="M3.5 3.5L10.5 10.5M10.5 3.5L3.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="topic-meta">
                                <span>Par <strong><?php echo htmlspecialchars($auteur); ?></strong> • dans </span>
                                <span class="badge-category badge-with-dot">
                                    <span class="badge-dot" style="background-color: <?php echo htmlspecialchars($badgeColor); ?>;"></span>
                                    <?php echo htmlspecialchars($categorieName); ?>
                                </span>
                                <span> • <?php echo htmlspecialchars($sujet['date_creation']); ?></span>
                            </div>
                            <div class="topic-description">
                                <?php echo htmlspecialchars(mb_substr($sujet['contenu'], 0, 200)) . (mb_strlen($sujet['contenu']) > 200 ? '...' : ''); ?>
                            </div>
                            <div class="topic-stats">
                                <div class="stat-item">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M5.333 8h5.334M8 5.333v5.334" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    <span><?php echo $sujet['reponse_count'] ?? 0; ?> réponses</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

