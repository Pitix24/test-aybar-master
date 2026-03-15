# Arquitectura de Asistente IA Omnicanal (Voz y Chat)

Este documento detalla la arquitectura diseñada para implementar un Asistente de Inteligencia Artificial capaz de atender a los clientes tanto por llamadas telefónicas tradicionales como por chat de WhatsApp, utilizando un **único número telefónico** integrado con nuestro ERP en Laravel.

## 🏗️ Resumen de la Arquitectura

La solución se compone de 4 pilares principales:

1.  **Twilio:** Proveedor del número de teléfono en la nube (SIP/Troncal).
2.  **Meta WhatsApp Cloud API (WAPI):** Canal oficial para mensajería de texto.
3.  **Vapi.ai:** Motor generativo conversacional para llamadas de voz en tiempo real.
4.  **Laravel (Backend) / n8n (Opcional):** Fuente de verdad de los datos y orquestador principal (n8n puede usarse como middleware visual de IA, pero Laravel puede manejar todo con sus `Controllers` y `Jobs`).

---

## 🧩 Flujo de Implementación Paso a Paso

Para lograr que un solo número atienda ambos canales sin conflictos, el orden de configuración es fundamental:

### Paso 1: Adquisición del Número Base (Twilio)
Se debe comprar un número telefónico virtual en **Twilio**. 
*   **¿Por qué Twilio?** Twilio actúa como la placa base o "chip en la nube". Es necesario tener el número aquí porque Vapi requiere acceso de bajo nivel (WebSockets/SIP) a la señal de voz para que la IA escuche y hable sin latencia. Además, Twilio permite recibir mensajes SMS en su panel.

### Paso 2: Configuración del Canal de Chat (WhatsApp API)
Se registra una cuenta de desarrollador en **Meta (WhatsApp Business API)**.
1.  Se ingresa a Meta **el mismo número** telefónico adquirido en Twilio.
2.  Meta pedirá verificar el número enviando un código de 6 dígitos por SMS.
3.  Ingresamos al panel de Twilio, leemos el SMS entrante con el código de Meta y verificamos la cuenta de WhatsApp.
4.  A partir de aquí, Meta enrutará todos los *mensajes de texto* hacia nuestro Webhook (Típicamente `WhatsappController.php` en Laravel, o a un flujo en n8n si se decide usar este último como orquestador externo).

### Paso 3: Configuración del Canal de Voz (Vapi.ai)
Se conecta el asistente de voz generativa a la línea telefónica.
1.  En el panel de **Vapi.ai**, se importa y asocia la cuenta/número de Twilio.
2.  Vapi configura automáticamente en Twilio un Webhook de voz. 
3.  A partir de aquí, toda *llamada telefónica* entrante a la red tradicional será contestada por el Asistente IA de Vapi.

---

## 🚦 Comportamiento Final del Sistema

El cliente, prospecto o inversor tendrá agendado un único contacto en su teléfono (Ej. "Asistente EntregaFest").

1.  **Si el cliente envía un mensaje por WhatsApp:**
    *   La petición viaja por la red de Meta.
    *   Llega a nuestro orquestador (Directamente a Laravel o a n8n).
    *   El orquestador consulta el contexto o datos base en nuestro ERP (`c:\laragon\www\aybar`).
    *   Se le pasa el contexto a la IA (Ej. OpenAI) para generar la respuesta y luego entregársela al cliente.
    
2.  **Si el cliente hace una Llamada Telefónica clásica:**
    *   La petición viaja por la red de telefonía (Claro, Movistar, etc.).
    *   Entra a los servidores de Twilio.
    *   Twilio abre un WebStream (conexión de audio en tiempo real) con Vapi.ai.
    *   Vapi transcribe, procesa la IA, emite la voz y (si requiere datos) hace peticiones HTTP/API a nuestro Laravel en tiempo real durante la llamada.

