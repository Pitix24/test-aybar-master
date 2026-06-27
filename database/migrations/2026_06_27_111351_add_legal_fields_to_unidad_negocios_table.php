<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidad_negocios', function (Blueprint $table) {
            // El email principal que recibe notificaciones (ej. notificacionesindecopi@aybarsac.com)
            $table->string('email_interno')->nullable()->unique()->after('slin_id');
            
            // Correos secundarios o variaciones
            $table->string('email_alias')->nullable()->after('email_interno');
            
            // Usuario/Gestor responsable por defecto para esta unidad
            $table->foreignId('responsable_id')->nullable()->after('email_alias')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('unidad_negocios', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->dropColumn(['email_interno', 'email_alias', 'responsable_id']);
        });
    }
};