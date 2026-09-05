<?php

return [

    

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'whatsapp' => [
        'portal_url' => env('WHATSAPP_PORTAL_URL', 'http://localhost:4200'),
    ],

    'certificados' => [
        'portal_url' => env('CERTIFICADOS_PORTAL_URL', env('WHATSAPP_PORTAL_URL', 'http://localhost:4201')),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'api_encrypt' => [
        'key' => env('API_ENCRYPT_KEY'),
    ],

    'portal' => [
        'api_key' => env('PORTAL_API_KEY'),
    ],

    'moodle' => [
        'url' => env('MOODLE_URL', 'http://moodle.local'),
        'token' => env('MOODLE_TOKEN'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
    ],

];
