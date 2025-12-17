<?php
/**
 * 📋 Task Controller - Gestion des tâches projet
 * Contrôleur pour les opérations CRUD des tâches avec support Kanban
 */

include_once(__DIR__ . '/../config.php');

class TaskController {
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    /**
     * 📋 Récupérer toutes les tâches d'un projet
     */
    public function getTasksByProject($projetId) {
        try {
            $sql = "SELECT t.*, u.nom as assignee_nom, u.prenom as assignee_prenom, 
                           p.titre as projet_titre
                    FROM taches_projet t 
                    LEFT JOIN users u ON t.assignee_id = u.id
                    LEFT JOIN projet p ON t.projet_id = p.id
                    WHERE t.projet_id = :projet_id 
                    ORDER BY t.ordre ASC, t.date_creation ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':projet_id', $projetId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getTasksByProject: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 📊 Récupérer les tâches groupées par statut (pour Kanban)
     */
    public function getTasksKanban($projetId) {
        $tasks = $this->getTasksByProject($projetId);
        
        $kanban = [
            'a_faire' => [],
            'en_cours' => [],
            'termine' => []
        ];
        
        foreach ($tasks as $task) {
            $kanban[$task['statut']][] = $task;
        }
        
        return $kanban;
    }
    
    /**
     * 📋 Récupérer toutes les tâches d'un utilisateur
     */
    public function getTasksByUser($userId) {
        try {
            $sql = "SELECT t.*, p.titre as projet_titre, p.id as projet_id,
                           u.nom as assignee_nom, u.prenom as assignee_prenom
                    FROM taches_projet t 
                    LEFT JOIN projet p ON t.projet_id = p.id
                    LEFT JOIN users u ON t.assignee_id = u.id
                    WHERE t.assignee_id = :user_id OR t.created_by = :user_id
                    ORDER BY 
                        CASE t.statut 
                            WHEN 'en_cours' THEN 1 
                            WHEN 'a_faire' THEN 2 
                            WHEN 'termine' THEN 3 
                        END,
                        CASE t.priorite 
                            WHEN 'haute' THEN 1 
                            WHEN 'moyenne' THEN 2 
                            WHEN 'basse' THEN 3 
                        END,
                        t.date_creation DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getTasksByUser: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ➕ Créer une nouvelle tâche
     */
    public function createTask($data) {
        try {
            $sql = "INSERT INTO taches_projet 
                    (projet_id, titre, description, statut, priorite, date_echeance, 
                     assignee_id, couleur, tags, temps_estime, created_by, ordre) 
                    VALUES 
                    (:projet_id, :titre, :description, :statut, :priorite, :date_echeance,
                     :assignee_id, :couleur, :tags, :temps_estime, :created_by, :ordre)";
            
            $stmt = $this->db->prepare($sql);
            
            // Calculer l'ordre pour la nouvelle tâche
            $ordre = $this->getNextOrder($data['projet_id'], $data['statut']);
            
            $stmt->bindParam(':projet_id', $data['projet_id'], PDO::PARAM_INT);
            $stmt->bindParam(':titre', $data['titre'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindParam(':statut', $data['statut'], PDO::PARAM_STR);
            $stmt->bindParam(':priorite', $data['priorite'], PDO::PARAM_STR);
            $stmt->bindParam(':date_echeance', $data['date_echeance']);
            $stmt->bindParam(':assignee_id', $data['assignee_id'], PDO::PARAM_INT);
            $stmt->bindParam(':couleur', $data['couleur'], PDO::PARAM_STR);
            $stmt->bindParam(':tags', $data['tags'], PDO::PARAM_STR);
            $stmt->bindParam(':temps_estime', $data['temps_estime'], PDO::PARAM_INT);
            $stmt->bindParam(':created_by', $data['created_by'], PDO::PARAM_INT);
            $stmt->bindParam(':ordre', $ordre, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return [
                'success' => true,
                'task_id' => $this->db->lastInsertId(),
                'message' => 'Tâche créée avec succès'
            ];
        } catch (Exception $e) {
            error_log("Erreur createTask: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la création de la tâche'
            ];
        }
    }
    
    /**
     * ✏️ Mettre à jour une tâche
     */
    public function updateTask($taskId, $data) {
        try {
            $sql = "UPDATE taches_projet SET 
                    titre = :titre, description = :description, statut = :statut,
                    priorite = :priorite, date_echeance = :date_echeance,
                    assignee_id = :assignee_id, couleur = :couleur, tags = :tags,
                    temps_estime = :temps_estime, temps_passe = :temps_passe
                    WHERE id = :task_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
            $stmt->bindParam(':titre', $data['titre'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindParam(':statut', $data['statut'], PDO::PARAM_STR);
            $stmt->bindParam(':priorite', $data['priorite'], PDO::PARAM_STR);
            $stmt->bindParam(':date_echeance', $data['date_echeance']);
            $stmt->bindParam(':assignee_id', $data['assignee_id'], PDO::PARAM_INT);
            $stmt->bindParam(':couleur', $data['couleur'], PDO::PARAM_STR);
            $stmt->bindParam(':tags', $data['tags'], PDO::PARAM_STR);
            $stmt->bindParam(':temps_estime', $data['temps_estime'], PDO::PARAM_INT);
            $stmt->bindParam(':temps_passe', $data['temps_passe'], PDO::PARAM_INT);
            
            $stmt->execute();
            
            return [
                'success' => true,
                'message' => 'Tâche mise à jour avec succès'
            ];
        } catch (Exception $e) {
            error_log("Erreur updateTask: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ];
        }
    }
    
    /**
     * 🔄 Changer le statut d'une tâche (pour drag & drop)
     */
    public function changeTaskStatus($taskId, $newStatus, $newOrder = null) {
        try {
            $this->db->beginTransaction();
            
            // Récupérer les infos de la tâche
            $task = $this->getTaskById($taskId);
            if (!$task) {
                throw new Exception("Tâche non trouvée");
            }
            
            // Mettre à jour le statut
            $sql = "UPDATE taches_projet SET statut = :statut";
            $params = [':statut' => $newStatus, ':task_id' => $taskId];
            
            // Gérer les dates selon le statut
            if ($newStatus === 'en_cours' && $task['statut'] === 'a_faire') {
                $sql .= ", date_debut = NOW()";
            } elseif ($newStatus === 'termine' && $task['statut'] !== 'termine') {
                $sql .= ", date_fin = NOW()";
            }
            
            // Gérer l'ordre si spécifié
            if ($newOrder !== null) {
                $sql .= ", ordre = :ordre";
                $params[':ordre'] = $newOrder;
            }
            
            $sql .= " WHERE id = :task_id";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Statut mis à jour avec succès'
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Erreur changeTaskStatus: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors du changement de statut'
            ];
        }
    }
    
    /**
     * 🗑️ Supprimer une tâche
     */
    public function deleteTask($taskId) {
        try {
            $sql = "DELETE FROM taches_projet WHERE id = :task_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
            $stmt->execute();
            
            return [
                'success' => true,
                'message' => 'Tâche supprimée avec succès'
            ];
        } catch (Exception $e) {
            error_log("Erreur deleteTask: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ];
        }
    }
    
    /**
     * 📊 Statistiques des tâches d'un projet
     */
    public function getProjectTaskStats($projetId) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN statut = 'a_faire' THEN 1 ELSE 0 END) as a_faire,
                        SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                        SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) as termine,
                        SUM(CASE WHEN priorite = 'haute' THEN 1 ELSE 0 END) as haute_priorite,
                        AVG(temps_passe) as temps_moyen,
                        SUM(temps_estime) as temps_total_estime,
                        SUM(temps_passe) as temps_total_passe
                    FROM taches_projet 
                    WHERE projet_id = :projet_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':projet_id', $projetId, PDO::PARAM_INT);
            $stmt->execute();
            
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calculer le pourcentage de progression
            if ($stats['total'] > 0) {
                $stats['progression'] = round(($stats['termine'] / $stats['total']) * 100, 1);
            } else {
                $stats['progression'] = 0;
            }
            
            return $stats;
        } catch (Exception $e) {
            error_log("Erreur getProjectTaskStats: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 🔍 Récupérer une tâche par ID
     */
    public function getTaskById($taskId) {
        try {
            $sql = "SELECT t.*, p.titre as projet_titre, 
                           u.nom as assignee_nom, u.prenom as assignee_prenom
                    FROM taches_projet t 
                    LEFT JOIN projet p ON t.projet_id = p.id
                    LEFT JOIN users u ON t.assignee_id = u.id
                    WHERE t.id = :task_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getTaskById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 📊 Statistiques utilisateur
     */
    public function getUserTaskStats($userId) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN statut = 'a_faire' THEN 1 ELSE 0 END) as a_faire,
                        SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                        SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) as termine,
                        SUM(CASE WHEN priorite = 'haute' AND statut != 'termine' THEN 1 ELSE 0 END) as urgentes,
                        SUM(CASE WHEN date_echeance < NOW() AND statut != 'termine' THEN 1 ELSE 0 END) as en_retard
                    FROM taches_projet 
                    WHERE assignee_id = :user_id OR created_by = :user_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getUserTaskStats: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 🔢 Calculer le prochain ordre pour une tâche
     */
    private function getNextOrder($projetId, $statut) {
        try {
            $sql = "SELECT COALESCE(MAX(ordre), 0) + 1 as next_order 
                    FROM taches_projet 
                    WHERE projet_id = :projet_id AND statut = :statut";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':projet_id', $projetId, PDO::PARAM_INT);
            $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['next_order'] ?? 1;
        } catch (Exception $e) {
            error_log("Erreur getNextOrder: " . $e->getMessage());
            return 1;
        }
    }
}
?>