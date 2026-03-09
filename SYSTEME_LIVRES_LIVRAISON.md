# 📚 SYSTÈME COMPLET LIVRES & LIVRAISON

## Vue d'ensemble

Système complet de gestion des 3 livres AsArt'sDev avec :
- ✅ **Bandes audio** (lectures intégrales)
- ✅ **Commentaires audio** de l'auteur
- ✅ **Suivi de livraison** en temps réel
- ✅ **Newsletters automatiques** à chaque évolution
- ✅ **Dédicaces personnalisées**
- ✅ **Estimation de prix** selon options

---

## 📖 LES 3 LIVRES

### Livre 1 - Les premières lueurs
- **Prix:** 35,00 €
- **Pages:** 380
- **Poids:** 0,65 kg
- **ISBN:** 978-2-ASART-001-1
- **Statut:** Disponible (50 exemplaires)

### Livre 2 - Entre ombres et lumières
- **Prix:** 38,00 €
- **Pages:** 420
- **Poids:** 0,72 kg
- **ISBN:** 978-2-ASART-002-8
- **Statut:** Disponible (45 exemplaires)

### Livre 3 - Entre rêves et tempête
- **Prix:** 42,00 €
- **Pages:** 450
- **Poids:** 0,78 kg
- **ISBN:** 978-2-ASART-003-5
- **Statut:** Disponible (40 exemplaires)

---

## 🎧 SYSTÈME AUDIO

### Types de bandes audio

1. **Introduction** (GRATUIT)
   - Présentation par l'auteur
   - ~8-10 minutes
   - Accessible à tous

2. **Lectures intégrales** (PREMIUM)
   - Lecture complète de chaque chapitre
   - ~35-45 minutes par chapitre
   - Accès réservé aux acheteurs

3. **Extraits** (GRATUIT)
   - Passages sélectionnés
   - ~5-10 minutes
   - Prévisualisation gratuite

### Types de commentaires audio

1. **Contexte**
   - Explications sur le contexte d'écriture
   - Choix narratifs
   - Accès gratuit ou premium selon le contenu

2. **Anecdotes**
   - Histoires vraies derrière les scènes
   - Souvenirs personnels
   - Généralement premium

3. **Analyses**
   - Décryptage des symboliques
   - Réflexions thématiques
   - Premium

4. **Réflexions**
   - Messages personnels de l'auteur
   - Philosophie créative
   - Mixte

### Statistiques audio

Chaque fichier audio enregistre :
- ✅ Nombre d'écoutes
- ✅ Durée formatée (HH:MM:SS)
- ✅ Type et ordre de lecture
- ✅ Transcription textuelle (pour accessibilité)

---

## 📦 SYSTÈME DE LIVRAISON

### Types d'envoi disponibles

#### 1. **Standard** (5,69 €)
- Délai: 5 jours ouvrés
- Transporteur: Colissimo
- Suivi: Oui
- Zone: France métropolitaine

#### 2. **Express** (8,99 €)
- Délai: 2 jours ouvrés
- Transporteur: La Poste Express
- Suivi: Oui
- Zone: France métropolitaine

#### 3. **Express Retour** (15,00 €)
- Délai: 24h
- Transporteur: UPS
- Suivi: Oui + retour possible
- Zone: France métropolitaine

#### 4. **Avec Dédicace** (30,00 €)
- Délai: 3-5 jours ouvrés
- Transporteur: Spécial
- **Inclut:**
  - Aller-retour chez l'auteur
  - Dédicace personnalisée manuscrite
  - Emballage spécial
  - Certificat d'authenticité
  - Suivi complet

### Suppléments tarifaires

```php
// Supplément poids (au-delà de 1kg)
Si poids > 1kg: +2,00 € par kg supplémentaire

// Supplément assurance (livres >100€)
Si prix livre > 100€: +5,00 €

// Suppléments géographiques
Europe: +6,80 € | Délai +5 jours
Monde: +19,30 € | Délai +15 jours

// Dédicace ajoutée à un envoi standard
Dédicace seule: +25,00 € | Délai +2 jours
```

