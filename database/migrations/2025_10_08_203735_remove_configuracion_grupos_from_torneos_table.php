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
        Schema::table('torneos', function (Blueprint $table) {
            // Remover foreign keys primero
            $table->dropForeign(['tamanio_grupo_id']);
            $table->dropForeign(['avance_grupos_id']);

            // Remover columnas de configuración de grupos (ahora están en categoria_torneo)
            $table->dropColumn(['numero_grupos', 'tamanio_grupo_id', 'avance_grupos_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('torneos', function (Blueprint $table) {
            // Restaurar columnas si se revierte la migración
            $table->integer('numero_grupos')->nullable();
            $table->foreignId('tamanio_grupo_id')->nullable()->constrained('tamanios_grupos')->onDelete('set null');
            $table->foreignId('avance_grupos_id')->nullable()->constrained('avances_grupos')->onDelete('set null');
        });
    }
};
