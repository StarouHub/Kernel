<?php
/**
 * Configuration des paiements
 */

return [
    // Configuration Stripe
    'stripe' => [
        'enabled' => true,
        'public_key' => getenv('STRIPE_PUBLIC_KEY') ?: 'pk_test_your_public_key_here',
        'secret_key' => getenv('STRIPE_SECRET_KEY') ?: 'sk_test_your_secret_key_here',
        'currency' => 'eur'
    ],
    
    // Configuration PayPal
    'paypal' => [
        'enabled' => false,
        'client_id' => getenv('PAYPAL_CLIENT_ID') ?: '',
        'client_secret' => getenv('PAYPAL_CLIENT_SECRET') ?: '',
        'mode' => 'sandbox' // ou 'live'
    ],
    
    // Devise par défaut
    'default_currency' => 'EUR',
    
    // Taux de TVA (en pourcentage)
    'vat_rate' => 20.0
];
