<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaFestProveedor extends Model
{
    protected $table = 'entrega_fest_proveedores';
    protected $fillable = ["entrega_fest_id", "nombre_comercial", "contacto_nombre", "contacto_telefono", "servicio_tipo", "h_llegada", "h_montaje", "h_show", "h_desmontaje", "estado", "observaciones"];

    public function requerimientos()
    {
        return $this->hasMany(EntregaFestProveedorRequerimiento::class, "proveedor_id");
    }

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }
}
