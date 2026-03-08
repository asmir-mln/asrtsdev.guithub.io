# 🚀 Guide de démarrage rapide - API Commandes AsArt'sDev

## ⚡ Installation en 3 étapes

### 1. Démarrer le serveur local
```bash
cd "c:\Users\Asmir\Documents\e com"
php -S localhost:8000
```

### 2. Tester avec le CLI
```bash
# Créer une commande test
node api/cli-orders.js create

# Lister les commandes
node api/cli-orders.js list

# Marquer comme payée
node api/cli-orders.js paid ORD-20260309-ABC123
```

### 3. Intégrer dans le site
Le fichier `orders-client.js` est déjà intégré dans `commande.html` !

## 📱 Utilisation sur le site

Quand un client remplit le formulaire sur **commande.html** :

1. ✅ Commande enregistrée automatiquement
2. 📧 Email envoyé à `asartdev.contact@gmail.com`
3. 💬 Modale de confirmation affichée au client
4. 🆔 Numéro de commande généré (ex: ORD-20260309-ABC123)

## 🔧 Configuration requise

### Email automatique (important!)
Pour que les emails fonctionnent, configure PHP :

**Windows (php.ini):**
```ini
[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = asartdev.contact@gmail.com
```

**Alternative : utiliser un service email**
- SendGrid (gratuit 100 emails/jour)
- Mailgun (gratuit 5000 emails/mois)
- SMTP2GO

## 📊 Fichier de données

Les commandes sont stockées dans :
```
api/data/orders.json
```

**Format :**
```json
[
  {
    "id": "ORD-20260309-ABC123",
    "nom": "Client Test",
    "email": "test@example.com",
    "produit": "Max et Milla",
    "prix": "15,00 €",
    "statut": "En attente de paiement",
    "paiement_recu": false,
    "date_creation": "2026-03-09 15:30:00"
  }
]
```

## 🔐 Sécurité IMPORTANTE

⚠️ **AVANT DE PUSH SUR GITHUB:**

1. Change la clé API dans `orders.php` ligne 54 :
```php
if ($apiKey !== 'TA_NOUVELLE_CLE_SUPER_SECRETE_2026') {
```

2. Vérifie que `.gitignore` contient :
```
api/data/
api/data/*.json
```

3. Ne partage JAMAIS :
   - Les données `api/data/orders.json`
   - Ta clé API réelle
   - Les emails clients

## 📱 URLs importantes

- **Site local :** http://localhost:8000
- **Page commande :** http://localhost:8000/commande.html
- **Musée :** http://localhost:8000/musee.html
- **API directe :** http://localhost:8000/api/orders.php

## 🎯 Workflow complet

```
Client remplit formulaire
         ↓
orders-client.js intercepte
         ↓
POST vers /api/orders.php
         ↓
Commande enregistrée dans orders.json
         ↓
Email automatique vers toi
         ↓
Modale de confirmation au client
         ↓
Tu reçois virement/chèque
         ↓
Tu marques payé via CLI:
node api/cli-orders.js paid ORD-xxx
```

## 💡 Astuces

### Voir les commandes en temps réel
```bash
# Windows PowerShell
Get-Content api/data/orders.json | ConvertFrom-Json | Format-List
```

### Backup automatique
```bash
# Créer backup quotidien
copy api/data/orders.json "api/data/orders-backup-$(Get-Date -Format 'yyyyMMdd').json"
```

### Statistiques rapides
```bash
node api/cli-orders.js list | findstr "Payé"
```

## 🚀 Prêt pour GitHub Pages ?

Cette API fonctionne en local, mais GitHub Pages ne supporte pas PHP.

**Solutions :**
1. **Héberger l'API ailleurs** (Heroku, Vercel, PlanetHoster gratuit)
2. **Utiliser Netlify Functions** (serverless gratuit)
3. **Email direct** (formulaire mailto:)

L'option actuelle (mailto:) est déjà configurée et fonctionne sans serveur !

---

**Support :** asartdev.contact@gmail.com | 0781586882
