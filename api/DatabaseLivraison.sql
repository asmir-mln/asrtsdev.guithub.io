-- ============================================================
-- TABLE LIVRAISON & SUIVI
-- ============================================================

CREATE TABLE IF NOT EXISTS livraisons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    donation_id INT NULL,
    
    -- Statut de livraison
    statut ENUM('en_attente', 'preparation', 'pret_expedition', 'envoye', 'en_transit', 'livre', 'retour_demande', 'retour_en_cours', 'retour_recu') DEFAULT 'en_attente',
    
    -- Transporteur
    transporteur VARCHAR(100) NULL COMMENT 'La Poste, UPS, DHL, Colissimo, etc.',
    numero_suivi VARCHAR(100) UNIQUE NULL,
    type_envoi VARCHAR(50) DEFAULT 'standard' COMMENT 'standard, express, express_retour, dedicace',
    
    -- Coûts
    prix_base DECIMAL(10, 2) DEFAULT 5.69,
    prix_livraison DECIMAL(10, 2) NULL,
    prix_assurance DECIMAL(10, 2) DEFAULT 0,
    prix_total DECIMAL(10, 2) NULL,
    
    -- Adresse de livraison
    adresse_livraison TEXT NOT NULL,
    ville_livraison VARCHAR(100) NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    pays_livraison VARCHAR(100) NOT NULL,
    
    -- Dates
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_preparation DATETIME NULL,
    date_envoi DATETIME NULL,
    date_livraison_estimee DATE NULL,
    date_livraison_reelle DATETIME NULL,
    
    -- Dédicace personnalisée
    dedicace_demandee BOOLEAN DEFAULT FALSE,
    dedicace_texte LONGTEXT NULL,
    dedicace_montant_supplementaire DECIMAL(10, 2) DEFAULT 0,
    
    -- Suivi détaillé
    derniere_mise_a_jour DATETIME NULL,
    derniere_localisation VARCHAR(255) NULL,
    notes_internes LONGTEXT NULL,
    
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_statut_livraison ON livraisons(statut);
CREATE INDEX idx_numero_suivi ON livraisons(numero_suivi);
CREATE INDEX idx_date_envoi ON livraisons(date_envoi);

-- ============================================================
-- TABLE HISTORIQUE SUIVI
-- ============================================================

CREATE TABLE IF NOT EXISTS historique_suivi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livraison_id INT NOT NULL,
    
    -- Statut de suivi
    statut VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    localisation VARCHAR(255) NULL,
    
    -- Timestamp
    date_event DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Notified
    email_envoye BOOLEAN DEFAULT FALSE,
    newsletter_envoye BOOLEAN DEFAULT FALSE,
    
    FOREIGN KEY (livraison_id) REFERENCES livraisons(id) ON DELETE CASCADE,
    INDEX idx_livraison_date (livraison_id, date_event),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE TARIFICATION LIVRAISON
-- ============================================================

