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
        Schema::create('asistencia_entrega_fests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invitado_entrega_fest_id')
                ->unique()
                ->constrained();

            $table->foreignId('user_id')->nullable()->constrained(); // quien hizo el check-in

            $table->timestamp('fecha_checkin')->nullable();
            $table->string('metodo')->nullable(); // qr, manual, dni
            $table->boolean('segunda_asistencia')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_entrega_fests');
    }
};
