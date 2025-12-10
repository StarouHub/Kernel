<?php
session_start();
require_once '../../config.php';
require_once '../../controller/userController.php';

$controller = new userController();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User(
        trim($_POST['nom']),
        trim($_POST['prenom']),
        trim($_POST['email']),
        trim($_POST['telephone']),
        trim($_POST['password']),
        'user'
    );

    if ($controller->addUser($user)) {
        $message = '<div class="alert alert-success text-center">Inscription réussie ! Vous pouvez vous connecter.</div>';
    } else {
        $message = '<div class="alert alert-danger text-center">Erreur lors de l\'inscription.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - Kernel</title>

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

  <link rel="stylesheet" href="inscription.css">
</head>
<body>

  <div class="register-container">
    <div class="logo">
      <i class="bi bi-hexagon-fill"></i> Kernel
    </div>

    <h3 class="register-title">Créer un compte</h3>
    <p class="register-subtitle">Rejoignez la communauté des innovateurs</p>

    <?php echo $message; ?>

    <form method="POST" id="registerForm" novalidate>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Prénom</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="prenom" class="form-control" placeholder="Jean" required>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Nom</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="nom" class="form-control" placeholder="Dupont" required>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Adresse Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="text" name="email" class="form-control" placeholder="votre@email.com" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-telephone"></i></span>
          <input type="text" name="telephone" class="form-control" placeholder="+216 XX XXX XXX" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" class="form-control" placeholder="••••••••" id="password" oninput="checkPasswordStrength()">
          <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('password', 'toggleIcon1')">
            <i class="bi bi-eye" id="toggleIcon1"></i>
          </span>
        </div>
        <div class="password-strength">
          <div class="password-strength-bar" id="strengthBar"></div>
        </div>
        <div class="password-hint" id="strengthText">Le mot de passe doit contenir au moins 8 caractères</div>
      </div>

      <div class="mb-4">
        <label class="form-label">Confirmer le mot de passe</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="confirmPassword" class="form-control" placeholder="••••••••" id="confirmPassword">
          <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('confirmPassword', 'toggleIcon2')">
            <i class="bi bi-eye" id="toggleIcon2"></i>
          </span>
        </div>
      </div>

      <button type="submit" class="btn-register">
        <i class="bi bi-person-plus me-2"></i> Créer mon compte
      </button>
    </form>

    <div class="login-link">
      Vous avez déjà un compte ? <a href="connexion.php">Se connecter</a>
    </div>

    <div class="text-center mt-4">
      <a href="home.html" class="btn btn-outline-light">Accéder à l'accueil (mode test)</a>
    </div>
  </div>

  <script src="inscription.js"></script>
</body>
</html>