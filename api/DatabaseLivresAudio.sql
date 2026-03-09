-- ============================================================
-- BASE DE DONNÉES LIVRES & AUDIO
-- ============================================================

CREATE TABLE IF NOT EXISTS livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    numero_livre INT NOT NULL COMMENT '1, 2 ou 3',
    isbn VARCHAR(50) UNIQUE,
    prix DECIMAL(10, 2) NOT NULL,
    nombre_pages INT NOT NULL,
    poids_kg DECIMAL(5, 2) NOT NULL,
    image_couverture VARCHAR(255),
    url_preview VARCHAR(255),
    disponible BOOLEAN DEFAULT TRUE,
    stock_disponible INT DEFAULT 0,
    date_publication DATETIME NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY idx_numero_livre (numero_livre),
    INDEX idx_disponible (disponible)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE BANDES AUDIO (LECTURES COMPLÈTES)
-- ============================================================

CREATE TABLE IF NOT EXISTS bandes_audio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livre_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    fichier_audio VARCHAR(255) NOT NULL,
    format VARCHAR(20) DEFAULT 'mp3',
    duree_secondes INT NOT NULL,
    ordre INT DEFAULT 1 COMMENT 'Ordre de lecture',
    type VARCHAR(50) DEFAULT 'lecture_integrale' COMMENT 'lecture_integrale, extrait, introduction',
    acces_gratuit BOOLEAN DEFAULT FALSE,
    nombre_ecoutes INT DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (livre_id) REFERENCES livres(id) ON DELETE CASCADE,
    INDEX idx_livre_ordre (livre_id, ordre),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE COMMENTAIRES AUDIO (ANALYSES & RÉFLEXIONS)
-- ============================================================

CREATE TABLE IF NOT EXISTS commentaires_audio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livre_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    fichier_audio VARCHAR(255) NOT NULL,
    duree_secondes INT NOT NULL,
    auteur_commentaire VARCHAR(100) DEFAULT 'Asmir Milianni',
    chapitre_reference VARCHAR(100) COMMENT 'Chapitre concerné',
    page_reference INT COMMENT 'Page concernée',
    type_commentaire VARCHAR(50) DEFAULT 'analyse' COMMENT 'analyse, reflexion, contexte, anecdote',
    acces_premium BOOLEAN DEFAULT TRUE,
    nombre_ecoutes INT DEFAULT 0,
    transcription TEXT COMMENT 'Transcription textuelle du commentaire',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (livre_id) REFERENCES livres(id) ON DELETE CASCADE,
    INDEX idx_livre_type (livre_id, type_commentaire),
    INDEX idx_chapitre (chapitre_reference)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES INITIALES - LES 3 LIVRES
-- ============================================================

INSERT INTO livres (
    titre, 
    description, 
    numero_livre, 
    isbn, 
    prix, 
    nombre_pages, 
    poids_kg, 
    disponible, 
    stock_disponible, 
    date_publication
) VALUES
(
    'Livre 1 – Les premières lueurs',
    'Le premier tome d''une trilogie autobiographique. Découvrez les origines, les premières luttes et les rêves naissants d''une vie marquée par la créativité et la résilience.',
    1,
    '978-2-ASART-001-1',
    35.00,
    380,
    0.65,
    TRUE,
    50,
    '2024-06-15 10:00:00'
),
(
    'Livre 2 – Entre ombres et lumières',
    'Le deuxième tome explore les défis, les doutes et les victoires qui sculptent un parcours unique. Entre créativité et adversité, une histoire de transformation.',
    2,
    '978-2-ASART-002-8',
    38.00,
    420,
    0.72,
    TRUE,
    45,
    '2025-03-20 10:00:00'
),
(
    'Livre 3 – Entre rêves et tempête',
    'Le tome final de la trilogie. L''aboutissement d''un voyage intérieur, entre passion créative et quête de sens. Une célébration de l''art comme force de vie.',
    3,
    '978-2-ASART-003-5',
    42.00,
    450,
    0.78,
    TRUE,
    40,
    '2026-01-10 10:00:00'
);

-- ============================================================
-- DONNÉES INITIALES - BANDES AUDIO (EXEMPLES)
-- ============================================================

INSERT INTO bandes_audio (livre_id, titre, description, fichier_audio, duree_secondes, ordre, type, acces_gratuit) VALUES
(1, 'Introduction - Les premières lueurs', 'Introduction lue par l''auteur - Découverte du contexte et de l''ambiance du premier tome', '/audio/livre1/intro.mp3', 480, 1, 'introduction', TRUE),
(1, 'Chapitre 1 - L''enfance', 'Lecture intégrale du chapitre 1', '/audio/livre1/chapitre01.mp3', 2400, 2, 'lecture_integrale', FALSE),
(1, 'Chapitre 2 - Les premiers pas', 'Lecture intégrale du chapitre 2', '/audio/livre1/chapitre02.mp3', 2100, 3, 'lecture_integrale', FALSE),

