<?php
/**
 * Database Migration Runner
 * Run this script to set up or update the database schema
 */

require_once __DIR__ . '/../src/App.php';

class MigrationRunner
{
    private $db;
    private $migrationsPath;
    private $seedsPath;
    
    public function __construct()
    {
        $app = App::getInstance();
        $this->db = $app->getDatabase();
        $this->migrationsPath = __DIR__ . '/migrations/';
        $this->seedsPath = __DIR__ . '/seeds/';
    }
    
    public function runMigrations()
    {
        echo "Running database migrations...\n";
        
        // Create migrations table if it doesn't exist
        $this->createMigrationsTable();
        
        // Get executed migrations
        $executed = $this->getExecutedMigrations();
        
        // Get migration files
        $files = glob($this->migrationsPath . '*.sql');
        sort($files);
        
        foreach ($files as $file) {
            $migration = basename($file);
            
            if (!in_array($migration, $executed)) {
                echo "Executing migration: {$migration}\n";
                $this->executeMigration($file, $migration);
            } else {
                echo "Skipping migration: {$migration} (already executed)\n";
            }
        }
        
        echo "Migrations completed.\n";
    }
    
    public function runSeeds()
    {
        echo "Running database seeds...\n";
        
        $files = glob($this->seedsPath . '*.sql');
        sort($files);
        
        foreach ($files as $file) {
            $seed = basename($file);
            echo "Executing seed: {$seed}\n";
            $this->executeSqlFile($file);
        }
        
        echo "Seeds completed.\n";
    }
    
    private function createMigrationsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->exec($sql);
    }
    
    private function getExecutedMigrations()
    {
        try {
            $stmt = $this->db->query("SELECT migration FROM migrations");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    private function executeMigration($file, $migration)
    {
        try {
            $this->executeSqlFile($file);
            
            // Mark as executed
            $stmt = $this->db->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $stmt->execute([$migration]);
            
            echo "✓ Migration {$migration} executed successfully.\n";
        } catch (Exception $e) {
            echo "✗ Error executing migration {$migration}: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    private function executeSqlFile($file)
    {
        $sql = file_get_contents($file);
        
        // Split by semicolon to handle multiple statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $this->db->exec($statement);
            }
        }
    }
}

// Run migrations and seeds
try {
    $runner = new MigrationRunner();
    
    if (isset($argv[1]) && $argv[1] === '--seeds-only') {
        $runner->runSeeds();
    } elseif (isset($argv[1]) && $argv[1] === '--migrations-only') {
        $runner->runMigrations();
    } else {
        $runner->runMigrations();
        $runner->runSeeds();
    }
    
    echo "\nDatabase setup completed successfully!\n";
    echo "Default admin credentials:\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}