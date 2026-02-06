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
        Schema::create('ticket_derivados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('de_area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('a_area_id')->nullable()->constrained('areas')->nullOnDelete();

            $table->foreignId('usuario_deriva_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_recibe_id')->nullable()->constrained('users')->nullOnDelete();

            $table->text('motivo')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_derivados');
    }
};
