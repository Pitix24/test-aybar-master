# Guía de Producción: n8n + Evolution API + Cloudflare

Esta configuración reemplaza a **ngrok** por un túnel permanente de Cloudflare usando tu dominio `aybarcorp.com`.

## 🌐 Arquitectura de Red

| Servicio | Subdominio sugerido | Puerto Interno | Propósito |
| :--- | :--- | :--- | :--- |
| **n8n** | `n8n.aybarcorp.com` | `5678` | Automatización de flujos y Webhooks. |
| **Evolution API** | `api.aybarcorp.com` | `8080` | Servidor de WhatsApp (Evolution). |
| **Dashboard** | `api.aybarcorp.com/manager` | `8080` | Panel para el código QR. |

---

## 🛠️ Configuración Inicial (Un solo pago de tiempo)

### Paso 1: Cloudflare Zero Trust
1. Entra a [Cloudflare Zero Trust](https://one.dash.cloudflare.com/).
2. Ve a **Networks** -> **Tunnels**.
3. Haz clic en **Create a Tunnel**. Ponle de nombre: `Aybar-Prod-Server`.
4. En **Install Connector**, copia el **Token** que sale después de `--token` (es un código largo).
5. Pega ese token en tu archivo `.env` como `CLOUDFLARE_TUNNEL_TOKEN=tu_token_aqui`.

### Paso 2: Configurar Rutas (Public Hostnames)
Dentro del túnel en Cloudflare, añade estos dos:

1. **n8n:**
   - Subdominio: `n8n` | Dominio: `aybarcorp.com`
   - Service: `http://n8n:5678`
2. **Evolution API:**
   - Subdominio: `api` | Dominio: `aybarcorp.com`
   - Service: `http://evolution-api:8080`

---

## 🚀 Despliegue

```powershell
# 1. Detener todo lo anterior
docker-compose -f docker-compose-n8n-evolutionapi.yml down

# 2. Levantar la nueva arquitectura profesional
docker-compose -f docker-compose-n8n-evolutionapi.yml up -d

# 3. Verificar que el túnel esté conectado
docker logs cloudflared-tunnel
```

---

## 📧 Pruebas de Webhooks (Producción)

Ahora tus URLs para Laravel o n8n serán:
- **Test Webhook:** `https://n8n.aybarcorp.com/webhook-test/xxxx`
- **Prod Webhook:** `https://n8n.aybarcorp.com/webhook/xxxx`

## 🔐 Seguridad
La API Key de Evolution API se mantiene como: `aybar_secure_token_2025`.
Pásala siempre en el header `apikey`.