<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Tu Pase de Entrada</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f7f9;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }

        .content {
            padding: 30px;
            text-align: center;
        }

        .ticket-box {
            background-color: #fafffb;
            border: 2px dashed #1a237e;
            border-radius: 16px;
            padding: 25px;
            margin: 20px 0;
        }

        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            padding: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .code-text {
            font-size: 24px;
            font-weight: bold;
            color: #1a237e;
            letter-spacing: 4px;
            margin-top: 15px;
        }

        .details {
            margin: 25px 0;
            border-top: 1px solid #eee;
            padding-top: 20px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .label {
            color: #888;
            font-size: 14px;
        }

        .value {
            color: #333;
            font-weight: 600;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }

        .important {
            color: #d32f2f;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0;">¡Confirmado!</h1>
            <p style="margin:10px 0 0;">Aquí tienes tu pase para el evento</p>
        </div>

        <div class="content">
            <h2 style="color: #1a237e;">{{ $evento->nombre }}</h2>
            <p>Hola <strong>{{ $nombrePersona }}</strong>, gracias por confirmar tu asistencia.</p>

            <div class="ticket-box">
                <p style="margin-top:0; font-weight:bold; color:#666;">PRESENTA ESTE CÓDIGO AL INGRESAR</p>
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $invitado->codigo_invitado }}"
                        alt="QR Access" style="width:200px; height:200px;">
                </div>
                <div class="code-text">{{ $invitado->codigo_invitado }}</div>
            </div>

            <div class="details">
                <div class="detail-row">
                    <span class="label">Fecha del Evento:</span>
                    <span class="value">{{ date('d/m/Y', strtotime($evento->fecha_entrega)) }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Acompañantes:</span>
                    <span class="value">{{ $invitado->cantidad_acompanantes_permitidos }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Transporte:</span>
                    <span class="value">
                        {{ $invitado->transporte === 'bus' ? 'Bus Aybar' : 'Propio' }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="label">Ubicación:</span>
                    <span class="value">{{ $proyecto }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Lote / Manzana:</span>
                    <span class="value">{{ $lote }} / {{ $manzana }}</span>
                </div>
            </div>

            <p class="important">No olvides llevar tu DNI y presentar este código al ingresar.</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Aybar Corp. Todos los derechos reservados.<br>
            Este es un correo automático, por favor no lo respondas.
        </div>
    </div>
</body>

</html>