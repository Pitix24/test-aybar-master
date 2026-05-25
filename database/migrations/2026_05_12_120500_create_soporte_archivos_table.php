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
        Schema::create('soporte_archivos', function (Blueprint $table) {
            $table->id();

            // Relación Polimórfica (Soporte o SoporteMensaje futura)
            $table->morphs('archivable');

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('nombre_original');
            $table->string('path');
            $table->string('url')->nullable();
            $table->string('titulo')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('extension', 20);
            $table->bigInteger('size');
            $table->string('mime_type');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soporte_archivos');
    }
};
