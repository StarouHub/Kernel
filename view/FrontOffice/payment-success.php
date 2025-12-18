<?php
// filepath: c:\xampp\htdocs\projetweb\Kernel\view\FrontOffice\payment-success.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../controller/controller.php';

// PHPMailer
require_once __DIR__ . '/../../lib/PHPMailer-6.9.1/src/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer-6.9.1/src/SMTP.php';
require_once __DIR__ . '/../../lib/PHPMailer-6.9.1/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

$tenderId = $_GET['tender_id'] ?? 0;
$amount = $_GET['amount'] ?? 0;
$projectName = $_GET['project'] ?? 'Projet';
$method = $_GET['method'] ?? 'stripe';
$paymentId = $_GET['payment_id'] ?? '';
$status = $_GET['status'] ?? '';
$customerEmail = $_GET['email'] ?? '';

// Generate transaction ID if not from Stripe
$transactionId = $paymentId ?: 'TXN-' . strtoupper(substr(md5(time()), 0, 8));
$transactionDate = date('d/m/Y H:i');
$totalAmount = $amount * 1.015; // Including 1.5% fees

// ALWAYS save transaction and investment to database
$controller = new InvestmentController();
$saveResult = null;
$saveError = '';

try {
    $saveResult = $controller->createInvestment([
        'tenderId' => $tenderId,
        'projectName' => $projectName,
        'amount' => $amount,
        'roi' => 15,
        'sector' => 'Investissement',
        'paymentId' => $transactionId
    ]);
} catch (Exception $e) {
    $saveError = $e->getMessage();
}

// Send email receipt if email provided
$emailSent = false;
$emailError = '';

