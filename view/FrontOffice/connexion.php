<?php
session_start();
require_once '../../config.php';
  
require_once 'C:/xampp/htdocs/projetweb/Kernel/controller/userController.php';


$controller = new userController();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $user = $controller->getUserByEmail($email);

    if ($user && $password === $user->getMdp()) {     // ← CHANGED HERE
        $_SESSION['user'] = [
            'id'     => $user->getId(),
            'nom'    => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email'  => $user->getEmail(),
            'role'   => $user->getRole()
        ];
        header('Location: home.php');
        exit;
    } else {
        $error = "Email ou mot de passe incorrect";
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
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 250">
            <circle cx="150" cy="100" r="60" fill="#60A5FA" opacity="0.3"/>
            <circle cx="150" cy="100" r="40" fill="#60A5FA" opacity="0.5"/>
            <circle cx="150" cy="100" r="20" fill="#FFFFFF"/>
            <rect x="130" y="160" width="40" height="60" fill="#FFFFFF" rx="5"/>
            <circle cx="100" cy="200" r="15" fill="#F59E0B"/>
            <circle cx="200" cy="200" r="15" fill="#F59E0B"/>
            <line x1="100" y1="200" x2="150" y2="160" stroke="#FFFFFF" stroke-width="3"/>
            <line x1="200" y1="200" x2="150" y2="160" stroke="#FFFFFF" stroke-width="3"/>
          </svg>
        </div>
      </div>

      <div class="col-lg-7 login-right">
        <div class="d-lg-none logo">
          <i class="bi bi-hexagon-fill"></i> Kernel
        </div>

        <h3 class="login-title">Connexion</h3>
        <p class="login-subtitle">Connectez-vous pour accéder à votre espace</p>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" id="loginForm" novalidate>
          <div class="mb-3">
            <label class="form-label">Adresse Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="text" name="email" class="form-control" placeholder="votre@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label">Mot de passe</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" name="password" class="form-control" placeholder="••••••••" id="password">
              <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                <i class="bi bi-eye" id="toggleIcon"></i>
              </span>
            </div>
          </div>

          <div class="text-end mb-4">
            <a href="mdpo1.php" class="forgot-password">Mot de passe oublié ?</a>
          </div>

          <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
          </button>
        </form>
        
        <div class="register-link">
          Pas encore de compte ? <a href="inscriptiom.php">Créer un compte</a>
        </div>

        <div class="text-center mt-4">
          <a href="home.html" class="btn btn-outline-light">Accéder à l'accueil (mode test)</a>
        </div>
      </div>
    </div>
  </div>

  <script src="connexion.js"></script>
</body>
</html>