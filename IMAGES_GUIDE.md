# 📸 Guide de Gestion des Images - AsArt'sDev

## 📁 Structure des Dossiers

```
images/
├── session-mars-2026/        # Session photo actuelle (images récentes)
│   └── placeholder-original.png  # Ancienne image renommée
├── livres/                   # Couvertures des 3 livres autobiographiques
│   ├── livre1-souvenirs-enfance.jpg
│   ├── livre2-entre-reves-tempete.jpg
│   └── livre3-vision-technologique.jpg
├── musee/                    # Tableaux pour le musée virtuel
│   ├── tableau-001.jpg
│   ├── tableau-002.jpg
│   └── tableau-003.jpg
├── accueil/                  # Images pour la page d'accueil
│   ├── parcours-autodidacte.jpg
│   ├── decouverte-scientifique.jpg
│   └── vision-innovation.jpg
└── animaux/                  # Illustrations pour les histoires d'animaux
    ├── animal-illustration-01.jpg
    ├── animal-illustration-02.jpg
    └── animal-illustration-03.jpg
```

---

## 🏷️ Convention de Nommage (éviter doublons)

### Format recommandé :
```
[categorie]-[description]-[date].extension
```

### Exemples :
- `livre-souvenirs-enfance-2026-03-09.jpg`
- `musee-tableau-abstrait-2026-03-09.png`
- `accueil-hero-banner-2026-03-09.jpg`
- `animal-chat-aventure-2026-03-09.jpg`

### Règles :
1. ✅ **Minuscules uniquement** : `livre-01.jpg` (pas `Livre-01.JPG`)
2. ✅ **Tirets pour séparer** : `musee-tableau-01.jpg` (pas `musee_tableau_01.jpg`)
3. ✅ **Date ISO** : `2026-03-09` (année-mois-jour)
4. ✅ **Description claire** : `parcours-autodidacte.jpg` (pas `img123.jpg`)

---

## 📋 Images Requises par Page

### **index.html** (Page d'accueil)
| Ligne | Description | Chemin recommandé | Dimensions |
|-------|-------------|-------------------|------------|
| 463 | Parcours autodidacte | `images/accueil/parcours-autodidacte.jpg` | 1200x260px |
| 469 | Découverte scientifique | `images/accueil/decouverte-scientifique.jpg` | 1200x260px |
| 475 | Vision technologique | `images/accueil/vision-technologique.jpg` | 1200x260px |

### **home.html** (Créations littéraires)
| Ligne | Description | Chemin recommandé | Dimensions |
|-------|-------------|-------------------|------------|
| 37 | Illustration animal 1 | `images/animaux/animal-illustration-01.jpg` | 800x600px |
| 38 | Illustration animal 2 | `images/animaux/animal-illustration-02.jpg` | 800x600px |
| 39 | Illustration animal 3 | `images/animaux/animal-illustration-03.jpg` | 800x600px |

### **musee.html** (Musée virtuel)
| Ligne | Description | Chemin recommandé | Dimensions |
|-------|-------------|-------------------|------------|
| 425 | Tableau 1 | `images/musee/tableau-001.jpg` | 1000x800px |
| 434 | Tableau 2 | `images/musee/tableau-002.jpg` | 1000x800px |
| 443 | Tableau 3 | `images/musee/tableau-003.jpg` | 1000x800px |

### **bibliotheque.html** (Bibliothèque)
| Ligne | Description | Chemin recommandé | Dimensions |
|-------|-------------|-------------------|------------|
| 581 | Max et Mila | `images/livres/livre1-souvenirs-enfance.jpg` | 400x600px |
| 620 | Les Trois Vies | `images/livres/livre2-entre-reves-tempete.jpg` | 400x600px |
| 661 | Vision Technologique | `images/livres/livre3-vision-technologique.jpg` | 400x600px |

---

## 🎨 Spécifications Techniques

### Formats recommandés :
- **JPEG (.jpg)** : Photos, illustrations complexes (80-90% qualité)
- **PNG (.png)** : Logos, graphiques, transparence nécessaire
- **WebP (.webp)** : Format moderne (meilleure compression) - fallback JPEG requis
- **SVG (.svg)** : Icônes, logos vectoriels

