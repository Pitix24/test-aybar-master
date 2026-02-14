<?php

use App\Livewire\Erp\Inicio\InicioLivewire;
use Illuminate\Support\Facades\Route;

//Route::get('/', InicioLivewire::class)->name('home');

Route::get('/perfil', InicioLivewire::class)->name('home');

require __DIR__ . '/atc.php';
require __DIR__ . '/cita.php';
require __DIR__ . '/sistema.php';
require __DIR__ . '/usuario.php';
require __DIR__ . '/letra.php';
require __DIR__ . '/backoffice.php';
require __DIR__ . '/negocio.php';

/*
--------------------------------------------------------------------------
PERMISOS DEL ERP
--------------------------------------------------------------------------
Convención: recurso.accion
MODULO
1. modulo-negocio.ver

UNIDAD NEGOCIO
1. unidad-negocio.navegacion
2. unidad-negocio.ver
3. unidad-negocio.crear
4. unidad-negocio.editar
5. unidad-negocio.eliminar
6. unidad-negocio.exportar

GRUPO PROYECTO
1. grupo-proyecto.navegacion
2. grupo-proyecto.ver
3. grupo-proyecto.crear
4. grupo-proyecto.editar
5. grupo-proyecto.eliminar
6. grupo-proyecto.exportar

PROYECTO
1. proyecto.navegacion
2. proyecto.ver
3. proyecto.crear
4. proyecto.editar
5. proyecto.eliminar
6. proyecto.exportar

SEDE
1. sede.navegacion
2. sede.ver
3. sede.crear
4. sede.editar
5. sede.eliminar
6. sede.exportar

AREA
1. area.navegacion
2. area.ver
3. area.crear
4. area.editar
5. area.eliminar
6. area.exportar
7. area.ver-usuarios
8. area.ver-solicitudes
9. area.agregar-usuarios
10. area.agregar-solicitudes
11. area.eliminar-usuarios
12. area.eliminar-solicitudes
13. area.exportar-usuarios
14. area.exportar-solicitudes
*/
