<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteDocumento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cliente_documentos';

    protected $fillable = [
        'proyecto_id',
        'tipo_cliente_documentos_id',
        'titulo',
        'descripcion',
        'icono',
        'clicks',
        'solo_lectura',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'solo_lectura' => 'boolean',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoClienteDocumento::class, 'tipo_cliente_documentos_id');
    }

    public function archivoPdf()
    {
        return $this->morphOne(MarketingArchivo::class, 'archivable');
    }
}
