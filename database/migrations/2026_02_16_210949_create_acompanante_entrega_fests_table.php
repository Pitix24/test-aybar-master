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
        Schema::create('acompanante_entrega_fests', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 15);
            $table->string('nombres');
            $table->string('email')->nullable();
            $table->string('celular')->nullable();
            $table->foreignId('prospecto_entrega_fest_id')
                ->constrained('prospecto_entrega_fests')
                ->cascadeOnDelete();
            $table->foreignId('invitado_entrega_fest_id')
                ->constrained('invitado_entrega_fests')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acompanante_entrega_fests');
    }
};