> **Importante:** Las "llamadas de WhatsApp" (las que se realizan mediante el ícono de teléfono *dentro de la app* usando conexión a internet) **no están soportadas nativamente** por la API oficial de Meta para negocios. Si el usuario desea hablar mediante voz, el bot de WhatsApp debe guiarlo: *"Para atención por voz, por favor realice una llamada telefónica normal a este mismo número"*.

---

## 🛠️ Requisitos en nuestro Backend (Laravel)

Dado que la carga pesada de inteligencia y transmisión recae en los servicios externos de IA (y Vapi), nuestro ERP se concentra en servir información y orquestar el chat:

1.  **Exposición de APIs:** Debemos crear rutas específicas (ej. `routes/api/ia.php`) protegidas mediante tokens.
2.  **Endpoints Funcionales:** Ejemplos: `/api/ia/consultar-prospecto`, `/api/ia/validar-asistencia`, `/api/ia/extraer-itinerario`.
6.  **Registro de Actividad:** Cada vez que el bot (por chat o voz) finalice una interacción, debe hacer un POST a nuestro CRM para guardar el "Log" o "Resumen de la llamada/chat" en el historial del **Prospecto** respectivo.

---

## 🔍 ¿Cómo interactúa la IA con nuestra Base de Datos?

Para que los clientes puedan preguntar sobre un dato específico (Ej. *"¿Cuál es mi número de lote?"* o *"¿En qué proyecto estoy?"*), existen dos enfoques. **Para este proyecto, se recomienda el Enfoque 1 (Endpoints de API):**

### Enfoque 1: Endpoints de API / Function Calling (🚀 RECOMENDADO)
En lugar de darle acceso libre a la IA a la base de datos, nosotros construimos "puertas controladas" (Endpoints) en Laravel. Cuando un cliente pregunta algo, la IA sabe que debe golpear esa puerta para pedir la información.

**El Flujo:**
1.  **El cliente dice:** *"Quiero saber sobre qué lote es mi contrato"*.
2.  **La IA deduce:** *"Necesito usar la herramienta `consultar-lote-cliente` que me proporcionaron"*.
3.  **La IA hace una petición HTTP (API Rest):** Hace un `POST /api/ia/consultar-lote-cliente` enviando el número de teléfono del cliente (que ya conoce por WhatsApp).
4.  **Laravel hace el trabajo seguro:** Tu código en Laravel recibe la petición, hace la consulta controlada con Eloquent (ej. `Cliente::where('telefono', $request->telefono)->with('lotes')->first()`), y le devuelve un JSON limpio a la IA.
5.  **La IA responde:** Toma el JSON y formula la frase: *"Hola, veo en el sistema que tu contrato corresponde al Lote 15 del Proyecto Valle Verde."*

**Ventajas:**
*   **100% Seguro:** La IA nunca ve ni toca la base de datos directamente.
*   **Lógica de Negocio Protegida:** Laravel sigue manejando los permisos, cálculos complejos o validaciones antes de entregar la información.
*   **Esta es la estrategia que se detalla en la sección "Requisitos en nuestro Backend (Laravel)".**

### Enfoque 2: Agentes de Base de Datos (Text-to-SQL)
Este enfoque le da a la IA el "mapa" de tu base de datos y le permite escribir y ejecutar consultas SQL (`SELECT`) directamente. Generalmente se implementa usando el nodo *SQL Agent* de **n8n**.

**El Flujo:**
1.  **El Gerente dice:** *"¿Cuántos contratos firmamos hoy en el proyecto X?"*.
2.  **La IA deduce:** Convierte esa frase a `SELECT COUNT(*) FROM contratos WHERE proyecto = 'X' AND DATE(created_at) = CURDATE()`.
3.  **n8n ejecuta:** n8n toma ese SQL generado, lo ejecuta contra la base de datos (con un usuario de *solo lectura*), y devuelve el resultado a la IA.

