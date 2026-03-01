<div class="p-6">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('erp.entrega-fest.staff.dashboard', $evento->id) }}"
            class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Itinerario en Vivo</h1>
    </div>

    <div
        class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">

        @foreach($evento->itinerarioBloques as $bloque)
            <div
                class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <!-- Icono de Estado en el centro -->
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full border border-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 
                            {{ $bloque->estado === 'COMPLETADO' ? 'bg-emerald-500 text-white' : ($bloque->estado === 'EN_CURSO' ? 'bg-orange-500 text-white animate-pulse' : 'bg-slate-200 text-slate-500') }}">
                    <i class="fas {{ $bloque->estado === 'COMPLETADO' ? 'fa-check' : 'fa-clock' }} text-sm"></i>
                </div>

                <!-- Tarjeta de Contenido -->
                <div
                    class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
                    <div class="flex flex-col sm:flex-row justify-between items-start mb-4 gap-2">
                        <div>
                            <span
                                class="text-sm font-bold text-orange-600 uppercase tracking-widest">{{ $bloque->hora_inicio }}
                                - {{ $bloque->hora_fin }}</span>
                            <h3 class="text-xl font-bold text-gray-800">{{ $bloque->titulo }}</h3>
                            <p class="text-gray-500 text-sm"><i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $bloque->ubicacion }}</p>
                        </div>
                        <div class="flex gap-1">
                            @if($bloque->estado !== 'COMPLETADO')
                                <button wire:click="actualizarEstado({{ $bloque->id }}, 'COMPLETADO')"
                                    class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-colors"
                                    title="Marcar como completado">
                                    <i class="fas fa-check"></i>
                                </button>
                                @if($bloque->estado !== 'EN_CURSO')
                                    <button wire:click="actualizarEstado({{ $bloque->id }}, 'EN_CURSO')"
                                        class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition-colors"
                                        title="Iniciar ahora">
                                        <i class="fas fa-play text-xs"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if($bloque->checklists->count() > 0)
                        <div class="mt-4 pt-4 border-t border-gray-50 space-y-2">
                            @foreach($bloque->checklists as $item)
                                <div class="flex items-center gap-3 group/item">
                                    <button wire:click="toggleChecklist({{ $item->id }})"
                                        class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all {{ $item->esta_listo ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-200 group-hover/item:border-emerald-300' }}">
                                        @if($item->esta_listo) <i class="fas fa-check text-[8px]"></i> @endif
                                    </button>
                                    <span
                                        class="text-sm {{ $item->esta_listo ? 'text-gray-400 line-through' : 'text-gray-700' }}">{{ $item->tarea }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div
                        class="mt-4 inline-block px-3 py-1 bg-slate-100 rounded-full text-[10px] font-bold text-slate-500 uppercase">
                        {{ $bloque->responsable_rol }}
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>