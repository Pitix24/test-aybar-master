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
        Schema::create('prospecto_bancarizacion_entrega_fests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_fest_id')->constrained();
            $table->foreignId('prospecto_entrega_fest_id')
                ->constrained('prospecto_entrega_fests', null, 'idx_pef_banc_id')
                ->cascadeOnDelete();
            $table->string('cuota');
            $table->decimal('importe', 10, 2);
            $table->date('fecha_deposito_real');
            $table->enum('estado', [
                'PENDIENTE',
                'BANCARIZADO',
            ])->default('PENDIENTE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospecto_bancarizacion_entrega_fests');
    }
};
