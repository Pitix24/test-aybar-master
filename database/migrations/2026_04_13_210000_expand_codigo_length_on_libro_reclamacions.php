<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('libro_reclamacions', 'codigo')) {
            return;
        }

        Schema::table('libro_reclamacions', function (Blueprint $table) {
            $table->string('codigo', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('libro_reclamacions', 'codigo')) {
            return;
        }

        Schema::table('libro_reclamacions', function (Blueprint $table) {
            $table->string('codigo', 3)->nullable()->change();
        });
    }
};
