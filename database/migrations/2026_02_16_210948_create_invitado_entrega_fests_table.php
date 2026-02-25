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
            $table->foreignId('prospecto_entrega_fest_id')->unique()->constrained();

            $table->string('codigo_invitado')->unique(); // QR o código interno

            $table->integer('cantidad_acompanantes_permitidos')->default(0);

            $table->boolean('confirmado')->default(false);
            $table->enum('estado_confirmacion', ['pendiente', 'confirmado', 'no_asiste'])->default('pendiente');
            $table->enum('transporte', ['bus', 'propio', 'na'])->default('na');
            $table->text('observaciones_asistencia')->nullable();

            $table->timestamps();
            $table->unique(['entrega_fest_id', 'codigo_invitado']);
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
