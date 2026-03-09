#!/bash
# ============================================================
# 📦 SCRIPT D'INSTALLATION COMPLET
# Système Donations + Reminders + Admin
# Date: 9 mars 2026
# ============================================================

set -e

echo "╔════════════════════════════════════════════════════════════╗"
echo "║  Installation Système Donations AsArt'sDev                ║"
echo "║  Étape 1/5: Préparation                                   ║"
echo "╚════════════════════════════════════════════════════════════╝"

# Configuration de base
PROJECT_ROOT="/Users/Asmir/Documents/e\ com"
BACKUP_DATE=$(date '+%Y%m%d_%H%M%S')

echo "📁 Répertoire projet: $PROJECT_ROOT"
echo "💾 Sauvegarde: ${PROJECT_ROOT}/api/backups/${BACKUP_DATE}/"

# ============================================================
# ÉTAPE 1: SAUVEGARDER LES FICHIERS ACTUELS
# ============================================================

echo ""
echo "ÉTAPE 1: Sauvegarde de sécurité..."
mkdir -p "${PROJECT_ROOT}/api/backups/${BACKUP_DATE}"

for file in \
    "${PROJECT_ROOT}/api/Database.sql" \
    "${PROJECT_ROOT}/api/Controller/ClientController.php" \
    "${PROJECT_ROOT}/api/Entity/Commande.php"; do
    if [ -f "$file" ]; then
        cp "$file" "${PROJECT_ROOT}/api/backups/${BACKUP_DATE}/" 2>/dev/null || true
        echo "  ✅ Sauvegardé: $(basename $file)"
    fi
done

# ============================================================
# ÉTAPE 2: EXÉCUTER LES MIGRATIONS BD
# ============================================================

echo ""
echo "ÉTAPE 2: Migrations base de données..."
echo ""

cd "$PROJECT_ROOT"

echo "  📊 Exécution DatabaseUpgrade.sql..."
mysql -u root -p asartsdev < api/DatabaseUpgrade.sql

echo "  ✅ Tables créées:"
echo "     - donations"
echo "     - reminder_commandes"
echo "     - cadeaux"
echo "     - audit_ip"
echo "     - Views: donations_admin_view, commandes_reminders_view"

# ============================================================
# ÉTAPE 3: COPIER LES FICHIERS PHP
# ============================================================

echo ""
echo "ÉTAPE 3: Installation des fichiers..."
echo ""

FILES_TO_COPY=(
    "api/Entity/Donation.php:src/Entity/Donation.php"
    "api/Entity/ReminderCommande.php:src/Entity/ReminderCommande.php"
    "api/Form/DonationFormType.php:src/Form/DonationFormType.php"
    "api/Controller/AdminController.php:src/Controller/AdminController.php"
    "api/Controller/DonationController.php:src/Controller/DonationController.php"
    "api/Service/ReminderService.php:src/Service/ReminderService.php"
)

for file_pair in "${FILES_TO_COPY[@]}"; do
    IFS=":" read -r src dst <<< "$file_pair"
    
    mkdir -p "$(dirname "$dst")"
    cp "$src" "$dst"
    echo "  ✅ $(basename $dst)"
done

# ============================================================
# ÉTAPE 4: CONFIGURATION SYMFONY
# ============================================================

echo ""
echo "ÉTAPE 4: Configuration Symfony..."
echo ""

# Vérifier/créer les fichiers de configuration
mkdir -p src/Repository

echo "  ✅ Répertoires creés: src/Repository, src/Entity, src/Form, src/Service, src/Controller"

# ============================================================
# ÉTAPE 5: INSTRUCTIONS MANUELLES
# ============================================================

echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║           ⚠️ ÉTAPES MANUELLES REQUISES                    ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

echo "📝 1. MODIFICATION Entity/Commande.php"
echo "   Ajouter ces relations dans la classe:"
echo ""
cat << 'EOF'
    /**
     * @ORM\OneToMany(targetEntity="Donation", mappedBy="commande", cascade={"persist", "remove"})
     */
    private $donations;

    /**
     * @ORM\OneToMany(targetEntity="ReminderCommande", mappedBy="commande", cascade={"persist", "remove"})
     */
    private $reminders;

    // Dans le constructeur:
    $this->donations = new ArrayCollection();
    $this->reminders = new ArrayCollection();
    
    // Ajouter les getters:
    public function getDonations() { return $this->donations; }
    public function getReminders() { return $this->reminders; }
EOF

echo ""
echo "🔧 2. CONFIGURATION .env"
echo "   Ajouter/mettre à jour:"
echo ""
cat << 'EOF'
# Mailer - Choisir un fournisseur:
# Gmail
MAILER_DSN=smtp://username:password@smtp.gmail.com:587

# Mailgun
MAILER_DSN=mailgun+api://key@sandboxXXX.mailgun.org

# SendGrid
MAILER_DSN=sendgrid+api://SG.XXXX

MAILER_FROM=noreply@asartsdev.com
EOF

echo ""
echo "📍 3. ROUTES (config/routes.yaml)"
echo "   Ajouter:"
echo ""
cat << 'EOF'
app:
    resource: ../src/Controller/
    type: annotation

# Routes spécifiques
donation:
    resource: ../src/Controller/DonationController.php
    type: annotation
    prefix: /donation

admin:
    resource: ../src/Controller/AdminController.php
    type: annotation
    prefix: /admin
EOF

echo ""
echo "📦 4. GÉNÉRATION DOCTRINE REPOSITORIES"
cd "$PROJECT_ROOT"
php bin/console make:repository Donation
php bin/console make:repository ReminderCommande

echo ""
echo "🗄️ 5. MIGRATIONS DOCTRINE (si entités mises à jour)"
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate

echo ""
echo "🧪 6. TEST MAILER"
php bin/console mailer:test admin@asartsdev.com

echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║              ✅ INSTALLATION TERMINE                       ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "📊 Accès admin:"
echo "   URL: http://localhost/admin/dashboard"
echo "   Routes disponibles:"
echo "     • GET  /admin/dashboard"
echo "     • GET  /admin/donations"
echo "     • GET  /admin/commandes"
echo "     • GET  /admin/reminders"
echo "     • POST /admin/reminder/{id}/send"
echo ""
echo "💰 Formulaire donation:"
echo "   URL: http://localhost/donation/formulaire"
echo "   Routes disponibles:"
echo "     • GET  /donation/formulaire"
echo "     • POST /donation/submit"
echo "     • GET  /donation/status/{code}"
echo "     • GET  /donation/cadeaux"
echo ""
echo "🔒 Suivi public (anonyme):"
echo "   URL: http://localhost/admin/track/ANON-XXXXX"
echo ""
echo "📚 Documentation:"
echo "   • DONATIONS_REMINDERS_README.md (complet)"
echo "   • EXEMPLES.php (code examples)"
echo ""
echo "⚠️  N'oubliez pas:"
echo "   1. Configurer .env avec MAILER_DSN"
echo "   2. Modifier Entity/Commande.php"
echo "   3. Créer les repositories Doctrine"
echo "   4. Exécuter les migrations"
echo ""

echo "🎉 Système prêt pour l'utilisation!"
echo "   Créé: 9 mars 2026"
echo "   Auteur: Asmir Milianni"
echo ""
