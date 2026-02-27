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
        Schema::create('invitado_entrega_fests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entrega_fest_id')->constrained();

            // Solo UNO de los dos tendrá valor, el otro será null
            $table->foreignId('prospecto_entrega_fest_id')
                ->nullable()
                ->constrained('prospecto_entrega_fests')
                ->nullOnDelete();

            $table->foreignId('copropietario_entrega_fest_id')
                ->nullable()
                ->constrained('copropietario_entrega_fests')
                ->nullOnDelete();

            $table->string('codigo_invitado')->unique(); // QR o código interno

            $table->integer('cantidad_acompanantes_permitidos')->default(0);

            $table->boolean('confirmado')->default(false);
            $table->enum('estado_confirmacion', ['pendiente', 'confirmado', 'no_asiste'])->default('pendiente');
            $table->enum('transporte', ['bus', 'propio', 'na'])->default('na');
            $table->text('observaciones_asistencia')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitado_entrega_fests');
    }
};
