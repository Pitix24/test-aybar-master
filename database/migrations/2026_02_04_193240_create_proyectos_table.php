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
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unidad_negocio_id')->constrained('unidad_negocios')->onDelete('cascade');
            $table->foreignId('grupo_proyecto_id')->constrained('grupo_proyectos')->onDelete('cascade');

            $table->string('nombre')->unique();
            $table->string('slin_id')->nullable();
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
        Schema::dropIfExists('proyectos');
    }
};
