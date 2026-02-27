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
        Schema::create('copropietario_entrega_fests', function (Blueprint $table) {
            $table->id();

            // FK al prospecto titular del lote (hereda lote, manzana, entrega_fest_id, proyecto_id)
            $table->foreignId('prospecto_entrega_fest_id')
                ->constrained('prospecto_entrega_fests')
                ->cascadeOnDelete();

            // Solo los campos que cambian por persona
            $table->string('dni', 15);
            $table->string('nombres');
            $table->string('email')->nullable();
            $table->string('celular')->nullable();

            $table->timestamps();

            // Un DNI no puede repetirse en el mismo prospecto
            $table->unique(['prospecto_entrega_fest_id', 'dni']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('copropietario_entrega_fests');
    }
};