if (!empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    // Build HTML email
    $subject = "Reçu de paiement - Kernel Investment #" . $transactionId;
    
    $emailBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 30px; text-align: center; border-radius: 12px 12px 0 0; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 12px 12px; }
            .receipt-box { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            .receipt-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
            .receipt-row:last-child { border-bottom: none; }
            .receipt-row.total { font-weight: bold; font-size: 18px; color: #10B981; border-top: 2px solid #10B981; margin-top: 10px; padding-top: 15px; }
            .success-icon { font-size: 48px; color: #10B981; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>⬡ Kernel Investment</h1>
                <p>Reçu de paiement</p>
            </div>
            <div class="content">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div class="success-icon">✓</div>
                    <h2 style="color: #10B981; margin: 10px 0;">Paiement Réussi!</h2>
                    <p>Merci pour votre investissement</p>
                </div>
                
                <div class="receipt-box">
                    <div class="receipt-row">
                        <span>Transaction ID</span>
                        <strong>' . htmlspecialchars($transactionId) . '</strong>
                    </div>
                    <div class="receipt-row">
                        <span>Date</span>
                        <strong>' . $transactionDate . '</strong>
                    </div>
                    <div class="receipt-row">
                        <span>Projet</span>
                        <strong>' . htmlspecialchars($projectName) . '</strong>
                    </div>
                    <div class="receipt-row">
                        <span>Méthode de paiement</span>
                        <strong>Stripe (Carte Bancaire)</strong>
                    </div>
                    <div class="receipt-row">
                        <span>Montant investi</span>
                        <strong>' . number_format($amount, 0, ',', ' ') . ' TND</strong>
                    </div>
                    <div class="receipt-row">
                        <span>Frais de plateforme (1.5%)</span>
                        <strong>' . number_format($amount * 0.015, 2, ',', ' ') . ' TND</strong>
                    </div>
                    <div class="receipt-row total">
                        <span>Total payé</span>
                        <strong>' . number_format($totalAmount, 2, ',', ' ') . ' TND</strong>
                    </div>
                </div>
                
                <p style="text-align: center; color: #666;">
                    Conservez ce reçu pour vos dossiers.<br>
                    Pour toute question, contactez notre support.
                </p>
            </div>
            <div class="footer">
                <p>© ' . date('Y') . ' Kernel Investment Platform. Tous droits réservés.</p>
                <p>Ceci est un reçu automatique, merci de ne pas répondre à cet email.</p>
            </div>
        </div>
    </body>
    </html>';
    
    // Send email using PHPMailer
    try {
        $mail = new PHPMailer(true);
        
        // SMTP Configuration for Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'alibichiou24@gmail.com';
        $mail->Password = 'ixoz kbdc mukk jxqm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Recipients
        $mail->setFrom('alibichiou24@gmail.com', 'Kernel Investment');
        $mail->addAddress($customerEmail);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $emailBody;
        $mail->AltBody = "Reçu de paiement - Transaction: $transactionId - Montant: $amount TND";
        
        $mail->send();
        $emailSent = true;
    } catch (PHPMailerException $e) {
        $emailSent = false;
        $emailError = $mail->ErrorInfo;
    }
}

$methodNames = [
    'card' => 'Carte Bancaire',
    'bank' => 'Virement Bancaire',
    'mobile' => 'Paiement Mobile',
    'moyasar' => 'Moyasar (Carte Bancaire)',
    'stripe' => 'Stripe (Carte Bancaire)'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi - Kernel</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Raleway:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
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
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            
            <h1>Paiement Réussi!</h1>
            <p class="success-message">Votre investissement a été confirmé avec succès</p>

            <?php if ($saveResult && isset($saveResult['success']) && $saveResult['success']): ?>
            <div class="alert alert-success" style="margin: 10px 0; padding: 10px; border-radius: 8px; background: #D1FAE5; color: #065F46;">
                <i class="bi bi-check-circle"></i> Transaction enregistrée dans l'historique
            </div>
            <?php elseif ($saveError || ($saveResult && isset($saveResult['success']) && !$saveResult['success'])): ?>
            <div class="alert alert-warning" style="margin: 10px 0; padding: 10px; border-radius: 8px; background: #FEF3C7; color: #92400E;">
                <i class="bi bi-exclamation-triangle"></i> Note: <?php echo $saveError ?: ($saveResult['error'] ?? 'Erreur lors de l\'enregistrement'); ?>
            </div>
            <?php endif; ?>

            <div class="receipt">
                <div class="receipt-header">
                    <i class="bi bi-receipt"></i>
                    <span>Reçu de transaction</span>
                </div>
                
                <div class="receipt-body">
                    <div class="receipt-row"><span>Transaction ID</span><strong><?php echo $transactionId; ?></strong></div>
                    <div class="receipt-row"><span>Date</span><strong><?php echo date('d/m/Y H:i'); ?></strong></div>
                    <div class="receipt-row"><span>Projet</span><strong><?php echo htmlspecialchars($projectName); ?></strong></div>
                    <div class="receipt-row"><span>Méthode</span><strong><?php echo $methodNames[$method] ?? 'Carte'; ?></strong></div>
                    <div class="receipt-row"><span>Montant investi</span><strong><?php echo number_format($amount, 0, ',', ' '); ?> TND</strong></div>
                    <div class="receipt-row"><span>Frais</span><strong><?php echo number_format($amount * 0.015, 2, ',', ' '); ?> TND</strong></div>
                    <div class="receipt-divider"></div>
                    <div class="receipt-row total"><span>Total payé</span><strong><?php echo number_format($amount * 1.015, 2, ',', ' '); ?> TND</strong></div>
                </div>
            </div>

            <div class="success-actions">
                <a href="investissement.php" class="btn-invest-more">
                    <i class="bi bi-briefcase-fill"></i> Voir mes investissements
                </a>
                <button class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer-fill"></i> Imprimer
                </button>
            </div>

            <?php if (!empty($customerEmail)): ?>
            <div class="email-notice" style="<?php echo $emailSent ? 'background: #D1FAE5; color: #065F46;' : 'background: #FEF3C7; color: #92400E;'; ?>">
                <?php if ($emailSent): ?>
                <i class="bi bi-envelope-check-fill"></i>
                <span>Reçu envoyé à <strong><?php echo htmlspecialchars($customerEmail); ?></strong></span>
                <?php else: ?>
                <i class="bi bi-envelope-exclamation-fill"></i>
                <span>Le reçu n'a pas pu être envoyé à <?php echo htmlspecialchars($customerEmail); ?></span>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="email-notice" style="background: #f3f4f6; color: #6b7280;">
                <i class="bi bi-envelope"></i>
                <span>Aucun email fourni pour le reçu</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>