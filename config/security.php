<?php
/**
 * Security Configuration
 */

return [
    // CSRF Protection
    'csrf' => [
        'enabled' => true,
        'token_name' => 'csrf_token',
        'token_length' => 32,
        'expire_time' => 3600, // 1 hour
    ],
    
    // XSS Protection
    'xss' => [
        'enabled' => true,
        'filter_input' => true,
        'escape_output' => true,
    ],
    
    // SQL Injection Protection
    'sql_injection' => [
        'use_prepared_statements' => true,
        'validate_input' => true,
    ],
    
    // Session Security
    'session' => [
        'secure_cookies' => false, // Set to true in production with HTTPS
        'httponly_cookies' => true,
        'samesite' => 'Lax',
        'regenerate_id' => true,
        'expire_on_close' => false,
        'lifetime' => 7200, // 2 hours
    ],
    
    // Password Security
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => false,
        'hash_algorithm' => PASSWORD_BCRYPT,
        'hash_cost' => 12,
    ],
    
    // Rate Limiting
    'rate_limit' => [
        'enabled' => true,
        'login_attempts' => [
            'max_attempts' => 5,
            'lockout_duration' => 900, // 15 minutes
            'reset_time' => 3600, // 1 hour
        ],
        'api_requests' => [
            'max_per_minute' => 60,
            'max_per_hour' => 1000,
        ],
    ],
    
    // Input Validation
    'validation' => [
        'strict_mode' => true,
        'trim_input' => true,
        'max_input_length' => 10000,
        'allowed_html_tags' => '<b><i><u><em><strong><p><br><a><ul><ol><li>',
    ],
    
    // File Upload Security
    'file_upload' => [
        'enabled' => true,
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'csv', 'txt'],
        'scan_for_malware' => false, // Enable if you have ClamAV or similar
        'quarantine_path' => 'storage/quarantine/',
    ],
    
    // Content Security Policy
    'csp' => [
        'enabled' => false, // Enable when implementing CSP headers
        'directives' => [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline' https://cdn.tailwindcss.com https://code.jquery.com",
            'style-src' => "'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com",
            'img-src' => "'self' data: https:",
            'font-src' => "'self' https://cdnjs.cloudflare.com",
        ],
    ],
    
    // Headers Security
    'headers' => [
        'x_frame_options' => 'DENY',
        'x_content_type_options' => 'nosniff',
        'x_xss_protection' => '1; mode=block',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'permissions_policy' => 'geolocation=(), microphone=(), camera=()',
    ],
];