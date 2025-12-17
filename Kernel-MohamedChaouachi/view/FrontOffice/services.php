<?php
include_once(__DIR__ . '/../components/office-switch.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Services Kernel - Chatbot & Mailing</title>
  
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #2563EB;
      --secondary-color: #7C3AED;
      --dark-color: #1F2937;
      --light-bg: #F9FAFB;
    }
    
    body {
      font-family: 'Roboto', sans-serif;
      background: var(--light-bg);
      padding-top: 80px;
      padding-bottom: 50px;
    }
    
    .header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      padding: 15px 0;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .logo {
      font-size: 28px;
      font-weight: 700;
      color: white;
      text-decoration: none;
      font-family: 'Raleway', sans-serif;
    }
    
    .hero {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 80px 0;
      text-align: center;
      margin-bottom: 50px;
    }
    
    .hero h1 {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 20px;
    }
    
    .service-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      margin-bottom: 30px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.08);
      transition: all 0.3s;
      height: 100%;
    }
    
    .service-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .service-icon {
      font-size: 72px;
      margin-bottom: 25px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .service-card h2 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 15px;
      color: var(--dark-color);
    }
    
    .service-card p {
      color: #6B7280;
      line-height: 1.8;
      margin-bottom: 20px;
    }
    
    .feature-list {
      list-style: none;
      padding: 0;
      margin: 20px 0;
    }
    
    .feature-list li {
      padding: 10px 0;
      color: #4B5563;
      border-bottom: 1px solid #E5E7EB;
    }
    
    .feature-list li:last-child {
      border-bottom: none;
    }
    
    .feature-list i {
      color: var(--primary-color);
      margin-right: 10px;
    }
    
    .btn-service {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 15px 40px;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      display: inline-block;
      transition: all 0.3s;
      border: none;
    }
    
    .btn-service:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
      color: white;
    }
    
    .btn-test {
      background: white;
      color: var(--primary-color);
      border: 2px solid var(--primary-color);
      padding: 12px 30px;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      display: inline-block;
      transition: all 0.3s;
      margin-left: 10px;
    }
    
    .btn-test:hover {
      background: var(--primary-color);
      color: white;
    }
    
    .stats-section {
      background: white;
      padding: 60px 0;
      margin: 50px 0;
      border-radius: 20px;
    }
    
    .stat-item {
      text-align: center;
      padding: 20px;
    }
    
    .stat-number {
      font-size: 48px;
      font-weight: 700;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .stat-label {
      color: #6B7280;
      font-size: 16px;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <?php echo renderOfficeSwitch('front', 'actualite'); ?>
  <?php echo renderChatbotWidget(); ?>
  
  <header class="header d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo">
        <i class="bi bi-hexagon-fill"></i> Kernel
      </a>
      <div class="d-flex gap-2">
        <a class="btn btn-light" href="listeprojet.php">
          <i class="bi bi-folder me-2"></i> Projets
        </a>
        <a class="btn btn-light" href="listeActualite.php">
          <i class="bi bi-newspaper me-2"></i> Actualités
        </a>
      </div>
    </div>
  </header>

  <div class="hero">
    <div class="container">
      <h1>🚀 Services Kernel</h1>
      <p class="lead">Découvrez nos services intelligents pour améliorer votre expérience</p>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <!-- Chatbot Service -->
      <div class="col-md-6">
        <div class="service-card">
          <div class="service-icon">
            <i class="bi bi-robot"></i>
          </div>
          <h2>Assistant Virtuel Chatbot</h2>
          <p>
            Un assistant intelligent qui répond à vos questions sur les projets et actualités 
            en temps réel. Basé sur les données de la plateforme, il vous aide à trouver 
            rapidement l'information dont vous avez besoin.
          </p>
          
          <ul class="feature-list">
            <li><i class="bi bi-check-circle-fill"></i> Recherche de projets par mots-clés</li>
            <li><i class="bi bi-check-circle-fill"></i> Informations sur les actualités</li>
            <li><i class="bi bi-check-circle-fill"></i> Statistiques et budgets</li>
            <li><i class="bi bi-check-circle-fill"></i> Navigation par catégories</li>
            <li><i class="bi bi-check-circle-fill"></i> Réponses en langage naturel</li>
          </ul>
          
          <div class="mt-4">
            <a href="chatbot.php" class="btn-service">
              <i class="bi bi-chat-dots me-2"></i> Essayer le Chatbot
            </a>
            <a href="../../test_chatbot.php" class="btn-test" target="_blank">
              <i class="bi bi-flask me-2"></i> Tester
            </a>
          </div>
        </div>
      </div>

      <!-- Mailing Service -->
      <div class="col-md-6">
        <div class="service-card">
          <div class="service-icon">
            <i class="bi bi-envelope-at"></i>
          </div>
          <h2>Système de Mailing</h2>
          <p>
            Restez informé des dernières actualités ! Notre système de notifications par email 
            vous alerte automatiquement des nouvelles publications et mises à jour des projets 
            que vous suivez.
          </p>
          
          <ul class="feature-list">
            <li><i class="bi bi-check-circle-fill"></i> Notifications nouvelles actualités</li>
            <li><i class="bi bi-check-circle-fill"></i> Alertes de mises à jour</li>
            <li><i class="bi bi-check-circle-fill"></i> Digest hebdomadaire personnalisé</li>
            <li><i class="bi bi-check-circle-fill"></i> Templates HTML modernes</li>
            <li><i class="bi bi-check-circle-fill"></i> Mode simulation pour tests</li>
          </ul>
          
          <div class="mt-4">
            <a href="mailing.php" class="btn-service">
              <i class="bi bi-send me-2"></i> Gérer les Notifications
            </a>
            <a href="../../test_mailing.php" class="btn-test" target="_blank">
              <i class="bi bi-flask me-2"></i> Tester
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-section">
      <div class="container">
        <h2 class="text-center mb-5">📊 Statistiques des Services</h2>
        <div class="row">
          <div class="col-md-3">
            <div class="stat-item">
              <div class="stat-number">8+</div>
              <div class="stat-label">Commandes Chatbot</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-item">
              <div class="stat-number">3</div>
              <div class="stat-label">Types de Notifications</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-item">
              <div class="stat-number">100%</div>
              <div class="stat-label">Temps Réel</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-item">
              <div class="stat-number">∞</div>
              <div class="stat-label">Évolutif</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Documentation -->
    <div class="row mt-5">
      <div class="col-12">
        <div class="service-card">
          <h2><i class="bi bi-book me-2"></i> Documentation</h2>
          <p>
            Pour en savoir plus sur l'utilisation et la configuration de ces services, 
            consultez notre documentation complète.
          </p>
          <a href="../../SERVICES_README.md" target="_blank" class="btn-service">
            <i class="bi bi-file-text me-2"></i> Lire la Documentation
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
