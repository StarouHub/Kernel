<?php
/**
 * Composant de notifications universel pour User et Admin
 */
if (!isset($_SESSION['user_id'])) {
    return;
}

$notifications = $_SESSION['notifications'] ?? [];
$unread_count = count(array_filter($notifications, fn($n) => !($n['read'] ?? false)));
?>
<!-- Panel de Notifications -->
<div class="notifications-panel" id="notificationsPanel" style="display: none;">
    <div class="notifications-header">
        <h5 class="mb-0">
            <i class="bi bi-bell-fill"></i> Notifications
            <?php if ($unread_count > 0): ?>
                <span class="badge bg-danger ms-2"><?= $unread_count ?></span>
            <?php endif; ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" onclick="closeNotificationsPanel(event)"></button>
    </div>
    
    <div class="notifications-body" id="notificationsBody">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-5">
                <i class="bi bi-bell-slash" style="font-size: 3rem; color: #cbd5e1;"></i>
                <p class="text-muted mt-3">Aucune notification</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
                <div class="notification-item <?= !($notif['read'] ?? false) ? 'unread' : '' ?>" 
                     data-notif-id="<?= htmlspecialchars($notif['id']) ?>"
                     onclick="event.stopPropagation(); markNotificationRead('<?= htmlspecialchars($notif['id']) ?>', this)">
                    <div class="notification-icon bg-<?= $notif['color'] ?? 'primary' ?>">
                        <i class="bi <?= $notif['icon'] ?? 'bi-bell' ?>"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-message"><?= htmlspecialchars($notif['message']) ?></div>
                        <div class="notification-meta">
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> <?= date('d/m/Y H:i', strtotime($notif['date'])) ?>
                            </small>
                            <?php if (!empty($notif['reclamation_id'])): ?>
                                <?php
                                $detail_url = '';
                                if (function_exists('isAdmin') && isAdmin()) {
                                    $detail_url = '../BackOffice/detailadmin.php?id=' . $notif['reclamation_id'];
                                } else {
                                    $detail_url = '../FrontOffice/detailreclamation.php?id=' . $notif['reclamation_id'];
                                }
                                ?>
                                <a href="<?= $detail_url ?>" 
                                   class="btn btn-sm btn-outline-primary ms-2" 
                                   onclick="event.stopPropagation()">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!($notif['read'] ?? false)): ?>
                        <div class="notification-dot"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="notifications-footer">
        <button class="btn btn-sm btn-outline-light" onclick="event.stopPropagation(); markAllAsRead()">
            <i class="bi bi-check-all"></i> Tout marquer comme lu
        </button>
    </div>
</div>

