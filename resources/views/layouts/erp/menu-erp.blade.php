<!--CONTENEDOR ASIDE-->
<aside class="contenedor_aside" :class="{ 'estilo_abierto_contenedor_aside': estadoAsideAbierto }"
    data-seleccionado-nivel-1="{{ $seleccionadoNivel_1 }}" data-seleccionado-nivel-2="{{ $seleccionadoNivel_2 }}"
    data-seleccionado-nivel-3="{{ $seleccionadoNivel_3 }}" data-seleccionado-nivel-4="{{ $seleccionadoNivel_4 }}">

    <!--NAV ICONOS NIVEL 1-->
    <div class="contenedor_nav_iconos">
        <span x-on:click="toggleContenedorNavLinks" class="contenedor_menu_hamburguesa">
            <i class="fa-solid fa-bars"></i>
        </span>

        <ul>
            @foreach ($menuPrincipal as $n1)
                <li data-id="{{ $n1->id }}" @click="toogleNivel_1($event, {{ $n1->id }})"
                    :class="{'nav_icono_seleccionado': seleccionadoNivel_1 == Number($el.dataset.id)}">

                    <a href="{{ $n1->ruta ? route($n1->ruta) : ($n1->url ?? '#') }}">
                        <i class="{{ $n1->icono }}"></i>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <!--NAV LINKS-->
    <div class="contenedor_nav_links" :class="{ 'estilo_abierto_contenedor_nav_links': estadoNavAbierto }">

        <div class="contenedor_logo">
            {{--<a href="{{ route('home') }}">--}}
                <a href="">
                    <img src="{{ asset('assets/imagen/logo-aybar-corp-verde.png') }}" alt="">
                </a>
        </div>

        <!--SIDEBAR-->
        <nav class="sidebar_nav">
            <div class="sidebar_scroll">

                <ul class="nivel_1">
                    @foreach ($menuPrincipal as $n1)
                        @if ($n1->submenus->isNotEmpty())

                            <ul class="submenu1" :class="{ 'ocultar_nivel': seleccionadoNivel_1 !== {{ $n1->id }} }">

                                @foreach ($n1->submenus as $n2)
                                    <li>
                                        <span class="contenedor_a" data-id="{{ $n2->id }}"
                                            @click.stop="toogleNivel_2($event, {{ $n2->id }})"
                                            :class="{'sidebar_nav_seleccionado': seleccionadoNivel_2 === Number($el.dataset.id)}">

                                            <a href="{{ $n2->ruta ? route($n2->ruta) : ($n2->url ?? '#') }}">
                                                <i class="{{ $n2->icono }}"></i>
                                                {{ $n2->nombre }}
                                            </a>

                                            @if ($n2->submenus->isNotEmpty())
                                                <i class="fa-solid fa-angle-down"></i>
                                            @endif
                                        </span>

                                        @if ($n2->submenus->isNotEmpty())
                                            <!--NIVEL 3-->
                                            <ul class="nivel_2" :class="{ 'ocultar_nivel': seleccionadoNivel_2 !== {{ $n2->id }} }">

                                                @foreach ($n2->submenus as $n3)
                                                    <li>
                                                        <span class="contenedor_a" data-id="{{ $n3->id }}"
                                                            @click.stop="toogleNivel_3($event, {{ $n3->id }})"
                                                            :class="{'sidebar_item_seleccionado': seleccionadoNivel_3 === Number($el.dataset.id)}">

                                                            <a href="{{ $n3->ruta ? route($n3->ruta) : ($n3->url ?? '#') }}">

                                                                <span class="punto_item">
                                                                    <i class="fa-solid fa-circle"></i>
                                                                </span>
                                                                {{ $n3->nombre }}
                                                            </a>

                                                            @if ($n3->submenus->isNotEmpty())
                                                                <i class="fa-solid fa-angle-down"></i>
                                                            @endif
                                                        </span>

                                                        @if ($n3->submenus->isNotEmpty())
                                                            <!--NIVEL 4-->
                                                            <ul class="submenu3"
                                                                :class="{ 'ocultar_nivel': seleccionadoNivel_3 !== {{ $n3->id }} }">

                                                                @foreach ($n3->submenus as $n4)
                                                                    <li>
                                                                        <span class="contenedor_a" data-id="{{ $n4->id }}"
                                                                            @click.stop="toogleNivel_4($event, {{ $n4->id }})"
                                                                            :class="{'sidebar_item_seleccionado': seleccionadoNivel_4 === Number($el.dataset.id)}">

                                                                            <a href="{{ $n4->ruta ? route($n4->ruta) : ($n4->url ?? '#') }}">
                                                                                <span class="punto_item"><i class="fa-solid fa-circle"></i></span>
                                                                                {{ $n4->nombre }}
                                                                            </a>
                                                                        </span>
                                                                    </li>
                                                                @endforeach

                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach

                                            </ul>
                                        @endif

                                    </li>
                                @endforeach

                            </ul>

                        @endif
                    @endforeach
                </ul>

            </div>
        </nav>
    </div>
</aside>