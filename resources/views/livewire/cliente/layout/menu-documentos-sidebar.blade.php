<div wire:init="loadProyectos" x-data="{ open: {{ request()->routeIs('cliente.reglamento') || request()->routeIs('cliente.documentos.*') ? 'true' : 'false' }} }">
    <button type="button" @click="open = !open" class="{{ request()->routeIs('cliente.reglamento') || request()->routeIs('cliente.documentos.*') ? 'active' : '' }}" style="width: 100%;">
        <span><i class="fa-solid fa-file-lines"></i> Documentos</span>
        <i class="fa-solid fa-chevron-down" style="font-size: 0.8rem; transition: transform 0.2s ease;" :style="open ? 'transform: rotate(180deg)' : ''"></i>
    </button>
    <div x-show="open" x-transition style="display: none; background-color: #f8fafc;">
        <a href="{{ route('cliente.reglamento') }}" class="{{ request()->routeIs('cliente.reglamento') ? 'active' : '' }}" style="padding-left: 2.8rem; border-bottom: 1px solid var(--color-light-border);">
            <span><i class="fa-solid fa-scale-balanced"></i> Reglamentos</span>
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        @if(!$readyToLoad)
            <div style="padding: 10px 0 10px 2.8rem; border-bottom: 1px solid var(--color-light-border); color: var(--color-text-light); font-size: 0.9rem;">
                <i class="fa-solid fa-spinner fa-spin"></i> Cargando proyectos...
            </div>
        @else
            @foreach($proyectos as $p)
            <a href="{{ route('cliente.documentos.proyecto', $p['id']) }}" class="{{ request()->is('portal/documentos/'.$p['id']) ? 'active' : '' }}" style="padding-left: 2.8rem; border-bottom: 1px solid var(--color-light-border);">
                <span><i class="fa-solid fa-folder-open"></i> {{ \Illuminate\Support\Str::limit($p['nombre'], 20) }}</span>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
            @endforeach
        @endif
    </div>
</div>
