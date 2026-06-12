<div class="ef_page">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap');
    </style>

    <div class="ef_container">
        @if (!$enviado)
        <div class="ef_header" style="background: var(--ef-primary); padding: 40px 20px;">
            <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png" alt="Entrega Fest"
                class="ef_logo_main" style="width: 240px; margin-bottom: 20px;">
            <h2 style="color: #ffffff; font-size: 1.6rem; font-weight: 700; margin: 0; letter-spacing: -0.5px;">
                Programación de cita para firma de contrato</h2>
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
                    <label>DNI/Documento de Identidad</label>
                    <span>{{ $prospecto->dni }}</span>
                </div>
                <div class="ef_info_group">
                    <label>Proyecto</label>
                    <span>{{ $this->proyectoActivo?->nombre ?? 'N/A' }}</span>
                </div>
                <div class="ef_info_group">
                    <label>Dirección de la Sede Principal</label>
                    <span>{{ $direccion_sede ?: 'N/A' }}</span>
                </div>
                <div class="ef_info_group">
                    <label>Manzana - Lote</label>
                    <span>{{ $this->manzanaActiva ?? 'N/A' }} - {{ $this->loteActivo ?? 'N/A' }}</span>
                </div>
            </div>

            <form wire:submit.prevent="save">
                {{-- Aviso informativo --}}
                <div style="background: #f0fdfa; border: 1px dashed #99f6e4; border-radius: 15px;
                            padding: 16px; margin-bottom: 25px; color: #004d55; font-size: 0.9rem;">
                    <p style="margin: 0 0 8px 0; font-weight: 700;">
                        <i class="fa-solid fa-circle-info"></i> Información importante
                    </p>
                    <ul style="margin: 0; padding-left: 22px; line-height: 1.7;">
                        <li>Le informamos que su contrato correspondiente al proyecto {{ $prospecto->proyecto?->nombre ?? 'N/A' }}
                            ya se encuentra listo para firma. Le agradeceremos revisar el documento y
                            programar su cita únicamente si no presenta observaciones. En caso de
                            tener alguna consulta sobre el contrato, por favor comuníquese con el
                            Área de Formalización de Contratos al siguiente correo:</li>
                        <li style="margin-top: 8px;">
                            <a href="mailto: legalcontratos@aybarsac.com" style="color: #004d55;
                            font-weight: 600; text-decoration: underline; display: inline-flex; align-items: center; gap: 6px;">
                                legalcontratos@aybarsac.com
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Aviso informativo --}}
                <div style="background: #f0fdfa; border: 1px dashed #99f6e4; border-radius: 15px;
                            padding: 16px; margin-bottom: 25px; color: #004d55; font-size: 0.9rem;">
                    <p style="margin: 0 0 8px 0; font-weight: 700;">
                        <i class="fa-solid fa-circle-info"></i> Sobre nuestros horarios
                    </p>
                    <ul style="margin: 0; padding-left: 22px; line-height: 1.7;">
                        <li>Debemos recordar que nuestros horarios de atención son los siguientes.</li>
                        <li>Lunes a Viernes: <strong>10:00 AM a 5:00 PM</strong>.</li>
                    </ul>
                </div>

                {{-- Campo: FECHA --}}
                <div class="ef_input_group">
                    <label for="fecha" style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-calendar-days"></i>
                        Selecciona el día (Lunes a Viernes)
                    </label>
                    <input type="date"
                        id="fecha"
                        wire:model.live="fecha"
                        class="ef_input"
                        min="{{ $fechaMinima }}"
                        onkeydown="return false"
                        style="padding: 18px 20px; font-size: 1.1rem; border: 2px solid var(--ef-primary); border-radius: 40px;">
                    @error('fecha')
                        <span class="mensaje_error"
                            style="color: #dc2626; font-size: 0.85rem; margin-top: 8px; display: block; font-weight: 600;">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Campo: HORA --}}
                <div class="ef_input_group" style="margin-top: 20px;">
                    <label for="hora" style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-clock"></i>
                        Selecciona el horario disponible
                    </label>

                    <div class="ef_select_wrapper">
                        <select id="hora" wire:model="hora" class="ef_input" required
                                @if(empty($fecha)) disabled @endif>
                            <option value="">
                                @if(empty($fecha))
                                    -- Primero selecciona una fecha --
                                @else
                                    -- Elige un horario --
                                @endif
                            </option>

                            @foreach($horariosDisponibles as $slot)
                                <option value="{{ $slot['hora'] }}"
                                        @disabled(!$slot['disponible'])>
                                    {{ $slot['hora'] }} hrs
                                    @if(!$slot['disponible'])
                                        — No disponible
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @error('hora')
                        <span class="mensaje_error"
                            style="color: #dc2626; font-size: 0.85rem; margin-top: 8px; display: block; font-weight: 600;">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror

                    {{-- Mensajito informativo cuando hay slots ocupados --}}
                    @if(!empty($fecha) && collect($horariosDisponibles)->where('disponible', false)->isNotEmpty())
                        <p style="font-size: 0.8rem; color: #b45309; margin-top: 10px; text-align: center;
                                background: #fef3c7; padding: 8px 12px; border-radius: 8px;">
                            <i class="fa-solid fa-circle-info"></i>
                            Algunos horarios no están disponibles porque nuestro equipo legal ya
                            tiene la capacidad máxima de atención.
                        </p>
                    @else
                        <p style="font-size: 0.85rem; color: #888; margin-top: 10px; text-align: center;">
                            Le recordamos llegar puntualmente a su cita y portar su DNI vigente.
                            En caso de contar con un copropietario, será necesario que este también
                            asista y presente su documento de identidad para la atención correspondiente.
                        </p>
                    @endif
                </div>

                <button type="submit" class="ef_btn_submit" wire:loading.attr="disabled"
                        style="margin-top: 30px; background: linear-gradient(135deg, #f8cc00 0%, #ff7e33 100%);
                            border-radius: 30px; padding: 20px; box-shadow: 0 10px 20px rgba(255,126,51,0.3);">
                    <span wire:loading.remove style="display: flex; align-items: center; gap: 10px;">
                        Confirmar mi cita <i class="fa-solid fa-circle-check"></i>
                    </span>
                    <span wire:loading>Guardando... <i class="fa-solid fa-circle-notch fa-spin"></i></span>
                </button>
            </form>
        </div>

        @else
        <div class="ef_header" style="background: var(--ef-primary); padding: 40px 20px;">
            <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png" alt="Entrega Fest"
                class="ef_logo_main" style="width: 200px; margin-bottom: 20px;">
            <h2 style="color: #ffffff; font-size: 1.4rem; font-weight: 700; margin: 0;">¡Cita Agendada!</h2>
        </div>

        <div class="ef_body">
            <div class="ef_success_body" style="padding: 20px 0;">
                <div style="font-size: 80px; color: #15803d; margin-bottom: 20px;">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <p class="ef_success_text" style="font-size: 1.2rem; margin-bottom: 30px;">
                    {{ $mensaje_exito }}
                </p>

                @if($fecha_firma)
                <div class="ef_ticket_dashed" style="padding: 30px;">
                    <span class="ef_ticket_label">Tu fecha programada</span>
                    <p class="ef_ticket_code" style="font-size: 1.6rem; letter-spacing: 0;">
                        <i class="fa-solid fa-clock" style="margin-right: 10px; opacity: 0.5;"></i>
                        {{ \Carbon\Carbon::parse($fecha_firma)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY [a
                        las] HH:mm') }}
                    </p>
                </div>
                @endif

                @if($direccion_sede)
                <div class="ef_ticket_dashed"
                    style="padding: 24px; margin-top: 20px; background: #f8fafc; border-color: #004d55;">
                    <span class="ef_ticket_label">Dirección de la sede principal</span>
                    <p class="ef_ticket_code" style="font-size: 1.05rem; letter-spacing: 0; line-height: 1.6;">
                        <i class="fa-solid fa-location-dot" style="margin-right: 10px; opacity: 0.5;"></i>
                        {{ $direccion_sede }}
                    </p>
                </div>
                @endif

                <div
                    style="margin-top: 30px; padding: 20px; background: #fff1f2; border-radius: 15px; color: #d32f2f; font-weight: 700; display: inline-block;">
                    <i class="fa-solid fa-circle-info"></i> Le recordamos llegar puntualmente a su cita y portar su DNI vigente.
                    En caso de contar con un copropietario, será necesario que este también asista y presente su documento de identidad para la atención correspondiente.
                </div>
            </div>
        </div>
        @endif
    </div>

    <div style="text-align: center; margin-top: 20px; padding-bottom: 40px; font-size: 13px; color: #888;">
        &copy; {{ date('Y') }} Aybar Corp - Todos los derechos reservados.
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const fechaInput = document.getElementById('fecha');
            if (!fechaInput) return;

            fechaInput.addEventListener('change', function (e) {
                const valor = e.target.value;
                if (!valor) return;

                const [yyyy, mm, dd] = valor.split('-').map(Number);
                const fecha = new Date(yyyy, mm - 1, dd);
                const dia = fecha.getDay();

                if (dia === 0 || dia === 6) {
                    alert('⚠️ Solo puedes agendar de Lunes a Viernes. Por favor selecciona otro día.');
                    e.target.value = '';
                    @this.set('fecha', '');
                }
            });
        });
    </script>
</div>
