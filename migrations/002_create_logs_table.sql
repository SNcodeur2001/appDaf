-- Migration pour créer la table de journalisation
CREATE TABLE IF NOT EXISTS request_logs (
    id SERIAL PRIMARY KEY,
    date_heure TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    localisation VARCHAR(200),
    ip_address INET NOT NULL,
    statut VARCHAR(20) NOT NULL CHECK (statut IN ('Success', 'Échec')),
    nci_recherche VARCHAR(20),
    endpoint VARCHAR(200),
    method VARCHAR(10),
    user_agent TEXT,
    response_time_ms INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour optimiser les recherches sur les logs
CREATE INDEX idx_logs_date_heure ON request_logs(date_heure);
CREATE INDEX idx_logs_statut ON request_logs(statut);
CREATE INDEX idx_logs_ip ON request_logs(ip_address);
