<?php
// view/backoffice/detailadmin.php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../controller/ReclamationController.php';
require_once __DIR__ . '/../../controller/ReponseController.php';
require_once __DIR__ . '/../../controller/PieceJointeController.php';
require_once __DIR__ . '/../../controller/AIController.php';

if (!isAdmin()) {
    header('Location: ../frontoffice/dashboard.php');
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: gestionreclamations.php');
    exit;
}

$reclamationCtrl = new ReclamationController();
$reponseCtrl = new ReponseController();
$pieceJointeCtrl = new PieceJointeController();
$aiCtrl = new AIController();

$data = $reclamationCtrl->getDetails($id);
if (!$data['success'] || !$data['reclamation']) {
    header('Location: gestionreclamations.php');
    exit;
}

$reclamation = $data['reclamation'];
$reponses = $data['reponses'];
$piecesJointes = $pieceJointeCtrl->getByReclamation($id);

// Récupérer les pièces jointes des réponses
$piecesJointesReponses = [];
foreach ($reponses as $reponse) {
    $pj = $pieceJointeCtrl->getByReponse($reponse['id']);
    if ($pj) {
        $piecesJointesReponses[$reponse['id']] = $pj;
    }
}

$successMessage = '';
$errorMessage = '';

// Analyse IA
$aiAnalysis = null;
$aiResponse = '';
if (isset($_GET['analyze_ai'])) {
    $aiAnalysis = $aiCtrl->analyzeReclamation($reclamation['titre'], $reclamation['description']);
    
    if ($aiAnalysis['success']) {
        // Générer une réponse basée sur l'analyse
        $aiResponse = generateAIResponseFromAnalysis($aiAnalysis['analysis'], $reclamation);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mettre à jour le statut
    if (isset($_POST['update_statut'])) {
        $result = $reclamationCtrl->updateStatut($id, $_POST['statut']);
        if ($result['success']) {
            $successMessage = $result['message'];
            $data = $reclamationCtrl->getDetails($id);
            $reclamation = $data['reclamation'];
            
            // Notification à l'utilisateur
            $statusText = str_replace('-', ' ', $_POST['statut']);
            sendRealTimeNotification(
                $reclamation['utilisateur_id'],
                "Votre réclamation #{$id} a été marquée comme '" . ucfirst($statusText) . "'",
                'info',
                'status_change',
                $id
            );
        } else {
            $errorMessage = $result['message'];
        }
    }
    
    // Ajouter une réponse
    elseif (isset($_POST['message']) && !empty(trim($_POST['message']))) {
        $message = trim($_POST['message']);
        $result = $reponseCtrl->ajouter($id, $message, true);
        
        if ($result['success']) {
            $reponse_id = $result['reponse_id'] ?? null;
            
            // Gérer les pièces jointes de la réponse
            if (!empty($_FILES['pieces_jointes_reponse']['name'][0]) && $reponse_id) {
                $uploadResult = $pieceJointeCtrl->upload($_FILES['pieces_jointes_reponse'], $id, $reponse_id);
                if (!$uploadResult['success']) {
                    $errorMessage .= " (Erreur upload: " . $uploadResult['message'] . ")";
                }
            }
            
            $successMessage = $result['message'];
            $data = $reclamationCtrl->getDetails($id);
            $reponses = $data['reponses'];
        } else {
            $errorMessage = $result['message'];
        }
    }
    
    // Upload de pièces jointes supplémentaires
    elseif (isset($_FILES['pieces_jointes_supp']['name'][0])) {
        $uploadResult = $pieceJointeCtrl->upload($_FILES['pieces_jointes_supp'], $id);
        if ($uploadResult['success']) {
            $successMessage = 'Pièces jointes ajoutées avec succès';
            $piecesJointes = $pieceJointeCtrl->getByReclamation($id);
        } else {
            $errorMessage = $uploadResult['message'];
        }
    }
    
    // Supprimer une pièce jointe
    elseif (isset($_POST['delete_attachment'])) {
        $result = $pieceJointeCtrl->delete($_POST['attachment_id']);
        if ($result['success']) {
            $successMessage = 'Pièce jointe supprimée';
            $piecesJointes = $pieceJointeCtrl->getByReclamation($id);
        } else {
            $errorMessage = $result['message'];
        }
    }
}

// Fonction pour générer une réponse IA
function generateAIResponseFromAnalysis($analysis, $reclamation) {
    $templates = [
        'bug' => "Bonjour,\n\nNous avons identifié le bug que vous avez signalé ({$analysis['type']}). Notre équipe technique travaille actuellement sur une correction. Nous estimons un délai de résolution de 2-3 jours ouvrables.\n\nEn attendant, voici une solution temporaire : [suggestion basée sur l'analyse]\n\nNous vous tiendrons informé dès que le correctif sera déployé.\n\nCordialement,\nL'équipe technique Kernel",
        
        'technique' => "Bonjour,\n\nNous avons analysé votre problème technique ({$analysis['type']}). Notre équipe support va prendre en charge votre demande dans les plus brefs délais.\n\nPour nous aider à résoudre plus rapidement :\n1. Fournissez des captures d'écran si possible\n2. Indiquez les étapes précises pour reproduire le problème\n3. Précisez votre environnement (navigateur, OS, etc.)\n\nNous reviendrons vers vous dans un délai de 24h.\n\nCordialement,\nL'équipe support Kernel",
        
        'contenu' => "Bonjour,\n\nMerci pour votre retour sur le contenu. Nous avons noté votre suggestion concernant : [détail du contenu].\n\nNotre équipe éditoriale va examiner votre proposition et procéder aux modifications si elles sont pertinentes. Vous recevrez une notification lorsque les changements seront effectifs.\n\nN'hésitez pas à nous contacter pour toute autre suggestion.\n\nCordialement,\nL'équipe éditoriale Kernel",
        
        'suggestion' => "Bonjour,\n\nMerci pour votre excellente suggestion ! Nous l'ajoutons à notre backlog d'améliorations pour les prochaines versions.\n\nVotre idée a été notée avec la priorité : {$analysis['priorite']}\n\nNous vous tiendrons informé lorsque cette fonctionnalité sera planifiée pour développement.\n\nCordialement,\nL'équipe produit Kernel"
    ];
    
    $template = $templates[$analysis['type']] ?? $templates['autre'] ?? "Bonjour,\n\nNous avons bien reçu votre message et allons traiter votre demande dans les meilleurs délais.\n\nUn membre de notre équipe vous contactera prochainement pour plus d'informations si nécessaire.\n\nCordialement,\nL'équipe support Kernel";
    
    // Personnaliser la réponse
    $response = str_replace(
        ['[suggestion basée sur l\'analyse]', '[détail du contenu]'],
        [
            $analysis['suggestions'] ? "Nous vous suggérons de : " . implode(', ', $analysis['suggestions']) : "Veuillez patienter pendant notre investigation.",
            substr($reclamation['description'], 0, 100) . '...'
        ],
        $template
    );
    
    return $response;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réclamation #<?= $id ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-primary: #1e3a8a;
            --admin-secondary: #4c1d95;
            --admin-accent: #7c3aed;
            --ai-color: #06b6d4;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--admin-primary), var(--admin-secondary));
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 280px;
            box-shadow: 5px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
            background: #f8fafc;
            min-height: 100vh;
        }
        
        .nav-link {
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 5px 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(5px);
        }
        
        .logo-container {
            width: 60px; height: 60px; background: white; border-radius: 15px; 
            display: flex; align-items: center; justify-content: center; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            margin: 0 auto 15px;
        }
        
        .reclamation-header-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(30, 58, 138, 0.1);
            border-left: 5px solid var(--admin-accent);
            margin-bottom: 30px;
        }
        
        .message-user {
            background: #f0f9ff;
            border-radius: 15px;
            border-bottom-left-radius: 5px;
            margin-right: 20%;
            border: 1px solid #bae6fd;
            position: relative;
        }
        
        .message-admin {
            background: linear-gradient(135deg, var(--admin-accent), #8b5cf6);
            color: white;
            border-radius: 15px;
            border-bottom-right-radius: 5px;
            margin-left: 20%;
            border: 1px solid #7c3aed;
            position: relative;
        }
        
        .message-user::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 20px;
            border: 10px solid transparent;
            border-right-color: #bae6fd;
        }
        
        .message-admin::after {
            content: '';
            position: absolute;
            right: -10px;
            top: 20px;
            border: 10px solid transparent;
            border-left-color: #7c3aed;
        }
        
        .chat-container {
            max-height: 600px;
            overflow-y: auto;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
        }
        
        .message-content {
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.6;
        }
        
        .attachment-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .attachment-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }
        
        .attachment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .attachment-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
            cursor: pointer;
        }
        
        .attachment-icon {
            width: 100%;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            color: var(--admin-primary);
            font-size: 2.5rem;
        }
        
        .ai-assistant-card {
            background: linear-gradient(135deg, #f0f9ff, #ecfeff);
            border-radius: 15px;
            border: 2px dashed var(--ai-color);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .ai-header {
            background: linear-gradient(135deg, var(--ai-color), #0ea5e9);
            color: white;
            padding: 20px;
        }
        
        .ai-suggestion {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 15px;
            border-left: 4px solid var(--ai-color);
        }
        
        .ai-badge {
            background: var(--ai-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-top: 4px solid var(--admin-accent);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--admin-primary);
            line-height: 1;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 25px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--admin-accent);
        }
        
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 17px;
            width: 2px;
            height: calc(100% + 8px);
            background: #e2e8f0;
        }
        
        .timeline-item:last-child::after {
            display: none;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .quick-action-btn {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .quick-action-btn:hover {
            border-color: var(--admin-accent);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(124, 58, 237, 0.1);
        }
        
        .priority-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .priority-urgente { background: #ef4444; }
        .priority-haute { background: #f97316; }
        .priority-normale { background: #3b82f6; }
        .priority-basse { background: #6b7280; }
        
        /* Animation pour nouveaux messages */
        @keyframes highlightNew {
            0% { background-color: rgba(255, 255, 0, 0.1); }
            100% { background-color: transparent; }
        }
        
        .new-message {
            animation: highlightNew 2s ease-in-out;
        }
        
        /* Scrollbar personnalisée */
        .chat-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .chat-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .chat-container::-webkit-scrollbar-thumb {
            background: var(--admin-accent);
            border-radius: 10px;
        }
        
        .chat-container::-webkit-scrollbar-thumb:hover {
            background: var(--admin-secondary);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .message-user, .message-admin {
                margin: 10px 0;
            }
        }
        
        .notification-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 11px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-4">
            <div class="text-center mb-4">
                <div class="logo-container">
                    <i class="bi bi-shield-check text-primary" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1">Kernel Admin</h4>
                <small class="text-light">Panel de gestion</small>
            </div>
            
            <div class="mb-4">
                <div class="d-flex align-items-center bg-white bg-opacity-10 p-3 rounded">
                    <div class="rounded-circle bg-white p-2 me-2">
                        <i class="bi bi-person-fill text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold"><?= htmlspecialchars($_SESSION['nom']) ?></div>
                        <small class="text-light">Administrateur</small>
                    </div>
                    <span class="badge bg-success">En ligne</span>
                </div>
            </div>
            
            <nav class="nav flex-column">
                <a href="dashboard.php" class="nav-link">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="gestionreclamations.php" class="nav-link">
                    <i class="bi bi-list-task"></i> Gestion réclamations
                </a>
                <a href="statistiques.php" class="nav-link">
                    <i class="bi bi-bar-chart"></i> Statistiques
                </a>
                <a href="?id=<?= $id ?>&analyze_ai=1" class="nav-link">
                    <i class="bi bi-robot"></i> Analyse IA
                </a>
                <hr class="text-white my-3">
                <div class="dropdown">
                    <button class="btn btn-light position-relative w-100" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i> Notifications
                        <?php if (getUnreadNotificationsCount() > 0): ?>
                            <span class="notification-count"><?= getUnreadNotificationsCount() ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end w-100">
                        <div class="dropdown-header">Notifications récentes</div>
                        <?php foreach (array_slice($_SESSION['notifications'] ?? [], 0, 5) as $n): ?>
                            <a class="dropdown-item small" href="#">
                                <i class="bi <?= $n['icon'] ?? 'bi-bell' ?> text-<?= $n['color'] ?? 'secondary' ?>"></i>
                                <?= htmlspecialchars($n['message'] ?? '') ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="../frontoffice/dashboard.php" class="nav-link">
                    <i class="bi bi-arrow-left-right"></i> Interface utilisateur
                </a>
                <a href="../../logout.php" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </nav>
            
            <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                <small class="text-light opacity-75 d-block mb-2">Réclamation #<?= $id ?></small>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-light">
                        <i class="bi bi-clock"></i> <?= date('H:i') ?>
                    </small>
                    <span class="badge bg-light text-dark">
                        <?= count($reponses) ?> messages
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <!-- Navigation -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-white p-3 rounded shadow-sm">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="gestionreclamations.php">Gestion réclamations</a></li>
                <li class="breadcrumb-item active">Détails #<?= $id ?></li>
            </ol>
        </nav>

        <!-- Messages d'alerte -->
        <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">
                    <strong>Succès !</strong> <?= htmlspecialchars($successMessage) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">
                    <strong>Erreur !</strong> <?= htmlspecialchars($errorMessage) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php endif; ?>

        <!-- En-tête de la réclamation -->
        <div class="reclamation-header-card p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <h2 class="mb-0 me-3"><?= htmlspecialchars($reclamation['titre']) ?></h2>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="update_statut" value="1">
                            <select name="statut" 
                                    class="form-select form-select-sm d-inline" 
                                    onchange="this.form.submit()"
                                    style="width: auto; min-width: 150px;">
                                <option value="en-attente" <?= $reclamation['statut']=='en-attente'?'selected':'' ?>>⏳ En attente</option>
                                <option value="en-cours" <?= $reclamation['statut']=='en-cours'?'selected':'' ?>>🔧 En cours</option>
                                <option value="resolue" <?= $reclamation['statut']=='resolue'?'selected':'' ?>>✅ Résolue</option>
                                <option value="fermee" <?= $reclamation['statut']=='fermee'?'selected':'' ?>>🔒 Fermée</option>
                            </select>
                        </form>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-secondary">
                            <i class="bi bi-tag"></i> <?= ucfirst($reclamation['type']) ?>
                        </span>
                        <span class="badge 
                            <?= $reclamation['priorite'] == 'urgente' ? 'bg-danger' : 
                               ($reclamation['priorite'] == 'haute' ? 'bg-warning' : 
                               ($reclamation['priorite'] == 'normale' ? 'bg-primary' : 'bg-secondary')) ?>">
                            <span class="priority-indicator priority-<?= $reclamation['priorite'] ?>"></span>
                            <?= ucfirst($reclamation['priorite']) ?>
                        </span>
                        <span class="badge 
                            <?= $reclamation['statut'] == 'en-attente' ? 'bg-warning text-dark' : 
                               ($reclamation['statut'] == 'en-cours' ? 'bg-info' : 'bg-success') ?>">
                            <?= ucfirst(str_replace('-', ' ', $reclamation['statut'])) ?>
                        </span>
                        <?php if (count($piecesJointes) > 0): ?>
                        <span class="badge bg-info">
                            <i class="bi bi-paperclip"></i> <?= count($piecesJointes) ?> pièce(s) jointe(s)
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Description :</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0 message-content"><?= nl2br(htmlspecialchars($reclamation['description'])) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stats-card">
                        <h5><i class="bi bi-info-circle"></i> Informations</h5>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Auteur :</small>
                                <small><strong><?= htmlspecialchars($reclamation['auteur'] ?? 'Inconnu') ?></strong></small>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">ID Utilisateur :</small>
                                <small><strong>#<?= $reclamation['utilisateur_id'] ?></strong></small>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Créée le :</small>
                                <small><strong><?= date('d/m/Y H:i', strtotime($reclamation['date_creation'])) ?></strong></small>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Dernière activité :</small>
                                <small><strong><?= !empty($reponses) ? date('d/m/Y H:i', strtotime(end($reponses)['date_reponse'])) : 'Aucune' ?></strong></small>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Durée ouverte :</small>
                                <small><strong>
                                    <?php
                                    $dateCreation = new DateTime($reclamation['date_creation']);
                                    $now = new DateTime();
                                    $interval = $dateCreation->diff($now);
                                    echo $interval->days . 'j ' . $interval->h . 'h';
                                    ?>
                                </strong></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assistant IA -->
        <?php if ($aiAnalysis && $aiAnalysis['success']): ?>
        <div class="ai-assistant-card mb-4">
            <div class="ai-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><i class="bi bi-robot"></i> Kernel Assistant - Analyse IA</h4>
                        <small>Analyse automatique de la réclamation</small>
                    </div>
                    <span class="badge bg-light text-dark">
                        Confiance : <?= round(($aiAnalysis['analysis']['confidence'] ?? 0) * 100) ?>%
                    </span>
                </div>
            </div>
            
            <div class="ai-suggestion">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-lightbulb"></i> Recommandations :</h6>
                        <div class="mt-2">
                            <span class="ai-badge mb-2">
                                <i class="bi bi-tag"></i> Type : <?= ucfirst($aiAnalysis['analysis']['type']) ?>
                            </span>
                            <span class="ai-badge mb-2">
                                <i class="bi bi-flag"></i> Priorité : <?= ucfirst($aiAnalysis['analysis']['priorite']) ?>
                            </span>
                            <?php if (!empty($aiAnalysis['analysis']['suggestions'])): ?>
                            <div class="mt-3">
                                <small class="text-muted d-block mb-1">Pièces jointes recommandées :</small>
                                <?php foreach($aiAnalysis['analysis']['suggestions'] as $suggestion): ?>
                                <span class="badge bg-light text-dark mb-1">
                                    <i class="bi bi-check"></i> <?= $suggestion ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-chat-left-text"></i> Réponse IA suggérée :</h6>
                        <div class="bg-light p-3 rounded mt-2" style="max-height: 200px; overflow-y: auto;">
                            <p class="mb-0 small"><?= nl2br(htmlspecialchars($aiResponse)) ?></p>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-primary" onclick="useAIResponse()">
                                <i class="bi bi-check-circle"></i> Utiliser cette réponse
                            </button>
                            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="editAIResponse()">
                                <i class="bi bi-pencil"></i> Modifier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions rapides -->
        <div class="quick-actions mb-4">
            <div class="quick-action-btn" onclick="document.getElementById('admin_message').focus()">
                <i class="bi bi-reply text-primary" style="font-size: 2rem;"></i>
                <div class="mt-2">Répondre</div>
                <small class="text-muted">Envoyer une réponse</small>
            </div>
            
            <div class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#attachmentsModal">
                <i class="bi bi-paperclip text-info" style="font-size: 2rem;"></i>
                <div class="mt-2">Pièces jointes</div>
                <small class="text-muted"><?= count($piecesJointes) ?> fichier(s)</small>
            </div>
            
            <div class="quick-action-btn" onclick="window.print()">
                <i class="bi bi-printer text-secondary" style="font-size: 2rem;"></i>
                <div class="mt-2">Imprimer</div>
                <small class="text-muted">Exporter cette conversation</small>
            </div>
            
            <div class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#timelineModal">
                <i class="bi bi-clock-history text-success" style="font-size: 2rem;"></i>
                <div class="mt-2">Timeline</div>
                <small class="text-muted">Voir l'historique</small>
            </div>
        </div>

        <!-- Conversation -->
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-chat-dots"></i> Conversation</h4>
                    <span class="badge bg-light text-dark">
                        <?= count($reponses) ?> message<?= count($reponses) > 1 ? 's' : '' ?>
                    </span>
                </div>
            </div>
            
            <div class="card-body p-0">
                <?php if (empty($reponses)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-chat-left-text" style="font-size: 4rem; color: #dee2e6;"></i>
                    <h5 class="mt-3">Aucun message</h5>
                    <p class="text-muted">Démarrez la conversation avec l'utilisateur</p>
                </div>
                <?php else: ?>
                <div class="chat-container" id="chatContainer">
                    <?php foreach($reponses as $reponse): ?>
                    <div class="mb-4 p-3 <?= $reponse['est_admin'] ? 'message-admin' : 'message-user' ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong class="d-flex align-items-center gap-2">
                                    <?php if ($reponse['est_admin']): ?>
                                    <i class="bi bi-shield-check"></i> Administrateur
                                    <?php else: ?>
                                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($reponse['repondeur'] ?? 'Utilisateur') ?>
                                    <?php endif; ?>
                                </strong>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">
                                    <?= date('d/m/Y H:i', strtotime($reponse['date_reponse'])) ?>
                                </small>
                                <?php if (isset($piecesJointesReponses[$reponse['id']])): ?>
                                <small class="text-primary">
                                    <i class="bi bi-paperclip"></i> <?= count($piecesJointesReponses[$reponse['id']]) ?> fichier(s)
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="mb-2 message-content"><?= nl2br(htmlspecialchars($reponse['message'])) ?></p>
                        
                        <!-- Pièces jointes de la réponse -->
                        <?php if (isset($piecesJointesReponses[$reponse['id']])): ?>
                        <div class="attachment-preview mt-3">
                            <?php foreach($piecesJointesReponses[$reponse['id']] as $pj): ?>
                            <div class="attachment-card">
                                <?php
                                $extension = strtolower(pathinfo($pj['nom_original'], PATHINFO_EXTENSION));
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                ?>
                                
                                <?php if ($isImage): ?>
                                <img src="<?= $pj['chemin'] ?>" 
                                     alt="<?= htmlspecialchars($pj['nom_original']) ?>" 
                                     class="attachment-image"
                                     onclick="viewImage(this)">
                                <?php else: ?>
                                <div class="attachment-icon">
                                    <i class="bi bi-file-earmark-<?= 
                                        $extension == 'pdf' ? 'pdf' : 
                                        (in_array($extension, ['doc', 'docx']) ? 'word' : 
                                        ($extension == 'mp4' ? 'play' : 'text')) 
                                    ?>"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div class="p-3">
                                    <small class="d-block text-truncate" title="<?= htmlspecialchars($pj['nom_original']) ?>">
                                        <?= htmlspecialchars($pj['nom_original']) ?>
                                    </small>
                                    <small class="text-muted">
                                        <?= round($pj['taille_octets'] / 1024, 1) ?> Ko
                                    </small>
                                    <div class="mt-2">
                                        <a href="<?= $pj['chemin'] ?>" 
                                           download="<?= htmlspecialchars($pj['nom_original']) ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Formulaire de réponse -->
                <div class="p-4 border-top">
                    <form method="post" id="adminReplyForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="admin_message" class="form-label fw-bold">
                                <i class="bi bi-reply"></i> Répondre en tant qu'administrateur :
                            </label>
                            <textarea class="form-control" 
                                      id="admin_message" 
                                      name="message" 
                                      rows="4" 
                                      placeholder="Tapez votre réponse ici..." 
                                      required></textarea>
                            <div class="form-text">
                                Votre réponse sera envoyée à l'utilisateur et marquera la réclamation comme "En cours".
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="pieces_jointes_reponse" class="form-label">
                                <i class="bi bi-paperclip"></i> Ajouter des pièces jointes à la réponse :
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="pieces_jointes_reponse" 
                                   name="pieces_jointes_reponse[]" 
                                   multiple
                                   accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.mp4">
                            <div class="form-text">
                                Formats acceptés : Images, PDF, Word, TXT, MP4 • Max 10 Mo par fichier
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary" id="adminSubmitBtn">
                                <i class="bi bi-send"></i> Envoyer la réponse
                            </button>
                            <div>
                                <a href="gestionreclamations.php" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-left"></i> Retour à la liste
                                </a>
                                <button type="button" class="btn btn-outline-info" onclick="insertAITemplate()">
                                    <i class="bi bi-robot"></i> Modèle IA
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pièces jointes -->
    <div class="modal fade" id="attachmentsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-paperclip"></i> Pièces jointes de la réclamation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($piecesJointes)): ?>
                    <div class="attachment-preview">
                        <?php foreach($piecesJointes as $pj): ?>
                        <div class="attachment-card">
                            <?php
                            $extension = strtolower(pathinfo($pj['nom_original'], PATHINFO_EXTENSION));
                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                            ?>
                            
                            <?php if ($isImage): ?>
                            <img src="<?= $pj['chemin'] ?>" 
                                 alt="<?= htmlspecialchars($pj['nom_original']) ?>" 
                                 class="attachment-image"
                                 onclick="viewImage(this)">
                            <?php else: ?>
                            <div class="attachment-icon">
                                <i class="bi bi-file-earmark-<?= 
                                    $extension == 'pdf' ? 'pdf' : 
                                    (in_array($extension, ['doc', 'docx']) ? 'word' : 
                                    ($extension == 'mp4' ? 'play' : 'text')) 
                                ?>"></i>
                            </div>
                            <?php endif; ?>
                            
                            <div class="p-3">
                                <small class="d-block text-truncate" title="<?= htmlspecialchars($pj['nom_original']) ?>">
                                    <?= htmlspecialchars($pj['nom_original']) ?>
                                </small>
                                <small class="text-muted d-block">
                                    <?= round($pj['taille_octets'] / 1024, 1) ?> Ko • 
                                    <?= date('d/m/Y H:i', strtotime($pj['date_upload'])) ?>
                                </small>
                                <div class="mt-2 d-flex gap-2">
                                    <a href="<?= $pj['chemin'] ?>" 
                                       download="<?= htmlspecialchars($pj['nom_original']) ?>" 
                                       class="btn btn-sm btn-outline-primary flex-grow-1">
                                        <i class="bi bi-download"></i> Télécharger
                                    </a>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="delete_attachment" value="1">
                                        <input type="hidden" name="attachment_id" value="<?= $pj['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Supprimer cette pièce jointe ?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-paperclip display-1 text-muted"></i>
                        <h5 class="mt-3">Aucune pièce jointe</h>
                        <p class="text-muted">Aucun fichier n'a été joint à cette réclamation</p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Formulaire d'ajout de pièces jointes -->
                    <div class="mt-4 pt-4 border-top">
                        <h6><i class="bi bi-cloud-upload"></i> Ajouter des pièces jointes supplémentaires</h6>
                        <form method="post" enctype="multipart/form-data" class="mt-3">
                            <div class="mb-3">
                                <input type="file" 
                                       name="pieces_jointes_supp[]" 
                                       multiple 
                                       class="form-control"
                                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.mp4">
                                <div class="form-text">
                                    Vous pouvez ajouter jusqu'à 10 fichiers, 10 Mo max par fichier
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Uploader
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Timeline -->
    <div class="modal fade" id="timelineModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-clock-history"></i> Timeline de la réclamation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Réclamation créée</strong>
                                    <p class="mb-0 text-muted">Par <?= htmlspecialchars($reclamation['auteur'] ?? 'Utilisateur') ?></p>
                                </div>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($reclamation['date_creation'])) ?></small>
                            </div>
                        </div>
                        
                        <?php foreach($reponses as $reponse): ?>
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?= $reponse['est_admin'] ? 'Réponse administrateur' : 'Réponse utilisateur' ?></strong>
                                    <p class="mb-0 text-muted">Par <?= htmlspecialchars($reponse['repondeur'] ?? ($reponse['est_admin'] ? 'Administrateur' : 'Utilisateur')) ?></p>
                                    <p class="mb-0 mt-1 small"><?= substr($reponse['message'], 0, 100) ?>...</p>
                                </div>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($reponse['date_reponse'])) ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if ($reclamation['statut'] != 'en-attente'): ?>
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Statut changé</strong>
                                    <p class="mb-0 text-muted">De "en-attente" à "<?= str_replace('-', ' ', $reclamation['statut']) ?>"</p>
                                </div>
                                <small class="text-muted"><?= date('d/m/Y H:i') ?></small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Viewer pour images -->
    <div id="imageViewer" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; cursor: pointer;">
        <div style="position: absolute; top: 20px; right: 20px;">
            <button class="btn btn-danger" onclick="closeImageViewer()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <img id="viewerImage" style="max-width: 90%; max-height: 90%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.js"></script>
    <script>
        // Utiliser la réponse IA
        function useAIResponse() {
            const aiResponse = `<?= addslashes($aiResponse ?? '') ?>`;
            if (aiResponse) {
                document.getElementById('admin_message').value = aiResponse;
                document.getElementById('admin_message').focus();
                showToast('Réponse IA insérée', 'success');
            }
        }
        
        function editAIResponse() {
            useAIResponse();
            showToast('Vous pouvez maintenant modifier la réponse IA', 'info');
        }
        
        // Insérer un modèle de réponse
        function insertAITemplate() {
            const templates = {
                'bug': "Bonjour,\n\nNous avons identifié le bug que vous avez signalé. Notre équipe technique travaille actuellement sur une correction. Nous vous tiendrons informé dès que le correctif sera déployé.\n\nCordialement,\nL'équipe technique",
                'technique': "Bonjour,\n\nNous avons pris en compte votre problème technique. Notre équipe va analyser la situation et vous proposera une solution dans les plus brefs délais.\n\nCordialement,\nL'équipe support",
                'contenu': "Bonjour,\n\nMerci pour votre retour sur le contenu. Nous allons examiner votre suggestion et procéder aux modifications si nécessaire.\n\nCordialement,\nL'équipe éditoriale",
                'suggestion': "Bonjour,\n\nMerci pour votre suggestion ! Nous l'ajoutons à notre liste d'améliorations à étudier pour les prochaines versions.\n\nCordialement,\nL'équipe produit"
            };
            
            const type = '<?= $reclamation["type"] ?? "autre" ?>';
            const template = templates[type] || templates['technique'];
            
            document.getElementById('admin_message').value = template;
            document.getElementById('admin_message').focus();
            showToast('Modèle inséré', 'success');
        }
        
        // Visualiser les images
        function viewImage(img) {
            const viewer = document.getElementById('imageViewer');
            const viewerImage = document.getElementById('viewerImage');
            
            viewerImage.src = img.src;
            viewer.style.display = 'block';
        }
        
        function closeImageViewer() {
            document.getElementById('imageViewer').style.display = 'none';
        }
        
        // Toast notifications
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
            toast.style.zIndex = 9999;
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-info-circle'} me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
        
        // Auto-scroll vers le bas de la conversation
        function scrollToBottom() {
            const chatContainer = document.getElementById('chatContainer');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }
        
        // Marquer les nouveaux messages
        function highlightNewMessages() {
            document.querySelectorAll('.message-user, .message-admin').forEach(msg => {
                msg.classList.add('new-message');
                setTimeout(() => {
                    msg.classList.remove('new-message');
                }, 2000);
            });
        }
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();
            highlightNewMessages();
            
            // Gestion du formulaire de réponse
            document.getElementById('adminReplyForm').addEventListener('submit', function(e) {
                const message = document.getElementById('admin_message').value.trim();
                if (!message) {
                    e.preventDefault();
                    showToast('Veuillez saisir un message', 'warning');
                    return false;
                }
                
                const submitBtn = document.getElementById('adminSubmitBtn');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Envoi en cours...';
                submitBtn.disabled = true;
                
                return true;
            });
            
            // Touche Ctrl+Enter pour envoyer
            document.getElementById('admin_message').addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    document.getElementById('adminReplyForm').submit();
                }
            });
            
            // Auto-expand textarea
            document.getElementById('admin_message').addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
        
        // Fermer la visualisation d'image avec ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageViewer();
            }
        });
        
        // Gérer le clic en dehors de l'image pour fermer
        document.getElementById('imageViewer').addEventListener('click', function(e) {
            if (e.target.id === 'imageViewer') {
                closeImageViewer();
            }
        });
    </script>
</body>
</html>