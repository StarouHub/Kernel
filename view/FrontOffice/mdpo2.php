<?php
// view/FrontOffice/mdpo2.php
session_start();

// Vérifier que l'email est en session
if (!isset($_SESSION['reset_email'])) {
    header('Location: mdpo1.php');
    exit;
}

require_once __DIR__ . '/../../controller/userController.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resend'])) {
        // Renvoyer le code
        $controller = new userController();
        $result = $controller->sendResetCode($_SESSION['reset_email']);
        
        if ($result['success']) {
            $success = true;
        } else {
            $error = $result['message'];
        }
    } else {
        // Vérifier le code
        $code = trim($_POST['code'] ?? '');
        
        if (empty($code) || strlen($code) !== 6) {
            $error = 'Veuillez entrer un code à 6 chiffres.';
        } else {
            $controller = new userController();
            $result = $controller->verifyResetCode($_SESSION['reset_email'], $code);
            
            if ($result['success']) {
                // Stocker le code vérifié en session
                $_SESSION['verified_code'] = $code;
                header('Location: mdpo3.php');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Masquer partiellement l'email pour l'affichage
$email = $_SESSION['reset_email'];
$emailParts = explode('@', $email);
$maskedEmail = substr($emailParts[0], 0, 1) . '***' . substr($emailParts[0], -1) . '@' . $emailParts[1];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Vérification - Kernel</title>
  
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <link href="mdpo2.css" rel="stylesheet">
</head>

<body>
  <div class="verify-container">
    <div class="row g-0">
      <div class="col-lg-5 verify-left">
        <div class="logo mb-4">
          <i class="bi bi-hexagon-fill"></i> Kernel
        </div>
        <h2>Vérification en cours</h2>
        <p>Nous avons envoyé un code de vérification à 6 chiffres à votre adresse email. Entrez-le pour continuer.</p>
        
        <div class="verify-illustration">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 250">
            <rect x="75" y="80" width="150" height="110" fill="#FFFFFF" rx="10"/>
            <path d="M 75 80 L 150 140 L 225 80" fill="none" stroke="#60A5FA" stroke-width="4"/>
            <circle cx="150" cy="125" r="25" fill="#F59E0B"/>
            <text x="150" y="135" font-size="24" fill="white" text-anchor="middle" font-weight="bold">✓</text>
            
            <rect x="95" y="200" width="30" height="35" fill="#60A5FA" rx="5" opacity="0.8"/>
            <rect x="135" y="200" width="30" height="35" fill="#7C3AED" rx="5" opacity="0.8"/>
            <rect x="175" y="200" width="30" height="35" fill="#F59E0B" rx="5" opacity="0.8"/>
            
            <circle cx="50" cy="100" r="15" fill="#10B981" opacity="0.6"/>
            <circle cx="250" cy="180" r="20" fill="#EF4444" opacity="0.6"/>
          </svg>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
          <h5 style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">📧 Vérifiez votre email</h5>
          <p style="font-size: 14px; margin: 0; opacity: 0.9;">Si vous ne voyez pas l'email, consultez vos spams.</p>
        </div>
      </div>
      
      <div class="col-lg-7 verify-right">
        <div class="d-lg-none logo">
          <i class="bi bi-hexagon-fill"></i> Kernel
        </div>
        
        <h3 class="verify-title">Entrez le code de vérification</h3>
        <p class="verify-subtitle">
          Nous avons envoyé un code à <strong><?= htmlspecialchars($maskedEmail) ?></strong><br>
          Entrez le code à 6 chiffres ci-dessous.
        </p>
        
        <div class="steps-indicator">
          <div class="step completed">
            <div class="step-circle">✓</div>
            <div class="step-label">Email</div>
          </div>
          <div class="step active">
            <div class="step-circle">2</div>
            <div class="step-label">Vérification</div>
          </div>
          <div class="step">
            <div class="step-circle">3</div>
            <div class="step-label">Nouveau mot de passe</div>
          </div>
        </div>
        
        <div class="timer-box">
          <div>
            <i class="bi bi-clock"></i>
            <span style="color: #92400E; font-weight: 500;">Code expire dans</span>
          </div>
          <div class="timer" id="timer">30:00</div>
        </div>
        
        <?php if ($error): ?>
        <div class="error-message show">
          <i class="bi bi-x-circle me-2"></i>
          <strong>Erreur !</strong> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success">
          <i class="bi bi-check-circle me-2"></i>Code renvoyé avec succès !
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
          <div class="code-inputs">
            <input type="text" class="code-input" maxlength="1" name="digit1" id="code1" autofocus>
            <input type="text" class="code-input" maxlength="1" name="digit2" id="code2">
            <input type="text" class="code-input" maxlength="1" name="digit3" id="code3">
            <input type="text" class="code-input" maxlength="1" name="digit4" id="code4">
            <input type="text" class="code-input" maxlength="1" name="digit5" id="code5">
            <input type="text" class="code-input" maxlength="1" name="digit6" id="code6">
          </div>
          
          <input type="hidden" name="code" id="fullCode">
          
          <button type="submit" class="btn-verify" id="verifyBtn" disabled>
            <i class="bi bi-shield-check me-2"></i> Vérifier le code
          </button>
        </form>
        
        <div class="resend-link">
          <span style="color: #6B7280;">Vous n'avez pas reçu le code ?</span><br>
          <form method="POST" action="" style="display: inline;">
            <button type="submit" name="resend" value="1" id="resendBtn" class="btn btn-link" style="color: var(--primary-color); text-decoration: underline;">
              <i class="bi bi-arrow-clockwise me-1"></i> Renvoyer le code
            </button>
          </form>
        </div>
        
        <div class="back-link">
          <a href="mdpo1.php">
            <i class="bi bi-arrow-left"></i> Retour
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="mdpo2.js"></script>
</body>
</html>