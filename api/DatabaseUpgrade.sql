/* 
 * AsArt'sDev | UPGRADE BASE DE DONNÉES
 * ====================================
 * Ajout des tables pour gestion des commandes/donations avec reminders
 * Exécutez ce script APRÈS Database.sql
 * 
 * Date: 9 Mars 2026
 */

-- ============================================
-- 1. TABLE REMINDERS COMMANDE
-- ============================================
CREATE TABLE IF NOT EXISTS reminder_commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    type_reminder ENUM('creation', 'paiement_recu', 'preparation', 'pret_expedition', 'envoye', 'recu', 'relance') DEFAULT 'creation',
    message LONGTEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_envoi DATETIME NULL,
    statut ENUM('brouillon', 'envoye', 'non_applicable') DEFAULT 'brouillon',
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_commande_reminder ON reminder_commandes(commande_id);
CREATE INDEX idx_statut_reminder ON reminder_commandes(statut);

-- ============================================
-- 2. TABLE DONATIONS (Extension de Commandes)
-- ============================================
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    devise VARCHAR(3) DEFAULT 'EUR',
    
    -- Information donateur
    nom_donateur VARCHAR(255) NULL COMMENT 'NULL = donation anonyme',
    email_donateur VARCHAR(255) NULL,
    telephone_donateur VARCHAR(20) NULL,
    adresse_donateur TEXT NULL,
    
    -- Traçabilité
    ip_adresse VARCHAR(45) NOT NULL COMMENT 'IPv4 ou IPv6',
    user_agent LONGTEXT NULL,
    date_donation DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Gestion cadeaux
    eligible_cadeau BOOLEAN DEFAULT FALSE COMMENT 'TRUE si info complète fournie',
    cadeau_id INT NULL,
    cadeau_envoye DATETIME NULL,
    
    -- Suivi anonyme
    code_suivi VARCHAR(32) UNIQUE NOT NULL,
    statut_suivi ENUM('en_attente', 'confirmee', 'traitee', 'envoyee') DEFAULT 'en_attente',
    
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_email_donation ON donations(email_donateur);
CREATE INDEX idx_code_suivi ON donations(code_suivi);
CREATE INDEX idx_eligible_cadeau ON donations(eligible_cadeau);
CREATE INDEX idx_date_donation ON donations(date_donation);

