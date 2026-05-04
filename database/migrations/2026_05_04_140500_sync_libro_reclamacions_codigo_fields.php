<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('libro_reclamacions')
            ->whereNull('codigo')
            ->whereNotNull('codigo_ticket')
            ->update([
                'codigo' => DB::raw('codigo_ticket'),
            ]);

        DB::table('libro_reclamacions')
            ->whereNull('codigo_ticket')
            ->whereNotNull('codigo')
            ->update([
                'codigo_ticket' => DB::raw('codigo'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se revierte el backfill; ambos campos quedan como compatibilidad histórica.
    }
};
