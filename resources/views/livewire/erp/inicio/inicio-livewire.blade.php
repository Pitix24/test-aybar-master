<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="actualizarPerfil, actualizarPassword, photo" message="Procesando..." />

    <div class="g_panel g_dashboard_hero">
        <div class="g_dashboard_hero_content">
            <h1 class="g_dashboard_hero_title">{{ $saludo }}, <span>{{ $usuario->name }}</span>!</h1>
            <p class="g_dashboard_hero_text">
                {{ $mensajeBienvenida }}
            </p>
        </div>
        <div class="g_dashboard_hero_decor_1"></div>
        <div class="g_dashboard_hero_decor_2"></div>
    </div>

    <div class="g_fila">
        <div class="g_panel_dashboard_grid" style="">
            <div class="g_panel" title="Total tickets históricos">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Tickets Activo</h2>
                        <p class="g_negrita">{{ $metricas['tickets_asignados'] }}</p>
                    </div>
                    <i class="fa-solid fa-ticket" style="color: var(--color-primario);"></i>
                </div>
            </div>

            <div class="g_panel" title="Tickets con estado Cerrado">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_valor">
                        <h2>Citas Próximas</h2>
                        <p class="g_negrita">{{  $metricas['proximas_citas'] }}</p>
                    </div>
                    <i class="fa-solid fa-check-double" style="color: #10B981;"></i>
                </div>
            </div>

            <div class="g_panel" title="Tickets que no están cerrados">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_valor">
                        <h2>Roles Asignados</h2>
                        <p class="g_negrita">{{ $rolesConPermisos->count() }}</p>
                    </div>
                    <i class="fa-solid fa-spinner" style="color: #3B82F6;"></i>
                </div>
            </div>

            <div class="g_panel" title="Tickets sin cierre con más de 3 días de antigüedad">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_valor">
                        <h2>Áreas</h2>
                        <p class="g_negrita" style="color: #EF4444;">{{ $areasUsuario->count()}}</p>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation" style="color: #EF4444;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel" x-data="{ activeTab: 'resumen' }">
        <div class="g_tab_navegacion">
            <div class="g_tab_botones">
                <button @click="activeTab = 'resumen'" class="g_tab_boton"
                    :class="activeTab === 'resumen' ? 'g_tab_active' : 'g_tab_inactive'">
                    <i class="fa-solid fa-user-tie"></i> Mi Perfil
                </button>
                <button @click="activeTab = 'seguridad'" class="g_tab_boton"
                    :class="activeTab === 'seguridad' ? 'g_tab_active' : 'g_tab_inactive'">
                    <i class="fa-solid fa-shield-halved"></i> Seguridad
                </button>
                <button @click="activeTab = 'areas'" class="g_tab_boton"
                    :class="activeTab === 'areas' ? 'g_tab_active' : 'g_tab_inactive'">
                    <i class="fa-solid fa-building"></i> Áreas
                </button>
                <button @click="activeTab = 'permisos'" class="g_tab_boton"
                    :class="activeTab === 'permisos' ? 'g_tab_active' : 'g_tab_inactive'">
                    <i class="fa-solid fa-key"></i> Roles y Permisos
                </button>
            </div>
        </div>

        <div x-show="activeTab === 'resumen'" x-transition class="g_tab_content">
            <div class="g_fila">
                <div class="g_columna_4">
                    <div class="g_panel g_perfil_avatar_container">
                        <h4 class="g_panel_titulo">Foto de Perfil</h4>
                        <div class="g_perfil_avatar_wrapper">
                            <div class="g_perfil_avatar">
                                @if ($photo)
                                    <img src="{{ $photo->temporaryUrl() }}">
                                @elseif($usuario->profile_photo_path)
                                    <img src="{{ asset('storage/' . $usuario->profile_photo_path) }}">
                                @else
                                    <div class="g_perfil_avatar_placeholder">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                @endif
                            </div>
                            <label for="photo_upload" class="g_perfil_avatar_upload_label">
                                <i class="fa-solid fa-camera"></i>
                            </label>
                            <input type="file" id="photo_upload" wire:model="photo" style="display: none;">
                        </div>
                        <p class="g_perfil_avatar_info">Formatos: JPG, PNG. Máx 2MB.</p>
                    </div>
                </div>

                <div class="g_columna_8">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">Datos Generales</h4>
                        <div class="formulario">
                            <div class="g_fila">
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Nombre Completo</label>
                                    <input type="text" wire:model.live="name">
                                    @error('name') <p class="mensaje_error">{{ $message }}</p> @enderror
                                </div>
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Correo Electrónico</label>
                                    <input type="text" value="{{ $email }}" disabled style="background: #f8f8f8;">
                                </div>
                            </div>

                            <div class="g_fila">
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Usuario ID</label>
                                    <input type="text" value="#{{ str_pad($usuario->id, 5, '0', STR_PAD_LEFT) }}"
                                        disabled style="background: #f8f8f8;">
                                </div>
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Última Conexión</label>
                                    <input type="text" value="Recientemente" disabled style="background: #f8f8f8;">
                                </div>
                            </div>

                            <div class="formulario_botones">
                                <button wire:click="actualizarPerfil" class="g_boton guardar">
                                    <i class="fa-solid fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'seguridad'" x-transition class="g_tab_content" x-cloak>
            <div class="g_fila">
                <div class="g_columna_8">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">Cambiar Contraseña</h4>
                        <div class="formulario">
                            <div class="g_margin_bottom_10">
                                <label>Contraseña Actual</label>
                                <input type="password" wire:model="password_actual">
                                @error('password_actual') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                            <div class="g_fila">
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Nueva Contraseña</label>
                                    <input type="password" wire:model="password_nuevo">
                                    @error('password_nuevo') <p class="mensaje_error">{{ $message }}</p> @enderror
                                </div>
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Confirmar Nueva Contraseña</label>
                                    <input type="password" wire:model="password_confirmacion">
                                    @error('password_confirmacion') <p class="mensaje_error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="formulario_botones">
                                <button wire:click="actualizarPassword" class="g_boton guardar">
                                    <i class="fa-solid fa-key"></i> Actualizar Seguridad
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="g_columna_4">
                    <div class="g_panel" style="background: #fcfcfc;">
                        <h4 class="g_panel_titulo">Tips de Seguridad</h4>
                        <ul style="padding-left: 20px; color: #666; font-size: 0.9rem;">
                            <li style="margin-bottom: 10px;">Use al menos 8 caracteres.</li>
                            <li style="margin-bottom: 10px;">Combine letras, números y símbolos.</li>
                            <li style="margin-bottom: 10px;">No use contraseñas de otros sitios.</li>
                            <li style="margin-bottom: 10px;">Cambie su contraseña cada 90 días.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'areas'" x-transition class="g_tab_content" x-cloak>
            @if ($areasUsuario->count())
                <h4 class="g_panel_titulo">Áreas Asignadas</h4>
                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Área</th>
                                <th>Fecha asignación</th>
                                <th>Tipos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($areasUsuario as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div>
                                            <i class="{{ $item->icono ?? 'fa-solid fa-shapes' }}"
                                                style="color: {{ $item->color }};"></i>
                                            <span>{{ $item->nombre }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $item->pivot?->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td>
                                        @if ($item->tiposSolicitud->count())
                                            <div class="g_cell_tags">
                                                @foreach ($item->tiposSolicitud as $tipo)
                                                    <span class="g_badge">{{ $tipo->nombre }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div x-show="activeTab === 'permisos'" x-transition class="g_tab_content" x-cloak>
            @if (count($rolesConPermisos))
                <h4 class="g_panel_titulo">Roles y Permisos</h4>
                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Rol</th>
                                <th>Permisos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rolesConPermisos as $index => $rol)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="g_badge primary">{{ ucfirst($rol['nombre']) }}</span></td>
                                    <td>
                                        @if ($rol['permisos']->count())
                                            <div class="g_celda_tags">
                                                @foreach ($rol['permisos'] as $permiso)
                                                    <span class="g_badge">
                                                        <i class="fa-solid fa-check-circle"></i> {{ $permiso }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>