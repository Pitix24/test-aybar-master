<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estado_libro_reclamaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique()->comment('Estado: NUEVO, EN_GESTION, OBSERVADO, RESUELTO, NO_PROCEDE, CERRADO');
            $table->string('descripcion', 255)->nullable()->comment('Descripción del estado');
            $table->string('color', 20)->nullable()->comment('Color para UI (badge)');
            $table->boolean('es_final')->default(false)->comment('Si es estado terminal');
            $table->integer('orden')->default(0)->comment('Orden de aparición');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estado_libro_reclamaciones');
    }
};
