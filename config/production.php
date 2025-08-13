<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Production Settings
    |--------------------------------------------------------------------------
    */
    
    'upload' => [
        'max_size' => env('UPLOAD_MAX_SIZE', 10240), // KB
        'allowed_image_types' => explode(',', env('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,gif,webp')),
        'image_quality' => env('IMAGE_QUALITY', 85),
        'resize_images' => env('RESIZE_IMAGES', true),
        'max_width' => env('MAX_IMAGE_WIDTH', 1920),
        'max_height' => env('MAX_IMAGE_HEIGHT', 1080),
    ],

    'security' => [
        'force_https' => env('FORCE_HTTPS', false),
        'security_headers' => env('SECURE_HEADERS', false),
        'content_security_policy' => env('CSP_ENABLED', false),
        'rate_limiting' => [
            'api' => env('RATE_LIMIT_API', 60),
            'web' => env('RATE_LIMIT_WEB', 300),
            'contact_form' => env('RATE_LIMIT_CONTACT', 5),
        ],
    ],

    'performance' => [
        'cache_views' => env('CACHE_VIEWS', true),
        'cache_config' => env('CACHE_CONFIG', true),
        'cache_routes' => env('CACHE_ROUTES', true),
        'optimize_images' => env('OPTIMIZE_IMAGES', true),
        'enable_compression' => env('ENABLE_COMPRESSION', true),
    ],

    'monitoring' => [
        'error_reporting' => env('ERROR_REPORTING', true),
        'log_queries' => env('LOG_QUERIES', false),
        'log_requests' => env('LOG_REQUESTS', false),
        'sentry_enabled' => env('SENTRY_ENABLED', false),
    ],

    'backup' => [
        'enabled' => env('BACKUP_ENABLED', false),
        'disk' => env('BACKUP_DISK', 'local'),
        'schedule' => env('BACKUP_SCHEDULE', 'daily'),
        'keep_backups' => env('BACKUP_KEEP', 7),
    ],
];
