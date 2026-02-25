<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prospecto_entrega_fests', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        Schema::table('invitado_entrega_fests', function (Blueprint $table) {
            $table->enum('estado_confirmacion', ['pendiente', 'confirmado', 'no_asiste'])->default('pendiente')->after('confirmado');
            $table->enum('transporte', ['bus', 'propio', 'na'])->default('na')->after('estado_confirmacion');
            $table->text('observaciones_asistencia')->nullable()->after('transporte');
        });

        // Generar UUIDs para registros existentes si los hay
        \App\Models\ProspectoEntregaFest::all()->each(function ($p) {
            if (!$p->uuid) {
                $p->uuid = (string) \Illuminate\Support\Str::uuid();
                $p->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospecto_entrega_fests', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('invitado_entrega_fests', function (Blueprint $table) {
            $table->dropColumn(['estado_confirmacion', 'transporte', 'observaciones_asistencia']);
        });
    }
};
