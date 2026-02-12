<?php

namespace App\Livewire\Cliente\Lote;

use App\Models\EstadoSolicitudEvidenciaPago;
use App\Models\EvidenciaPago;
use App\Models\Proyecto;
use App\Models\SolicitudEvidenciaPago;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use OpenAI;

class AdjuntarVoucherPago extends Component
{
    use WithFileUploads;

    public $lote;
    public $cuota;

    public $imagen;
    public $datos = [];
    public $procesando = false;

    public $empresas, $unidad_negocio_id = '';
    public $proyectos = [], $proyecto_id = '';

    protected function rules()
    {
        return [
            'imagen' => 'required|image|max:4096',
            'datos.numero' => 'required',
            'datos.banco' => 'required',
            'datos.monto' => 'required',
            'datos.fecha' => 'required',
            'unidad_negocio_id' => 'required',
            'proyecto_id' => 'required',
        ];
    }

    protected $validationAttributes = [
        'unidad_negocio_id' => 'Razón Social',
        'proyecto_id' => 'Proyecto',
        'datos.numero' => 'N° operación',
        'datos.banco' => 'Banco',
        'datos.monto' => 'Monto',
        'datos.fecha' => 'Fecha',
    ];

    public function mount($cuota, $lote)
    {
        $this->cuota = $cuota;
        $this->lote = $lote;

        $this->empresas = UnidadNegocio::all();
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';

        if ($value) {
            $this->loadProyectos();
        }
    }

    public function loadProyectos()
    {
        if (!is_null($this->unidad_negocio_id)) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->get();
        }
    }

    public function procesarImagen()
    {
        $this->validate([
            'imagen' => 'required|image|max:4096',
        ]);

        $this->procesando = true;

        try {
            // Convertir imagen a base64
            $imageData = base64_encode(file_get_contents($this->imagen->getRealPath()));

            $client = OpenAI::client(config('services.openai.key'));

            // --- PETICIÓN CORRECTA ---
            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                "type" => "text",
                                "text" => "Extrae los datos EXACTOS del comprobante BCP.
Devuelve únicamente un JSON válido con esta estructura:

{
  \"numero_operacion\": \"\",
  \"banco\": \"\",
  \"monto\": \"\",
  \"fecha\": \"AAAA-MM-DD\"
}

Muy importante:
- La fecha SIEMPRE debe estar en formato AAAA-MM-DD.

NO agregues explicación ni texto adicional. Solo JSON.",
                            ],
                            [
                                "type" => "image_url",
                                "image_url" => [
                                    "url" => "data:image/jpeg;base64,{$imageData}",
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            // --- PROCESAR RESPUESTA ---
            $content = $response->choices[0]->message->content;

            $texto = '';

            // Si devuelve blocks
            if (is_array($content)) {
                foreach ($content as $block) {
                    if (($block['type'] ?? null) === 'text') {
                        $texto .= $block['text'];
                    }
                }
            } else {
                $texto = $content;
            }

            // Limpiar ```json
            $texto = trim(preg_replace('/```json|```/', '', $texto));

            // Decodificar JSON
            $data = json_decode($texto, true);

            if (!$data) {
                session()->flash('error', 'No se pudo extraer la información correctamente.');
                $this->procesando = false;
                return;
            }

            if (!$data['numero_operacion']) {
                session()->flash('error', 'No se detectaron campos válidos');
                $this->procesando = false;
                return;
            }

            // Asignar datos
            $this->datos = [
                'numero' => $data['numero_operacion'] ?? null,
                'banco' => $data['banco'] ?? null,
                'monto' => $data['monto'] ?? null,
                'fecha' => $data['fecha'] ?? null,
            ];
        } catch (\Exception $e) {
            session()->flash('error', '❌ Error: ' . $e->getMessage());
        }

        $this->procesando = false;
    }

    public function eliminarImagen()
    {
        $this->reset(['imagen', 'datos']);
    }

    private function normalizarMonto($valor)
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        return (float) str_replace(',', '', $valor);
    }

    public function guardar()
    {
        $this->validate();

        DB::transaction(function () {

            $monto_operacion = $this->normalizarMonto($this->cuota["MtoOperacion"] ?? null);
            $slinMonto = $this->normalizarMonto($this->cuota["Cuota"] ?? null);
            $slinPenalidad = $this->normalizarMonto($this->cuota["Penalidad"] ?? null);

            $solicitud = SolicitudEvidenciaPago::updateOrCreate(
                [
                    'codigo_cuota' => $this->cuota["idCuota"] ?? null,
                ],
                [
                    'unidad_negocio_id' => $this->unidad_negocio_id,
                    'proyecto_id' => $this->proyecto_id,
                    'cliente_id' => Auth::id(),
                    'estado_solicitud_evidencia_pago_id' => EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::PENDIENTE) ?? 1,

                    'razon_social' => $this->lote["razon_social"] ?? null,
                    'nombre_proyecto' => $this->lote["descripcion"] ?? null,
                    'etapa' => $this->lote["id_etapa"] ?? null,
                    'manzana' => $this->lote["id_manzana"] ?? null,
                    'lote' => $this->lote["id_lote"] ?? null,
                    'codigo_cliente' => $this->lote["id_cliente"] ?? null,
                    'numero_cuota' => $this->cuota["NroCuota"] ?? null,
                    'transaccion_id' => $this->cuota["IdTransaccion"] ?? null,
                    'fecha_operacion' => $this->cuota["FecOperacion"] ?? null,
                    'fecha_vencimiento' => $this->cuota["FecVencimiento"] ?? null,
                    'monto_operacion' => $monto_operacion,
                    'slin_monto' => $slinMonto,
                    'slin_penalidad' => $slinPenalidad,
                    'slin_numero_operacion' => $this->cuota["NroOperacion"] ?? null,
                    'ticket' => $this->cuota["Ticket"] ?? null,
                    'comprobante' => $this->cuota["Comprobante"] ?? null,
                    'lote_completo' =>
                        $this->lote['id_proyecto'] .
                        $this->lote['id_etapa'] . '-' .
                        $this->lote['id_manzana'] . '-' .
                        $this->lote['id_lote'],
                    'slin_asbanc' => $this->cuota["Asbanc"] ?? false,
                    'slin_evidencia' => $this->cuota["EvidPago"] ?? false,
                ]
            );

            $ruta = $this->imagen->store('evidencias', 'public');

            $monto = null;
            if (!empty($this->datos['monto'])) {
                $monto = preg_replace('/[^0-9.]/', '', $this->datos['monto']);
            }

            EvidenciaPago::create([
                'solicitud_evidencia_pago_id' => $solicitud->id,
                'estado_solicitud_evidencia_pago_id' => EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::PENDIENTE) ?? 1,

                'path' => $ruta,
                'url' => Storage::url($ruta),
                'extension' => $this->imagen->getClientOriginalExtension(),

                'numero_operacion' => $this->datos["numero"] ?? null,
                'banco' => $this->datos["banco"] ?? null,
                'monto' => $monto,
                'fecha' => $this->datos['fecha'] ?? null,
            ]);
        });

        session()->flash('success', 'Comprobante guardado correctamente');
        $this->reset(['imagen', 'datos', 'unidad_negocio_id', 'proyecto_id']);
        $this->dispatch('actualizarCronograma');
    }

    public function render()
    {
        return view('livewire.cliente.lote.adjuntar-voucher-pago');
    }
}
