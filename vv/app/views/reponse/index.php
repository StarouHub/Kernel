<?php
$pageTitle = 'Réponses';
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../../helpers/auth.php';
$currentUserId = getUserId();
?>

<div class="forum-page-container">
    <div class="page-header">
        <h1>Toutes les Réponses</h1>
        <p>Gérez toutes les réponses du forum</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="error-message">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill="currentColor"/>
            </svg>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($reponses)): ?>
        <div class="card empty-state">
            <p>Aucune réponse trouvée</p>
        </div>
    <?php else: ?>
        <?php foreach ($reponses as $reponse): ?>
            <?php 
            $reponse_user_id = isset($_SESSION['reponse_owners'][$reponse['id']]) ? $_SESSION['reponse_owners'][$reponse['id']] : ($reponse['user_id'] ?? null);
            $canEdit = isAdmin() || canEditReponse($reponse_user_id); 
            ?>
            <div class="reply-card">
                <div class="reply-header">
                    <div class="reply-avatar">R</div>
                    <div class="user-info">
                        <div class="user-name">Réponse #<?php echo htmlspecialchars($reponse['id']); ?></div>
                        <div class="post-time"><?php echo htmlspecialchars($reponse['date']); ?></div>
                        <div style="margin-top: 5px;">
                            <span style="color: var(--text-muted); font-size: 13px;">Sujet: 
                                <a href="index.php?controller=sujet&action=show&id=<?php echo $reponse['sujet_id']; ?>" 
                                   style="color: var(--primary-color); text-decoration: none;">
                                    <?php echo htmlspecialchars($reponse['sujet_titre'] ?? 'N/A'); ?>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="post-content">
                    <?php echo nl2br(htmlspecialchars($reponse['contenu'])); ?>
                </div>
                <?php if ($canEdit): ?>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color);">
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
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
