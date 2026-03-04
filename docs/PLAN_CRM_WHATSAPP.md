# Plan de Implementación: Aybar CRM - Integración WhatsApp Business

## 1. ¿Qué es un CRM y por qué Aybar se está convirtiendo en uno?
Un **CRM (Customer Relationship Management)** no es solo una base de datos de clientes; es una estrategia integral para gestionar todas las interacciones de la empresa con sus clientes actuales y potenciales.

Aybar ya tiene la "operación" (el ERP: lotes, letras, tickets). Al añadir **WhatsApp**, estás agregando la capa de **Relación Diaria**. 
*   **ERP:** Gestiona el "qué" (el contrato, la letra, el pago).
*   **CRM:** Gestiona el "quién" y "cómo" (la conversación, el sentimiento del cliente, la rapidez de respuesta).

Al integrar WhatsApp, conviertes a Aybar en un **CRM Omnicanal**, donde un agente de ATC puede ver el historial de tickets del cliente mientras chatea con él en tiempo real.

---

## 2. Requisitos Técnicos
Para implementar esto de manera profesional (no usando emuladores de celular que se desconectan), necesitamos:
1.  **WhatsApp Business API (Cloud API de Meta):** Es la versión oficial para empresas.
2.  **Meta for Developers Account:** Necesaria para obtener el ID de la App y el Token de acceso.
3.  **Webhook:** Un endpoint en Laravel para recibir los mensajes en tiempo real.
4.  **Stack Tecnológico:**
    *   **Laravel:** Procesamiento de Webhooks y lógica de negocio.
    *   **Livewire 4:** Interfaz reactiva para el chat en tiempo real.
    *   **Alpine.js:** Scroll automático, manejo de multimedia y reactividad ligera en el cliente.

---

## 3. Arquitectura del Sistema de Chat

### A. El Flujo de Entrada (Chatbot de Enrutamiento)
Cuando el cliente escribe, el sistema no lo asigna de inmediato. Pasa por un "Menú de Bienvenida":
1.  **Identificación:** El sistema busca el número en la tabla `clientes`.
2.  **Menú:** Envía un mensaje interactivo (botones o lista):
    *   "1. Consultas Generales (ATC)"
    *   "2. Pagos y Evidencias (Backoffice)"
    *   "3. Firmas y Letras (Letras)"
3.  **Enrutamiento:** Según la opción, el sistema marca la conversación para el departamento correspondiente.

### B. Lógica de Asignación
*   **Asignación por Departamento:** La conversación solo aparece en la bandeja del equipo seleccionado (rol `atc`, `backoffice` o `letras`).
*   **Asignación Aleatoria (Round Robin):** Dentro del equipo, el sistema busca al agente que tenga menos chats activos o al primero disponible de forma circular para que la carga sea equitativa.

---

## 4. Propuesta de Base de Datos (Nuevas Tablas)

