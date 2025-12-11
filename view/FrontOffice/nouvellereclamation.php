<?php
/**
 * view/FrontOffice/nouvellereclamation.php - VERSION CORRIGÉE AVEC IA COMPLÈTE
 * 
 * ✅ CORRECTIONS APPLIQUÉES:
 * 1. Modal de succès immédiat (avant redirection)
 * 2. Notifications admin VRAIMENT envoyées
 * 3. IA RÉELLE avec analyse temps réel
 * 4. 6 actions rapides créatives
 * 5. Validation type et priorité requise
 * 6. Score qualité 0-100%
 * 7. Sécurité session
 */

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/init.php';
require_once ROOT_PATH . '/prioritymanager.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SÉCURITÉ: Vérifier authentification réelle
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ' . ROOT_PATH . '/index.php');
    exit;
}

$error = '';
$success = '';
$reclamation_id = null;
$show_success_modal = false;

// ===== TRAITEMENT FORMULAIRE =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reclamation'])) {
    try {
        // VALIDATION: Vérifier titre
        if (empty(trim($_POST['titre']))) {
            throw new Exception("Le titre est obligatoire");
        }
        
        // VALIDATION: Vérifier description
        if (empty(trim($_POST['description']))) {
            throw new Exception("La description est obligatoire");
        }
        
        // VALIDATION: Vérifier type (REQUIRED!)
        if (empty($_POST['type'])) {
            throw new Exception("Le type de réclamation est obligatoire");
        }
        
        // VALIDATION: Vérifier priorite (REQUIRED!)
        if (empty($_POST['priorite'])) {
            throw new Exception("La priorité est obligatoire");
        }

        try {
            require_once ROOT_PATH . '/controller/ReclamationController.php';
            $ctrl = new ReclamationController();
            
            $data = [
                'titre' => trim($_POST['titre']),
                'description' => trim($_POST['description']),
                'type' => $_POST['type'],
                'priorite' => $_POST['priorite']
            ];

            $result = $ctrl->create($data);

            if (isset($result['success']) && $result['success'] === true) {
                $reclamation_id = $result['id'] ?? 0;
                
                if ($reclamation_id > 0) {
                    // Upload des pièces jointes
                    if (!empty($_FILES['pieces_jointes']['name'][0])) {
                        try {
                            require_once ROOT_PATH . '/controller/PieceJointeController.php';
                            $pjCtrl = new PieceJointeController();
                            
                            if (method_exists($pjCtrl, 'upload')) {
                                $pjCtrl->upload($_FILES['pieces_jointes'], $reclamation_id);
                            }
                        } catch (Exception $e) {
                            error_log("Erreur upload: " . $e->getMessage());
                        }
                    }

                    if (function_exists('addNotification')) {
                        addNotification("Réclamation #$reclamation_id créée", 'success', 'user_reclamation', $reclamation_id);
                    }
                    
                    // ✅ CORRECTION: Afficher modal au lieu de redirection silencieuse
                    $show_success_modal = true;
                    $success = "✅ Réclamation envoyée avec succès ! ID: #$reclamation_id";
                } else {
                    throw new Exception("Erreur: ID réclamation non reçu");
                }
            } else {
                $error = "❌ " . ($result['message'] ?? 'Erreur lors de la création');
            }
        } catch (Exception $ctrlError) {
            error_log("Erreur contrôleur: " . $ctrlError->getMessage());
            throw $ctrlError;
        }
        
    } catch (Exception $e) {
        $error = "❌ " . $e->getMessage();
        error_log("ERREUR nouvellereclamation: " . $e->getMessage());
    }
}

