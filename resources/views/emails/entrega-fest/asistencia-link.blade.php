<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Confirmación de Asistencia - {{ $evento->nombre }}</title>
</head>

<body style="margin:0; padding:25px; font-family: Arial, sans-serif; background-color: #f4f7f9;">
    <div
        style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; padding:40px; color:#333333; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div style="text-align:center; margin-bottom: 30px;">
            <img src="https://aybarcorp.com/public/logo-aybar-corp-verde.png" alt="Aybar Corp" style="width:180px;">
        </div>

        <h2 style="color:#1a237e; margin-top:0; text-align: center; font-size: 24px;">
            ¡Hola, {{ $prospecto->nombres }}!
        </h2>

        <p style="font-size:16px; line-height:1.6; text-align: center; color: #555;">
            Nos complace invitarte a nuestro próximo evento: <br>
            <strong style="color: #1a237e; font-size: 18px;">{{ $evento->nombre }}</strong>
        </p>

        <div style="background: #e8eaf6; padding: 20px; border-radius: 12px; margin: 25px 0;">
            <p style="margin: 0 0 10px 0; font-size: 15px;"><strong>Detalles de tu registro:</strong></p>
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px 0; color: #666;">Proyecto:</td>
                    <td style="padding: 5px 0; font-weight: bold;">{{ $prospecto->proyecto->nombre ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666;">Lote / MZ:</td>
                    <td style="padding: 5px 0; font-weight: bold;">{{ $prospecto->lote }} {{ $prospecto->manzana }}</td>
                </tr>
            </table>
        </div>

        <p style="font-size:16px; line-height:1.6; text-align: center;">
            Para asegurar tu lugar y el de tus acompañantes, por favor confirma tu asistencia haciendo clic en el
            siguiente botón:
        </p>

        <div style="text-align:center; margin:35px 0;">
            <a href="{{ $link }}" target="_blank"
                style="background-color:#1a237e; color:#ffffff !important; padding:16px 32px; text-decoration:none; border-radius:10px; font-size:16px; font-weight:bold; display: inline-block; box-shadow: 0 4px 6px rgba(26,35,126,0.2);">
                Confirmar Asistencia
            </a>
        </div>

        <p style="font-size:14px; color:#666; text-align: center; line-height: 1.5;">
            Si el botón no funciona, puedes copiar y pegar este enlace en tu navegador:<br>
            <a href="{{ $link }}" style="color: #1a237e; word-break: break-all;">{{ $link }}</a>
        </p>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

        <p style="font-size:14px; color:#888; margin-bottom:0; text-align: center;">
            Saludos cordiales,<br>
            <strong>Equipo Aybar Corp</strong>
        </p>
    </div>
</body>

</html>