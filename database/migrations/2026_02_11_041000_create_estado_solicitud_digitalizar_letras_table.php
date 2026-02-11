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
        Schema::create('estado_solicitud_digitalizar_letras', function (Blueprint $table) {
            $table->id();

            $table->string('nombre')->unique();
            $table->string('color')->nullable();
            $table->string('icono')->nullable();
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
        Schema::dropIfExists('estado_solicitud_digitalizar_letras');
    }
};
