# 📊 AsArt'sDev - Base de Données Client Symfony

## 🚀 Installation & Configuration

### 1️⃣ **Prérequis**
```bash
PHP 8.0+ | Symfony 5.4+ | MySQL 8.0+ | Composer
```

### 2️⃣ **Installation**

#### a) Créer la base de données
```bash
php bin/console doctrine:database:create
```

#### b) Exécuter le schéma SQL
```bash
# Option 1: Via migrations
php bin/console doctrine:migrations:migrate

# Option 2: Importer directement
mysql -u root -p asartsdev < api/Database.sql
```

#### c) Configurer `.env.local`
```env
DATABASE_URL="mysql://root:password@127.0.0.1:3306/asartsdev"
MAILER_DSN="smtp://localhost"
```

### 3️⃣ **Structure des fichiers créés**

```
api/
├── Database.sql              ← Schéma complet
├── Entity/
│   ├── Client.php           ← Entité principali
│   ├── Commande.php
│   ├── Document.php
│   └── ActiviteClient.php
├── Form/
│   └── ClientFormType.php   ← Formulaire Symfony
├── Controller/
│   └── ClientController.php ← Contrôleur API/Web
├── Repository/
│   ├── ClientRepository.php ← Requêtes personnalisées
│   ├── CommandeRepository.php
│   └── DocumentRepository.php
├── Service/
│   └── ClientService.php    ← Logique métier
└── README.md               ← Ce fichier
```

---

## 📝 **Routes disponibles**

### Enregistrement Client
```
POST  /clients/enregistrement
```
**Données attendues (formulaire multipart):**
- `nom` (string) - requis
- `prenom` (string) - requis
- `email` (email) - requis, unique
- `telephone` (string) - optionnel
- `adresse` (string) - optionnel
- `codePostal` (string)
- `ville` (string)
- `entreprise` (string)
- `fonction` (string)
- `typeClient` (enum: particulier|entreprise|partenaire|investisseur)
- `interets` (string avec virgules)
- `notes` (textarea)

**Réponse:**
```json
{
  "success": true,
  "client_id": 1,
  "message": "Enregistrement réussi"
}
```

### Dashboard Client
```
GET   /clients/{id}/dashboard
```
Affiche le profil, commandes, documents et activités du client.

### Modifier le profil
```
POST  /clients/{id}/edit
```
Mêmes données que l'enregistrement.

### Upload de document
```
POST  /clients/{id}/upload
```
**Multipart avec:**
- `document` (file) - PDF, JPG, PNG, Word (max 50MB)
- `type` (string) - contrat|devis|facture|cv|presentation|autre

**Réponse:**
```json
{
  "success": true,
  "document": {
    "id": 1,
    "nom": "Mon CV.pdf",
    "type": "cv",
    "date": "09/03/2026 10:30"
  }
}
```

### Lister les clients
```
GET   /clients/list?page=1&statut=actif&type=entreprise
```

---

## 🗄️ **Structure de la Base de Données**

### **Clients** (Table principale)
| Colonne | Type | Description |
|---------|------|-----------|
| id | INT | Clé primaire |
| nom | VARCHAR | Obligatoire |
| prenom | VARCHAR | Obligatoire |
| email | VARCHAR | Unique, obligatoire |
| telephone | VARCHAR | Optionnel |
| adresse | TEXT | Adresse complète |
| type_client | ENUM | particulier\|entreprise\|partenaire\|investisseur |
| statut | ENUM | actif\|inactif\|suspendu\|prospect |
| interets | JSON | Array d'intérêts |
| date_inscription | DATETIME | Auto |
| documents_path | VARCHAR | Chemin au dossier documents |

### **Commandes**
Lie chaque client à ses commandes/projets.

### **Documents**
Stocke les métadonnées des uploads (path, type, taille, etc.)

### **Activites_Clients**
Log automatique de toutes les actions: création, modification, uploads, etc.

---

## 🔧 **Utilisation du Service ClientService**

```php
// Dans un contrôleur
public function maFonction(ClientService $clientService, Client $client)
{
    // Upload de document
    $document = $clientService->handleDocumentUpload(
        $client,
        $uploadedFile,
        'cv'
    );

    // Stats client
    $stats = $clientService->getClientStats($client);
    // Retourne: totalCommandes, totalDocuments, montantTotal, derniereActivite

    // Rapport
    $rapport = $clientService->generateClientReport($client);

    // Email
    $clientService->sendConfirmationEmail($client);
}
```

---

## 📄 **Exemple d'intégration HTML**

```html
<form action="/clients/enregistrement" method="POST" enctype="multipart/form-data">
    <input type="text" name="nom" placeholder="Nom" required>
    <input type="text" name="prenom" placeholder="Prénom" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="tel" name="telephone" placeholder="Téléphone">
    <input type="text" name="ville" placeholder="Ville">
    
    <select name="typeClient">
        <option value="particulier">Particulier</option>
        <option value="entreprise">Entreprise</option>
        <option value="partenaire">Partenaire</option>
    </select>
    
    <input type="text" name="interets" placeholder="IA, Innovation, Tech...">
    <textarea name="notes" placeholder="Notes internes"></textarea>
    
    <button type="submit">S'enregistrer</button>
</form>
```

---

## 🔐 **Sécurité**

✅ Validation CSRF sur tous les formulaires
✅ Validation des types MIME fichiers
✅ Limite de taille (50MB)
✅ Paramètres préparés (Doctrine ORM)
✅ Authentification optionnelle à implémenter

---

## 🚀 **Prochaines étapes recommandées**

- [ ] Ajouter authentification Client avec JWT
- [ ] Implémenter envoi d'emails (Mailer Symfony)
- [ ] Générer PDF avec Dompdf
- [ ] Ajouter pagination API REST
- [ ] Tests unitaires avec PHPUnit
- [ ] Système de notifications
- [ ] Intégration Stripe pour paiements

---

**Créé par:** Asmir Milianni | **AI:** Milian-NIA 136QI
**Date:** 09.03.2026 | **Version:** 1.0.0
