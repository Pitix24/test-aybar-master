<div x-data="{ menuAbierto: false, submenu: null, scrollY: 0, ocultarBanner: false }" x-init="window.addEventListener('scroll', () => {
    ocultarBanner = window.scrollY > scrollY && window.scrollY > 30;
    scrollY = window.scrollY;
})" :class="{ 'web_header_contenedor': ocultarBanner }"
    x-effect="document.body.classList.toggle('no-scroll', menuAbierto)">
    <!-- Header -->
    <header class="web_header">
        <div class="web_header_cuerpo">

            <a href="https://aybarcorp.com/">
                <img class="logo" src="{{ asset('assets/imagen/logo-aybar-corp-blanco.png') }}" alt="Logo">
            </a>

            <!-- Botón menú móvil -->
            <button class="web_menu_toggle" @click="menuAbierto = true" aria-label="Abrir menú">
                <i class="fa-solid fa-bars"></i>
            </button>

            <!-- Menú lateral -->
            <nav class="web_nav_menu" :class="{ 'active': menuAbierto }">

                <div class="cabecera_sidebar">
                    <button class="web_menu_toggle" @click="menuAbierto = false" aria-label="Cerrar menú">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <a href="https://aybarcorp.com/">
                        <img class="logo" src="{{ asset('assets/imagen/logo-aybar-corp-blanco.png') }}" alt="Logo">
                    </a>
                </div>

                <ul class="menu_principal">
                    @guest
                    <li class="menu_item">
                        <a href="/ingresar" class="boton_personalizado boton_personalizado_amarillo_v2"><i
                                class="fa-solid fa-user-circle"></i>MI PORTAL</a>
                    </li>
                    @else
                    @if (auth()->user()->rol === 'cliente')
                    <li class="menu_item menu_cliente">
                        <a href="{{ route('cliente.home') }}" class="boton_personalizado boton_personalizado_blanco_v2">
                            <i class="fa-solid fa-address-card"></i>
                            MI PERFIL
                        </a>
                    </li>

                    <li class="menu_item menu_cliente">
                        <a href="{{ route('cliente.lote') }}" class="boton_personalizado boton_personalizado_blanco_v2">
                            <i class="fa-solid fa-border-all"></i>
                            MIS PROYECTOS
                        </a>
                    </li>

                    <li class="menu_item menu_cliente">
                        <a href="{{ route('cliente.tutorial') }}"
                            class="boton_personalizado boton_personalizado_amarillo_v2">
                            <i class="fa-solid fa-circle-play"></i>
                            TUTORIALES
                        </a>
                    </li>

                    <li class="menu_item menu_cliente">
                        <form method="POST" action="{{ route('logout.cliente') }}">
                            @csrf
                            <button type="submit" class="boton_personalizado boton_personalizado_negro">
                                <span><i class="fa-solid fa-power-off"></i> CERRAR</span>
                            </button>
                        </form>
                    </li>
                    @elseif (auth()->user()->rol === 'admin')
                    <li class="menu_item">
                        <a href="{{ route('admin.home') }}"
                            class="boton_personalizado boton_personalizado_amarillo_v2">BACKOFFICE</a>
                    </li>
                    @endif
                    @endguest
                </ul>
            </nav>

            <div class="web_nav_overlay" :class="{ 'active': menuAbierto }" @click="menuAbierto = false">
            </div>

        </div>
    </header>
</div>