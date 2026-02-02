<header class="header_layout_pagina" x-data="themeSwitcher()" x-init="initTheme()">
    <span class="layout_menu_hamburguesa_celular" x-on:click="toggleContenedorAside">
        <i class="fa-solid fa-bars"></i>
    </span>

    <button type="button" class="theme-toggle" x-on:click="toggleTheme()" title="Cambiar tema">
        <i x-show="theme === 'light'" class="fa-solid fa-moon"></i>
        <i x-show="theme === 'dark'" class="fa-solid fa-sun"></i>
    </button>
</header>