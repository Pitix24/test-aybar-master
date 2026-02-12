<header class="header_layout_pagina" x-data="themeSwitcher()" x-init="initTheme()">
    <span class="layout_menu_hamburguesa_celular" @click="toggleContenedorAside">
        <i class="fa-solid fa-bars"></i>
    </span>

    <div class="header_menu" x-data="{ menuAbierto: null }" @keydown.escape.window="menuAbierto = null">
        <div>Fecha y Hora</div>
        <!-- Dropdown Tema -->
        <div class="header_dropdown_wrapper" x-data="{ 
            get open() { return menuAbierto === 'tema' }, 
            set open(val) { menuAbierto = val ? 'tema' : null } 
        }">
            <button type="button" class="header_dropdown_trigger" @click="open = !open" title="Cambiar tema">
                <i x-show="theme === 'light'" class="fa-solid fa-sun" x-cloak></i>
                <i x-show="theme === 'dark'" class="fa-solid fa-moon" x-cloak></i>
            </button>

            <x-theme-dropdown x-show="open" @click.outside="if(open) open = false" />
        </div>

        <!-- Dropdown Perfil -->
        <div class="header_dropdown_wrapper" x-data="{ 
            get open() { return menuAbierto === 'perfil' }, 
            set open(val) { menuAbierto = val ? 'perfil' : null } 
        }">
            <button type="button" class="header_dropdown_trigger" @click="open = !open">
                <img src="{{ asset('assets/imagen/default.jpg') }}" class="header_profile_avatar">

                <div class="header_profile_info">
                    <span>{{ auth()->user()->name }}</span>
                    <small>{{ auth()->user()->email }}</small>
                </div>

                <i class="fa-solid fa-sort-down header_arrow"></i>
            </button>

            <x-profile-dropdown x-show="open" @click.outside="if(open) open = false" />
        </div>
    </div>
</header>