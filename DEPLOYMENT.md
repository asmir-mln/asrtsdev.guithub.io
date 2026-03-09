# 🚀 Déploiement AsArt'sDev sur GitHub Pages

## ⚠️ Résolution erreur 404

Si vous obtenez une erreur **404 - There isn't a GitHub Pages site here**, suivez ces étapes :

### Étape 1 : Activer GitHub Pages

1. **Allez sur la page des paramètres :**
   ```
   https://github.com/asmir-mln/asrtsdev.guithub.io/settings/pages
   ```

2. **Dans la section "Build and deployment" :**
   - **Source** : Sélectionnez `Deploy from a branch`
   - **Branch** : Sélectionnez `main` 
   - **Folder** : Sélectionnez `/ (root)`
   - Cliquez sur le bouton **Save**

3. **Attendez le déploiement (2-3 minutes)**

4. **Rechargez la page des Settings > Pages**
   - Vous devriez voir un message vert : "Your site is live at https://asmir-mln.github.io/asrtsdev.guithub.io/"

### Étape 2 : Vérifier le déploiement

1. **Allez sur l'onglet Actions :**
   ```
   https://github.com/asmir-mln/asrtsdev.guithub.io/actions
   ```

2. Vous devriez voir un workflow **"pages build and deployment"** en cours ou terminé avec ✅

3. Une fois terminé, visitez :
   ```
   https://asmir-mln.github.io/asrtsdev.guithub.io/
   ```

### Étape 3 : Test local (optionnel)

Pour tester le site en local avant de déployer :

```powershell
# Lancer un serveur web local
python -m http.server 8000

# Ou avec PHP
php -S localhost:8000

# Puis visitez : http://localhost:8000
```

## 📦 Contenu déployé

✅ **Pages principales :**
- `index.html` - Page d'accueil AsArt'sDev
- `home.html` - Max et Milla (livre enfant)
- `musee.html` - Musée virtuel avec synthèse vocale
- `bibliotheque.html` - Bibliothèque interactive
- `contact.html` - Formulaire de contact
- `commande.html` - Page de commande
- `paiement.html` - Paiement Stripe

✅ **Fonctionnalités :**
- Accessibilité WCAG 2.1 (ARIA labels, alt texts détaillés)
- Protection images
- Musée virtuel avec audio
- Bibliothèque dynamique

⚠️ **Note importante :**
Le dossier `api/` contient du code PHP qui ne fonctionnera pas sur GitHub Pages (HTML/CSS/JS uniquement). Pour activer l'API, il faudra un hébergement PHP séparé (Heroku, OVH, etc.).

## 🔗 Domaine personnalisé (optionnel)

Pour utiliser `asartsdev.com` :

1. Dans Settings > Pages > Custom domain : Entrez `asartsdev.com`
2. Configurez les DNS chez votre registrar :
   ```
   Type: A
   Host: @
   Value: 185.199.108.153
          185.199.109.153
          185.199.110.153
          185.199.111.153
   
   Type: CNAME
   Host: www
   Value: asmir-mln.github.io
   ```

## 📞 Support

Si le problème persiste :
- Vérifiez que le dépôt est **public** (pas privé)
- Assurez-vous d'avoir activé Pages dans Settings
- Attendez 5-10 minutes après la première activation
- Consultez : https://docs.github.com/pages

---

**Dernière mise à jour :** 9 mars 2026  
**Status :** ✅ Prêt pour déploiement
