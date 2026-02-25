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
            $table->string('email');
            $table->string('celular');

            $table->string('lote')->nullable();
            $table->string('manzana')->nullable();

            $table->enum('estado', [
                'pendiente',
                'observado',
                'aprobado',
                'rechazado'
            ])->default('pendiente');

            $table->text('observacion')->nullable();

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
                'pendiente',
                'observado',
                'aprobado',
                'rechazado'
            ])->default('pendiente');

            // Legal
            $table->enum('estado_contrato_preeliminar_emitido', [
                'pendiente',
                'observado',
                'aprobado',
                'rechazado'
            ])->default('pendiente');

            $table->enum('estado_firma_contrato_firmado', [
                'pendiente',
                'observado',
                'aprobado',
                'rechazado'
            ])->default('pendiente');
            $table->dateTime('fecha_firma')->nullable(); //fecha firma contrato
            $table->dateTime('fecha_generacion_contrato')->nullable(); //fecha generacion contrato

            $table->timestamps();
            $table->unique(['entrega_fest_id', 'dni']);
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
