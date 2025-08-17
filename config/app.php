<?php
/**
 * Application Configuration
 */

return [
    'name' => 'Email Marketing Tool',
    'version' => '2.0.0',
    'debug' => true,
    'timezone' => 'UTC',
    
    // Environment settings
    'environment' => 'development',
    
    // Base URL configuration
    'base_url' => ($_SERVER['SERVER_NAME'] == 'localhost') 
        ? 'http://localhost/e-marketing/'
        : 'https://aliabbas.pk/',
    
    // Session configuration
    'session' => [
        'lifetime' => 7200, // 2 hours
        'cookie_httponly' => true,
        'cookie_secure' => false, // Set to true in production with HTTPS
        'cookie_samesite' => 'Lax',
    ],
    
    // Security settings
    'security' => [
        'csrf_token_name' => 'csrf_token',
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
    ],
    
    // Pagination settings
    'pagination' => [
        'per_page' => 25,
        'max_per_page' => 100,
    ],
    
    // File upload settings
    'uploads' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'csv'],
        'path' => 'storage/uploads/',
    ],
    
    // Email settings
    'email' => [
        'queue_batch_size' => 50,
        'max_retry_attempts' => 3,
        'default_delay' => 1, // seconds between emails
        'tracking_pixel' => true,
    ],
    
    // Cache settings
    'cache' => [
        'enabled' => true,
        'default_ttl' => 3600, // 1 hour
        'path' => 'storage/cache/',
    ],
    
    // Logging settings
    'logging' => [
        'enabled' => true,
        'level' => 'info', // debug, info, warning, error
        'path' => 'storage/logs/',
        'max_files' => 30,
    ],
];