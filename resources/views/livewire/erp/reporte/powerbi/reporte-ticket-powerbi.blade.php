<div class="g_gap_pagina">
    <x-powerbi-embed
        :configured="$embedData['configured'] ?? false"
        :embedToken="$embedData['embedToken'] ?? ''"
        :embedUrl="$embedData['embedUrl'] ?? ''"
        :reportId="$embedData['reportId'] ?? ''"
        :pageName="$embedData['pageName'] ?? null"
        :titulo="$titulo"
        :reporteKey="$reporteKey"
        :rutaClasica="$rutaClasica"
    />
</div>