### Exemple de calculs

#### Livre 1 - Envoi standard France
```
Prix livre:     35,00 €
Livraison std:   5,69 €
Poids (0,65kg):  0,00 € (pas de supplément)
─────────────────────────
TOTAL:          40,69 €
Délai: 5 jours
```

#### Livre 1 - Avec dédicace personnalisée
```
Prix livre:     35,00 €
Envoi dédicace: 30,00 €
─────────────────────────
TOTAL:          65,00 €
Délai: 3-5 jours

Comprend:
✅ Dédicace manuscrite personnalisée
✅ Visite chez l'auteur pour signature
✅ Emballage premium
✅ Certificat d'authenticité
✅ Suivi complet aller-retour
```

#### Pack 3 livres - Standard
```
Livre 1:        35,00 €
Livre 2:        38,00 €
Livre 3:        42,00 €
Livraison:       5,69 €
Supp. poids:     2,00 € (poids total 2,15kg)
─────────────────────────
TOTAL:         122,69 €
Délai: 5 jours
```

---

## 🚚 SUIVI DE LIVRAISON

### Statuts disponibles

1. **en_attente**
   - Commande reçue
   - En attente de traitement

2. **preparation**
   - Commande en cours de préparation
   - Emballage du colis

3. **pret_expedition**
   - Colis prêt
   - Attente d'enlèvement transporteur

4. **envoye**
   - Colis expédié
   - Numéro de suivi généré
   - **→ EMAIL AUTOMATIQUE ENVOYÉ**

5. **en_transit**
   - Colis en cours d'acheminement
   - Localisation mise à jour régulièrement
   - **→ NEWSLETTER SUIVI ENVOYÉE**

6. **livre**
   - Colis livré avec succès
   - Date de livraison réelle enregistrée
   - **→ EMAIL CONFIRMATION ENVOYÉ**

### Historique de suivi

Chaque changement de statut crée une entrée dans `historique_suivi` avec :
- Date/heure exacte
- Description du statut
- Localisation actuelle
- Email envoyé: oui/non
- Newsletter envoyée: oui/non

### Exemple d'historique

```
📦 Commande LIV-65F8A9BC123

09/03/2026 10:23  |  en_attente
                  |  "Commande reçue et en attente de traitement"
                  |  ✅ Email de confirmation envoyé

09/03/2026 14:45  |  preparation
                  |  "Votre commande est en cours de préparation"
                  |  ✅ Notification envoyée

10/03/2026 09:15  |  envoye
                  |  "Votre colis a été expédié"
                  |  Numéro suivi: 6C12345678901234
                  |  ✅ Newsletter envoyée

10/03/2026 18:30  |  en_transit
                  |  Localisation: "Centre de tri Paris"
                  |  ✅ Mise à jour envoyée

12/03/2026 11:00  |  en_transit
                  |  Localisation: "Agence locale Lyon"
                  |  ✅ Newsletter envoyée

13/03/2026 14:20  |  livre
                  |  "Votre colis a été livré avec succès"
                  |  ✅ Email de confirmation envoyé
```

---

## 📧 NEWSLETTERS AUTOMATIQUES

### Déclencheurs automatiques

Chaque changement de statut déclenche **automatiquement** :

1. **Envoi d'email** au client
2. **Enregistrement** dans `newsletter_suivi`
3. **Création d'entrée** dans `historique_suivi`
4. **Notification** à l'administrateur

### Contenus des emails

#### Email "Commande reçue"
```
Sujet: 📨 Commande reçue - AsArt'sDev

Bonjour [Nom],

Nous avons bien reçu votre commande LIV-XXXXX.

📚 Livre commandé: [Titre]
💰 Montant total: XX,XX €
📦 Type d'envoi: [Type]
⏱️ Délai estimé: X jours

Votre commande sera traitée dans les plus brefs délais.

Merci de votre confiance ! 🙏
```

