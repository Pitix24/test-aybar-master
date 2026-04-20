# Plan Faseado de Deprecacion de Campos Duplicados en Libro Reclamaciones

Fecha: 2026-04-20
Estado: En ejecucion (Fase 1 y Fase 2 cerradas)
Estrategia: Faseada segura

## 1) Objetivo
Reducir deuda tecnica en `libro_reclamacions` eliminando columnas duplicadas (legacy) y consolidando el modelo de datos en el contrato canonico `cliente_*`, minimizando riesgo de regresiones en Web/ERP.

## 2) Alcance
Incluye:
- Migracion de escrituras a `cliente_*`.
- Compatibilidad temporal de lectura para historicos.
- Backfill verificable legacy -> canonico.
- Corte de compatibilidad.
- Eliminacion final de columnas legacy y `nota_fuente_*` (segun validacion funcional final).

No incluye:
- Rediseno funcional completo del modulo.
- Cambios de UX fuera de los formularios/lecturas afectados por la depuracion.

## 3) Contrato Canonico
Fuente de verdad para datos del reclamante:
- `cliente_tipo_documento`
- `cliente_documento`
- `cliente_nombre`
- `cliente_email`
- `cliente_celular`
- `cliente_direccion`

Campos legacy candidatos a deprecacion:
- `nombre`
- `apellido_paterno`
- `apellido_materno`
- `tipo_documento`
- `numero_documento`
- `telefono`
- `email`
- `domicilio`
- `nota_fuente_titulo`
- `nota_fuente_fecha`

## 4) Fases de Implementacion
### Fase 1: Inventario final y reglas
- Cerrar lista definitiva de columnas a retirar.
- Confirmar regla de precedencia para backfill:
  - Si `cliente_*` tiene valor, se respeta.
  - Si `cliente_*` esta vacio, se completa desde legacy.
- Definir criterio funcional de `nota_fuente_*`:
  - Retiro total, o
  - Sustitucion por un unico campo de observacion.

Entregable:
- Matriz final de columnas + reglas de migracion de datos.

### Fase 2: Escritura canonica (sin drop)
- Ajustar Livewire Web/ERP para persistir solo `cliente_*`.
- Evitar nuevas escrituras en legacy.
- Mantener lectura compatible temporal para historicos.

Entregable:
- Flujo de alta/edicion funcionando sin generar deuda nueva.

### Fase 3: Backfill historico
- Crear migracion/script de datos para completar `cliente_*` desde legacy cuando corresponda.
- Registrar metricas antes/despues.

Entregable:
- `cliente_*` completo en historicos segun reglas.

### Fase 4: Corte de compatibilidad
- Retirar fallback legacy en modelo/vistas.
- Limpiar validaciones/payloads residuales.

Entregable:
- Lecturas y escrituras 100% en contrato canonico.

### Fase 5: Drop final
- Crear migracion destructiva para eliminar columnas legacy y `nota_fuente_*` aprobadas.
- Eliminar indices/constraints asociados si existieran.

Entregable:
- Esquema limpio sin rastros legacy.

## 5) Archivos Objetivo (primera pasada)
- `app/Models/LibroReclamacion/LibroReclamacion.php`
- `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php`
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php`
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`
- `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php`
- `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-lista.blade.php`
- `database/migrations/2026_02_16_211526_create_libro_reclamacions_table.php`
- `database/migrations/*` (nuevas migraciones de backfill y drop)
- `tests/Feature/Livewire/LibroReclamacionFase5Test.php`

## 6) Riesgos y Mitigaciones
Riesgo: romper registros historicos que aun dependan de legacy.
Mitigacion: fase de compatibilidad + backfill con metricas antes de drop.

Riesgo: discrepancias entre legacy y `cliente_*`.
Mitigacion: regla de precedencia explicita y reporte de conflictos.

Riesgo: regresiones en formularios Web/ERP.
Mitigacion: pruebas focalizadas + validacion manual de flujos clave.

## 7) Verificacion Tecnica
Checklist minimo por fase:
1. Ejecutar pruebas focalizadas de Libro Reclamaciones.
2. Validar creacion/edicion en Web y ERP.
3. Verificar en BD porcentaje de registros sin `cliente_*` criticos.
4. Confirmar ausencia de nuevas escrituras legacy.

Comandos base sugeridos:
- `php artisan test tests/Feature/Livewire/LibroReclamacionFase5Test.php`
- `php artisan test`

## 8) Criterios de Cierre
- No hay escrituras en columnas legacy.
- Historicos resueltos en `cliente_*`.
- Fallback eliminado del codigo.
- Migracion destructiva aplicada sin errores.
- Pruebas y validacion manual en verde.

## 9) Rollback
- Si falla Fase 2-4: revertir cambios de codigo y mantener columnas.
- Si falla Fase 5: rollback de migracion destructiva y reactivar fallback temporal.
- Mantener respaldo previo del esquema/datos antes del drop final.

## 10) Proximo Paso Operativo
Iniciar Fase 3 con backfill historico idempotente, metricas de conflictos y reporte de pendientes.

## 11) Evidencia de avance
- Entregable de Fase 1 completado en:
  - `docs/FASE_1_MATRIZ_REGLAS_BACKFILL_LIBRO_RECLAMACIONES.md`
- Cierre de Fase 2 (escritura canonica) documentado en:
  - `docs/FASE_2_ESCRITURA_CANONICA_LIBRO_RECLAMACIONES.md`
