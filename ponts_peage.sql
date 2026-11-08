
-- ============================================
-- BASE DE DONNÉES : ponts_peage
-- Serveur : MariaDB (WAMP)
-- ============================================

CREATE DATABASE IF NOT EXISTS ponts_peage
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ponts_peage;

-- --------------------------------------------
-- TABLE : vehicules
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_vehicule VARCHAR(60) NOT NULL,
    icone VARCHAR(10) NOT NULL,
    tarif DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO vehicules (type_vehicule, icone, tarif) VALUES
('Moto',           '🏍️',  500.00),
('Voiture légère', '🚗',  500.00),
('Taxi / Minibus', '🚕',  500.00),
('Bus',            '🚌',  1500.00),
('Camion léger',   '🚛',  1500.00),
('Camion lourd',   '🚚', 3000.00);

-- --------------------------------------------
-- TABLE : moyens_paiement
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS moyens_paiement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    icone VARCHAR(10) NOT NULL,
    description VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO moyens_paiement (nom, icone, description) VALUES
('Espèces',       '💵', 'Paiement en cash'),
('Wave',          '🔵', 'Mobile Money Wave'),
('Moov Money',    '🟡', 'Mobile Money Moov'),
('Orange Money',  '🟠', 'Mobile Money Orange'),
('Carte bancaire','💳', 'Visa / Mastercard');

-- --------------------------------------------
-- TABLE : paiements
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicule_id INT NOT NULL,
    moyen_paiement_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    reference VARCHAR(30) UNIQUE NOT NULL,
    agent VARCHAR(100) DEFAULT 'Agent',
    date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id),
    FOREIGN KEY (moyen_paiement_id) REFERENCES moyens_paiement(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;