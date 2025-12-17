<?php
// controller/prioritycontroller.php
require_once __DIR__ . '/../model/priority.php';
require_once __DIR__ . '/../prioritymanager.php'; // Pour l'analyse auto

class PriorityController {
    private $db;
    private $priorityManager;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->priorityManager = new PriorityManager();
    }

    // Analyse automatique + sauvegarde
    public function analyzeAndSave($reclamation_id, $titre, $description) {
        $analysis = $this->priorityManager->analyzePriority($titre, $description);

        $sql = "INSERT INTO priority_analyses 
                (reclamation_id, priority, score, reason, confidence, analysis_date) 
                VALUES (?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                priority = VALUES(priority),
                score = VALUES(score),
                reason = VALUES(reason),
                confidence = VALUES(confidence),
                analysis_date = NOW()";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $reclamation_id,
            $analysis['priority'],
            $analysis['score'],
            $analysis['reason'],
            $analysis['confidence']
        ]);

        // Mettre à jour aussi la table reclamations
        $sql2 = "UPDATE reclamations SET 
                 priorite = ?, 
                 priority_score = ?, 
                 priority_reason = ? 
                 WHERE id = ?";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([
            $analysis['priority'],
            $analysis['score'],
            $analysis['reason'],
            $reclamation_id
        ]);

        return $analysis;
    }

    // Escalade manuelle de priorité (admin)
    public function escalate($reclamation_id) {
        if (!isAdmin()) {
            return ['success' => false, 'message' => 'Accès refusé'];
        }

        $sql = "SELECT priorite FROM reclamations WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reclamation_id]);
        $current = $stmt->fetchColumn();

        $next = match($current) {
            'basse'    => 'normale',
            'normale'  => 'haute',
            'haute'    => 'critique',
            'critique' => 'critique',
            default    => 'normale'
        };

        if ($next === $current) {
            return ['success' => false, 'message' => 'Priorité déjà au maximum'];
        }

        $sql = "UPDATE reclamations SET priorite = ?, priority_reason = CONCAT(priority_reason, '\n[ESCALADE MANUELLE → ', ?) WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$next, $next, $reclamation_id]);

        // Log dans priority_analyses
        $this->logManualChange($reclamation_id, $next, "Escalade manuelle par admin");

        return ['success' => true, 'new_priority' => $next];
    }

    // Forcer une priorité manuellement
    public function forcePriority($reclamation_id, $new_priority) {
        if (!isAdmin()) {
            return ['success' => false, 'message' => 'Accès refusé'];
        }

        $levels = ['critique', 'haute', 'normale', 'basse'];
        if (!in_array($new_priority, $levels)) {
            return ['success' => false, 'message' => 'Priorité invalide'];
        }

        $sql = "UPDATE reclamations SET priorite = ?, priority_reason = CONCAT(IFNULL(priority_reason,''), '\n[FORCÉE MANUELLEMENT → ', ?) WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$new_priority, $new_priority, $reclamation_id]);

        $this->logManualChange($reclamation_id, $new_priority, "Priorité forcée par admin");

        return ['success' => true, 'new_priority' => $new_priority];
    }

    private function logManualChange($reclamation_id, $priority, $reason) {
        $sql = "INSERT INTO priority_analyses 
                (reclamation_id, priority, score, reason, confidence, analysis_date) 
                VALUES (?, ?, 100, ?, 1.00, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reclamation_id, $priority, $reason]);
    }

    // Récupérer l'historique d'une réclamation
    public function getHistory($reclamation_id) {
        $sql = "SELECT * FROM priority_analyses 
                WHERE reclamation_id = ? 
                ORDER BY analysis_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reclamation_id]);
        return $stmt->fetchAll();
    }
}

// =============================================
// ROUTAGE AJAX (à la fin du fichier)
// =============================================

if (isset($_GET['action']) || isset($_POST['action'])) {
    header('Content-Type: application/json');
    $controller = new PriorityController();

    $action = $_GET['action'] ?? $_POST['action'];

    switch ($action) {
        case 'escalate_priority':
            $id = (int)($_POST['reclamation_id'] ?? 0);
            $result = $controller->escalate($id);
            echo json_encode($result);
            break;

        case 'force_priority':
            $id = (int)($_POST['reclamation_id'] ?? 0);
            $priority = $_POST['new_priority'] ?? '';
            $result = $controller->forcePriority($id, $priority);
            echo json_encode($result);
            break;

        case 'get_history':
            $id = (int)($_GET['id'] ?? 0);
            $history = $controller->getHistory($id);
            echo json_encode(['success' => true, 'history' => $history]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Action inconnue']);
    }
    exit;
}
?>