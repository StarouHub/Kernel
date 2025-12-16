<?php
$pageTitle = 'Créer une Réponse';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="forum-page-container">
    <div class="create-topic-container">
        <div class="create-topic-header">
            <h1>
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <path d="M14 3v22M3 14h22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Nouvelle Réponse
            </h1>
        </div>
        <div class="create-topic-body">
            <div class="info-box">
                <strong>Sujet:</strong> <?php echo htmlspecialchars($sujet['titre']); ?>
            </div>

            <?php if (isset($error)): ?>
                <div class="error-message">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill="currentColor"/>
                    </svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?controller=reponse&action=create&sujet_id=<?php echo $sujet['id']; ?>" id="form-reponse-create">
                <input type="hidden" name="sujet_id" value="<?php echo $sujet['id']; ?>">
                
                <div style="margin-bottom: 24px;">
                    <label class="form-label" for="contenu">Contenu <span class="required">*</span></label>
                    <textarea class="form-control" 
                              id="contenu" 
                              name="contenu" 
                              rows="10" 
                              placeholder="Partagez votre réponse ou votre solution..."><?php echo isset($_POST['contenu']) ? htmlspecialchars($_POST['contenu']) : ''; ?></textarea>
                    <div class="field-error" data-error-for="contenu"></div>
                </div>

                <div style="margin-top: 32px; display: flex; gap: 12px; align-items: center;">
                    <a href="index.php?controller=sujet&action=show&id=<?php echo $sujet['id']; ?>" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-publish">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M14 2L7 9M14 2L10 14L7 9M14 2L2 6L7 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Publier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
