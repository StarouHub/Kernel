<?php
// Stripe Payment Integration
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../controller/controller.php';

// Stripe API Keys - Your actual test keys
$stripe_publishable_key = 'pk_test_51Sd5WTIghmeylVfhRs3B3RKnSnHvb6Zjvw64npQSF8f5PFdLXoQWM4UlqRkmJfkoIEg1ASJbZiC4UGPXzEwKKZxE00R9tEEOmF';
$stripe_secret_key = 'sk_test_51Sd5WTIghmeylVfhH4KRu0qGnw5GZDLOo0zuJgwmqZz0EiiNhfGqdIJaPqZAlAJH4DzNGag7nuho4bdmP5cnyxLf00UdPvK8EF';

$tenderId = $_GET['tender_id'] ?? 0;
$amount = $_GET['amount'] ?? 0;
$projectName = $_GET['project'] ?? 'Projet';

// Stripe requires amount in cents - multiply by 100
$amountInCents = intval(floatval($amount) * 100);

$controller = new InvestmentController();
$tenders = $controller->getTenders();
$tender = null;
foreach ($tenders as $t) {
    if ($t['id'] == $tenderId) {
        $tender = $t;
        break;
    }
}

// Generate unique transaction reference
$transactionRef = 'INV-' . $tenderId . '-' . time();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Sécurisé - Kernel</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Raleway:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href="payment.css" rel="stylesheet">
    
    <!-- Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body style="padding-top: 0;">
    <header class="header d-flex align-items-center" style="position: relative;">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="investissement.php" class="logo">
                <i class="bi bi-hexagon-fill"></i> Kernel
            </a>
            <span style="color: #10B981; font-weight: 600;"><i class="bi bi-shield-lock-fill"></i> Paiement Sécurisé Stripe</span>
        </div>
    </header>

    <div class="payment-container">
        <div class="payment-wrapper">
            <div class="payment-form-section">
                <h1 class="payment-title"><i class="bi bi-credit-card-2-front-fill"></i> Paiement</h1>
                <p class="payment-subtitle">Complétez votre investissement en toute sécurité</p>

                <!-- Stripe Payment Form -->
                <div class="stripe-form-container">
                    <form id="payment-form">
                        <div class="form-group mb-3">
                            <label class="form-label"><i class="bi bi-envelope-fill"></i> Email (pour le reçu)</label>
                            <input type="email" id="customer-email" class="form-control" placeholder="votre@email.com" required style="padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px;">
                            <div id="email-error" class="text-danger mt-1"></div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label"><i class="bi bi-credit-card"></i> Informations de carte</label>
                            <div id="card-element" class="form-control" style="padding: 12px; height: auto;"></div>
                            <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                        </div>
                        
                        <button type="submit" id="submit-button" class="btn-pay">
                            <i class="bi bi-lock-fill"></i>
                            <span id="button-text">Payer <?php echo number_format($amount, 0, ',', ' '); ?> TND</span>
                            <span id="spinner" class="d-none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Traitement...
                            </span>
                        </button>
                    </form>
                </div>

                <p class="security-note mt-4">
                    <i class="bi bi-shield-check"></i>
                    Vos informations sont protégées par Stripe - Paiement sécurisé PCI DSS
                </p>
            </div>

            <div class="order-summary">
                <h3><i class="bi bi-receipt"></i> Récapitulatif</h3>
                
                <div class="project-info">
                    <div class="project-icon"><?php echo $tender ? substr($tender['sector'] ?? 'P', 0, 1) : 'P'; ?></div>
                    <div class="project-details">
                        <h4><?php echo htmlspecialchars($projectName); ?></h4>
                        <span><?php echo $tender ? htmlspecialchars($tender['sector']) : 'Investissement'; ?></span>
                    </div>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-row">
                    <span>Montant d'investissement</span>
                    <span><?php echo number_format($amount, 0, ',', ' '); ?> TND</span>
                </div>
                <div class="summary-row">
                    <span>Frais de plateforme (1.5%)</span>
                    <span><?php echo number_format($amount * 0.015, 2, ',', ' '); ?> TND</span>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-row total">
                    <span>Total à payer</span>
                    <span><?php echo number_format($amount * 1.015, 2, ',', ' '); ?> TND</span>
                </div>

                <div class="roi-info">
                    <i class="bi bi-graph-up-arrow"></i>
                    <div>
                        <strong>ROI estimé: <?php echo $tender ? $tender['expected_roi'] : '15'; ?>%</strong>
                        <span>Retour sur investissement prévu</span>
                    </div>
                </div>

                <div class="guarantee">
                    <div class="guarantee-item"><i class="bi bi-shield-check"></i><span>Paiement 100% sécurisé</span></div>
                    <div class="guarantee-item"><i class="bi bi-arrow-repeat"></i><span>Remboursement sous 14 jours</span></div>
                    <div class="guarantee-item"><i class="bi bi-headset"></i><span>Support client 24/7</span></div>
                </div>

                <div class="payment-logos mt-4 text-center">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/200px-Visa_Inc._logo.svg.png" alt="Visa" height="24" class="me-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/200px-Mastercard-logo.svg.png" alt="Mastercard" height="24" class="me-2">
                </div>
            </div>
        </div>
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h3>Traitement en cours...</h3>
            <p>Veuillez ne pas fermer cette page</p>
        </div>
    </div>

    <script>
    // Initialize Stripe with your publishable key
    // IMPORTANT: Replace this with your actual pk_test_ key from https://dashboard.stripe.com/test/apikeys
    const stripe = Stripe('<?php echo $stripe_publishable_key; ?>');
    const elements = stripe.elements();
    
    // Create card element
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#1e293b',
                fontFamily: '"Roboto", sans-serif',
                '::placeholder': {
                    color: '#94a3b8'
                }
            },
            invalid: {
                color: '#ef4444',
                iconColor: '#ef4444'
            }
        }
    });
    
    cardElement.mount('#card-element');
    
    // Handle validation errors
    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
    
    // Handle form submission
    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');
        const emailInput = document.getElementById('customer-email');
        const emailError = document.getElementById('email-error');
        
        // Validate email
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!email || !emailRegex.test(email)) {
            emailError.textContent = 'Veuillez entrer une adresse email valide';
            emailInput.focus();
            return;
        }
        emailError.textContent = '';
        
        submitButton.disabled = true;
        buttonText.classList.add('d-none');
        spinner.classList.remove('d-none');
        
        // For a school project, we'll simulate the payment
        // In production, you would create a PaymentIntent on your server
        const {token, error} = await stripe.createToken(cardElement);
        
        if (error) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            submitButton.disabled = false;
            buttonText.classList.remove('d-none');
            spinner.classList.add('d-none');
        } else {
            // Token created successfully - redirect to success page
            // In production, send token to your server to create a charge
            console.log('Payment token:', token);
            
            // Simulate successful payment and redirect with email
            window.location.href = `payment-success.php?tender_id=<?php echo $tenderId; ?>&amount=<?php echo $amount; ?>&project=<?php echo urlencode($projectName); ?>&method=stripe&payment_id=${token.id}&email=${encodeURIComponent(email)}`;
        }
    });
    </script>

    <style>
    .stripe-form-container {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 24px;
        margin: 20px 0;
    }
    
    #card-element {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        transition: border-color 0.3s ease;
    }
    
    #card-element:focus-within {
        border-color: #10B981;
    }
    
    .StripeElement--focus {
        border-color: #10B981;
    }
    
    .StripeElement--invalid {
        border-color: #ef4444;
    }
    </style>
</body>
</html>