<?php
session_start();
include_once(__DIR__ . '/../../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../../components/office-switch.php');

$actualiteController = new ActualiteController();

// Récupérer toutes les actualités
$actualites = $actualiteController->listActualites();

// Récupérer les messages de session
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Actualités - Backoffice Kernel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #2563EB;
            --secondary: #7C3AED;
            --accent: #F59E0B;
        }
        
        body {
            background: #F9FAFB;
            overflow-x: hidden;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            min-height: 100vh;
            color: white;
            position: fixed;
            width: 250px;
            padding: 20px;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        
        .sidebar h3 {
            font-weight: 700;
            margin-bottom: 30px;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
            width: calc(100% - 250px);
            overflow-x: hidden;
        }
        
        .page-header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            font-weight: 600;
        }
        
        .table {
            margin: 0;
        }
        
        .table thead th {
            border-bottom: 2px solid #E5E7EB;
            color: #6B7280;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            padding: 15px;
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background: #F9FAFB;
        }
        
        .badge {
            padding: 6px 12px;
            font-weight: 500;
            border-radius: 20px;
        }
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 14px;
            margin: 0 2px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body>
    <?php echo renderOfficeSwitch('back', 'actualite'); ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="sidebar">
                <h3><i class="bi bi-hexagon-fill"></i> KERNEL</h3>
                <p class="text-white-50 small">Backoffice Admin</p>
                <hr style="border-color: rgba(255,255,255,0.2);">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../projet/listeProjet.php">
                            <i class="bi bi-lightbulb me-2"></i> Projets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="listeActualite.php">
                            <i class="bi bi-newspaper me-2"></i> Actualités
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../categorie/listeCategorie.php">
                            <i class="bi bi-grid me-2"></i> Catégories
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i class="bi bi-newspaper text-primary"></i> Gestion des Actualités</h1>
                        <p class="text-muted mb-0">Gérez les actualités des projets</p>
                    </div>
                    <div>
                        <a href="ajouterActualite.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i> Nouvelle Actualité
                        </a>
                        <a href="searchActualites.php" class="btn btn-outline-primary">
                            <i class="bi bi-search me-2"></i> Rechercher par Projet
                        </a>
                    </div>
                </div>

                <!-- Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Table Card -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-2"></i> Liste des Actualités (<?php echo count($actualites); ?>)
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($actualites)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 48px; color: #E5E7EB;"></i>
                                <p class="text-muted mt-3">Aucune actualité pour le moment</p>
                                <a href="ajouterActualite.php" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i> Créer la première actualité
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Titre</th>
                                            <th>Type</th>
                                            <th>Projet</th>
                                            <th>Date Publication</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($actualites as $actu): 
                                            // Badges de type
                                            $typeBadges = [
                                                'milestone' => ['class' => 'bg-success', 'icon' => 'trophy', 'text' => 'Milestone'],
                                                'update' => ['class' => 'bg-primary', 'icon' => 'arrow-repeat', 'text' => 'Update'],
                                                'announcement' => ['class' => 'bg-warning', 'icon' => 'megaphone', 'text' => 'Annonce']
                                            ];
                                            $badge = $typeBadges[$actu['type']] ?? $typeBadges['update'];
                                        ?>
                                            <tr>
                                                <td><strong>#<?php echo $actu['id']; ?></strong></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($actu['titre']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars(substr($actu['contenu'], 0, 60)) . '...'; ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $badge['class']; ?>">
                                                        <i class="bi bi-<?php echo $badge['icon']; ?> me-1"></i>
                                                        <?php echo $badge['text']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="bi bi-folder text-primary me-1"></i>
                                                    <?php echo htmlspecialchars($actu['projet_titre']); ?>
                                                </td>
                                                <td>
                                                    <i class="bi bi-calendar text-muted me-1"></i>
                                                    <?php echo date('d/m/Y H:i', strtotime($actu['date_publication'])); ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="modifierActualite.php?id=<?php echo $actu['id']; ?>" 
                                                       class="btn btn-sm btn-warning btn-action" 
                                                       title="Modifier">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="supprimerActualite.php?id=<?php echo $actu['id']; ?>" 
                                                       class="btn btn-sm btn-danger btn-action" 
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?');"
                                                       title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
