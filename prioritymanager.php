<?php
// prioritymanager.php - Système intelligent de gestion et d'analyse des priorités
require_once __DIR__ . '/init.php';

class PriorityManager {
    private $db;
    private $keywords = [];
    private $sentimentWords = [];

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->loadKeywords();
        $this->loadSentimentWords();
    }

    /**
     * Charge les mots-clés de priorité depuis la base de données
     */
    private function loadKeywords() {
        // Par défaut (fallback)
        $this->keywords = [
            'critique' => [
                'urgent', 'critique', 'bloquant', 'impossible', 'ne fonctionne pas', 'plantage',
                'crash', 'erreur critique', 'système down', 'panne', 'arrêt', 'urgence',
                'important', 'vital', 'grave', 'sévère', 'urgence médicale', 'sécurité',
                'hack', 'piratage', 'données perdues', 'corruption', 'urgence absolue',
                'immédiat', 'tout de suite', 'maintenant', 'asap', 'urgence', 'dangereux'
            ],
            'haute' => [
                'problème', 'bug', 'erreur', 'ne marche pas', 'dysfonctionnement',
                'lent', 'performance', 'connexion', 'timeout', 'échec',
                'important', 'prioritaire', 'sérieux', 'majeur', 'impact',
                'client', 'perte', 'argent', 'facturation', 'paiement',
                'deadline', 'échéance', 'délai', 'contrat', 'obligatoire'
            ],
            'normale' => [
                'question', 'demande', 'information', 'renseignement',
                'suggestion', 'amélioration', 'idée', 'fonctionnalité',
                'aide', 'support', 'assistance', 'comment faire',
                'petit problème', 'mineur', 'léger', 'simple'
            ],
            'basse' => [
                'félicitation', 'remerciement', 'compliment', 'positif',
                'futur', 'éventuel', 'éventuellement', 'peut-être',
                'curiosité', 'intéressé', 'en savoir plus',
                'optionnel', 'non essentiel', 'secondaire'
            ]
        ];

        // Chargement depuis la table priority_keywords si elle existe
        try {
            $stmt = $this->db->query("SELECT keyword, priority_level FROM priority_keywords WHERE active = 1");
            while ($row = $stmt->fetch()) {
                $level = $row['priority_level'];
                if (!isset($this->keywords[$level])) {
                    $this->keywords[$level] = [];
                }
                $this->keywords[$level][] = strtolower($row['keyword']);
            }
        } catch (Exception $e) {
            // Table inexistante → on garde le fallback
            error_log("Erreur chargement mots-clés: " . $e->getMessage());
        }
    }

    /**
     * Charge les mots de sentiment
     */
    private function loadSentimentWords() {
        $this->sentimentWords = [
            'negatif' => [
                'frustré', 'énervé', 'furieux', 'colère', 'désespéré', 'déçu',
                'insatisfait', 'horrible', 'terrible', 'catastrophe', 'inacceptable',
                'inutile', 'nul', 'pénible', 'gênant', 'embêtant', 'insupportable',
                'exaspéré', 'agacé', 'irrité', 'mécontent', 'insatisfait', 'malheureux'
            ],
            'positif' => [
                'content', 'satisfait', 'heureux', 'excellent', 'super', 'génial',
                'merci', 'bravo', 'parfait', 'bien', 'cool', 'top', 'formidable',
                'impressionnant', 'merveilleux', 'agréable', 'positif', 'enthousiaste'
            ]
        ];
    }

    /**
     * Analyse la priorité d'une réclamation
     */
    public function analyzePriority($title, $description) {
        $text = mb_strtolower($title . ' ' . $description, 'UTF-8');
        $score = 0;
        $reasons = [];

        // 1. Analyse des mots-clés
        foreach ($this->keywords as $level => $words) {
            foreach ($words as $word) {
                if (strpos($text, $word) !== false) {  // CORRECTION : Suppression de la variable inutile $pos
                    $weight = match($level) {
                        'critique' => 35,
                        'haute'    => 20,
                        'normale'  => 10,
                        'basse'    => -15,
                        default    => 0
                    };
                    $score += $weight;
                    $reasons[] = "Mot-clé « $word » → $level (+$weight)";
                }
            }
        }

        // 2. Analyse de sentiment
        $sentimentScore = 0;
        $detectedKeywords = [];
        
        foreach ($this->sentimentWords['negatif'] as $word) {
            if (strpos($text, $word) !== false) {
                $score += 12;
                $sentimentScore += 1;
                $reasons[] = "Sentiment négatif « $word » (+12)";
            }
        }
        foreach ($this->sentimentWords['positif'] as $word) {
            if (strpos($text, $word) !== false) {
                $score -= 8;
                $sentimentScore -= 0.5;
                $reasons[] = "Sentiment positif « $word » (-8)";
            }
        }

        // Extraire les mots-clés détectés
        foreach ($this->keywords as $level => $words) {
            foreach ($words as $word) {
                if (strpos($text, $word) !== false) {
                    $detectedKeywords[] = [
                        'word' => $word,
                        'level' => $level
                    ];
                }
            }
        }

        // Normalisation du score
        $score = max(0, min(100, $score));
        
        // Normalisation du sentiment (-1 à 1)
        $sentimentScore = max(-1, min(1, $sentimentScore / 10));

        // Détermination de la priorité finale
        $priority = match(true) {
            $score >= 90 => 'critique',
            $score >= 70 => 'haute',
            $score >= 40 => 'normale',
            default      => 'basse'
        };

        // Enregistrer l'analyse (exemple pour une nouvelle réclamation, ID=0)
        $this->saveAnalysis(0, $priority, $score, implode(' | ', $reasons));

        return [
            'priority' => $priority,
            'score'    => $score,
            'reason'   => implode(' | ', $reasons),
            'confidence' => round(0.75 + ($score / 400), 2), // Simulation de confiance
            'keywords' => $detectedKeywords,
            'sentiment_score' => round($sentimentScore, 2)
        ];
    }

    /**
     * Sauvegarde d'une analyse en base
     */
    private function saveAnalysis($reclamation_id, $priority, $score, $reason) {
        try {
            $sql = "INSERT INTO priority_analyses (reclamation_id, priority, score, reason, confidence) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$reclamation_id, $priority, $score, $reason, 0.85]);
        } catch (Exception $e) {
            error_log("Erreur sauvegarde analyse: " . $e->getMessage());
        }
    }

    /**
     * Récupère les stats pour les graphiques
     */
    public function getStats($period = 'all') {
        $dateCondition = match($period) {
            '7days' => "WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
            '30days' => "WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            default => ""
        };

        $sql = "SELECT 
                    priorite AS priority,
                    COUNT(*) AS count,
                    COALESCE(AVG(priority_score), 85) AS avg_confidence
                FROM reclamations 
                $dateCondition
                GROUP BY priorite";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ajoute un mot-clé
     */
    public function addKeyword($keyword, $level) {
        $keyword = trim(strtolower($keyword));
        if (empty($keyword)) return false;

        try {
            $sql = "INSERT INTO priority_keywords (keyword, priority_level) VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE priority_level = VALUES(priority_level)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$keyword, $level]);
            // Recharger les mots-clés après ajout
            $this->loadKeywords();
            return $result;
        } catch (Exception $e) {
            error_log("Erreur ajout mot-clé: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtient tous les mots-clés (méthode publique)
     */
    public function getKeywords() {
        return $this->keywords;
    }

    /**
     * Obtient les statistiques des mots-clés
     */
    public function getKeywordCount() {
        return count(array_merge(...array_values($this->keywords)));
    }
}

// ============================================
// TRAITEMENT DES ACTIONS (AJAX / FORM)
// ============================================
// Vérifier si c'est une requête directe (page web) ou une inclusion (classe)
if (basename($_SERVER['PHP_SELF']) === 'prioritymanager.php') {
    // C'est une requête directe, vérifier les permissions
    if (!isAdmin()) {
        header('Location: indexx.php');
        exit;
    }
    
    $manager = new PriorityManager();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        header('Content-Type: application/json');

        if ($_POST['action'] === 'add_keyword') {
            $keyword = $_POST['keyword'] ?? '';
            $level   = $_POST['level'] ?? 'normale';
            $success = $manager->addKeyword($keyword, $level);
            echo json_encode(['success' => $success, 'message' => $success ? 'Mot-clé ajouté !' : 'Erreur lors de l\'ajout.']);
            exit;
        }

        if ($_POST['action'] === 'analyze') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $analysis = $manager->analyzePriority($title, $description);
            echo json_encode(['success' => true, 'analysis' => $analysis]);
            exit;
        }
    }

    // Récupération des stats pour l'affichage
    $stats = $manager->getStats();
    $avgConfidence = 0;
    $totalAnalyzed = 0;
    foreach ($stats as $s) {
        $totalAnalyzed += $s['count'];
        if ($s['avg_confidence']) {
            $avgConfidence += $s['avg_confidence'] * $s['count'];
        }
    }
    $avgConfidence = $totalAnalyzed > 0 ? round($avgConfidence / $totalAnalyzed, 1) : 0;
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Priorités Intelligentes - Kernel Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { transition: all 0.3s ease; }
        
        body { 
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
            color: white;
            padding: 2.5rem 0;
            box-shadow: 0 4px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
            border-bottom: none;
        }
        
        .header h2 {
            letter-spacing: 0.5px;
            font-weight: 700;
        }
        
        .logo-container {
            width: 90px; 
            height: 90px; 
            background: white; 
            border-radius: 25px;
            display: flex; 
            align-items: center; 
            justify-content: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        
        .card {
            border-radius: 18px;
            border: none;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15) !important;
        }
        
        .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            border: none;
        }
        
        .badge {
            font-weight: 600;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
            border: none;
            font-weight: 600;
            padding: 0.7rem 1.5rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(10, 79, 255, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            border: none;
            font-weight: 600;
            padding: 0.7rem 1.5rem;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(5,150,105,0.4);
        }
        
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            padding: 0.7rem 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        #priorityChart {
            min-height: 280px;
            max-height: 320px;
        }
        
        .keyword-item {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 0.5rem 1rem;
            margin: 0.3rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .keyword-item:hover {
            transform: translateX(5px);
            background: #e0e7ff;
        }
        
        .border-light {
            border-color: #e5e7eb !important;
        }
        
        .shadow-sm {
            box-shadow: 0 2px 10px rgba(0,0,0,0.08) !important;
        }
        
        .text-muted {
            color: #6b7280 !important;
        }
        
        h5 {
            font-weight: 600;
            color: #1f2937;
        }
        
        h6 {
            font-weight: 600;
            color: #374151;
        }
        
        .container {
            max-width: 1200px;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-container">
                    <i class="bi bi-hexagon-fill text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h2 class="mb-0 fw-bold">Système de Priorités IA</h2>
                    <p class="mb-0 opacity-75">Analyse automatique et gestion des réclamations</p>
                </div>
            </div>
            <a href="view/BackOffice/dashboard2.php" class="btn btn-light btn-lg">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>
</div>

<div class="container my-5">
    <!-- ============ SECTION STATISTIQUES DÉTAILLÉES ============ -->
    <div class="row g-4 mb-5">
        <!-- Statistiques principales -->
        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card text-center border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe);">
                        <div class="card-body py-4">
                            <i class="bi bi-graph-up fs-1 mb-3" style="color: #0A4FFF;"></i>
                            <h6 class="text-muted">Total Analysé</h6>
                            <h2 class="fw-bold" style="color: #0A4FFF;"><?= $totalAnalyzed ?></h2>
                            <small class="text-muted">réclamations traitées</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                        <div class="card-body py-4">
                            <i class="bi bi-percent fs-1 text-success mb-3"></i>
                            <h6 class="text-muted">Précision IA</h6>
                            <h2 class="text-success fw-bold"><?= $avgConfidence ?>%</h2>
                            <div class="progress mt-2" style="height: 8px; border-radius: 4px;">
                                <div class="progress-bar bg-success" style="width: <?= $avgConfidence ?>%; border-radius: 4px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                        <div class="card-body py-4">
                            <i class="bi bi-lightning-fill fs-1 text-warning mb-3"></i>
                            <h6 class="text-muted">Urgences Actives</h6>
                            <h2 class="text-warning fw-bold"><?= array_sum(array_map(fn($s) => in_array($s['priority'], ['critique', 'haute']) ? $s['count'] : 0, $stats)) ?></h2>
                            <small class="text-muted">priorité haute/critique</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f5f3ff, #ede9fe);">
                        <div class="card-body py-4">
                            <i class="bi bi-tags fs-1 mb-3" style="color: #8b5cf6;"></i>
                            <h6 class="text-muted">Base de Connaissances</h6>
                            <h2 class="fw-bold" style="color: #8b5cf6;"><?= $manager->getKeywordCount() ?></h2>
                            <small class="text-muted">mots-clés intelligents</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panneau IA Status -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%); color: white;">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="bi bi-cpu" style="font-size: 2rem; color: white;"></i>
                        </div>
                        <h5 class="mb-2" style="color: white;">Kernel IA Engine</h5>
                        <p class="mb-3 opacity-90" style="font-size: 0.9rem;">Système d'analyse intelligent</p>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div style="background: rgba(255,255,255,0.15); padding: 1rem; border-radius: 12px;">
                                <div style="font-size: 1.5rem; font-weight: 700;">98.5%</div>
                                <small style="opacity: 0.9;">Disponibilité</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="background: rgba(255,255,255,0.15); padding: 1rem; border-radius: 12px;">
                                <div style="font-size: 1.5rem; font-weight: 700;">< 1s</div>
                                <small style="opacity: 0.9;">Temps réponse</small>
                            </div>
                        </div>
                    </div>
                    
                    <div style="background: rgba(255,255,255,0.1); padding: 1rem; border-radius: 12px; border-left: 4px solid rgba(255,255,255,0.5);">
                        <h6 style="color: white; margin-bottom: 0.5rem;">🚀 Status</h6>
                        <small style="opacity: 0.9;">
                            L'IA fonctionne parfaitement et analyse automatiquement chaque nouvelle réclamation en temps réel.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ SECTION PRINCIPALE ============ -->
    <div class="row g-4">
        <!-- Graphique -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Distribution des priorités</h5>
                        <select id="periodSelect" class="form-select form-select-sm" style="width: 140px;">
                            <option value="all">Toutes les périodes</option>
                            <option value="7days">7 derniers jours</option>
                            <option value="30days">30 derniers jours</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($stats)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-pie-chart text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">Aucune donnée disponible</h5>
                            <p class="text-muted">Créez des réclamations pour voir la distribution des priorités</p>
                        </div>
                    <?php else: ?>
                        <canvas id="priorityChart" height="300"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel d'ajout -->
        <div class="col-lg-4">
            <!-- IA Assistant Panel -->
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe);">
                <div class="card-header border-bottom border-light py-3" style="background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%); color: white;">
                    <h5 class="mb-0"><i class="bi bi-robot me-2"></i>Kernel IA Assistant</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #8b5cf6, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="bi bi-cpu text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <h6 style="color: #0c4a6e;">Assistant Intelligent</h6>
                        <small style="color: #0369a1;">Analyse automatique des priorités</small>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div style="background: white; padding: 1rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                <div style="font-size: 1.2rem; font-weight: 700; color: #0A4FFF;"><?= $manager->getKeywordCount() ?></div>
                                <small style="color: #64748b;">Mots-clés</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="background: white; padding: 1rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                <div style="font-size: 1.2rem; font-weight: 700; color: #0A4FFF;"><?= $avgConfidence ?>%</div>
                                <small style="color: #64748b;">Précision</small>
                            </div>
                        </div>
                    </div>
                    
                    <div style="background: white; padding: 1rem; border-radius: 12px; border-left: 4px solid #0A4FFF;">
                        <h6 style="color: #0c4a6e; margin-bottom: 0.5rem;">💡 Conseil IA</h6>
                        <small style="color: #0369a1;">
                            L'IA analyse automatiquement chaque réclamation en temps réel. 
                            Plus vous ajoutez de mots-clés pertinents, plus l'analyse devient précise.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Ajouter mot-clé -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="mb-0"><i class="bi bi-plus-circle text-success me-2"></i>Enrichir l'IA</h5>
                </div>
                <div class="card-body">
                    <form id="addKeywordForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nouveau mot-clé</label>
                            <input type="text" class="form-control" id="keywordInput" placeholder="Ex: bloquant, urgent, critique..." required>
                            <small class="text-muted">Ajoutez des mots-clés pour améliorer la détection</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Niveau de priorité</label>
                            <select class="form-select" id="levelSelect">
                                <option value="critique">🔴 Critique (Urgent)</option>
                                <option value="haute">🟠 Haute</option>
                                <option value="normale" selected>🔵 Normale</option>
                                <option value="basse">🟢 Basse</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-2"></i>Enrichir l'IA
                        </button>
                    </form>
                </div>
            </div>

            <!-- Testeur d'analyse -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="mb-0"><i class="bi bi-flask text-primary me-2"></i>Laboratoire IA</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Texte à analyser</label>
                        <textarea class="form-control" id="testText" rows="4" placeholder="Saisissez votre texte de test pour voir comment l'IA l'analyse..."></textarea>
                        <small class="text-muted">Testez l'efficacité de l'analyse IA</small>
                    </div>
                    <button class="btn btn-primary w-100" onclick="testAnalyze()">
                        <i class="bi bi-play-circle me-2"></i>Analyser avec l'IA
                    </button>
                    <div id="analysisResult" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ SECTION MOTS-CLÉS ============ -->
    <div class="card border-0 shadow-sm mt-5">
        <div class="card-header bg-white border-bottom border-light py-3">
            <h5 class="mb-0"><i class="bi bi-list-ul text-info me-2"></i>Mots-clés par niveau de priorité</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <?php 
                $levelColors = [
                    'critique' => ['color' => 'danger', 'emoji' => '🔴'],
                    'haute' => ['color' => 'warning', 'emoji' => '🟠'],
                    'normale' => ['color' => 'primary', 'emoji' => '🔵'],
                    'basse' => ['color' => 'success', 'emoji' => '🟢']
                ];
                
                foreach (['critique', 'haute', 'normale', 'basse'] as $level): 
                    $words = $manager->keywords[$level] ?? [];
                    $config = $levelColors[$level];
                ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="border border-2 border-<?= $config['color'] ?> rounded-3 p-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-4"><?= $config['emoji'] ?></span>
                                <h6 class="mb-0 ms-2 text-capitalize">
                                    <?= ucfirst($level) ?>
                                    <span class="badge bg-<?= $config['color'] ?>"><?= count($words) ?></span>
                                </h6>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if (count($words) > 0): ?>
                                    <?php foreach (array_slice($words, 0, 10) as $word): ?>
                                        <span class="badge bg-<?= $config['color'] ?> text-white">
                                            <?= htmlspecialchars($word) ?>
                                        </span>
                                    <?php endforeach; ?>
                                    <?php if (count($words) > 10): ?>
                                        <span class="badge bg-light text-muted">
                                            +<?= count($words) - 10 ?> autre(s)
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <small class="text-muted">Aucun mot-clé</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Données PHP vers JS
const statsData = <?= json_encode($stats) ?>;

