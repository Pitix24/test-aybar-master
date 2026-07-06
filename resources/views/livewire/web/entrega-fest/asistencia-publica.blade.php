<div class="ef_page">
    @vite('resources/css/erp/entregafest/invitacion.css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap');
    </style>

    <div class="ef_container">
        @if (!$enviado)
            <div class="ef_header">
                <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png" alt="Entrega Fest"
                    class="ef_logo_main">
                <p class="ef_header_text">Formulario de Confirmación de Asistencia</p>
                <div class="ef_badge_type">Titular del Terreno</div>
            </div>

            <div class="ef_body">
                @if (session()->has('error'))
                    <div class="alert-error"
                        style="margin-bottom: 20px; padding: 15px; background: #fee2e2; color: #dc2626; border-radius: 12px; font-weight: 600;">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="ef_info_card">
                    <div class="ef_info_group">
                        <label>Nombre Completo</label>
                        <span>{{ $prospecto->nombres }}</span>
                    </div>
                    <div class="ef_info_group">
                        <label>DNI / Documento</label>
                        <span>{{ $prospecto->dni }}</span>
                    </div>
                    <div class="ef_info_group">
                        <label>Proyecto</label>
                        <span>{{ $prospecto->proyecto?->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="ef_info_group">
                        <label>Terreno / MZ</label>
                        <span>{{ $prospecto->lote }} - {{ $prospecto->manzana }}</span>
                    </div>
                </div>

                <form wire:submit.prevent="save">
                    <p class="ef_question">¿Confirmas tu asistencia al evento?</p>

                    <div class="ef_btn_group">
                        <label class="ef_btn_choice {{ $asistira === 'si' ? 'active_si' : '' }}">
                            <input type="radio" wire:model.live="asistira" value="si" style="display: none;">
                            SÍ, asistiré
                        </label>
                        <label class="ef_btn_choice {{ $asistira === 'no' ? 'active_no' : '' }}">
                            <input type="radio" wire:model.live="asistira" value="no" style="display: none;">
                            No podré asistir
                        </label>
                    </div>

                    @if ($asistira === 'si')
                        <div class="ef_form_grid">
                            <div class="ef_input_group">
                                <label>Nº de acompañantes (máx. 2)</label>
                                <select wire:model.live="cantidad_acompanantes" class="ef_input">
                                    <option value="0">Sin acompañantes</option>
                                    <option value="1">1 acompañante</option>
                                    <option value="2">2 acompañantes</option>
                                </select>
                            </div>

                            <div class="ef_input_group">
                                <label>Tipo de Transporte</label>
                                <select wire:model="transporte" class="ef_input">
                                    <option value="bus">Bus</option>
                                    <option value="propio">Movilidad Propia</option>
                                </select>
                            </div>
                        </div>

                        @if ($cantidad_acompanantes > 0)
                            <div style="margin-top: 25px; padding: 15px; background: #f0fdfa; border: 1px dashed #99f6e4; border-radius: 12px;">
                                <p style="margin: 0 0 8px 0; color: #004d55; font-size: 0.95rem; font-weight: 700;">
                                    <i class="fa-solid fa-circle-info"></i> Información Importante
                                </p>
                                <ul style="margin: 0; padding-left: 20px; color: #004d55; font-size: 0.85rem; line-height: 1.6;">
                                    <li><i class="fa-solid fa-check" style="padding-right: 5px;"></i>En esta zona podrá registrar el nombre completo y número de DNI de cada uno de sus acompañantes.</a></li>
                                    <li><i class="fa-solid fa-check" style="padding-right: 5px;"></i>Los niños mayores de 3 años cuentan como acompañante.</a></li>
                                </ul>
                            </div>

                            @for ($i = 0; $i < $cantidad_acompanantes; $i++)
                                <div class="ef_companion_section" style="margin-top: 20px; margin-bottom: 20px; padding: 25px 20px; background: #ffffff; border-radius: 15px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                                    <h3 style="color: #334155; font-size: 1.1rem; margin-top: 0; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
                                        <i class="fa-solid fa-user-plus" style="color: #e68a00;"></i> Datos del acompañante {{ $i + 1 }}
                                    </h3>
                                    <div class="ef_form_grid">
                                        <div class="ef_input_group">
                                            <label>DNI del acompañante</label>
                                            <input type="text" wire:model="acompanantes.{{ $i }}.dni" class="ef_input" placeholder="Ingrese DNI">
                                            @error("acompanantes.{$i}.dni") <span class="error-msg" style="color: #ef4444; font-size: 0.8rem; font-weight: 600;">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="ef_input_group">
                                            <label>Nombres completos</label>
                                            <input type="text" wire:model="acompanantes.{{ $i }}.nombres" class="ef_input" placeholder="Ingrese nombres">
                                            @error("acompanantes.{$i}.nombres") <span class="error-msg" style="color: #ef4444; font-size: 0.8rem; font-weight: 600;">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="ef_input_group">
                                            <label>Email (opcional)</label>
                                            <input type="email" wire:model="acompanantes.{{ $i }}.email" class="ef_input" placeholder="correo@ejemplo.com">
                                            @error("acompanantes.{$i}.email") <span class="error-msg" style="color: #ef4444; font-size: 0.8rem; font-weight: 600;">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="ef_input_group">
                                            <label>Celular (opcional)</label>
                                            <input type="text" wire:model="acompanantes.{{ $i }}.celular" class="ef_input" placeholder="999 999 999">
                                            @error("acompanantes.{$i}.celular") <span class="error-msg" style="color: #ef4444; font-size: 0.8rem; font-weight: 600;">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        @endif
                    @endif

                    <div class="ef_input_group">
                        <label>Observaciones o requerimientos (opcional)</label>
                        <textarea wire:model="observaciones" rows="3" class="ef_textarea"
                            placeholder="Escribe aquí si tienes alguna observación..."></textarea>
                    </div>

                    <button type="submit" class="ef_btn_submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Enviar registro <i class="fa-solid fa-paper-plane"></i></span>
                        <span wire:loading>Procesando... <i class="fa-solid fa-circle-notch fa-spin"></i></span>
                    </button>
                </form>
            </div>
        @else
            <div class="ef_header">
                <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png" alt="Entrega Fest"
                    class="ef_logo_main">
                <p class="ef_header_text">Tu pase de entrada 🎫</p>
            </div>

            <div class="ef_body">
                <div class="ef_success_body">
                    <h2 class="ef_success_title">¡Hola, {{ $prospecto->nombres }}!</h2>
                    <p class="ef_success_text">
                        Tu asistencia al evento <strong>{{ $evento->nombre }}</strong> ha sido confirmada. Aquí tienes tu
                        pase oficial de ingreso:
                    </p>

                    @if($asistira === 'si' && $codigo_invitado)
                        <div class="ef_ticket_dashed">
                            <span class="ef_ticket_label">Presenta este código al ingresar</span>

                            <div class="ef_qr_box">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $codigo_invitado }}"
                                    alt="QR Asistencia" style="width: 200px; height: 200px; display: block;">
                            </div>

                            <p class="ef_ticket_code">{{ $codigo_invitado }}</p>
                        </div>

                        <div class="ef_ticket_footer">
                            <div class="ef_footer_row">
                                <span class="ef_footer_label">Proyecto</span>
                                <span class="ef_footer_value">{{ $prospecto->proyecto?->nombre ?? 'N/A' }}</span>
                            </div>
                            <div class="ef_footer_row">
                                <span class="ef_footer_label">Terreno / Manzana</span>
                                <span class="ef_footer_value">{{ $prospecto->lote }} {{ $prospecto->manzana }}</span>
                            </div>
                            <div class="ef_footer_row">
                                <span class="ef_footer_label">Acompañantes</span>
                                <span class="ef_footer_value">{{ $cantidad_acompanantes }}</span>
                            </div>
                            <div class="ef_footer_row">
                                <span class="ef_footer_label">Transporte</span>
                                <span class="ef_footer_value">{{ $transporte === 'bus' ? 'Bus' : 'Propio' }}</span>
                            </div>
                        </div>

                        <div
                            style="margin-top: 25px; padding: 15px; background: #fff1f2; border-radius: 12px; font-size: 0.85rem; color: #d32f2f; line-height: 1.5; text-align: center; font-weight: 700;">
                            <i class="fa-solid fa-camera"></i> Toma una captura de pantalla y llévalo el día del evento.
                        </div>
                    @else
                        <div style="padding: 40px; text-align: center;">
                            <div style="font-size: 50px; color: #666; margin-bottom: 20px;">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <p style="color: #666; font-size: 1.1rem;">{{ $mensaje_exito }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
