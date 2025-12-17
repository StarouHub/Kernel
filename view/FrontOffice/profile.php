<?php
session_start();
require_once '../../config.php';
require_once '../../controller/userController.php';

$controller = new userController();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->setPrenom(trim($_POST['prenom']));
    $user->setNom(trim($_POST['nom']));
    $user->setEmail(trim($_POST['email']));
    $user->setTelephone(trim($_POST['telephone']));

    if (!empty($_POST['password'])) {
        $user->setMdp($_POST['password']);
    }

    if ($controller->updateUser($user)) {
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
  <title>Mon Profil</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0d0d1e;
      --card: #1a1a2e;
      --input: #252540;
      --pink: #ff2e63;
      --purple-start: #8b5cf6;
      --purple-end: #ec4899;
      --text: #e0e0e0;
      --muted: #aaaaaa;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      padding: 30px 15px;
    }

    .container { max-width: 700px; margin: 0 auto; }

    .profile-card {
      background: var(--card);
      border-radius: 32px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0,0,0,0.6);
    }

    .header {
      background: linear-gradient(135deg, var(--purple-start), var(--purple-end));
      padding: 40px 30px;
      text-align: center;
      color: white;
    }

    .header h1 {
      font-size: 2.4rem;
      font-weight: 800;
      margin-bottom: 8px;
    }

    .header p {
      font-size: 1.1rem;
      opacity: 0.9;
    }

    .body {
      padding: 50px 40px;
    }

    .form-group {
      margin-bottom: 28px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 600;
      color: var(--muted);
      font-size: 1rem;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 18px 22px;
      border-radius: 16px;
      border: none;
      background: var(--input);
      color: white;
      font-size: 1.1rem;
      transition: all 0.3s;
    }

    input::placeholder {
      color: #777;
    }

    input:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 46, 99, 0.3);
    }

    .role-badge {
      display: inline-block;
      padding: 12px 32px;
      background: rgba(255, 46, 99, 0.2);
      color: var(--pink);
      border: 2px solid var(--pink);
      border-radius: 50px;
      font-weight: 700;
      font-size: 1.05rem;
      margin: 15px 0 30px;
    }

    .btn-group {
      display: flex;
      gap: 20px;
      justify-content: center;
      margin-top: 50px;
    }

    .btn {
      padding: 16px 50px;
      border: none;
      border-radius: 50px;
      font-size: 1.2rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.4s;
      text-transform: uppercase;
    }

    .btn-save {
      background: linear-gradient(135deg, #ff2e63, #ff6b9d);
      color: white;
      box-shadow: 0 10px 30px rgba(255, 46, 99, 0.5);
      min-width: 200px;
    }

    .btn-cancel {
      background: #2d2d44;
      color: #ccc;
      min-width: 160px;
    }

    .btn:hover {
      transform: translateY(-6px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.6);
    }

    .alert {
      padding: 15px;
      border-radius: 16px;
      text-align: center;
      font-weight: 600;
      margin-bottom: 30px;
    }
    .alert-success { background: rgba(34,197,94,0.2); color: #86efac; border: 1px solid #22c55e; }
    .alert-danger { background: rgba(239,68,68,0.2); color: #fca5a5; border: 1px solid #ef4444; }

    @media (max-width: 576px) {
      .body { padding: 40px 25px; }
      .btn-group { flex-direction: column; }
      .btn { width: 100%; }
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="profile-card">

      <div class="header">
        <h1>Modifier l'utilisateur</h1>
        <p>ID : #<?php echo $user->getId(); ?> • <?php echo htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()); ?></p>
      </div>

      <div class="body">

        <?php if (isset($success)): ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" id="profileForm">

          <div class="form-group">
            <label>Prénom</label>
            <input type="text" name="prenom" value="<?php echo htmlspecialchars($user->getPrenom()); ?>" required>
          </div>

          <div class="form-group">
            <label>Nom</label>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($user->getNom()); ?>" required>
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
          </div>

          <div class="form-group">
            <label>Téléphone</label>
            <input type="text" name="telephone" value="<?php echo htmlspecialchars($user->getTelephone()); ?>" placeholder="8 chiffres">
          </div>

          <div class="form-group">
            <label>Nouveau mot de passe (laisser vide pour garder l'actuel)</label>
            <input type="password" name="password" placeholder="Minimum 8 caractères">
          </div>

          <div class="text-center">
            <div class="role-badge">
              <?php echo $user->getRole() === 'admin' ? 'Administrateur' : 'Utilisateur standard'; ?>
            </div>
            <p style="color:#888; font-size:0.95rem;">Le rôle ne peut pas être modifié ici</p>
          </div>

          <div class="btn-group">
            <button type="submit" class="btn btn-save">Sauvegarder</button>
            <a href="index.php" class="btn btn-cancel">Annuler</a>
          </div>

        </form>
      </div>
    </div>
  </div>

</body>
</html>