<!-- Bouton de notification dans la navbar -->
<button type="button" class="btn btn-light position-relative notifications-toggle" 
        onclick="toggleNotificationsPanel(event)" 
        id="notificationBellBtn"
        style="z-index: 1051;">
    <i class="bi bi-bell"></i>
    <?php if ($unread_count > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $unread_count > 9 ? '9+' : $unread_count ?>
        </span>
    <?php endif; ?>
</button>

<style>
.notifications-panel {
    position: fixed;
    top: 70px;
    right: 20px;
    width: 400px;
    max-height: 600px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    z-index: 1050;
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideInRight 0.3s ease;
}

.notifications-panel.show {
    display: flex !important;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notifications-header {
    background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
    color: white;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.notifications-body {
    flex: 1;
    overflow-y: auto;
    max-height: 500px;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    cursor: pointer;
    transition: background 0.2s;
    position: relative;
}

.notification-item:hover {
    background: #f8fafc;
}

.notification-item.unread {
    background: #eff6ff;
    border-left: 4px solid #0A4FFF;
}

.notification-item.unread .notification-message {
    font-weight: 600;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
    margin-right: 1rem;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-message {
    color: #1e293b;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 0.5rem;
}

.notification-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.notification-dot {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 8px;
    height: 8px;
    background: #ef4444;
    border-radius: 50%;
}

.notifications-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    text-align: center;
}

.notifications-toggle {
    position: relative;
}

/* Scrollbar personnalisée */
.notifications-body::-webkit-scrollbar {
    width: 6px;
}

.notifications-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notifications-body::-webkit-scrollbar-thumb {
    background: #0A4FFF;
    border-radius: 3px;
}

.notifications-body::-webkit-scrollbar-thumb:hover {
    background: #4AA8FF;
}

@media (max-width: 768px) {
    .notifications-panel {
        width: 100%;
        right: 0;
        top: 60px;
        border-radius: 0;
        max-height: calc(100vh - 60px);
    }
}
</style>

<script>
let notificationsPanelOpen = false;

function toggleNotificationsPanel(event) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    
    // Annuler tout timeout de fermeture en cours
    if (clickTimeout) {
        clearTimeout(clickTimeout);
        clickTimeout = null;
    }
    
    const panel = document.getElementById('notificationsPanel');
    if (panel) {
        notificationsPanelOpen = !notificationsPanelOpen;
        
        if (notificationsPanelOpen) {
            // Ouvrir le panel
            panel.style.display = 'flex';
            panel.classList.add('show');
            // Forcer le réaffichage
            setTimeout(() => {
                panel.style.visibility = 'visible';
                panel.style.opacity = '1';
            }, 10);
            // Ne pas recharger la page, juste mettre à jour le badge
            updateNotificationBadge();
        } else {
            // Fermer le panel
            panel.classList.remove('show');
            panel.style.display = 'none';
        }
    }
}

function closeNotificationsPanel(event) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    
    notificationsPanelOpen = false;
    const panel = document.getElementById('notificationsPanel');
    if (panel) {
        panel.classList.remove('show');
        panel.style.display = 'none';
    }
}

// Fermer le panel en cliquant en dehors (avec délai pour éviter la fermeture immédiate)
let clickTimeout = null;
document.addEventListener('click', function(event) {
    const panel = document.getElementById('notificationsPanel');
    const btn = document.getElementById('notificationBellBtn');
    
    if (!panel || !btn) return;
    
    // Vérifier que le clic n'est pas sur le panel ou le bouton
    const clickedInsidePanel = panel.contains(event.target);
    const clickedOnButton = btn.contains(event.target);
    const clickedOnCloseBtn = event.target.classList.contains('btn-close');
    
    // Si on clique sur le bouton de notification, ne pas fermer
    if (clickedOnButton) {
        return;
    }
    
    // Si on clique sur le bouton de fermeture, fermer immédiatement
    if (clickedOnCloseBtn) {
        closeNotificationsPanel(event);
        return;
    }
    
    // Si on clique dans le panel, ne pas fermer
    if (clickedInsidePanel) {
        return;
    }
    
    // Si le panel est ouvert et qu'on clique en dehors, fermer avec un délai
    if (notificationsPanelOpen && !clickedInsidePanel && !clickedOnButton) {
        // Annuler le timeout précédent
        if (clickTimeout) {
            clearTimeout(clickTimeout);
        }
        
        // Nouveau timeout avec délai plus long
        clickTimeout = setTimeout(() => {
            if (notificationsPanelOpen) {
                closeNotificationsPanel();
            }
        }, 200);
    }
}, false);

async function markNotificationRead(notifId, element) {
    try {
        const response = await fetch('../../api/mark-notification-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `notification_id=${encodeURIComponent(notifId)}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Marquer visuellement comme lu
            if (element) {
                element.classList.remove('unread');
                const dot = element.querySelector('.notification-dot');
                if (dot) dot.remove();
                element.querySelector('.notification-message').style.fontWeight = 'normal';
            }
            
            // Mettre à jour le badge
            updateNotificationBadge();
        }
    } catch (error) {
        console.error('Erreur marquage notification:', error);
    }
}

async function markAllAsRead() {
    try {
        const response = await fetch('../../api/mark-notification-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'mark_all=1'
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Marquer toutes comme lues visuellement
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.remove('unread');
                const dot = item.querySelector('.notification-dot');
                if (dot) dot.remove();
                const message = item.querySelector('.notification-message');
                if (message) message.style.fontWeight = 'normal';
            });
            
            // Mettre à jour le badge
            updateNotificationBadge();
        }
    } catch (error) {
        console.error('Erreur marquage toutes notifications:', error);
    }
}

async function refreshNotifications() {
    try {
        const response = await fetch('../../api/get-notifications.php');
        const data = await response.json();
        
        if (data.success) {
            // Mettre à jour le badge sans recharger la page
            updateNotificationBadge();
            
            // Si le panel est ouvert, on peut mettre à jour le contenu
            // mais sans recharger la page pour éviter la fermeture
            if (notificationsPanelOpen && data.notifications) {
                // Optionnel : mettre à jour dynamiquement le contenu
                // Pour l'instant, on garde le contenu existant
            }
        }
    } catch (error) {
        console.error('Erreur rafraîchissement notifications:', error);
    }
}

function updateNotificationBadge() {
    const unreadItems = document.querySelectorAll('.notification-item.unread');
    const badge = document.querySelector('#notificationBellBtn .badge');
    const count = unreadItems.length;
    
    if (count > 0) {
        if (!badge) {
            const btn = document.getElementById('notificationBellBtn');
            if (btn) {
                const span = document.createElement('span');
                span.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                span.textContent = count > 9 ? '9+' : count;
                btn.appendChild(span);
            }
        } else {
            badge.textContent = count > 9 ? '9+' : count;
        }
    } else {
        if (badge) {
            badge.remove();
        }
    }
}

// Rafraîchir les notifications toutes les 30 secondes (seulement si le panel est fermé)
setInterval(() => {
    if (!notificationsPanelOpen) {
        updateNotificationBadge();
    }
}, 30000);

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    updateNotificationBadge();
    
    // S'assurer que le panel est bien caché au départ
    const panel = document.getElementById('notificationsPanel');
    if (panel) {
        panel.style.display = 'none';
        notificationsPanelOpen = false;
    }
});
</script>

