# AsArt'sDev — Site Officiel 🚀

**Auteur :** Asmir Milianni  
**Contact :** 📧 asartdev.contact@gmail.com | 📱 0781586882  
**Positionnement :** Laboratoire d'innovation — Paris 2026

---

## 📌 À propos du projet

**AsArt'sDev** est un écosystème d'innovation fondé par **Asmir Milianni**, combinant :

- 📚 **Édition autobiographique** — Récits vrais d'un parcours d'autodidacte dyslexique à technologue
- 🧠 **Technologie accessible** — IA, DevOps, solutions inclusives pour malvoyance/surdité/mobilité
- 🎨 **Création numérique** — Illustrations et designs 3D
- 💰 **Investissement d'impact** — Financement de projets avec impact social

**Produits phares :**
- Max et Milla (enfant, 15€ précommande)
- Les trois vies d'Asmir (autobiographie adulte, 39,99€)

---

## 🌍 Vision : Paris, Berceaux d'innovation et d'accessibilité

AsArt'sDev construit à Paris un **labo où récit personnel, technologie éthique et impact social deviennent une force commerciale**.

**4 piliers :**
1. **Accessibilité & Médecine** — Transformer handicaps en innovations produit
2. **Tech for Good** — Startups en santé, IA éthique, solutions durables
3. **Communautés créatives** — Prototypage collaboratif (illustrateurs, devs, entrepreneurs)
4. **Écosystème de financement** — Accès à VCs, business angels, CIVIC, CNL, FONCAP

---

## 📁 Structure du projet

```
.
├── index.html                    # Accueil (Mon Parcours 3 images + Vision Paris)
├── home.html                     # Max et Milla (version enfant)
├── contact.html                  # Formulaire contact + Démarches sérieuses
├── commande.html                 # Page commande (3 produits)
├── paiement.html                 # Formulaire paiement Stripe
├── donation.php                  # Don / Achat / Partenariat (virement bancaire)
├── image-protection.html         # Politique protection images
│
├── style.css                     # Styles globaux
├── paiement.js                   # JavaScript paiement & formulaires
├── tableaux.js                   # Logique tableaux
├── cgv-popup.js                  # Pop-up CGV + signature électronique
│
├── api/                          # Backend PHP
│   ├── config.example.php        # Config Stripe (template)
│   ├── create-payment-intent.php
│   ├── verify-payment.php
│   └── webhook.php
│
├── LivresAsArtsDev/              # Sous-dossier produits
│   ├── cgv.html                  # Conditions Générales de Vente
│   └── livres/
│       ├── aventures-animaux/    # "Max et Milla"
│       │   └── index.html
│       ├── porjetbio adulte/
│       └── souvenir-enfance/
│           └── index.html
│
├── MemoLivreAutobiographique/    # Ressources autobiographie
│   └── pdf_version.pdf           # Aperçu PDF (39,99€)
│
├── API_PAIEMENT_README.md        # Doc API
├── LANCEMENT_INTERNATIONAL.md    # Guide lancement FR/EN
├── note.txt                      # Options hosting (GitHub Pages, Cloudflare)
├── .gitignore
└── README.md                     # Ce fichier
```

---

## 🛍️ Catalogue produits

| Produit | Prix | Statut | Description |
|---------|------|--------|-------------|
| **Max et Milla** (enfant) | 15€ | 📅 Précommande | Histoire vraie, cycle des 10 ans, illustrations |
| **Biographie** | Devis | 📋 Devis | Version imprimée + envoi personnalisé |
| **Les trois vies d'Asmir** (adulte) | 39,99€ | 📝 Évolution | Manuscrit non finalisé, version illustrée par l'auteur |
| **Don libre** | Variable | ✅ Actif | Supporters du projet |

---

## 💳 Moyens de paiement

### 1. Virement bancaire (PRIMARY)
```
Bénéficiaire : Samir Milianni
IBAN        : FR76 **** **** **** **** **** 122 (masqué publiquement)
BIC         : REVOFRP2 (Revolut Bank UAB)
Détails complets : envoyés par email après validation formulaire
```

