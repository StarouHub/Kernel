<?php
session_start();
require_once '../../config.php';
require_once '../../controller/userController.php';

$controller = new userController();

// Protection : doit être connecté
if (!isset($_SESSION['user'])) {
    header('Location: connexion.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$user = $controller->getUserById($userId);

if (!$user) {
    session_destroy();
    header('Location: connexion.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->setPrenom(trim($_POST['prenom']));
    $user->setNom(trim($_POST['nom']));
    $user->setEmail(trim($_POST['email']));
    $user->setTelephone(trim($_POST['telephone']));

    if (!empty($_POST['password'])) {
        $user->setMdp($_POST['password']); // en clair, comme demandé
    }

    if ($controller->updateUser($user)) {
        // Mise à jour de la session
        $_SESSION['user']['prenom'] = $user->getPrenom();
        $_SESSION['user']['nom'] = $user->getNom();
        $_SESSION['user']['email'] = $user->getEmail();
        $success = "Profil mis à jour avec succès !";
    } else {
        $error = "Erreur lors de la mise à jour.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mon Profil - Kernel</title>
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
    .modify-header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 50px 40px; text-align: center; }
    .modify-header h1 { margin: 0; font-size: 3rem; font-weight: 900; }
    .modify-header p { margin: 15px 0 0; font-size: 1.4rem; opacity: 0.95; }
    .modify-body { padding: 60px 50px; }
    .form-label { font-weight: 600; color: #444; margin-bottom: 10px; }
    .form-control {
      height: 56px; border-radius: 12px; border: 2px solid #e0e0e0; padding: 0 20px; font-size: 1.1rem;
      transition: all 0.3s;
    }
    .form-control:focus {
      border-color: #667eea; box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }
    .error-msg { color: #dc3545; font-size: 0.9rem; margin-top: 5px; display: none; }
    .current-role {
      display: inline-block; padding: 12px 30px; background: #667eea; color: white;
      border-radius: 50px; font-weight: bold; font-size: 1.2rem; margin: 20px 0;
    }
    .btn-save {
      background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 18px 60px;
      border: none; border-radius: 50px; font-weight: 700; font-size: 1.3rem; margin-top: 20px;
    }
    .btn-cancel {
      background: #6c757d; color: white; padding: 18px 60px; border: none;
      border-radius: 50px; font-weight: 700; font-size: 1.3rem; text-decoration: none; margin-left: 15px;
    }
    .btn-save:hover, .btn-cancel:hover {
      transform: translateY(-5px); box-shadow: 0 0 15px 30px rgba(0,0,0,0.3);
    }
    .alert { border-radius: 15px; padding: 20px; text-align: center; font-weight: 600; }
  </style>
</head>
<body>
  <div class="modify-wrapper">
    <div class="modify-card">
      <div class="modify-header">
        <h1>Kernel</h1>
        <p>Mon Profil</p>
      </div>

      <div class="modify-body">
        <?php if (isset($success)): ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" id="profileForm" novalidate>
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
            <div class="error-msg" id="emailError">Email invalide</div>
          </div>

          <div class="mt-4">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($user->getTelephone()); ?>">
            <div class="error-msg" id="telephoneError">Téléphone : exactement 8 chiffres</div>
          </div>

          <div class="mt-4">
            <label class="form-label">Nouveau mot de passe</label>
            <input type="password" name="password" class="form-control" placeholder="Laisser vide pour ne pas changer">
            <div class="error-msg" id="passwordError">Minimum 8 caractères</div>
          </div>

          <div class="mt-5 text-center">
            <span class="current-role">
              Rôle : <?php echo $user->getRole() === 'admin' ? 'Administrateur' : 'Utilisateur'; ?>
            </span>
            <p class="text-muted fst-italic">Le rôle ne peut pas être modifié ici</p>
          </div>

          <div class="text-center mt-5">
            <button type="submit" class="btn-save">Sauvegarder les modifications</button>
            <a href="home.php" class="btn-cancel">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('profileForm').addEventListener('submit', function(e) {
      let hasError = false;
      document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');

      const prenom = document.querySelector('[name="prenom"]').value.trim();
      if (prenom === '') {
        document.getElementById('prenomError').style.display = 'block';
        hasError = true;
      }

      const nom = document.querySelector('[name="nom"]').value.trim();
      if (nom === '') {
        document.getElementById('nomError').style.display = 'block';
        hasError = true;
      }

      const email = document.querySelector('[name="email"]').value.trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        document.getElementById('emailError').style.display = 'block';
        hasError = true;
      }

      const telephone = document.querySelector('[name="telephone"]').value.trim();
      if (!/^\d{8}$/.test(telephone)) {
        document.getElementById('telephoneError').style.display = 'block';
        hasError = true;
      }

      const password = document.querySelector('[name="password"]').value;
      if (password !== '' && password.length < 8) {
        document.getElementById('passwordError').style.display = 'block';
        hasError = true;
      }

      if (hasError) e.preventDefault();
    });
  </script>
</body>
</html>