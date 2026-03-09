# 📧 GUIDE CONFIGURATION EMAILS - AsArt'sDev
## Envoyer tous les emails sur sam.mln51@icloud.com

**Date:** 9 mars 2026  
**Status:** ✅ Prêt pour mise en place  
**Contact:** sam.mln51@icloud.com

---

## 📋 Table des matières

1. [Configuration iCloud Mail](#configuration-icloud-mail)
2. [Tests d'envoi](#tests-denvoi)
3. [Dépannage](#dépannage)
4. [Routes de test](#routes-de-test)

---

## Configuration iCloud Mail

### Étape 1️⃣: Récupérer un "mot de passe spécifique à l'app"

**iCloud Mail requiert:** Un mot de passe spécifique pour les applications tierces

1. **Aller à:** https://appleid.apple.com
2. **Se connecter** avec: sam.mln51@icloud.com + mot de passe
3. **Navigation:** Sécurité → Mots de passe spécifiques à l'app
4. **Générer:**
   - Sélectionner "Mail" et "Windows"
   - Cliquer "Générer"
   - Copier le mot de passe généré (16 caractères)
   - Format: `xxxx-xxxx-xxxx-xxxx`

### Étape 2️⃣: Configurer .env.local

**Fichier:** `c:\Users\Asmir\Documents\e com\.env.local`

```env
# 📧 EMAILS
MAILER_DSN=smtp://sam.mln51@icloud.com:MOT_DE_PASSE_GENERE@smtp.mail.icloud.com:587?encryption=tls
MAILER_FROM=noreply@asartsdev.com
ADMIN_EMAIL=sam.mln51@icloud.com
SUPPORT_EMAIL=sam.mln51@icloud.com
```

**Exemple complet:**
```env
MAILER_DSN=smtp://sam.mln51@icloud.com:wxyz-wxyz-wxyz-wxyz@smtp.mail.icloud.com:587?encryption=tls
MAILER_FROM=noreply@asartsdev.com
ADMIN_EMAIL=sam.mln51@icloud.com
SUPPORT_EMAIL=sam.mln51@icloud.com
SEND_DONATION_REMINDERS=true
NOTIFY_ADMIN_ON_DONATION=true
NOTIFY_ADMIN_SUSPICIOUS=true
```

### Étape 3️⃣: Copier les services

**Files à copier:**

```bash
# Depuis l'API
cp api/Service/NotificationService.php src/Service/

# Mettre à jour services.yaml
cp api/config-services.yaml config/services.yaml
```

### Étape 4️⃣: Activer l'authentification

**iCloud Mail - Sécurité:**
1. Aller à https://appleid.apple.com
2. Activer: "Accès des apps moins sécurisées" (Ioptionnel mais recommandé)
3. Vérifier: "Authentification à deux facteurs" activée

---

## Tests d'envoi

### 🧪 Test 1: Email direct (Command Symfony)

```bash
# Depuis la racine du projet
php bin/console mailer:test sam.mln51@icloud.com
```

**Résultat attendu:**
```
✓ Email sent to sam.mln51@icloud.com
```

### 🧪 Test 2: Via Controller (HTTP Request)

**URL:** `http://localhost/admin/test-email`

Créer un controller temporaire:

```php
<?php
namespace App\Controller;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Response;

class TestController extends AbstractController {
    public function testEmail(NotificationService $notif): Response {
        $sent = $notif->testEmail('sam.mln51@icloud.com');
        return new Response($sent ? 'Email envoyé! ✅' : 'Erreur ❌');
    }
}
```

### 🧪 Test 3: Test donation complète

**Étapes:**
1. Aller à: `http://localhost/donation/formulaire`
2. Remplir le formulaire (donation complète)
3. Montant: 50€
4. Email: ANY (test)
5. Soumettre

**Notifications automatiques reçues sur sam.mln51@icloud.com:**
1. ✅ Email de confirmation au donateur
2. ✅ Notification admin pour chaque donation
3. ✅ Reminders automatiques aux étapes suivantes

---

## Routes de test

### Vérification configuration

**GET /admin/config-test**

Endpoint pour vérifier la configuration:
```json
{
  "mailer_configured": true,
  "admin_email": "sam.mln51@icloud.com",
  "notifications_enabled": true,
  "reminders_enabled": true
}
```

### Envoyer email de test

**POST /admin/send-test-email**

```bash
curl -X POST http://localhost/admin/send-test-email \
  -H "Content-Type: application/json" \
  -d '{"email": "sam.mln51@icloud.com"}'
```

### Simuler donation

**POST /donation/submit-test**

```bash
curl -X POST http://localhost/donation/submit-test \
  -H "Content-Type: application/json" \
  -d '{
    "montant": 100,
    "typeDonation": "complete",
    "nomDonateur": "Test User",
    "emailDonateur": "sam.mln51@icloud.com",
    "adresseDonateur": "Paris, France"
  }'
```

---

## Dépannage

### ❌ Erreur: "SMTP connection refused"

**Cause:** Mauvais serveur SMTP ou port

**Solution:**
```env
# Vérifier MAILER_DSN
# smtp.mail.icloud.com:587 (correct)
# Pas: smtp.icloud.com (mauvais)
```

### ❌ Erreur: "Authentication failed"

**Cause:** Mot de passe incorrect ou expiré

**Solution:**
1. Générer nouveau mot de passe spécifique à l'app
2. Mettre à jour .env.local
3. Vider cache: `php bin/console cache:clear`

### ❌ Erreur: "Connection timeout"

**Cause:** Firewall ou connexion réseau

**Solution:**
```bash
# Tester la connexion SMTP
telnet smtp.mail.icloud.com 587

# Si erreur: vérifier paramètres réseau
```

### ❌ Email reçu mais vide/mal formé

**Cause:** HTML mal rendu

**Solution:** Vérifier dans `NotificationService.php` la syntaxe HTML

---

## ✅ Checklist de configuration

- [ ] Mot de passe spécifique à l'app généré = `xxxx-xxxx-xxxx-xxxx`
- [ ] .env.local créé avec MAILER_DSN correct
- [ ] ADMIN_EMAIL = sam.mln51@icloud.com
- [ ] SUPPORT_EMAIL = sam.mln51@icloud.com
- [ ] NotificationService.php copié dans src/Service/
- [ ] config/services.yaml mis à jour
- [ ] Test email lancé avec succès ✅
- [ ] Email de test reçu dans sam.mln51@icloud.com ✅
- [ ] Donation test soumise
- [ ] Email de confirmation reçu ✅
- [ ] Email notification admin reçu ✅

---

## 🎯 Quand tout fonctionne

### Email de donation vient de composer:

**Donateur reçoit:**
1. ✅ Confirmation de donation (dans 1 min)
2. ✅ Rappel de préparation cadeau (dans 24h)
3. ✅ Avis d'expédition (quand envoyé)
4. ✅ Confirmation de réception

**Admin reçoit:**
1. ✅ Notification chaque donation (immédiat)
2. ⚠️ Alerte si donation suspecte (immédiat)
3. 🔔 Reminders à traiter (manuel)

### Sur sam.mln51@icloud.com:

Recevoir TOUS les emails:
- 💰 Donations
- 📝 Reminders
- ⚠️ Alertes
- 📊 Notifications admin
- 🎁 Confirmations cadeaux

---

## 📞 Support

**En cas de problème:**

1. Vérifier logs: `var/log/dev.log`
2. Tester configuration: `php bin/console config:dump`
3. Vérifier .env.local existe
4. Vérifier MAILER_DSN format est correct
5. Tester telnet: `telnet smtp.mail.icloud.com 587`

---

## 📚 Fichiers modifiés

- ✅ `.env.local` - Configuration email
- ✅ `Service/NotificationService.php` - Envoi emails
- ✅ `config/services.yaml` - Services Symfony
- ✅ `Controller/DonationController.php` - Notification donation
- ✅ `Controller/AdminController.php` - Notification admin
- ✅ `Service/ReminderService.php` - Notification reminders

---

**Status:** 🟢 Prêt pour mise en œuvre  
**Date:** 9 mars 2026  
**Destinataire:** sam.mln51@icloud.com
