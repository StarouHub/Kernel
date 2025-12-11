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
                    AVG(priority_score) AS avg_confidence
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
        header('Location: index.php');
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
            background: linear-gradient(135deg, #1e3a8a 0%, #5b21b6 100%);
            color: white;
            padding: 2.5rem 0;
            box-shadow: 0 15px 40px rgba(30,58,138,0.4);
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
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border: none;
            font-weight: 600;
            padding: 0.7rem 1.5rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30,58,138,0.4);
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
                    <i class="bi bi-robot text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h2 class="mb-0 fw-bold">Système de Priorités IA</h2>
                    <p class="mb-0 opacity-75">Analyse automatique et gestion des réclamations</p>
                </div>
            </div>
            <a href="view/BackOffice/dashboard.php" class="btn btn-light btn-lg">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>
</div>

<div class="container my-5">
    <!-- ============ SECTION STATISTIQUES ============ -->
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm h-100">
                <div class="card-body py-4">
                    <i class="bi bi-graph-up fs-1 text-primary mb-3"></i>
                    <h6 class="text-muted">Réclamations analysées</h6>
                    <h2 class="text-primary fw-bold"><?= $totalAnalyzed ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm h-100">
                <div class="card-body py-4">
                    <i class="bi bi-percent fs-1 text-success mb-3"></i>
                    <h6 class="text-muted">Confiance moyenne</h6>
                    <h2 class="text-success fw-bold"><?= $avgConfidence ?>%</h2>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: <?= $avgConfidence ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm h-100">
                <div class="card-body py-4">
                    <i class="bi bi-tags fs-1 text-info mb-3"></i>
                    <h6 class="text-muted">Mots-clés actifs</h6>
                    <h2 class="text-info fw-bold"><?= $manager->getKeywordCount() ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm h-100">
                <div class="card-body py-4">
                    <i class="bi bi-lightning-fill fs-1 text-warning mb-3"></i>
                    <h6 class="text-muted">Urgences détectées</h6>
                    <h2 class="text-warning fw-bold"><?= array_sum(array_map(fn($s) => in_array($s['priority'], ['critique', 'haute']) ? $s['count'] : 0, $stats)) ?></h2>
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
                    <canvas id="priorityChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Panel d'ajout -->
        <div class="col-lg-4">
            <!-- Ajouter mot-clé -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="mb-0"><i class="bi bi-plus-circle text-success me-2"></i>Ajouter un mot-clé</h5>
                </div>
                <div class="card-body">
                    <form id="addKeywordForm">
                        <div class="mb-3">
                            <label class="form-label small">Mot-clé</label>
                            <input type="text" class="form-control" id="keywordInput" placeholder="Ex: bloquant, urgent..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Niveau de priorité</label>
                            <select class="form-select" id="levelSelect">
                                <option value="critique">🔴 Critique (Urgent)</option>
                                <option value="haute">🟠 Haute</option>
                                <option value="normale" selected>🔵 Normale</option>
                                <option value="basse">🟢 Basse</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-2"></i>Ajouter le mot-clé
                        </button>
                    </form>
                </div>
            </div>

            <!-- Testeur d'analyse -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="mb-0"><i class="bi bi-robot text-primary me-2"></i>Tester l'analyse</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small">Titre + Description</label>
                        <textarea class="form-control" id="testText" rows="4" placeholder="Saisissez votre texte de test..."></textarea>
                    </div>
                    <button class="btn btn-primary w-100" onclick="testAnalyze()">
                        <i class="bi bi-play-circle me-2"></i>Analyser
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

    const priorities = stats.map(stat => stat.new_priority);
    const counts = stats.map(stat => stat.count);
    const colors = priorities.map(p => {
        switch (p) {
            case 'critique': return 'rgba(220, 53, 69, 0.8)';
            case 'haute': return 'rgba(255, 159, 64, 0.8)';
            case 'normale': return 'rgba(54, 162, 235, 0.8)';
            case 'basse': return 'rgba(153, 102, 255, 0.8)';
            default: return 'rgba(201, 203, 207, 0.8)';
        }
    });

    chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: priorities.map(p => p.charAt(0).toUpperCase() + p.slice(1)),
            datasets: [{
                data: counts,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

// Créer le graphique initial
createPriorityChart(statsData);

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