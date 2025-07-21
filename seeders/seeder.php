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
        
        // Réinitialiser les séquences/AUTO_INCREMENT
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
        
        // Vider la table d'abord
        $this->pdo->exec("DELETE FROM citoyen");
        
        // Réinitialiser les séquences/AUTO_INCREMENT
        if ($this->driver === 'pgsql') {
            $this->pdo->exec("SELECT setval('citoyen_id_seq', 1, false)");
        } else {
            $this->pdo->exec("ALTER TABLE citoyen AUTO_INCREMENT = 1");
        }
        
        $citoyens = [
            [
                'nom' => 'Diallo',
                'prenom' => 'Mamadou',
                'cni' => '1234567890123',
                'date_naissance' => '1985-03-15',
                'lieu_naissance' => 'Dakar, Sénégal',
                'photo_cni_recto' => 'uploads/cni/recto_1234567890123.jpg',
                'photo_cni_verso' => 'uploads/cni/verso_1234567890123.jpg'
            ],
            [
                'nom' => 'Sarr',
                'prenom' => 'Aïssa',
                'cni' => '2345678901234',
                'date_naissance' => '1990-07-22',
                'lieu_naissance' => 'Thiès, Sénégal',
                'photo_cni_recto' => 'uploads/cni/recto_2345678901234.jpg',
                'photo_cni_verso' => 'uploads/cni/verso_2345678901234.jpg'
            ],
            [
                'nom' => 'Ndiaye',
                'prenom' => 'Ibrahima',
                'cni' => '3456789012345',
                'date_naissance' => '1988-12-10',
                'lieu_naissance' => 'Saint-Louis, Sénégal',
                'photo_cni_recto' => 'uploads/cni/recto_3456789012345.jpg',
                'photo_cni_verso' => 'uploads/cni/verso_3456789012345.jpg'
            ],
            [
                'nom' => 'Ba',
                'prenom' => 'Fatou',
                'cni' => '4567890123456',
                'date_naissance' => '1992-05-18',
                'lieu_naissance' => 'Kaolack, Sénégal',
                'photo_cni_recto' => 'uploads/cni/recto_4567890123456.jpg',
                'photo_cni_verso' => 'uploads/cni/verso_4567890123456.jpg'
            ],
            [
                'nom' => 'Fall',
                'prenom' => 'Ousmane',
                'cni' => '5678901234567',
                'date_naissance' => '1987-09-03',
                'lieu_naissance' => 'Ziguinchor, Sénégal',
                'photo_cni_recto' => 'uploads/cni/recto_5678901234567.jpg',
                'photo_cni_verso' => 'uploads/cni/verso_5678901234567.jpg'
            ],
            [
                'nom' => 'Sow',
                'prenom' => 'Mariama',
                'cni' => '6789012345678',
                'date_naissance' => '1993-11-28',
                'lieu_naissance' => 'Tambacounda, Sénégal',
                'photo_cni_recto' => 'uploads/cni/recto_6789012345678.jpg',
                'photo_cni_verso' => 'uploads/cni/verso_6789012345678.jpg'
            ],
            [
                'nom' => 'Diouf',
                'prenom' => 'Cheikh',
                'cni' => '7890123456789',
                'date_naissance' => '1986-01-14',
                'lieu_naissance' => 'Diourbel, Sénégal',
                'photo_cni_recto' => 'uploads/cni/recto_7890123456789.jpg',
                'photo_cni_verso' => 'uploads/cni/verso_7890123456789.jpg'
            ],
            [
                'nom' => 'Cissé',
                'prenom' => 'Aminata',
                'cni' => '8901234567890',
                'date_naissance' => '1991-08-07',
                'lieu_naissance' => 'Kolda, Sénégal',
                'photo_cni_recto' => 'uploads/cni/recto_8901234567890.jpg',
                'photo_cni_verso' => 'uploads/cni/verso_8901234567890.jpg'
            ]
        ];
        
        $sql = "INSERT INTO citoyen (nom, prenom, cni, date_naissance, lieu_naissance, photo_cni_recto, photo_cni_verso) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($citoyens as $citoyen) {
            $stmt->execute([
                $citoyen['nom'],
                $citoyen['prenom'],
                $citoyen['cni'],
                $citoyen['date_naissance'],
                $citoyen['lieu_naissance'],
                $citoyen['photo_cni_recto'],
                $citoyen['photo_cni_verso']
            ]);
        }
        
        echo "✅ " . count($citoyens) . " citoyens insérés.\n";
    }
    
    public function seedJournal(): void {
        echo "📊 Insertion des entrées de journal ($this->driver)...\n";
        
        // Vider la table d'abord
        $this->pdo->exec("DELETE FROM journal");
        
        // Réinitialiser les séquences/AUTO_INCREMENT
        if ($this->driver === 'pgsql') {
            $this->pdo->exec("SELECT setval('journal_id_seq', 1, false)");
        } else {
            $this->pdo->exec("ALTER TABLE journal AUTO_INCREMENT = 1");
        }
        
        // Récupérer les CNI existants
        $stmt = $this->pdo->query("SELECT cni FROM citoyen");
        $cnis = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($cnis)) {
            echo "⚠️ Aucun citoyen trouvé. Veuillez d'abord exécuter seedCitoyens().\n";
            return;
        }
        
        $journalEntries = [];
        $statuts = ['SUCCES', 'ECHEC', 'EN_COURS', 'ANNULE'];
        $codes_http = ['200', '201', '400', '401', '403', '404', '500', '503'];
        $ips = [
            '192.168.1.10',
            '10.0.0.15',
            '172.16.0.5',
            '192.168.0.100',
            '10.1.1.25',
            '172.20.0.8',
            '192.168.100.50',
            '10.10.10.10'
        ];
        $localisations = [
            'Dakar, Plateau',
            'Thiès, Centre-ville',
            'Saint-Louis, Sor',
            'Kaolack, Médina',
            'Ziguinchor, Centre',
            'Tambacounda, Ville',
            'Diourbel, Centre',
            'Kolda, Sikilo'
        ];
        
        // Générer des entrées de journal pour les 30 derniers jours
        for ($i = 0; $i < 150; $i++) {
            $date = date('Y-m-d', strtotime("-" . rand(0, 30) . " days"));
            $heure = rand(8, 18); // Heures de bureau
            $cni = $cnis[array_rand($cnis)];
            $statut = $statuts[array_rand($statuts)];
            
            // Associer codes HTTP aux statuts de manière logique
            $code_http = match($statut) {
                'SUCCES' => ['200', '201'][array_rand(['200', '201'])],
                'ECHEC' => ['400', '401', '403', '404', '500'][array_rand(['400', '401', '403', '404', '500'])],
                'EN_COURS' => '202',
                'ANNULE' => '409'
            };
            
            $journalEntries[] = [
                'date' => $date,
                'heure' => $heure,
                'ip_adresse' => $ips[array_rand($ips)],
                'localisation' => $localisations[array_rand($localisations)],
                'status' => $statut,
                'code_http' => $code_http,
                'cni' => $cni
            ];
        }
        
        $sql = "INSERT INTO journal (date, heure, ip_adresse, localisation, status, code_http, cni) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($journalEntries as $entry) {
            $stmt->execute([
                $entry['date'],
                $entry['heure'],
                $entry['ip_adresse'],
                $entry['localisation'],
                $entry['status'],
                $entry['code_http'],
                $entry['cni']
            ]);
        }
        
        echo "✅ " . count($journalEntries) . " entrées de journal insérées.\n";
    }
    
    public function showStats(): void {
        echo "\n📊 Statistiques de la base ($this->driver):\n";
        
        // Compter les citoyens
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM citoyen");
        $citoyenCount = $stmt->fetch()['count'];
        echo "👥 Citoyens: $citoyenCount\n";
        
        // Compter les entrées de journal par statut
        $stmt = $this->pdo->query("
            SELECT status, COUNT(*) as count 
            FROM journal 
            GROUP BY status 
            ORDER BY status
        ");
        while ($row = $stmt->fetch()) {
            echo "📋 Journal {$row['status']}: {$row['count']}\n";
        }
        
        // Compter les entrées par code HTTP
        $stmt = $this->pdo->query("
            SELECT code_http, COUNT(*) as count 
            FROM journal 
            GROUP BY code_http 
            ORDER BY code_http
        ");
        echo "🔢 Codes HTTP:\n";
        while ($row = $stmt->fetch()) {
            echo "   {$row['code_http']}: {$row['count']} requêtes\n";
        }
        
        // Activité par jour (derniers 7 jours)
        $stmt = $this->pdo->query("
            SELECT date, COUNT(*) as count 
            FROM journal 
            WHERE date >= CURRENT_DATE - INTERVAL '7 days'
            GROUP BY date 
            ORDER BY date DESC
            LIMIT 7
        ");
        echo "📅 Activité (7 derniers jours):\n";
        while ($row = $stmt->fetch()) {
            echo "   {$row['date']}: {$row['count']} événements\n";
        }
    }
    
    public function createDirectories(): void {
        echo "📁 Création des dossiers d'upload...\n";
        
        $directories = [
            'uploads',
            'uploads/cni',
            'uploads/temp'
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                echo "✅ Dossier créé: $dir\n";
            } else {
                echo "ℹ️ Dossier existe: $dir\n";
            }
        }
    }
    
    public function createSampleFiles(): void {
        echo "📄 Création des fichiers d'exemple CNI...\n";
        
        // Récupérer les CNI pour créer les fichiers correspondants
        $stmt = $this->pdo->query("SELECT cni FROM citoyen");
        $cnis = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($cnis as $cni) {
            $rectoFile = "uploads/cni/recto_{$cni}.jpg";
            $versoFile = "uploads/cni/verso_{$cni}.jpg";
            
            if (!file_exists($rectoFile)) {
                $content = "CNI RECTO - $cni - Fichier d'exemple";
                file_put_contents($rectoFile, $content);
                echo "✅ Fichier créé: $rectoFile\n";
            }
            
            if (!file_exists($versoFile)) {
                $content = "CNI VERSO - $cni - Fichier d'exemple";
                file_put_contents($versoFile, $content);
                echo "✅ Fichier créé: $versoFile\n";
            }
        }
    }
    
    public function seedAll(): void {
        echo "🌱 Seeding complet de la base Citoyen-Journal ($this->driver)...\n\n";
        
        $this->clearAll();
        $this->seedCitoyens();
        $this->seedJournal();
        
        echo "\n🎉 Seeding terminé avec succès !\n";
        $this->showStats();
    }
    
    public function resetSequences(): void {
        echo "🔢 Réinitialisation des séquences...\n";
        
        if ($this->driver === 'pgsql') {
            // Obtenir le max ID actuel pour chaque table
            $stmt = $this->pdo->query("SELECT COALESCE(MAX(id), 0) + 1 as next_val FROM citoyen");
            $nextCitoyenId = $stmt->fetch()['next_val'];
            
            $stmt = $this->pdo->query("SELECT COALESCE(MAX(id), 0) + 1 as next_val FROM journal");
            $nextJournalId = $stmt->fetch()['next_val'];
            
            $this->pdo->exec("SELECT setval('citoyen_id_seq', $nextCitoyenId, false)");
            $this->pdo->exec("SELECT setval('journal_id_seq', $nextJournalId, false)");
        } else {
            // Pour MySQL, les AUTO_INCREMENT se gèrent automatiquement
            echo "ℹ️ MySQL gère automatiquement les AUTO_INCREMENT.\n";
        }
        
        echo "✅ Séquences réinitialisées.\n";
    }
}

// 🚀 Exécution
try {
    $driver = $_ENV['DB_DRIVER'] ?? 'pgsql';
    $dbName = $_ENV['DB_NAME'] ?? 'citoyen_db';
    
    echo "🌱 Démarrage du seeder Citoyen-Journal ($driver)...\n";
    echo "📊 Base de données: $dbName\n\n";
    
    $seeder = new CitoyenJournalSeeder();
    
    // Vérifier les arguments de ligne de commande
    $action = $argv[1] ?? 'all';
    
    switch ($action) {
        case 'citoyens':
        case 'citizens':
            $seeder->seedCitoyens();
            break;
            
        case 'journal':
        case 'log':
            $seeder->seedJournal();
            break;
            
        case 'clear':
        case 'clean':
            $seeder->clearAll();
            break;
            
        case 'stats':
            $seeder->showStats();
            break;
            
        case 'files':
            $seeder->createDirectories();
            $seeder->createSampleFiles();
            break;
            
        case 'sequences':
            $seeder->resetSequences();
            break;
            
        case 'all':
        default:
            $seeder->createDirectories();
            $seeder->seedAll();
            $seeder->createSampleFiles();
            break;
    }
    
    echo "\n💡 Commandes disponibles:\n";
    echo "  php citoyen_seeder.php all       - Seeding complet (défaut)\n";
    echo "  php citoyen_seeder.php citoyens  - Seulement les citoyens\n";
    echo "  php citoyen_seeder.php journal   - Seulement le journal\n";
    echo "  php citoyen_seeder.php clear     - Vider toutes les données\n";
    echo "  php citoyen_seeder.php stats     - Afficher les statistiques\n";
    echo "  php citoyen_seeder.php files     - Créer les dossiers et fichiers\n";
    echo "  php citoyen_seeder.php sequences - Réinitialiser les séquences\n";
    echo "\n🎯 Driver utilisé: $driver\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur de base de données: " . $e->getMessage() . "\n";
    echo "💡 Vérifiez que la migration a été exécutée et que la base existe.\n";
    echo "💡 Driver configuré: " . ($_ENV['DB_DRIVER'] ?? 'pgsql') . "\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}