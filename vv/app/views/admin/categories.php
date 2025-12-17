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
                <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 3 12V7a4 4 0 0 1 4-4z"></path>
            </svg>
            <h1>Gestion des Catégories</h1>
        </div>
        <p class="forum-page-subtitle">Créez et gérez les catégories du forum</p>
    </div>

    <div style="margin-bottom: 32px;">
        <a href="index.php?controller=categorie&action=create" class="button-gradient">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Ajouter une catégorie
        </a>
    </div>

    <?php if (empty($categories)): ?>
        <div class="card empty-state">
            <p>Aucune catégorie trouvée</p>
        </div>
    <?php else: ?>
        <div class="card" style="padding: 0; overflow: hidden;">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Couleur</th>
                        <th>Date de création</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $categorie): ?>
                        <?php $catColor = getCategoryColor($categorie); ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--text-muted);">#<?php echo htmlspecialchars($categorie['id']); ?></td>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($categorie['name']); ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="color-dot" style="background-color: <?php echo htmlspecialchars($catColor); ?>; width: 20px; height: 20px;"></span>
                                    <span style="font-size: 13px; color: var(--text-muted);"><?php echo htmlspecialchars($catColor); ?></span>
                                </div>
                            </td>
                            <td style="color: var(--text-muted);"><?php echo htmlspecialchars($categorie['created_at']); ?></td>
                            <td style="text-align: center;">
                                <a href="index.php?controller=categorie&action=edit&id=<?php echo $categorie['id']; ?>" class="action-link edit">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M8.75 1.75L11.25 4.25M10.5 2.5L2.625 10.375L1.75 12.25L3.625 11.375L11.5 3.5L10.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Modifier
                                </a>
                                <a href="index.php?controller=categorie&action=delete&id=<?php echo $categorie['id']; ?>" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');"
                                   class="action-link delete">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M3.5 3.5L10.5 10.5M10.5 3.5L3.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

