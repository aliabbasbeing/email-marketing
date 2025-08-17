<?php
/**
 * Application Bootstrap
 * This file initializes the application and handles routing
 */

class App
{
    private static $instance = null;
    private $config = [];
    private $db = null;
    private $router = null;
    
    private function __construct()
    {
        $this->loadConfiguration();
        $this->initializeSession();
        $this->initializeDatabase();
        $this->initializeRouter();
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function loadConfiguration()
    {
        $this->config = [
            'app' => require_once __DIR__ . '/../config/app.php',
            'database' => require_once __DIR__ . '/../config/database.php',
            'smtp' => require_once __DIR__ . '/../config/smtp.php',
            'security' => require_once __DIR__ . '/../config/security.php',
        ];
        
        // Set timezone
        date_default_timezone_set($this->config['app']['timezone']);
        
        // Set error reporting based on debug mode
        if ($this->config['app']['debug']) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
    }
    
    private function initializeSession()
    {
        $sessionConfig = $this->config['security']['session'];
        
        session_set_cookie_params([
            'lifetime' => $sessionConfig['lifetime'],
            'httponly' => $sessionConfig['httponly_cookies'],
            'secure' => $sessionConfig['secure_cookies'],
            'samesite' => $sessionConfig['samesite']
        ]);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Regenerate session ID periodically for security
        if ($sessionConfig['regenerate_id'] && !isset($_SESSION['last_regeneration'])) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        } elseif (isset($_SESSION['last_regeneration']) && 
                  (time() - $_SESSION['last_regeneration']) > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    private function initializeDatabase()
    {
        $dbConfig = $this->config['database']['connections']['mysql'];
        
        try {
            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
            $this->db = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
        } catch (PDOException $e) {
            if ($this->config['app']['debug']) {
                die("Database connection failed: " . $e->getMessage());
            } else {
                die("Database connection failed. Please try again later.");
            }
        }
    }
    
    private function initializeRouter()
    {
        $this->router = new Router();
        $this->defineRoutes();
    }
    
    private function defineRoutes()
    {
        // Dashboard routes
        $this->router->get('/', 'DashboardController@index');
        $this->router->get('/dashboard', 'DashboardController@index');
        
        // Authentication routes
        $this->router->get('/login', 'AuthController@showLogin');
        $this->router->post('/login', 'AuthController@login');
        $this->router->get('/register', 'AuthController@showRegister');
        $this->router->post('/register', 'AuthController@register');
        $this->router->post('/logout', 'AuthController@logout');
        
        // Email campaign routes
        $this->router->get('/campaigns', 'CampaignController@index');
        $this->router->get('/campaigns/create', 'CampaignController@create');
        $this->router->post('/campaigns', 'CampaignController@store');
        $this->router->get('/campaigns/{id}', 'CampaignController@show');
        $this->router->get('/campaigns/{id}/results', 'CampaignController@results');
        
        // Email management routes
        $this->router->get('/emails', 'EmailController@index');
        $this->router->post('/emails/import', 'EmailController@import');
        $this->router->delete('/emails/{id}', 'EmailController@delete');
        
        // SMTP configuration routes
        $this->router->get('/settings/smtp', 'SettingsController@smtp');
        $this->router->post('/settings/smtp', 'SettingsController@updateSmtp');
        
        // API routes
        $this->router->get('/api/campaigns/{id}/progress', 'ApiController@campaignProgress');
        $this->router->post('/api/campaigns/{id}/stop', 'ApiController@stopCampaign');
        $this->router->get('/api/templates', 'ApiController@templates');
        $this->router->post('/api/templates', 'ApiController@createTemplate');
        
        // Tracking routes
        $this->router->get('/track/{id}', 'TrackingController@open');
        $this->router->get('/click/{id}', 'TrackingController@click');
    }
    
    public function run()
    {
        try {
            $this->router->dispatch();
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
    
    private function handleException(Exception $e)
    {
        if ($this->config['app']['debug']) {
            echo "<h1>Error</h1>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        } else {
            http_response_code(500);
            include __DIR__ . '/../templates/pages/error.php';
        }
    }
    
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public function getDatabase()
    {
        return $this->db;
    }
}

/**
 * Simple Router Class
 */
class Router
{
    private $routes = [];
    private $middleware = [];
    
    public function get($path, $handler)
    {
        $this->routes['GET'][$path] = $handler;
    }
    
    public function post($path, $handler)
    {
        $this->routes['POST'][$path] = $handler;
    }
    
    public function put($path, $handler)
    {
        $this->routes['PUT'][$path] = $handler;
    }
    
    public function delete($path, $handler)
    {
        $this->routes['DELETE'][$path] = $handler;
    }
    
    public function middleware($name, $callback)
    {
        $this->middleware[$name] = $callback;
    }
    
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path if exists
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/') {
            $path = substr($path, strlen($basePath));
        }
        
        if (!isset($this->routes[$method])) {
            $this->handle404();
            return;
        }
        
        foreach ($this->routes[$method] as $route => $handler) {
            $pattern = $this->convertRouteToRegex($route);
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove full match
                $this->callHandler($handler, $matches);
                return;
            }
        }
        
        $this->handle404();
    }
    
    private function convertRouteToRegex($route)
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }
    
    private function callHandler($handler, $params = [])
    {
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controllerName, $method) = explode('@', $handler);
            $controllerClass = "\\Controllers\\{$controllerName}";
            
            if (!class_exists($controllerClass)) {
                require_once __DIR__ . "/Controllers/{$controllerName}.php";
            }
            
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $params);
        } elseif (is_callable($handler)) {
            call_user_func_array($handler, $params);
        }
    }
    
    private function handle404()
    {
        http_response_code(404);
        include __DIR__ . '/../templates/pages/404.php';
    }
}

// Auto-loader for classes
spl_autoload_register(function ($className) {
    $className = ltrim($className, '\\');
    $fileName = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';
    
    if (file_exists($fileName)) {
        require_once $fileName;
    }
});