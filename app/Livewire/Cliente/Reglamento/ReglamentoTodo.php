<?php

namespace App\Livewire\Cliente\Reglamento;

use App\Models\Reglamento;
use App\Models\Proyecto;
use App\Services\AybarSlinService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
class ReglamentoTodo extends Component
{
    /** IDs de proyectos locales que pertenecen al cliente (cargados en mount) */
    public array $proyectoIds = [];

    /** Lista de proyectos para el filtro: [id => nombre] */
    public array $proyectos = [];

    /** Valor seleccionado en el filtro ('' = todos) */
    public string $proyectoFiltro = '';

    public function mount(AybarSlinService $slinService): void
    {
        try {
            if (
                auth()->user()->necesitaActualizarDatosPersonales() ||
                auth()->user()->necesitaActualizarDireccion()
            ) {
                session()->flash('info', 'Para poder acceder a los reglamentos, es obligatorio que actualices tus datos.');
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

            $this->proyectoIds = $this->obtenerProyectosDelCliente($slinService, $cliente);

            if (empty($this->proyectoIds)) {
                session()->flash('info', 'No se encontraron proyectos asociados.');
                return;
            }

            // Cargar nombres de proyectos para el select de filtro
            $this->proyectos = Proyecto::whereIn('id', $this->proyectoIds)
                ->where('activo', true)
                ->orderBy('nombre')
                ->pluck('nombre', 'id')
                ->toArray();

        } catch (\Exception $e) {
            Log::channel('reglamento')->error('Error en mount ReglamentoTodo: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al cargar los reglamentos. Intente nuevamente.');
        }
    }

    /**
     * Obtiene los IDs de proyectos locales del cliente basado en sus lotes SLIN
     */
    private function obtenerProyectosDelCliente(AybarSlinService $slinService, array $cliente): array
    {
        try {
            $slinProyectoIds = [];

            foreach ($cliente['empresas'] ?? [] as $empresa) {
                try {
                    $lotes = $slinService->getLotes(
                        $empresa['codigo'],
                        $empresa['id_empresa']
                    );

                    foreach ($lotes as $lote) {
                        if (isset($lote['id_servicio']) && $lote['id_servicio'] === '02') {
                            if (isset($lote['id_proyecto'])) {
                                $slinProyectoIds[] = $lote['id_proyecto'];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::channel('reglamento')->warning(
                        'Error al obtener lotes para empresa ' . $empresa['id_empresa'] . ': ' . $e->getMessage()
                    );
                }
            }

            if (empty($slinProyectoIds)) {
                return [];
            }

            return Proyecto::whereIn('slin_id', array_unique($slinProyectoIds))
                ->where('activo', true)
                ->pluck('id')
                ->toArray();

        } catch (\Exception $e) {
            Log::channel('reglamento')->error('Error en obtenerProyectosDelCliente: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Registra el click en un reglamento (excluye impersonación)
     */
    public function registrarClick(int $id): void
    {
        if (session()->has('impersonator_id')) {
            return;
        }

        try {
            $reglamento = Reglamento::find($id);
            if ($reglamento) {
                $reglamento->increment('clicks');
            }
        } catch (\Exception $e) {
            Log::channel('reglamento')->error('Error registrando click en reglamento ' . $id . ': ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Reglamento::whereIn('proyecto_id', $this->proyectoIds)
            ->where('activo', true)
            ->with(['archivoPdf', 'proyecto']);

        if ($this->proyectoFiltro !== '') {
            $query->where('proyecto_id', (int) $this->proyectoFiltro);
        }

        $reglamentos = $query->orderBy('orden')->get();

        return view('livewire.cliente.reglamento.reglamento-todo', [
            'reglamentos' => $reglamentos,
            'proyectos'   => $this->proyectos,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
