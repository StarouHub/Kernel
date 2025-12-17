<?php
/**
 * Configuration des paiements
 * 
 * INSTRUCTIONS:
 * 1. Copiez ce fichier vers payment_config.php
 * 2. Remplissez vos clés API réelles
 * 3. Ne commitez JAMAIS payment_config.php dans Git
 */

return [
    'stripe' => [
        'secret_key' => 'sk_test_...', // Votre clé secrète Stripe
        'publishable_key' => 'pk_test_...', // Votre clé publique Stripe
        'webhook_secret' => 'whsec_...', // Secret du webhook Stripe
        'mode' => 'test' // 'test' ou 'live'
    ],
    'paypal' => [
        'client_id' => '...', // Votre Client ID PayPal
        'client_secret' => '...', // Votre Client Secret PayPal
        'mode' => 'sandbox' // 'sandbox' ou 'live'
    ],
    'currency' => 'EUR',
    'tax_rate' => 0.20 // TVA à 20%
];
