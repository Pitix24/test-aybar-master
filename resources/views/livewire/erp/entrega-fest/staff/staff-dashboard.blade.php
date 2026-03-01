<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">
                {{ $evento->nombre }}
            </h1>
            <p class="text-gray-500 font-medium">Panel de Operaciones en Campo</p>
        </div>
        <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-full border border-blue-100">
            <span class="relative flex h-3 w-3">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
            </span>
            <span class="text-blue-700 font-semibold text-sm">Evento Activo</span>
        </div>
    </div>

    <!-- Grid de Navegación Operativa -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Itinerario -->
        <a href="{{ route('erp.entrega-fest.staff.itinerario', $evento->id) }}"
            class="group relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 p-3 bg-orange-50 text-orange-500 rounded-bl-2xl">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Itinerario</h3>
            <p class="text-gray-500 text-sm mb-4">Run of Show, tiempos y cronograma en vivo.</p>
            <div class="flex items-center justify-between mt-auto">
                <span class="text-orange-600 font-bold text-lg">{{ $evento->itinerario_bloques_count }} Bloques</span>
                <i class="fas fa-arrow-right text-gray-300 group-hover:text-orange-500 transition-colors"></i>
            </div>
        </a>

        <!-- MOP -->
        <a href="{{ route('erp.entrega-fest.staff.mop', $evento->id) }}"
            class="group relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 p-3 bg-purple-50 text-purple-500 rounded-bl-2xl">
                <i class="fas fa-tasks text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Manual OP</h3>
            <p class="text-gray-500 text-sm mb-4">Mis tareas asignadas y protocolos por rol.</p>
            <div class="flex items-center justify-between mt-auto">
                <span class="text-purple-600 font-bold text-lg">Operativo</span>
                <i class="fas fa-arrow-right text-gray-300 group-hover:text-purple-500 transition-colors"></i>
            </div>
        </a>

        <!-- Incidencias -->
        <a href="{{ route('erp.entrega-fest.staff.incidencias', $evento->id) }}"
            class="group relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 p-3 bg-red-50 text-red-500 rounded-bl-2xl">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Incidencias</h3>
            <p class="text-gray-500 text-sm mb-4">Reportar problemas, emergencias o fallas.</p>
            <div class="flex items-center justify-between mt-auto">
                <span class="text-red-600 font-bold text-lg">{{ $evento->incidencias_count }} Reportes</span>
                <i class="fas fa-arrow-right text-gray-300 group-hover:text-red-500 transition-colors"></i>
            </div>
        </a>

        <!-- Proveedores -->
        <a href="{{ route('erp.entrega-fest.staff.proveedores', $evento->id) }}"
            class="group relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 p-3 bg-emerald-50 text-emerald-500 rounded-bl-2xl">
                <i class="fas fa-truck-loading text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Proveedores</h3>
            <p class="text-gray-500 text-sm mb-4">Logística de entrada, montaje y servicios.</p>
            <div class="flex items-center justify-between mt-auto">
                <span class="text-emerald-600 font-bold text-lg">{{ $evento->proveedores_count }} Externos</span>
                <i class="fas fa-arrow-right text-gray-300 group-hover:text-emerald-500 transition-colors"></i>
            </div>
        </a>

        <!-- Asistencia Fast -->
        <a href="{{ route('erp.entrega-fest.vista.asistencia', $evento->id) }}"
            class="group relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 p-3 bg-indigo-50 text-indigo-500 rounded-bl-2xl">
                <i class="fas fa-qrcode text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Asistencia</h3>
            <p class="text-gray-500 text-sm mb-4">Acceso rápido a escaneo de invitados.</p>
            <div class="flex items-center justify-between mt-auto">
                <span class="text-indigo-600 font-bold text-lg">CHECK-IN</span>
                <i class="fas fa-arrow-right text-gray-300 group-hover:text-indigo-500 transition-colors"></i>
            </div>
        </a>

        <!-- Recursos -->
        <a href="{{ route('erp.entrega-fest.staff.recursos', $evento->id) }}"
            class="group relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 p-3 bg-amber-50 text-amber-500 rounded-bl-2xl">
                <i class="fas fa-file-alt text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Recursos</h3>
            <p class="text-gray-500 text-sm mb-4">Planos, protocolos y planes de contingencia.</p>
            <div class="flex items-center justify-between mt-auto">
                <span class="text-amber-600 font-bold text-lg">Visualizar</span>
                <i class="fas fa-arrow-right text-gray-300 group-hover:text-amber-500 transition-colors"></i>
            </div>
        </a>

    </div>
</div>