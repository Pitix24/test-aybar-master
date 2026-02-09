<header class="header_layout_pagina" x-data="themeSwitcher()" x-init="initTheme()">
    <span class="layout_menu_hamburguesa_celular" @click="toggleContenedorAside">
        <i class="fa-solid fa-bars"></i>
    </span>

    <div class="g_header_actions">
        <div class="g_dropdown_wrapper" x-data="{ open: false }">
            <button type="button" class="g_dropdown_trigger" @click="open = !open" title="Cambiar tema">
                <i x-show="theme === 'light'" class="fa-solid fa-sun" x-cloak></i>
                <i x-show="theme === 'dark'" class="fa-solid fa-moon" x-cloak></i>
            </button>

            <x-theme-dropdown x-show="open" @click.outside="open = false" />
        </div>

        <div class="g_dropdown_wrapper" x-data="{ open: false }">
            <button type="button" class="g_dropdown_trigger" @click="open = !open">
                <img src="{{ asset('assets/imagen/default.jpg') }}" class="g_profile_avatar">

                <div class="g_profile_info">
                    <span>{{ auth()->user()->name }}</span>
                    <small>{{ auth()->user()->email }}</small>
                </div>

                <i class="fa-solid fa-sort-down g_arrow"></i>
            </button>

            <x-profile-dropdown x-show="open" @click.outside="open = false" />
        </div>
    </div>
</header>