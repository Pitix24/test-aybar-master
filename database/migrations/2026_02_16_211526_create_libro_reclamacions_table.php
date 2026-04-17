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
        Schema::create('libro_reclamacions', function (Blueprint $table) {
            $table->bigIncrements('ticket');

            $table->foreignId('unidad_negocio_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('proyecto_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gestor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();

            $table->string('serie')->default('TCK');
            $table->unsignedBigInteger('numero_reclamo')->nullable();
            $table->string('codigo_ticket', 20)->nullable();
            $table->string('codigo', 20)->nullable();

            $table->string('manzana', 5)->nullable();
            $table->string('lote', 5)->nullable();

            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('domicilio');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->enum('tipo_documento', ['DNI', 'RUC', 'CE', 'NO_DEFINIDO'])->default('DNI');
            $table->string('numero_documento');

            $table->enum('tipo_bien_contratado', ['PRODUCTO', 'SERVICIO', 'NO_DEFINIDO'])->default('PRODUCTO');
            $table->decimal('monto_reclamado', 10, 2)->nullable();
            $table->text('descripcion')->nullable();

            $table->enum('tipo_pedido', ['RECLAMO', 'QUEJA', 'NO_DEFINIDO'])->default('RECLAMO');
            $table->text('detalle')->nullable();
            $table->text('pedido')->nullable();
            $table->boolean('conformidad')->default(false);

            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_respuesta')->nullable()->comment('Fecha de respuesta al usuario.');

            $table->unsignedBigInteger('estado_libro_reclamaciones_id')->nullable();
            $table->enum('clasificacion', ['PROCEDE', 'NO_PROCEDE', 'PENDIENTE_REVISION'])->default('PENDIENTE_REVISION');

            $table->string('cliente_tipo_documento', 50)->nullable();
            $table->string('cliente_documento', 20)->nullable();
            $table->string('cliente_nombre', 255)->nullable();
            $table->string('cliente_email', 255)->nullable();
            $table->string('cliente_celular', 30)->nullable();
            $table->text('cliente_direccion')->nullable();
            $table->text('asunto')->nullable();
            $table->json('lotes')->nullable();
            $table->string('nota_fuente_titulo', 255)->nullable();
            $table->dateTime('nota_fuente_fecha')->nullable();
            $table->dateTime('assigned_at')->nullable();
            $table->text('observaciones_internas')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('archivo_1')->nullable();
            $table->string('archivo_2')->nullable();
            $table->string('archivo_3')->nullable();
            $table->string('archivo_4')->nullable();

            $table->boolean('leido')->default(false);
            $table->enum('estado', ['NUEVO', 'REVISION', 'RESUELTO', 'CERRADO'])->default('NUEVO');

            $table->unique('codigo');
            $table->index(['unidad_negocio_id', 'numero_reclamo']);
            $table->index('codigo_ticket');
            $table->index('estado_libro_reclamaciones_id');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libro_reclamacions');
    }
};
