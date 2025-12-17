<?php
session_start();
require_once '../../config.php';
require_once '../../controller/userController.php';

$controller = new userController();
$error = '';
$captcha_error = '';

$recaptcha_secret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";

// Check remember me cookie
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
    $user = $controller->validateRememberToken($_COOKIE['remember_token']);
    if ($user) {
        // Check if user is banned
        if ($user->isBanned()) {
            $banUntil = date('d/m/Y à H:i', strtotime($user->getBannedUntil()));
            $error = "Votre compte est banni jusqu'au $banUntil";
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        } else {
            $_SESSION['user'] = [
                'id'     => $user->getId(),
                'nom'    => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email'  => $user->getEmail(),
                'role'   => $user->getRole()
            ];
            header('Location: index.php');
            exit;
        }
    } else {
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Verify reCAPTCHA
    if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        $response = $_POST['g-recaptcha-response'];
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$response}");
        $captcha_success = json_decode($verify);

        if ($captcha_success->success == false) {
            $captcha_error = "Échec de la vérification reCAPTCHA. Es-tu un robot ?";
        }
    } else {
        $captcha_error = "Veuillez cocher la case reCAPTCHA.";
    }

    if (!empty($captcha_error)) {
        $error = $captcha_error;
    } else {
        // Verify credentials
        $user = $controller->getUserByEmail($email);

        if ($user && $password === $user->getMdp()) {
            // Check if user is banned
            if ($user->isBanned()) {
                $banUntil = date('d/m/Y à H:i', strtotime($user->getBannedUntil()));
                $error = "Votre compte est banni jusqu'au $banUntil. Contactez un administrateur pour plus d'informations.";
            } else {
                $_SESSION['user'] = [
                    'id'     => $user->getId(),
                    'nom'    => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email'  => $user->getEmail(),
                    'role'   => $user->getRole()
                ];

                if ($remember) {
                    $token = $controller->createRememberToken($user->getId());
                    setcookie('remember_token', $token, [
                        'expires' => time() + 30 * 24 * 3600,
                        'path' => '/',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                }

                header('Location: index.php');
                exit;
            }
        } else {
            $error = "Email ou mot de passe incorrect";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - Kernel</title>

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="connexion.css">

  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<div class="login-container">
  <div class="row g-0">
    <div class="col-lg-5 login-left">
      <div class="logo mb-4">
        <i class="bi bi-hexagon-fill"></i> Kernel
      </div>
      <h2>Bienvenue sur Kernel</h2>
      <p>Rejoignez la communauté des innovateurs et transformez vos idées en projets concrets.</p>

      <div class="login-illustration">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 250">...</svg>
      </div>
    </div>

    <div class="col-lg-7 login-right">
      <div class="d-lg-none logo"><i class="bi bi-hexagon-fill"></i> Kernel</div>

      <h3 class="login-title">Connexion</h3>
      <p class="login-subtitle">Connectez-vous pour accéder à votre espace</p>

      <?php if ($error): ?>
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" id="loginForm" novalidate>
        <div class="mb-3">
          <label class="form-label">Adresse Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" placeholder="votre@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Mot de passe</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
            <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
              <i class="bi bi-eye" id="toggleIcon"></i>
            </span>
          </div>
        </div>

        <div class="mb-3">
          <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
        </div>

        <div class="mb-4 d-flex justify-content-between align-items-center">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" <?= isset($_POST['remember']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="remember">Rester connecté</label>
          </div>
          <a href="mdpo1.php" class="forgot-password">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="btn-login">
          <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
        </button>
      </form>

      <div class="register-link">
        Pas encore de compte ? <a href="inscription.php">Créer un compte</a>
      </div>
    </div>
  </div>
</div>

<script src="connexion.js"></script>
</body>
</html>