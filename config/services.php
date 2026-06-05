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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'valor' => [
        'app_id' => env('VALOR_APP_ID'),
        'app_key' => env('VALOR_APP_KEY'),
        'epi' => env('VALOR_EPI'),
        'client_token' => env('VALOR_CLIENT_TOKEN'),
        'test_mode' => env('VALOR_TEST_MODE', true),
    ],

    'task_health_webhook' => [
        'token' => env('TASK_HEALTH_WEBHOOK_TOKEN'),
    ],

    'mdo' => [
        'api_key' => env('MDO_API_KEY', ''),
    ],

    'elevenlabs' => [
        'base_url'               => env('ELEVENLABS_BASE_URL', 'https://api.elevenlabs.io/v1'),
        'api_key'                => env('ELEVENLABS_API_KEY', ''),
        'agent_id'               => env('ELEVENLABS_AGENT_ID', ''),
        'agent_phone_number_id'  => env('ELEVENLABS_AGENT_PHONE_NUMBER_ID', ''),
        'reminder_agent_id'      => env('ELEVENLABS_REMINDER_AGENT_ID', ''),
        'outbound_call_url'      => env('ELEVENLABS_OUTBOUND_CALL_URL', 'https://api.elevenlabs.io/v1/convai/twilio/outbound-call'),
        'exclude_location_ids'   => [57, 69]
    ],

];
