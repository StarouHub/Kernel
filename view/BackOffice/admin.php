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
$message = '';

// Handle delete
if (isset($_GET['delete'])) {
    $controller->deleteUser((int)$_GET['delete']);
    header('Location: admin.php?success=delete');
    exit;
}

// Handle ban
if (isset($_POST['ban_user'])) {
    $userId = (int)$_POST['user_id'];
    $banDuration = (int)$_POST['ban_duration'];
    
    $banUntil = date('Y-m-d H:i:s', strtotime("+$banDuration days"));
    
    if ($controller->banUser($userId, $banUntil)) {
        header('Location: admin.php?success=ban');
        exit;
    }
}

// Handle unban
if (isset($_GET['unban'])) {
    $controller->unbanUser((int)$_GET['unban']);
    header('Location: admin.php?success=unban');
    exit;
}

$totalUsers = count($users);
$bannedCount = count(array_filter($users, fn($u) => $u->isBanned()));
$activeCount = $totalUsers - $bannedCount;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administration - Kernel</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: #f5f7fa;
      font-family: 'Inter', sans-serif;
      color: #2d3748;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      height: 100vh;
      width: 280px;
      background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
      padding: 30px 0;
      z-index: 1000;
      box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
    }

    .sidebar-logo {
      padding: 0 30px 30px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 30px;
    }

    .sidebar-logo h2 {
      color: white;
      font-size: 28px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .sidebar-logo h2 i {
      font-size: 32px;
      color: #60a5fa;
    }

    .sidebar-logo p {
      color: #94a3b8;
      font-size: 13px;
      margin-top: 5px;
      font-weight: 500;
    }

    .sidebar-menu {
      padding: 0 15px;
    }

    .menu-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 14px 20px;
      color: #cbd5e1;
      text-decoration: none;
      border-radius: 12px;
      margin-bottom: 8px;
      transition: all 0.3s ease;
      font-weight: 500;
    }

    .menu-item:hover, .menu-item.active {
      background: rgba(96, 165, 250, 0.15);
      color: #60a5fa;
      transform: translateX(5px);
    }

    .menu-item i {
      font-size: 20px;
      width: 24px;
    }

    /* Main Content */
    .main-content {
      margin-left: 280px;
      padding: 30px 40px;
      min-height: 100vh;
    }

    /* Header */
    .page-header {
      background: white;
      padding: 30px 35px;
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .page-header h1 {
      font-size: 32px;
      font-weight: 800;
      color: #1e293b;
      margin: 0;
    }

    .page-header p {
      color: #64748b;
      margin: 5px 0 0;
      font-size: 15px;
    }

    .header-actions {
      display: flex;
      gap: 12px;
    }

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: white;
      padding: 25px;
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
      display: flex;
      align-items: center;
      gap: 20px;
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
    }

    .stat-icon.primary {
      background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
      color: white;
    }

    .stat-icon.success {
      background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
      color: white;
    }

    .stat-icon.danger {
      background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
      color: white;
    }

    .stat-info h3 {
      font-size: 32px;
      font-weight: 800;
      color: #1e293b;
      margin: 0;
    }

    .stat-info p {
      color: #64748b;
      margin: 5px 0 0;
      font-size: 14px;
      font-weight: 500;
    }

    /* Search Box */
    .search-container {
      background: white;
      padding: 25px;
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
      margin-bottom: 25px;
    }

    .search-wrapper {
      position: relative;
      max-width: 500px;
    }

    .search-box {
      width: 100%;
      padding: 14px 50px 14px 20px;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 15px;
      transition: all 0.3s ease;
      background: #f8fafc;
    }

    .search-box:focus {
      outline: none;
      border-color: #60a5fa;
      background: white;
      box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.1);
    }

    .search-icon {
      position: absolute;
      right: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 18px;
    }

    /* Table */
    .table-container {
      background: white;
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }

    .table {
      margin: 0;
    }

    .table thead {
      background: #f8fafc;
      border-bottom: 2px solid #e2e8f0;
    }

    .table thead th {
      padding: 18px 20px;
      font-weight: 700;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #64748b;
      border: none;
    }

    .table tbody td {
      padding: 20px;
      vertical-align: middle;
      border-bottom: 1px solid #f1f5f9;
      font-size: 14px;
    }

    .table tbody tr {
      transition: all 0.2s ease;
    }

    .table tbody tr:hover {
      background: #f8fafc;
    }

    .table tbody tr.hidden {
      display: none;
    }

    .table tbody tr:last-child td {
      border-bottom: none;
    }

    /* Badges */
    .badge {
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 12px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .badge-admin {
      background: #fef2f2;
      color: #dc2626;
    }

    .badge-user {
      background: #eff6ff;
      color: #2563eb;
    }

    .badge-banned {
      background: #fef2f2;
      color: #dc2626;
    }

    .badge-active {
      background: #f0fdf4;
      color: #16a34a;
    }

    /* Action Buttons */
    .btn-action {
      padding: 8px 16px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 13px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
      cursor: pointer;
      margin: 2px;
    }

    .btn-edit {
      background: #fef3c7;
      color: #d97706;
    }

    .btn-edit:hover {
      background: #fde68a;
      color: #b45309;
      transform: translateY(-2px);
    }

    .btn-delete {
      background: #fee2e2;
      color: #dc2626;
    }

    .btn-delete:hover {
      background: #fecaca;
      color: #b91c1c;
      transform: translateY(-2px);
    }

    .btn-ban {
      background: #fee2e2;
      color: #dc2626;
    }

    .btn-ban:hover {
      background: #fecaca;
      color: #b91c1c;
      transform: translateY(-2px);
    }

    .btn-unban {
      background: #dcfce7;
      color: #16a34a;
    }

    .btn-unban:hover {
      background: #bbf7d0;
      color: #15803d;
      transform: translateY(-2px);
    }

    /* Success Message */
    .success-msg {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
      padding: 16px 24px;
      border-radius: 12px;
      text-align: center;
      font-weight: 600;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .no-results {
      text-align: center;
      padding: 60px;
      color: #94a3b8;
      font-size: 16px;
    }

    .no-results i {
      font-size: 48px;
      margin-bottom: 15px;
      opacity: 0.5;
    }

    /* Modal */
    .modal-content {
      border-radius: 20px;
      border: none;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
      color: white;
      border-radius: 20px 20px 0 0;
      padding: 25px 30px;
      border: none;
    }

    .modal-title {
      font-weight: 700;
      font-size: 20px;
    }

    .modal-body {
      padding: 35px;
    }

    .modal-body p {
      font-size: 15px;
      color: #64748b;
      margin-bottom: 25px;
    }

    .form-label {
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 10px;
      font-size: 14px;
    }

    .form-select {
      padding: 12px 16px;
      border: 2px solid #e2e8f0;
      border-radius: 10px;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    .form-select:focus {
      border-color: #60a5fa;
      box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.1);
      outline: none;
    }

    .btn-modal-submit {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: white;
      border: none;
      padding: 14px 28px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 15px;
      width: 100%;
      transition: all 0.3s ease;
      margin-top: 10px;
    }

    .btn-modal-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
    }

    .btn-close-white {
      filter: brightness(0) invert(1);
    }

    /* User ID Badge */
    .user-id {
      font-weight: 700;
      color: #1e293b;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 0;
        overflow: hidden;
      }

      .main-content {
        margin-left: 0;
        padding: 20px;
      }

      .stats-grid {
        grid-template-columns: 1fr;
      }

      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sidebar-logo">
    <h2><i class="bi bi-hexagon-fill"></i> Kernel</h2>
    <p>Panneau d'administration</p>
  </div>
  
  <div class="sidebar-menu">
    <a href="admin.php" class="menu-item active">
      <i class="bi bi-people-fill"></i>
      <span>Utilisateurs</span>
    </a>
    <a href="../frontoffice/home.php" class="menu-item">
      <i class="bi bi-house-fill"></i>
      <span>Accueil</span>
    </a>
    <a href="../frontoffice/profile.php" class="menu-item">
      <i class="bi bi-person-fill"></i>
      <span>Mon Profil</span>
    </a>
    <a href="../frontoffice/logout.php" class="menu-item">
      <i class="bi bi-box-arrow-right"></i>
      <span>Déconnexion</span>
    </a>
  </div>
</div>

<!-- Main Content -->
<div class="main-content">
  
  <!-- Header -->
  <div class="page-header">
    <div>
      <h1>Gestion des Utilisateurs</h1>
      <p>Gérez et surveillez tous les utilisateurs de la plateforme</p>
    </div>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div class="success-msg">
      <i class="bi bi-check-circle-fill"></i>
      <?php 
        if ($_GET['success'] === 'delete') echo 'Utilisateur supprimé avec succès !';
        elseif ($_GET['success'] === 'ban') echo 'Utilisateur banni avec succès !';
        elseif ($_GET['success'] === 'unban') echo 'Utilisateur débanni avec succès !';
      ?>
    </div>
  <?php endif; ?>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon primary">
        <i class="bi bi-people-fill"></i>
      </div>
      <div class="stat-info">
        <h3><?php echo $totalUsers; ?></h3>
        <p>Total Utilisateurs</p>
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon success">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <div class="stat-info">
        <h3><?php echo $activeCount; ?></h3>
        <p>Utilisateurs Actifs</p>
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon danger">
        <i class="bi bi-ban"></i>
      </div>
      <div class="stat-info">
        <h3><?php echo $bannedCount; ?></h3>
        <p>Utilisateurs Bannis</p>
      </div>
    </div>
  </div>

  <!-- Search -->
  <div class="search-container">
    <div class="search-wrapper">
      <input type="text" id="searchInput" class="search-box" placeholder="Rechercher par nom, prénom ou email..." autocomplete="off">
      <i class="bi bi-search search-icon"></i>
    </div>
  </div>

  <!-- Table -->
  <div class="table-container">
    <table class="table" id="usersTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Utilisateur</th>
          <th>Email</th>
          <th>Téléphone</th>
          <th>Rôle</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><span class="user-id">#<?php echo $u->getId(); ?></span></td>
            <td data-search="<?php echo strtolower($u->getPrenom() . ' ' . $u->getNom() . ' ' . $u->getEmail()); ?>">
              <strong><?php echo htmlspecialchars($u->getPrenom() . ' ' . $u->getNom()); ?></strong>
            </td>
            <td><?php echo htmlspecialchars($u->getEmail()); ?></td>
            <td><?php echo htmlspecialchars($u->getTelephone() ?: '-'); ?></td>
            <td>
              <span class="badge <?php echo $u->getRole() === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                <i class="bi bi-<?php echo $u->getRole() === 'admin' ? 'shield-fill' : 'person-fill'; ?>"></i>
                <?php echo $u->getRole() === 'admin' ? 'Admin' : 'User'; ?>
              </span>
            </td>
            <td>
              <?php if ($u->isBanned()): ?>
                <span class="badge badge-banned">
                  <i class="bi bi-ban"></i>
                  Banni jusqu'au <?php echo date('d/m/Y', strtotime($u->getBannedUntil())); ?>
                </span>
              <?php else: ?>
                <span class="badge badge-active">
                  <i class="bi bi-check-circle-fill"></i>
                  Actif
                </span>
              <?php endif; ?>
            </td>
            <td>
              <a href="modify.php?id=<?php echo $u->getId(); ?>" class="btn-action btn-edit">
                <i class="bi bi-pencil-fill"></i> Modifier
              </a>
              
              <?php if ($u->isBanned()): ?>
                <a href="?unban=<?php echo $u->getId(); ?>" class="btn-action btn-unban"
                   onclick="return confirm('Débannir cet utilisateur ?')">
                  <i class="bi bi-check-circle-fill"></i> Débannir
                </a>
              <?php else: ?>
                <button class="btn-action btn-ban" 
                        onclick="openBanModal(<?php echo $u->getId(); ?>, '<?php echo htmlspecialchars($u->getPrenom() . ' ' . $u->getNom()); ?>')">
                  <i class="bi bi-ban"></i> Bannir
                </button>
              <?php endif; ?>
              
              <a href="?delete=<?php echo $u->getId(); ?>" class="btn-action btn-delete"
                 onclick="return confirm('Supprimer cet utilisateur définitivement ?')">
                <i class="bi bi-trash-fill"></i> Supprimer
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div id="noResults" class="no-results" style="display:none;">
      <i class="bi bi-search"></i>
      <p>Aucun utilisateur trouvé pour cette recherche</p>
    </div>
  </div>

</div>

<!-- Ban Modal -->
<div class="modal fade" id="banModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-ban me-2"></i>Bannir l'utilisateur
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Vous êtes sur le point de bannir <strong id="userName"></strong>. Choisissez la durée du bannissement :</p>
        <form method="POST" id="banForm">
          <input type="hidden" name="user_id" id="banUserId">
          <div class="mb-3">
            <label class="form-label">Durée du bannissement</label>
            <select class="form-select" name="ban_duration" required>
              <option value="">Sélectionner une durée</option>
              <option value="1">1 jour</option>
              <option value="3">3 jours</option>
              <option value="7">7 jours (1 semaine)</option>
              <option value="14">14 jours (2 semaines)</option>
              <option value="30">30 jours (1 mois)</option>
              <option value="90">90 jours (3 mois)</option>
              <option value="180">180 jours (6 mois)</option>
              <option value="365">365 jours (1 an)</option>
            </select>
          </div>
          <button type="submit" name="ban_user" class="btn-modal-submit">
            <i class="bi bi-ban me-2"></i>Confirmer le bannissement
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
  const filter = this.value.toLowerCase().trim();
  const rows = document.querySelectorAll('#usersTable tbody tr');
  let visibleCount = 0;

  rows.forEach(row => {
    const searchText = row.querySelector('td[data-search]')?.getAttribute('data-search') || '';

    if (searchText.includes(filter)) {
      row.classList.remove('hidden');
      visibleCount++;
    } else {
      row.classList.add('hidden');
    }
  });

  document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
});

// Ban modal functionality
let banModal;
document.addEventListener('DOMContentLoaded', function() {
  banModal = new bootstrap.Modal(document.getElementById('banModal'));
});

function openBanModal(userId, userName) {
  document.getElementById('banUserId').value = userId;
  document.getElementById('userName').textContent = userName;
  banModal.show();
}
</script>

</body>
</html>