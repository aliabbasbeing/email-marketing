<?php
/**
 * Installation Utilities
 * Shared functions for installation and environment detection
 */

class InstallerUtils 
{
    /**
     * Detect the hosting environment
     */
    public static function detectEnvironment()
    {
        $environment = [
            'type' => 'generic',
            'is_xampp' => false,
            'is_cpanel' => false,
            'is_localhost' => false,
            'web_server' => 'unknown',
            'php_version' => PHP_VERSION,
            'can_write_files' => false,
            'has_mysql' => false,
            'has_sqlite' => false,
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? '',
            'script_path' => __DIR__,
        ];

        // Check if localhost
        $environment['is_localhost'] = in_array($_SERVER['HTTP_HOST'] ?? '', [
            'localhost', '127.0.0.1', '::1'
        ]) || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost:') === 0;

        // Detect XAMPP
        $environment['is_xampp'] = (
            $environment['is_localhost'] && (
                strpos($environment['document_root'], 'xampp') !== false ||
                strpos($environment['document_root'], 'htdocs') !== false ||
                file_exists($environment['document_root'] . '/../xampp-control.exe') ||
                file_exists('/opt/lampp/htdocs') ||
                file_exists('C:\\xampp\\htdocs')
            )
        );

        // Detect cPanel
        $environment['is_cpanel'] = (
            !$environment['is_localhost'] && (
                file_exists($_SERVER['HOME'] . '/public_html') ||
                strpos($environment['document_root'], 'public_html') !== false ||
                isset($_SERVER['cPanel']) ||
                file_exists('/usr/local/cpanel')
            )
        );

        // Set environment type
        if ($environment['is_xampp']) {
            $environment['type'] = 'xampp';
        } elseif ($environment['is_cpanel']) {
            $environment['type'] = 'cpanel';
        } elseif ($environment['is_localhost']) {
            $environment['type'] = 'localhost';
        }

        // Detect web server
        $server_software = $_SERVER['SERVER_SOFTWARE'] ?? '';
        if (strpos($server_software, 'Apache') !== false) {
            $environment['web_server'] = 'apache';
        } elseif (strpos($server_software, 'nginx') !== false) {
            $environment['web_server'] = 'nginx';
        } elseif (strpos($server_software, 'Microsoft-IIS') !== false) {
            $environment['web_server'] = 'iis';
        } elseif (php_sapi_name() === 'cli-server') {
            $environment['web_server'] = 'php-cli-server';
        }

        // Check file write permissions
        $environment['can_write_files'] = is_writable(__DIR__ . '/../');

        // Check database support
        $environment['has_mysql'] = extension_loaded('pdo_mysql');
        $environment['has_sqlite'] = extension_loaded('pdo_sqlite');

        return $environment;
    }

    /**
     * Check system requirements
     */
    public static function checkRequirements()
    {
        $requirements = [
            'php_version' => [
                'required' => '7.4.0',
                'current' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '7.4.0', '>=')
            ],
            'pdo' => [
                'required' => true,
                'current' => extension_loaded('pdo'),
                'status' => extension_loaded('pdo')
            ],
            'pdo_mysql' => [
                'required' => false, // Optional, SQLite fallback available
                'current' => extension_loaded('pdo_mysql'),
                'status' => true // Always pass since SQLite is fallback
            ],
            'pdo_sqlite' => [
                'required' => true,
                'current' => extension_loaded('pdo_sqlite'),
                'status' => extension_loaded('pdo_sqlite')
            ],
            'mbstring' => [
                'required' => true,
                'current' => extension_loaded('mbstring'),
                'status' => extension_loaded('mbstring')
            ],
            'openssl' => [
                'required' => true,
                'current' => extension_loaded('openssl'),
                'status' => extension_loaded('openssl')
            ],
            'curl' => [
                'required' => true,
                'current' => extension_loaded('curl'),
                'status' => extension_loaded('curl')
            ],
            'file_permissions' => [
                'required' => true,
                'current' => is_writable(__DIR__ . '/../'),
                'status' => is_writable(__DIR__ . '/../')
            ]
        ];

        $all_passed = true;
        foreach ($requirements as $req) {
            if ($req['required'] && !$req['status']) {
                $all_passed = false;
                break;
            }
        }

