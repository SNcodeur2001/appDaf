<?php
class CitoyenJournalMigration {
    private PDO $pdo;
    private string $driver;
    
    public function __construct() {
        $this->loadEnv();
        $this->driver = $_ENV['DB_DRIVER'] ?? 'pgsql';
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
        
        // Debug : vérifiez que les variables sont bien chargées
        echo "🔍 Debug - Variables chargées :\n";
        echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NON DÉFINI') . "\n";
        echo "DB_PORT: " . ($_ENV['DB_PORT'] ?? 'NON DÉFINI') . "\n";
        echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'NON DÉFINI') . "\n";
        echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'NON DÉFINI') . "\n\n";
    }
    
    private function connectToDefaultDatabase(): PDO {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '5432';
        $user = $_ENV['DB_USER'] ?? ($this->driver === 'pgsql' ? 'postgres' : 'root');
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        
        if ($this->driver === 'pgsql') {
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=postgres", $user, $pass);
        } else {
            $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
        }
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
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
    
    public function createDatabase(string $dbName): void {
        echo "🔧 Création de la base de données '$dbName' ($this->driver)...\n";
        
        try {
            if ($this->driver === 'pgsql') {
                $this->createPostgreSQLDatabase($dbName);
            } else {
                $this->createMySQLDatabase($dbName);
            }
            
            $this->initializeConnection();
            echo "✅ Connexion à la base '$dbName' établie.\n";
            
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création/connexion à la base: " . $e->getMessage());
        }
    }
    
    private function createPostgreSQLDatabase(string $dbName): void {
        $defaultPdo = $this->connectToDefaultDatabase();
        
        $stmt = $defaultPdo->prepare("SELECT 1 FROM pg_database WHERE datname = ?");
        $stmt->execute([$dbName]);
        
        if (!$stmt->fetch()) {
            $defaultPdo->exec("CREATE DATABASE \"$dbName\"");
            echo "✅ Base de données PostgreSQL '$dbName' créée.\n";
        } else {
            echo "ℹ️ Base de données PostgreSQL '$dbName' existe déjà.\n";
        }
    }
    
    private function createMySQLDatabase(string $dbName): void {
        $defaultPdo = $this->connectToDefaultDatabase();
        
        $defaultPdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✅ Base de données MySQL '$dbName' créée ou existe déjà.\n";
    }
    
    public function createTypes(): void {
        if ($this->driver === 'pgsql') {
            echo "🔧 Création du type StatutEnum PostgreSQL...\n";
            
            $type = "CREATE TYPE statut_enum AS ENUM ('SUCCES', 'ECHEC', 'EN_COURS', 'ANNULE')";
            
            try {
                $this->pdo->exec($type);
                echo "✅ Type StatutEnum PostgreSQL créé.\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                } else {
                    echo "ℹ️ Type StatutEnum existe déjà.\n";
                }
            }
        } else {
            echo "ℹ️ MySQL utilise des ENUM intégrés, pas de types personnalisés à créer.\n";
        }
    }
    
    public function createTables(): void {
        echo "📋 Création des tables ($this->driver)...\n";
        
        if ($this->driver === 'pgsql') {
            $this->createPostgreSQLTables();
        } else {
            $this->createMySQLTables();
        }
        
        echo "✅ Tables créées avec succès.\n";
    }
    
    private function createPostgreSQLTables(): void {
        $tables = [
            "CREATE TABLE IF NOT EXISTS citoyen (
                id SERIAL PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                cni VARCHAR(50) NOT NULL UNIQUE,
                date_naissance DATE NOT NULL,
                lieu_naissance VARCHAR(200) NOT NULL,
                photo_cni_recto VARCHAR(500),
                photo_cni_verso VARCHAR(500),
                created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS journal (
                id SERIAL PRIMARY KEY,
                date DATE NOT NULL,
                heure INTEGER NOT NULL CHECK (heure >= 0 AND heure <= 23),
                ip_adresse INET NOT NULL,
                localisation VARCHAR(200) NOT NULL,
                status statut_enum NOT NULL,
                code_http VARCHAR(10) NOT NULL,
                cni VARCHAR(50) NOT NULL,
                created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (cni) REFERENCES citoyen(cni) ON DELETE CASCADE
            )"
        ];
        
        foreach ($tables as $sql) {
            $this->pdo->exec($sql);
        }
    }
    
    private function createMySQLTables(): void {
        $tables = [
            "CREATE TABLE IF NOT EXISTS citoyen (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                cni VARCHAR(50) NOT NULL UNIQUE,
                date_naissance DATE NOT NULL,
                lieu_naissance VARCHAR(200) NOT NULL,
                photo_cni_recto VARCHAR(500),
                photo_cni_verso VARCHAR(500),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            "CREATE TABLE IF NOT EXISTS journal (
                id INT AUTO_INCREMENT PRIMARY KEY,
                date DATE NOT NULL,
                heure TINYINT UNSIGNED NOT NULL CHECK (heure <= 23),
                ip_adresse VARCHAR(45) NOT NULL,
                localisation VARCHAR(200) NOT NULL,
                status ENUM('SUCCES', 'ECHEC', 'EN_COURS', 'ANNULE') NOT NULL,
                code_http VARCHAR(10) NOT NULL,
                cni VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (cni) REFERENCES citoyen(cni) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
        
        foreach ($tables as $sql) {
            $this->pdo->exec($sql);
        }
    }
    
    public function dropTables(): void {
        echo "🗑️ Suppression des tables existantes...\n";
        
        if ($this->driver === 'mysql') {
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        }
        
        $tables = ['journal', 'citoyen'];
        foreach ($tables as $table) {
            if ($this->driver === 'pgsql') {
                $this->pdo->exec("DROP TABLE IF EXISTS $table CASCADE");
            } else {
                $this->pdo->exec("DROP TABLE IF EXISTS $table");
            }
        }
        
        if ($this->driver === 'mysql') {
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        }
        
        echo "✅ Tables supprimées.\n";
    }
    
    public function dropTypes(): void {
        if ($this->driver === 'pgsql') {
            echo "🗑️ Suppression du type StatutEnum PostgreSQL...\n";
            
            try {
                $this->pdo->exec("DROP TYPE IF EXISTS statut_enum CASCADE");
                echo "✅ Type StatutEnum supprimé.\n";
            } catch (PDOException $e) {
                // Ignorer les erreurs
            }
        } else {
            echo "ℹ️ MySQL n'utilise pas de types personnalisés à supprimer.\n";
        }
    }
    
    public function showTables(): void {
        echo "\n📋 Tables créées ($this->driver):\n";
        
        if ($this->driver === 'pgsql') {
            $stmt = $this->pdo->query("
                SELECT tablename 
                FROM pg_tables 
                WHERE schemaname = 'public' 
                AND tablename IN ('citoyen', 'journal')
                ORDER BY tablename
            ");
            
            while ($row = $stmt->fetch()) {
                echo "- " . $row['tablename'] . "\n";
            }
            
            echo "\n🔧 Type personnalisé:\n";
            $stmt = $this->pdo->query("
                SELECT typname 
                FROM pg_type 
                WHERE typname = 'statut_enum'
            ");
            if ($row = $stmt->fetch()) {
                echo "- " . $row['typname'] . "\n";
            }
        } else {
            $stmt = $this->pdo->query("SHOW TABLES");
            while ($row = $stmt->fetch()) {
                echo "- " . $row[0] . "\n";
            }
        }
    }
    
    public function resetSequences(): void {
        echo "🔢 Réinitialisation des séquences...\n";
        
        if ($this->driver === 'pgsql') {
            $sequences = [
                'citoyen_id_seq' => 1,
                'journal_id_seq' => 1
            ];
            
            foreach ($sequences as $sequence => $value) {
                $this->pdo->exec("SELECT setval('$sequence', $value, false)");
            }
        } else {
            $this->pdo->exec("ALTER TABLE citoyen AUTO_INCREMENT = 1");
            $this->pdo->exec("ALTER TABLE journal AUTO_INCREMENT = 1");
        }
        
        echo "✅ Séquences/AUTO_INCREMENT réinitialisés.\n";
    }
    
    public function reset(): void {
        echo "🔄 Réinitialisation complète de la base ($this->driver)...\n";
        
        $dbName = $_ENV['DB_NAME'] ?? 'citoyen_db';
        
        $this->createDatabase($dbName);
        $this->dropTables();
        $this->dropTypes();
        $this->createTypes();
        $this->createTables();
        
        echo "✅ Base réinitialisée.\n";
    }
}

// 🚀 Exécution
try {
    $driver = $_ENV['DB_DRIVER'] ?? 'pgsql';
    $dbName = $_ENV['DB_NAME'] ?? 'citoyen_db';
    
    echo "🚀 Démarrage de la migration Citoyen-Journal ($driver)...\n\n";
    
    $migration = new CitoyenJournalMigration();
    
    if (isset($argv[1]) && $argv[1] === '--reset') {
        $migration->reset();
        $migration->resetSequences();
    } else {
        $migration->createDatabase($dbName);
        $migration->createTypes();
        $migration->createTables();
    }
    
    $migration->showTables();
    
    echo "\n🎉 Migration Citoyen-Journal ($driver) terminée avec succès.\n";
    echo "💡 Pour réinitialiser : php citoyen_migration.php --reset\n";
    echo "💡 Base de données: $dbName ($driver)\n";
    echo "💡 Relation: Journal.cni -> Citoyen.cni (clé étrangère)\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur PDO : " . $e->getMessage() . "\n";
    echo "💡 Vérifiez que votre SGBD est démarré et que les identifiants sont corrects.\n";
    echo "💡 Driver configuré : " . ($_ENV['DB_DRIVER'] ?? 'pgsql') . "\n";
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}