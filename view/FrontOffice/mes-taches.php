<?php
include_once(__DIR__ . '/../../controller/projetcontroller.php');
include_once(__DIR__ . '/../../controller/actualitecontroller.php');
include_once(__DIR__ . '/../../controller/taskcontroller.php');
include_once(__DIR__ . '/../components/main-navigation.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');

$projetController = new ProjetController();
$actualiteController = new ActualiteController();
$taskController = new TaskController();

// Récupérer les données de l'utilisateur (simulation avec user_id = 1)
$userId = 1;
$projets = $projetController->listProjets();
$actualites = $actualiteController->listActualites();

// Filtrer les projets et actualités de l'utilisateur (simulation)
$mesProjets = array_slice($projets, 0, 5); // Simulation
$mesActualites = array_slice($actualites, 0, 5); // Simulation

// Récupérer les tâches de l'utilisateur
$mesTaches = $taskController->getTasksByUser($userId);
$statsUtilisateur = $taskController->getUserTaskStats($userId);

// Paramètres de vue
$viewMode = $_GET['view'] ?? 'dashboard'; // dashboard, kanban, list
$selectedProject = $_GET['project'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Mes Tâches - Kernel</title>
    
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
        
        .page-header {
            background: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .page-title {
            font-size: 36px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 10px;
            font-family: 'Raleway', sans-serif;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #E5E7EB;
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }
        
        .card-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-primary-sm {
            background: var(--primary-color);
            color: white;
            border: 1px solid var(--primary-color);
        }
        
        .btn-primary-sm:hover {
            background: #1D4ED8;
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-success-sm {
            background: #10B981;
            color: white;
            border: 1px solid #10B981;
        }
        
        .btn-success-sm:hover {
            background: #059669;
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-warning-sm {
            background: var(--accent-color);
            color: white;
            border: 1px solid var(--accent-color);
        }
        
        .btn-warning-sm:hover {
            background: #E68A00;
            color: white;
            transform: translateY(-1px);
        }
        
        .item-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .item-list li {
            padding: 15px 0;
            border-bottom: 1px solid #F3F4F6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .item-list li:last-child {
            border-bottom: none;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .item-meta {
            font-size: 13px;
            color: #6B7280;
            display: flex;
            gap: 15px;
        }
        
        .item-actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .action-btn.view {
            background: #EBF8FF;
            color: var(--primary-color);
        }
        
        .action-btn.edit {
            background: #FEF3C7;
            color: var(--accent-color);
        }
        
        .action-btn.delete {
            background: #FEE2E2;
            color: #EF4444;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6B7280;
        }
        
        .empty-state i {
            font-size: 48px;
            color: #E5E7EB;
            margin-bottom: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .quick-action-card {
            background: white;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s;
        }
        
        .quick-action-card:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary-color);
            transform: translateY(-3px);
        }
        
        .quick-action-card i {
            font-size: 32px;
            margin-bottom: 15px;
            color: var(--accent-color);
        }
        
        .quick-action-card:hover i {
            color: var(--primary-color);
        }
        
        /* 📋 Task Management Styles */
        .view-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .view-btn {
            padding: 8px 16px;
            border: 2px solid #E5E7EB;
            background: white;
            color: #6B7280;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .view-btn.active {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }
        
        .view-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .view-btn.active:hover {
            color: white;
        }
        
        /* 📊 Kanban Board Styles */
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        
        .kanban-column {
            background: #F8FAFC;
            border-radius: 12px;
            padding: 20px;
            min-height: 500px;
        }
        
        .kanban-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #E2E8F0;
        }
        
        .kanban-title {
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .kanban-count {
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
        
        .task-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: grab;
            transition: all 0.3s;
            border-left: 4px solid #3B82F6;
        }
        
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .task-card.dragging {
            opacity: 0.5;
            transform: rotate(5deg);
        }
        
        .task-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .task-description {
            font-size: 12px;
            color: #6B7280;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        
        .task-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: #9CA3AF;
        }
        
        .task-priority {
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 10px;
        }
        
        .priority-haute {
            background: #FEE2E2;
            color: #DC2626;
        }
        
        .priority-moyenne {
            background: #FEF3C7;
            color: #D97706;
        }
        
        .priority-basse {
            background: #D1FAE5;
            color: #059669;
        }
        
        .task-tags {
            display: flex;
            gap: 4px;
            margin-top: 8px;
            flex-wrap: wrap;
        }
        
        .task-tag {
            background: #EBF8FF;
            color: #1E40AF;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 500;
        }
        
        .add-task-btn {
            width: 100%;
            padding: 12px;
            border: 2px dashed #CBD5E1;
            background: transparent;
            color: #64748B;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .add-task-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }
        
        /* 📋 List View Styles */
        .tasks-list {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .tasks-list-header {
            background: #F8FAFC;
            padding: 15px 20px;
            border-bottom: 1px solid #E2E8F0;
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 1fr 100px;
            gap: 15px;
            font-weight: 600;
            font-size: 12px;
            color: #64748B;
            text-transform: uppercase;
        }
        
        .task-list-item {
            padding: 15px 20px;
            border-bottom: 1px solid #F1F5F9;
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 1fr 100px;
            gap: 15px;
            align-items: center;
            transition: all 0.3s;
        }
        
        .task-list-item:hover {
            background: #F8FAFC;
        }
        
        .task-list-item:last-child {
            border-bottom: none;
        }
        
        .task-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .task-list-title {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 14px;
        }
        
        .task-list-project {
            font-size: 12px;
            color: #6B7280;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            text-align: center;
        }
        
        .status-a_faire {
            background: #FEF3C7;
            color: #D97706;
        }
        
        .status-en_cours {
            background: #DBEAFE;
            color: #1D4ED8;
        }
        
        .status-termine {
            background: #D1FAE5;
            color: #059669;
        }
        
        .task-actions {
            display: flex;
            gap: 5px;
        }
        
        .task-action-btn {
            width: 28px;
            height: 28px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.3s;
        }
        
        .btn-edit-task {
            background: #FEF3C7;
            color: #D97706;
        }
        
        .btn-delete-task {
            background: #FEE2E2;
            color: #DC2626;
        }
        
        .task-action-btn:hover {
            transform: scale(1.1);
        }
        
        /* 📱 Responsive Design */
        @media (max-width: 768px) {
            .kanban-board {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .tasks-list-header,
            .task-list-item {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .task-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
        
        /* 🎨 Column Colors */
        .kanban-column.a-faire {
            border-top: 4px solid #F59E0B;
        }
        
        .kanban-column.en-cours {
            border-top: 4px solid #3B82F6;
        }
        
        .kanban-column.termine {
            border-top: 4px solid #10B981;
        }
        
        .kanban-column.a-faire .kanban-count {
            background: #F59E0B;
        }
        
        .kanban-column.en-cours .kanban-count {
            background: #3B82F6;
        }
        
        .kanban-column.termine .kanban-count {
            background: #10B981;
        }
        
        /* 🎨 Modal Styles */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0;
            border-bottom: none;
        }
        
        .modal-title {
            font-weight: 600;
            font-family: 'Raleway', sans-serif;
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .modal-footer {
            border-top: 1px solid #E5E7EB;
            padding: 20px 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
        
        .color-picker .color-option {
            transition: all 0.3s;
        }
        
        .color-picker .color-option:hover {
            transform: scale(1.1);
        }
        
        .color-picker .color-option.selected {
            border-color: var(--dark-color) !important;
            transform: scale(1.2);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            background: #1D4ED8;
            border-color: #1D4ED8;
        }
        
        .btn-secondary {
            background: #6B7280;
            border-color: #6B7280;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
        }
        
        /* 🎯 Task Card Enhancements */
        .task-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        
        .task-card-actions {
            display: flex;
            gap: 4px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .task-card:hover .task-card-actions {
            opacity: 1;
        }
        
        .task-card-btn {
            width: 24px;
            height: 24px;
            border: none;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.9);
            color: #6B7280;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
        }
        
        .task-card-btn:hover {
            background: white;
            color: var(--primary-color);
            transform: scale(1.1);
        }
        
        .task-card-btn.delete:hover {
            color: #EF4444;
            background: #FEE2E2;
        }
        
        .task-title {
            flex: 1;
            margin-right: 8px;
        }
        
        /* 🔍 Validation Styles */
        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .form-control.is-valid,
        .form-select.is-valid {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .alert-danger h6 {
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .alert ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        
        .alert li {
            margin-bottom: 5px;
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <?php echo renderMainNavigation('profil'); ?>
    <?php echo renderChatbotWidget(); ?>
    
    <div class="page-header">
        <div class="container">
            <h1 class="page-title"><i class="bi bi-list-task"></i> Mes Tâches</h1>
            <p>Gérez tous vos projets, actualités et tâches depuis un seul endroit</p>
            
            <!-- View Toggle -->
            <div class="view-toggle">
                <a href="?view=dashboard" class="view-btn <?php echo $viewMode === 'dashboard' ? 'active' : ''; ?>">
                    <i class="bi bi-grid"></i> Tableau de bord
                </a>
                <a href="?view=kanban" class="view-btn <?php echo $viewMode === 'kanban' ? 'active' : ''; ?>">
                    <i class="bi bi-kanban"></i> Vue Kanban
                </a>
                <a href="?view=list" class="view-btn <?php echo $viewMode === 'list' ? 'active' : ''; ?>">
                    <i class="bi bi-list-ul"></i> Vue Liste
                </a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($mesProjets); ?></div>
                <div class="stat-label">Mes Projets</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #10B981, #059669);">
                <div class="stat-number"><?php echo count($mesActualites); ?></div>
                <div class="stat-label">Mes Actualités</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #F59E0B, #E68A00);">
                <div class="stat-number"><?php echo $statsUtilisateur['en_cours'] ?? 0; ?></div>
                <div class="stat-label">Tâches en cours</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #EF4444, #DC2626);">
                <div class="stat-number"><?php echo $statsUtilisateur['urgentes'] ?? 0; ?></div>
                <div class="stat-label">Actions urgentes</div>
            </div>
        </div>
        
        <?php if ($viewMode === 'dashboard'): ?>
        <!-- Actions Rapides -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-lightning"></i> Actions Rapides
                </h3>
            </div>
            <div class="quick-actions-grid">
                <a href="ajoutprojet.php" class="quick-action-card">
                    <i class="bi bi-plus-circle"></i>
                    <h5>Nouveau Projet</h5>
                    <p class="text-muted">Créer un nouveau projet innovant</p>
                </a>
                <a href="ajouterActualite.php" class="quick-action-card">
                    <i class="bi bi-newspaper"></i>
                    <h5>Publier Actualité</h5>
                    <p class="text-muted">Partager les dernières nouvelles</p>
                </a>
                <a href="listeprojet.php" class="quick-action-card">
                    <i class="bi bi-search"></i>
                    <h5>Explorer Projets</h5>
                    <p class="text-muted">Découvrir de nouveaux projets</p>
                </a>
                <a href="?view=kanban" class="quick-action-card">
                    <i class="bi bi-kanban"></i>
                    <h5>Gérer Tâches</h5>
                    <p class="text-muted">Organiser vos tâches en Kanban</p>
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($viewMode === 'kanban'): ?>
        <!-- Vue Kanban -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-kanban"></i> Tableau Kanban
                </h3>
                <div class="card-actions">
                    <button class="btn-sm-action btn-success-sm" onclick="openAddTaskModal()">
                        <i class="bi bi-plus"></i> Nouvelle tâche
                    </button>
                </div>
            </div>
            
            <div class="kanban-board">
                <!-- Colonne À Faire -->
                <div class="kanban-column a-faire" data-status="a_faire">
                    <div class="kanban-header">
                        <div class="kanban-title">
                            <i class="bi bi-circle"></i> À Faire
                        </div>
                        <div class="kanban-count"><?php echo count(array_filter($mesTaches, fn($t) => $t['statut'] === 'a_faire')); ?></div>
                    </div>
                    
                    <?php foreach ($mesTaches as $tache): ?>
                        <?php if ($tache['statut'] === 'a_faire'): ?>
                        <div class="task-card" data-task-id="<?php echo $tache['id']; ?>" draggable="true" style="border-left-color: <?php echo $tache['couleur'] ?? '#3B82F6'; ?>;">
                            <div class="task-card-header">
                                <div class="task-title"><?php echo htmlspecialchars($tache['titre']); ?></div>
                                <div class="task-card-actions">
                                    <button class="task-card-btn" onclick="editTask(<?php echo $tache['id']; ?>)" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="task-card-btn delete" onclick="deleteTask(<?php echo $tache['id']; ?>)" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php if ($tache['description']): ?>
                            <div class="task-description"><?php echo htmlspecialchars(substr($tache['description'], 0, 100)) . (strlen($tache['description']) > 100 ? '...' : ''); ?></div>
                            <?php endif; ?>
                            <div class="task-meta">
                                <span class="task-priority priority-<?php echo $tache['priorite']; ?>">
                                    <?php echo ucfirst($tache['priorite']); ?>
                                </span>
                                <span><?php echo htmlspecialchars($tache['projet_titre']); ?></span>
                            </div>
                            <?php if ($tache['tags']): ?>
                            <div class="task-tags">
                                <?php foreach (explode(',', $tache['tags']) as $tag): ?>
                                <span class="task-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <button class="add-task-btn" onclick="openAddTaskModal('a_faire')">
                        <i class="bi bi-plus"></i> Ajouter une tâche
                    </button>
                </div>
                
                <!-- Colonne En Cours -->
                <div class="kanban-column en-cours" data-status="en_cours">
                    <div class="kanban-header">
                        <div class="kanban-title">
                            <i class="bi bi-arrow-clockwise"></i> En Cours
                        </div>
                        <div class="kanban-count"><?php echo count(array_filter($mesTaches, fn($t) => $t['statut'] === 'en_cours')); ?></div>
                    </div>
                    
                    <?php foreach ($mesTaches as $tache): ?>
                        <?php if ($tache['statut'] === 'en_cours'): ?>
                        <div class="task-card" data-task-id="<?php echo $tache['id']; ?>" draggable="true" style="border-left-color: <?php echo $tache['couleur'] ?? '#3B82F6'; ?>;">
                            <div class="task-card-header">
                                <div class="task-title"><?php echo htmlspecialchars($tache['titre']); ?></div>
                                <div class="task-card-actions">
                                    <button class="task-card-btn" onclick="editTask(<?php echo $tache['id']; ?>)" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="task-card-btn delete" onclick="deleteTask(<?php echo $tache['id']; ?>)" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php if ($tache['description']): ?>
                            <div class="task-description"><?php echo htmlspecialchars(substr($tache['description'], 0, 100)) . (strlen($tache['description']) > 100 ? '...' : ''); ?></div>
                            <?php endif; ?>
                            <div class="task-meta">
                                <span class="task-priority priority-<?php echo $tache['priorite']; ?>">
                                    <?php echo ucfirst($tache['priorite']); ?>
                                </span>
                                <span><?php echo htmlspecialchars($tache['projet_titre']); ?></span>
                            </div>
                            <?php if ($tache['tags']): ?>
                            <div class="task-tags">
                                <?php foreach (explode(',', $tache['tags']) as $tag): ?>
                                <span class="task-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <button class="add-task-btn" onclick="openAddTaskModal('en_cours')">
                        <i class="bi bi-plus"></i> Ajouter une tâche
                    </button>
                </div>
                
                <!-- Colonne Terminé -->
                <div class="kanban-column termine" data-status="termine">
                    <div class="kanban-header">
                        <div class="kanban-title">
                            <i class="bi bi-check-circle"></i> Terminé
                        </div>
                        <div class="kanban-count"><?php echo count(array_filter($mesTaches, fn($t) => $t['statut'] === 'termine')); ?></div>
                    </div>
                    
                    <?php foreach ($mesTaches as $tache): ?>
                        <?php if ($tache['statut'] === 'termine'): ?>
                        <div class="task-card" data-task-id="<?php echo $tache['id']; ?>" draggable="true" style="border-left-color: <?php echo $tache['couleur'] ?? '#3B82F6'; ?>;">
                            <div class="task-card-header">
                                <div class="task-title"><?php echo htmlspecialchars($tache['titre']); ?></div>
                                <div class="task-card-actions">
                                    <button class="task-card-btn" onclick="editTask(<?php echo $tache['id']; ?>)" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="task-card-btn delete" onclick="deleteTask(<?php echo $tache['id']; ?>)" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php if ($tache['description']): ?>
                            <div class="task-description"><?php echo htmlspecialchars(substr($tache['description'], 0, 100)) . (strlen($tache['description']) > 100 ? '...' : ''); ?></div>
                            <?php endif; ?>
                            <div class="task-meta">
                                <span class="task-priority priority-<?php echo $tache['priorite']; ?>">
                                    <?php echo ucfirst($tache['priorite']); ?>
                                </span>
                                <span><?php echo htmlspecialchars($tache['projet_titre']); ?></span>
                            </div>
                            <?php if ($tache['tags']): ?>
                            <div class="task-tags">
                                <?php foreach (explode(',', $tache['tags']) as $tag): ?>
                                <span class="task-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($viewMode === 'list'): ?>
        <!-- Vue Liste -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-list-ul"></i> Liste des Tâches
                </h3>
                <div class="card-actions">
                    <button class="btn-sm-action btn-success-sm" onclick="openAddTaskModal()">
                        <i class="bi bi-plus"></i> Nouvelle tâche
                    </button>
                </div>
            </div>
            
            <div class="tasks-list">
                <div class="tasks-list-header">
                    <div>Tâche</div>
                    <div>Statut</div>
                    <div>Priorité</div>
                    <div>Échéance</div>
                    <div>Actions</div>
                </div>
                
                <?php if (empty($mesTaches)): ?>
                <div class="empty-state">
                    <i class="bi bi-list-task"></i>
                    <h5>Aucune tâche créée</h5>
                    <p>Commencez par créer votre première tâche</p>
                    <button class="btn-sm-action btn-primary-sm" onclick="openAddTaskModal()">
                        <i class="bi bi-plus"></i> Créer une tâche
                    </button>
                </div>
                <?php else: ?>
                    <?php foreach ($mesTaches as $tache): ?>
                    <div class="task-list-item">
                        <div class="task-info">
                            <div class="task-list-title"><?php echo htmlspecialchars($tache['titre']); ?></div>
                            <div class="task-list-project"><?php echo htmlspecialchars($tache['projet_titre']); ?></div>
                        </div>
                        <div>
                            <span class="status-badge status-<?php echo $tache['statut']; ?>">
                                <?php 
                                $statusLabels = [
                                    'a_faire' => 'À Faire',
                                    'en_cours' => 'En Cours', 
                                    'termine' => 'Terminé'
                                ];
                                echo $statusLabels[$tache['statut']];
                                ?>
                            </span>
                        </div>
                        <div>
                            <span class="task-priority priority-<?php echo $tache['priorite']; ?>">
                                <?php echo ucfirst($tache['priorite']); ?>
                            </span>
                        </div>
                        <div>
                            <?php if ($tache['date_echeance']): ?>
                                <?php echo date('d/m/Y', strtotime($tache['date_echeance'])); ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                        <div class="task-actions">
                            <button class="task-action-btn btn-edit-task" onclick="editTask(<?php echo $tache['id']; ?>)" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="task-action-btn btn-delete-task" onclick="deleteTask(<?php echo $tache['id']; ?>)" title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($viewMode === 'dashboard'): ?>
        <div class="row">
            <!-- Mes Projets -->
            <div class="col-lg-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-lightbulb"></i> Mes Projets
                        </h3>
                        <div class="card-actions">
                            <a href="ajoutprojet.php" class="btn-sm-action btn-success-sm">
                                <i class="bi bi-plus"></i> Nouveau
                            </a>
                            <a href="listeprojet.php?filter=mes-projets" class="btn-sm-action btn-primary-sm">
                                <i class="bi bi-list"></i> Voir tout
                            </a>
                        </div>
                    </div>
                    
                    <?php if (empty($mesProjets)): ?>
                        <div class="empty-state">
                            <i class="bi bi-lightbulb"></i>
                            <h5>Aucun projet créé</h5>
                            <p>Commencez par créer votre premier projet innovant</p>
                            <a href="ajoutprojet.php" class="btn-sm-action btn-primary-sm">
                                <i class="bi bi-plus"></i> Créer un projet
                            </a>
                        </div>
                    <?php else: ?>
                        <ul class="item-list">
                            <?php foreach ($mesProjets as $projet): ?>
                                <li>
                                    <div class="item-info">
                                        <div class="item-title"><?php echo htmlspecialchars($projet['titre']); ?></div>
                                        <div class="item-meta">
                                            <span><i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($projet['date_creation'])); ?></span>
                                            <span><i class="bi bi-cash"></i> <?php echo number_format($projet['budget_requis'], 0, ',', ' '); ?> TND</span>
                                            <span><i class="bi bi-tag"></i> <?php echo ucfirst($projet['statut']); ?></span>
                                        </div>
                                    </div>
                                    <div class="item-actions">
                                        <a href="detailsprojet.php?id=<?php echo $projet['id']; ?>" class="action-btn view" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="modifierprojet.php?id=<?php echo $projet['id']; ?>" class="action-btn edit" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?view=kanban&project=<?php echo $projet['id']; ?>" class="action-btn" style="background: #DBEAFE; color: #1D4ED8;" title="Gérer tâches">
                                            <i class="bi bi-kanban"></i>
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Mes Tâches Récentes -->
            <div class="col-lg-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-list-task"></i> Mes Tâches Récentes
                        </h3>
                        <div class="card-actions">
                            <a href="?view=kanban" class="btn-sm-action btn-success-sm">
                                <i class="bi bi-kanban"></i> Kanban
                            </a>
                            <a href="?view=list" class="btn-sm-action btn-primary-sm">
                                <i class="bi bi-list"></i> Voir tout
                            </a>
                        </div>
                    </div>
                    
                    <?php if (empty($mesTaches)): ?>
                        <div class="empty-state">
                            <i class="bi bi-list-task"></i>
                            <h5>Aucune tâche créée</h5>
                            <p>Commencez par créer votre première tâche</p>
                            <a href="?view=kanban" class="btn-sm-action btn-primary-sm">
                                <i class="bi bi-plus"></i> Créer une tâche
                            </a>
                        </div>
                    <?php else: ?>
                        <ul class="item-list">
                            <?php foreach (array_slice($mesTaches, 0, 5) as $tache): ?>
                                <li>
                                    <div class="item-info">
                                        <div class="item-title"><?php echo htmlspecialchars($tache['titre']); ?></div>
                                        <div class="item-meta">
                                            <span><i class="bi bi-folder"></i> <?php echo htmlspecialchars($tache['projet_titre']); ?></span>
                                            <span class="task-priority priority-<?php echo $tache['priorite']; ?>">
                                                <?php echo ucfirst($tache['priorite']); ?>
                                            </span>
                                            <span class="status-badge status-<?php echo $tache['statut']; ?>">
                                                <?php 
                                                $statusLabels = [
                                                    'a_faire' => 'À Faire',
                                                    'en_cours' => 'En Cours', 
                                                    'termine' => 'Terminé'
                                                ];
                                                echo $statusLabels[$tache['statut']];
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="item-actions">
                                        <a href="?view=kanban&project=<?php echo $tache['projet_id']; ?>" class="action-btn view" title="Voir projet">
                                            <i class="bi bi-kanban"></i>
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- 📋 Task Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalTitle">Créer une nouvelle tâche</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="taskForm" onsubmit="handleTaskSubmit(event)">
                        <input type="hidden" id="taskId" name="task_id" value="">
                        <input type="hidden" id="taskColor" name="couleur" value="#3B82F6">
                        <input type="hidden" name="created_by" value="<?php echo $userId; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taskProject" class="form-label">Projet *</label>
                                    <select id="taskProject" name="projet_id" class="form-select" required>
                                        <option value="">Sélectionner un projet</option>
                                        <?php foreach ($projets as $projet): ?>
                                        <option value="<?php echo $projet['id']; ?>">
                                            <?php echo htmlspecialchars($projet['titre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taskStatus" class="form-label">Statut</label>
                                    <select id="taskStatus" name="statut" class="form-select">
                                        <option value="a_faire">À Faire</option>
                                        <option value="en_cours">En Cours</option>
                                        <option value="termine">Terminé</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Titre de la tâche *</label>
                            <input type="text" id="taskTitle" name="titre" class="form-control" required placeholder="Ex: Développer l'interface utilisateur">
                        </div>
                        
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Description</label>
                            <textarea id="taskDescription" name="description" class="form-control" rows="3" placeholder="Décrivez la tâche en détail..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taskPriority" class="form-label">Priorité</label>
                                    <select id="taskPriority" name="priorite" class="form-select">
                                        <option value="basse">Basse</option>
                                        <option value="moyenne" selected>Moyenne</option>
                                        <option value="haute">Haute</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taskDeadline" class="form-label">Date d'échéance</label>
                                    <input type="date" id="taskDeadline" name="date_echeance" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taskTags" class="form-label">Tags (séparés par des virgules)</label>
                                    <input type="text" id="taskTags" name="tags" class="form-control" placeholder="Ex: Frontend, React, UI">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taskEstimatedTime" class="form-label">Temps estimé (heures)</label>
                                    <input type="number" id="taskEstimatedTime" name="temps_estime" class="form-control" min="1" placeholder="Ex: 8">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Couleur de la tâche</label>
                            <div class="color-picker d-flex gap-2 flex-wrap">
                                <div class="color-option selected" style="background: #3B82F6; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" data-color="#3B82F6"></div>
                                <div class="color-option" style="background: #EF4444; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" data-color="#EF4444"></div>
                                <div class="color-option" style="background: #10B981; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" data-color="#10B981"></div>
                                <div class="color-option" style="background: #F59E0B; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" data-color="#F59E0B"></div>
                                <div class="color-option" style="background: #8B5CF6; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" data-color="#8B5CF6"></div>
                                <div class="color-option" style="background: #EC4899; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" data-color="#EC4899"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="taskForm" class="btn btn-primary" id="taskSubmitBtn">Créer la tâche</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    
    <script>
        // 🎯 Task Management JavaScript
        
        // Initialize drag & drop for Kanban view
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('.kanban-board')) {
                initializeKanban();
            }
        });
        
        function initializeKanban() {
            const columns = document.querySelectorAll('.kanban-column');
            
            columns.forEach(column => {
                new Sortable(column, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'task-card-ghost',
                    chosenClass: 'task-card-chosen',
                    dragClass: 'task-card-drag',
                    filter: '.add-task-btn, .kanban-header',
                    onStart: function(evt) {
                        evt.item.classList.add('dragging');
                    },
                    onEnd: function(evt) {
                        evt.item.classList.remove('dragging');
                        
                        const taskId = evt.item.dataset.taskId;
                        const newStatus = evt.to.dataset.status;
                        const oldStatus = evt.from.dataset.status;
                        
                        if (newStatus !== oldStatus) {
                            updateTaskStatus(taskId, newStatus);
                            updateKanbanCounts();
                        }
                    }
                });
            });
        }
        
        // Update task status via AJAX
        function updateTaskStatus(taskId, newStatus) {
            fetch('../../api/update-task-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    task_id: taskId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Tâche mise à jour avec succès', 'success');
                } else {
                    showNotification('Erreur lors de la mise à jour', 'error');
                    // Revert the change if needed
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erreur de connexion', 'error');
                location.reload();
            });
        }
        
        // Update Kanban column counts
        function updateKanbanCounts() {
            const columns = document.querySelectorAll('.kanban-column');
            
            columns.forEach(column => {
                const tasks = column.querySelectorAll('.task-card');
                const count = column.querySelector('.kanban-count');
                if (count) {
                    count.textContent = tasks.length;
                }
            });
        }
        
        // Show notification
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
            notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }
        
        // 📋 CRUD Modal Functions
        function openAddTaskModal(status = 'a_faire') {
            // Reset form
            document.getElementById('taskForm').reset();
            document.getElementById('taskId').value = '';
            document.getElementById('taskModalTitle').textContent = 'Créer une nouvelle tâche';
            document.getElementById('taskSubmitBtn').textContent = 'Créer la tâche';
            
            // Set default status
            document.getElementById('taskStatus').value = status;
            
            // Pre-select project if available
            const projectId = new URLSearchParams(window.location.search).get('project');
            if (projectId) {
                document.getElementById('taskProject').value = projectId;
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('taskModal'));
            modal.show();
        }
        
        function editTask(taskId) {
            // Fetch task data
            fetch(`../../api/get-task.php?id=${taskId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const task = data.task;
                        
                        // Fill form with task data
                        document.getElementById('taskId').value = task.id;
                        document.getElementById('taskProject').value = task.projet_id;
                        document.getElementById('taskTitle').value = task.titre;
                        document.getElementById('taskDescription').value = task.description || '';
                        document.getElementById('taskStatus').value = task.statut;
                        document.getElementById('taskPriority').value = task.priorite;
                        document.getElementById('taskDeadline').value = task.date_echeance ? task.date_echeance.split(' ')[0] : '';
                        document.getElementById('taskTags').value = task.tags || '';
                        document.getElementById('taskEstimatedTime').value = task.temps_estime || '';
                        document.getElementById('taskColor').value = task.couleur || '#3B82F6';
                        
                        // Update color picker
                        updateColorPicker(task.couleur || '#3B82F6');
                        
                        // Update modal title
                        document.getElementById('taskModalTitle').textContent = 'Modifier la tâche';
                        document.getElementById('taskSubmitBtn').textContent = 'Mettre à jour';
                        
                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('taskModal'));
                        modal.show();
                    } else {
                        showNotification('Erreur lors du chargement de la tâche', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Erreur de connexion', 'error');
                });
        }
        
        // 🔍 Validation JavaScript personnalisée
        function validateTaskForm() {
            const errors = [];
            
            // Validation du projet
            const projet = document.getElementById('taskProject').value;
            if (!projet || projet === '') {
                errors.push('Veuillez sélectionner un projet');
                markFieldAsError('taskProject');
            } else {
                markFieldAsValid('taskProject');
            }
            
            // Validation du titre
            const titre = document.getElementById('taskTitle').value.trim();
            if (!titre || titre.length < 3) {
                errors.push('Le titre doit contenir au moins 3 caractères');
                markFieldAsError('taskTitle');
            } else if (titre.length > 255) {
                errors.push('Le titre ne peut pas dépasser 255 caractères');
                markFieldAsError('taskTitle');
            } else {
                markFieldAsValid('taskTitle');
            }
            
            // Validation de la description (optionnelle mais si présente, max 1000 caractères)
            const description = document.getElementById('taskDescription').value.trim();
            if (description && description.length > 1000) {
                errors.push('La description ne peut pas dépasser 1000 caractères');
                markFieldAsError('taskDescription');
            } else {
                markFieldAsValid('taskDescription');
            }
            
            // Validation du temps estimé (si présent, doit être positif)
            const tempsEstime = document.getElementById('taskEstimatedTime').value;
            if (tempsEstime && (isNaN(tempsEstime) || parseInt(tempsEstime) < 1)) {
                errors.push('Le temps estimé doit être un nombre positif');
                markFieldAsError('taskEstimatedTime');
            } else {
                markFieldAsValid('taskEstimatedTime');
            }
            
            return errors;
        }
        
        // Marquer un champ comme ayant une erreur
        function markFieldAsError(fieldId) {
            const field = document.getElementById(fieldId);
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
        }
        
        // Marquer un champ comme valide
        function markFieldAsValid(fieldId) {
            const field = document.getElementById(fieldId);
            field.classList.add('is-valid');
            field.classList.remove('is-invalid');
        }
        
        // Effacer toutes les validations visuelles
        function clearValidationStyles() {
            const fields = ['taskProject', 'taskTitle', 'taskDescription', 'taskEstimatedTime'];
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                field.classList.remove('is-invalid', 'is-valid');
            });
        }
        
        // Afficher les erreurs de validation
        function showValidationErrors(errors) {
            const errorHtml = errors.map(error => `<li>${error}</li>`).join('');
            const errorMessage = `
                <div class="alert alert-danger" role="alert">
                    <h6><i class="bi bi-exclamation-triangle"></i> Erreurs de validation :</h6>
                    <ul class="mb-0">${errorHtml}</ul>
                </div>
            `;
            
            // Supprimer les anciennes erreurs
            const existingErrors = document.querySelector('#taskModal .alert-danger');
            if (existingErrors) {
                existingErrors.remove();
            }
            
            // Ajouter les nouvelles erreurs au début du modal body
            const modalBody = document.querySelector('#taskModal .modal-body');
            modalBody.insertAdjacentHTML('afterbegin', errorMessage);
            
            // Faire défiler vers le haut du modal
            modalBody.scrollTop = 0;
        }
        
        // Supprimer les messages d'erreur
        function clearValidationErrors() {
            const existingErrors = document.querySelector('#taskModal .alert-danger');
            if (existingErrors) {
                existingErrors.remove();
            }
        }
        
        // Handle form submission avec validation améliorée
        function handleTaskSubmit(event) {
            event.preventDefault();
            
            // Effacer les validations précédentes
            clearValidationStyles();
            clearValidationErrors();
            
            // Valider le formulaire
            const validationErrors = validateTaskForm();
            
            if (validationErrors.length > 0) {
                showValidationErrors(validationErrors);
                return false;
            }
            
            // Désactiver le bouton de soumission pour éviter les doubles clics
            const submitBtn = document.getElementById('taskSubmitBtn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Traitement...';
            
            // Préparer les données
            const formData = new FormData(document.getElementById('taskForm'));
            const taskId = document.getElementById('taskId').value;
            
            // Debug: Afficher les données envoyées
            console.log('Données envoyées:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            const url = taskId ? '../../api/update-task.php' : '../../api/create-task.php';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Response is not JSON:', text);
                        throw new Error('Réponse serveur invalide: ' + text);
                    }
                });
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    showNotification(data.message || 'Opération réussie', 'success');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
                    modal.hide();
                    
                    // Reload page to show changes
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Erreur lors de l\'opération', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erreur de connexion: ' + error.message, 'error');
            })
            .finally(() => {
                // Réactiver le bouton
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        }
        
        // Color picker functionality
        function updateColorPicker(selectedColor) {
            document.querySelectorAll('.color-option').forEach(option => {
                option.classList.remove('selected');
                if (option.dataset.color === selectedColor) {
                    option.classList.add('selected');
                }
            });
        }
        
        // Initialize color picker and real-time validation
        document.addEventListener('DOMContentLoaded', function() {
            // Color picker
            document.querySelectorAll('.color-option').forEach(option => {
                option.addEventListener('click', function() {
                    updateColorPicker(this.dataset.color);
                    document.getElementById('taskColor').value = this.dataset.color;
                });
            });
            
            // Real-time validation
            const fieldsToValidate = [
                { id: 'taskProject', validator: validateProject },
                { id: 'taskTitle', validator: validateTitle },
                { id: 'taskDescription', validator: validateDescription },
                { id: 'taskEstimatedTime', validator: validateEstimatedTime }
            ];
            
            fieldsToValidate.forEach(field => {
                const element = document.getElementById(field.id);
                if (element) {
                    element.addEventListener('blur', function() {
                        field.validator(this);
                    });
                    
                    element.addEventListener('input', function() {
                        // Supprimer les styles d'erreur pendant la saisie
                        this.classList.remove('is-invalid');
                        clearValidationErrors();
                    });
                }
            });
        });
        
        // Validateurs individuels pour la validation en temps réel
        function validateProject(field) {
            if (!field.value || field.value === '') {
                markFieldAsError(field.id);
                return false;
            } else {
                markFieldAsValid(field.id);
                return true;
            }
        }
        
        function validateTitle(field) {
            const value = field.value.trim();
            if (!value || value.length < 3) {
                markFieldAsError(field.id);
                return false;
            } else if (value.length > 255) {
                markFieldAsError(field.id);
                return false;
            } else {
                markFieldAsValid(field.id);
                return true;
            }
        }
        
        function validateDescription(field) {
            const value = field.value.trim();
            if (value && value.length > 1000) {
                markFieldAsError(field.id);
                return false;
            } else {
                markFieldAsValid(field.id);
                return true;
            }
        }
        
        function validateEstimatedTime(field) {
            const value = field.value;
            if (value && (isNaN(value) || parseInt(value) < 1)) {
                markFieldAsError(field.id);
                return false;
            } else {
                markFieldAsValid(field.id);
                return true;
            }
        }
        
        function deleteTask(taskId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
                fetch('../../api/delete-task.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        task_id: taskId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Tâche supprimée avec succès', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification('Erreur lors de la suppression', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Erreur de connexion', 'error');
                });
            }
        }
        
        // Add some CSS for drag & drop states
        const style = document.createElement('style');
        style.textContent = `
            .task-card-ghost {
                opacity: 0.4;
            }
            
            .task-card-chosen {
                transform: rotate(5deg);
            }
            
            .task-card-drag {
                transform: rotate(10deg);
                box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            }
            
            .kanban-column.sortable-drag-over {
                background: rgba(59, 130, 246, 0.1);
                border: 2px dashed #3B82F6;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>