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
        Schema::create('auditoria_prospecto_contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospecto_entrega_fest_id')->nullable()->constrained('prospecto_entrega_fests')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('media_id')->nullable();
            $table->string('accion', 50);
            $table->string('collection_name')->default('contrato-preliminar');
            $table->string('file_name')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['prospecto_entrega_fest_id', 'created_at'], 'idx_auditoria_prospecto_fecha');
            $table->index(['accion', 'created_at'], 'idx_auditoria_accion_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_prospecto_contratos');
    }
};
