<!doctype html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        @page {
            margin: 140px 40px 110px 40px;
        }

        .header_pagina {
            position: fixed;
            top: -140px;
            left: 0;
            right: 0;
            height: 140px;
        }

        .header-bar {
            height: 18px;
            background-color: #02424E;
            width: 100%;
        }

        .header-logo {
            text-align: center;
            margin-top: 22px;
        }

        .header-logo img {
            width: 90px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            z-index: -1000;
        }

        .watermark img {
            width: 430px;
        }

        .footer {
            position: fixed;
            bottom: -110px;
            left: 0;
            right: 0;
            height: 110px;
            width: 100%;
            color: #02424E;
            font-size: 9px;
            line-height: 14px;
        }

        .footer h3 {
            font-size: 9px;
            letter-spacing: 1px;
        }

        .footer p {
            line-height: 10px;
        }

        .footer-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 16px;
            background-color: #FFA726;
            width: 100%;
        }
    </style>
</head>

<body>

    <div class="header_pagina">
        <div class="header-bar"></div>

        <div class="header-logo">
            <img src="{{ public_path('assets/imagen/logo-aybar-corp-blanco-2.png') }}">
        </div>
    </div>

    <div class="watermark">
        <img src="{{ public_path('assets/imagen/logo-aybar-corp-blanco-2.png') }}">
    </div>

    @yield('content')

    <div class="footer">
        <h3>DIRECCIÓN:</h3>
        <p>Av. Circunvalación del Golf Los Incas 134 – <br>
            Santiago de Surco Patio Panorama, Torre 02 - piso 19</p>

        <div class="footer-bar"></div>
    </div>

</body>

</html>