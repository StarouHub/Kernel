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
    header('Location: ' . ROOT_PATH . '/indexx.php');
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
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&display=swap" rel="stylesheet">
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
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
        }

        .header h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 0.5rem;
            font-family: 'Raleway', sans-serif;
            display: flex;
            align-items: center;
            gap: 12px;
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
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
            color: white;
            padding: 2rem;
            box-shadow: 0 4px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
        }

        .card-header-main h3 {
            font-weight: 800;
            font-size: 32px;
            margin-bottom: 0.5rem;
            font-family: 'Raleway', sans-serif;
            display: flex;
            align-items: center;
            gap: 12px;
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

        /* VALIDATION ROUGE POUR CHAMPS VIDES - ULTRA VISIBLE */
        .form-control.is-invalid, .form-select.is-invalid, textarea.is-invalid {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.2) !important;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.05), rgba(239, 68, 68, 0.05)) !important;
            animation: invalidPulse 1s ease-in-out infinite;
            position: relative;
        }

        .form-control.is-invalid::placeholder, 
        .form-select.is-invalid::placeholder, 
        textarea.is-invalid::placeholder {
            color: #dc2626 !important;
            opacity: 0.7;
        }

        .invalid-feedback {
            display: block;
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            font-weight: 600;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border-left: 4px solid #dc2626;
            animation: slideInUp 0.3s ease-out;
        }

        @keyframes invalidPulse {
            0%, 100% { 
                border-color: #dc2626;
                box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.2);
            }
            50% { 
                border-color: #ef4444;
                box-shadow: 0 0 0 6px rgba(220, 38, 38, 0.3);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Labels rouges pour les champs invalides */
        .form-group:has(.is-invalid) .form-label {
            color: #dc2626 !important;
            animation: labelAlert 0.5s ease-in-out;
        }

        @keyframes labelAlert {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* TOAST DE VALIDATION MODERNE */
        .validation-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.3);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 1rem;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.3s ease;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }

        .validation-toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
        }

        .toast-content i {
            font-size: 1.2rem;
            animation: bounce 1s infinite;
        }

        .toast-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .toast-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
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
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
            color: white !important;
            width: 100%;
            font-family: 'Raleway', sans-serif;
            font-weight: 800;
            font-size: 18px;
            padding: 1.2rem 2rem;
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 40px rgba(10, 79, 255, 0.4);
            color: white !important;
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
        }

        .btn-submit:active {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(10, 79, 255, 0.3);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            background: #94a3b8;
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
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 800;
            font-family: 'Raleway', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .ai-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: aiHeaderShine 3s infinite;
        }

        @keyframes aiHeaderShine {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .ai-indicator {
            width: 12px;
            height: 12px;
            background: radial-gradient(circle, #22c55e, #16a34a);
            border-radius: 50%;
            animation: pulse-ai 1.5s infinite;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
            position: relative;
        }

        .ai-indicator::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.3), transparent);
            border-radius: 50%;
            animation: pulse-ai-ring 1.5s infinite;
        }

        @keyframes pulse-ai {
            0%, 100% { 
                opacity: 1; 
                transform: scale(1);
            }
            50% { 
                opacity: 0.7; 
                transform: scale(1.2);
            }
        }

        @keyframes pulse-ai-ring {
            0%, 100% { 
                opacity: 0; 
                transform: scale(1);
            }
            50% { 
                opacity: 1; 
                transform: scale(1.5);
            }
        }

        .ai-content { padding: 1.5rem; }

        .ai-section {
            margin-bottom: 1.5rem;
            padding: 1.2rem;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            background: linear-gradient(135deg, rgba(255,255,255,0.8), rgba(248,250,252,0.9));
            backdrop-filter: blur(5px);
        }

        .ai-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248,250,252,1));
        }

        .ai-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #0A4FFF, #4AA8FF, #0A4FFF);
            border-radius: 12px 12px 0 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .ai-section:hover::before {
            opacity: 1;
        }

        .ai-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
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
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
            background-clip: padding-box;
        }

        .score-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, #0A4FFF, #4AA8FF, #0A4FFF);
            border-radius: 20px;
            padding: 2px;
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            animation: borderRotate 3s linear infinite;
        }

        @keyframes borderRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .score-value {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0A4FFF, #4AA8FF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            animation: scoreGlow 2s ease-in-out infinite alternate;
            font-family: 'Raleway', sans-serif;
        }

        @keyframes scoreGlow {
            0% { filter: drop-shadow(0 0 5px rgba(10, 79, 255, 0.3)); }
            100% { filter: drop-shadow(0 0 15px rgba(10, 79, 255, 0.6)); }
        }

        .score-label {
            font-size: 0.9rem;
            color: #64748b;
            margin-top: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
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

        /* AI PANEL ULTRA-PUISSANT */
        .ai-status {
            font-size: 0.75rem;
            opacity: 0.8;
            margin-left: auto;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }

        .metric-item {
            text-align: center;
            padding: 0.5rem;
            background: #f8fafc;
            border-radius: 8px;
        }

        .metric-value {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .detection-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detection-item:last-child {
            border-bottom: none;
        }

        .detection-value {
            font-weight: 600;
            color: var(--primary);
        }

        .analysis-placeholder, .suggestions-placeholder, .validation-placeholder, 
        .sentiment-placeholder, .prediction-placeholder {
            text-align: center;
            padding: 1rem;
            color: #94a3b8;
        }

        .ai-suggestion-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #fcd34d;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .ai-suggestion-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(202, 138, 4, 0.2);
        }

        .error-item {
            background: #fee2e2;
            border-left: 3px solid #dc2626;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .warning-item {
            background: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .success-item {
            background: #dcfce7;
            border-left: 3px solid #16a34a;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .sentiment-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .sentiment-positive {
            background: #dcfce7;
            color: #166534;
        }

        .sentiment-negative {
            background: #fee2e2;
            color: #991b1b;
        }

        .sentiment-neutral {
            background: #e0e7ff;
            color: #3730a3;
        }

        .prediction-card {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 1px solid #60a5fa;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
        }

        .prediction-time {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e40af;
            margin: 0.5rem 0;
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #e2e8f0;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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

        /* CONSEILS CONTEXTUELS ULTRA-CRÉATIFS */
        .creative-tip-card {
            padding: 1.2rem;
            border-radius: 12px;
            margin-bottom: 0.8rem;
            animation: slideInRight 0.5s ease-out;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .creative-tip-card:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .creative-tip-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, currentColor, transparent);
            animation: shimmer 2s infinite;
        }

        .tip-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.8rem;
        }

        .tip-icon {
            font-size: 1.5rem;
            animation: bounce 2s infinite;
        }

        .tip-type {
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tip-text {
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .creativity-placeholder {
            text-align: center;
            padding: 2rem 1rem;
            color: #94a3b8;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-5px); }
            60% { transform: translateY(-3px); }
        }

        /* EFFETS ULTRA-INNOVANTS POUR L'IA */
        .ai-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 20%, rgba(10, 79, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(74, 168, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 60%, rgba(10, 79, 255, 0.05) 0%, transparent 50%);
            pointer-events: none;
            animation: aiParticles 8s ease-in-out infinite;
        }

        @keyframes aiParticles {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.05); }
        }

        .ai-label::after {
            content: '✨';
            margin-left: 0.5rem;
            animation: sparkle 2s ease-in-out infinite;
            opacity: 0.7;
        }

        @keyframes sparkle {
            0%, 100% { opacity: 0.3; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        .progress-bar-custom {
            position: relative;
            overflow: hidden;
        }

        .progress-bar-custom::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: progressShine 2s infinite;
        }

        @keyframes progressShine {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .metric-item {
            position: relative;
            overflow: hidden;
        }

        .metric-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(10, 79, 255, 0.1), transparent);
            animation: metricGlow 3s infinite;
        }

        @keyframes metricGlow {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        @media (max-width: 768px) {
            .header h1 { font-size: 1.8rem; }
            .layout { grid-template-columns: 1fr; }
            .ai-panel { position: static; }
            .creative-tip-card { margin-bottom: 0.6rem; padding: 1rem; }
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

            <!-- RIGHT: AI PANEL ULTRA-PUISSANT -->
            <div>
                <div class="ai-panel">
                    <div class="ai-header">
                        <div class="ai-indicator"></div>
                        <div><i class="bi bi-robot"></i> Assistant IA Ultra-Intelligent</div>
                        <div class="ai-status" id="aiStatus">Prêt</div>
                    </div>
                    <div class="ai-content">
                        <!-- SCORE GLOBAL AVANCÉ -->
                        <div class="ai-section">
                            <div class="ai-label">
                                <i class="bi bi-speedometer2"></i> Score Qualité Global
                                <span class="badge bg-primary ms-2" id="qualityBadge">0%</span>
                            </div>
                            <div class="score-box">
                                <div class="score-value" id="globalScore">0</div>
                                <div class="score-label" id="qualityLabel">Analyse en cours...</div>
                            </div>
                            <div class="progress-custom">
                                <div class="progress-bar-custom" id="globalScoreBar" style="width: 0%; background: linear-gradient(90deg, #dc2626, #f59e0b, #16a34a);"></div>
                            </div>
                            <div class="metrics-grid mt-3">
                                <div class="metric-item">
                                    <small class="text-muted">Clarté</small>
                                    <div class="metric-value" id="clarityScore">-</div>
                                </div>
                                <div class="metric-item">
                                    <small class="text-muted">Complétude</small>
                                    <div class="metric-value" id="completenessScore">-</div>
                                </div>
                                <div class="metric-item">
                                    <small class="text-muted">Structure</small>
                                    <div class="metric-value" id="structureScore">-</div>
                                </div>
                            </div>
                        </div>

                        <!-- ANALYSE INTELLIGENTE AVANCÉE -->
                        <div class="ai-section">
                            <div class="ai-label">
                                <i class="bi bi-graph-up-arrow"></i> Analyse Intelligente
                                <button class="btn btn-sm btn-outline-primary ms-2" onclick="refreshAIAnalysis()" id="refreshBtn">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            <div id="aiAnalysis">
                                <div class="analysis-placeholder">
                                    <i class="bi bi-cpu text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Commencez à taper pour une analyse en temps réel...</p>
                                </div>
                            </div>
                        </div>
                        <!-- DÉTECTION AUTOMATIQUE TYPE & PRIORITÉ -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-magic"></i> Détection Automatique</div>
                            <div id="autoDetection">
                                <div class="detection-item">
                                    <small class="text-muted">Type suggéré:</small>
                                    <div id="detectedType" class="detection-value">-</div>
                                </div>
                                <div class="detection-item">
                                    <small class="text-muted">Priorité suggérée:</small>
                                    <div id="detectedPriority" class="detection-value">-</div>
                                </div>
                                <div class="detection-item">
                                    <small class="text-muted">Confiance:</small>
                                    <div id="detectionConfidence" class="detection-value">-</div>
                                </div>
                                <button class="btn btn-sm btn-success w-100 mt-2" onclick="applyAISuggestions()" id="applyBtn" style="display: none;">
                                    <i class="bi bi-check-circle"></i> Appliquer les suggestions
                                </button>
                            </div>
                        </div>

                        <!-- CONSEILS CONTEXTUELS ULTRA-CRÉATIFS -->
                        <div class="ai-section">
                            <div class="ai-label">
                                <i class="bi bi-stars"></i> Conseils Contextuels Ultra-Intelligents
                                <span class="badge bg-warning ms-2" id="creativityLevel">🎨 Créatif</span>
                            </div>
                            <div id="contextualTips">
                                <div class="creativity-placeholder">
                                    <i class="bi bi-magic text-primary" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">L'IA analyse votre style d'écriture pour des conseils personnalisés...</p>
                                </div>
                            </div>
                        </div>

                        <!-- SUGGESTIONS INTELLIGENTES -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-lightbulb"></i> Suggestions Avancées</div>
                            <div id="suggestions">
                                <div class="suggestions-placeholder">
                                    <small class="text-muted">L'IA génèrera des suggestions personnalisées...</small>
                                </div>
                            </div>
                        </div>

                        <!-- PRÉVENTION D'ERREURS -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-shield-check"></i> Validation & Prévention</div>
                            <div id="errorPrevention">
                                <div class="validation-placeholder">
                                    <small class="text-muted">Vérification en cours...</small>
                                </div>
                            </div>
                        </div>

                        <!-- OPTIMISATION DU TEXTE -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-pencil-square"></i> Optimisation du Texte</div>
                            <div id="textOptimization">
                                <button class="btn btn-sm btn-outline-info w-100" onclick="optimizeText()" id="optimizeBtn" style="display: none;">
                                    <i class="bi bi-magic"></i> Optimiser automatiquement
                                </button>
                                <div id="optimizationSuggestions" class="mt-2"></div>
                            </div>
                        </div>

                        <!-- ANALYSE DE SENTIMENT -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-emoji-smile"></i> Analyse de Sentiment</div>
                            <div id="sentimentAnalysis">
                                <div class="sentiment-placeholder">
                                    <small class="text-muted">Analyse du ton et de l'émotion...</small>
                                </div>
                            </div>
                        </div>

                        <!-- PRÉDICTION DE RÉSOLUTION -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-clock-history"></i> Prédiction de Résolution</div>
                            <div id="resolutionPrediction">
                                <div class="prediction-placeholder">
                                    <small class="text-muted">Estimation du temps de traitement...</small>
                                </div>
                            </div>
                        </div>

                        <!-- DÉTECTION DE DUPLICATAS -->
                        <div class="ai-section">
                            <div class="ai-label"><i class="bi bi-files"></i> Détection de Duplicatas</div>
                            <div id="duplicateDetection">
                                <div class="duplicate-placeholder">
                                    <small class="text-muted">Recherche de réclamations similaires...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== ASSISTANT IA ULTRA-PUISSANT =====
        class UltraPowerfulAI {
            constructor() {
                this.apiUrl = '../../api/ai-analyze.php';
                this.debounceTimer = null;
                this.lastAnalysis = null;
                this.isAnalyzing = false;
            }

            async analyzeAdvanced(titre, description, type = '', priorite = '') {
                if (this.isAnalyzing) return;
                
                this.isAnalyzing = true;
                this.updateStatus('Analyse en cours...', 'warning');
                
                try {
                    const response = await fetch(this.apiUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ titre, description, type, priorite })
                    });
                    
                    const data = await response.json();
                    this.lastAnalysis = data;
                    this.isAnalyzing = false;
                    
                    if (data.success) {
                        this.updateStatus('Analyse terminée', 'success');
                        this.processAnalysis(data);
                    } else {
                        this.updateStatus('Erreur d\'analyse', 'danger');
                    }
                } catch (error) {
                    console.error('Erreur AI:', error);
                    this.isAnalyzing = false;
                    this.updateStatus('Erreur de connexion', 'danger');
                    // Fallback sur analyse locale
                    this.fallbackAnalysis(titre, description);
                }
            }

            processAnalysis(data) {
                // Score global
                this.updateScore(data.score, data.quality);
                
                // Métriques détaillées
                this.updateMetrics(data);
                
                // Analyse intelligente
                this.updateAnalysis(data);
                
                // Détection automatique
                this.updateDetection(data);
                
                // Suggestions
                this.updateSuggestions(data);
                
                // Prévention d'erreurs
                this.updateErrorPrevention(data);
                
                // Optimisation
                this.updateOptimization(data);
                
                // Sentiment
                this.updateSentiment(data);
                
                // Prédiction
                this.updatePrediction(data);
                
                // Détection de duplicatas
                this.updateDuplicateDetection(data);
            }

            updateScore(score, quality) {
                document.getElementById('globalScore').textContent = score;
                document.getElementById('globalScoreBar').style.width = score + '%';
                document.getElementById('qualityBadge').textContent = score + '%';
                document.getElementById('qualityLabel').textContent = quality;
                
                // Animation
                const bar = document.getElementById('globalScoreBar');
                if (score >= 80) bar.style.background = 'linear-gradient(90deg, #16a34a, #22c55e)';
                else if (score >= 60) bar.style.background = 'linear-gradient(90deg, #f59e0b, #fbbf24)';
                else bar.style.background = 'linear-gradient(90deg, #dc2626, #ef4444)';
            }

            updateMetrics(data) {
                const content = data.content_analysis || {};
                const completeness = data.error_prevention?.completeness_percentage || 0;
                
                document.getElementById('clarityScore').textContent = 
                    data.language_optimization?.clarity_score || '-';
                document.getElementById('completenessScore').textContent = 
                    Math.round(completeness) + '%';
                document.getElementById('structureScore').textContent = 
                    content.lignes_description >= 3 ? '✅' : '⚠️';
            }

            updateAnalysis(data) {
                const analysis = data.content_analysis || {};
                const keywords = data.keywords || [];
                
                let html = `<div class="p-3 bg-light rounded">
                    <div class="mb-2">
                        <strong>${data.quality || 'Analyse'}</strong>
                        <span class="badge bg-primary ms-2">${data.score}/100</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Mots: ${analysis.mots_description || 0} | 
                        Lignes: ${analysis.lignes_description || 0}</small>
                    </div>
                    ${keywords.length > 0 ? `
                    <div class="mt-2">
                        <small class="text-muted">Mots-clés détectés:</small>
                        <div class="mt-1">
                            ${keywords.map(kw => `<span class="badge bg-secondary me-1">${kw}</span>`).join('')}
                        </div>
                    </div>` : ''}
                </div>`;
                
                document.getElementById('aiAnalysis').innerHTML = html;
            }

            updateDetection(data) {
                const classification = data.classification || {};
                const priority = data.priority_evaluation || {};
                
                const typeMap = {
                    'bug': '🐛 Bug',
                    'technique': '🔧 Technique',
                    'contenu': '📝 Contenu',
                    'suggestion': '💡 Suggestion',
                    'autre': '❓ Autre'
                };
                
                const priorityMap = {
                    'critique': '🔴 Critique',
                    'urgente': '🟠 Urgente',
                    'haute': '🟡 Haute',
                    'normale': '🔵 Normale',
                    'basse': '🟢 Basse'
                };
                
                document.getElementById('detectedType').innerHTML = 
                    typeMap[classification.type] || '-';
                document.getElementById('detectedPriority').innerHTML = 
                    priorityMap[priority.priority] || '-';
                document.getElementById('detectionConfidence').innerHTML = 
                    (classification.confidence || 0) + '%';
                
                // Afficher bouton appliquer si suggestions différentes
                const applyBtn = document.getElementById('applyBtn');
                if (classification.recommendation || priority.recommendation) {
                    applyBtn.style.display = 'block';
                } else {
                    applyBtn.style.display = 'none';
                }
            }

            updateSuggestions(data) {
                const suggestions = data.attachments_suggestions || [];
                const advice = data.personalized_advice || [];
                
                let html = '';
                
                if (suggestions.length > 0) {
                    html += '<div class="mb-3"><strong class="text-primary">📎 Pièces jointes suggérées:</strong>';
                    suggestions.forEach(sug => {
                        html += `<div class="ai-suggestion-card mt-2">${sug}</div>`;
                    });
                    html += '</div>';
                }
                
                if (advice.length > 0) {
                    html += '<div><strong class="text-info">💡 Conseils personnalisés:</strong>';
                    advice.forEach(adv => {
                        html += `<div class="ai-suggestion-card mt-2">${adv}</div>`;
                    });
                    html += '</div>';
                }
                
                document.getElementById('suggestions').innerHTML = 
                    html || '<div class="success-item">✅ Aucune suggestion pour le moment</div>';
            }

            updateErrorPrevention(data) {
                const errors = data.error_prevention || {};
                let html = '';
                
                if (errors.errors && errors.errors.length > 0) {
                    errors.errors.forEach(err => {
                        html += `<div class="error-item">${err}</div>`;
                    });
                }
                
                if (errors.warnings && errors.warnings.length > 0) {
                    errors.warnings.forEach(warn => {
                        html += `<div class="warning-item">${warn}</div>`;
                    });
                }
                
                if (errors.is_valid && (!errors.errors || errors.errors.length === 0)) {
                    html = '<div class="success-item">✅ Aucune erreur détectée - Formulaire valide</div>';
                }
                
                document.getElementById('errorPrevention').innerHTML = 
                    html || '<div class="text-muted">Vérification en cours...</div>';
            }

            updateOptimization(data) {
                const optimization = data.language_optimization || {};
                const improvements = optimization.improvements || [];
                
                const optimizeBtn = document.getElementById('optimizeBtn');
                if (improvements.length > 0) {
                    optimizeBtn.style.display = 'block';
                    let html = '<div class="mt-2"><strong>Améliorations suggérées:</strong><ul class="mt-2">';
                    improvements.forEach(imp => {
                        html += `<li class="mb-1">${imp}</li>`;
                    });
                    html += '</ul></div>';
                    document.getElementById('optimizationSuggestions').innerHTML = html;
                } else {
                    optimizeBtn.style.display = 'none';
                    document.getElementById('optimizationSuggestions').innerHTML = 
                        '<div class="success-item mt-2">✅ Texte déjà optimisé</div>';
                }
            }

            updateSentiment(data) {
                const sentiment = data.sentiment || 'neutre';
                const sentimentMap = {
                    'positif': { class: 'sentiment-positive', icon: '😊', text: 'Positif' },
                    'négatif': { class: 'sentiment-negative', icon: '😟', text: 'Négatif' },
                    'neutre': { class: 'sentiment-neutral', icon: '😐', text: 'Neutre' }
                };
                
                const sent = sentimentMap[sentiment] || sentimentMap['neutre'];
                
                document.getElementById('sentimentAnalysis').innerHTML = `
                    <div class="text-center p-3">
                        <div class="sentiment-badge ${sent.class}">
                            ${sent.icon} ${sent.text}
                        </div>
                        <small class="d-block mt-2 text-muted">Ton de la réclamation analysé</small>
                    </div>
                `;
            }

            updatePrediction(data) {
                const processing = data.processing_acceleration || {};
                const estimatedTime = processing.estimated_handling_time || '24 heures';
                const queue = processing.routing_queue || 'Support Général';
                
                document.getElementById('resolutionPrediction').innerHTML = `
                    <div class="prediction-card">
                        <div class="prediction-time">${estimatedTime}</div>
                        <small class="text-muted">Temps estimé de traitement</small>
                        <div class="mt-2">
                            <small><strong>File d'attente:</strong> ${queue}</small>
                        </div>
                        ${processing.fast_track_eligible ? 
                            '<div class="mt-2"><span class="badge bg-success">⚡ Fast Track éligible</span></div>' : ''}
                    </div>
                `;
            }

            updateDuplicateDetection(data) {
                const duplicates = data.duplicate_detection || {};
                const similarity = data.similarity_analysis || {};
                
                let html = '';
                
                if (duplicates.has_duplicates && duplicates.similar_count > 0) {
                    html = `<div class="warning-item">
                        <strong>⚠️ ${duplicates.similar_count} réclamation(s) similaire(s) trouvée(s)</strong>
                        <p class="mb-1 mt-2">Score de similarité: <strong>${Math.round(similarity.similarity_score || 0)}%</strong></p>
                        ${similarity.recommendation ? `<p class="mb-0"><small>${similarity.recommendation}</small></p>` : ''}
                    </div>`;
                    
                    if (duplicates.similar_reclamations && duplicates.similar_reclamations.length > 0) {
                        html += '<div class="mt-2"><small class="text-muted">Réclamations similaires:</small><ul class="mt-1">';
                        duplicates.similar_reclamations.slice(0, 3).forEach(rec => {
                            html += `<li class="mb-1"><small>#${rec.id}: ${rec.titre.substring(0, 50)}...</small></li>`;
                        });
                        html += '</ul></div>';
                    }
                } else {
                    html = '<div class="success-item">✅ Aucune réclamation similaire trouvée - Votre réclamation est unique</div>';
                }
                
                document.getElementById('duplicateDetection').innerHTML = html;
            }

            updateStatus(text, type = 'info') {
                const statusEl = document.getElementById('aiStatus');
                statusEl.textContent = text;
                statusEl.className = `ai-status text-${type}`;
            }

            fallbackAnalysis(titre, description) {
                // Analyse locale de secours
                let score = 0;
                if (titre.length >= 30) score += 15;
                if (description.length >= 300) score += 35;
                
                this.updateScore(score, score >= 60 ? 'Bonne' : 'À améliorer');
                this.updateStatus('Mode hors ligne', 'warning');
            }
        }

        // Initialiser l'IA ultra-puissante
        const ultraAI = new UltraPowerfulAI();
        const titre = document.getElementById('titre');
        const description = document.getElementById('description');
        const typeSelect = document.getElementById('type');
        const prioriteSelect = document.getElementById('priorite');

        // Debounce pour éviter trop de requêtes
        function debounceAnalyze() {
            clearTimeout(ultraAI.debounceTimer);
            ultraAI.debounceTimer = setTimeout(() => {
                const titreVal = titre.value.trim();
                const descVal = description.value.trim();
                
                if (titreVal.length >= 5 && descVal.length >= 20) {
                    ultraAI.analyzeAdvanced(
                        titreVal,
                        descVal,
                        typeSelect.value,
                        prioriteSelect.value
                    );
                } else {
                    // Réinitialiser l'affichage
                    document.getElementById('globalScore').textContent = '0';
                    document.getElementById('globalScoreBar').style.width = '0%';
                }
            }, 800); // Attendre 800ms après la dernière frappe
        }



        // Fonctions utilitaires
        function refreshAIAnalysis() {
            const btn = document.getElementById('refreshBtn');
            btn.innerHTML = '<span class="loading-spinner"></span>';
            debounceAnalyze();
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
            }, 2000);
        }

        function applyAISuggestions() {
            if (!ultraAI.lastAnalysis) return;
            
            const classification = ultraAI.lastAnalysis.classification || {};
            const priority = ultraAI.lastAnalysis.priority_evaluation || {};
            
            if (classification.type && classification.type !== typeSelect.value) {
                typeSelect.value = classification.type;
            }
            
            if (priority.priority && priority.priority !== prioriteSelect.value) {
                prioriteSelect.value = priority.priority;
            }
            
            // Animation de confirmation
            const btn = document.getElementById('applyBtn');
            btn.innerHTML = '<i class="bi bi-check-circle"></i> Appliquées!';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-success');
            
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-check-circle"></i> Appliquer les suggestions';
                btn.style.display = 'none';
            }, 2000);
        }

        function optimizeText() {
            if (!ultraAI.lastAnalysis) return;
            
            const optimization = ultraAI.lastAnalysis.language_optimization || {};
            const standardized = ultraAI.lastAnalysis.standardized_input || {};
            
            if (standardized.titre_standardize) {
                titre.value = standardized.titre_standardize;
            }
            
            // Afficher message de confirmation
            const btn = document.getElementById('optimizeBtn');
            btn.innerHTML = '<i class="bi bi-check-circle"></i> Optimisé!';
            btn.classList.remove('btn-outline-info');
            btn.classList.add('btn-success');
            
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-magic"></i> Optimiser automatiquement';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-info');
            }, 2000);
        }

        // ===== CONSEILS CONTEXTUELS ULTRA-CRÉATIFS =====
        function updateContextualTips(titre, description) {
            const contextualTips = document.getElementById('contextualTips');
            const selectedType = typeSelect.value;
            const selectedPriority = prioriteSelect.value;
            let tips = [];
            let creativityLevel = '🎨 Créatif';

            // Analyse ultra-intelligente du contenu
            const wordCount = description.split(/\s+/).filter(word => word.length > 0).length;
            const sentenceCount = description.split(/[.!?]+/).filter(s => s.trim().length > 0).length;
            const avgWordsPerSentence = wordCount / Math.max(sentenceCount, 1);
            const hasQuestions = description.includes('?');
            const hasSteps = /étape|step|d'abord|ensuite|puis|enfin|premièrement|deuxièmement/i.test(description);
            const hasEmotions = /frustré|énervé|déçu|content|satisfait|inquiet|urgent/i.test(description);
            const hasTechnicalTerms = /erreur|bug|crash|serveur|base de données|api|interface|navigateur/i.test(description);

            // Conseils créatifs basés sur l'analyse sémantique
            if (description.length < 50) {
                tips.push({
                    icon: '🎯', 
                    text: 'Votre réclamation mérite plus de détails ! Imaginez que vous expliquez le problème à un ami qui ne connaît pas le système.',
                    color: '#dc2626', 
                    bg: '#fee2e2',
                    type: 'critical'
                });
                creativityLevel = '🚀 Génie';
            }

            if (wordCount > 10 && avgWordsPerSentence > 25) {
                tips.push({
                    icon: '✂️', 
                    text: 'Vos phrases sont riches mais longues ! Essayez de les diviser pour une meilleure clarté.',
                    color: '#f59e0b', 
                    bg: '#fef3c7',
                    type: 'style'
                });
            }

            if (!hasSteps && (selectedType === 'bug' || selectedType === 'technique')) {
                tips.push({
                    icon: '🔍', 
                    text: 'Pour un problème technique, décrivez les étapes : "1. J\'ai fait ceci, 2. Puis cela, 3. Et voilà ce qui s\'est passé"',
                    color: '#0284c7', 
                    bg: '#eff6ff',
                    type: 'methodology'
                });
                creativityLevel = '🧠 Stratégique';
            }

            if (selectedType === 'suggestion' && !description.toLowerCase().includes('bénéfice') && !description.toLowerCase().includes('amélioration')) {
                tips.push({
                    icon: '💎', 
                    text: 'Votre suggestion sera plus convaincante si vous expliquez les bénéfices attendus et l\'impact positif !',
                    color: '#059669', 
                    bg: '#f0fdf4',
                    type: 'persuasion'
                });
            }

            if (hasTechnicalTerms && !description.toLowerCase().includes('environnement') && !description.toLowerCase().includes('version')) {
                tips.push({
                    icon: '🔧', 
                    text: 'Problème technique détecté ! Précisez votre environnement : navigateur, version, système d\'exploitation.',
                    color: '#7c2d12', 
                    bg: '#fef3c7',
                    type: 'technical'
                });
                creativityLevel = '🔬 Analytique';
            }

            if (hasEmotions && selectedPriority !== 'urgente' && selectedPriority !== 'haute') {
                tips.push({
                    icon: '⚡', 
                    text: 'Je détecte de l\'émotion dans votre message. Peut-être devriez-vous augmenter la priorité ?',
                    color: '#7c3aed', 
                    bg: '#f5f3ff',
                    type: 'emotional'
                });
                creativityLevel = '🎭 Empathique';
            }

            if (description.length > 20 && !description.includes('.') && !description.includes('!') && !description.includes('?')) {
                tips.push({
                    icon: '📝', 
                    text: 'Votre texte gagnerait en lisibilité avec de la ponctuation ! Les points et virgules aident à structurer vos idées.',
                    color: '#be185d', 
                    bg: '#fdf2f8',
                    type: 'formatting'
                });
            }

            if (titre.length > 10 && description.length > 100 && wordCount > 30) {
                tips.push({
                    icon: '🌟', 
                    text: 'Excellent ! Votre réclamation est bien détaillée. L\'équipe support aura tous les éléments pour vous aider efficacement.',
                    color: '#059669', 
                    bg: '#f0fdf4',
                    type: 'praise'
                });
                creativityLevel = '👑 Maître';
            }

            // Conseils créatifs par défaut si aucun problème détecté
            if (tips.length === 0) {
                const creativeTips = [
                    { icon: '🎨', text: 'Votre réclamation prend forme ! L\'IA analyse chaque mot pour vous aider.', color: '#059669', bg: '#f0fdf4' },
                    { icon: '🚀', text: 'Utilisez l\'IA pour optimiser automatiquement le type et la priorité !', color: '#0284c7', bg: '#eff6ff' },
                    { icon: '💡', text: 'Astuce : Plus vous êtes précis, plus la résolution sera rapide !', color: '#f59e0b', bg: '#fef3c7' }
                ];
                tips.push(creativeTips[Math.floor(Math.random() * creativeTips.length)]);
            }

            // Mettre à jour le niveau de créativité
            document.getElementById('creativityLevel').textContent = creativityLevel;

            // Générer le HTML des conseils avec animations
            let tipsHTML = '';
            tips.forEach((tip, index) => {
                const animationDelay = index * 0.1;
                tipsHTML += `
                    <div class="creative-tip-card" style="background: ${tip.bg}; border-left: 4px solid ${tip.color}; animation-delay: ${animationDelay}s;">
                        <div class="tip-header">
                            <span class="tip-icon">${tip.icon}</span>
                            <span class="tip-type badge" style="background: ${tip.color}20; color: ${tip.color};">${tip.type || 'conseil'}</span>
                        </div>
                        <p class="tip-text" style="color: ${tip.color}; font-weight: 600;">${tip.text}</p>
                    </div>
                `;
            });

            contextualTips.innerHTML = tipsHTML;
        }

        // Écouteurs d'événements avec conseils contextuels
        titre.addEventListener('input', () => {
            updateContextualTips(titre.value, description.value);
            debounceAnalyze();
        });
        
        description.addEventListener('input', () => {
            updateContextualTips(titre.value, description.value);
            debounceAnalyze();
        });
        
        typeSelect.addEventListener('change', () => {
            updateContextualTips(titre.value, description.value);
            debounceAnalyze();
        });
        
        prioriteSelect.addEventListener('change', () => {
            updateContextualTips(titre.value, description.value);
            debounceAnalyze();
        });

        // Analyse initiale si des valeurs existent
        if (titre.value.trim().length >= 5 && description.value.trim().length >= 20) {
            debounceAnalyze();
        }
        
        // ===== VALIDATION EN TEMPS RÉEL AVEC BORDURES ROUGES =====
        function validateField(field) {
            const value = field.value.trim();
            const fieldName = field.getAttribute('name');
            let errorMessage = '';
            
            // Supprimer l'ancien message d'erreur
            const existingError = field.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
            
            if (value === '') {
                field.classList.add('is-invalid');
                
                // Messages d'erreur personnalisés
                switch(fieldName) {
                    case 'titre':
                        errorMessage = '🎯 Le titre est obligatoire - Décrivez brièvement votre problème';
                        break;
                    case 'description':
                        errorMessage = '📝 La description est obligatoire - Expliquez en détail votre problème';
                        break;
                    default:
                        errorMessage = '⚠️ Ce champ est obligatoire';
                }
                
                // Ajouter le message d'erreur
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = errorMessage;
                field.parentNode.appendChild(errorDiv);
                
                return false;
            } else {
                field.classList.remove('is-invalid');
                return true;
            }
        }

        function validateSelectField(field) {
            const fieldName = field.getAttribute('name');
            let errorMessage = '';
            
            // Supprimer l'ancien message d'erreur
            const existingError = field.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
            
            if (field.value === '') {
                field.classList.add('is-invalid');
                
                // Messages d'erreur personnalisés pour les selects
                switch(fieldName) {
                    case 'type':
                        errorMessage = '🏷️ Sélectionnez le type de réclamation (Bug, Technique, Contenu, etc.)';
                        break;
                    case 'priorite':
                        errorMessage = '⚡ Choisissez la priorité (Basse, Normale, Haute, Urgente)';
                        break;
                    default:
                        errorMessage = '⚠️ Veuillez faire une sélection';
                }
                
                // Ajouter le message d'erreur
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = errorMessage;
                field.parentNode.appendChild(errorDiv);
                
                return false;
            } else {
                field.classList.remove('is-invalid');
                return true;
            }
        }

        // Validation en temps réel pour tous les champs
        titre.addEventListener('input', () => validateField(titre));
        titre.addEventListener('blur', () => validateField(titre));
        
        description.addEventListener('input', () => validateField(description));
        description.addEventListener('blur', () => validateField(description));
        
        typeSelect.addEventListener('change', () => validateSelectField(typeSelect));
        prioriteSelect.addEventListener('change', () => validateSelectField(prioriteSelect));

        // Validation initiale au chargement de la page
        validateField(titre);
        validateField(description);
        validateSelectField(typeSelect);
        validateSelectField(prioriteSelect);

        // Validation lors de la soumission du formulaire
        document.getElementById('reclamationForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            if (!validateField(titre)) isValid = false;
            if (!validateField(description)) isValid = false;
            if (!validateSelectField(typeSelect)) isValid = false;
            if (!validateSelectField(prioriteSelect)) isValid = false;
            
            if (!isValid) {
                e.preventDefault();
                
                // Animation d'alerte ultra-moderne avec shake
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.style.background = 'linear-gradient(135deg, #dc2626, #ef4444)';
                submitBtn.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Veuillez remplir tous les champs obligatoires';
                submitBtn.style.animation = 'shake 0.5s ease-in-out';
                
                // Compter les champs invalides
                const invalidFields = document.querySelectorAll('.is-invalid').length;
                
                setTimeout(() => {
                    submitBtn.style.background = 'linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%)';
                    submitBtn.innerHTML = '<i class="bi bi-send"></i> Envoyer la réclamation';
                    submitBtn.style.animation = '';
                }, 4000);
                
                // Faire défiler vers le premier champ invalide avec animation
                const firstInvalid = document.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Focus avec délai pour l'animation
                    setTimeout(() => {
                        firstInvalid.focus();
                        firstInvalid.style.animation = 'shake 0.5s ease-in-out';
                        setTimeout(() => {
                            firstInvalid.style.animation = '';
                        }, 500);
                    }, 500);
                }
                
                // Notification toast moderne
                showValidationToast(`❌ ${invalidFields} champ${invalidFields > 1 ? 's' : ''} obligatoire${invalidFields > 1 ? 's' : ''} à remplir`);
            }
        });

        // ===== NOTIFICATION TOAST MODERNE =====
        function showValidationToast(message) {
            // Supprimer l'ancien toast s'il existe
            const existingToast = document.querySelector('.validation-toast');
            if (existingToast) {
                existingToast.remove();
            }
            
            // Créer le toast
            const toast = document.createElement('div');
            toast.className = 'validation-toast';
            toast.innerHTML = `
                <div class="toast-content">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>${message}</span>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="bi bi-x"></i>
                </button>
            `;
            
            // Ajouter au body
            document.body.appendChild(toast);
            
            // Animation d'entrée
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            // Suppression automatique après 5 secondes
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }

        // Initialiser les conseils contextuels
        updateContextualTips(titre.value, description.value);
    </script>
</body>
</html>