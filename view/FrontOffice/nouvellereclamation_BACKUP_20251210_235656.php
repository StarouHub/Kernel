<?php
// view/FrontOffice/nouvellereclamation.php - VERSION ULTRA INTELLIGENTE AVEC IA PROFESSIONNELLE
define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/init.php';
require_once ROOT_PATH . '/prioritymanager.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . ROOT_PATH . '/index.php');
    exit;
}

$error = '';
$success = '';
$reclamation_id = null;
$show_success_modal = false;

// NE PAS forcer user_id = 1, c'est une faille de sécurité!
if (empty($_SESSION['user_id'])) {
    die('Erreur de session. Veuillez vous reconnecter.');
}

// ========== CRÉATION RÉCLAMATION ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reclamation'])) {
    try {
        if (empty(trim($_POST['titre']))) {
            throw new Exception("Le titre est obligatoire");
        }
        if (empty(trim($_POST['description']))) {
            throw new Exception("La description est obligatoire");
        }

        try {
            require_once ROOT_PATH . '/controller/ReclamationController.php';
            $ctrl = new ReclamationController();
            
            $data = [
                'titre' => trim($_POST['titre']),
                'description' => trim($_POST['description']),
                'type' => $_POST['type'] ?? 'autre',
                'priorite' => $_POST['priorite'] ?? 'normale'
            ];

            if (method_exists($ctrl, 'create')) {
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

                        // ✅ CORRECTION: Afficher modal de succès au lieu de redirection silencieuse
                        $show_success_modal = true;
                        $success = "✅ Réclamation envoyée avec succès ! ID: #$reclamation_id";
                    } else {
                        $error = "Erreur: ID réclamation non reçu";
                    }
                } else {
                    $error = $result['message'] ?? 'Erreur lors de la création';
                }
            } else {
                throw new Exception("La méthode 'create' n'existe pas dans ReclamationController");
            }
        } catch (Exception $ctrlError) {
            error_log("Erreur création réclamation (contrôleur): " . $ctrlError->getMessage());
            error_log("Trace: " . $ctrlError->getTraceAsString());
            throw new Exception("Erreur du contrôleur: " . $ctrlError->getMessage());
        }
        
    } catch (Exception $e) {
        $error = "❌ Erreur: " . $e->getMessage();
        error_log("ERREUR nouvellereclamation: " . $e->getMessage());
        error_log("Trace complète: " . $e->getTraceAsString());
    }
}

