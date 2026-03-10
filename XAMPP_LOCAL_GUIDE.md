# 🖥️ Guide de Test Local avec XAMPP

Ce guide explique comment tester le site **AsArt'sDev** en local sur Windows avec XAMPP.

---

## ⚠️ Erreur courante à éviter

Ne jamais coller une URL directement dans PowerShell :

```powershell
# ❌ INCORRECT — provoque "CommandNotFoundException"
http://localhost/asrtsdev.guithub.io/

# ✅ CORRECT — ouvrir dans le navigateur par défaut
Start-Process "http://localhost/asrtsdev.guithub.io/"
```

---

## 🚀 Étapes pour lancer le site en local

### 1. Installer et démarrer XAMPP

1. Télécharger XAMPP : <https://www.apachefriends.org/fr/index.html>
2. Lancer **XAMPP Control Panel** (en tant qu'administrateur)
3. Cliquer sur **Start** à côté d'**Apache**
4. Le voyant doit passer au vert ✅

### 2. Placer les fichiers du site

Copier le dossier du projet dans le répertoire `htdocs` de XAMPP :

```powershell
# Depuis PowerShell — copier le projet dans htdocs
Copy-Item -Recurse -Force "C:\Users\Asmir\Documents\e com\asrtsdev.guithub.io" `
  "C:\xampp\htdocs\asrtsdev.guithub.io"
```

> **Ou** créer un lien symbolique pour ne pas dupliquer les fichiers :
>
> ```powershell
> # Exécuter PowerShell en tant qu'administrateur
> New-Item -ItemType Junction `
>   -Path "C:\xampp\htdocs\asrtsdev.guithub.io" `
>   -Target "C:\Users\Asmir\Documents\e com\asrtsdev.guithub.io"
> ```

### 3. Ouvrir le site dans le navigateur

```powershell
# Ouvrir le site dans le navigateur par défaut
Start-Process "http://localhost/asrtsdev.guithub.io/"

# Ou ouvrir une page spécifique
Start-Process "http://localhost/asrtsdev.guithub.io/index.html"
Start-Process "http://localhost/asrtsdev.guithub.io/home.html"
```

### 4. Valider les fichiers du site

```powershell
# Naviguer dans le dossier du projet
Set-Location "C:\Users\Asmir\Documents\e com\asrtsdev.guithub.io"

# Vérifier que Node.js est installé
node --version

# Lancer le validateur de site
node validate-site.js
```

---

## 🐍 Alternative sans XAMPP (Python)

Si Python est installé, cette méthode est plus simple :

```powershell
# Naviguer dans le dossier
Set-Location "C:\Users\Asmir\Documents\e com\asrtsdev.guithub.io"

# Lancer un serveur HTTP local
python -m http.server 8000

# Puis ouvrir dans le navigateur
Start-Process "http://localhost:8000"
```

---

## 🔧 Résolution des problèmes courants

| Problème | Solution |
|---|---|
| `CommandNotFoundException` en collant une URL dans PowerShell | Utiliser `Start-Process "http://..."` |
| Apache ne démarre pas (port 80 occupé) | Changer le port dans XAMPP → Apache → Config → `httpd.conf` |
| Page 403 Forbidden | Vérifier les permissions du dossier dans `htdocs` |
| Fichiers PHP ne s'exécutent pas | S'assurer que XAMPP Apache est démarré (pas juste PHP CLI) |
| `node` non reconnu | Installer Node.js depuis <https://nodejs.org> |

---

## ✅ Checklist avant déploiement

```powershell
# Dans le dossier du projet
Set-Location "C:\Users\Asmir\Documents\e com\asrtsdev.guithub.io"

# 1. Valider le site
node validate-site.js

# 2. Vérifier les modifications Git
git status
git diff --stat

# 3. Commiter et pousser
git add .
git commit -m "Mise à jour du site"
git push origin main
```

Après le `push`, GitHub Pages déploiera automatiquement en 2–3 minutes.  
URL du site déployé : **<https://asmir-mln.github.io/asrtsdev.guithub.io/>**

---

**Dernière mise à jour :** mars 2026
