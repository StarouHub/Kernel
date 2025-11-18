<?php
// Include the controller
include 'controller.php';
$controller = new InvestmentController();

// Get data from controller
$investments = $controller->getInvestments();
$tenders = $controller->getTenders();
$portfolio = $controller->getPortfolio();
$transactions = $controller->getTransactions();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Mes Investissements - Kernel</title>

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

  <link href="style.css" rel="stylesheet">
</head>

<body>
  <header class="header d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo">
        <i class="bi bi-hexagon-fill"></i> Kernel
      </a>

      <nav class="navmenu">
        <ul>
          <li><a href="index.php">Accueil</a></li>
          <li><a href="projets-list.php">Projets</a></li>
          <li><a href="evenements-list.php">Événements</a></li>
          <li><a href="forum.php">Forum</a></li>
          <li><a href="index.php" style="color: var(--accent-color);">Mes Investissements</a></li>
        </ul>
      </nav>

      <a class="btn-getstarted" href="profil-utilisateur.php">Mon Profil</a>
    </div>
  </header>

  <div class="page-header">
    <div class="container">
      <h1><i class="bi bi-cash-coin"></i> Mes Investissements</h1>
      <p>Gérez et suivez vos investissements et les appels d'offres publiés par les porteurs de projet.</p>
    </div>
  </div>

  <div class="container">
    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background: #DBEAFE; color: var(--primary-color);">
          <i class="bi bi-wallet2"></i>
        </div>
        <div class="stat-value" id="totalInvested"><?php echo number_format($portfolio['totalInvested'], 0, ',', ' '); ?> TND</div>
        <div class="stat-label">Total Investi</div>
        <span class="stat-change positive" id="monthlyChange">+<?php echo $portfolio['monthlyChange']; ?>% ce mois</span>
      </div>

      <div class="stat-card">
        <div class="stat-icon" style="background: #D1FAE5; color: #10B981;">
          <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div class="stat-value" id="totalGains"><?php echo number_format($portfolio['totalGains'], 0, ',', ' '); ?> TND</div>
        <div class="stat-label">Gains Totaux</div>
        <span class="stat-change positive" id="gainsChange">+<?php echo $portfolio['gainsChange']; ?>%</span>
      </div>

      <div class="stat-card">
        <div class="stat-icon" style="background: #FEF3C7; color: var(--accent-color);">
          <i class="bi bi-briefcase"></i>
        </div>
        <div class="stat-value" id="activeProjects"><?php echo $portfolio['activeProjects']; ?></div>
        <div class="stat-label">Projets Actifs</div>
        <span class="stat-change positive" id="projectsChange">+<?php echo $portfolio['projectsChange']; ?> ce mois</span>
      </div>

      <div class="stat-card">
        <div class="stat-icon" style="background: #E0E7FF; color: var(--secondary-color);">
          <i class="bi bi-trophy"></i>
        </div>
        <div class="stat-value" id="investorScore"><?php echo $portfolio['investorScore']; ?></div>
        <div class="stat-label">Score Investisseur</div>
        <span class="stat-change positive">Excellent</span>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <!-- Portfolio Chart -->
        <div class="content-card">
          <div class="section-title">
            <i class="bi bi-graph-up"></i> Évolution du Portfolio
          </div>
          <div class="chart-placeholder">
            <i class="bi bi-bar-chart-line" style="font-size: 48px; margin-right: 15px;"></i>
            Graphique d'évolution des investissements
          </div>
        </div>

        <!-- Active Investments -->
        <div class="content-card">
          <div class="section-title">
            <i class="bi bi-briefcase"></i> Investissements Actifs (<span id="activeInvestmentsCount"><?php echo count($investments); ?></span>)
          </div>

          <div class="filter-tabs">
            <button class="filter-tab active" data-filter="all">Tous</button>
            <button class="filter-tab" data-filter="active">En cours</button>
            <button class="filter-tab" data-filter="completed">Financés</button>
            <button class="filter-tab" data-filter="profitable">Rentables</button>
          </div>

          <div id="activeInvestmentsList">
            <?php foreach($investments as $investment): ?>
            <div class="investment-item">
              <img src="<?php echo $investment['thumbnail']; ?>" class="project-thumb" alt="<?php echo $investment['projectName']; ?>">
              <div class="investment-details">
                <div class="project-name"><?php echo $investment['projectName']; ?></div>
                <div class="investment-meta">
                  <span><i class="bi bi-calendar"></i> Investi le <?php echo date('d/m/Y', strtotime($investment['date'])); ?></span>
                  <span><i class="bi bi-percent"></i> ROI: +<?php echo $investment['roi']; ?>%</span>
                </div>
                <span class="investment-status status-<?php echo $investment['status']; ?>">
                  <?php 
                  $statusText = [
                    'active' => 'En cours',
                    'completed' => 'Financé',
                    'pending' => 'En attente'
                  ];
                  echo $statusText[$investment['status']] ?? $investment['status'];
                  ?>
                </span>
              </div>
              <div class="investment-amount"><?php echo number_format($investment['amount'], 0, ',', ' '); ?> TND</div>
            </div>
            <?php endforeach; ?>
          </div>

          <button class="btn-invest-more w-100 mt-3" onclick="window.location.href='projets-list.php'">
            <i class="bi bi-plus-circle me-2"></i> Découvrir plus de projets
          </button>
        </div>

        <!-- APPEL D'OFFRES BOX -->
        <div class="content-card" id="tenders-box">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="section-title" style="margin-bottom: 0;"><i class="bi bi-megaphone"></i> Appels d'Offres</div>
            <button class="btn-create-tender" id="createTenderBtn">
              <i class="bi bi-plus-circle me-2"></i> Créer un Appel d'Offres
            </button>
          </div>

          <div style="display:flex; gap:10px; margin-bottom:12px; align-items:center;">
            <select id="tenderFilter" class="filter-tab" style="padding:8px 12px;">
              <option value="all">Tous les secteurs</option>
              <option value="IoT">IoT</option>
              <option value="AI">IA</option>
              <option value="Blockchain">Blockchain</option>
              <option value="Health">Health</option>
              <option value="Energy">Énergie</option>
              <option value="Education">Éducation</option>
            </select>

            <button class="filter-tab" id="sortClose" style="padding:8px 12px;">Proches de clôture</button>
            <div style="margin-left:auto; color:#6B7280; font-size:13px;">{ <span id="tenderCount"><?php echo count($tenders); ?></span> } offres</div>
          </div>

          <div id="tendersList" style="display:grid; gap:12px;">
            <?php foreach($tenders as $tender): ?>
            <?php
            $progress = round(($tender['raised'] / $tender['fundingTarget']) * 100);
            $daysLeft = ceil((strtotime($tender['deadline']) - time()) / (60 * 60 * 24));
            ?>
            <div class="investment-item" style="justify-content: space-between;">
              <div style="display:flex; gap:12px; align-items:center;">
                <div style="width:64px; height:48px; background:linear-gradient(135deg,#2563EB,#7C3AED); border-radius:8px; color:white; display:flex; align-items:center; justify-content:center; font-weight:700;">
                  <?php echo substr($tender['sector'], 0, 3); ?>
                </div>
                <div style="min-width:200px;">
                  <div style="font-weight:600;"><?php echo $tender['projectName']; ?></div>
                  <div style="font-size:13px; color:#6B7280;"><?php echo $tender['shortPitch']; ?></div>
                  <div class="progress-container">
                    <div class="progress-bar" style="width: <?php echo $progress; ?>%"></div>
                  </div>
                  <div style="margin-top:6px; font-size:13px;">
                    <span style="font-weight:700; color:#2563EB;"><?php echo number_format($tender['raised'], 0, ',', ' '); ?> TND</span>
                    <span style="color:#6B7280;"> / <?php echo number_format($tender['fundingTarget'], 0, ',', ' '); ?> TND</span>
                    <span style="margin-left:10px;" class="status-<?php echo $tender['status']; ?>">
                      <?php echo $tender['status'] == 'open' ? 'Ouvert' : 'Financé'; ?>
                    </span>
                  </div>
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:12px;">
                <div style="text-align:right; font-weight:700;">Min <?php echo number_format($tender['minInvestment'], 0, ',', ' '); ?> TND</div>
                <div style="text-align:right; font-size:13px; color:#6B7280;">Clôture: <?php echo $daysLeft; ?> j</div>
                <div>
                  <button class="filter-tab" data-id="<?php echo $tender['tenderId']; ?>">Voir</button>
                  <?php if($tender['status'] == 'open'): ?>
                  <button class="btn-invest-more" data-invest="<?php echo $tender['tenderId']; ?>" style="margin-left:8px;">Investir</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Transactions History -->
        <div class="content-card">
          <div class="section-title">
            <i class="bi bi-clock-history"></i> Historique des Transactions
          </div>

          <table class="transaction-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Projet</th>
                <th>Montant</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody id="transactionHistoryBody">
              <?php foreach($transactions as $transaction): ?>
              <tr>
                <td><?php echo date('d/m/Y', strtotime($transaction['date'])); ?></td>
                <td>
                  <span class="transaction-type type-<?php echo $transaction['type']; ?>">
                    <i class="bi <?php echo $transaction['type'] === 'investment' ? 'bi-arrow-up-right' : 'bi-arrow-down-left'; ?>"></i>
                    <?php 
                    $typeText = [
                      'investment' => 'Investissement',
                      'return' => 'Retour',
                      'dividend' => 'Dividende'
                    ];
                    echo $typeText[$transaction['type']] ?? $transaction['type'];
                    ?>
                  </span>
                </td>
                <td><?php echo $transaction['project']; ?></td>
                <td style="font-weight: 600; color: <?php echo $transaction['amount'] < 0 ? '#EF4444' : '#10B981'; ?>;">
                  <?php echo $transaction['amount'] < 0 ? '' : '+'; ?><?php echo number_format(abs($transaction['amount']), 0, ',', ' '); ?> TND
                </td>
                <td>
                  <span class="investment-status status-<?php echo $transaction['status']; ?>">
                    <?php 
                    $statusText = [
                      'confirmed' => 'Confirmé',
                      'pending' => 'En attente',
                      'received' => 'Reçu'
                    ];
                    echo $statusText[$transaction['status']] ?? $transaction['status'];
                    ?>
                  </span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="content-card">
          <div class="section-title">
            <i class="bi bi-lightning"></i> Actions Rapides
          </div>

          <button class="btn-invest-more w-100 mb-3" onclick="window.location.href='projets-list.php'">
            <i class="bi bi-search me-2"></i> Explorer les Projets
          </button>

          <button class="btn-invest-more w-100 mb-3" id="exportReportBtn">
            <i class="bi bi-download me-2"></i> Télécharger le Rapport
          </button>

          <button class="btn-invest-more w-100" id="settingsBtn">
            <i class="bi bi-gear me-2"></i> Paramètres
          </button>
        </div>

        <!-- Investment Tips -->
        <div class="content-card">
          <div class="section-title">
            <i class="bi bi-lightbulb"></i> Conseils d'Investissement
          </div>

          <div style="background: var(--light-bg); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
            <h6 style="font-weight: 600; color: var(--dark-color); margin-bottom: 8px;">
              <i class="bi bi-check-circle" style="color: #10B981;"></i> Diversifiez votre portfolio
            </h6>
            <p style="font-size: 14px; color: #6B7280; margin: 0;">Répartissez vos investissements sur différents secteurs pour minimiser les risques.</p>
          </div>

          <div style="background: var(--light-bg); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
            <h6 style="font-weight: 600; color: var(--dark-color); margin-bottom: 8px;">
              <i class="bi bi-clock" style="color: var(--accent-color);"></i> Vision long terme
            </h6>
            <p style="font-size: 14px; color: #6B7280; margin: 0;">Les meilleurs retours viennent souvent des investissements à long terme.</p>
          </div>

          <div style="background: var(--light-bg); padding: 15px; border-radius: 10px;">
            <h6 style="font-weight: 600; color: var(--dark-color); margin-bottom: 8px;">
              <i class="bi bi-graph-up" style="color: var(--primary-color);"></i> Suivez vos projets
            </h6>
            <p style="font-size: 14px; color: #6B7280; margin: 0;">Restez informé de l'évolution de vos investissements régulièrement.</p>
          </div>
        </div>

        <!-- Top Performing Projects -->
        <div class="content-card">
          <div class="section-title">
            <i class="bi bi-star"></i> Meilleurs Rendements
          </div>

          <div id="topPerformersList">
            <?php 
            $topPerformers = array_slice($investments, 0, 3);
            foreach($topPerformers as $investment): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #E5E7EB;">
              <div>
                <div style="font-weight: 600; font-size: 14px;"><?php echo $investment['projectName']; ?></div>
                <div style="font-size: 12px; color: #6B7280;"><?php echo $investment['sector']; ?></div>
              </div>
              <div style="color: #10B981; font-weight: 700;">+<?php echo $investment['roi']; ?>%</div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer"></div>

  <!-- INVEST MODAL -->
  <div id="investModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); align-items:center; justify-content:center; z-index:2000;">
    <div style="width:520px; max-width:95%; background:white; border-radius:12px; padding:20px;">
      <h5 id="modalTitle" style="margin:0 0 10px 0;"></h5>
      <div style="font-size:13px; color:#6B7280; margin-bottom:12px;" id="modalMeta"></div>

      <div class="mb-2">
        <label style="font-size:13px;">Montant (TND)</label>
        <input id="investAmount" type="number" class="form-control" style="margin-bottom:10px;" />
        <div id="investHint" style="font-size:13px; color:#EF4444; display:none; margin-bottom:10px;"></div>
        <div style="font-size:13px; color:#6B7280;">Vous devrez compléter KYC avant tout transfert (simulé pour demo).</div>
      </div>

      <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:14px;">
        <button class="filter-tab" id="cancelInvest">Annuler</button>
        <button class="btn-invest-more" id="confirmInvest">Confirmer</button>
      </div>
    </div>
  </div>

  <!-- CREATE TENDER MODAL -->
  <div id="createTenderModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); align-items:center; justify-content:center; z-index:2000; overflow-y: auto; padding: 20px 0;">
    <div style="width:700px; max-width:95%; background:white; border-radius:12px; padding:30px; max-height: 90vh; overflow-y: auto;">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 style="margin:0;"><i class="bi bi-plus-circle me-2"></i> Créer un Appel d'Offres</h4>
        <button class="filter-tab" id="cancelCreateTender">Annuler</button>
      </div>

      <form id="tenderForm" method="POST" action="controller.php">
        <input type="hidden" name="action" value="create_tender">
        
        <div class="form-group">
          <label class="form-label">Nom du Projet *</label>
          <input type="text" class="form-control" id="projectName" name="projectName" required placeholder="Ex: Plateforme IA pour l'agriculture">
          <div class="form-text">Un nom clair et attractif pour votre projet</div>
        </div>

        <div class="form-group">
          <label class="form-label">Pitch Court *</label>
          <textarea class="form-control" id="shortPitch" name="shortPitch" rows="2" required placeholder="Décrivez brièvement votre projet en une ou deux phrases"></textarea>
          <div class="form-text">Cette description apparaîtra dans la liste des appels d'offres</div>
        </div>

        <div class="form-group">
          <label class="form-label">Secteur *</label>
          <select class="form-control" id="projectSector" name="sector" required>
            <option value="">Sélectionnez un secteur</option>
            <option value="IoT">IoT</option>
            <option value="AI">Intelligence Artificielle</option>
            <option value="Blockchain">Blockchain</option>
            <option value="Health">Santé</option>
            <option value="Energy">Énergie</option>
            <option value="Education">Éducation</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Type d'Offre *</label>
          <select class="form-control" id="offerType" name="offerType" required>
            <option value="">Sélectionnez un type</option>
            <option value="Equity">Capital (Equity)</option>
            <option value="Convertible">