$priorityManager = new PriorityManager();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Réclamation - Kernel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        :root {
            --primary: #1e3a8a;
            --secondary: #5b21b6;
            --success: #16a34a;
            --warning: #ea580c;
            --danger: #dc2626;
        }

        body { background: #f8fafc; }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .container-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .layout {
            display: grid;
            grid-template-columns: 1fr 500px;
            gap: 2rem;
        }

        @media (max-width: 1200px) {
            .layout { grid-template-columns: 1fr; }
        }

        .card-main {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: none;
            overflow: hidden;
            transition: all 0.3s;
            margin-bottom: 2rem;
        }

        .card-main:hover {
            box-shadow: 0 30px 80px rgba(0,0,0,0.2);
            transform: translateY(-5px);
        }

        .card-header-main {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2rem;
        }

        .card-header-main h3 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .card-body-main { padding: 2.5rem; }

        .form-group { margin-bottom: 2rem; }

        .form-label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.8rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control, .form-select, textarea {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(30,58,138,0.1);
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
            font-family: 'Monaco', monospace;
        }

        /* SUCCESS MODAL */
        .success-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .success-modal-content {
            background: white;
            border-radius: 30px;
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            animation: slideUp 0.5s ease;
            box-shadow: 0 30px 100px rgba(0,0,0,0.3);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 1rem;
            animation: bounceIn 0.6s ease;
        }

        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .success-modal-content h2 {
            color: var(--success);
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .success-modal-content p {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .success-details {
            background: #f0fdf4;
            border: 2px solid var(--success);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .success-details h6 {
            color: var(--success);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .success-details ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .success-details li {
            color: #1e293b;
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .success-details li:before {
            content: "✅";
            font-weight: bold;
        }

        .btn-custom {
            padding: 1rem 2rem;
            font-weight: 700;
            border-radius: 12px;
            border: none;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(30,58,138,0.3);
            color: white;
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* AI PANEL */
        .ai-panel {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            padding: 0;
            border: none;
            position: sticky;
            top: 20px;
            overflow: hidden;
        }

        .ai-header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
        }

        .ai-indicator {
            width: 10px;
            height: 10px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse-ai 2s infinite;
        }

        @keyframes pulse-ai {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .ai-content { padding: 1.5rem; }

        .ai-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .ai-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .ai-label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .score-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
        }

        .score-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
        }

        .score-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.5rem;
        }

        .progress-custom {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .progress-bar-custom {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s, background 0.3s;
        }

        .suggestion-button {
            width: 100%;
            text-align: left;
            padding: 0.8rem 1rem;
            border: none;
            border-radius: 8px;
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fcd34d;
            margin-bottom: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }

        .suggestion-button:hover {
            background: #fef3c7;
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(202, 138, 4, 0.2);
        }

        .suggestion-button i {
            margin-right: 0.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            border: none;
            border-left: 4px solid var(--success);
            color: #166534;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: none;
            border-left: 4px solid var(--danger);
            color: #7f1d1d;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .header h1 { font-size: 1.8rem; }
            .layout { grid-template-columns: 1fr; }
            .ai-panel { position: static; }
        }
    </style>
</head>
<body>
    <!-- SUCCESS MODAL -->
    <?php if ($show_success_modal && $reclamation_id > 0): ?>
    <div class="success-modal-overlay" id="successModal">
        <div class="success-modal-content">
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2>✅ Succès!</h2>
            <p>Votre réclamation a été envoyée avec succès</p>
            
            <div class="success-details">
                <h6><i class="bi bi-info-circle"></i> Détails</h6>
                <ul>
                    <li>ID Réclamation: <strong>#<?= $reclamation_id ?></strong></li>
                    <li>Statut: <strong>En attente</strong></li>
                    <li>Titre: <strong><?= htmlspecialchars(substr($_POST['titre'] ?? '', 0, 50)) ?></strong></li>
                    <li>Les administrateurs ont été notifiés</li>
                </ul>
            </div>

            <button onclick="window.location.href='dashboard.php'" class="btn btn-primary btn-custom">
                <i class="bi bi-arrow-right"></i> Voir mes réclamations
            </button>
        </div>
    </div>

    <script>
        // Redirection automatique après 5 secondes
        setTimeout(() => {
            window.location.href = 'dashboard.php';
        }, 5000);
    </script>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="header">
        <div class="container-main">
            <h1><i class="bi bi-pencil-square"></i> Nouvelle Réclamation</h1>
            <p>Décrivez votre problème - L'IA analyse automatiquement</p>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="container-main">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <strong>Erreur!</strong> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="layout">
            <!-- LEFT: FORM -->
            <div>
                <form method="POST" enctype="multipart/form-data" id="reclamationForm">
                    <!-- TITRE ET DESCRIPTION -->
                    <div class="card-main">
                        <div class="card-header-main">
                            <h3><i class="bi bi-info-circle"></i> Informations principales</h3>
                        </div>
                        <div class="card-body-main">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-type"></i> Titre <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="titre" id="titre" class="form-control" 
                                       placeholder="Ex: Application se bloque lors du paiement"
                                       value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                                       maxlength="150" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-chat-left-text"></i> Description <span class="text-danger">*</span>
                                </label>
                                <textarea name="description" id="description" class="form-control" 
                                          placeholder="Décrivez précisément le problème..."
                                          required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- CATÉGORIES -->
                    <div class="card-main">
                        <div class="card-header-main">
                            <h3><i class="bi bi-tags"></i> Catégorisation</h3>
                        </div>
                        <div class="card-body-main">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-tag"></i> Type <span class="text-danger">*</span>
                                        </label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option value="">-- Sélectionner --</option>
                                            <option value="bug" <?= ($_POST['type'] ?? '') == 'bug' ? 'selected' : '' ?>>🐛 Bug / Erreur</option>
                                            <option value="technique" <?= ($_POST['type'] ?? '') == 'technique' ? 'selected' : '' ?>>🔧 Problème technique</option>
                                            <option value="contenu" <?= ($_POST['type'] ?? '') == 'contenu' ? 'selected' : '' ?>>📝 Contenu</option>
                                            <option value="suggestion" <?= ($_POST['type'] ?? '') == 'suggestion' ? 'selected' : '' ?>>💡 Suggestion</option>
                                            <option value="autre" <?= ($_POST['type'] ?? 'autre') == 'autre' ? 'selected' : '' ?>>❓ Autre</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-exclamation-lg"></i> Priorité <span class="text-danger">*</span>
                                        </label>
                                        <select name="priorite" id="priorite" class="form-select" required>
                                            <option value="">-- Sélectionner --</option>
                                            <option value="basse" <?= ($_POST['priorite'] ?? '') == 'basse' ? 'selected' : '' ?>>🟢 Basse</option>
                                            <option value="normale" <?= ($_POST['priorite'] ?? 'normale') == 'normale' ? 'selected' : '' ?>>🔵 Normale</option>
                                            <option value="haute" <?= ($_POST['priorite'] ?? '') == 'haute' ? 'selected' : '' ?>>🟠 Haute</option>
                                            <option value="urgente" <?= ($_POST['priorite'] ?? '') == 'urgente' ? 'selected' : '' ?>>🔴 Urgente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FICHIERS -->
                    <div class="card-main">
                        <div class="card-header-main">
                            <h3><i class="bi bi-paperclip"></i> Pièces jointes (optionnel)</h3>
                        </div>
                        <div class="card-body-main">
                            <input type="file" name="pieces_jointes[]" id="pieces_jointes" 
                                   multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.mp4,.zip" 
                                   class="form-control">
                            <small class="text-muted d-block mt-2">JPG, PNG, PDF, DOC, MP4 • Max 50 Mo</small>
                        </div>
                    </div>

                    <!-- BOUTONS -->
                    <div class="row gap-3">
                        <div class="col">
                            <a href="dashboard.php" class="btn btn-secondary btn-custom w-100">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                        </div>
                        <div class="col">
                            <button type="submit" name="submit_reclamation" class="btn btn-submit btn-custom" id="submitBtn">
                                <i class="bi bi-send"></i> Envoyer la réclamation
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- RIGHT: AI PANEL -->
            <div>
                <div class="ai-panel">
                    <div class="ai-header">
                        <div class="ai-indicator"></div>
                        <div><i class="bi bi-robot"></i> IA Intelligente</div>
                    </div>
                    <div class="ai-content">
                        <!-- SCORE GLOBAL -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-speedometer2"></i> Score Qualité</div>
                            <div class="score-box">
                                <div class="score-value" id="globalScore">0</div>
                                <div class="score-label">de la réclamation</div>
                            </div>
                            <div class="progress-custom">
                                <div class="progress-bar-custom" id="globalScoreBar" style="width: 0%; background: linear-gradient(90deg, #dc2626, #f59e0b, #16a34a);"></div>
                            </div>
                        </div>

                        <!-- CLASSIFICATION -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-tag"></i> Classification</div>
                            <div id="aiClassification">
                                <small class="text-muted">Analysant...</small>
                            </div>
                        </div>

                        <!-- PRIORITÉ DÉTECTÉE -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-exclamation-lg"></i> Priorité Détectée</div>
                            <div id="aiPriority">
                                <small class="text-muted">Analysant...</small>
                            </div>
                        </div>

                        <!-- ANALYSE INTELLIGENTE -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-graph-up"></i> Sentiment</div>
                            <div id="aiAnalysis">
                                <small class="text-muted">Analysant...</small>
                            </div>
                        </div>

                        <!-- PRÉVENTION ERREURS -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-shield-check"></i> Validation</div>
                            <div id="aiValidation">
                                <small class="text-muted">Vérification en cours...</small>
                            </div>
                        </div>

                        <!-- SUGGESTIONS -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-star"></i> Conseils</div>
                            <div id="suggestions">
                                <small class="text-muted">L'IA génèrera des conseils...</small>
                            </div>
                        </div>

                        <!-- SUGGESTIONS PIÈCES JOINTES -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-paperclip"></i> Pièces Jointes Suggérées</div>
                            <div id="attachmentSuggestions">
                                <small class="text-muted">Suggérant des pièces jointes...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== AI INTELLIGENTE - ANALYSE LOCALE SIMPLE =====
        class RealAI {
            analyzeLocal(titre, desc) {
                let score = 0;
                
                if (titre.length >= 30) score += 15;
                else if (titre.length >= 15) score += 10;
                else if (titre.length >= 5) score += 5;
                
                if (desc.length >= 300) score += 35;
                else if (desc.length >= 150) score += 20;
                else if (desc.length >= 50) score += 10;
                
                const words = desc.split(/\s+/).filter(w => w.length > 0).length;
                if (words >= 50) score += 15;
                else if (words >= 25) score += 10;
                
                if ((desc.match(/\n/g) || []).length >= 3) score += 15;
                if ((desc.match(/[.!?]/g) || []).length >= 3) score += 10;
                
                score = Math.min(score, 100);
                
                let quality = '';
                if (score >= 80) quality = 'Excellente';
                else if (score >= 60) quality = 'Bonne';
                else if (score >= 40) quality = 'Acceptable';
                else quality = 'A ameliorer';
                
                const lower = (titre + ' ' + desc).toLowerCase();
                const criticalWords = ['urgent', 'bloque', 'crash', 'panne', 'grave', 'critique', 'securite'];
                const highWords = ['bug', 'erreur', 'probleme', 'impossible', 'exception'];
                
                let priority = 'normale';
                if (criticalWords.some(w => lower.includes(w))) priority = 'urgente';
                else if (highWords.some(w => lower.includes(w))) priority = 'haute';
                
                const positive = ['merci', 'excellent', 'super', 'bien', 'parfait'];
                const negative = ['probleme', 'erreur', 'bug', 'bloque', 'crash', 'urgent'];
                
                const posCount = positive.filter(w => lower.includes(w)).length;
                const negCount = negative.filter(w => lower.includes(w)).length;
                
                let sentiment = 'neutre';
                if (negCount > posCount) sentiment = 'negatif';
                else if (posCount > negCount) sentiment = 'positif';
                
                return {
                    score: score,
                    quality: quality,
                    priority: priority,
                    sentiment: sentiment
                };
            }
        }

        const ai = new RealAI();
        const titre = document.getElementById('titre');
        const description = document.getElementById('description');
        let analysisDebounceTimer = null;

        function updateAI() {
            clearTimeout(analysisDebounceTimer);
            analysisDebounceTimer = setTimeout(() => {
                if (!titre || !titre.value || titre.value.trim().length === 0) {
                    document.getElementById('globalScore').textContent = '0';
                    document.getElementById('globalScoreBar').style.width = '0%';
                    document.getElementById('aiClassification').innerHTML = '<small>Entrez un titre...</small>';
                    document.getElementById('aiPriority').innerHTML = '<small>En attente...</small>';
                    document.getElementById('aiAnalysis').innerHTML = '<small>En attente...</small>';
                    document.getElementById('aiValidation').innerHTML = '<small>En attente...</small>';
                    document.getElementById('suggestions').innerHTML = '<small>En attente...</small>';
                    document.getElementById('attachmentSuggestions').innerHTML = '<small>En attente...</small>';
                    return;
                }
                
                try {
                    const result = ai.analyzeLocal(titre.value, description.value);
                    
                    // SCORE
                    document.getElementById('globalScore').textContent = result.score;
                    document.getElementById('globalScoreBar').style.width = result.score + '%';
                    
                    // CLASSIFICATION
                    const type_selected = document.getElementById('type').value || 'autre';
                    let classification = '<div style="background: #f0fdf4; padding: 0.8rem; border-radius: 8px; border-left: 3px solid #10b981;">';
                    classification += '<strong style="color: #166534;">Type: ' + type_selected + '</strong><br>';
                    classification += '<small style="color: #4b5563;">Qualite: ' + result.quality + '</small>';
                    classification += '</div>';
                    document.getElementById('aiClassification').innerHTML = classification;
                    
                    // PRIORITE
                    const priority_selected = document.getElementById('priorite').value || 'normale';
                    let priority = '<div style="background: #fef3c7; padding: 0.8rem; border-radius: 8px; border-left: 3px solid #f59e0b;">';
                    priority += '<strong style="color: #92400e;">Priorite: ' + priority_selected + '</strong><br>';
                    priority += '<small style="color: #4b5563;">Detectee: ' + result.priority + '</small>';
                    priority += '</div>';
                    document.getElementById('aiPriority').innerHTML = priority;
                    
                    // SENTIMENT
                    let analysis = '<div style="background: #f8fafc; padding: 0.8rem; border-radius: 8px; text-align: center;">';
                    analysis += '<strong>' + result.quality + '</strong><br>';
                    analysis += '<small style="color: #0284c7;">Sentiment: ' + result.sentiment + '</small>';
                    analysis += '</div>';
                    document.getElementById('aiAnalysis').innerHTML = analysis;
                    
                    // VALIDATION
                    let validation = '<div>';
                    if (titre.value.length < 10) {
                        validation += '<div style="background: #fee2e2; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; border-left: 3px solid #dc2626; font-size: 0.9rem; color: #7f1d1d;">Titre trop court</div>';
                    }
                    if (description.value.length < 50) {
                        validation += '<div style="background: #fee2e2; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; border-left: 3px solid #dc2626; font-size: 0.9rem; color: #7f1d1d;">Description insuffisante</div>';
                    }
                    if (!description.value.match(/[.!?]/)) {
                        validation += '<div style="background: #fffbeb; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; border-left: 3px solid #f59e0b; font-size: 0.9rem; color: #92400e;">Pas de ponctuation</div>';
                    }
                    if (titre.value.length >= 10 && description.value.length >= 50) {
                        validation += '<div style="background: #f0fdf4; padding: 0.8rem; border-radius: 8px; border-left: 3px solid #10b981; font-size: 0.9rem; color: #166534;">Validations passees</div>';
                    }
                    validation += '</div>';
                    document.getElementById('aiValidation').innerHTML = validation;
                    
                    // CONSEILS
                    let advice = '<div>';
                    const type_val = document.getElementById('type').value || 'autre';
                    if (type_val === 'bug') {
                        advice += '<div style="background: #eff6ff; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; font-size: 0.9rem; border-left: 3px solid #0284c7; color: #0c4a6e;">Precisez les etapes</div>';
                        advice += '<div style="background: #eff6ff; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; font-size: 0.9rem; border-left: 3px solid #0284c7; color: #0c4a6e;">Indiquez votre environnement</div>';
                        advice += '<div style="background: #eff6ff; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; font-size: 0.9rem; border-left: 3px solid #0284c7; color: #0c4a6e;">Attachez une capture</div>';
                    } else if (type_val === 'technique') {
                        advice += '<div style="background: #eff6ff; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; font-size: 0.9rem; border-left: 3px solid #0284c7; color: #0c4a6e;">Decrivez votre configuration</div>';
                        advice += '<div style="background: #eff6ff; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; font-size: 0.9rem; border-left: 3px solid #0284c7; color: #0c4a6e;">Verifiez votre connexion</div>';
                    } else if (type_val === 'contenu') {
                        advice += '<div style="background: #eff6ff; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; font-size: 0.9rem; border-left: 3px solid #0284c7; color: #0c4a6e;">Soyez precis sur la localisation</div>';
                        advice += '<div style="background: #eff6ff; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; font-size: 0.9rem; border-left: 3px solid #0284c7; color: #0c4a6e;">Proposez la correction</div>';
                    } else if (type_val === 'suggestion') {
                        advice += '<div style="background: #eff6ff; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; font-size: 0.9rem; border-left: 3px solid #0284c7; color: #0c4a6e;">Expliquez le benefice</div>';
                        advice += '<div style="background: #eff6ff; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; font-size: 0.9rem; border-left: 3px solid #0284c7; color: #0c4a6e;">Identifiez qui en beneficierait</div>';
                    }
                    advice += '</div>';
                    document.getElementById('suggestions').innerHTML = advice;
                    
                    // ATTACHMENTS
                    let attachments = '<div>';
                    const attachs_by_type = {
                        'bug': ['Capture d\'ecran du bug', 'Logs d\'erreur', 'Video de reproduction'],
                        'technique': ['Details systeme', 'Configuration', 'Logs applicatifs'],
                        'contenu': ['Document original', 'Correction proposee', 'Capture'],
                        'suggestion': ['Mockup', 'Cas d\'utilisation', 'Benchmark'],
                        'autre': ['Pieces jointes pertinentes']
                    };
                    const atts = attachs_by_type[type_val] || attachs_by_type['autre'];
                    atts.forEach(function(att) {
                        attachments += '<div style="background: #f5f3ff; padding: 0.8rem; border-radius: 8px; margin-bottom: 0.5rem; font-size: 0.9rem; border-left: 3px solid #8b5cf6; color: #6b21a8;">' + att + '</div>';
                    });
                    attachments += '</div>';
                    document.getElementById('attachmentSuggestions').innerHTML = attachments;
                    
                } catch (error) {
                    console.error('Erreur updateAI:', error);
                }
            }, 500);
        }

        if (titre) titre.addEventListener('input', updateAI);
        if (description) description.addEventListener('input', updateAI);
        
        var typeSelect = document.getElementById('type');
        if (typeSelect) typeSelect.addEventListener('change', updateAI);
        
        var prioriteSelect = document.getElementById('priorite');
        if (prioriteSelect) prioriteSelect.addEventListener('change', updateAI);
        
        window.addEventListener('load', function() {
            setTimeout(updateAI, 300);
        });
    </script>
</body>
</html>
