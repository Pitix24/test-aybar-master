<div class="asistencia-publica-container">
    <style>
        .asistencia-publica-wrapper {
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-header {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .form-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .form-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }

        .form-header .sub-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 13px;
            margin-top: 8px;
        }

        .form-body {
            padding: 30px;
        }

        .info-pill {
            background: #f1f8e9;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 4px solid #2e7d32;
        }

        .info-pill-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            display: block;
        }

        .info-value {
            font-weight: 600;
            color: #333;
        }

        .field-group {
            margin-bottom: 24px;
        }

        .field-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 15px;
        }

        .field-group small {
            display: block;
            color: #888;
            font-size: 12px;
            margin-top: 4px;
        }

        .form-input-fecha {
            width: 100%;
            padding: 14px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
            background: #fff;
        }

        .form-input-fecha:focus {
            outline: none;
            border-color: #2e7d32;
        }

        .btn-submit {
            width: 100%;
            background: #2e7d32;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            background: #1b5e20;
            transform: translateY(-2px);
        }

        .btn-submit:disabled {
            background: #9e9e9e;
            cursor: not-allowed;
        }

        .success-card {
            text-align: center;
            padding: 40px;
        }

        .success-icon {
            font-size: 60px;
            color: #4caf50;
            margin-bottom: 20px;
        }

        .fecha-box {
            background: #e8f5e9;
            border: 2px solid #2e7d32;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .fecha-box strong {
            font-size: 20px;
            color: #1b5e20;
            display: block;
            margin-top: 8px;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
        }

        .mensaje_error {
            color: #c62828;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        @media (max-width: 600px) {
            .info-pill-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="asistencia-publica-wrapper">
        <div class="form-card">
            @if (!$enviado)
                <div class="form-header">
                    <h1>{{ $evento->nombre }}</h1>
                    <p>Agendamiento de Cita - Firma de Contrato</p>
                    <span class="sub-badge">
                        <i class="fa-solid fa-file-signature"></i> Contrato Preliminar Aprobado ✅
                    </span>
                </div>

                <div class="form-body">
                    @if (session()->has('error'))
                        <div class="alert-error">{{ session('error') }}</div>
                    @endif

                    {{-- Datos del prospecto --}}
                    <div class="info-pill">
                        <div class="info-pill-grid">
                            <div>
                                <span class="info-label">Nombre Completo</span>
                                <span class="info-value">{{ $prospecto->nombres }}</span>
                            </div>
                            <div>
                                <span class="info-label">DNI</span>
                                <span class="info-value">{{ $prospecto->dni }}</span>
                            </div>
                            <div>
                                <span class="info-label">Proyecto</span>
                                <span class="info-value">{{ $prospecto->proyecto?->nombre ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="info-label">Lote / Manzana</span>
                                <span class="info-value">{{ $prospecto->lote }} - {{ $prospecto->manzana }}</span>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="field-group">
                            <label for="fecha_firma">
                                <i class="fa-solid fa-calendar-days"></i>
                                Selecciona la fecha para tu cita de firma *
                            </label>
                            <input type="datetime-local" id="fecha_firma" wire:model="fecha_firma" class="form-input-fecha"
                                min="{{ now()->addDay()->format('Y-m-d\TH:i') }}">
                            @error('fecha_firma')
                                <span class="mensaje_error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                            <small>Selecciona el día y la hora en que podrás acudir a firmar tu contrato.</small>
                        </div>

                        <button type="submit" class="btn-submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                Confirmar mi Cita <i class="fa-solid fa-calendar-check"></i>
                            </span>
                            <span wire:loading>
                                Guardando... <i class="fa-solid fa-circle-notch fa-spin"></i>
                            </span>
                        </button>
                    </form>
                </div>

            @else
                <div class="form-header" style="background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);">
                    <h1>{{ $evento->nombre }}</h1>
                    <p>Agendamiento de Cita</p>
                </div>

                <div class="success-card">
                    <div class="success-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h2 style="color: #1b5e20;">¡Cita Agendada!</h2>
                    <p style="color: #666; margin-top: 10px; font-size: 16px;">
                        {{ $mensaje_exito }}
                    </p>

                    @if($fecha_firma)
                        <div class="fecha-box">
                            <span style="font-size: 14px; color: #555; text-transform: uppercase; letter-spacing: 1px;">
                                Tu fecha de cita
                            </span>
                            <strong>
                                <i class="fa-solid fa-calendar-days"></i>
                                {{ \Carbon\Carbon::parse($fecha_firma)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm') }}
                            </strong>
                        </div>
                    @endif

                    <p style="margin-top: 20px; font-size: 14px; color: #888;">
                        <i class="fa-solid fa-circle-info"></i>
                        Recuerda llevar tu <strong>DNI original</strong> el día de la cita.
                    </p>
                </div>
            @endif
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 13px; color: #888;">
            &copy; {{ date('Y') }} Aybar Corp - Todos los derechos reservados.
        </div>
    </div>
</div>