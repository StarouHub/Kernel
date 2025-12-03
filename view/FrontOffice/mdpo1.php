<?php
// view/FrontOffice/mdpo1.php
session_start();

require_once __DIR__ . '/../../controller/userController.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Veuillez entrer votre adresse email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } else {
        $controller = new userController();
        $result = $controller->sendResetCode($email);
        
        if ($result['success']) {
            // Stocker l'email en session pour les pages suivantes
            $_SESSION['reset_email'] = $email;
            $success = $result['message'];
            
            // Redirection automatique après 2 secondes
            header("refresh:2;url=mdpo2.php");
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
  <title>Mot de passe oublié - Kernel</title>

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

  <link href="mdpo1.css" rel="stylesheet">
</head>

<body>
  <div class="reset-container">
    <div class="row g-0">
      <div class="col-lg-5 reset-left">
        <div class="logo mb-4">
          <i class="bi bi-hexagon-fill"></i> Kernel
        </div>
        <h2>Réinitialisation sécurisée</h2>
        <p>Ne vous inquiétez pas ! La réinitialisation de votre mot de passe est simple et rapide. Nous vous enverrons un code sécurisé par email.</p>

        <div class="reset-illustration">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 250">
            <circle cx="150" cy="100" r="50" fill="#60A5FA" opacity="0.3"/>
            <rect x="125" y="90" width="50" height="50" fill="#FFFFFF" rx="5"/>
            <rect x="135" y="80" width="30" height="20" fill="none" stroke="#FFFFFF" stroke-width="3" rx="15"/>
            <circle cx="150" cy="115" r="5" fill="#2563EB"/>
            <rect x="148" y="120" width="4" height="12" fill="#2563EB"/>

            <circle cx="80" cy="180" r="25" fill="#F59E0B" opacity="0.8"/>
            <path d="M 65 175 L 80 185 L 95 175" stroke="white" stroke-width="2" fill="none"/>
            <rect x="65" y="172" width="30" height="18" fill="none" stroke="white" stroke-width="2" rx="2"/>

            <circle cx="220" cy="180" r="25" fill="#10B981" opacity="0.8"/>
            <circle cx="210" cy="180" r="6" fill="white"/>
            <line x1="216" y1="180" x2="230" y2="180" stroke="white" stroke-width="3"/>
            <line x1="224" y1="176" x2="224" y2="180" stroke="white" stroke-width="3"/>
            <line x1="228" y1="176" x2="228" y2="180" stroke="white" stroke-width="3"/>

            <line x1="105" y1="180" x2="125" y2="140" stroke="#FFFFFF" stroke-width="2" stroke-dasharray="4,4" opacity="0.5"/>
            <line x1="195" y1="180" x2="175" y2="140" stroke="#FFFFFF" stroke-width="2" stroke-dasharray="4,4" opacity="0.5"/>
          </svg>
        </div>

        <div style="margin-top: 30px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
          <h5 style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">🔒 Sécurité garantie</h5>
          <p style="font-size: 14px; margin: 0; opacity: 0.9;">Vos données sont protégées avec un chiffrement de niveau bancaire.</p>
        </div>
      </div>

      <div class="col-lg-7 reset-right">
        <div class="d-lg-none logo">
          <i class="bi bi-hexagon-fill"></i> Kernel
        </div>

        <h3 class="reset-title">Mot de passe oublié ?</h3>
        <p class="reset-subtitle">Pas de problème ! Entrez votre adresse email et nous vous enverrons un code pour réinitialiser votre mot de passe.</p>

        <div class="steps-indicator">
          <div class="step active">
            <div class="step-circle">1</div>
            <div class="step-label">Email</div>
          </div>
          <div class="step">
            <div class="step-circle">2</div>
            <div class="step-label">Vérification</div>
          </div>
          <div class="step">
            <div class="step-circle">3</div>
            <div class="step-label">Nouveau mot de passe</div>
          </div>
        </div>

        <div class="info-box">
          <i class="bi bi-info-circle"></i>
          <p><strong>Important :</strong> Assurez-vous d'utiliser l'adresse email associée à votre compte Kernel.</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger">
          <i class="bi bi-x-circle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="success-message show">
          <div style="display: flex; align-items: start;">
            <i class="bi bi-check-circle-fill"></i>
            <div>
              <h5 style="font-size: 16px; font-weight: 600; color: #065F46; margin-bottom: 8px;">Email envoyé avec succès !</h5>
              <p style="margin: 0; font-size: 14px; color: #047857;">
                Nous avons envoyé un code de vérification à votre adresse email. Veuillez vérifier votre boîte de réception (et vos spams).
              </p>
              <p style="margin-top: 10px; margin-bottom: 0; font-size: 13px; color: #059669;">
                <strong>Le code expirera dans 30 minutes.</strong>
              </p>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-3">
            <label class="form-label">Adresse Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" class="form-control" placeholder="votre@email.com" required>
            </div>
            <small class="text-muted">Entrez l'email que vous avez utilisé lors de l'inscription</small>
          </div>

          <button type="submit" class="btn-reset">
            <i class="bi bi-send me-2"></i> Envoyer le code de vérification
          </button>
        </form>

        <div class="back-link">
          <a href="connexion.php">
            <i class="bi bi-arrow-left"></i> Retour à la connexion
          </a>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: var(--light-bg); border-radius: 10px;">
          <h5 style="font-size: 14px; font-weight: 600; color: var(--dark-color); margin-bottom: 10px;">
            <i class="bi bi-question-circle me-2"></i>Besoin d'aide ?
          </h5>
          <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #6B7280;">
            <li>Vérifiez que l'email est correctement orthographié</li>
            <li>Consultez votre dossier spam/courrier indésirable</li>
            <li>Le code est valable pendant 30 minutes</li>
            <li>Contactez le support : <a href="mailto:support@kernel.tn" style="color: var(--primary-color);">support@kernel.tn</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</body>
</html>