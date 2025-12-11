<?php
// controller/ReponseController.php
require_once __DIR__ . '/../model/Reponse.php';

class ReponseController {
    private $db;

    public function __construct() {
        // Démarrer la session si pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    // NOUVELLE VERSION CORRIGÉE DE LA MÉTHODE ajouter()
    public function ajouter($reclamation_id, $message, $est_admin = false) {
        try {
            if (empty(trim($message))) {
                throw new Exception("Le message ne peut pas être vide");
            }

            $reponse = new Reponse();
            $reponse->setReclamationId($reclamation_id);
            $reponse->setMessage($message);
            $reponse->setEstAdmin($est_admin);
            $reponse->setUtilisateurId($_SESSION['user_id']);

            $this->db->beginTransaction();
            
            // 1. Insérer la réponse
            $sql = "INSERT INTO reponses (reclamation_id, utilisateur_id, message, est_admin, date_reponse) 
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            
            $result = $stmt->execute([
                $reponse->getReclamationId(),
                $reponse->getUtilisateurId(),
                $reponse->getMessage(),
                $reponse->isAdmin() ? 1 : 0
            ]);

            if (!$result) {
                throw new Exception("Erreur lors de l'insertion de la réponse");
            }
            
            $reponse_id = $this->db->lastInsertId();

            // 2. Récupérer les infos de la réclamation
            $sql = "SELECT r.titre, r.utilisateur_id, u.nom as auteur_nom, r.statut, u.email as auteur_email
                    FROM reclamations r 
                    LEFT JOIN users u ON r.utilisateur_id = u.id 
                    WHERE r.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$reclamation_id]);
            $reclamation = $stmt->fetch();
            
            if (!$reclamation) {
                throw new Exception("Réclamation non trouvée");
            }

            $message_success = '';
            $reponse_id_return = $reponse_id;

            // 3. Mettre à jour le statut si c'est une réponse admin
            if ($est_admin && $reclamation['statut'] === 'en-attente') {
                $this->updateReclamationStatus($reclamation_id, 'en-cours');
                
                // Notification à l'utilisateur
                $adminName = htmlspecialchars($_SESSION['nom'] ?? 'Administrateur');
                $notificationMsg = "L'administrateur $adminName a répondu à votre réclamation #$reclamation_id";
                
                // Ajouter la notification dans le système
                if (function_exists('addNotification')) {
                    addNotification($notificationMsg, 'info', 'admin_reply', $reclamation_id);
                }
                
                // Envoyer une notification en temps réel
                if (function_exists('sendRealTimeNotification')) {
                    sendRealTimeNotification(
                        $reclamation['utilisateur_id'],
                        "L'administrateur a répondu à votre réclamation #$reclamation_id",
                        'success',
                        'admin_reply',
                        $reclamation_id
                    );
                }
                
                $message_success = 'Réponse envoyée avec succès et réclamation marquée comme "En cours"';
            } 
            // 4. Si c'est une réponse utilisateur
            elseif (!$est_admin) {
                // Notification aux administrateurs
                $sql = "SELECT id, nom FROM users WHERE role = 'admin'";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $admins = $stmt->fetchAll();
                
                $userName = htmlspecialchars($_SESSION['nom'] ?? 'Utilisateur');
                foreach ($admins as $admin) {
                    if (function_exists('addNotification')) {
                        addNotification(
                            "$userName a répondu à la réclamation #$reclamation_id",
                            'warning',
                            'user_reply',
                            $reclamation_id
                        );
                    }
                    
                    // Notification en temps réel aux admins
                    if (function_exists('sendRealTimeNotification')) {
                        sendRealTimeNotification(
                            $admin['id'],
                            "Nouvelle réponse utilisateur sur la réclamation #$reclamation_id",
                            'info',
                            'user_reply',
                            $reclamation_id
                        );
                    }
                }
                
                $message_success = 'Message envoyé avec succès';
            }
            else {
                $message_success = 'Réponse envoyée avec succès';
            }

            // 5. Enregistrer l'activité
            $sql = "INSERT INTO activity_logs (user_id, action, details, created_at) 
                    VALUES (?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $_SESSION['user_id'],
                $est_admin ? 'admin_reply_added' : 'user_reply_added',
                json_encode([
                    'reclamation_id' => $reclamation_id,
                    'reponse_id' => $reponse_id,
                    'est_admin' => $est_admin
                ])
            ]);

            $this->db->commit();
            
            return [
                'success' => true, 
                'message' => $message_success,
                'reponse_id' => $reponse_id_return
            ];

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Erreur ajout réponse: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function updateReclamationStatus($id, $status) {
        $sql = "UPDATE reclamations SET statut = ?, date_modification = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function getReponsesByReclamation($reclamation_id) {
        $sql = "SELECT r.*, u.nom as auteur_nom, u.role as auteur_role
                FROM reponses r 
                LEFT JOIN users u ON r.utilisateur_id = u.id 
                WHERE r.reclamation_id = ? 
                ORDER BY r.date_reponse ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reclamation_id]);
        return $stmt->fetchAll();
    }

    public function getNombreReponses($reclamation_id) {
        $sql = "SELECT COUNT(*) as count FROM reponses WHERE reclamation_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reclamation_id]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    // Nouvelle méthode pour créer une réponse avec plus de paramètres
    public function create($data) {
        try {
            $reclamation_id = $data['reclamation_id'] ?? 0;
            $message = $data['message'] ?? '';
            $est_admin = $data['est_admin'] ?? false;
            
            if (!$reclamation_id) {
                throw new Exception("ID de réclamation requis");
            }
            
            return $this->ajouter($reclamation_id, $message, $est_admin);
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>