**Desventajas y Cuándo Usarlo:**
*   **Riesgo de "Alucinaciones":** La IA podría escribir mal la consulta si la estructura de la base de datos es muy compleja (muchos *JOINs*).
*   **No recomendado para clientes finales:** Es peligroso e ineficiente que la IA genere SQL dinámico cada vez que un cliente pregunta por su lote.
*   **Recomendación:** Este enfoque es útil **solo para un bot interno** (uso administrativo/gerencial) donde un usuario interno quiere hacer preguntas analíticas o estadísticas complejas que no valdría la pena programar como un endpoint fijo de Laravel.

---

## ⚡ Comunicación Interna en Tiempo Real (Laravel Reverb)

Para que los asesores de Atención al Cliente (ATC) vean reflejados los mensajes entrantes de WhatsApp en sus pantallas instantáneamente (sin refrescar la página en `ChatContainer.php`), utilizaremos **Laravel Reverb**.

Dado que contamos con un **servidor dedicado**, Reverb es la opción ideal:
*   **¿Qué es?** Es un servidor de WebSockets oficial y nativo de Laravel (creado como alternativa gratuita y auto-alojada a *Pusher*).
*   **Ahorro y Control:** No hay costos mensuales por límite de conexiones (como en Pusher). Al estar alojado en nuestra propia máquina, los mensajes no viajan a servidores de terceros, incrementando la seguridad y la velocidad.
*   **Consumo Responsable:** Como el servidor de WebSockets corre directamente en nuestra máquina, cada asesor conectado "consumirá" una pequeña fracción de la memoria RAM del servidor para mantener la conexión WebSocket abierta, lo cual está perfectamente cubierto por los recursos de un servidor dedicado.
*   **Implementación:** Se gestiona directamente desde la terminal del servidor ejecutando el servicio secundario `php artisan reverb:start`, y permite disparar eventos a los componentes de Livewire nativamente.

---

## 🤖 Orquestador de IA Opcional: n8n (Self-Hosted)

Si se desea agilizar la creación de flujos conversacionales y evitar programar la lógica de negocio de la IA manualmente en PHP, se recomienda integrar **n8n**. 

**¿Cuál es el rol de n8n vs Laravel?**
* **Laravel (El Cerebro de Datos):** Sigue siendo dueño de la base de datos MySQL. Se encarga de exponer los Endpoints (`/api/ia/consultar-prospecto`).
* **n8n (El Director de Orquesta):** En lugar de escribir en Laravel todo el código de *"Si el cliente dice X, mándale el prompt Y a OpenAI y luego junta el JSON"*, lo haces visualmente en n8n arrastrando cajitas. n8n recibe el mensaje de WhatsApp, llama a OpenAI, y si OpenAI necesita datos, n8n es quien hace la petición HTTP a tu Endpoint de Laravel.

**Ventajas y Detalles de usar n8n Self-Hosted (en servidor dedicado):**
*   **Costos (¡Es Gratis!):** n8n tiene una versión "Community Edition" que es **100% gratuita de por vida** si la alojas tú mismo. A diferencia de su versión de pago en la nube (n8n Cloud) o herramientas como Zapier, al alojarlo en tu servidor no hay límites de tareas ni pagos mensuales.
*   **Instalación requerida (Docker):** Sí, para que sea gratis, tú debes gestionar el software. Se instala usando **Docker** directamente en tu servidor dedicado. Correrá junto a tu proyecto de Laravel pero en un puerto diferente (ej. `tu-dominio.com:5678`).
*   **Privacidad:** Los mensajes fluyen por nuestro propio servidor, no por un Saas externo de terceros.
*   **Libertad Visual:** Permite ajustar el comportamiento del bot (cambiar "System Prompts", agregar documentos RAG) sin tocar el código fuente de `c:\laragon\www\aybar` a cada minuto.

> **Respuesta Rápida:** Sí, n8n es gratis si usas la versión Self-Hosted. Para instalarlo, necesitas ejecutar un contenedor de Docker en tu mismo servidor. Si en el futuro prefieres no lidiar con Docker, n8n ofrece planes de pago en la nube, o puedes programar todo el bot directamente en código PHP (Laravel) sin usar n8n en absoluto.

