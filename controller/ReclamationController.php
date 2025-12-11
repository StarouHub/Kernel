<?php
// controller/ReclamationController.php
require_once __DIR__ . '/../model/Reclamation.php';
require_once __DIR__ . '/../model/Reponse.php';
require_once __DIR__ . '/../prioritymanager.php'; // NOUVEAU: inclure PriorityManager

class ReclamationController {
    private $db;
    private $priorityManager; // NOUVEAU: instance du PriorityManager

    // Constantes pour les scores de priorité
    const SCORE_CRITICAL = 90;
    const SCORE_HIGH = 70;
    const SCORE_MEDIUM = 40;
    const SCORE_LOW = 10;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->priorityManager = new PriorityManager(); // NOUVEAU: initialiser
    }

    public function create($data) {
        try {
            // Validation des données
            if (empty(trim($data['titre']))) {
                throw new Exception("Le titre est obligatoire");
            }
            
            if (empty(trim($data['description']))) {
                throw new Exception("La description est obligatoire");
            }

            // NOUVEAU: Validation du type
            $type = trim($data['type'] ?? '');
            if (empty($type)) {
                throw new Exception("Le type de réclamation est obligatoire");
            }
            
            // Valider que le type est dans la liste autorisée
            $allowedTypes = ['bug', 'technique', 'contenu', 'suggestion', 'autre'];
            if (!in_array($type, $allowedTypes)) {
                throw new Exception("Type invalide. Les types autorisés sont: " . implode(', ', $allowedTypes));
            }

            // NOUVEAU: Validation de la priorité
            $priorite = trim($data['priorite'] ?? '');
            if (empty($priorite)) {
                throw new Exception("La priorité est obligatoire");
            }

            $allowedPriorities = ['basse', 'normale', 'haute', 'urgente', 'critique'];
            if (!in_array($priorite, $allowedPriorities)) {
                throw new Exception("Priorité invalide. Les priorités autorisées sont: " . implode(', ', $allowedPriorities));
            }
            
            // NOUVEAU: Analyse automatique de la priorité
            $autoPriority = $this->priorityManager->analyzePriority(
                trim($data['titre']), 
                trim($data['description'])
            );
            
            // Utiliser la priorité automatique si aucune n'est fournie
            $priorite = $data['priorite'] ?? $autoPriority['priority'];
            $priorityScore = $autoPriority['score'];
            $priorityReason = $autoPriority['reason'];
            
            // Créer l'objet Reclamation
            $r = new Reclamation();
            $r->setTitre(trim($data['titre']));
            $r->setDescription(trim($data['description']));
            $r->setType($data['type']); // Utiliser la valeur validée
            $r->setPriorite($priorite);
            $r->setPriorityScore($priorityScore); // NOUVEAU
            $r->setPriorityReason($priorityReason); // NOUVEAU
            $r->setStatut('en-attente');
            
            // Vérifier que user_id existe
            if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
                throw new Exception("Session utilisateur invalide. Veuillez vous reconnecter.");
            }
            
            $r->setUtilisateurId($_SESSION['user_id']);
            
            // Commencer une transaction
            $this->db->beginTransaction();
            
            // NOUVEAU: Requête SQL mise à jour avec les champs de priorité
            $sql = "INSERT INTO reclamations 
                    (utilisateur_id, titre, description, type, priorite, statut, 
                     priority_score, priority_reason, date_creation) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $r->getUtilisateurId(),
                $r->getTitre(),
                $r->getDescription(),
                $r->getType(),
                $r->getPriorite(),
                $r->getStatut(),
                $r->getPriorityScore(),
                $r->getPriorityReason()
            ]);
            
            $reclamation_id = $this->db->lastInsertId();
            
            // Valider la transaction AVANT les logs (pour éviter que les logs fassent échouer la création)
            $this->db->commit();
            
            // NOUVEAU: Log de l'analyse de priorité (après commit pour ne pas bloquer)
            try {
                $this->logPriorityAnalysis($reclamation_id, $autoPriority, $priorite);
            } catch (Exception $e) {
                error_log("Erreur log analyse priorité (non bloquant): " . $e->getMessage());
            }
            
            // CORRECTION : Notification aux administrateurs (version améliorée)
            try {
                $this->notifyAdminsNewReclamation($reclamation_id, $r, $autoPriority);
            } catch (Exception $e) {
                error_log("Erreur notification admin (non bloquant): " . $e->getMessage());
            }
            
            // Enregistrer l'activité (version améliorée) - non bloquant
            try {
                $this->logActivity('reclamation_create', [
                    'reclamation_id' => $reclamation_id,
                    'titre' => $r->getTitre(),
                    'type' => $r->getType(),
                    'priorite' => $r->getPriorite(),
                    'priority_score' => $r->getPriorityScore(),
                    'priority_reason' => $r->getPriorityReason(),
                    'auto_priority' => $autoPriority['priority'],
                    'confidence' => $autoPriority['confidence']
                ]);
            } catch (Exception $e) {
                error_log("Erreur log activité (non bloquant): " . $e->getMessage());
            }
            
            // NOUVEAU: Vérifier si c'est une priorité critique (non bloquant)
            if ($r->getPriorite() === 'critique') {
                try {
                    $this->escalateCriticalPriority($reclamation_id, $r);
                } catch (Exception $e) {
                    error_log("Erreur escalade priorité critique (non bloquant): " . $e->getMessage());
                }
            }
            
            return [
                'success' => true,
                'id' => $reclamation_id,
                'message' => 'Réclamation créée avec succès. ID: #' . $reclamation_id,
                'priority_info' => [ // NOUVEAU: retourner les infos de priorité
                    'assigned' => $r->getPriorite(),
                    'auto_suggested' => $autoPriority['priority'],
                    'score' => $r->getPriorityScore(),
                    'reason' => $r->getPriorityReason()
                ]
            ];
            
        } catch (Exception $e) {
            // Annuler en cas d'erreur
            if ($this->db->inTransaction()) {
                try {
                    $this->db->rollBack();
                } catch (Exception $rollbackError) {
                    error_log("Erreur lors du rollback: " . $rollbackError->getMessage());
                }
            }
            
            $errorMessage = $e->getMessage();
            error_log("Erreur création réclamation: " . $errorMessage);
            error_log("Trace: " . $e->getTraceAsString());
            
            // Message d'erreur plus détaillé pour le debug
            $userMessage = 'Erreur lors de la création de la réclamation.';
            if (strpos($errorMessage, 'SQLSTATE') !== false) {
                $userMessage .= ' Erreur de base de données. Veuillez contacter l\'administrateur.';
            } else {
                $userMessage .= ' ' . $errorMessage;
            }
            
            return [
                'success' => false,
                'message' => $userMessage,
                'debug' => $errorMessage // Pour le debug en développement
            ];
        }
    }

    // ==================== MÉTHODES D'ANALYSE IA ====================

    /**
     * Détecte le type de réclamation basé sur le titre et la description
     */
    private function detectType($titre, $description) {
        $text = mb_strtolower($titre . ' ' . $description, 'UTF-8');
        
        // Mots-clés pour chaque type
        $bugKeywords = ['bug', 'erreur', 'plant', 'crash', 'ne marche pas', 'ne fonctionne pas', 
                        'bloqué', 'bloquant', 'impossible', 'échec', 'échoue', 'exception'];
        $techniqueKeywords = ['lent', 'performance', 'connexion', 'timeout', 'chargement', 
                             'déconnexion', 'réseau', 'serveur', 'api', 'base de données'];
        $contenuKeywords = ['faute', 'orthographe', 'texte', 'contenu', 'image', 'photo', 
                           'lien', 'url', 'page', 'affichage', 'traduction'];
        $suggestionKeywords = ['suggestion', 'amélioration', 'idée', 'fonctionnalité', 
                               'nouveau', 'ajouter', 'proposer', 'recommandation'];
        
        // Compter les occurrences
        $bugCount = 0;
        $techniqueCount = 0;
        $contenuCount = 0;
        $suggestionCount = 0;
        
        foreach ($bugKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) $bugCount++;
        }
        foreach ($techniqueKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) $techniqueCount++;
        }
        foreach ($contenuKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) $contenuCount++;
        }
        foreach ($suggestionKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) $suggestionCount++;
        }
        
        // Retourner le type avec le plus de correspondances
        $scores = [
            'bug' => $bugCount,
            'technique' => $techniqueCount,
            'contenu' => $contenuCount,
            'suggestion' => $suggestionCount
        ];
        
        $maxScore = max($scores);
        if ($maxScore > 0) {
            return array_search($maxScore, $scores);
        }
        
        return 'autre';
    }

    /**
     * Reformule le titre pour le rendre plus professionnel
     */
    private function reformulateTitle($titre) {
        $titre = trim($titre);
        
        // Capitaliser la première lettre
        if (!empty($titre)) {
            $titre = mb_strtoupper(mb_substr($titre, 0, 1, 'UTF-8'), 'UTF-8') . 
                     mb_substr($titre, 1, null, 'UTF-8');
        }
        
        // Supprimer les points d'exclamation multiples
        $titre = preg_replace('/!{2,}/', '!', $titre);
        
        // Ajouter un point final si manquant
        if (!empty($titre) && !in_array(mb_substr($titre, -1, 1, 'UTF-8'), ['.', '!', '?'])) {
            $titre .= '.';
        }
        
        return $titre;
    }

    /**
     * Reformule la description pour améliorer la clarté
     */
    private function reformulateDescription($description) {
        $description = trim($description);
        
        // Capitaliser la première lettre
        if (!empty($description)) {
            $description = mb_strtoupper(mb_substr($description, 0, 1, 'UTF-8'), 'UTF-8') . 
                          mb_substr($description, 1, null, 'UTF-8');
        }
        
        // Supprimer les espaces multiples
        $description = preg_replace('/\s+/', ' ', $description);
        
        // Ajouter un point final si manquant
        if (!empty($description) && !in_array(mb_substr($description, -1, 1, 'UTF-8'), ['.', '!', '?'])) {
            $description .= '.';
        }
        
        return $description;
    }

    /**
     * Suggère des pièces jointes basées sur le contenu
     */
    private function suggestAttachments($titre, $description) {
        $text = mb_strtolower($titre . ' ' . $description, 'UTF-8');
        $suggestions = [];
        
        if (preg_match('/écran|capture|screenshot|image|photo|visuel/', $text)) {
            $suggestions[] = 'capture d\'écran';
        }
        if (preg_match('/facture|commande|achat|paiement|transaction/', $text)) {
            $suggestions[] = 'numéro de facture ou référence';
        }
        if (preg_match('/log|erreur|trace|débug|console/', $text)) {
            $suggestions[] = 'fichier log ou message d\'erreur';
        }
        if (preg_match('/vidéo|enregistrement|démonstration|tutoriel/', $text)) {
            $suggestions[] = 'vidéo de démonstration';
        }
        if (preg_match('/document|fichier|pdf|word|excel/', $text)) {
            $suggestions[] = 'document concerné';
        }
        
        return array_unique($suggestions);
    }

    /**
     * Calcule le score de confiance de l'analyse
     */
    private function calculateConfidence($titre, $description) {
        $confidence = 0.5; // Base de confiance
        
        // Plus le texte est long, plus la confiance augmente
        $totalLength = mb_strlen($titre . ' ' . $description, 'UTF-8');
        if ($totalLength > 100) $confidence += 0.1;
        if ($totalLength > 200) $confidence += 0.1;
        if ($totalLength > 500) $confidence += 0.1;
        
        // Présence de détails techniques augmente la confiance
        $technicalIndicators = ['erreur', 'code', 'ligne', 'fichier', 'système', 'navigateur', 'version'];
        $foundIndicators = 0;
        $text = mb_strtolower($titre . ' ' . $description, 'UTF-8');
        foreach ($technicalIndicators as $indicator) {
            if (strpos($text, $indicator) !== false) {
                $foundIndicators++;
            }
        }
        $confidence += min(0.2, $foundIndicators * 0.05);
        
        // Limiter entre 0.5 et 0.95
        return min(0.95, max(0.5, $confidence));
    }

    /**
     * Extrait les mots-clés importants du texte
     */
    private function extractKeywords($titre, $description) {
        $text = mb_strtolower($titre . ' ' . $description, 'UTF-8');
        
        // Mots à ignorer (stop words)
        $stopWords = ['le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 
                     'est', 'sont', 'a', 'ont', 'être', 'avoir', 'faire', 'pour', 
                     'avec', 'sans', 'dans', 'sur', 'par', 'ce', 'cette', 'ces'];
        
        // Extraire les mots
        $words = preg_split('/[\s\p{P}]+/u', $text);
        $keywords = [];
        
        foreach ($words as $word) {
            $word = trim($word);
            if (mb_strlen($word, 'UTF-8') > 3 && !in_array($word, $stopWords)) {
                if (!isset($keywords[$word])) {
                    $keywords[$word] = 0;
                }
                $keywords[$word]++;
            }
        }
        
        // Trier par fréquence et retourner les 10 premiers
        arsort($keywords);
        return array_slice(array_keys($keywords), 0, 10);
    }

    /**
     * Enregistre une activité dans le journal d'audit (non bloquant)
     */
    private function logActivity($action, $data = []) {
        try {
            // Vérifier si la table existe avant d'insérer
            $checkTable = "SHOW TABLES LIKE 'activity_logs'";
            $result = $this->db->query($checkTable);
            if ($result->rowCount() == 0) {
                // Table n'existe pas, créer une version simplifiée
                $createTable = "CREATE TABLE IF NOT EXISTS activity_logs (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT,
                    action VARCHAR(100),
                    data TEXT,
                    ip_address VARCHAR(45),
                    user_agent VARCHAR(255),
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_user (user_id),
                    INDEX idx_action (action),
                    INDEX idx_created (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $this->db->exec($createTable);
            }
            
            $sql = "INSERT INTO activity_logs 
                    (user_id, action, data, ip_address, user_agent, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $_SESSION['user_id'] ?? null,
                $action,
                json_encode($data),
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
            
            return true;
        } catch (Exception $e) {
            // Ne pas faire échouer la création de réclamation si le log échoue
            error_log("Erreur log activité (non bloquant): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Assigne une réclamation à un utilisateur
     */
    private function assignTo($reclamation_id, $user_id) {
        try {
            $sql = "UPDATE reclamations SET assigne_a = ?, date_modification = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $reclamation_id]);
            
            // Log de l'assignation
            $this->logActivity('reclamation_assigned', [
                'reclamation_id' => $reclamation_id,
                'assigned_to' => $user_id
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log("Erreur assignation: " . $e->getMessage());
            return false;
        }
    }

    public function analyzeWithAI($titre, $description) {
        try {
            // NOUVEAU: Intégrer l'analyse du PriorityManager avec l'IA existante
            $priorityAnalysis = $this->priorityManager->analyzePriority($titre, $description);
            
            // Analyse IA existante (complétée)
            $analysis = [
                'type' => $this->detectType($titre, $description),
                'priorite' => $priorityAnalysis['priority'], // Utiliser la priorité analysée
                'titre_reformule' => $this->reformulateTitle($titre),
                'description_reformulee' => $this->reformulateDescription($description),
                'suggestions' => $this->suggestAttachments($titre, $description),
                'confidence' => $this->calculateConfidence($titre, $description),
                'keywords' => $this->extractKeywords($titre, $description),
                // NOUVEAU: Données du PriorityManager
                'priority_score' => $priorityAnalysis['score'],
                'priority_reason' => $priorityAnalysis['reason'],
                'priority_confidence' => $priorityAnalysis['confidence'],
                'detected_keywords' => $priorityAnalysis['keywords'] ?? [],
                'sentiment_score' => $priorityAnalysis['sentiment_score'] ?? 0
            ];
            
            // NOUVEAU: Synergie entre l'IA et le PriorityManager
            $analysis['final_priority'] = $this->synergizePriorityAnalysis(
                $analysis['priorite'],
                $priorityAnalysis,
                $analysis['confidence']
            );
            
            return [
                'success' => true,
                'analysis' => $analysis,
                'message' => 'Analyse IA complétée avec un score de confiance de ' . 
                            ($analysis['confidence'] * 100) . '%' .
                            ' - Priorité détectée: ' . $analysis['final_priority']['priority'] .
                            ' (Score: ' . $analysis['final_priority']['score'] . '%)'
            ];
            
        } catch (Exception $e) {
            error_log("Erreur analyse IA: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'analyse IA'];
        }
    }

    // ==================== NOUVELLES MÉTHODES POUR PRIORITY MANAGER ====================

    /**
     * NOUVEAU: Log de l'analyse de priorité (non bloquant)
     */
    private function logPriorityAnalysis($reclamation_id, $analysis, $final_priority) {
        try {
            // Vérifier si la table existe avant d'insérer
            $checkTable = "SHOW TABLES LIKE 'priority_analysis_logs'";
            $result = $this->db->query($checkTable);
            if ($result->rowCount() == 0) {
                // Table n'existe pas, créer une version simplifiée
                $createTable = "CREATE TABLE IF NOT EXISTS priority_analysis_logs (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    reclamation_id INT NOT NULL,
                    auto_priority VARCHAR(20),
                    final_priority VARCHAR(20),
                    score INT DEFAULT 0,
                    confidence DECIMAL(3,2) DEFAULT 0.5,
                    detected_keywords TEXT,
                    sentiment_score DECIMAL(3,2) DEFAULT 0,
                    reason TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_reclamation (reclamation_id),
                    INDEX idx_created (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $this->db->exec($createTable);
            }
            
            $sql = "INSERT INTO priority_analysis_logs 
                    (reclamation_id, auto_priority, final_priority, score, 
                     confidence, detected_keywords, sentiment_score, reason, 
                     created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $reclamation_id,
                $analysis['priority'] ?? 'normale',
                $final_priority,
                $analysis['score'] ?? 0,
                $analysis['confidence'] ?? 0.5,
                json_encode($analysis['keywords'] ?? []),
                $analysis['sentiment_score'] ?? 0,
                $analysis['reason'] ?? ''
            ]);
            
            return true;
        } catch (Exception $e) {
            // Ne pas faire échouer la création si le log échoue
            error_log("Erreur log analyse priorité (non bloquant): " . $e->getMessage());
            return false;
        }
    }

    /**
     * NOUVEAU: Notification améliorée avec info priorité
     */
    private function notifyAdminsNewReclamation($reclamation_id, $reclamation, $priorityAnalysis) {
        try {
            $sql = "SELECT id, nom, email FROM users WHERE role = 'admin' OR role = 'supervisor'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $admins = $stmt->fetchAll();
            
            if (empty($admins)) {
                error_log("Aucun admin trouvé pour la notification");
                return;
            }

            $username = htmlspecialchars($_SESSION['nom'] ?? 'Utilisateur');
            $title = substr($reclamation->getTitre(), 0, 80);
            $priority = strtoupper($reclamation->getPriorite());
            
            // Déterminer l'émoji, icon et type basé sur priorité
            $priorityEmoji = [
                'urgente' => '🔴',
                'haute' => '🟠',
                'normale' => '🔵',
                'basse' => '🟢'
            ];
            
            $priorityIcon = [
                'urgente' => 'bi-exclamation-circle-fill',
                'haute' => 'bi-exclamation-circle',
                'normale' => 'bi-info-circle',
                'basse' => 'bi-check-circle'
            ];
            
            $notificationType = [
                'urgente' => 'danger',
                'haute' => 'warning',
                'normale' => 'info',
                'basse' => 'success'
            ];
            
            $emoji = $priorityEmoji[$reclamation->getPriorite()] ?? '⚪';
            $icon = $priorityIcon[$reclamation->getPriorite()] ?? 'bi-info-circle';
            $type = $notificationType[$reclamation->getPriorite()] ?? 'info';
            
            foreach ($admins as $admin) {
                // Message court et lisible pour les notifications SESSION
                $shortMsg = sprintf(
                    "%s [#%d] %s - %s (Score IA: %d%%)",
                    $emoji,
                    $reclamation_id,
                    $title,
                    $username,
                    $priorityAnalysis['score'] ?? 50
                );
                
                // Message détaillé pour la base de données
                $detailedMsg = sprintf(
                    "NOUVELLE RÉCLAMATION #%d\nPriorité: %s (Score IA: %d%%)\nUtilisateur: %s\nTitre: %s\nType: %s\nAnalyse IA: %s",
                    $reclamation_id,
                    $priority,
                    $priorityAnalysis['score'] ?? 50,
                    $username,
                    $title,
                    ucfirst($reclamation->getType() ?? 'autre'),
                    $priorityAnalysis['reason'] ?? 'Analyse standard'
                );
                
                // Ajouter à notifications_history (Base de données)
                try {
                    $notifSql = "INSERT INTO notifications_history 
                                (user_id, message, type, category, reclamation_id, is_read, created_at) 
                                VALUES (?, ?, ?, ?, ?, 0, NOW())";
                    $notifStmt = $this->db->prepare($notifSql);
                    $result = $notifStmt->execute([
                        $admin['id'],
                        $detailedMsg,
                        $type,
                        'new_reclamation_priority',
                        $reclamation_id
                    ]);
                    
                    if (!$result) {
                        error_log("Échec insertion notification pour admin: " . $admin['id']);
                    }
                } catch (Exception $e) {
                    error_log("Exception lors notification: " . $e->getMessage());
                    // Créer la table si elle n'existe pas
                    try {
                        $this->createNotificationsTable();
                        $notifStmt = $this->db->prepare($notifSql);
                        $notifStmt->execute([
                            $admin['id'],
                            $detailedMsg,
                            $type,
                            'new_reclamation_priority',
                            $reclamation_id
                        ]);
                    } catch (Exception $createError) {
                        error_log("Impossible de créer/utiliser notifications_history: " . $createError->getMessage());
                    }
                }
            }
            
        } catch (Exception $e) {
            error_log("Erreur notification admin: " . $e->getMessage());
        }
    }

    /**
     * Créer la table notifications_history si elle n'existe pas
     */
    private function createNotificationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS notifications_history (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            message LONGTEXT NOT NULL,
            type VARCHAR(50) DEFAULT 'info',
            category VARCHAR(50) DEFAULT 'general',
            reclamation_id INT,
            is_read BOOLEAN DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (reclamation_id) REFERENCES reclamations(id)
        )";
        
        $this->db->exec($sql);
    }

    /**
     * NOUVEAU: Escalade des priorités critiques
     */
    private function escalateCriticalPriority($reclamation_id, $reclamation) {
        try {
            // Log d'escalade
            $this->logActivity('priority_escalation', [
                'reclamation_id' => $reclamation_id,
                'priority' => $reclamation->getPriorite(),
                'reason' => $reclamation->getPriorityReason()
            ]);
            
            // Assigner automatiquement à un superviseur disponible
            $this->autoAssignToSupervisor($reclamation_id);
            
            // Créer une tâche urgente
            $this->createUrgentTask($reclamation_id, $reclamation);
            
        } catch (Exception $e) {
            error_log("Erreur escalade priorité: " . $e->getMessage());
        }
    }

    /**
     * NOUVEAU: Assignation automatique aux superviseurs
     */
    private function autoAssignToSupervisor($reclamation_id) {
        try {
            // Chercher un superviseur avec le moins de réclamations assignées
            $sql = "SELECT u.id, u.nom, 
                    (SELECT COUNT(*) FROM reclamations r2 WHERE r2.assigne_a = u.id AND r2.statut = 'en-cours') as workload
                    FROM users u 
                    WHERE u.role = 'supervisor' AND u.is_active = 1
                    ORDER BY workload ASC, RAND() 
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $supervisor = $stmt->fetch();
            
            if ($supervisor) {
                $this->assignTo($reclamation_id, $supervisor['id']);
                
                // Notification spéciale au superviseur dans la base de données
                try {
                    $notifSql = "INSERT INTO notifications_history 
                                (user_id, message, type, category, reclamation_id, is_read, created_at) 
                                VALUES (?, ?, ?, ?, ?, 0, NOW())";
                    $notifStmt = $this->db->prepare($notifSql);
                    $notifStmt->execute([
                        $supervisor['id'],
                        "🚨 RÉCLAMATION CRITIQUE #{$reclamation_id} ASSIGNÉE\n" .
                        "Cette réclamation a été détectée comme critique et vous a été assignée automatiquement.",
                        'danger',
                        'critical_assignment',
                        $reclamation_id
                    ]);
                } catch (Exception $e) {
                    error_log("Erreur notification superviseur: " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            error_log("Erreur assignation auto: " . $e->getMessage());
        }
    }

    /**
     * NOUVEAU: Création de tâche urgente
     */
    private function createUrgentTask($reclamation_id, $reclamation) {
        try {
            $sql = "INSERT INTO urgent_tasks 
                    (reclamation_id, title, description, priority, assigned_to, 
                     deadline, created_at) 
                    VALUES (?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 2 HOUR), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $reclamation_id,
                "[CRITIQUE] Réclamation #" . $reclamation_id . " - " . $reclamation->getTitre(),
                $reclamation->getDescription() . "\n\nPriorité: " . $reclamation->getPriorite() . 
                "\nScore: " . $reclamation->getPriorityScore() . 
                "\nRaison: " . $reclamation->getPriorityReason(),
                'critical',
                $_SESSION['user_id']
            ]);
        } catch (Exception $e) {
            error_log("Erreur création tâche urgente: " . $e->getMessage());
        }
    }

    /**
     * NOUVEAU: Synergie entre l'analyse IA et PriorityManager
     */
    private function synergizePriorityAnalysis($iaPriority, $pmAnalysis, $iaConfidence) {
        // Poids des différentes analyses
        $pmWeight = 0.6;  // PriorityManager plus fiable pour la priorité
        $iaWeight = 0.4;  // IA plus fiable pour le type
        
        // Convertir les priorités en scores
        $priorityScores = [
            'critique' => 90,
            'haute' => 70,
            'normale' => 40,
            'basse' => 10
        ];
        
        $pmScore = $priorityScores[$pmAnalysis['priority']] ?? 40;
        $iaScore = $priorityScores[$iaPriority] ?? 40;
        
        // Calcul du score final pondéré
        $finalScore = ($pmScore * $pmWeight) + ($iaScore * $iaWeight);
        
        // Déterminer la priorité finale basée sur le score
        if ($finalScore >= self::SCORE_CRITICAL) {
            $finalPriority = 'critique';
        } elseif ($finalScore >= self::SCORE_HIGH) {
            $finalPriority = 'haute';
        } elseif ($finalScore >= self::SCORE_MEDIUM) {
            $finalPriority = 'normale';
        } else {
            $finalPriority = 'basse';
        }
        
        // Si les deux systèmes sont en désaccord mais confiants, prendre le plus élevé
        if ($iaPriority !== $pmAnalysis['priority'] && 
            $pmAnalysis['confidence'] > 0.8 && $iaConfidence > 0.7) {
            $finalPriority = $pmScore > $iaScore ? $pmAnalysis['priority'] : $iaPriority;
        }
        
        return [
            'priority' => $finalPriority,
            'score' => round($finalScore),
            'ia_priority' => $iaPriority,
            'pm_priority' => $pmAnalysis['priority'],
            'synergy_score' => $pmAnalysis['confidence'] * $iaConfidence
        ];
    }

    /**
     * NOUVEAU: Vérifier et mettre à jour les priorités en retard
     */
    public function checkAndUpdateOverduePriorities() {
        try {
            $sql = "SELECT r.*, 
                    DATEDIFF(NOW(), r.date_creation) as days_old,
                    TIMESTAMPDIFF(HOUR, r.date_creation, NOW()) as hours_old
                    FROM reclamations r
                    WHERE r.statut = 'en-attente' 
                    AND r.priorite != 'critique'
                    AND (
                        (r.priorite = 'haute' AND TIMESTAMPDIFF(HOUR, r.date_creation, NOW()) > 24) OR
                        (r.priorite = 'normale' AND TIMESTAMPDIFF(HOUR, r.date_creation, NOW()) > 48) OR
                        (r.priorite = 'basse' AND DATEDIFF(NOW(), r.date_creation) > 5)
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $overdueReclamations = $stmt->fetchAll();
            
            $updatedCount = 0;
            
            foreach ($overdueReclamations as $reclamation) {
                // Escalader la priorité
                $newPriority = $this->getEscalatedPriority($reclamation['priorite']);
                
                $updateSql = "UPDATE reclamations 
                             SET priorite = ?, priority_reason = CONCAT(priority_reason, ' | Escalade automatique après ', ?, ' heures'), 
                                 date_modification = NOW() 
                             WHERE id = ?";
                
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([
                    $newPriority,
                    $reclamation['hours_old'],
                    $reclamation['id']
                ]);
                
                $updatedCount++;
                
                // Log de l'escalade
                $this->logActivity('auto_priority_escalation', [
                    'reclamation_id' => $reclamation['id'],
                    'old_priority' => $reclamation['priorite'],
                    'new_priority' => $newPriority,
                    'hours_old' => $reclamation['hours_old']
                ]);
            }
            
            return $updatedCount;
            
        } catch (Exception $e) {
            error_log("Erreur vérification priorités en retard: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * NOUVEAU: Obtenir la priorité escaladée
     */
    private function getEscalatedPriority($currentPriority) {
        $priorityHierarchy = ['basse' => 'normale', 'normale' => 'haute', 'haute' => 'critique', 'critique' => 'critique'];
        return $priorityHierarchy[$currentPriority] ?? $currentPriority;
    }

    /**
     * Récupère toutes les réclamations avec filtres optionnels
     */
    public function getAll($user_id = null, $filters = []) {
        try {
            $conditions = [];
            $params = [];
            
            // Filtre par utilisateur
            if ($user_id !== null) {
                $conditions[] = "r.utilisateur_id = ?";
                $params[] = $user_id;
            }
            
            // Filtres optionnels
            if (!empty($filters['priorite'])) {
                $conditions[] = "r.priorite = ?";
                $params[] = $filters['priorite'];
            }
            
            if (!empty($filters['statut'])) {
                $conditions[] = "r.statut = ?";
                $params[] = $filters['statut'];
            }
            
            if (!empty($filters['type'])) {
                $conditions[] = "r.type = ?";
                $params[] = $filters['type'];
            }
            
            if (!empty($filters['search'])) {
                $conditions[] = "(r.titre LIKE ? OR r.description LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $sql = "SELECT r.*, 
                    u.nom as auteur,
                    DATEDIFF(NOW(), r.date_creation) as jours_ecoules,
                    TIMESTAMPDIFF(HOUR, r.date_creation, NOW()) as heures_ecoulees,
                    (SELECT COUNT(*) FROM reponses rep WHERE rep.reclamation_id = r.id) as nombre_reponses,
                    CASE 
                        WHEN r.statut = 'en-attente' AND r.priorite = 'critique' AND TIMESTAMPDIFF(HOUR, r.date_creation, NOW()) > 2 THEN 1
                        WHEN r.statut = 'en-attente' AND r.priorite = 'haute' AND TIMESTAMPDIFF(HOUR, r.date_creation, NOW()) > 24 THEN 1
                        WHEN r.statut = 'en-attente' AND r.priorite = 'normale' AND TIMESTAMPDIFF(HOUR, r.date_creation, NOW()) > 48 THEN 1
                        ELSE 0
                    END as est_en_retard
                    FROM reclamations r
                    LEFT JOIN users u ON r.utilisateur_id = u.id
                    $whereClause
                    ORDER BY 
                        CASE r.priorite 
                            WHEN 'critique' THEN 1
                            WHEN 'haute' THEN 2
                            WHEN 'normale' THEN 3
                            WHEN 'basse' THEN 4
                        END,
                        r.date_creation DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Erreur getAll réclamations: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les détails d'une réclamation
     */
    public function getDetails($id) {
        try {
            $sql = "SELECT r.*, u.nom as auteur
                    FROM reclamations r
                    LEFT JOIN users u ON r.utilisateur_id = u.id
                    WHERE r.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $reclamation = $stmt->fetch();
            
            if (!$reclamation) {
                return ['success' => false, 'message' => 'Réclamation non trouvée'];
            }
            
            // Récupérer les réponses
            require_once __DIR__ . '/ReponseController.php';
            $reponseCtrl = new ReponseController();
            $reponses = $reponseCtrl->getReponsesByReclamation($id);
            
            // Récupérer les pièces jointes
            require_once __DIR__ . '/PieceJointeController.php';
            $pjCtrl = new PieceJointeController();
            $piecesJointes = [];
            if (method_exists($pjCtrl, 'getByReclamation')) {
                $piecesJointes = $pjCtrl->getByReclamation($id);
            }
            
            return [
                'success' => true,
                'reclamation' => $reclamation,
                'reponses' => $reponses,
                'pieces_jointes' => $piecesJointes
            ];
            
        } catch (Exception $e) {
            error_log("Erreur getDetails réclamation: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors du chargement'];
        }
    }

    /**
     * Met à jour le statut d'une réclamation
     */
    public function updateStatut($id, $statut) {
        try {
            $allowedStatuses = ['en-attente', 'en-cours', 'resolue', 'fermee'];
            if (!in_array($statut, $allowedStatuses)) {
                return ['success' => false, 'message' => 'Statut invalide'];
            }
            
            $sql = "UPDATE reclamations SET statut = ?, date_modification = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$statut, $id]);
            
            $this->logActivity('reclamation_status_update', [
                'reclamation_id' => $id,
                'new_status' => $statut
            ]);
            
            return ['success' => true, 'message' => 'Statut mis à jour'];
            
        } catch (Exception $e) {
            error_log("Erreur updateStatut: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
        }
    }

    /**
     * Récupère les statistiques générales
     */
    public function getStats() {
        try {
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'en-attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN statut = 'en-cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN statut = 'resolue' THEN 1 ELSE 0 END) as resolues,
                    SUM(CASE WHEN statut = 'fermee' THEN 1 ELSE 0 END) as fermees,
                    SUM(CASE WHEN priorite = 'critique' OR priorite = 'urgente' THEN 1 ELSE 0 END) as urgentes,
                    COALESCE((SELECT COUNT(DISTINCT reclamation_id) FROM piecejointes), 0) as avec_pieces_jointes,
                    0 as en_retard,
                    0 as sans_reponse
                    FROM reclamations";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch();
            
            return $stats ?: [
                'total' => 0,
                'en_attente' => 0,
                'en_cours' => 0,
                'resolues' => 0,
                'fermees' => 0,
                'urgentes' => 0,
                'avec_pieces_jointes' => 0,
                'en_retard' => 0,
                'sans_reponse' => 0
            ];
            
        } catch (Exception $e) {
            error_log("Erreur getStats: " . $e->getMessage());
            return [
                'total' => 0,
                'en_attente' => 0,
                'en_cours' => 0,
                'resolues' => 0,
                'fermees' => 0,
                'urgentes' => 0,
                'avec_pieces_jointes' => 0,
                'en_retard' => 0,
                'sans_reponse' => 0
            ];
        }
    }

    /**
     * NOUVEAU: Statistiques de priorité
     */
    public function getPriorityStats($period = '30days') {
        try {
            $dateCondition = match($period) {
                '7days' => "WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
                '30days' => "WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
                '90days' => "WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 90 DAY)",
                default => ""
            };
            
            $sql = "SELECT 
                    priorite,
                    COUNT(*) as total,
                    AVG(priority_score) as avg_score,
                    SUM(CASE WHEN statut = 'en-attente' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN statut = 'en-cours' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN statut = 'resolue' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN statut = 'fermee' THEN 1 ELSE 0 END) as closed,
                    AVG(TIMESTAMPDIFF(HOUR, date_creation, 
                        CASE WHEN statut IN ('resolue', 'fermee') THEN date_modification ELSE NOW() END)) as avg_resolution_time
                    FROM reclamations 
                    {$dateCondition}
                    GROUP BY priorite
                    ORDER BY 
                        CASE priorite 
                            WHEN 'critique' THEN 1
                            WHEN 'haute' THEN 2
                            WHEN 'normale' THEN 3
                            WHEN 'basse' THEN 4
                        END";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetchAll();
            
            // Calculer les pourcentages
            $total = array_sum(array_column($stats, 'total'));
            foreach ($stats as &$stat) {
                $stat['percentage'] = $total > 0 ? round(($stat['total'] / $total) * 100, 1) : 0;
            }
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Erreur statistiques priorité: " . $e->getMessage());
            return [];
        }
    }
}
?>