# Installation des dépendances AsArt'sDev API

## 📦 Installation avec Composer

### 1. Installer Composer (si ce n'est pas déjà fait)

**Windows :**
```powershell
# Télécharger depuis https://getcomposer.org/download/
# Ou via Chocolatey :
choco install composer
```

**Linux/macOS :**
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Installer les dépendances du projet

```powershell
# Dans le dossier racine du projet
cd "C:\Users\Asmir\Documents\e com"

# Installer toutes les dépendances Symfony
composer install
```

Cela va installer :
- ✅ Symfony Framework 6.4
- ✅ Doctrine ORM (gestion BDD)
- ✅ Stripe PHP SDK
- ✅ PHPMailer
- ✅ Tous les composants Symfony (Form, Validator, Mailer, etc.)

### 3. Configurer l'environnement

```powershell
# Copier le fichier d'environnement
copy .env.local.example .env.local

# Éditer .env.local avec vos clés API
notepad .env.local
```

**Contenu de .env.local :**
```env
# Database
DATABASE_URL="mysql://root:@127.0.0.1:3306/asartsdev_db"

# Stripe
STRIPE_SECRET_KEY="sk_test_xxxxx"
STRIPE_PUBLIC_KEY="pk_test_xxxxx"

# Mailer
MAILER_DSN="smtp://localhost:1025"
```

### 4. Créer la base de données

```powershell
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

### 5. Lancer le serveur de développement

```powershell
# Serveur Symfony
php -S localhost:8000 -t .
```

## 🔧 Structure des dépendances

| Package | Version | Usage |
|---------|---------|-------|
| symfony/framework-bundle | 6.4 | Core Symfony |
| doctrine/orm | ^2.17 | Base de données |
| stripe/stripe-php | ^13.0 | Paiements |
| phpmailer/phpmailer | ^6.9 | Emails |
| symfony/form | 6.4 | Formulaires |
| symfony/validator | 6.4 | Validation |

## ⚠️ Notes importantes

1. **composer.lock** est ignoré dans `.gitignore` pour éviter les conflits de versions
2. Le dossier **vendor/** ne doit JAMAIS être commité
3. Les fichiers **.env.local** contiennent vos clés secrètes et sont ignorés
4. Exécutez `composer update` régulièrement pour les mises à jour de sécurité

## 🚀 Déploiement

Pour GitHub Pages :
- Le site HTML/CSS/JS est hébergé directement
- L'API PHP nécessite un hébergement séparé avec PHP 8.1+ (ex: Heroku, OVH, etc.)

## 📚 Documentation

- [Symfony Documentation](https://symfony.com/doc/current/index.html)
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)
- [Stripe API PHP](https://stripe.com/docs/api?lang=php)
