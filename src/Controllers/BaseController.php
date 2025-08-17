<?php

namespace Controllers;

/**
 * Base Controller Class
 * All controllers should extend this class
 */
abstract class BaseController
{
    protected $app;
    protected $db;
    protected $config;
    
    public function __construct()
    {
        $this->app = \App::getInstance();
        $this->db = $this->app->getDatabase();
        $this->config = $this->app->getConfig();
        
        // Initialize CSRF protection
        $this->initializeCsrf();
    }
    
    /**
     * Render a view with data
     */
    protected function render($view, $data = [], $layout = 'main')
    {
        // Extract data variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = __DIR__ . "/../../templates/pages/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \Exception("View file not found: {$view}");
        }
        
        // Get the view content
        $content = ob_get_clean();
        
        // Include the layout
        $layoutFile = __DIR__ . "/../../templates/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Redirect to a URL
     */
    protected function redirect($url, $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Get input data
     */
    protected function input($key = null, $default = null)
    {
        $data = array_merge($_GET, $_POST);
        
        if ($key === null) {
            return $data;
        }
        
        return isset($data[$key]) ? $this->sanitizeInput($data[$key]) : $default;
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCsrf()
    {
        if (!$this->config['security']['csrf']['enabled']) {
            return true;
        }
        
        $token = $this->input($this->config['security']['csrf']['token_name']);
        
        if (!$token || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
        
        return true;
    }
    
    /**
     * Generate CSRF token
     */
    protected function generateCsrf()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes($this->config['security']['csrf']['token_length']));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Initialize CSRF protection
     */
    private function initializeCsrf()
    {
        if ($this->config['security']['csrf']['enabled']) {
            $this->generateCsrf();
        }
    }
    
    /**
     * Check if user is authenticated
     */
    protected function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Authentication required'], 401);
            } else {
                $this->redirect('/login');
            }
        }
    }
    
    /**
     * Check if user has specific role
     */
    protected function requireRole($role)
    {
        $this->requireAuth();
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Insufficient permissions'], 403);
            } else {
                $this->render('error', ['message' => 'Access denied']);
            }
        }
    }
    
    /**
     * Check if request is AJAX
     */
    protected function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
    
    /**
     * Sanitize input data
     */
    protected function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        
        // Trim whitespace
        $data = trim($data);
        
        // Remove null bytes
        $data = str_replace(chr(0), '', $data);
        
        // Convert special characters to HTML entities
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $data;
    }
    
    /**
     * Validate input data
     */
    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $fieldRules = explode('|', $ruleSet);
            
            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;
                
                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field] = ucfirst($field) . ' is required';
                        }
                        break;
                    
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = ucfirst($field) . ' must be a valid email';
                        }
                        break;
                    
                    case 'min':
                        if (!empty($value) && strlen($value) < $ruleValue) {
                            $errors[$field] = ucfirst($field) . " must be at least {$ruleValue} characters";
                        }
                        break;
                    
                    case 'max':
                        if (!empty($value) && strlen($value) > $ruleValue) {
                            $errors[$field] = ucfirst($field) . " must not exceed {$ruleValue} characters";
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Flash message to session
     */
    protected function flash($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * Get flash message
     */
    protected function getFlash($key)
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    /**
     * Log activity
     */
    protected function log($message, $level = 'info')
    {
        if (!$this->config['app']['logging']['enabled']) {
            return;
        }
        
        $logFile = __DIR__ . '/../../' . $this->config['app']['logging']['path'] . 'app.log';
        $timestamp = date('Y-m-d H:i:s');
        $userId = $_SESSION['user_id'] ?? 'guest';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $logEntry = "[{$timestamp}] [{$level}] [User: {$userId}] [IP: {$ip}] {$message}" . PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}