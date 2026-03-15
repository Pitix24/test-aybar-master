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
3.  **Registro de Actividad:** Cada vez que el bot (por chat o voz) finalice una interacción, debe hacer un POST a nuestro CRM para guardar el "Log" o "Resumen de la llamada/chat" en el historial del **Prospecto** respectivo.

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

Dado que contamos con un **servidor dedicado**, la opción ideal y lógica es usar la versión **Self-Hosted (100% Gratuita)**:
*   **Instalación:** Se despliega fácilmente mediante **Docker** en la misma máquina donde reside nuestro proyecto Laravel, operando en un puerto separado (ej. 5678).
*   **Ahorro Económico:** Al auto-alojarlo con nuestros propios recursos (RAM/CPU), es gratuito de por vida. Evitamos cualquier pago mensual o restricciones en la cantidad de envíos/ejecuciones.
*   **Privacidad y Seguridad:** Los datos sensibles de los clientes y transacciones fluyen directamente desde la API oficial de Meta hacia nuestro propio servidor y hacia la IA, sin ser retenidos por servidores de orquestación de terceros.
*   **Delegación Operativa:** Permite que cualquier cambio en el comportamiento del chatbot o conexión a nuevos documentos (como PDFs para RAG) se realice mediante un panel cien por ciento visual, liberando a los desarrolladores de modificar `c:\laragon\www\aybar` a cada instante.