### Tailles optimales :
- **Hero/banner** : 1920x1080px (16:9) - max 300KB
- **Tableaux musée** : 1200x900px (4:3) - max 200KB
- **Couvertures livres** : 600x900px (2:3) - max 150KB
- **Illustrations** : 1000x750px (4:3) - max 180KB
- **Thumbnails** : 400x300px - max 50KB

### Optimisation :
```bash
# Compression JPEG (PowerShell avec ImageMagick si installé)
magick convert input.jpg -quality 85 -resize 1200x output.jpg

# Compression PNG
pngquant --quality=65-80 input.png -o output.png
```

---

## 🔄 Workflow de Remplacement

### Étape 1 : Préparer les nouvelles images
1. Nommer selon convention : `categorie-description-2026-03-09.jpg`
2. Optimiser (compression, dimensions)
3. Placer dans dossier approprié

### Étape 2 : Mise à jour des fichiers HTML
Remplacer les références à l'ancienne image :

**Ancien (à remplacer)** :
```html
<img src="ChatGPT%20Image%208%20mars%202026,%2020_29_29.png" alt="...">
```

**Nouveau** :
```html
<img src="images/accueil/parcours-autodidacte.jpg" alt="...">
```

### Étape 3 : Vérification
- ✅ Image s'affiche correctement
- ✅ Pas d'erreur 404 dans console navigateur
- ✅ Performance acceptable (< 2s chargement total)

---

## 📦 Images Actuelles à Remplacer

### ⚠️ Image placeholder utilisée partout
**Ancien nom** : `ChatGPT Image 8 mars 2026, 20_29_29.png`  
**Nouveau nom** : `images/session-mars-2026/placeholder-original.png`  
**Status** : ✅ Renommé et archivé

### 🔄 Pages à mettre à jour avec vraies images

1. **index.html** (3 images) - Priorité HAUTE
2. **home.html** (3 images) - Priorité HAUTE
3. **musee.html** (3 tableaux) - Priorité MOYENNE
4. **bibliotheque.html** (3 couvertures) - Priorité HAUTE

**Total : 12 images à remplacer**

---

## 🎯 Prochaines Étapes

1. ✅ Créer structure de dossiers `images/`
2. ✅ Renommer placeholder existant
3. ⏳ **Ajouter nouvelles images dans `images/session-mars-2026/`**
4. ⏳ **Renommer images selon convention**
5. ⏳ **Déplacer vers dossiers appropriés**
6. ⏳ **Mettre à jour références HTML**
7. ⏳ **Tester affichage sur toutes pages**
8. ⏳ **Commit Git avec message clair**

---

## 📁 Session Photo Actuelle (Mars 2026)

**Dossier** : `images/session-mars-2026/`  
**Usage** : Stocker temporairement les nouvelles images avant tri

### Instructions :
1. Copier toutes les nouvelles photos dans ce dossier
2. Renommer selon convention
3. Trier par catégorie (livres, musee, accueil, animaux)
4. Déplacer vers dossiers finaux
5. Archiver ou supprimer placeholder

---

## 🛠️ Scripts Utiles (PowerShell)

### Lister toutes les images actuelles
```powershell
Get-ChildItem -Path "images" -Recurse -Include *.jpg,*.png,*.svg | Select-Object Name, Length, LastWriteTime
```

### Renommer en masse (exemple)
```powershell
Get-ChildItem "images/session-mars-2026/*.jpg" | ForEach-Object {
    $newName = "musee-tableau-" + $_.Name
    Rename-Item $_.FullName -NewName $newName
}
```

### Vérifier taille des images
```powershell
Get-ChildItem "images" -Recurse -File | Where-Object { $_.Length -gt 300KB } | Select-Object Name, @{Name="Size (KB)";Expression={[math]::Round($_.Length/1KB,2)}}
```

---

## 📞 Support

Pour toute question sur la gestion des images :
- Voir documentation complète dans ce fichier
- Structure des dossiers créée automatiquement
- Convention de nommage stricte pour éviter doublons

**Dernière mise à jour** : 9 mars 2026
