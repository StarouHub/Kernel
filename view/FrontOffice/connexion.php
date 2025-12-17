<?php
session_start();
require_once '../../config.php';
require_once '../../controller/userController.php';

<<<<<<< HEAD
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
=======
$controller = new UserController();
$error = '';

// Rediriger si déjà connecté
if ($controller->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $result = $controller->login($email, $password);
        
        if ($result['success']) {
            // TODO: Implémenter remember me si nécessaire
            header('Location: index.php');
            exit();
        } else {
            $error = $result['message'];
>>>>>>> origin/MohamedChaouachi
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<<<<<<< HEAD
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
=======
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Kernel</title>
    
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 20px;
        }
        
        .login-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .logo {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }
        
        .login-right {
            padding: 60px 40px;
        }
        
        .login-title {
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .login-subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        
        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e1e5e9;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            width: 100%;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .register-link {
            text-align: center;
            margin-top: 30px;
            color: #666;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .login-left {
                display: none;
            }
            
            .login-container {
                margin: 10px;
            }
            
            .login-right {
                padding: 40px 30px;
            }
        }
    </style>
>>>>>>> origin/MohamedChaouachi
</head>
<body>

<div class="login-container">
<<<<<<< HEAD
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
=======
    <div class="row g-0">
        <div class="col-lg-5 login-left">
            <div class="logo mb-4">
                <i class="bi bi-hexagon-fill"></i> Kernel
            </div>
            <h2>Bienvenue sur Kernel</h2>
            <p>Rejoignez la communauté des innovateurs et transformez vos idées en projets concrets.</p>
        </div>

        <div class="col-lg-7 login-right">
            <div class="d-lg-none text-center mb-4">
                <div class="logo" style="color: #667eea;"><i class="bi bi-hexagon-fill"></i> Kernel</div>
            </div>

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
                        <input type="email" name="email" class="form-control" placeholder="votre@email.com" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" id="password" 
                               placeholder="••••••••" required>
                        <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                               <?= isset($_POST['remember']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="remember">Rester connecté</label>
                    </div>
                    <a href="mot-de-passe-oublie.php" class="forgot-password">Mot de passe oublié ?</a>
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

<script>
function togglePassword() {
    const password = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}

// Validation du formulaire
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const email = document.querySelector('input[name="email"]').value.trim();
    const password = document.querySelector('input[name="password"]').value;
    
    if (!email || !password) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs.');
        return false;
    }
    
    if (!email.includes('@')) {
        e.preventDefault();
        alert('Veuillez entrer une adresse email valide.');
        return false;
    }
});
</script>

>>>>>>> origin/MohamedChaouachi
</body>
</html>