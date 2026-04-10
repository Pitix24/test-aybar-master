<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('libro_reclamacions', function (Blueprint $table) {
            $table->string('manzana', 5)->nullable()->after('proyecto_id');
            $table->string('lote', 5)->nullable()->after('manzana');
        });
    }

    public function down(): void
    {
        Schema::table('libro_reclamacions', function (Blueprint $table) {
            $table->dropColumn(['manzana', 'lote']);
        });
    }
};
