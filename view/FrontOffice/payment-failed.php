<?php
// Payment Failed Page
error_reporting(E_ALL);
ini_set('display_errors', 1);

$tenderId = $_GET['tender_id'] ?? 0;
$amount = $_GET['amount'] ?? 0;
$projectName = $_GET['project'] ?? 'Projet';
$error = $_GET['error'] ?? 'Une erreur est survenue lors du paiement';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Échoué - Kernel</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Raleway:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="stylee.css" rel="stylesheet">
    <link href="payment.css" rel="stylesheet">
</head>
<body style="padding-top: 0;">
    <header class="header d-flex align-items-center" style="position: relative;">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="investissement.php" class="logo">
                <i class="bi bi-hexagon-fill"></i> Kernel
            </a>
        </div>
    </header>

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            
            <h1 style="color: #EF4444;">Paiement Échoué</h1>
            <p class="success-message">Votre paiement n'a pas pu être traité</p>

            <div class="receipt" style="border-color: #FEE2E2; background: #FEF2F2;">
                <div class="receipt-header" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>Détails de l'erreur</span>
                </div>
                
                <div class="receipt-body">
                    <div class="receipt-row"><span>Projet</span><strong><?php echo htmlspecialchars($projectName); ?></strong></div>
                    <div class="receipt-row"><span>Montant</span><strong><?php echo number_format($amount, 0, ',', ' '); ?> TND</strong></div>
                    <div class="receipt-divider"></div>
                    <div class="receipt-row"><span>Erreur</span><strong style="color: #EF4444;"><?php echo htmlspecialchars($error); ?></strong></div>
                </div>
            </div>

            <div class="success-actions">
                <a href="payment.php?tender_id=<?php echo $tenderId; ?>&amount=<?php echo $amount; ?>&project=<?php echo urlencode($projectName); ?>" class="btn-invest-more" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    <i class="bi bi-arrow-repeat"></i> Réessayer le paiement
                </a>
                <a href="investissement.php" class="btn btn-outline-secondary">
                    <i class="bi bi-house-fill"></i> Retour à l'accueil
                </a>
            </div>

            <div class="email-notice" style="background: #FEF2F2; color: #B91C1C;">
                <i class="bi bi-question-circle-fill"></i>
                <span>Besoin d'aide? Contactez notre support</span>
            </div>
        </div>
    </div>
</body>
</html>
