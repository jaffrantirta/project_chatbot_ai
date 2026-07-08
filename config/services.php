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

    'openai' => [
        'key'      => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.tokenrouter.com/v1'),
        'model'    => env('OPENAI_MODEL', 'openai/gpt-4o-mini'),
    ],

    // TokenRouter serves no embedding models, so embeddings use a separate
    // OpenAI-compatible provider (default: Google Gemini's compatibility endpoint).
    'embedding' => [
        'key'      => env('EMBEDDING_API_KEY', env('OPENAI_API_KEY')),
        'base_url' => env('EMBEDDING_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta/openai'),
        'model'    => env('OPENAI_EMBEDDING_MODEL', 'gemini-embedding-001'),
    ],

];
