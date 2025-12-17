<?php
$pageTitle = 'Créer un Sujet';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="forum-page-container">
    <div class="create-topic-container">
        <div class="create-topic-header">
            <h1>
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <path d="M14 3v22M3 14h22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Nouveau Sujet
            </h1>
        </div>
        <div class="create-topic-body">
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="error-message">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill="currentColor"/>
                    </svg>
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?controller=sujet&action=create" id="form-sujet-create">
                <div style="margin-bottom: 24px;">
                    <label class="form-label" for="titre">Titre <span class="required">*</span></label>
                    <input type="text" 
                           class="form-control" 
                           id="titre" 
                           name="titre" 
                           placeholder="Donnez un titre clair à votre sujet..."
                           value="<?php echo isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : ''; ?>">
                    <div class="field-error" data-error-for="titre"></div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label class="form-label" for="categorie_id">Catégorie <span class="required">*</span></label>
                    <select class="form-select" id="categorie_id" name="categorie_id">
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?php echo $categorie['id']; ?>" 
                                    <?php echo (isset($_POST['categorie_id']) && $_POST['categorie_id'] == $categorie['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categorie['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="field-error" data-error-for="categorie_id"></div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label class="form-label" for="contenu">Contenu <span class="required">*</span></label>
                    <textarea class="form-control" 
                              id="contenu" 
                              name="contenu" 
                              rows="12" 
                              placeholder="Décrivez votre sujet en détail..."><?php echo isset($_POST['contenu']) ? htmlspecialchars($_POST['contenu']) : ''; ?></textarea>
                    <div class="field-error" data-error-for="contenu"></div>
                </div>

                <div style="margin-top: 32px; display: flex; gap: 12px; align-items: center;">
                    <a href="index.php?controller=sujet&action=index" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-publish">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M14 2L7 9M14 2L10 14L7 9M14 2L2 6L7 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Publier le sujet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
