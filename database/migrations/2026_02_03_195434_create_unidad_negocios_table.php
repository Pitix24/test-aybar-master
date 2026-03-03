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
        Schema::create('unidad_negocios', function (Blueprint $table) {
            $table->id();

            $table->string('nombre')->unique();
            $table->string('razon_social')->unique()->nullable();
            $table->string('ruc')->unique()->nullable();
            $table->string('slin_id')->unique()->nullable();
            $table->text('direccion')->nullable();

            //REPRESENTANTE LEGAL DEL GIRADOR
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
