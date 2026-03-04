<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'WhatsApp CRM - Aybar' }}</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <!-- Assets (el whatsapp.css ya viene como @import en erp.css) -->
    @vite([
        'resources/css/erp/erp.css',
        'resources/js/erp/erp.js'
    ])

    <style>
        /* Reset para la vista WhatsApp */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            background: #00a884;
        }

        /* Franja verde superior + fondo gris inferior (estilo WA Web) */
        .wsp_layout_wrapper {
            height: 100vh;
            width: 100vw;
            background: linear-gradient(to bottom,
                    #00a884 0%,
                    #00a884 127px,
                    #dadbd3 127px,
                    #dadbd3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Contenedor principal tipo app WA */
        .wsp_app_shell {
            width: 100%;
            height: 100%;
            max-width: 1600px;
            max-height: 97vh;
            background: #fff;
            box-shadow: 0 6px 24px rgba(11, 20, 26, .12);
            display: flex;
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        @media (min-width: 1441px) {
            .wsp_app_shell {
                width: 95%;
                border-radius: 4px;
                margin-top: 16px;
            }
        }
    </style>

    @livewireStyles
</head>

<body>

    <div class="wsp_layout_wrapper">
        <div class="wsp_app_shell">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

</body>

</html>