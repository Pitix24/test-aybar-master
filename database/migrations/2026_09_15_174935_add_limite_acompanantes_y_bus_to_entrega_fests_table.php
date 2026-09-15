<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrega_fests', function (Blueprint $table) {
            $table->unsignedInteger('limite_acompanantes')->default(1)->after('limite_invitados');
            // 0 = sin límite (todos pueden ir en bus). No es nullable a propósito: 0 es
            // un valor explícito y sin ambigüedad, evita el vacío-vs-null del input numérico.
            $table->unsignedInteger('limite_asientos_bus')->default(0)->after('limite_acompanantes');
        });
    }

    public function down(): void
    {
        Schema::table('entrega_fests', function (Blueprint $table) {
            $table->dropColumn('limite_acompanantes');
            $table->dropColumn('limite_asientos_bus');
        });
    }
};
