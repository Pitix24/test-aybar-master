# Fase 5 - Drop Final de Campos Legacy (Libro Reclamaciones)

Fecha: 2026-04-20
Estado: Cerrada
Objetivo de fase: eliminar del esquema y del código los campos legacy y `nota_fuente_*` ya deprecados.

## 1) Implementacion tecnica
Se implementaron cambios de cierre en modelo, Livewire, vistas y migraciones.

### 1.1 Modelo y capa de aplicacion
Archivo:
- `app/Models/LibroReclamacion/LibroReclamacion.php`

Acciones:
- Se retiraron del `fillable` los campos legacy eliminados.
- Se retiro `nota_fuente_*` del `fillable` y `casts`.
- Se elimino la compatibilidad residual de `booted()->creating(...)` que escribia columnas legacy.
- Se eliminaron metodos de resolucion de nota/origen legacy que dependian de columnas retiradas.

### 1.2 Componentes ERP
Archivos:
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php`
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`

Acciones:
- Se removio persistencia de `nota_fuente_titulo` y `nota_fuente_fecha`.
- Se limpiaron estados/props y metodos residuales asociados a nota fuente.

### 1.3 Vistas ERP
Archivos:
- `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php`
- `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-editar.blade.php`

Acciones:
- Se retiro UI de nota fuente (titulo/fecha/contenido).
- Se elimino dependencia a metodos legacy de modelo (`esOrigenErp`, `tituloNotaFuenteResuelto`, `contenidoNotaFuenteResuelto`).
- Se mantuvo visualizacion de observaciones internas.

## 2) Migraciones de esquema
### 2.1 Baseline consolidada
Archivo:
- `database/migrations/2026_02_16_211526_create_libro_reclamacions_table.php`

Accion:
- Se removieron del create base las columnas legacy y `nota_fuente_*` para instalaciones nuevas limpias.

### 2.2 Migracion destructiva para entornos existentes
Archivo:
- `database/migrations/2026_04_20_190000_drop_legacy_columns_from_libro_reclamacions_table.php`

Columnas eliminadas:
- `nombre`
- `apellido_paterno`
- `apellido_materno`
- `domicilio`
- `telefono`
- `email`
- `tipo_documento`
- `numero_documento`
- `nota_fuente_titulo`
- `nota_fuente_fecha`

## 3) Ejecucion y verificacion
Comando ejecutado:
- `php artisan migrate --path=database/migrations/2026_04_20_190000_drop_legacy_columns_from_libro_reclamacions_table.php --force`

Estado en historial:
- `2026_04_20_190000_drop_legacy_columns_from_libro_reclamacions_table` aparece como Ran.

Chequeo de esquema posterior:
- `nombre=0`
- `nota_fuente_titulo=0`
- `cliente_nombre=1`

Interpretacion:
- Legacy removido correctamente.
- Contrato canonico `cliente_*` permanece intacto.

## 4) Pruebas
Prueba focal ejecutada:
- `tests/Feature/Livewire/LibroReclamacionFase5Test.php`

Resultado:
- 5 pruebas aprobadas
- 0 fallas

## 5) Resultado de fase
Fase 5 cerrada con exito:
- Esquema limpio sin columnas legacy ni `nota_fuente_*`.
- Codigo alineado al contrato canonico.
- Validacion tecnica y pruebas en verde.
