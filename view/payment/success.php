<?php
$pageTitle = 'Paiement réussi';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
  .success-container {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
  }
  
  .success-card {
    background: white;
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
  }
  
  .success-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #10B981, #059669);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 40px;
    color: white;
  }
  
  .success-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 15px;
  }
  
  .success-message {
    color: #6B7280;
    font-size: 16px;
    margin-bottom: 30px;
    line-height: 1.6;
  }
  
  .payment-details {
    background: #F9FAFB;
    padding: 20px;
    border-radius: 10px;
    margin: 30px 0;
    text-align: left;
  }
  
  .detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #E5E7EB;
  }
  
  .detail-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
  }
  
  .detail-label {
    color: #6B7280;
    font-weight: 500;
  }
  
  .detail-value {
    color: var(--dark-color);
    font-weight: 600;
  }
  
  .btn-group {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 30px;
  }
  
  .btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 12px 24px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
  }
  
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    color: white;
  }
  
  .btn-secondary {
    background: white;
    color: var(--primary-color);
    padding: 12px 24px;
    border: 2px solid var(--primary-color);
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
  }
  
  .btn-secondary:hover {
    background: #F0F9FF;
    color: var(--primary-color);
  }
  
  .info-box {
    background: #DBEAFE;
    border-left: 4px solid #2563EB;
    padding: 15px;
    border-radius: 5px;
    margin-top: 20px;
    text-align: left;
  }
  
  .info-box i {
    color: #2563EB;
    margin-right: 8px;
  }
</style>

<div class="container">
  <div class="success-container">
    <div class="success-card">
      <div class="success-icon">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      
      <h1 class="success-title">Paiement réussi !</h1>
      
      <p class="success-message">
        Votre paiement a été traité avec succès. Votre inscription à l'événement 
        <strong><?php echo htmlspecialchars($evenement['titre']); ?></strong> est confirmée.
      </p>
      
      <div class="payment-details">
        <h3 style="margin-bottom: 20px; color: var(--dark-color);">Détails du paiement</h3>
        
        <div class="detail-row">
          <span class="detail-label">Montant payé:</span>
          <span class="detail-value"><?php echo number_format($payment['amount'], 2, ',', ' '); ?> €</span>
        </div>
        
        <div class="detail-row">
          <span class="detail-label">Méthode de paiement:</span>
          <span class="detail-value"><?php echo strtoupper($payment['payment_method']); ?></span>
        </div>
        
        <div class="detail-row">
          <span class="detail-label">Transaction ID:</span>
          <span class="detail-value" style="font-size: 12px;"><?php echo htmlspecialchars($payment['transaction_id'] ?? 'N/A'); ?></span>
        </div>
        
        <div class="detail-row">
          <span class="detail-label">Date du paiement:</span>
          <span class="detail-value"><?php echo date('d/m/Y à H:i', strtotime($payment['created_at'])); ?></span>
        </div>
      </div>
      
      <div class="info-box">
        <i class="bi bi-envelope-check"></i>
        <strong>Email envoyé</strong><br>
        Votre facture et votre confirmation d'inscription avec QR code ont été envoyées à votre adresse email.
      </div>
      
      <div class="btn-group">
        <a href="index.php?action=details&id=<?php echo $payment['id_evenement']; ?>" class="btn-primary">
          <i class="bi bi-calendar-event"></i> Voir l'événement
        </a>
        <a href="index.php" class="btn-secondary">
          <i class="bi bi-house"></i> Retour à l'accueil
        </a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