#### Email "Colis expédié"
```
Sujet: 🚚 Votre colis est parti !

Bonjour [Nom],

Bonne nouvelle ! Votre colis a été expédié.

📦 Numéro de suivi: 6C12345678901234
🚛 Transporteur: Colissimo
📅 Livraison estimée: XX/XX/XXXX

[Bouton: Suivre ma livraison]

L'équipe AsArt'sDev
```

#### Email "Colis livré"
```
Sujet: ✅ Votre colis est arrivé !

Bonjour [Nom],

🎉 Félicitations ! Votre colis a été livré avec succès.

Nous espérons que vous apprécierez votre lecture !

N'hésitez pas à :
- Écouter les bandes audio incluses 🎧
- Découvrir les commentaires de l'auteur 💬
- Nous partager votre avis 📝

Bonne lecture ! 📖

L'équipe AsArt'sDev
```

---

## ✍️ DÉDICACES PERSONNALISÉES

### Processus complet

#### Étape 1: Demande du client
Le client commande avec option "dédicace personnalisée" et fournit :
- Nom du destinataire
- Message souhaité (max 200 caractères)
- Adresse de livraison finale

#### Étape 2: Notification à l'auteur
Email automatique envoyé à `sam.mln51@icloud.com` :
```
Sujet: ✍️ Nouvelle demande de dédicace

Bonjour Asmir,

Une nouvelle demande de dédicace vient d'arriver !

📚 Livre: [Titre]
👤 Client: [Nom]
✉️ Contact: [Email]

💬 Message souhaité:
"[Texte de la dédicace demandée]"

📍 Adresse de livraison:
[Adresse complète]

[Bouton: Accepter la demande]
[Bouton: Contacter le client]
```

#### Étape 3: Préparation
- Le livre est envoyé à l'adresse de l'auteur
- L'auteur crée la dédicace manuscrite personnalisée
- Photos prises pour archives + satisfaction client

#### Étape 4: Expédition au client
- Emballage premium avec certificat d'authenticité
- Envoi avec suivi Colissimo
- Newsletter automatique avec photos de la dédicace

#### Étape 5: Suivi
Le client reçoit des mises à jour automatiques :
- ✅ Demande validée
- ✅ Livre reçu par l'auteur
- ✅ Dédicace réalisée (avec photo)
- ✅ Expédition en cours
- ✅ Livraison effectuée

### Prix dédicace

```
Dédicace complète:          30,00 €

Comprend:
- Aller chez l'auteur:       7,50 €
- Temps de dédicace:        10,00 €
- Retour au client:          7,50 €
- Emballage premium:         3,00 €
- Certificat authenticité:   2,00 €
─────────────────────────────────
Total:                      30,00 €
```

### Formules spéciales

#### Pour dons > 1000 €
Les donateurs de plus de 1000 € bénéficient **GRATUITEMENT** de :
- ✅ Pack 3 livres dédicacés
- ✅ Visite personnelle possible (si détour < 100km)
- ✅ Rencontre avec l'auteur (visio ou présentiel)
- ✅ Attestation de partenariat signée
- ✅ Visibilité comme mécène officiel
- ✅ Newsletter personnalisée mensuelle

---

## 🎁 FORMULES CADEAUX EXISTANTES

### Pack Découverte (5,00 €)
- 1 eBook gratuit
- Accès 1 mois bibliothèque numérique
- Insider letter exclusive

### Pack Bibliothèque 1 An (25,00 €)
- Accès 1 an bibliothèque complète
- 12 × Insider letters
- 5 eBooks exclusifs
- Webinaires mensuels

### Livre Édition Premium (50,00 €)
- 1 Livre édition premium (400 pages)
- Signature auteur
- Bookmark personnalisé
- Certificat d'authenticité

### Pack 3 Livres Auteur (100,00 €)
- 3 Livres édition spéciale
- Coffret premium
- Poster signé
- Meeting virtuel avec l'auteur

### Expérience VIP (500,00 €)
- Tous les livres
- Rencontre en visio privée
- Consultation stratégique 2h
- Crédits pour formations

### Partenariat & Dédicace (1000,00 €+)
- Tous les contenus
- **Visite personnelle pour dédicace** si détour < 100km
- Partenariat formalisé
- Visibilité publique comme mécène
- Newsletter personnalisée

