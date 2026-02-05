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
        Schema::create('direccions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
            $table->foreignId('provincia_id')->constrained('provincias')->onDelete('cascade');
            $table->foreignId('distrito_id')->constrained('distritos')->onDelete('cascade');

            $table->string('direccion');
            $table->string('direccion_numero');
            $table->string('opcional')->nullable();
            $table->string('codigo_postal');
            $table->string('referencia')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direccions');
    }
};