$priorityManager = new PriorityManager();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
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
            --info: #0284c7;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eef8 100%);
            min-height: 100vh;
            color: #1e293b;
        }
        
        /* HEADER */
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 3rem 0;
            box-shadow: 0 20px 50px rgba(30,58,138,0.4);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            transform: translate(150px, -150px);
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .logo-box {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        .logo-box i { font-size: 3rem; color: var(--primary); }
        
        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.95;
            margin-bottom: 0;
        }
        
        .container-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 1rem;
        }
        
        /* LAYOUT */
        .layout {
            display: grid;
            grid-template-columns: 1fr 450px;
            gap: 2rem;
        }
        
        @media (max-width: 1200px) {
            .layout { grid-template-columns: 1fr; }
        }
        
        /* CARDS */
        .card-main {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: none;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .card-main:hover {
            box-shadow: 0 30px 80px rgba(0,0,0,0.2);
            transform: translateY(-5px);
        }
        
        .card-header-main {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2rem;
            border: none;
        }
        
        .card-header-main h3 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .card-header-main p {
            margin-bottom: 0;
            opacity: 0.9;
        }
        
        .card-body-main {
            padding: 2.5rem;
        }
        
        /* FORM */
        .form-group {
            margin-bottom: 2rem;
        }
        
        .form-label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.8rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-label i { color: var(--primary); font-size: 1.2rem; }
        
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
        
        .char-count {
            text-align: right;
            font-size: 0.9rem;
            color: #94a3b8;
            margin-top: 0.5rem;
        }
        
        .error-text {
            display: none;
            color: var(--danger);
            font-size: 0.9rem;
            margin-top: 0.5rem;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
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
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .ai-content {
            padding: 1.5rem;
        }
        
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
        
        /* SCORES */
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
        
        /* BADGES */
        .badge-custom {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            margin: 0.25rem;
        }
        
        .badge-critical { background: #fee2e2; color: #991b1b; }
        .badge-high { background: #fed7aa; color: #92400e; }
        .badge-medium { background: #fef3c7; color: #78350f; }
        .badge-low { background: #dbeafe; color: #1e40af; }
        .badge-keyword { background: #f0f4f8; color: var(--primary); border: 1px solid var(--primary); }
        
        .keywords-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        /* SUGGESTIONS */
        .suggestion-box {
            background: #fffbeb;
            border-left: 4px solid var(--warning);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
        }
        
        .suggestion-box i { color: var(--warning); margin-right: 0.5rem; }
        
        /* BUTTONS */
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
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-cancel {
            background: #e2e8f0;
            color: #1e293b;
        }
        
        .btn-cancel:hover {
            background: #cbd5e1;
        }
        
        /* FILE UPLOAD */
        .file-zone {
            border: 3px dashed #cbd5e1;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8fafc;
        }
        
        .file-zone:hover {
            border-color: var(--primary);
            background: #eff6ff;
        }
        
        .file-zone i { font-size: 2.5rem; color: #64748b; margin-bottom: 0.5rem; }
        
        .file-list {
            display: grid;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .file-item {
            background: #f8fafc;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* ALERTS */
        .alert-success {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            border: none;
            border-left: 4px solid var(--success);
            color: #166534;
            border-radius: 12px;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: none;
            border-left: 4px solid var(--danger);
            color: #7f1d1d;
            border-radius: 12px;
        }
        
        .success-modal {
            position: fixed;
            top: 30px;
            right: 30px;
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            border-left: 4px solid var(--success);
            animation: slideInRight 0.5s ease;
            z-index: 9999;
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(500px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .success-icon {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: var(--success);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .header h1 { font-size: 1.8rem; }
            .layout { grid-template-columns: 1fr; }
            .ai-panel { position: static; }
            .card-body-main, .ai-content { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <div class="container-lg header-content">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="logo-box">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
                <div class="col">
                    <h1><i class="bi bi-pencil-square"></i> Nouvelle Réclamation</h1>
                    <p>Décrivez votre problème - L'IA vous aidera à l'analyser et le prioriser</p>
                </div>
                <div class="col-auto">
                    <a href="dashboard.php" class="btn btn-light">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="container-main">
        <?php if (!empty($success) && $reclamation_id): ?>
            <div class="success-modal">
                <div class="d-flex align-items-center">
                    <span class="success-icon">✓</span>
                    <div>
                        <h5 style="margin-bottom: 0.25rem; color: var(--success);">Envoyé avec succès !</h5>
                        <p style="margin-bottom: 0; color: #64748b;">Réclamation #<?= $reclamation_id ?> enregistrée</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <strong>Erreur !</strong> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- LAYOUT: Form + AI Panel -->
        <div class="layout">
            <!-- LEFT: FORM -->
            <div>
                <form method="POST" enctype="multipart/form-data" id="reclamationForm">
                    <!-- SECTION 1: TITRE ET DESCRIPTION -->
                    <div class="card-main mb-4">
                        <div class="card-header-main">
                            <h3><i class="bi bi-info-circle"></i> Informations principales</h3>
                            <p>Décrivez précisément votre problème</p>
                        </div>
                        <div class="card-body-main">
                            <!-- Titre -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-type"></i> Titre de la réclamation
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="titre" 
                                       id="titre" 
                                       class="form-control" 
                                       placeholder="Ex: Application se bloque lors du paiement"
                                       value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                                       maxlength="150"
                                       required>
                                <div class="error-text" id="titreError">
                                    <i class="bi bi-exclamation-circle"></i> Minimum 5 caractères requis
                                </div>
                                <div class="char-count">
                                    <span id="titreCount">0</span>/150
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-chat-left-text"></i> Description détaillée
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control" 
                                          placeholder="Décrivez le problème:&#10;1. Quand cela s'est produit?&#10;2. Les étapes pour reproduire?&#10;3. Quel est l'impact?&#10;4. Détails supplémentaires?"
                                          required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                <div class="error-text" id="descriptionError">
                                    <i class="bi bi-exclamation-circle"></i> Minimum 10 caractères requis
                                </div>
                                <div class="char-count">
                                    <span id="descCount">0</span>/5000
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: CATÉGORIES -->
                    <div class="card-main mb-4">
                        <div class="card-header-main">
                            <h3><i class="bi bi-tags"></i> Catégorisation</h3>
                            <p>Type et priorité de votre problème</p>
                        </div>
                        <div class="card-body-main">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-tag"></i> Type
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option value="">-- Sélectionner --</option>
                                            <option value="bug" <?= ($_POST['type'] ?? '') == 'bug' ? 'selected' : '' ?>>🐛 Bug / Erreur</option>
                                            <option value="technique" <?= ($_POST['type'] ?? '') == 'technique' ? 'selected' : '' ?>>🔧 Problème technique</option>
                                            <option value="contenu" <?= ($_POST['type'] ?? '') == 'contenu' ? 'selected' : '' ?>>📝 Contenu</option>
                                            <option value="suggestion" <?= ($_POST['type'] ?? '') == 'suggestion' ? 'selected' : '' ?>>💡 Suggestion</option>
                                            <option value="autre" <?= ($_POST['type'] ?? 'autre') == 'autre' ? 'selected' : '' ?>>❓ Autre</option>
                                        </select>
                                        <small class="text-muted d-block mt-2">Suggestion IA: <span id="typeSuggestionText">--</span></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-exclamation-lg"></i> Priorité
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="priorite" id="priorite" class="form-select" required>
                                            <option value="">-- Sélectionner --</option>
                                            <option value="basse" <?= ($_POST['priorite'] ?? '') == 'basse' ? 'selected' : '' ?>>🟢 Basse</option>
                                            <option value="normale" <?= ($_POST['priorite'] ?? 'normale') == 'normale' ? 'selected' : '' ?>>🔵 Normale</option>
                                            <option value="haute" <?= ($_POST['priorite'] ?? '') == 'haute' ? 'selected' : '' ?>>🟠 Haute</option>
                                            <option value="urgente" <?= ($_POST['priorite'] ?? '') == 'urgente' ? 'selected' : '' ?>>🔴 Urgente</option>
                                        </select>
                                        <small class="text-muted d-block mt-2">Suggestion IA: <span id="prioritySuggestionText">--</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: FICHIERS -->
                    <div class="card-main mb-4">
                        <div class="card-header-main">
                            <h3><i class="bi bi-paperclip"></i> Pièces jointes (optionnel)</h3>
                            <p>Joignez des captures d'écran, des logs, etc.</p>
                        </div>
                        <div class="card-body-main">
                            <div class="file-zone" onclick="document.getElementById('pieces_jointes').click()">
                                <i class="bi bi-cloud-upload"></i>
                                <p class="fw-bold mb-1">Cliquez ou glissez des fichiers</p>
                                <small class="text-muted">JPG, PNG, PDF, DOC, MP4 • Max 50 Mo total</small>
                                <input type="file" 
                                       name="pieces_jointes[]" 
                                       id="pieces_jointes" 
                                       multiple 
                                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.mp4,.zip" 
                                       style="display:none">
                            </div>
                            <div id="fileList" class="file-list"></div>
                        </div>
                    </div>

                    <!-- SECTION 4: BOUTONS -->
                    <div class="row gap-3">
                        <div class="col">
                            <a href="dashboard.php" class="btn btn-cancel btn-custom w-100">
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
                        <div>
                            <i class="bi bi-robot"></i> Analyse IA
                        </div>
                    </div>
                    <div class="ai-content">
                        <!-- SCORE GLOBAL -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-speedometer2"></i> Score global</div>
                            <div class="score-box">
                                <div class="score-value" id="globalScore">0</div>
                                <div class="score-label">Qualité de la réclamation</div>
                            </div>
                            <div class="progress-custom">
                                <div class="progress-bar-custom" id="globalScoreBar" style="width: 0%; background: linear-gradient(90deg, var(--danger), var(--warning), var(--success));"></div>
                            </div>
                        </div>

                        <!-- ANALYSE DE CONTENU -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-graph-up"></i> Statistiques</div>
                            <div class="score-box">
                                <div style="font-size: 1.3rem; font-weight: 700; color: #64748b; margin-bottom: 0.5rem;">
                                    <span id="charCount">0</span> caractères
                                </div>
                                <div style="font-size: 0.9rem; color: #94a3b8;">
                                    <span id="wordCount">0</span> mots • <span id="sentenceCount">0</span> phrases
                                </div>
                            </div>
                        </div>

                        <!-- DÉTECTION INTELLIGENTE -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-check-circle"></i> Détections</div>
                            <div style="display: grid; gap: 0.75rem;">
                                <div class="suggestion-box">
                                    <i class="bi bi-lightning-fill"></i> Urgence: <strong id="urgencyLevel">Non détectée</strong>
                                </div>
                                <div class="suggestion-box">
                                    <i class="bi bi-chat-left-quote"></i> Sentiment: <strong id="sentimentLevel">Neutre</strong>
                                </div>
                                <div class="suggestion-box">
                                    <i class="bi bi-eye"></i> Clarté: <strong id="clarityLevel">À améliorer</strong>
                                </div>
                            </div>
                        </div>

                        <!-- MOTS-CLÉS -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-lightning"></i> Mots-clés détectés</div>
                            <div class="keywords-list" id="keywordsList">
                                <span class="text-muted small">Aucun mot-clé détecté</span>
                            </div>
                        </div>

                        <!-- BOUTONS D'ACTION IA -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-joystick"></i> Actions rapides</div>
                            <div style="display: grid; gap: 0.6rem;">
                                <button type="button" class="btn btn-sm w-100" style="background: #dbeafe; color: #1e40af; border: none; border-radius: 8px; padding: 0.7rem; font-weight: 600; cursor: pointer; transition: all 0.3s;" id="btnAutoType" onclick="autoSelectType()">
                                    <i class="bi bi-magic"></i> Détecter le type
                                </button>
                                <button type="button" class="btn btn-sm w-100" style="background: #fef3c7; color: #78350f; border: none; border-radius: 8px; padding: 0.7rem; font-weight: 600; cursor: pointer; transition: all 0.3s;" id="btnAutoPriority" onclick="autoSelectPriority()">
                                    <i class="bi bi-flag"></i> Définir priorité
                                </button>
                                <button type="button" class="btn btn-sm w-100" style="background: #f0fdf4; color: #166534; border: none; border-radius: 8px; padding: 0.7rem; font-weight: 600; cursor: pointer; transition: all 0.3s;" id="btnValidate" onclick="validateForm()">
                                    <i class="bi bi-check-circle"></i> Valider
                                </button>
                            </div>
                        </div>

                        <!-- SUGGESTIONS IA -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-lightbulb"></i> Suggestions IA</div>
                            <div id="suggestionsList">
                                <small class="text-muted">Tapez pour voir les suggestions...</small>
                            </div>
                        </div>

                        <!-- SCORE PRIORITÉ IA -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-star"></i> Score de priorité</div>
                            <div class="score-box">
                                <div class="score-value" id="priorityScore">0</div>
                                <div class="score-label" id="priorityReason">Non analysé</div>
                            </div>
                            <div class="progress-custom">
                                <div class="progress-bar-custom" id="priorityScoreBar" style="width: 0%; background: var(--primary);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== ADVANCED AI ANALYZER =====
        class AdvancedAIAnalyzer {
            constructor() {
                this.criticalKeywords = ['urgent', 'critique', 'bloqué', 'crash', 'down', 'panne', 'grave', 'danger', 'sécurité', 'immédiat', 'catastrophe', 'perte'];
                this.highKeywords = ['bug', 'erreur', 'ne marche pas', 'impossible', 'problème', 'défaut', 'échec', 'broken', 'failed'];
                this.mediumKeywords = ['amélioration', 'lent', 'lenteur', 'performance', 'interface', 'design', 'utilisabilité'];
                this.lowKeywords = ['typo', 'orthographe', 'couleur', 'style', 'cosmétique'];
                
                this.urgentKeywords = ['urgent', 'immédiat', 'rapidement', 'vite', 'asap', 'quickly', 'immediately'];
                this.negativeKeywords = ['mauvais', 'horrible', 'terrible', 'nul', 'bad', 'awful'];
                this.positiveKeywords = ['merci', 'super', 'bien', 'excellent', 'bravo', 'good', 'great'];
                
                this.typeIndicators = {
                    bug: ['bug', 'erreur', 'crash', 'plantage', 'exception'],
                    technique: ['connexion', 'serveur', 'réseau', 'base', 'api', 'dns'],
                    contenu: ['texte', 'orthographe', 'description', 'libellé'],
                    suggestion: ['amélioration', 'ajouter', 'proposer', 'feature', 'idée']
                };
            }

            analyze(titre, desc) {
                const text = (titre + ' ' + desc).toLowerCase();
                
                return {
                    score: this.calculateScore(titre, desc),
                    urgency: this.detectUrgency(text),
                    sentiment: this.detectSentiment(text),
                    clarity: this.detectClarity(titre, desc),
                    keywords: this.extractKeywords(text),
                    priority: this.detectPriority(text),
                    type: this.detectType(text),
                    suggestions: this.generateSuggestions(titre, desc)
                };
            }

            calculateScore(titre, desc) {
                let score = 0;
                
                if (titre.length >= 20) score += 20;
                else if (titre.length >= 10) score += 10;
                
                if (desc.length >= 150) score += 30;
                else if (desc.length >= 50) score += 15;
                
                const words = (titre + ' ' + desc).split(/\s+/).length;
                if (words >= 40) score += 20;
                else if (words >= 20) score += 10;
                
                if ((desc.match(/\n/g) || []).length >= 2) score += 10;
                if ((desc.match(/\?/g) || []).length >= 1) score += 5;
                
                return Math.min(score, 100);
            }

            detectUrgency(text) {
                const matches = this.urgentKeywords.filter(kw => text.includes(kw)).length;
                return matches > 0 ? '🔴 Urgent' : '🟢 Normal';
            }

            detectSentiment(text) {
                const negative = this.negativeKeywords.filter(kw => text.includes(kw)).length;
                const positive = this.positiveKeywords.filter(kw => text.includes(kw)).length;
                
                if (negative > positive) return '😞 Négatif';
                if (positive > 0) return '😊 Positif';
                return '😐 Neutre';
            }

            detectClarity(titre, desc) {
                let score = 0;
                if (titre.length >= 20) score += 30;
                if (desc.length >= 150) score += 40;
                if ((desc.match(/\n/g) || []).length >= 2) score += 20;
                if ((desc.match(/\?/g) || []).length >= 1) score += 10;
                
                if (score >= 70) return '✅ Excellente';
                if (score >= 50) return '🔶 Bonne';
                return '⚠️ À améliorer';
            }

            extractKeywords(text) {
                const keywords = [];
                const allKeywords = [
                    ...this.criticalKeywords,
                    ...this.highKeywords,
                    ...this.urgentKeywords
                ];
                
                allKeywords.forEach(kw => {
                    if (text.includes(kw) && !keywords.includes(kw)) {
                        keywords.push(kw);
                    }
                });
                
                return keywords.slice(0, 8);
            }

            detectPriority(text) {
                const critical = this.criticalKeywords.filter(kw => text.includes(kw)).length;
                const high = this.highKeywords.filter(kw => text.includes(kw)).length;
                const medium = this.mediumKeywords.filter(kw => text.includes(kw)).length;
                
                if (critical >= 2 || (critical > 0 && high > 0)) {
                    return { level: 'CRITIQUE', score: 95, color: '#dc2626', badge: 'badge-critical' };
                } else if (high >= 2 || (high > 0 && medium > 0)) {
                    return { level: 'HAUTE', score: 75, color: '#ea580c', badge: 'badge-high' };
                } else if (medium > 0) {
                    return { level: 'MOYENNE', score: 50, color: '#f59e0b', badge: 'badge-medium' };
                }
                return { level: 'BASSE', score: 25, color: '#0284c7', badge: 'badge-low' };
            }

            detectType(text) {
                for (const [type, indicators] of Object.entries(this.typeIndicators)) {
                    if (indicators.some(ind => text.includes(ind))) {
                        return type;
                    }
                }
                return 'autre';
            }

            generateSuggestions(titre, desc) {
                const suggestions = [];
                
                if (titre.length < 10) {
                    suggestions.push('📝 Titre plus descriptif svp');
                }
                if (desc.length < 50) {
                    suggestions.push('💬 Développez davantage votre description');
                }
                if (!desc.includes('?')) {
                    suggestions.push('❓ Inclure une question peut aider');
                }
                if ((desc.match(/\n/g) || []).length === 0) {
                    suggestions.push('📋 Utilisez des sauts de ligne pour plus de clarté');
                }
                
                return suggestions.slice(0, 3);
            }
        }

        // Initialiser l'analyseur
        const analyzer = new AdvancedAIAnalyzer();
        const titre = document.getElementById('titre');
        const description = document.getElementById('description');
        const form = document.getElementById('reclamationForm');
        const submitBtn = document.getElementById('submitBtn');

        // Analyser en temps réel
        function analyze() {
            const t = titre.value;
            const d = description.value;
            const result = analyzer.analyze(t, d);

            // Vérifier que les éléments existent avant de les mettre à jour
            const globalScore = document.getElementById('globalScore');
            const globalScoreBar = document.getElementById('globalScoreBar');
            const charCount = document.getElementById('charCount');
            const wordCount = document.getElementById('wordCount');
            const sentenceCount = document.getElementById('sentenceCount');
            const urgencyLevel = document.getElementById('urgencyLevel');
            const sentimentLevel = document.getElementById('sentimentLevel');
            const clarityLevel = document.getElementById('clarityLevel');
            const keywordsList = document.getElementById('keywordsList');
            const suggestionsList = document.getElementById('suggestionsList');
            const priorityScore = document.getElementById('priorityScore');
            const priorityScoreBar = document.getElementById('priorityScoreBar');
            const priorityReason = document.getElementById('priorityReason');
            const typeSuggestionText = document.getElementById('typeSuggestionText');
            const prioritySuggestionText = document.getElementById('prioritySuggestionText');

            if (globalScore) globalScore.textContent = result.score;
            if (globalScoreBar) globalScoreBar.style.width = result.score + '%';
            
            if (charCount) charCount.textContent = (t + d).length;
            if (wordCount) wordCount.textContent = (t + ' ' + d).split(/\s+/).filter(w => w).length;
            if (sentenceCount) sentenceCount.textContent = (d.split(/[.!?]+/).filter(s => s.trim()).length);
            
            if (urgencyLevel) urgencyLevel.textContent = result.urgency;
            if (sentimentLevel) sentimentLevel.textContent = result.sentiment;
            if (clarityLevel) clarityLevel.textContent = result.clarity;

            // Mots-clés
            if (keywordsList) {
                if (result.keywords.length > 0) {
                    keywordsList.innerHTML = result.keywords
                        .map(kw => `<span class="badge-custom badge-keyword">${kw}</span>`)
                        .join('');
                } else {
                    keywordsList.innerHTML = '<span class="text-muted small">Aucun mot-clé détecté</span>';
                }
            }

            // Suggestions
            if (suggestionsList) {
                if (result.suggestions.length > 0) {
                    suggestionsList.innerHTML = result.suggestions
                        .map((sug, idx) => {
                            let actionFunc = '';
                            let btnClass = 'style="background: #fffbeb; color: #92400e; border: 1px solid #fcd34d; border-radius: 8px; padding: 0.75rem 1rem; font-weight: 600; cursor: pointer; width: 100%; text-align: left; margin-bottom: 0.5rem; transition: all 0.3s;"';
                            
                            // Définir l'action selon le type de suggestion
                            if (sug.includes('Titre')) {
                                actionFunc = `onclick="applySuggestion('title')"`;
                            } else if (sug.includes('Développez')) {
                                actionFunc = `onclick="applySuggestion('description')"`;
                            } else if (sug.includes('question')) {
                                actionFunc = `onclick="applySuggestion('question')"`;
                            } else if (sug.includes('sauts de ligne')) {
                                actionFunc = `onclick="applySuggestion('format')"`;
                            }
                            
                            return `<button type="button" class="btn btn-suggestion" ${btnClass} ${actionFunc}>
                                <i class="bi bi-lightbulb-fill"></i> ${sug}
                            </button>`;
                        })
                        .join('');
                } else {
                    suggestionsList.innerHTML = '<small class="text-muted">✅ Excellent! Pas de suggestions.</small>';
                }
            }

            // Priorité
            if (priorityScore) priorityScore.textContent = result.priority.score;
            if (priorityScoreBar) {
                priorityScoreBar.style.width = result.priority.score + '%';
                priorityScoreBar.style.background = result.priority.color;
            }
            if (priorityReason) priorityReason.textContent = result.priority.level;

            // Suggestions type/priorité
            if (typeSuggestionText) typeSuggestionText.textContent = result.type || '--';
            if (prioritySuggestionText) prioritySuggestionText.textContent = result.priority.level || '--';

            // Validation
            const valid = t.length >= 5 && d.length >= 10;
            if (submitBtn) submitBtn.disabled = !valid;
        }

        // Compteurs de caractères
        if (titre) {
            titre.addEventListener('input', function() {
                const titreCount = document.getElementById('titreCount');
                if (titreCount) titreCount.textContent = this.value.length;
                analyze();
            });
        }

        if (description) {
            description.addEventListener('input', function() {
                const descCount = document.getElementById('descCount');
                if (descCount) descCount.textContent = this.value.length;
                analyze();
            });
        }

        // Gestion des fichiers
        const piecesJointes = document.getElementById('pieces_jointes');
        if (piecesJointes) {
            piecesJointes.addEventListener('change', function() {
                const list = document.getElementById('fileList');
                if (!list) return;
                
                list.innerHTML = '';
                let total = 0;

                if (this.files.length > 0) {
                    for (let f of this.files) {
                        total += f.size;
                        const size = (f.size / 1024 / 1024).toFixed(2);
                        list.innerHTML += `
                            <div class="file-item">
                                <span><i class="bi bi-file"></i> ${f.name}</span>
                                <span class="badge bg-light text-dark">${size} Mo</span>
                            </div>
                        `;
                    }

                    if (total > 50 * 1024 * 1024) {
                        alert('Taille max: 50 Mo');
                        this.value = '';
                    }
                }
            });
        }

        // Submit
        if (form) {
            form.addEventListener('submit', function(e) {
                if (titre.value.length < 5 || description.value.length < 10) {
                    e.preventDefault();
                    alert('Formulaire incomplet');
                    return false;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Envoi...';
                }
            });
        }

        // Init - Appeler analyze une première fois au chargement
        document.addEventListener('DOMContentLoaded', function() {
            if (titre) titre.focus();
            analyze();
        });

        // ===== FONCTIONS D'ACTION IA VRAIES =====
        
        // Détecter et sélectionner le type automatiquement
        function autoSelectType() {
            const t = titre.value;
            const d = description.value;
            const text = (t + ' ' + d).toLowerCase();
            
            let detectedType = 'autre';
            
            if (['bug', 'erreur', 'crash', 'plantage', 'exception'].some(kw => text.includes(kw))) {
                detectedType = 'bug';
            } else if (['connexion', 'serveur', 'réseau', 'base', 'api', 'dns'].some(kw => text.includes(kw))) {
                detectedType = 'technique';
            } else if (['texte', 'orthographe', 'description', 'libellé'].some(kw => text.includes(kw))) {
                detectedType = 'contenu';
            } else if (['amélioration', 'ajouter', 'proposer', 'feature', 'idée'].some(kw => text.includes(kw))) {
                detectedType = 'suggestion';
            }
            
            document.getElementById('type').value = detectedType;
            alert('✅ Type sélectionné: ' + detectedType);
            analyze();
        }

        // Définir la priorité automatiquement
        function autoSelectPriority() {
            const t = titre.value;
            const d = description.value;
            const text = (t + ' ' + d).toLowerCase();
            
            let priority = 'normale';
            
            const critical = ['urgent', 'critique', 'bloqué', 'crash', 'down', 'panne'].filter(kw => text.includes(kw)).length;
            const high = ['bug', 'erreur', 'impossible', 'problème'].filter(kw => text.includes(kw)).length;
            
            if (critical >= 2 || (critical > 0 && high > 0)) {
                priority = 'urgente';
            } else if (high >= 2) {
                priority = 'haute';
            } else if (['amélioration', 'lent'].some(kw => text.includes(kw))) {
                priority = 'normale';
            } else {
                priority = 'basse';
            }
            
            document.getElementById('priorite').value = priority;
            alert('✅ Priorité définie: ' + priority.toUpperCase());
            analyze();
        }

        // Valider le formulaire
        function validateForm() {
            const titre_val = titre.value.trim();
            const desc_val = description.value.trim();
            const type_val = document.getElementById('type').value;
            const priorite_val = document.getElementById('priorite').value;
            
            let errors = [];
            
            if (titre_val.length < 5) {
                errors.push('Titre trop court (min 5 caractères)');
            }
            if (desc_val.length < 10) {
                errors.push('Description trop courte (min 10 caractères)');
            }
            if (!type_val) {
                errors.push('Type non sélectionné');
            }
            if (!priorite_val) {
                errors.push('Priorité non sélectionnée');
            }
            
            if (errors.length > 0) {
                alert('❌ Erreurs:\n\n' + errors.join('\n'));
                return false;
            }
            
            alert('✅ Formulaire valide!\n\nTitre: ' + titre_val + '\nDescription: ' + desc_val.substring(0, 50) + '...\nType: ' + type_val + '\nPriorité: ' + priorite_val);
            return true;
        }

        // ===== APPLIQUER LES SUGGESTIONS =====
        function applySuggestion(type) {
            const scrollOptions = { behavior: 'smooth', block: 'center' };
            
            switch(type) {
                case 'title':
                    // Suggestion: Titre plus descriptif
                    titre.focus();
                    titre.scrollIntoView(scrollOptions);
                    
                    // Ajouter un texte d'aide
                    if (titre.value.length < 10) {
                        const hint = prompt('💡 Conseil: Soyez plus descriptif!\n\nExemple:\n- Mauvais: "App bug"\n- Bon: "Application se bloque au démarrage"\n\nVotre nouveau titre:');
                        if (hint) {
                            titre.value = hint;
                            titre.dispatchEvent(new Event('input'));
                            alert('✅ Titre mis à jour!');
                        }
                    } else {
                        alert('ℹ️ Votre titre est déjà assez descriptif!');
                    }
                    break;
                    
                case 'description':
                    // Suggestion: Développer la description
                    description.focus();
                    description.scrollIntoView(scrollOptions);
                    
                    if (description.value.length < 100) {
                        const hint = prompt('💡 Développez votre description!\n\nExemples de détails à ajouter:\n- Quand le problème a commencé?\n- Comment le reproduire?\n- Quel est l\'impact?\n\nVotre texte supplémentaire:');
                        if (hint) {
                            description.value += '\n\n' + hint;
                            description.dispatchEvent(new Event('input'));
                            alert('✅ Description complétée!');
                        }
                    } else {
                        alert('ℹ️ Votre description est déjà détaillée!');
                    }
                    break;
                    
                case 'question':
                    // Suggestion: Ajouter une question
                    description.focus();
                    description.scrollIntoView(scrollOptions);
                    
                    if (!description.value.includes('?')) {
                        const question = prompt('💡 Posez une question pour clarifier!\n\nExemples:\n- Est-ce que ce bug apparaît en production?\n- Avez-vous un délai pour la correction?\n\nVotre question:');
                        if (question) {
                            description.value += '\n\nQuestion: ' + question;
                            description.dispatchEvent(new Event('input'));
                            alert('✅ Question ajoutée!');
                        }
                    } else {
                        alert('ℹ️ Vous avez déjà une question!');
                    }
                    break;
                    
                case 'format':
                    // Suggestion: Ajouter des sauts de ligne
                    description.focus();
                    description.scrollIntoView(scrollOptions);
                    
                    if ((description.value.match(/\n/g) || []).length < 2) {
                        const formattedText = prompt('💡 Formatez mieux votre description!\n\nExemple de format:\n1. Problème: ...\n2. Quand: ...\n3. Étapes: ...\n4. Impact: ...\n\nVotre texte formaté:');
                        if (formattedText) {
                            description.value = formattedText;
                            description.dispatchEvent(new Event('input'));
                            alert('✅ Formatage appliqué!');
                        }
                    } else {
                        alert('ℹ️ Votre texte est déjà bien formaté!');
                    }
                    break;
            }
            
            analyze();
        }
    </script>
</body>
</html>
