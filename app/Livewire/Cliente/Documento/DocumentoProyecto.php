<?php

namespace App\Livewire\Cliente\Documento;

use App\Models\ClienteDocumento;
use App\Models\Proyecto;
use App\Models\TipoClienteDocumento;
use App\Models\UnidadNegocio;
use App\Services\AybarSlinService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed; // <-- 1. Importar Computed

#[Lazy]
#[Layout('layouts.cliente.layout-cliente', ['anchoPantalla' => '100%'])]
class DocumentoProyecto extends Component
{
    public $proyecto_id;
    public $proyecto_nombre = '';

    public function mount($proyecto_id): void
    {
        $this->proyecto_id = $proyecto_id;
        $slinService = app(AybarSlinService::class);

        try {
            if (
                auth()->user()->necesitaActualizarDatosPersonales() ||
                auth()->user()->necesitaActualizarDireccion()
            ) {
                session()->flash('info', 'Para poder acceder a los documentos, es obligatorio que actualices tus datos.');
                return;
            }

            $perfil = Auth::user()->perfilCliente;
            if (!$perfil || !$perfil->dni) {
                session()->flash('error', 'No se encontró información de perfil para el usuario actual.');
                return;
            }

            $cliente = $slinService->getCliente($perfil->dni);

            if (empty($cliente)) {
                session()->flash('error', 'No se pudo obtener su información. Por favor, inténtelo más tarde.');
                return;
            }

            $proyectosPermitidos = $this->obtenerProyectosDelCliente($slinService, $cliente);

            if (!in_array((int)$this->proyecto_id, $proyectosPermitidos)) {
                abort(403, 'No tiene acceso a los documentos de este proyecto.');
            }

            $proyecto = Proyecto::find($this->proyecto_id);
            $this->proyecto_nombre = $proyecto ? $proyecto->nombre : '';

            // 3. IMPORTANTE: No llames a cargarDocumentos() ni asignes nada a $this->documentosAgrupados aquí.

        } catch (\Exception $e) {
            Log::error('Error en mount DocumentoProyecto: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al cargar los documentos. Intente nuevamente.');
        }
    }

    // 4. EL MÉTODO COMPUTADO
    // Este método reemplaza a tu antiguo cargarDocumentos() y a la variable public.
    #[Computed]
    public function documentosAgrupados()
    {
        return TipoClienteDocumento::where('activo', true)
            ->whereHas('clienteDocumentos', function ($query) {
                $query->where('proyecto_id', $this->proyecto_id)
                      ->where('activo', true);
            })
            ->with(['clienteDocumentos' => function ($query) {
                $query->where('proyecto_id', $this->proyecto_id)
                      ->where('activo', true)
                      ->with('archivoPdf')
                      ->orderBy('orden');
            }])
            ->orderBy('orden')
            ->get();
    }

    private function obtenerProyectosDelCliente(AybarSlinService $slinService, array $cliente): array
    {
        try {
            $proyectosEncontrados = [];
            foreach ($cliente['empresas'] ?? [] as $empresa) {
                try {
                    $unidadNegocio = UnidadNegocio::where('slin_id', (string)$empresa['id_empresa'])->first();
                    if (!$unidadNegocio) {
                        continue;
                    }
                    $lotes = $slinService->getLotes(
                        $empresa['codigo'],
                        $empresa['id_empresa']
                    );
                    $slinProyectoIds = [];
                    foreach ($lotes as $lote) {
                        if (isset($lote['id_servicio']) && $lote['id_servicio'] === '02') {
                            if (isset($lote['id_proyecto'])) {
                                $slinProyectoIds[] = (string) $lote['id_proyecto'];
                            }
                        }
                    }
                    if (!empty($slinProyectoIds)) {
                        $slinProyectoIds = array_unique(array_map('strval', $slinProyectoIds));
                        $proyectosIdsDeEmpresa = Proyecto::where('unidad_negocio_id', $unidadNegocio->id)
                            ->whereIn('slin_id', $slinProyectoIds)
                            ->where('activo', true)
                            ->pluck('id')
                            ->toArray();
                        $proyectosEncontrados = array_merge($proyectosEncontrados, $proyectosIdsDeEmpresa);
                    }
                } catch (\Exception $e) {
                    Log::warning('Error al obtener lotes para empresa ' . $empresa['id_empresa'] . ': ' . $e->getMessage());
                }
            }
            return array_unique($proyectosEncontrados);
        } catch (\Exception $e) {
            Log::error('Error en obtenerProyectosDelCliente: ' . $e->getMessage());
            return [];
        }
    }

    public function registrarClick(int $id): void
    {
        // ... (Tu código actual se mantiene igual) ...
        if (session()->has('impersonator_id')) {
            return;
        }
        try {
            $doc = ClienteDocumento::find($id);
            if ($doc) {
                $doc->increment('clicks');
            }
        } catch (\Exception $e) {
            Log::error('Error registrando click en documento ' . $id . ': ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.cliente.documento.documento-proyecto');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
