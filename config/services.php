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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'cat' => [
        'client_id' => env(
            'CAT_CLIENT_ID',
            '23f1628f-fa51-4612-ad8f-48931ed4b70f'
        ),
        'token_url' => env(
            'CAT_TOKEN_URL',
            'https://signin.cat.com/4f0f19d0-f44c-4a03-b8cb-ab327bd2b12b/b2c_1a_p2_v1_signin_prod/oauth2/v2.0/token'
        ),
        'scope' => env(
            'CAT_SCOPE'
        ),
    ],

];