---

## 🔧 API ENDPOINTS

### Livres

```http
GET /api/livres/with-audio
Retourne tous les livres avec leurs bandes audio et commentaires

GET /api/livres/{id}
Détails d'un livre spécifique

POST /api/livres/commander
Crée une commande pour un livre

POST /api/livres/demande-dedicace
Demande une dédicace personnalisée
```

### Audio

```http
POST /api/livres/audio/{id}/ecoute
Enregistre une écoute de bande audio

POST /api/livres/commentaire/{id}/ecoute
Enregistre une écoute de commentaire
```

### Exemple de requête

```json
POST /api/livres/commander

{
  "livre_id": 1,
  "type_envoi": "dedicace",
  "dedicace_demandee": true,
  "dedicace_texte": "Pour Marie, avec toute mon amitié. Continue de rêver ! - Asmir",
  "nom": "Marie Dupont",
  "email": "marie@exemple.fr",
  "adresse_livraison": {
    "rue": "12 rue de la Paix",
    "ville": "Lyon",
    "code_postal": "69001",
    "pays": "France"
  }
}
```

### Réponse

```json
{
  "success": true,
  "message": "Commande créée avec succès",
  "commande": {
    "numero_commande": "LIV-65F8A9BC123",
    "montant_livre": 35.00,
    "montant_livraison": 30.00,
    "montant_total": 65.00,
    "delai_estime": "3-5 jours",
    "type_envoi": "dedicace",
    "dedicace": true
  }
}
```

---

## 📊 BASE DE DONNÉES

### Tables créées

1. **livres**
   - Informations sur les 3 livres
   - Stock disponible
   - Prix, poids, ISBN

2. **bandes_audio**
   - Fichiers audio des lectures
   - Durée, ordre, type
   - Compteur d'écoutes

3. **commentaires_audio**
   - Commentaires de l'auteur
   - Transcriptions
   - Références (chapitre/page)

4. **livraisons**
   - Suivi des expéditions
   - Statuts, dates
   - Options de dédicace

5. **historique_suivi**
   - Historique complet de chaque livraison
   - Localisations
   - Emails envoyés

6. **tarif_livraison**
   - Grille tarifaire
   - Par transporteur, zone, poids

7. **newsletter_suivi**
   - Toutes les newsletters envoyées
   - Contenu HTML
   - Dates d'envoi

8. **formules_cadeaux**
   - Formules prédéfinies
   - Contenu, prix

### View SQL

```sql
CREATE VIEW livres_avec_audio AS
SELECT 
    l.id,
    l.titre,
    l.numero_livre,
    COUNT(DISTINCT ba.id) as nombre_bandes_audio,
    COUNT(DISTINCT ca.id) as nombre_commentaires,
    SUM(ba.duree_secondes) as duree_totale_lecture,
    SUM(ca.duree_secondes) as duree_totale_commentaires
FROM livres l
LEFT JOIN bandes_audio ba ON l.id = ba.livre_id
LEFT JOIN commentaires_audio ca ON l.id = ca.livre_id
GROUP BY l.id;
```

---

## 🚀 WORKFLOW COMPLET

### Scénario 1: Achat simple

1. Client commande Livre 1 (standard)
2. ✅ Email "Commande reçue" envoyé
3. Admin prépare le colis
4. Statut → "preparation"
   - ✅ Newsletter envoyée
5. Colis expédié
6. Statut → "envoye"
   - ✅ Email avec n° suivi envoyé
7. Colis en transit
8. Statut → "en_transit" (plusieurs fois)
   - ✅ Newsletter à chaque mise à jour
9. Colis livré
10. Statut → "livre"
    - ✅ Email confirmation envoyé

### Scénario 2: Achat avec dédicace

1. Client commande Livre 2 avec dédicace
2. ✅ Email "Demande reçue" au client
3. ✅ Email notification à `sam.mln51@icloud.com`
4. Livre envoyé à l'auteur
5. Statut → "envoye" (vers auteur)
   - ✅ Newsletter "En route vers l'auteur"
