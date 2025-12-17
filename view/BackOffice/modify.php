<?php
session_start();
require_once '../../config.php';
require_once '../../controller/userController.php';

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
        $user->setMdp($_POST['password']);
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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0f0f1e;
      --card: #1a1a2e;
      --text: #e0e0e0;
      --text-muted: #aaa;
      --pink: #ff2e63;
      --purple: #764ba2;
      --border: rgba(255, 46, 99, 0.2);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      padding: 30px 15px;
      background-image: radial-gradient(circle at 10% 20%, rgba(255, 46, 99, 0.1) 0%, transparent 20%),
                        radial-gradient(circle at 90% 80%, rgba(118, 75, 162, 0.15) 0%, transparent 20%);
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
    }

    .card {
      background: var(--card);
      border-radius: 28px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
      border: 1px solid var(--border);
    }

    .header {
      background: linear-gradient(135deg, #667eea, #764ba2);
      padding: 40px;
      text-align: center;
      color: white;
    }

    .header h1 {
      font-size: 2.8rem;
      font-weight: 800;
      margin-bottom: 10px;
      text-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }

    .header p {
      font-size: 1.3rem;
      opacity: 0.9;
    }

    .body {
      padding: 50px;
    }

    .form-group {
      margin-bottom: 28px 0;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 600;
      color: var(--text);
      font-size: 1.1rem;
    }

    input[type="text"],
    input[type="password"],
    input[type="email"],
    select {
      width: 100%;
      padding: 18px 22px;
      border-radius: 16px;
      border: 2px solid #33334d;
      background: #16213e;
      color: white;
      font-size: 1.1rem;
      transition: all 0.3s;
    }

    input:focus, select:focus {
      outline: none;
      border-color: var(--pink);
      box-shadow: 0 0 0 4px rgba(255, 46, 99, 0.2);
    }

    .role-badge {
      display: inline-block;
      padding: 12px 28px;
      background: rgba(255, 46, 99, 0.2);
      color: var(--pink);
      border: 1px solid var(--pink);
      border-radius: 50px;
      font-weight: 600;
      font-size: 1.1rem;
    }

    .btn-group {
      text-align: center;
      margin-top: 50px;
      display: flex;
      gap: 20px;
      justify-content: center;
    }

    .btn {
      padding: 16px 40px;
      border: none;
      border-radius: 50px;
      font-size: 1.2rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-save {
      background: linear-gradient(135deg, #ff2e63, #ff6b9d);
      color: white;
      box-shadow: 0 10px 30px rgba(255, 46, 99, 0.4);
    }

    .btn-cancel {
      background: #33334d;
      color: #ccc;
    }

    .btn:hover {
      transform: translateY(-6px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.5);
    }

    .error-msg {
      color: #ff6b6b;
      font-size: 0.95rem;
      margin-top: 8px;
      display: none;
    }

    @media (max-width: 768px) {
      .body { padding: 30px 25px; }
      .header h1 { font-size: 2.2rem; }
      .btn-group { flex-direction: column; }
      .btn { width: 100%; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="header">
        <h1>Modifier l'utilisateur</h1>
        <p>ID : #<?php echo $user->getId(); ?> • <?php echo htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()); ?></p>
      </div>

      </div>

      <div class="body">
        <form method="POST" id="modifyForm">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" value="<?php echo htmlspecialchars($user->getPrenom()); ?>" required>
                <div class="error-msg" id="prenomError">Le prénom est obligatoire</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($user->getNom()); ?>" required>
                <div class="error-msg" id="nomError">Le nom est obligatoire</div>
              </div>
            </div>

          <div class="form-group mt-4">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
            <div class="error-msg" id="emailError">Email invalide</div>
          </div>

          <div class="form-group mt-4">
            <label>Téléphone</label>
            <input type="text" name="telephone" value="<?php echo htmlspecialchars($user->getTelephone()); ?>" placeholder="Ex: 22112233">
            <div class="error-msg" id="telephoneError">8 chiffres requis</div>
          </div>

          <div class="form-group mt-4">
            <label>Nouveau mot de passe (laisser vide pour garder l'actuel)</label>
            <input type="password" name="password" placeholder="Minimum 8 caractères">
            <div class="error-msg" id="passwordError">Minimum 8 caractères</div>
          </div>

          <div class="form-group mt-4">
            <label>Rôle actuel</label><br>
            <span class="role-badge">
              <?php echo $user->getRole() === 'admin' ? 'Administrateur' : 'Utilisateur standard'; ?>
            </span>
          </div>

          <div class="form-group mt-4">
            <label>Changer le rôle</label>
            <select name="role" class="form-control">
              <option value="user" <?php echo $user->getRole() === 'user' ? 'selected' : ''; ?>>Utilisateur</option>
              <option value="admin" <?php echo $user->getRole() === 'admin' ? 'selected' : ''; ?>>Administrateur</option>
            </select>
          </div>

          <div class="btn-group">
            <button type="submit" class="btn btn-save">Sauvegarder</button>
            <a href="admin.php" class="btn btn-cancel">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('modifyForm').addEventListener('submit', function(e) {
      let error = false;

      document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');

      const prenom = document.querySelector('[name="prenom"]').value.trim();
      if (!prenom) { document.getElementById('prenomError').style.display = 'block'; error = true; }

      const nom = document.querySelector('[name="nom"]').value.trim();
      if (!nom) { document.getElementById('nomError').style.display = 'block'; error = true; }

      const email = document.querySelector('[name="email"]').value.trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) { document.getElementById('emailError').style.display = 'block'; error = true; }

      const tel = document.querySelector('[name="telephone"]').value.trim();
      if (tel && !/^\d{8}$/.test(tel)) { document.getElementById('telephoneError').style.display = 'block'; error = true; }

      const pwd = document.querySelector('[name="password"]').value;
      if (pwd && pwd.length < 8) { document.getElementById('passwordError').style.display = 'block'; error = true; }

      if (error) e.preventDefault();
    });
  </script>
</body>
</html>