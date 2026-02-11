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
        Schema::create('envio_cavalis', function (Blueprint $table) {
            $table->id();

            $table->date('fecha_corte');

            $table->foreignId('unidad_negocio_id')
                ->constrained('unidad_negocios')
                ->cascadeOnDelete();

            $table->foreignId('estado_solicitud_digitalizar_letra_id')->default(1)->constrained('estado_solicitud_digitalizar_letras', indexName: 'env_cavali_estado_sdl_fk')->onDelete('restrict');

            $table->timestamp('enviado_at')->nullable();
            $table->string('archivo_zip')->nullable();

            $table->timestamps();

            // 🔹 Un solo corte por día y por unidad
            $table->unique(
                ['fecha_corte', 'unidad_negocio_id'],
                'envio_cavali_fecha_unidad_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envio_cavalis');
    }
};
