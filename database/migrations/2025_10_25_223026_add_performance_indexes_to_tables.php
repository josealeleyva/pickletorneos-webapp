<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Esta migración agrega índices de performance a las tablas principales
     * para optimizar queries frecuentes identificados en el análisis de código.
     */
    public function up(): void
    {
        // === TABLA PARTIDOS ===
        // Nota: La tabla partidos NO tiene torneo_id directamente,
        // se relaciona con torneos a través de equipos

        // Índice compuesto para queries que filtran por grupo y estado
        // Usado en: TorneoGrupoController::calcularTablaPosiciones()
        Schema::table('partidos', function (Blueprint $table) {
            $table->index(['grupo_id', 'estado'], 'idx_partidos_grupo_estado');
        });

        // Índice para búsqueda de partidos por cancha y fecha
        // Usado en: validación de disponibilidad de canchas
        Schema::table('partidos', function (Blueprint $table) {
            $table->index(['cancha_id', 'fecha_hora'], 'idx_partidos_cancha_fecha');
        });

        // Índice para búsqueda de partidos por llave
        // Usado en: TorneoLlaveController
        Schema::table('partidos', function (Blueprint $table) {
            $table->index('llave_id', 'idx_partidos_llave');
        });

        // Índice para búsqueda de partidos por estado
        // Usado en: notificaciones, listados de partidos
        Schema::table('partidos', function (Blueprint $table) {
            $table->index('estado', 'idx_partidos_estado');
        });

        // === TABLA EQUIPOS ===
        // Índice para búsqueda de equipos por grupo
        // Usado en: cálculo de tablas de posiciones, sorteos
        Schema::table('equipos', function (Blueprint $table) {
            $table->index('grupo_id', 'idx_equipos_grupo');
        });

        // Índice compuesto para equipos por torneo y grupo
        // Usado en: listados de equipos, fixture
        Schema::table('equipos', function (Blueprint $table) {
            $table->index(['torneo_id', 'grupo_id'], 'idx_equipos_torneo_grupo');
        });

        // === TABLA LLAVES ===
        // Índice compuesto para búsqueda de llaves por torneo y ronda
        // Usado en: generación y visualización de brackets
        Schema::table('llaves', function (Blueprint $table) {
            $table->index(['torneo_id', 'ronda'], 'idx_llaves_torneo_ronda');
        });

        // Índice para búsqueda de próxima llave
        // Usado en: navegación de brackets, avance de equipos
        Schema::table('llaves', function (Blueprint $table) {
            $table->index('proxima_llave_id', 'idx_llaves_proxima');
        });

        // === TABLA PAGOS_TORNEOS ===
        // Índice compuesto para búsqueda de pagos por organizador y estado
        // Usado en: dashboard de organizador, reportes
        Schema::table('pagos_torneos', function (Blueprint $table) {
            $table->index(['organizador_id', 'estado'], 'idx_pagos_organizador_estado');
        });

        // Índice para búsqueda de pagos por referencia de pago
        // Usado en: webhooks de MercadoPago, verificación de pagos
        Schema::table('pagos_torneos', function (Blueprint $table) {
            $table->index('referencia_pago', 'idx_pagos_referencia');
        });

        // === TABLA EQUIPO_JUGADOR ===
        // Índice para búsqueda de jugadores por equipo
        // Usado en: listado de equipos, notificaciones
        Schema::table('equipo_jugador', function (Blueprint $table) {
            $table->index('equipo_id', 'idx_equipo_jugador_equipo');
        });

        // Índice para búsqueda de equipos por jugador
        // Usado en: perfil de jugador, historial
        Schema::table('equipo_jugador', function (Blueprint $table) {
            $table->index('jugador_id', 'idx_equipo_jugador_jugador');
        });

        // === TABLA JUGADORES ===
        // Índice para búsqueda de jugadores por organizador
        // Usado en: listados de jugadores del organizador
        Schema::table('jugadores', function (Blueprint $table) {
            $table->index('organizador_id', 'idx_jugadores_organizador');
        });

        // Índice para búsqueda de jugadores por usuario
        // Usado en: vinculación de cuentas, perfil
        Schema::table('jugadores', function (Blueprint $table) {
            $table->index('user_id', 'idx_jugadores_user');
        });

        // === TABLA TORNEOS ===
        // Índice compuesto para búsqueda de torneos por organizador y estado
        // Usado en: dashboard de organizador, listados filtrados
        Schema::table('torneos', function (Blueprint $table) {
            $table->index(['organizador_id', 'estado'], 'idx_torneos_organizador_estado');
        });

        // Índice para búsqueda de torneos por deporte
        // Usado en: listados públicos, búsquedas
        Schema::table('torneos', function (Blueprint $table) {
            $table->index('deporte_id', 'idx_torneos_deporte');
        });

        // Índice para búsqueda de torneos por complejo
        // Usado en: listados de torneos por complejo
        Schema::table('torneos', function (Blueprint $table) {
            $table->index('complejo_id', 'idx_torneos_complejo');
        });

        // === TABLA GRUPOS ===
        // Índice compuesto para búsqueda de grupos por torneo
        // Usado en: listados de grupos, sorteos
        Schema::table('grupos', function (Blueprint $table) {
            $table->index(['torneo_id', 'orden'], 'idx_grupos_torneo_orden');
        });

        // === TABLA JUEGOS ===
        // Índice para búsqueda de juegos por partido
        // Usado en: carga de resultados, visualización de partidos
        Schema::table('juegos', function (Blueprint $table) {
            $table->index(['partido_id', 'orden'], 'idx_juegos_partido_orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar índices en orden inverso
        Schema::table('juegos', function (Blueprint $table) {
            $table->dropIndex('idx_juegos_partido_orden');
        });

        Schema::table('grupos', function (Blueprint $table) {
            $table->dropIndex('idx_grupos_torneo_orden');
        });

        Schema::table('torneos', function (Blueprint $table) {
            $table->dropIndex('idx_torneos_complejo');
            $table->dropIndex('idx_torneos_deporte');
            $table->dropIndex('idx_torneos_organizador_estado');
        });

        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropIndex('idx_jugadores_user');
            $table->dropIndex('idx_jugadores_organizador');
        });

        Schema::table('equipo_jugador', function (Blueprint $table) {
            $table->dropIndex('idx_equipo_jugador_jugador');
            $table->dropIndex('idx_equipo_jugador_equipo');
        });

        Schema::table('pagos_torneos', function (Blueprint $table) {
            $table->dropIndex('idx_pagos_referencia');
            $table->dropIndex('idx_pagos_organizador_estado');
        });

        Schema::table('llaves', function (Blueprint $table) {
            $table->dropIndex('idx_llaves_proxima');
            $table->dropIndex('idx_llaves_torneo_ronda');
        });

        Schema::table('equipos', function (Blueprint $table) {
            $table->dropIndex('idx_equipos_torneo_grupo');
            $table->dropIndex('idx_equipos_grupo');
        });

        Schema::table('partidos', function (Blueprint $table) {
            $table->dropIndex('idx_partidos_estado');
            $table->dropIndex('idx_partidos_llave');
            $table->dropIndex('idx_partidos_cancha_fecha');
            $table->dropIndex('idx_partidos_grupo_estado');
        });
    }
};
