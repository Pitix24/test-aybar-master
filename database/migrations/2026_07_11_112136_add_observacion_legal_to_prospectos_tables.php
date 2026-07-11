<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prospecto_historicos', function (Blueprint $table) {
            $table->boolean('observacion_legal')->default(false)->after('lote_entregado');
        });

        Schema::table('prospecto_entrega_fests', function (Blueprint $table) {
            $table->boolean('observacion_legal')->default(false)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('prospecto_historicos', function (Blueprint $table) {
            $table->dropColumn('observacion_legal');
        });

        Schema::table('prospecto_entrega_fests', function (Blueprint $table) {
            $table->dropColumn('observacion_legal');
        });
    }
};
