<?php
// view/FrontOffice/detailreclamation.php
// CORRECTION : Chemin absolu
define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/init.php';

// Démarrer la session si pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . ROOT_PATH . '/indexx.php');
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: mesreclamations.php');
    exit;
}

// Initialiser les variables pour éviter les erreurs
$reclamation = [];
$reponses = [];
$piecesJointes = [];
$successMessage = '';
$errorMessage = '';

try {
    // Charger les contrôleurs
    require_once ROOT_PATH . '/controller/ReclamationController.php';
    require_once ROOT_PATH . '/controller/ReponseController.php';
    require_once ROOT_PATH . '/controller/PieceJointeController.php';
    
    $reclamationCtrl = new ReclamationController();
    $reponseCtrl = new ReponseController();
    $pieceJointeCtrl = new PieceJointeController();
    
    // Récupérer les données
    if (method_exists($reclamationCtrl, 'getDetails')) {
        $data = $reclamationCtrl->getDetails($id);
        
        if ($data['success'] && isset($data['reclamation'])) {
            $reclamation = $data['reclamation'];
            $reponses = $data['reponses'] ?? [];
            $piecesJointes = $data['pieces_jointes'] ?? [];
            
            // Vérifier que l'utilisateur est propriétaire
            if ($reclamation['utilisateur_id'] != $_SESSION['user_id'] && !isAdmin()) {
                header('Location: mesreclamations.php');
                exit;
            }
        } else {
            header('Location: mesreclamations.php');
            exit;
        }
    }
} catch (Exception $e) {
    error_log("Erreur detailreclamation: " . $e->getMessage());
    $errorMessage = "Erreur lors du chargement des données";
}

