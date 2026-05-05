<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Consolidada: incluye 'codigo' (antes en 2026_05_04_131500_add_codigo_to_unidad_negocios_table.php)
     *              y ubicación geográfica (antes en 2026_05_05_add_ubicacion_to_unidad_negocios_table.php)
     */
    public function up(): void
    {
        Schema::create('unidad_negocios', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 3)->nullable()->unique();

            $table->string('nombre')->unique();
            $table->string('razon_social')->unique()->nullable();
            $table->string('ruc')->unique()->nullable();
            $table->string('slin_id')->unique()->nullable();
            $table->string('direccion')->nullable();

            // UBICACIÓN GEOGRÁFICA
            $table->foreignId('region_id')->nullable()->constrained('regions')->onDelete('set null');
            $table->foreignId('provincia_id')->nullable()->constrained('provincias')->onDelete('set null');
            $table->foreignId('distrito_id')->nullable()->constrained('distritos')->onDelete('set null');

            // REPRESENTANTE LEGAL DEL GIRADOR
            $table->string('cavali_girador_tipo_documento')->nullable();
            $table->string('cavali_girador_documento')->nullable();
            $table->string('cavali_girador_nombre')->nullable();
            $table->string('cavali_girador_apellido')->nullable();
            $table->string('cavali_girador_email')->nullable();
            $table->string('cavali_girador_telefono')->nullable();

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
        Schema::dropIfExists('unidad_negocios');
    }
};
