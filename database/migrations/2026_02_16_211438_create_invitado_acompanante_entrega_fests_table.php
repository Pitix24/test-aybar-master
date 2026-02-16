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
        Schema::create('invitado_acompanante_entrega_fests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitado_entrega_fest_id')
                ->constrained(null, null, 'fk_invitado_acompanante_fest');

            $table->string('dni')->nullable();
            $table->string('nombre');
            $table->string('apellidos');

            $table->boolean('asistio')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitado_acompanante_entrega_fests');
    }
};
