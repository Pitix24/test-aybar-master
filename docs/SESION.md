# Plan de Monitoreo y Análisis de Tráfico (Tabla Sessions)

Este documento detalla cómo aprovechar la información almacenada en la tabla `sessions` de Laravel para obtener métricas de uso, seguridad y optimización del sistema.

---

## 1. Diccionario de Datos de la Tabla `sessions`

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| **id** | String | Identificador único de la sesión (almacenado en la cookie del navegador). |
| **user_id** | BigInt (NULL) | ID del usuario si está logueado. Si es `NULL`, es un visitante anónimo o bot. |
| **ip_address** | String | Dirección IP desde donde se realiza la conexión. |
| **user_agent** | Text | Información técnica del dispositivo: navegador, OS y versión. |
| **payload** | Text | Datos cifrados de la sesión (tokens, variables temporales, mensajes flash). |
| **last_activity** | Integer | Timestamp (Unix) del último movimiento del usuario. |

---

## 2. Plan de Reportes Técnicos

### A. Reporte de Actividad en Tiempo Real
Permite saber cuánta carga real tiene el servidor en un momento dado.
- **Métrica**: Usuarios activos en los últimos 5 minutos.
- **Implementación (Eloquent)**:
  ```php
  $activeUsers = DB::table('sessions')
      ->where('last_activity', '>=', now()->subMinutes(5)->getTimestamp())
      ->count();
  ```

### B. Análisis de Audiencia (Registrados vs. Anónimos)
Identifica si el tráfico es de personal interno o de visitantes externos/bots.
- **SQL**:
  ```sql
  SELECT 
      CASE WHEN user_id IS NULL THEN 'Anónimo' ELSE 'Registrado' END as tipo,
      COUNT(*) as total
  FROM sessions
  GROUP BY tipo;
  ```

### C. Auditoría de Seguridad (Multisesión)
Detecta si una cuenta de usuario está siendo compartida o ha sido vulnerada.
- **Objetivo**: Listar usuarios que tienen sesiones abiertas desde diferentes direcciones IP.
- **SQL**:
  ```sql
  SELECT user_id, COUNT(DISTINCT ip_address) as conteo_ips
  FROM sessions
  WHERE user_id IS NOT NULL
  GROUP BY user_id
  HAVING conteo_ips > 1;
  ```

### D. Optimización de UI (Dispositivos)
Analiza si se debe dar prioridad al desarrollo Móvil o Escritorio (Desktop).
- **Métrica**: Extracción de sistema operativo desde `user_agent`.
- **Análisis**: Usuarios con "Android/iPhone" vs "Windows/Macintosh".

---

## 3. Implementación de un Dashboard de Control

Se recomienda crear una vista para el administrador que incluya las siguientes tablas:

### Tabla 1: Usuarios actualmente en línea
| Usuario (Email/Nombre) | IP | Última Actividad | Dispositivo |
| :--- | :--- | :--- | :--- |
| Juan Pérez | 192.168.1.5 | Hace 2 min | Windows / Chrome |
| Maria Lopez | 180.20.10.5 | Hace 10 seg | Android / Samsung |

### Tabla 2: Mapa de Tráfico por IP
Utilizar la columna `ip_address` con servicios como `ip-api.com` para visualizar:
1. Ciudad / País de origen.
2. Proveedor de Internet (ISP).

---

## 4. Notas de Mantenimiento

1.  **Limpieza (Garbage Collection):** Laravel limpia automáticamente las sesiones viejas basándose en la configuración `SESSION_LIFETIME` en el archivo `.env`.
2.  **Rendimiento:** Con 600 registros el impacto es nulo. Si la tabla llega a >50,000 registros, se recomienda indexar el campo `last_activity`.
3.  **Privacidad:** El almacenamiento de IPs debe estar alineado con las políticas de privacidad de la empresa.
