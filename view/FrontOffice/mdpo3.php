<?php
// view/FrontOffice/mdpo3.php
session_start();

// Vérifier que l'email et le code vérifié sont en session
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['verified_code'])) {
    header('Location: mdpo1.php');
    exit;
}

require_once __DIR__ . '/../../controller/userController.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($newPassword) || empty($confirmPassword)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($newPassword) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif (!preg_match('/[A-Z]/', $newPassword)) {
        $error = 'Le mot de passe doit contenir au moins une lettre majuscule.';
    } elseif (!preg_match('/[a-z]/', $newPassword)) {
        $error = 'Le mot de passe doit contenir au moins une lettre minuscule.';
    } elseif (!preg_match('/[0-9]/', $newPassword)) {
        $error = 'Le mot de passe doit contenir au moins un chiffre.';
    } elseif (!preg_match('/[@$!%*?&]/', $newPassword)) {
        $error = 'Le mot de passe doit contenir au moins un caractère spécial (@$!%*?&).';
    } else {
        $controller = new userController();
        $result = $controller->resetPassword(
            $_SESSION['reset_email'], 
            $_SESSION['verified_code'], 
            $newPassword
        );
        
        if ($result['success']) {
            // Nettoyer la session
            unset($_SESSION['reset_email']);
            unset($_SESSION['verified_code']);
            
            $success = true;
            
            // Redirection vers connexion après 2 secondes
            header("refresh:2;url=connexion.php");
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Nouveau mot de passe - Kernel</title>
  
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <link href="mdpo3.css" rel="stylesheet">
</head>

<body>
  <div class="password-container">
    <div class="row g-0">
      <div class="col-lg-5 password-left">
        <div class="logo mb-4">
          <i class="bi bi-hexagon-fill"></i> Kernel
        </div>
        <h2>Presque terminé !</h2>
        <p>Créez un nouveau mot de passe sécurisé pour protéger votre compte. Assurez-vous qu'il soit fort et unique.</p>
        
        <div class="password-illustration">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 250">
            <path d="M 150 40 L 200 60 L 200 130 Q 200 170 150 200 Q 100 170 100 130 L 100 60 Z" fill="#10B981" opacity="0.8"/>
            <path d="M 150 50 L 190 65 L 190 130 Q 190 160 150 185 Q 110 160 110 130 L 110 65 Z" fill="#FFFFFF"/>
            
            <path d="M 130 125 L 145 140 L 175 105" stroke="#10B981" stroke-width="6" fill="none" stroke-linecap="round"/>
            
            <circle cx="70" cy="180" r="25" fill="#F59E0B" opacity="0.8"/>
            <circle cx="63" cy="180" r="7" fill="white"/>
            <line x1="70" y1="180" x2="85" y2="180" stroke="white" stroke-width="4"/>
            <line x1="78" y1="175" x2="78" y2="180" stroke="white" stroke-width="4"/>
            <line x1="83" y1="175" x2="83" y2="180" stroke="white" stroke-width="4"/>
            
            <circle cx="230" cy="180" r="25" fill="#2563EB" opacity="0.8"/>
            <rect x="220" y="180" width="20" height="15" fill="white" rx="2"/>
            <rect x="225" y="173" width="10" height="10" fill="none" stroke="white" stroke-width="2" rx="5"/>
            
            <circle cx="50" cy="100" r="3" fill="#FBBF24"/>
            <circle cx="250" cy="120" r="4" fill="#FBBF24"/>
            <circle cx="150" cy="220" r="3" fill="#FBBF24"/>
          </svg>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
          <h5 style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">🔐 Conseils de sécurité</h5>
          <ul style="font-size: 14px; margin: 0; padding-left: 20px; opacity: 0.9; text-align: left;">
            <li>Utilisez au moins 8 caractères</li>
            <li>Mélangez majuscules et minuscules</li>
            <li>Ajoutez des chiffres et symboles</li>
            <li>Évitez les mots courants</li>
          </ul>
        </div>
      </div>
      
      <div class="col-lg-7 password-right">
        <div class="d-lg-none logo">
          <i class="bi bi-hexagon-fill"></i> Kernel
        </div>
        
        <h3 class="password-title">Créer un nouveau mot de passe</h3>
        <p class="password-subtitle">
          Votre nouveau mot de passe doit être différent des mots de passe précédents et respecter les critères de sécurité.
        </p>
        
        <div class="steps-indicator">
          <div class="step">
            <div class="step-circle">✓</div>
            <div class="step-label">Email</div>
          </div>
          <div class="step">
            <div class="step-circle">✓</div>
            <div class="step-label">Vérification</div>
          </div>
          <div class="step active">
            <div class="step-circle">3</div>
            <div class="step-label">Nouveau mot de passe</div>
          </div>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger">
          <i class="bi bi-x-circle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success">
          <i class="bi bi-check-circle me-2"></i>
          <strong>Succès !</strong> Votre mot de passe a été réinitialisé. Redirection vers la page de connexion...
        </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="passwordForm">
          <div class="mb-3">
            <label class="form-label">Nouveau mot de passe *</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" name="new_password" class="form-control" placeholder="Entrez votre nouveau mot de passe" required id="newPassword">
              <span class="input-group-text" style="cursor: pointer; border-left: none; border-radius: 0 10px 10px 0;" id="toggleNew">
                <i class="bi bi-eye" id="toggleIcon1"></i>
              </span>
            </div>
            <div class="password-strength">
              <div class="password-strength-bar" id="strengthBar"></div>
            </div>
            <div class="password-hint" id="strengthText">Le mot de passe doit contenir au moins 8 caractères</div>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Confirmer le mot de passe *</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
              <input type="password" name="confirm_password" class="form-control" placeholder="Confirmez votre mot de passe" required id="confirmPassword">
              <span class="input-group-text" style="cursor: pointer; border-left: none; border-radius: 0 10px 10px 0;" id="toggleConfirm">
                <i class="bi bi-eye" id="toggleIcon2"></i>
              </span>
            </div>
            <div class="password-hint" id="matchText"></div>
          </div>
          
          <div class="requirements-box">
            <h5 style="font-size: 14px; font-weight: 600; margin-bottom: 15px; color: var(--dark-color);">
              Votre mot de passe doit contenir :
            </h5>
            <div class="requirement-item" id="req-length">
              <i class="bi bi-circle"></i>
              <span>Au moins 8 caractères</span>
            </div>
            <div class="requirement-item" id="req-uppercase">
              <i class="bi bi-circle"></i>
              <span>Une lettre majuscule</span>
            </div>
            <div class="requirement-item" id="req-lowercase">
              <i class="bi bi-circle"></i>
              <span>Une lettre minuscule</span>
            </div>
            <div class="requirement-item" id="req-number">
              <i class="bi bi-circle"></i>
              <span>Un chiffre</span>
            </div>
            <div class="requirement-item" id="req-special">
              <i class="bi bi-circle"></i>
              <span>Un caractère spécial (@$!%*?&)</span>
            </div>
          </div>
          
          <button type="submit" class="btn-save" id="saveBtn" disabled>
            <i class="bi bi-check-circle me-2"></i> Enregistrer le mot de passe
          </button>
        </form>
      </div>
    </div>
  </div>

  <script src="mdpo3.js"></script>
</body>
</html>