<?php

namespace App\Models\LibroReclamacion;

use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use App\Services\LibroReclamacion\LibroReclamacionNumeroService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibroReclamacion extends Model
{
    use SoftDeletes;

    protected $table = 'libro_reclamacions';
    protected $primaryKey = 'ticket';

    protected $fillable = [
        'unidad_negocio_id',
        'proyecto_id',
        'cliente_id',
        'gestor_id',
        'serie',
        'numero_reclamo',
        'codigo_ticket',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'domicilio',
        'telefono',
        'email',
        'tipo_documento',
        'numero_documento',
        'tipo_bien_contratado',
        'monto_reclamado',
        'descripcion',
        'tipo_pedido',
        'detalle',
        'pedido',
        'conformidad',
        'observaciones',
        'fecha_respuesta',
        'archivo_1',
        'archivo_2',
        'archivo_3',
        'archivo_4',
        'leido',
        'estado',
    ];

    protected $casts = [
        'conformidad' => 'boolean',
        'leido' => 'boolean',
        'fecha_respuesta' => 'datetime',
        'monto_reclamado' => 'decimal:2',
    ];

    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public static function generarTicket(int $unidadNegocioId, string $razonSocial): array
    {
        return app(LibroReclamacionNumeroService::class)->generar($unidadNegocioId, $razonSocial);
    }
}