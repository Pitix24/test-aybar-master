<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>📋 Instrucciones del Evento - {{ $evento->nombre }}</title>
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
                        <td align="center"
                            style="background: linear-gradient(135deg, #f8cc00 0%, #ff7e33 100%); padding: 50px 30px 40px;">
                            <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png"
                                alt="Entrega Fest"
                                style="width: 260px; max-width: 90%; height: auto; display: block; margin-bottom: 20px;">
                            <h1
                                style="margin: 0; font-size: 20px; color: #004d55; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                                Instrucciones del Evento
                            </h1>
                        </td>
                    </tr>

                    <!-- IMAGEN PRINCIPAL -->
                    <tr>
                        <td align="center">
                            <img src="{{ $imagenUrl }}" alt="Banner Evento"
                                style="width: 100%; max-width: 600px; display: block;">
                        </td>
                    </tr>

                    <!-- CUERPO -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2
                                style="margin: 0 0 20px 0; color: #333333; font-size: 24px; font-weight: 700; text-align: center;">
                                ¡Hola, {{ $nombrePersona }}! 👋
                            </h2>

                            <p
                                style="margin: 0 0 30px 0; color: #555555; font-size: 16px; line-height: 1.6; text-align: center;">
                                Queremos compartirte detalles importantes para que disfrutes al máximo el evento 
                                <strong style="color: #004d55;">{{ $evento->nombre }}</strong>.
                                Por favor, lee con atención estas indicaciones:
                            </p>

                            <!-- TARJETA DE RECOMENDACIONES -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #004d55; border-radius: 25px; margin-bottom: 35px;">
                                <tr>
                                    <td style="padding: 30px;">
                                        <h3 style="margin: 0 0 15px 0; color: #f8cc00; font-size: 16px; font-weight: 700; text-transform: uppercase;">
                                            ⚠️ Importante para tu ingreso:
                                        </h3>
                                        <ul style="margin: 0; padding: 0; list-style-type: none; color: #ffffff;">
                                            <li style="margin-bottom: 12px; font-size: 15px; display: table;">
                                                <span style="display: table-cell; padding-right: 10px;">✅</span>
                                                <span style="display: table-cell;">Presentar tu <strong>código QR o invitación</strong> digital al ingresar.</span>
                                            </li>
                                            <li style="margin-bottom: 12px; font-size: 15px; display: table;">
                                                <span style="display: table-cell; padding-right: 10px;">✅</span>
                                                <span style="display: table-cell;">Llevar tu <strong>DNI original</strong> físico (indispensable).</span>
                                            </li>
                                            <li style="margin-bottom: 12px; font-size: 15px; display: table;">
                                                <span style="display: table-cell; padding-right: 10px;">✅</span>
                                                <span style="display: table-cell;">Llegar con <strong>puntualidad</strong> a la hora indicada.</span>
                                            </li>
                                            <li style="margin-bottom: 0; font-size: 15px; display: table;">
                                                <span style="display: table-cell; padding-right: 10px;">✅</span>
                                                <span style="display: table-cell;">Respetar las indicaciones de nuestro personal de staff.</span>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin: 0; color: #555555; font-size: 15px; text-align: center; line-height: 1.6;">
                                Si tienes alguna duda adicional, no dudes en contactarnos.<br>
                                <strong style="color: #333333;">¡Te esperamos para vivir una experiencia inolvidable!</strong>
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