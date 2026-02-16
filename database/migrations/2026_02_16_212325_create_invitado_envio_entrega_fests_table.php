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
        Schema::create('invitado_envio_entrega_fests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitado_entrega_fest_id')
                ->constrained(null, null, 'fk_invitado_envio_fest');

            $table->enum('canal', [
                'correo',
                'whatsapp',
                'llamada'
            ]);

            $table->enum('estado', [
                'pendiente',
                'enviado',
                'fallido',
                'confirmado'
            ])->default('pendiente');

            $table->text('detalle')->nullable(); // respuesta, observación

            $table->foreignId('user_id')->nullable()->constrained(); // quien hizo el contacto

            $table->timestamp('fecha_envio')->nullable();
            $table->timestamps();
            $table->index(['canal', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitado_envio_entrega_fests');
    }
};
