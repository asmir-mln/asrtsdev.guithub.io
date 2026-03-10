# Copilot Coding Agent Instructions — AsArt'sDev

## Project Overview

**AsArt'sDev** is a French creative/innovation website and e-commerce platform built by **Asmir Milianni**.
It sells books and digital products, accepts donations, and showcases the author's story (autobiographical
narrative, accessible technology, digital art).

- **Live site:** deployed via GitHub Pages from the `main` branch
- **Primary language:** French (UI text, comments, documentation)
- **Author contact:** asartdev.contact@gmail.com

---

## Tech Stack

| Layer        | Technology                                |
|--------------|-------------------------------------------|
| Front-end    | Plain HTML5, CSS3, vanilla JavaScript     |
| Back-end     | PHP 7.4+ (API endpoints in `api/`)        |
| Payments     | Stripe (PHP SDK, `api/` folder) + bank transfer |
| Deployment   | GitHub Pages (`.github/workflows/deploy.yml`) |
| Package mgr  | npm (only for CLI tooling, no bundler)    |

There is **no build step** — the repository is deployed as-is to GitHub Pages.
Do **not** introduce bundlers (Webpack, Vite, Parcel) or transpilers unless explicitly requested.

---

## Repository Structure

```
.
├── index.html              # Home page (journey + Paris vision)
├── home.html               # "Max et Milla" children's book page
├── contact.html            # Contact form + serious enquiries section
├── commande.html           # Order page (3 products)
├── paiement.html           # Stripe payment form
├── donation.php            # Donation / bank-transfer alternative
├── confirmation-paiement.html
├── bibliotheque.html       # Digital library
├── musee.html              # Virtual museum
├── livres-audio.html       # Audio books
├── ia-devops.html          # AI/DevOps page
├── image-protection.html   # Image copyright policy
├── style.css               # Global styles
├── paiement-styles.css     # Payment-specific styles
├── protection-styles.css   # Image-protection styles
├── logo-style.css          # Logo styles
├── paiement.js             # Payment & form logic
├── tableaux.js             # Tables logic
├── cgv-popup.js            # CGV pop-up + e-signature
├── milian-nia.js           # Additional JS utilities
├── api/                    # PHP back-end
│   ├── config.example.php  # Stripe config template (never commit real keys)
│   ├── create-payment-intent.php
│   ├── verify-payment.php
│   └── webhook.php
├── LivresAsArtsDev/        # Book sub-pages and CGV
├── MemoLivreAutobiographique/
├── images/                 # Site images
├── composer.json           # PHP dependencies
├── package.json            # Node CLI tooling
├── .github/
│   ├── copilot-instructions.md   # This file
│   └── workflows/deploy.yml      # GitHub Pages deployment
└── README.md
```

---

## Local Development

### Static front-end (no PHP needed)
```bash
# Open any HTML file directly in a browser, or start a simple server:
python3 -m http.server 8000
# Then visit http://localhost:8000
```

### With PHP back-end
```bash
# Requires PHP 7.4+
php -S localhost:8000 -t .
# Then visit http://localhost:8000
```

### npm CLI tool
```bash
npm install          # No runtime dependencies; installs dev tooling only
npm test             # Runs: node api/cli-orders.js create && node api/cli-orders.js list
```

---

## Coding Conventions

- **HTML:** Semantic HTML5 elements (`<main>`, `<section>`, `<article>`, `<nav>`).
  Include `lang="fr"` on `<html>` tags.  All images must have meaningful `alt` attributes.
- **CSS:** Single global stylesheet (`style.css`) for shared styles; page-specific
  stylesheets for isolated concerns. Use CSS custom properties for colours/sizes.
  Mobile-first, responsive design.
- **JavaScript:** Vanilla ES6+ (no frameworks). Keep scripts small and focused.
  Avoid inline `onclick` handlers — use `addEventListener`.
- **PHP:** PSR-4 autoloading via Composer. Never hardcode secrets; use
  `api/config.php` (gitignored) derived from `api/config.example.php`.
- **Encoding:** UTF-8 everywhere. Ensure accented French characters render correctly.
- **Line endings:** LF (Unix).

---

## Security Rules

1. **Never commit real API keys, IBAN, or payment credentials.**
   Use `config.example.php` as a template; the real `config.php` is gitignored.
2. Validate and sanitise all user inputs server-side (PHP) before processing payments.
3. The `.env.local` file is gitignored — do not commit it.
4. Image-protection JavaScript (`milian-nia.js`, `image-protection.html`) must
   remain intact on all pages where images are displayed.

---

## Deployment

- Every push to `main` triggers `.github/workflows/deploy.yml` which publishes
  the site to GitHub Pages automatically.
- There is no staging environment — test locally before merging to `main`.
- The `.nojekyll` file prevents GitHub Pages from treating the repo as a Jekyll site.

---

## What the Agent Should NOT Do

- Do not remove or weaken image-protection mechanisms.
- Do not commit secrets, real payment credentials, or personal data (IBAN, phone, email).
- Do not add npm/Composer dependencies without documenting them.
- Do not change the deployment workflow unless there is a specific bug to fix.
- Do not convert the site to a JavaScript framework (React, Vue, etc.) unless explicitly asked.
- Do not delete or rename existing HTML pages — external links may depend on them.
