<?php

namespace App\Models\LibroReclamacion;

use App\Models\Ticket;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use App\Services\LibroReclamacion\LibroReclamacionNumeroService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class LibroReclamacion extends Model
{
    use SoftDeletes;

    protected $table = 'libro_reclamacions';
    protected $primaryKey = 'ticket';

    protected $fillable = [
        'unidad_negocio_id',
        'proyecto_id',
        'ticket_id',
        'manzana',
        'lote',
        'cliente_id',
        'gestor_id',
        'serie',
        'numero_reclamo',
        'codigo_ticket',
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
        'codigo',
        'clasificacion',
        'cliente_tipo_documento',
        'cliente_documento',
        'cliente_nombre',
        'cliente_email',
        'cliente_celular',
        'cliente_direccion',
        'es_cliente_menor',
        'representante_legal_nombre',
        'representante_legal_apellido_paterno',
        'representante_legal_apellido_materno',
        'asunto',
        'lotes',
        'assigned_at',
        'observaciones_internas',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'ticket_id' => 'integer',
        'conformidad' => 'boolean',
        'leido' => 'boolean',
        'es_cliente_menor' => 'boolean',
        'fecha_respuesta' => 'datetime',
        'monto_reclamado' => 'decimal:2',
        'lotes' => 'array',
        'assigned_at' => 'datetime',
    ];

    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function ticketRelacionado()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function eliminador()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function estadoActualNombre(): string
    {
        $nombreTicket = (string) ($this->ticketRelacionado?->estado?->nombre ?? '');

        if ($nombreTicket !== '') {
            return $nombreTicket;
        }

        $clasificacion = strtoupper(trim((string) $this->clasificacion));

        if ($clasificacion === 'NO_PROCEDE') {
            return 'NO PROCEDE';
        }

        if ($clasificacion === 'PENDIENTE_REVISION') {
            return 'PENDIENTE VERIFICACION';
        }

        $nombre = 'N/D';

        return str_replace('_', ' ', $nombre);
    }

    protected static function booted()
    {
        static::saving(function ($reclamacion) {
            $codigo = trim((string) ($reclamacion->codigo_ticket ?: $reclamacion->codigo ?: ''));

            if ($codigo === '' && $reclamacion->unidad_negocio_id) {
                $ticket = self::generarTicket($reclamacion->unidad_negocio_id);
                $codigo = (string) ($ticket['codigo_ticket'] ?? '');
            }

            if ($codigo !== '') {
                $reclamacion->codigo_ticket = $codigo;
                $reclamacion->codigo = $codigo;
            }
        });

        static::creating(function ($reclamacion) {
            if (!$reclamacion->created_by) {
                $reclamacion->created_by = Auth::id();
            }
        });
        static::updating(function ($reclamacion) {
            $reclamacion->updated_by = Auth::id();
        });
    }

    public static function generarTicket(?int $unidadNegocioId): array
    {
        return app(LibroReclamacionNumeroService::class)->generar($unidadNegocioId);
    }
}
