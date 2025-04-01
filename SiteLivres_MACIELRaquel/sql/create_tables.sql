CREATE TABLE IF NOT EXISTS clients (
    code_client SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    pays VARCHAR(50) NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS panier (
    code_client INT REFERENCES clients(code_client) ON DELETE CASCADE,
    code_exemplaire INT NOT NULL,
    quantite INT NOT NULL CHECK (quantite > 0),
    PRIMARY KEY (code_client, code_exemplaire)
);

CREATE TABLE IF NOT EXISTS commande (
    code_client INT REFERENCES clients(code_client) ON DELETE CASCADE,
    code_exemplaire INT NOT NULL,
    quantite INT NOT NULL CHECK (quantite > 0),
    prix DECIMAL(10,2) NOT NULL,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (code_client, code_exemplaire, date_commande)
);
