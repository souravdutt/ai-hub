<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'workers' => [
        'ai' => [
            'account_id' => env('WORKERS_ACCOUNT_ID'),
            'api_token' => env('WORKERS_API_TOKEN'),
            'models' => [
                'nlp' => env('WORKERS_MODEL_ID_NLP', '@cf/meta/llama-3.1-8b-instruct'),
                'tti' => env('WORKERS_MODEL_ID_TTI', '@cf/lykon/dreamshaper-8-lcm'),
            ],
        ]
    ]

];
