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
        Schema::create('envio_cavali_solicitud', function (Blueprint $table) {
            $table->id();

            $table->foreignId('envios_cavali_id')
                ->constrained('envios_cavali', indexName: 'env_cavali_sol_env_fk')
                ->cascadeOnDelete();

            $table->foreignId('solicitud_digitalizar_letras_id')
                ->constrained('solicitud_digitalizar_letras', indexName: 'env_cavali_sol_dig_fk')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['envios_cavali_id', 'solicitud_digitalizar_letras_id'],
                'envio_cavali_solicitud_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envio_cavali_solicitud');
    }
};
