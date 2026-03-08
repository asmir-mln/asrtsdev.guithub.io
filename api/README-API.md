# AsArt'sDev Orders API 🚀

API open-source pour gérer les commandes sans paiement en ligne direct.

## 📋 Fonctionnalités

- ✅ Enregistrement des commandes
- ✅ Suivi des paiements manuels (virement/chèque)
- ✅ Notifications email automatiques
- ✅ CLI pour gestion admin
- ✅ Stockage JSON simple (pas de base de données requise)
- ✅ API REST complète

## 🔧 Installation

### Prérequis
- PHP 7.4+ avec fonction `mail()` activée
- Node.js 14+ (pour CLI optionnel)

### Configuration

1. **Configurer l'API PHP**
```bash
cd api/
# Créer le dossier data
mkdir data
chmod 755 data
```

2. **Modifier les paramètres** dans `orders.php` :
```php
define('ADMIN_EMAIL', 'asartdev.contact@gmail.com');
```

3. **Changer la clé API** (sécurité) :
```php
$apiKey !== 'VOTRE_CLE_SECRETE_ICI'
```

## 🚀 Utilisation

### A. Via formulaire web

```html
<form id="orderForm">
  <input name="nom" required>
  <input name="email" type="email" required>
  <input name="produit" required>
  <button type="submit">Commander</button>
</form>

<script>
document.getElementById('orderForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData);
  
  const response = await fetch('/api/orders.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  });
  
  const result = await response.json();
  
  if (result.success) {
    alert(`Commande enregistrée ! ID: ${result.order.id}`);
  }
});
</script>
```

### B. Via CLI (Node.js)

```bash
# Lister toutes les commandes
node cli-orders.js list

# Créer une commande test
node cli-orders.js create

# Marquer comme payée
node cli-orders.js paid ORD-20260309-ABC123

# Aide
node cli-orders.js help
```

### C. Via API REST directe

**POST /api/orders.php** - Créer une commande
```bash
curl -X POST http://localhost:8000/api/orders.php \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Jean Dupont",
    "email": "jean@example.com",
    "telephone": "0612345678",
    "produit": "Max et Milla",
    "prix": "15,00 €"
  }'
```

**GET /api/orders.php?api_key=XXX** - Récupérer commandes
```bash
curl http://localhost:8000/api/orders.php?api_key=ASARTSDEV_SECRET_2026
```

**PUT /api/orders.php** - Mettre à jour commande
```bash
curl -X PUT http://localhost:8000/api/orders.php \
  -H "Content-Type: application/json" \
  -d '{
    "id": "ORD-20260309-ABC123",
    "api_key": "ASARTSDEV_SECRET_2026",
    "statut": "Payé",
    "paiement_recu": true
  }'
```

## 📊 Structure des données

### Commande
```json
{
  "id": "ORD-20260309-ABC123",
  "nom": "Jean Dupont",
  "email": "jean@example.com",
  "telephone": "0612345678",
  "adresse": "Paris, France",
  "produit": "Max et Milla",
  "prix": "15,00 €",
  "message": "Message optionnel",
  "statut": "En attente de paiement",
  "paiement_recu": false,
  "date_creation": "2026-03-09 15:30:00"
}
```

## 🔐 Sécurité

1. **Changer la clé API** (ligne 54 de `orders.php`) :
```php
if ($apiKey !== 'NOUVELLE_CLE_SUPER_SECRETE') {
```

2. **Protéger le dossier data/** avec `.htaccess` :
```apache
Order deny,allow
Deny from all
```

3. **Utiliser HTTPS** en production

## 📝 Licence

MIT License - Open-source AsArt'sDev 2026

## 🤝 Contribution

Pull requests bienvenues sur GitHub !

## 📧 Support

asartdev.contact@gmail.com | 0781586882
