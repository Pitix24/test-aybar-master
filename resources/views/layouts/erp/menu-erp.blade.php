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
            <li data-id="{{ $n1['id'] }}" :class="{
                    'nav_icono_seleccionado': seleccionadoNivel_1 == Number($el.dataset.id),
                    'no-hover': seleccionadoNivel_1 == Number($el.dataset.id)
                }">

                <a @click="toogleNivel_1($event, {{ $n1['id'] }})" @if (!count($n1['submenus']))
                    href="{{ $n1['ruta'] !== '#' ? route($n1['ruta']) : '#' }}" @endif>
                    <i class="{{ $n1['icono'] }}"></i>
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
                    @if (count($n1['submenus']) > 0)

                    <ul class="submenu1" :class="{ 'ocultar_nivel': seleccionadoNivel_1 !== {{ $n1['id'] }} }">

                        @foreach ($n1['submenus'] as $n2)
                        <li>
                            <span class="contenedor_a" data-id="{{ $n2['id'] }}" :class="{
                                    'sidebar_nav_seleccionado': seleccionadoNivel_2 === Number($el.dataset.id),
                                    'no-hover': seleccionadoNivel_2 == Number($el.dataset.id)
                                }">

                                <a @click.stop="toogleNivel_2($event, {{ $n2['id'] }})" @if (!count($n2['submenus']))
                                    href="{{ $n2['ruta'] !== '#' ? route($n2['ruta']) : '#' }}" @endif>
                                    <i class="{{ $n2['icono'] }}"></i>
                                    {{ $n2['nombre'] }}
                                </a>

                                @if (count($n2['submenus']) > 0)
                                <i class="fa-solid fa-sort-down"></i>
                                @endif
                            </span>

                            @if (count($n2['submenus']) > 0)
                            <!--NIVEL 3-->
                            <ul class="nivel_2" :class="{ 'ocultar_nivel': seleccionadoNivel_2 !== {{ $n2['id'] }} }">

                                @foreach ($n2['submenus'] as $n3)
                                <li>
                                    <span class="contenedor_a" data-id="{{ $n3['id'] }}"
                                        :class="{'sidebar_item_seleccionado': seleccionadoNivel_3 === Number($el.dataset.id)}">

                                        <a @click.stop="toogleNivel_3($event, {{ $n3['id'] }})"
                                            @if(!count($n3['submenus']))
                                            href="{{ $n3['ruta'] !== '#' ? route($n3['ruta']) : '#' }}" @endif>

                                            <span class="punto_item">
                                                <i class="fa-solid fa-circle"></i>
                                            </span>
                                            {{ $n3['nombre'] }}
                                        </a>

                                        @if (count($n3['submenus']) > 0)
                                        <i class="fa-solid fa-sort-down"></i>
                                        @endif
                                    </span>

                                    @if (count($n3['submenus']) > 0)
                                    <!--NIVEL 4-->
                                    <ul class="submenu3"
                                        :class="{ 'ocultar_nivel': seleccionadoNivel_3 !== {{ $n3['id'] }} }">

                                        @foreach ($n3['submenus'] as $n4)
                                        <li>
                                            <span class="contenedor_a" data-id="{{ $n4['id'] }}" :class="{
                                                    'sidebar_item_seleccionado': seleccionadoNivel_4 === Number($el.dataset.id)
                                                }">

                                                <a @click.stop="toogleNivel_4($event, {{ $n4['id'] }})"
                                                    href="{{ count($n4['submenus']) ? 'javascript:void(0)' : route($n4['ruta']) }}">
                                                    <span class="punto_item"><i class="fa-solid fa-circle"></i></span>
                                                    {{ $n4['nombre'] }}
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