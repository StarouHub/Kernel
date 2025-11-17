<?php
session_start();
require_once '../../config/config.php';
require_once '../../controllers/userController.php';

$controller = new userController();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$user = $controller->getUserById((int)$_GET['id']);
if (!$user) die('Utilisateur non trouvé');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->setPrenom(trim($_POST['prenom']));
    $user->setNom(trim($_POST['nom']));
    $user->setEmail(trim($_POST['email']));
    $user->setTelephone(trim($_POST['telephone']));
    $user->setRole($_POST['role']);

    if (!empty($_POST['password'])) {
        $user->setMdp($_POST['password']); // 100% texte clair
    }

    $controller->updateUser($user);
    header('Location: admin.php?success=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modifier l'utilisateur - Kernel</title>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      font-family: 'Raleway', sans-serif;
      margin: 0;
      padding: 20px 0;
    }
    .modify-wrapper { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .modify-card { background: white; border-radius: 25px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; max-width: 800px; width: 100%; }
    .modify-header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 40px; text-align: center; }
    .modify-header h1 { margin: 0; font-size: 2.5rem; font-weight: 900; }
    .modify-header p { margin: 10px 0 0; opacity: 0.9; font-size: 1.2rem; }
    .modify-body { padding: 50px; }
    .form-label { font-weight: 600; color: #444; margin-bottom: 8px; }
    .form-control, .form-select {
      height: 56px; border-radius: 12px; border: 2px solid #e0e0e0; padding: 0 20px; font-size: 1.1rem;
      transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
      border-color: #667eea; box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }
    .error-msg { color: #dc3545; font-size: 0.9rem; margin-top: 5px; display: none; }
    .btn-save {
      background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 16px 50px;
      border: none; border-radius: 50px; font-weight: 700; font-size: 1.2rem;
    }
    .btn-cancel {
      background: #6c757d; color: white; padding: 16px 50px; border: none;
      border-radius: 50px; font-weight: 700; font-size: 1.2rem; text-decoration: none;
    }
    .btn-save:hover, .btn-cancel:hover {
      transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.3);
    }
    .current-role {
      display: inline-block; padding: 10px 25px; background: #667eea; color: white;
      border-radius: 50px; font-weight: bold; font-size: 1.1rem;
    }
  </style>
</head>
<body>
  <div class="modify-wrapper">
    <div class="modify-card">
      <div class="modify-header">
        <h1>Kernel</h1>
        <p>Modifier l'utilisateur #<?php echo $user->getId(); ?></p>
      </div>

      <div class="modify-body">
        <form method="POST" id="modifyForm" novalidate>
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label">Prénom</label>
              <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($user->getPrenom()); ?>">
              <div class="error-msg" id="prenomError">Le prénom est obligatoire</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nom</label>
              <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($user->getNom()); ?>">
              <div class="error-msg" id="nomError">Le nom est obligatoire</div>
            </div>
          </div>

          <div class="mt-4">
            <label class="form-label">Email</label>
            <input type="text" name="email" class="form-control" value="<?php echo htmlspecialchars($user->getEmail()); ?>">
            <div class="error-msg" id="emailError">Veuillez entrer un email valide</div>
          </div>

          <div class="mt-4">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($user->getTelephone()); ?>">
            <div class="error-msg" id="telephoneError">Le téléphone doit contenir exactement 8 chiffres</div>
          </div>

          <div class="mt-4">
            <label class="form-label">Nouveau mot de passe</label>
            <input type="password" name="password" class="form-control" placeholder="Laisser vide pour ne pas changer">
            <div class="error-msg" id="passwordError">Le mot de passe doit contenir au moins 8 caractères</div>
          </div>

          <div class="mt-4">
            <label class="form-label">Rôle actuel</label><br>
            <span class="current-role">
              <?php echo $user->getRole() === 'admin' ? 'Administrateur' : 'Utilisateur'; ?>
            </span>
          </div>

          <div class="mt-4">
            <label class="form-label">Changer le rôle</label>
            <select name="role" class="form-select">
              <option value="user" <?php echo $user->getRole() === 'user' ? 'selected' : ''; ?>>Utilisateur</option>
              <option value="admin" <?php echo $user->getRole() === 'admin' ? 'selected' : ''; ?>>Administrateur</option>
            </select>
          </div>

          <div class="text-center mt-5">
            <button type="submit" class="btn-save">Sauvegarder</button>
            <a href="admin.php" class="btn-cancel ms-3">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JavaScript intégré directement dans le fichier -->
  <script>
    document.getElementById('modifyForm').addEventListener('submit', function(e) {
      let hasError = false;

      // Réinitialiser les messages d'erreur
      document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');

      // Prénom
      const prenom = document.querySelector('[name="prenom"]').value.trim();
      if (prenom === '') {
        document.getElementById('prenomError').style.display = 'block';
        hasError = true;
      }

      // Nom
      const nom = document.querySelector('[name="nom"]').value.trim();
      if (nom === '') {
        document.getElementById('nomError').style.display = 'block';
        hasError = true;
      }

      // Email
      const email = document.querySelector('[name="email"]').value.trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (email === '' || !emailRegex.test(email)) {
        document.getElementById('emailError').style.display = 'block';
        hasError = true;
      }

      // Téléphone : exactement 8 chiffres
      const telephone = document.querySelector('[name="telephone"]').value.trim();
      if (!/^\d{8}$/.test(telephone)) {
        document.getElementById('telephoneError').style.display = 'block';
        hasError = true;
      }

      // Mot de passe (seulement si rempli)
      const password = document.querySelector('[name="password"]').value;
      if (password !== '' && password.length < 8) {
        document.getElementById('passwordError').style.display = 'block';
        hasError = true;
      }

      if (hasError) {
        e.preventDefault();
      }
    });
  </script>
</body>
</html>