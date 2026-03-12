# Guide de développement local — AsArt'sDev

## Prérequis

- [XAMPP](https://www.apachefriends.org/fr/index.html) installé (Apache + PHP)
- **ou** PHP installé séparément (≥ 7.4)
- Un navigateur web (Chrome, Firefox, Edge…)

---

## Option 1 — XAMPP (recommandé sur Windows)

1. **Démarrez XAMPP** → lancez **Apache** depuis le panneau de contrôle XAMPP.
2. **Copiez le projet** dans le dossier `htdocs` de XAMPP :
   ```
   C:\xampp\htdocs\asrtsdev.guithub.io\
   ```
3. **Ouvrez votre navigateur** et accédez à :
   ```
   http://localhost/asrtsdev.guithub.io/
   ```

> ⚠️ **Important** : l'URL doit être saisie dans la **barre d'adresse du navigateur**,
> pas dans PowerShell ou le terminal. Taper une URL dans PowerShell provoque
> l'erreur `CommandNotFoundException`.

---

## Option 2 — Serveur PHP intégré

```bash
# Depuis le dossier du projet (remplacez par votre chemin réel)
cd "C:\chemin\vers\asrtsdev.guithub.io"

# Lancer le serveur PHP
php -S localhost:8000

# Puis ouvrir dans le navigateur
# http://localhost:8000
```

---

## Vérifier le contenu du dossier (PowerShell)

Pour lister les fichiers du projet en PowerShell :

```powershell
# Remplacez par votre chemin réel
cd "C:\chemin\vers\asrtsdev.guithub.io"
dir
```

---

## Pages principales

| Fichier | Description |
|---------|-------------|
| `index.html` | Accueil |
| `home.html` | Max et Milla |
| `contact.html` | Formulaire de contact |
| `commande.html` | Page de commande |
| `paiement.html` | Paiement Stripe |
| `donation.php` | Don / virement bancaire |

---

## Déploiement (GitHub Pages)

Le site est automatiquement déployé via GitHub Actions à chaque push sur `main`.

URL publique : `https://asmir-mln.github.io/asrtsdev.guithub.io/`

---

*AsArt'sDev — Asmir Milianni © 2026*