## 🏗️ Arquitectura Técnica Detallada

Esta sección define el ecosistema de servicios, sus puertos y cómo fluye la información entre ellos para el Asistente EntregaFest.

### 🔌 Mapa de Puertos y Servicios (Servidor Dedicado)

| Servicio | Puerto | Protocolo | Función |
| :--- | :--- | :--- | :--- |
| **Laravel ERP** | `80` / `443` | HTTP / HTTPS | Backend principal y fuente de datos (MySQL). |
| **Laravel Reverb** | `8080` | WS / WSS | Sincronización en tiempo real para el chat administrativo. |
| **n8n (Docker)** | `5678` | HTTP | Orquestador de flujos de IA para WhatsApp. |
| **MySQL** | `3306` | TCP | Base de datos (acceso interno para Laravel y n8n). |

---

### 💬 Flujo 1: Chat de WhatsApp
**Ruta:** Meta ↔️ n8n ↔️ OpenAI ↔️ Laravel

1.  **Entrada:** El cliente envía un mensaje a través de WhatsApp.
2.  **Recepción:** Meta (WhatsApp Cloud API) envía un Webhook al **n8n** (que corre localmente en el puerto `5678`).
3.  **Análisis (n8n):**
    *   n8n recibe el mensaje y el número de teléfono.
    *   n8n consulta a la **API de Laravel** (`/api/ia/cliente`) para saber quién es y qué lotes tiene.
    *   n8n envía el mensaje + los datos del cliente a **OpenAI**.
4.  **Respuesta:** OpenAI genera la respuesta y n8n la envía de vuelta al cliente mediante la API de Meta.
5.  **Sincronización:** n8n notifica a Laravel que hay un nuevo mensaje. Laravel emite un evento vía **Reverb (puerto 8080)** para que el asesor vea el mensaje en su panel sin refrescar.

---

### 📞 Flujo 2: Llamada de Voz (Real-Time)
**Ruta:** Twilio ↔️ Vapi.ai ↔️ OpenAI ↔️ Laravel

1.  **Entrada:** El cliente llama al número de Twilio.
2.  **Conexión:** Twilio transfiere el flujo de audio a **Vapi.ai** mediante WebSockets (SIP/WebStream).
3.  **Inteligencia (Vapi.ai + OpenAI):**
    *   Vapi transcribe el audio a texto en milisegundos.
    *   Vapi envía ese texto a **OpenAI** para decidir qué responder.
    *   Si el cliente pregunta por su deuda o proyecto, **Vapi hace una petición HTTP "Tool Call"** a los Endpoints de Laravel en el puerto `443`.
4.  **Salida:** Vapi convierte la respuesta de texto de OpenAI en voz (TTS) y la envía de vuelta a Twilio para que el cliente la escuche.
5.  **Cierre:** Al colgar, Vapi envía un resumen de la llamada a Laravel para guardarlo en el historial del prospecto.

---

### 🧩 Resumen de Herramientas
*   **Twilio:** Troncal telefónica (El "Chip" virtual).
*   **Meta WAPI:** Conector oficial de mensajes.
*   **Vapi.ai:** El "Cerebro de Voz" que gestiona la latencia y el audio.
*   **n8n (Local):** El "Director de Orquesta" que une WhatsApp con Laravel y OpenAI.
*   **Laravel Reverb:** El "Mensajero" interno para que el Staff vea todo en tiempo real.
*   **OpenAI (GPT-4o):** El motor de lenguaje que genera el razonamiento.

---

### 🚀 Ventaja de esta Configuración
Al tener **n8n y Reverb** corriendo en tu propio servidor dedicado, la latencia es mínima y los costos de orquestación son cero (solo pagas por el consumo de tokens de OpenAI y minutos de Vapi/Twilio). Tu base de datos siempre está protegida detrás de los Endpoints de Laravel.


Solución en Laravel: Tendrías que configurar Queue Workers, Redis, y Jobs para procesar todo en segundo plano. No es imposible, pero suma mucha complejidad técnica de infraestructura.