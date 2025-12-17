<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="forum-page-container">
    <div class="forum-page-header">
        <div class="forum-page-title">
            <svg class="title-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            <h1>Gestion des Réponses</h1>
        </div>
        <p class="forum-page-subtitle">Gérez toutes les réponses du forum</p>
    </div>

    <?php if (empty($reponses)): ?>
        <div class="card empty-state">
            <p>Aucune réponse trouvée</p>
        </div>
    <?php else: ?>
        <div class="forum-main-content">
            <?php foreach ($reponses as $reponse): ?>
                <?php
                $reponse_user_id = isset($_SESSION['reponse_owners'][$reponse['id']]) ? $_SESSION['reponse_owners'][$reponse['id']] : ($reponse['user_id'] ?? null);
                $auteur = 'Utilisateur #' . ($reponse_user_id ?? 'N/A');
                $excerpt = mb_substr($reponse['contenu'], 0, 150);
                ?>
                <div class="reply-card">
                    <div class="reply-header">
                        <div class="reply-avatar">R</div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($auteur); ?></div>
                            <div class="post-time"><?php echo htmlspecialchars($reponse['date']); ?></div>
                            <div style="margin-top: 8px;">
                                <span style="color: var(--text-muted); font-size: 13px;">Sujet: </span>
                                <a href="index.php?controller=sujet&action=show&id=<?php echo $reponse['sujet_id']; ?>" 
                                   style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                                    <?php echo htmlspecialchars($reponse['sujet_titre'] ?? 'N/A'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="post-content">
                        <?php echo nl2br(htmlspecialchars($excerpt)); ?>
                        <?php if (mb_strlen($reponse['contenu']) > 150): ?>
                            <span style="color: var(--text-muted);">...</span>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color); display: flex; gap: 12px;">
                        <a href="index.php?controller=reponse&action=edit&id=<?php echo $reponse['id']; ?>" 
                           class="action-link edit">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M8.75 1.75L11.25 4.25M10.5 2.5L2.625 10.375L1.75 12.25L3.625 11.375L11.5 3.5L10.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Modifier
                        </a>
                        <a href="index.php?controller=reponse&action=delete&id=<?php echo $reponse['id']; ?>" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?');"
                           class="action-link delete">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M3.5 3.5L10.5 10.5M10.5 3.5L3.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            Supprimer
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

