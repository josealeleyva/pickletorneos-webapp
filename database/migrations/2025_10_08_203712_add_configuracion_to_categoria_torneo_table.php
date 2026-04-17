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
        Schema::table('categoria_torneo', function (Blueprint $table) {
            // Agregar campos de configuración de grupos por categoría
            $table->integer('numero_grupos')->nullable()->after('categoria_id');
            $table->foreignId('tamanio_grupo_id')->nullable()->after('numero_grupos')
                ->constrained('tamanios_grupos')->onDelete('set null');
            $table->foreignId('avance_grupos_id')->nullable()->after('tamanio_grupo_id')
                ->constrained('avances_grupos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categoria_torneo', function (Blueprint $table) {
            $table->dropForeign(['tamanio_grupo_id']);
            $table->dropForeign(['avance_grupos_id']);
            $table->dropColumn(['numero_grupos', 'tamanio_grupo_id', 'avance_grupos_id']);
        });
    }
};
