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
        'base_password' => env('SLIN_BASEPASSWORD'),
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
        'entregafest' => [
            'pre_invitacion' => env('N8N_WEBHOOK_ENTREGA_FEST_PRE_INVITACION'),
            'asistencia_invitacion' => env('N8N_WEBHOOK_ENTREGA_FEST_ASISTENCIA_INVITACION'),
            'asistencia_invitacion_masivo' => env('N8N_WEBHOOK_ENTREGA_FEST_ASISTENCIA_INVITACION_MASIVO'),
            'asistencia_confirmacion' => env('N8N_WEBHOOK_ENTREGA_FEST_ASISTENCIA_CONFIRMACION'),
            'instrucciones' => env('N8N_WEBHOOK_ENTREGA_FEST_INSTRUCCIONES'),
            'contrato_preliminar' => env('N8N_WEBHOOK_ENTREGA_FEST_CONTRATO_PRELIMINAR'),
            'cita_agendar' => env('N8N_WEBHOOK_ENTREGA_FEST_CITA_AGENDAR'),
            'cita_confirmacion' => env('N8N_WEBHOOK_ENTREGA_FEST_CITA_CONFIRMACION'),
            'cita_recordatorio' => env('N8N_WEBHOOK_ENTREGA_FEST_CITA_RECORDATORIO'),
        ],
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'phone_id' => env('WHATSAPP_PHONE_ID'),
        'business_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN', 'aybar_crm_secret_token'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
    ],
    
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'pubsub_topic' => env('GOOGLE_PUBSUB_TOPIC'),
        'inbox' => env('GOOGLE_GMAIL_INBOX'),
        'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),
    ],
];
