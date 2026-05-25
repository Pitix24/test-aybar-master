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
        Schema::create('reglamentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');

            $table->string('titulo');
            $table->text('descripcion')->nullable();

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
        Schema::dropIfExists('reglamentos');
    }
};
