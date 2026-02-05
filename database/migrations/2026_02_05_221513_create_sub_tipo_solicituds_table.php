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
        Schema::create('sub_tipo_solicituds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_solicitud_id')
                ->constrained('tipo_solicituds')
                ->cascadeOnDelete();

            $table->string('nombre')->unique();
            $table->integer('tiempo_solucion')->nullable(); // si es null, hereda del tipo
            $table->boolean('activo')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_tipo_solicituds');
    }
};
