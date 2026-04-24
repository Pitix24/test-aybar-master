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
        Schema::create('avance_proyectos', function (Blueprint $table) {
            $table->id();
            
            // Relaciones (Jerarquía)
            $table->foreignId('unidad_negocio_id')->constrained('unidad_negocios')->onDelete('cascade');
            $table->foreignId('grupo_proyecto_id')->nullable()->constrained('grupo_proyectos')->onDelete('cascade');
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->onDelete('cascade');

            // Datos del video
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('video_id'); 
            
            $table->bigInteger('clicks')->default(0);
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avance_proyectos');
    }
};
