<?php

return [
    'domain'      => env('SITE_HOSTNAME', 'localhost'),
    'pwa' => [
        'url'                     => env('CS261_PWA_URL', 'localhost:3030'),
        'email_verification_path' => env('CS261_PWA_EMAIL_VERIFICATION_PATH', '/verify-email'),
    ],
    'analytics' => [
        'mock' => (bool) env('CS261_ANALYTICS_MOCK', true),
        'endpoint' => env('CS261_ANALYTICS_ENDPOINT', ''),
    ],
    'test' => [
        'recaptcha_override' => env('CS261_TEST_RECAPTCHA_OVERRIDE', '')
    ]
];