-- ============================================
-- 3. TABLE CADEAUX/CONTREPARTIES
-- ============================================
CREATE TABLE IF NOT EXISTS cadeaux (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_cadeau VARCHAR(255) NOT NULL,
    description LONGTEXT NULL,
    montant_minimum DECIMAL(10, 2) NOT NULL,
    montant_maximum DECIMAL(10, 2) NULL,
    quantite_disponible INT DEFAULT -1 COMMENT '-1 = illimité',
    quantite_utilisee INT DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO cadeaux (nom_cadeau, description, montant_minimum, quantite_disponible) VALUES
('👤 Remerciement personnel', 'Email de remerciement personnel signé Asmir', 5.00, -1),
('📚 Accès bibliothèque numérique', 'Accès 1 an à la bibliothèque AsArt\'sDev', 25.00, -1),
('🎨 Œuvre numérique exclusive', 'Reproduction numérique haute résolution exclusive', 50.00, 100),
('💝 Pack VIP donateur', 'Accès VIP + œuvre + newsletter exclusive', 100.00, 50),
('🏆 Reconnaissance publique', 'Nom sur page dédiée des mécènes', 500.00, -1),
('👑 Partenariat stratégique', 'Consultation + logo + visibilité permanente', 5000.00, 10);

CREATE TABLE IF NOT EXISTS audit_ip (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_adresse VARCHAR(45) NOT NULL,
    type_action ENUM('donation', 'commande', 'inscription', 'login', 'autre') DEFAULT 'autre',
    reference_id INT NULL,
    reference_type VARCHAR(100) NULL,
    user_agent LONGTEXT NULL,
    pays VARCHAR(100) NULL,
    region VARCHAR(100) NULL,
    date_action DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_ip_action ON audit_ip(ip_adresse, date_action);
CREATE INDEX idx_type_action ON audit_ip(type_action);

-- ============================================
-- 5. TABLE INVESTISSEURS
-- ============================================
CREATE TABLE IF NOT EXISTS investisseurs (
     id INT AUTO_INCREMENT PRIMARY KEY,
     entreprise VARCHAR(255) NOT NULL,
     montant DECIMAL(12, 2) NOT NULL,
     valide BOOLEAN DEFAULT FALSE,
     token VARCHAR(64) UNIQUE NULL,
     email VARCHAR(255) NULL,
     justificatif VARCHAR(255) NULL,
     strategie LONGTEXT NULL,
     ia_score DECIMAL(6, 2) DEFAULT 0,
     total_score DECIMAL(6, 2) DEFAULT 0,
     ip_access VARCHAR(45) NULL,
     nda_signe BOOLEAN DEFAULT FALSE,
     token_expire DATETIME NULL,
     date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_investisseurs_valide ON investisseurs(valide);
CREATE INDEX idx_investisseurs_ip ON investisseurs(ip_access);
CREATE INDEX idx_investisseurs_date ON investisseurs(date_creation);

CREATE OR REPLACE VIEW donations_admin_view AS
SELECT 
    d.id,
    d.commande_id,
    d.montant,
    CASE 
        WHEN d.nom_donateur IS NULL THEN '🔒 ANONYME'
        ELSE CONCAT(d.nom_donateur, ' (', d.email_donateur, ')')
    END as donateur_info,
    d.ip_adresse,
    d.eligible_cadeau,
    CASE 
        WHEN d.eligible_cadeau = 1 AND d.cadeau_envoye IS NULL THEN '⏳ EN ATTENTE'
        WHEN d.cadeau_envoye IS NOT NULL THEN '✅ ENVOYE'
        WHEN d.eligible_cadeau = 0 THEN '❌ INELIGIBLE'
    END as statut_cadeau,
    d.date_donation,
    d.code_suivi
FROM donations d
ORDER BY d.date_donation DESC;

CREATE OR REPLACE VIEW commandes_reminders_view AS
SELECT 
    c.id,
    c.numero_commande,
    c.type_projet,
    c.montant,
    c.statut,
    GROUP_CONCAT(DISTINCT r.type_reminder ORDER BY r.date_creation DESC SEPARATOR ', ') as reminders_types,
    COUNT(DISTINCT r.id) as nombre_reminders,
    MAX(r.date_envoi) as dernier_reminder_envoye,
    c.date_creation
FROM commandes c
LEFT JOIN reminder_commandes r ON c.id = r.commande_id
GROUP BY c.id
ORDER BY c.date_creation DESC;

CREATE TABLE IF NOT EXISTS reminder_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(100) NOT NULL UNIQUE,
    titre VARCHAR(255) NOT NULL,
    sujet_email VARCHAR(255) NOT NULL,
    contenu_template LONGTEXT NOT NULL COMMENT 'Utilise {{variables}} pour templating',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO reminder_templates (type, titre, sujet_email, contenu_template) VALUES
('creation', 'Rappel de votre commande',
 'Merci pour votre commande #{{numeroCommande}}',
 'Bonjour {{nomDonateur}},\n\nNous avons bien reçu votre donation de {{montant}}€.\n\nNuméro de suivi: {{codeSuivi}}\n\nStatut: {{statut}}\n\nMerci pour votre soutien!'),

('paiement_recu', 'Paiement confirmé',
 'Paiement confirmé pour votre commande #{{numeroCommande}}',
 'Bonjour {{nomDonateur}},\n\nVotre paiement de {{montant}}€ a été confirné.\n\nVotre contrepartie prépare...\n\nCode suivi: {{codeSuivi}}'),

('preparation', 'Votre cadeau est en préparation',
 'Votre contrepartie se prépare 🎁',
 'Bonjour {{nomDonateur}},\n\nVotre cadeau {{nomCadeau}} est actuellement en préparation.\n\nNous vous le ferons parvenir prochainement!\n\nCode: {{codeSuivi}}'),

('pret_expedition', 'Prêt à être expédié',
 'Votre cadeau part demain! 📦',
 'Bonjour {{nomDonateur}},\n\nVotre contrepartie {{nomCadeau}} est prête et sera expédiée demain!\n\nSuivi: {{codeSuivi}}'),

('envoye', 'Colis expédié 📬',
 'Votre cadeau est en route!',
 'Bonjour {{nomDonateur}},\n\nVotre colis a été expédié le {{dateEnvoi}}.\n\nNuméro de suivi: {{numeroSuivi}}\n\nAdrresse: {{adresse}}'),

('recu', 'Cadeau reçu? 📝',
 'Avez-vous reçu votre contrepartie?',
 'Bonjour {{nomDonateur}},\n\nNous espérons que vous avez reçu votre cadeau {{nomCadeau}} en bon état!\n\nPourriez-vous nous le confirmer? Merci!');


ALTER TABLE commandes ADD COLUMN IF NOT EXISTS 
    ip_donateur VARCHAR(45) NULL AFTER notes;

ALTER TABLE commandes ADD COLUMN IF NOT EXISTS 
    type_donation ENUM('complete', 'anonyme') DEFAULT 'complete' AFTER ip_donateur;

ALTER TABLE commandes ADD COLUMN IF NOT EXISTS 
    code_suivi_anonyme VARCHAR(32) UNIQUE NULL AFTER type_donation;


INSERT INTO commandes (numero_commande, type_projet, montant, statut, type_donation, ip_donateur) 
VALUES ('DON-2026-TEST-001', 'donation', 25.00, 'confirmee', 'anonyme', '192.168.1.100');

INSERT INTO donations (commande_id, montant, ip_adresse, code_suivi, eligible_cadeau) 
VALUES (LAST_INSERT_ID(), 25.00, '192.168.1.100', 'ANON-' . UUID(), FALSE);

INSERT INTO commandes (numero_commande, type_projet, montant, statut, type_donation, ip_donateur) 
VALUES ('DON-2026-TEST-002', 'donation', 100.00, 'confirmee', 'complete', '203.0.113.50');

INSERT INTO donations (commande_id, montant, nom_donateur, email_donateur, telephone_donateur, 
                       adresse_donateur, ip_adresse, code_suivi, eligible_cadeau, statut_suivi) 
VALUES (LAST_INSERT_ID(), 100.00, 'Asmir Milianni', 'asmir@asartsdev.com', '0781586882',
        'Paris, France', '203.0.113.50', 'ASMIR-001', TRUE, 'confirmee');

INSERT INTO reminder_commandes (commande_id, type_reminder, message, statut) 
VALUES (1, 'creation', 'Donation de 25€ reçue - Anonyme - Pas de cadeau', 'envoye');

INSERT INTO reminder_commandes (commande_id, type_reminder, message, statut) 
VALUES (2, 'creation', 'Donation de 100€ reçue de Asmir Milianni - Eligible cadeau VIP', 'envoye');


DELIMITER //

CREATE TRIGGER after_commande_confirmee
AFTER UPDATE ON commandes
FOR EACH ROW
BEGIN
    IF NEW.statut = 'confirmee' AND OLD.statut != 'confirmee' THEN
        INSERT INTO reminder_commandes (commande_id, type_reminder, message, statut)
        VALUES (NEW.id, 'creation', 
                CONCAT('Commande #', NEW.numero_commande, ' - Montant: ', NEW.montant, '€'),
                'envoye');
    END IF;
END //

DELIMITER ;


CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100) NOT NULL,
    operation VARCHAR(50) NOT NULL,
    record_id INT NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    user_id INT NULL,
    ip_adresse VARCHAR(45) NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_table_op (table_name, operation),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. BDD ULTRA SECURISEE - FORMULAIRES
-- ============================================
-- Objectif:
-- - Meme base de donnees
-- - 2 tables metier separees (versements / contacts)
-- - Colonnes specifiques par type de formulaire
-- - Tracabilite forte (IP, User-Agent, hash)
-- - Compteur automatique du nombre de formulaires

CREATE TABLE IF NOT EXISTS formulaires_compteurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_formulaire ENUM('versement', 'contact') NOT NULL UNIQUE,
    nombre_formulaires BIGINT UNSIGNED NOT NULL DEFAULT 0,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO formulaires_compteurs (type_formulaire, nombre_formulaires)
VALUES ('versement', 0), ('contact', 0)
ON DUPLICATE KEY UPDATE type_formulaire = VALUES(type_formulaire);

CREATE TABLE IF NOT EXISTS versements_secure (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_uuid CHAR(36) NOT NULL UNIQUE COMMENT 'UUID de suivi externe',
    montant_centimes BIGINT UNSIGNED NOT NULL COMMENT 'Stockage entier pour eviter erreurs flottantes',
    devise CHAR(3) NOT NULL DEFAULT 'EUR',
    mode_versement ENUM('carte', 'virement', 'paypal', 'autre') NOT NULL,
    nom_payeur VARCHAR(255) NULL,
    email_payeur VARCHAR(255) NULL,
    telephone_payeur VARCHAR(30) NULL,
    statut ENUM('recu', 'verifie', 'rejete', 'rembourse') NOT NULL DEFAULT 'recu',

    -- Securite / integrite
    ip_adresse VARCHAR(45) NOT NULL,
    user_agent LONGTEXT NULL,
    payload_chiffre LONGTEXT NULL COMMENT 'Donnees sensibles chiffrees applicativement',
    payload_hash CHAR(64) NOT NULL COMMENT 'SHA-256 pour controle integrite',

    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_verification DATETIME NULL,

    CONSTRAINT chk_versements_montant CHECK (montant_centimes > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_versements_date ON versements_secure(date_creation);
CREATE INDEX idx_versements_email ON versements_secure(email_payeur);
CREATE INDEX idx_versements_statut ON versements_secure(statut);
CREATE INDEX idx_versements_ip ON versements_secure(ip_adresse);
CREATE UNIQUE INDEX uq_versements_hash ON versements_secure(payload_hash);

CREATE TABLE IF NOT EXISTS contacts_secure (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_uuid CHAR(36) NOT NULL UNIQUE COMMENT 'UUID de suivi externe',
    type_contact ENUM('standard', 'pro', 'investisseur', 'partenaire') NOT NULL DEFAULT 'standard',
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telephone VARCHAR(30) NULL,
    entreprise VARCHAR(255) NULL,
    sujet VARCHAR(255) NULL,
    message LONGTEXT NOT NULL,
    statut ENUM('recu', 'traite', 'archive', 'spam') NOT NULL DEFAULT 'recu',

    -- Securite / integrite
    ip_adresse VARCHAR(45) NOT NULL,
    user_agent LONGTEXT NULL,
    payload_chiffre LONGTEXT NULL COMMENT 'Donnees sensibles chiffrees applicativement',
    payload_hash CHAR(64) NOT NULL COMMENT 'SHA-256 pour controle integrite',

    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_traitement DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_contacts_date ON contacts_secure(date_creation);
CREATE INDEX idx_contacts_email ON contacts_secure(email);
CREATE INDEX idx_contacts_type ON contacts_secure(type_contact);
CREATE INDEX idx_contacts_statut ON contacts_secure(statut);
CREATE INDEX idx_contacts_ip ON contacts_secure(ip_adresse);
CREATE UNIQUE INDEX uq_contacts_hash ON contacts_secure(payload_hash);

CREATE TABLE IF NOT EXISTS transactions_secure (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_crypt TEXT NOT NULL,
    email_crypt TEXT NOT NULL,
    nda_accepte TINYINT(1) NOT NULL,
    ip_client VARCHAR(50) NOT NULL,
    preuve_photo LONGBLOB NOT NULL,
    montant DECIMAL(10,2) DEFAULT 0,
    methode_paiement VARCHAR(50),
    statut ENUM('pending','confirmed') DEFAULT 'pending',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_transactions_statut ON transactions_secure(statut);
CREATE INDEX idx_transactions_date ON transactions_secure(date_creation);
CREATE INDEX idx_transactions_ip ON transactions_secure(ip_client);

CREATE TABLE IF NOT EXISTS brevets_secure (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_crypt TEXT NOT NULL,
    email_crypt TEXT NOT NULL,
    nda_accepte TINYINT(1) NOT NULL,
    ip_client VARCHAR(50) NOT NULL,
    preuve_photo LONGBLOB NOT NULL,
    type_innovation VARCHAR(100),
    statut ENUM('pending','en_cours','finalise') DEFAULT 'pending',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_brevets_statut ON brevets_secure(statut);
CREATE INDEX idx_brevets_date ON brevets_secure(date_creation);
CREATE INDEX idx_brevets_ip ON brevets_secure(ip_client);

CREATE TABLE IF NOT EXISTS orders_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(40) NOT NULL UNIQUE,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telephone VARCHAR(50) NULL,
    adresse TEXT NULL,
    produit VARCHAR(255) NOT NULL,
    prix DECIMAL(10,2) NOT NULL DEFAULT 0,
    quantite INT NOT NULL DEFAULT 1,
    panier_json LONGTEXT NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    remise DECIMAL(10,2) NOT NULL DEFAULT 0,
    frais_envoi DECIMAL(10,2) NOT NULL DEFAULT 0,
    frais_service DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    message LONGTEXT NULL,
    statut VARCHAR(120) NOT NULL DEFAULT 'En attente de paiement',
    paiement_recu TINYINT(1) NOT NULL DEFAULT 0,
    ip_adresse VARCHAR(45) NULL,
    user_agent LONGTEXT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_mise_a_jour DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_orders_records_email ON orders_records(email);
CREATE INDEX idx_orders_records_statut ON orders_records(statut);
CREATE INDEX idx_orders_records_date ON orders_records(date_creation);

-- Triggers compteur automatique du nombre de formulaires
DELIMITER //

CREATE TRIGGER trg_count_versements_after_insert
AFTER INSERT ON versements_secure
FOR EACH ROW
BEGIN
    UPDATE formulaires_compteurs
    SET nombre_formulaires = nombre_formulaires + 1
    WHERE type_formulaire = 'versement';
END //

CREATE TRIGGER trg_count_contacts_after_insert
AFTER INSERT ON contacts_secure
FOR EACH ROW
BEGIN
    UPDATE formulaires_compteurs
    SET nombre_formulaires = nombre_formulaires + 1
    WHERE type_formulaire = 'contact';
END //

DELIMITER ;

-- Vue de pilotage: nombre de formulaires par type
CREATE OR REPLACE VIEW formulaires_stats_view AS
SELECT
    type_formulaire,
    nombre_formulaires,
    date_modification
FROM formulaires_compteurs
ORDER BY type_formulaire;

COMMIT;
