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
        'key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'project' => env('OPENAI_PROJECT'),
    ],

    'slin' => [
        'url' => env('SLIN_URL'),
        'user' => env('SLIN_USER'),
        'password' => env('SLIN_PASSWORD'),
    ],

    'canvia' => [
        'url' => env('CANVIA_SOAP_URL'),
        'user' => env('CANVIA_SOAP_USER'),
        'password' => env('CANVIA_SOAP_PASSWORD'),
    ],

    'aybar_slin' => [
        'url' => env('AYBAR_SLIN_URL', 'https://aybarcorp.com/slin'),
    ],

    'n8n' => [
        'webhook_email_invitaciones' => env('N8N_WEBHOOK_EMAIL_INVITACIONES'),
        'webhook_whatsapp_invitaciones' => env('N8N_WEBHOOK_WHATSAPP_INVITACIONES'),
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'phone_id' => env('WHATSAPP_PHONE_ID'),
        'business_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN', 'aybar_crm_secret_token'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
    ],

];
