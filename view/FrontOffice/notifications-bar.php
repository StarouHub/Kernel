<?php
/**
 * view/FrontOffice/notifications-bar.php - Barre de notifications utilisateur
 * Affiche toutes les notifications non lues et les détails des réclamations
 */

if (!isset($_SESSION['user_id'])) {
    exit;
}

$notifications = $_SESSION['notifications'] ?? [];
$unread_count = count(array_filter($notifications, fn($n) => !$n['read'] ?? true));
?>

<!-- NOTIFICATIONS BAR -->
<div class="notifications-container" id="notificationsBar">
    <style>
        .notifications-container {
            position: fixed;
            top: 60px;
            right: 20px;
            z-index: 1050;
            max-height: 600px;
            width: 350px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            display: none;
            flex-direction: column;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .notifications-container.active { display: flex; }

        .notifications-header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .notifications-header h5 {
            margin: 0;
            font-weight: 700;
            font-size: 1rem;
        }

        .notifications-badge {
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .notifications-list {
            flex: 1;
            overflow-y: auto;
            max-height: 400px;
        }

        .notification-item {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            gap: 0.75rem;
            background: white;
        }

        .notification-item:hover {
            background: #f8fafc;
        }

        .notification-item.unread {
            background: #f0f9ff;
            border-left: 3px solid #0284c7;
        }

        .notification-icon {
            font-size: 1.25rem;
            min-width: 2rem;
            text-align: center;
            margin-top: 0.25rem;
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-message {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
            word-wrap: break-word;
        }

        .notification-time {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .notification-type {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 0.3rem;
            background: #f1f5f9;
            color: #475569;
        }

        .notification-type.success { background: #dcfce7; color: #166534; }
        .notification-type.danger { background: #fee2e2; color: #991b1b; }
        .notification-type.warning { background: #fef3c7; color: #92400e; }
        .notification-type.info { background: #e0f2fe; color: #0c4a6e; }

        .notifications-footer {
            background: #f8fafc;
            padding: 0.75rem 1.5rem;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .notifications-footer a {
            color: #0284c7;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .notifications-footer a:hover {
            text-decoration: underline;
        }

        .notifications-toggle {
            position: relative;
        }

        .notifications-toggle .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .notifications-container {
                width: 100%;
                right: 0;
                left: 0;
                border-radius: 0;
                max-height: 500px;
            }
        }
    </style>

    <!-- Header -->
    <div class="notifications-header">
        <h5>
            <i class="bi bi-bell-fill"></i> Notifications
            <?php if ($unread_count > 0): ?>
                <span class="notifications-badge"><?= $unread_count ?></span>
            <?php endif; ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" onclick="toggleNotifications()"></button>
    </div>

    <!-- List -->
    <div class="notifications-list" id="notificationsList">
        <?php if (empty($notifications)): ?>
            <div style="padding: 2rem; text-align: center; color: #94a3b8;">
                <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                <p style="margin: 0;">Aucune notification</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
                <div class="notification-item <?= empty($notif['read']) ? 'unread' : '' ?>" onclick="markAsRead('<?= $notif['id'] ?>')">
                    <div class="notification-icon">
                        <i class="bi <?= $notif['icon'] ?? 'bi-info-circle' ?>"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-message"><?= htmlspecialchars($notif['message']) ?></div>
                        <div class="notification-time"><?= $notif['date'] ?></div>
                        <span class="notification-type <?= $notif['type'] ?? 'info' ?>">
                            <?= ucfirst($notif['type'] ?? 'info') ?>
                        </span>
                        <?php if (!empty($notif['reclamation_id'])): ?>
                            <div style="margin-top: 0.5rem;">
                                <a href="detailreclamation.php?id=<?= $notif['reclamation_id'] ?>" 
                                   style="color: #0284c7; font-size: 0.85rem; text-decoration: none;">
                                    → Voir réclamation #<?= $notif['reclamation_id'] ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="notifications-footer">
        <a href="mesreclamations.php">Voir toutes les notifications</a>
    </div>
</div>

<!-- NOTIFICATION BELL BUTTON (à mettre dans la navbar) -->
<button type="button" class="btn btn-sm btn-light notifications-toggle" onclick="toggleNotifications()" 
        style="position: relative; margin-left: auto;" id="notificationBell">
    <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
    <?php if ($unread_count > 0): ?>
        <span class="badge"><?= $unread_count ?></span>
    <?php endif; ?>
</button>

<script>
    function toggleNotifications() {
        const container = document.getElementById('notificationsBar');
        container.classList.toggle('active');
    }

    function markAsRead(notifId) {
        fetch('<?= ROOT_PATH ?>/api/mark-notification-read.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ notification_id: notifId })
        }).then(() => {
            document.getElementById(notifId)?.classList.remove('unread');
        });
    }

    // Fermer la notification si on clique ailleurs
    document.addEventListener('click', function(event) {
        const container = document.getElementById('notificationsBar');
        const bell = document.getElementById('notificationBell');
        if (container && !container.contains(event.target) && !bell.contains(event.target)) {
            container.classList.remove('active');
        }
    });

    // Auto-refresh notifications tous les 5 secondes
    setInterval(() => {
        fetch('<?= ROOT_PATH ?>/api/get-notifications.php')
            .then(r => r.json())
            .then(data => {
                if (data.updated) {
                    location.reload();
                }
            })
            .catch(e => console.error('Erreur notifications:', e));
    }, 5000);
</script>
