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
        Schema::table('juegos', function (Blueprint $table) {
            // Agregar nuevas columnas para el sistema de carga de resultados
            $table->integer('numero_juego')->after('partido_id')->nullable();
            $table->integer('juegos_equipo1')->after('numero_juego')->nullable();
            $table->integer('juegos_equipo2')->after('juegos_equipo1')->nullable();
            $table->foreignId('equipo_ganador_id')->after('juegos_equipo2')->nullable()->constrained('equipos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('juegos', function (Blueprint $table) {
            $table->dropForeign(['equipo_ganador_id']);
            $table->dropColumn(['numero_juego', 'juegos_equipo1', 'juegos_equipo2', 'equipo_ganador_id']);
        });
    }
};