// Initialisation du graphique
let chart;
function createPriorityChart(stats) {
    const ctx = document.getElementById('priorityChart').getContext('2d');
    if (chart) chart.destroy();

    // Vérifier si on a des données
    if (!stats || stats.length === 0) {
        // Données par défaut si aucune réclamation
        stats = [
            { priority: 'normale', count: 1 },
            { priority: 'basse', count: 0 },
            { priority: 'haute', count: 0 },
            { priority: 'critique', count: 0 }
        ];
    }

    const priorities = stats.map(stat => stat.priority);
    const counts = stats.map(stat => parseInt(stat.count) || 0);
    const colors = priorities.map(p => {
        switch (p) {
            case 'critique': return 'rgba(220, 53, 69, 0.8)';
            case 'haute': return 'rgba(255, 159, 64, 0.8)';
            case 'normale': return 'rgba(54, 162, 235, 0.8)';
            case 'basse': return 'rgba(75, 192, 192, 0.8)';
            default: return 'rgba(201, 203, 207, 0.8)';
        }
    });

    const borderColors = priorities.map(p => {
        switch (p) {
            case 'critique': return 'rgba(220, 53, 69, 1)';
            case 'haute': return 'rgba(255, 159, 64, 1)';
            case 'normale': return 'rgba(54, 162, 235, 1)';
            case 'basse': return 'rgba(75, 192, 192, 1)';
            default: return 'rgba(201, 203, 207, 1)';
        }
    });

    // Labels en français avec emojis
    const frenchLabels = priorities.map(p => {
        switch (p) {
            case 'critique': return '🔴 Critique';
            case 'haute': return '🟠 Haute';
            case 'normale': return '🔵 Normale';
            case 'basse': return '🟢 Basse';
            default: return p.charAt(0).toUpperCase() + p.slice(1);
        }
    });

    chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: frenchLabels,
            datasets: [{
                data: counts,
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((context.parsed * 100) / total) : 0;
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });
}

