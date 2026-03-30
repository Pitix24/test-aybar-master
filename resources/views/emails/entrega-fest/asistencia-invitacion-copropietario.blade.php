<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invitación Especial - {{ $evento->nombre }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap');
    </style>
</head>

<body style="margin:0; padding:0; font-family: 'Outfit', Helvetica, Arial, sans-serif; background-color: #f8f9fa;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0"
        style="background-color: #f8f9fa; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 40px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <!-- HEADER GRADIENTE -->
                    <tr>
                        <td align="center" bgcolor="#ff7c31"
                            style="background-color: #ff7c31; background-image: linear-gradient(135deg, #fec400 0%, #ff7e33 100%); padding: 50px 30px 40px;">
                            <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png"
                                alt="Entrega Fest"
                                style="width: 280px; max-width: 90%; height: auto; display: block; margin-bottom: 20px;">
                            <p style="margin: 0; color: #004d55; font-size: 18px; font-weight: 400; line-height: 1.4;">
                                Confirma tu <strong style="font-weight: 700;">asistencia</strong> al
                                <br>
                                <strong style="font-weight: 700; font-size: 20px;">{{ $evento->nombre }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- CUERPO -->
                    <tr>
                        <td style="padding: 40px;">
                            <div style="text-align: center; margin-bottom: 15px;">
                                <span
                                    style="background: #e8f5e9; color: #1b5e20; padding: 6px 15px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase;">Copropietario</span>
                            </div>

                            <h2
                                style="margin: 0 0 20px 0; color: #333333; font-size: 24px; font-weight: 700; text-align: center;">
                                ¡Hola, {{ $copropietario->nombres }}!
                            </h2>

                            <p
                                style="margin: 0 0 30px 0; color: #555555; font-size: 16px; line-height: 1.6; text-align: center;">
                                Lo invitamos cordialmente a confirmar su asistencia. Por favor, complete el siguiente
                                formulario:
                            </p>

                            <!-- TARJETA DE INFO DARK -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #004d55; border-radius: 25px; margin-bottom: 35px;">
                                <tr>
                                    <td style="padding: 25px 30px;">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="50%" style="padding-bottom: 15px;">
                                                    <span
                                                        style="display: block; font-size: 11px; color: rgba(255,255,255,0.7); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Proyecto</span>
                                                    <span
                                                        style="display: block; font-size: 16px; color: #ffffff; font-weight: 700;">{{ $copropietario->prospecto?->proyecto?->nombre ?? 'N/A' }}</span>
                                                </td>
                                                <td width="50%" style="padding-bottom: 15px;">
                                                    <span
                                                        style="display: block; font-size: 11px; color: rgba(255,255,255,0.7); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">DNI
                                                        / Documento</span>
                                                    <span
                                                        style="display: block; font-size: 16px; color: #ffffff; font-weight: 700;">{{ $copropietario->dni }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <span
                                                        style="display: block; font-size: 11px; color: rgba(255,255,255,0.7); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Lote
                                                        y Manzana</span>
                                                    <span
                                                        style="display: block; font-size: 16px; color: #ffffff; font-weight: 700;">{{ $copropietario->prospecto?->lote ?? '—' }}
                                                        {{ $copropietario->prospecto?->manzana ?? '—' }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- BOTÓN DE ACCIÓN -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="10">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $link }}" target="_blank"
                                            style="background-color: #e68a00; color: #ffffff; padding: 18px 35px; text-decoration: none; border-radius: 15px; font-size: 18px; font-weight: 700; display: inline-block; text-shadow: 0 1px 2px rgba(0,0,0,0.1); box-shadow: 0 4px 15px rgba(230,138,0,0.3);">
                                            Confirmar mi asistencia
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin: 40px 0 0 0; color: #999999; font-size: 13px; text-align: center; line-height: 1.4;">
                                Si tienes problemas con el botón, copia y pega este enlace en tu navegador:<br>
                                <a href="{{ $link }}" style="color: #ff7e33;">{{ $link }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center"
                            style="background-color: #f1f3f5; padding: 30px; border-top: 1px solid #eeeeee;">
                            <img src="https://aybarcorp.com/public/assets/entregafest/logo-aybar-corp-fondo-blanco.png"
                                alt="Aybar Corp"
                                style="width: 100px; margin-bottom: 10px; filter: grayscale(1); opacity: 0.5;">
                            <p style="margin: 0; color: #aaaaaa; font-size: 12px;">
                                &copy; {{ date('Y') }} Aybar Corp. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>