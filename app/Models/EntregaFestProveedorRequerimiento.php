<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaFestProveedorRequerimiento extends Model
{
    protected $table = 'entrega_fest_proveedor_requerimientos';
    protected $fillable = ["proveedor_id", "requerimiento", "esta_cubierto"];

    public function proveedor()
    {
        return $this->belongsTo(EntregaFestProveedor::class, "proveedor_id");
    }
}
