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
        Schema::create('area_sede', function (Blueprint $table) {
            $table->id();

            $table->foreignId('area_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sede_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['area_id', 'sede_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area_sede', function (Blueprint $table) {
            //
        });
    }
};
