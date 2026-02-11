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
        Schema::create('solicitud_evidencia_pago_emails', function (Blueprint $table) {
            $table->id();

            $table->foreignId('solicitud_evidencia_pago_id')->constrained(indexName: 'sol_evid_pago_email_fk')->onDelete('cascade');
            $table->foreignId('emisor_id')->nullable()->constrained('users')->nullOnDelete(); // Admin
            $table->foreignId('receptor_id')->nullable()->constrained('users')->nullOnDelete(); // Cliente

            $table->string('asunto');
            $table->longText('mensaje');
            $table->timestamp('enviado_at');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_evidencia_pago_emails');
    }
};
