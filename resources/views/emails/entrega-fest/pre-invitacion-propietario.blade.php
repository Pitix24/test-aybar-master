<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pre-invitación - {{ $evento->nombre }}</title>
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
                                Queremos conocer su interés en <strong style="font-weight: 700;">participar</strong> del
                                <br>
                                <strong style="font-weight: 700; font-size: 20px;">{{ $evento->nombre }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- CUERPO -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2
                                style="margin: 0 0 10px 0; color: #004d55; font-size: 24px; font-weight: 700; text-align: center;">
                                ¡Hola, {{ $prospecto->nombres }}!
                            </h2>

                            <p
                                style="margin: 0 0 30px 0; color: #555555; font-size: 15px; line-height: 1.6; text-align: center;">
                                Por favor, complete este formulario solo si desea participar en el evento.
                            </p>

                            <!-- TARJETA DE INFO DARK -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #004d55; border-radius: 25px; margin-bottom: 35px; box-shadow: 0 8px 20px rgba(0,77,85,0.2);">
                                <tr>
                                    <td style="padding: 30px 35px;">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="55%" style="padding-bottom: 20px;">
                                                    <span
                                                        style="display: block; font-size: 11px; color: rgba(255,255,255,0.6); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.5px;">PROYECTO</span>
                                                    <span
                                                        style="display: block; font-size: 16px; color: #ffffff; font-weight: 700;">{{ $prospecto->proyecto->nombre ?? 'N/A' }}</span>
                                                </td>
                                                <td width="45%" style="padding-bottom: 20px;">
                                                    <span
                                                        style="display: block; font-size: 11px; color: rgba(255,255,255,0.6); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.5px;">DNI/DOCUMENTO
                                                        DE IDENTIDAD</span>
                                                    <span
                                                        style="display: block; font-size: 16px; color: #ffffff; font-weight: 700;">{{ $prospecto->dni }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <span
                                                        style="display: block; font-size: 11px; color: rgba(255,255,255,0.6); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.5px;">TERRENO
                                                        Y MZ</span>
                                                    <span
                                                        style="display: block; font-size: 16px; color: #ffffff; font-weight: 700;">{{ $prospecto->lote }}
                                                        {{ $prospecto->manzana }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin: 0 0 30px 0; font-size: 12px; color: #777777; font-style: italic; text-align: left;">
                                *Este correo no es una confirmación de asistencia.
                            </p>

                            <!-- BOTÓN DE ACCIÓN -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="10">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $link }}" target="_blank"
                                            style="background-color: #e68a00; color: #ffffff; padding: 18px 35px; text-decoration: none; border-radius: 15px; font-size: 18px; font-weight: 700; display: inline-block; text-shadow: 0 1px 2px rgba(0,0,0,0.1); box-shadow: 0 4px 15px rgba(230,138,0,0.3);">
                                            Confirmar interés
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin: 40px 0 0 0; color: #999999; font-size: 12px; text-align: center; line-height: 1.4;">
                                Si el botón no funciona, puedes copiar y pegar este enlace en tu navegador:<br>
                                <a href="{{ $link }}" style="color: #ff7e33; text-decoration: none;">{{ $link }}</a>
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