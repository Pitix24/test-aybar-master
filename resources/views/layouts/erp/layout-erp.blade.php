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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- SCRIPTS -->
    @vite(['resources/css/erp/erp.css', 'resources/js/erp/erp.js'])

    <!-- Librería Swiper -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- STYLES -->
    @livewireStyles
</head>

<body x-data="xDataLayout()" x-init="initLayout()" x-cloak class="contenedor_layout_general">

    <!--MENU PRINCIPAL-->
    @include('layouts.erp.menu-erp')

    <!--CONTENEDOR LAYOUT PAGINA-->
    <div class="contenedor_layout_pagina" :class="{ 'estilo_contenedor_layout_pagina': estadoNavAbierto }">
        <!--HEADER LAYOUT PAGINA-->
        @livewire('erp.menu.componente-header-livewire')

        <!--CONTENIDO LAYOUT PAGINA-->
        <div class="contenido_layout_pagina">
            <div class="g_centrar_pagina" @hasSection('anchoPantalla') style="max-width: @yield('anchoPantalla')"
            @endif">
                <main class="g_pading_pagina">
                    @yield('content')
                    @if (isset($slot))
                        {{ $slot }}
                    @endif
                </main>
            </div>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>