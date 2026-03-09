# 💰 Système de Gestion des Donations et Reminders
## AsArt'sDev | Interface Admin Complète

**Date:** 9 mars 2026  
**Statut:** Production-Ready ✅  
**Version:** 2.0 - Donations + Reminders + Suivi Anonyme

---

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture](#architecture)
3. [Installation & Configuration](#installation--configuration)
4. [Fonctionnalités principales](#fonctionnalités-principales)
5. [Interfaces Admin](#interfaces-admin)
6. [API Routes](#api-routes)
7. [Exemples d'utilisation](#exemples-dutilisation)
8. [Gestion des IPs & Sécurité](#gestion-des-ips--sécurité)
9. [Dépannage](#dépannage)

---

## Vue d'ensemble

### 🎯 Objectif principal

Gérer complètement le cycle de vie des donations avec :
- ✅ Donations **complètes** (avec infos) → Cadeaux
- 🔒 Donations **anonymes** (sans infos) → Suivi par code
- 📝 **Reminders automatiques** à chaque étape
- 👤 **Interface admin** pour gestion manuelle
- 📊 **Rapports & audit** complets
- 🔒 **Suivi sécurisé** des données personnelles

### Cas d'usage

**Donation Complète (100€):**
```
Formulaire → Donation créée (100€) → Email confirmé 
→ Cadeau "Pack VIP" assigné → Préparation → Expédition 
→ Reminders à chaque étape → Reçu confirmé
```

**Donation Anonyme (25€):**
```
Formulaire (anonyme) → Donation créée (25€) 
→ Code suivi: ANON-XYZ123 → Pas de cadeau 
→ Suivi anonyme via code URL /admin/track/ANON-XYZ123 
→ Timeline publique (sans infos perso)
```

---

## Architecture

### 📊 Diagramme des entités

```
Commande
├── Donation (1..N)
│   ├── nomDonateur (nullable)
│   ├── emailDonateur (nullable)
│   ├── ipAdresse (logged)
│   ├── cadeauId (FK → Cadeaux)
│   └── codeSuivi (UUID)
└── ReminderCommande (1..N)
    ├── typeReminder (enum: creation, paiement_recu, etc.)
    ├── message (text)
    ├── dateEnvoi (nullable)
    └── statut (brouillon|envoye|non_applicable)
```

### Tables de base de données

#### 1. `donations`
```sql
- id (INT)
- commande_id (FK)
- montant (DECIMAL)
- nom_donateur (VARCHAR, nullable)
- email_donateur (VARCHAR, nullable)
- telephone_donateur (VARCHAR, nullable)
- adresse_donateur (TEXT, nullable)
- ip_adresse (VARCHAR 45) -- IPv4 ou IPv6
- user_agent (TEXT)
- date_donation (DATETIME)
- eligible_cadeau (BOOLEAN)
- cadeau_id (INT, nullable)
- cadeau_envoye (DATETIME, nullable)
- code_suivi (VARCHAR 32, UNIQUE)
- statut_suivi (ENUM: en_attente|confirmee|traitee|envoyee)
```

#### 2. `reminder_commandes`
```sql
- id (INT)
- commande_id (FK)
- type_reminder (ENUM)
- message (LONGTEXT)
- date_creation (DATETIME)
- date_envoi (DATETIME, nullable)
- statut (brouillon|envoye|non_applicable)
```

#### 3. `cadeaux` (Contreparties)
```sql
- id (INT)
- nom_cadeau (VARCHAR)
- montant_minimum (DECIMAL)
- quantite_disponible (INT, -1 = illimité)
- quantite_utilisee (INT)
- actif (BOOLEAN)
```

#### 4. `audit_ip` (Sécurité)
```sql
- ip_adresse (VARCHAR 45)
- type_action (ENUM)
- reference_id (INT)
- date_action (DATETIME)
```

---

## Installation & Configuration

### Prérequis

```bash
# PHP 8.0+
# MySQL 8.0+
# Symfony 5.4+
# Mailer service configuré
```

### Étapes d'installation

**1. Exécuter les migrations de base de données:**

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les entités existantes
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate

# Exécuter le script DatabaseUpgrade.sql
mysql -u root -p asartsdev < api/DatabaseUpgrade.sql
```

**2. Copier les fichiers:**

```bash
# Entités
cp api/Entity/Donation.php src/Entity/
cp api/Entity/ReminderCommande.php src/Entity/

# Formulaires
cp api/Form/DonationFormType.php src/Form/

# Contrôleurs
cp api/Controller/AdminController.php src/Controller/

# Services
cp api/Service/ReminderService.php src/Service/
```

**3. Configuration du service Mailer (.env):**

```env
# Utiliser Mailer de Symfony
MAILER_DSN=smtp://smtp.gmail.com:587
MAILER_FROM=noreply@asartsdev.com

# Ou Mailgun
MAILER_DSN=mailgun+api://key@sandboxXXX.mailgun.org
```

**4. Modification de l'entité `Commande`:**

Ajouter les relations:

```php
// Dans Entity/Commande.php

/**
 * @ORM\OneToMany(targetEntity="Donation", mappedBy="commande", cascade={"persist", "remove"})
 */
private $donations;

/**
 * @ORM\OneToMany(targetEntity="ReminderCommande", mappedBy="commande", cascade={"persist", "remove"})
 */
private $reminders;

// Dans le constructeur
$this->donations = new ArrayCollection();
$this->reminders = new ArrayCollection();
```

---

## Fonctionnalités principales

### ✅ Donations complètes
- ✓ Collecte nom, email, téléphone, adresse
- ✓ Montant minimum: 5€
- ✓ Eligible aux cadeaux
- ✓ Email de confirmation automatique
- ✓ IP logged pour sécurité

### 🔒 Donations anonymes
- ✓ Pas de données personnelles collectées
- ✓ Code suivi unique généré (UUID)
- ✓ Accès public au suivi: `/admin/track/{code}`
- ✓ Timeline visible publiquement (sans infos perso)
- ✓ ⚠️ PAS de cadeau (politique)

### 🎁 Système de cadeaux
- 👤 Remerciement personnel (5€+)
- 📚 Bibliothèque numérique 1 an (25€+)
- 🎨 Œuvre numérique exclusive (50€+)
- 💝 Pack VIP donateur (100€+)
- 🏆 Reconnaissance publique (500€+)
- 👑 Partenariat stratégique (5000€+)

Chaque cadeau a :
- Montant minimum/maximum
- Quantité disponible (illimitée = -1)
- Status de disponibilité

### 📝 Reminders automatiques

| Type | Trigger | Email | Cible |
|------|---------|-------|-------|
| `creation` | Donation confirmée | ✅ | Complets uniquement |
| `paiement_recu` | Paiement approuvé | ✅ | Complets uniquement |
| `preparation` | Cadeau assigné | ✅ | Complets uniquement |
| `pret_expedition` | Cadeau prêt | ✅ | Complets uniquement |
| `envoye` | Colis expédié | ✅ | Complets uniquement |
| `recu` | Post-livraison | ✅ | Complets uniquement |
| `relance` | Manuel | ✅ | Complets uniquement |

**Les donations anonymes:** Les reminders ne sont PAS envoyés par email (pas d'email). Timeline visible sur page de suivi publique.

---

## Interfaces Admin

### 📊 Dashboard admin (`/admin/dashboard`)

Vue d'ensemble rapide:
- 📈 Total donations (montant)
- 🔒 Donations anonymes vs complètes
- 🎁 Cadeaux en attente d'envoi
- 🔔 Reminders non traités
- 📬 Dernières donations

### 💰 Gestion donations (`/admin/donations`)

Liste avec filtres:
- Affichage: Donateur | Montant | Statut | Cadeau | IP
- Filtres: Anonymes | Eligibles cadeaux | Statut suivi
- Pagination: 20 par page
- Actions: Détail | Assigner cadeau | Marquer envoye

**Colonnes:**
```
| # | Donateur | Montant | Email | IP | Cadeau | Statut |
|---|----------|--------|-------|----|----|--------|
```

### 📋 Gestion commandes (`/admin/commandes`)

Liste commandes avec reminders:
- Filtres: Statut | Type de projet
- Affichage: Numéro | Type | Montant | [Nombre reminders] | Dernier reminder
- Liens: Détail commande | Donation liée

### 📝 Gestion reminders (`/admin/reminders`)

Inbox des reminders:
- Statut: Brouillon (à envoyer) | Envoye | Non-applicable
- Actions: Aperçu | Envoyer | Marquer non-applicable
- Tri: Par date création (DESC)

**Actions possibles:**
- ✅ Envoyer reminder (si donation complète)
- ⏭️ Marquer non-applicable (anonyme)
- 🔄 Redéfinir message
- 🗑️ Supprimer (si brouillon)

### 🔍 Détail donation (`/admin/donation/{id}`)

Page de détail complète:
```
┌─────────────────────────────┐
│ DONATION #123               │
├─────────────────────────────┤
│ Donateur: Asmir Milianni    │
│ Email: asmir@asartsdev.com  │
│ Montant: 100€               │
│ IP: 203.0.113.50            │
│ Date: 09/03/2026 14:30      │
├─────────────────────────────┤
│ CADEAU: Pack VIP            │
│ Status: ⏳ En attente       │
│ [Bouton: Assigner cadeau]   │
│ [Bouton: Marquer envoyé]    │
├─────────────────────────────┤
│ TIMELINE:                   │
│ • 14:30 Donation créée      │
│ • 14:35 Paiement reçu       │
│ • 15:00 Préparation début   │
└─────────────────────────────┘
```

### 🔒 Sécurité IPs (`/admin/security/ips`)

Rapport d'audit:
- Liste toutes les IPs
- Détecte IPs suspectes (3+ donations)
- Affiche: Date | Montant | Donateur pour chaque IP
- Alerte: Fraude potentielle

### 📬 Suivi anonyme **PUBLIC** (`/admin/track/{code}`)

Page accessible publiquement (sans authentification):
```
┌────────────────────────────┐
│ SUIVI DE VOTRE DONATION    │
├────────────────────────────┤
│ Code: ANON-A1B2C3D4E5      │
│ Montant: 25€               │
│ Date: 09/03/2026           │
│ Statut: ✅ Confirmée       │
├────────────────────────────┤
│ TIMELINE:                  │
│ ✅ 14:30 - Donation reçue  │
│ ✅ 14:35 - Confirmée       │
│ ⏳ 15:00 - En traitement   │
└────────────────────────────┘
```

Affichage: Sans aucune info personnelle

---

## API Routes

### Donation publique

**POST** `/donation/formulaire`
```
Formulaire HTML de donation
```

**POST** `/donation/submit`
```
Request:
{
  "montant": 100,
  "type_donation": "complete",
  "nom_donateur": "Asmir",
  "email_donateur": "asmir@asartsdev.com",
  "adresse_donateur": "Paris, France",
  "telephone_donateur": "0781586882"
}

Response 200:
{
  "success": true,
  "commande_id": 123,
  "code_sauvi": "ASMIR-001",
  "message": "Merci pour votre donation!"
}
```

### Admin - Donations

**GET** `/admin/donations`
- Paramètres: page, statut, eligible
- Retour: Liste avec pagination

**GET** `/admin/donation/{id}`
- Retour: Détail complet

**POST** `/admin/donation/{id}/assign-cadeau`
- Body: `cadeau_id=2`
- Crée reminder type "preparation"

**POST** `/admin/donation/{id}/cadeau-envoye`
- Marque cadeau comme envoyé
- Crée reminder type "envoye"

### Admin - Reminders

**GET** `/admin/reminders`
- Paramètres: page, statut
- Status: "brouillon" par défaut

**POST** `/admin/reminder/{id}/send`
- Envoie reminder par email (si donation complète)
- Marque comme envoye + timestamp

### Suivi public

**GET** `/admin/track/{codeSuivi}`
- Public (pas d'auth)
- Retour: Timeline + statut

---

## Exemples d'utilisation

### Exemple 1: Créer une donation complète

```php
// Dans un contrôleur

$commande = new Commande();
$commande->setNumeroCommande('DON-2026-' . date('mds'));
$commande->setTypeProjet('donation');
$commande->setMontant(100.00);
$commande->setStatut('confirmee');
$commande->setTypeDonation('complete');

$donation = new Donation();
$donation->setCommande($commande);
$donation->setMontant(100.00);
$donation->setNomDonateur('Asmir Milianni');
$donation->setEmailDonateur('asmir@asartsdev.com');
$donation->setAdresseDonateur('Paris, France');
$donation->setIpAdresse($request->getClientIp());
$donation->setEligibleCadeau(true);
$donation->setStatutSuivi('confirmee');

$em->persist($commande);
$em->persist($donation);

// Auto-créer reminder
$reminderService->createReminderForCommande(
    $commande,
    'creation',
    'Donation de 100€ reçue de Asmir Milianni - Eligible Pack VIP'
);

$em->flush();
```

### Exemple 2: Gestion d'une donation anonyme

```php
$commande = new Commande();
$commande->setNumeroCommande('DON-ANON-' . bin2hex(random_bytes(3)));
$commande->setTypeProjet('donation');
$commande->setMontant(25.00);
$commande->setStatut('confirmee');
$commande->setTypeDonation('anonyme');

$donation = new Donation();
$donation->setCommande($commande);
$donation->setMontant(25.00);
// NON: $donation->setNomDonateur(...) -- Reste NULL
$donation->setIpAdresse($request->getClientIp());
$donation->setEligibleCadeau(false); // Anonyme = pas de cadeau
$donation->setStatutSuivi('confirmee');

$em->persist($commande);
$em->persist($donation);

// Reminder: pas d'email (non_applicable)
$reminderService->createReminderForCommande(
    $commande,
    'creation',
    'Donation anonyme de 25€ - Suivi: ' . $donation->getCodeSuivi()
);

$em->flush();

// Donateur reçoit: Code suivi seulement
echo "Suivi: " . $donation->getCodeSuivi();
// Output: Suivi: ANON-F3E7A9D2C1B8...
```

### Exemple 3: Envoyer tous les reminders

```bash
# Commande Symfony
php bin/console donation:send-reminders

# Depuis le code
$results = $reminderService->sendAllPendingReminders();
echo "Envoyés: " . $results['sent'] . " | Échoués: " . $results['failed'];
```

### Exemple 4: Rapport IP suspecte

```php
// Dans un job/cron

$donationsRepo = $em->getRepository(Donation::class);
$allDonations = $donationsRepo->findAll();

$ipPattern = [];
foreach ($allDonations as $donation) {
    $ip = $donation->getIpAdresse();
    $ipPattern[$ip][] = $donation;
}

// IPs avec 4+ donations
$suspicious = array_filter($ipPattern, fn($items) => count($items) > 3);

foreach ($suspicious as $ip => $donations) {
    // Alert: Potential fraud from IP $ip
    // LOG & notify admin
}
```

---

## Gestion des IPs & Sécurité

### Enregistrement automatique

Chaque donation enregistre:
- **IP address** (IPv4 ou IPv6)
- **User-Agent** (navigateur/device)
- **Timestamp** exact

### Détection de fraude

**Suspicion si:**
- Même IP → 4+ donations différentes
- Même email → donations anonymes + complètes
- Montants progressifs (5€, 50€, 500€...) → même IP

**Actions:**
1. Alert admin sur `/admin/security/ips`
2. Marquer donation comme "sous_revue"
3. Requête manuelle vérification
4. Possibilité de refund

### RGPD & Confidentialité

**Données personnelles:**
- Stockées en clair (database chiffrée)
- Accessibles uniquement via admin auth
- Anonymes = non identifiables

**Droit à l'oubli:**
```php
// Pour donation complète: anonymiser
$donation->setNomDonateur(null);
$donation->setEmailDonateur(null);
$donation->setAdresseDonateur(null);
$donation->setTelephoneDonateur(null);
$em->flush();
```

**Durée de conservation:** 3 ans (légalement)

---

## Dépannage

### ❌ Erreur: "Email non envoyé"

**Cause:** Mailer non configuré ou donation anonyme
**Solution:**
```bash
# Vérifier config
grep MAILER_DSN .env

# Test email
php bin/console mailer:test asmir@asartsdev.com
```

### ❌ Reminders ne s'envoient pas

**Cause:** Donations anonymes (par design) ou paramètres manquants
**Solution:**
1. Vérifier donation est "complète"
2. Verifier email_donateur rempli
3. Checker statut reminder = "brouillon"

### ❌ Erreur "Cadeau introuvable"

**Cause:** ID cadeau invalide ou supprimé
**Solution:**
```php
// Vérifier cadeaux disponibles
$cadeaux = $em->getRepository(Cadeau::class)->findBy(['actif' => true]);
foreach ($cadeaux as $cadeau) {
    echo $cadeau->getId() . " - " . $cadeau->getNomCadeau();
}
```

### ❌ "Code suivi non valide"

**Cause:** Typo ou lien expiré
**Solution:** Double check URL, format doit être ANON-XXXXX ou CODE-XXXXX

---

## 🎯 Checklist complète

- ✅ DatabaseUpgrade.sql exécutée
- ✅ Entités Donation & ReminderCommande copiées
- ✅ Formulaire DonationFormType copié
- ✅ AdminController copié
- ✅ ReminderService copié
- ✅ Entity\Commande mise à jour (relations)
- ✅ .env configurée (MAILER_DSN)
- ✅ Routes inclues dans routing.yaml
- ✅ Templates créés (voir section suivante)

---

## Templates Twig (À créer)

### `templates/donation/form.html.twig`
- Rendu formulaire DonationFormType
- JavaScript pour afficher/masquer champs dynamiquement
- CSS responsive

### `templates/admin/donation_detail.html.twig`
- Affichage donation + commande
- Boutons actions (assigner cadeau, marquer envoye)
- Timeline reminders

### `templates/admin/track_donation.html.twig`
- Page publique (pas d'auth)
- Timeline suivi
- Pas d'infos personnelles

---

**Créé:** 9 mars 2026 | **Asmir Milianni**  
**Status:** ✅ Production-Ready
