<header class="header_layout_pagina" x-data="themeSwitcher()" x-init="initTheme()">
    <span class="layout_menu_hamburguesa_celular" x-on:click="toggleContenedorAside">
        <i class="fa-solid fa-bars"></i>
    </span>

    <x-profile-dropdown :name="auth()->user()->name" :email="auth()->user()->email" />
</header>