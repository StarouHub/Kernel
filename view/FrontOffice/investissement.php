<?php
// C:\xampp\htdocs\projetweb\Kernel\view\Frontoffice\investissement.php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include controller
$controllerPath = __DIR__ . '/../../controller/controller.php';

if (!file_exists($controllerPath)) {
    die("Erreur: Fichier controller non trouvé à: $controllerPath");
}

include $controllerPath;

// Create controller instance
$controller = new InvestmentController();

// Get data from controller
$investments = $controller->getInvestments();
$tenders = $controller->getTenders();
$portfolio = $controller->getPortfolio();
$transactions = $controller->getTransactions();

// Debug info (remove in production)
error_log("Investments: " . count($investments) . ", Tenders: " . count($tenders) . ", Transactions: " . count($transactions));
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

  <link href="stylee.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    /* Back Button Styles - Fixed position top left */
    .back-button {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      background: linear-gradient(135deg, #dc3545, #c82333);
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(220, 53, 69, 0.25);
      text-decoration: none;
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 9999;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }

    .back-button:hover {
      background: linear-gradient(135deg, #c82333, #bd2130);
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(220, 53, 69, 0.35);
      color: white;
    }

    .back-button:active {
      transform: translateY(0);
      box-shadow: 0 2px 8px rgba(220, 53, 69, 0.25);
    }

    .back-button i {
      font-size: 16px;
      transition: transform 0.3s ease;
    }

    .back-button:hover i {
      transform: translateX(-3px);
    }

    /* Adjust header for back button */
    .header {
      position: relative;
      z-index: 999;
    }

    /* Adjust page header padding */
    .page-header {
      padding-top: 100px;
      position: relative;
    }

    /* Mobile adjustments */
    @media (max-width: 768px) {
      .back-button {
        top: 15px;
        left: 15px;
        padding: 10px 15px;
      }
      
      .back-button span {
        display: none;
      }
      
      .back-button i {
        font-size: 18px;
        margin: 0;
      }
      
      .page-header {
        padding-top: 80px;
      }
    }

    @media (max-width: 576px) {
      .back-button {
        top: 10px;
        left: 10px;
        padding: 8px 12px;
        font-size: 0;
      }
      
      .back-button i {
        font-size: 20px;
      }
    }
  </style>
</head>

<body>
  <!-- Back Button - Fixed position -->
  <a href="index.php" class="back-button">
    <i class="bi bi-arrow-left"></i>
    <span>Retour à l'accueil</span>
  </a>

  <!-- Header -->
  <header class="header d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo">
        <i class="bi bi-hexagon-fill"></i> Kernel
      </a>

      <nav class="navmenu">
        <ul>
          <li><a href="index.php">Accueil</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Page Header -->
  <div class="page-header">
    <div class="container">
      <h1><i class="bi bi-cash-coin"></i> Mes Investissements</h1>
      <p>Gérez et suivez vos investissements et les appels d'offres publiés par les porteurs de projet.</p>
    </div>
  </div>

  <!-- Main Content -->
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

    <!-- Main Content Grid -->
    <div class="row">
      <div class="col-lg-8">
        <!-- Portfolio Chart -->
        <div class="content-card">
          <div class="section-title">
            <i class="bi bi-graph-up"></i> Évolution du Portfolio
          </div>
          <div style="height: 300px; position: relative;">
            <canvas id="portfolioChart"></canvas>
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
            <?php if (count($investments) > 0): ?>
              <?php foreach ($investments as $investment): ?>
                <div class="investment-item">
                  <img src="<?php echo $investment['thumbnail']; ?>" class="project-thumb" alt="<?php echo htmlspecialchars($investment['projectName']); ?>">
                  <div class="investment-details">
                    <div class="project-name"><?php echo htmlspecialchars($investment['projectName']); ?></div>
                    <div class="investment-meta">
                      <span><i class="bi bi-calendar"></i> Investi le <?php echo $investment['date']; ?></span>
                      <span><i class="bi bi-percent"></i> ROI: +<?php echo $investment['roi']; ?>%</span>
                      <span><i class="bi bi-tag"></i> <?php echo $investment['sector']; ?></span>
                    </div>
                    <span class="investment-status status-<?php echo $investment['status']; ?>">
                      <?php echo $investment['statusText'] ?? ($investment['status'] === 'active' ? 'En cours' : 'Financé'); ?>
                    </span>
                  </div>
                  <div class="investment-amount"><?php echo number_format($investment['amount'], 0, ',', ' '); ?> TND</div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div style="text-align:center; padding:20px; color:#6B7280;">Aucun investissement trouvé</div>
            <?php endif; ?>
          </div>

          <button class="btn-invest-more w-100 mt-3" onclick="window.location.href='projets-list.html'">
            <i class="bi bi-plus-circle me-2"></i> Découvrir plus de projets
          </button>
        </div>

        <!-- Appels d'Offres Section -->
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
            <?php if (count($tenders) > 0): ?>
              <?php foreach ($tenders as $tender): 
                $progress = $tender['progress'] ?? round(($tender['raised'] / $tender['fundingTarget']) * 100);
                $daysLeft = $tender['daysLeft'] ?? 0;
              ?>
                <div class="investment-item" style="justify-content: space-between;">
                  <div style="display:flex; gap:12px; align-items:center;">
                    <div style="width:64px; height:48px; background:linear-gradient(135deg,#2563EB,#7C3AED); border-radius:8px; color:white; display:flex; align-items:center; justify-content:center; font-weight:700;">
                      <?php echo substr($tender['sector'], 0, 3); ?>
                    </div>
                    <div style="min-width:200px;">
                      <div style="font-weight:600;"><?php echo htmlspecialchars($tender['projectName']); ?></div>
                      <div style="font-size:13px; color:#6B7280;"><?php echo htmlspecialchars($tender['shortPitch']); ?></div>
                      <div class="progress-container">
                        <div class="progress-bar" style="width: <?php echo $progress; ?>%"></div>
                      </div>
                      <div style="margin-top:6px; font-size:13px;">
                        <span style="font-weight:700; color:#2563EB;"><?php echo number_format($tender['raised'], 0, ',', ' '); ?> TND</span>
                        <span style="color:#6B7280;"> / <?php echo number_format($tender['fundingTarget'], 0, ',', ' '); ?> TND</span>
                        <span style="margin-left:10px;" class="status-open">Ouvert</span>
                      </div>
                    </div>
                  </div>
                  <div style="display:flex; align-items:center; gap:12px;">
                    <div style="text-align:right; font-weight:700;">Min <?php echo number_format($tender['minInvestment'], 0, ',', ' '); ?> TND</div>
                    <div style="text-align:right; font-size:13px; color:#6B7280;">Clôture: <?php echo $daysLeft; ?> j</div>
                    <div>
                      <button class="filter-tab" onclick="investmentPlatform.showTenderDetails(<?php echo $tender['id']; ?>)">Voir</button>
                      <button class="btn-invest-more" data-id="<?php echo $tender['id']; ?>" style="margin-left:8px;">Investir</button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div style="text-align:center; padding:20px; color:#6B7280;">Aucun appel d'offres trouvé</div>
            <?php endif; ?>
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
              <!-- Transactions loaded by JavaScript -->
            </tbody>
          </table>
          
          <div class="text-center mt-3">
            <button class="filter-tab" id="refreshTransactionsBtn">
              <i class="bi bi-arrow-clockwise"></i> Actualiser
            </button>
            <button class="filter-tab" id="clearTransactionsBtn" style="margin-left: 10px; border-color: #EF4444; color: #EF4444;">
              <i class="bi bi-trash"></i> Effacer l'historique
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="content-card">
          <div class="section-title">
            <i class="bi bi-lightning"></i> Actions Rapides
          </div>

          <button class="btn-invest-more w-100 mb-3" onclick="window.location.href='projets-list.html'">
            <i class="bi bi-search me-2"></i> Explorer les Projets
          </button>

          <button class="btn-invest-more w-100 mb-3" id="exportReportBtn">
            <i class="bi bi-download me-2"></i> Télécharger le Rapport
          </button>

          <button class="btn-invest-more w-100 mb-3" id="testConnectionBtn">
            <i class="bi bi-wrench me-2"></i> Tester Connexion
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
        
        <!-- Top Performers -->
        <div class="content-card">
          <div class="section-title">
            <i class="bi bi-star"></i> Meilleurs Rendements
          </div>
          <div id="topPerformersList">
            <?php 
            $topInvestments = array_slice($investments, 0, 3);
            foreach ($topInvestments as $investment): 
            ?>
              <div style="background: var(--light-bg); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                  <h6 style="font-weight: 600; color: var(--dark-color); margin: 0; flex: 1;"><?php echo htmlspecialchars($investment['projectName']); ?></h6>
                  <span style="background: #10B981; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                    +<?php echo $investment['roi']; ?>%
                  </span>
                </div>
                <div style="font-size: 14px; color: #6B7280;">
                  Investi: <?php echo number_format($investment['amount'], 0, ',', ' '); ?> TND
                </div>
                <div style="font-size: 12px; color: #6B7280; margin-top: 5px;">
                  <?php echo $investment['sector']; ?> • <?php echo $investment['date']; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer"></div>

  <!-- Invest Modal -->
  <div id="investModal" class="modal-overlay">
    <div class="modal-content">
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

  <!-- Create Tender Modal -->
  <div id="createTenderModal" class="modal-overlay">
    <div class="modal-content-large">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 style="margin:0;"><i class="bi bi-plus-circle me-2"></i> Créer un Appel d'Offres</h4>
        <button class="filter-tab" id="cancelCreateTender">Annuler</button>
      </div>

      <form id="tenderForm">
        <div class="form-group">
          <label class="form-label">Nom du Projet *</label>
          <input type="text" class="form-control" id="projectName" required placeholder="Ex: Plateforme IA pour l'agriculture">
          <div class="form-text">Un nom clair et attractif pour votre projet</div>
          <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
          <label class="form-label">Pitch Court *</label>
          <textarea class="form-control" id="shortPitch" rows="2" required placeholder="Décrivez brièvement votre projet en une ou deux phrases"></textarea>
          <div class="form-text">Cette description apparaîtra dans la liste des appels d'offres</div>
          <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
          <label class="form-label">Description Détaillée</label>
          <textarea class="form-control" id="projectDescription" rows="4" placeholder="Décrivez en détail votre projet, ses objectifs et sa valeur ajoutée"></textarea>
          <div class="form-text">Les investisseurs verront cette description complète</div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Secteur *</label>
              <select class="form-control" id="projectSector" required>
                <option value="">Sélectionnez un secteur</option>
                <option value="IoT">IoT</option>
                <option value="AI">Intelligence Artificielle</option>
                <option value="Blockchain">Blockchain</option>
                <option value="Health">Santé</option>
                <option value="Energy">Énergie</option>
                <option value="Education">Éducation</option>
                <option value="E-commerce">E-commerce</option>
                <option value="Fintech">Fintech</option>
                <option value="Other">Autre</option>
              </select>
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Type d'Offre *</label>
              <select class="form-control" id="offerType" required>
                <option value="">Sélectionnez un type</option>
                <option value="Equity">Capital (Equity)</option>
                <option value="Convertible">Prêt Convertible</option>
                <option value="Reward">Récompenses</option>
                <option value="Donation">Don</option>
                <option value="Loan">Prêt</option>
              </select>
              <div class="invalid-feedback"></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Montant Cible (TND) *</label>
              <input type="number" class="form-control" id="fundingTarget" required min="1000" placeholder="Ex: 50000">
              <div class="form-text">Le montant total que vous souhaitez lever</div>
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">ROI Estimé (%) *</label>
              <input type="number" class="form-control" id="expectedROI" required min="1" max="100" step="0.1" placeholder="Ex: 15.5">
              <div class="form-text">Le retour sur investissement estimé pour les investisseurs</div>
              <div class="invalid-feedback"></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Investissement Minimum (TND) *</label>
              <input type="number" class="form-control" id="minInvestment" required min="100" placeholder="Ex: 500">
              <div class="form-text">Le montant minimum qu'un investisseur peut investir</div>
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Investissement Maximum (TND)</label>
              <input type="number" class="form-control" id="maxInvestment" placeholder="Ex: 20000">
              <div class="form-text">Laissez vide si pas de limite</div>
              <div class="invalid-feedback"></div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Date de Clôture *</label>
          <input type="date" class="form-control" id="deadline" required>
          <div class="form-text">Date limite pour recevoir des investissements</div>
          <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
          <label class="form-label">Logo du Projet (URL)</label>
          <input type="url" class="form-control" id="projectLogo" placeholder="https://example.com/logo.png">
          <div class="form-text">Lien vers une image pour représenter votre projet</div>
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
          <button type="button" class="filter-tab" id="cancelTenderForm">Annuler</button>
          <button type="submit" class="btn-create-tender">Publier l'Appel d'Offres</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Pass PHP data to JavaScript -->
  <script>
    window.tendersData = <?php echo json_encode($tenders ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    window.investmentsData = <?php echo json_encode($investments ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    window.transactionsData = <?php echo json_encode($transactions ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    window.userId = 1;
    window.baseUrl = '../../controller/controller.php';

    console.log('Application data loaded:', {
        tenders: window.tendersData.length,
        investments: window.investmentsData.length,
        transactions: window.transactionsData.length
    });
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script src="scripta.js"></script>
</body>
</html>