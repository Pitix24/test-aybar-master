# Orquestación de Tareas con n8n (Alternativa a Redis/Queue Workers)

Esta guía detalla la metodología para ejecutar procesos pesados o masivos (Jobs) de Laravel utilizando **n8n** como motor de ejecución. Esto permite liberar a Laravel de la carga de procesamiento, evitar fallos por "Timeouts" y eliminar la necesidad de configurar infraestructura compleja como Redis o Supervisor.

## 🚀 El Concepto: "n8n Loop Orchestration"

En lugar de que Laravel gestione una cola interna, Laravel actúa como un **Cerebro de Datos** que expone micro-tareas, y n8n actúa como el **Brazo Ejecutor** que gestiona los tiempos, reintentos y el flujo masivo.

---

## 💬 Gestión de WhatsApp (Webhooks Externos)

En esta arquitectura, el Webhook de Meta (WhatsApp) ya **no reside en Laravel**. Esto elimina la carga de validar firmas, procesar JSONs complejos y gestionar colas (Jobs) en PHP.

### La Nueva Ruta del Mensaje:
1.  **Recepción:** Meta envía el mensaje al Webhook de **n8n**.
2.  **Procesamiento:** n8n limpia los datos, identifica al cliente y consulta a la IA (OpenAI).
3.  **Acción en Laravel:** n8n llama a un endpoint de tu API Laravel solo para **Sincronizar Datos**.

### ¿Cómo "entra" el mensaje a Laravel?
Laravel deja de ser el receptor del tráfico de Meta y se convierte en un **Receptor de Logs de n8n**.

*   **Endpoint de Sincronización:** `POST /api/whatsapp/sync-message`
*   **Función de Laravel:** Recibe un JSON simplificado desde n8n (ej: `{"telefono": "...", "mensaje": "...", "direccion": "entrada"}`) y lo inserta directamente en las tablas `whatsapp_conversaciones` y `whatsapp_mensajes`.
*   **Notificación Real-Time:** Laravel dispara su evento de **Reverb** para que el Staff vea el mensaje en el ERP.

---

## 🛠️ Estructura del Trabajo

Para cada "Job" que queramos migrar a este sistema, seguiremos este patrón de 3 pasos:

### 1. El Cerebro (Laravel - Controller)
Creamos un controlador encargado de exponer la tarea.

```php
// app/Http/Controllers/Api/Ia/JobOrchestratorController.php

public function getPendientesCavali() {
    // Retorna solo una lista de IDs para procesar
    return SolicitudDigitalizarLetra::where('estado_id', 'ENVIADO')->pluck('id');
}

public function procesarCavali(Request $request, $id) {
    // Contiene la lógica exacta de UN SOLO registro
    // Esto garantiza que la petición HTTP termine en milisegundos
    $solicitud = SolicitudDigitalizarLetra::findOrFail($id);
    $resultado = (new CavaliService())->procesar($solicitud);
    
    return response()->json(['status' => 'success', 'data' => $resultado]);
}
```

### 2. El Ejecutor (n8n - Workflow)
Diseñamos un flujo en n8n con los siguientes nodos:

1.  **Schedule Trigger:** Define cuándo inicia (ej. todos los días a las 2 AM).
2.  **HTTP Request (GET):** Llama a `/api/jobs/cavali/pendientes`.
3.  **Split In Batches:** Toma la lista de IDs y los divide (ej. de 1 en 1).
4.  **HTTP Request (POST):** Llama a `/api/jobs/cavali/procesar/{{$json.id}}`. 
    *   *Nota: n8n esperará a que cada petición termine antes de pasar a la siguiente.*
5.  **Wait (Opcional):** Añade un delay (ej. 1 seg) para no saturar APIs externas o tu propia base de datos.

### 3. El Reporte (Notificaciones)
Agregamos al final del flujo de n8n:
*   **Nodo WhatsApp:** Envía un mensaje al administrador con el resumen: *"Proceso Cavali terminado: 150 procesados, 0 fallidos"*.

---

## 🔄 Resumen de Responsabilidades (Bidireccional)

### n8n (El Portero y Mensajero)
*   Recibe el Webhook de Meta (WhatsApp).
*   Valida la firma y seguridad (X-Hub-Signature).
*   Pregunta a la IA (OpenAI).
*   Gestiona los "Wait" y "Sleep" para envíos masivos de 2,000+ mensajes.
*   Informa a Laravel de los resultados.

### Laravel (El Almacén y la Interfaz)
*   **Dueño de la BD:** Registra los mensajes ya procesados por n8n.
*   **Lógica de Negocio:** Expone endpoints para que n8n sepa, por ejemplo, los lotes de un cliente o sus deudas.
*   **Staff UI:** Proporciona la pantalla donde los humanos de Aybar leen y responden manualmente.

---

## ✅ Ventajas de este Modelo

| Característica | Con Redis/Queue Workers | Con n8n Orchestrator |
| :--- | :--- | :--- |
| **Configuración** | Difícil (Redis, Supervisor, Crons) | Fácil (Interfaz Visual) |
| **Debugging** | Ver archivos de Log de texto | Ver el flujo visual en tiempo real |
| **Timeouts** | Riesgo alto en procesos de 1h+ | Imposible (n8n gestiona el tiempo) |
| **Memoria RAM** | Alta (Laravel carga todo el proceso) | Mínima (Laravel procesa de 1 en 1) |
| **Masivos (SMS/WA)** | Difícil de controlar la cadencia | Fácil (Nodo "Wait" entre envíos) |

---

## 🚦 Cuándo usar cada uno

*   **Usa n8n:** Para envíos masivos de WhatsApp/SMS, integraciones con APIs externas (Cavali, SUNAT, Meta) y tareas programadas que requieren visibilidad.
*   **Usa Laravel nativo:** Solo para tareas internas inmediatas y muy sencillas que no requieran seguimiento visual.

---

## 🛠️ Próximos Pasos en Aybar
1.  Crear `JobOrchestratorController` en Laravel para mapear las tareas actuales.
2.  Importar en n8n los flujos base de "Loop Masivo".
3.  Desactivar los Crons manuales del servidor Hostinger.
