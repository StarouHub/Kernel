<?php
session_start();
require_once '../../config.php';
require_once '../../controller/userController.php';

$controller = new userController();

// Protection admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

$users = $controller->getAllUsers();
$registrationData = $controller->getRegistrationsByMonth();

// Statistiques
$totalUsers = count($users);
$bannedCount = count(array_filter($users, fn($u) => $u->isBanned()));
$activeCount = $totalUsers - $bannedCount;
$adminCount = count(array_filter($users, fn($u) => $u->getRole() === 'admin'));
$userCount = $totalUsers - $adminCount;

// Gestion des actions
if (isset($_GET['delete'])) {
    $controller->deleteUser((int)$_GET['delete']);
    header('Location: admin.php?success=delete');
    exit;
}

if (isset($_POST['ban_user'])) {
    $userId = (int)$_POST['user_id'];
    $banDuration = (int)$_POST['ban_duration'];
    $banUntil = date('Y-m-d H:i:s', strtotime("+$banDuration days"));
    
    if ($controller->banUser($userId, $banUntil)) {
        header('Location: admin.php?success=ban');
        exit;
    }
}

if (isset($_GET['unban'])) {
    $controller->unbanUser((int)$_GET['unban']);
    header('Location: admin.php?success=unban');
    exit;
}
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
    :root {
      --primary: #60a5fa;
      --danger: #ef4444;
      --success: #10b981;
      --dark: #1e293b;
      --gray-100: #f8fafc;
      --gray-200: #e2e8f0;
      --gray-600: #64748b;
      --gray-800: #1e293b;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #f5f7fa;
      color: #2d3748;
      margin: 0;
      padding: 0;
    }

    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      width: 280px;
      height: 100vh;
      background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
      padding: 30px 0;
      box-shadow: 4px 0 24px rgba(0,0,0,0.12);
      z-index: 1000;
    }

    .sidebar-logo h2 {
      color: white;
      font-weight: 800;
      font-size: 28px;
      padding: 0 30px 30px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      margin: 0 0 30px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .sidebar-logo i { color: var(--primary); font-size: 32px; }

    .menu-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 14px 30px;
      color: #cbd5e1;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .menu-item:hover, .menu-item.active {
      background: rgba(96, 165, 250, 0.15);
      color: var(--primary);
      transform: translateX(5px);
    }

    .main-content {
      margin-left: 280px;
      padding: 30px 40px;
      min-height: 100vh;
    }

    .page-header {
      background: white;
      padding: 30px 35px;
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

    .page-header h1 {
      font-size: 32px;
      font-weight: 800;
      color: var(--dark);
      margin: 0;
    }

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
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
      display: flex;
      align-items: center;
      gap: 20px;
      transition: transform 0.3s ease;
    }

    .stat-card:hover { transform: translateY(-5px); }

    .stat-icon {
      width: 60px; height: 60px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      color: white;
    }

    .stat-icon.primary { background: linear-gradient(135deg, #60a5fa, #3b82f6); }
    .stat-icon.success { background: linear-gradient(135deg, #34d399, #10b981); }
    .stat-icon.danger { background: linear-gradient(135deg, #f87171, #ef4444); }

    .charts-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 30px;
      margin-bottom: 30px;
    }

    .chart-container {
      background: white;
      padding: 25px;
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .search-container {
      background: white;
      padding: 25px;
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
      margin-bottom: 25px;
    }

    .search-box {
      width: 100%;
      max-width: 500px;
      padding: 14px 50px 14px 20px;
      border: 2px solid var(--gray-200);
      border-radius: 12px;
      font-size: 15px;
      background: var(--gray-100);
      transition: all 0.3s;
    }

    .search-box:focus {
      outline: none;
      border-color: var(--primary);
      background: white;
      box-shadow: 0 0 0 4px rgba(96,165,250,0.1);
    }

    .table-container {
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .table thead th {
      background: var(--gray-100);
      font-weight: 700;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--gray-600);
      padding: 18px 20px;
      border: none;
      cursor: pointer;
      user-select: none;
      position: relative;
    }

    }

    .table thead th.sortable:hover {
      background: #e2e8f0;
    }

    .table thead th::after {
      content: ' ↕';
      opacity: 0.3;
      font-size: 12px;
      margin-left: 5px;
    }

    .table thead th.asc::after { content: ' ↑'; opacity: 1; font-weight: bold; }
    .table thead th.desc::after { content: ' ↓'; opacity: 1; font-weight: bold; }

    .badge {
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
    }

    .badge-admin { background: #fef2f2; color: #dc2626; }
    .badge-user { background: #eff6ff; color: #2563eb; }
    .badge-banned { background: #fee2e2; color: #dc2626; }
    .badge-active { background: #f0fdf4; color: #16a34a; }

    .btn-action {
      padding: 8px 16px;
      border: none;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      margin: 2px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
    }

    .btn-edit { background: #fef3c7; color: #d97706; }
    .btn-edit:hover { background: #fde68a; transform: translateY(-2px); }

    .btn-delete { background: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background: #fecaca; transform: translateY(-2px); }

    .btn-ban { background: #fee2e2; color: #dc2626; }
    .btn-ban:hover { background: #fecaca; transform: translateY(-2px); }

    .btn-unban { background: #dcfce7; color: #16a34a; }
    .btn-unban:hover { background: #bbf7d0; transform: translateY(-2px); }

    .success-msg {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      padding: 16px 24px;
      border-radius: 12px;
      text-align: center;
      font-weight: 600;
      margin-bottom: 25px;
      box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }

    @media (max-width: 768px) {
      .sidebar { width: 0; overflow: hidden; }
      .main-content { margin-left: 0; padding: 20px; }
      .stats-grid, .charts-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sidebar-logo">
    <h2>Kernel</h2>
    <p>Panneau d'administration</p>
  </div>
  <div class="sidebar-menu">
    <a href="admin.php" class="menu-item active">Utilisateurs</a>
    <a href="../frontoffice/index.php" class="menu-item">Accueil</a>
    <a href="../frontoffice/profile.php" class="menu-item">Mon Profil</a>
    <a href="../frontoffice/logout.php" class="menu-item">Déconnexion</a>
  </div>
</div>

<!-- Main Content -->
<div class="main-content">

  <div class="page-header">
    <div>
      <h1>Gestion des Utilisateurs</h1>
      <p>Gérez tous les utilisateurs de la plateforme en temps réel</p>
    </div>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div class="success-msg">
      <?php 
        echo match($_GET['success']) {
          'delete' => 'Utilisateur supprimé avec succès !',
          'ban' => 'Utilisateur banni avec succès !',
          'unban' => 'Utilisateur débanni avec succès !',
        };
      ?>
    </div>
  <?php endif; ?>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon primary">T.U</div>
      <div class="stat-info">
        <h3><?=$totalUsers?></h3>
        <p>Total Utilisateurs</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon success">Actif</div>
      <div class="stat-info">
        <h3><?=$activeCount?></h3>
        <p>Actifs</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon danger">Ban</div>
      <div class="stat-info">
        <h3><?=$bannedCount?></h3>
        <p>Bannis</p>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="charts-row">
    <div class="chart-container">
      <h3>Inscriptions 2025</h3>
      <canvas id="registrationChart"></canvas>
    </div>
    <div class="chart-container">
      <h3>Répartition des Rôles</h3>
      <canvas id="rolesChart"></canvas>
    </div>
  </div>

  <!-- Search -->
  <div class="search-container">
    <div style="position:relative;max-width:500px;">
      <input type="text" id="searchInput" class="search-box" placeholder="Rechercher par nom, email, téléphone...">
      <i class="bi bi-search" style="position:absolute;right:18px;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
    </div>
  </div>

  <!-- Table -->
  <div class="table-container">
    <table class="table table-hover" id="usersTable">
      <thead>
        <tr>
          <th class="sortable" data-col="id">ID</th>
          <th class="sortable" data-col="name">Utilisateur</th>
          <th class="sortable" data-col="email">Email</th>
          <th class="sortable" data-col="phone">Téléphone</th>
          <th class="sortable" data-col="role">Rôle</th>
          <th class="sortable" data-col="status">Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr data-search="<?=strtolower($u->getPrenom() . ' ' . $u->getNom() . ' ' . $u->getEmail() . ' ' . $u->getTelephone())?>">
            <td>#<?=$u->getId()?></td>
            <td><strong><?=htmlspecialchars($u->getPrenom() . ' ' . $u->getNom())?></strong></td>
            <td><?=htmlspecialchars($u->getEmail())?></td>
            <td><?=htmlspecialchars($u->getTelephone() ?: '-')?></td>
            <td>
              <span class="badge <?= $u->getRole() === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                <?= $u->getRole() === 'admin' ? 'Admin' : 'User' ?>
              </span>
            </td>
            <td>
              <?php if ($u->isBanned()): ?>
                <span class="badge badge-banned">
                  Banni jusqu'au <?=date('d/m/Y', strtotime($u->getBannedUntil()))?>
                </span>
              <?php else: ?>
                <span class="badge badge-active">Actif</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="modify.php?id=<?=$u->getId()?>" class="btn-action btn-edit">Modifier</a>

              <?php if ($u->isBanned()): ?>
                <a href="?unban=<?=$u->getId()?>" class="btn-action btn-unban"
                   onclick="return confirm('Débannir cet utilisateur ?')">Débannir</a>
              <?php else: ?>
                <button class="btn-action btn-ban" onclick="openBanModal(<?=$u->getId()?>, '<?=htmlspecialchars($u->getPrenom().' '.$u->getNom(), ENT_QUOTES)?>')">
                  Bannir
                </button>
              <?php endif; ?>

              <a href="?delete=<?=$u->getId()?>" class="btn-action btn-delete"
                 onclick="return confirm('Supprimer définitivement cet utilisateur ?')">Supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Ban Modal -->
<div class="modal fade" id="banModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">Bannir l'utilisateur</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Bannir <strong id="userName"></strong> pour :</p>
        <form method="POST" id="banForm">
          <input type="hidden" name="user_id" id="banUserId">
          <select name="ban_duration" class="form-select mb-3" required>
            <option value="">Choisir une durée</option>
            <option value="1">1 jour</option>
            <option value="3">3 jours</option>
            <option value="7">7 jours</option>
            <option value="30">30 jours</option>
            <option value="90">90 jours</option>
            <option value="365">1 an</option>
          </select>
          <button type="submit" name="ban_user" class="btn btn-danger w-100">Confirmer le bannissement</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Recherche + Tri
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const table = document.getElementById('usersTable');
  const rows = table.querySelectorAll('tbody tr');
  let sortDirection = {};
  
  // Recherche
  searchInput.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    let visible = 0;
    rows.forEach(row => {
      const text = row.getAttribute('data-search');
      if (text.includes(term)) {
        row.style.display = '';
        visible++;
      } else {
        row.style.display = 'none';
      }
    });
  });

  // Tri
  document.querySelectorAll('th.sortable').forEach(th => {
    th.addEventListener('click', function() {
      const col = this.getAttribute('data-col');
      const direction = sortDirection[col] === 'asc' ? 'desc' : 'asc';
      sortDirection = { [col]: direction };

      // Mise à jour des flèches
      document.querySelectorAll('th').forEach(h => h.classList.remove('asc', 'desc'));
      this.classList.add(direction);

      // Tri des lignes
      const rowsArray = Array.from(rows);
      rowsArray.sort((a, b) => {
        let aVal, bVal;

        switch(col) {
          case 'id':
            aVal = parseInt(a.cells[0].textContent.replace('#', ''));
            bVal = parseInt(b.cells[0].textContent.replace('#', ''));
            break;
          case 'name':
            aVal = a.cells[1].textContent;
            bVal = b.cells[1].textContent;
            break;
          case 'email':
            aVal = a.cells[2].textContent;
            bVal = b.cells[2].textContent;
            break;
          case 'phone':
            aVal = a.cells[3].textContent;
            bVal = b.cells[3].textContent;
            break;
          case 'role':
            aVal = a.cells[4].querySelector('.badge').textContent.trim();
            bVal = b.cells[4].querySelector('.badge').textContent.trim();
            break;
          case 'status':
            aVal = a.cells[5].querySelector('.badge').textContent.includes('Banni') ? 1 : 0;
            bVal = b.cells[5].querySelector('.badge').textContent.includes('Banni') ? 1 : 0;
            break;
        }

        if (aVal < bVal) return direction === 'asc' ? -1 : 1;
        if (aVal > bVal) return direction === 'asc' ? 1 : -1;
        return 0;
      });

      // Réinsertion
      rowsArray.forEach(row => table.querySelector('tbody').appendChild(row));
    });
  });

  // Modal Bannissement
  window.openBanModal = function(id, name) {
    document.getElementById('banUserId').value = id;
    document.getElementById('userName').textContent = name;
    new bootstrap.Modal(document.getElementById('banModal')).show();
  };

  // Graphiques
  const regData = <?=json_encode($registrationData)?>;
  new Chart(document.getElementById('registrationChart'), {
    type: 'line',
    data: {
      labels: regData.labels,
      datasets: [{
        label: 'Inscriptions',
        data: regData.data,
        borderColor: '#60a5fa',
        backgroundColor: 'rgba(96,165,250,0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
  });

  new Chart(document.getElementById('rolesChart'), {
    type: 'pie',
    data: {
      labels: ['Admins', 'Utilisateurs'],
      datasets: [{ data: [<?=$adminCount?>, <?=$userCount?>], backgroundColor: ['#ef4444', '#2563eb'] }]
    },
    options: { responsive: true }
  });
});
</script>

</body>
</html>