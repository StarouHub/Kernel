<?php
session_start();
require_once '../../config/config.php';
require_once '../../controllers/userController.php';

$controller = new userController();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

$users = $controller->getAllUsers();

if (isset($_GET['delete'])) {
    $controller->deleteUser((int)$_GET['delete']);
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administration - Kernel</title>

  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- This CSS makes backoffice look EXACTLY like frontoffice -->
  <link rel="stylesheet" href="../frontoffice/css/backoffice.css">
</head>
<body>
  <div class="back-container">
    <div class="back-card">
      <div class="back-header">
        <div class="logo"><i class="bi bi-hexagon-fill"></i> Kernel</div>
        <h1 class="back-title">Gestion des utilisateurs</h1>
      </div>

      <div class="back-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
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
                  <td><?php echo $u->getId(); ?></td>
                  <td><?php echo htmlspecialchars($u->getPrenom()); ?></td>
                  <td><?php echo htmlspecialchars($u->getNom()); ?></td>
                  <td><?php echo htmlspecialchars($u->getEmail()); ?></td>
                  <td><?php echo htmlspecialchars($u->getTelephone()); ?></td>
                  <td><span class="badge bg-<?php echo $u->getRole() === 'admin' ? 'danger' : 'primary'; ?>">
                    <?php echo $u->getRole(); ?></span></td>
                  <td>
                    <a href="modify.php?id=<?php echo $u->getId(); ?>" class="btn btn-edit btn-sm">
                      <i class="bi bi-pencil"></i> Modifier
                    </a>
                    <a href="?delete=<?php echo $u->getId(); ?>" class="btn btn-delete btn-sm"
                       onclick="return confirm('Supprimer cet utilisateur ?')">
                      <i class="bi bi-trash"></i> Supprimer
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <a href="../frontoffice/home.php" class="btn-back">
          <i class="bi bi-arrow-left-circle"></i> Retour à l'accueil
        </a>
      </div>
    </div>
  </div>
</body>
</html>