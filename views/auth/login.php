<?php
// Redirect if already logged in
if (isset($_SESSION['user_role'])) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Connexion - Choisir votre rôle';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo $pageTitle; ?></title>

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
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-container {
      max-width: 500px;
      width: 100%;
    }

    .login-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .logo {
      text-align: center;
      margin-bottom: 30px;
    }

    .logo i {
      font-size: 64px;
      color: var(--primary-color);
      margin-bottom: 15px;
    }

    .logo h1 {
      font-family: 'Raleway', sans-serif;
      font-size: 32px;
      font-weight: 700;
      color: var(--dark-color);
      margin: 0;
    }

    .logo p {
      color: #6B7280;
      margin-top: 10px;
    }

    .role-option {
      border: 2px solid #E5E7EB;
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 15px;
      cursor: pointer;
      transition: all 0.3s;
      text-decoration: none;
      display: block;
      color: inherit;
    }

    .role-option:hover {
      border-color: var(--primary-color);
      background: #F0F7FF;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
      text-decoration: none;
      color: inherit;
    }

    .role-option.active {
      border-color: var(--primary-color);
      background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(124, 58, 237, 0.1));
    }

    .role-icon {
      font-size: 48px;
      margin-bottom: 15px;
      display: block;
    }

    .role-option.admin .role-icon {
      color: var(--accent-color);
    }

    .role-option.user .role-icon {
      color: var(--primary-color);
    }

    .role-title {
      font-size: 24px;
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 10px;
    }

    .role-description {
      color: #6B7280;
      font-size: 14px;
      line-height: 1.6;
    }

    .role-badge {
      display: inline-block;
      padding: 4px 12px;
      background: var(--primary-color);
      color: white;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 600;
      margin-top: 10px;
    }

    .role-option.admin .role-badge {
      background: var(--accent-color);
    }

    .info-box {
      background: #F0F7FF;
      border-left: 4px solid var(--primary-color);
      padding: 15px;
      border-radius: 8px;
      margin-top: 20px;
      font-size: 14px;
      color: #374151;
    }

    .info-box i {
      color: var(--primary-color);
      margin-right: 8px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <div class="logo">
        <i class="bi bi-hexagon-fill"></i>
        <h1>Kernel</h1>
        <p>Choisissez votre mode de connexion</p>
      </div>

      <form method="POST" action="index.php?action=login">
        <a href="#" class="role-option admin" onclick="selectRole('admin'); return false;">
          <i class="bi bi-shield-check role-icon"></i>
          <div class="role-title">Administrateur</div>
          <div class="role-description">
            Accès complet avec possibilité de créer, modifier et supprimer des événements.
          </div>
          <span class="role-badge">Accès complet</span>
        </a>

        <a href="#" class="role-option user" onclick="selectRole('user'); return false;">
          <i class="bi bi-person role-icon"></i>
          <div class="role-title">Utilisateur</div>
          <div class="role-description">
            Accès en lecture seule. Vous pouvez consulter les événements mais ne pouvez pas les modifier.
          </div>
          <span class="role-badge">Lecture seule</span>
        </a>

        <input type="hidden" name="role" id="roleInput" value="">
        <button type="submit" id="submitBtn" class="btn btn-primary w-100" style="display: none; padding: 15px; border-radius: 10px; font-weight: 600; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border: none;">
          <i class="bi bi-box-arrow-in-right me-2"></i> Continuer
        </button>
      </form>

      <div class="info-box">
        <i class="bi bi-info-circle"></i>
        <strong>Note:</strong> Cette application utilise un système de rôles simple. Sélectionnez votre rôle pour continuer.
      </div>
    </div>
  </div>

  <script>
    function selectRole(role) {
      // Remove active class from all options
      document.querySelectorAll('.role-option').forEach(opt => {
        opt.classList.remove('active');
      });
      
      // Add active class to selected option
      event.currentTarget.classList.add('active');
      
      // Set hidden input value
      document.getElementById('roleInput').value = role;
      
      // Show submit button
      document.getElementById('submitBtn').style.display = 'block';
    }
  </script>
</body>
</html>

