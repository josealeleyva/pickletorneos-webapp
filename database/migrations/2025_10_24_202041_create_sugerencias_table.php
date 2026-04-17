<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sugerencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['sugerencia', 'soporte', 'bug', 'otro'])->default('sugerencia');
            $table->string('asunto');
            $table->text('mensaje');
            $table->enum('estado', ['nueva', 'en_revision', 'respondida', 'cerrada'])->default('nueva');
            $table->text('respuesta')->nullable();
            $table->timestamp('respondida_en')->nullable();
            $table->foreignId('respondida_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sugerencias');
    }
};