(2, 'Introduction - Entre ombres et lumières', 'Introduction du tome 2 par l''auteur', '/audio/livre2/intro.mp3', 520, 1, 'introduction', TRUE),
(2, 'Chapitre 1 - La transition', 'Lecture intégrale du chapitre 1', '/audio/livre2/chapitre01.mp3', 2600, 2, 'lecture_integrale', FALSE),

(3, 'Introduction - Entre rêves et tempête', 'Introduction du tome final', '/audio/livre3/intro.mp3', 550, 1, 'introduction', TRUE),
(3, 'Chapitre 1 - Le commencement de la fin', 'Lecture intégrale du chapitre 1', '/audio/livre3/chapitre01.mp3', 2800, 2, 'lecture_integrale', FALSE);

-- ============================================================
-- DONNÉES INITIALES - COMMENTAIRES AUDIO (EXEMPLES)
-- ============================================================

INSERT INTO commentaires_audio (
    livre_id, 
    titre, 
    description, 
    fichier_audio, 
    duree_secondes, 
    chapitre_reference, 
    page_reference, 
    type_commentaire, 
    acces_premium,
    transcription
) VALUES
(
    1,
    'Pourquoi ce titre ?',
    'Commentaire audio expliquant le choix du titre "Les premières lueurs" et sa signification profonde dans le contexte de l''histoire',
    '/audio/livre1/commentaires/titre.mp3',
    180,
    'Introduction',
    5,
    'contexte',
    FALSE,
    'Le titre "Les premières lueurs" représente ces moments d''éveil, de prise de conscience qui marquent le début d''un voyage intérieur...'
),
(
    1,
    'Anecdote - La scène du parc',
    'Histoire vraie derrière la scène emblématique du parc au chapitre 3',
    '/audio/livre1/commentaires/anecdote_parc.mp3',
    240,
    'Chapitre 3',
    78,
    'anecdote',
    TRUE,
    'Cette scène est inspirée d''un moment réel vécu en 2015. J''étais assis dans ce parc...'
),
(
    1,
    'Analyse - Le symbolisme des couleurs',
    'Décryptage des symboliques utilisées tout au long du premier tome',
    '/audio/livre1/commentaires/symbolisme.mp3',
    360,
    NULL,
    NULL,
    'analyse',
    TRUE,
    'Les couleurs jouent un rôle central dans ma narration. Le bleu représente...'
),
(
    2,
    'Contexte - Transition entre tome 1 et 2',
    'Comprendre l''évolution narrative entre les deux premiers livres',
    '/audio/livre2/commentaires/transition.mp3',
    280,
    'Introduction',
    NULL,
    'contexte',
    FALSE,
    'Entre le tome 1 et le tome 2, il y a eu une période de transformation intense...'
),
(
    2,
    'Réflexion - Sur la créativité face à l''adversité',
    'Réflexion personnelle sur le thème central du tome 2',
    '/audio/livre2/commentaires/creativite.mp3',
    420,
    'Chapitre 5',
    142,
    'reflexion',
    TRUE,
    'Quand tout semble s''écrouler, la créativité devient non pas un luxe, mais une nécessité vitale...'
),
(
    3,
    'Message final de l''auteur',
    'Message personnel d''Asmir aux lecteurs ayant terminé la trilogie',
    '/audio/livre3/commentaires/message_final.mp3',
    480,
    'Épilogue',
    450,
    'reflexion',
    FALSE,
    'Si vous lisez ces mots, c''est que vous avez parcouru avec moi ce chemin de trois tomes. Je voulais vous dire...'
);

-- ============================================================
-- VIEW LIVRES COMPLETS AVEC AUDIO
-- ============================================================

CREATE OR REPLACE VIEW livres_avec_audio AS
SELECT 
    l.id,
    l.titre,
    l.numero_livre,
    l.prix,
    l.disponible,
    l.stock_disponible,
    COUNT(DISTINCT ba.id) as nombre_bandes_audio,
    COUNT(DISTINCT ca.id) as nombre_commentaires,
    SUM(DISTINCT ba.duree_secondes) as duree_totale_lecture,
    SUM(DISTINCT ca.duree_secondes) as duree_totale_commentaires,
    SUM(ba.nombre_ecoutes) as total_ecoutes_bandes,
    SUM(ca.nombre_ecoutes) as total_ecoutes_commentaires
FROM livres l
LEFT JOIN bandes_audio ba ON l.id = ba.livre_id
LEFT JOIN commentaires_audio ca ON l.id = ca.livre_id
GROUP BY l.id
ORDER BY l.numero_livre;

COMMIT;
