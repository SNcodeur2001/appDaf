<?php

class CitoyenJournalSeeder {
    private PDO $pdo;
    private string $driver;

    public function __construct() {
        $this->loadEnv();
        $this->driver = $_ENV['DB_DRIVER'] ?? 'pgsql';
        $this->initializeConnection();
    }

    private function loadEnv(): void {
        $envFile = dirname(__DIR__) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
                [$key, $value] = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }

    private function initializeConnection(): void {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '5432';
        $user = $_ENV['DB_USER'] ?? ($this->driver === 'pgsql' ? 'postgres' : 'root');
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        $dbName = $_ENV['DB_NAME'] ?? 'citoyen_db';

        if ($this->driver === 'pgsql') {
            $this->pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbName", $user, $pass);
        } else {
            $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4", $user, $pass);
        }

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function clearAll(): void {
        echo "🧹 Nettoyage de toutes les données...\n";

        if ($this->driver === 'mysql') {
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        }

        $this->pdo->exec("DELETE FROM journal");
        $this->pdo->exec("DELETE FROM citoyen");

        if ($this->driver === 'mysql') {
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        }

        if ($this->driver === 'pgsql') {
            $this->pdo->exec("SELECT setval('citoyen_id_seq', 1, false)");
            $this->pdo->exec("SELECT setval('journal_id_seq', 1, false)");
        } else {
            $this->pdo->exec("ALTER TABLE citoyen AUTO_INCREMENT = 1");
            $this->pdo->exec("ALTER TABLE journal AUTO_INCREMENT = 1");
        }

        echo "✅ Toutes les données supprimées et séquences réinitialisées.\n";
    }

    public function seedCitoyens(): void {
        echo "👥 Insertion des citoyens ($this->driver)...\n";

        $this->pdo->exec("DELETE FROM citoyen");
        if ($this->driver === 'pgsql') {
            $this->pdo->exec("SELECT setval('citoyen_id_seq', 1, false)");
        } else {
            $this->pdo->exec("ALTER TABLE citoyen AUTO_INCREMENT = 1");
        }

      $citoyens = [
    ['Camara', 'Fatimata', '1234567890123', '1983-04-11', 'Dakar, Sénégal', 'uploads/cni/recto_1234567890123.jpg', 'uploads/cni/verso_1234567890123.jpg'],
    ['Gaye', 'Moussa', '2345678901234', '1991-10-05', 'Pikine, Sénégal', 'uploads/cni/recto_2345678901234.jpg', 'uploads/cni/verso_2345678901234.jpg'],
    ['Sy', 'Khady', '3456789012345', '1987-01-20', 'Thiès, Sénégal', 'uploads/cni/recto_3456789012345.jpg', 'uploads/cni/verso_3456789012345.jpg'],
    ['Ndour', 'Alioune', '4567890123456', '1990-06-18', 'Guédiawaye, Sénégal', 'uploads/cni/recto_4567890123456.jpg', 'uploads/cni/verso_4567890123456.jpg'],
    ['Kane', 'Seynabou', '5678901234567', '1985-12-03', 'Kaolack, Sénégal', 'uploads/cni/recto_5678901234567.jpg', 'uploads/cni/verso_5678901234567.jpg'],
    ['Dieng', 'Boubacar', '6789012345678', '1994-08-14', 'Saint-Louis, Sénégal', 'uploads/cni/recto_6789012345678.jpg', 'uploads/cni/verso_6789012345678.jpg'],
    ['Mbaye', 'Astou', '7890123456789', '1992-03-09', 'Ziguinchor, Sénégal', 'uploads/cni/recto_7890123456789.jpg', 'uploads/cni/verso_7890123456789.jpg'],
    ['Toure', 'Serigne', '8901234567890', '1989-11-26', 'Mbour, Sénégal', 'uploads/cni/recto_8901234567890.jpg', 'uploads/cni/verso_8901234567890.jpg']
];


        $stmt = $this->pdo->prepare("INSERT INTO citoyen (nom, prenom, cni, date_naissance, lieu_naissance, photo_cni_recto, photo_cni_verso) VALUES (?, ?, ?, ?, ?, ?, ?)");

        foreach ($citoyens as $row) {
            $stmt->execute($row);
        }

        echo "✅ " . count($citoyens) . " citoyens insérés.\n";
    }

    public function seedJournal(): void {
        echo "📊 Insertion des entrées de journal ($this->driver)...\n";

        $this->pdo->exec("DELETE FROM journal");
        if ($this->driver === 'pgsql') {
            $this->pdo->exec("SELECT setval('journal_id_seq', 1, false)");
        } else {
            $this->pdo->exec("ALTER TABLE journal AUTO_INCREMENT = 1");
        }

        $stmt = $this->pdo->query("SELECT cni FROM citoyen");
        $cnis = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (empty($cnis)) {
            echo "⚠️ Aucun citoyen trouvé. Exécutez seedCitoyens() d'abord.\n";
            return;
        }

        $statuts = ['SUCCES', 'ECHEC'];
        $codes_http = ['200', '201', '400', '401', '403', '404', '500', '503'];
        $ips = ['192.168.1.10','10.0.0.15','172.16.0.5','192.168.0.100','10.1.1.25','172.20.0.8','192.168.100.50','10.10.10.10'];
        $localisations = ['Dakar, Plateau','Thiès, Centre-ville','Saint-Louis, Sor','Kaolack, Médina','Ziguinchor, Centre','Tambacounda, Ville','Diourbel, Centre','Kolda, Sikilo'];

        $stmt = $this->pdo->prepare("INSERT INTO journal (date, heure, ip_adresse, localisation, status, code_http, cni) VALUES (?, ?, ?, ?, ?, ?, ?)");

        for ($i = 0; $i < 150; $i++) {
            $date = date('Y-m-d', strtotime("-" . rand(0, 30) . " days"));
            $heure = rand(8, 18);
            $statut = $statuts[array_rand($statuts)];
            $cni = $cnis[array_rand($cnis)];

            $code_http = match($statut) {
                'SUCCES' => ['200', '201'][rand(0, 1)],
                'ECHEC' => ['400', '401', '403', '404', '500'][rand(0, 4)],
                'EN_COURS' => '202',
                'ANNULE' => '409',
            };

            $stmt->execute([
                $date,
                $heure,
                $ips[array_rand($ips)],
                $localisations[array_rand($localisations)],
                $statut,
                $code_http,
                $cni
            ]);
        }

        echo "✅ 150 entrées de journal insérées.\n";
    }

    public function seedAll(): void {
        echo "🌱 Seeding complet...\n";
        $this->clearAll();
        $this->seedCitoyens();
        $this->seedJournal();
        echo "🎉 Seeding terminé.\n";
        $this->showStats();
    }

    public function showStats(): void {
        echo "\n📊 Statistiques:\n";

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM citoyen");
        echo "👥 Citoyens: " . $stmt->fetchColumn() . "\n";

        $stmt = $this->pdo->query("SELECT status, COUNT(*) as total FROM journal GROUP BY status ORDER BY status");
        foreach ($stmt as $row) {
            echo "📋 Journal {$row['status']}: {$row['total']}\n";
        }
    }
}

// --- Exécution CLI ---
try {
    echo "🚀 Démarrage du seeder CitoyenJournalSeeder...\n\n";
    $seeder = new CitoyenJournalSeeder();

    $action = $argv[1] ?? 'all';

    match ($action) {
        'citoyens', 'citizens' => $seeder->seedCitoyens(),
        'journal', 'log'       => $seeder->seedJournal(),
        'clear', 'clean'       => $seeder->clearAll(),
        'stats'                => $seeder->showStats(),
        default                => $seeder->seedAll()
    };

    echo "\n✅ Terminé !\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
