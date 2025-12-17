<?php
<<<<<<< HEAD
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

=======
include_once(__DIR__ . '/../components/main-navigation.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Mon Profil - Kernel</title>
    
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563EB;
            --secondary-color: #7C3AED;
            --accent-color: #F59E0B;
            --dark-color: #1F2937;
            --light-bg: #F9FAFB;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: var(--light-bg);
            padding-top: 80px;
            padding-bottom: 50px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin: 0 auto 20px;
        }
        
        .profile-name {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .profile-role {
            font-size: 18px;
            opacity: 0.9;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            display: block;
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            padding: 15px 0;
            border-bottom: 1px solid #F3F4F6;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .btn-edit {
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-edit:hover {
            background: #1D4ED8;
            color: white;
            transform: translateY(-2px);
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .quick-link {
            background: #F3F4F6;
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s;
        }
        
        .quick-link:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .quick-link i {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--accent-color);
        }
        
        .quick-link:hover i {
            color: var(--primary-color);
        }
    </style>
</head>

<body>
    <?php echo renderMainNavigation('profil'); ?>
    <?php echo renderChatbotWidget(); ?>
    
    <div class="profile-header">
        <div class="container">
            <div class="profile-avatar">
                <i class="bi bi-person"></i>
            </div>
            <h1 class="profile-name">John Doe</h1>
            <p class="profile-role">Innovateur & Entrepreneur</p>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-number">5</span>
                    <span class="stat-label">Projets créés</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">12</span>
                    <span class="stat-label">Actualités publiées</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">3</span>
                    <span class="stat-label">Projets financés</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">150</span>
                    <span class="stat-label">Points Kernel</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="card-title">
                            <i class="bi bi-person-lines-fill"></i> Informations Personnelles
                        </h3>
                        <a href="#" class="btn-edit">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Nom complet</div>
                            <div class="info-value">John Doe</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">john.doe@kernel.tn</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Téléphone</div>
                            <div class="info-value">+216 XX XXX XXX</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Localisation</div>
                            <div class="info-value">Tunis, Tunisie</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Spécialité</div>
                            <div class="info-value">Intelligence Artificielle</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Membre depuis</div>
                            <div class="info-value">Janvier 2024</div>
                        </div>
                    </div>
                </div>
                
                <div class="profile-card">
                    <h3 class="card-title">
                        <i class="bi bi-file-text"></i> À propos
                    </h3>
                    <p class="text-muted">
                        Passionné par l'innovation technologique et l'entrepreneuriat, je développe des solutions IA 
                        pour résoudre les défis du quotidien. Toujours à la recherche de nouvelles opportunités 
                        de collaboration et d'investissement.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="profile-card">
                    <h3 class="card-title">
                        <i class="bi bi-lightning"></i> Actions Rapides
                    </h3>
                    <div class="quick-links">
                        <a href="mes-taches.php" class="quick-link">
                            <i class="bi bi-list-task"></i>
                            <div>Mes Tâches</div>
                        </a>
                        <a href="ajoutprojet.php" class="quick-link">
                            <i class="bi bi-plus-circle"></i>
                            <div>Nouveau Projet</div>
                        </a>
                        <a href="ajouterActualite.php" class="quick-link">
                            <i class="bi bi-newspaper"></i>
                            <div>Publier Actualité</div>
                        </a>
                        <a href="#" class="quick-link">
                            <i class="bi bi-gear"></i>
                            <div>Paramètres</div>
                        </a>
                    </div>
                </div>
                
                <div class="profile-card">
                    <h3 class="card-title">
                        <i class="bi bi-trophy"></i> Badges & Réalisations
                    </h3>
                    <div class="text-center py-4">
                        <i class="bi bi-award" style="font-size: 48px; color: var(--accent-color);"></i>
                        <h5 class="mt-3">Innovateur Confirmé</h5>
                        <p class="text-muted">5+ projets créés avec succès</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
>>>>>>> origin/MohamedChaouachi
</body>
</html>