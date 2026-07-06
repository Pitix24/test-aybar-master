<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('descripcion')">

    <title>@yield('titulo', config('app.name'))</title>

    <!-- Estilos generales -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- SCRIPTS -->
    @vite(['resources/css/web/web.css', 'resources/js/web/web.js'])
    @vite('resources/css/erp/entregafest/invitacion.css')

    <!-- Librería Swiper -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- STYLES -->
    @livewireStyles
</head>

<body class="layout_web">

    @include('layouts.web.menu-web')

    <main class="layout_web_contenido">
        @yield('contenido')
        @if (isset($slot))
            {{ $slot }}
        @endif
    </main>

    @livewireScripts
</body>

</html>
