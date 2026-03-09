/* AsArt'sDev | Database Schema | Signature invisible | ASmir Milia */
/* ================================================================
   BASE DE DONNÉES - Enregistrement Clients & Documents
   Symfony + PHP | 09.03.2026
   ================================================================ */

-- Table des clients
CREATE TABLE IF NOT EXISTS clients (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(255) NOT NULL,
  prenom VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  telephone VARCHAR(20),
  adresse TEXT,
  code_postal VARCHAR(10),
  ville VARCHAR(100),
  pays VARCHAR(100),
  
  -- Informations professionnelles
  entreprise VARCHAR(255),
  fonction VARCHAR(100),
  
  -- Données personnalisées
  type_client ENUM('particulier', 'entreprise', 'partenaire', 'investisseur') DEFAULT 'particulier',
  statut ENUM('actif', 'inactif', 'suspendu', 'prospect') DEFAULT 'prospect',
  
  -- Intérêts (JSON)
  interets JSON,
  
  -- Documents
  documents_path VARCHAR(500),
  
  -- Métadonnées
  date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  date_derniere_contact DATETIME,
  
  -- Notes internes
  notes TEXT,
  
  INDEX idx_email (email),
  INDEX idx_type (type_client),
  INDEX idx_statut (statut),
  INDEX idx_date (date_inscription)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commandes/projets liés
CREATE TABLE IF NOT EXISTS commandes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  client_id INT NOT NULL,
  numero_commande VARCHAR(50) UNIQUE,
  
  -- Détails de la commande
  type_projet ENUM('livre', 'art', 'tech', 'partenariat', 'autre') DEFAULT 'autre',
  description TEXT,
  
  montant DECIMAL(10, 2),
  acompte DECIMAL(10, 2),
  devise VARCHAR(3) DEFAULT 'EUR',
  
  -- Statut
  statut ENUM('devis', 'confirmee', 'en_cours', 'livree', 'annulee') DEFAULT 'devis',
  date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  date_livraison_prevue DATE,
  date_livraison_reelle DATE,
  
  -- Traçabilité
  notes TEXT,
  
  FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
  INDEX idx_client (client_id),
  INDEX idx_statut (statut),
  INDEX idx_numero (numero_commande)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des documents/uploads
CREATE TABLE IF NOT EXISTS documents (
  id INT PRIMARY KEY AUTO_INCREMENT,
  client_id INT NOT NULL,
  commande_id INT,
  
  nom_fichier VARCHAR(255) NOT NULL,
  path_fichier VARCHAR(500) NOT NULL,
  mime_type VARCHAR(100),
  taille_bytes INT,
  
  type_document ENUM('contrat', 'devis', 'facture', 'cv', 'presentation', 'autre') DEFAULT 'autre',
  
  date_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
  FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE SET NULL,
  INDEX idx_client (client_id),
  INDEX idx_commande (commande_id),
  INDEX idx_type (type_document)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des logs d'activité
CREATE TABLE IF NOT EXISTS activites_clients (
  id INT PRIMARY KEY AUTO_INCREMENT,
  client_id INT NOT NULL,
  
  type_activite ENUM('creation', 'modification', 'document_upload', 'commande', 'contact', 'note') DEFAULT 'modification',
  description TEXT,
  
  date_activite DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
  INDEX idx_client (client_id),
  INDEX idx_date (date_activite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des authentifications/tokens
CREATE TABLE IF NOT EXISTS client_sessions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  client_id INT NOT NULL,
  
  token VARCHAR(255) UNIQUE,
  ip_address VARCHAR(45),
  user_agent VARCHAR(500),
  
  date_connexion DATETIME DEFAULT CURRENT_TIMESTAMP,
  date_expiration DATETIME,
  
  FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
  INDEX idx_client (client_id),
  INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données initiales pour test
INSERT INTO clients (nom, prenom, email, telephone, ville, type_client, statut, interets) 
VALUES (
  'Milianni', 
  'Asmir', 
  'asmir@asartsdev.com', 
  '0781586882', 
  'Paris',
  'partenaire',
  'actif',
  JSON_ARRAY('IA', 'Innovation', 'Tech Inclusive')
) ON DUPLICATE KEY UPDATE date_modification = CURRENT_TIMESTAMP;

-- Vue pour les statistiques clients
CREATE OR REPLACE VIEW stats_clients AS
SELECT 
  COUNT(*) as total_clients,
  SUM(CASE WHEN statut = 'actif' THEN 1 ELSE 0 END) as clients_actifs,
  SUM(CASE WHEN statut = 'prospect' THEN 1 ELSE 0 END) as prospects,
  SUM(CASE WHEN type_client = 'entreprise' THEN 1 ELSE 0 END) as entreprises,
  SUM(CASE WHEN type_client = 'partenaire' THEN 1 ELSE 0 END) as partenaires
FROM clients;
