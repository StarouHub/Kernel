<?php
$pageTitle = 'Modifier une Catégorie';
$presetColors = ['#2563EB', '#7C3AED', '#0EA5E9', '#F97316', '#10B981', '#F43F5E', '#14B8A6', '#FBBF24'];
require_once __DIR__ . '/../layout/header.php';
$currentColor = $categorie['color'] ?? '#2563EB';
?>

<div class="forum-page-container">
    <div class="create-topic-container">
        <div class="create-topic-header">
            <h1>
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <path d="M8.75 1.75L11.25 4.25M10.5 2.5L2.625 10.375L1.75 12.25L3.625 11.375L11.5 3.5L10.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Modifier la Catégorie
            </h1>
        </div>
        <div class="create-topic-body">
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill="currentColor"/>
                    </svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?controller=categorie&action=edit&id=<?php echo $categorie['id']; ?>" id="form-categorie-edit">
                <div style="margin-bottom: 24px;">
                    <label class="form-label" for="name">Nom de la catégorie <span class="required">*</span></label>
                    <input type="text" 
                           class="form-control" 
                           id="name" 
                           name="name" 
                           placeholder="Ex: Sécurité, Développement, Design..."
                           value="<?php echo htmlspecialchars($categorie['name']); ?>">
                    <div class="field-error" data-error-for="name"></div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label class="form-label" for="color">Couleur <span class="required">*</span></label>
                    <div class="color-picker-wrapper js-color-picker">
                        <div class="color-preview" data-color-preview style="background-color: <?php echo htmlspecialchars($currentColor); ?>;"></div>
                        <div class="color-picker-fields">
                            <input type="text"
                                   class="form-control color-input"
                                   id="color"
                                   name="color"
                                   placeholder="#2563EB"
                                   value="<?php echo htmlspecialchars($currentColor); ?>"
                                   autocomplete="off"
                                   data-color-input>
                            <div class="field-error" data-error-for="color"></div>
                            <div class="color-swatches" role="listbox" aria-label="Couleurs suggérées">
                                <?php foreach ($presetColors as $colorOption): ?>
                                    <button type="button"
                                            class="color-swatch"
                                            data-color-option="<?php echo $colorOption; ?>"
                                            aria-label="Choisir la couleur <?php echo $colorOption; ?>"
                                            style="background-color: <?php echo $colorOption; ?>;">
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <small class="color-helper-text">Entrez un code hexadécimal (#RRGGBB) ou sélectionnez une couleur.</small>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 32px; display: flex; gap: 12px; align-items: center;">
                    <a href="index.php?controller=categorie&action=index" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-publish">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M13.333 4L6 11.333 2.667 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
