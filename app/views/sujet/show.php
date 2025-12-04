<?php
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/view_helpers.php';
$pageTitle = htmlspecialchars($sujet['titre']);
$currentRole = getRole();
$currentUserId = getUserId();

function getCategoryColor($categorie) {
    if (!empty($categorie['color'])) {
        return $categorie['color'];
    }
    $colors = ['#2563EB', '#7C3AED', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];
    return $colors[$categorie['id'] % count($colors)];
}

$badgeColor = '#2563EB';
if (isset($sujet['categorie_id'])) {
    require_once __DIR__ . '/../../models/Categorie.php';
    $catModel = new Categorie();
    $cat = $catModel->getById($sujet['categorie_id']);
    if ($cat) {
        $badgeColor = getCategoryColor($cat);
    }
}

$sujet_user_id = isset($_SESSION['sujet_owners'][$sujet['id']]) ? $_SESSION['sujet_owners'][$sujet['id']] : ($sujet['user_id'] ?? null);
$canEditSujet = isAdmin() || canEditSujet($sujet_user_id);

require_once __DIR__ . '/../layout/header.php';
?>

<div class="forum-page-container" style="max-width: 1000px;">
    <div class="breadcrumb">
        <a href="index.php?controller=sujet&action=index">Sujets</a> / 
        <span><?php echo htmlspecialchars($sujet['titre']); ?></span>
    </div>

    <div class="card" style="margin-bottom: 32px;">
        <div class="card-header" style="position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px;">
                        <h1 class="discussion-title" style="flex: 1; margin: 0;"><?php echo htmlspecialchars($sujet['titre']); ?></h1>
                        <?php if ($canEditSujet): ?>
                            <div class="topic-admin-actions">
                                <a href="index.php?controller=sujet&action=edit&id=<?php echo $sujet['id']; ?>" 
                                   class="admin-action-btn edit-btn"
                                   aria-label="Modifier le sujet">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M8.75 1.75L11.25 4.25M10.5 2.5L2.625 10.375L1.75 12.25L3.625 11.375L11.5 3.5L10.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                <a href="index.php?controller=sujet&action=delete&id=<?php echo $sujet['id']; ?>" 
                                   class="admin-action-btn delete-btn"
                                   aria-label="Supprimer le sujet"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce sujet ?');">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M3.5 3.5L10.5 10.5M10.5 3.5L3.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="meta-row">
                        <span class="badge-category badge-with-dot">
                            <span class="badge-dot" style="background-color: <?php echo htmlspecialchars($badgeColor); ?>;"></span>
                            <?php echo htmlspecialchars($sujet['categorie_name'] ?? 'Non catégorisé'); ?>
                        </span>
                        <span style="color: var(--text-muted); font-size: 14px;">Créé le <?php echo htmlspecialchars($sujet['date_creation']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="post-card" style="margin-bottom: 0; padding: 24px; box-shadow: none; border: none;">
            <div class="post-header">
                <div class="user-avatar"><?php echo strtoupper(substr($sujet['titre'], 0, 1)); ?></div>
                <div class="user-info">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <div class="user-name">Vous</div>
                        <span class="author-badge">Auteur</span>
                    </div>
                    <div class="post-time">à l'instant</div>
                </div>
            </div>
            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($sujet['contenu'])); ?>
            </div>
            <div class="post-actions">
                <button class="action-btn like-btn" data-sujet-id="<?php echo $sujet['id']; ?>">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M13.333 7.333c0 3.314-2.239 6-5 6s-5-2.686-5-6 2.239-5 5-5 5 1.686 5 5z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M10.667 7.333L8 4.667 5.333 7.333M8 4.667v5.333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    J'aime
                </button>
                <button class="action-btn reply-btn">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M5.333 8h5.334M8 5.333v5.334" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Répondre
                </button>
                <button class="action-btn solved">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M13.333 4L6 11.333 2.667 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Résolu
                </button>
            </div>
        </div>
    </div>

    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" style="color: var(--primary-color);">
            <path d="M18 5v8a2 2 0 0 1-2 2h-5l-5 5v-5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h2 class="section-title" style="margin: 0;">Réponses</h2>
    </div>

    <?php if (empty($reponses)): ?>
        <div class="card empty-state">
            <p style="margin: 0; font-size: 15px; color: var(--text-muted);">Aucune réponse pour le moment. Soyez le premier à répondre !</p>
        </div>
    <?php else: ?>
        <?php foreach ($reponses as $reponse): ?>
            <?php 
            $reponse_user_id = isset($_SESSION['reponse_owners'][$reponse['id']]) ? $_SESSION['reponse_owners'][$reponse['id']] : ($reponse['user_id'] ?? null);
            $canEditReponse = isAdmin() || canEditReponse($reponse_user_id); 
            ?>
            <div class="reply-card">
                <div class="reply-header">
                    <div class="reply-avatar">R</div>
                    <div class="user-info">
                        <div class="user-name">Réponse #<?php echo htmlspecialchars($reponse['id']); ?></div>
                        <div class="post-time"><?php echo htmlspecialchars($reponse['date']); ?></div>
                    </div>
                </div>
                <div class="post-content" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                    <?php echo nl2br(htmlspecialchars($reponse['contenu'])); ?>
                </div>
                <?php if ($canEditReponse): ?>
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 8px;">
                        <a href="index.php?controller=reponse&action=edit&id=<?php echo $reponse['id']; ?>" 
                           class="admin-action-btn edit-btn"
                           aria-label="Modifier la réponse">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M8.75 1.75L11.25 4.25M10.5 2.5L2.625 10.375L1.75 12.25L3.625 11.375L11.5 3.5L10.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <a href="index.php?controller=reponse&action=delete&id=<?php echo $reponse['id']; ?>" 
                           class="admin-action-btn delete-btn"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?');"
                           aria-label="Supprimer la réponse">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M3.5 3.5L10.5 10.5M10.5 3.5L3.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="reply-form">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" style="color: var(--primary-color);">
                <path d="M13.5 3.5L16.5 6.5M15 5L8 12L5 15L8 12L15 5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3 style="margin: 0;">Ajouter une réponse</h3>
        </div>
        <form method="POST" action="index.php?controller=reponse&action=create&sujet_id=<?php echo $sujet['id']; ?>" onsubmit="return validateForm(this);">
            <input type="hidden" name="sujet_id" value="<?php echo $sujet['id']; ?>">
            <div style="margin-bottom: 24px;">
                <textarea class="form-control" 
                          id="contenu" 
                          name="contenu" 
                          rows="6" 
                          placeholder="Partagez votre réponse ou votre solution..."
                          required></textarea>
            </div>
            <button type="submit" class="btn-submit">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink: 0;">
                    <path d="M14 2L7 9M14 2L10 14L7 9M14 2L2 6L7 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Publier la réponse
            </button>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        if (this.classList.contains('liked')) {
            this.classList.remove('liked');
        } else {
            this.classList.add('liked');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
