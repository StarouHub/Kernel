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
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top" style="background: linear-gradient(135deg, #1e3a8a 0%, #5b21b6 100%) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.15);">
    <div class="container-fluid">
        <!-- Logo/Brand -->
        <a class="navbar-brand fw-bold" href="dashboard.php" style="font-size: 1.4rem;">
            <i class="bi bi-gem"></i> Kernel Platform
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
                    <button type="button" class="btn btn-light btn-sm position-relative" 
                            onclick="toggleNotificationsBar()" id="notificationBell"
                            style="margin: 0 10px;">
                        <i class="bi bi-bell-fill" style="font-size: 1.1rem;"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                <?= min($unread_count, 9) . ($unread_count > 9 ? '+' : '') ?>
                            </span>
                        <?php endif; ?>
                    </button>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars(substr($nom_utilisateur, 0, 20)) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profil.php"><i class="bi bi-person"></i> Mon Profil</a></li>
                        <li><a class="dropdown-item" href="mesreclamations.php"><i class="bi bi-list-check"></i> Mes Réclamations</a></li>
                        <?php if ($role === 'admin'): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../BackOffice/dashboard.php"><i class="bi bi-gear"></i> BackOffice Admin</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../../logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- NOTIFICATIONS BAR (À côté) -->
<?php include 'notifications-bar.php'; ?>

<script>
    function toggleNotificationsBar() {
        const bar = document.getElementById('notificationsBar');
        if (bar) {
            bar.classList.toggle('active');
        }
    }

    // Charger les notifications admin si connecté
    function loadAdminNotifications() {
        fetch('<?= dirname($_SERVER['REQUEST_URI']) ?>/../../api/load-admin-notifications.php')
            .then(r => r.json())
            .then(data => {
                console.log('Notifications chargées:', data);
                if (data.notifications_loaded > 0) {
                    // Refresh la page pour afficher les notifications
                    location.reload();
                }
            })
            .catch(e => console.error('Erreur chargement notifications:', e));
    }

    // Au chargement de la page, charger les notifications
    document.addEventListener('DOMContentLoaded', () => {
        loadAdminNotifications();
    });
</script>