6. Auteur reçoit le livre
7. Statut → "dedicace_en_cours"
   - ✅ Newsletter "Dédicace en cours de création"
8. Auteur fait la dédicace + photo
9. Livre expédié au client final
10. Statut → "envoye" (vers client)
    - ✅ Newsletter avec photo de la dédicace
11. Statut → "en_transit"
    - ✅ Mises à jour régulières
12. Statut → "livre"
    - ✅ Email "Profitez de votre livre dédicacé !"

### Scénario 3: Grand mécène (>1000€)

1. Don de 1500 € effectué
2. ✅ Email remerciement + détails formule
3. ✅ Notification admin pour préparation
4. Admin contacte le client:
   - "Souhaitez-vous une visite personnelle ?"
   - "Quel message pour les dédicaces ?"
5. Si visite possible (< 100km détour)
   - Planification de la rencontre
   - Dédicace sur place
   - Remise personnelle
6. Sinon envoi des 3 livres dédicacés
7. + Attestation de partenariat signée
8. + Newsletter personnalisée mensuelle

---

## ✅ CHECKLIST INSTALLATION

### 1. Base de données

```bash
# Créer les tables livraison
mysql -u root -p nom_base < DatabaseLivraison.sql

# Créer les tables livres & audio
mysql -u root -p nom_base < DatabaseLivresAudio.sql
```

### 2. Entities Symfony

Les entities sont dans `/api/Entity/` :
- ✅ Livre.php
- ✅ BandeAudio.php
- ✅ CommentaireAudio.php
- (+ Commande.php, Livraison.php existants)

### 3. Controllers

- ✅ LivreController.php → `/api/Controller/`

### 4. Services

- ✅ LivreService.php → `/api/Service/`
- (+ NotificationService.php existant)

### 5. Frontend

- ✅ livres-audio.html → Page affichage des livres

### 6. Configuration emails

Vérifier `.env.local` :
```env
MAILER_DSN=smtp://sam.mln51@icloud.com:PASSWORD@smtp.mail.icloud.com:587?encryption=tls
ADMIN_EMAIL=sam.mln51@icloud.com
CONTACT_EMAIL=asartsdev.contact@gmail.com
```

### 7. Migrations Doctrine

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### 8. Tests

```bash
# Tester l'API livres
curl http://localhost/api/livres/with-audio

# Tester une commande
curl -X POST http://localhost/api/livres/commander \
  -H "Content-Type: application/json" \
  -d '{"livre_id":1,"type_envoi":"standard","nom":"Test","email":"test@test.fr"}'
```

---

## 📝 NOTES IMPORTANTES

### Emails automatiques

**TOUS les emails sont envoyés automatiquement** à chaque changement de statut. Aucune intervention manuelle requise !

### Newsletters

Les newsletters sont **générées avec HTML complet** incluant :
- En-tête avec gradient or
- Badges de statut colorés
- Timeline de progression
- Boutons d'action
- Footer avec contacts

### Dédicaces > 1000€

Pour les grands mécènes, **vérifier la distance** avant de proposer visite personnelle :
- Si < 100km de détour → Visite possible
- Si > 100km → Envoi avec dédicace à distance

### Fichiers audio

Les fichiers audio doivent être placés dans :
```
/public/audio/
  ├── livre1/
  │   ├── intro.mp3
  │   ├── chapitre01.mp3
  │   └── commentaires/
  │       ├── titre.mp3
  │       └── anecdote_parc.mp3
  ├── livre2/
  └── livre3/
```

---

## 🎯 PROCHAINES ÉTAPES

1. ✅ Ajouter les vraies bandes audio
2. ✅ Enregistrer les commentaires de l'auteur
3. ✅ Tester le workflow complet commande → livraison
4. ✅ Vérifier les emails automatiques
5. ✅ Ajuster les templates de newsletters
6. ✅ Configurer les alertes admin

---

**Système créé le:** 9 mars 2026  
**Version:** 1.0  
**Auteur:** AsArt'sDev  
**Contact:** asartsdev.contact@gmail.com
