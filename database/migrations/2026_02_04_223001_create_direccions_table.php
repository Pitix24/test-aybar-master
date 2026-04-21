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

            $table->foreignId('pais_id')->nullable()->constrained('pais')->onDelete('set null');
            $table->foreignId('region_id')->nullable()->constrained('regions')->onDelete('set null');
            $table->foreignId('provincia_id')->nullable()->constrained('provincias')->onDelete('set null');
            $table->foreignId('distrito_id')->nullable()->constrained('distritos')->onDelete('set null');

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