```sql
-- Gestión de la sesión de chat
CREATE TABLE whatsapp_conversaciones (
    id BIGINT PRIMARY KEY,
    cliente_id BIGINT,
    agente_id BIGINT, -- Usuario con rol ATC/Backoffice/Letras
    estado ENUM('nuevo', 'en_menu', 'asignado', 'cerrado'),
    departamento_destino ENUM('atc', 'backoffice', 'letras'),
    mensajes_sin_leer INT DEFAULT 0, -- Contador para notificaciones
    last_message_at TIMESTAMP
);

-- Historial de mensajes
CREATE TABLE whatsapp_mensajes (
    id BIGINT PRIMARY KEY,
    conversacion_id BIGINT,
    direccion ENUM('entrante', 'saliente'),
    tipo ENUM('texto', 'imagen', 'documento', 'plantilla'),
    contenido TEXT, -- Texto o URL del archivo
    wa_message_id VARCHAR(255), -- ID de Meta para tracking
    estado ENUM('enviado', 'entregado', 'leido', 'fallido') DEFAULT 'enviado',
    reaccion VARCHAR(50), -- Emoji de la reacción si existe
    created_at TIMESTAMP
);

-- Plantillas homologadas por Meta
CREATE TABLE whatsapp_plantillas (
    id BIGINT PRIMARY KEY,
    nombre VARCHAR(100), -- ej: 'pago_verificado'
    contenido TEXT, -- "Hola {{1}}, tu pago ha sido validado."
    categoria VARCHAR(50)
);

-- Inteligencia del Chatbot (Preguntas y Respuestas)
CREATE TABLE whatsapp_conocimiento (
    id BIGINT PRIMARY KEY,
    pregunta_clave VARCHAR(100), -- Palabra que activa el bot
    respuesta TEXT, -- Lo que el bot responde
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Gestión de Envíos Masivos (Campañas)
CREATE TABLE whatsapp_campañas (
    id BIGINT PRIMARY KEY,
    nombre VARCHAR(100), -- ej: 'Promo Lotes Marzo'
    plantilla_id BIGINT,
    segmento_filtro JSON, -- ej: {"rol": "cliente", "distrito": "Asia"}
    estado ENUM('borrador', 'programado', 'enviando', 'finalizado'),
    total_enviados INT DEFAULT 0,
    total_leidos INT DEFAULT 0,
    programado_para TIMESTAMP,
    created_at TIMESTAMP
);

CREATE TABLE whatsapp_conocimiento (
    id BIGINT PRIMARY KEY,
    pregunta_clave VARCHAR(100),
    respuesta TEXT,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 5. Plan de Implementación Detallado (Fases)

### Fase 1: Configuración de Infraestructura (Semana 1)
*   Crear cuenta en **Meta for Developers**.
*   Configurar el **WhatsApp Business API**.
*   Configurar el Webhook en Laravel (`routes/api.php`) para recibir los JSON de Meta.
*   Verificar el número de teléfono.

### Fase 2: El "Cerebro" del Chatbot (Semana 2)
*   Desarrollar un `ChatService` en Laravel que procese los mensajes entrantes.
*   Implementar la lógica de "Estado de Conversación" (para saber si el cliente está en el menú o ya está hablando con alguien).
*   Crear el envío automático del menú inicial.

### Fase 3: Interfaz del Agente (Livewire) (Semana 3)
*   Crear una vista tipo "Dashboard de Chat" (estilo WhatsApp Web) en Aybar.
*   Usar **Livewire** para el refresco en tiempo real de los mensajes.
*   Integrar "Acciones Rápidas": Botón para crear un Ticket directamente desde el chat o ver la Solicitud de Letra del cliente.

### Fase 4: Plantillas y Notificaciones (Semana 4)
*   Configurar las plantillas HSM (High Security Messages) para poder iniciar conversaciones con el cliente (Meta solo permite iniciar con plantillas si han pasado >24h).
*   Automatizar mensajes: Ej: Cuando se valide una evidencia en Backoffice, enviar un WhatsApp automático al cliente.

---

## 6. Ventajas para Aybar
1.  **Trazabilidad:** Sabrás cuánto tarda un agente de ATC en responder un WhatsApp.
2.  **Seguridad:** Los chats son de la empresa, no del celular personal del trabajador.
3.  **Eficiencia:** El cliente ya llega "filtrado" al departamento correcto.
4.  **Omnicanalidad:** Puedes convertir un chat de WhatsApp en un Ticket con un solo clic.
## 7. Respuestas a Capacidades Específicas

### ¿Se puede hacer un Chatbot?
**Sí, y es la parte más potente.** 
El sistema no solo enviará el menú inicial. Podemos programar respuestas automáticas para preguntas frecuentes (FAQ) usando una tabla de `whatsapp_conocimiento`.
*   *Ejemplo:* Si el cliente escribe "horario", el sistema responde automáticamente sin que intervenga un humano.
*   *Integración ERP:* El chatbot puede consultar la base de datos. Si el cliente pregunta "¿Cómo va mi lote?", el bot puede consultar la tabla `proyectos` y responder con el estado actual en segundos.

### ¿Se pueden enviar Mensajes Masivos?
**Sí, a través de Plantillas de Marketing.**
Meta permite enviar mensajes a miles de clientes a la vez, pero bajo estas condiciones:
1.  **Plantillas Pre-aprobadas:** Debes registrar el mensaje en el panel de Meta (ej: "¡Gran feria de lotes este domingo!").
2.  **Costo:** Meta cobra por "conversación iniciada por marketing", pero es la forma más efectiva de llegar al 100% de tus clientes sin que te bloqueen el número (a diferencia de usar WhatsApp personal).
3.  **Segmentación:** En Aybar podemos filtrar: "Enviar este mensaje masivo solo a los clientes que tienen letras pendientes".

### ¿Seguimiento de Lectura (Visto)?
**Sí, el API nos avisa en tiempo real.**
Cada vez que un cliente abre el mensaje, Meta envía un evento de `read`. 
*   En la tabla `whatsapp_mensajes` tendremos el campo `estado` (enviado, entregado, leido).
*   En el panel de control de Aybar, verás el **doble check azul** cuando el cliente haya visto tu mensaje, igual que en la app oficial.

### Notificaciones y Mensajes Pendientes
**Sí, tendremos un contador global.**
En el menú lateral de Aybar, podemos mostrar un globo rojo (badge) con el número total de chats esperando respuesta.
*   **Gestión por Agente:** Cada vez que entre un mensaje nuevo, el contador `mensajes_sin_leer` aumentará. Cuando el agente abra el chat, el contador se reinicia. Esto asegura que ningún cliente se quede sin atención.

### Reacciones (Emojis)
**Sí, el API de Meta soporta reacciones.**
Podremos ver si un cliente reaccionó con un 👍 o ❤️ a un mensaje de la empresa. Esto se traduce en un campo `reaccion` en nuestra tabla de mensajes, permitiendo que la interfaz de Aybar refleja exactamente lo que el cliente ve en su celular.

### Adjuntar Archivos (Multimedia)
**Esta es la clave para Backoffice y Letras.**
Podremos recibir y enviar:
*   **Imágenes:** Fotos de los vouchers de pago.
*   **Documentos (PDF):** Enviar contratos de letras o estados de cuenta.
*   **Audios:** Para explicaciones rápidas del cliente.
*   **Ubicación:** Si el cliente necesita saber dónde queda un lote.
Todos estos archivos se guardarán en el almacenamiento de Aybar (Storage) para que queden como evidencia histórica en el expediente del cliente.

## 8. Presupuesto y Costos (Estimados para Perú)

Al usar la **API Cloud de Meta** directamente, eliminamos costos de intermediarios. El esquema de cobro es por "Conversaciones de 24 horas".

### Beneficio Principal: 1,000 Chats Gratis
Meta otorga las primeras **1,000 conversaciones de Servicio** (iniciadas por el cliente) de forma **gratuita** cada mes. Si tu volumen de consultas diarias es moderado, el costo mensual de WhatsApp para Aybar podría ser **S/ 0.00**.

### Tabla de Tarifas (Soles - Estimado)
Si se superan los 1,000 gratis o si la empresa inicia el contacto:

| Categoría | Iniciado por | Costo Conversación (24h) | Uso Típico |
| :--- | :--- | :--- | :--- |
| **Servicio** | Cliente | ~ S/ 0.06 - 0.07 | Consultas generales, soporte. |
| **Utilidad** | Empresa | ~ S/ 0.08 | Avisos de pago, confirmación de cita. |
| **Marketing** | Empresa | ~ S/ 0.25 | Envíos masivos, promociones de lotes. |
| **Autenticación** | Empresa | ~ S/ 0.08 | Códigos de acceso (OTPs). |

### Gastos Operativos Adicionales
1.  **Número de Teléfono:** Se recomienda un número nuevo (prepago desde S/ 5.00) dedicado solo para la API.
2.  **Servidor:** Aybar deberá estar alojado en un servidor con SSL (HTTPS) válido (Aprox. desde S/ 40.00 al mes).
3.  **Mantenimiento:** S/ 0.00 (al ser un desarrollo propio integrado en Laravel).

## 9. Envíos Masivos y Seguridad (Bloqueos)

Una de las mayores dudas es: **¿Cuánto puedo enviar sin que me bloqueen?** Al usar el API Oficial (Cloud API), las reglas cambian a tu favor:

### Niveles de Envío (Tiers)
Meta no te bloquea de la nada, sino que te asigna "Niveles de Confianza":
*   **Nivel 1:** Puedes enviar mensajes a **1,000 clientes únicos** en un periodo de 24 horas.
*   **Nivel 2:** Puedes enviar a **10,000 clientes únicos** en 24 horas.
*   **Nivel 3:** Puedes enviar a **100,000 clientes únicos** en 24 horas.

**¿Cómo subes de nivel?** Si envías mensajes de calidad y los clientes no te reportan como spam, Meta te sube de nivel automáticamente en pocos días.

### ¿Por qué NO te bloquean?
1.  **Validación de Plantilla:** Antes de enviar un masivo, Meta aprueba el texto. Si ellos lo aprueban, ya tienes su "permiso".
2.  **Uso de Botón de "Darse de baja":** Podemos incluir un botón en el masivo que diga "No recibir más". Si el cliente hace clic, Aybar lo marca para no enviarle más, evitando que el cliente use el botón "REPORTE/BLOQUEAR" de WhatsApp, que es lo que realmente afecta tu reputación.

## 10. ¿Tablas o Funcionalidad?
Ambas cosas son necesarias y ya están incluidas en el plan:
*   **Tablas:** Necesitamos `whatsapp_conocimiento` para que el bot sepa qué responder y `whatsapp_campañas` para guardar el historial de tus envíos masivos y sus estadísticas (quién lo leyó y quién no).
*   **Funcionalidad:** El código en Laravel se encargará de "leer" esas tablas y disparar los mensajes en el momento justo.


1. Las Tablas Necesarias
He añadido las tablas de Chatbot (whatsapp_conocimiento) y Masivos (whatsapp_campañas).

La de masivos es genial porque te permitirá guardar un "filtro" (ej: enviar solo a clientes de Lima que deban 2 letras) y ver en tiempo real cuántos lo abrieron.
2. Seguridad contra Bloqueos (Límites de Envío)
Aquí está lo más importante para tu tranquilidad: Meta no te bloquea si usas la API oficial.

Límites Graduales: Empiezas pudiendo enviar a 1,000 personas al día. Si no tienes reportes de spam, Meta te sube a 10,000 y luego a 100,000 automáticamente.
Plantillas Pre-aprobadas: Al usar plantillas que Meta ya revisó, el riesgo de bloqueo es casi cero.
Gestión de Bajas: El plan incluye un botón de "No deseo recibir más mensajes", así el cliente no tiene que reportarte como spam para dejar de recibir publicidad.

Marketing
US$0.077 + IGV

Utilidad
US$0.001 + IGV

SMS (SPAN)

Template: Marketing o Utilidad(Mensaje a una respuesta del cliente )

