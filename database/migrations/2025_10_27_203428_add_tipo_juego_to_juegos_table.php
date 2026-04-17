<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('juegos', function (Blueprint $table) {
            // Agregar campo tipo_juego después de partido_id
            $table->enum('tipo_juego', ['set', 'partido', 'ida', 'vuelta', 'penales'])
                  ->default('set')
                  ->after('partido_id');
        });

        // Actualizar juegos existentes según el deporte del torneo
        // Para fútbol: tipo_juego = 'partido'
        // Para padel/tenis: tipo_juego = 'set'
        DB::statement("
            UPDATE juegos j
            INNER JOIN partidos p ON j.partido_id = p.id
            LEFT JOIN grupos g ON p.grupo_id = g.id
            LEFT JOIN llaves l ON p.llave_id = l.id
            LEFT JOIN torneos t ON (g.torneo_id = t.id OR l.torneo_id = t.id)
            LEFT JOIN deportes d ON t.deporte_id = d.id
            SET j.tipo_juego = CASE
                WHEN d.nombre = 'Futbol' THEN 'partido'
                ELSE 'set'
            END
            WHERE t.id IS NOT NULL AND d.id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('juegos', function (Blueprint $table) {
            $table->dropColumn('tipo_juego');
        });
    }
};
