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
        Schema::create('prospecto_entrega_fests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entrega_fest_id')->constrained();
            $table->foreignId('proyecto_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained(); // quien lo registró

            $table->string('dni', 15);
            $table->string('nombre');
            $table->string('apellidos');

            // Campos de contexto inmobiliario
            $table->string('codigo_cliente')->nullable();
            $table->string('codigo_cuota')->nullable();
            $table->string('lote')->nullable();
            $table->string('manzana')->nullable();
            $table->string('etapa')->nullable();

            $table->enum('estado', [
                'pendiente',
                'observado',
                'aprobado',
                'rechazado'
            ])->default('pendiente');

            $table->text('observacion')->nullable();

            $table->timestamps();
            $table->unique(['entrega_fest_id', 'dni']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospecto_entrega_fests');
    }
};
