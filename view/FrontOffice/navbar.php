<?php
/**
 * view/FrontOffice/navbar.php - Barre de navigation avec notifications
 * À inclure en haut de chaque page FrontOffice
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nom_utilisateur = $_SESSION['nom'] ?? 'Utilisateur';
$role = $_SESSION['role'] ?? 'user';
$unread_count = count(array_filter($_SESSION['notifications'] ?? [], fn($n) => !$n['read'] ?? true));
?>

<!-- NAVBAR PRINCIPALE -->
<nav class="main-header navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-fluid">
        <!-- Logo/Brand -->
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-hexagon-fill"></i> Kernel
        </a>

        <!-- Toggler pour mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapse content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <!-- Lien Nouvelle Réclamation -->
                <li class="nav-item">
                    <a class="nav-link fw-600" href="nouvellereclamation.php">
                        <i class="bi bi-plus-circle"></i> Nouvelle Réclamation
                    </a>
                </li>

                <!-- Lien Mes Réclamations -->
                <li class="nav-item">
                    <a class="nav-link fw-600" href="mesreclamations.php">
                        <i class="bi bi-file-text"></i> Mes Réclamations
                    </a>
                </li>

                <!-- Bell Notifications -->
                <li class="nav-item position-relative">
                    <?php 
                    // Inclure le composant de notifications
                    $notifications_path = __DIR__ . '/../components/notifications-panel.php';
                    if (file_exists($notifications_path)) {
                        include $notifications_path;
                    }
                    ?>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars(substr($nom_utilisateur, 0, 20)) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profil.php"><i class="bi bi-person"></i> Mon Profil</a></li>
                        <li><a class="dropdown-item" href="mesreclamations.php"><i class="bi bi-list-check"></i> Mes Réclamations</a></li>
                        <?php if ($role === 'admin'): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../BackOffice/dashboard2.php"><i class="bi bi-gear"></i> BackOffice Admin</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../../logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
// Charger les notifications depuis la BD au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Charger les notifications depuis la base de données
    fetch('../../api/get-notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Les notifications sont déjà dans la session via init.php
                // On met juste à jour le badge
                updateNotificationBadge();
            }
        })
        .catch(error => console.error('Erreur chargement notifications:', error));
});
</script>
