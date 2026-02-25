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
        Schema::table('entrega_fests', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('nombre');
        });

        // Generar slugs para registros existentes
        \App\Models\EntregaFest::all()->each(function ($evento) {
            $evento->slug = \Illuminate\Support\Str::slug($evento->nombre) . '-' . $evento->id;
            $evento->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrega_fests', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
