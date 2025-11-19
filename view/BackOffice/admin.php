<?php
session_start();
require_once '../../config.php';
require_once '../../controller/userController.php';

$controller = new userController();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

$users = $controller->getAllUsers();

if (isset($_GET['delete'])) {
    $controller->deleteUser((int)$_GET['delete']);
    header('Location: admin.php?success=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administration - Kernel</title>
  
  <!-- MÊME POLICE ET BOOTSTRAP QUE MODIFIE.PHP -->
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- STYLE 100% IDENTIQUE À MODIFY.PHP (même couleurs, même ombres, même tout) -->
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      font-family: 'Raleway', sans-serif;
      margin: 0;
      padding: 30px 0;
    }
    .admin-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .admin-card {
      background: white;
      border-radius: 25px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      overflow: hidden;
      max-width: 1200px;
      width: 100%;
    }
    .admin-header {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      padding: 50px 40px;
      text-align: center;
    }
    .admin-header h1 {
      margin: 0;
      font-size: 3rem;
      font-weight: 900;
      letter-spacing: 1px;
    }
    .admin-header p {
      margin: 15px 0 0;
      font-size: 1.4rem;
      opacity: 0.95;
      font-weight: 500;
    }
    .admin-body {
      padding: 60px 50px;
    }
    .table {
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
      margin-bottom: 40px;
    }
    .table thead {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
    }
    .table thead th {
      font-weight: 700;
      font-size: 1.1rem;
      padding: 18px 15px;
      border: none;
    }
    .table tbody td {
      padding: 18px 15px;
      vertical-align: middle;
      font-size: 1rem;
    }
    .table tbody tr:hover {
      background-color: #f8f5ff;
    }
    .badge {
      padding: 10px 20px;
      border-radius: 50px;
      font-weight: bold;
      font-size: 0.95rem;
    }
    .badge-admin { background: #e74c3c; }
    .badge-user { background: #3498db; }

    /* BOUTONS IDENTIQUES À MODIFY.PHP */
    .btn-action {
      padding: 12px 24px;
      border: none;
      border-radius: 50px;
      font-weight: 700;
      font-size: 1rem;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
      min-width: 110px;
      text-align: center;
    }
    .btn-edit {
      background: linear-gradient(135deg, #f39c12, #e67e22);
      color: white;
    }
    .btn-delete {
      background: linear-gradient(135deg, #e74c3c, #c0392b);
      color: white;
    }
    .btn-edit:hover, .btn-delete:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.3);
      color: white;
    }

    .btn-back {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
      padding: 18px 60px;
      border: none;
      border-radius: 50px;
      font-weight: 700;
      font-size: 1.3rem;
      text-decoration: none;
      display: block;
      width: fit-content;
      margin: 20px auto 0;
      transition: all 0.4s ease;
    }
    .btn-back:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 40px rgba(40, 167, 69, 0.4);
      color: white;
    }

    .success-msg {
      background: #d4edda;
      color: #155724;
      padding: 15px;
      border-radius: 12px;
      text-align: center;
      font-weight: 600;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>

  <div class="admin-wrapper">
    <div class="admin-card">
      
      <!-- HEADER IDENTIQUE À MODIFY.PHP -->
      <div class="admin-header">
        <h1>Kernel</h1>
        <p>Gestion des utilisateurs</p>
      </div>

      <div class="admin-body">

        <!-- Message de succès -->
        <?php if (isset($_GET['success'])): ?>
          <div class="success-msg">
            Utilisateur supprimé avec succès !
          </div>
        <?php endif; ?>

        <!-- Tableau magnifique -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Rôle</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
                <tr>
                  <td><strong>#<?php echo $u->getId(); ?></strong></td>
                  <td><?php echo htmlspecialchars($u->getPrenom()); ?></td>
                  <td><?php echo htmlspecialchars($u->getNom()); ?></td>
                  <td><?php echo htmlspecialchars($u->getEmail()); ?></td>
                  <td><?php echo htmlspecialchars($u->getTelephone()); ?></td>
                  <td>
                    <span class="badge <?php echo $u->getRole() === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                      <?php echo $u->getRole() === 'admin' ? 'Administrateur' : 'Utilisateur'; ?>
                    </span>
                  </td>
                  <td>
                    <a href="modify.php?id=<?php echo $u->getId(); ?>" class="btn-action btn-edit">
                      Modifier
                    </a>
                    <a href="?delete=<?php echo $u->getId(); ?>" class="btn-action btn-delete ms-2"
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                      Supprimer
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Bouton retour magnifique -->
        <a href="../frontoffice/home.php" class="btn-back">
          Retour à l'accueil
        </a>
      </div>
    </div>
  </div>

</body>
</html>