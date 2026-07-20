<?php
$m = \App\Models\Menu::find(80);
if($m){
    $td = \App\Models\Menu::create(['parent_id'=>80, 'nombre'=>'Tipos de Documento', 'icono'=>'fa-solid fa-tags', 'nivel'=>2, 'orden'=>0, 'permiso'=>'tipo_cliente_documento.navegacion']);
    \App\Models\Menu::create(['parent_id'=>$td->id, 'nombre'=>'Lista', 'ruta'=>'tipo-cliente-documento.lista', 'icono'=>'', 'nivel'=>3, 'orden'=>0, 'permiso'=>'tipo_cliente_documento.lista']);
    \App\Models\Menu::create(['parent_id'=>$td->id, 'nombre'=>'Crear', 'ruta'=>'tipo-cliente-documento.crear', 'icono'=>'', 'nivel'=>3, 'orden'=>0, 'permiso'=>'tipo_cliente_documento.crear']);
    
    $cd = \App\Models\Menu::create(['parent_id'=>80, 'nombre'=>'Documentos Cliente', 'icono'=>'fa-solid fa-file-pdf', 'nivel'=>2, 'orden'=>0, 'permiso'=>'cliente_documento.navegacion']);
    \App\Models\Menu::create(['parent_id'=>$cd->id, 'nombre'=>'Lista', 'ruta'=>'cliente-documento.lista', 'icono'=>'', 'nivel'=>3, 'orden'=>0, 'permiso'=>'cliente_documento.lista']);
    \App\Models\Menu::create(['parent_id'=>$cd->id, 'nombre'=>'Crear', 'ruta'=>'cliente-documento.crear', 'icono'=>'', 'nivel'=>3, 'orden'=>0, 'permiso'=>'cliente_documento.crear']);
}
echo "Done";
