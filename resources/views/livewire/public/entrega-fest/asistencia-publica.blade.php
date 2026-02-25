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
            background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
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

        .form-body {
            padding: 30px;
        }

        .info-pill {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 4px solid #1a237e;
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
            margin-bottom: 20px;
        }

        .field-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        .radio-options {
            display: flex;
            gap: 20px;
        }

        .radio-option {
            flex: 1;
            position: relative;
        }

        .radio-option input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .radio-label {
            display: block;
            text-align: center;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .radio-option input:checked+.radio-label {
            border-color: #1a237e;
            background: #e8eaf6;
            color: #1a237e;
        }

        .form-select,
        .form-input,
        .form-textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .form-select:focus,
        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #1a237e;
        }

        .btn-submit {
            width: 100%;
            background: #1a237e;
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
            background: #0d47a1;
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

        .alert-error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
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
                    <p>Formulario de Confirmación de Asistencia</p>
                </div>

                <div class="form-body">
                    @if (session()->has('error'))
                        <div class="alert-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="info-pill">
                        <div class="info-pill-grid">
                            <div>
                                <span class="info-label">Nombre Completo</span>
                                <span class="info-value">{{ $prospecto->nombres }}</span>
                            </div>
                            <div>
                                <span class="info-label">DNI / Documento</span>
                                <span class="info-value">{{ $prospecto->dni }}</span>
                            </div>
                            <div>
                                <span class="info-label">Proyecto</span>
                                <span class="info-value">{{ $prospecto->proyecto?->nombre ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="info-label">Lote y Manzana</span>
                                <span class="info-value">{{ $prospecto->lote }} - {{ $prospecto->manzana }}</span>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="field-group">
                            <label>¿Confirmas tu asistencia al evento?</label>
                            <div class="radio-options">
                                <label class="radio-option">
                                    <input type="radio" wire:model.live="asistira" value="si">
                                    <span class="radio-label">SÍ, asistiré</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" wire:model.live="asistira" value="no">
                                    <span class="radio-label">NO podré asistir</span>
                                </label>
                            </div>
                        </div>

                        @if ($asistira === 'si')
                            <div class="g_fila">
                                <div class="field-group g_columna_6">
                                    <label>Nº de acompañantes (máx. 3)</label>
                                    <select wire:model="cantidad_acompanantes" class="form-select">
                                        <option value="0">Sin acompañantes</option>
                                        <option value="1">1 acompañante</option>
                                        <option value="2">2 acompañantes</option>
                                        <option value="3">3 acompañantes</option>
                                    </select>
                                    @error('cantidad_acompanantes') <span class="mensaje_error">{{ $message }}</span> @enderror
                                </div>

                                <div class="field-group g_columna_6">
                                    <label>Tipo de Transporte</label>
                                    <select wire:model="transporte" class="form-select">
                                        <option value="bus">Bus / Movilidad Aybar</option>
                                        <option value="propio">Movilidad Propia</option>
                                    </select>
                                    @error('transporte') <span class="mensaje_error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        <div class="field-group">
                            <label>Observaciones o requerimientos (Opcional)</label>
                            <textarea wire:model="observaciones" rows="3" class="form-textarea"
                                placeholder="Escribe aquí si tienes alguna observación..."></textarea>
                            @error('observaciones') <span class="mensaje_error">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn-submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Enviar Registro <i class="fa-solid fa-paper-plane"></i></span>
                            <span wire:loading>Procesando... <i class="fa-solid fa-circle-notch fa-spin"></i></span>
                        </button>
                    </form>
                </div>
            @else
                <div class="success-card">
                    <div class="success-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h2>¡Registro Enviado!</h2>
                    <p style="color: #666; margin-top: 15px; font-size: 18px;">
                        {{ $mensaje_exito }}
                    </p>

                    @if($asistira === 'si' && $codigo_invitado)
                        <div
                            style="margin-top: 30px; padding: 30px; background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #eee;">
                            <h3 style="color: #1a237e; font-weight: 800; margin-bottom: 20px;">TU PASE DE ENTRADA</h3>

                            <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                                <div style="padding: 15px; background: white; border: 2px solid #1a237e; border-radius: 15px;">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ $codigo_invitado }}"
                                        alt="QR Asistencia" style="width: 180px; height: 180px;">
                                </div>

                                <div style="text-align: center;">
                                    <span
                                        style="display: block; font-size: 14px; text-transform: uppercase; color: #666; letter-spacing: 1px;">Código
                                        de Invitado</span>
                                    <span
                                        style="display: block; font-size: 22px; font-weight: 800; color: #1a237e; letter-spacing: 3px;">{{ $codigo_invitado }}</span>
                                </div>
                            </div>

                            <div
                                style="margin-top: 25px; padding: 15px; background: #e8eaf6; border-radius: 12px; font-size: 14px; color: #1a237e; line-height: 1.5;">
                                <i class="fa-solid fa-camera"></i> <strong>Toma una captura de pantalla</strong> de este código
                                y preséntalo en el ingreso el día del evento.
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 13px; color: #888;">
            &copy; {{ date('Y') }} Aybar - Todos los derechos reservados.
        </div>
    </div>
</div>