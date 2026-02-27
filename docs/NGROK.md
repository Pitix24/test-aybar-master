# 🚀 Configuración de Ngrok para Webhooks (WhatsApp Business)

Este documento detalla los pasos para exponer tu servidor local de Laragon a internet usando **Ngrok**, permitiendo que Meta (Facebook/WhatsApp) envíe eventos a tu webhook local de forma segura.

## 🛠️ Requisitos Previos
1. **Laragon** iniciado (Apache en puerto 80 o Artisan Serve en 8000).
2. Haber iniciado sesión en [ngrok.com](https://dashboard.ngrok.com).

---

## 🔑 1. Configurar Autenticación (Solo una vez)
Para que la conexión sea persistente y puedas usar URLs personalizadas, debes agregar tu token de autenticación.

Ejecuta este comando en la terminal (si estás en una PC nueva):
```powershell
C:\laragon\bin\ngrok\ngrok.exe config add-authtoken 3A0Ohq5eZRJCmDRZa1tGKyUYq5h_PHW7yYcCMiSuCrkab7Yi
```

---

## 🌐 2. Levantar el Túnel
Dependiendo de cómo estés trabajando localmente, elige uno de los siguientes comandos:

### Opción A: Usando el servidor de Laragon (Puerto 80)
Si tu proyecto carga como `http://aybar.test` o similar en el puerto 80:
```powershell
C:\laragon\bin\ngrok\ngrok.exe http 80
```

### Opción B: Usando Artisan Serve (Puerto 8000)
Si ejecutas `php artisan serve` en la raíz del proyecto:
```powershell
C:\laragon\bin\ngrok\ngrok.exe http 8000
```

> [!TIP]
> **¿Quieres una URL que no cambie?**
> Ngrok ofrece un dominio gratuito permanente. Si ya lo reclamaste en el dashboard, usa:
> `C:\laragon\bin\ngrok\ngrok.exe http --url=TU_DOMINIO.ngrok-free.app 80`

---

## 💬 3. Configuración en Meta (WhatsApp Business)
Una vez que Ngrok te dé la URL pública (ej. `https://random-name.ngrok-free.app`), debes actualizarla en el Dashboard de Meta Developers:

1. **Webhook URL:** `https://TU_URL_NGROK.ngrok-free.app/api/whatsapp/webhook`
2. **Verify Token:** `aybar_crm_secret_token` (Este es el valor en tu archivo `.env`)

---

## 📝 Resumen de Datos
- **Ruta Webhook:** `/api/whatsapp/webhook`
- **Token de Verificación:** `aybar_crm_secret_token`
- **Ubicación de Ngrok:** `C:\laragon\bin\ngrok\ngrok.exe`


C:\laragon\bin\ngrok\ngrok.exe config add-authtoken 3A0Ohq5eZRJCmDRZa1tGKyUYq5h_PHW7yYcCMiSuCrkab7Yi

C:\laragon\bin\ngrok\ngrok.exe http 8000 --log=stdout
C:\laragon\bin\ngrok\ngrok.exe update

C:\laragon\bin\ngrok\ngrok.exe http 8000 --log=stdout

C:\laragon\bin\ngrok\ngrok.exe http 8000 --log=stdout

https://darcie-semitropical-todd.ngrok-free.dev/

https://darcie-semitropical-todd.ngrok-free.dev/api/whatsapp/webhook

Abrir:
C:\laragon\bin\ngrok\ngrok.exe http 8000
C:\laragon\bin\ngrok\ngrok.exe http 8000

https://darcie-semitropical-todd.ngrok-free.dev/api/whatsapp/webhook


C:\laragon\bin\ngrok\ngrok.exe http 8000 --region=sa
