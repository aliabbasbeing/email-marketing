<?php
/**
 * SMTP Configuration
 */

return [
    'default' => 'main',
    
    'connections' => [
        'main' => [
            'host' => $_ENV['SMTP_HOST'] ?? '',
            'port' => $_ENV['SMTP_PORT'] ?? 587,
            'username' => $_ENV['SMTP_USERNAME'] ?? '',
            'password' => $_ENV['SMTP_PASSWORD'] ?? '',
            'encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls', // tls, ssl, or null
            'from_email' => $_ENV['SMTP_FROM_EMAIL'] ?? '',
            'from_name' => $_ENV['SMTP_FROM_NAME'] ?? 'Email Marketing Tool',
            'timeout' => 30,
            'auth' => true,
        ],
        
        // Backup SMTP configuration (optional)
        'backup' => [
            'host' => $_ENV['SMTP_BACKUP_HOST'] ?? '',
            'port' => $_ENV['SMTP_BACKUP_PORT'] ?? 587,
            'username' => $_ENV['SMTP_BACKUP_USERNAME'] ?? '',
            'password' => $_ENV['SMTP_BACKUP_PASSWORD'] ?? '',
            'encryption' => $_ENV['SMTP_BACKUP_ENCRYPTION'] ?? 'tls',
            'from_email' => $_ENV['SMTP_BACKUP_FROM_EMAIL'] ?? '',
            'from_name' => $_ENV['SMTP_BACKUP_FROM_NAME'] ?? 'Email Marketing Tool',
            'timeout' => 30,
            'auth' => true,
        ],
    ],
    
    // Email queue settings
    'queue' => [
        'enabled' => true,
        'batch_size' => 50,
        'delay_between_batches' => 60, // seconds
        'max_retries' => 3,
        'retry_delay' => 300, // 5 minutes
    ],
    
    // Email tracking settings
    'tracking' => [
        'enabled' => true,
        'open_tracking' => true,
        'click_tracking' => true,
        'pixel_url' => '/track.php',
    ],
    
    // Rate limiting
    'rate_limit' => [
        'enabled' => true,
        'max_per_hour' => 1000,
        'max_per_day' => 5000,
    ],
    
    // Bounce handling
    'bounce' => [
        'enabled' => true,
        'max_bounces' => 3,
        'handler_url' => '/bounce_handler.php',
    ],
];