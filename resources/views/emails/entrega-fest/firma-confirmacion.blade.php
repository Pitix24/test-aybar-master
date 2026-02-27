<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>✅ Cita de Firma Confirmada - {{ $evento->nombre }}</title>
</head>

<body style="margin:0; padding:25px; font-family: Arial, sans-serif; background-color: #f4f7f9;">
    <div
        style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; padding:40px; color:#333333; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">

        {{-- Logo --}}
        <div style="text-align:center; margin-bottom: 30px;">
            <img src="https://aybarcorp.com/public/logo-aybar-corp-verde.png" alt="Aybar Corp" style="width:180px;">
        </div>

        {{-- Título --}}
        <h2 style="color:#1b5e20; margin-top:0; text-align: center; font-size: 24px;">
            ¡Hola, {{ $prospecto->nombres }}! ✅
        </h2>

        <p style="font-size:16px; line-height:1.6; text-align: center; color: #555;">
            Tu cita de firma de contrato ha sido <strong>agendada con éxito</strong> para el evento:<br>
            <strong style="color: #1b5e20; font-size: 18px;">{{ $evento->nombre }}</strong>
        </p>

        {{-- Fecha de cita destacada --}}
        <div
            style="background: #e8f5e9; border: 2px solid #2e7d32; border-radius: 16px; padding: 25px; margin: 25px 0; text-align: center;">
            <p style="margin:0 0 8px 0; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #555;">
                📅 Tu fecha de cita
            </p>
            <p style="margin:0; font-size: 22px; font-weight: bold; color: #1b5e20;">
                {{ ucfirst($fechaFormateada) }}
            </p>
        </div>

        {{-- Datos del lote --}}
        <div style="background: #f5f5f5; padding: 20px; border-radius: 12px; margin: 20px 0;">
            <p style="margin: 0 0 10px 0; font-size: 15px;"><strong>Datos de tu lote:</strong></p>
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px 0; color: #666;">Proyecto:</td>
                    <td style="padding: 5px 0; font-weight: bold;">{{ $prospecto->proyecto?->nombre ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666;">Lote / MZ:</td>
                    <td style="padding: 5px 0; font-weight: bold;">{{ $prospecto->lote }} {{ $prospecto->manzana }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666;">DNI:</td>
                    <td style="padding: 5px 0; font-weight: bold;">{{ $prospecto->dni }}</td>
                </tr>
            </table>
        </div>

        {{-- Recordatorio --}}
        <div
            style="background: #fff8e1; border-left: 4px solid #f9a825; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; font-size: 14px; color: #5d4037; line-height: 1.6;">
                <strong>⚠️ Recuerda:</strong><br>
                • Presentarte <strong>puntualmente</strong> el día de la cita.<br>
                • Llevar tu <strong>DNI original</strong>.<br>
                • Si necesitas cambiar la fecha, comunícate con nosotros lo antes posible.
            </p>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

        <p style="font-size:14px; color:#888; margin-bottom:0; text-align: center;">
            Saludos cordiales,<br>
            <strong>Equipo Aybar Corp</strong>
        </p>
    </div>
</body>

</html>