<?php
/**
 * Admin Notification System - Real-time notifications for admins
 */

class AdminNotificationSystem {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Obtenir les notifications non lues pour un admin
     */
    public function getUnreadNotifications($adminId, $limit = 10) {
        try {
            $sql = "SELECT * FROM notifications_history 
                    WHERE user_id = ? AND is_read = 0
                    ORDER BY created_at DESC
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$adminId, $limit]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération notifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($notificationId) {
        try {
            $sql = "UPDATE notifications_history SET is_read = 1 WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$notificationId]);
        } catch (Exception $e) {
            error_log("Erreur marquer comme lue: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir le nombre de notifications non lues
     */
    public function getUnreadCount($adminId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM notifications_history 
                    WHERE user_id = ? AND is_read = 0";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$adminId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Obtenir les nouvelles réclamations pour affichage
     */
    public function getNewReclamationNotifications($adminId) {
        try {
            $sql = "SELECT n.*, r.id as reclamation_id, r.titre, r.priorite, r.created_at as reclamation_time
                    FROM notifications_history n
                    LEFT JOIN reclamations r ON n.reclamation_id = r.id
                    WHERE n.user_id = ? 
                    AND n.category = 'new_reclamation_priority'
                    AND n.is_read = 0
                    ORDER BY n.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$adminId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur notifications réclamations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * API pour notifications en temps réel (JSON)
     */
    public function getNotificationsJSON($adminId) {
        $unread = $this->getUnreadNotifications($adminId, 5);
        $count = $this->getUnreadCount($adminId);
        
        return [
            'success' => true,
            'count' => $count,
            'notifications' => $unread
        ];
    }
}
?>
