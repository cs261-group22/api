<?php

return [
    'domain'      => env('SITE_HOSTNAME', 'localhost'),
    'pwa' => [
        'url'                     => env('CS261_PWA_URL', 'localhost:3030'),
        'email_verification_path' => env('CS261_PWA_EMAIL_VERIFICATION_PATH', '/verify-email'),
        'email_password_reset'    => env('CS261_PWA_PASSWORD_RESET_PATH', '/password-reset'),
    ],
    'analytics' => [
        'endpoint' => env('CS261_ANALYTICS_ENDPOINT', ''),
        'api_key' => env('CS261_ANALYTICS_API_KEY', ''),
        'api_secret' => env('CS261_ANALYTICS_API_SECRET', ''),
    ],
];
