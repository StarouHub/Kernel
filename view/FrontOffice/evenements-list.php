<?php
include_once(__DIR__ . '/../components/main-navigation.php');
include_once(__DIR__ . '/../components/chatbot-widget.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Événements - Kernel</title>
    
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
        
        .coming-soon {
            text-align: center;
            padding: 100px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .coming-soon i {
            font-size: 120px;
            color: var(--accent-color);
            margin-bottom: 30px;
        }
        
        .coming-soon h2 {
            color: var(--dark-color);
            margin-bottom: 20px;
            font-family: 'Raleway', sans-serif;
        }
        
        .coming-soon p {
            color: #6B7280;
            font-size: 18px;
            margin-bottom: 30px;
        }
        
        .btn-back {
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #1D4ED8;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <?php echo renderMainNavigation('evenements'); ?>
    <?php echo renderChatbotWidget(); ?>
    
    <div class="page-header">
        <div class="container">
            <h1><i class="bi bi-calendar-event"></i> Événements</h1>
            <p>Découvrez les événements tech et innovation</p>
        </div>
    </div>
    
    <div class="container">
        <div class="coming-soon">
            <i class="bi bi-calendar-event"></i>
            <h2>Événements à venir</h2>
            <p>La section événements sera bientôt disponible avec des conférences, workshops et meetups dédiés à l'innovation.</p>
            <a href="index.php" class="btn-back">
                <i class="bi bi-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>