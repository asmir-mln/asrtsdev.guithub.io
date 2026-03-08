# 💳 API de Paiement par Carte Bancaire - AsArt'sDev

## 📋 Vue d'ensemble

Système complet de paiement sécurisé par carte bancaire utilisant **Stripe**, intégré au site e-commerce AsArt'sDev.

## 🚀 Fichiers créés

### Pages Web
- **paiement.html** - Page de paiement sécurisée avec formulaire de carte
- **confirmation-paiement.html** - Page de confirmation après paiement réussi
- **commande.html** - Mise à jour avec redirection vers le paiement
- **commande.js** - Script pour gérer le formulaire de commande

### Styles
- **paiement-styles.css** - Styles pour la page de paiement

### Scripts JavaScript
- **paiement.js** - Intégration Stripe côté client

### API Backend (PHP)
- **api/create-payment-intent.php** - Création des intentions de paiement
- **api/webhook.php** - Gestion des événements Stripe (webhooks)
- **api/verify-payment.php** - Vérification du statut des paiements

## 🔧 Installation

### 1. Créer un compte Stripe

1. Allez sur https://stripe.com
2. Créez un compte gratuit
3. Récupérez vos clés API :
   - Clé publique (pk_test_...)
   - Clé secrète (sk_test_...)

### 2. Installer la bibliothèque Stripe PHP

Dans le dossier `api/`, exécutez :

```bash
composer require stripe/stripe-php
```

Ou téléchargez manuellement depuis : https://github.com/stripe/stripe-php/releases

### 3. Configuration des clés API

#### Dans paiement.js (ligne 4)
```javascript
const STRIPE_PUBLIC_KEY = 'pk_test_VOTRE_CLE_PUBLIQUE_ICI';
```

#### Dans tous les fichiers PHP du dossier api/
```php
define('STRIPE_SECRET_KEY', 'sk_test_VOTRE_CLE_SECRETE_ICI');
```

### 4. Configuration des Webhooks

1. Dans le tableau de bord Stripe, allez dans **Développeurs** > **Webhooks**
2. Cliquez sur **Ajouter un point de terminaison**
3. URL : `https://votredomaine.com/api/webhook.php`
4. Événements à écouter :
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `charge.refunded`
5. Copiez le **Secret de signature du webhook** (whsec_...)
6. Ajoutez-le dans `api/webhook.php` :
```php
define('STRIPE_WEBHOOK_SECRET', 'whsec_VOTRE_SECRET_ICI');
```

## 📁 Structure des fichiers

```
e com/
├── paiement.html
├── paiement.js
├── paiement-styles.css
├── confirmation-paiement.html
├── commande.html
├── commande.js
├── api/
│   ├── create-payment-intent.php
│   ├── webhook.php
│   ├── verify-payment.php
│   └── stripe-php/ (bibliothèque Stripe)
└── logs/
    ├── transactions.log
    └── webhooks.log
```

## 🔒 Sécurité

### Points importants :

✅ **HTTPS obligatoire** - Ne jamais utiliser en HTTP
✅ **Clés secrètes** - Ne jamais exposer côté client
✅ **Validation webhook** - Vérifier la signature Stripe
✅ **Montant minimum** - 0,50€ minimum
✅ **Logs sécurisés** - Stocker les logs hors du web root

### Protection des clés API

Créez un fichier `.env` ou `config.php` hors du dossier web :

```php
<?php
// config.php (hors du web root)
return [
    'stripe' => [
        'public_key' => 'pk_test_...',
        'secret_key' => 'sk_test_...',
        'webhook_secret' => 'whsec_...'
    ]
];
```

## 🧪 Tests

### Mode Test Stripe

Utilisez ces cartes de test :

| Carte | Numéro | Résultat |
|-------|--------|----------|
| Visa réussie | 4242 4242 4242 4242 | Paiement accepté |
| Visa refusée | 4000 0000 0000 0002 | Carte refusée |
| 3D Secure | 4000 0027 6000 3184 | Nécessite authentification |

- Date d'expiration : N'importe quelle date future
- CVC : N'importe quel 3 chiffres
- Code postal : N'importe lequel

### Tester le workflow complet

1. Allez sur `commande.html`
2. Remplissez le formulaire
3. Cliquez sur "Commander"
4. Vous serez redirigé vers `paiement.html`
5. Utilisez une carte de test
6. Après paiement : redirection vers `confirmation-paiement.html`

## 📊 Suivi des paiements

### Dans le tableau de bord Stripe
- Visualisez tous les paiements
- Gérez les remboursements
- Consultez les statistiques

### Logs locaux
- `logs/transactions.log` - Toutes les transactions
- `logs/webhooks.log` - Événements webhook

## 🌐 Mise en production

### Checklist avant le déploiement :

- [ ] Remplacer les clés test par les clés live (pk_live_... et sk_live_...)
- [ ] Activer HTTPS sur tout le site
- [ ] Configurer les webhooks en production
- [ ] Tester avec de vraies cartes (petits montants)
- [ ] Configurer l'envoi d'emails de confirmation
- [ ] Mettre en place une base de données pour les commandes
- [ ] Configurer la livraison automatique des produits numériques
- [ ] Vérifier les conditions générales de vente
- [ ] Tester sur mobile et desktop

## 💡 Fonctionnalités avancées (à implémenter)

### Suggestions d'amélioration :

1. **Base de données**
   - Stocker les commandes
   - Historique des transactions
   - Gestion des clients

2. **Emails automatiques**
   - Confirmation de commande
   - Livraison du produit numérique
   - Suivi de commande

3. **Tableau de bord admin**
   - Visualiser les commandes
   - Gérer les produits
   - Statistiques de vente

4. **Fonctionnalités supplémentaires**
   - Codes promo
   - Abonnements récurrents
   - Panier multi-produits
   - Gestion des stocks

## 🆘 Support

### Documentation Stripe
- https://stripe.com/docs
- https://stripe.com/docs/payments/accept-a-payment

### Ressources utiles
- Guide d'intégration : https://stripe.com/docs/payments/quickstart
- Test de cartes : https://stripe.com/docs/testing
- API PHP : https://stripe.com/docs/api/php

## 📝 Notes importantes

1. **Conformité légale** :
   - Respecter le RGPD pour les données clients
   - Afficher clairement les CGV
   - Mentionner la politique de remboursement

2. **Performance** :
   - Mettre en cache la bibliothèque Stripe
   - Optimiser les requêtes API
   - Utiliser un CDN pour les assets

3. **Maintenance** :
   - Vérifier régulièrement les logs
   - Mettre à jour la bibliothèque Stripe
   - Surveiller les tentatives de fraude

## 📞 Contact

Pour toute question sur cette implémentation :
- **Développeur** : ASmir Milia
- **Projet** : AsArt'sDev
- **Date** : Février 2026

---

© 2026 AsArt'sDev – créé par ASmir Milia. Tous droits réservés.

