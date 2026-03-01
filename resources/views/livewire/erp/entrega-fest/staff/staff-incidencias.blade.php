<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('erp.entrega-fest.staff.dashboard', $evento->id) }}"
                class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Canal de Incidencias</h1>
        </div>
        <button wire:click="$toggle('mostrarFormulario')"
            class="w-full sm:w-auto px-6 py-3 bg-red-600 text-white font-bold rounded-xl shadow-lg hover:bg-red-700 transition-all flex items-center justify-center gap-2">
            <i class="fas {{ $mostrarFormulario ? 'fa-times' : 'fa-plus' }}"></i>
            {{ $mostrarFormulario ? 'Cancelar' : 'Reportar Incidencia' }}
        </button>
    </div>

    @if($mostrarFormulario)
        <div
            class="bg-white p-6 rounded-2xl shadow-xl border border-red-100 mb-8 animate-in fade-in slide-in-from-top-4 duration-300">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Nueva Incidencia</h3>
            <form wire:submit.prevent="reportar" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Problema</label>
                    <select wire:model="tipo"
                        class="w-full rounded-xl border-gray-200 focus:ring-red-500 focus:border-red-500">
                        <option>Logística</option>
                        <option>Seguridad</option>
                        <option>Técnico</option>
                        <option>Salud</option>
                        <option>Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Prioridad</label>
                    <select wire:model="prioridad"
                        class="w-full rounded-xl border-gray-200 focus:ring-red-500 focus:border-red-500">
                        <option>Baja</option>
                        <option>Media</option>
                        <option>Alta</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Descripción de los hechos</label>
                    <textarea wire:model="descripcion" rows="3"
                        class="w-full rounded-xl border-gray-200 focus:ring-red-500 focus:border-red-500"
                        placeholder="¿Qué pasó? Sea específico..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Ubicación exacta</label>
                    <input type="text" wire:model="ubicacion"
                        class="w-full rounded-xl border-gray-200 focus:ring-red-500 focus:border-red-500"
                        placeholder="Ej: Puerta 2, Detrás del escenario">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Evidencia (Fotos)</label>
                    <input type="file" wire:model="fotos" multiple
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                </div>

                <div class="md:col-span-2 flex justify-end pt-4">
                    <button type="submit"
                        class="px-8 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all shadow-md">
                        Enviar Reporte <i class="fas fa-paper-plane ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($incidencias as $inc)
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <span
                                class="px-3 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-full uppercase">{{ $inc->tipo }}</span>
                            <span
                                class="px-3 py-1 {{ $inc->prioridad === 'Alta' ? 'bg-red-100 text-red-600' : ($inc->prioridad === 'Media' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600') }} text-[10px] font-bold rounded-full uppercase">
                                Prioridad {{ $inc->prioridad }}
                            </span>
                        </div>
                        <span
                            class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase">{{ $inc->estado }}</span>
                    </div>

                    <h4 class="text-lg font-bold text-gray-800 mb-2">{{ $inc->descripcion }}</h4>
                    <p class="text-sm text-gray-500 mb-4"><i class="fas fa-map-marker-alt mr-1"></i>
                        {{ $inc->ubicacion ?: 'No especificada' }}</p>

                    <div class="flex items-center gap-2 mb-6 text-[11px] text-gray-400 font-medium">
                        <i class="fas fa-user-circle"></i> {{ $inc->informante->name }}
                        <span class="mx-1">•</span>
                        <i class="fas fa-clock"></i> {{ $inc->created_at->diffForHumans() }}
                    </div>

                    @if($inc->media->count() > 0)
                        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                            @foreach($inc->getMedia('evidencias') as $media)
                                <a href="{{ $media->getUrl() }}" target="_blank"
                                    class="shrink-0 w-20 h-20 rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                                    <img src="{{ $media->getUrl('thumb') ?? $media->getUrl() }}"
                                        class="w-full h-full object-cover hover:scale-110 transition-transform">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>