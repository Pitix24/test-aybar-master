<header class="header_layout_pagina">
    <span class="layout_menu_hamburguesa_celular" x-on:click="toggleContenedorAside">
        <i class="fa-solid fa-bars"></i>
    </span>

    <div class="contenedor_profile" x-data="{ open: false }" @click.outside="open = false">

        <div class="contenedor_texto">
            <p>{{ auth()?->user()?->name }}</p>
            <span>{{ auth()?->user()?->email }}</span>
        </div>

        <img src="{{ asset('assets/imagen/default.jpg') }}" alt="">

        <i class="fa-solid fa-sort-down cursor-pointer" @click="open = !open"></i>

        <div class="dropdown_profile" x-show="open" x-transition x-cloak>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown_item">
                    <i class="fa-solid fa-power-off"></i>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</header>