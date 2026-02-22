<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'WhatsApp CRM - Aybar' }}</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    
    <!-- Assets -->
    @vite(['resources/css/erp/erp.css', 'resources/js/erp/erp.js'])

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            font-family: 'Inter', sans-serif;
            background-color: #dadbd3; /* Color de fondo estilo WA Web */
            overflow: hidden;
        }

        .whatsapp-layout-wrapper {
            height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Degradado superior característico de WA Web */
            background: linear-gradient(to bottom, #00a884 0%, #00a884 127px, #dadbd3 127px, #dadbd3 100%);
        }

        .whatsapp-main-container {
            width: 100%;
            height: 100%;
            max-width: 1600px;
            max-height: 95vh;
            background: #fff;
            box-shadow: 0 6px 18px rgba(11,20,26,.05);
            display: flex;
            position: relative;
            z-index: 10;
        }

        /* Responsive para pantallas grandes */
        @media (min-width: 1441px) {
            .whatsapp-main-container {
                width: 95%;
                border-radius: 3px;
                margin-top: 20px;
            }
        }
    </style>

    @livewireStyles
</head>

<body>

    <div class="whatsapp-layout-wrapper">
        <div class="whatsapp-main-container">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>