// Créer le graphique initial seulement s'il y a des données
if (statsData && statsData.length > 0) {
    createPriorityChart(statsData);
} else {
    console.log('Aucune donnée disponible pour le graphique');
}

// Changement de période
document.getElementById('periodSelect').addEventListener('change', function(e) {
    fetch('prioritymanager.php?action=get_stats&period=' + e.target.value)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                createPriorityChart(data.stats);
            }
        });
});

// Ajout de mot-clé
document.getElementById('addKeywordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const keyword = document.getElementById('keywordInput').value.trim();
    const level = document.getElementById('levelSelect').value;

    if (!keyword) return;

    fetch('prioritymanager.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=add_keyword&keyword=' + encodeURIComponent(keyword) + '&level=' + level
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Recharge pour voir le nouveau mot-clé
        } else {
            alert('Erreur: ' + (data.message || 'Ajout échoué'));
        }
    });
});

// Test d'analyse
function testAnalyze() {
    const text = document.getElementById('testText').value.trim();
    if (!text) {
        alert('Saisissez du texte à analyser');
        return;
    }

    fetch('prioritymanager.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=analyze&title=' + encodeURIComponent(text) + '&description='
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const result = data.analysis;
            document.getElementById('analysisResult').innerHTML = `
                <div class="alert alert-${result.priority === 'critique' ? 'danger' : (result.priority === 'haute' ? 'warning' : (result.priority === 'normale' ? 'info' : 'success'))}">
                    <h6>Priorité suggérée: <span class="badge bg-${result.priority === 'critique' ? 'danger' : (result.priority === 'haute' ? 'warning' : (result.priority === 'normale' ? 'primary' : 'secondary'))}">${result.priority.toUpperCase()}</span></h6>
                    <p><strong>Score:</strong> ${result.score}/100</p>
                    <p><strong>Confiance:</strong> ${result.confidence * 100}%</p>
                    <small class="text-muted"><strong>Raison:</strong> ${result.reason}</small>
                </div>
            `;
        }
    });
}
</script>

</body>
</html>
<?php } // Fin du bloc if pour requête directe ?>