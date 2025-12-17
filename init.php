<?php
// init.php - À LA RACINE - VERSION CORRIGÉE
session_start();

// NE PAS redéfinir le rôle depuis l'URL ici
// C'est index.php qui gère ça

// Inclure la base de données (si nécessaire)
if (file_exists(__DIR__ . '/config/databaset.php')) {
    require_once __DIR__ . '/config/databaset.php';
}

// Fonctions utilitaires
function isAdmin() {
    return ($_SESSION['role'] ?? 'user') === 'admin';
}

function addNotification($message, $type = 'info', $category = 'general', $reclamation_id = null) {
    if (!isset($_SESSION['notifications'])) {
        $_SESSION['notifications'] = [];
    }
    
    $notification = [
        'id' => uniqid('notif_', true),
        'message' => $message,
        'type' => $type,
        'category' => $category,
        'reclamation_id' => $reclamation_id,
        'date' => date('Y-m-d H:i:s'),
        'read' => false,
        'icon' => getNotificationIcon($type),
        'color' => getNotificationColor($type)
    ];
    
    array_unshift($_SESSION['notifications'], $notification);
    
    // Limiter à 50 notifications
    $_SESSION['notifications'] = array_slice($_SESSION['notifications'], 0, 50);
    
    return $notification['id'];
}

function getNotificationIcon($type) {
    $icons = [
        'info' => 'bi-info-circle',
        'success' => 'bi-check-circle',
        'warning' => 'bi-exclamation-triangle',
        'danger' => 'bi-x-circle',
        'system' => 'bi-gear',
        'user_reclamation' => 'bi-person-plus',
        'admin_reply' => 'bi-shield-check',
        'user_reply' => 'bi-chat-left-text',
        'status_change' => 'bi-arrow-repeat'
    ];
    
    return $icons[$type] ?? 'bi-bell';
}

function getNotificationColor($type) {
    $colors = [
        'info' => 'primary',
        'success' => 'success',
        'warning' => 'warning',
        'danger' => 'danger',
        'system' => 'secondary',
        'user_reclamation' => 'primary',
        'admin_reply' => 'success',
        'user_reply' => 'info',
        'status_change' => 'warning'
    ];
    
    return $colors[$type] ?? 'primary';
}

function getUnreadNotificationsCount() {
    if (isset($_SESSION['notifications'])) {
        return count(array_filter($_SESSION['notifications'], fn($n) => !$n['read']));
    }
    return 0;
}

function markNotificationRead($id) {
    if (isset($_SESSION['notifications'])) {
        foreach ($_SESSION['notifications'] as &$n) {
            if ($n['id'] === $id) {
                $n['read'] = true;
                break;
            }
        }
    }
}

// Nettoyer les anciennes notifications de session (garder 24h)
function cleanupOldNotifications() {
    if (!isset($_SESSION['notifications'])) {
        return;
    }
    
    $cutoff = date('Y-m-d H:i:s', strtotime('-24 hours'));
    $_SESSION['notifications'] = array_filter($_SESSION['notifications'], function($notification) use ($cutoff) {
        return $notification['date'] > $cutoff;
    });
    
    // Réindexer
    $_SESSION['notifications'] = array_values($_SESSION['notifications']);
}

// Exécuter le nettoyage avec une probabilité de 10%
if (rand(1, 10) === 1) {
    cleanupOldNotifications();
}

// Charger les notifications depuis la base de données
function loadNotificationsFromDatabase() {
    if (!isset($_SESSION['user_id'])) {
        return;
    }
    
    try {
        $database = Database::getInstance();
        $db = $database->getConnection();
        
        // Charger les notifications non lues de la base de données
        $sql = "SELECT id, message, type, category, reclamation_id, created_at, is_read 
                FROM notifications_history 
                WHERE user_id = ? AND is_read = 0 
                ORDER BY created_at DESC 
                LIMIT 50";
        $stmt = $db->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
        $dbNotifications = $stmt->fetchAll();
        
        // Convertir en format session
        $notifications = [];
        foreach ($dbNotifications as $n) {
            $notifications[] = [
                'id' => 'db_' . $n['id'],
                'message' => $n['message'],
                'type' => $n['type'],
                'category' => $n['category'] ?? 'general',
                'reclamation_id' => $n['reclamation_id'],
                'date' => $n['created_at'],
                'read' => (bool)$n['is_read'],
                'icon' => getNotificationIcon($n['type']),
                'color' => getNotificationColor($n['type'])
            ];
        }
        
        // Fusionner avec les notifications de session existantes
        if (isset($_SESSION['notifications'])) {
            $notifications = array_merge($notifications, $_SESSION['notifications']);
        }
        
        // Trier par date (plus récentes en premier) et limiter à 50
        usort($notifications, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        $_SESSION['notifications'] = array_slice($notifications, 0, 50);
        
    } catch (Exception $e) {
        error_log("Erreur chargement notifications: " . $e->getMessage());
        // En cas d'erreur, initialiser avec une notification par défaut
        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications'] = [];
        }
    }
}

// Charger les notifications au chargement de init.php
loadNotificationsFromDatabase();
?>