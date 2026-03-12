<?php
/* AsArt'sDev | Configuration PayPal | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */

/**
 * Configuration PayPal pour AsArt'sDev
 * 
 * IMPORTANT : Ne JAMAIS commiter ce fichier avec les vraies clés !
 * Utilisez des variables d'environnement en production.
 */

// Mode: sandbox pour les tests, live pour la production
define('PAYPAL_MODE', 'sandbox'); // Changez en 'live' pour production

// Clés API PayPal
// Obtenez-les sur : https://developer.paypal.com/dashboard/applications
if (PAYPAL_MODE === 'sandbox') {
    // Clés de test (sandbox)
    define('PAYPAL_CLIENT_ID', 'VOTRE_CLIENT_ID_SANDBOX_ICI');
    define('PAYPAL_CLIENT_SECRET', 'VOTRE_CLIENT_SECRET_SANDBOX_ICI');
} else {
    // Clés de production (live)
    define('PAYPAL_CLIENT_ID', 'VOTRE_CLIENT_ID_LIVE_ICI');
    define('PAYPAL_CLIENT_SECRET', 'VOTRE_CLIENT_SECRET_LIVE_ICI');
}

// URL de l'API PayPal
define('PAYPAL_API_URL', PAYPAL_MODE === 'sandbox' 
    ? 'https://api-m.sandbox.paypal.com' 
    : 'https://api-m.paypal.com'
);

// Email du compte PayPal marchand
define('PAYPAL_MERCHANT_EMAIL', 'asartdev.contact@gmail.com');

// Devise par défaut
define('PAYPAL_CURRENCY', 'EUR');

// URL de retour après paiement
define('PAYPAL_RETURN_URL', 'https://asartsdev.github.io/confirmation-paiement.html');
define('PAYPAL_CANCEL_URL', 'https://asartsdev.github.io/paiement.html');

// Fonction pour obtenir un token d'accès PayPal
function getPayPalAccessToken() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_API_URL . '/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ':' . PAYPAL_CLIENT_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Accept-Language: fr_FR'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    error_log('Erreur PayPal getAccessToken: ' . $response);
    return null;
}

// Fonction pour créer une commande PayPal
function createPayPalOrder($amount, $description = 'Achat AsArt\'sDev') {
    $accessToken = getPayPalAccessToken();
    if (!$accessToken) {
        return ['error' => 'Impossible d\'obtenir le token PayPal'];
    }

    $orderData = [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'amount' => [
                'currency_code' => PAYPAL_CURRENCY,
                'value' => number_format($amount, 2, '.', '')
            ],
            'description' => $description
        ]],
        'application_context' => [
            'return_url' => PAYPAL_RETURN_URL,
            'cancel_url' => PAYPAL_CANCEL_URL,
            'brand_name' => 'AsArt\'sDev',
            'locale' => 'fr-FR',
            'landing_page' => 'BILLING',
            'user_action' => 'PAY_NOW'
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_API_URL . '/v2/checkout/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 201) {
        return json_decode($response, true);
    }

    error_log('Erreur PayPal createOrder: ' . $response);
    return ['error' => 'Erreur lors de la création de la commande', 'details' => $response];
}

// Fonction pour capturer un paiement PayPal
function capturePayPalOrder($orderId) {
    $accessToken = getPayPalAccessToken();
    if (!$accessToken) {
        return ['error' => 'Impossible d\'obtenir le token PayPal'];
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_API_URL . '/v2/checkout/orders/' . $orderId . '/capture');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 201) {
        return json_decode($response, true);
    }

    error_log('Erreur PayPal captureOrder: ' . $response);
    return ['error' => 'Erreur lors de la capture du paiement', 'details' => $response];
}

return [
    'client_id' => PAYPAL_CLIENT_ID,
    'mode' => PAYPAL_MODE,
    'currency' => PAYPAL_CURRENCY
];
