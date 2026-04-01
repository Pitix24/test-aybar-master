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
        Schema::create('prospecto_entrega_fests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entrega_fest_id')->constrained();
            $table->foreignId('proyecto_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained(); // quien lo registró

            $table->string('dni', 15);
            $table->string('nombres');
            $table->string('email')->nullable();
            $table->string('celular')->nullable();

            $table->boolean('preinvitacion_confirmada')->nullable();

            $table->string('lote')->nullable()->collation('utf8mb4_bin');
            $table->string('manzana')->nullable()->collation('utf8mb4_bin');

            $table->enum('estado_cliente', [
                'ADENDA',
                'DESISTIMIENTO',
                'DEVOLUCION_DE_APORTES',
                'CARTA_NOTARIAL',
                'PLANTON',
                'RESOLUCION_DE_CONTRATO',
                'VENDIDO'
            ])->default('ADENDA');

            // BackOffice
            $table->enum('grupo', [
                'A',
                'B',
                'C',
                'D'
            ])->default('A');
            $table->foreignId('gestor_backoffice_id')->nullable()->constrained('users')->nullOnDelete(); //gestor backoffice
            $table->dateTime('fecha_culminacion_eecc')->nullable(); //fecha culminación estado de cuenta
            $table->string('link_carpeta_eecc')->nullable(); // esto será un link
            $table->string('link_eecc_firmado')->nullable(); // esto será un link
            $table->foreignId('validador_backoffice_id')->nullable()->constrained('users')->nullOnDelete(); //validador backoffice
            $table->dateTime('fecha_validacion_eecc')->nullable(); //fecha validación estado de cuenta
            $table->enum('estado_backoffice', [
                'PENDIENTE',
                'BANCARIZAR',
                'PENALIDAD',
                'OBSERVADO',
                'CONFORME'
            ])->default('PENDIENTE');

            // Legal
            $table->enum('estado_contrato_preeliminar_emitido', [
                'PENDIENTE',
                'GENERADO',
                'OBSERVADO',
                'CONFORME'
            ])->default('PENDIENTE');

            $table->enum('estado_firma_contrato_firmado', [
                'PENDIENTE',
                'FIRMADO'
            ])->default('PENDIENTE');
            $table->dateTime('fecha_firma')->nullable(); //fecha firma contrato
            $table->dateTime('fecha_generacion_contrato')->nullable(); //fecha generacion contrato

            $table->timestamps();
            $table->unique(['entrega_fest_id', 'proyecto_id', 'lote', 'manzana'], 'prospecto_entrega_fest_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospecto_entrega_fests');
    }
};