// Traitement du formulaire de réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        try {
            if (method_exists($reponseCtrl, 'create')) {
                $result = $reponseCtrl->create([
                    'reclamation_id' => $id,
                    'message' => $message,
                    'est_admin' => 0,
                    'est_interne' => 0
                ]);
                
                if ($result['success'] ?? false) {
                    $successMessage = $result['message'] ?? 'Message envoyé avec succès';
                    
                    // Recharger les données
                    $data = $reclamationCtrl->getDetails($id);
                    $reclamation = $data['reclamation'] ?? $reclamation;
                    $reponses = $data['reponses'] ?? $reponses;
                    $piecesJointes = $data['pieces_jointes'] ?? $piecesJointes;
                } else {
                    $errorMessage = $result['message'] ?? 'Erreur lors de l\'envoi du message';
                }
            } else {
                $errorMessage = 'Fonction non disponible';
            }
        } catch (Exception $e) {
            error_log("Erreur envoi message: " . $e->getMessage());
            $errorMessage = 'Erreur lors de l\'envoi du message';
        }
    } else {
        $errorMessage = "Veuillez saisir un message";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réclamation #<?= $id ?> - Kernel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #5b21b6;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .header {
            background: linear-gradient(135deg, #0A4FFF 0%, #4AA8FF 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: 0 4px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
        }
        
        .logo-container {
            width: 80px; height: 80px; background: white; border-radius: 20px; 
            display: flex; align-items: center; justify-content: center; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .reclamation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .reclamation-header {
            border-left: 5px solid var(--primary-color);
            padding-left: 20px;
            margin-bottom: 1.5rem;
        }
        
        .message-user {
            background: #f0f4ff;
            border-radius: 15px 15px 15px 0;
            padding: 15px;
            max-width: 75%;
            margin-left: auto;
            position: relative;
            border: 1px solid #dbeafe;
        }
        
        .message-admin {
            background: #e8f4ff;
            border-radius: 15px 15px 0 15px;
            padding: 15px;
            max-width: 75%;
            margin-right: auto;
            position: relative;
            border: 1px solid #bae6fd;
        }
        
        .message-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .attachment-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #dee2e6;
            transition: all 0.3s;
        }
        
        .attachment-card:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        
        .chat-container {
            max-height: 500px;
            overflow-y: auto;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-4">
                    <div class="logo-container">
                        <i class="bi bi-hexagon-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <div>
                        <h2 class="m-0 fw-bold" style="font-size: 32px; font-weight: 800; font-family: 'Raleway', sans-serif;">Réclamation #<?= $id ?></h2>
                        <small class="opacity-90">Suivi de votre demande</small>
                    </div>
                </div>
                <div>
                    <!-- CORRECTION : Chemins relatifs dans le même dossier -->
                    <a href="mesreclamations.php" class="btn btn-light me-2">
                        <i class="bi bi-arrow-left"></i> Mes réclamations
                    </a>
                    <a href="dashboard.php" class="btn btn-outline-light">
                        <i class="bi bi-house"></i> Accueil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Messages d'alerte -->
        <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-4"></i>
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
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div class="flex-grow-1">
                    <strong>Erreur !</strong> <?= htmlspecialchars($errorMessage) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php endif; ?>

        <?php if (empty($reclamation)): ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> Réclamation non trouvée ou accès refusé.
                <a href="mesreclamations.php" class="alert-link">Retour à mes réclamations</a>
            </div>
        <?php else: ?>
            <!-- En-tête de la réclamation -->
            <div class="reclamation-card p-4 mb-4">
                <div class="reclamation-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="mb-1"><?= htmlspecialchars($reclamation['titre'] ?? '') ?></h3>
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> Créée le <?= date('d/m/Y à H:i', strtotime($reclamation['date_creation'] ?? 'now')) ?>
                                <?php if (!empty($reclamation['date_modification'])): ?>
                                    • <i class="bi bi-pencil"></i> Modifiée le <?= date('d/m/Y à H:i', strtotime($reclamation['date_modification'])) ?>
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <div class="mb-2">
                                <?php
                                $statusColor = match($reclamation['statut'] ?? 'en-attente') {
                                    'en-attente' => 'warning',
                                    'en-cours' => 'info',
                                    'resolue' => 'success',
                                    'fermee' => 'secondary',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $statusColor ?> status-badge">
                                    <i class="bi <?= 
                                        ($reclamation['statut'] ?? 'en-attente') == 'en-attente' ? 'bi-hourglass-split' : 
                                        (($reclamation['statut'] ?? 'en-attente') == 'en-cours' ? 'bi-arrow-repeat' : 
                                        (($reclamation['statut'] ?? 'en-attente') == 'resolue' ? 'bi-check-circle' : 'bi-lock')) 
                                    ?>"></i>
                                    <?= ucfirst(str_replace('-', ' ', $reclamation['statut'] ?? 'en-attente')) ?>
                                </span>
                            </div>
                            <div>
                                <?php
                                $priorityColor = match($reclamation['priorite'] ?? 'normale') {
                                    'urgente' => 'danger',
                                    'haute' => 'warning',
                                    'normale' => 'primary',
                                    'basse' => 'secondary',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $priorityColor ?> me-1">
                                    <i class="bi bi-flag"></i> <?= ucfirst($reclamation['priorite'] ?? 'normale') ?>
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-tag"></i> <?= ucfirst($reclamation['type'] ?? 'autre') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5 class="mb-3"><i class="bi bi-card-text text-primary"></i> Description</h5>
                    <div class="bg-light p-4 rounded">
                        <p class="mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars($reclamation['description'] ?? '') ?></p>
                    </div>
                </div>
                
                <!-- Pièces jointes -->
                <?php if (!empty($piecesJointes)): ?>
                <div class="mb-4">
                    <h5 class="mb-3"><i class="bi bi-paperclip text-primary"></i> Pièces jointes (<?= count($piecesJointes) ?>)</h5>
                    <div class="row g-3">
                        <?php foreach($piecesJointes as $pj): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="attachment-card">
                                <div class="d-flex align-items-center">
                                    <i class="bi <?= 
                                        preg_match('/\.(jpg|jpeg|png|gif)$/i', $pj['nom_original'] ?? '') ? 'bi-file-image' :
                                        (preg_match('/\.pdf$/i', $pj['nom_original'] ?? '') ? 'bi-file-pdf' :
                                        (preg_match('/\.(doc|docx)$/i', $pj['nom_original'] ?? '') ? 'bi-file-word' :
                                        (preg_match('/\.(xls|xlsx)$/i', $pj['nom_original'] ?? '') ? 'bi-file-excel' : 'bi-file-earmark-text')))
                                    ?> text-primary fs-4 me-3"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold small text-truncate" title="<?= htmlspecialchars($pj['nom_original'] ?? '') ?>">
                                            <?= htmlspecialchars($pj['nom_original'] ?? '') ?>
                                        </div>
                                        <div class="text-muted small">
                                            <?= round(($pj['taille_octets'] ?? 0) / 1024, 1) ?> Ko • 
                                            <?= date('d/m/Y H:i', strtotime($pj['date_upload'] ?? 'now')) ?>
                                        </div>
                                    </div>
                                    <a href="<?= htmlspecialchars($pj['chemin'] ?? '#') ?>" 
                                       download="<?= htmlspecialchars($pj['nom_original'] ?? '') ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Télécharger">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Informations supplémentaires -->
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-info-circle text-primary"></i> Informations</h6>
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">ID :</span>
                                        <span class="fw-bold">#<?= $id ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Auteur :</span>
                                        <span class="fw-bold"><?= htmlspecialchars($reclamation['auteur'] ?? 'Vous') ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Statut :</span>
                                        <span class="fw-bold"><?= ucfirst(str_replace('-', ' ', $reclamation['statut'] ?? 'en-attente')) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Priorité :</span>
                                        <span class="fw-bold">
                                            <span class="badge bg-<?= $priorityColor ?>"><?= ucfirst($reclamation['priorite'] ?? 'normale') ?></span>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Type :</span>
                                        <span class="fw-bold"><?= ucfirst($reclamation['type'] ?? 'autre') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-clock text-primary"></i> Délais</h6>
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Ouverte depuis :</span>
                                        <span class="fw-bold">
                                            <?php
                                            try {
                                                $dateCreation = new DateTime($reclamation['date_creation'] ?? 'now');
                                                $now = new DateTime();
                                                $interval = $dateCreation->diff($now);
                                                echo $interval->days . ' jour(s)';
                                            } catch (Exception $e) {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Dernière activité :</span>
                                        <span class="fw-bold">
                                            <?php if (!empty($reponses)): ?>
                                                <?= date('d/m/Y H:i', strtotime(end($reponses)['date_reponse'] ?? 'now')) ?>
                                            <?php else: ?>
                                                Aucune
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Nombre de messages :</span>
                                        <span class="fw-bold"><?= count($reponses) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Pièces jointes :</span>
                                        <span class="fw-bold"><?= count($piecesJointes) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversation -->
            <div class="reclamation-card p-4">
                <h4 class="mb-4">
                    <i class="bi bi-chat-dots text-primary"></i> Conversation
                    <span class="badge bg-secondary ms-2"><?= count($reponses) ?> message<?= count($reponses) > 1 ? 's' : '' ?></span>
                </h4>
                
                <?php if (empty($reponses)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-chat-left-text display-1 text-muted"></i>
                    <h5 class="mt-3">Aucun message</h5>
                    <p class="text-muted">Notre équipe n'a pas encore répondu à votre réclamation</p>
                    <div class="mt-3">
                        <small class="text-primary">
                            <i class="bi bi-info-circle"></i> Notre équipe traitera votre demande dans les plus brefs délais
                        </small>
                    </div>
                </div>
                <?php else: ?>
                <div class="chat-container mb-4">
                    <?php foreach($reponses as $reponse): ?>
                    <div class="mb-4 <?= ($reponse['est_admin'] ?? false) ? 'message-admin' : 'message-user' ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong class="d-flex align-items-center">
                                    <?php if ($reponse['est_admin'] ?? false): ?>
                                    <i class="bi bi-shield-check text-primary me-2"></i> 
                                    <?= htmlspecialchars($reponse['repondeur'] ?? 'Administrateur') ?>
                                    <span class="badge bg-info ms-2">Support</span>
                                    <?php else: ?>
                                    <i class="bi bi-person-circle text-primary me-2"></i> Vous
                                    <?php endif; ?>
                                </strong>
                            </div>
                            <div>
                                <small class="message-time">
                                    <i class="bi bi-clock"></i> <?= date('d/m/Y à H:i', strtotime($reponse['date_reponse'] ?? 'now')) ?>
                                </small>
                            </div>
                        </div>
                        <p class="mb-2" style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($reponse['message'] ?? '')) ?></p>
                        
                        <!-- Pièces jointes de la réponse -->
                        <?php if (isset($reponse['pieces_jointes_count']) && $reponse['pieces_jointes_count'] > 0): ?>
                        <div class="mt-3">
                            <small class="text-primary">
                                <i class="bi bi-paperclip"></i> <?= $reponse['pieces_jointes_count'] ?> fichier(s) joint(s)
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Formulaire de réponse -->
                <?php if (($reclamation['statut'] ?? '') != 'fermee'): ?>
                <div class="border-top pt-4">
                    <form method="POST" id="replyForm">
                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">
                                <i class="bi bi-reply text-primary"></i> Ajouter un message :
                            </label>
                            <textarea class="form-control" 
                                      id="message" 
                                      name="message" 
                                      rows="4" 
                                      placeholder="Tapez votre message ici..." 
                                      required
                                      style="resize: vertical;"></textarea>
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> Votre message sera visible par l'équipe support.
                                <br><small>Astuce : Appuyez sur <kbd>Ctrl</kbd> + <kbd>Entrée</kbd> pour envoyer rapidement.</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send"></i> Envoyer le message
                            </button>
                            <div class="text-muted small text-end">
                                <i class="bi bi-clock-history"></i> Notre équipe vous répondra dans les plus brefs délais
                                <br><small>Statut actuel : <span class="fw-bold"><?= ucfirst(str_replace('-', ' ', $reclamation['statut'] ?? 'en-attente')) ?></span></small>
                            </div>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <div class="alert alert-secondary text-center">
                    <i class="bi bi-lock fs-4"></i>
                    <h5 class="mt-2">Cette réclamation est fermée</h5>
                    <p class="mb-0">Vous ne pouvez plus ajouter de messages à une réclamation fermée.</p>
                    <small class="d-block mt-2">
                        <!-- CORRECTION : Chemin relatif -->
                        <a href="nouvellereclamation.php" class="text-decoration-none">
                            <i class="bi bi-plus-circle"></i> Créer une nouvelle réclamation si nécessaire
                        </a>
                    </small>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation du formulaire
        const replyForm = document.getElementById('replyForm');
        if (replyForm) {
            replyForm.addEventListener('submit', function(e) {
                const message = document.getElementById('message');
                
                if (!message.value.trim()) {
                    e.preventDefault();
                    message.classList.add('is-invalid');
                    message.focus();
                    
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning alert-dismissible fade show mt-3';
                    alertDiv.innerHTML = `
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Veuillez saisir un message avant d'envoyer.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    
                    this.parentNode.insertBefore(alertDiv, this);
                    
                    return false;
                }
                
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Envoi en cours...';
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-secondary');
            });
        }
        
        // Auto-scroll vers le bas de la conversation
        function scrollToBottom() {
            const chatContainer = document.querySelector('.chat-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }
        
        // Auto-expand textarea
        const messageTextarea = document.getElementById('message');
        if (messageTextarea) {
            messageTextarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Touche Ctrl+Entrée pour envoyer
            messageTextarea.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    if (replyForm) replyForm.submit();
                }
            });
        }
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();
        });
    </script>
</body>
</html>