        return [
            'passed' => $all_passed,
            'requirements' => $requirements
        ];
    }

    /**
     * Create database configuration based on environment
     */
    public static function createDatabaseConfig($environment, $mysql_config = null)
    {
        $config = [];

        if ($environment['type'] === 'xampp' || ($environment['is_localhost'] && !$mysql_config)) {
            // Use SQLite for XAMPP/localhost by default
            $config = [
                'DB_CONNECTION' => 'sqlite',
                'DB_DATABASE' => __DIR__ . '/../storage/database.sqlite',
                'DB_HOST' => '',
                'DB_PORT' => '',
                'DB_USERNAME' => '',
                'DB_PASSWORD' => ''
            ];
        } elseif ($mysql_config && isset($mysql_config['host'])) {
            // Use provided MySQL configuration
            $config = [
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $mysql_config['host'],
                'DB_PORT' => $mysql_config['port'] ?? '3306',
                'DB_DATABASE' => $mysql_config['database'],
                'DB_USERNAME' => $mysql_config['username'],
                'DB_PASSWORD' => $mysql_config['password'] ?? ''
            ];
        } else {
            // Default to SQLite as fallback
            $config = [
                'DB_CONNECTION' => 'sqlite',
                'DB_DATABASE' => __DIR__ . '/../storage/database.sqlite',
                'DB_HOST' => '',
                'DB_PORT' => '',
                'DB_USERNAME' => '',
                'DB_PASSWORD' => ''
            ];
        }

        return $config;
    }

    /**
     * Test database connection
     */
    public static function testDatabaseConnection($config)
    {
        try {
            if ($config['DB_CONNECTION'] === 'sqlite') {
                // Ensure storage directory exists
                $storage_dir = dirname($config['DB_DATABASE']);
                if (!is_dir($storage_dir)) {
                    mkdir($storage_dir, 0755, true);
                }
                
                $dsn = "sqlite:" . $config['DB_DATABASE'];
                $pdo = new PDO($dsn);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->exec('PRAGMA foreign_keys = ON');
                return ['success' => true, 'message' => 'SQLite database connection successful'];
            } else {
                $dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};charset=utf8mb4";
                $pdo = new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Try to create database if it doesn't exist
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['DB_DATABASE']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                return ['success' => true, 'message' => 'MySQL database connection successful'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    /**
     * Create .env file from configuration
     */
    public static function createEnvFile($config)
    {
        $env_content = "# Email Marketing Tool Configuration\n";
        $env_content .= "# Generated automatically on " . date('Y-m-d H:i:s') . "\n\n";
        
        $env_content .= "# Database Configuration\n";
        foreach ($config as $key => $value) {
            if (strpos($key, 'DB_') === 0) {
                $env_content .= "{$key}={$value}\n";
            }
        }
        
        $env_content .= "\n# SMTP Configuration\n";
        $env_content .= "SMTP_HOST=smtp.gmail.com\n";
        $env_content .= "SMTP_PORT=587\n";
        $env_content .= "SMTP_USERNAME=your-email@gmail.com\n";
        $env_content .= "SMTP_PASSWORD=your-app-password\n";
        $env_content .= "SMTP_ENCRYPTION=tls\n";
        $env_content .= "SMTP_FROM_EMAIL=your-email@gmail.com\n";
        $env_content .= "SMTP_FROM_NAME=\"Email Marketing Tool\"\n\n";
        
        $env_content .= "# Application Configuration\n";
        $env_content .= "APP_DEBUG=" . (isset($config['APP_DEBUG']) ? $config['APP_DEBUG'] : 'true') . "\n";
        $env_content .= "APP_ENV=" . (isset($config['APP_ENV']) ? $config['APP_ENV'] : 'development') . "\n\n";
        
        $env_content .= "# Security\n";
        $env_content .= "CSRF_TOKEN_LENGTH=32\n";
        $env_content .= "SESSION_LIFETIME=7200\n";
        $env_content .= "PASSWORD_MIN_LENGTH=8\n";

        $env_file_path = __DIR__ . '/../.env';
        return file_put_contents($env_file_path, $env_content) !== false;
    }

    /**
     * Run database migrations
     */
    public static function runMigrations()
    {
        try {
            // Load environment
            self::loadEnv();
            
            // Include migration runner
            require_once __DIR__ . '/../database/migrate.php';
            
            $migrator = new MigrationRunner();
            
            // Capture output silently
            ob_start();
            $migrator->runMigrations();
            
            // Try to run seeds, but don't fail if they error (e.g., duplicate data)
            try {
                $migrator->runSeeds();
            } catch (Exception $seedError) {
                // Log seed error but don't fail the installation
                error_log("Seed warning: " . $seedError->getMessage());
            }
            
            $output = ob_get_clean();
            
            return ['success' => true, 'output' => $output];
        } catch (Exception $e) {
            ob_end_clean(); // Clean any pending output
            return ['success' => false, 'message' => 'Migration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Load environment variables from .env file
     */
    public static function loadEnv()
    {
        $env_file = __DIR__ . '/../.env';
        if (!file_exists($env_file)) {
            return false;
        }

        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
            }
        }
        return true;
    }

    /**
     * Create necessary directories
     */
    public static function createDirectories()
    {
        $directories = [
            __DIR__ . '/../storage',
            __DIR__ . '/../storage/logs',
            __DIR__ . '/../storage/uploads',
            __DIR__ . '/../storage/cache',
            __DIR__ . '/../storage/sessions'
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Set proper file permissions
     */
    public static function setPermissions()
    {
        $paths = [
            __DIR__ . '/../storage' => 0777,
            __DIR__ . '/../storage/logs' => 0777,
            __DIR__ . '/../storage/uploads' => 0777,
            __DIR__ . '/../storage/cache' => 0777,
            __DIR__ . '/../storage/sessions' => 0777,
            __DIR__ . '/../public' => 0755
        ];

        foreach ($paths as $path => $permission) {
            if (file_exists($path)) {
                chmod($path, $permission);
            }
        }
        return true;
    }

    /**
     * Create .htaccess files for security
     */
    public static function createHtaccessFiles()
    {
        // Main .htaccess for URL rewriting
        $main_htaccess = __DIR__ . '/../.htaccess';
        if (!file_exists($main_htaccess)) {
            $content = "# Email Marketing Tool - Main .htaccess\n";
            $content .= "RewriteEngine On\n\n";
            $content .= "# Redirect to public directory\n";
            $content .= "RewriteCond %{REQUEST_URI} !^/public/\n";
            $content .= "RewriteRule ^(.*)$ /public/$1 [L]\n\n";
            $content .= "# Security headers\n";
            $content .= "Header always set X-Frame-Options DENY\n";
            $content .= "Header always set X-Content-Type-Options nosniff\n";
            $content .= "Header always set X-XSS-Protection \"1; mode=block\"\n\n";
            $content .= "# Hide sensitive files\n";
            $content .= "<Files \".env\">\n";
            $content .= "    Order allow,deny\n";
            $content .= "    Deny from all\n";
            $content .= "</Files>\n";
            
            file_put_contents($main_htaccess, $content);
        }

        // Public .htaccess
        $public_htaccess = __DIR__ . '/../public/.htaccess';
        if (!file_exists($public_htaccess)) {
            $content = "# Email Marketing Tool - Public .htaccess\n";
            $content .= "RewriteEngine On\n\n";
            $content .= "# Handle Angular HTML5 routing\n";
            $content .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
            $content .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
            $content .= "RewriteRule . /index.php [L]\n\n";
            $content .= "# Disable directory browsing\n";
            $content .= "Options -Indexes\n\n";
            $content .= "# Security headers\n";
            $content .= "Header always set X-Frame-Options DENY\n";
            $content .= "Header always set X-Content-Type-Options nosniff\n";
            $content .= "Header always set X-XSS-Protection \"1; mode=block\"\n";
            
            file_put_contents($public_htaccess, $content);
        }

        // Storage .htaccess (deny access)
        $storage_htaccess = __DIR__ . '/../storage/.htaccess';
        if (!file_exists($storage_htaccess)) {
            $content = "# Deny access to storage directory\n";
            $content .= "Order deny,allow\n";
            $content .= "Deny from all\n";
            
            file_put_contents($storage_htaccess, $content);
        }

        return true;
    }
}