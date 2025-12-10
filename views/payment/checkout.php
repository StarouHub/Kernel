<?php
$pageTitle = 'Paiement';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
  .checkout-container {
    max-width: 800px;
    margin: 0 auto;
  }
  
  .checkout-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
  }
  
  .checkout-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #E5E7EB;
  }
  
  .checkout-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 10px;
  }
  
  .event-summary {
    background: #F9FAFB;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
  }
  
  .summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #E5E7EB;
  }
  
  .summary-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
  }
  
  .summary-label {
    color: #6B7280;
    font-weight: 500;
  }
  
  .summary-value {
    color: var(--dark-color);
    font-weight: 600;
  }
  
  .price-total {
    font-size: 24px;
    color: var(--primary-color);
    font-weight: 700;
  }
  
  .payment-methods {
    margin-top: 30px;
  }
  
  .payment-method {
    border: 2px solid #E5E7EB;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .payment-method:hover {
    border-color: var(--primary-color);
    background: #F0F9FF;
  }
  
  .payment-method input[type="radio"] {
    margin-right: 10px;
  }
  
  .payment-method.selected {
    border-color: var(--primary-color);
    background: #F0F9FF;
  }
  
  .payment-method-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: var(--dark-color);
  }
  
  .payment-method-icon {
    margin-right: 10px;
    font-size: 24px;
  }
  
  .btn-pay {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 20px;
  }
  
  .btn-pay:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(37, 99, 235, 0.4);
  }
  
  .security-badge {
    text-align: center;
    margin-top: 20px;
    color: #6B7280;
    font-size: 14px;
  }
  
  .security-badge i {
    color: #10B981;
    margin-right: 5px;
  }
</style>

<div class="container">
  <div class="checkout-container">
    <div class="checkout-card">
      <div class="checkout-header">
        <h1><i class="bi bi-credit-card"></i> Paiement sécurisé</h1>
        <p style="color: #6B7280;">Finalisez votre inscription en effectuant le paiement</p>
      </div>
      
      <div class="event-summary">
        <h3 style="margin-bottom: 20px; color: var(--dark-color);">Résumé de votre commande</h3>
        
        <div class="summary-row">
          <span class="summary-label">Événement:</span>
          <span class="summary-value"><?php echo htmlspecialchars($evenement['titre']); ?></span>
        </div>
        
        <div class="summary-row">
          <span class="summary-label">Type:</span>
          <span class="summary-value"><?php echo htmlspecialchars($evenement['type']); ?></span>
        </div>
        
        <div class="summary-row">
          <span class="summary-label">Date:</span>
          <span class="summary-value"><?php echo Evenement::formatDateForDisplay($evenement['date']); ?></span>
        </div>
        
        <div class="summary-row">
          <span class="summary-label">Lieu:</span>
          <span class="summary-value"><?php echo htmlspecialchars($evenement['lieu']); ?></span>
        </div>
        
        <div class="summary-row">
          <span class="summary-label">Participant:</span>
          <span class="summary-value"><?php echo htmlspecialchars($inscriptionData['prenom'] . ' ' . $inscriptionData['nom']); ?></span>
        </div>
        
        <div class="summary-row" style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #E5E7EB;">
          <span class="summary-label" style="font-size: 20px;">Total à payer:</span>
          <span class="summary-value price-total"><?php echo number_format($eventPrice, 2, ',', ' '); ?> €</span>
        </div>
      </div>
      
      <form id="paymentForm" action="index.php?action=process_payment" method="post">
        <input type="hidden" name="inscription_id" value="<?php echo htmlspecialchars($inscriptionId); ?>">
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId); ?>">
        
        <div class="payment-methods">
          <h3 style="margin-bottom: 20px; color: var(--dark-color);">Méthode de paiement</h3>
          
          <label class="payment-method" for="stripe">
            <input type="radio" id="stripe" name="payment_method" value="stripe" checked>
            <span class="payment-method-label">
              <i class="bi bi-credit-card payment-method-icon"></i>
              Carte bancaire (Stripe)
            </span>
          </label>
          
          <label class="payment-method" for="paypal">
            <input type="radio" id="paypal" name="payment_method" value="paypal">
            <span class="payment-method-label">
              <i class="bi bi-paypal payment-method-icon" style="color: #0070BA;"></i>
              PayPal
            </span>
          </label>
        </div>
        
        <button type="submit" class="btn-pay">
          <i class="bi bi-lock-fill"></i> Payer <?php echo number_format($eventPrice, 2, ',', ' '); ?> €
        </button>
        
        <div class="security-badge">
          <i class="bi bi-shield-check"></i>
          Paiement 100% sécurisé et crypté
        </div>
      </form>
    </div>
    
    <div class="checkout-card" style="text-align: center;">
      <a href="index.php?action=details&id=<?php echo $eventId; ?>" style="color: #6B7280; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Retour aux détails de l'événement
      </a>
    </div>
  </div>
</div>

<script>
  // Gérer la sélection des méthodes de paiement
  document.querySelectorAll('.payment-method input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
      document.querySelectorAll('.payment-method').forEach(method => {
        method.classList.remove('selected');
      });
      this.closest('.payment-method').classList.add('selected');
    });
  });
  
  // Initialiser la sélection
  document.querySelector('.payment-method input[type="radio"]:checked').closest('.payment-method').classList.add('selected');
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
