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
        Schema::create('tipo_solicituds', function (Blueprint $table) {
            $table->id();

            $table->string('nombre')->unique();
            $table->integer('tiempo_solucion')->nullable(); // tiempo por defecto (en horas)
            $table->boolean('activo')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_solicituds');
    }
};
