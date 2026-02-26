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
        Schema::create('solicitud_digitalizar_letras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unidad_negocio_id')->constrained('unidad_negocios')->cascadeOnDelete();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->nullOnDelete(); //user_id
            $table->foreignId('gestor_id')->nullable()->constrained('users')->nullOnDelete(); //quien atiende al cliente //asignado

            $table->foreignId('estado_solicitud_digitalizar_letra_id')->default(1)->constrained('estado_solicitud_digitalizar_letras', indexName: 'sdl_estado_sdl_fk')->onDelete('restrict');

            // Identidad de la cuota
            $table->string('lote_completo')->nullable();
            $table->string('codigo_cuota')->unique();

            // Slin
            $table->string('razon_social');
            $table->string('nombre_proyecto');
            $table->string('etapa');
            $table->string('manzana');
            $table->string('lote');
            $table->string('codigo_cliente')->nullable();
            $table->string('numero_cuota')->nullable();
            $table->string('codigo_venta')->nullable();
            $table->string('fecha_vencimiento')->nullable();
            $table->string('importe_cuota')->nullable();

            $table->text('observacion')->nullable();

            //DB ANTIGUO
            $table->string('dni')->nullable();
            $table->string('nombres')->nullable();
            $table->string('email')->nullable();
            $table->string('celular')->nullable();
            $table->string('direccion')->nullable();
            $table->string('region')->nullable();
            $table->string('provincia')->nullable();
            $table->string('distrito')->nullable();
            $table->string('origen')->default('portal')->nullable(); //portal o slin

            //SUPERVISOR
            $table->foreignId('usuario_valida_id')->nullable()->constrained('users')->nullOnDelete(); //CERRADO POR
            $table->dateTime('fecha_validacion')->nullable(); //FECHA CIERRE

            //AUDITORIA
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); //CREADO POR
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['unidad_negocio_id', 'estado_solicitud_digitalizar_letra_id'],
                'idx_sdl_unidad_estado'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_digitalizar_letras');
    }
};
