# Fase 1 - Matriz Final y Reglas de Backfill (Libro Reclamaciones)

Fecha: 2026-04-20
Estado: Implementado (definicion funcional y tecnica)
Base de verificacion: esquema vigente + uso real en Livewire Web/ERP + modelo

## 1) Resultado de Fase 1
Se cierra una matriz unica para la migracion faseada de columnas legacy hacia contrato canonico.

Decision principal:
- Contrato canonico real del esquema actual: `cliente_tipo_documento`, `cliente_documento`, `cliente_nombre`, `cliente_email`, `cliente_celular`, `cliente_direccion`.

## 2) Contrato Canonico Confirmado
Columnas canonicas (source of truth al cierre de Fase 4):
- `cliente_tipo_documento`
- `cliente_documento`
- `cliente_nombre`
- `cliente_email`
- `cliente_celular`
- `cliente_direccion`

Notas:
- El esquema actual NO contiene columnas canonicas separadas para departamento/provincia/distrito/referencia en `cliente_*`.
- El esquema actual SI contiene `domicilio` (legacy) y `cliente_direccion` (canonico).

## 3) Matriz Legacy -> Canonico
Regla general de precedencia:
1. Si el campo canonico tiene dato util, se respeta.
2. Si el campo canonico esta vacio y legacy tiene dato util, se completa desde legacy.
3. Nunca se sobreescribe un canonico poblado con valor legacy.

Definicion de "dato util":
- No null
- No cadena vacia tras `trim()`
- No placeholders tecnicos: `-`, `N/D`, `NO DEFINIDO`

### 3.1 Identidad y contacto
| Legacy | Canonico destino | Regla de transformacion | Estado Fase 1 |
|---|---|---|---|
| `tipo_documento` | `cliente_tipo_documento` | `UPPER(TRIM())`; validar en catalogo permitido y fallback a `NO_DEFINIDO` si invalido | Aprobado |
| `numero_documento` | `cliente_documento` | `TRIM()` | Aprobado |
| `nombre` + `apellido_paterno` + `apellido_materno` | `cliente_nombre` | concatenar con espacio, limpiar dobles espacios, `TRIM()` | Aprobado |
| `email` | `cliente_email` | `LOWER(TRIM())` | Aprobado |
| `telefono` | `cliente_celular` | `TRIM()` | Aprobado |
| `domicilio` | `cliente_direccion` | `TRIM()` | Aprobado |

### 3.2 Campos de nota de origen
| Campo | Accion planificada | Estado Fase 1 |
|---|---|---|
| `nota_fuente_titulo` | Mantener temporalmente hasta Fase 5; sigue en uso ERP crear/editar/ver | Aprobado |
| `nota_fuente_fecha` | Mantener temporalmente hasta Fase 5; sigue en uso ERP crear/editar/ver | Aprobado |
| `nota_fuente_contenido` | No existe como columna fisica en esquema vigente; no aplica drop | Cerrado |

## 4) Regla de Backfill (especificacion)
Para cada registro de `libro_reclamacions`:
- `cliente_tipo_documento` se completa desde `tipo_documento` si canonico vacio.
- `cliente_documento` se completa desde `numero_documento` si canonico vacio.
- `cliente_nombre` se completa desde composicion de nombre/apellidos si canonico vacio.
- `cliente_email` se completa desde `email` si canonico vacio.
- `cliente_celular` se completa desde `telefono` si canonico vacio.
- `cliente_direccion` se completa desde `domicilio` si canonico vacio.

Control de conflictos:
- Si canonico y legacy tienen dato util diferente, NO se reemplaza canonico.
- Registrar conteo de conflictos para revision manual.

## 5) Criterios de aceptacion de Fase 1
- Matriz legacy -> canonico cerrada y sin ambiguedad.
- Regla de precedencia documentada.
- Definicion de `nota_fuente_*` y estado de cada campo cerrados.
- Lista de datos placeholders que NO deben migrarse cerrada.

## 6) Insumos para Fase 2 y Fase 3
Fase 2 (codigo):
- Web/ERP deben escribir solo en columnas canonicas para identidad/contacto.
- Mantener compatibilidad temporal para lectura historica durante transicion.

Fase 3 (datos):
- Ejecutar backfill con idempotencia y reporte de:
  - total evaluados
  - total actualizados
  - total conflictos
  - total pendientes sin dato util

## 7) Referencias del repositorio
- `database/migrations/2026_02_16_211526_create_libro_reclamacions_table.php`
- `app/Models/LibroReclamacion/LibroReclamacion.php`
- `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php`
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php`
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`
- `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php`
- `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-lista.blade.php`

## 8) Proximo paso
Iniciar Fase 2 con cambio de escrituras en componentes Web/ERP para dejar de persistir campos legacy de identidad/contacto en nuevas altas/ediciones.
