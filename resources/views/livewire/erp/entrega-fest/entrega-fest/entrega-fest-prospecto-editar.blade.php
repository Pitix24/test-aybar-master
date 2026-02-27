<div class="g_gap_pagina" x-data="{ activeTab: 'prospecto' }">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Evaluación de Prospecto</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.invitados', $evento->id) }}" class="g_boton cancelar">
                Invitados <i class="fa-solid fa-users"></i></a>

            <a href="{{ route('erp.entrega-fest.vista.asistencia', $evento->id) }}" class="g_boton info">
                Asistencia <i class="fa-solid fa-user-check"></i></a>

            <a href="{{ route('erp.entrega-fest.vista.prospectos', $evento->id) }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel g_gap_pagina">
                <div class="g_perfil_avatar_container">
                    <div class="g_perfil_avatar_wrapper">
                        <div class="g_perfil_avatar">
                            <div class="g_perfil_avatar_placeholder">
                                {{ substr($prospecto->nombres, 0, 1) }}
                            </div>
                        </div>
                    </div>
                    <div class="g_perfil_avatar_info">
                        <h3 class="g_negrita">{{ $prospecto->nombres }}</h3>
                        <p>{{ $prospecto->dni }}</p>
                    </div>
                </div>

                <div class="g_perfil_politicas">
                    <div class="informacion_resumen_grid">
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Evento</span>
                            <span class="informacion_resumen_valor">{{ $evento->nombre }}</span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Proyecto</span>
                            <span class="informacion_resumen_valor">{{ $prospecto->proyecto?->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Fecha Registro</span>
                            <span class="informacion_resumen_valor">{{ $prospecto->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Registrado por</span>
                            <span class="informacion_resumen_valor">{{ $prospecto->user?->name ?? 'Sistema' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="informacion_beneficio_item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>{{ $prospecto->email }}</span>
                    </div>
                    <div class="informacion_beneficio_item" style="margin-top: 8px;">
                        <i class="fa-solid fa-phone"></i>
                        <span>{{ $prospecto->celular }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="g_columna_8">
            <div class="g_panel">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button @click="activeTab = 'prospecto'" class="g_tab_boton"
                            :class="activeTab === 'prospecto' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-address-card"></i> Datos Básicos
                        </button>
                        <button @click="activeTab = 'backoffice'" class="g_tab_boton"
                            :class="activeTab === 'backoffice' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-briefcase"></i> BackOffice
                        </button>
                        <button @click="activeTab = 'legal'" class="g_tab_boton"
                            :class="activeTab === 'legal' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-file-contract"></i> Información Legal
                        </button>
                        <button @click="activeTab = 'copropietarios'" class="g_tab_boton"
                            :class="activeTab === 'copropietarios' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-people-group"></i> Copropietarios
                            @if(count($copropietarios) > 0)
                                <span class="g_badge info" style="margin-left:6px; font-size:0.7rem; padding:2px 6px;">
                                    {{ count($copropietarios) }}
                                </span>
                            @endif
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'prospecto'" x-transition class="g_tab_content">
                    <form wire:submit.prevent="updateProspecto" class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Nombres Completos <span class="obligatorio">*</span></label>
                                <input type="text" wire:model="nombres" class="@error('nombres') input-error @enderror">
                                @error('nombres') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>DNI / Documento <span class="obligatorio">*</span></label>
                                <input type="text" wire:model="dni" class="@error('dni') input-error @enderror"
                                    readonly>
                                @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Correo Electrónico <span class="obligatorio">*</span></label>
                                <input type="email" wire:model="email" class="@error('email') input-error @enderror">
                                @error('email') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Celular <span class="obligatorio">*</span></label>
                                <input type="text" wire:model="celular" class="@error('celular') input-error @enderror">
                                @error('celular') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Proyecto <span class="obligatorio">*</span></label>
                                <select wire:model="proyecto_id" class="@error('proyecto_id') select-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach ($proyectos as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Manzana</label>
                                <input type="text" wire:model="manzana">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Lote</label>
                                <input type="text" wire:model="lote">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Estado Prospecto <span class="obligatorio">*</span></label>
                                <select wire:model="estado">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="observado">Observado</option>
                                    <option value="aprobado">Aprobado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_12">
                                <label>Observaciones del Registro</label>
                                <textarea wire:model="observacion" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="g_tab_form_buttons centrar">
                            <button type="submit" class="g_boton guardar">
                                <i class="fa-solid fa-save"></i> Actualizar Datos Personales
                            </button>
                        </div>
                    </form>
                </div>

                <div x-show="activeTab === 'backoffice'" x-transition class="g_tab_content">
                    <form wire:submit.prevent="updateBackoffice" class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Grupo Asignado <span class="obligatorio">*</span></label>
                                <select wire:model="grupo">
                                    <option value="A">Grupo A</option>
                                    <option value="B">Grupo B</option>
                                    <option value="C">Grupo C</option>
                                    <option value="D">Grupo D</option>
                                </select>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Gestor de Cuenta</label>
                                <select wire:model="gestor_backoffice_id">
                                    <option value="">Sin asignar</option>
                                    @foreach ($usuarios as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Culminación EECC</label>
                                <input type="datetime-local" wire:model="fecha_culminacion_eecc">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Enlace Carpeta EECC</label>
                                <input type="text" wire:model="link_carpeta_eecc">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Enlace EECC Firmado</label>
                                <input type="text" wire:model="link_eecc_firmado">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Estado Administrativo <span class="obligatorio">*</span></label>
                                <select wire:model="estado_backoffice">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="observado">Observado</option>
                                    <option value="aprobado">Aprobado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Validador</label>
                                <select wire:model="validador_backoffice_id">
                                    <option value="">Sin asignar</option>
                                    @foreach ($usuarios as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Fecha Validación</label>
                                <input type="datetime-local" wire:model="fecha_validacion_eecc">
                            </div>
                        </div>

                        <div class="g_tab_form_buttons centrar">
                            <button type="submit" class="g_boton guardar">
                                <i class="fa-solid fa-save"></i> Guardar Avance BackOffice
                            </button>
                        </div>
                    </form>
                </div>

                <div x-show="activeTab === 'legal'" x-transition class="g_tab_content">
                    <form wire:submit.prevent="updateLegal" class="formulario">

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Contrato Preliminar <span class="obligatorio">*</span></label>
                                <select wire:model="estado_contrato_preeliminar_emitido">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="observado">Observado</option>
                                    <option value="aprobado">Generado / Emitido</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Generación de Contrato</label>
                                <input type="datetime-local" wire:model="fecha_generacion_contrato">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Firma de Contrato <span class="obligatorio">*</span></label>
                                <select wire:model="estado_firma_contrato_firmado">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="observado">Observado</option>
                                    <option value="aprobado">Firmado Correctamente</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Fecha de Firma</label>
                                <input type="datetime-local" wire:model="fecha_firma">
                            </div>
                        </div>

                        <div class="g_tab_form_buttons centrar">
                            <button type="submit" class="g_boton guardar">
                                <i class="fa-solid fa-save"></i> Guardar Seguimiento Legal
                            </button>
                        </div>
                    </form>
                </div>
                <div x-show="activeTab === 'copropietarios'" x-transition class="g_tab_content">

                    {{-- Cabecera del tab --}}
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <div>
                            <strong style="font-size:0.95rem;">
                                <i class="fa-solid fa-people-group"></i>
                                Copropietarios del lote
                                <span style="font-size:0.8rem; color:#999;">
                                    Mz: {{ $prospecto->manzana ?? '—' }} / Lt: {{ $prospecto->lote ?? '—' }}
                                </span>
                            </strong>
                        </div>
                        @if($cop_modo !== 'crear')
                            <button wire:click="abrirFormCrear" class="g_boton primary" style="font-size:0.85rem;">
                                <i class="fa-solid fa-plus"></i> Agregar Copropietario
                            </button>
                        @endif
                    </div>

                    {{-- Formulario CREAR --}}
                    @if($cop_modo === 'crear')
                        <div class="g_panel"
                            style="background:#f0f7ff; border:1px solid #c3d9f7; margin-bottom:16px; padding:16px; border-radius:10px;">
                            <h5 style="margin-bottom:12px; color:var(--color-primary);"><i
                                    class="fa-solid fa-user-plus"></i> Nuevo Copropietario</h5>
                            <div class="formulario">
                                <div class="g_fila">
                                    <div class="g_margin_bottom_10 g_columna_3">
                                        <label>DNI <span class="obligatorio">*</span></label>
                                        <input type="text" wire:model.blur="cop_dni"
                                            class="@error('cop_dni') input-error @enderror" placeholder="12345678">
                                        @error('cop_dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="g_margin_bottom_10 g_columna_9">
                                        <label>Nombres Completos <span class="obligatorio">*</span></label>
                                        <input type="text" wire:model.blur="cop_nombres"
                                            class="@error('cop_nombres') input-error @enderror"
                                            placeholder="Ej: María Torres Lara">
                                        @error('cop_nombres') <p class="mensaje_error">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="g_fila">
                                    <div class="g_margin_bottom_10 g_columna_6">
                                        <label>Correo Electrónico</label>
                                        <input type="email" wire:model.blur="cop_email"
                                            class="@error('cop_email') input-error @enderror"
                                            placeholder="correo@ejemplo.com">
                                        @error('cop_email') <p class="mensaje_error">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="g_margin_bottom_10 g_columna_6">
                                        <label>Celular</label>
                                        <input type="text" wire:model.blur="cop_celular"
                                            class="@error('cop_celular') input-error @enderror" placeholder="987654321">
                                        @error('cop_celular') <p class="mensaje_error">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div style="display:flex; gap:10px;">
                                    <button wire:click="storeCopropietario" class="g_boton guardar"
                                        style="font-size:0.85rem;">
                                        <i class="fa-solid fa-save"></i> Guardar
                                    </button>
                                    <button wire:click="cancelarCopropietario" class="g_boton danger"
                                        style="font-size:0.85rem;">
                                        <i class="fa-solid fa-xmark"></i> Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Tabla de copropietarios --}}
                    <div class="g_contenedor_tabla">
                        <table class="g_tabla">
                            <thead>
                                <tr>
                                    <th>DNI</th>
                                    <th>Nombres</th>
                                    <th>Correo</th>
                                    <th>Celular</th>
                                    <th class="g_celda_centro">Invitación</th>
                                    <th class="g_celda_centro">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($copropietarios as $cop)
                                    @if($cop_modo === 'editar' && $cop_editando_id == $cop['id'])
                                        {{-- Fila en modo edición --}}
                                        <tr wire:key="cop-edit-{{ $cop['id'] }}" style="background:#fffbea;">
                                            <td style="padding:6px 8px;">
                                                <div class="formulario">
                                                    <input type="text" wire:model.blur="cop_dni" style="min-width:90px;"
                                                        class="@error('cop_dni') input-error @enderror" placeholder="DNI">
                                                    @error('cop_dni') <p class="mensaje_error" style="font-size:0.7rem;">
                                                    {{ $message }}</p> @enderror
                                                </div>
                                            </td>
                                            <td style="padding:6px 8px;">
                                                <div class="formulario">
                                                    <input type="text" wire:model.blur="cop_nombres" style="min-width:160px;"
                                                        class="@error('cop_nombres') input-error @enderror"
                                                        placeholder="Nombres">
                                                    @error('cop_nombres') <p class="mensaje_error" style="font-size:0.7rem;">
                                                    {{ $message }}</p> @enderror
                                                </div>
                                            </td>
                                            <td style="padding:6px 8px;">
                                                <div class="formulario">
                                                    <input type="email" wire:model.blur="cop_email" style="min-width:160px;"
                                                        class="@error('cop_email') input-error @enderror"
                                                        placeholder="correo@ejemplo.com">
                                                    @error('cop_email') <p class="mensaje_error" style="font-size:0.7rem;">
                                                    {{ $message }}</p> @enderror
                                                </div>
                                            </td>
                                            <td style="padding:6px 8px;">
                                                <div class="formulario">
                                                    <input type="text" wire:model.blur="cop_celular" style="min-width:110px;"
                                                        class="@error('cop_celular') input-error @enderror"
                                                        placeholder="987654321">
                                                    @error('cop_celular') <p class="mensaje_error" style="font-size:0.7rem;">
                                                    {{ $message }}</p> @enderror
                                                </div>
                                            </td>
                                            <td></td>
                                            <td class="g_celda_acciones g_celda_centro" style="white-space:nowrap;">
                                                <button wire:click="updateCopropietario" class="g_accion guardar"
                                                    title="Guardar cambios">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                                <button wire:click="cancelarCopropietario" class="g_accion eliminar"
                                                    title="Cancelar">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Fila normal --}}
                                        <tr wire:key="cop-{{ $cop['id'] }}">
                                            <td class="g_negrita">{{ $cop['dni'] }}</td>
                                            <td>{{ $cop['nombres'] }}</td>
                                            <td style="font-size:0.85rem; color:#555;">
                                                {{ $cop['email'] ?? '—' }}
                                            </td>
                                            <td style="font-size:0.85rem; color:#555;">
                                                {{ $cop['celular'] ?? '—' }}
                                            </td>
                                            <td class="g_celda_centro">
                                                @php
                                                    $copModel = \App\Models\CopropietarioEntregaFest::find($cop['id']);
                                                @endphp
                                                @if($copModel?->invitado)
                                                    <span class="g_badge success" style="font-size:0.7rem;">Con invitación</span>
                                                @else
                                                    <span class="g_badge light" style="font-size:0.7rem;">Sin invitación</span>
                                                @endif
                                            </td>
                                            <td class="g_celda_acciones g_celda_centro">
                                                <button wire:click="editarCopropietario({{ $cop['id'] }})"
                                                    class="g_accion editar" title="Editar">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </button>
                                                <button wire:click="eliminarCopropietario({{ $cop['id'] }})"
                                                    wire:confirm="¿Eliminar este copropietario?" class="g_accion eliminar"
                                                    title="Eliminar">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="g_celda_centro" style="padding:30px; color:#999;">
                                            <i class="fa-solid fa-people-group"
                                                style="font-size:1.5rem; display:block; margin-bottom:8px;"></i>
                                            Este lote no tiene copropietarios registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>