### 2. Stripe (en préparation)
- Paiements sécurisés par carte
- Webhooks configurés pour confirmations

### 3. PayPal (optionnel)
- Alternative de paiement

---

## 🔐 Sécurité & Protection

✅ **Copyright** — Tous contenus © AsArt'sDev  
✅ **Image Protection** — Téléchargement + drag-drop bloqués  
✅ **RGPD Compliant** — Données clients sécurisées  
✅ **E-signature** — Pop-up CGV obligatoire  
✅ **Form Validation** — Email, téléphone, adresse vérifiés  
✅ **Accessible Design** — Navigation au clavier, contrast ratios OK  

---

## 📞 Contact & Engagement

### Pour tous types de demandes
📧 **Email :** [asartdev.contact@gmail.com](mailto:asartdev.contact@gmail.com)  
📱 **Téléphone :** [0781586882](tel:0781586882)  
⏰ **Réponse :** moins de 48h ouvrées

### Démarches sérieuses (emploi, recrutement, financement)
👉 Section dédiée sur [contact.html](contact.html)  
Accès direct : téléphone + email prioritaire

---

## 🚀 Déploiement

### Hosting gratuit (recommandé)
```bash
# GitHub Pages (+ custom domain possible)
https://asartsdev.github.io

# Alternative : Cloudflare Pages
https://asartsdev.pages.dev
```

### Installation locale
```bash
git clone https://github.com/asartsdev/asartsdev.github.io.git
cd asartsdev.github.io

# Serveur PHP local
php -S localhost:8000

# Accès
http://localhost:8000
```

---

## 📊 État du projet

### ✅ Complété
- [x] Pages HTML (index, home, contact, commande, paiement, donation)
- [x] Informations de contact (téléphone + email)
- [x] Infrastructure paiement (virement bancaire + Stripe template)
- [x] Protection images (JavaScript + alerts)
- [x] Vision de Paris (3 images + 4 piliers)
- [x] Démarches sérieuses (section contact.html)
- [x] Nettoyage fichiers (repo prêt GitHub)
- [x] Encodage UTF-8 (français correct)
- [x] Responsive design (mobile/tablet/desktop)
- [x] Accessibilité de base (alt text, labels, semantic HTML)

### ⏳ Todo (post-lancement)
- [ ] Stripe API keys (production)
- [ ] Email backend (PHP mailer)
- [ ] Analytics (Google/Matomo)
- [ ] CDN pour images (Cloudinary/Bunny)
- [ ] Blog / Updates (section news)
- [ ] Webhook email notifications

---

## 🔗 Pages principales

- **[Accueil](index.html)** — Parcours 3 images + Vision Paris + CTA
- **[Contact](contact.html)** — Formulaire + Démarches sérieuses
- **[Commander](commande.html)** — Produits + formulaire commande
- **[Paiement](paiement.html)** — Stripe integration
- **[Don/Achat](donation.php)** — Virement bancaire alternative
- **[Protection](image-protection.html)** — Politique droits d'auteur
- **[CGV](LivresAsArtsDev/cgv.html)** — Conditions Générales

---

## 📝 Licence & Crédits

© 2026 **AsArt'sDev** — Asmir Milianni  
Tous droits réservés.

**Marque :** ASARTSDEV (en développement)  
**Logo :** ChatGPT Image ™  
**PDFs :** Ressources autobiographiques propriétaires  

Voir [CGV complètes](LivresAsArtsDev/cgv.html) pour détails légaux.

---

## 🤝 Contribuer / Partenariat

Pour investisseurs, recruteurs, partenaires publics/privés :

👉 **[contact.html — Section "Démarches Sérieuses"](contact.html)**  
📧 asartdev.contact@gmail.com  
📱 0781586882

---

**Dernière mise à jour :** 8 mars 2026  
**Statut :** Prêt pour GitHub Pages push ✅