CREATE TABLE IF NOT EXISTS tarif_livraison (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transporteur VARCHAR(100) NOT NULL,
    type_envoi VARCHAR(50) NOT NULL COMMENT 'standard, express, dedicace',
    poids_min DECIMAL(5, 2) DEFAULT 0,
    poids_max DECIMAL(5, 2),
    zone VARCHAR(100) COMMENT 'france, europe, monde',
    prix_base DECIMAL(10, 2) DEFAULT 5.69,
    prix_kg_supplementaire DECIMAL(10, 2),
    delai_livraison INT COMMENT 'jours',
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES INITIALES TARIFICATION
-- ============================================================

INSERT INTO tarif_livraison (transporteur, type_envoi, zone, prix_base, delai_livraison, actif) VALUES
('Colissimo', 'standard', 'france', 5.69, 5, TRUE),
('Colissimo', 'standard', 'europe', 12.50, 10, TRUE),
('Colissimo', 'standard', 'monde', 25.00, 20, TRUE),
('La Poste', 'express', 'france', 8.99, 2, TRUE),
('La Poste', 'express', 'europe', 18.50, 5, TRUE),
('UPS', 'express_retour', 'france', 15.00, 1, TRUE),
('Special', 'dedicace', 'france', 30.00, 3, TRUE);

-- ============================================================
-- TABLE NEWSLETTER SUIVI
-- ============================================================

CREATE TABLE IF NOT EXISTS newsletter_suivi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livraison_id INT NOT NULL,
    email_destinataire VARCHAR(255) NOT NULL,
    sujet_template VARCHAR(255) NOT NULL,
    contenu_html LONGTEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_envoi DATETIME NULL,
    statut ENUM('brouillon', 'envoye', 'non_applicable') DEFAULT 'brouillon',
    
    FOREIGN KEY (livraison_id) REFERENCES livraisons(id) ON DELETE CASCADE,
    INDEX idx_date_envoi (date_envoi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE FORMULES CADEAUX EXISTANTES
-- ============================================================

CREATE TABLE IF NOT EXISTS formules_cadeaux (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_formule VARCHAR(255) NOT NULL,
    description LONGTEXT,
    montant_minimum DECIMAL(10, 2),
    contenu_formule LONGTEXT COMMENT 'Description des livres/articles inclus',
    type_formule ENUM('livres', 'digital', 'experience', 'dedicace', 'pack') DEFAULT 'livres',
    poids_estime DECIMAL(5, 2) COMMENT 'en kg',
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES INITIALES FORMULES
-- ============================================================

INSERT INTO formules_cadeaux (nom_formule, description, montant_minimum, contenu_formule, type_formule, poids_estime) VALUES
(
    'Pack Découverte',
    'Idéal pour débuter votre aventure AsArt\'sDev',
    5.00,
    '- 1 eBook gratuit\n- Accès 1 mois bibliothèque numérique\n- Insider letter exclusive',
    'digital',
    0.00
),
(
    'Pack Bibliothèque 1 Année',
    'Accès complet à 1 année de contenu créatif',
    25.00,
    '- Accès 1 an bibliothèque\n- 12 x Insider letter\n- 5 eBooks exclusifs\n- Webinaires mensuels',
    'digital',
    0.00
),
(
    'Livre Édition Premium',
    'Édition limitée signée - Récit autobiographique',
    50.00,
    '- 1 Livre édition premium (400 pages)\n- Signature auteur\n- Bookmark personnalisé\n- Certificat d\'authenticité',
    'livres',
    0.8
),
(
    'Pack 3 Livres Auteur',
    'Trilogie complète œuvres AsArt\'sDev',
    100.00,
    '- 3 Livres édition spéciale\n- Coffret premium\n- Poster signé\n- Meeting virtuel auteur',
    'livres',
    2.5
),
(
    'Expérience VIP',
    'Engagement privilégié pour 1 an',
    500.00,
    '- Tous les livres\n- Rencontre en visio privée\n- Consultation stratégique 2h\n- Crédits pour formations',
    'experience',
    3.0
),
(
    'Partenariat & Dédicace',
    'Pour les grands soutiens - Packages sur mesure',
    1000.00,
    '- Tous les contenus\n- Visite personnelle pour dédicace\n- Partenariat formalisé\n- Visibilité publique\n- Newsletter personnalisée',
    'dedicace',
    5.0
);

-- ============================================================
-- VIEW SUIVI COMPLET
-- ============================================================

CREATE OR REPLACE VIEW suivi_livraison_complet AS
SELECT 
    l.id,
    l.commande_id,
    c.numero_commande,
    l.statut,
    l.type_envoi,
    l.transporteur,
    l.numero_suivi,
    l.prix_total,
    l.date_creation,
    l.date_envoi,
    l.date_livraison_estimee,
    l.date_livraison_reelle,
    COUNT(DISTINCT hs.id) as nombre_mises_a_jour,
    MAX(hs.date_event) as derniere_mise_a_jour,
    hs.description as dernier_statut_description,
    l.adresse_livraison
FROM livraisons l
LEFT JOIN commandes c ON l.commande_id = c.id
LEFT JOIN historique_suivi hs ON l.id = hs.livraison_id
GROUP BY l.id
ORDER BY l.date_creation DESC;

COMMIT;
