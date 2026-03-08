<?php
/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
/**
 * Fichier de configuration pour le système de paiement
 * AsArt'sDev
 * 
 * ⚠️ IMPORTANT : 
 * 1. Renommez ce fichier en config.php
 * 2. Ajoutez config.php à .gitignore
 * 3. Ne commitez JAMAIS vos vraies clés API
 */

return [
    // Configuration Stripe
    'stripe' => [
        // Clés de test (préfixe pk_test_ et sk_test_)
        'test' => [
            'public_key' => 'pk_test_VOTRE_CLE_PUBLIQUE_TEST',
            'secret_key' => 'sk_test_VOTRE_CLE_SECRETE_TEST',
            'webhook_secret' => 'whsec_VOTRE_SECRET_WEBHOOK_TEST'
        ],
        
        // Clés de production (préfixe pk_live_ et sk_live_)
        'live' => [
            'public_key' => 'pk_live_VOTRE_CLE_PUBLIQUE_LIVE',
            'secret_key' => 'sk_live_VOTRE_CLE_SECRETE_LIVE',
            'webhook_secret' => 'whsec_VOTRE_SECRET_WEBHOOK_LIVE'
        ],
        
        // Mode actuel : 'test' ou 'live'
        'mode' => 'test'
    ],
    
    // Configuration email
    'email' => [
        'from' => 'contact@asartsdev.com',
        'reply_to' => 'contact@asartsdev.com',
        'admin' => 'admin@asartsdev.com'
    ],
    
    // Configuration générale
    'site' => [
        'name' => 'AsArt\'sDev',
        'url' => 'https://www.asartsdev.com',
        'currency' => 'EUR',
        'locale' => 'fr_FR'
    ],
    
    // Configuration des produits
    'products' => [
        'aventures-animaux' => [
            'name' => 'Les Aventures des Animaux',
            'price' => 9.99,
            'type' => 'digital',
            'file' => 'products/aventures-animaux.pdf'
        ],
        'souvenirs-enfance' => [
            'name' => 'Souvenirs d\'Enfance',
            'price' => 12.99,
            'type' => 'digital',
            'file' => 'products/souvenirs-enfance.pdf'
        ]
    ],
    
    // Configuration des logs
    'logging' => [
        'enabled' => true,
        'path' => __DIR__ . '/logs/',
        'max_size' => 10 * 1024 * 1024, // 10 MB
        'retention_days' => 90
    ],
    
    // Configuration de sécurité
    'security' => [
        'min_amount' => 0.50, // Montant minimum en euros
        'max_amount' => 1000.00, // Montant maximum en euros
        'allowed_currencies' => ['EUR', 'USD'],
        'rate_limit' => [
            'attempts' => 10,
            'period' => 3600 // 1 heure en secondes
        ]
    ]
];


